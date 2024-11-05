<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
//$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
//$consumtion_library=return_library_array( "select job_no, avg_finish_cons from wo_pre_cost_fabric_cost_dtls", "job_no", "avg_finish_cons");
$order_arr=return_library_array("select id, po_number from wo_po_break_down","id","po_number");
$report_arr=array(1=>'Knit Finish',2=>'Woven Finish');

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	/*echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();*/
	if($data[0] != 0){
            echo create_drop_down("cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
        }
        else {
            echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in ($party)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
        exit();
        }
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_color_finish_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $company_id;die;

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
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	//if($year_id!=0) $year_cond="$year_field='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
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
                       <th width="150">Buyer Name</th><th width="200">Date Range</th><th></th>
                    </thead>
        			<tr>
                    <input type="hidden" id="selected_booking">
                   	<td>
                     <?
					echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
							?>
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>','create_booking_search_list_view', 'search_div', 'order_wise_color_finish_fabric_stock_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
		//if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
		if($db_type==0)
		{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		}
		if($db_type==2)
		{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
		}
		$po_array=array();
		$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst  where company_id='$company' $buyer $booking_date and booking_type=1 and is_short=2 and   status_active=1  and is_deleted=0 order by booking_no");
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
		} //echo $po_array[$row[csf("po_break_down_id")]];
		 $approved=array(0=>"No",1=>"Yes");
		 $is_ready=array(0=>"No",1=>"Yes",2=>"No");
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
		$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
		$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
		 $sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where company_id=$company $buyer $booking_date and booking_type=1 and is_short in(1,2) and  status_active=1  and 	is_deleted=0 order by booking_no";
		echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');

