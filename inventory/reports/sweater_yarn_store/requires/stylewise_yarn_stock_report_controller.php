<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Job No Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $data=explode('_',$data);
    $text_style_no=str_replace("'","",$data[3]);
	//print_r ($data);
	?>	
    <script>

 		var selected_id = new Array;
		var selected_name = new Array;
		var selected_style_name = new Array();

	    function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'rpt_tablelist_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var selectStyle = splitSTR[3];
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
					
			if( jQuery.inArray( selectID, selected_id, selectStyle ) == -1 )
			{
			    selected_id.push( selectID );
			    selected_name.push( selectDESC );					
			    selected_style_name.push( selectStyle );					
			}
			else
		    {
				for( var i = 0; i < selected_id.length; i++ )
				{
				    if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_style_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var style = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
			    id += selected_id[i] + ',';
			    name += selected_name[i] + ','; 
			    style += selected_style_name[i] + ','; 
			}
			id 	 = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 ); 
			style = style.substr( 0, style.length - 1 ); 
			$('#txt_job_id').val( id );
		    $('#txt_job_no').val( name );
		    $('#txt_style_ref').val( style );
		}

	</script>
     <input type="hidden" id="txt_job_id" />
	 <? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );?>
    <?
	
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	
	//echo $sql;//die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("rpt_tablelist_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("rpt_tablelist_view",-1);','0,0,0,0,0','',1) ;
	echo "<input type='hidden' id='txt_job_id' />";
	echo "<input type='hidden' id='txt_job_no' />";
	echo "<input type='hidden' id='txt_style_ref' />";
	exit();
}

