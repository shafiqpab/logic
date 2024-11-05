<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_working_company")
{
    $data=explode("**", $data);
    if(empty($data[0]))
    {
    	$sql="SELECT a.id,a.supplier_name as name  FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company in ($data[1])  and a.status_active=1 group by a.id,a.supplier_name  union all select comp.id, comp.company_name as name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by name";
        $sql="";
        echo create_drop_down( "cbo_working_company", 160, $sql,"id,name", 1, "-- Select Company --", $selected, "","","","","","",3 ); 
    }
    else if($data[0]==1)
    {
        echo create_drop_down("cbo_working_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name",1,"-- Select Company --", $selected,"","","");
    }else{
        $sql="SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company in ($data[1])  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
       // echo $sql;
        echo create_drop_down( "cbo_working_company", 160, $sql,"id,supplier_name", 1, "-- Select Company --", $selected, "","","","","","",3 ); 
    }
             
    exit();
}
if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
}

if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_index = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_index.push(str[0]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1] && selected_index[i]==str[0])
					{
						selected_id.splice(i, 1);
						selected_name.splice(i, 1);
						selected_index.splice(i, 1);
						break;
					}
				}
				
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:1080px;">
				<table width="1070" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Style</th>
						<th>PO</th>
						<th>Job</th>
						<th>Wo No</th>
						<th>Bill No</th>
						<th>Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center" >
								<input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" style="width: 100px;justify-content: center;text-align: center;">
							</td>
							<td align="center">
								<input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width: 100px;justify-content: center;text-align: center;">
							</td> 
							<td align="center">
								<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width: 100px;justify-content: center;text-align: center;">
							</td>
							<td align="center">
								<input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width: 100px;justify-content: center;text-align: center;">
							</td>
							<td align="center">
								<input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width: 100px;justify-content: center;text-align: center;">
							</td>                 
							
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('txt_style_ref_no').value + '**' + document.getElementById('txt_po_no').value + '**' + document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_wo_no').value+ '**' + document.getElementById('txt_bill_no').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'piece_rate_wo_bill_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);

	
	$company_id = $data[0];
	$txt_style_ref_no=$data[1];
	$txt_po_no=$data[2];
	$txt_job_no=$data[3];
	$txt_wo_no=$data[4];
	$txt_bill_no=$data[5];
	$start_date = $data[6];
	$end_date = $data[7];
	

	

	$job_po_cond_wo='';
	$wo_no_con='';
	$search_field_cond='';
	$wo_date_con='';
	$date_search_cond ='';

	if(!empty($company_id))
	{
		$company_con=" and  a.company_id in ($company_id) ";
	}

	 if( !empty($txt_job_no))
	 {
	 	$job_po_cond = " and a.job_no_prefix_num=".trim($txt_job_no);   
	 }
	 if(!empty($txt_po_no))
	 {
	 	$job_po_cond.= "  and c.po_number like('%".trim($txt_po_no)."%') ";  
	 }

	if(!empty($txt_wo_no))
	{
		$wo_no_con=" and a.sys_number_prefix_num=" . $txt_wo_no ;
	}
	if(!empty($txt_style_ref_no))
	{
		
		$wo_no_con.=	"  and LOWER(b.style_ref) like LOWER('%" . $txt_style_ref_no . "%') ";
	}
	if(!empty($txt_bill_no))
	{
		$search_field_cond=	" and a.sys_number_prefix_num=" . $txt_bill_no ;
	}

	 $sql = "select a.id,a.job_no,a.job_no_prefix_num,c.id as po_id,c.po_number from wo_po_details_master a,wo_po_break_down c where  a.job_no=c.job_no_mst and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $job_po_cond  "; 
	 $sql_job_po=sql_select( $sql);
	 $po_ids=array();
	 foreach ($sql_job_po as $row) {
	 	array_push($po_ids, $row[csf('po_id')]);
	 }
	 $po_ids=array_unique($po_ids);
	 if(count($po_ids))
	 {
	 	$job_po_cond_wo= where_con_using_array($po_ids,0,"b.po_id");
	 }

	if($db_type==0)
	{
		if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_search_cond ="";

		if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
	}

	else if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_search_cond ="";

		if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
	}


	$kw_sql="SELECT a.sys_number as wo_no,a.id as wo_id,a.wo_date,a.company_id,b.id,b.job_id,b.po_id,b.style_ref from piece_rate_wo_urmi_mst a , piece_rate_wo_urmi_dtls b where  a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $wo_date_con $wo_no_con $job_po_cond_wo ";


	

	//echo $kw_sql;

	$kw_result=sql_select($kw_sql);
	$data_arr=array();
	$wo_dtls_ids=array();
	$all_po_ids=array();
	foreach ($kw_result as $row) {
		
		array_push($wo_dtls_ids, $row[csf('id')]);
		array_push($all_po_ids, $row[csf('po_id')]);
		$data_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$data_arr[$row[csf('id')]]['wo_id']=$row[csf('wo_id')];
		$data_arr[$row[csf('id')]]['wo_no']=$row[csf('wo_no')];
		$data_arr[$row[csf('id')]]['wo_date']=$row[csf('wo_date')];
		$data_arr[$row[csf('id')]]['job_id']=$row[csf('job_id')];
		$data_arr[$row[csf('id')]]['po_id']=$row[csf('po_id')];
		$data_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref')];
		
		
	}

	
	$wo_dtls_cond='';
	 $wo_dtls_ids=array_unique($wo_dtls_ids);
	 if(count($wo_dtls_ids))
	 {
	 	$wo_dtls_cond= where_con_using_array($wo_dtls_ids,0,"b.wo_dtls_id");
	 }

	

  
	$sql="SELECT a.sys_number as bill_no, a.bill_date,c.sys_number as wo_no,d.id as wo_dtls_id,d.style_ref,(select e.job_no from wo_po_details_master e where e.status_active=1 and d.job_id=e.id) as job_no , (select h.po_number from wo_po_break_down h where h.status_active=1 and d.po_id=h.id) as po_number 
	from piece_rate_bill_mst a,piece_rate_bill_dtls b ,piece_rate_wo_urmi_mst c,piece_rate_wo_urmi_dtls d 
	where a.id=b.mst_id and c.id=d.mst_id and b.wo_dtls_id=d.id and a.status_active=1 and b.status_active=1  and c.status_active=1 and d.status_active=1   $date_search_cond  $company_con $search_field_cond $wo_dtls_cond order by a.id,b.id";

	//echo $sql;

	

	

  
    echo create_list_view("tbl_list_search", "Style Ref. No,PO No,Job No,Wo No,Bill No, Bill Date", "150,150,115,140,170", "1000", "270", 0, $sql, "js_set_value_job", "wo_dtls_id,wo_no", "", 1, "", $arr, "style_ref,po_number,job_no,wo_no,bill_no,bill_date", "", '', '', '',1);
    exit();
} 

 
if($action=="report_generate")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$location_arr=return_library_array( "select id, location_name from lib_location group by id, location_name", "id", "location_name");
	
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$date_type = str_replace("'","",trim($cbo_date_type));
	$hide_wo_dtls_id = str_replace("'","",trim($hide_wo_dtls_id));
	$txt_wo_no = str_replace("'","",trim($txt_wo_no));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_working_company=str_replace("'","",trim($cbo_working_company));
	$cbo_buyer_name=str_replace("'","",trim($cbo_buyer_name));
	$cbo_source=str_replace("'","",trim($cbo_source));
	
	$cbo_date_type=str_replace("'","",trim($cbo_date_type));
	$wo_company_con='';
	$company_con='';
	$buyer_cond='';
	//echo $cbo_search_by.'--'.$txt_search_common;die;
	if(!empty($cbo_working_company) )
	{
		$wo_company_con=" and a.working_company_id in ($cbo_working_company)";
	}
	if(!empty($cbo_buyer_name) )
	{
		$buyer_cond=" and b.buyer_id =$cbo_buyer_name";
	}
	if(!empty($cbo_company_id))
	{
		$company_con=" and  a.company_id in ($cbo_company_id) ";
	}

	$wo_no_con='';
	$search_field_cond='';
	$wo_date_con='';
	$date_search_cond ='';

	$job_po_cond_wo='';

	$source_cond='';

	$order_by="a.company_id, b.id ";

	if(!empty($cbo_source))
	{
		$source_cond=" and a.source=$cbo_source";
	}

	/*
	if(!empty($txt_search_common))
	{
		if($cbo_search_by==1)
		{
			//$wo_no_con=" and b.style_ref=" . $txt_search_common ;
			$wo_no_con=	" and LOWER(b.style_ref) like LOWER('%" . $txt_search_common . "%')";
		}
		
		else if($cbo_search_by==2 || $cbo_search_by==3)
		{
			

			 if($cbo_search_by==2 )
			 {
			 	$job_po_cond = " and a.job_no_prefix_num=".trim($txt_search_common);   
			 }else{
			 	$job_po_cond = " and c.po_number like('%".trim($txt_search_common)."%') ";  
			 }

			 $sql = "select a.id,a.job_no,a.job_no_prefix_num,c.id as po_id,c.po_number from wo_po_details_master a,wo_po_break_down c where  a.job_no=c.job_no_mst and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $job_po_cond  "; 
			 $sql_job_po=sql_select( $sql);
			 $po_ids=array();
			 foreach ($sql_job_po as $row) {
			 	array_push($po_ids, $row[csf('po_id')]);
			 }
			 $po_ids=array_unique($po_ids);
			 if(count($po_ids))
			 {
			 	$job_po_cond_wo= where_con_using_array($po_ids,0,"b.po_id");
			 }
		}
		
		else if($cbo_search_by==4)
		{
			$wo_no_con=" and a.sys_number_prefix_num=" . $txt_search_common ;
		}
		else{
			$search_field_cond=	" and a.sys_number_prefix_num=" . $txt_search_common ;
		}
	}
	*/

	


	$wo_dtls_cond_wo='';

	if(!empty($txt_wo_no) && !empty($hide_wo_dtls_id))
	{
		$h_wo_dtls_ids=explode(",", $hide_wo_dtls_id);
		$h_wo_dtls_ids=array_unique($h_wo_dtls_ids);
		if(count($h_wo_dtls_ids))
		{
			$wo_dtls_cond_wo=where_con_using_array($h_wo_dtls_ids,0,"b.id");
		}
	}
	else
	{
		if($date_type==1)
		{
			
			

			if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_search_cond ="";
			}

			if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_search_cond ="";
			}
			$order_by=" a.company_id, a.id ";
			
		}else{
			
			if($db_type==0)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
			}

			if($db_type==2)
			{
				if ($start_date!="" &&  $end_date!="") $wo_date_con  = "and a.wo_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
			}
			$order_by=" a.company_id,b.wo_dtls_id ";
		}
	}

	if($date_type==1)
	{
		$order_by=" a.company_id, a.id ";
	}
	else
	{
		$order_by=" a.company_id,b.wo_dtls_id ";
	}


	

	$kw_sql="SELECT a.sys_number as wo_no,a.id as wo_id,a.wo_date,a.company_id,a.cbo_source as source,a.working_company_id,a.rate_for,b.id,b.order_source,b.ord_recev_company,b.job_id,b.po_id,b.buyer_id,b.item_id,b.style_ref,b.color_type,b.client_id,b.wo_qty,b.po_qty,b.uom,b.avg_rate,b.amount,b.remarks, a.pay_mode from piece_rate_wo_urmi_mst a , piece_rate_wo_urmi_dtls b where  a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $wo_date_con  $wo_dtls_cond_wo $buyer_cond";
	//echo "<pre>".$kw_sql."</pre>";

	//echo $kw_sql;

	$kw_result=sql_select($kw_sql);
	$data_arr=array();
	$wo_dtls_ids=array();
	$all_po_ids=array();
	foreach ($kw_result as $row) {
		
		array_push($wo_dtls_ids, $row[csf('id')]);
		array_push($all_po_ids, $row[csf('po_id')]);
		$data_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$data_arr[$row[csf('id')]]['wo_id']=$row[csf('wo_id')];
		$data_arr[$row[csf('id')]]['wo_no']=$row[csf('wo_no')];
		$data_arr[$row[csf('id')]]['wo_date']=$row[csf('wo_date')];
		$data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		$data_arr[$row[csf('id')]]['source']=$row[csf('source')];
		$data_arr[$row[csf('id')]]['working_company_id']=$row[csf('working_company_id')];
		$data_arr[$row[csf('id')]]['rate_for']=$row[csf('rate_for')];
		$data_arr[$row[csf('id')]]['order_source']=$row[csf('order_source')];
		$data_arr[$row[csf('id')]]['ord_recev_company']=$row[csf('ord_recev_company')];
		$data_arr[$row[csf('id')]]['job_id']=$row[csf('job_id')];
		$data_arr[$row[csf('id')]]['po_id']=$row[csf('po_id')];
		$data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$data_arr[$row[csf('id')]]['item_id']=$row[csf('item_id')];
		$data_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref')];
		$data_arr[$row[csf('id')]]['color_type']=$row[csf('color_type')];
		$data_arr[$row[csf('id')]]['client_id']=$row[csf('client_id')];
		$data_arr[$row[csf('id')]]['wo_qty']=$row[csf('wo_qty')];
		$data_arr[$row[csf('id')]]['po_qty']=$row[csf('po_qty')];
		$data_arr[$row[csf('id')]]['uom']=$row[csf('uom')];
		$data_arr[$row[csf('id')]]['avg_rate']=$row[csf('avg_rate')];
		$data_arr[$row[csf('id')]]['amount']=$row[csf('amount')];
		$data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
		$data_arr[$row[csf('id')]]['pay_mode']=$row[csf('pay_mode')];
		
	}

	
	$wo_dtls_cond='';
	 $wo_dtls_ids=array_unique($wo_dtls_ids);
	
	 $wo_dtls_cond= where_con_using_array($wo_dtls_ids,0,"b.wo_dtls_id");
	 

	 $all_po_cond='';
	 $all_po_ids=array_unique($all_po_ids);
	 if(count($all_po_ids))
	 {
	 	$all_po_cond= where_con_using_array($all_po_ids,0,"c.id");
	 }

	  $job_po_sql = "select a.job_no,c.id as po_id,c.po_number from wo_po_details_master a,wo_po_break_down c where  a.job_no=c.job_no_mst and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $all_po_cond  "; 
	  $job_po_result=sql_select($job_po_sql);
	  $po_wise_data=array();
	  foreach ($job_po_result as $row) {
	  	$po_wise_data[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
	  	$po_wise_data[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
	  }

  


   
	
	$sql="SELECT a.sys_number as bill_no,a.id , a.bill_date,a.manual_bill,a.company_id,a.working_company_id,a.source,a.currency,a.exchange_rate,a.location,a.remarks,b.bill_qty,b.avg_rate,b.amount,a.upcharge,a.grand_total,a.discount ,b.wo_dtls_id,b.remarks as dtls_remark
	from piece_rate_bill_mst a,piece_rate_bill_dtls b 
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $wo_company_con  $date_search_cond  $company_con $search_field_cond $wo_dtls_cond $source_cond  order by $order_by";

	//echo "<pre>".$sql."</pre>";

	
	$bill_wise=array();
	$pre_b='';
	$result=sql_select($sql);
	$bill_ids=array();
	foreach ($result as $row) {
		array_push($bill_ids, $row[csf('id')]);
	}
	$bill_id_cond='';
	if(count($bill_ids))
	{
		$bill_id_cond=where_con_using_array($bill_ids,0,"a.id");
	}

	$sql_bill_qty=sql_select("SELECT a.id , sum(b.bill_qty) as bill_qty
	from piece_rate_bill_mst a,piece_rate_bill_dtls b 
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $bill_id_cond group by a.id");
	$bill_wise_qnt=array();
	foreach ($sql_bill_qty as $row) {
		$bill_wise_qnt[$row[csf('id')]]=$row[csf('bill_qty')];
	}
	
	//echo "<pre>".$sql."</pre>";
	


	
	$client_arr=return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  group by a.id , a.buyer_name order by buyer_name ",'id','buyer_name');
	
	
	$table_width="2350"; $colspan="21";
	ob_start();
	?>
    <fieldset style="width:100%">	
        
         <h3 style="justify-content: center;text-align: center;" class="form_caption"><strong><?=$report_title; ?></strong></h3>
         <br>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" >
            <thead>
                <tr>
                	<th width="40">SL</th>
                	<th width="160">Company</th>
                    <th width="80">Location</th>
                    <th width="115">Source</th>
                    <th width="160">Working Company</th>
                    <th width="110">WO No.</th>
                    <th width="60">WO Date</th>
                    <th width="70">Rate For</th>
                    <th width="70">Currency</th>
                    <th width="70" >Pay Mode</th>
                    <th width="120">Buyer</th>
                    <th width="120">Style</th>
                    <th width="100">Job No.</th>
                    <th width="115">Buyer. Client. </th>
                    <th width="115">Item</th>
                    <th width="120">PO</th>
                    <th width="70">PO Qty</th>
                    <th width="80">Color Type</th>
                    <th width="70">WO Qty</th>
                    <th width="70">Rate</th>
                    <th width="70">Amount</th>
                    <th width="110">Bill No. </th>
                    <th width="70">Bill Date</th>
                    <th width="70">Bill Qty</th>
                    <th >Net Bill Value</th>
                </tr>

            </thead>
       
        
           <tbody id="table_body">
            
			<?php 
				$i=1;
				$j=1;
				$previous_bill_no='';
				$wo_qty=0;
				$bill_qty=0;
				$amount=0;
				$upcharge=0;
				$discount=0;
				$tot_bill_amt=0;
				foreach ($result as $row) 
				{

				
						$current_bill_no=$row[csf('bill_no')];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						
						$buyer=$buyer_arr[$data_arr[$row[csf('wo_dtls_id')]]['buyer_id']];
						

						$wo_qty+=$row[csf('wo_qty')];
						$bill_qty+=$row[csf('bill_qty')];
						$amount+=$row[csf('amount')];

						$wo_company='';
						if($row[csf('source')]==1)
						{
							$wo_company=$company_arr[$row[csf('working_company_id')]];
						}else{
								$wo_company=$supllier_arr[$row[csf('working_company_id')]];
						}

						$po_id=$data_arr[$row[csf('wo_dtls_id')]]['po_id'];
						
						
						$net_qnt=($row[csf('grand_total')]/$bill_wise_qnt[$row[csf('id')]])*$row[csf('bill_qty')];


						?>
				
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								
								
								
								<td  ><?=$i; ?></td>
								<td  ><p style="word-break:break-all;"><?=$company_arr[$row[csf('company_id')]]; ?></p></td>
								<td  ><p style="word-break:break-all;"><?=$location_arr[$row[csf('location')]]; ?></p></td>
								
								<td ><p style="word-break:break-all;"><?=$knitting_source[$row[csf('source')]]; ?></p></td>
								<td ><p style="word-break:break-all;"><?=$wo_company; ?></p></td>
								<td ><p style="word-break:break-all;"><?=$data_arr[$row[csf('wo_dtls_id')]]['wo_no'] ?></p></td>
								<td ><p style="word-break:break-all;"><?=change_date_format($data_arr[$row[csf('wo_dtls_id')]]['wo_date']); ?></p></td>
								<td><p style="word-break:break-all;"><?=$rate_for[$data_arr[$row[csf('wo_dtls_id')]]['rate_for']]; ?></p></td>
								<td><p style="word-break:break-all;"><?=$currency[$row[csf('currency')]]; ?></p></td>
								<td><p style="word-break:break-all;"><?=$pay_mode[$data_arr[$row[csf('wo_dtls_id')]]['pay_mode']]; ?></p></td>
								
								<td><p style="word-break:break-all;"><?=$buyer; ?></p></td>
								<td><p style="word-break:break-all;"><?=$data_arr[$row[csf('wo_dtls_id')]]['style_ref']; ?></p></td>
								
								<td><p style="word-break:break-all;"><?=$po_wise_data[$po_id]['job_no']; ?></p></td>
								<td><p style="word-break:break-all;"><?=$client_arr[$data_arr[$row[csf('wo_dtls_id')]]['client_id']]; ?></p></td>
								<td><p style="word-break:break-all;"><?=$garments_item[$data_arr[$row[csf('wo_dtls_id')]]['item_id']]; ?></p></td>
								<td><p style="word-break:break-all;"><?=$po_wise_data[$po_id]['po_number']; ?></p></td>
								
								
								<td align="right"><p><?=number_format($data_arr[$row[csf('wo_dtls_id')]]['po_qty'],2); ?></p></td>
								<td ><p style="word-break:break-all;"><?=$color_type[$data_arr[$row[csf('wo_dtls_id')]]['color_type']]; ?></p></td>
								<td align="right"><p><?=number_format($data_arr[$row[csf('wo_dtls_id')]]['wo_qty'],2); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('avg_rate')],2); ?></p></td>
								<td align="right"><?=number_format($row[csf('amount')],2); ?></td>
								<td align=""><p style="word-break:break-all;"><?=$row[csf('bill_no')]; ?></p></td>
								<td align="right"><p ><?=change_date_format($row[csf('bill_date')]); ?></p></td>
								<td align="right"><p><?=number_format($row[csf('bill_qty')],2); ?></p></td>
								
								<td style="vertical-align: top" align="right" ><p><?=number_format($net_qnt,2); ?></p></td>

							

								
							</tr>

				
						
						<?	
							$i++;
							$previous_bill_no=$current_bill_no;

				}
			 ?>
			
			</tbody>
			 <tfoot >
			
			 	
			 	
			 </tfoot>
           </table>
        
        
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

if ($action == "work_order_print") 
{
	echo load_html_head_contents("Knitting W/O ", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$address="";
?>
	
		
        <table style="margin-top:10px;" width="1200" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Program Date</th>
                <th width="90">Program no </th>
                <th width="250">Fabric Description</th>
                <th width="100">M/C Dia x Gauge</th>
                <th width="100">S.L</th>
                <th width="150">Color Range</th>
                <th width="100">Program Qty.</th>
                <th width="80">WO Qty.</th>
                <th width="70">Rate</th>
                
                <th>Amount</th>
            </thead>
            <tbody>
            	<?php 

            		$sql="SELECT a.id,a.program_date,a.program_no,a.fabric_desc,a.machine_dia,a.machine_gg,a.stitch_length,a.color_range,a.program_qnty,a.wo_qty,a.rate,a.amount from knitting_work_order_dtls a where a.status_active=1 and a.is_deleted=0 and a.mst_id=$data[1] and a.id in (select b.wo_dtls_id from wo_bill_dtls b where b.status_active=1 and b.is_deleted=0)";
            		//echo $sql;
            		$result=sql_select($sql);
            		$i=1;
            		$program_qnty=0;
            		$wo_qty=0;
            		$amount=0;
            		foreach ($result as $row) 
            		{
            			$program_qnty+=$row[csf('program_qnty')];
            			$wo_qty+=$row[csf('wo_qty')];
            			$amount+=$row[csf('amount')];
            			
            			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		 ?>

            		 	<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td><?=$i; ?></td>
								<td>
									<p><?=change_date_format($row[csf('program_date')]); ?></p>
								</td>
								<td><p><?php echo $row[csf('program_no')] ?></p></td>
								<td><p><?php echo $row[csf('fabric_desc')] ?></p></td>
								<td><p><?php echo $row[csf('machine_dia')]." x ".$row[csf('machine_gg')] ?></p></td>
								<td><p><?php echo $row[csf('stitch_length')] ?></p></td>
								<td><p><?php echo $color_range[$row[csf('color_range')]] ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('program_qnty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('wo_qty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('rate')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('amount')],2) ?></p></td>

            		 	</tr>
            			<?php 
            			$i++;
            		} 

            	?>

             </tbody>
             <tfoot>
				<tr>
					<td colspan="7" align="right">Total</td>
					<td align="right"><p><?php echo number_format($program_qnty,2) ?></p></td>
					<td align="right"><p><?php echo number_format($wo_qty,2) ?></p></td>
					<td></td>
					<td align="right"><p><?php echo number_format($amount,2) ?></p></td>
				</tr>
            </tfoot>
        </table>
		
    </div>
    <?
    exit();
}


?>