exit();
}// Booking Search End
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
	if($db_type==0)  $find_inset=" and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	if($db_type==2 || $db_type==1)$find_inset=" and b.job_no_prefix_num in($data[2]) ";
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  $find_inset";
	if($db_type==0) $year_field_by="and YEAR(b.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if(trim($data[3])!=0) $year_cond=" $year_field_by='$data[3]'"; else $year_cond="";
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond $year_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(1=>$buyer);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_color_finish_fabric_stock_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	//echo "SELECT format_id from lib_report_template where template_name ='".$data."'  and module_id=6 and report_id=94 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=94 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	/*echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";*/

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==178){echo "$('#search1').show();\n";}
			if($id==195){echo "$('#search2').show();\n";}
			if($id==107){echo "$('#search3').show();\n";}
			if($id==242){echo "$('#search4').show();\n";}
		}
	}
	else
	{
		echo "$('#search1').hide();\n";
		echo "$('#search2').hide();\n";
		echo "$('#search3').hide();\n";
		echo "$('#search4').hide();\n";
	}
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$type_id=str_replace("'","",$type);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$book_no=str_replace("'","",$txt_book_no);
	$book_id=str_replace("'","",$txt_book_id);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no_id=str_replace("'","",$txt_order_id);
	$order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$job_year=str_replace("'","",$cbo_year);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	if($cbo_company_id==0 && $buyer_id==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.</span>";
		die;
	}

	if($cbo_report_type){
		if($cbo_company_id !=0) $companycond="and f.company_id=$cbo_company_id"; else $companycond = "";
	}


	if($cbo_report_type == 3){
		if($cbo_company_id !=0) $companycond="and a.company_name=$cbo_company_id"; else $companycond = "";
	}

	if($buyer_id==0)
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
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	//echo $buyer_id_cond; die;
	//echo $buyer_id_cond;die;$order_no=str_replace("'","",$txt_order_no);
	if($txt_file_no!="" || $txt_file_no!=0) $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($txt_ref_no!="") $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond="and YEAR(a.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and f.booking_no_prefix_num='$book_no'";

	if(str_replace("'","",$txt_order_id)!="")  $order_cond=" and b.id in ($order_no_id)"; else $order_cond="";
	if(str_replace("'","",$txt_order_no)!="") $order_cond.=" and b.po_number in ('$order_no')"; else $order_cond="";
	//else $order_cond='';
	//echo $order_cond;die;
	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $ship_date=""; else $ship_date= "and b.pub_shipment_date>=".$txt_date_from."";
	//if( $date_from=="") $receive_date=""; else $receive_date= " and d.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_style)!="") $style_cond="and a.style_ref_no=$txt_style"; else $style_cond="";
	//==================================================
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		if(str_replace("'","",$cbo_presantation_type)==1)
		{
		$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond");// and a.season like '$txt_season'

		$po_array=array(); $all_po_id='';
		$style_array=array(); $all_style_id='';
		$job_array=array(); $all_job_id='';
		$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
		$shipdate_array=array();$shipdate_array='';
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			$style_array[$row[csf('id')]]=$row[csf('style')];
			$file_array[$row[csf('id')]]=$row[csf('file_no')];
			$ref_array[$row[csf('id')]]=$row[csf('grouping')];
			$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
			$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		} //echo $all_po_id;die;
		if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
		$knit_fin_recv_array=array();
		$sql_recv=sql_select("select a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no, sum(a.quantity) AS finish_receive_qnty
		from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
		where b.id=a.dtls_id and a.entry_form in (7,37,66,68,225) and a.trans_id!=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no");
		foreach($sql_recv as $row)
		{
			 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
			 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
		}

		//print_r($fin_recv_array);die; inv_issue_master
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b,inv_issue_master c  where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) group by b.po_breakdown_id, b.color_id, a.body_part_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}

		$finish_fab_data=array();
		$sql_recv=sql_select("select c.po_breakdown_id, c.color_id,
		sum(CASE WHEN c.entry_form in (46) THEN c.quantity END) AS receive_return,
		sum(CASE WHEN c.entry_form in (52) THEN c.quantity END) AS issue_return,
		sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=5 THEN c.quantity END) AS transfer_in,
		sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=6 THEN c.quantity END) AS transfer_out
		from order_wise_pro_details c
		where c.entry_form in (46,52,14,15) and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id");
		foreach($sql_recv as $row)
		{
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
		}

		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		$dia_array=array();
		$color_dia_array=sql_select( "select po_break_down_id,pre_cost_fabric_cost_dtls_id,color_number_id,dia_width from  wo_pre_cos_fab_co_avg_con_dtls");
		foreach($color_dia_array as $row)
		{
			$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
		} //var_dump($dia_array);

		$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
		$plan_cut_array=array();
		$sql_plan=sql_select("SELECT c.po_break_down_id,b.fabric_color_id,a.body_part_id, sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty, sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b, wo_po_color_size_breakdown c WHERE a.job_no=c.job_no_mst and b.is_short=2 and a.id=b.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=c.po_break_down_id and b.job_no=c.job_no_mst and c.id=b.color_size_table_id  and c.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by c.po_break_down_id,b.fabric_color_id,a.body_part_id");
		foreach( $sql_plan as $row){
			$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
		}


		$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id,sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty,sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=2 and  e.item_category=2 then d.fin_fab_qnty else 0 end) as other_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and e.job_no=a.job_no and d.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $sql_booking as $row_book){
			$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
			$tot_req_qty_other=$row_book[csf('other_fin_fab_qnty')];
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['other_fin_fab_qnty']=$tot_req_qty_other;;
		}
		$short_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id, sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty,sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e  WHERE a.job_no=d.job_no and a.job_no=e.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no  and  d.po_break_down_id in($all_po_id) and a.fab_nature_id=2  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $short_booking as $row){
			$tot_short_req_qty=$row[csf('short_fin_fab_qnty')];
			$tot_short_req_qty_other=$row[csf('short_fin_fab_qnty_other')];
			$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty']=$tot_short_req_qty;
			$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty_other']=$tot_short_req_qty_other;
		}

		?>
    	<fieldset style="width:2090px;">
        <table cellpadding="0" cellspacing="0" width="2080">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
            </tr>
        </table>
        <div align="left"><b>Main Fabric </b></div>
		<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job</th>
                <th width="100">Buyer</th>
                <th width="90">Order</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="100">Style</th>
                <th width="100">Body Part</th>
                <th width="80">Color Type</th>
                <th width="120">F.Construction</th>
                <th width="120">F.Composition</th>
                <th width="45">GSM</th>
                <th width="40"><p>Fab.Dia</p></th>
                <th width="70"><p>Rack</p></th>
                <th width="70"><p>Shelf</p></th>
                <th width="75">Ship Date</th>
                <th width="80">Order Qty(Pcs)</th>
                <th width="110">Color</th>
                <th width="80">Req. Qty</th>
                <th width="80">Total Recv.</th>
                <th width="80">Recv. Balance</th>
                <th width="80">Total Issued</th>
                <th width="80">Stock</th>
                <th width="50">Cons/Pcs</th>
                <th width="80">Possible Cut Pcs</th>
                <th>Actual Cut</th>
            </thead>
        </table>
        <div style="width:2100px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
            <?
				$all_po_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
				$sql_main="Select g.po_break_down_id as po_id, f.buyer_id as buyer_name, f.job_no, g.fabric_color_id as color_id, c.body_part_id, c.id as pre_cost_fab_dtls_id, c.avg_finish_cons, c.construction, c.gsm_weight, c.composition, c.color_type_id, f.booking_no_prefix_num, g.booking_type
				from  wo_po_details_master a, wo_pre_cost_fabric_cost_dtls  c, wo_booking_mst f, wo_booking_dtls g
				where a.job_no=c.job_no and g.job_no=f.job_no and c.job_no=g.job_no and g.booking_no=f.booking_no and c.id=g.pre_cost_fabric_cost_dtls_id  and f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and g.booking_type=1 $companycond and c.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and c.fab_nature_id=2  $booking_no_cond $style_cond ";
				$p=1;
				foreach($all_po_arr as $all_po)
				{
					if($p==1) $sql_main .="  and (g.po_break_down_id  in(".implode(',',$all_po).")"; else  $sql_main .=" OR g.po_break_down_id  in(".implode(',',$all_po).")";		$p++;
				}
				$sql_main .=")";
				$sql_main .=" group by  g.po_break_down_id,c.body_part_id,f.buyer_id,f.job_no,c.avg_finish_cons, g.fabric_color_id,f.booking_no_prefix_num,g.booking_type,c.gsm_weight,c.id,c.body_part_id,c.construction,c.composition,c.color_type_id order by c.construction,c.composition ";
				//echo $sql_main;die;
				$nameArray=sql_select($sql_main );

				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}

				$i=1;
				//$nameArray=sql_select($sql_main );
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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$ship_date=$shipdate_array[$row[csf('po_id')]];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					//echo $row[csf("avg_finish_cons")].'='.$row[csf("color_number_avg")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;
					$fin_rack=$knit_fin_recv_array[$order_id][$body_part_id][$color_id]['rack'];
					$fin_shelf=$knit_fin_recv_array[$order_id][$body_part_id][$color_id]['shelf'];

					//echo $row[csf("avg_finish_cons")];
					//$req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$tot_short_req_qty=$short_booking_qnty[$order_id][$body_part_id][$color_id]['short_fin_fab_qnty'];
					$reg_booking_qty=$booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']+$tot_short_req_qty;
					$tot_order_qty_pcs=$plan_cut_array[$order_id][$body_part_id][$color_id]['plan_cut_qnty'];
					$knit_recv_qty=$knit_fin_recv_array[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]]['receive_qnty'];
					$knit_issue_qty=$issue_qnty[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]]['issue_qnty'];
					//echo $knit_recv_qty.'ddd';
					$knit_recv_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['receive_return'];
					$knit_issue_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['issue_return'];
					$knit_transfer_in_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_in'];
					$knit_transfer_out_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_out'];

					$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
					$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;

					$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);

					$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
					$tot_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];

					$dia=$dia_array[$row[csf('pre_cost_fab_dtls_id')]][$order_id][$color_id]['dia'];
					$style_id=$style_array[$order_id];
					$job_prefix_no=$job_array[$order_id];
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo $job_prefix_no; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
						<td width="90"><div style="word-break:break-all"><? echo $order_arr[$order_id]; ?></div></td>
						<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id]; ?></div></td>
						<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id]; ?></div></td>
						<td width="100"><p><? echo $style_id; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
						<td width="80"><p><? echo $color_type[$row[csf("color_type_id")]]; ?></p></td>
						<td width="120"><p><? echo $row[csf("construction")]; ?></p></td>
						<td width="120"><p><?  echo $row[csf("composition")]; ?></p></td>
						<td width="45"><p><? echo $row[csf("gsm_weight")]; ?></p></td>
						<td width="40"><p><? echo $dia; ?></p></td>
						<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
						<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
						<td width="75" align="right"><p><? echo change_date_format($ship_date); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_order_qty_pcs); ?></p></td>
						<td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
						<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
						<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
						<td width="80" align="right"><p><? $possible_cut=$tot_knit_recv_qty/$tot_cons_avg; echo number_format($possible_cut); ?></p></td>
						<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
					</tr>
				<?
				$i++;
				//$total_order_qnty+=$row[csf("order_quantity_set")];
				$total_recv_qty+=$tot_knit_recv_qty;
				$total_issue_qty+=$tot_knit_issue_qty;
				$total_balance_qty+=$tot_recv_bal;
				$total_stock_qty+=$tot_stock;
				$total_actual_cut_qty+=$tot_actual_cut_qty;
				$total_reg_booking_qty+=$reg_booking_qty;
				$cons_per+=$tot_cons_avg;
				$total_qty_pcs+=$tot_order_qty_pcs;
				$tot_possible_cut+=$possible_cut;
				}
				?>
				</table>
				<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="90"></th>
						<th width="70"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="120"></th>
						<th width="45"></th>
						<th width="40"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs); ?></th>
						<th width="80" align="right" id=""><? echo number_format($total_qty_pcs); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
						<th width="50" align="right"><? echo number_format($cons_per); ?></th>
						<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
						<th width="" align="right"><? echo number_format($total_actual_cut_qty); ?></th>
					</tfoot>
				</table>
			</div>
			 </fieldset>
			 <br/>
			 <fieldset style="width:155px;">
			<div align="left"><b>Other Fabric </b>
			</div>
			<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="90">Job</th>
					<th width="100">Buyer</th>
					<th width="80">Order No</th>
					<th width="70">File No</th>
					<th width="80">Ref. No</th>
					<th width="100">Style</th>
					<th width="100">Body Part</th>
					<th width="80">Color Type</th>
					<th width="120">F.Construction</th>
					<th width="120">F.Composition</th>
					<th width="45">GSM</th>
					<th width="40">Fab.Dia</th>
					<th width="70">Rack</th>
					<th width="70">Shelf</th>
					<th width="75">Ship Date</th>
					<th width="80">Order Qty(Pcs)</th>
					<th width="110">Color</th>
					<th width="80">Req. Qty</th>
					<th width="80">Total Recv.</th>
					<th width="80">Recv. Balance</th>
					<th width="80">Total Issued</th>
					<th width="">Stock</th>
				</thead>
			</table>
			<div style="width:1900px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
				<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
				<?

						if( $date_from=="") $ship_date2=""; else $ship_date2= "and b.pub_shipment_date>=".$txt_date_from."";
						$poDataArray2=sql_select("select b.id,  b.pub_shipment_date,b.po_number,a.buyer_name,a.job_no_prefix_num,b.file_no,b.grouping,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date2 $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond");
						$po_array=array(); $all_po_id2='';
						$style_array=array(); $all_style_id='';
						$job_array=array(); $all_job_id='';
						$buyer_array=array();$all_buyer_id='';
						$ship_date_array=array();$ship_date_array='';
						foreach($poDataArray2 as $row)
						{
						$po_array[$row[csf('id')]]=$row[csf('po_number')];
						$style_array[$row[csf('id')]]=$row[csf('style')];
						$file_array[$row[csf('id')]]=$row[csf('file_no')];
						$ref_array[$row[csf('id')]]=$row[csf('grouping')];
						$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
						$ship_date_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
						$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
						if($all_po_id2=="") $all_po_id2=$row[csf('id')]; else $all_po_id2.=",".$row[csf('id')];
						}//echo $all_po_id2;die;
						if($all_po_id2==0 || $all_po_id2=="") $all_po_id3=0;else $all_po_id3=$all_po_id2;
						$all_po_arr2=array_chunk(array_unique(explode(",",$all_po_id3)),999);
						//print_r($all_po_arr2);die;
							$sql_other="Select g.po_break_down_id as po_id,f.buyer_id as buyer_name,f.job_no,g.fabric_color_id as color_id,c.body_part_id,c.id as pre_cost_fab_dtls_id,c.avg_finish_cons,c.construction,c.gsm_weight,c.composition,c.color_type_id,f.booking_no_prefix_num,g.booking_type
						from  wo_po_details_master a, wo_pre_cost_fabric_cost_dtls  c,wo_booking_mst f,wo_booking_dtls g where a.job_no=c.job_no and g.booking_no=f.booking_no and g.job_no=c.job_no  and c.id=g.pre_cost_fabric_cost_dtls_id and  f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.fab_nature_id=2  and f.item_category=2 and  c.is_deleted=0 and g.booking_type=1  and f.company_id=$cbo_company_id  and c.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)  $booking_no_cond  ";
						$j=1;
						foreach($all_po_arr2 as $all_po2)
						{
							if($j==1) $sql_other .="  and (g.po_break_down_id  in(".implode(',',$all_po2).")"; else  $sql_other .=" OR g.po_break_down_id  in(".implode(',',$all_po2).")";		$j++;
						}
						$sql_other .=")";
						$sql_other .=" group by  g.po_break_down_id,c.body_part_id,f.buyer_id,f.job_no,c.avg_finish_cons, g.fabric_color_id,f.booking_no_prefix_num,g.booking_type,c.gsm_weight,c.id,c.body_part_id,c.construction,c.composition,c.color_type_id order by c.construction,c.composition ";

				//echo $sql_other;

				$k=1;
				$other_data=sql_select( $sql_other );
				foreach ($other_data as $rows)
				{
					if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";

					$dzn_qnty=0;
					if($costing_per_id_library[$rows[csf('job_no')]]==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//echo $rows[csf("booking_no")];
					$shipdate=$ship_date_array[$rows[csf('po_id')]];
					$order_id_other=$rows[csf('po_id')];
					$color_id_other=$rows[csf("color_id")];
					$cons_avg_other=$rows[csf("avg_finish_cons")];
					$tot_cons_avg_other=$cons_avg_other/$dzn_qnty;

					$body_part_id_other=$rows[csf("body_part_id")];
					$fin_rack=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['rack_no'];
					$fin_shelf=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['shelf_no'];
					//$req_qty_other=($rows[csf("avg_finish_cons")]/$dzn_qnty)*$rows[csf("order_quantity_set")];
					$tot_short_req_qty2=$short_booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['short_fin_fab_qnty_other'];
					$reg_booking_qty_other=$booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['other_fin_fab_qnty']+$tot_short_req_qty2;
					$tot_qty_pcs_other=$plan_cut_array[$order_id_other][$body_part_id_other][$color_id_other]['plan_cut_qnty_other'];

					$knit_recv_qty_other=$knit_fin_recv_array[$order_id_other][$body_part_id_other][$color_id_other]['receive_qnty'];
					$knit_issue_qty_other=$issue_qnty[$order_id_other][$body_part_id_other][$color_id_other]['issue_qnty'];

					//$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];

					$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];
					$knit_issue_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['issue_return'];
					$knit_transfer_in_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_in'];
					$knit_transfer_out_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_out'];

					$tot_knit_recv_qty_other=($knit_recv_qty_other+$knit_transfer_in_qty_other+$knit_issue_return_qty_other);
					$tot_recv_bal_other=$reg_booking_qty_other-$tot_knit_recv_qty_other;
					$tot_knit_issue_qty_other=($knit_issue_qty_other+$knit_transfer_out_qty_other+$knit_recv_return_qty_other);
					$tot_stock_other=$tot_knit_recv_qty_other-$tot_knit_issue_qty_other;
					$tot_actual_cut_qty_other=$actual_cut_qnty[$order_id_other][$color_id_other]['actual_cut_qty'];
					$dia_other=$dia_array[$rows[csf('pre_cost_fab_dtls_id')]][$order_id_other][$color_id_other]['dia'];

					$style_id=$style_array[$order_id_other];
					$job_prefix_no=$job_array[$order_id_other];
					?>
					<tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor2;?>')" id="trother<? echo $k;?>">
						<td width="30"><? echo $k; ?></td>
						<td width="90"><p><? echo $job_prefix_no; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$rows[csf("buyer_name")]]; ?></p></td>

						<td width="80"><div style="word-break:break-all"><? echo $order_arr[$order_id_other];  //$order_arr[ ?></div></td>
						<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id_other]; ?></div></td>
						<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id_other]; ?></div></td>
						<td width="100"><p><? echo $style_id; ?></p></td>

						<td width="100"><p><?  echo $body_part[$rows[csf("body_part_id")]]; //$body_part ?></p></td>
						<td width="80"><p><? echo $color_type[$rows[csf("color_type_id")]]; ?></p></td>
						<td width="120"><p><? echo $rows[csf("construction")]; ?></p></td>
						<td width="120"><p><? echo $rows[csf("composition")]; ?></p></td>
						<td width="45"><p><? echo $rows[csf("gsm_weight")]; ?></p></td>
						<td width="40"><p><? echo $dia_other; ?></p></td>
						<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
						<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
						<td width="75" align="right"><p><? echo change_date_format($shipdate); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_qty_pcs_other); ?></p></td>
						<td width="110"><p><? echo $color_arr[$rows[csf("color_id")]]; ?></p></td>
						<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('job_no')];?>','<? echo $rows[csf('buyer_name')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','<? echo $rows[csf('booking_no')]; ?>','<? echo $rows[csf('booking_type')]; ?>','req_qty_main_short_other')"><? echo number_format($reg_booking_qty_other,2); ?></a></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty_other,2); ?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_recv_bal_other,2); ?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty_other,2); ?></a></p></td>
						<td width="" align="right"><p><? echo number_format($tot_stock_other,2); ?></p></td>
					</tr>
				<?
				$k++;
				//$total_order_qnty_other+=$rows[csf("order_quantity_set")];
				$total_req_qty_other+=$reg_booking_qty_other;
				$total_knit_recv_qty_other+=$tot_knit_recv_qty_other;
				$total_recv_bal_other+=$tot_recv_bal_other;
				$total_knit_stock_other_qty+=$tot_stock_other;
				$total_tot_knit_issue_qty_other+=$tot_knit_issue_qty_other;
				$total_qty_pcs_other+=$tot_qty_pcs_other;
				}
				?>
				</table>
				<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						 <th width="90"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="70"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs_other); ?></th>
						<th width="80" align="right" id=""><? echo number_format($total_qty_pcs_other); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_req_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_knit_recv_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_bal_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_tot_knit_issue_qty_other); ?></th>
						<th width="" align="right"><? echo number_format($total_knit_stock_other_qty); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		}
		else
		{
			//echo "select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond ";
			$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $year_cond $style_cond");// and a.season like '$txt_season'

			$job_array=array(); $job_data_arr=array(); $all_po_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$job_array[$row[csf('id')]]['pDate']=$row[csf('pub_shipment_date')];
				$job_data_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$job_data_arr[$row[csf('job_no')]]['style']=$row[csf('style')];
				$job_data_arr[$row[csf('job_no')]]['jobPre']=$row[csf('job_no_prefix_num')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
			} //echo $all_po_id;die;
			if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			unset($poDataArray);
			$knit_fin_recv_array=array();
			$sql_recv=sql_select("select a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no, sum(a.quantity) AS finish_receive_qnty
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
			where b.id=a.dtls_id and a.entry_form in (7,37,66,68) and a.trans_id!=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no");
			foreach($sql_recv as $row)
			{
				$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
				if($row[csf('rack_no')]!='')
				{
					$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
				}
				if($row[csf('shelf_no')]!=0)
				{
					if($row[csf('shelf_no')]!='')
					{
						$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
					}
				}
			}
			unset($sql_recv);

			$issue_qnty=array();
			$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b, inv_issue_master c where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) group by b.po_breakdown_id, b.color_id, a.body_part_id");
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
			}
			unset($sql_issue);

			$finish_fab_data=array();
			$sql_retTrns=sql_select("select c.po_breakdown_id, c.color_id,
			sum(CASE WHEN c.entry_form in (46) THEN c.quantity END) AS receive_return,
			sum(CASE WHEN c.entry_form in (52) THEN c.quantity END) AS issue_return,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=5 THEN c.quantity END) AS transfer_in,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=6 THEN c.quantity END) AS transfer_out
			from order_wise_pro_details c
			where c.entry_form in (46,52,14,15) and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id");
			foreach($sql_recv as $row)
			{
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
			}
			unset($sql_retTrns);

			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);
			$dia_array=array();
			$color_dia_array=sql_select( "select po_break_down_id, pre_cost_fabric_cost_dtls_id, color_number_id, dia_width from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in ($all_po_id) and po_break_down_id!=0");
			foreach($color_dia_array as $row)
			{
				$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
			} //var_dump($dia_array);
			unset($color_dia_array);

			//$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
			$plan_cut_array=array();
			$sql_plan=sql_select("select c.po_break_down_id,b.fabric_color_id,a.body_part_id,
			sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty,
			sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other

			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_color_size_breakdown c
			WHERE a.job_no=c.job_no_mst and b.is_short=2 and a.id=b.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=c.po_break_down_id and b.job_no=c.job_no_mst and c.id=b.color_size_table_id  and
			 c.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and
			b.is_deleted=0 group by c.po_break_down_id,b.fabric_color_id,a.body_part_id");
			foreach( $sql_plan as $row)
			{

				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
			}


			$sql_booking="select a.id as pre_cost_fab_dtls_id, a.avg_finish_cons, a.construction, a.composition, a.gsm_weight, a.color_type_id, a.body_part_id, b.booking_no_prefix_num, c.po_break_down_id, b.buyer_id as buyer_name, b.job_no, b.is_short, c.fabric_color_id, c.fin_fab_qnty

			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_mst b, wo_booking_dtls c
			WHERE a.job_no=b.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.job_no=c.job_no and c.po_break_down_id in($all_po_id) and c.po_break_down_id!=0 and b.is_short in (1,2) and a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0";
			//echo $sql_booking; die;
			$sql_booking_res=sql_select($sql_booking); $booking_data_arr=array(); $booking_dataOther_arr=array(); $book_qty_arr=array(); $book_otrQty_arr=array();
			foreach( $sql_booking_res as $row)
			{
				$aa='';
				$aa=$row[csf('body_part_id')];
				if($aa==1 || $aa==14 || $aa==15 || $aa==16 || $aa==17 || $aa==20)
				{
					$booking_data_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';
					if($row[csf('is_short')]==2)
					{
						$book_qty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_qty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}

					$booking_data_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.'0'.'__';
				}
				else
				{
					$booking_dataOther_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';

					if($row[csf('is_short')]==2)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}

					$booking_dataOther_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.'0'.'__';
				}
			}
			?>
            <fieldset style="width:2090px;">
                <table cellpadding="0" cellspacing="0" width="2080">
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?> (Style/ Job Wise)</strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
                    </tr>
                </table>
                <div align="left"><b>Main Fabric </b></div>
                <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Job</th>
                        <th width="100">Buyer</th>
						<th width="100">Style</th>
                        <th width="90">Order</th>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Body Part</th>
                        <th width="80">Color Type</th>
                        <th width="120">F.Construction</th>
                        <th width="120">F.Composition</th>
                        <th width="45">GSM</th>
                        <th width="40"><p>Fab.Dia</p></th>
                        <th width="70"><p>Rack</p></th>
                        <th width="70"><p>Shelf</p></th>
                        <th width="75">Ship Date</th>
                        <th width="80">Order Qty(Pcs)</th>
                        <th width="110">Color</th>
                        <th width="80">Req. Qty</th>
                        <th width="80">Total Recv.</th>
                        <th width="80">Recv. Balance</th>
                        <th width="80">Total Issued</th>
                        <th width="80">Stock</th>
                        <th width="50">Cons/Pcs</th>
                        <th width="80">Possible Cut Pcs</th>
                        <th>Actual Cut</th>
                    </thead>
                </table>
                <div style="width:2100px; max-height:350px; overflow-y:scroll;" id="scroll_body">
                    <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
                    <?

				$i=1;
				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}
				foreach($booking_data_arr as $job_no=>$job_data)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$dzn_qnty=0;
					if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
					else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
					else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
					else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					//echo $dzn_qnty.'='.$row[csf("cons")];

					$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';
					$poIds=array_filter(array_unique(explode(',',$job_data['po'])));
					foreach($poIds as $po_id)
					{
						if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
						if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
						if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
						if($ref=='') $ref=$job_array[$po_id]['ref']; else $ref.=', '.$job_array[$po_id]['ref'];
					}
					$file_no=implode(', ',array_unique(explode(', ',$file)));
					$po_no=implode(', ',array_unique(explode(', ',$order_no)));
					$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
					$intRef=implode(', ',array_unique(explode(', ',$ref)));

					$style_ref=''; $buyer_name=''; $job_pre='';
					$style_ref=$job_data_arr[$job_no]['style'];
					$buyer_name=$buyer_arr[$job_data_arr[$job_no]['buyer']];
					$job_pre=$job_data_arr[$job_no]['jobPre'];

					$other_data=array_filter(array_unique(explode('__',$job_data['all_data'])));
					foreach($other_data as $book_data)
					{
						$data_ex='';
						$data_ex=explode('##',$book_data);

						$body_part_id=''; $color_type=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

						$body_part_id=$data_ex[0];
						$color_type=$data_ex[1];
						$construction=$data_ex[2];
						$composition=$data_ex[3];
						$gsm_weight=$data_ex[4];
						$color_id=$data_ex[5];
						$avg_finish_cons=$data_ex[6];
						$booking_no_pre=$data_ex[7];
						$pre_cost_id=$data_ex[8];

						$main_fin_qty=$book_qty_arr[$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['main'];
						$short_qty=$book_qty_arr[$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['short'];
						//$short_othQty=$data_ex[11];$main_fin_othQty=$data_ex[9];

						$reg_booking_qty=0;
						$reg_booking_qty=$main_fin_qty+$short_qty;
						$tot_cons_avg=$avg_finish_cons/$dzn_qnty;


						$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
						foreach($poIds as $po_id)
						{
							$rack=''; $shelf='';
							$rack=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['rack'];
							if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
							$shelf=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['shelf'];
							if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
							$plan_cut_qty+=$plan_cut_array[$po_id][$body_part_id][$color_id]['plan_cut_qnty'];

							$knit_recv_qty+=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['receive_qnty'];
							$knit_issue_qty+=$issue_qnty[$po_id][$body_part_id][$color_id]['issue_qnty'];
							//echo $knit_recv_qty.'ddd';
							$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
							$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
							$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
							$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

							$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

							$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
						}
						$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
						$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;
						$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
						$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><? echo $job_pre; ?></td>
								<td width="100"><p><? echo $buyer_name; ?></p></td>
								<td width="100"><p><? echo $style_ref; ?></p></td>
								<td width="90"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
								<td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>
								<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
								<td width="80"><p><? echo $color_type[$color_type]; ?></p></td>
								<td width="120"><p><? echo $construction; ?></p></td>
								<td width="120"><p><?  echo $composition; ?></p></td>
								<td width="45"><p><? echo $gsm_weight; ?></p></td>
								<td width="40"><p><? echo $dia; ?></p></td>
								<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
								<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
								<td width="75"><p><? echo $ship_date; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
								<td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
								<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
								<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
								<td width="80" align="right"><p><? $possible_cut=$tot_knit_recv_qty/$tot_cons_avg; echo number_format($possible_cut); ?></p></td>
								<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
							</tr>
						<?
						$i++;
						$total_order_qnty+=$plan_cut_qty;
						$total_recv_qty+=$tot_knit_recv_qty;
						$total_issue_qty+=$tot_knit_issue_qty;
						$total_balance_qty+=$tot_recv_bal;
						$total_stock_qty+=$tot_stock;
						$total_actual_cut_qty+=$tot_actual_cut_qty;
						$total_reg_booking_qty+=$reg_booking_qty;
						$cons_per+=$tot_cons_avg;
						$total_qty_pcs+=$plan_cut_qty;
						$tot_possible_cut+=$possible_cut;
					}
				}
				?>
				</table>
				<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="75">Total</th>
						<th width="80" align="right"><? //echo number_format($total_qty_pcs); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
						<th width="50" align="right"><? echo number_format($cons_per,4); ?></th>
						<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
						<th align="right"><? echo number_format($total_actual_cut_qty); ?></th>
					</tfoot>
				</table>
			</div>
			 </fieldset>
			 <br/>
			 <fieldset style="width:155px;">
			<div align="left"><b>Other Fabric </b>
			</div>
			<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="90">Job</th>
					<th width="100">Buyer</th>
                    <th width="100">Style</th>
					<th width="80">Order No</th>
					<th width="70">File No</th>
					<th width="80">Ref. No</th>
					<th width="100">Body Part</th>
					<th width="80">Color Type</th>
					<th width="120">F.Construction</th>
					<th width="120">F.Composition</th>
					<th width="45">GSM</th>
					<th width="40">Fab.Dia</th>
					<th width="70">Rack</th>
					<th width="70">Shelf</th>
					<th width="75">Ship Date</th>
					<th width="80">Order Qty(Pcs)</th>
					<th width="110">Color</th>
					<th width="80">Req. Qty</th>
					<th width="80">Total Recv.</th>
					<th width="80">Recv. Balance</th>
					<th width="80">Total Issued</th>
					<th width="">Stock</th>
				</thead>
			</table>
			<div style="width:1900px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
				<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
				<?
				$k=1;
				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}
				foreach($booking_dataOther_arr as $jobNo=>$jobData)
				{
					if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";
					$dzn_qnty=0;
					if($costing_per_id_library[$jobNo]==1) $dzn_qnty=12;
					else if($costing_per_id_library[$jobNo]==3) $dzn_qnty=12*2;
					else if($costing_per_id_library[$jobNo]==4) $dzn_qnty=12*3;
					else if($costing_per_id_library[$jobNo]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					//echo $rows[csf("booking_no")];

					$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';
					$poIds=array_filter(array_unique(explode(',',$jobData['po'])));
					foreach($poIds as $po_id)
					{
						if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
						if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
						if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
						if($intRef=='') $intRef=$job_array[$po_id]['ref']; else $intRef.=', '.$job_array[$po_id]['ref'];
					}
					$file_no=implode(', ',array_unique(explode(', ',$file)));
					$po_no=implode(', ',array_unique(explode(', ',$order_no)));
					$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
					$intRef=implode(', ',array_unique(explode(', ',$ref)));

					$style_ref=''; $buyer_name=''; $job_pre='';
					$style_ref=$job_data_arr[$jobNo]['style'];
					$buyer_name=$buyer_arr[$job_data_arr[$jobNo]['buyer']];
					$job_pre=$job_data_arr[$jobNo]['jobPre'];
					$other_data='';
					$other_data=array_filter(array_unique(explode('__',$jobData['all_data'])));
					//print_r($other_data);
					foreach($other_data as $book_data)
					{
					//	echo $book_data;
						$data_ex='';
						$data_ex=explode('##',$book_data);

						$body_part_id=''; $color_type=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

						$body_part_id=$data_ex[0];
						$color_type=$data_ex[1];
						$construction=$data_ex[2];
						$composition=$data_ex[3];
						$gsm_weight=$data_ex[4];
						$color_id=$data_ex[5];
						$avg_finish_cons=$data_ex[6];
						$booking_no_pre=$data_ex[7];
						$pre_cost_id=$data_ex[8];

						$main_fin_othQty=$book_otrQty_arr[$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['main'];
						$short_othQty=$book_otrQty_arr[$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['short'];

						$req_booking_qty=0;
						$req_booking_qty=$main_fin_othQty+$short_othQty;
						$tot_cons_avg=$avg_finish_cons/$dzn_qnty;

						//echo $jobNo.'=='.$job_pre.'=='.$buyer_name.'=='.$style_ref.'=='.$body_part_id.'<br>';
						$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
						foreach($poIds as $po_id)
						{
							$rack=''; $shelf='';
							$rack=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['rack'];
							if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
							$shelf=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['shelf'];
							if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
							$plan_cut_qty+=$plan_cut_array[$po_id][$body_part_id][$color_id]['plan_cut_qnty_other'];

							$knit_recv_qty+=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['receive_qnty'];
							$knit_issue_qty+=$issue_qnty[$po_id][$body_part_id][$color_id]['issue_qnty'];
							//echo $knit_recv_qty.'ddd';
							$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
							$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
							$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
							$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

							$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

							$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
						}
						$tot_knit_recv_qty=0; $tot_recv_bal=0; $tot_knit_issue_qty=0; $tot_knit_issue_qty=0; $tot_stock=0;
						$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
						$tot_recv_bal=$req_booking_qty-$tot_knit_recv_qty;
						$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
						$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;

						/*$shipdate=$ship_date_array[$rows[csf('po_id')]];
						$order_id_other=$rows[csf('po_id')];
						$color_id_other=$rows[csf("color_id")];
						$cons_avg_other=$rows[csf("avg_finish_cons")];
						$tot_cons_avg_other=$cons_avg_other/$dzn_qnty;

						$body_part_id_other=$rows[csf("body_part_id")];
						$fin_rack=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['rack_no'];
						$fin_shelf=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['shelf_no'];*/
						//$req_qty_other=($rows[csf("avg_finish_cons")]/$dzn_qnty)*$rows[csf("order_quantity_set")];
						/*$tot_short_req_qty2=$short_booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['short_fin_fab_qnty_other'];
						$reg_booking_qty_other=$booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['other_fin_fab_qnty']+$tot_short_req_qty2;
						$tot_qty_pcs_other=$plan_cut_array[$order_id_other][$body_part_id_other][$color_id_other]['plan_cut_qnty_other'];

						$knit_recv_qty_other=$knit_fin_recv_array[$order_id_other][$body_part_id_other][$color_id_other]['receive_qnty'];
						$knit_issue_qty_other=$issue_qnty[$order_id_other][$body_part_id_other][$color_id_other]['issue_qnty'];

						//$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];

						$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];
						$knit_issue_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['issue_return'];
						$knit_transfer_in_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_in'];
						$knit_transfer_out_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_out'];

						$tot_knit_recv_qty_other=($knit_recv_qty_other+$knit_transfer_in_qty_other+$knit_issue_return_qty_other);
						$tot_recv_bal_other=$reg_booking_qty_other-$tot_knit_recv_qty_other;
						$tot_knit_issue_qty_other=($knit_issue_qty_other+$knit_transfer_out_qty_other+$knit_recv_return_qty_other);
						$tot_stock_other=$tot_knit_recv_qty_other-$tot_knit_issue_qty_other;
						$tot_actual_cut_qty_other=$actual_cut_qnty[$order_id_other][$color_id_other]['actual_cut_qty'];
						$dia_other=$dia_array[$rows[csf('pre_cost_fab_dtls_id')]][$order_id_other][$color_id_other]['dia'];

						$style_id=$style_array[$order_id_other];
						$job_prefix_no=$job_array[$order_id_other];*/
						?>
                        <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor2;?>')" id="trother<? echo $k;?>">
                            <td width="30"><? echo $k; ?></td>
                            <td width="90"><p><? echo $job_pre; ?></p></td>
                            <td width="100"><p><? echo $buyer_name; ?></p></td>
                            <td width="100"><p><? echo $style_ref; ?></p></td>
                            <td width="80"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
                            <td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>

                            <td width="100"><p><?  echo $body_part[$body_part_id]; //$body_part ?></p></td>
                            <td width="80"><p><? echo $color_type[$color_type]; ?></p></td>
                            <td width="120"><p><? echo $construction; ?></p></td>
                            <td width="120"><p><? echo $composition; ?></p></td>
                            <td width="45"><p><? echo $gsm_weight; ?></p></td>
                            <td width="40"><p><? echo $dia; ?></p></td>
                            <td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
                            <td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
                            <td width="75" align="right"><p><? echo $ship_date; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
                            <td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
                            <td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('job_no')];?>','<? echo $rows[csf('buyer_name')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','<? echo $rows[csf('booking_no')]; ?>','<? echo $rows[csf('booking_type')]; ?>','req_qty_main_short_other')"><? echo number_format($req_booking_qty,2); ?></a></p></td>
                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2); ?></a></p></td>
                            <td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
                            <td width="" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
                        </tr>
                    <?
						$k++;
						//$total_order_qnty_other+=$rows[csf("order_quantity_set")];
						$total_req_qty_other+=$req_booking_qty;
						$total_knit_recv_qty_other+=$tot_knit_recv_qty;
						$total_recv_bal_other+=$tot_recv_bal;
						$total_knit_stock_other_qty+=$tot_stock;
						$total_tot_knit_issue_qty_other+=$tot_knit_issue_qty;
						$total_qty_pcs_other+=$plan_cut_qty;
                    }
				}
				?>
				</table>
				<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="75" align="right"><? //echo number_format($total_qty_pcs_other); ?></th>
						<th width="80" align="right"><? //echo number_format($total_qty_pcs_other); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_req_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_knit_recv_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_bal_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_tot_knit_issue_qty_other); ?></th>
						<th align="right"><? echo number_format($total_knit_stock_other_qty); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		}
	}//Knit end
	else if($cbo_report_type==3) // Woven Finish Start
	{
		//echo "select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond"; die;
		$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond");

		$po_array=array(); $all_po_id='';
		$style_array=array(); $all_style_id='';
		$job_array=array(); $all_job_id='';
		$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
		$shipdate_array=array();$shipdate_array='';
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			$style_array[$row[csf('id')]]=$row[csf('style')];
			$file_array[$row[csf('id')]]=$row[csf('file_no')];
			$ref_array[$row[csf('id')]]=$row[csf('grouping')];
			$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
			$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}
		if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;

		$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id,sum(case when a.body_part_type in(1,20,30,40,50) and d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_booking_mst e WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and d.po_break_down_id in($all_po_id) and a.fab_nature_id=3 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $sql_booking as $row_book){
			$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
		}

		$fin_woven_recv_array=array();

		$sql_recv=sql_select("select c.id as prop_id,c.po_breakdown_id,c.color_id,c.quantity as finish_receive_qnty, d.body_part_id from inv_receive_master a,product_details_master b, order_wise_pro_details c ,inv_transaction d where a.entry_form=c.entry_form and  b.id=c.prod_id and c.entry_form in (17) and c.trans_id <> 0 and a.item_category=3 and a.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.id = c.trans_id and c.po_breakdown_id IN ($all_po_id)");
		foreach($sql_recv as $row)
		{
			if($chk_prop_id[$row[csf('prop_id')]]  == "")
			{
				$chk_prop_id[$row[csf('prop_id')]] = $row[csf('prop_id')];
				$fin_woven_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			}

		}
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id,  a.body_part_id, b.color_id,sum(b.quantity) as issue_qnty from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.id = b.trans_id and a.mst_id = c.id and c.entry_form in (19) and b.entry_form in (19) and b.trans_id <> 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 group by b.po_breakdown_id, a.body_part_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
		$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}
		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		?>
	    <fieldset style="width:1330px;">
	        <table cellpadding="0" cellspacing="0" width="1330">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?><br><? $report_type=(str_replace("'","",$cbo_report_type)); if($report_type==2) echo 'Woven Finish'; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	            </tr>
	        </table>
	        <div align="left"><b>Main Fabric</b></div>
			<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="80">Stock</th>
	                <th width="80">Cons/Pcs</th>
	                <th width="80">Possible Cut Pcs</th>
	                <th width="">Actual Cut</th>
	            </thead>
	        </table>
	        <div style="width:1350px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	            <?
						$sql_main="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id  and c.fab_nature_id=3 $ship_date $buyer_id_cond $job_no_cond $order_cond $style_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id order by a.buyer_name,b.id,a.job_no, b.po_number";
				//echo $sql_main;die;
				$i=1;
				$nameArray=sql_select( $sql_main );

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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;

					//$woven_req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$woven_req_qty = ceil($booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']);
					$woven_recv_qty=$fin_woven_recv_array[$order_id][$body_part_id][$color_id]['receive_qnty'];
					//$woven_recv_return_qty=$fin_woven_recv_array[$order_id][$color_id]['receive_return'];
					$tot_woven_recv_qty=$woven_recv_qty;
					$tot_woven_recv_bal=$woven_req_qty-$tot_woven_recv_qty;
					$tot_woven_issue_qty=$issue_qnty[$order_id][$body_part_id][$color_id]['issue_qnty'];
					$tot_woven_stock=$tot_woven_recv_qty-$tot_woven_issue_qty;
					$tot_woven_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];
					?>
	                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
	                    <td width="90"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $order_arr[$order_id]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf("order_quantity_set")],2,'.',''); ?></p></td>
	                    <td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($woven_req_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_qty,2);?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_issue_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_stock,2); ?></p></td>
	                    <td width="80" align="right"><p><?  echo number_format($tot_cons_avg,2); ?></p></td>
	                    <td width="80" align="right"><p><? $possible_cut=$tot_knit_issue_qty/($tot_cons_avg/$dzn_qnty); echo number_format($possible_cut,2); ?></p></td>
	                    <td width="" align="right"><p><? echo number_format($tot_woven_actual_cut_qty,2);?></p></td>
	                </tr>
	            <?
	            $i++;
				$total_order_qnty+=$row[csf("order_quantity_set")];
				$total_woven_recv_qty+=$tot_woven_recv_qty;
				$total_woven_issue_qty+=$tot_woven_issue_qty;
				$total_woven_balance_qty+=$tot_woven_recv_bal;
				$total_woven_stock_qty+=$tot_woven_stock;
				$total_woven_actual_cut_qty+=$tot_woven_actual_cut_qty;
				}
				?>
	            </table>
	            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="80" align="right"><? //echo number_format($total_req_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_balance_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_stock_qty,2,'.',''); ?></th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80" align="right"><? //echo number_format($total_possible_cut_pcs,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_actual_cut_qty,2,'.',''); ?></th>
	                </tfoot>
	            </table>
	        </div>
	        <div align="left"><b>Other Fabric </b>
	        </div>
			<table width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order No</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="">Stock</th>
	            </thead>
	        </table>
	        <div style="width:1130px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
				<table width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
	            <?
				// Knit Finish Start
						$sql_other2="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id and c.body_part_id  not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)  and c.fab_nature_id=3   $ship_date $buyer_id_cond $job_no_cond $order_id_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id order by a.buyer_name,b.id,a.job_no, b.po_number";
				//echo $sql_other;die;
				$k=1;
				$other_data=sql_select( $sql_other );
				foreach ($other_data as $rows)
				{
					if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";

					$dzn_qnty=0;
					if($costing_per_id_library[$rows[csf('job_no')]]==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id_library[$rows[csf('job_no')]]==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$order_id_other=$rows[csf('po_id')];
					$color_id_other=$rows[csf("color_id")];
					$body_part_id_other=$rows[csf("body_part_id")];
					//$tot_cons_avg_other=$cons_avg/$rows[csf("pcs")];
					$woven_req_qty_other=($rows[csf("cons")]/$dzn_qnty)*$rows[csf("order_quantity_set")];
					$woven_recv_qty_other=$fin_woven_recv_array[$order_id_other][$body_part_id_other][$color_id_other]['receive_qnty'];
					$tot_woven_recv_qty_other=$woven_recv_qty_other;
					$tot_woven_recv_bal_other=$woven_req_qty_other-$tot_woven_recv_qty_other;
					$tot_woven_issue_qty_other=$issue_qnty[$order_id_other][$body_part_id_other][$color_id_other]['issue_qnty'];
					$tot_woven_stock_other=$tot_woven_recv_qty_other-$tot_woven_issue_qty_other;
					//$tot_actual_cut_qty_other=$actual_cut_qnty[$order_id_other][$color_id_other]['actual_cut_qty'];
					?>
	                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor;?>')" id="trother<? echo $k;?>">
	                    <td width="30"><? echo $k; ?></td>
	                    <td width="100"><p><? echo $buyer_arr[$rows[csf("buyer_name")]]; ?></p></td>
	                    <td width="90"><p><? echo $rows[csf("job_no_prefix_num")]; ?></p></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $rows[csf("po_number")]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $rows[csf("style_ref_no")]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $body_part[$rows[csf("body_part_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($rows[csf("order_quantity_set")],2,'.',''); ?></p></td>
	                    <td width="110"><p><? echo $color_arr[$rows[csf("color_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($woven_req_qty_other,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_knit_recv_qty_other,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal_other,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_issue_qty_other,2); ?></p></td>
	                    <td width="" align="right"><p><? echo number_format($tot_woven_stock_other,2); ?></p></td>
	                </tr>
	            <?
	            $k++;
				$total_order_qnty_other+=$rows[csf("order_quantity_set")];
				$total_woven_req_qty_other+=$woven_req_qty_other;
				$total_woven_recv_qty_other+=$tot_knit_recv_qty_other;
				$total_woven_recv_bal_other+=$tot_woven_recv_bal_other;
				$total_woven_stock_other+=$tot_woven_stock_other;
				$total_woven_issue_qty_other+=$tot_woven_issue_qty_other;
				}
				?>
	            </table>
	            <table width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty_other,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_req_qty_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_bal_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty_other,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_stock_other,2,'.',''); ?></th>
	                </tfoot>
	            </table>
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
	echo "$total_data####$filename####$type_id";

    exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$type_id=str_replace("'","",$type);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$book_no=str_replace("'","",$txt_book_no);
	$book_id=str_replace("'","",$txt_book_id);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no_id=str_replace("'","",$txt_order_id);
	$order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$job_year=str_replace("'","",$cbo_year);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);

	$shipment_status_cond='';
	 if($cbo_shipment_status==1) //Running Order Balance Qty
	{
		$shipment_status_cond=" and  b.shiping_status <> 3 ";
	}
	else if($cbo_shipment_status==2) //Fully Shipped
	{
		$shipment_status_cond=" and b.shiping_status=3 ";
	}
	if($cbo_company_id==0 && $buyer_id==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.</span>";
		die;
	}

	if($cbo_report_type){
		if($cbo_company_id !=0) $companycond="and f.company_id=$cbo_company_id"; else $companycond = "";
	}


	if($cbo_report_type == 3){
		if($cbo_company_id !=0) $companycond="and a.company_name=$cbo_company_id"; else $companycond = "";
	}

	if($buyer_id==0)
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
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	//echo $buyer_id_cond; die;
	//echo $buyer_id_cond;die;$order_no=str_replace("'","",$txt_order_no);
	if($txt_file_no!="" || $txt_file_no!=0) $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($txt_ref_no!="") $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond="and YEAR(a.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and f.booking_no_prefix_num='$book_no'";

	if(str_replace("'","",$txt_order_id)!="")  $order_cond=" and b.id in ($order_no_id)"; else $order_cond="";
	if(str_replace("'","",$txt_order_no)!="") $order_cond.=" and b.po_number in ('$order_no')"; else $order_cond="";
	//else $order_cond='';
	//echo $order_cond;die;
	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="" && empty($shipment_status_cond)) $ship_date=""; else $ship_date= "and b.pub_shipment_date>=".$txt_date_from."";
	//if( $date_from=="") $receive_date=""; else $receive_date= " and d.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_style)!="") $style_cond="and a.style_ref_no=$txt_style"; else $style_cond="";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	//==================================================
	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		if(str_replace("'","",$cbo_presantation_type)==1)
		{
		$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond $shipment_status_cond");// and a.season like '$txt_season'

		$po_array=array(); $all_po_id='';
		$style_array=array(); $all_style_id='';
		$job_array=array(); $all_job_id='';
		$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
		$shipdate_array=array();
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			$style_array[$row[csf('id')]]=$row[csf('style')];
			$file_array[$row[csf('id')]]=$row[csf('file_no')];
			$ref_array[$row[csf('id')]]=$row[csf('grouping')];
			$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
			$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		} //echo $all_po_id;die;
		if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_breakdown_id in($ids) or";
				$po_cond_for_in2.=" a.po_break_down_id in($ids) or";
				$po_cond_for_in3.=" d.po_break_down_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
			$po_cond_for_in2=" and a.po_break_down_id in($poIds)";
			$po_cond_for_in3=" and d.po_break_down_id in($poIds)";
		}

		//gsm_weight
		$knit_fin_recv_array=array();

		$sql_recv=sql_select("select b.po_breakdown_id, b.color_id, c.body_part_id,c.gsm as gsm_weight, c.rack_no, c.shelf_no, sum(b.quantity) AS finish_receive_qnty, sum(b.grey_used_qty) AS grey_used_qnty
		from inv_receive_master a,order_wise_pro_details b, pro_finish_fabric_rcv_dtls c
		where a.id=c.mst_id and c.id=b.dtls_id  and a.item_category=2  and a.entry_form in (7,37,66,68,225) and b.entry_form in (7,37,66,68,225) and b.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id $po_cond_for_in group by b.po_breakdown_id, b.color_id, c.gsm,c.body_part_id, c.rack_no, c.shelf_no");
	//	echo "select b.po_breakdown_id, b.color_id, c.body_part_id,c.gsm as gsm_weight, c.rack_no, c.shelf_no, sum(b.quantity) AS finish_receive_qnty
	//	from inv_receive_master a,order_wise_pro_details b, pro_finish_fabric_rcv_dtls c
		//where a.id=c.mst_id and c.id=b.dtls_id  and a.item_category=2  and a.entry_form in (7,37,66,68,225) and b.entry_form in (7,37,66,68,225) and b.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id $po_cond_for_in group by b.po_breakdown_id, b.color_id, c.gsm,c.body_part_id, c.rack_no, c.shelf_no";
		foreach($sql_recv as $row)
		{
			$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]]['grey_used_qnty']+=$row[csf('grey_used_qnty')];
			$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]]['rack']=$row[csf('rack_no')];
			$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]]['shelf']=$row[csf('shelf_no')];
		}
		unset($sql_recv);

		//print_r($fin_recv_array);die; inv_issue_master
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b,inv_issue_master c  where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) $po_cond_for_in group by b.po_breakdown_id, b.color_id, a.body_part_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}
		unset($sql_issue);

		$finish_fab_data=array();
		$sql_recv=sql_select("select b.po_breakdown_id, b.color_id,
		sum(CASE WHEN b.entry_form in (46) THEN b.quantity END) AS receive_return,
		sum(CASE WHEN b.entry_form in (52) THEN b.quantity END) AS issue_return,
		sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=5 THEN b.quantity END) AS transfer_in,
		sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=6 THEN b.quantity END) AS transfer_out
		from order_wise_pro_details b
		where b.entry_form in (46,52,14,15) and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id");
		foreach($sql_recv as $row)
		{
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
			 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
		}
		unset($sql_recv);
		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1 $po_cond_for_in2 group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		unset($sql_actual_cut_qty);
		$dia_array=array();
		$color_dia_array=sql_select( "select a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.color_number_id,a.dia_width from  wo_pre_cos_fab_co_avg_con_dtls a where a.status_active=1 $po_cond_for_in2");
		foreach($color_dia_array as $row)
		{
			$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
		} //var_dump($dia_array);
		unset($color_dia_array);
		$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
		$plan_cut_array=array();
		$sql_plan=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id, sum(case when  d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty, sum(case when d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls d, wo_po_color_size_breakdown c WHERE a.job_no=c.job_no_mst and d.is_short=2 and a.id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id=c.po_break_down_id and d.job_no=c.job_no_mst and c.id=d.color_size_table_id    and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");

		foreach( $sql_plan as $row){
			$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
		}
		unset($sql_plan);


		$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id,sum(case when d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty,sum(case when  d.is_short=2 and  e.item_category=2 then d.fin_fab_qnty else 0 end) as other_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and e.job_no=a.job_no  and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $sql_booking as $row_book){
			$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
			$tot_req_qty_other=$row_book[csf('other_fin_fab_qnty')];
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['other_fin_fab_qnty']=$tot_req_qty_other;;
		}
			unset($sql_booking);
		$short_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id, sum(case when  d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty,sum(case when  d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e  WHERE a.job_no=d.job_no and a.job_no=e.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no  and  d.po_break_down_id in($all_po_id) and a.fab_nature_id=2  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $short_booking as $row){
			$tot_short_req_qty=$row[csf('short_fin_fab_qnty')];
			$tot_short_req_qty_other=$row[csf('short_fin_fab_qnty_other')];
			$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty']=$tot_short_req_qty;
			$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty_other']=$tot_short_req_qty_other;
		}
		unset($short_booking);

		?>
    	<fieldset style="width:2170px;">
        <table cellpadding="0" cellspacing="0" width="2160">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
            </tr>
        </table>
        <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
		<table width="2160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job</th>
                <th width="100">Buyer</th>
                <th width="90">Order</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="100">Style</th>
                <th width="100">Body Part</th>
                <th width="80">Color Type</th>
                <th width="120">F.Construction</th>
                <th width="120">F.Composition</th>
                <th width="45">GSM</th>
                <th width="40"><p>Fab.Dia</p></th>
                <th width="70"><p>Rack</p></th>
                <th width="70"><p>Shelf</p></th>
                <th width="75">Ship Date</th>
                <th width="80">Order Qty(Pcs)</th>
                <th width="110">Color</th>
                <th width="80">Req. Qty</th>
                <th width="80">Total Recv.</th>
                <th width="80">Total Grey Used.</th>
                <th width="80">Recv. Balance</th>
                <th width="80">Total Issued</th>
                <th width="80">Stock</th>
                <th width="50">Cons/Pcs</th>
                <th width="80">Possible Cut Pcs</th>
                <th>Actual Cut</th>
            </thead>
        </table>
        <div style="width:2180px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="2160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
            <?
				//and c.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)
				//$all_po_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
				 $sql_main="Select d.po_break_down_id as po_id, f.buyer_id as buyer_name, f.job_no, d.fabric_color_id as color_id, c.body_part_id, c.id as pre_cost_fab_dtls_id, c.avg_finish_cons, c.construction, c.gsm_weight, c.composition, c.color_type_id, f.booking_no_prefix_num, d.booking_type
				from  wo_po_details_master a, wo_pre_cost_fabric_cost_dtls  c, wo_booking_mst f, wo_booking_dtls d
				where a.job_no=c.job_no and c.job_no=d.job_no and d.booking_no=f.booking_no and c.id=d.pre_cost_fabric_cost_dtls_id  and f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.booking_type=1 $companycond  and c.fab_nature_id=2  $booking_no_cond $style_cond $po_cond_for_in3 group by  d.po_break_down_id,c.body_part_id,f.buyer_id,f.job_no,c.avg_finish_cons, d.fabric_color_id,f.booking_no_prefix_num,d.booking_type,c.gsm_weight,c.id,c.body_part_id,c.construction,c.composition,c.color_type_id order by f.buyer_id,f.job_no,d.fabric_color_id";

				$nameArray=sql_select($sql_main );

				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				unset($sql_rack);
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}
				unset($sql_shelf);

				$i=1;
				//$nameArray=sql_select($sql_main );
				$jobNo_array=array();
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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$ship_date=$shipdate_array[$row[csf('po_id')]];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					//echo $row[csf("avg_finish_cons")].'='.$row[csf("color_number_avg")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;
					$fin_rack=$knit_fin_recv_array[$order_id][$body_part_id][$color_id][$row[csf("gsm_weight")]]['rack'];
					$fin_shelf=$knit_fin_recv_array[$order_id][$body_part_id][$color_id][$row[csf("gsm_weight")]]['shelf'];

					//echo $row[csf("avg_finish_cons")];
					//$req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$tot_short_req_qty=$short_booking_qnty[$order_id][$body_part_id][$color_id]['short_fin_fab_qnty'];
					$reg_booking_qty=$booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']+$tot_short_req_qty;
					$tot_order_qty_pcs=$plan_cut_array[$order_id][$body_part_id][$color_id]['plan_cut_qnty'];
					$knit_recv_qty=$knit_fin_recv_array[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]][$row[csf("gsm_weight")]]['receive_qnty'];
					$knit_grey_used_qnty=$knit_fin_recv_array[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]][$row[csf("gsm_weight")]]['grey_used_qnty'];
					$knit_issue_qty=$issue_qnty[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]]['issue_qnty'];

					$knit_recv_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['receive_return'];
					$knit_issue_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['issue_return'];
					$knit_transfer_in_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_in'];
					$knit_transfer_out_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_out'];

					$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
					//echo $knit_recv_qty.'ddd'.$knit_transfer_in_qty.'ddd'.$knit_issue_return_qty.'<br>';
					$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;

					$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);

					$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
					$tot_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];

					$dia=$dia_array[$row[csf('pre_cost_fab_dtls_id')]][$order_id][$color_id]['dia'];
					$style_id=$style_array[$order_id];
					$job_prefix_no=$job_array[$order_id];
					$possible_cut=$tot_knit_recv_qty/$tot_cons_avg;


                    if(!in_array($row[csf('job_no')],$jobNo_array))
						{
								if($i!=1)
								{
											?>
											<tr class="tbl_bottom">
												<td colspan="16" align="right"><b>Job Wise Total</b></td>
												<td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
												<td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                                <td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>

												<td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
												<td align="right"><? echo number_format($tot_sub_knit_grey_used_qnty,2,'.',''); ?></td>
												<td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
												<td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
												<td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
												<td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
                                                <td align="right"><? echo number_format($tot_sub_possible_cut,2,'.',''); ?></td>
                                                <td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>

											</tr>
											<?
											unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
											unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
											unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
											unset($tot_sub_recv_bal);unset($tot_sub_issue_qty);
											unset($tot_sub_tot_stock);unset($tot_sub_knit_grey_used_qnty);
								}

									$jobNo_array[$i]=$row[csf('job_no')];
					   }
									$tot_sub_order_qty_pcs+=$tot_order_qty_pcs;
									$tot_sub_reg_booking_qty+=$reg_booking_qty;
									$tot_sub_knit_recv_qty+=$tot_knit_recv_qty;
									$tot_sub_recv_bal+=$tot_recv_bal;
									$tot_sub_issue_qty+=$tot_knit_issue_qty;
									$tot_sub_tot_stock+=$tot_stock;
									$tot_sub_tot_cons_avg+=$tot_cons_avg;
									$tot_sub_tot_actual_cut_qty+=$tot_actual_cut_qty;
									$tot_sub_knit_grey_used_qnty+=$knit_grey_used_qnty;
									?>

					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo $job_prefix_no; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
						<td width="90"><div style="word-break:break-all"><? echo $order_arr[$order_id]; ?></div></td>
						<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id]; ?></div></td>
						<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id]; ?></div></td>
						<td width="100"><p><? echo $style_id; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
						<td width="80"><p><? echo $color_type[$row[csf("color_type_id")]]; ?></p></td>
						<td width="120"><p><? echo $row[csf("construction")]; ?></p></td>
						<td width="120"><p><?  echo $row[csf("composition")]; ?></p></td>
						<td width="45"><p><? echo $row[csf("gsm_weight")]; ?></p></td>
						<td width="40"><p><? echo $dia; ?></p></td>
						<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
						<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
						<td width="75" align="right"><p><? echo change_date_format($ship_date); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_order_qty_pcs,0); ?></p></td>
						<td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
						<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
						<td width="80" align="right" title="PO=<? echo $order_id.' Body='.$row[csf("body_part_id")].' Color='.$row[csf("color_id")];?>"<p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('gsm_weight')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
						<td width="80" align="right" title="PO=<? echo $order_id.' Body='.$row[csf("body_part_id")].' Color='.$row[csf("color_id")];?>"<p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('gsm_weight')]; ?>','grey_used_receive_popup');"><? echo number_format($knit_grey_used_qnty,2);?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('gsm_weight')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
						<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
						<td width="80" align="right"><p><?  echo number_format($possible_cut); ?></p></td>
						<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
					</tr>
				<?
				$i++;
				//$total_order_qnty+=$row[csf("order_quantity_set")];
				$total_recv_qty+=$tot_knit_recv_qty;
				$total_issue_qty+=$tot_knit_issue_qty;
				$total_balance_qty+=$tot_recv_bal;
				$total_stock_qty+=$tot_stock;
				$total_actual_cut_qty+=$tot_actual_cut_qty;
				$total_reg_booking_qty+=$reg_booking_qty;
				$cons_per+=$tot_cons_avg;
				$total_qty_pcs+=$tot_order_qty_pcs;
				$tot_possible_cut+=$possible_cut;
				$tot_knit_grey_used_qnty+=$knit_grey_used_qnty;
				}
				?>
				</table>
				<table width="2160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="90"></th>
						<th width="70"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="120"></th>
						<th width="45"></th>
						<th width="40"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs); ?></th>
						<th width="80" id="total_order_qnty" align="right" id=""><? echo number_format($total_qty_pcs); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80"  id="total_req_qty" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
						<th width="80" id="total_rec_qty" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
						<th width="80" id="total_grey_used_qty" align="right"><? echo number_format($tot_knit_grey_used_qnty,2,'.',''); ?></th>
						<th width="80" id="total_rec_bal" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
						<th width="80" id="total_issue_qty" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" id="total_stock" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
						<th width="50" align="right"><? echo number_format($cons_per); ?></th>
						<th width="80" id="total_actual_cut_qty" align="right"><? echo number_format($tot_possible_cut); ?></th>
						<th width=""  id="total_actual_cut_qty"   align="right"><? echo number_format($total_actual_cut_qty); ?></th>
					</tfoot>
				</table>
			</div>
			 </fieldset>

		<?
		}
		else
		{
			//echo "select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond ";
			$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $year_cond $style_cond $shipment_status_cond");// and a.season like '$txt_season'

			$job_array=array(); $job_data_arr=array(); $all_po_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$job_array[$row[csf('id')]]['pDate']=$row[csf('pub_shipment_date')];
				$job_data_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$job_data_arr[$row[csf('job_no')]]['style']=$row[csf('style')];
				$job_data_arr[$row[csf('job_no')]]['jobPre']=$row[csf('job_no_prefix_num')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
			} //echo $all_po_id;die;
			if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_breakdown_id in($ids) or";
				$po_cond_for_in2.=" a.po_break_down_id in($ids) or";
				$po_cond_for_in3.=" d.po_break_down_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
			$po_cond_for_in2=" and a.po_break_down_id in($poIds)";
			$po_cond_for_in3=" and d.po_break_down_id in($poIds)";
		}

			unset($poDataArray);
			$knit_fin_recv_array=array();
			$sql_recv=sql_select("select b.po_breakdown_id, b.color_id, c.body_part_id, c.rack_no, c.shelf_no, sum(b.quantity) AS finish_receive_qnty
		from order_wise_pro_details b, pro_finish_fabric_rcv_dtls c
		where c.id=b.dtls_id and b.entry_form in (7,37,66,68,225) and b.trans_id!=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id, c.body_part_id, c.rack_no, c.shelf_no");

			//$sql_recv=sql_select("select a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no, sum(a.quantity) AS finish_receive_qnty
			//from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
			//where b.id=a.dtls_id and a.entry_form in (7,37,66,68) and a.trans_id!=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no");
			foreach($sql_recv as $row)
			{
				$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
				if($row[csf('rack_no')]!='')
				{
					$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
				}
				if($row[csf('shelf_no')]!=0)
				{
					if($row[csf('shelf_no')]!='')
					{
						$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
					}
				}
			}
			unset($sql_recv);

			$issue_qnty=array();

			$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b, inv_issue_master c where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) $po_cond_for_in group by b.po_breakdown_id, b.color_id, a.body_part_id");
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
			}
			unset($sql_issue);

			$finish_fab_data=array();
			$sql_retTrns=sql_select("select b.po_breakdown_id, b.color_id,
			sum(CASE WHEN b.entry_form in (46) THEN b.quantity END) AS receive_return,
			sum(CASE WHEN b.entry_form in (52) THEN b.quantity END) AS issue_return,
			sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=5 THEN b.quantity END) AS transfer_in,
			sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=6 THEN b.quantity END) AS transfer_out
			from order_wise_pro_details b
			where b.entry_form in (46,52,14,15) and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id");
			foreach($sql_recv as $row)
			{
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
			}
			unset($sql_retTrns);

			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);
			$dia_array=array();
			$color_dia_array=sql_select( "select a.po_break_down_id, a.pre_cost_fabric_cost_dtls_id, a.color_number_id, a.dia_width from wo_pre_cos_fab_co_avg_con_dtls a where  a.po_break_down_id!=0 $po_cond_for_in2");

			foreach($color_dia_array as $row)
			{
				$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
			} //var_dump($dia_array);
			unset($color_dia_array);

			//$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
			$plan_cut_array=array();
			$sql_plan=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id, sum(case when  d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty, sum(case when d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls d, wo_po_color_size_breakdown c WHERE a.job_no=c.job_no_mst and d.is_short=2 and a.id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id=c.po_break_down_id and d.job_no=c.job_no_mst and c.id=d.color_size_table_id    and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
			foreach( $sql_plan as $row)
			{

				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
			}
			unset($sql_plan);
			$sql_booking="select a.id as pre_cost_fab_dtls_id, a.avg_finish_cons, a.construction, a.composition, a.gsm_weight, a.color_type_id, a.body_part_id, b.booking_no_prefix_num, d.po_break_down_id, b.buyer_id as buyer_name, d.job_no, b.is_short, d.fabric_color_id, d.fin_fab_qnty
			FROM  wo_booking_mst b, wo_booking_dtls d,wo_pre_cost_fabric_cost_dtls a
			WHERE  b.booking_no=d.booking_no and a.job_no=d.job_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id!=0 and b.is_short in (1,2) and a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3";

		//	echo $sql_booking; die;
			$sql_booking_res=sql_select($sql_booking); $booking_data_arr=array(); $booking_dataOther_arr=array(); $book_qty_arr=array(); $book_otrQty_arr=array();
			foreach( $sql_booking_res as $row)
			{
				$aa='';
				$aa=$row[csf('body_part_id')];
				if($aa==1 || $aa==14 || $aa==15 || $aa==16 || $aa==17 || $aa==20)
				{
					$booking_data_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';
					if($row[csf('is_short')]==2)
					{
						$book_qty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_qty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}

					$booking_data_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.$row[csf('pre_cost_fab_dtls_id')].'__';
				}
				else
				{
					$booking_dataOther_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';

					if($row[csf('is_short')]==2)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}//pre_cost_fab_dtls_id

					$booking_dataOther_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.$row[csf('pre_cost_fab_dtls_id')].'__';
				}
			}
			unset($sql_booking_res);
			?>
            <fieldset style="width:2090px;">
                <table cellpadding="0" cellspacing="0" width="2080">
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?> (Style/ Job Wise)</strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
                    </tr>
                </table>
                <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
                <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Job</th>
                        <th width="100">Buyer</th>
						<th width="100">Style</th>
                        <th width="90">Order</th>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Body Part</th>
                        <th width="80">Color Type</th>
                        <th width="120">F.Construction</th>
                        <th width="120">F.Composition</th>
                        <th width="45">GSM</th>
                        <th width="40"><p>Fab.Dia</p></th>
                        <th width="70"><p>Rack</p></th>
                        <th width="70"><p>Shelf</p></th>
                        <th width="75">Ship Date</th>
                        <th width="80">Order Qty(Pcs)</th>
                        <th width="110">Color</th>
                        <th width="80">Req. Qty</th>
                        <th width="80">Total Recv.</th>
                        <th width="80">Recv. Balance</th>
                        <th width="80">Total Issued</th>
                        <th width="80">Stock</th>
                        <th width="50">Cons/Pcs</th>
                        <th width="80">Possible Cut Pcs</th>
                        <th>Actual Cut</th>
                    </thead>
                </table>
                <div style="width:2100px; max-height:350px; overflow-y:scroll;" id="scroll_body">
                    <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
                    <?

				$i=1;
				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				unset($sql_rack);
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}
				unset($sql_shelf);$jobNo_array=array();
				$total_qty_pcs=$total_recv_qty=$total_issue_qty=$total_balance_qty=$total_stock_qty=$total_actual_cut_qty=$total_reg_booking_qty=$tot_possible_cut=$total_actual_cut_qty=0;
				foreach($booking_data_arr as $job_no=>$job_data)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$dzn_qnty=0;
					if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
					else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
					else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
					else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					//echo $dzn_qnty.'='.$row[csf("cons")];

					$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';

					$poIds=array_filter(array_unique(explode(',',$job_data['po'])));
					foreach($poIds as $po_id)
					{
						if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
						if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
						if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
						if($ref=='') $ref=$job_array[$po_id]['ref']; else $ref.=', '.$job_array[$po_id]['ref'];
					}
					$file_no=implode(', ',array_unique(explode(', ',$file)));
					$po_no=implode(', ',array_unique(explode(', ',$order_no)));
					$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
					$intRef=implode(', ',array_unique(explode(', ',$ref)));

					$style_ref=''; $buyer_name=''; $job_pre='';
					$style_ref=$job_data_arr[$job_no]['style'];
					$buyer_name=$buyer_arr[$job_data_arr[$job_no]['buyer']];
					$job_pre=$job_data_arr[$job_no]['jobPre'];

					$other_data=array_filter(array_unique(explode('__',$job_data['all_data'])));
					foreach($other_data as $book_data)
					{
						$data_ex='';
						$data_ex=explode('##',$book_data);

						$body_part_id=''; $color_type_id=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

						$body_part_id=$data_ex[0];
						$color_type_id=$data_ex[1];
						//echo $body_part_id.'='.$color_type_id.'<br>';
						$construction=$data_ex[2];
						$composition=$data_ex[3];
						$gsm_weight=$data_ex[4];
						$color_id=$data_ex[5];
						$avg_finish_cons=$data_ex[6];
						$booking_no_pre=$data_ex[7];
						$pre_cost_id=$data_ex[8];
						//echo $booking_no_pre.'='.$pre_cost_id.'<br>';

						$main_fin_qty=$book_qty_arr[$job_no][$body_part_id][$color_type_id][$construction][$composition][$gsm_weight][$color_id]['main'];
						$short_qty=$book_qty_arr[$job_no][$body_part_id][$color_type_id][$construction][$composition][$gsm_weight][$color_id]['short'];
						//$short_othQty=$data_ex[11];$main_fin_othQty=$data_ex[9];

						$reg_booking_qty=0;
						$reg_booking_qty=$main_fin_qty+$short_qty;
						$tot_cons_avg=$avg_finish_cons/$dzn_qnty;


						$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
						foreach($poIds as $po_id)
						{
							$rack=''; $shelf='';
							$rack=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['rack'];
							if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
							$shelf=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['shelf'];
							if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
							$plan_cut_qty+=$plan_cut_array[$po_id][$body_part_id][$color_id]['plan_cut_qnty'];

							$knit_recv_qty+=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['receive_qnty'];
							$knit_issue_qty+=$issue_qnty[$po_id][$body_part_id][$color_id]['issue_qnty'];
							//echo $knit_recv_qty.'ddd';
							$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
							$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
							$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
							$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

							$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

							$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
						}
						$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
						$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;
						$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
						$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
						$possible_cut=$tot_knit_recv_qty/$tot_cons_avg;
						//echo $job_no.'SS';

						 if(!in_array($job_no,$jobNo_array))
						 {
									if($i!=1)
									{
											?>
                                    <tr class="tbl_bottom">
                                        <td colspan="16" align="right"><b>Job Wise Total</b></td>
                                        <td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
                                        <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_possible_cut,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>
                                    </tr>
											<?
									unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
									unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_recv_bal);
									unset($tot_sub_issue_qty);
									unset($tot_sub_tot_stock);
									}

									$jobNo_array[$i]=$job_no;
					 		 }
									$tot_sub_order_qty_pcs+=$plan_cut_qty;
									$tot_sub_reg_booking_qty+=$reg_booking_qty;
									$tot_sub_knit_recv_qty+=$tot_knit_recv_qty;
									$tot_sub_recv_bal+=$tot_recv_bal;
									$tot_sub_issue_qty+=$tot_knit_issue_qty;
									$tot_sub_tot_stock+=$tot_stock;
									$tot_sub_tot_cons_avg+=$tot_cons_avg;
									$tot_sub_tot_actual_cut_qty+=$tot_actual_cut_qty;

						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><? echo $job_pre; ?></td>
								<td width="100"><p><? echo $buyer_name; ?></p></td>
								<td width="100"><p><? echo $style_ref; ?></p></td>
								<td width="90"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
								<td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>
								<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
								<td width="80"><p><? echo $color_type[$color_type_id]; ?></p></td>
								<td width="120"><p><? echo $construction; ?></p></td>
								<td width="120"><p><?  echo $composition; ?></p></td>
								<td width="45"><p><? echo $gsm_weight; ?></p></td>
								<td width="40"><p><? echo $dia; ?></p></td>
								<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
								<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
								<td width="75"><p><? echo $ship_date; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
								<td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
								<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
								<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($possible_cut); ?></p></td>
								<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
							</tr>
						<?
						$i++;
						$total_order_qnty+=$plan_cut_qty;
						$total_recv_qty+=$tot_knit_recv_qty;
						$total_issue_qty+=$tot_knit_issue_qty;
						$total_balance_qty+=$tot_recv_bal;
						$total_stock_qty+=$tot_stock;
						$total_actual_cut_qty+=$tot_actual_cut_qty;
						$total_reg_booking_qty+=$reg_booking_qty;
						$cons_per+=$tot_cons_avg;
						$total_qty_pcs+=$plan_cut_qty;
						$tot_possible_cut+=$possible_cut;
					}
				}
				?>
				</table>
				<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="75">Total</th>
						<th width="80" align="right"><? echo number_format($total_qty_pcs); ?></th>
						<th width="110">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
						<th width="50" align="right"><? echo number_format($cons_per,4); ?></th>
						<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
						<th align="right"><? echo number_format($total_actual_cut_qty); ?></th>
					</tfoot>
				</table>
			</div>

			 <br/>

		<?
		}
	}//Knit end
	else if($cbo_report_type==3) // Woven Finish Start
	{
		//echo "select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond"; die;
		$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond $shipment_status_cond");

		$po_array=array(); $all_po_id='';
		$style_array=array(); $all_style_id='';
		$job_array=array(); $all_job_id='';
		$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
		$shipdate_array=array();$shipdate_array='';
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			$style_array[$row[csf('id')]]=$row[csf('style')];
			$file_array[$row[csf('id')]]=$row[csf('file_no')];
			$ref_array[$row[csf('id')]]=$row[csf('grouping')];
			$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
			$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}
		if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_breakdown_id in($ids) or";
				$po_cond_for_in2.=" a.po_break_down_id in($ids) or";
				$po_cond_for_in3.=" d.po_break_down_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
			$po_cond_for_in2=" and a.po_break_down_id in($poIds)";
			$po_cond_for_in3=" and d.po_break_down_id in($poIds)";
		}

		$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id,sum(case when  d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_booking_mst e WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no  and a.fab_nature_id=3 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $sql_booking as $row_book){
			$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
		}
	unset($sql_booking);
		$fin_woven_recv_array=array();

		$sql_recv=sql_select("select b.id as prop_id,b.po_breakdown_id,b.color_id,b.quantity as finish_receive_qnty, f.body_part_id from inv_receive_master a,product_details_master c, order_wise_pro_details b ,inv_transaction f where a.entry_form=b.entry_form and  c.id=b.prod_id and b.entry_form in (17) and b.trans_id <> 0 and a.item_category=3 and a.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.id = b.trans_id $po_cond_for_in");


		foreach($sql_recv as $row)
		{
			if($chk_prop_id[$row[csf('prop_id')]]  == "")
			{
				$chk_prop_id[$row[csf('prop_id')]] = $row[csf('prop_id')];
				$fin_woven_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			}

		}
		unset($sql_recv);
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id,  a.body_part_id, b.color_id,sum(b.quantity) as issue_qnty from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.id = b.trans_id and a.mst_id = c.id and c.entry_form in (19) and b.trans_id <> 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $po_cond_for_in group by b.po_breakdown_id, a.body_part_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
		$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}
		unset($sql_issue);
		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  $po_cond_for_in2 group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		unset($sql_actual_cut_qty);
		?>
	    <fieldset style="width:1330px;">
	        <table cellpadding="0" cellspacing="0" width="1330">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?><br><? $report_type=(str_replace("'","",$cbo_report_type)); if($report_type==2) echo 'Woven Finish'; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	            </tr>
	        </table>
	        <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
			<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="80">Stock</th>
	                <th width="80">Cons/Pcs</th>
	                <th width="80">Possible Cut Pcs</th>
	                <th width="">Actual Cut</th>
	            </thead>
	        </table>
	        <div style="width:1350px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	            <?
						$sql_main="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id  and c.fab_nature_id=3 $ship_date $buyer_id_cond $job_no_cond $order_cond $style_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id order by a.buyer_name,b.id,a.job_no, b.po_number";
				//echo $sql_main;die;
				$i=1;
				$nameArray=sql_select( $sql_main );
				$jobNo_array=array();$total_woven_req_qty=$total_woven_recv_qty=$total_woven_balance_qty=$total_woven_issue_qty=$total_woven_stock_qty=$total_woven_actual_cut_qty=$total_woven_possible_cut=0;
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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;

					//$woven_req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$woven_req_qty = ceil($booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']);
					$woven_recv_qty=$fin_woven_recv_array[$order_id][$body_part_id][$color_id]['receive_qnty'];
					//$woven_recv_return_qty=$fin_woven_recv_array[$order_id][$color_id]['receive_return'];
					$tot_woven_recv_qty=$woven_recv_qty;
					$tot_woven_recv_bal=$woven_req_qty-$tot_woven_recv_qty;
					$tot_woven_issue_qty=$issue_qnty[$order_id][$body_part_id][$color_id]['issue_qnty'];
					$tot_woven_stock=$tot_woven_recv_qty-$tot_woven_issue_qty;
					$tot_woven_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];
					 $possible_cut=$tot_knit_issue_qty/($tot_cons_avg/$dzn_qnty);
					if(!in_array($row[csf("job_no")],$jobNo_array))
						 {
									if($i!=1)
									{
											?>
                                    <tr class="tbl_bottom">
                                        <td colspan="6" align="right"><b>Job Wise Total</b></td>
                                        <td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
                                        <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
                                         <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>
                                    </tr>
											<?
									unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
									unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_recv_bal);
									unset($tot_sub_issue_qty);
									unset($tot_sub_tot_stock);
									}

									$jobNo_array[$i]=$row[csf("job_no")];
					 		 }
									$tot_sub_order_qty_pcs+=$row[csf("order_quantity_set")];
									$tot_sub_reg_booking_qty+=$woven_req_qty;
									$tot_sub_knit_recv_qty+=$tot_woven_recv_qty;
									$tot_sub_recv_bal+=$tot_woven_recv_bal;
									$tot_sub_issue_qty+=$tot_woven_issue_qty;
									$tot_sub_tot_stock+=$tot_woven_stock;
									$tot_sub_tot_cons_avg+=$tot_cons_avg;
									$tot_sub_tot_actual_cut_qty+=$tot_woven_actual_cut_qty;

					?>
	                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
	                    <td width="90"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $order_arr[$order_id]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf("order_quantity_set")],2,'.',''); ?></p></td>
	                    <td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($woven_req_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_qty,2);?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_issue_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_stock,2); ?></p></td>
	                    <td width="80" align="right"><p><?  echo number_format($tot_cons_avg,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($possible_cut,2); ?></p></td>
	                    <td width="" align="right"><p><? echo number_format($tot_woven_actual_cut_qty,2);?></p></td>
	                </tr>
	            <?
	            $i++;
				$total_order_qnty+=$row[csf("order_quantity_set")];
				$total_woven_recv_qty+=$tot_woven_recv_qty;
				$total_woven_req_qty+=$woven_req_qty;
				$total_woven_issue_qty+=$tot_woven_issue_qty;
				$total_woven_balance_qty+=$tot_woven_recv_bal;
				$total_woven_stock_qty+=$tot_woven_stock;
				$total_woven_possible_cut+=$possible_cut;
				$total_woven_actual_cut_qty+=$tot_woven_actual_cut_qty;
				}
				?>
	            </table>
	            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_req_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_balance_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_stock_qty,2,'.',''); ?></th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_possible_cut,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_actual_cut_qty,2,'.',''); ?></th>
	                </tfoot>
	            </table>
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
	echo "$total_data####$filename####$type_id";

    exit();
}