if ($action=="style_no_popup")
{
	echo load_html_head_contents("Style No Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $data=explode('_',$data);
    $text_style_no=str_replace("'","",$data[3]);
	//print_r ($data);
	?>	
    <script>
	function js_set_value( style_id )
	{
		//alert(po_id)
		document.getElementById('txt_style_id').value=style_id;
		parent.emailwindow.hide();
	}
    setFilterGrid('rpt_tablelist_view',-1); 
	</script>
     <input type="hidden" id="txt_style_id" />
    <?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	$styleRefArr = return_library_array("select id,style_ref_name from lib_style_ref ","id","style_ref_name");
	
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	
	//echo $sql;//die;
	
	$arr=array(0=>$styleRefArr,2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("rpt_tablelist_view", "Style Ref.,Job No,Prod. Dept.,Marchant,Team Name", "110,100,110,150,150","680","360",0, $sql , "js_set_value", "id,style_ref_no,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "style_ref_no,job_no_prefix_num,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("rpt_tablelist_view",-1);','0,0,0,0,0','') ;
	exit();
}

if ($action == "load_drop_down_store") {
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;

	}
	else
	{
		$disable = 0;
	}

	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

if($action=="photo_view")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$img_sql="select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry' and master_tble_id='$photo_location'";
	$img_result = sql_select($img_sql);
	foreach ($img_result as $path) 
	{
		$img_arr.="<img src='../../../../".$path[csf('image_location')]."' width='200' style='padding: 5px;' alt='No Image Found'/>";
	}
	?>
    <div style="width:200px; margin:1px auto; display: inline;"> 
		<? echo $img_arr;?>
    </div>
<?
}


if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$rpt_type=str_replace("'","",$rpt_type);
	//echo $cbo_store_name;//die;
	if($rpt_type==1)
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$company_short_name_array = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
		$companyArr[0] = "All Company";
		$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
		//$composition
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
		
	
		if ($db_type == 0) {
			//$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
			$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			//$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
			$from_date = change_date_format($from_date, '', '', 1);
			$to_date = change_date_format($to_date, '', '', 1);
			//$cbo_year = "extract()";cbo_year_selection
		} else {
			$from_date = "";
			$to_date = "";
			//$exchange_rate = 1;
		}
		$date_cond = $date_cond_order = "";
		if($from_date!="" && $to_date!="")
		{
			if($cbo_date_type==1) $date_cond = " and a.transaction_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==2) $date_cond_order = " and po_received_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==3) $date_cond_order = " and pub_shipment_date between '$from_date' and '$to_date' ";
		}

		//echo $txt_job_no;die;
		$job_no_po_cond=$job_no_datas="";$job_no_cond="";
		if($txt_job_no!="") 
		{
			$txt_job_no_array=explode(",",$txt_job_no);
			$job_no_datas="";
			foreach($txt_job_no_array as $job_no)
			{
				$job_no_datas.="'".$job_no."',";
			}
			$job_no_datas=chop($job_no_datas,",");
			//echo $job_no_datas;die;
			if($cbo_ship_status>0)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name and wo_po_details_master.job_no in($job_no_datas) $date_cond
				group by job_no_mst 
				having count(*)=count(case shiping_status when 3 then 1 else null end)";
				//echo $sql."<br>";//die;
				$ship_result=sql_select($sql);				
				$job_no_in_full_shipment=array();
				$sl=0;
				$job_no_cond=" and a.job_no in (";
				foreach ($ship_result as $row) {
					array_push($job_no_in_full_shipment, $row[csf('job_no_mst')]);
					if($sl>0) $job_no_cond.=",";
					$job_no_cond.="'".$row[csf('job_no_mst')]."'";
					$sl++;
					//echo "<pre>".$row[csf('job_no_mst')]."</pre>";
				}
				if($cbo_ship_status==1){
					$sl=0;
					$job_no_cond=" and a.job_no in (";
					foreach ($txt_job_no_array as $job_no) {
						
						if(!in_array($job_no, $job_no_in_full_shipment)){
							$job_no_cond.="'".$job_no."',";
						}
					}
					$job_no_cond=chop($job_no_cond,",");
					$job_no_cond.=")";
					//echo $job_no_cond;die;
				}
				else
				{
					$job_no_cond.=")";
				}
			}
			else
			{
				$job_no_cond=" and a.job_no in($job_no_datas)";
			}
		} 
		else
		{
			if($cbo_ship_status==2)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			else if($cbo_ship_status==1) 
			{
				$sql="select count(*) as total_count, count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			//echo $sql."<br>";//die;
			$ship_result=sql_select($sql);
			//print_r($ship_result);die;
			foreach ($ship_result as $row) {
				$tot_rows++;
				$jobNos .= "'".$row[csf('job_no_mst')]."',";
			}
			//echo $jobNos;die;
			$job_no_cond = '';
			if ($jobNos != '')
			{
				$jobNos = array_flip(array_flip(explode(',', rtrim($jobNos,','))));
				//print_r($jobNos);die;
				if($db_type==2 && $tot_rows>1000)
				{
					$job_no_cond = ' and (';
					$jobNoArr = array_chunk($jobNos,999);
					foreach($jobNoArr as $jobs)
					{
						$jobs = implode(',',$jobs);
						$job_no_cond .= " a.job_no in($jobs) or ";
					}
					$job_no_cond = rtrim($job_no_cond,'or ');
					$job_no_cond .= ')';
				}
				else
				{
					$jobNos = implode(',', $jobNos);
					$job_no_cond=" and a.job_no in ($jobNos)";
				}
			}
		}
		
	
		if($cbo_store_name!=0){
			$store_name_cond=" and a.store_id in($cbo_store_name)"; 
		}else {
			$store_name_cond="";
		}
	
		if($cbo_buyer!=0){
			$buyer_cond=" and a.buyer_id in($cbo_buyer)"; 
		}else {
			$buyer_cond="";
		}

		if ($txt_composition_id != "")
		{
			$yarn_comp_cond = " and b.yarn_comp_type1st in ($txt_composition_id) ";
			$sample_yarn_comp_cond = " and a.yarn_comp_type1st in ($txt_composition_id) ";
		}
		
		$current_date=date("d-m-Y");
		$p=1;
		$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
		$company_wise_data=array();
		foreach($queryText as $row)
		{
			$company_wise_data[$row["COMPANY_ID"]]++;
		}
		
		$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
		foreach($queryText as $val)
		{
			if($company_check_arr[$val["COMPANY_ID"]]=="")
			{
				$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
				$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				$sEndDate = $sStartDate;
				$previous_date=$sStartDate;
				$previous_rate=$val["CONVERSION_RATE"];
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				
				$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {
					
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$q=1;
			}
			else
			{
				$q++;
				$sStartDate = date("Y-m-d", strtotime($previous_date));
				if($company_wise_data[$val["COMPANY_ID"]]==$q)
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					
					$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
					$sEndDate = date("Y-m-d", strtotime($current_date));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
					while ($sCurrentDate <= $sEndDate) {
						
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
				else
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
			}
			$p++;
		}
		unset($queryText);
		//echo "<pre>";print_r($conversion_data_arr);die;
	
		$sql = "select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose,
		a.cons_quantity,
		0 as re_conning_rcvd_issue_qnty,
		(case when c.receive_purpose in(15) then a.cons_quantity else 0  end) as twisting_received, 
		0 as loan_rcvd_issue_qnty,
		0 as sample_issue,
		0 as twisting_issue,
		0 as mending,
		0 as linking
		from inv_transaction a, product_details_master b, inv_receive_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1,4) and a.entry_form in(248,382) $date_cond 
		union all
		select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.issue_purpose,
		a.cons_quantity,
		0 as re_conning_rcvd_issue_qnty,
		0 as twisting_received,
		0 as loan_rcvd_issue_qnty,
		(case when a.transaction_type in(2) $date_cond and c.issue_purpose in(4,8) then a.cons_quantity else 0 end) as sample_issue,
		(case when a.transaction_type in(2,3) and c.issue_purpose in(15) then a.cons_quantity else 0 end) as twisting_issue,
		0 as mending,
		(case when a.transaction_type in(2) and c.issue_purpose in(47) then a.cons_quantity else 0 end) as linking
		from inv_transaction a, product_details_master b, inv_issue_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277,381) $date_cond
		union all
		select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose,
		a.cons_quantity,
		0 as re_conning_rcvd_issue_qnty,
		0 as twisting_received,
		0 as loan_rcvd_issue_qnty,
		0 as sample_issue,
		0 as twisting_issue,
		0 as mending,
		0 as linking
		from inv_transaction a, product_details_master b, inv_item_transfer_mst c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5,6) and a.entry_form in(249) $date_cond
		order by id";
		//echo $sql;die;
		$result = sql_select($sql);
		//echo "<pre>";print_r($result);die;
		foreach ($result  as $row) 
		{
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['company']=$row[csf('company_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_purpose']=$row[csf('issue_purpose')];
			if ($row[csf('trans_type')]==1) {
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['purchase']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_received']+=$row[csf('twisting_received')];
				$rate = ($row[csf('cons_amount')]*1)/($row[csf('cons_quantity')]*1)/($row[csf('exchange_rate')]*1);
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][1].=$row[csf('rcv_iss_trans_id')].',';
					
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('order_amount')];
				
			} 		
			else if ($row[csf('trans_type')]==4) {
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_return']+=$row[csf('cons_quantity')];		
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_issue_return']+=$row[csf('cons_quantity')];		
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][4].=$row[csf('rcv_iss_trans_id')].',';	
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('cons_amount')]/$conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];	
			}
			else if ($row[csf('trans_type')]==5) {
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['transfer_in_qnty']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][5].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('cons_amount')]/$conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			}
			else if ($row[csf('trans_type')]==2) 
			{
				if($row[csf('basis')]==6) 
				{
					$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot_issue']+=$row[csf('cons_quantity')];
				}
				if($row[csf('issue_purpose')]==1){
					$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['knitting']+=$row[csf('cons_quantity')];
				}
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_issue']+=$row[csf('twisting_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][2].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			}
			else if ($row[csf('trans_type')]==3) {
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['rcv_return']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_rcv_return']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][3].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			}
			else if ($row[csf('trans_type')]==6) {
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['transfer_out_qnty']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][6].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			}
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['loan_rcvd']+=$row[csf('loan_rcvd_issue_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['re_conning_rcvd']+=$row[csf('re_conning_rcvd_issue_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['re_conning_issue']+=$row[csf('re_conning_rcvd_issue_qnty')];
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['mending']+=$row[csf('mending')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['linking']+=$row[csf('linking')];
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['loan_issue']+=$row[csf('loan_issue')];
			
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['cons_rate']=$row[csf('cons_rate')];
			//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_rate']=$rate;
			//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_amount']= ($row[csf('current_stock')]*1)*($rate*1);
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['cons_amount']=$row[csf('cons_amount')];
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['allocated_qnty']=$row[csf('allocated_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['available_qnty']=$row[csf('available_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['item_category']=$row[csf('item_category')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['entry_form']=$row[csf('entry_form')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
			
			$all_prod_ids.=$row[csf('prod_id')].',';
			//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
			//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['exchange_rate']=$row[csf('exchange_rate')];
		}
		
		//echo "<pre>";print_r($yarn_data_array);die;
		
		$lot_ratio_sql=sql_select("select a.job_no, b.prod_id, b.alocated_qty as alocated_qty 
		from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b
		where a.id=b.mst_id and a.entry_form=253 and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company_name $store_name_cond");
		$allocate_data=array();
		foreach($lot_ratio_sql as $row)
		{
			$allocate_data[$row[csf("job_no")]][$row[csf("prod_id")]]+=$row[csf("alocated_qty")];
		}
		
		//echo "<pre>";print_r($yarn_data_array);die;
		
		foreach ($yarn_data_array as $job_no => $prod_data) {
			$row_span = 0;
			foreach ($prod_data as $prod_id => $value) {
				$row_span++;
			}
			$job_wise_span[$job_no]=$row_span;
		}
		//unset($row);
		//var_dump($yarn_data_array);
		$all_prod_ids=rtrim($all_prod_ids,',');
		ob_start();

		?>
		<div style="width:3420px;">
		<table width="3420" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
					<th width="150" align="center" rowspan="2">Company</th>
					<th width="100" align="center" rowspan="2">Buyer</th>
					<th width="110" align="center" rowspan="2">Style No</th>
					<th width="100" align="center" rowspan="2">Job No</th>
					<th width="100" align="center" rowspan="2">Image</th>
					<th width="100" align="center" rowspan="2">Yarn Supplier</th>
					<th width="80" align="center" rowspan="2">Yarn Count</th>
					<th width="130" align="center" rowspan="2">Yarn Composition</th>
					<th width="110" align="center" rowspan="2">Yarn Color</th>
					<th width="80" align="center" rowspan="2">Lot</th> 
					<th width="560" align="center"  colspan="7">Receive Details (Lbs)</th> 
					<th width="800" align="center" colspan="10">Issue Details (Lbs)</th>
					<th width="100" align="center" rowspan="2">Current Stock (Lbs)</th>
					<th width="80" align="center" rowspan="2">Avg Rate/Lbs</th>
					<th width="110" align="center" rowspan="2">Total Amount (USD)</th>
					<th width="110" align="center" rowspan="2">Total Amount (BDT)</th>
					<th width="100" align="center" rowspan="2">Allocated</th>
					<th width="100" align="center" style="word-break: break-all;"  rowspan="2">Allocated Yarn Balance</th>
					<th width="100" align="center" rowspan="2">Available</th>
					<th  align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="80" >Purchase </th>
					<th width="80" >Issue Return</th>
					<th width="80" >Trans. In</th>
					<th width="80" >Loan Rcvd</th>
					<th width="80" >Twisting Rcvd</th>
					<th width="80" >Re-conning Rcvd</th>
					<th width="80" >Total Rcvd</th>
	
					<th width="80">Re-conning Issue</th>
					<th width="80">Twisting Issue</th>
					<th width="80">Knitting</th>
					<th width="80">Sample</th>
					<th width="80">Mending</th>
					<th width="80">Linking</th>
					<th width="80">Receive Return</th>
					<th width="80">Loan Issue</th>
					<th width="80">Trans. Out</th>
					<th width="80">Total Issue</th>
				</tr>
			</thead>
		</table>
	    <div style="width:3420px; overflow-y: scroll; max-height: 380px;" align="left">
			<table width="3420" class="rpt_table" rules="all" border="1" id="scroll_body"  style="word-break:break-all;">
				<tbody>
					<? 
					$i=1;
					$j=1;
					
					$tot_purchage="";
					// echo "<pre>";
					// var_dump($yarn_data_array);
					foreach ($yarn_data_array as $job_key=>$prod_data ) 
					{
						$kk=0;
						foreach ($prod_data as $row) 
						{
							if ($row['remarks']!="") {
								$remarks_cond = "#FF0000";
							}else{
								$remarks_cond = "none";
							}
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FEFEFE";
							$total_received_qnty +=(($row['purchase']*1)+($row['issue_return']*1)+($row['transfer_in_qnty']*1)+($row['loan_rcvd']*1)+($row['re_conning_rcvd']*1));

							//$cons_avg_rate=$row['avg_rate_per_unit']*1;
							//$total_received_amt =$row['purchase_amt'];
							$total_issue_qnty +=(($row['knitting']*1)+($row['re_conning_issue']*1)+($row['sample_issue']*1)+($row['mending']*1)+($row['linking']*1)+($row['rcv_return']*1)+($row['loan_issue']*1)+($row['transfer_out_qnty']*1)+($row['twisting_issue']*1));
							//$total_issue_amt +=$row['knitting_amt'];  +($row['lot_issue']*1)
							
							//$current_stock_qnty =($total_received_qnty-$total_issue_qnty);
							$current_stock_qnty =$row['current_stock'];
							//$total_amount_usd = ($row['current_stock']*1)*($rate*1);
							$total_amount_usd = $row['current_stock_value_usd'];
							$total_amount_bdt = $row['current_stock_value_bdt'];
							$rate=0;
							if($row['current_stock_value_usd']!=0 && $row['current_stock']!=0) $rate=$row['current_stock_value_usd']/$row['current_stock'];
							//$allocate_qnty=$allocate_data[$row['job_no']][$row['prod_id']]-$row['lot_issue'];
							$allocate_qnty=$allocate_data[$row['job_no']][$row['prod_id']];
							//$available_qnty = (($current_stock_qnty)-($row['allocated_qnty']*1));
							
							//for allocated yarn balance
							$allocatedYarnBalance = $allocate_qnty - $row['lot_issue'];

							if($current_stock_qnty>0 && $current_stock_qnty>=$allocatedYarnBalance)
							{
								$available_qnty = $current_stock_qnty-$allocate_qnty;
								$cu_available_qnty = $current_stock_qnty-$allocatedYarnBalance;
							}else{
								$available_qnty = 0;
								$cu_available_qnty=0;
							}
						
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<?
								if($kk==0)
								{
									$rowspan=$job_wise_span[$job_key];
									?>
									<td  width="30"rowspan="<? echo $rowspan;?>" valign="middle"><? echo $j;?></td>
									<td  width="150"  style="word-break: break-all;" rowspan="<? echo $rowspan;?>" valign="middle"><? echo $companyArr[$row['company']];?></td>
									<td  width="100"  style="word-break: break-all;" rowspan="<? echo $rowspan;?>" valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
									<td width="110"  style="word-break: break-all;" rowspan="<? echo $rowspan;?>" valign="middle"><? echo $row['style_ref_no'];?></td>
									<td width="100"  style="word-break: break-all;" rowspan="<? echo $rowspan;?>" valign="middle"><? echo $row['job_no'];?></td>
									<td width="100"  style="word-break: break-all;" rowspan="<? echo $rowspan;?>" valign="middle" align='center' onclick="openmypage_image('requires/stylewise_yarn_stock_report_controller.php?action=show_image&job_no=<? echo $row['job_no'];?>','Image View')"><img src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25'  /></td>
									<?
									$kk++;
								}
								/*,'<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>'*/
								?>
								<td width="100"  style="word-break: break-all;"><? echo $supplierArr[$row['supplier_id']];?></td>
								<td width="80" style="word-break: break-all;"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
								<td width="130"  style="word-break: break-all;"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
								<td width="110"  style="word-break: break-all;"><? echo $color_name_arr[$row['color']];?></td>
								<td width="80" title="<? echo "Prod_ID==".$row['prod_id']; ?>"  style="word-break: break-all;"><? echo $row['lot'];?> </td>
								<td width="80"  style="word-break: break-all;" align="right"><? echo $row['purchase'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['issue_return'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['transfer_in_qnty'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['loan_rcvd'];?> </td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['twisting_received'];?> </td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['re_conning_rcvd'];?></td>
								<td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>" align="right" > <a href='#report_details' onClick="openmypage('<? echo $row[1]; ?>','<? echo $row['prod_id'];?>','received_popup','','');"><? echo number_format($total_received_qnty,2);?></a> </td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['re_conning_issue'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['twisting_issue'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['knitting'],4);?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['sample_issue'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['mending'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['linking'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['rcv_return'];?></td>
								<td width="80" style="word-break: break-all;" align="right"><? echo $row['loan_issue'];?></td>

								<td width="80" style="word-break: break-all;" align="right"><? echo $row['transfer_out_qnty'];?></td>
								<td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','issue_popup','','');"><? echo number_format($total_issue_qnty,2);?></a> </td>
								<td width="100" style="word-break: break-all;" align="right"><?  echo number_format($current_stock_qnty,4);?></td>
								<td width="80" style="word-break: break-all;" align="right" title="<? echo "Stock Value Usd=".$row['current_stock_value_usd'].", Current Stock=".$row['current_stock'] ?>"><? if(number_format($current_stock_qnty,4,'.','')!=0) echo number_format($rate,2); else echo "0.00";?></td>
								<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_usd, 2); else echo "0.00"; ?></td>
								<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_bdt,2); else echo "0.00"; ?></td>
								<td width="100" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right" title="<?= "tot allocate=".$allocate_data[$row['job_no']][$row['prod_id']]." allocate issue=".$row['lot_issue']; ?>"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','alocate_popup');"><? echo number_format($allocate_qnty,2);?></a> </td>
								<td width="100" style="word-break: break-all;" align="right"> <? echo number_format($allocatedYarnBalance,4);?></td>
								<td width="100"style="word-break: break-all;" align="right" title="<?= "current stock-allocate qnty balance (Note: allocate qnty balance=allocate qnty-Lot ratio wise issue)"; ?>"> <? echo number_format($cu_available_qnty,4);?>
								</td>
								<td><? echo $store_name_arr[$row['store_id']];?></td>
							</tr>
							<?
							$total_received_qnty=$total_received_amt=$total_issue_qnty=$total_issue_amt=$total_amount=0;	
							$i++;
							$tot_purchage+=$row['purchase'];
							$total_issue_return+=$row['issue_return'];
							$total_trans_in+=$row['transfer_in_qnty'];
							$total_loan_rcvd+=$row['loan_rcvd'];
							$total_twisting_rcvd+=$row['twisting_received'];
							$total_reconning_recvd+=$row['re_conning_rcvd'];
							$total_recvd=($row['purchase']*1) + ($row['issue_return']*1) + ($row['transfer_in_qnty']*1) + ($row['loan_rcvd']*1) + ($row['re_conning_rcvd']*1);
							$grand_total_received +=$total_recvd;
							$total_reconning_issue+=$row['re_conning_issue'];
							$total_twisting_issue+=$row['twisting_issue'];
							$total_knitting+=$row['knitting'];
							$total_sample+=$row['sample_issue'];
							$total_mending+=$row['mending'];
							$total_linking+=$row['linking'];
							$total_recv_return+=$row['rcv_return'];
							$total_loan_issue+=$row['loan_issue'];
							$total_trans_out+=$row['transfer_out_qnty'];
							$total_issue= ($row['knitting']*1) + ($row['sample_issue']*1) +($row['mending']*1) +($row['linking']*1) +($row['rcv_return']*1) +($row['transfer_out_qnty']*1) + ($row['re_conning_issue']*1) + ($row['loan_issue']*1);
							$grand_total_issue += $total_issue;
							$total_current_stock+=$current_stock_qnty;
							$total_usd_amount=$total_current_stock*$rate;
							$total_bdt_amount+=$total_amount_bdt;
							$total_allocated+=$allocate_qnty;
							$total_available+=$cu_available_qnty;
							$wo_number = $row['job_no'];
							$total_allocatedYarnBalance+=$allocatedYarnBalance;
						}
						$j++;
					}
					?>
				</tbody>
				<tfoot>
					<tr bgcolor="#ecfa8b">
						<td align="right" colspan="11" style="text-align:right;"><strong>Grand Total:</strong> </td>
						<td align="right" id="total_puchase"><strong> <? echo  number_format($tot_purchage,2);?></strong></td>
						<td align="right" id="total_issue_return)"><strong><? echo  number_format($total_issue_return,2);?></strong></td>
						<td align="right" id="total_trans_in"><strong><? echo  number_format($total_trans_in,2);?></strong></td>
						<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_loan_rcvd,2);?></strong></td>
						<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_twisting_rcvd,2);?></strong></td>
						<td align="right" id="total_reconning_recvd"><strong><? echo  number_format($total_reconning_recvd,2);?></strong></td>
						<td align="right" id="total_recvd"><strong><? echo  number_format($grand_total_received,2);?></strong></td>
						<td align="right" id="total_reconning_issue"><strong><? echo  number_format($total_reconning_issue,2);?></strong></td>
						<td align="right" id="total_knitting"><strong><? echo  number_format($total_twisting_issue,2);?></strong></td>
						<td align="right" id="total_knitting"><strong><? echo  number_format($total_knitting,2);?></strong></td>
						<td align="right" id="total_sample"><strong><? echo  number_format($total_sample,2);?></strong></td>
						<td align="right" id="total_mending"><strong><? echo  number_format($total_mending,2);?></strong></td>
						<td align="right" id="total_linking"><strong><? echo  number_format($total_linking,2);?></strong></td>
						<td align="right" id="total_recv_return"><strong><? echo  number_format($total_recv_return,2);?></strong></td>
						<td align="right" id="total_loan_issue"><strong><? echo  number_format($total_loan_issue,2);?></strong></td>
						<td align="right" id="total_trans_out"><strong><? echo  number_format($total_trans_out,2);?></strong></td>
						<td align="right" id="total_issue"><strong><? echo  number_format($grand_total_issue,2);?></strong></td>
						<td align="right" id="total_current_stock"><strong><? echo  number_format($total_current_stock,2);?></strong></td>
						<td align="right" id=""></td>
						<td align="right" id="total_usd_amount"><strong><? echo  number_format($total_usd_amount,2);?></strong></td>
						<td align="right" id="total_bdt_amount"><strong><? echo  number_format($total_bdt_amount,2);?></strong></td>
						<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocated,2);?></strong></td>
						<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocatedYarnBalance,2);?></strong></td>
						<td align="right" id="total_available"><strong><? echo  number_format($total_available,2);?></strong></td>
						<td ></td>
					</tr>
				</tfoot>
			</table>
		</div>
	 </div> 
		<?
	}
	else if($rpt_type==3)
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$company_short_name_array = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
		$companyArr[0] = "All Company";
		$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
		//$composition
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
		$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	
		if ($db_type == 0) {
			//$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
			$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			//$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
			$from_date = change_date_format($from_date, '', '', 1);
			$to_date = change_date_format($to_date, '', '', 1);
			//$cbo_year = "extract()";cbo_year_selection
		} else {
			$from_date = "";
			$to_date = "";
			//$exchange_rate = 1;
		}
		

		//echo $txt_job_no;die;
		$job_no_po_cond=$job_no_datas="";$job_no_cond="";
		if($txt_job_no!="") 
		{
			$txt_job_no_array=explode(",",$txt_job_no);
			$job_no_datas="";
			foreach($txt_job_no_array as $job_no)
			{
				$job_no_datas.="'".$job_no."',";
			}
			$job_no_datas=chop($job_no_datas,",");
			//echo $job_no_datas;die;
			if($cbo_ship_status>0)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name and wo_po_details_master.job_no in($job_no_datas) $date_cond
				group by job_no_mst 
				having count(*)=count(case shiping_status when 3 then 1 else null end)";
				//echo $sql."<br>";//die;
				$ship_result=sql_select($sql);				
				$job_no_in_full_shipment=array();
				$sl=0;
				$job_no_cond=" and a.job_no in (";
				foreach ($ship_result as $row) {
					array_push($job_no_in_full_shipment, $row[csf('job_no_mst')]);
					if($sl>0) $job_no_cond.=",";
					$job_no_cond.="'".$row[csf('job_no_mst')]."'";
					$sl++;
					//echo "<pre>".$row[csf('job_no_mst')]."</pre>";
				}
				if($cbo_ship_status==1){
					$sl=0;
					$job_no_cond=" and a.job_no in (";
					foreach ($txt_job_no_array as $job_no) {
						
						if(!in_array($job_no, $job_no_in_full_shipment)){
							$job_no_cond.="'".$job_no."',";
						}
					}
					$job_no_cond=chop($job_no_cond,",");
					$job_no_cond.=")";
					//echo $job_no_cond;die;
				}
				else
				{
					$job_no_cond.=")";
				}
			}
			else
			{
				$job_no_cond=" and a.job_no in($job_no_datas)";
			}
		} 
		else
		{
			if($cbo_ship_status==2)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			else if($cbo_ship_status==1) 
			{
				$sql="select count(*) as total_count, count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			//echo $sql."<br>";//die;
			$ship_result=sql_select($sql);
			//print_r($ship_result);die;
			foreach ($ship_result as $row) {
				$tot_rows++;
				$jobNos .= "'".$row[csf('job_no_mst')]."',";
			}
			//echo $jobNos;die;
			$job_no_cond = '';
			if ($jobNos != '')
			{
				$jobNos = array_flip(array_flip(explode(',', rtrim($jobNos,','))));
				//print_r($jobNos);die;
				if($db_type==2 && $tot_rows>1000)
				{
					$job_no_cond = ' and (';
					$jobNoArr = array_chunk($jobNos,999);
					foreach($jobNoArr as $jobs)
					{
						$jobs = implode(',',$jobs);
						$job_no_cond .= " a.job_no in($jobs) or ";
					}
					$job_no_cond = rtrim($job_no_cond,'or ');
					$job_no_cond .= ')';
				}
				else
				{
					$jobNos = implode(',', $jobNos);
					$job_no_cond=" and a.job_no in ($jobNos)";
				}
			}
		}
		
	
		if($cbo_store_name!=0){
			$store_name_cond=" and a.store_id in($cbo_store_name)"; 
		}else {
			$store_name_cond="";
		}
	
		if($cbo_buyer!=0){
			$buyer_cond=" and a.buyer_id in($cbo_buyer)"; 
		}else {
			$buyer_cond="";
		}

		if ($txt_composition_id != "")
		{
			$yarn_comp_cond = " and b.yarn_comp_type1st in ($txt_composition_id) ";
			$sample_yarn_comp_cond = " and a.yarn_comp_type1st in ($txt_composition_id) ";
		}
		
		$current_date=date("d-m-Y");
		$p=1;
		$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
		$company_wise_data=array();
		foreach($queryText as $row)
		{
			$company_wise_data[$row["COMPANY_ID"]]++;
		}
		//echo count($queryText);die;
		$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
		foreach($queryText as $val)
		{
			if($company_check_arr[$val["COMPANY_ID"]]=="")
			{
				$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
				$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				$sEndDate = $sStartDate;
				$previous_date=$sStartDate;
				$previous_rate=$val["CONVERSION_RATE"];
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				
				$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {
					
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$q=1;
			}
			else
			{
				$q++;
				$sStartDate = date("Y-m-d", strtotime($previous_date));
				if($company_wise_data[$val["COMPANY_ID"]]==$q)
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					
					$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
					$sEndDate = date("Y-m-d", strtotime($current_date));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
					while ($sCurrentDate <= $sEndDate) {
						
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
				else
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
			}
			$p++;
		}
		unset($queryText);
		//echo "<pre>";print_r($conversion_data_arr);die;
		
		$date_cond = $date_cond_order = "";
		if($from_date!="" && $to_date!="")
		{
			if($cbo_date_type==1) $date_cond = " and a.transaction_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==2) $date_cond_order = " and po_received_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==3) $date_cond_order = " and pub_shipment_date between '$from_date' and '$to_date' ";
		}
		//$job_no_cond=" and a.job_no in ('SSL-20-00300')";
		//$job_no_cond.=" and a.prod_id=17593";
		
		//echo $job_no_cond.tst;die;
		$sql = "SELECT a.id, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.yarn_type, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when c.receive_purpose not in(5,12,15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as purchase_qnty,
		(case when a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as rcv_issue_val,
		0 as others,
		(case when c.receive_purpose in(12) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as re_conning_rcvd_issue_qnty, 
		(case when c.receive_purpose in(5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as loan_rcvd_issue_qnty,
		0 as sample_issue,
		(case when c.receive_purpose in(15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as twisting_received_issue,
		0 as linking,
		0 as lot_issue
		from inv_transaction a, product_details_master b, inv_receive_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1,4) and a.entry_form in(248,382) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.transaction_date < '$to_date'
		union all
		select a.id, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.yarn_type, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when c.issue_purpose in(1) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as purchase_qnty,
		(case when a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as rcv_issue_val,
		(case when c.issue_purpose not in(1,5,4,8,12,15,47) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as others,
		(case when c.issue_purpose in(12) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as re_conning_rcvd_issue_qnty,
		(case when c.issue_purpose in(5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as loan_rcvd_issue_qnty,
		(case when c.issue_purpose in(4,8) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as sample_issue,
		(case when c.issue_purpose in(15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as twisting_received_issue,
		(case when c.issue_purpose in(47) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as linking,
		(case when a.receive_basis=6 and a.transaction_type=2 then a.cons_quantity else 0 end) as lot_issue
		from inv_transaction a, product_details_master b, inv_issue_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277,381) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.transaction_date < '$to_date'
		union all
		select a.id, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.yarn_type, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as purchase_qnty,
		(case when a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as rcv_issue_val,
		0 as others,
		0 as re_conning_rcvd_issue_qnty,
		0 as loan_rcvd_issue_qnty,
		0 as sample_issue,
		0 as twisting_received_issue,
		0 as linking,
		0 as lot_issue
		from inv_transaction a, product_details_master b, inv_item_transfer_mst c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5,6) and a.entry_form in(249) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond and a.transaction_date < '$to_date'
		order by id";
		//echo $sql;//die;
		$result = sql_select($sql);
		//echo "<pre>";print_r($result);die;and b.id=70994 and b.id=70994 and b.id=70994
		$test_data_arr=array();
		foreach ($result  as $row) 
		{
			$item_key=$row[csf('company_id')]."=".$row[csf('supplier_id')]."=".$row[csf('yarn_count_id')]."=".$row[csf('yarn_comp_type1st')]."=".$row[csf('yarn_type')]."=".$row[csf('color')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['company']=$row[csf('company_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['buyer_id']=$row[csf('buyer_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['style_ref_no']=$row[csf('style_ref_no')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['job_no']=$row[csf('job_no')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['supplier_id']=$row[csf('supplier_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['yarn_count_id']=$row[csf('yarn_count_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['color']=$row[csf('color')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['lot']=$row[csf('lot')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['remarks']=$row[csf('remarks')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['issue_purpose']=$row[csf('issue_purpose')];
			
			$yarn_data_array[$row[csf('job_no')]][$item_key]['cons_rate']=$row[csf('cons_rate')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['cons_amount']=$row[csf('cons_amount')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['allocated_qnty']=$row[csf('allocated_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['available_qnty']=$row[csf('available_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['store_id']=$row[csf('store_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['item_category']=$row[csf('item_category')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['entry_form']=$row[csf('entry_form')];
			
			if($yarn_data_array[$row[csf('job_no')]][$item_key][$row[csf('prod_id')]]=="")
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key][$row[csf('prod_id')]]=$row[csf('prod_id')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['prod_id'].=$row[csf('prod_id')].",";
			}
			
			//$test_data_arr[$row[csf('job_no')]][$item_key][$row[csf('trans_type')]].=$row[csf('trans_type')]."=".$row[csf('cons_amount')]."=".$row[csf('order_amount')]."___";
			$all_prod_ids_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			$yarn_data_array[$row[csf('job_no')]][$item_key]['lot_issue']+=$row[csf('lot_issue')];
			$exchange_fac= 1;
			if($conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])]>0) $exchange_fac= $conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			
			if ($row[csf('trans_type')]==1) 
			{
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_rcv_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$exchange_fac= 1;
				if($row[csf('cons_amount')]==$row[csf('order_amount')])
				{
					if($conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])]>0) $exchange_fac= $conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
				}
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv_amt']+=$row[csf('opening_rcv_issue_amt')];
				
				if($row[csf('opening_rcv_issue_amt_usd')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv_amt_usd']+=$row[csf('opening_rcv_issue_amt_usd')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['purchase']+=$row[csf('purchase_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['twisting_received']+=$row[csf('twisting_received')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['loan_received']+=$row[csf('loan_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['re_conning_received']+=$row[csf('re_conning_rcvd_issue_qnty')];
				
					
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				//echo $exchange_fac;
				if($row[csf('order_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']+=$row[csf('order_amount')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				
				$rate = ($row[csf('cons_amount')]*1)/($row[csf('cons_quantity')]*1)/($row[csf('exchange_rate')]*1);
				$yarn_data_array[$row[csf('job_no')]][$item_key][1].=$row[csf('rcv_iss_trans_id')].',';
				//$test_data="1_".$row[csf('order_amount')]."=";
				
			} 		
			else if ($row[csf('trans_type')]==4) 
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_rcv_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue_rtn']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue_rtn_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue_rtn_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$item_key]['issue_return']+=$row[csf('purchase_qnty')];
						
				$yarn_data_array[$row[csf('job_no')]][$item_key][4].=$row[csf('rcv_iss_trans_id')].',';	
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']+=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="4_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==5) 
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_rcv_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_in']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_in_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_in_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$item_key]['transfer_in_qnty']+=$row[csf('purchase_qnty')];
				
				$yarn_data_array[$row[csf('job_no')]][$item_key][5].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']+=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="5_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==2) 
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_issue_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_issue_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['knitting']+=$row[csf('purchase_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['others']+=$row[csf('others')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['re_conning_issue']+=$row[csf('re_conning_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['loan_issue']+=$row[csf('loan_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['sample_issue']+=$row[csf('sample_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['twisting_issue']+=$row[csf('twisting_received_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['linking_issue']+=$row[csf('linking')];
				
				
				$yarn_data_array[$row[csf('job_no')]][$item_key][2].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="2_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==3) 
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_issue_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv_return']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv_return_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_rcv_return_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$item_key]['rcv_return']+=$row[csf('others')];
				
				$yarn_data_array[$row[csf('job_no')]][$item_key][3].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="3_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==6) 
			{
				$yarn_data_array[$row[csf('job_no')]][$item_key]['tot_issue_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_out']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_out_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['opening_transfer_out_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$item_key]['transfer_out_qnty']+=$row[csf('purchase_qnty')];
				
				$yarn_data_array[$row[csf('job_no')]][$item_key][6].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$item_key]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="6_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";		
			}
			
		}
		//echo $test_data;die;
		//$test_data
		//echo "jahid<pre>";print_r($test_data_arr);die;
		//echo "<pre>";print_r($yarn_data_array);die;
		
		$lot_ratio_sql=sql_select("select a.job_no, b.prod_id, b.alocated_qty as alocated_qty 
		from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b
		where a.id=b.mst_id and a.entry_form=253 and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company_name $store_name_cond");
		$allocate_data=array();
		foreach($lot_ratio_sql as $row)
		{
			$allocate_data[$row[csf("job_no")]][$row[csf("prod_id")]]+=$row[csf("alocated_qty")];
		}
		
		//echo "<pre>";print_r($yarn_data_array);die;
		
		foreach ($yarn_data_array as $job_no => $prod_data) {
			$row_span = 0;
			foreach ($prod_data as $prod_id => $value) {
				$row_span++;
			}
			$job_wise_span[$job_no]=$row_span;
		}
		//unset($row);
		//var_dump($yarn_data_array);
		$all_prod_ids=implode(",",$all_prod_ids_arr);
		ob_start();

		?>
		 <div style="width:3620px;">
		<table width="3620" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
					<th width="150" align="center" rowspan="2">Company</th>
					<th width="100" align="center" rowspan="2">Buyer</th>
					<th width="110" align="center" rowspan="2">Style No</th>
					<th width="100" align="center" rowspan="2">Job No</th>
					<th width="100" align="center" rowspan="2">Image</th>
					<th width="100" align="center" rowspan="2">Yarn Supplier</th>
					<th width="80" align="center" rowspan="2">Yarn Count</th>
					<th width="130" align="center" rowspan="2">Yarn Composition</th>
					<th width="110" align="center" rowspan="2">Yarn Color</th>
					<th width="80" align="center" rowspan="2">Opening Balance (LBS)</th> 
					<th width="80" align="center" rowspan="2">Opening Value ($)</th> 
					<th width="640" align="center"  colspan="8">Receive Details (Lbs)</th> 
					<th width="880" align="center" colspan="11">Issue Details (Lbs)</th>
					<th width="100" align="center" rowspan="2">Current Stock (Lbs)</th>
					<th width="80" align="center" rowspan="2">Avg Rate/Lbs</th>
					<th width="110" align="center" rowspan="2">Total Amount (USD)</th>
					<th width="110" align="center" rowspan="2">Total Amount (BDT)</th>
					<th width="100" align="center" rowspan="2">Allocated</th>
					<th width="100" align="center" style="word-break: break-all;"  rowspan="2">Allocated Yarn Balance</th>
					<th width="100" align="center" rowspan="2">Available</th>
					<th  align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="80" >Purchase </th>
					<th width="80" >Issue Return</th>
					<th width="80" >Trans. In</th>
					<th width="80" >Loan Rcvd</th>
					<th width="80" >Twisting Rcvd</th>
					<th width="80" >Re-conning Rcvd</th>
					<th width="80" >Total Rcvd</th>
					<th width="80" >Total Rcvd. Value ($)</th>
					
                    <th width="80">Knitting</th>
					<th width="80">Re-conning Issue</th>
					<th width="80">Twisting Issue</th>
					<th width="80">Sample</th>
					<th width="80">Linking</th>
                    <th width="80">Loan Issue</th>
                    <th width="80">Others</th>
					<th width="80">Receive Return</th>
					<th width="80">Trans. Out</th>
					<th width="80">Total Issue</th>
					<th width="80">Total Issue Value ($)</th>
				</tr>
			</thead>
		</table>
	    <div style="width:3620px; overflow-y: scroll; max-height: 380px;" align="left">
			<table width="3620" class="rpt_table" rules="all" border="1" id="scroll_body"  style="word-break:break-all;">
				<tbody  class="rpt_table">
					<? 
					$i=1;$j=1;
					$tot_purchage="";$total_issue_qnty=$openint_qnty=$total_received_qnty=0;
					// echo "<pre>"; print_r($yarn_data_array);
					//echo $cbo_value_range_by.testsss;
					foreach ($yarn_data_array as $job_key=>$prod_data ) 
					{
						$kk=0;
						foreach ($prod_data as $row) 
						{
							if ($row['remarks']!="") {
								$remarks_cond = "#FF0000";
							}else{
								$remarks_cond = "none";
							}
							
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FEFEFE";
							
							$openint_qnty=(($row['opening_rcv']+$row['opening_issue_rtn']+$row['opening_transfer_in'])-($row['opening_issue']+$row['opening_rcv_return']+$row['opening_transfer_out']));


							$opening_amount=(($row['opening_rcv_amt']+$row['opening_issue_rtn_amt']+$row['opening_transfer_in_amt'])-($row['opening_issue_amt']+$row['opening_rcv_return_amt']+$row['opening_transfer_out_amt']));
							$opening_amount_usd=(($row['opening_rcv_amt_usd']+$row['opening_issue_rtn_amt_usd']+$row['opening_transfer_in_amt_usd'])-($row['opening_issue_amt_usd']+$row['opening_rcv_return_amt_usd']+$row['opening_transfer_out_amt_usd']));
							
							$total_received_qnty =(($row['purchase']*1)+($row['issue_return']*1)+($row['transfer_in_qnty']*1)+($row['loan_received']*1)+($row['twisting_received']*1)+($row['re_conning_received']*1));
							
							$total_issue_qnty =(($row['knitting']*1)+($row['re_conning_issue']*1)+($row['twisting_issue']*1)+($row['sample_issue']*1)+($row['linking_issue']*1)+($row['loan_issue']*1)+($row['others']*1)+($row['rcv_return']*1)+($row['transfer_out_qnty']*1));
							

							 //$current_stock_qnty =$row['current_stock']; $yarn_data_array[$row[csf('job_no')]][$item_key]['tot_issue_val']+=$row[csf('rcv_issue_val')]/$exchange_fac;
							$current_stock_qnty=(($openint_qnty+$total_received_qnty)-$total_issue_qnty);

							$total_receive_value=$row['tot_rcv_val'];
							$total_issue_value=$row['tot_issue_val'];
							
							$total_amount_usd = $row['current_stock_value_usd'];
							$total_amount_bdt = $row['current_stock_value_bdt'];
							$rate=0;
							//$row['current_stock']; replace by $current_stock_qnty new
							if($row['current_stock_value_usd']!=0 && number_format($current_stock_qnty,2,'.','')>0.00) $rate=$row['current_stock_value_usd']/$current_stock_qnty;
							
							
							$pord_id_arr=explode(",",chop($row['prod_id'],","));
							$allocate_qnty=0;
							foreach($pord_id_arr as $prodId)
							{
								$allocate_qnty+=$allocate_data[$row['job_no']][$prodId];
							}
							
							
							//for allocated yarn balance
							$allocatedYarnBalance = $allocate_qnty - $row['lot_issue'];

							if($current_stock_qnty>0 && $current_stock_qnty>=$allocatedYarnBalance)
							{
								$available_qnty = $current_stock_qnty-$allocate_qnty;
								$cu_available_qnty = $current_stock_qnty-$allocatedYarnBalance;
							}else{
								$available_qnty = 0;
								$cu_available_qnty=0;
							}
							
							if($cbo_value_range_by==2)
							{
								if(number_format($openint_qnty,2,'.','')>0.00 || number_format($total_received_qnty,2,'.','')>0.00 || number_format($total_issue_qnty,2,'.','')>0.00 || number_format($current_stock_qnty,2,'.','')>0.00)
								{
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="30" align="center"><? echo $j;?></td>
                                        <td width="150" style="word-break: break-all;"  valign="middle"><? echo $companyArr[$row['company']];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
                                        <td width="110" style="word-break: break-all;"  valign="middle"><? echo $row['style_ref_no'];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle"><? echo $row['job_no'];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle" align='center' onclick="openmypage_image('requires/stylewise_yarn_stock_report_controller.php?action=show_image&job_no=<? echo $row['job_no'];?>','Image View')"><img src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25'  /></td>
                                        <td width="100" style="word-break: break-all;"><? echo $supplierArr[$row['supplier_id']];?></td>
                                        <td width="80" style="word-break: break-all;"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
                                        <td width="130" style="word-break: break-all;"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
                                        <td width="110" style="word-break: break-all;"><? echo $color_name_arr[$row['color']];?></td>
                                        <td width="80" style="word-break: break-all;" title="<?=$row['opening_rcv']."+".$row['opening_issue_rtn']."+".$row['opening_transfer_in']."-".$row['opening_issue']."+".$row['opening_rcv_return']."+".$row['opening_transfer_out']?>" align="right"><? echo number_format($openint_qnty,2,'.',''); ?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? if(number_format($openint_qnty,2,'.','')>0) echo number_format($opening_amount_usd,2,'.',''); else echo "0.00";?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['purchase'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['issue_return'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_in_qnty'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_received'],2,'.','');?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_received'],2,'.','');?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_received'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>" align="right"> <a href='#report_details' onClick="openmypage('<? echo chop($row[1],",")."_".chop($row[4],",")."_".chop($row[5],","); ?>','<? echo chop($row['prod_id'],",");?>','received_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_received_qnty,2,'.','');?></a> </td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<?="total_rev_qty*cons_rate= ".$total_received_qnty."*".$row['cons_rate']?>"><? echo number_format($total_receive_value,2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['knitting'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_issue'],2,'.','');?></td>
                                        
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['sample_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['linking_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['others'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['rcv_return'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_out_qnty'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right"> <a href='#report_details' onClick="openmypage_issue('<? echo chop($row[2],",")."_".chop($row[3],","); ?>','<? echo $row[6]; ?>','<? echo chop($row['prod_id'],",");?>','<? echo $row['job_no'];?>','issue_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_issue_qnty,2);?></a> </td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<?="total_issue*Cons_rate=".$total_issue_qnty."*".$row['cons_rate']?>"><? echo number_format($total_issue_value,2,'.','');?></td>
                                        <td width="100" style="word-break: break-all;" align="right"><?  echo number_format($current_stock_qnty,2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<? echo "Stock Value Usd=".$row['current_stock_value_usd'].", Current Stock=".$row['current_stock'] ?>"><? echo number_format($rate,2,'.','');?></td>
                                        <td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_usd, 2,'.',''); else echo "0.00"; ?></td>
                                        <td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_bdt,2,'.',''); else echo "0.00"; ?></td>
                                        <td width="100" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right" title="<?= "tot allocate=".$allocate_qnty." allocate issue=".$row['lot_issue']; ?>"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo chop($row['prod_id'],",");?>','<? echo $row['job_no'];?>','alocate_popup');"><? echo number_format($allocate_qnty,2,'.','');?></a> </td>
                                        <td width="100" style="word-break: break-all;" align="right"> <? echo number_format($allocatedYarnBalance,4,'.','');?></td>
                                        <td width="100"style="word-break: break-all;" align="right" title="<?= "current stock-allocate qnty balance (Note: allocate qnty balance=allocate qnty-Lot ratio wise issue)"; ?>"> <? echo number_format($cu_available_qnty,4,'.','');
                                            ?>
                                        </td>
                                        <td><? echo $store_name_arr[$row['store_id']];?></td>
                                    </tr>
                                    <?
                                    $i++;$j++;
                                    $total_opening+=$openint_qnty;
                                    $total_opening_value+=$opening_amount_usd;
                                    $gt_purchage+=$row['purchase'];
                                    $gt_issue_return+=$row['issue_return'];
                                    $gt_trans_in+=$row['transfer_in_qnty'];
                                    $gt_loan_rcvd+=$row['loan_received'];
                                    $gt_twisting_rcvd+=$row['twisting_received'];
                                    $gt_reconning_recvd+=$row['re_conning_received'];
                                    $gt_recvd+=$total_received_qnty;
                                    $gt_rec_value+=$total_receive_value;
                                    
                                    $gt_knitting+=$row['knitting'];
                                    $gt_reconning_issue+=$row['re_conning_issue'];
                                    $gt_twisting_issue+=$row['twisting_issue'];
                                    $gt_sample+=$row['sample_issue'];
                                    $gt_linking+=$row['linking_issue'];
                                    $gt_loan_issue+=$row['loan_issue'];
                                    $gt_others+=$row['others'];
                                    $gt_recv_return+=$row['rcv_return'];
                                    $gt_trans_out+=$row['transfer_out_qnty'];								
                                    $gt_issue+= $total_issue_qnty;
                                    $gt_issue_value += $total_issue_value;
                                    
                                    
                                    
                                    $gt_current_stock+=$current_stock_qnty;
                                    $gt_usd_amount=$total_amount_usd;
                                    $gt_bdt_amount+=$total_amount_bdt;
                                    
                                    $gt_allocated+=$allocate_qnty;
                                    $gt_allocatedYarnBalance+=$allocatedYarnBalance;
                                    $gt_available+=$cu_available_qnty;
								}
							}
							else
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $j;?></td>
                                    <td width="150" style="word-break: break-all;"  valign="middle"><? echo $companyArr[$row['company']];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
                                    <td width="110" style="word-break: break-all;"  valign="middle"><? echo $row['style_ref_no'];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle"><? echo $row['job_no'];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle" align='center' onclick="openmypage_image('requires/stylewise_yarn_stock_report_controller.php?action=show_image&job_no=<? echo $row['job_no'];?>','Image View')"><img src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25'  /></td>
									<td width="100" style="word-break: break-all;"><? echo $supplierArr[$row['supplier_id']];?></td>
									<td width="80" style="word-break: break-all;"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
									<td width="130" style="word-break: break-all;"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
									<td width="110" style="word-break: break-all;"><? echo $color_name_arr[$row['color']];?></td>
									<td width="80" style="word-break: break-all;" title="<?=$row['opening_rcv']."+".$row['opening_issue_rtn']."+".$row['opening_transfer_in']."-".$row['opening_issue']."+".$row['opening_rcv_return']."+".$row['opening_transfer_out']?>" align="right"><? echo number_format($openint_qnty,2,'.',''); ?> </td>
									<td width="80" style="word-break: break-all;" title="<?=$row['opening_rcv_amt_usd']."+".$row['opening_issue_rtn_amt_usd']."+".$row['opening_transfer_in_amt_usd']."(-)".$row['opening_issue_amt_usd']."+".$row['opening_rcv_return_amt_usd']."+".$row['opening_transfer_out_amt_usd']?>" align="right"><? if(number_format($openint_qnty,2,'.','')>0) echo number_format($opening_amount_usd,2,'.',''); else echo "0.00";?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['purchase'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['issue_return'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_in_qnty'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_received'],2,'.','');?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_received'],2,'.','');?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_received'],2,'.','');?></td>
									<td width="80" title="<?=$row['purchase']."+".$row['issue_return']."+".$row['transfer_in_qnty']."+".$row['loan_received']."+".$row['twisting_received']."+".$row['re_conning_received'];?>" style="word-break: break-all; background-color: <? echo $remarks_cond;?>" align="right"> <a href='#report_details' onClick="openmypage('<? echo chop($row[1],",")."_".chop($row[4],",")."_".chop($row[5],",") ?>','<? echo chop($row['prod_id'],",");?>','received_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_received_qnty,2,'.','');?></a> </td>

									<td width="80" style="word-break: break-all;" align="right" title="<?="total_rev_qty*cons_rate= ".$total_received_qnty."*".$row['cons_rate']?>"><? echo number_format($total_receive_value,2,'.','');?></td>
                                    <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['knitting'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_issue'],2,'.','');?></td>
									
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['sample_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['linking_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['others'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['rcv_return'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_out_qnty'],2,'.','');?></td>
									<td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right"> <a href='#report_details' onClick="openmypage_issue('<? echo chop($row[2],",")."_".chop($row[3],","); ?>','<? echo $row[6]; ?>','<? echo chop($row['prod_id'],",");?>','<? echo $row['job_no'];?>','issue_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_issue_qnty,2);?></a> </td>
									<td width="80" style="word-break: break-all;" align="right" title="<?="total_issue*Cons_rate=".$total_issue_qnty."*".$row['cons_rate']?>"><? echo number_format($total_issue_value,2,'.','');?></td>
									<td width="100" style="word-break: break-all;" align="right"><?  echo number_format($current_stock_qnty,2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right" title="<? echo "Stock Value Usd=".$row['current_stock_value_usd'].", Current Stock=".number_format($current_stock_qnty,2) ?>"><? echo number_format($rate,2,'.','');?></td>
									<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_usd, 2,'.',''); else echo "0.00"; ?></td>
									<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_bdt,2,'.',''); else echo "0.00"; ?></td>
									<td width="100" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right" title="<?= "tot allocate=".$allocate_qnty." allocate issue=".$row['lot_issue']; ?>"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo chop($row['prod_id'],",");?>','<? echo $row['job_no'];?>','alocate_popup');"><? echo number_format($allocate_qnty,2,'.','');?></a> </td>
									<td width="100" style="word-break: break-all;" align="right"> <? echo number_format($allocatedYarnBalance,4,'.','');?></td>
									<td width="100"style="word-break: break-all;" align="right" title="<?= "current stock-allocate qnty balance (Note: allocate qnty balance=allocate qnty-Lot ratio wise issue)"; ?>"> <? echo number_format($cu_available_qnty,4,'.','');
										?>
									</td>
									<td><? echo $store_name_arr[$row['store_id']];?></td>
								</tr>
								<?
								$i++;$j++;
								$total_opening+=$openint_qnty;
								$total_opening_value+=$opening_amount_usd;
								$gt_purchage+=$row['purchase'];
								$gt_issue_return+=$row['issue_return'];
								$gt_trans_in+=$row['transfer_in_qnty'];
								$gt_loan_rcvd+=$row['loan_received'];
								$gt_twisting_rcvd+=$row['twisting_received'];
								$gt_reconning_recvd+=$row['re_conning_received'];
								$gt_recvd+=$total_received_qnty;
								$gt_rec_value+=$total_receive_value;
								
								$gt_knitting+=$row['knitting'];
								$gt_reconning_issue+=$row['re_conning_issue'];
								$gt_twisting_issue+=$row['twisting_issue'];
								$gt_sample+=$row['sample_issue'];
								$gt_linking+=$row['linking_issue'];
								$gt_loan_issue+=$row['loan_issue'];
								$gt_others+=$row['others'];
								$gt_recv_return+=$row['rcv_return'];
								$gt_trans_out+=$row['transfer_out_qnty'];								
								$gt_issue+= $total_issue_qnty;
								$gt_issue_value += $total_issue_value;
								
								
								
								$gt_current_stock+=$current_stock_qnty;
								$gt_usd_amount=$total_amount_usd;
								$gt_bdt_amount+=$total_amount_bdt;
								
								$gt_allocated+=$allocate_qnty;
								$gt_allocatedYarnBalance+=$allocatedYarnBalance;
								$gt_available+=$cu_available_qnty;
							}	
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr bgcolor="#ecfa8b">
						<td align="right" colspan="10" style="text-align:right;"><strong>Grand Total:</strong> </td>
						<td align="right"><? echo  number_format($total_opening,2,'.','');?></strong></td>
						<td align="right"><? echo  number_format($total_opening_value,2,'.','');?></strong></td>
						<td align="right"><? echo  number_format($gt_purchage,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_issue_return,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_trans_in,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_loan_rcvd,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_twisting_rcvd,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_reconning_recvd,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_recvd,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_rec_value,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_knitting,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_reconning_issue,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_twisting_issue,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_sample,2,'.','');?></strong></td>
                        <td align="right"><strong><? echo  number_format($gt_linking,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_loan_issue,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_others,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_recv_return,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_trans_out,2,'.','');?></strong></td>
						
						<td align="right"><strong><? echo  number_format($gt_issue,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_issue_value,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_current_stock,2,'.','');?></strong></td>
						<td align="right" id=""></td>
						<td align="right"><strong><? echo  number_format($gt_usd_amount,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_bdt_amount,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_allocated,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_allocatedYarnBalance,2,'.','');?></strong></td>
						<td align="right"><strong><? echo  number_format($gt_available,2,'.','');?></strong></td>
						<td ></td>
					</tr>
				</tfoot>
			</table>
		</div>
	 </div>
		<?
			$sql_without_wo_order="
			select b.id,b.company_id, b.supplier_id, b.prod_id, b.item_category, b.store_id,b.job_no, b.buyer_id, b.style_ref_no,b.entry_form, b.receive_basis as basis, b.cons_quantity, b.cons_amount, b.cons_rate, b.order_rate, 
			b.order_qnty, b.order_amount, b.transaction_type as trans_type, a.id as product_id, a.lot, a.color, a.current_stock, a.yarn_count_id, a.avg_rate_per_unit, a.allocated_qnty, a.available_qnty, a.yarn_comp_type1st, d.wo_number, d.booking_type, 1 as type
			from product_details_master a, inv_transaction b, com_pi_item_details c, wo_non_order_info_mst d
			where a.id=b.prod_id and b.pi_wo_batch_no=c.pi_id and c.work_order_id=d.id and b.transaction_type =1 and a.company_id  = $cbo_company_name and a.item_category_id=1 and b.item_category=1 and d.wo_basis_id=3 and d.entry_form=284 and b.prod_id in($all_prod_ids) $store_name_cond $buyer_cond $sample_yarn_comp_cond
			union all
			select b.id,b.company_id, b.supplier_id, b.prod_id, b.item_category, b.store_id,b.job_no, b.buyer_id, b.style_ref_no,b.entry_form, b.receive_basis as basis, b.cons_quantity, b.cons_amount, b.cons_rate, b.order_rate, 
			b.order_qnty, b.order_amount, b.transaction_type as trans_type, a.id as product_id, a.lot, a.color, a.current_stock, a.yarn_count_id, a.avg_rate_per_unit, a.allocated_qnty, a.available_qnty, a.yarn_comp_type1st, d.wo_number, d.booking_type, 2 as type
			from product_details_master a, inv_transaction b, inv_issue_master c, wo_non_order_info_mst d
			where a.id=b.prod_id and b.mst_id=c.id and c.buyer_job_no=d.wo_number and b.pi_wo_batch_no=d.id and b.transaction_type =2 and a.company_id  = $cbo_company_name and a.item_category_id=1 and b.item_category=1 and d.wo_basis_id=3 and d.entry_form=284 and c.issue_basis=10 and c.issue_purpose=8  and b.prod_id in($all_prod_ids)  $store_name_cond $buyer_cond $sample_yarn_comp_cond";
			//echo $sql_without_wo_order;
	
			$non_order_data_result=sql_select($sql_without_wo_order);
			$sample_data_array=array();
			foreach ($non_order_data_result as $row) {
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['wo_number'] = $row[csf('wo_number')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['booking_type'] = $row[csf('booking_type')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['company']=$row[csf('company_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
				if ($row[csf('trans_type')]==1) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['purchase']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_received']+=$row[csf('twisting_received')];
					$rate = ($row[csf('cons_amount')]*1)/($row[csf('cons_quantity')]*1)/($row[csf('exchange_rate')]*1);
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][1].=$row[csf('rcv_iss_trans_id')].',';	
					//$order_amount = ($row[csf('cons_quantity')]*1)*$rate;
				} 		
				else if ($row[csf('trans_type')]==4) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['issue_return']+=$row[csf('cons_quantity')];		
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_issue_return']+=$row[csf('cons_quantity')];		
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][4].=$row[csf('rcv_iss_trans_id')].',';		
				}
				else if ($row[csf('trans_type')]==5) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['transfer_in_qnty']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][5].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==2) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['knitting']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_issue']+=$row[csf('twisting_issue')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][2].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==3) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['rcv_return']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_rcv_return']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][3].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==6) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['transfer_out_qnty']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][6].=$row[csf('rcv_iss_trans_id')].',';
				}
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['loan_rcvd']+=$row[csf('loan_rcvd_issue_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['re_conning_rcvd']+=$row[csf('re_conning_rcvd_issue_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['re_conning_issue']+=$row[csf('re_conning_rcvd_issue_qnty')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['mending']+=$row[csf('mending')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['linking']+=$row[csf('linking')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['loan_issue']+=$row[csf('loan_issue')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['current_stock']=$row[csf('current_stock')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['cons_rate']=$row[csf('cons_rate')];
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_rate']=$rate;
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_amount']= ($row[csf('current_stock')]*1)*($rate*1);
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['cons_amount']=$row[csf('cons_amount')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['allocated_qnty']=$row[csf('allocated_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['available_qnty']=$row[csf('available_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['item_category']=$row[csf('item_category')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['entry_form']=$row[csf('entry_form')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
				$company=$row[csf('company_id')];
				
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['exchange_rate']=$row[csf('exchange_rate')];
	
				foreach ($sample_data_array as $job_no => $prod_data) {
					$row_span = 0;
					foreach ($prod_data as $prod_id => $value) {
						$row_span++;
					}
					$job_wise_span[$job_no]=$row_span;
				}
	
				
			}
			
	
		?>
		<h1 class="table_caption" style="margin-top: 15px;">Sample Against Yarn Receive : <? echo $company_short_name_array[$company];?></h1>
		<table width="3000" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
					<th width="80" align="center" rowspan="2">Company</th>
					<th width="100" align="center" rowspan="2">Buyer</th>
					<th width="110" align="center" rowspan="2">Style No</th>
					<th width="100" align="center" rowspan="2">WO No</th>
					<th width="80" align="center" rowspan="2">Booking Type</th>
					<th width="100" align="center" rowspan="2">Yarn Supplier</th>
					<th width="80" align="center" rowspan="2">Yarn Count</th>
					<th width="130" align="center" rowspan="2">Yarn Composition</th>
					<th width="110" align="center" rowspan="2">Yarn Color</th>
					<th width="80" align="center" rowspan="2">Lot</th> 
	
					<th width="" align="center" colspan="7">Receive Details (Lbs)</th> 
	
					<th width="" align="center" colspan="10">Issue Details (Lbs)</th>
	
					<th width="100" align="center" rowspan="2">Current Stock (Lbs)</th>
					<th width="80" align="center" rowspan="2">Avg Rate/Lbs</th>
					<th width="110" align="center" rowspan="2">Total Amount (USD)</th>
					<th width="110" align="center" rowspan="2">Total Amount (BDT)</th>
					<th width="100" align="center" rowspan="2">Allocated</th>
					<th width="100" align="center" rowspan="2">Allocated Yarn Balance</th>
					<th width="100" align="center" rowspan="2">Available</th>
					<th width="100" align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="80" >Purchase </th>
					<th width="80" >Issue Return</th>
					<th width="80" >Trans. In</th>
					<th width="80" >Loan Rcvd</th>
					<th width="80" >Twisting Rcvd</th>
					<th width="80" >Re-conning Rcvd</th>
					<th width="80" >Total Rcvd</th>
	
					<th width="80">Re-conning Issue</th>
					<th width="80">Twisting Issue</th>
					<th width="80">Knitting</th>
					<th width="80">Sample</th>
					<th width="80">Mending</th>
					<th width="80">Linking</th>
					<th width="80">Receive Return</th>
					<th width="80">Loan Issue</th>
					<th width="80">Trans. Out</th>
					<th width="80">Total Issue</th>
				</tr>
			</thead>
			<tbody id="scroll_body" class="rpt_table">
				<? 
				$i=1;
				$j=1;
				$bgcolor="#EEEEEE";
				$tot_purchage="";
				// echo "<pre>";
				// var_dump($yarn_data_array);
				foreach ($sample_data_array as $job_key=>$prod_data ) 
				{
					$kk=0;
					foreach ($prod_data as $prod_id => $row) 
					{
						$total_received_qnty +=(($row['purchase']*1)+($row['issue_return']*1)+($row['transfer_in_qnty']*1)+($row['loan_rcvd']*1)+($row['re_conning_rcvd']*1));
						//$cons_avg_rate=$row['avg_rate_per_unit']*1;
						//$total_received_amt =$row['purchase_amt'];
						$total_issue_qnty +=(($row['knitting']*1)+($row['re_conning_issue']*1)+($row['sample_issue']*1)+($row['mending']*1)+($row['linking']*1)+($row['rcv_return']*1)+($row['loan_issue']*1)+($row['transfer_out_qnty']*1));
						//$total_issue_amt +=$row['knitting_amt'];
						$current_stock_qnty =($total_received_qnty-$total_issue_qnty);
						$total_amount_usd = ($row['current_stock']*1)*($rate*1);
						$available_qnty = (($current_stock_qnty)-($row['allocated_qnty']*1));
						//$available_qnty = ($row['available_qnty']*1)-($row['transfer_out_qnty']*1);
						
						//for allocated yarn balance
						$allocatedYarnBlnc = $row['allocated_qnty'] - $total_issue_qnty;
					
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<?
							if($kk==0)
							{
								$rowspan=$job_wise_span[$job_key];
							 
							?>
							<td rowspan="<? echo $rowspan;?>"><? echo $j;?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $companyArr[$row['company']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $buyerArr[$row['buyer_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $row['style_ref_no'];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $row['job_no'];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $short_booking_type[$row['booking_type']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $supplierArr[$row['supplier_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
							<?
							$j++;
							$kk++;
							}
							/*,'<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>'*/
								
							?>
							<td><? echo $color_name_arr[$row['color']];?></td>
							<td title="<? echo "Prod_ID==".$row['prod_id']; ?>"><? echo $row['lot'];?> </td>
	
							<td align="right" ><? echo $row['purchase'];?></td>
							<td align="right"><? echo $row['issue_return'];?></td>
							<td align="right"><? echo $row['transfer_in_qnty'];?></td>
							<td align="right"><? echo $row['loan_rcvd'];?> </td>
							<td align="right"><? echo $row['twisting_received'];?> </td>
							<td align="right"><? echo $row['re_conning_rcvd'];?></td>
							<td align="right"> <a href='#report_details' onClick="openmypage('<? echo $row[1]; ?>','<? echo $row['prod_id'];?>','received_popup','','');"><? echo number_format($total_received_qnty,2);?></a> </td>
	
							<td align="right"><? echo $row['re_conning_issue'];?></td>
							<td align="right"><? echo $row['twisting_issue'];?></td>
							<td align="right"><? echo $row['knitting'];?></td>
							<td align="right"><? echo $row['sample_issue'];?></td>
							<td align="right"><? echo $row['mending'];?></td>
							<td align="right"><? echo $row['linking	'];?></td>
							<td align="right"><? echo $row['rcv_return'];?></td>
							<td align="right"><? echo $row['loan_issue'];?></td>
							<td align="right"><? echo $row['transfer_out_qnty'];?></td>
							<td align="right"><? echo number_format($total_issue_qnty,2);?></td>
	
							<td align="right"><? echo number_format($current_stock_qnty,2);?></td>
							<td align="right"><? echo $rate;?></td>
							<td align="right"><? echo number_format($total_amount_usd, 2);?></td>
							<td align="right"><? echo number_format($row['cons_amount'],2);?></td>
							<td align="right"><? echo $row['allocated_qnty'];?></td>
							<td align="right"><? echo $allocatedYarnBlnc;?></td>
							<td align="right"><? echo $available_qnty;?></td>
							<td><? echo $store_name_arr[$row['store_id']];?></td>
						</tr>
						<?
						$total_received_qnty=$total_received_amt=$total_issue_qnty=$total_issue_amt=$total_amount=0;	
						$i++;
						$tot_purchage+=$row['purchase'];
						$total_issue_return+=$row['issue_return'];
						$total_trans_in+=$row['transfer_in_qnty'];
						$total_loan_rcvd+=$row['loan_rcvd'];
						$total_twisting_rcvd+=$row['twisting_received'];
						$total_reconning_recvd+=$row['re_conning_rcvd'];
						$total_recvd=($row['purchase']*1) + ($row['issue_return']*1) + ($row['transfer_in_qnty']*1) + ($row['loan_rcvd']*1) + ($row['re_conning_rcvd']*1);
						$grand_total_received +=$total_recvd;
						$total_reconning_issue+=$row['re_conning_issue'];
						$total_twisting_issue+=$row['twisting_issue'];
						$total_knitting+=$row['knitting'];
						$total_sample+=$row['sample_issue'];
						$total_mending+=$row['mending'];
						$total_linking+=$row['linking'];
						$total_recv_return+=$row['rcv_return'];
						$total_loan_issue+=$row['loan_issue'];
						$total_trans_out+=$row['transfer_out_qnty'];
						$total_issue= ($row['knitting']*1) + ($row['sample_issue']*1) +($row['mending']*1) +($row['linking']*1) +($row['rcv_return']*1) +($row['transfer_out_qnty']*1) + ($row['re_conning_issue']*1) + ($row['loan_issue']*1);
						$grand_total_issue += $total_issue;
						$total_current_stock+=$row['current_stock'];
						$total_usd_amount=$total_current_stock*$rate;
						$total_bdt_amount+=$row['cons_amount'];
						$total_allocated+=$row['allocated_qnty'];
						$total_available+=$row['available_qnty'];
						$total_allocatedYarnBlnc+=$allocatedYarnBlnc;
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr bgcolor="#ecfa8b">
					<td align="right" colspan="11" style="text-align:right;"><strong>Grand Total:</strong> </td>
					<td align="right" id="total_puchase"><? echo  number_format($tot_purchage,2);?></strong></td>
					<td align="right" id="total_issue_return)"><strong><? echo  number_format($total_issue_return,2);?></strong></td>
					<td align="right" id="total_trans_in"><strong><? echo  number_format($total_trans_in,2);?></strong></td>
					<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_loan_rcvd,2);?></strong></td>
					<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_twisting_rcvd,2);?></strong></td>
					<td align="right" id="total_reconning_recvd"><strong><? echo  number_format($total_reconning_recvd,2);?></strong></td>
					<td align="right" id="total_recvd"><strong><? echo  number_format($grand_total_received,2);?></strong></td>
					<td align="right" id="total_reconning_issue"><strong><? echo  number_format($total_reconning_issue,2);?></strong></td>
					<td align="right" id="total_knitting"><strong><? echo  number_format($total_twisting_issue,2);?></strong></td>
					<td align="right" id="total_knitting"><strong><? echo  number_format($total_knitting,2);?></strong></td>
					<td align="right" id="total_sample"><strong><? echo  number_format($total_sample,2);?></strong></td>
					<td align="right" id="total_mending"><strong><? echo  number_format($total_mending,2);?></strong></td>
					<td align="right" id="total_linking"><strong><? echo  number_format($total_linking,2);?></strong></td>
					<td align="right" id="total_recv_return"><strong><? echo  number_format($total_recv_return,2);?></strong></td>
					<td align="right" id="total_loan_issue"><strong><? echo  number_format($total_loan_issue,2);?></strong></td>
					<td align="right" id="total_trans_out"><strong><? echo  number_format($total_trans_out,2);?></strong></td>
					<td align="right" id="total_issue"><strong><? echo  number_format($grand_total_issue,2);?></strong></td>
					<td align="right" id="total_current_stock"><strong><? echo  number_format($total_current_stock,2);?></strong></td>
					<td align="right" id=""></td>
					<td align="right" id="total_usd_amount"><strong><? echo  number_format($total_usd_amount,2);?></strong></td>
					<td align="right" id="total_bdt_amount"><strong><? echo  number_format($total_bdt_amount,2);?></strong></td>
					<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocated,2);?></strong></td>
					<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocatedYarnBlnc,2);?></strong></td>
					<td align="right" id="total_available"><strong><? echo  number_format($total_available,2);?></strong></td>
					<td ></td>
				</tr>
			</tfoot>
		</table>
		<?
	}
	else if($rpt_type==333)//show2 button backup
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$company_short_name_array = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
		$companyArr[0] = "All Company";
		$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
		//$composition
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
		$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	
		if ($db_type == 0) {
			//$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
			$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			//$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
			$from_date = change_date_format($from_date, '', '', 1);
			$to_date = change_date_format($to_date, '', '', 1);
			//$cbo_year = "extract()";cbo_year_selection
		} else {
			$from_date = "";
			$to_date = "";
			//$exchange_rate = 1;
		}
		

		//echo $txt_job_no;die;
		$job_no_po_cond=$job_no_datas="";$job_no_cond="";
		if($txt_job_no!="") 
		{
			$txt_job_no_array=explode(",",$txt_job_no);
			$job_no_datas="";
			foreach($txt_job_no_array as $job_no)
			{
				$job_no_datas.="'".$job_no."',";
			}
			$job_no_datas=chop($job_no_datas,",");
			//echo $job_no_datas;die;
			if($cbo_ship_status>0)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name and wo_po_details_master.job_no in($job_no_datas) $date_cond
				group by job_no_mst 
				having count(*)=count(case shiping_status when 3 then 1 else null end)";
				//echo $sql."<br>";//die;
				$ship_result=sql_select($sql);				
				$job_no_in_full_shipment=array();
				$sl=0;
				$job_no_cond=" and a.job_no in (";
				foreach ($ship_result as $row) {
					array_push($job_no_in_full_shipment, $row[csf('job_no_mst')]);
					if($sl>0) $job_no_cond.=",";
					$job_no_cond.="'".$row[csf('job_no_mst')]."'";
					$sl++;
					//echo "<pre>".$row[csf('job_no_mst')]."</pre>";
				}
				if($cbo_ship_status==1){
					$sl=0;
					$job_no_cond=" and a.job_no in (";
					foreach ($txt_job_no_array as $job_no) {
						
						if(!in_array($job_no, $job_no_in_full_shipment)){
							$job_no_cond.="'".$job_no."',";
						}
					}
					$job_no_cond=chop($job_no_cond,",");
					$job_no_cond.=")";
					//echo $job_no_cond;die;
				}
				else
				{
					$job_no_cond.=")";
				}
			}
			else
			{
				$job_no_cond=" and a.job_no in($job_no_datas)";
			}
		} 
		else
		{
			if($cbo_ship_status==2)
			{
				$sql="select count(*) as total_count,count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			else if($cbo_ship_status==1) 
			{
				$sql="select count(*) as total_count, count(case shiping_status when 3 then 1 else null end) as full_count,job_no_mst 
				from wo_po_break_down join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no 
				where wo_po_details_master.company_name=$cbo_company_name $date_cond_order  
				group by job_no_mst having count(*)=count(case shiping_status when 3 then 1 else null end)";
			}
			//echo $sql."<br>";//die;
			$ship_result=sql_select($sql);
			//print_r($ship_result);die;
			foreach ($ship_result as $row) {
				$tot_rows++;
				$jobNos .= "'".$row[csf('job_no_mst')]."',";
			}
			//echo $jobNos;die;
			$job_no_cond = '';
			if ($jobNos != '')
			{
				$jobNos = array_flip(array_flip(explode(',', rtrim($jobNos,','))));
				//print_r($jobNos);die;
				if($db_type==2 && $tot_rows>1000)
				{
					$job_no_cond = ' and (';
					$jobNoArr = array_chunk($jobNos,999);
					foreach($jobNoArr as $jobs)
					{
						$jobs = implode(',',$jobs);
						$job_no_cond .= " a.job_no in($jobs) or ";
					}
					$job_no_cond = rtrim($job_no_cond,'or ');
					$job_no_cond .= ')';
				}
				else
				{
					$jobNos = implode(',', $jobNos);
					$job_no_cond=" and a.job_no in ($jobNos)";
				}
			}
		}
		
	
		if($cbo_store_name!=0){
			$store_name_cond=" and a.store_id in($cbo_store_name)"; 
		}else {
			$store_name_cond="";
		}
	
		if($cbo_buyer!=0){
			$buyer_cond=" and a.buyer_id in($cbo_buyer)"; 
		}else {
			$buyer_cond="";
		}

		if ($txt_composition_id != "")
		{
			$yarn_comp_cond = " and b.yarn_comp_type1st in ($txt_composition_id) ";
			$sample_yarn_comp_cond = " and a.yarn_comp_type1st in ($txt_composition_id) ";
		}
		
		$current_date=date("d-m-Y");
		$p=1;
		$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
		$company_wise_data=array();
		foreach($queryText as $row)
		{
			$company_wise_data[$row["COMPANY_ID"]]++;
		}
		//echo count($queryText);die;
		$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
		foreach($queryText as $val)
		{
			if($company_check_arr[$val["COMPANY_ID"]]=="")
			{
				$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
				$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				$sEndDate = $sStartDate;
				$previous_date=$sStartDate;
				$previous_rate=$val["CONVERSION_RATE"];
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				
				$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {
					
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$q=1;
			}
			else
			{
				$q++;
				$sStartDate = date("Y-m-d", strtotime($previous_date));
				if($company_wise_data[$val["COMPANY_ID"]]==$q)
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					
					$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
					$sEndDate = date("Y-m-d", strtotime($current_date));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
					while ($sCurrentDate <= $sEndDate) {
						
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
				else
				{
					$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
					$sCurrentDate = $sStartDate;
					//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
					while ($sCurrentDate <= $sEndDate) {
						$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
					}
					$previous_date=$val["CON_DATE"];
					$previous_rate=$val["CONVERSION_RATE"];
				}
			}
			$p++;
		}
		unset($queryText);
		//echo "<pre>";print_r($conversion_data_arr);die;
		
		$date_cond = $date_cond_order = "";
		if($from_date!="" && $to_date!="")
		{
			if($cbo_date_type==1) $date_cond = " and a.transaction_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==2) $date_cond_order = " and po_received_date between '$from_date' and '$to_date' ";
			else if($cbo_date_type==3) $date_cond_order = " and pub_shipment_date between '$from_date' and '$to_date' ";
		}
		//$job_no_cond=" and a.job_no in ('SSL-20-00300')";
		//$job_no_cond.=" and a.prod_id=17593";
		
		//echo $job_no_cond.tst;die;
		$sql = "SELECT a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when c.receive_purpose not in(5,12,15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as purchase_qnty,
		0 as others,
		(case when c.receive_purpose in(12) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as re_conning_rcvd_issue_qnty, 
		(case when c.receive_purpose in(5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as loan_rcvd_issue_qnty,
		0 as sample_issue,
		(case when c.receive_purpose in(15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0  end) as twisting_received_issue,
		0 as linking,
		0 as lot_issue
		from inv_transaction a, product_details_master b, inv_receive_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1,4) and a.entry_form in(248,382) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond 
		union all
		select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when c.issue_purpose in(1) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as knitting_qnty,
		(case when c.issue_purpose not in(1,5,4,8,12,15,47) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as others,
		(case when c.issue_purpose in(12) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as re_conning_rcvd_issue_qnty,
		(case when c.issue_purpose in(5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as loan_rcvd_issue_qnty,
		(case when c.issue_purpose in(4,8) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as sample_issue,
		(case when c.issue_purpose in(15) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as twisting_received_issue,
		(case when c.issue_purpose in(47) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as linking,
		(case when a.receive_basis=6 and a.transaction_type=2 then a.cons_quantity else 0 end) as lot_issue
		from inv_transaction a, product_details_master b, inv_issue_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277,381) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond 
		union all
		select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.transaction_date, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose, a.cons_quantity,
		(case when a.transaction_date < '$from_date' then a.cons_quantity else 0 end) as opening_rcv_issue,
		(case when a.transaction_date < '$from_date' then a.cons_amount else 0 end) as opening_rcv_issue_amt,
		(case when a.transaction_date < '$from_date' then a.order_amount else 0 end) as opening_rcv_issue_amt_usd,
		(case when a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as transfer_quantity,
		0 as others,
		0 as re_conning_rcvd_issue_qnty,
		0 as loan_rcvd_issue_qnty,
		0 as sample_issue,
		0 as twisting_received_issue,
		0 as linking,
		0 as lot_issue
		from inv_transaction a, product_details_master b, inv_item_transfer_mst c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5,6) and a.entry_form in(249) $job_no_cond $store_name_cond $buyer_cond $yarn_comp_cond 
		order by id";
		//echo $sql;die;
		$result = sql_select($sql);
		//echo "<pre>";print_r($result);die;
		foreach ($result  as $row) 

		{
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['company']=$row[csf('company_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_purpose']=$row[csf('issue_purpose')];
			
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['cons_rate']=$row[csf('cons_rate')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['cons_amount']=$row[csf('cons_amount')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['allocated_qnty']=$row[csf('allocated_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['available_qnty']=$row[csf('available_qnty')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['item_category']=$row[csf('item_category')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['entry_form']=$row[csf('entry_form')];
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
			
			$all_prod_ids.=$row[csf('prod_id')].',';
			$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot_issue']+=$row[csf('lot_issue')];
			$exchange_fac= 1;
			if($conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])]>0) $exchange_fac= $conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
			if ($row[csf('trans_type')]==1) 
			{
				$exchange_fac= 1;
				if($row[csf('cons_amount')]==$row[csf('order_amount')])
				{
					if($conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])]>0) $exchange_fac= $conversion_data_arr[$row[csf('company_id')]][change_date_format($row[csf('transaction_date')])];
				}
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv_amt']+=$row[csf('opening_rcv_issue_amt')];
				
				if($row[csf('opening_rcv_issue_amt_usd')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv_amt_usd']+=$row[csf('opening_rcv_issue_amt_usd')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['purchase']+=$row[csf('purchase_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_received']+=$row[csf('twisting_received')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['loan_received']+=$row[csf('loan_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['re_conning_received']+=$row[csf('re_conning_rcvd_issue_qnty')];
				
					
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				if($row[csf('order_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('order_amount')]/$exchange_fac;
				
				$rate = ($row[csf('cons_amount')]*1)/($row[csf('cons_quantity')]*1)/($row[csf('exchange_rate')]*1);
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][1].=$row[csf('rcv_iss_trans_id')].',';
				//$test_data="1_".$row[csf('order_amount')]."=";
				
			} 		
			else if ($row[csf('trans_type')]==4) 
			{
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue_rtn']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue_rtn_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue_rtn_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_return']+=$row[csf('purchase_qnty')];
						
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][4].=$row[csf('rcv_iss_trans_id')].',';	
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="4_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==5) 
			{
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_in']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_in_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_in_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['transfer_in_qnty']+=$row[csf('transfer_quantity')];
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][5].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']+=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']+=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']+=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="5_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==2) 
			{
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_issue_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['knitting']+=$row[csf('knitting_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['others']+=$row[csf('others')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['re_conning_issue']+=$row[csf('re_conning_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['loan_issue']+=$row[csf('loan_rcvd_issue_qnty')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['twisting_issue']+=$row[csf('twisting_received_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['linking_issue']+=$row[csf('linking')];
				
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][2].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="2_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==3) 
			{
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv_return']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv_return_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_rcv_return_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['rcv_return']+=$row[csf('others')];
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][3].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="3_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";	
			}
			else if ($row[csf('trans_type')]==6) 
			{
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_out']+=$row[csf('opening_rcv_issue')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_out_amt']+=$row[csf('opening_rcv_issue_amt')];
				if($row[csf('opening_rcv_issue_amt')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['opening_transfer_out_amt_usd']+=$row[csf('opening_rcv_issue_amt')]/$exchange_fac;
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['transfer_out_qnty']+=$row[csf('transfer_quantity')];
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]][6].=$row[csf('rcv_iss_trans_id')].',';
				
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock']-=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_bdt']-=$row[csf('cons_amount')];
				if($row[csf('cons_amount')]>0) $yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['current_stock_value_usd']-=$row[csf('cons_amount')]/$exchange_fac;
				$test_data.="6_".$row[csf('cons_amount')]."*".$row[csf('cons_amount')]/$exchange_fac."=".$exchange_fac."=".$row[csf('company_id')]."=".change_date_format($row[csf('transaction_date')])."__";		
			}
			
		}
		//echo $test_data;die;
		//echo "<pre>";print_r($yarn_data_array);die;
		
		$lot_ratio_sql=sql_select("select a.job_no, b.prod_id, b.alocated_qty as alocated_qty 
		from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b
		where a.id=b.mst_id and a.entry_form=253 and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company_name $store_name_cond");
		$allocate_data=array();
		foreach($lot_ratio_sql as $row)
		{
			$allocate_data[$row[csf("job_no")]][$row[csf("prod_id")]]+=$row[csf("alocated_qty")];
		}
		
		//echo "<pre>";print_r($yarn_data_array);die;
		
		foreach ($yarn_data_array as $job_no => $prod_data) {
			$row_span = 0;
			foreach ($prod_data as $prod_id => $value) {
				$row_span++;
			}
			$job_wise_span[$job_no]=$row_span;
		}
		//unset($row);
		//var_dump($yarn_data_array);
		$all_prod_ids=rtrim($all_prod_ids,',');
		ob_start();

		?>
		 <div style="width:3620px;">
		<table width="3620" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
					<th width="150" align="center" rowspan="2">Company</th>
					<th width="100" align="center" rowspan="2">Buyer</th>
					<th width="110" align="center" rowspan="2">Style No</th>
					<th width="100" align="center" rowspan="2">Job No</th>
					<th width="100" align="center" rowspan="2">Image</th>
					<th width="100" align="center" rowspan="2">Yarn Supplier</th>
					<th width="80" align="center" rowspan="2">Yarn Count</th>
					<th width="130" align="center" rowspan="2">Yarn Composition</th>
					<th width="110" align="center" rowspan="2">Yarn Color</th>
					<th width="80" align="center" rowspan="2">Opening Balance (LBS)</th> 
					<th width="80" align="center" rowspan="2">Opening Value ($)</th> 
					<th width="640" align="center"  colspan="8">Receive Details (Lbs)</th> 
					<th width="880" align="center" colspan="11">Issue Details (Lbs)</th>
					<th width="100" align="center" rowspan="2">Current Stock (Lbs)</th>
					<th width="80" align="center" rowspan="2">Avg Rate/Lbs</th>
					<th width="110" align="center" rowspan="2">Total Amount (USD)</th>
					<th width="110" align="center" rowspan="2">Total Amount (BDT)</th>
					<th width="100" align="center" rowspan="2">Allocated</th>
					<th width="100" align="center" style="word-break: break-all;"  rowspan="2">Allocated Yarn Balance</th>
					<th width="100" align="center" rowspan="2">Available</th>
					<th  align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="80" >Purchase </th>
					<th width="80" >Issue Return</th>
					<th width="80" >Trans. In</th>
					<th width="80" >Loan Rcvd</th>
					<th width="80" >Twisting Rcvd</th>
					<th width="80" >Re-conning Rcvd</th>
					<th width="80" >Total Rcvd</th>
					<th width="80" >Total Rcvd. Value ($)</th>
					
                    <th width="80">Knitting</th>
					<th width="80">Re-conning Issue</th>
					<th width="80">Twisting Issue</th>
					<th width="80">Sample</th>
					<th width="80">Linking</th>
                    <th width="80">Loan Issue</th>
                    <th width="80">Others</th>
					<th width="80">Receive Return</th>
					<th width="80">Trans. Out</th>
					<th width="80">Total Issue</th>
					<th width="80">Total Issue Value ($)</th>
				</tr>
			</thead>
		</table>
	    <div style="width:3620px; overflow-y: scroll; max-height: 380px;" align="left">
			<table width="3620" class="rpt_table" rules="all" border="1" id="scroll_body"  style="word-break:break-all;">
				<tbody  class="rpt_table">
					<? 
					$i=1;$j=1;
					$tot_purchage="";
					// echo "<pre>"; print_r($yarn_data_array);
					//echo $cbo_value_range_by.testsss;
					foreach ($yarn_data_array as $job_key=>$prod_data ) 
					{
						$kk=0;
						foreach ($prod_data as $row) 
						{
							if ($row['remarks']!="") {
								$remarks_cond = "#FF0000";
							}else{
								$remarks_cond = "none";
							}
							
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FEFEFE";
							
							$openint_qnty=(($row['opening_rcv']+$row['opening_issue_rtn']+$row['opening_transfer_in'])-($row['opening_issue']+$row['opening_rcv_return']+$row['opening_transfer_out']));
							$opening_amount=(($row['opening_rcv_amt']+$row['opening_issue_rtn_amt']+$row['opening_transfer_in_amt'])-($row['opening_issue_amt']+$row['opening_rcv_return_amt']+$row['opening_transfer_out_amt']));
							$opening_amount_usd=(($row['opening_rcv_amt_usd']+$row['opening_issue_rtn_amt_usd']+$row['opening_transfer_in_amt_usd'])-($row['opening_issue_amt_usd']+$row['opening_rcv_return_amt_usd']+$row['opening_transfer_out_amt_usd']));
							
							$total_received_qnty =(($row['purchase']*1)+($row['issue_return']*1)+($row['transfer_in_qnty']*1)+($row['loan_received']*1)+($row['twisting_received']*1)+($row['re_conning_received']*1));
							
							$total_issue_qnty =(($row['knitting']*1)+($row['re_conning_issue']*1)+($row['twisting_issue']*1)+($row['sample_issue']*1)+($row['linking_issue']*1)+($row['loan_issue']*1)+($row['others']*1)+($row['rcv_return']*1)+($row['transfer_out_qnty']*1));
							
							$current_stock_qnty =$row['current_stock'];
							$total_amount_usd = $row['current_stock_value_usd'];
							$total_amount_bdt = $row['current_stock_value_bdt'];
							$rate=0;
							if($row['current_stock_value_usd']!=0 && $row['current_stock']!=0) $rate=$row['current_stock_value_usd']/$row['current_stock'];
							
							$total_receive_value=$total_received_qnty*$rate;
							$total_issue_value=$total_issue_qnty*$rate;
							
							$allocate_qnty=$allocate_data[$row['job_no']][$row['prod_id']];
							
							
							
							//for allocated yarn balance
							$allocatedYarnBalance = $allocate_qnty - $row['lot_issue'];

							if($current_stock_qnty>0 && $current_stock_qnty>=$allocatedYarnBalance)
							{
								$available_qnty = $current_stock_qnty-$allocate_qnty;
								$cu_available_qnty = $current_stock_qnty-$allocatedYarnBalance;
							}else{
								$available_qnty = 0;
								$cu_available_qnty=0;
							}
							
							if($cbo_value_range_by==2)
							{
								if(number_format($openint_qnty,2,'.','')>0.00 || number_format($total_received_qnty,2,'.','')>0.00 || number_format($total_issue_qnty,2,'.','')>0.00 || number_format($current_stock_qnty,2,'.','')>0.00)
								{
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="30" align="center"><? echo $j;?></td>
                                        <td width="150" style="word-break: break-all;"  valign="middle"><? echo $companyArr[$row['company']];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
                                        <td width="110" style="word-break: break-all;"  valign="middle"><? echo $row['style_ref_no'];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle"><? echo $row['job_no'];?></td>
                                        <td width="100" style="word-break: break-all;"  valign="middle" align='center' onclick="openmypage_image('requires/stylewise_yarn_stock_report_controller.php?action=show_image&job_no=<? echo $row['job_no'];?>','Image View')"><img src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25'  /></td>
                                        <td width="100" style="word-break: break-all;"><? echo $supplierArr[$row['supplier_id']];?></td>
                                        <td width="80" style="word-break: break-all;"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
                                        <td width="130" style="word-break: break-all;"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
                                        <td width="110" style="word-break: break-all;"><? echo $color_name_arr[$row['color']];?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($openint_qnty,2,'.',''); ?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($opening_amount_usd,2,'.','');?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['purchase'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['issue_return'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_in_qnty'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_received'],2,'.','');?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_received'],2,'.','');?> </td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_received'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>" align="right"> <a href='#report_details' onClick="openmypage('<? echo $row[1]; ?>','<? echo $row['prod_id'];?>','received_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_received_qnty,2,'.','');?></a> </td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<?="total_rev_qty*cons_rate= ".$total_received_qnty."*".$row['cons_rate']?>"><? echo number_format($total_receive_value,2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['knitting'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_issue'],2,'.','');?></td>
                                        
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['sample_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['linking_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_issue'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['others'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['rcv_return'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_out_qnty'],2,'.','');?></td>
                                        <td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','issue_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_issue_qnty,2);?></a> </td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<?="total_issue*Cons_rate=".$total_issue_qnty."*".$row['cons_rate']?>"><? echo number_format($total_issue_value,2,'.','');?></td>
                                        <td width="100" style="word-break: break-all;" align="right"><?  echo number_format($current_stock_qnty,2,'.','');?></td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<? echo "Stock Value Usd=".$row['current_stock_value_usd'].", Current Stock=".$row['current_stock'] ?>"><? echo number_format($rate,2,'.','');?></td>
                                        <td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_usd, 2,'.',''); else echo "0.00"; ?></td>
                                        <td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_bdt,2,'.',''); else echo "0.00"; ?></td>
                                        <td width="100" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right" title="<?= "tot allocate=".$allocate_data[$row['job_no']][$row['prod_id']]." allocate issue=".$row['lot_issue']; ?>"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','alocate_popup');"><? echo number_format($allocate_qnty,2,'.','');?></a> </td>
                                        <td width="100" style="word-break: break-all;" align="right"> <? echo number_format($allocatedYarnBalance,4,'.','');?></td>
                                        <td width="100"style="word-break: break-all;" align="right" title="<?= "current stock-allocate qnty balance (Note: allocate qnty balance=allocate qnty-Lot ratio wise issue)"; ?>"> <? echo number_format($cu_available_qnty,4,'.','');
                                            ?>
                                        </td>
                                        <td><? echo $store_name_arr[$row['store_id']];?></td>
                                    </tr>
                                    <?
                                    $i++;$j++;
                                    $total_opening+=$openint_qnty;
                                    $total_opening_value+=$opening_amount_usd;
                                    $gt_purchage+=$row['purchase'];
                                    $gt_issue_return+=$row['issue_return'];
                                    $gt_trans_in+=$row['transfer_in_qnty'];
                                    $gt_loan_rcvd+=$row['loan_received'];
                                    $gt_twisting_rcvd+=$row['twisting_received'];
                                    $gt_reconning_recvd+=$row['re_conning_received'];
                                    $gt_recvd=$total_received_qnty;
                                    $gt_rec_value+=$total_receive_value;
                                    
                                    $gt_knitting+=$row['knitting'];
                                    $gt_reconning_issue+=$row['re_conning_issue'];
                                    $gt_twisting_issue+=$row['twisting_issue'];
                                    $gt_sample+=$row['sample_issue'];
                                    $gt_linking+=$row['linking_issue'];
                                    $gt_loan_issue+=$row['loan_issue'];
                                    $gt_others+=$row['others'];
                                    $gt_recv_return+=$row['rcv_return'];
                                    $gt_trans_out+=$row['transfer_out_qnty'];								
                                    $gt_issue= $total_issue_qnty;
                                    $gt_issue_value += $total_issue_value;
                                    
                                    
                                    
                                    $gt_current_stock+=$current_stock_qnty;
                                    $gt_usd_amount=$total_amount_usd;
                                    $gt_bdt_amount+=$total_amount_bdt;
                                    
                                    $gt_allocated+=$allocate_qnty;
                                    $gt_allocatedYarnBalance+=$allocatedYarnBalance;
                                    $gt_available+=$cu_available_qnty;
								}
							}
							else
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $j;?></td>
                                    <td width="150" style="word-break: break-all;"  valign="middle"><? echo $companyArr[$row['company']];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
                                    <td width="110" style="word-break: break-all;"  valign="middle"><? echo $row['style_ref_no'];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle"><? echo $row['job_no'];?></td>
                                    <td width="100" style="word-break: break-all;"  valign="middle" align='center' onclick="openmypage_image('requires/stylewise_yarn_stock_report_controller.php?action=show_image&job_no=<? echo $row['job_no'];?>','Image View')"><img src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25'  /></td>
									<td width="100" style="word-break: break-all;"><? echo $supplierArr[$row['supplier_id']];?></td>
									<td width="80" style="word-break: break-all;"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
									<td width="130" style="word-break: break-all;"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
									<td width="110" style="word-break: break-all;"><? echo $color_name_arr[$row['color']];?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($openint_qnty,2,'.',''); ?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($opening_amount_usd,2,'.','');?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['purchase'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['issue_return'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_in_qnty'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_received'],2,'.','');?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_received'],2,'.','');?> </td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_received'],2,'.','');?></td>
									<td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>" align="right"> <a href='#report_details' onClick="openmypage('<? echo $row[1]; ?>','<? echo $row['prod_id'];?>','received_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_received_qnty,2,'.','');?></a> </td>
									<td width="80" style="word-break: break-all;" align="right" title="<?="total_rev_qty*cons_rate= ".$total_received_qnty."*".$row['cons_rate']?>"><? echo number_format($total_receive_value,2,'.','');?></td>
                                    <td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['knitting'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['re_conning_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['twisting_issue'],2,'.','');?></td>
									
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['sample_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['linking_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['loan_issue'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['others'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['rcv_return'],2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row['transfer_out_qnty'],2,'.','');?></td>
									<td width="80" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','issue_popup','<? echo $from_date;?>','<? echo $to_date;?>');"><? echo number_format($total_issue_qnty,2);?></a> </td>
									<td width="80" style="word-break: break-all;" align="right" title="<?="total_issue*Cons_rate=".$total_issue_qnty."*".$row['cons_rate']?>"><? echo number_format($total_issue_value,2,'.','');?></td>
									<td width="100" style="word-break: break-all;" align="right"><?  echo number_format($current_stock_qnty,2,'.','');?></td>
									<td width="80" style="word-break: break-all;" align="right" title="<? echo "Stock Value Usd=".$row['current_stock_value_usd'].", Current Stock=".$row['current_stock'] ?>"><? echo number_format($rate,2,'.','');?></td>
									<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_usd, 2,'.',''); else echo "0.00"; ?></td>
									<td width="110" style="word-break: break-all;" align="right"><? if($current_stock_qnty!=0) echo number_format($total_amount_bdt,2,'.',''); else echo "0.00"; ?></td>
									<td width="100" style="word-break: break-all; background-color: <? echo $remarks_cond;?>;" align="right" title="<?= "tot allocate=".$allocate_data[$row['job_no']][$row['prod_id']]." allocate issue=".$row['lot_issue']; ?>"> <a href='#report_details' onClick="openmypage_issue('<? echo $row[2]; ?>','<? echo $row[6]; ?>','<? echo $row['prod_id'];?>','<? echo $row['job_no'];?>','alocate_popup');"><? echo number_format($allocate_qnty,2,'.','');?></a> </td>
									<td width="100" style="word-break: break-all;" align="right"> <? echo number_format($allocatedYarnBalance,4,'.','');?></td>
									<td width="100"style="word-break: break-all;" align="right" title="<?= "current stock-allocate qnty balance (Note: allocate qnty balance=allocate qnty-Lot ratio wise issue)"; ?>"> <? echo number_format($cu_available_qnty,4,'.','');
										?>
									</td>
									<td><? echo $store_name_arr[$row['store_id']];?></td>
								</tr>
								<?
								$i++;$j++;
								$total_opening+=$openint_qnty;
								$total_opening_value+=$opening_amount_usd;
								$gt_purchage+=$row['purchase'];
								$gt_issue_return+=$row['issue_return'];
								$gt_trans_in+=$row['transfer_in_qnty'];
								$gt_loan_rcvd+=$row['loan_received'];
								$gt_twisting_rcvd+=$row['twisting_received'];
								$gt_reconning_recvd+=$row['re_conning_received'];
								$gt_recvd=$total_received_qnty;
								$gt_rec_value+=$total_receive_value;
								
								$gt_knitting+=$row['knitting'];
								$gt_reconning_issue+=$row['re_conning_issue'];
								$gt_twisting_issue+=$row['twisting_issue'];
								$gt_sample+=$row['sample_issue'];
								$gt_linking+=$row['linking_issue'];
								$gt_loan_issue+=$row['loan_issue'];
								$gt_others+=$row['others'];
								$gt_recv_return+=$row['rcv_return'];
								$gt_trans_out+=$row['transfer_out_qnty'];								
								$gt_issue= $total_issue_qnty;
								$gt_issue_value += $total_issue_value;
								
								
								
								$gt_current_stock+=$current_stock_qnty;
								$gt_usd_amount=$total_amount_usd;
								$gt_bdt_amount+=$total_amount_bdt;
								
								$gt_allocated+=$allocate_qnty;
								$gt_allocatedYarnBalance+=$allocatedYarnBalance;
								$gt_available+=$cu_available_qnty;
							}	
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr bgcolor="#ecfa8b">
						<td align="right" colspan="10" style="text-align:right;">Grand Total: </td>
						<td align="right" id="value_total_opening"> <? echo  number_format($total_opening,2,'.','');?></td>
						<td align="right" id="value_total_opening_value"> <? echo  number_format($total_opening_value,2,'.','');?></td>
						<td align="right" id="value_gt_purchage"> <? echo  number_format($gt_purchage,2,'.','');?></td>
						<td align="right" id="value_gt_issue_return"><? echo  number_format($gt_issue_return,2,'.','');?></td>
						<td align="right" id="value_gt_trans_in"><? echo  number_format($gt_trans_in,2,'.','');?></td>
						<td align="right" id="value_gt_loan_rcvd"><? echo  number_format($gt_loan_rcvd,2,'.','');?></td>
						<td align="right" id="value_gt_twisting_rcvd"><? echo  number_format($gt_twisting_rcvd,2,'.','');?></td>
						<td align="right" id="value_gt_reconning_recvd"><? echo  number_format($gt_reconning_recvd,2,'.','');?></td>
						<td align="right" id="value_gt_recvd"><? echo  number_format($gt_recvd,2,'.','');?></td>
						<td align="right" id="value_gt_rec_value"><? echo  number_format($gt_rec_value,2,'.','');?></td>
						<td align="right" id="value_gt_knitting"><? echo  number_format($gt_knitting,2,'.','');?></td>
						<td align="right" id="value_gt_reconning_issue"><? echo  number_format($gt_reconning_issue,2,'.','');?></td>
						<td align="right" id="value_gt_twisting_issue"><? echo  number_format($gt_twisting_issue,2,'.','');?></td>
						<td align="right" id="value_gt_sample"><? echo  number_format($gt_sample,2,'.','');?></td>
                        <td align="right" id="value_total_opening"><? echo  number_format($gt_linking,2,'.','');?></td>
						<td align="right" id="value_gt_linking"><? echo  number_format($gt_loan_issue,2,'.','');?></td>
						<td align="right" id="value_gt_others"><? echo  number_format($gt_others,2,'.','');?></td>
						<td align="right" id="value_gt_recv_return"><? echo  number_format($gt_recv_return,2,'.','');?></td>
						<td align="right" id="value_gt_trans_out"><? echo  number_format($gt_trans_out,2,'.','');?></td>
						
						<td align="right" id="value_gt_issue"><? echo  number_format($gt_issue,2,'.','');?></td>
						<td align="right" id="value_gt_issue_value"><? echo  number_format($gt_issue_value,2,'.','');?></td>
						<td align="right" id="value_gt_current_stock"><? echo  number_format($gt_current_stock,2,'.','');?></td>
						<td align="right" id=""></td>
						<td align="right" id="value_gt_usd_amount"><? echo  number_format($gt_usd_amount,2,'.','');?></td>
						<td align="right" id="value_gt_bdt_amount"><? echo  number_format($gt_bdt_amount,2,'.','');?></td>
						<td align="right" id="value_gt_allocated"><? echo  number_format($gt_allocated,2,'.','');?></td>
						<td align="right" id="value_gt_allocatedYarnBalance"><? echo  number_format($gt_allocatedYarnBalance,2,'.','');?></td>
						<td align="right" id="value_gt_available"><? echo  number_format($gt_available,2,'.','');?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	 </div>
		<?
			$sql_without_wo_order="
			select b.id,b.company_id, b.supplier_id, b.prod_id, b.item_category, b.store_id,b.job_no, b.buyer_id, b.style_ref_no,b.entry_form, b.receive_basis as basis, b.cons_quantity, b.cons_amount, b.cons_rate, b.order_rate, 
			b.order_qnty, b.order_amount, b.transaction_type as trans_type, a.id as product_id, a.lot, a.color, a.current_stock, a.yarn_count_id, a.avg_rate_per_unit, a.allocated_qnty, a.available_qnty, a.yarn_comp_type1st, d.wo_number, d.booking_type, 1 as type
			from product_details_master a, inv_transaction b, com_pi_item_details c, wo_non_order_info_mst d
			where a.id=b.prod_id and b.pi_wo_batch_no=c.pi_id and c.work_order_id=d.id and b.transaction_type =1 and a.company_id  = $cbo_company_name and a.item_category_id=1 and b.item_category=1 and d.wo_basis_id=3 and d.entry_form=284 and b.prod_id in($all_prod_ids) $store_name_cond $buyer_cond $sample_yarn_comp_cond
			union all
			select b.id,b.company_id, b.supplier_id, b.prod_id, b.item_category, b.store_id,b.job_no, b.buyer_id, b.style_ref_no,b.entry_form, b.receive_basis as basis, b.cons_quantity, b.cons_amount, b.cons_rate, b.order_rate, 
			b.order_qnty, b.order_amount, b.transaction_type as trans_type, a.id as product_id, a.lot, a.color, a.current_stock, a.yarn_count_id, a.avg_rate_per_unit, a.allocated_qnty, a.available_qnty, a.yarn_comp_type1st, d.wo_number, d.booking_type, 2 as type
			from product_details_master a, inv_transaction b, inv_issue_master c, wo_non_order_info_mst d
			where a.id=b.prod_id and b.mst_id=c.id and c.buyer_job_no=d.wo_number and b.pi_wo_batch_no=d.id and b.transaction_type =2 and a.company_id  = $cbo_company_name and a.item_category_id=1 and b.item_category=1 and d.wo_basis_id=3 and d.entry_form=284 and c.issue_basis=10 and c.issue_purpose=8  and b.prod_id in($all_prod_ids)  $store_name_cond $buyer_cond $sample_yarn_comp_cond";
			//echo $sql_without_wo_order;
	
			$non_order_data_result=sql_select($sql_without_wo_order);
			$sample_data_array=array();
			foreach ($non_order_data_result as $row) {
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['wo_number'] = $row[csf('wo_number')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['booking_type'] = $row[csf('booking_type')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['company']=$row[csf('company_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
				if ($row[csf('trans_type')]==1) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['purchase']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_received']+=$row[csf('twisting_received')];
					$rate = ($row[csf('cons_amount')]*1)/($row[csf('cons_quantity')]*1)/($row[csf('exchange_rate')]*1);
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][1].=$row[csf('rcv_iss_trans_id')].',';	
					//$order_amount = ($row[csf('cons_quantity')]*1)*$rate;
				} 		
				else if ($row[csf('trans_type')]==4) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['issue_return']+=$row[csf('cons_quantity')];		
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_issue_return']+=$row[csf('cons_quantity')];		
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][4].=$row[csf('rcv_iss_trans_id')].',';		
				}
				else if ($row[csf('trans_type')]==5) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['transfer_in_qnty']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][5].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==2) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['knitting']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_issue']+=$row[csf('twisting_issue')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][2].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==3) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['rcv_return']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['twisting_rcv_return']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][3].=$row[csf('rcv_iss_trans_id')].',';
				}
				else if ($row[csf('trans_type')]==6) {
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['transfer_out_qnty']+=$row[csf('cons_quantity')];
					$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]][6].=$row[csf('rcv_iss_trans_id')].',';
				}
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['loan_rcvd']+=$row[csf('loan_rcvd_issue_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['re_conning_rcvd']+=$row[csf('re_conning_rcvd_issue_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['re_conning_issue']+=$row[csf('re_conning_rcvd_issue_qnty')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['mending']+=$row[csf('mending')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['linking']+=$row[csf('linking')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['loan_issue']+=$row[csf('loan_issue')];
				
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['current_stock']=$row[csf('current_stock')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['cons_rate']=$row[csf('cons_rate')];
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_rate']=$rate;
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['order_amount']= ($row[csf('current_stock')]*1)*($rate*1);
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['cons_amount']=$row[csf('cons_amount')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['allocated_qnty']=$row[csf('allocated_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['available_qnty']=$row[csf('available_qnty')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['item_category']=$row[csf('item_category')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['entry_form']=$row[csf('entry_form')];
				$sample_data_array[$row[csf('wo_number')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
				$company=$row[csf('company_id')];
				
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['sample_issue']+=$row[csf('sample_issue')];
				//$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['exchange_rate']=$row[csf('exchange_rate')];
	
				foreach ($sample_data_array as $job_no => $prod_data) {
					$row_span = 0;
					foreach ($prod_data as $prod_id => $value) {
						$row_span++;
					}
					$job_wise_span[$job_no]=$row_span;
				}
	
				
			}
			
	
		?>
		<h1 class="table_caption" style="margin-top: 15px;">Sample Against Yarn Receive : <? echo $company_short_name_array[$company];?></h1>
		<table width="3000" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
					<th width="80" align="center" rowspan="2">Company</th>
					<th width="100" align="center" rowspan="2">Buyer</th>
					<th width="110" align="center" rowspan="2">Style No</th>
					<th width="100" align="center" rowspan="2">WO No</th>
					<th width="80" align="center" rowspan="2">Booking Type</th>
					<th width="100" align="center" rowspan="2">Yarn Supplier</th>
					<th width="80" align="center" rowspan="2">Yarn Count</th>
					<th width="130" align="center" rowspan="2">Yarn Composition</th>
					<th width="110" align="center" rowspan="2">Yarn Color</th>
					<th width="80" align="center" rowspan="2">Lot</th> 
	
					<th width="" align="center" colspan="7">Receive Details (Lbs)</th> 
	
					<th width="" align="center" colspan="10">Issue Details (Lbs)</th>
	
					<th width="100" align="center" rowspan="2">Current Stock (Lbs)</th>
					<th width="80" align="center" rowspan="2">Avg Rate/Lbs</th>
					<th width="110" align="center" rowspan="2">Total Amount (USD)</th>
					<th width="110" align="center" rowspan="2">Total Amount (BDT)</th>
					<th width="100" align="center" rowspan="2">Allocated</th>
					<th width="100" align="center" rowspan="2">Allocated Yarn Balance</th>
					<th width="100" align="center" rowspan="2">Available</th>
					<th width="100" align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="80" >Purchase </th>
					<th width="80" >Issue Return</th>
					<th width="80" >Trans. In</th>
					<th width="80" >Loan Rcvd</th>
					<th width="80" >Twisting Rcvd</th>
					<th width="80" >Re-conning Rcvd</th>
					<th width="80" >Total Rcvd</th>
	
					<th width="80">Re-conning Issue</th>
					<th width="80">Twisting Issue</th>
					<th width="80">Knitting</th>
					<th width="80">Sample</th>
					<th width="80">Mending</th>
					<th width="80">Linking</th>
					<th width="80">Receive Return</th>
					<th width="80">Loan Issue</th>
					<th width="80">Trans. Out</th>
					<th width="80">Total Issue</th>
				</tr>
			</thead>
			<tbody id="scroll_body" class="rpt_table">
				<? 
				$i=1;
				$j=1;
				$bgcolor="#EEEEEE";
				$tot_purchage="";
				// echo "<pre>";
				// var_dump($yarn_data_array);
				foreach ($sample_data_array as $job_key=>$prod_data ) 
				{
					$kk=0;
					foreach ($prod_data as $prod_id => $row) 
					{
						$total_received_qnty +=(($row['purchase']*1)+($row['issue_return']*1)+($row['transfer_in_qnty']*1)+($row['loan_rcvd']*1)+($row['re_conning_rcvd']*1));
						//$cons_avg_rate=$row['avg_rate_per_unit']*1;
						//$total_received_amt =$row['purchase_amt'];
						$total_issue_qnty +=(($row['knitting']*1)+($row['re_conning_issue']*1)+($row['sample_issue']*1)+($row['mending']*1)+($row['linking']*1)+($row['rcv_return']*1)+($row['loan_issue']*1)+($row['transfer_out_qnty']*1));
						//$total_issue_amt +=$row['knitting_amt'];
						$current_stock_qnty =($total_received_qnty-$total_issue_qnty);
						$total_amount_usd = ($row['current_stock']*1)*($rate*1);
						$available_qnty = (($current_stock_qnty)-($row['allocated_qnty']*1));
						//$available_qnty = ($row['available_qnty']*1)-($row['transfer_out_qnty']*1);
						
						//for allocated yarn balance
						$allocatedYarnBlnc = $row['allocated_qnty'] - $total_issue_qnty;
					
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<?
							if($kk==0)
							{
								$rowspan=$job_wise_span[$job_key];
							 
							?>
							<td rowspan="<? echo $rowspan;?>"><? echo $j;?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $companyArr[$row['company']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $buyerArr[$row['buyer_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $row['style_ref_no'];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $row['job_no'];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $short_booking_type[$row['booking_type']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $supplierArr[$row['supplier_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
							<td rowspan="<? echo $rowspan;?>"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
							<?
							$j++;
							$kk++;
							}
							/*,'<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>'*/
								
							?>
							<td><? echo $color_name_arr[$row['color']];?></td>
							<td title="<? echo "Prod_ID==".$row['prod_id']; ?>"><? echo $row['lot'];?> </td>
	
							<td align="right" ><? echo $row['purchase'];?></td>
							<td align="right"><? echo $row['issue_return'];?></td>
							<td align="right"><? echo $row['transfer_in_qnty'];?></td>
							<td align="right"><? echo $row['loan_rcvd'];?> </td>
							<td align="right"><? echo $row['twisting_received'];?> </td>
							<td align="right"><? echo $row['re_conning_rcvd'];?></td>
							<td align="right"> <a href='#report_details' onClick="openmypage('<? echo $row[1]; ?>','<? echo $row['prod_id'];?>','received_popup','','');"><? echo number_format($total_received_qnty,2);?></a> </td>
	
							<td align="right"><? echo $row['re_conning_issue'];?></td>
							<td align="right"><? echo $row['twisting_issue'];?></td>
							<td align="right"><? echo $row['knitting'];?></td>
							<td align="right"><? echo $row['sample_issue'];?></td>
							<td align="right"><? echo $row['mending'];?></td>
							<td align="right"><? echo $row['linking	'];?></td>
							<td align="right"><? echo $row['rcv_return'];?></td>
							<td align="right"><? echo $row['loan_issue'];?></td>
							<td align="right"><? echo $row['transfer_out_qnty'];?></td>
							<td align="right"><? echo number_format($total_issue_qnty,2);?></td>
	
							<td align="right"><? echo number_format($current_stock_qnty,2);?></td>
							<td align="right"><? echo $rate;?></td>
							<td align="right"><? echo number_format($total_amount_usd, 2);?></td>
							<td align="right"><? echo number_format($row['cons_amount'],2);?></td>
							<td align="right"><? echo $row['allocated_qnty'];?></td>
							<td align="right"><? echo $allocatedYarnBlnc;?></td>
							<td align="right"><? echo $available_qnty;?></td>
							<td><? echo $store_name_arr[$row['store_id']];?></td>
						</tr>
						<?
						$total_received_qnty=$total_received_amt=$total_issue_qnty=$total_issue_amt=$total_amount=0;	
						$i++;
						$tot_purchage+=$row['purchase'];
						$total_issue_return+=$row['issue_return'];
						$total_trans_in+=$row['transfer_in_qnty'];
						$total_loan_rcvd+=$row['loan_rcvd'];
						$total_twisting_rcvd+=$row['twisting_received'];
						$total_reconning_recvd+=$row['re_conning_rcvd'];
						$total_recvd=($row['purchase']*1) + ($row['issue_return']*1) + ($row['transfer_in_qnty']*1) + ($row['loan_rcvd']*1) + ($row['re_conning_rcvd']*1);
						$grand_total_received +=$total_recvd;
						$total_reconning_issue+=$row['re_conning_issue'];
						$total_twisting_issue+=$row['twisting_issue'];
						$total_knitting+=$row['knitting'];
						$total_sample+=$row['sample_issue'];
						$total_mending+=$row['mending'];
						$total_linking+=$row['linking'];
						$total_recv_return+=$row['rcv_return'];
						$total_loan_issue+=$row['loan_issue'];
						$total_trans_out+=$row['transfer_out_qnty'];
						$total_issue= ($row['knitting']*1) + ($row['sample_issue']*1) +($row['mending']*1) +($row['linking']*1) +($row['rcv_return']*1) +($row['transfer_out_qnty']*1) + ($row['re_conning_issue']*1) + ($row['loan_issue']*1);
						$grand_total_issue += $total_issue;
						$total_current_stock+=$row['current_stock'];
						$total_usd_amount=$total_current_stock*$rate;
						$total_bdt_amount+=$row['cons_amount'];
						$total_allocated+=$row['allocated_qnty'];
						$total_available+=$row['available_qnty'];
						$total_allocatedYarnBlnc+=$allocatedYarnBlnc;
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr bgcolor="#ecfa8b">
					<td align="right" colspan="11" style="text-align:right;"><strong>Grand Total:</strong> </td>
					<td align="right" id="total_puchase"><strong> <? echo  number_format($tot_purchage,2);?></strong></td>
					<td align="right" id="total_issue_return)"><strong><? echo  number_format($total_issue_return,2);?></strong></td>
					<td align="right" id="total_trans_in"><strong><? echo  number_format($total_trans_in,2);?></strong></td>
					<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_loan_rcvd,2);?></strong></td>
					<td align="right" id="total_loan_rcvd"><strong><? echo  number_format($total_twisting_rcvd,2);?></strong></td>
					<td align="right" id="total_reconning_recvd"><strong><? echo  number_format($total_reconning_recvd,2);?></strong></td>
					<td align="right" id="total_recvd"><strong><? echo  number_format($grand_total_received,2);?></strong></td>
					<td align="right" id="total_reconning_issue"><strong><? echo  number_format($total_reconning_issue,2);?></strong></td>
					<td align="right" id="total_knitting"><strong><? echo  number_format($total_twisting_issue,2);?></strong></td>
					<td align="right" id="total_knitting"><strong><? echo  number_format($total_knitting,2);?></strong></td>
					<td align="right" id="total_sample"><strong><? echo  number_format($total_sample,2);?></strong></td>
					<td align="right" id="total_mending"><strong><? echo  number_format($total_mending,2);?></strong></td>
					<td align="right" id="total_linking"><strong><? echo  number_format($total_linking,2);?></strong></td>
					<td align="right" id="total_recv_return"><strong><? echo  number_format($total_recv_return,2);?></strong></td>
					<td align="right" id="total_loan_issue"><strong><? echo  number_format($total_loan_issue,2);?></strong></td>
					<td align="right" id="total_trans_out"><strong><? echo  number_format($total_trans_out,2);?></strong></td>
					<td align="right" id="total_issue"><strong><? echo  number_format($grand_total_issue,2);?></strong></td>
					<td align="right" id="total_current_stock"><strong><? echo  number_format($total_current_stock,2);?></strong></td>
					<td align="right" id=""></td>
					<td align="right" id="total_usd_amount"><strong><? echo  number_format($total_usd_amount,2);?></strong></td>
					<td align="right" id="total_bdt_amount"><strong><? echo  number_format($total_bdt_amount,2);?></strong></td>
					<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocated,2);?></strong></td>
					<td align="right" id="total_allocated"><strong><? echo  number_format($total_allocatedYarnBlnc,2);?></strong></td>
					<td align="right" id="total_available"><strong><? echo  number_format($total_available,2);?></strong></td>
					<td ></td>
				</tr>
			</tfoot>
		</table>
		<?
	}
	else
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$lot_ratio_arr = return_library_array("select id, cutting_no from ppl_cut_lay_mst", 'id', 'cutting_no');
		$samp_wo_arr = return_library_array("select id, wo_number from wo_non_order_info_mst where entry_form=284 and wo_basis_id=3", 'id', 'wo_number');
		
	
		if ($db_type == 0) {
			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
			$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			$from_date = change_date_format($from_date, '', '', 1);
			$to_date = change_date_format($to_date, '', '', 1);
		} else {
			$from_date = "";
			$to_date = "";
		}
		
		
		/*if($txt_job_no!="") 
		{
			// $master_job_no = return_field_value("job_no", "wo_po_details_master", "job_no_prefix_num=$txt_job_no and company_name=$cbo_company_name and status_active=1 and to_char(insert_date,'YYYY')='$cbo_year_selection'");
			// $sql_cond=" and a.job_no ='$master_job_no'"; 

			$sql_result=sql_select("select job_no from wo_po_details_master where job_no_prefix_num in ($txt_job_no) and company_name=$cbo_company_name and status_active=1 and to_char(insert_date,'YYYY')='$cbo_year_selection'");
			//print_r($sql_result);die;
			$job_no_cond=" and a.job_no in (";
			$sl=0;
			foreach ($sql_result as $row) {
				if($sl>0) $job_no_cond.=",";
				$job_no_cond.="'".$row[csf('job_no')]."'";
				$sl++;
			}
			$job_no_cond.=")";


		}else {
			$sql_cond="";
		}*/
		//echo $cbo_buyer;die;
		
		if($txt_job_no!="")
		{
			$txt_job_no_array=explode(",",$txt_job_no);
			$job_no_datas="";
			foreach($txt_job_no_array as $job_no)
			{
				$job_no_datas.="'".$job_no."',";
			}
			$job_no_datas=chop($job_no_datas,",");
		}
		//echo $job_no_datas;die;
		$sql_cond="";$job_data_cond="";
		if($cbo_buyer>0)
		{
			$sql_cond.=" and a.buyer_id=$cbo_buyer";
			$job_data_cond.=" and buyer_name=$cbo_buyer";
		}
		if($job_no_datas!="")
		{
			$sql_cond.=" and a.job_no in($job_no_datas)";
			$job_data_cond.=" and job_no in($job_no_datas)";
		}

		//if($txt_job_no !="") $sql_cond.=" and a.job_no like '%$txt_job_no'"; 
		if($from_date !="" && $to_date !="") $sql_cond.= " and a.transaction_date between '$from_date' and '$to_date' ";
		//if($txt_style_no !="") $sql_cond.=" and a.style_ref_no like '%$txt_style_no%'";
		if($cbo_store_name !=0) $sql_cond.=" and a.store_id in($cbo_store_name)"; 
		//if($cbo_buyer !=0) $sql_cond.=" and a.buyer_id in($cbo_buyer)";
		//echo "select id, job_no, buyer_name, style_ref_no, gmts_item_id, gauge from wo_po_details_master where company_name=$cbo_company_name $job_data_cond";die;
		$job_sql=sql_select("select id, job_no, buyer_name, style_ref_no, gmts_item_id, gauge from wo_po_details_master where company_name=$cbo_company_name $job_data_cond");
		$job_data=array();
		foreach($job_sql as $row)
		{
			$job_library[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
			$job_library[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$job_library[$row[csf("job_no")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
			$job_library[$row[csf("job_no")]]["gauge"]=$row[csf("gauge")];
			$job_id_no_arr[$row[csf("id")]]=$row[csf("job_no")];
		}
		//echo "<pre>";print_r($job_data['SSL-19-00096']);die;
		$sql = "select a.id as transaction_id, a.transaction_date, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.store_id, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis, c.receive_purpose as rcv_iss_purpose, a.pi_wo_batch_no, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.job_no, b.id as product_id, b.color, b.lot, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate,c.id as rcv_iss_trans_id, c.recv_number as rcv_iss_num, a.cons_quantity
		from inv_transaction a, product_details_master b, inv_receive_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1) and a.entry_form in(248) $sql_cond
		union all
		select a.id as transaction_id, a.transaction_date, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.store_id, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis, c.issue_purpose as rcv_iss_purpose, a.pi_wo_batch_no, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.job_no, b.id as product_id, b.color, b.lot, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.issue_number as rcv_iss_num, a.cons_quantity
		from inv_transaction a, product_details_master b, inv_issue_master c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2) and a.entry_form in(277) and c.issue_purpose<>8 and c.issue_basis<>10 and a.receive_basis<>10 $sql_cond
		union all
		select a.id as transaction_id, a.transaction_date, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.store_id, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis, 0 as rcv_iss_purpose, a.pi_wo_batch_no, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.job_no, b.id as product_id, b.color, b.lot, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.transfer_system_id as rcv_iss_num, a.cons_quantity
		from inv_transaction a, product_details_master b, inv_item_transfer_mst c
		where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5,6) and a.entry_form in(249) $sql_cond
		order by job_no, color, product_id, transaction_id";
		//echo $sql;die;
		$result = sql_select($sql);
		//echo "<pre>";print_r($result);die;
		foreach ($result  as $row) 
		{
			if ($row[csf('trans_type')]==1)
			{
				if($rcv_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=="")
				{
					$rcv_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=$row[csf('prod_id')];
					$i=1;
				}
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['transaction_date']=$row[csf('transaction_date')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['rcv_num']=$row[csf('rcv_iss_num')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['supplier_id']=$row[csf('supplier_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_count_id']=$row[csf('yarn_count_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['color']=$row[csf('color')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['lot']=$row[csf('lot')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['store_id']=$row[csf('store_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['rcv_qnty']=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['rcv_type']=$row[csf('trans_type')];
				$i++;
			}
			else if ($row[csf('trans_type')]==2)
			{
				if($iss_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=="")
				{
					$iss_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=$row[csf('prod_id')];
					$i=1;
				}
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['issue_transaction_date']=$row[csf('transaction_date')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['issue_basis']=$row[csf('receive_basis')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['issue_purpose']=$row[csf('rcv_iss_purpose')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['iss_num']=$row[csf('rcv_iss_num')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['pi_wo_batch_no']=$row[csf('pi_wo_batch_no')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['store_id']=$row[csf('store_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['isuse_qnty']=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['iss_type']=$row[csf('trans_type')];
				$i++;
			}
			else if ($row[csf('trans_type')]==5)
			{
				if($in_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=="")
				{
					$in_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=$row[csf('prod_id')];
					$i=1;
				}
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['transaction_date']=$row[csf('transaction_date')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['supplier_id']=$row[csf('supplier_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_count_id']=$row[csf('yarn_count_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['color']=$row[csf('color')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['lot']=$row[csf('lot')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['store_id']=$row[csf('store_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_in_qnty']=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_in_type']=$row[csf('trans_type')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_num']=$row[csf('rcv_iss_num')];
				$i++;
			}
			else if ($row[csf('trans_type')]==6)
			{
				if($out_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=="")
				{
					$out_job_item_check[$row[csf('job_no')]][$row[csf('prod_id')]]=$row[csf('prod_id')];
					$i=1;
				}
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['supplier_id']=$row[csf('supplier_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_count_id']=$row[csf('yarn_count_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['color']=$row[csf('color')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['lot']=$row[csf('lot')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['store_id']=$row[csf('store_id')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_out_qnty']=$row[csf('cons_quantity')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_out_type']=$row[csf('trans_type')];
				$yarn_data_array[$row[csf('job_no')]][$row[csf('color')]][$row[csf('prod_id')]][$i]['trans_num']=$row[csf('rcv_iss_num')];
				$i++;
			}
		}
		//echo count($yarn_data_array)."<pre>";print_r($yarn_data_array);die;
		ob_start();
		?>
		<table width="1800" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="30" align="center" rowspan="2">SL</th>
                    <th width="70" align="center" rowspan="2">Trns. Date</th>
					<th width="" align="center" colspan="8">Receive Details (Lbs)</th> 
					<th width="" align="center" colspan="6">Issue Details (Lbs)</th>
					<th width="100" align="center" rowspan="2">Balance</th>
					<th align="center" rowspan="2">Store Name</th>
				</tr>
				<tr>
					<th width="120">Transaction ID </th> <!--830-->
					<th width="120">Yarn Supp.</th>
					<th width="80">Y. Count</th>
					<th width="150">Yarn Composition</th>
					<th width="120">Color & Color No</th>
					<th width="80">Lot No</th>
					<th width="80">Trans. IN</th>
                    <th width="80">Recvd. Qty.</th>
	
					<th width="120">Transaction ID</th>
					<th width="100">Issue Basis</th> <!--600-->
					<th width="120">Issue Purpose</th>
					<th width="120">Ratio/Job/WO</th>
					<th width="80">Trans. Out</th>
					<th width="80">Issue Qty.</th>
				</tr>
			</thead>
			<tbody id="scroll_body" class="rpt_table">
				<? 
				$i=1;
				foreach($yarn_data_array as $job_key=>$job_data ) 
				{
					?>
                    <tr>
                    	<td colspan="18" bgcolor="#FFFFCC" title="<? echo $job_library[$job_key]["buyer_name"].$job_key; ?>">
						<? echo "Buyer : ".$buyerArr[$job_library[$job_key]["buyer_name"]]."&nbsp; &nbsp; &nbsp;Style No : ".$job_library[$job_key]["style_ref_no"]."&nbsp; &nbsp; &nbsp;Job No : ".$job_key."&nbsp; &nbsp; &nbsp;GMT Item : ".$garments_item[$job_library[$job_key]["gmts_item_id"]]."&nbsp; &nbsp; &nbsp;Gauge : ".$garments_item[$job_library[$job_key]["gauge"]]; ?>
                        <td>
                    </tr>
                    <?
					foreach($job_data as $color_id=>$color_data) 
					{
						foreach($color_data as $prod_id=>$prod_data) 
						{
							foreach($prod_data as $r_id=>$row)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$trans_date="";
								$trans_date=$row['transaction_date'];
								if($trans_date=="") $trans_date=$row['issue_transaction_date'];
								
								if($row['issue_basis']==5) $job_lot_wo_no=$job_id_no_arr[$row['pi_wo_batch_no']];
								else if($row['issue_basis']==6) $job_lot_wo_no=$lot_ratio_arr[$row['pi_wo_batch_no']];
								else $job_lot_wo_no=$samp_wo_arr[$row['pi_wo_batch_no']];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td align="center"><? echo $i;?></td>
									<td align="center" title="<? echo $trans_date; ?>"><p><? echo change_date_format($trans_date);?>&nbsp;</p></td>
									<td><p>
									<? 
										if ($row['trans_in_type'] == 5) {
											echo $row['trans_num'];
										} else {
											echo $row['rcv_num'];
										}
										
									?>&nbsp;</p></td>
									<td><p><? echo $supplierArr[$row['supplier_id']];?>&nbsp;</p></td>
									<td><p><? echo $yarn_count_arr[$row['yarn_count_id']];?>&nbsp;</p></td>
									<td><p><? echo $composition[$row['yarn_comp_type1st']];?>&nbsp;</p></td>
									<td><p><? echo $color_name_arr[$row['color']];?>&nbsp;</p></td>
									<td><p><?
									if($row['trans_in_type'] == 5) //$row['trans_out_type'] == 6 ||
									{
										if($row['lot'] !=""){
											echo $row['lot']." (T)";
										}else{
											echo $row['lot'];
										}
										
									}else{
										echo $row['lot'];
									}
										?>&nbsp;</p></td>
									<td align="right">
									<? echo number_format($row['trans_in_qnty'],2);	?>
									</td>
									<td align="right"><? echo number_format($row['rcv_qnty'],2);?></td>
			
									<td><p>
									<? 
										if ($row['trans_out_type'] == 6) {
											echo $row['trans_num'];
										} else {
											echo $row['iss_num'];
										}
									?>&nbsp;
									</p></td>
									<td title="<? echo $r_id; ?>"><p><? echo $issue_basis[$row['issue_basis']];?>&nbsp;</p></td>
									<td><p><? echo $yarn_issue_purpose[$row['issue_purpose']];?>&nbsp;</p></td>
									<td title="<? echo $row['pi_wo_batch_no'];?>"><p><? echo $job_lot_wo_no; ?>&nbsp;</p></td>
									<td align="right"><? echo number_format($row['trans_out_qnty'],2);?></td>
									<td align="right"><? echo number_format($row['isuse_qnty'],2);?></td>
									<td align="right">&nbsp;</td>
									<td><? echo $store_name_arr[$row['store_id']];?></td>
								</tr>
								<?
								$i++;
								$item_trans_in_qnty+=$row['trans_in_qnty'];
								$item_rcv_qnty+=$row['rcv_qnty'];
								$item_trans_out_qnty+=$row['trans_out_qnty'];
								$item_isuse_qnty+=$row['isuse_qnty'];
								
								$color_trans_in_qnty+=$row['trans_in_qnty'];
								$color_rcv_qnty+=$row['rcv_qnty'];
								$color_trans_out_qnty+=$row['trans_out_qnty'];
								$color_isuse_qnty+=$row['isuse_qnty'];
								
								$gt_trans_in_qnty+=$row['trans_in_qnty'];
								$gt_rcv_qnty+=$row['rcv_qnty'];
								$gt_trans_out_qnty+=$row['trans_out_qnty'];
								$gt_isuse_qnty+=$row['isuse_qnty'];
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td colspan="8" align="right">Item Total:</td>
                                <td align="right"><? echo number_format($item_trans_in_qnty,2);?></td>
                                <td align="right"><? echo number_format($item_rcv_qnty,2);?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($item_trans_out_qnty,2);?></td>
                                <td align="right"><? echo number_format($item_isuse_qnty,2);?></td>
                                <td align="right"><? $item_balance=($item_trans_in_qnty+$item_rcv_qnty)-($item_trans_out_qnty+$item_isuse_qnty); echo number_format($item_balance,2);?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
							$item_trans_in_qnty=$item_rcv_qnty=$item_trans_out_qnty=$item_isuse_qnty=$item_balance=0;
						}
						?>
                        <tr bgcolor="#999999">
                            <td colspan="8" align="right">Color Total:</td>
                            <td align="right"><? echo number_format($color_trans_in_qnty,2);?></td>
                            <td align="right"><? echo number_format($color_rcv_qnty,2);?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><? echo number_format($color_trans_out_qnty,2);?></td>
                            <td align="right"><? echo number_format($color_isuse_qnty,2);?></td>
                            <td align="right"><? $color_balance=($color_trans_in_qnty+$color_rcv_qnty)-($color_trans_out_qnty+$color_isuse_qnty); echo number_format($color_balance,2);?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
						$color_trans_in_qnty=$color_rcv_qnty=$color_trans_out_qnty=$color_isuse_qnty=$color_balance=0;
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="8" align="right">Grand Total:</th>
					<th align="right"><? echo number_format($gt_trans_in_qnty,2);?></th>
                    <th align="right"><? echo number_format($gt_rcv_qnty,2);?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th align="right"><? echo number_format($gt_trans_out_qnty,2);?></th>
					<th align="right"><? echo number_format($gt_isuse_qnty,2);?></th>
                    <th><? $grand_balance=($gt_trans_in_qnty+$gt_rcv_qnty)-($gt_trans_out_qnty+$gt_isuse_qnty); echo number_format($grand_balance,2);?></th>
                    <th>&nbsp;</th>
				</tr>
			</tfoot>
		</table>
		<?
	}
	
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
                //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
            //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}

if($action=="received_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $from_date."=".$to_date;
	?>
    
 	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>   
	<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" >
				<caption>Receive Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Rcvd. Basis</th>
                    <th width="90">PI/WO No</th>
                    <th width="90">MRR No</th>
                    <th width="80">MRR Date</th>
                    <th width="90">Yarn Color</th>
                    <th width="120">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">MRR Qty.</th>
                    <th width="80">Unit Price</th>
                    <th width="90">MRR Value</th>
                    <th>Remarks</th>
				</thead>
			</table>
                <?
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;

					$item_color_con2="";$item_size_con2="";
					if($item_color)
					{
						$item_color_con2=" and d.item_color in($item_color)";
					}
					if($item_size)
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					//echo $rec_id.jahid;die;
					$recive_id="";
					$rec_id_arr=explode("_",chop($rec_id,','));
					$recive_id=chop($rec_id_arr[0],',');
					if(chop($rec_id_arr[1],',')!="")
					{
						if($rec_id!="" ) $recive_id.=",".chop($rec_id_arr[1],','); else $recive_id=chop($rec_id_arr[1],',');
					}
					$transfer_id=chop($rec_id_arr[2],',');
					if($transfer_id=="") $transfer_id=0;
					$prod_id=chop($prod_id,',');
					$date_cond="";
					if($from_date!="" && $to_date!="") $date_cond=" and a.transaction_date between '$from_date' and '$to_date'";
					$sql = "select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount, a.transaction_type as trans_type, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id,c.recv_number, a.remarks, c.receive_date ,a.cons_quantity, a.pi_wo_batch_no, a.cons_uom
					from inv_transaction a, product_details_master b, inv_receive_master c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $companyID and c.id in($recive_id) and a.prod_id in($prod_id) and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1,4) and a.entry_form in(248,382) $date_cond
					union all
					select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.TRANSFER_SYSTEM_ID as recv_number, a.remarks, c.TRANSFER_DATE as receive_date ,a.cons_quantity, a.pi_wo_batch_no, a.cons_uom
					from inv_transaction a, product_details_master b, inv_item_transfer_mst c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $companyID and c.id in($transfer_id) and a.prod_id in($prod_id) and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5) and a.entry_form in(249) $date_cond";
					//echo $sql;
					$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
					//$req_arr = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst where  company_id=$companyID and status_active = 1 and is_deleted=0", 'id', 'ydw_no');
					$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where importer_id=$companyID and status_active = 1 and is_deleted=0 and item_category_id=1", 'id', 'pi_number');
					$wo_arr = return_library_array("select id, wo_number from wo_non_order_info_mst where company_name=$companyID and status_active = 1 and is_deleted=0 and entry_form=234", 'id', 'wo_number');
					//echo $sql;
					$dtlsArray=sql_select($sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						/*if($row[csf('trans_type')]==2) $trans_type="Issue";
						else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
						else $trans_type="Transfer Out";*/
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
		                    <td width="100"><p><? echo $receive_basis_arr[$row[csf('basis')]]; ?></p></td>
		                    <td width="90"><p><? 
		                    if ($row[csf('basis')]==2)
		                    {
		                    	echo $wo_arr[$row[csf('pi_wo_batch_no')]];
		                    }
		                    else if ($row[csf('basis')]==1) 
		                    {
		                      	echo $pi_arr[$row[csf('pi_wo_batch_no')]];
		                    } 
		                    else
		                    {
		                    	echo '';
		                    } 
		                    ?></td>
		                    <td width="90" style="word-wrap: break-word; word-break: break-all"><? echo $row[csf('recv_number')]; ?></td>
		                    <td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></td>
		                    <td width="90" style="word-wrap: break-word; word-break: break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>
		                    <td width="120" style="word-wrap: break-word; word-break: break-all"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></td>
		                    <td width="80"><? echo $row[csf('lot')]; ?></td>
		                    <td width="80" align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></td>
		                    <td width="80" align="right"><p><? echo $row[csf('cons_rate')]; ?></td>
		                    <td width="90" align="right"><p><? echo number_format($row[csf('cons_amount')],2); ?></td>
		                    <td align="center"><p><? echo $row[csf('remarks')]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						$tot_val+=$row[csf('cons_amount')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_val,2); ?></td>
						<td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <!-- <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script> -->
    <?
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
    
 	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>   
	<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" >
				<caption>Issue Details </caption>
                <thead>
                	<!-- Sl	Issue  Basis		Issue Purpose	Issue ID No	Issue ID Date	Yarn Color	Yarn Composition	Y. Lot	Issue Qty.	Remarks -->
                    <th width="30">Sl</th>
                    <th width="100">Issue Basis</th>
                    <th width="120">Lot Ration/Job /WO No</th>
                    <th width="90">Issue Purpose</th>
                    <th width="120">Issue ID No</th>
                    <th width="70">Issue ID Date</th>
                    <th width="90">Yarn Color</th>
                    <th width="120">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">Issue Qty.</th>
                    <th>Remarks</th>
				</thead>
			</table>
                <?
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;

					$item_color_con2="";$item_size_con2="";
					if($item_color)
					{
						$item_color_con2=" and d.item_color in($item_color)";
					}
					if($item_size)
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					//echo $rec_id.'=='.$issue_id.'=='.$transfer_id;die;
					$issue_id_arr=explode("_",chop($issue_id,','));
					$issue_id=chop($issue_id_arr[0],',');
					if(chop($issue_id_arr[1],',')!="")
					{
						if($issue_id!="") $issue_id.=",".chop($issue_id_arr[1],','); else  $issue_id=chop($issue_id_arr[1],',');
					}
					$transfer_id=chop($transfer_id,',');
					if($job_no) $job_no_cond=" and a.job_no='$job_no'"; else $job_no_cond="";
					if($rec_id) $issue_id_cond=" and c.id in($rec_id)"; else $issue_id_cond="";
					if($transfer_id) $tran_id_cond=" and c.id in($transfer_id)"; else $tran_id_cond="";
					
					//and c.id in($rec_id)
					/*select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount, a.transaction_type as trans_type, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id,c.recv_number, a.remarks, c.receive_date ,a.cons_quantity, a.pi_wo_batch_no, a.cons_uom
					from inv_transaction a, product_details_master b, inv_issue_master c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $companyID and c.id in($rec_id) and a.prod_id=$prod_id  and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277)*/  
					$date_cond="";
					if($from_date!="" && $to_date!="") $date_cond=" and a.transaction_date between '$from_date' and '$to_date'";
					$sql = " 
					select a.id as transaction_id, c.issue_number as trans_no , c.issue_date as trans_date, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.store_id, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis, c.issue_purpose , a.pi_wo_batch_no, a.requisition_no , a.booking_no , a.job_no, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.job_no, b.id as product_id, b.color, b.lot, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, a.cons_quantity 
					from inv_transaction a, product_details_master b, inv_issue_master c 
					where a.prod_id = b.id and a.mst_id=c.id and a.item_category = 1 and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277,381) and c.issue_purpose<>8 and c.issue_basis<>10 and a.receive_basis<>10 and a.company_id = $companyID and a.prod_id in($prod_id) $job_no_cond $issue_id_cond $date_cond
					union all 
					select a.id as transaction_id, c.transfer_system_id as trans_no , c.transfer_date as trans_date, a.company_id, b.supplier_id, a.prod_id, a.item_category, a.store_id, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis, 0 as issue_purpose , a.pi_wo_batch_no, a.requisition_no , a.booking_no , a.job_no, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty, a.order_amount, a.transaction_type as trans_type, a.job_no, b.id as product_id, b.color, b.lot, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, 0 as exchange_rate, c.id as rcv_iss_trans_id, a.cons_quantity from inv_transaction a, product_details_master b, inv_item_transfer_mst c where a.prod_id = b.id and a.mst_id=c.id and a.item_category = 1 and a.company_id = $companyID $job_no_cond $tran_id_cond and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5) and a.entry_form in(249) and a.prod_id in($prod_id) $date_cond";

					$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
					//echo $sql;
					$dtlsArray=sql_select($sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						/*if($row[csf('trans_type')]==2) $trans_type="Issue";
						else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
						else $trans_type="Transfer Out";*/
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
		                    <td width="100"><p><? echo $issue_basis[$row[csf('receive_basis')]]; ?></p></td>
		                    <td width="120"><p><? 
		                    //echo $req_arr[$row[csf('pi_wo_batch_no')]];
		                    if ($row[csf('receive_basis')]==6) // lot ratio
		                    {
		                    	echo $row[csf('requisition_no')];
		                    }
		                    else if ($row[csf('receive_basis')]==9 || $row[csf('receive_basis')]==10 ) // service booking + Sample Booking
		                    {
		                      	echo $row[csf('booking_no')];
		                    } 
		                    else if ($row[csf('receive_basis')]==5 ) // job
		                    {
		                      	echo $row[csf('job_no')];
		                    } 
		                    else
		                    {
		                    	echo '';
		                    } 
		                    ?></td>
		                    <td width="90" style="word-wrap: break-word; word-break: break-all"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
		                    <td width="120" style="word-wrap: break-word; word-break: break-all"><? echo $row[csf('trans_no')]; ?></td>
		                    <td width="70" align="center"><p><? echo change_date_format($row[csf('trans_date')]); ?></td>
		                    <td width="90" style="word-wrap: break-word; word-break: break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>
		                    <td width="120" style="word-wrap: break-word; word-break: break-all"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></td>
		                    <td width="80"><? echo $row[csf('lot')]; ?></td>
		                    <td width="80" align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></td>
		                    <td align="center"><p><? echo $row[csf('remarks')]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						//$tot_val+=$row[csf('cons_amount')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        
						<td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <!-- <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script> -->
    <?
	exit();
}

if($action=="alocate_popup")
{
	echo load_html_head_contents("allocate Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
    
 	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>   
	<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" >
				<caption>Yarn Allocated Details</caption>
                <thead>
                	<!-- Sl	Lot Ration No	Lot Ration Date	Yarn Color	Yarn Composition	Y. Lot	 AllocatedQty.	Remarks -->
                    <th width="30">Sl</th>
                    <th width="100">Lot Ration No</th>
                    <th width="100">Lot Ration Date</th>
                    <th width="100">Yarn Color</th>
                    <th width="170">Yarn Composition</th>
                    <th width="100">Y. Lot</th>
                    <th width="90">Allocated Qty.</th>
                    <th>Remarks</th>
				</thead>
			</table>
                <?
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					$item_color_con2="";$item_size_con2="";
					if($item_color)
					{
						$item_color_con2=" and d.item_color in($item_color)";
					}
					if($item_size)
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$rec_id=chop($rec_id,',');
					$prod_id=chop($prod_id,',');
					$transfer_id=chop($transfer_id,',');
					if($job_no) $job_no_cond=" and a.job_no='$job_no'"; else $job_no_cond="";
					if($prod_id!="") $prod_id_cond=" and b.prod_id in($prod_id)"; else $prod_id_cond="";
					/*if($rec_id) $issue_id_cond=" and c.id in($rec_id)"; else $issue_id_cond="";
					if($transfer_id) $tran_id_cond=" and c.id in($transfer_id)"; else $tran_id_cond="";*/

					$dtlsArray=sql_select("SELECT a.cutting_no,a.entry_date,a.job_no,b.color_id,b.produc_name_details,b.lot, b.prod_id, b.alocated_qty as alocated_qty from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b where a.id=b.mst_id and a.entry_form=253 and a.status_active=1 and b.status_active=1 and a.company_id= $companyID $job_no_cond $prod_id_cond");

					$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
					
					//$dtlsArray=sql_select($sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						/*if($row[csf('trans_type')]==2) $trans_type="Issue";
						else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
						else $trans_type="Transfer Out";*/
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
		                    <td width="100" style="word-wrap: break-word; word-break: break-all"><? echo $row[csf('cutting_no')]; ?></td>
		                    <td width="100" align="center"><p><? echo change_date_format($row[csf('entry_date')]); ?></td>
		                    <td width="100" style="word-wrap: break-word; word-break: break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>
		                    <td width="170" style="word-wrap: break-word; word-break: break-all"><? echo $row[csf('produc_name_details')]; ?></td>
		                    <td width="100"><? echo $row[csf('lot')]; ?></td>
		                    <td width="100" align="right"><p><? echo number_format($row[csf('alocated_qty')],2); ?></td>
		                    <td align="center"><p><? echo $row[csf('remarks')]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('alocated_qty')];
						//$tot_val+=$row[csf('cons_amount')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        
						<td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <!-- <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script> -->
    <?
	exit();
}


if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array(); var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_pre_composition_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{
				js_set_value( old[k] )
			}
		}
	}

	function js_set_value( str )
	{

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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

		$('#hidden_composition_id').val(id);
		$('#hidden_composition').val(name);
	}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Yarn Receive Details</legend>
		<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
		<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="2">
						<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
					</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="">Composition Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		$i = 1;

		$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
		$pre_composition_id_arr=explode(",",$pre_composition_id);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";


			if(in_array($row[csf("id")],$pre_composition_id_arr))
			{
				if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="50">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
				</td>
				<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("SELECT image_location from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
	<style>
		.zoom {
			padding: 80px;
			transition: transform .2s; /* Animation */
			margin: 0 auto;
		}

		.zoom:hover {
			transform: scale(1.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
		}
	</style>
    <table>
        <tr>
			<?
				foreach ($data_array as $row)
				{
					?>
					<td><div class="zoom"><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></div></td>
					<?
				}
			?>
        </tr>
    </table>
    <?
	exit();
}