if($action=="report_generate3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$type_id=str_replace("'","",$type);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$book_no=str_replace("'","",$txt_book_no);
	$book_id=str_replace("'","",$txt_book_id);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no_id=str_replace("'","",$txt_order_id);
	$order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$job_year=str_replace("'","",$cbo_year);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	if($cbo_company_id==0 && $buyer_id==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.</span>";
		die;
	}

	if($cbo_report_type){
		if($cbo_company_id !=0) $companycond="and f.company_id=$cbo_company_id"; else $companycond = "";
	}


	if($cbo_report_type == 3){
		if($cbo_company_id !=0) $companycond="and a.company_name=$cbo_company_id"; else $companycond = "";
	}

	if($buyer_id==0)
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
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	//echo $buyer_id_cond; die;
	//echo $buyer_id_cond;die;$order_no=str_replace("'","",$txt_order_no);
	if($txt_file_no!="" || $txt_file_no!=0) $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($txt_ref_no!="") $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond="and YEAR(a.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and f.booking_no_prefix_num='$book_no'";

	if(str_replace("'","",$txt_order_id)!="")  $order_cond=" and b.id in ($order_no_id)"; else $order_cond="";
	if(str_replace("'","",$txt_order_no)!="") $order_cond.=" and b.po_number in ('$order_no')"; else $order_cond="";
	//else $order_cond='';
	//echo $order_cond;die;
	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $ship_date=""; else $ship_date= "and b.pub_shipment_date>=".$txt_date_from."";
	//if( $date_from=="") $receive_date=""; else $receive_date= " and d.receive_date <=".$txt_date_from."";
	if(str_replace("'","",$txt_style)!="") $style_cond="and a.style_ref_no=$txt_style"; else $style_cond="";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//==================================================
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (661)");
	if($r_id)
	{
		oci_commit($con);
	}
	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		if(str_replace("'","",$cbo_presantation_type)==1)
		{
			$sql_main="SELECT d.po_break_down_id as po_id, f.buyer_id as buyer_name, f.job_no, d.fabric_color_id as color_id, c.id as pre_cost_fab_dtls_id, c.avg_finish_cons, c.construction, c.gsm_weight, c.composition, c.lib_yarn_count_deter_id as deter_id, c.color_type_id, f.booking_no_prefix_num, d.booking_type, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no_prefix_num, a.style_ref_no as style,c.body_part_id
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fabric_cost_dtls c, wo_booking_dtls d, wo_booking_mst f
			where a.id=b.job_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=d.pre_cost_fabric_cost_dtls_id and d.booking_mst_id=f.id
			and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.booking_type=1 and f.company_id=1 and c.fab_nature_id=2 $companycond $booking_no_cond $style_cond $ref_cond $file_cond $order_cond $ship_date $buyer_id_cond $job_no_cond
			group by d.po_break_down_id, f.buyer_id,f.job_no, c.avg_finish_cons, d.fabric_color_id, f.booking_no_prefix_num,d.booking_type, c.gsm_weight, c.id, c.construction, c.composition, c.lib_yarn_count_deter_id, c.color_type_id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no_prefix_num, a.style_ref_no,c.body_part_id
			order by f.buyer_id, f.job_no, d.fabric_color_id";

			//echo $sql_main;

			$nameArray=sql_select($sql_main );
			if(empty($nameArray))
			{
				echo "Data Not Found";
				die;
			}
			foreach ($nameArray as $row)
			{
				$all_po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			}

			$all_po_id_arr = array_filter($all_po_id_arr);
			$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 661, 1,$all_po_id_arr, $empty_arr);//PO ID


			$knit_fin_recv_array=array();
			$sql_recv=sql_select("select b.po_breakdown_id, b.color_id, c.gsm as gsm_weight, c.fabric_description_id, c.width, sum(b.quantity) AS finish_receive_qnty
			from inv_receive_master a,order_wise_pro_details b, pro_finish_fabric_rcv_dtls c, gbl_temp_engine g
			where a.id=c.mst_id and c.id=b.dtls_id  and a.item_category=2  and a.entry_form in (7,37,66,68,225) and b.entry_form in (7,37,66,68,225) and b.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and b.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 and a.company_id=$cbo_company_id  group by b.po_breakdown_id, b.color_id, c.gsm, c.fabric_description_id, c.width");
			//c.body_part_id,
			//$po_cond_for_in


			foreach($sql_recv as $row)
			{
				$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]][$row[csf('fabric_description_id')]][$row[csf('width')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			}
			unset($sql_recv);

			//print_r($fin_recv_array);die; inv_issue_master
			$issue_qnty=array();
			$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, d.detarmination_id, d.gsm, d.dia_width, sum(b.quantity) as issue_qnty from inv_finish_fabric_issue_dtls a, order_wise_pro_details b, inv_issue_master c, product_details_master d, gbl_temp_engine g where a.id=b.dtls_id and c.id=a.mst_id and a.prod_id=d.id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) and b.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=661  group by b.po_breakdown_id, b.color_id, d.detarmination_id, d.gsm, d.dia_width");
			//a.body_part_id,
			//$po_cond_for_in
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]][$row_iss[csf('gsm')]][$row_iss[csf('detarmination_id')]][$row_iss[csf('dia_width')]]['issue_qnty']+=$row_iss[csf('issue_qnty')];
			}
			unset($sql_issue);

			$finish_fab_data=array();
			$sql_recv=sql_select("select b.po_breakdown_id, b.color_id, d.detarmination_id, d.gsm, d.dia_width,
			sum(CASE WHEN b.entry_form in (46) THEN b.quantity END) AS receive_return,
			sum(CASE WHEN b.entry_form in (52) THEN b.quantity END) AS issue_return,
			sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=5 THEN b.quantity END) AS transfer_in,
			sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=6 THEN b.quantity END) AS transfer_out
			from order_wise_pro_details b, , product_details_master d, gbl_temp_engine g
			where b.entry_form in (46,52,14,15) and a.prod_id=d.id and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 group by b.po_breakdown_id, b.color_id, d.detarmination_id, d.gsm, d.dia_width");

			foreach($sql_recv as $row)
			{
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('detarmination_id')]][$row[csf('dia_width')]]['receive_return']+=$row[csf('receive_return')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('detarmination_id')]][$row[csf('dia_width')]]['issue_return']+=$row[csf('issue_return')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('detarmination_id')]][$row[csf('dia_width')]]['transfer_in']+=$row[csf('transfer_in')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('detarmination_id')]][$row[csf('dia_width')]]['transfer_out']+=$row[csf('transfer_out')];
			}
			unset($sql_recv);
			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select("SELECT a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, gbl_temp_engine g where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 group by  a.po_break_down_id,c.color_number_id"); //$po_cond_for_in2
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);

			$dia_array=array();
			$color_dia_array=sql_select( "select a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.color_number_id,a.dia_width from  wo_pre_cos_fab_co_avg_con_dtls a, gbl_temp_engine g where a.status_active=1 and a.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=661");
			//$po_cond_for_in2
			foreach($color_dia_array as $row)
			{
				$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
			}
			unset($color_dia_array);

			$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
			$plan_cut_array=array();
			$sql_plan=sql_select("SELECT d.po_break_down_id,d.fabric_color_id, sum(case when  d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty, sum(case when d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls d, wo_po_color_size_breakdown c, gbl_temp_engine g WHERE a.job_no=c.job_no_mst and d.is_short=2 and a.id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id=c.po_break_down_id and d.job_no=c.job_no_mst and c.id=d.color_size_table_id and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0  and d.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 group by d.po_break_down_id,d.fabric_color_id");  //$po_cond_for_in3
			//,a.body_part_id

			foreach( $sql_plan as $row){
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
			}
			unset($sql_plan);

			$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.lib_yarn_count_deter_id, a.gsm_weight, d.dia_width,sum(case when d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty,sum(case when d.is_short=2 and e.item_category=2 then d.fin_fab_qnty else 0 end) as other_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_booking_mst e, gbl_temp_engine g WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and e.job_no=a.job_no  and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 group by d.po_break_down_id,d.fabric_color_id,a.lib_yarn_count_deter_id, a.gsm_weight, d.dia_width"); //$po_cond_for_in3


			//a.body_part_id,
			foreach( $sql_booking as $row_book)
			{
				$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
				$tot_req_qty_other=$row_book[csf('other_fin_fab_qnty')];
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]][$row_book[csf('lib_yarn_count_deter_id')]][$row_book[csf('gsm_weight')]][$row_book[csf('dia_width')]]['fin_fab_qnty']=$tot_req_qty;
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]][$row_book[csf('lib_yarn_count_deter_id')]][$row_book[csf('gsm_weight')]][$row_book[csf('dia_width')]]['other_fin_fab_qnty']=$tot_req_qty_other;;
			}
				unset($sql_booking);
			$short_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.lib_yarn_count_deter_id, a.gsm_weight, d.dia_width, sum(case when  d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty,sum(case when  d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty_other FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_booking_mst e, gbl_temp_engine g  WHERE a.job_no=d.job_no and a.job_no=e.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and a.fab_nature_id=2  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=661 group by d.po_break_down_id,d.fabric_color_id,a.lib_yarn_count_deter_id, a.gsm_weight, d.dia_width"); //$po_cond_for_in3

			//,a.body_part_id

			foreach( $short_booking as $row)
			{
				$tot_short_req_qty=$row[csf('short_fin_fab_qnty')];
				$tot_short_req_qty_other=$row[csf('short_fin_fab_qnty_other')];
				$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['short_fin_fab_qnty']=$tot_short_req_qty;
				$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['short_fin_fab_qnty_other']=$tot_short_req_qty_other;
			}
			unset($short_booking);

			$costing_per_id_library=return_library_array( "select a.job_no, a.costing_per from wo_pre_cost_mst a, wo_po_details_master b, wo_po_break_down c, gbl_temp_engine d
			where a.job_id=b.ID and b.id=c.job_id and c.id=d.ref_val and d.entry_form=661 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0", "job_no", "costing_per");

			?>
	    	<fieldset style="width:2000px;">
	        <table cellpadding="0" cellspacing="0" width="2000">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	            </tr>
	        </table>
	        <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
			<table width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="80">Job</th>
	                <th width="100">Buyer</th>
	                <th width="90">Order</th>
	                <th width="70">File No</th>
	                <th width="80">Ref. No</th>
	                <th width="100">Style</th>
	                <th width="80">Color Type</th>
	                <th width="120">F.Construction</th>
	                <th width="120">F.Composition</th>
	                <th width="45">GSM</th>
	                <th width="40"><p>Fab.Dia</p></th>
	                <th width="75">Ship Date</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="80">Stock</th>
	                <th width="50">Cons/Pcs</th>
	                <th width="80">Possible Cut Pcs</th>
	                <th>Actual Cut</th>
	            </thead>
	        </table>
	        <div style="width:1960px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	            <?
					$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (661)");
					if($r_id2)
					{
						oci_commit($con);
						disconnect($con);
					}

					$data_array= array();

					foreach ($nameArray as $row)
					{
						$dia=$dia_array[$row[csf('pre_cost_fab_dtls_id')]][$row[csf('po_id')]][$row[csf("color_id")]]['dia'];
						$fabric_str =   $row[csf("deter_id")]."*".$row[csf("gsm_weight")]."*".$dia."*".$row[csf("color_id")];

						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['po_number']=$row[csf("po_number")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['job_no_prefix_num']=$row[csf("job_no_prefix_num")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['buyer_name']=$row[csf("buyer_name")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['style']=$row[csf("style")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['file_no']=$row[csf("file_no")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['grouping']=$row[csf("grouping")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['pub_shipment_date']=$row[csf("pub_shipment_date")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['color_types'].= $color_type[$row[csf("color_type_id")]].",";
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['color_id']=$row[csf("color_id")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['construction']=$row[csf("construction")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['composition']=$row[csf("composition")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['deter_id']=$row[csf("deter_id")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['gsm_weight']=$row[csf("gsm_weight")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['dia_width']=$dia;
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['avg_finish_cons'] +=$row[csf("avg_finish_cons")];
						$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$fabric_str]['body_part_id']=$row[csf("body_part_id")];
					}

					$i=1;
					//$nameArray=sql_select($sql_main );
					$jobNo_array=array();
					foreach ($data_array as $job_no=>$job_data)
					{
						foreach($job_data as $po_id =>$po_data)
						{
							foreach($po_data as $fabStr =>$row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$dzn_qnty=0;
								if($costing_per_id_library[$job_no]==1)
								{
									$dzn_qnty=12;
								}
								else if($costing_per_id_library[$job_no]==3)
								{
									$dzn_qnty=12*2;
								}
								else if($costing_per_id_library[$job_no]==4)
								{
									$dzn_qnty=12*3;
								}
								else if($costing_per_id_library[$job_no]==5)
								{
									$dzn_qnty=12*4;
								}
								else
								{
									$dzn_qnty=1;
								}

								$ship_date=$row['pub_shipment_date'];
								$order_id=$po_id;
								$color_id=$row["color_id"];
								$body_part_id=0;
								$cons_avg=$row["avg_finish_cons"];
								$deter_id=$row["deter_id"];
								$gsm_weight=$row["gsm_weight"];
								$dia_width=$row["dia_width"];
								$tot_cons_avg=$cons_avg/$dzn_qnty;

								$tot_short_req_qty=$short_booking_qnty[$order_id][$color_id][$deter_id][$gsm_weight][$dia_width]['short_fin_fab_qnty'];
								$reg_booking_qty=$booking_qnty[$order_id][$color_id][$deter_id][$gsm_weight][$dia_width]['fin_fab_qnty']+$tot_short_req_qty;
								$tot_order_qty_pcs=$plan_cut_array[$order_id][$color_id]['plan_cut_qnty'];

								$knit_recv_qty=$knit_fin_recv_array[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['receive_qnty'];
								$knit_issue_qty=$issue_qnty[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['issue_qnty'];

								$knit_recv_return_qty=$finish_fab_data[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['receive_return'];
								$knit_issue_return_qty=$finish_fab_data[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['issue_return'];
								$knit_transfer_in_qty=$finish_fab_data[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['transfer_in'];
								$knit_transfer_out_qty=$finish_fab_data[$order_id][$color_id][$gsm_weight][$deter_id][$dia_width]['transfer_out'];

								$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
								//echo $knit_recv_qty.'ddd'.$knit_transfer_in_qty.'ddd'.$knit_issue_return_qty.'<br>';
								$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;

								$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);

								$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
								$tot_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];

								$color_types = implode(",",array_unique(explode(",",chop($row["color_types"],','))));
								$style_id=$row['style'];
								$job_prefix_no=$job_array[$order_id];
								$possible_cut=$tot_knit_recv_qty/$tot_cons_avg;

								if(!in_array($job_no,$jobNo_array))
								{
									if($i!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td colspan="13" align="right"><b>Job Wise Total</b></td>
											<td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
											<td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
											<td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>

											<td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
											<td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
											<td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
											<td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
											<td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
											<td align="right"><? echo number_format($tot_sub_possible_cut,2,'.',''); ?></td>
											<td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>

										</tr>
										<?
										unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
										unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
										unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
										unset($tot_sub_recv_bal);
										unset($tot_sub_issue_qty);
										unset($tot_sub_tot_stock);
									}
									$jobNo_array[$i]=$job_no;
							   	}

								$tot_sub_order_qty_pcs+=$tot_order_qty_pcs;
								$tot_sub_reg_booking_qty+=$reg_booking_qty;
								$tot_sub_knit_recv_qty+=$tot_knit_recv_qty;
								$tot_sub_recv_bal+=$tot_recv_bal;
								$tot_sub_issue_qty+=$tot_knit_issue_qty;
								$tot_sub_tot_stock+=$tot_stock;
								$tot_sub_tot_cons_avg+=$tot_cons_avg;
								$tot_sub_tot_actual_cut_qty+=$tot_actual_cut_qty;

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="80"><p><? echo $row["job_no_prefix_num"]; ?></p></td>
									<td width="100"><p><? echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
									<td width="90"><div style="word-break:break-all"><? echo $order_arr[$order_id]; ?></div></td>
									<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id]; ?></div></td>
									<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id]; ?></div></td>
									<td width="100"><p><? echo $style_id; ?></p></td>
									<td width="80"><p><? echo $color_types; ?></p></td>
									<td width="120" title="<? echo $deter_id;?>"><p><? echo $row["construction"]; ?></p></td>
									<td width="120"><p><?  echo $row["composition"]; ?></p></td>
									<td width="45"><p><? echo $row["gsm_weight"]; ?></p></td>
									<td width="40"><p><? echo $dia; ?></p></td>

									<td width="75" align="right"><p><? echo change_date_format($ship_date); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($tot_order_qty_pcs,0); ?></p></td>
									<td width="110"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
									<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $order_id; ?>','<? echo $job_no;?>','<? echo $row['buyer_name']; ?>','<? echo $row['body_part_id']; ?>','<? echo $row['color_id']; ?>','<? echo $row['booking_no_prefix_num']; ?>','<? echo $row['booking_type']; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
									<td width="80" align="right" title="PO=<? echo $order_id.' Body='.$row["body_part_id"].' Color='.$row["color_id"];?>"<p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo $row['body_part_id']; ?>','<? echo $row['color_id']; ?>','<? echo $row['gsm_weight']; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
									<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $order_id; ?>','<? echo $row['body_part_id']; ?>','<? echo $row['color_id']; ?>','<? echo $row['gsm_weight']; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
									<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
									<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
									<td width="80" align="right"><p><?  echo number_format($possible_cut); ?></p></td>
									<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
								</tr>
								<?
								$i++;

								$total_recv_qty+=$tot_knit_recv_qty;
								$total_issue_qty+=$tot_knit_issue_qty;
								$total_balance_qty+=$tot_recv_bal;
								$total_stock_qty+=$tot_stock;
								$total_actual_cut_qty+=$tot_actual_cut_qty;
								$total_reg_booking_qty+=$reg_booking_qty;
								$cons_per+=$tot_cons_avg;
								$total_qty_pcs+=$tot_order_qty_pcs;
								$tot_possible_cut+=$possible_cut;

							}

						}
					}
					?>
					</table>
					<table width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="70"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="45"></th>
							<th width="40"></th>
							<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs); ?></th>
							<th width="80" id="value_total_order_qnty" align="right" id=""><? echo number_format($total_qty_pcs); ?></th>
							<th width="110">&nbsp;</th>
							<th width="80"  id="value_total_req_qty" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
							<th width="80" id="value_total_rec_qty" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
							<th width="80" id="value_total_rec_bal" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
							<th width="80" id="value_total_issue_qty" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="80" id="value_total_stock" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
							<th width="50" align="right"><? echo number_format($cons_per); ?></th>
							<th width="80" id="value_total_possible_cut_pcs" align="right"><? echo number_format($tot_possible_cut); ?></th>
							<th width=""  id="value_total_actual_cut_qty"   align="right"><? echo number_format($total_actual_cut_qty); ?></th>
						</tfoot>
					</table>
				</div>
				 </fieldset>

			<?
		}
		else
		{
			$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
			$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $year_cond $style_cond");// and a.season like '$txt_season'

			$job_array=array(); $job_data_arr=array(); $all_po_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$job_array[$row[csf('id')]]['pDate']=$row[csf('pub_shipment_date')];
				$job_data_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$job_data_arr[$row[csf('job_no')]]['style']=$row[csf('style')];
				$job_data_arr[$row[csf('job_no')]]['jobPre']=$row[csf('job_no_prefix_num')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
			} //echo $all_po_id;die;
			if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$po_cond_for_in3=" and (";

				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_breakdown_id in($ids) or";
					$po_cond_for_in2.=" a.po_break_down_id in($ids) or";
					$po_cond_for_in3.=" d.po_break_down_id in($ids) or";
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
				$po_cond_for_in3=chop($po_cond_for_in3,'or ');
				$po_cond_for_in3.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$all_po_id)));
				$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
				$po_cond_for_in2=" and a.po_break_down_id in($poIds)";
				$po_cond_for_in3=" and d.po_break_down_id in($poIds)";
			}

				unset($poDataArray);
				$knit_fin_recv_array=array();
				$sql_recv=sql_select("select b.po_breakdown_id, b.color_id, c.rack_no, c.shelf_no, sum(b.quantity) AS finish_receive_qnty from order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where c.id=b.dtls_id and b.entry_form in (7,37,66,68,225) and b.trans_id!=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id, c.rack_no, c.shelf_no");
				//c.body_part_id,

				foreach($sql_recv as $row)
				{
					$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
					if($row[csf('rack_no')]!='')
					{
						$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
					}
					if($row[csf('shelf_no')]!=0)
					{
						if($row[csf('shelf_no')]!='')
						{
							$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
						}
					}
				}
				unset($sql_recv);

				$issue_qnty=array();

				$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, sum(b.quantity) as issue_qnty from inv_finish_fabric_issue_dtls a, order_wise_pro_details b, inv_issue_master c where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) $po_cond_for_in group by b.po_breakdown_id, b.color_id");
				//a.body_part_id,

				foreach( $sql_issue as $row_iss )
				{
					$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
				}
				unset($sql_issue);

				$finish_fab_data=array();
				$sql_retTrns=sql_select("select b.po_breakdown_id, b.color_id,
				sum(CASE WHEN b.entry_form in (46) THEN b.quantity END) AS receive_return,
				sum(CASE WHEN b.entry_form in (52) THEN b.quantity END) AS issue_return,
				sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=5 THEN b.quantity END) AS transfer_in,
				sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=6 THEN b.quantity END) AS transfer_out
				from order_wise_pro_details b
				where b.entry_form in (46,52,14,15) and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id");


				foreach($sql_retTrns as $row)
				{
					$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
					$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
					$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
					$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
				}
				unset($sql_retTrns);

				$actual_cut_qnty=array();
				$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
				foreach( $sql_actual_cut_qty as $row_actual)
				{
					$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
				}
				unset($sql_actual_cut_qty);
				$dia_array=array();
				$color_dia_array=sql_select( "select a.po_break_down_id, a.pre_cost_fabric_cost_dtls_id, a.color_number_id, a.dia_width from wo_pre_cos_fab_co_avg_con_dtls a where  a.po_break_down_id!=0 $po_cond_for_in2");

				foreach($color_dia_array as $row)
				{
					$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
				} //var_dump($dia_array);
				unset($color_dia_array);

				//$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
				$plan_cut_array=array();
				$sql_plan=sql_select("SELECT d.po_break_down_id,d.fabric_color_id, sum(case when  d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty, sum(case when d.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls d, wo_po_color_size_breakdown c WHERE a.job_no=c.job_no_mst and d.is_short=2 and a.id=d.pre_cost_fabric_cost_dtls_id and d.po_break_down_id=c.po_break_down_id and d.job_no=c.job_no_mst and c.id=d.color_size_table_id and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id");
				//,a.body_part_id
				foreach( $sql_plan as $row)
				{
					$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
					$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
				}
				unset($sql_plan);
				$sql_booking="select a.id as pre_cost_fab_dtls_id, a.body_part_id, a.avg_finish_cons, a.construction, a.composition, a.gsm_weight, a.color_type_id, b.booking_no_prefix_num, d.po_break_down_id, b.buyer_id as buyer_name, d.job_no, b.is_short, d.fabric_color_id, d.fin_fab_qnty
				FROM  wo_booking_mst b, wo_booking_dtls d,wo_pre_cost_fabric_cost_dtls a
				WHERE  b.booking_no=d.booking_no and a.job_no=d.job_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id!=0 and b.is_short in (1,2) and a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3";
				//a.body_part_id,

				//echo $sql_booking; //die;
				$sql_booking_res=sql_select($sql_booking); $booking_data_arr=array(); $booking_dataOther_arr=array(); $book_qty_arr=array(); $book_otrQty_arr=array();
				foreach( $sql_booking_res as $row)
				{
					$aa='';
					$aa=$row[csf('body_part_id')];
					if($aa==1 || $aa==14 || $aa==15 || $aa==16 || $aa==17 || $aa==20)
					{
						$booking_data_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';
						if($row[csf('is_short')]==2)
						{
							$book_qty_arr[$row[csf('job_no')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
						}
						else if($row[csf('is_short')]==1)
						{
							$book_qty_arr[$row[csf('job_no')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
						}

						$booking_data_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.$row[csf('pre_cost_fab_dtls_id')].'__';
					}
					else
					{
						$booking_dataOther_arr[$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';

						if($row[csf('is_short')]==2)
						{
							$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
						}
						else if($row[csf('is_short')]==1)
						{
							$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
						}//pre_cost_fab_dtls_id

						$booking_dataOther_arr[$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.$row[csf('pre_cost_fab_dtls_id')].'__';
					}
				}
				unset($sql_booking_res);
				?>
	            <fieldset style="width:2090px;">
	                <table cellpadding="0" cellspacing="0" width="2080">
	                    <tr  class="form_caption" style="border:none;">
	                       <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?> (Style/ Job Wise)</strong></td>
	                    </tr>
	                    <tr  class="form_caption" style="border:none;">
	                       <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
	                    </tr>
	                    <tr  class="form_caption" style="border:none;">
	                       <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	                    </tr>
	                </table>
	                <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
	                <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	                    <thead>
	                        <th width="30">SL</th>
	                        <th width="80">Job</th>
	                        <th width="100">Buyer</th>
							<th width="100">Style</th>
	                        <th width="90">Order</th>
	                        <th width="70">File No</th>
	                        <th width="80">Ref. No</th>
	                        <th width="100">Body Part</th>
	                        <th width="80">Color Type</th>
	                        <th width="120">F.Construction</th>
	                        <th width="120">F.Composition</th>
	                        <th width="45">GSM</th>
	                        <th width="40"><p>Fab.Dia</p></th>
	                        <th width="70"><p>Rack</p></th>
	                        <th width="70"><p>Shelf</p></th>
	                        <th width="75">Ship Date</th>
	                        <th width="80">Order Qty(Pcs)</th>
	                        <th width="110">Color</th>
	                        <th width="80">Req. Qty</th>
	                        <th width="80">Total Recv.</th>
	                        <th width="80">Recv. Balance</th>
	                        <th width="80">Total Issued</th>
	                        <th width="80">Stock</th>
	                        <th width="50">Cons/Pcs</th>
	                        <th width="80">Possible Cut Pcs</th>
	                        <th>Actual Cut</th>
	                    </thead>
	                </table>
	                <div style="width:2100px; max-height:350px; overflow-y:scroll;" id="scroll_body">
	                    <table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	                    <?

					$i=1;
					$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
					from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
					where  a.floor_room_rack_id=b.rack_id
					and a.company_id=$cbo_company_id
					and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
					foreach ($sql_rack as $row)
					{
						$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
					}
					unset($sql_rack);
					$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
					from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
					where  a.floor_room_rack_id=b.shelf_id
					and a.company_id=$cbo_company_id
					and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
					foreach ($sql_shelf as $row)
					{
						$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
					}
					unset($sql_shelf);$jobNo_array=array();
					$total_qty_pcs=$total_recv_qty=$total_issue_qty=$total_balance_qty=$total_stock_qty=$total_actual_cut_qty=$total_reg_booking_qty=$tot_possible_cut=$total_actual_cut_qty=0;
					foreach($booking_data_arr as $job_no=>$job_data)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0;
						if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						//echo $dzn_qnty.'='.$row[csf("cons")];

						$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';

						$poIds=array_filter(array_unique(explode(',',$job_data['po'])));
						foreach($poIds as $po_id)
						{
							if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
							if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
							if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
							if($ref=='') $ref=$job_array[$po_id]['ref']; else $ref.=', '.$job_array[$po_id]['ref'];
						}
						$file_no=implode(', ',array_unique(explode(', ',$file)));
						$po_no=implode(', ',array_unique(explode(', ',$order_no)));
						$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
						$intRef=implode(', ',array_unique(explode(', ',$ref)));

						$style_ref=''; $buyer_name=''; $job_pre='';
						$style_ref=$job_data_arr[$job_no]['style'];
						$buyer_name=$buyer_arr[$job_data_arr[$job_no]['buyer']];
						$job_pre=$job_data_arr[$job_no]['jobPre'];

						$other_data=array_filter(array_unique(explode('__',$job_data['all_data'])));
						foreach($other_data as $book_data)
						{
							$data_ex='';
							$data_ex=explode('##',$book_data);

							$body_part_id=''; $color_type_id=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

							$body_part_id=$data_ex[0];
							$color_type_id=$data_ex[1];
							//echo $body_part_id.'='.$color_type_id.'<br>';
							$construction=$data_ex[2];
							$composition=$data_ex[3];
							$gsm_weight=$data_ex[4];
							$color_id=$data_ex[5];
							$avg_finish_cons=$data_ex[6];
							$booking_no_pre=$data_ex[7];
							$pre_cost_id=$data_ex[8];
							//echo $booking_no_pre.'='.$pre_cost_id.'<br>';

							$main_fin_qty=$book_qty_arr[$job_no][$color_type_id][$construction][$composition][$gsm_weight][$color_id]['main'];
							$short_qty=$book_qty_arr[$job_no][$color_type_id][$construction][$composition][$gsm_weight][$color_id]['short'];
							//$short_othQty=$data_ex[11];$main_fin_othQty=$data_ex[9];

							$reg_booking_qty=0;
							$reg_booking_qty=$main_fin_qty+$short_qty;
							$tot_cons_avg=$avg_finish_cons/$dzn_qnty;


							$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
							foreach($poIds as $po_id)
							{
								$rack=''; $shelf='';
								$rack=$knit_fin_recv_array[$po_id][$color_id]['rack'];
								if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
								$shelf=$knit_fin_recv_array[$po_id][$color_id]['shelf'];
								if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
								$plan_cut_qty+=$plan_cut_array[$po_id][$color_id]['plan_cut_qnty'];

								$knit_recv_qty+=$knit_fin_recv_array[$po_id][$color_id]['receive_qnty'];
								$knit_issue_qty+=$issue_qnty[$po_id][$color_id]['issue_qnty'];
								//echo $knit_recv_qty.'ddd';
								$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
								$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
								$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
								$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

								$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

								$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
							}
							$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
							$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;
							$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
							$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
							$possible_cut=$tot_knit_recv_qty/$tot_cons_avg;
							//echo $job_no.'SS';

							 if(!in_array($job_no,$jobNo_array))
							 {
										if($i!=1)
										{
												?>
	                                    <tr class="tbl_bottom">
	                                        <td colspan="16" align="right"><b>Job Wise Total</b></td>
	                                        <td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
	                                        <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
	                                        <td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
	                                        <td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_possible_cut,2,'.',''); ?></td>
	                                        <td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>
	                                    </tr>
												<?
										unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
										unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
										unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
										unset($tot_sub_recv_bal);
										unset($tot_sub_issue_qty);
										unset($tot_sub_tot_stock);
										}

										$jobNo_array[$i]=$job_no;
						 		 }
										$tot_sub_order_qty_pcs+=$plan_cut_qty;
										$tot_sub_reg_booking_qty+=$reg_booking_qty;
										$tot_sub_knit_recv_qty+=$tot_knit_recv_qty;
										$tot_sub_recv_bal+=$tot_recv_bal;
										$tot_sub_issue_qty+=$tot_knit_issue_qty;
										$tot_sub_tot_stock+=$tot_stock;
										$tot_sub_tot_cons_avg+=$tot_cons_avg;
										$tot_sub_tot_actual_cut_qty+=$tot_actual_cut_qty;

							?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="80"><? echo $job_pre; ?></td>
									<td width="100"><p><? echo $buyer_name; ?></p></td>
									<td width="100"><p><? echo $style_ref; ?></p></td>
									<td width="90"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
									<td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
									<td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>
									<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
									<td width="80"><p><? echo $color_type[$color_type_id]; ?></p></td>
									<td width="120"><p><? echo $construction; ?></p></td>
									<td width="120"><p><?  echo $composition; ?></p></td>
									<td width="45"><p><? echo $gsm_weight; ?></p></td>
									<td width="40"><p><? echo $dia; ?></p></td>
									<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
									<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
									<td width="75"><p><? echo $ship_date; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
									<td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
									<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $gsm_weight;?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
									<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
									<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $gsm_weight;?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
									<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
									<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($possible_cut); ?></p></td>
									<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
								</tr>
							<?
							$i++;
							$total_order_qnty+=$plan_cut_qty;
							$total_recv_qty+=$tot_knit_recv_qty;
							$total_issue_qty+=$tot_knit_issue_qty;
							$total_balance_qty+=$tot_recv_bal;
							$total_stock_qty+=$tot_stock;
							$total_actual_cut_qty+=$tot_actual_cut_qty;
							$total_reg_booking_qty+=$reg_booking_qty;
							$cons_per+=$tot_cons_avg;
							$total_qty_pcs+=$plan_cut_qty;
							$tot_possible_cut+=$possible_cut;
						}
					}
					?>
					</table>
					<table width="2080" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="45">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="75">Total</th>
							<th width="80" align="right"><? echo number_format($total_qty_pcs); ?></th>
							<th width="110">&nbsp;</th>
							<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
							<th width="50" align="right"><? echo number_format($cons_per,4); ?></th>
							<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
							<th align="right"><? echo number_format($total_actual_cut_qty); ?></th>
						</tfoot>
					</table>
				</div>

				 <br/>

			<?
		}
	}//Knit end
	else if($cbo_report_type==3) // Woven Finish Start
	{
		$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
		$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $style_cond");

		$po_array=array(); $all_po_id='';
		$style_array=array(); $all_style_id='';
		$job_array=array(); $all_job_id='';
		$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
		$shipdate_array=array();$shipdate_array='';
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			$style_array[$row[csf('id')]]=$row[csf('style')];
			$file_array[$row[csf('id')]]=$row[csf('file_no')];
			$ref_array[$row[csf('id')]]=$row[csf('grouping')];
			$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
			$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}
		if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_breakdown_id in($ids) or";
				$po_cond_for_in2.=" a.po_break_down_id in($ids) or";
				$po_cond_for_in3.=" d.po_break_down_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
			$po_cond_for_in2=" and a.po_break_down_id in($poIds)";
			$po_cond_for_in3=" and d.po_break_down_id in($poIds)";
		}

		$sql_booking=sql_select("SELECT d.po_break_down_id,d.fabric_color_id,a.body_part_id,sum(case when  d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_booking_mst e WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no  and a.fab_nature_id=3 and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in3 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
		foreach( $sql_booking as $row_book){
			$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
			$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
		}
		unset($sql_booking);
		$fin_woven_recv_array=array();

		$sql_recv=sql_select("select b.id as prop_id,b.po_breakdown_id,b.color_id,b.quantity as finish_receive_qnty, f.body_part_id from inv_receive_master a,product_details_master c, order_wise_pro_details b ,inv_transaction f where a.entry_form=b.entry_form and  c.id=b.prod_id and b.entry_form in (17) and b.trans_id <> 0 and a.item_category=3 and a.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.id = b.trans_id $po_cond_for_in");


		foreach($sql_recv as $row)
		{
			if($chk_prop_id[$row[csf('prop_id')]]  == "")
			{
				$chk_prop_id[$row[csf('prop_id')]] = $row[csf('prop_id')];
				$fin_woven_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			}

		}
		unset($sql_recv);
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id,  a.body_part_id, b.color_id,sum(b.quantity) as issue_qnty from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.id = b.trans_id and a.mst_id = c.id and c.entry_form in (19) and b.trans_id <> 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $po_cond_for_in group by b.po_breakdown_id, a.body_part_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
		$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}
		unset($sql_issue);
		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  $po_cond_for_in2 group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		unset($sql_actual_cut_qty);
		?>
	    <fieldset style="width:1330px;">
	        <table cellpadding="0" cellspacing="0" width="1330">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?><br><? $report_type=(str_replace("'","",$cbo_report_type)); if($report_type==2) echo 'Woven Finish'; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	            </tr>
	        </table>
	        <div align="left"><b><? if($cbo_report_type==1) echo "Knit Finish";else echo 'Woven Finish';?></b></div>
			<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="80">Stock</th>
	                <th width="80">Cons/Pcs</th>
	                <th width="80">Possible Cut Pcs</th>
	                <th width="">Actual Cut</th>
	            </thead>
	        </table>
	        <div style="width:1350px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	            <?
						$sql_main="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id  and c.fab_nature_id=3 $ship_date $buyer_id_cond $job_no_cond $order_cond $style_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id order by a.buyer_name,b.id,a.job_no, b.po_number";
				//echo $sql_main;die;
				$i=1;
				$nameArray=sql_select( $sql_main );
				$jobNo_array=array();$total_woven_req_qty=$total_woven_recv_qty=$total_woven_balance_qty=$total_woven_issue_qty=$total_woven_stock_qty=$total_woven_actual_cut_qty=$total_woven_possible_cut=0;
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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;

					//$woven_req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$woven_req_qty = ceil($booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']);
					$woven_recv_qty=$fin_woven_recv_array[$order_id][$body_part_id][$color_id]['receive_qnty'];
					//$woven_recv_return_qty=$fin_woven_recv_array[$order_id][$color_id]['receive_return'];
					$tot_woven_recv_qty=$woven_recv_qty;
					$tot_woven_recv_bal=$woven_req_qty-$tot_woven_recv_qty;
					$tot_woven_issue_qty=$issue_qnty[$order_id][$body_part_id][$color_id]['issue_qnty'];
					$tot_woven_stock=$tot_woven_recv_qty-$tot_woven_issue_qty;
					$tot_woven_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];
					 $possible_cut=$tot_knit_issue_qty/($tot_cons_avg/$dzn_qnty);
					if(!in_array($row[csf("job_no")],$jobNo_array))
						 {
									if($i!=1)
									{
											?>
                                    <tr class="tbl_bottom">
                                        <td colspan="6" align="right"><b>Job Wise Total</b></td>
                                        <td align="right"><? echo number_format($tot_sub_order_qty_pcs,2,'.',''); ?></td>
                                        <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_reg_booking_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_knit_recv_qty,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_recv_bal,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_issue_qty,2,'.',''); ?> </td>
                                        <td align="right"><? echo number_format($tot_sub_tot_stock,2,'.',''); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_cons_avg,2,'.',''); ?></td>
                                         <td align="right"><? //echo number_format($tot_sub_reg_booking_qty); ?></td>
                                        <td align="right"><? echo number_format($tot_sub_tot_actual_cut_qty,2,'.',''); ?></td>
                                    </tr>
											<?
									unset($tot_sub_order_qty_pcs);unset($tot_sub_tot_cons_avg);
									unset($tot_sub_reg_booking_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_knit_recv_qty);unset($tot_sub_tot_actual_cut_qty);
									unset($tot_sub_recv_bal);
									unset($tot_sub_issue_qty);
									unset($tot_sub_tot_stock);
									}

									$jobNo_array[$i]=$row[csf("job_no")];
					 		 }
									$tot_sub_order_qty_pcs+=$row[csf("order_quantity_set")];
									$tot_sub_reg_booking_qty+=$woven_req_qty;
									$tot_sub_knit_recv_qty+=$tot_woven_recv_qty;
									$tot_sub_recv_bal+=$tot_woven_recv_bal;
									$tot_sub_issue_qty+=$tot_woven_issue_qty;
									$tot_sub_tot_stock+=$tot_woven_stock;
									$tot_sub_tot_cons_avg+=$tot_cons_avg;
									$tot_sub_tot_actual_cut_qty+=$tot_woven_actual_cut_qty;

					?>
	                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
	                    <td width="90"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $order_arr[$order_id]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                    <td width="100" style="word-break:break-all;"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf("order_quantity_set")],2,'.',''); ?></p></td>
	                    <td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($woven_req_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_qty,2);?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_issue_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_stock,2); ?></p></td>
	                    <td width="80" align="right"><p><?  echo number_format($tot_cons_avg,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($possible_cut,2); ?></p></td>
	                    <td width="" align="right"><p><? echo number_format($tot_woven_actual_cut_qty,2);?></p></td>
	                </tr>
	            <?
	            $i++;
				$total_order_qnty+=$row[csf("order_quantity_set")];
				$total_woven_recv_qty+=$tot_woven_recv_qty;
				$total_woven_req_qty+=$woven_req_qty;
				$total_woven_issue_qty+=$tot_woven_issue_qty;
				$total_woven_balance_qty+=$tot_woven_recv_bal;
				$total_woven_stock_qty+=$tot_woven_stock;
				$total_woven_possible_cut+=$possible_cut;
				$total_woven_actual_cut_qty+=$tot_woven_actual_cut_qty;
				}
				?>
	            </table>
	            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_req_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_balance_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_stock_qty,2,'.',''); ?></th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_possible_cut,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_actual_cut_qty,2,'.',''); ?></th>
	                </tfoot>
	            </table>
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
	echo "$total_data####$filename####$type_id####$cbo_report_type";

    exit();
}

if($action=="report_generate_uom")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$book_no=str_replace("'","",$txt_book_no);
	$book_id=str_replace("'","",$txt_book_id);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no_id=str_replace("'","",$txt_order_id);
	$order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$job_year=str_replace("'","",$cbo_year);
	$cbo_uom=str_replace("'","",$cbo_uom);

	if($cbo_uom) $cbo_uom_cond = " and a.uom in ($cbo_uom)"; else $cbo_uom_cond = "";
	//echo $cbo_report_type;die;
	//if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	if($buyer_id==0)
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
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	//echo $buyer_id_cond;die;$order_no=str_replace("'","",$txt_order_no);
	if($txt_file_no!="" || $txt_file_no!=0) $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($txt_ref_no!="") $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond="and YEAR(a.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and f.booking_no_prefix_num='$book_no'";

	if(str_replace("'","",$txt_order_id)!="")  $order_cond=" and b.id in ($order_no_id)";
	else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and b.po_number in ('$order_no')";
	else $order_cond='';
	//echo $order_id_cond;die;
	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $ship_date=""; else $ship_date= "and b.pub_shipment_date>=".$txt_date_from."";
	//if( $date_from=="") $receive_date=""; else $receive_date= " and d.receive_date <=".$txt_date_from."";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	//==================================================
	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		if(str_replace("'","",$cbo_presantation_type)==1)
		{
			$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond ");// and a.season like '$txt_season'

			$po_array=array(); $all_po_id='';
			$style_array=array(); $all_style_id='';
			$job_array=array(); $all_job_id='';
			$buyer_array=array();$all_buyer_id='';$file_array=array();$ref_array=array();
			$shipdate_array=array();$shipdate_array='';
			foreach($poDataArray as $row)
			{
				$po_array[$row[csf('id')]]=$row[csf('po_number')];
				$style_array[$row[csf('id')]]=$row[csf('style')];
				$file_array[$row[csf('id')]]=$row[csf('file_no')];
				$ref_array[$row[csf('id')]]=$row[csf('grouping')];
				$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
				$shipdate_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
				$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
			} //echo $all_po_id;die;
			if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;


			$knit_fin_recv_array=array();
			$sql_recv=sql_select("select a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no, sum(a.quantity) AS finish_receive_qnty
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
			where b.id=a.dtls_id and a.entry_form in (7,37,66,68) and a.trans_id!=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no");
			foreach($sql_recv as $row)
			{
				 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
				 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
				 $knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
			}

			//print_r($fin_recv_array);die; inv_issue_master
			$issue_qnty=array();
			$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b,inv_issue_master c  where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) group by b.po_breakdown_id, b.color_id, a.body_part_id");
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
			}

			$finish_fab_data=array();
			$sql_recv=sql_select("select c.po_breakdown_id, c.color_id,
			sum(CASE WHEN c.entry_form in (46) THEN c.quantity END) AS receive_return,
			sum(CASE WHEN c.entry_form in (52) THEN c.quantity END) AS issue_return,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=5 THEN c.quantity END) AS transfer_in,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=6 THEN c.quantity END) AS transfer_out
			from order_wise_pro_details c
			where c.entry_form in (46,52,14,15) and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id");
			foreach($sql_recv as $row)
			{
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
				 $finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
			}

			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			$dia_array=array();
			$color_dia_array=sql_select( "select po_break_down_id,pre_cost_fabric_cost_dtls_id,color_number_id,dia_width from  wo_pre_cos_fab_co_avg_con_dtls");
			foreach($color_dia_array as $row)
			{
				$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
			} //var_dump($dia_array);

			$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
			$plan_cut_array=array();
			$sql_plan=sql_select("select c.po_break_down_id,b.fabric_color_id,a.body_part_id,
			sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty,
			sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other	FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_color_size_breakdown c WHERE a.job_no=c.job_no_mst and b.is_short=2 and a.id=b.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=c.po_break_down_id and b.job_no=c.job_no_mst and c.id=b.color_size_table_id  and
			 c.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and
			b.is_deleted=0 group by c.po_break_down_id,b.fabric_color_id,a.body_part_id");
			foreach( $sql_plan as $row)
			{

				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
			}



			$sql_booking=sql_select("select d.po_break_down_id,d.fabric_color_id,a.body_part_id,
			sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=2 then d.fin_fab_qnty else 0 end) as main_fin_fab_qnty,
			sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=2 and  e.item_category=2 then d.fin_fab_qnty else 0 end) as other_fin_fab_qnty
			FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e
			WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and e.job_no=a.job_no
			  and d.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and d.booking_type=1 and d.status_active=1 and
			d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
			//$all_po_id
			foreach( $sql_booking as $row_book)
			{
				$tot_req_qty=$row_book[csf('main_fin_fab_qnty')];
				$tot_req_qty_other=$row_book[csf('other_fin_fab_qnty')];
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['fin_fab_qnty']=$tot_req_qty;
				$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('body_part_id')]][$row_book[csf('fabric_color_id')]]['other_fin_fab_qnty']=$tot_req_qty_other;
				//$booking_qnty[$row_book[csf('po_break_down_id')]][$row_book[csf('fabric_color_id')]]['plan_cut_qnty']=$row_book[csf('plan_cut_qnty')];
			}
			$short_booking=sql_select("select d.po_break_down_id,d.fabric_color_id,a.body_part_id,
			sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty,
			sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and d.is_short=1 then d.fin_fab_qnty else 0 end) as short_fin_fab_qnty_other
	        FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,  wo_booking_mst e  WHERE a.job_no=d.job_no and a.job_no=e.job_no and a.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no  and  d.po_break_down_id in($all_po_id) and a.fab_nature_id=2  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,a.body_part_id");
			foreach( $short_booking as $row)
			{
				$tot_short_req_qty=$row[csf('short_fin_fab_qnty')];
				$tot_short_req_qty_other=$row[csf('short_fin_fab_qnty_other')];

				$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty']=$tot_short_req_qty;
				$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['short_fin_fab_qnty_other']=$tot_short_req_qty_other;

				//$short_booking_qnty[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			}


			?>
		    <fieldset style="width:2140px;">
		        <table cellpadding="0" cellspacing="0" width="2080">
		            <tr  class="form_caption" style="border:none;">
		               <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
		            </tr>
		            <tr  class="form_caption" style="border:none;">
		               <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
		            </tr>
		            <tr  class="form_caption" style="border:none;">
		               <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
		            </tr>
		        </table>
		        <div align="left"><b>Main Fabric </b></div>
				<table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
		            <thead>
		                <th width="30">SL</th>
		                <th width="80">Job</th>
		                <th width="100">Buyer</th>
		                <th width="90">Order</th>
		                <th width="70">File No</th>
		                <th width="80">Ref. No</th>
		                <th width="100">Style</th>
		                <th width="100">Body Part</th>
		                <th width="80">Color Type</th>
		                <th width="120">F.Construction</th>
		                <th width="120">F.Composition</th>
		                <th width="45">GSM</th>
		                <th width="40"><p>Fab.Dia</p></th>
		                <th width="70"><p>Rack</p></th>
		                <th width="70"><p>Shelf</p></th>
		                <th width="75">Ship Date</th>
		                <th width="80">Order Qty(Pcs)</th>
		                <th width="110">Color</th>
		                <th width="50">UOM</th>
		                <th width="80">Req. Qty</th>
		                <th width="80">Total Recv.</th>
		                <th width="80">Recv. Balance</th>
		                <th width="80">Total Issued</th>
		                <th width="80">Stock</th>
		                <th width="50">Cons/Pcs</th>
		                <th width="80">Possible Cut Pcs</th>
		                <th>Actual Cut</th>
		            </thead>
		        </table>
		        <div style="width:2150px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
		            <?


						$all_po_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);

						$sql_main="Select g.po_break_down_id as po_id, f.buyer_id as buyer_name, f.job_no, g.fabric_color_id as color_id, c.body_part_id, c.id as pre_cost_fab_dtls_id, c.avg_finish_cons, c.construction, c.gsm_weight, c.composition, c.color_type_id,c.uom, f.booking_no_prefix_num, g.booking_type
						from  wo_pre_cost_fabric_cost_dtls  c, wo_booking_mst f, wo_booking_dtls g
						where g.job_no=f.job_no and c.job_no=g.job_no and g.booking_no=f.booking_no and c.id=g.pre_cost_fabric_cost_dtls_id  and f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and g.booking_type=1  and f.company_id=$cbo_company_id  and c.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and c.fab_nature_id=2  $booking_no_cond  ";
						$p=1;
						foreach($all_po_arr as $all_po)
						{
							if($p==1) $sql_main .="  and (g.po_break_down_id  in(".implode(',',$all_po).")"; else  $sql_main .=" OR g.po_break_down_id  in(".implode(',',$all_po).")";		$p++;
						}
						$sql_main .=")";
						$sql_main .=" group by  c.uom,g.po_break_down_id,c.body_part_id,f.buyer_id,f.job_no,c.avg_finish_cons, g.fabric_color_id,f.booking_no_prefix_num,g.booking_type,c.gsm_weight,c.id,c.body_part_id,c.construction,c.composition,c.color_type_id order by c.uom,c.construction,c.composition ";


						$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
						from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
						where  a.floor_room_rack_id=b.rack_id
						and a.company_id=$cbo_company_id
						and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
						foreach ($sql_rack as $row)
						{
							$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
						}
						$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
						from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
						where  a.floor_room_rack_id=b.shelf_id
						and a.company_id=$cbo_company_id
						and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");

						foreach ($sql_shelf as $row)
						{
							$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
						}



						//echo $sql_main;//die;
						$i=1; $sub_uom="";$sub_reg_booking_qty=$sub_recv_qty=$sub_balance_qty=$sub_issue_qty=$sub_stock_qty=$sub_cons_per=$sub_possible_cut=$sub_actual_cut_qty = 0;
						$nameArray=sql_select($sql_main );

						foreach ($nameArray as $row)
						{
							if (!in_array($row[csf("uom")], $checkUomArr))
							{
                                $checkUomArr[$i] = $row[csf("uom")];
                                if ($i > 1)
                                {
                                	$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
                                    ?>
                                    <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                        <td colspan="18" align="right"><b>UOM Total</b></td>
                                        <td align="center"><b><? echo $unit_of_measurement[$sub_uom];?></b></td>
                                        <td align="right"><? echo number_format($sub_reg_booking_qty,2); ?></td>
                                        <td align="right"><? echo number_format($sub_recv_qty,2); ?></td>
                                        <td align="right"><? echo number_format($sub_balance_qty,2); ?></td>
                                        <td align="right"><? echo number_format($sub_issue_qty,2); ?></td>
                                        <td align="right"><? echo number_format($sub_stock_qty,2); ?></td>
                                        <td align="right"><? echo number_format($sub_cons_per,2); ?></td>
                                        <td align="right"><? echo number_format($sub_possible_cut,2); ?></td>
                                        <td align="right"><? echo number_format($sub_actual_cut_qty); ?></td>
                                    </tr>
                                    <?
                                    $sub_uom="";
                                    $sub_reg_booking_qty = 0;
                                    $sub_recv_qty = 0;
                                    $sub_balance_qty = 0;
                                    $sub_issue_qty = 0;
                                    $sub_stock_qty = 0;
                                    $sub_cons_per = 0;
                                    $sub_possible_cut = 0;
                                    $sub_actual_cut_qty = 0;
                                }
							}

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
							//echo $dzn_qnty.'='.$row[csf("cons")];
							$ship_date=$shipdate_array[$row[csf('po_id')]];
							$order_id=$row[csf('po_id')];
							$color_id=$row[csf("color_id")];
							$body_part_id=$row[csf("body_part_id")];
							$cons_avg=$row[csf("avg_finish_cons")];
							//echo $row[csf("avg_finish_cons")].'='.$row[csf("color_number_avg")];
							$tot_cons_avg=$cons_avg/$dzn_qnty;
							$fin_rack=$knit_fin_recv_array[$order_id][$body_part_id][$color_id]['rack'];
							$fin_shelf=$knit_fin_recv_array[$order_id][$body_part_id][$color_id]['shelf'];

							//echo $row[csf("avg_finish_cons")];
							//$req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
							$tot_short_req_qty=$short_booking_qnty[$order_id][$body_part_id][$color_id]['short_fin_fab_qnty'];
							$reg_booking_qty=$booking_qnty[$order_id][$body_part_id][$color_id]['fin_fab_qnty']+$tot_short_req_qty;
							$tot_order_qty_pcs=$plan_cut_array[$order_id][$body_part_id][$color_id]['plan_cut_qnty'];
							$knit_recv_qty=$knit_fin_recv_array[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]]['receive_qnty'];
							$knit_issue_qty=$issue_qnty[$row[csf('po_id')]][$row[csf("body_part_id")]][$row[csf("color_id")]]['issue_qnty'];
							//echo $knit_recv_qty.'ddd';
							$knit_recv_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['receive_return'];
							$knit_issue_return_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['issue_return'];
							$knit_transfer_in_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_in'];
							$knit_transfer_out_qty=$finish_fab_data[$row[csf('po_id')]][$row[csf("color_id")]]['transfer_out'];

							$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
							$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;

							$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);

							$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
							$tot_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];

							$dia=$dia_array[$row[csf('pre_cost_fab_dtls_id')]][$order_id][$color_id]['dia'];
							$style_id=$style_array[$order_id];
							$job_prefix_no=$job_array[$order_id];


							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="80"><p><? echo $job_prefix_no; ?></p></td>
								<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
								<td width="90"><div style="word-break:break-all"><? echo $order_arr[$order_id]; ?></div></td>
								<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id]; ?></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id]; ?></div></td>
								<td width="100"><p><? echo $style_id; ?></p></td>
								<td width="100"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
								<td width="80"><p><? echo $color_type[$row[csf("color_type_id")]]; ?></p></td>
								<td width="120"><p><? echo $row[csf("construction")]; ?></p></td>
								<td width="120"><p><?  echo $row[csf("composition")]; ?></p></td>
								<td width="45"><p><? echo $row[csf("gsm_weight")]; ?></p></td>
								<td width="40"><p><? echo $dia; ?></p></td>
								<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
								<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
								<td width="75" align="right"><p><? echo change_date_format($ship_date); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_order_qty_pcs); ?></p></td>
								<td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
								<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
								<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
								<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
								<td width="80" align="right"><p><? $possible_cut=$tot_knit_recv_qty/$tot_cons_avg; echo number_format($possible_cut); ?></p></td>
								<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
							</tr>
							<?
							$i++;
							//$total_order_qnty+=$row[csf("order_quantity_set")];
							$total_recv_qty+=$tot_knit_recv_qty;
							$total_issue_qty+=$tot_knit_issue_qty;
							$total_balance_qty+=$tot_recv_bal;
							$total_stock_qty+=$tot_stock;
							$total_actual_cut_qty+=$tot_actual_cut_qty;
							$total_reg_booking_qty+=$reg_booking_qty;
							$cons_per+=$tot_cons_avg;
							$total_qty_pcs+=$tot_order_qty_pcs;
							$tot_possible_cut+=$possible_cut;

							$sub_uom .= $row[csf("uom")].",";
							$sub_reg_booking_qty+=$reg_booking_qty;
							$sub_recv_qty+=$tot_knit_recv_qty;
							$sub_balance_qty+=$tot_recv_bal;
							$sub_issue_qty+=$tot_knit_issue_qty;
							$sub_stock_qty+=$tot_stock;
							$sub_cons_per+=$tot_cons_avg;
							$sub_possible_cut+=$possible_cut;
							$sub_actual_cut_qty+=$tot_actual_cut_qty;


						}
						$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
						?>
							<tr bgcolor="#CCCCCC" style="font-weight: bold ">
							    <td colspan="18" align="right"><b>UOM Total</b></td>
							    <td align="center"><b><? echo $unit_of_measurement[$sub_uom];?></b></td>
							    <td align="right"><? echo number_format($sub_reg_booking_qty,2); ?></td>
							    <td align="right"><? echo number_format($sub_recv_qty,2); ?></td>
                                <td align="right"><? echo number_format($sub_balance_qty,2); ?></td>
                                <td align="right"><? echo number_format($sub_issue_qty,2); ?></td>
                                <td align="right"><? echo number_format($sub_stock_qty,2); ?></td>
                                <td align="right"><? echo number_format($sub_cons_per,2); ?></td>
                                <td align="right"><? echo number_format($sub_possible_cut,2); ?></td>
                                <td align="right"><? echo number_format($sub_actual_cut_qty); ?></td>
							</tr>
						</table>
						<table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
							<tfoot>
								<th width="30"></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="90"></th>
								<th width="70"></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="80"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="45"></th>
								<th width="40"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs); ?></th>
								<th width="80" align="right" id=""><? echo number_format($total_qty_pcs); ?></th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
								<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
								<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
								<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
								<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
								<th width="50" align="right"><? echo number_format($cons_per); ?></th>
								<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
								<th width="" align="right"><? echo number_format($total_actual_cut_qty); ?></th>
							</tfoot>
						</table>
				</div>
			</fieldset>
			<br/>
			<fieldset style="width:155px;">
				<div align="left"><b>Other Fabric </b>
				</div>
				<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="90">Job</th>
						<th width="100">Buyer</th>
						<th width="80">Order No</th>
						<th width="70">File No</th>
						<th width="80">Ref. No</th>
						<th width="100">Style</th>
						<th width="100">Body Part</th>
						<th width="80">Color Type</th>
						<th width="120">F.Construction</th>
						<th width="120">F.Composition</th>
						<th width="45">GSM</th>
						<th width="40">Fab.Dia</th>
						<th width="70">Rack</th>
						<th width="70">Shelf</th>
						<th width="75">Ship Date</th>
						<th width="80">Order Qty(Pcs)</th>
						<th width="110">Color</th>
						<th width="50">UOM</th>
						<th width="80">Req. Qty</th>
						<th width="80">Total Recv.</th>
						<th width="80">Recv. Balance</th>
						<th width="80">Total Issued</th>
						<th width="">Stock</th>
					</thead>
				</table>
				<div style="width:1950px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
					<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
						<?

						if( $date_from=="") $ship_date2=""; else $ship_date2= "and b.pub_shipment_date>=".$txt_date_from."";
						$poDataArray2=sql_select("select b.id,  b.pub_shipment_date,b.po_number,a.buyer_name,a.job_no_prefix_num,b.file_no,b.grouping,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date2 $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond");
						$po_array=array(); $all_po_id2='';
						$style_array=array(); $all_style_id='';
						$job_array=array(); $all_job_id='';
						$buyer_array=array();$all_buyer_id='';
						$ship_date_array=array();$ship_date_array='';
						foreach($poDataArray2 as $row)
						{
						$po_array[$row[csf('id')]]=$row[csf('po_number')];
						$style_array[$row[csf('id')]]=$row[csf('style')];
						$file_array[$row[csf('id')]]=$row[csf('file_no')];
						$ref_array[$row[csf('id')]]=$row[csf('grouping')];
						$buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
						$ship_date_array[$row[csf('id')]]=$row[csf('pub_shipment_date')];
						$job_array[$row[csf('id')]]=$row[csf('job_no_prefix_num')];
						if($all_po_id2=="") $all_po_id2=$row[csf('id')]; else $all_po_id2.=",".$row[csf('id')];
						}//echo $all_po_id2;die;
						if($all_po_id2==0 || $all_po_id2=="") $all_po_id3=0;else $all_po_id3=$all_po_id2;
						$all_po_arr2=array_chunk(array_unique(explode(",",$all_po_id3)),999);
						//print_r($all_po_arr2);die;

							$sql_other="Select g.po_break_down_id as po_id,f.buyer_id as buyer_name,f.job_no,g.fabric_color_id as color_id,c.body_part_id,c.id as pre_cost_fab_dtls_id,c.avg_finish_cons,c.construction,c.gsm_weight,c.composition,c.color_type_id,f.booking_no_prefix_num,g.booking_type,c.uom
						from  wo_pre_cost_fabric_cost_dtls  c,wo_booking_mst f,wo_booking_dtls g where  g.booking_no=f.booking_no and g.job_no=c.job_no  and c.id=g.pre_cost_fabric_cost_dtls_id and  f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.fab_nature_id=2  and f.item_category=2 and  c.is_deleted=0 and g.booking_type=1  and f.company_id=$cbo_company_id  and c.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)  $booking_no_cond  ";
						$j=1;
						foreach($all_po_arr2 as $all_po2)
						{
							if($j==1) $sql_other .="  and (g.po_break_down_id  in(".implode(',',$all_po2).")"; else  $sql_other .=" OR g.po_break_down_id  in(".implode(',',$all_po2).")";		$j++;
						}
						$sql_other .=")";
						$sql_other .=" group by  g.po_break_down_id,c.body_part_id,f.buyer_id,f.job_no,c.avg_finish_cons, g.fabric_color_id,f.booking_no_prefix_num,g.booking_type,c.gsm_weight,c.id,c.body_part_id,c.construction,c.composition,c.color_type_id,c.uom order by c.uom,c.construction,c.composition ";

						//echo $sql_other;

						$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
						from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
						where  a.floor_room_rack_id=b.rack_id
						and a.company_id=$cbo_company_id
						and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
						foreach ($sql_rack as $row)
						{
							$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
						}
						$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
						from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
						where  a.floor_room_rack_id=b.shelf_id
						and a.company_id=$cbo_company_id
						and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
						foreach ($sql_shelf as $row)
						{
							$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
						}

						$k=1; $sub_uom="";$checkUomArr=array();
						$other_data=sql_select( $sql_other );
						foreach ($other_data as $rows)
						{
							if (!in_array($rows[csf("uom")], $checkUomArr))
							{
                                $checkUomArr[$k] = $rows[csf("uom")];
                                if ($k > 1)
                                {
                                	$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
                                    ?>
                                    <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                        <td colspan="18" align="right"><b>UOM Total</b></td>
                                        <td align="center"><b><? echo $unit_of_measurement[$sub_uom];?></b></td>
                                        <td align="right"><? echo number_format($sub_req_qty_other,2); ?></td>
                                        <td align="right"><? echo number_format($sub_knit_recv_qty_other,2); ?></td>
                                        <td align="right"><? echo number_format($sub_recv_bal_other,2); ?></td>
                                        <td align="right"><? echo number_format($sub_tot_knit_issue_qty_other,2); ?></td>
                                        <td align="right"><? echo number_format($sub_knit_stock_other_qty); ?></td>
                                    </tr>
                                    <?
                                    $sub_uom="";
                                    $sub_req_qty_other = 0;
                                    $sub_knit_recv_qty_other=0;
                                    $sub_recv_bal_other=0;
                                    $sub_tot_knit_issue_qty_other=0;
                                    $sub_knit_stock_other_qty=0;

                                }
							}

							if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";

							$dzn_qnty=0;
							if($costing_per_id_library[$rows[csf('job_no')]]==1)
							{
								$dzn_qnty=12;
							}
							else if($costing_per_id_library[$rows[csf('job_no')]]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($costing_per_id_library[$rows[csf('job_no')]]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($costing_per_id_library[$rows[csf('job_no')]]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							//echo $rows[csf("booking_no")];
							$shipdate=$ship_date_array[$rows[csf('po_id')]];
							$order_id_other=$rows[csf('po_id')];
							$color_id_other=$rows[csf("color_id")];
							$cons_avg_other=$rows[csf("avg_finish_cons")];
							$tot_cons_avg_other=$cons_avg_other/$dzn_qnty;

							$body_part_id_other=$rows[csf("body_part_id")];
							$fin_rack=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['rack_no'];
							$fin_shelf=$fin_rack_shelf_array[$order_id_other][$body_part_id_other][$color_id_other]['shelf_no'];
							//$req_qty_other=($rows[csf("avg_finish_cons")]/$dzn_qnty)*$rows[csf("order_quantity_set")];
							$tot_short_req_qty2=$short_booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['short_fin_fab_qnty_other'];
							$reg_booking_qty_other=$booking_qnty[$order_id_other][$rows[csf("body_part_id")]][$color_id_other]['other_fin_fab_qnty']+$tot_short_req_qty2;
							$tot_qty_pcs_other=$plan_cut_array[$order_id_other][$body_part_id_other][$color_id_other]['plan_cut_qnty_other'];

							$knit_recv_qty_other=$knit_fin_recv_array[$order_id_other][$body_part_id_other][$color_id_other]['receive_qnty'];
							$knit_issue_qty_other=$issue_qnty[$order_id_other][$body_part_id_other][$color_id_other]['issue_qnty'];

							//$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];

							$knit_recv_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['receive_return'];
							$knit_issue_return_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['issue_return'];
							$knit_transfer_in_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_in'];
							$knit_transfer_out_qty_other=$finish_fab_data[$order_id_other][$color_id_other]['transfer_out'];

							$tot_knit_recv_qty_other=($knit_recv_qty_other+$knit_transfer_in_qty_other+$knit_issue_return_qty_other);
							$tot_recv_bal_other=$reg_booking_qty_other-$tot_knit_recv_qty_other;
							$tot_knit_issue_qty_other=($knit_issue_qty_other+$knit_transfer_out_qty_other+$knit_recv_return_qty_other);
							$tot_stock_other=$tot_knit_recv_qty_other-$tot_knit_issue_qty_other;
							$tot_actual_cut_qty_other=$actual_cut_qnty[$order_id_other][$color_id_other]['actual_cut_qty'];
							$dia_other=$dia_array[$rows[csf('pre_cost_fab_dtls_id')]][$order_id_other][$color_id_other]['dia'];

							$style_id=$style_array[$order_id_other];
							$job_prefix_no=$job_array[$order_id_other];
							?>
							<tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor2;?>')" id="trother<? echo $k;?>">
								<td width="30"><? echo $k; ?></td>
								<td width="90"><p><? echo $job_prefix_no; ?></p></td>
								<td width="100"><p><? echo $buyer_arr[$rows[csf("buyer_name")]]; ?></p></td>

								<td width="80"><div style="word-break:break-all"><? echo $order_arr[$order_id_other];  //$order_arr[ ?></div></td>
								<td width="70"><div style="word-break:break-all"><? echo $file_array[$order_id_other]; ?></div></td>
								<td width="80"><div style="word-break:break-all"><? echo $ref_array[$order_id_other]; ?></div></td>
								<td width="100"><p><? echo $style_id; ?></p></td>

								<td width="100"><p><?  echo $body_part[$rows[csf("body_part_id")]]; //$body_part ?></p></td>
								<td width="80"><p><? echo $color_type[$rows[csf("color_type_id")]]; ?></p></td>
								<td width="120"><p><? echo $rows[csf("construction")]; ?></p></td>
								<td width="120"><p><? echo $rows[csf("composition")]; ?></p></td>
								<td width="45"><p><? echo $rows[csf("gsm_weight")]; ?></p></td>
								<td width="40"><p><? echo $dia_other; ?></p></td>
								<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
								<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
								<td width="75" align="right"><p><? echo change_date_format($shipdate); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_qty_pcs_other); ?></p></td>
								<td width="110"><p><? echo $color_arr[$rows[csf("color_id")]]; ?></p></td>
								<td width="50" align="center"><p><? echo $unit_of_measurement[$rows[csf("uom")]]; ?></p></td>
								<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('job_no')];?>','<? echo $rows[csf('buyer_name')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','<? echo $rows[csf('booking_no')]; ?>','<? echo $rows[csf('booking_type')]; ?>','req_qty_main_short_other')"><? echo number_format($reg_booking_qty_other,2); ?></a></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty_other,2); ?></a></p></td>
								<td width="80" align="right"><p><? echo number_format($tot_recv_bal_other,2); ?></p></td>
								<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty_other,2); ?></a></p></td>
								<td width="" align="right"><p><? echo number_format($tot_stock_other,2); ?></p></td>
							</tr>
						<?
						$k++;
						//$total_order_qnty_other+=$rows[csf("order_quantity_set")];
						$total_req_qty_other+=$reg_booking_qty_other;
						$total_knit_recv_qty_other+=$tot_knit_recv_qty_other;
						$total_recv_bal_other+=$tot_recv_bal_other;
						$total_knit_stock_other_qty+=$tot_stock_other;
						$total_tot_knit_issue_qty_other+=$tot_knit_issue_qty_other;
						$total_qty_pcs_other+=$tot_qty_pcs_other;

						$sub_uom .= $rows[csf("uom")].",";
						$sub_req_qty_other+=$reg_booking_qty_other;
						$sub_knit_recv_qty_other+=$tot_knit_recv_qty_other;
						$sub_recv_bal_other+=$tot_recv_bal_other;
						$sub_tot_knit_issue_qty_other+=$tot_knit_issue_qty_other;
						$sub_knit_stock_other_qty+=$tot_stock_other;


						}
						$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
						?>
						<tr bgcolor="#CCCCCC" style="font-weight: bold ">
                            <td colspan="18" align="right"><b>UOM Total</b></td>
                            <td align="center"><b><? echo $unit_of_measurement[$sub_uom];?></b></td>
                            <td align="right"><? echo number_format($sub_req_qty_other,2); ?></td>
                            <td align="right"><? echo number_format($sub_knit_recv_qty_other,2); ?></td>
                            <td align="right"><? echo number_format($sub_recv_bal_other,2); ?></td>
                            <td align="right"><? echo number_format($sub_tot_knit_issue_qty_other,2); ?></td>
                            <td align="right"><? echo number_format($sub_knit_stock_other_qty); ?></td>
                        </tr>
					</table>
					<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30"></th>
							 <th width="90"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="70"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="45">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="75" align="right" id=""><? //echo number_format($total_qty_pcs_other); ?></th>
							<th width="80" align="right" id=""><? echo number_format($total_qty_pcs_other); ?></th>
							<th width="110">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="80" align="right"><? echo number_format($total_req_qty_other,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_knit_recv_qty_other,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_bal_other,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_tot_knit_issue_qty_other); ?></th>
							<th width="" align="right"><? echo number_format($total_knit_stock_other_qty); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
				<?
		}
		else
		{
			//echo "select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond ";
			$poDataArray=sql_select("select b.id, b.pub_shipment_date, b.file_no, b.grouping, b.po_number, a.job_no, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $ship_date $buyer_id_cond $job_no_cond $order_cond $ref_cond $file_cond $year_cond ");// and a.season like '$txt_season'

			$job_array=array(); $job_data_arr=array(); $all_po_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$job_array[$row[csf('id')]]['pDate']=$row[csf('pub_shipment_date')];
				$job_data_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$job_data_arr[$row[csf('job_no')]]['style']=$row[csf('style')];
				$job_data_arr[$row[csf('job_no')]]['jobPre']=$row[csf('job_no_prefix_num')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
			} //echo $all_po_id;die;
			if($all_po_id==0 || $all_po_id=="") $all_po_id=0; else $all_po_id=$all_po_id;
			unset($poDataArray);
			$knit_fin_recv_array=array();
			$sql_recv=sql_select("select a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no, sum(a.quantity) AS finish_receive_qnty
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
			where b.id=a.dtls_id and a.entry_form in (7,37,66,68) and a.trans_id!=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, a.color_id, b.body_part_id, b.rack_no, b.shelf_no");
			foreach($sql_recv as $row)
			{
				$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
				if($row[csf('rack_no')]!='')
				{
					$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['rack']=$row[csf('rack_no')];
				}
				if($row[csf('shelf_no')]!=0)
				{
					if($row[csf('shelf_no')]!='')
					{
						$knit_fin_recv_array[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]['shelf']=$row[csf('shelf_no')];
					}
				}
			}
			unset($sql_recv);

			$issue_qnty=array();
			$sql_issue=sql_select("select b.po_breakdown_id, b.color_id, a.body_part_id, sum(b.quantity) as issue_qnty  from inv_finish_fabric_issue_dtls a, order_wise_pro_details b, inv_issue_master c where a.id=b.dtls_id and c.id=a.mst_id and b.trans_id!=0 and c.entry_form in (18,46,71) and  a.status_active=1 and a.is_deleted=0 and b.entry_form in(18,71) group by b.po_breakdown_id, b.color_id, a.body_part_id");
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
			}
			unset($sql_issue);

			$finish_fab_data=array();
			$sql_retTrns=sql_select("select c.po_breakdown_id, c.color_id,
			sum(CASE WHEN c.entry_form in (46) THEN c.quantity END) AS receive_return,
			sum(CASE WHEN c.entry_form in (52) THEN c.quantity END) AS issue_return,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=5 THEN c.quantity END) AS transfer_in,
			sum(CASE WHEN c.entry_form in (14,15) and c.trans_type=6 THEN c.quantity END) AS transfer_out
			from order_wise_pro_details c
			where c.entry_form in (46,52,14,15) and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id");
			foreach($sql_recv as $row)
			{
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_return']=$row[csf('receive_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue_return']=$row[csf('issue_return')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_in']=$row[csf('transfer_in')];
				$finish_fab_data[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['transfer_out']=$row[csf('transfer_out')];
			}
			unset($sql_retTrns);

			$actual_cut_qnty=array();
			$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
			foreach( $sql_actual_cut_qty as $row_actual)
			{
				$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
			}
			unset($sql_actual_cut_qty);
			$dia_array=array();
			$color_dia_array=sql_select( "select po_break_down_id, pre_cost_fabric_cost_dtls_id, color_number_id, dia_width from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in ($all_po_id) and po_break_down_id!=0");
			foreach($color_dia_array as $row)
			{
				$dia_array[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['dia']=$row[csf('dia_width')];
			} //var_dump($dia_array);
			unset($color_dia_array);

			//$booking_qnty=array(); $short_booking_qnty=array(); $booking_qnty_other=array();  $short_booking_qnty_other=array();
			$plan_cut_array=array();
			$sql_plan=sql_select("select c.po_break_down_id,b.fabric_color_id,a.body_part_id,
			sum(case when  a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty,
			sum(case when  a.body_part_id not in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219) and b.is_short=2 then c.plan_cut_qnty else 0 end) as plan_cut_qnty_other

			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_color_size_breakdown c
			WHERE a.job_no=c.job_no_mst and b.is_short=2 and a.id=b.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=c.po_break_down_id and b.job_no=c.job_no_mst and c.id=b.color_size_table_id  and
			 c.po_break_down_id in($all_po_id) and  a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and
			b.is_deleted=0 group by c.po_break_down_id,b.fabric_color_id,a.body_part_id");
			foreach( $sql_plan as $row)
			{

				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
				$plan_cut_array[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('fabric_color_id')]]['plan_cut_qnty_other']=$row[csf('plan_cut_qnty_other')];
			}


			$sql_booking="select a.id as pre_cost_fab_dtls_id, a.avg_finish_cons, a.construction, a.composition, a.gsm_weight, a.color_type_id, a.body_part_id, b.booking_no_prefix_num, c.po_break_down_id, b.buyer_id as buyer_name, b.job_no, b.is_short, c.fabric_color_id, c.fin_fab_qnty,a.uom

			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_mst b, wo_booking_dtls c
			WHERE a.job_no=b.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.job_no=c.job_no and c.po_break_down_id in($all_po_id) and c.po_break_down_id!=0 and b.is_short in (1,2) and a.fab_nature_id=2 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $cbo_uom_cond order by a.uom desc";
			//echo $sql_booking; die;
			$sql_booking_res=sql_select($sql_booking); $booking_data_arr=array(); $booking_dataOther_arr=array(); $book_qty_arr=array(); $book_otrQty_arr=array();
			foreach( $sql_booking_res as $row)
			{
				$aa='';
				$aa=$row[csf('body_part_id')];
				if($aa==1 || $aa==14 || $aa==15 || $aa==16 || $aa==17 || $aa==20)
				{
					$booking_data_arr[$row[csf("uom")]][$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';
					if($row[csf('is_short')]==2)
					{
						$book_qty_arr[$row[csf("uom")]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_qty_arr[$row[csf("uom")]][$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}

					$booking_data_arr[$row[csf("uom")]][$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.'0'.'__';
				}
				else
				{
					$booking_dataOther_arr[$row[csf("uom")]][$row[csf('job_no')]]['po'].=$row[csf('po_break_down_id')].',';

					if($row[csf('is_short')]==2)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['main']+=$row[csf('fin_fab_qnty')];
					}
					else if($row[csf('is_short')]==1)
					{
						$book_otrQty_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]]['short']+=$row[csf('fin_fab_qnty')];
					}

					$booking_dataOther_arr[$row[csf("uom")]][$row[csf('job_no')]]['all_data'].=$row[csf('body_part_id')].'##'.$row[csf('color_type_id')].'##'.$row[csf('construction')].'##'.$row[csf('composition')].'##'.$row[csf('gsm_weight')].'##'.$row[csf('fabric_color_id')].'##'.'0'.'##'.'0'.'##'.'0'.'__';
				}
			}

			?>
            <fieldset style="width:2140px;">
                <table cellpadding="0" cellspacing="0" width="2130">
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?> (Style/ Job Wise)</strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                    </tr>
                    <tr  class="form_caption" style="border:none;">
                       <td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
                    </tr>
                </table>
                <div align="left"><b>Main Fabric </b></div>
                <table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Job</th>
                        <th width="100">Buyer</th>
						<th width="100">Style</th>
                        <th width="90">Order</th>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Body Part</th>
                        <th width="80">Color Type</th>
                        <th width="120">F.Construction</th>
                        <th width="120">F.Composition</th>
                        <th width="45">GSM</th>
                        <th width="40"><p>Fab.Dia</p></th>
                        <th width="70"><p>Rack</p></th>
                        <th width="70"><p>Shelf</p></th>
                        <th width="75">Ship Date</th>
                        <th width="80">Order Qty(Pcs)</th>
                        <th width="110">Color</th>
                        <th width="50">UOM</th>
                        <th width="80">Req. Qty</th>
                        <th width="80">Total Recv.</th>
                        <th width="80">Recv. Balance</th>
                        <th width="80">Total Issued</th>
                        <th width="80">Stock</th>
                        <th width="50">Cons/Pcs</th>
                        <th width="80">Possible Cut Pcs</th>
                        <th>Actual Cut</th>
                    </thead>
                </table>
                <div style="width:2150px; max-height:350px; overflow-y:scroll;" id="scroll_body">
                    <table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	                    <?

							$i=1;
							$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
							from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
							where  a.floor_room_rack_id=b.rack_id
							and a.company_id=$cbo_company_id
							and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
							foreach ($sql_rack as $row)
							{
								$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
							}
							$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
							from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
							where  a.floor_room_rack_id=b.shelf_id
							and a.company_id=$cbo_company_id
							and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
							foreach ($sql_shelf as $row)
							{
								$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
							}
							foreach($booking_data_arr as $uom_id=>$uom_data)
							{
								foreach($uom_data as $job_no=>$job_data)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$dzn_qnty=0;
									if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;

									//echo $dzn_qnty.'='.$row[csf("cons")];

									$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';
									$poIds=array_filter(array_unique(explode(',',$job_data['po'])));
									foreach($poIds as $po_id)
									{
										if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
										if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
										if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
										if($ref=='') $ref=$job_array[$po_id]['ref']; else $ref.=', '.$job_array[$po_id]['ref'];
									}
									$file_no=implode(', ',array_unique(explode(', ',$file)));
									$po_no=implode(', ',array_unique(explode(', ',$order_no)));
									$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
									$intRef=implode(', ',array_unique(explode(', ',$ref)));

									$style_ref=''; $buyer_name=''; $job_pre='';
									$style_ref=$job_data_arr[$job_no]['style'];
									$buyer_name=$buyer_arr[$job_data_arr[$job_no]['buyer']];
									$job_pre=$job_data_arr[$job_no]['jobPre'];

									$other_data=array_filter(array_unique(explode('__',$job_data['all_data'])));
									foreach($other_data as $book_data)
									{
										$data_ex='';
										$data_ex=explode('##',$book_data);

										$body_part_id=''; $color_type=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

										$body_part_id=$data_ex[0];
										$color_type=$data_ex[1];
										$construction=$data_ex[2];
										$composition=$data_ex[3];
										$gsm_weight=$data_ex[4];
										$color_id=$data_ex[5];
										$avg_finish_cons=$data_ex[6];
										$booking_no_pre=$data_ex[7];
										$pre_cost_id=$data_ex[8];

										$main_fin_qty=$book_qty_arr[$uom_id][$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['main'];
										$short_qty=$book_qty_arr[$uom_id][$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['short'];
										//$short_othQty=$data_ex[11];$main_fin_othQty=$data_ex[9];

										$reg_booking_qty=0;
										$reg_booking_qty=$main_fin_qty+$short_qty;
										$tot_cons_avg=$avg_finish_cons/$dzn_qnty;


										$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
										foreach($poIds as $po_id)
										{
											$rack=''; $shelf='';
											$rack=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['rack'];
											if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
											$shelf=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['shelf'];
											if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
											$plan_cut_qty+=$plan_cut_array[$po_id][$body_part_id][$color_id]['plan_cut_qnty'];

											$knit_recv_qty+=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['receive_qnty'];
											$knit_issue_qty+=$issue_qnty[$po_id][$body_part_id][$color_id]['issue_qnty'];
											//echo $knit_recv_qty.'ddd';
											$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
											$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
											$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
											$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

											$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

											$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
										}
										$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
										$tot_recv_bal=$reg_booking_qty-$tot_knit_recv_qty;
										$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
										$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="80"><? echo $job_pre; ?></td>
												<td width="100"><p><? echo $buyer_name; ?></p></td>
												<td width="100"><p><? echo $style_ref; ?></p></td>
												<td width="90"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
												<td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
												<td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>
												<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
												<td width="80"><p><? echo $color_type[$color_type]; ?></p></td>
												<td width="120"><p><? echo $construction; ?></p></td>
												<td width="120"><p><?  echo $composition; ?></p></td>
												<td width="45"><p><? echo $gsm_weight; ?></p></td>
												<td width="40"><p><? echo $dia; ?></p></td>
												<td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
												<td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
												<td width="75"><p><? echo $ship_date; ?></p></td>
												<td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
												<td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
												<td width="50" align="center"><p><? echo $unit_of_measurement[$uom_id]; ?></p></td>
												<td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>','<? echo $row[csf('booking_type')]; ?>','req_qty_main_short')"><? echo number_format($reg_booking_qty,2); ?></a></p></td>
												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2);?></a></p></td>
												<td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('body_part_id')]; ?>','<? echo $row[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
												<td width="80" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
												<td width="50" align="right"><p><?  echo number_format($tot_cons_avg,4); ?></p></td>
												<td width="80" align="right"><p><? $possible_cut=$tot_knit_recv_qty/$tot_cons_avg; echo number_format($possible_cut); ?></p></td>
												<td align="right"><p><? echo number_format($tot_actual_cut_qty);?></p></td>
											</tr>
										<?
										$i++;
										$total_order_qnty+=$plan_cut_qty;
										$total_recv_qty+=$tot_knit_recv_qty;
										$total_issue_qty+=$tot_knit_issue_qty;
										$total_balance_qty+=$tot_recv_bal;
										$total_stock_qty+=$tot_stock;
										$total_actual_cut_qty+=$tot_actual_cut_qty;
										$total_reg_booking_qty+=$reg_booking_qty;
										$cons_per+=$tot_cons_avg;
										$total_qty_pcs+=$plan_cut_qty;
										$tot_possible_cut+=$possible_cut;

										$sub_reg_booking_qty+=$reg_booking_qty;
										$sub_recv_qty+=$tot_knit_recv_qty;
										$sub_balance_qty+=$tot_recv_bal;
										$sub_issue_qty+=$tot_knit_issue_qty;
										$sub_stock_qty+=$tot_stock;
										$sub_cons_per+=$tot_cons_avg;
										$sub_possible_cut+=$possible_cut;
										$sub_actual_cut_qty+=$tot_actual_cut_qty;
									}
								}
								?>
								<tr bgcolor="#CCCCCC" style="font-weight: bold" >
									<td colspan="18" align="right">UOM Total</td>
									<td align="center"><? echo $unit_of_measurement[$uom_id]; ?></td>
									<td align="right"><? echo number_format($sub_reg_booking_qty,2);?></td>
									<td align="right"><? echo number_format($sub_recv_qty,2);?></td>
									<td align="right"><? echo number_format($sub_balance_qty,2);?></td>
									<td align="right"><? echo number_format($sub_issue_qty,2);?></td>
									<td align="right"><? echo number_format($sub_stock_qty,2);?></td>
									<td align="right"><? echo number_format($sub_cons_per,2);?></td>
									<td align="right"><? echo number_format($sub_possible_cut,2);?></td>
									<td align="right"><? echo number_format($sub_actual_cut_qty,2);?></td>
								</tr>
								<?
								$sub_reg_booking_qty=$sub_recv_qty=$sub_balance_qty=$sub_issue_qty=$sub_stock_qty=$sub_cons_per=$sub_possible_cut=$sub_actual_cut_qty=0;
							}
						?>
					</table>
					<table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="45">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="75">Total</th>
							<th width="80" align="right"><? //echo number_format($total_qty_pcs); ?></th>
							<th width="110">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="80" align="right"><? echo number_format($total_reg_booking_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_balance_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="80" align="right"><? echo number_format($total_stock_qty,2,'.',''); ?></th>
							<th width="50" align="right"><? echo number_format($cons_per,4); ?></th>
							<th width="80" align="right"><? echo number_format($tot_possible_cut); ?></th>
							<th align="right"><? echo number_format($total_actual_cut_qty); ?></th>
						</tfoot>
					</table>
				</div>
			 </fieldset>
			 <br/>
			 <fieldset style="width:155px;">
			<div align="left"><b>Other Fabric </b>
			</div>
			<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="90">Job</th>
					<th width="100">Buyer</th>
                    <th width="100">Style</th>
					<th width="80">Order No</th>
					<th width="70">File No</th>
					<th width="80">Ref. No</th>
					<th width="100">Body Part</th>
					<th width="80">Color Type</th>
					<th width="120">F.Construction</th>
					<th width="120">F.Composition</th>
					<th width="45">GSM</th>
					<th width="40">Fab.Dia</th>
					<th width="70">Rack</th>
					<th width="70">Shelf</th>
					<th width="75">Ship Date</th>
					<th width="80">Order Qty(Pcs)</th>
					<th width="110">Color</th>
					<th width="50">UOM</th>
					<th width="80">Req. Qty</th>
					<th width="80">Total Recv.</th>
					<th width="80">Recv. Balance</th>
					<th width="80">Total Issued</th>
					<th width="">Stock</th>
				</thead>
			</table>
			<div style="width:1950px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
				<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
				<?
				$k=1;
				$sql_rack=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.rack_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_rack as $row)
				{
					$rack_name_arr[$row[csf('rack_id')]]['rack_name']=$row[csf('floor_room_rack_name')];
				}
				$sql_shelf=sql_select("select b.rack_id,b.shelf_id,a.floor_room_rack_name
				from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b
				where  a.floor_room_rack_id=b.shelf_id
				and a.company_id=$cbo_company_id
				and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
				foreach ($sql_shelf as $row)
				{
					$shelf_name_arr[$row[csf('rack_id')]][$row[csf('shelf_id')]]['shelf_name']=$row[csf('floor_room_rack_name')];
				}
				foreach($booking_dataOther_arr as $uom_id=>$uomData)
				{
					foreach($uomData as $jobNo=>$jobData)
					{
						if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";
						$dzn_qnty=0;
						if($costing_per_id_library[$jobNo]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$jobNo]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$jobNo]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$jobNo]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						//echo $rows[csf("booking_no")];

						$order_no=''; $po_no=''; $pub_sDate=''; $ship_date=''; $file=''; $file_no=''; $ref=''; $intRef='';
						$poIds=array_filter(array_unique(explode(',',$jobData['po'])));
						foreach($poIds as $po_id)
						{
							if($order_no=='') $order_no=$job_array[$po_id]['po']; else $order_no.=', '.$job_array[$po_id]['po'];
							if($pub_sDate=='') $pub_sDate=change_date_format($job_array[$po_id]['pDate']); else $pub_sDate.=', '.change_date_format($job_array[$po_id]['pDate']);
							if($file=='') $file=$job_array[$po_id]['file']; else $file.=', '.$job_array[$po_id]['file'];
							if($intRef=='') $intRef=$job_array[$po_id]['ref']; else $intRef.=', '.$job_array[$po_id]['ref'];
						}
						$file_no=implode(', ',array_unique(explode(', ',$file)));
						$po_no=implode(', ',array_unique(explode(', ',$order_no)));
						$ship_date=implode(', ',array_unique(explode(', ',$pub_sDate)));
						$intRef=implode(', ',array_unique(explode(', ',$ref)));

						$style_ref=''; $buyer_name=''; $job_pre='';
						$style_ref=$job_data_arr[$jobNo]['style'];
						$buyer_name=$buyer_arr[$job_data_arr[$jobNo]['buyer']];
						$job_pre=$job_data_arr[$jobNo]['jobPre'];
						$other_data='';
						$other_data=array_filter(array_unique(explode('__',$jobData['all_data'])));
						//print_r($other_data);
						foreach($other_data as $book_data)
						{
						//	echo $book_data;
							$data_ex='';
							$data_ex=explode('##',$book_data);

							$body_part_id=''; $color_type=''; $construction=''; $composition=''; $gsm_weight=''; $color_id=''; $avg_finish_cons=''; $booking_no_pre=''; $main_fin_qty=0; $main_fin_othQty=0; $short_qty=0; $short_othQty=0; $pre_cost_id='';

							$body_part_id=$data_ex[0];
							$color_type=$data_ex[1];
							$construction=$data_ex[2];
							$composition=$data_ex[3];
							$gsm_weight=$data_ex[4];
							$color_id=$data_ex[5];
							$avg_finish_cons=$data_ex[6];
							$booking_no_pre=$data_ex[7];
							$pre_cost_id=$data_ex[8];

							$main_fin_othQty=$book_otrQty_arr[$uom_id][$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['main'];
							$short_othQty=$book_otrQty_arr[$uom_id][$job_no][$body_part_id][$color_type][$construction][$composition][$gsm_weight][$color_id]['short'];

							$req_booking_qty=0;
							$req_booking_qty=$main_fin_othQty+$short_othQty;
							$tot_cons_avg=$avg_finish_cons/$dzn_qnty;

							//echo $jobNo.'=='.$job_pre.'=='.$buyer_name.'=='.$style_ref.'=='.$body_part_id.'<br>';
							$fin_rack=''; $shelf=''; $plan_cut_qty=0; $knit_recv_qty=0; $knit_issue_qty=0; $knit_recv_return_qty=0; $knit_issue_return_qty=0; $knit_transfer_in_qty=0; $knit_transfer_out_qty=0; $actual_cut_qty=0;
							foreach($poIds as $po_id)
							{
								$rack=''; $shelf='';
								$rack=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['rack'];
								if($fin_rack=='') $fin_rack=$rack; else $fin_rack.=', '.$rack;
								$shelf=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['shelf'];
								if($fin_shelf=='') $fin_shelf=$shelf; else $fin_shelf.=', '.$shelf;
								$plan_cut_qty+=$plan_cut_array[$po_id][$body_part_id][$color_id]['plan_cut_qnty_other'];

								$knit_recv_qty+=$knit_fin_recv_array[$po_id][$body_part_id][$color_id]['receive_qnty'];
								$knit_issue_qty+=$issue_qnty[$po_id][$body_part_id][$color_id]['issue_qnty'];
								//echo $knit_recv_qty.'ddd';
								$knit_recv_return_qty+=$finish_fab_data[$po_id][$color_id]['receive_return'];
								$knit_issue_return_qty+=$finish_fab_data[$po_id][$color_id]['issue_return'];
								$knit_transfer_in_qty+=$finish_fab_data[$po_id][$color_id]['transfer_in'];
								$knit_transfer_out_qty+=$finish_fab_data[$po_id][$color_id]['transfer_out'];

								$actual_cut_qty+=$actual_cut_qnty[$po_id][$color_id]['actual_cut_qty'];

								$dia=$dia_array[$pre_cost_id][$po_id][$color_id]['dia'];
							}
							$tot_knit_recv_qty=0; $tot_recv_bal=0; $tot_knit_issue_qty=0; $tot_knit_issue_qty=0; $tot_stock=0;
							$tot_knit_recv_qty=($knit_recv_qty+$knit_transfer_in_qty+$knit_issue_return_qty);
							$tot_recv_bal=$req_booking_qty-$tot_knit_recv_qty;
							$tot_knit_issue_qty=($knit_issue_qty+$knit_transfer_out_qty+$knit_recv_return_qty);
							$tot_stock=$tot_knit_recv_qty-$tot_knit_issue_qty;


							?>
	                        <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor2;?>')" id="trother<? echo $k;?>">
	                            <td width="30"><? echo $k; ?></td>
	                            <td width="90"><p><? echo $job_pre; ?></p></td>
	                            <td width="100"><p><? echo $buyer_name; ?></p></td>
	                            <td width="100"><p><? echo $style_ref; ?></p></td>
	                            <td width="80"><div style="word-break:break-all"><? echo $po_no; ?></div></td>
	                            <td width="70"><div style="word-break:break-all"><? echo $file_no; ?></div></td>
	                            <td width="80"><div style="word-break:break-all"><? echo $intRef; ?></div></td>

	                            <td width="100"><p><?  echo $body_part[$body_part_id]; //$body_part ?></p></td>
	                            <td width="80"><p><? echo $color_type[$color_type]; ?></p></td>
	                            <td width="120"><p><? echo $construction; ?></p></td>
	                            <td width="120"><p><? echo $composition; ?></p></td>
	                            <td width="45"><p><? echo $gsm_weight; ?></p></td>
	                            <td width="40"><p><? echo $dia; ?></p></td>
	                            <td width="70"><p><? echo $rack_name_arr[$fin_rack]['rack_name']; ?></p></td>
	                            <td width="70"><p><? echo $shelf_name_arr[$fin_rack][$fin_shelf]['shelf_name']; ?></p></td>
	                            <td width="75" align="right"><p><? echo $ship_date; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
	                            <td width="110"><p><? echo $color_arr[$color_id]; ?></p></td>
	                            <td width="50" align="center"><p><? echo $unit_of_measurement[$uom_id]; ?></p></td>
	                            <td width="80" align="right"><p><a href="##" onClick="generate_req_qty_report('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('job_no')];?>','<? echo $rows[csf('buyer_name')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','<? echo $rows[csf('booking_no')]; ?>','<? echo $rows[csf('booking_type')]; ?>','req_qty_main_short_other')"><? echo number_format($req_booking_qty,2); ?></a></p></td>
	                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','receive_popup');"><? echo number_format($tot_knit_recv_qty,2); ?></a></p></td>
	                            <td width="80" align="right"><p><? echo number_format($tot_recv_bal,2); ?></p></td>
	                            <td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $rows[csf('po_id')]; ?>','<? echo $rows[csf('body_part_id')]; ?>','<? echo $rows[csf('color_id')]; ?>','issue_popup');"><? echo number_format($tot_knit_issue_qty,2); ?></a></p></td>
	                            <td width="" align="right"><p><? echo number_format($tot_stock,2); ?></p></td>
	                        </tr>
	                    <?
							$k++;
							//$total_order_qnty_other+=$rows[csf("order_quantity_set")];
							$total_req_qty_other+=$req_booking_qty;
							$total_knit_recv_qty_other+=$tot_knit_recv_qty;
							$total_recv_bal_other+=$tot_recv_bal;
							$total_knit_stock_other_qty+=$tot_stock;
							$total_tot_knit_issue_qty_other+=$tot_knit_issue_qty;
							$total_qty_pcs_other+=$plan_cut_qty;

							$sub_req_qty_other+=$req_booking_qty;
							$sub_knit_recv_qty_other+=$tot_knit_recv_qty;
							$sub_recv_bal_other+=$tot_recv_bal;
							$sub_tot_knit_issue_qty_other+=$tot_knit_issue_qty;
							$sub_knit_stock_other_qty+=$tot_stock;


	                    }
					}
					?>
						<tr bgcolor="#CCCCCC" style="font-weight: bold;">
							<td colspan="18" align="right">UOM Total</td>
							<td align="center"><? echo $unit_of_measurement[$uom_id]; ?></td>
							<td align="right"><? echo number_format($sub_req_qty_other,2); ?></td>
							<td align="right"><? echo number_format($sub_knit_recv_qty_other,2); ?></td>
							<td align="right"><? echo number_format($sub_recv_bal_other,2); ?></td>
							<td align="right"><? echo number_format($sub_tot_knit_issue_qty_other,2); ?></td>
							<td align="right"><? echo number_format($sub_knit_stock_other_qty,2); ?></td>
						</tr>
					<?
					$sub_req_qty_other=$sub_knit_recv_qty_other=$sub_recv_bal_other=$sub_tot_knit_issue_qty_other=$sub_knit_stock_other_qty=0;
				}
				?>
				</table>
				<table width="1930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="45">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="75" align="right"><? //echo number_format($total_qty_pcs_other); ?></th>
						<th width="80" align="right"><? //echo number_format($total_qty_pcs_other); ?></th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="80" align="right"><? echo number_format($total_req_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_knit_recv_qty_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_recv_bal_other,2,'.',''); ?></th>
						<th width="80" align="right"><? echo number_format($total_tot_knit_issue_qty_other); ?></th>
						<th align="right"><? echo number_format($total_knit_stock_other_qty); ?></th>
					</tfoot>
				</table>
			</div>
			</fieldset>
			<?
		}
	}//Knit end
	else if($cbo_report_type==3) // Woven Finish Start
	{
		$fin_woven_recv_array=array();
		$sql_recv=sql_select("select c.id as prop_id,c.po_breakdown_id,c.color_id, c.quantity as finish_receive_qnty from inv_receive_master a,product_details_master b, order_wise_pro_details c where a.entry_form=c.entry_form and  b.id=c.prod_id and c.entry_form in (17) and c.trans_id!=0 and a.item_category=3 and a.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		foreach($sql_recv as $row)
		{
			if($chk_prop_id[$row[csf('prop_id')]]  == "")
			{
				$chk_prop_id[$row[csf('prop_id')]] = $row[csf('prop_id')];
				$fin_woven_recv_array[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_qnty']+=$row[csf('finish_receive_qnty')];
			}
		}
		/*echo '<pre>';
		echo 'total'. count($fin_woven_recv_array).'<br>';
		print_r($fin_woven_recv_array);
		die;*/
		$issue_qnty=array();
		$sql_issue=sql_select("select b.po_breakdown_id,  a.body_part_id, b.color_id,sum(b.quantity) as issue_qnty from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.id = b.trans_id and a.mst_id = c.id and c.entry_form in (19) and b.trans_id <> 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 group by b.po_breakdown_id, a.body_part_id,b.color_id");
		foreach( $sql_issue as $row_iss )
		{
			$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('body_part_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
		}

		$actual_cut_qnty=array();
		$sql_actual_cut_qty=sql_select(" select a.po_break_down_id,c.color_number_id, sum(b.production_qnty) as actual_cut_qty  from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=1 and a.is_deleted=0 and a.status_active=1  group by  a.po_break_down_id,c.color_number_id");
		foreach( $sql_actual_cut_qty as $row_actual)
		{
			$actual_cut_qnty[$row_actual[csf('po_break_down_id')]][$row_actual[csf('color_number_id')]]['actual_cut_qty']=$row_actual[csf('actual_cut_qty')];
		}
		?>
	    <fieldset style="width:1330px;">
	        <table cellpadding="0" cellspacing="0" width="1330">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?><br><? $report_type=(str_replace("'","",$cbo_report_type)); if($report_type==3) echo 'Woven Finish'; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
	            </tr>
	        </table>
	        <div align="left"><b>Main Fabric </b></div>
			<table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="50">UOM</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="80">Stock</th>
	                <th width="80">Cons/Pcs</th>
	                <th width="80">Possible Cut Pcs</th>
	                <th width="">Actual Cut</th>
	            </thead>
	        </table>
	        <div style="width:1400px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
	            <?
				// Woven Finish Start
						$sql_main="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons,c.uom  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id and c.body_part_id in(SELECT id from lib_body_part where body_part_type <> 30 and status_active=1 and is_deleted=0) and  c.fab_nature_id=3  $ship_date $buyer_id_cond $job_no_cond $order_id_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id,c.uom order by c.uom desc,a.buyer_name,b.id,a.job_no, b.po_number";
				//echo $sql_main;die;
				$i=1; $checkUOMArr = array(); $sub_uom="";$sub_woven_recv_qty=$sub_woven_balance_qty=$sub_woven_issue_qty=$sub_woven_stock_qty=$sub_woven_actual_cut_qty=0;
				$nameArray=sql_select( $sql_main );
				foreach ($nameArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if (!in_array($row[csf("uom")], $checkUOMArr))
					{
                        $checkUOMArr[$i] = $row[csf("uom")];
                        if ($i > 1)
                        {
                        	$sub_uom = implode(array_filter(array_unique(explode(",",chop($sub_uom,",")))));
                            ?>
                            <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                <td colspan="8" align="right"><b>UOM Total</b></td>
                                <td align="center"><? echo $unit_of_measurement[$sub_uom]; ?></td>
                                <td align="center"></td>
                                <td align="right"><? echo number_format($sub_woven_recv_qty,2,'.',''); ?></td>
                                <td align="right"><? echo number_format($sub_woven_balance_qty,2,'.',''); ?></td>
                                <td align="right"><? echo number_format($sub_woven_issue_qty,2,'.',''); ?></td>
                                <td align="right"><? echo number_format($sub_woven_stock_qty,2,'.',''); ?></td>
                                <td align="right" colspan="2"></td>
                                <td align="right"><? echo $sub_woven_actual_cut_qty; ?></td>
                            </tr>
                            <?
                            $sub_uom = "";
                            $sub_woven_recv_qty=$sub_woven_balance_qty=$sub_woven_issue_qty=$sub_woven_stock_qty=$sub_woven_actual_cut_qty=0;
                            //$tot_amount_qny = 0;
                        }
                    }

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
					//echo $dzn_qnty.'='.$row[csf("cons")];
					$order_id=$row[csf('po_id')];
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
					$cons_avg=$row[csf("avg_finish_cons")];
					$tot_cons_avg=$cons_avg/$dzn_qnty;
					$woven_req_qty=($row[csf("avg_finish_cons")]/$dzn_qnty)*$row[csf("order_quantity_set")];
					$woven_recv_qty=$fin_woven_recv_array[$order_id][$color_id]['receive_qnty'];
					//$woven_recv_return_qty=$fin_woven_recv_array[$order_id][$color_id]['receive_return'];
					$tot_woven_recv_qty=$woven_recv_qty;
					$tot_woven_recv_bal=$woven_req_qty-$tot_woven_recv_qty;
					$tot_woven_issue_qty=$issue_qnty[$order_id][$body_part_id][$color_id]['issue_qnty'];
					$tot_woven_stock=$tot_woven_recv_qty-$tot_woven_issue_qty;
					$tot_woven_actual_cut_qty=$actual_cut_qnty[$order_id][$color_id]['actual_cut_qty'];
					?>
	                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
	                    <td width="90"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
	                    <td width="80"><p><? echo $order_arr[$order_id]; ?></p></td>
	                    <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                    <td width="100"><p><? echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf("order_quantity_set")],2,'.',''); ?></p></td>
	                    <td width="110"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                    <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($woven_req_qty,2); ?></p></td>
	                    <td width="80" align="right" title="<? echo 'order='.$order_id.',color='.$color_id;?>"><p><? echo number_format($tot_woven_recv_qty,2);?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal,2); ?></p></td>
	                    <td width="80" align="right" title="<? echo 'order='.$order_id.',color='.$color_id.',body_part_id='.$body_part_id;?>"><p><? echo number_format($tot_woven_issue_qty,2); ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($tot_woven_stock,2); ?></p></td>
	                    <td width="80" align="right"><p><?  echo number_format($tot_cons_avg,2); ?></p></td>
	                    <td width="80" align="right"><p><? $possible_cut=$tot_knit_issue_qty/($tot_cons_avg/$dzn_qnty); echo number_format($possible_cut,2); ?></p></td>
	                    <td width="" align="right"><p><? echo number_format($tot_woven_actual_cut_qty,2);?></p></td>
	                </tr>
		            <?
		            $i++;
					$total_order_qnty+=$row[csf("order_quantity_set")];
					$total_woven_recv_qty+=$tot_woven_recv_qty;
					$total_woven_issue_qty+=$tot_woven_issue_qty;
					$total_woven_balance_qty+=$tot_woven_recv_bal;
					$total_woven_stock_qty+=$tot_woven_stock;
					$total_woven_actual_cut_qty+=$tot_woven_actual_cut_qty;

					$sub_uom .= $row[csf("uom")].",";
					$sub_woven_recv_qty+=$tot_woven_recv_qty;
					$sub_woven_balance_qty+=$tot_woven_recv_bal;
					$sub_woven_issue_qty+=$tot_woven_issue_qty;
					$sub_woven_stock_qty+=$tot_woven_stock;
					$sub_woven_actual_cut_qty+=$tot_woven_actual_cut_qty;

				}
				$sub_uom = implode(array_filter(array_unique(explode(",",chop($sub_uom,",")))));
				?>
					<tr bgcolor="#CCCCCC" style="font-weight: bold ">
                        <td colspan="8" align="right"><b>UOM Total</b></td>
                        <td align="center"><? echo $unit_of_measurement[$sub_uom]; ?></td>
                        <td align="center"></td>
                        <td align="right"><? echo number_format($sub_woven_recv_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_woven_balance_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_woven_issue_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_woven_stock_qty,2,'.',''); ?></td>
                        <td align="right" colspan="2"></td>
                        <td align="right"><? echo $sub_woven_actual_cut_qty; ?></td>
                    </tr>
	            </table>
	            <table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="50">&nbsp;</th>
	                    <th width="80" align="right"><? //echo number_format($total_req_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_balance_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_stock_qty,2,'.',''); ?></th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80" align="right"><? //echo number_format($total_possible_cut_pcs,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_actual_cut_qty,2,'.',''); ?></th>
	                </tfoot>
	            </table>
	        </div>
	        <div align="left"><b>Other Fabric </b></div>
			<table width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer</th>
	                <th width="90">Job</th>
	                <th width="80">Order No</th>
	                <th width="100">Style</th>
	                <th width="100">Body Part</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="110">Color</th>
	                <th width="50">UOM</th>
	                <th width="80">Req. Qty</th>
	                <th width="80">Total Recv.</th>
	                <th width="80">Recv. Balance</th>
	                <th width="80">Total Issued</th>
	                <th width="">Stock</th>
	            </thead>
	        </table>
	        <div style="width:1180px; max-height:350px; overflow-y:scroll;" id="scroll_body2">
				<table width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body2" >
	            	<?
					// Knit Finish Start
					$sql_other2="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, sum(d.order_quantity/a.total_set_qnty) as order_quantity_set, b.id as po_id, b.po_number,d.color_number_id as color_id,c.body_part_id,c.avg_finish_cons,c.uom  from  wo_po_details_master a, wo_po_break_down b,  wo_pre_cost_fabric_cost_dtls  c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and c.job_no=a.job_no and d.job_no_mst=c.job_no  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_id and c.body_part_id in(SELECT id from lib_body_part where body_part_type=30 and status_active=1 and is_deleted=0)  and c.fab_nature_id=3   $ship_date $buyer_id_cond $job_no_cond $order_id_cond group by b.id,c.body_part_id,d.color_number_id,a.job_no_prefix_num,c.avg_finish_cons, a.job_no, a.buyer_name, a.style_ref_no, b.po_number,b.plan_cut,d.color_number_id,c.body_part_id,c.uom order by c.uom desc, a.buyer_name,b.id,a.job_no, b.po_number";
					//echo $sql_other2;die;
					$k=1; $sub_uom="";$checkUOMArr = array();$sub_woven_req_qty_other=$sub_woven_recv_qty_other=$sub_woven_recv_bal_other=$sub_woven_issue_qty_other=$sub_woven_stock_other=0;
					$other_data=sql_select( $sql_other2 );
					foreach ($other_data as $rows)
					{
						if ($k%2==0) $bgcolor2="#E9F3FF"; else $bgcolor2="#FFFFFF";

						if (!in_array($rows[csf("uom")], $checkUOMArr))
						{
                            $checkUOMArr[$k] = $rows[csf("uom")];
                            if ($k > 1)
                            {
                            	$sub_uom = implode(array_filter(array_unique(explode(",",chop($sub_uom,",")))));
                                ?>
                                <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                    <td colspan="8" align="right"><b>UOM Total</b></td>
                                    <td align="center"><? echo $unit_of_measurement[$sub_uom]; ?></td>
                                    <td align="right"><? echo number_format($sub_woven_req_qty_other,2); ?></td>
			                        <td align="right"><? echo number_format($sub_woven_recv_qty_other,2); ?></td>
			                        <td align="right"><? echo number_format($sub_woven_recv_bal_other,2); ?></td>
			                        <td align="right"><? echo number_format($sub_woven_issue_qty_other,2); ?></td>
			                        <td align="right"><? echo number_format($sub_woven_stock_other,2); ?></td>
                                </tr>
                                <?
                                $sub_uom = "";
                               	$sub_woven_req_qty_other=$sub_woven_recv_qty_other=$sub_woven_recv_bal_other=$sub_woven_issue_qty_other=$sub_woven_stock_other=0;
                            }
                        }


						$dzn_qnty=0;
						if($costing_per_id_library[$rows[csf('job_no')]]==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id_library[$rows[csf('job_no')]]==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id_library[$rows[csf('job_no')]]==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id_library[$rows[csf('job_no')]]==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						//echo $dzn_qnty.'='.$row[csf("avg_finish_cons")];
						$order_id_other=$rows[csf('po_id')];
						$color_id_other=$rows[csf("color_id")];
						$body_part_id_other=$rows[csf("body_part_id")];
						//$tot_cons_avg_other=$cons_avg/$rows[csf("pcs")];
						$woven_req_qty_other=($rows[csf("avg_finish_cons")]/$dzn_qnty)*$rows[csf("order_quantity_set")];
						$woven_recv_qty_other=$fin_woven_recv_array[$order_id_other][$body_part_id_other][$color_id_other]['receive_qnty'];
						$tot_woven_recv_qty_other=$woven_recv_qty_other;
						$tot_woven_recv_bal_other=$woven_req_qty_other-$tot_woven_recv_qty_other;
						$tot_woven_issue_qty_other=$issue_qnty[$order_id_other][$body_part_id_other][$color_id_other]['issue_qnty'];
						$tot_woven_stock_other=$tot_woven_recv_qty_other-$tot_woven_issue_qty_other;
						//echo $woven_req_qty_other.'joy';
						//$tot_actual_cut_qty_other=$actual_cut_qnty[$order_id_other][$color_id_other]['actual_cut_qty'];
						?>
		                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('trother<? echo $k;?>','<? echo $bgcolor;?>')" id="trother<? echo $k;?>">
		                    <td width="30"><? echo $k; ?></td>
		                    <td width="100"><p><? echo $buyer_arr[$rows[csf("buyer_name")]]; ?></p></td>
		                    <td width="90"><p><? echo $rows[csf("job_no_prefix_num")]; ?></p></td>
		                    <td width="80"><p><? echo $rows[csf("po_number")]; ?></p></td>
		                    <td width="100"><p><? echo $rows[csf("style_ref_no")]; ?></p></td>
		                    <td width="100"><p><? echo $body_part[$rows[csf("body_part_id")]]; ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($rows[csf("order_quantity_set")],2,'.',''); ?></p></td>
		                    <td width="110"><p><? echo $color_arr[$rows[csf("color_id")]]; ?></p></td>
		                    <td width="50"><p><? echo $unit_of_measurement[$rows[csf("uom")]]; ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($woven_req_qty_other,2); ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_qty_other,2); ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($tot_woven_recv_bal_other,2); ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($tot_woven_issue_qty_other,2); ?></p></td>
		                    <td width="" align="right"><p><? echo number_format($tot_woven_stock_other,2); ?></p></td>
		                </tr>
			            <?
			            $k++;
						$total_order_qnty_other+=$rows[csf("order_quantity_set")];
						$total_woven_req_qty_other+=$woven_req_qty_other;
						$total_woven_recv_qty_other+=$tot_woven_recv_qty_other;
						$total_woven_recv_bal_other+=$tot_woven_recv_bal_other;
						$total_woven_stock_other+=$tot_woven_stock_other;
						$total_woven_issue_qty_other+=$tot_woven_issue_qty_other;

						$sub_uom .= $rows[csf("uom")].",";
						$sub_woven_req_qty_other+=$woven_req_qty_other;
						$sub_woven_recv_qty_other+=$tot_woven_recv_qty_other;
						$sub_woven_recv_bal_other+=$tot_woven_recv_bal_other;
						$sub_woven_issue_qty_other+=$tot_woven_issue_qty_other;
						$sub_woven_stock_other+=$tot_woven_stock_other;

						$sub_woven_req_qty_other=$sub_woven_recv_qty_other=$sub_woven_recv_bal_other=$sub_woven_issue_qty_other=$sub_woven_stock_other=0;
					}
					$sub_uom = implode(array_filter(array_unique(explode(",",chop($sub_uom,",")))));
					if(count($other_data) > 0){
                    ?>
                    <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                        <td colspan="8" align="right"><b>UOM Total</b></td>
                        <td align="center"><? echo $unit_of_measurement[$sub_uom]; ?></td>
                        <td align="right"><? echo number_format($sub_woven_req_qty_other,2); ?></td>
                        <td align="right"><? echo number_format($sub_woven_recv_qty_other,2); ?></td>
                        <td align="right"><? echo number_format($sub_woven_recv_bal_other,2); ?></td>
                        <td align="right"><? echo number_format($sub_woven_issue_qty_other,2); ?></td>
                        <td align="right"><? echo number_format($sub_woven_stock_other,2); ?></td>
                    </tr>
                    <? }?>
	            </table>
	            <table width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
	                <tfoot>
	                    <th width="30"></th>
	                    <th width="100"></th>
	                    <th width="90"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="80" align="right" id=""><? echo number_format($total_order_qnty_other,2,'.',''); ?></th>
	                    <th width="110">&nbsp;</th>
	                    <th width="50">&nbsp;</th>
	                    <th width="80" align="right"><? echo number_format($total_woven_req_qty_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_qty_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_recv_bal_other,2,'.',''); ?></th>
	                    <th width="80" align="right"><? echo number_format($total_woven_issue_qty_other,2,'.',''); ?></th>
	                    <th width="" align="right"><? echo number_format($total_woven_stock_other,2,'.',''); ?></th>
	                </tfoot>
	            </table>
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

    exit();
}
if($action=="req_qty_main_short")
{
	echo load_html_head_contents("Req Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no.'aziz';die;
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
<fieldset style="width:570px; margin-left:3px">
		<div align="center">
           <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
		<div id="report_container" align="center">

        <table  border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
         <tr>
        <td colspan="3" align="center"><strong> Main/Short Fabrics Details</strong></td>
        </tr>
        <tr>
        <td width="150" align="center" colspan="3"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td>
        </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                    <thead>
                        <th width="30">Sl</th>
                        <th width="30">Order No</th>
                        <th width="70">Booking No</th>
                        <th width="80">Booking Type</th>
                        <th width="80">Req.Qty.</th>
                    </thead>
                <tbody>
                <?
					$i=1;
					$booking_data=("select d.po_break_down_id,e.booking_no_prefix_num as booking_no, d.fabric_color_id,d.is_short,
					sum(d.fin_fab_qnty) as main_fin_fab_qnty
					FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d ,wo_booking_mst e
					WHERE a.job_no=b.job_no and
					a.id=b.pre_cost_fabric_cost_dtls_id and
					c.job_no_mst=a.job_no and

					d.job_no=a.job_no and
					c.id=b.color_size_table_id and
					d.fabric_color_id=$color_id and
					b.po_break_down_id=d.po_break_down_id and
					d.booking_no=e.booking_no and
					b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and


					a.body_part_id='$body_part_id' and
					d.job_no='$job_no' and
					d.po_break_down_id='$po_id' and
					d.is_short in(2) and
					d.booking_type=1 and
					d.status_active=1 and
					d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,d.is_short,e.booking_no_prefix_num");
					//echo $booking_data;
					$sql_result=sql_select($booking_data);
					foreach($sql_result as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$is_short=$row[csf('is_short')];

							if($is_short==1) $type='Short';
							else if($is_short==2) $type='main';
							else $type='';
							$booking_no=$row[csf('booking_no')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>

                            <td width="70"><p><? echo $booking_no; ?></p></td>
                            <td width="80"><p><? echo $type; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('main_fin_fab_qnty')],2); ?></p></td>

                        </tr>
						<?
						$tot_req_qty_main+=$row[csf('main_fin_fab_qnty')];
						$tot_amount+=$tot_cons_amount;
						$i++;
					}
				$booking_data2=("select d.po_break_down_id,d.fabric_color_id,d.is_short,
					sum(d.fin_fab_qnty)  as short_fin_fab_qnty,e.booking_no_prefix_num as booking_no
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d ,wo_booking_mst e
					WHERE
					a.id=d.pre_cost_fabric_cost_dtls_id and
					 e.job_no=a.job_no  and
					d.fabric_color_id=$color_id and

					d.job_no=a.job_no and
					d.booking_no=e.booking_no and
					d.po_break_down_id='$po_id' and

					a.body_part_id='$body_part_id' and
					d.job_no='$job_no' and

					d.is_short in(1) and
					d.booking_type=1  and
					d.status_active=1 and
					d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,d.is_short,e.booking_no_prefix_num");


					$sql_result2=sql_select($booking_data2);

					foreach($sql_result2 as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$is_short=$row[csf('is_short')];
							$booking_no=$row[csf('booking_no')];

							if($is_short==1) $type='short';
							else if($is_short==2) $type='main';
							else $type='';
						if($row[csf('short_fin_fab_qnty')]>0)
						{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="30"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                            <td width="70"><p><? echo $booking_no; ?></p></td>
                            <td width="80"><p><? echo $type; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('short_fin_fab_qnty')],2); ?></p></td>
                        </tr>
                        <?
                        }

						$tot_req_qty_short+=$row[csf('short_fin_fab_qnty')];
						$tot_amount+=$tot_cons_amount;
						$total_req_qty=$tot_req_qty_short+$tot_req_qty_main;
						$i++;
					}

				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                     	 <td>&nbsp; </td>
                    	<td align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_req_qty_short+$tot_req_qty_main,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="req_qty_main_short_other")
{
	echo load_html_head_contents("Req Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no.'aziz';die;
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
<fieldset style="width:570px; margin-left:3px">
		 <div align="center">
           <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
		<div id="report_container" align="center">
        <table  border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
             <tr>
            <td colspan="4" align="center"><strong>Other Fabrics Details</strong></td>
            </tr>
            <tr>
            <td width="150" align="center" colspan="4"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td>
            </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                    <thead>
                        <th width="30">Sl</th>
                        <th width="70">Order No</th>
                        <th width="70">Booking No</th>
                        <th width="80">Booking Type</th>
                        <th width="80">Req.Qty.</th>
                    </thead>
                <tbody>
                <?
					$i=1;//$order_arr
					$booking_data=("select d.po_break_down_id,e.booking_no_prefix_num as booking_no,d.fabric_color_id,d.is_short,
					SUM( d.fin_fab_qnty) as main_fin_fab_qnty
					FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d ,wo_booking_mst e
					WHERE a.job_no=b.job_no and
					a.id=b.pre_cost_fabric_cost_dtls_id and
					c.job_no_mst=a.job_no and
					c.id=b.color_size_table_id and
					d.fabric_color_id=$color_id and
					a.body_part_id='$body_part_id' and
					b.po_break_down_id=d.po_break_down_id and
					d.booking_no=e.booking_no and

					d.job_no=a.job_no and
					b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and


					d.po_break_down_id='$po_id' and
					d.job_no='$job_no' and
					d.is_short in(2) and
					e.item_category=2 and
					d.booking_type=1 and
					d.status_active=1 and
					d.is_deleted=0 group by d.po_break_down_id,d.fabric_color_id,d.is_short,e.booking_no_prefix_num");
					$sql_result=sql_select($booking_data);
					foreach($sql_result as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$is_short=$row[csf('is_short')];
							$booking_no=$row[csf('booking_no')];

							if($is_short==1) $type='Short';
							else if($is_short==2) $type='Main';
							else $type='';
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="70"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                             <td width="70"><p><? echo $booking_no; ?></p></td>
                            <td width="80"><p><? echo $type; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('main_fin_fab_qnty')],2); ?></p></td>

                        </tr>
						<?
						$tot_req_qty_main+=$row[csf('main_fin_fab_qnty')];
						$tot_amount+=$tot_cons_amount;
						$i++;
					}
				  $booking_data2=("select a.id,d.po_break_down_id,d.fabric_color_id,a.body_part_id,d.booking_type,d.is_short,
					SUM (d.fin_fab_qnty) as short_fin_fab_qnty,e.booking_no_prefix_num as booking_no
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d ,wo_booking_mst e
					WHERE
					a.id=d.pre_cost_fabric_cost_dtls_id and
					 e.job_no=a.job_no  and
					d.fabric_color_id=$color_id and
					d.booking_no=e.booking_no and
					d.job_no=a.job_no
					and d.po_break_down_id='$po_id' and
					a.body_part_id='$body_part_id' and
					d.job_no='$job_no' and
					d.is_short in(1) and
					d.booking_type=1  and
					e.item_category=2 and
					d.status_active=1 and
					d.is_deleted=0 group by a.id,d.po_break_down_id,d.fabric_color_id,d.booking_type,a.body_part_id,d.is_short,e.booking_no_prefix_num");
					$sql_result2=sql_select($booking_data2);
					foreach($sql_result2 as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$is_short=$row[csf('is_short')];
							$booking_no=$row[csf('booking_no')];

							if($is_short==1) $type='short';
							else if($is_short==2) $type='main';
							else $type='';
						if($row[csf('short_fin_fab_qnty')]>0)
						{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                            <td width="70"><p><? echo $booking_no; ?></p></td>
                            <td width="80"><p><? echo $type; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('short_fin_fab_qnty')],2); ?></p></td>
                        </tr>
                        <?
                        }

						$tot_req_qty_short+=$row[csf('short_fin_fab_qnty')];
						$tot_amount+=$tot_cons_amount;
						$total_req_qty_up=$tot_req_qty_short+$tot_req_qty_main;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td></td>
                    	<td align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_req_qty,2); ?></td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_req_qty_short+$tot_req_qty_main,2); ?></td>
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
	//echo "DDDD";die;
	?>
	<fieldset style="width:720px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Receive Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Receive ID</th>
						<th width="150">Supplier</th>
                        <th width="80">Prod. ID</th>
                        <th width="75">Receive Date</th>
                        <th width="200">Fabric Des.</th>
                        <th width="80">Rack</th>
                        <th width="80">Qty</th>
                        <th>Reject Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
					$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
					$i=1;
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($gsm_weight!='') $gsm_weight_cond=" and b.gsm=$gsm_weight"; else $gsm_weight_cond="";
					if($db_type==0)
					{
						$mrr_sql="select a.recv_number, a.receive_date, group_concat(b.rack_no) as rack_no, b.prod_id, sum(c.quantity) as quantity, sum(returnable_qnty) as returnable_qnty, a.knitting_company, a.knitting_source
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.color_id='$color' and c.trans_id!=0 $body_part_cond $gsm_weight_cond group by a.recv_number, a.receive_date, b.prod_id, a.knitting_company, a.knitting_source";
					}
					else
					{
						$mrr_sql="select a.recv_number, a.receive_date, listagg(cast(b.rack_no as varchar(4000)),',')  within group (order by b.rack_no) as rack_no, b.prod_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, a.knitting_company, a.knitting_source from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68,225) and c.entry_form in (7,37,66,68,225)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.color_id='$color' and c.trans_id!=0 and c.trans_type=1  $body_part_cond $gsm_weight_cond group by a.recv_number, a.receive_date, b.prod_id, a.knitting_company, a.knitting_source";
					}

					// echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="150"><p><? if($row[csf('knitting_source')]==3){ echo $supllier_arr[$row[csf('knitting_company')]];} else {echo $company_arr[$row[csf('knitting_company')]];} ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $rack=implode(",",array_unique(explode(",",$row[csf('rack_no')]))); ?>&nbsp;</p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td align="right"><p><? echo number_format($tot_reject,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Return ID</th>
                        <th width="80">Prod. ID</th>
                        <th width="75">Return Date</th>
                        <th width="250">Fabric Des.</th>
                        <th width="80">Rack</th>
                        <th width="80">Qty</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($db_type==0)
					{
						$ret_sql="select a.recv_number, a.receive_date, group_concat(b.rack) as rack_no, b.prod_id, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.recv_number, a.receive_date, b.prod_id";
					}
					else
					{
						$ret_sql="select a.recv_number, a.receive_date, listagg(cast(b.rack as varchar(4000)),',')  within group (order by b.rack) as rack_no,  b.prod_id, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.recv_number, a.receive_date, b.prod_id";
					}
					//echo $ret_sql;

					$retDataArray=sql_select($ret_sql);

					foreach($retDataArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="250" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $rack=implode(",",array_unique(explode(",",$row[csf('rack_no')]))); ?>&nbsp;</p></td>
                            <td  width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('returnable_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$tot_returnable_qnty+=$row[csf('returnable_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_returnable_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Transfer ID</th>
                        <th width="70">Transfer Date</th>
                        <th width="250">Fabric Des.</th>
                        <th width="50">UOM</th>
                        <th>Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					/*select b.po_breakdown_id, b.color_id,
							sum(CASE WHEN b.entry_form in (46) THEN b.quantity END) AS receive_return,
							sum(CASE WHEN b.entry_form in (52) THEN b.quantity END) AS issue_return,
							sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=5 THEN b.quantity END) AS transfer_in,
							sum(CASE WHEN b.entry_form in (14,15) and b.trans_type=6 THEN b.quantity END) AS transfer_out
							from order_wise_pro_details b
							where b.entry_form in (46,52,14,15) and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_breakdown_id, b.color_id
					*/					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(c.quantity) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15) and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);

					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="250" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
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
                    	<td colspan="5" align="right">Total Receive Balance</td>
                        <td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_issue_return_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="grey_used_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "DDDD";die;
	?>
	<fieldset style="width:650px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Receive ID</th>
                        <th width="80">Prod. ID</th>
                        <th width="75">Receive Date</th>
                        <th width="200">Fabric Des.</th>
                        <th width="80">Rack</th>
                        <th width="80">Qty</th>
						<th width="80">Grey Used Qty</th>
                        <th>Reject Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($gsm_weight!='') $gsm_weight_cond=" and b.gsm=$gsm_weight"; else $gsm_weight_cond="";
					if($db_type==0)
					{
						$mrr_sql="SELECT a.recv_number, a.receive_date, group_concat(b.rack_no) as rack_no, b.prod_id, sum(c.quantity) as quantity, sum(returnable_qnty) as returnable_qnty
						from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.color_id='$color' and c.trans_id!=0 $body_part_cond $gsm_weight_cond group by a.recv_number, a.receive_date, b.prod_id";
					}
					else
					{
						$mrr_sql="SELECT a.recv_number, a.receive_date, listagg(cast(b.rack_no as varchar(4000)),',')  within group (order by b.rack_no) as rack_no, b.prod_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty, sum(c.grey_used_qty) as grey_used_qty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
						 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68,225) and c.entry_form in (7,37,66,68,225)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.color_id='$color' and c.trans_id!=0 and c.trans_type=1  $body_part_cond $gsm_weight_cond group by a.recv_number, a.receive_date, b.prod_id";
					}

					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					$tot_qty=$tot_grey_used_qty=$tot_reject_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $rack=implode(",",array_unique(explode(",",$row[csf('rack_no')]))); ?>&nbsp;</p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('grey_used_qty')],2); ?></p></td>
                            <td align="right"><p><? echo number_format($tot_reject,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_grey_used_qty+=$row[csf('grey_used_qty')];
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_grey_used_qty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );

	/*$sql_transfer_out="select a.id, a.from_order_id, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 group by a.from_order_id,b.from_prod_id,a.id";
	$data_transfer_out_array=sql_select($sql_transfer_out);
	if(count($data_transfer_out_array)>0)
	{
		foreach( $data_transfer_out_array as $row )
		{
			$transfer_out_arr[$row[csf('from_order_id')]][$product_array[$row[csf('from_prod_id')]]]+=$row[csf('transfer_out_qnty')];
		}
	}*///var_dump ($transfer_in_arr);
	?>
<!--<div style="width:580px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:720px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="7">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Issue ID</th>
						<th width="150">Supplier</th>
                        <th width="70">Issue Date</th>
                        <th width="200">Fabric Des.</th>
                        <th width="80">Cut Unit No</th>
                        <th>Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
					$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id, b.cutting_unit, c.quantity, a.knit_dye_company 
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=18 and c.entry_form=18 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color' $body_part_cond";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="150"><p><? echo $supllier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="80" ><p><? echo $cutting_floor_library[$row[csf('cutting_unit')]]; ?> &nbsp;</p></td>
                            <td  align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
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
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="6">Receive Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="120">Return ID</th>
						<th width="150">Supplier</th>
                        <th width="70">Return Date</th>
                        <th width="200">Fabric Des.</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$ret_sql="select a.issue_number, a.issue_date, b.prod_id, sum(c.quantity) as quantity, d.knitting_company, d.knitting_source
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c , inv_receive_master d
						where a.id=b.mst_id and b.id=c.trans_id and a.RECEIVED_ID=d.id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.issue_number, a.issue_date, b.prod_id, d.knitting_company, d.knitting_source";
						
					$retDataArray=sql_select($ret_sql);

					foreach($retDataArray as $row) 
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="150"><p><? if($row[csf('knitting_source')]==3){ echo $supllier_arr[$row[csf('knitting_company')]];} else {echo $company_arr[$row[csf('knitting_company')]];}  ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td  align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_ret_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                 <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
                    </tr>

                </tfoot>
                </table>
                <br>
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
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

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
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
                        <td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty+$tot_ret_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>

            </table>
        </div>
    </fieldset>
    <?
	exit();
}

?>