<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "" );
	exit();
}

if($action=="jobno_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	if($req_type==1)
	{
		$caption="Please Enter Job No";
		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Internal Ref",4=>"Booking No");
	}
	else if ($req_type==2)
	{
		$caption="Please Enter Req. No";
		$search_by_arr=array(1=>"Req. No",2=>"Style Ref");
	}
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
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:580px;">
                <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th width="140">Buyer</th>
                        <th width="130">Search By</th>
                        <th width="140" id="search_by_td_up"><?=$caption; ?></th>
                        <th>
                            <input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                        </th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>                 
                            <td>	
                                <?
                                    
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                ?>
                            </td>     
                            <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $req_type; ?>', 'create_job_no_search_list_view', 'search_div', 'requset_lab_dip_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" /></td>
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$req_type=$data[4];
	if($req_type==1)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$data[1]";
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
		if($search_by==1) $search_field="a.job_no";
		else if($search_by==2) $search_field="a.style_ref_no"; 
		else if($search_by==3) $search_field="b.grouping";
		else if($search_by==4) $search_field="c.booking_no";
		
		else $search_field="job_no";
		//$year="year(insert_date)";
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
		$year_field="to_char(a.insert_date,'YYYY')";
		//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
		$arr=array (0=>$company_arr,1=>$buyer_arr);
		//$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by id DESC";
		$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field  as year from wo_po_details_master a, wo_po_break_down b, wo_po_lapdip_approval_info c where a.id=b.job_id and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name=$company_id and c.app_type=1 and $search_field like '$search_string' $buyer_id_cond group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date order by a.id DESC";
		
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else if($req_type==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and buyer_name=$data[1]";
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
		if($search_by==2) $search_field="style_ref_no"; else $search_field="requisition_number_prefix_num";
		//$year="year(insert_date)";
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
			$year_field="YEAR(insert_date)"; 
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
			$year_field="to_char(insert_date,'YYYY')";
		}
		//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
		$sql= "select id, requisition_number_prefix_num, $year_field as year, company_id, buyer_name, style_ref_no from sample_development_mst where entry_form_id=117 and  status_active=1 and is_deleted=0 and company_id=$company_id and sample_stage_id=2 and $search_field like '$search_string' $buyer_id_cond order by id DESC";
		$arr=array (0=>$company_arr,1=>$buyer_arr);
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Req. No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,requisition_number_prefix_num", "", 1, "company_id,buyer_name,0,0,0", $arr , "company_id,buyer_name,requisition_number_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	exit(); 
} // Job Search end

if ($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_lab_company_name=str_replace("'","",$cbo_lab_company_name);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_req_type=str_replace("'","",$cbo_req_type);
	$hid_job_id=str_replace("'","",$hid_job_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_status=str_replace("'","",$cbo_status);
	
	$companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$dealingMerchecnt_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name");
	
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	
	//echo $sqlSaveData;
	
	if($cbo_req_type==1)
	{
		if($cbo_company!=0) $bookComCond=" and a.company_id='$cbo_company'"; else  $bookComCond="";
		$sam_book_sql=sql_select("select b.po_break_down_id, a.booking_no, a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=4 and b.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookComCond");
		foreach($sam_book_sql as $row)
		{
			$sample_book_arr[$row[csf("po_break_down_id")]]["booking_no"]=$row[csf("booking_no")];
			$sample_book_arr[$row[csf("po_break_down_id")]]["is_approved"]=$row[csf("is_approved")];
		}
		
		$teamLeadr_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
		if($cbo_company!=0) $companyCond=" and a.company_name='$cbo_company'"; else  $companyCond="";
		if($hid_job_id=="") $jobCond=""; else $jobCond=" and a.id='$hid_job_id'";
		
		if($db_type==0)
		{
			if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		}
		else if($db_type==2 || $db_type==1)
		{
			if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".date("j-M-Y",strtotime($date_from))."' and '".date("j-M-Y",strtotime($date_to))."'"; else $shipment_date ="";
		}
		
		$sql="select a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.product_dept, a.company_name, a.style_description, a.team_leader, a.dealing_marchant, b.id as poid, b.po_number, b.pub_shipment_date, b.inserted_by, b.updated_by, e.fabric_color_id as color_number_id, d.id as labdipid, d.lapdip_no, d.submitted_to_buyer, d.approval_status, d.approval_status_date, d.send_to_factory_date as swatch_date, d.id as dtls_id  
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_po_lapdip_approval_info d, wo_booking_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.color_name_id=e.fabric_color_id and b.id=e.po_break_down_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.gmts_color_id and d.app_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.approval_status in(1,3,5) $companyCond $buyer_id_cond $shipment_date $jobCond $labdipid_cond  
		group by  a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.product_dept, a.company_name, a.style_description, a.team_leader, a.dealing_marchant, b.id , b.po_number, b.pub_shipment_date, b.inserted_by, b.updated_by, e.fabric_color_id, d.id, d.lapdip_no, d.submitted_to_buyer, d.approval_status, d.approval_status_date, d.send_to_factory_date
		order by a.id desc";
		
		$capReqJob="Job No";
		$capReqPo="Po No";
	}
	else if($cbo_req_type==2)
	{
		$teamLeadr_arr=return_library_array( "select b.id, a.team_leader_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id",'id','team_leader_name');
		if($cbo_company!=0) $companyCond=" and a.company_id='$cbo_company'"; else  $companyCond="";
		if($hid_job_id=="") $jobCond=""; else $jobCond=" and a.id='$hid_job_id'";
		
		if($db_type==0)
		{
			if ($date_from!="" &&  $date_to!="") $shipment_date = "and a.requisition_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		}
		else if($db_type==2 || $db_type==1)
		{
			if ($date_from!="" &&  $date_to!="") $shipment_date = "and a.requisition_date between '".date("j-M-Y",strtotime($date_from))."' and '".date("j-M-Y",strtotime($date_to))."'"; else $shipment_date ="";
		}
		$sql="select a.id, a.requisition_number_prefix_num as job_no_prefix_num, a.requisition_number as job_no, a.buyer_name, a.style_ref_no, a.product_dept, a.company_id as  company_name, '' as style_description, a.dealing_marchant, 0 as poid, a.sample_stage_id as po_number, a.estimated_shipdate as pub_shipment_date, b.id as fab_id, b.inserted_by, b.updated_by, c.color_id as color_number_id, c.fabric_color, c.swatch_delv_date as swatch_date, c.id as dtls_id
		from sample_development_mst a, sample_development_fabric_acc b, sample_development_rf_color c 
		where a.id=b.sample_mst_id and b.id=c.dtls_id and a.sample_stage_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $companyCond $buyer_id_cond $shipment_date $jobCond 
		order by a.id desc";
		$capReqJob="Req No";
		$capReqPo="Sample Stage";
	}
	//echo $sql;
	
	$sql_swatch_rcv=sql_select("select dtls_id, req_type, swatch_rcv_date from lab_swatch_rcv");
	$swatch_date_arr=array();
	foreach($sql_swatch_rcv as $row)
	{
		$swatch_date_arr[$row[csf("dtls_id")]][$row[csf("req_type")]]=$row[csf("swatch_rcv_date")];
	}
	unset($sql_swatch_rcv);
		
	ob_start();
	$permission_page=explode('_',$permission);
	if($permission_page[0]==2) $button_con="disabled"; else $button_con="";
	?>
	<fieldset style="width:3010px;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<div>
		<table width="3010px" cellspacing="0">
			<tr style="border:none;">
				<td colspan="33" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company]; ?></td>
			</tr>
			<tr style="border:none;">
				<td colspan="33" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to); ?>
				</td>
			</tr>
		</table>
		<table width="3010px" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr style="font-size:13px">
					<th width="30">SL.</th>
					<th width="90">Sys. No.</th>
					<th width="120">Lab Company</th>
                    <th width="120">Req Company</th>
					<th width="90"><?=$capReqJob; ?></th>
					<th width="100">Buyer Name</th>     
					<th width="100">Style Ref.</th>
					<th width="100">Style Description</th>
					<th width="70">Pord. Dept.</th>
					<th width="100">Team Leader</th>
					<th width="100">Dealing Merchant</th>
					<th width="100"><?=$capReqPo; ?></th>
					<th width="100">Fab. Color Name</th>
					<th width="60">Approval Status</th>
					
					<th width="70">Lab to Approve</th>     
					<th width="100" style="color:#2A3FFF">Color Reference</th>
                    <th width="90">Lab Dip No</th>
					<th width="50" style="color:#2A3FFF">Swatch No</th>
					<th width="50">From</th>
					<th width="50">To</th>
					<th width="80">Shade</th>
					<th width="80">Dye Type</th>
					<th width="80">Pantone No</th>
					<th width="80" style="color:#2A3FFF">Fabric Type</th>     
					<th width="70" style="color:#2A3FFF">Fabric weight</th>
					<th width="100" style="color:#2A3FFF">Fabric Composition</th>
					<th width="70">Swatch Del. Date</th>
					<th width="70">Swatch Receive Date</th>
					<th width="70">Fabric Receive Date</th>
					<th width="70">Lab Dip Process Date</th>
					<th width="70">Lab Dip Send Date</th>
                    <th width="150">Remarks</th>
					<th width="100">Request By</th>
                    <th width="100">Sample Booking</th>
					<th>&nbsp;</th>
				 </tr>
			</thead>
		</table>
		<div style="width:3010px; max-height:200px; overflow-y:scroll" id="scroll_body"> 
			<table width="2990px" border="1" cellspacing="0" class="rpt_table" rules="all">
			<?
			$ingrediants_arr=array();
			$data_ingradients = sql_select("select sys_no, color_ref_id, panton from lab_color_ingredients_mst where status_active=1 and is_deleted=0");
			foreach($data_ingradients as $row)
			{
				$ingrediants_arr[$row[csf("color_ref_id")]]['panton']=$row[csf("panton")];
				$ingrediants_arr[$row[csf("color_ref_id")]]['sys_no']=$row[csf("sys_no")];
			}
			unset($data_ingradients);
			
			$colorRef_arr=array();
			$dataColorRef=sql_select("select id, shade_brightness, dye_type, color_ref from lab_color_reference where status_active=1 and is_deleted=0");
			foreach($dataColorRef as $row)
			{
				$colorRef_arr[$row[csf("id")]]=$row[csf("color_ref")].'***'.$row[csf("dye_type")].'***'.$row[csf("shade_brightness")];
			}
			unset($dataColorRef);
			
			$saveData_arr=array();
	
			$sqlSaveData="select id, request_no, colorref_id, po_id, color_id, labdip_id, swatch_no, swatch_from, swatch_to, fabric_type, fabric_weight, fabric_composition, swatch_del_date, swatch_rec_date, fabric_rec_date, labdip_process_date, labdip_send_date, remarks, lab_company_id from lab_labdip_request where request_type='$cbo_req_type' and status_active=1 and is_deleted=0";
			//echo $sqlSaveData; die;
			$sqlSaveData_res=sql_select($sqlSaveData);
			$labdip_ids=array();
			foreach($sqlSaveData_res as $row)
			{
				$saveData_arr[$row[csf("po_id")]][$row[csf("color_id")]][$row[csf("labdip_id")]]=$row[csf("id")].'___'.$row[csf("request_no")].'___'.$row[csf("colorref_id")].'___'.$row[csf("swatch_no")].'___'.$row[csf("swatch_from")].'___'.$row[csf("swatch_to")].'___'.$row[csf("fabric_type")].'___'.$row[csf("fabric_weight")].'___'.$row[csf("fabric_composition")].'___'.$row[csf("swatch_del_date")].'___'.$row[csf("swatch_rec_date")].'___'.$row[csf("fabric_rec_date")].'___'.$row[csf("labdip_process_date")].'___'.$row[csf("labdip_send_date")].'___'.$row[csf("remarks")].'___'.$row[csf("lab_company_id")];
				array_push($labdip_ids, $row[csf("labdip_id")]);
			}
			unset($sqlSaveData_res);
			
			//echo $sql; //die;
			$sql_res=sql_select($sql); $i=1;
			foreach($sql_res as $srow)
			{
				$requsetBy=0;
				if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
				
				if($srow[csf("updated_by")]==0) $requsetBy=$srow[csf("inserted_by")]; else $requsetBy=$srow[csf("updated_by")];
				
				$teamleadr=""; $po_samplestage=""; $colorid="";
				if($cbo_req_type==1)
				{
					$teamleadr=$teamLeadr_arr[$srow[csf("team_leader")]];
					$po_samplestage=$srow[csf("po_number")];
					$colorid=$srow[csf("color_number_id")];
				}
				else
				{
					$teamleadr=$teamLeadr_arr[$srow[csf("dealing_marchant")]];
					$po_samplestage=$sample_stage[$srow[csf("po_number")]];
					$colorName_str="";
					if($srow[csf("fabric_color")]=="" || $srow[csf("fabric_color")]==0) $colorid=$srow[csf("color_number_id")]; else $colorid=$srow[csf("fabric_color")];
					$srow[csf("poid")]=$srow[csf("fab_id")];
				}
				
				$requestData=$upid=$request_no=$colorref_id=$swatch_no=$swatch_from=$swatch_to=$fabric_type=$fabric_weight=$fabric_composition=$swatch_del_date=$swatch_rec_date=$fabric_rec_date=$labdip_process_date=$labdip_send_date=$remarks="";
				
				$requestData=$saveData_arr[$srow[csf("poid")]][$colorid][$srow[csf("labdipid")]];
				
				if($requestData!="")
				{
					$exrequestData=explode("___",$requestData);
					
					$upid=$exrequestData[0];
					$request_no=$exrequestData[1];
					$colorref_id=$exrequestData[2];
					$swatch_no=$exrequestData[3];
					$swatch_from=$exrequestData[4];
					$swatch_to=$exrequestData[5];
					$fabric_type=$exrequestData[6];
					$fabric_weight=$exrequestData[7];
					$fabric_composition=$exrequestData[8];
					$swatch_del_date=$exrequestData[9];
					$swatch_rec_date=$exrequestData[10];
					$fabric_rec_date=$exrequestData[11];
					$labdip_process_date=$exrequestData[12];
					$labdip_send_date=$exrequestData[13];
					$remarks=$exrequestData[14];
					$cbo_lab_company_name=$exrequestData[15];
				}
				else
				{
					//swatch_date
					$swatch_del_date=$srow[csf("swatch_date")];
					$swatch_rec_date=$swatch_date_arr[$srow[csf("dtls_id")]][$cbo_req_type];
					//echo $swatch_del_date."<br>";
				}
				$panton_no=""; $labdipno='';
				$panton_no=$ingrediants_arr[$colorref_id]['panton_no'];
				
				$labdipno=$ingrediants_arr[$colorref_id]['sys_no'];
				
				$colorrefdata=$color_ref=$dye_type=$shade_brightness="";
				
				$colorrefdata=$colorRef_arr[$colorref_id];
				if($colorrefdata!="")
				{
					$excolorrefdata=explode("***",$colorrefdata);
					
					$color_ref=$excolorrefdata[0];
					$dye_type=$excolorrefdata[1];
					$shade_brightness=$excolorrefdata[2];
				}
				/*
				//## this code buseness unknown
				if($srow[csf("submitted_to_buyer")]!='') $appstatus="Complete";
				if($srow[csf("approval_status")]==3 && $srow[csf("approval_status_date")]!='') $appstatus="Approved";
				if($srow[csf("submitted_to_buyer")]=="" && $srow[csf("approval_status")]!=3 && $srow[csf("approval_status_date")]=='') $appstatus="Pending";*/
				$ok=true;
				$request_no_css=""; $ondblClick="";
				if($request_no!="") 
				{ 
					$request_no_css="color:blue;font-weight:bold"; $ondblClick="onDblClick='generate_lab_report(".$i.");'"; 
					$appstatus="Completed";
				}
				else
				{
					$appstatus="Pending";
				}
				if($cbo_status==1 && $appstatus=="Completed")
				{
					$ok=false;
				}
				if($cbo_status==2 && $appstatus=="Pending")
				{
					$ok=false;
				}
				
				if($ok==true)
				{

					?>
					<tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr<?=$i; ?>','<?=$bgcolors; ?>');" id="tr<?=$i; ?>" style="font-size:13px; <?=$request_no_css; ?>">
						<td width="30" align="center"><?=$i; ?></td>
						<td width="90" style="word-break:break-all"><input style="width:78px;<?=$request_no_css; ?>" type="text" class="text_boxes" name="txtSysNo_<?=$i; ?>" placeholder="Display" id="txtSysNo_<?=$i; ?>"  value="<?=$request_no; ?>" <?=$ondblClick; ?> readonly /></td>
                        <td width="120" style="word-break:break-all;"><?=$companyArr[$cbo_lab_company_name]; ?></td>
						<td width="120" style="word-break:break-all;"><?=$companyArr[$srow[csf("company_name")]]; ?></td>
						<td width="90" style="word-break:break-all"><?=$srow[csf("job_no")]; ?></td>
						<td width="100" style="word-break:break-all"><?=$buyer_arr[$srow[csf("buyer_name")]]; ?></td>     
						<td width="100" style="word-break:break-all"><?=$srow[csf("style_ref_no")]; ?></td>
						<td width="100" style="word-break:break-all"><?=$srow[csf("style_description")]; ?></td>
						<td width="70" style="word-break:break-all"><?=$product_dept[$srow[csf("product_dept")]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$teamleadr; ?></td>
						<td width="100" style="word-break:break-all"><?=$dealingMerchecnt_arr[$srow[csf("dealing_marchant")]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$po_samplestage; ?></td>
						<td width="100" style="word-break:break-all"><?=$color_arr[$colorid]; ?></td>
						<td width="60" style="background-color:#FFA; word-break:break-all"><?=$appstatus; ?></td>
						<td width="70" style="word-break:break-all">&nbsp;</td>     
						<td width="100" style="word-break:break-all">
							<input style="width:88px;" type="text" class="text_boxes" name="txtColorRef_<?=$i; ?>" id="txtColorRef_<?=$i; ?>" placeholder="Browse" onDblClick="fnc_openmyPage_colorRef(<?=$i; ?>);" value="<?=$color_ref; ?>" readonly />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtCompanyId_<?=$i; ?>" id="txtCompanyId_<?=$i; ?>" value="<?=$srow[csf("company_name")]; ?>" />
                            <input style="width:38px;" type="hidden" class="text_boxes" name="labCompanyId_<?=$i; ?>" id="labCompanyId_<?=$i; ?>" value="<?=$cbo_lab_company_name; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtBuyerId_<?=$i; ?>" id="txtBuyerId_<?=$i; ?>" value="<?=$srow[csf("buyer_name")]; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtPoId_<?=$i; ?>" id="txtPoId_<?=$i; ?>" value="<?=$srow[csf("poid")]; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtColorId_<?=$i; ?>" id="txtColorId_<?=$i; ?>" value="<?=$colorid; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtColorRefId_<?=$i; ?>" id="txtColorRefId_<?=$i; ?>" value="<?=$colorref_id; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtLabDipId_<?=$i; ?>" id="txtLabDipId_<?=$i; ?>" value="<?=$srow[csf("labdipid")]; ?>" />
							<input style="width:38px;" type="hidden" class="text_boxes" name="txtUpdateId_<?=$i; ?>" id="txtUpdateId_<?=$i; ?>" value="<?=$upid; ?>" />
						</td>
                        <td width="90" style="word-break:break-all"><input style="width:78px;" type="text" class="text_boxes" name="txtLabDipNo_<?=$i; ?>" id="txtLabDipNo_<?=$i; ?>" value="<?=$labdipno; ?>" readonly placeholder="Display" /></td>
						<td width="50" style="word-break:break-all"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSwatchNo_<?=$i; ?>" id="txtSwatchNo_<?=$i; ?>" value="<?=$swatch_no; ?>" /></td>
						<td width="50"><input style="width:38px;" type="text" class="text_boxes" name="txtSwatchNoFrom_<?=$i; ?>" id="txtSwatchNoFrom_<?=$i; ?>" value="<?=$swatch_from; ?>" /></td>
						<td width="50"><input style="width:38px;" type="text" class="text_boxes" name="txtSwatchNoTo_<?=$i; ?>" id="txtSwatchNoTo_<?=$i; ?>"  value="<?=$swatch_to; ?>"/></td>
						<td width="80" style="word-break:break-all" id="tdShade_<?=$i; ?>"><?=$dyeinglab_shadeBrightness_arr[$shade_brightness]; ?></td>
						<td width="80" style="word-break:break-all" id="tdDyeType_<?=$i; ?>"><?=$dyeinglab_dyetype_arr[$dye_type]; ?></td>
						<td width="80" style="word-break:break-all" id="tdPantoNo_<?=$i; ?>"><?=$panton_no; ?></td>
						<td width="80" style="word-break:break-all"><input style="width:68px;" type="text" class="text_boxes" name="txtFabricType_<?=$i; ?>" id="txtFabricType_<?=$i; ?>" value="<?=$fabric_type; ?>" /></td>     
						<td width="70" style="word-break:break-all"><input style="width:58px;" type="text" class="text_boxes" name="txtFabricWeight_<?=$i; ?>" id="txtFabricWeight_<?=$i; ?>" value="<?=$fabric_weight; ?>" /></td>
						<td width="100" style="word-break:break-all"><input style="width:88px;" type="text" class="text_boxes" name="txtFabricCompos_<?=$i; ?>" id="txtFabricCompos_<?=$i; ?>" value="<?=$fabric_composition; ?>" /></td>
						<td width="70" title="<?= $swatch_del_date;?>"><input style="width:58px;" type="text" class="datepicker" name="txtSwatchDelDate_<?=$i; ?>" id="txtSwatchDelDate_<?=$i; ?>" value="<?=  ($swatch_del_date!="" && $swatch_del_date!="0000-00-00") ? change_date_format($swatch_del_date) : "";  ?>" /></td>
						<td width="70" title="<?= $swatch_rec_date;?>"><input style="width:58px;" type="text" class="text_boxes" name="txtSwatchRecDate_<?=$i; ?>" id="txtSwatchRecDate_<?=$i; ?>" value="<?= ($swatch_rec_date!="" && $swatch_rec_date!="0000-00-00") ? change_date_format($swatch_rec_date) : ""; ?>" onDblClick="fn_swatch_rcv_date('<?=$i; ?>','<?=$srow[csf("dtls_id")];?>','<?=$cbo_req_type;?>');" readonly placeholder="Browse" /></td>
						<td width="70"><input style="width:58px;" type="text" class="datepicker" name="txtFabRecDate_<?=$i; ?>" id="txtFabRecDate_<?=$i; ?>" value="<?= ($fabric_rec_date!="" && $fabric_rec_date!="0000-00-00") ? change_date_format($fabric_rec_date) : ""; ?>" /></td>
						<td width="70"><input style="width:58px;" type="text" class="datepicker" name="txtLabDipProcessDate_<?=$i; ?>" id="txtLabDipProcessDate_<?=$i; ?>" value="<?= ($labdip_process_date!="" && $labdip_process_date!="0000-00-00") ? change_date_format($labdip_process_date) : ""; ?>" /></td>
						<td width="70"><input style="width:58px;" type="text" class="datepicker" name="txtLabDipSendDate_<?=$i; ?>" id="txtLabDipSendDate_<?=$i; ?>" value="<?=  ($labdip_send_date!="" && $labdip_send_date!="0000-00-00") ? change_date_format($labdip_send_date) : ""; ?>" /></td>
	                    <td width="150"><input style="width:135px;" type="text" class="text_boxes" name="txtRemarks_<?=$i; ?>" id="txtRemarks_<?=$i; ?>" value="<?=$remarks; ?>" /></td>
						<td width="100" style="word-break:break-all"><?=$user_arr[$requsetBy]; ?></td>
                        <?
						if($cbo_req_type==1)
						{
							?>
                            <td width="100" style="word-break:break-all">&nbsp;&nbsp;<a href='##' onClick="generate_booking_report('<?=$sample_book_arr[$srow[csf("poid")]]["booking_no"]; ?>','<?=$srow[csf("company_name")];?>','<?=$srow[csf("poid")];?>','2','2','<?=$sample_book_arr[$srow[csf("poid")]]["is_approved"]; ?>','<?=$srow[csf("job_no")];?>')"><? echo $sample_book_arr[$srow[csf("poid")]]["booking_no"]; ?></a></td>
                            <?
						}
						else
						{
							?>
                            <td width="100" style="word-break:break-all">&nbsp;&nbsp;</td>
                            <?
						}
						?>
                        
						<td align="center"><input type="button" id="showButton_<?=$i; ?>" class="formbutton" style="width:60px" value="Save" onClick="fnc_labdip_request_entry('<?=0; ?>','<?=$i; ?>');" <?=$button_con;?> /></td>
					 </tr>
					<?
					$i++;
				}
			}
			?>
			</table>
		</div>
	</div>
	</fieldset>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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

if($action=="colorref_popup")
{
	echo load_html_head_contents("Color Ref. Search Popup","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $data.'=='.$cbo_company_id;
	?>
	<script>
		function js_set_value(str)
		{ 
			$("#selected_str_data").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead> 
					<tr>
						<th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
					<tr>               	 
						<th width="140" class="must_entry_caption">Company Name</th>
                        <th width="100">Lab Dip No</th>
						<th width="100">Color Ref.</th>
                        <th width="100">Color</th> 
                        <th width="100">Shade Brightness</th>
						<th width="80">Dye Type</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
					</tr>           
				</thead>
				<tbody>
					<tr class="general">
						<td><? echo create_drop_down( "cbo_company_name", 140, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and id=$data $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $data, "",0); ?><input type="hidden" id="selected_str_data">
						</td>
                        <td><input type="text" name="txt_labdipno" id="txt_labdipno" class="text_boxes" style="width:90px" placeholder="" /></td>
						<td><input type="text" name="txt_colorref" id="txt_colorref" class="text_boxes" style="width:90px" placeholder="" /></td>
						<td><input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:90px" placeholder="" /></td>
						<td><? echo create_drop_down( "cbo_shadebrightness", 100, $dyeinglab_shadeBrightness_arr,"", 1, "-- Select --","", "",0 ); ?></td>
                        <td><? echo create_drop_down( "cbo_dyetype", 80, $dyeinglab_dyetype_arr,"", 1, "-- Select --","", "",0 ); ?></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_colorref').value+'_'+document.getElementById('txt_color').value+'_'+document.getElementById('cbo_shadebrightness').value+'_'+document.getElementById('cbo_dyetype').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_labdipno').value, 'create_colorref_search_list_view', 'search_div', 'requset_lab_dip_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                        </tr>
                    </tbody>
                </table>    
				</form>
                <div id="search_div"></div>
			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_colorref_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$colorref=$exdata[1];
	$color=$exdata[2];
	$shadebrightness=$exdata[3];
	$dyetype=$exdata[4];
	$search_type =$exdata[5];
	$labdipno =$exdata[6];
	
	if($cbo_company_id!=0) $companyCond=" and a.company_id='$cbo_company_id'"; else { echo "Please Select Company First."; die; }
	
	$colorref_cond=""; $color_cond=""; $shadebrightness_cond=""; $dyetype_cond="";
	if($search_type==1)
	{
		if($colorref!="") $colorref_cond="and a.color_ref='$colorref'";
		//if($color!="") $color_cond="and a.order_no='$color'";
		if ($shadebrightness!=0) $shadebrightness_cond=" and a.shade_brightness = '$shadebrightness' ";
		if ($dyetype!=0) $dyetype_cond=" and a.dye_type = '$dyetype' ";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($colorref!="") $colorref_cond="and a.color_ref  like '%$colorref%'";
		//if($color!="") $color_cond="and a.order_no  like '%$color%'";
		if($shadebrightness!=0) $shadebrightness_cond=" and a.shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and a.dye_type = '$dyetype' ";
	}
	else if($search_type==2)
	{
		if($colorref!="") $colorref_cond="and a.color_ref  like '$colorref%'";
		//if($color!="") $color_cond="and a.order_no  like '$color%'";
		if($shadebrightness!=0) $shadebrightness_cond=" and a.shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and a.dye_type = '$dyetype' ";
	}
	else if($search_type==3)
	{
		if($colorref!="") $colorref_cond="and a.color_ref  like '%$colorref'";
		//if($color!="") $color_cond="and a.order_no  like '%$color'";
		if($shadebrightness!=0) $shadebrightness_cond=" and a.shade_brightness = '$shadebrightness' ";
		if($dyetype!=0) $dyetype_cond=" and a.dye_type = '$dyetype' ";
	}
	
	if($labdipno!="") $labdipnocond=" and b.sys_prefix_num='$labdipno'"; else $labdipnocond="";
		
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	//echo "<pre>";
	//print_r($buyer_po_arr);
	
	?>
    <body>
		<div align="center">
			<fieldset style="width:700px;">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
                            <th width="100">Lab Dip No</th>
							<th width="100">Color Ref.</th>
                            <th width="100">Color</th>
                            <th width="60">Color Code</th>
                            <th width="80">Shade Brightness</th>
                            <th width="60">Shade Code</th>
                            <th width="80">Dye Type</th>
                            <th>Dye Type Code</th>
						</thead>
					</table>
					<div style="width:700px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="list_view" >
							<?
							$sql= "select a.id, a.company_id, a.color_id, a.color_code, a.shade_brightness, a.shade_code, a.dye_type, a.dyetype_code, a.colorref_prefix, a.colorref_prefix_num, a.color_ref, b.id as ingid, b.sys_no from lab_color_reference a, lab_color_ingredients_mst b where a.id=b.color_ref_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $companyCond $colorref_cond $color_cond $shadebrightness_cond $dyetype_cond $labdipnocond order by a.id DESC";
							//echo $sql; die;
							$sql_res=sql_select($sql);
							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$row[csf('id')].'__'.$row[csf('ingid')]; ?>');"> 
									<td width="30" align="center"><?=$i; ?></td>	
									<td width="100" align="center"><?php echo $row[csf("sys_no")]; ?></td>
                                    <td width="100" align="center"><?php echo $row[csf('color_ref')]; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
                                    <td width="60" style="word-break:break-all"><?php echo $row[csf("color_code")]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]]; ?></td>
                                    <td width="60" style="word-break:break-all"><?php echo $row[csf('shade_code')]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $dyeinglab_dyetype_arr[$row[csf('dye_type')]]; ?></td>
                                    <td style="word-break:break-all"><?php echo $dyeinglab_dyecode_arr[$row[csf('dyetype_code')]]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$exdata=explode("***",$data);
	$exdatazero=explode("__",$exdata[0]);
	$id=$exdatazero[0];
	$ingredientsid=$exdatazero[1];
	$inc=$exdata[1];
	
	$ingrediants_arr=array();
	$data_ingradients = sql_select("select id, sys_no, correction, color_ref_id, company_id, section_id, client_id, color_desc, panton, shade_no, construction, blend, remarks from lab_color_ingredients_mst where id='$ingredientsid'");
	foreach($data_ingradients as $row)
	{
		$ingrediants_arr[$row[csf("color_ref_id")]]['panton']=$row[csf("panton")];
		$ingrediants_arr[$row[csf("color_ref_id")]]['labdipno']=$row[csf("sys_no")];
		$ingrediants_arr[$row[csf("color_ref_id")]]['construction']=$row[csf("construction")];
		$ingrediants_arr[$row[csf("color_ref_id")]]['blend']=$row[csf("blend")];
	}
	unset($data_ingradients);
	
	$data_array=sql_select("select id, company_id, color_id, color_code, shade_brightness, shade_code, dye_type, dyetype_code, colorref_prefix, colorref_prefix_num, color_ref from lab_color_reference where id='$id'");
	if(count($data_array)>0)
	{
		foreach ($data_array as $row)
		{
			echo "$('#txtColorRef_".$inc."').val('".$row[csf("color_ref")]."');\n";
			echo "$('#txtColorRefId_".$inc."').val('".$row[csf("id")]."');\n";
			echo "$('#tdShade_".$inc."').text('".$dyeinglab_shadeBrightness_arr[$row[csf("shade_brightness")]]."');\n";
			echo "$('#tdDyeType_".$inc."').text('".$dyeinglab_dyetype_arr[$row[csf("dye_type")]]."');\n";
			echo "$('#tdPantoNo_".$inc."').text('".$ingrediants_arr[$row[csf("id")]]['panton']."');\n";
			echo "$('#txtLabDipNo_".$inc."').val('".$ingrediants_arr[$row[csf("id")]]['labdipno']."');\n";
			echo "$('#txtFabricType_".$inc."').val('".$ingrediants_arr[$row[csf("id")]]['construction']."');\n";
			echo "$('#txtFabricCompos_".$inc."').val('".$ingrediants_arr[$row[csf("id")]]['blend']."');\n";
		}
	}
	else
	{
		echo "$('#txtColorRef_".$inc."').val('');\n";
		echo "$('#txtColorRefId_".$inc."').val('');\n";
		echo "$('#tdShade_".$inc."').text('');\n";
		echo "$('#tdDyeType_".$inc."').text('');\n";
		echo "$('#tdPantoNo_".$inc."').text('');\n";
		echo "$('#txtLabDipNo_".$inc."').val('');\n";
		echo "$('#txtFabricType_".$inc."').val('');\n";
		echo "$('#txtFabricCompos_".$inc."').val('');\n";
	}
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert update Here
	{	
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$labCompanyId="labCompanyId_".$increment_id;
		$txtCompanyId="txtCompanyId_".$increment_id;
		$txtColorRefId="txtColorRefId_".$increment_id;
		$txtBuyerId="txtBuyerId_".$increment_id;
		$txtPoId="txtPoId_".$increment_id;
		$txtColorId="txtColorId_".$increment_id;
		$txtLabDipId="txtLabDipId_".$increment_id;
		$txtSwatchNo="txtSwatchNo_".$increment_id;
		$txtSwatchNoFrom="txtSwatchNoFrom_".$increment_id;
		$txtSwatchNoTo="txtSwatchNoTo_".$increment_id;
		$txtFabricType="txtFabricType_".$increment_id;
		$txtFabricWeight="txtFabricWeight_".$increment_id;
		$txtFabricCompos="txtFabricCompos_".$increment_id;
		$txtSwatchDelDate="txtSwatchDelDate_".$increment_id;
		$txtSwatchRecDate="txtSwatchRecDate_".$increment_id;
		$txtFabRecDate="txtFabRecDate_".$increment_id;
		$txtLabDipProcessDate="txtLabDipProcessDate_".$increment_id;
		$txtLabDipSendDate="txtLabDipSendDate_".$increment_id;
		$txtRemarks="txtRemarks_".$increment_id;
		$txtUpdateId="txtUpdateId_".$increment_id;
		$txtSysNo="txtSysNo_".$increment_id;
		$cboCompanyId=$$txtCompanyId;
		$labCompany=$$labCompanyId;
		if($db_type==0)
		{
			$txtSwatchRecDates=change_date_format(str_replace("'","",$$txtSwatchRecDate),"yyyy-mm-dd");
		}
		else
		{
			$txtSwatchRecDates=change_date_format(str_replace("'","",$$txtSwatchRecDate),"","",1);
		}
		
		
		$updateId=str_replace("'","",$$txtUpdateId);
		if($updateId=="")
		{
			if($db_type==0) $date_cond=" YEAR(insert_date)"; else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
			$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$labCompany), '', '', date("Y",time()), 6, "select request_prefix, request_prefix_num from lab_labdip_request where lab_company_id=$labCompany and $date_cond=".date('Y',time())." order by id DESC", "request_prefix", "request_prefix_num" ));
			//echo "10**". print_r($new_sys_no); die;
			$mst_id=return_next_id("id", "lab_labdip_request", 1);
			
			$field_array_mst="id, company_id, request_prefix, request_prefix_num, request_no, request_type, buyer_id, colorref_id, po_id, color_id, labdip_id, swatch_no, swatch_from, swatch_to, fabric_type, fabric_weight, fabric_composition, swatch_del_date, swatch_rec_date, fabric_rec_date, labdip_process_date, labdip_send_date, remarks, inserted_by, insert_date, status_active, is_deleted, lab_company_id";
			
			$data_array_mst="(".$mst_id.",".$$txtCompanyId.",'".$new_sys_no[1]."','".$new_sys_no[2]."','".$new_sys_no[0]."',".$cbo_req_type.",".$$txtBuyerId.",".$$txtColorRefId.",".$$txtPoId.",".$$txtColorId.",".$$txtLabDipId.",".$$txtSwatchNo.",".$$txtSwatchNoFrom.",".$$txtSwatchNoTo.",".$$txtFabricType.",".$$txtFabricWeight.",".$$txtFabricCompos.",".$$txtSwatchDelDate.",'".$txtSwatchRecDates."',".$$txtFabRecDate.",".$$txtLabDipProcessDate.",".$$txtLabDipSendDate.",".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$labCompany.")";
			$sysNo="'".$new_sys_no[0]."'";
		}
		else
		{
			$field_array_up="buyer_id*colorref_id*color_id*labdip_id*swatch_no*swatch_from*swatch_to*fabric_type*fabric_weight*fabric_composition*swatch_del_date*swatch_rec_date*fabric_rec_date*labdip_process_date*labdip_send_date*remarks*updated_by*update_date";
			
			$data_array_up="".$$txtBuyerId."*".$$txtColorRefId."*".$$txtColorId."*".$$txtLabDipId."*".$$txtSwatchNo."*".$$txtSwatchNoFrom."*".$$txtSwatchNoTo."*".$$txtFabricType."*".$$txtFabricWeight."*".$$txtFabricCompos."*".$$txtSwatchDelDate."*'".$txtSwatchRecDates."'*".$$txtFabRecDate."*".$$txtLabDipProcessDate."*".$$txtLabDipSendDate."*".$$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$mst_id="'".$updateId."'";
			$sysNo=$$txtSysNo;
		}
		//echo "10**insert into lab_labdip_request (".$field_array_up.") values ".$data_array_up;die;
		
		$flag=1;
		$showMsg=10;
		if($updateId=="")
		{
			//echo "10** insert into lab_labdip_request ($field_array_mst) values $data_array_mst";die;
			$rID=sql_insert("lab_labdip_request",$field_array_mst,$data_array_mst,0);
			//echo "10**".$rID;die;
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			$showMsg=0;
		}
		else
		{
			$rID=sql_update("lab_labdip_request",$field_array_up,$data_array_up,"id",$mst_id,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			$showMsg=1;
		}
		//echo '10**'.$flag.'='.$rID.'='.$rID1; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo $showMsg."**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$sysNo).'**'.str_replace("'",'',$increment_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo $showMsg."**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$sysNo).'**'.str_replace("'",'',$increment_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;	
	}
	//exit();
}

if($action=="labdip_order_submission_print")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	function show_company__($company_id, $show_cap, $fldlist) 
	{
		$fldarray = array("plot_no" => "plot_no", "level_no" => "level_no", "road_no" => "road_no", "block_no" => "block_no", "city" => "city", "zip_code" => "zip_code", "province" => "province", "country_id" => "country_id", "email" => "email", "website" => "website", "vat_number" => "vat_number");

		if (!is_array($fldlist)) {
			$fldlist = $fldarray;
		}

		$nameArray = sql_select("select a.plot_no, a.level_no, a.road_no, a.block_no, b.country_name as country_id, a.province, a.city, a.zip_code, a.email, a.vat_number from lib_company a left join  lib_country b on a.country_id =b.id where a.id=$company_id and a.status_active=1 and a.is_deleted=0");
		foreach ($nameArray as $result) 
		{
			foreach ($fldarray as $fld) {
				if (in_array($fld, $fldlist)) {
					if (trim($result[csf($fld)]) != "") {
						if ($show_cap == 1) {
							$address .= ucwords(str_replace("_", " ", $fld)) . "-";
							$address .= " " . trim($result[csf($fld)]);
						} else {
							$address .= " " . trim($result[csf($fld)]);
						}

						if ($address != '') {
							$address .= ",";
						}

					}
				}
			}
		}
		return $address;
	}
	//echo $company.'='.$report_title.'='.$sysNo.'='.$updateid.'='.$report_type;
	$companyArr=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyerArr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$seasonArr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$colorRefArr=return_library_array( "select id, color_ref from lab_color_reference", "id", "color_ref");
	
	
	$sqlData="SELECT id, request_no, request_type, buyer_id, colorref_id, po_id, color_id, labdip_id, swatch_no, swatch_from, swatch_to, fabric_type, fabric_weight, fabric_composition, labdip_send_date,lab_company_id, remarks from lab_labdip_request where id='$updateid' and status_active=1 and is_deleted=0";
	// echo $sqlData;
	$dataArray=sql_select($sqlData);
	
	$swatchNo=$dataArray[0][csf('swatch_no')];
	$swatchFrom=$dataArray[0][csf('swatch_from')];
	$request_type=$dataArray[0][csf('request_type')];
	$lab_company=$dataArray[0][csf('lab_company_id')]; 
	
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$lab_company'","image_location");

	$po_id=$dataArray[0][csf('po_id')]; $supplierName="";
	if($request_type==1)// Order
	{
		$sqlJob="SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.season_buyer_wise, b.id as poid, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id='$po_id'";
		$dataJob=sql_select($sqlJob); 
		
		$supplierName=$companyArr[$dataJob[0][csf('company_name')]];
	}
	else if($request_type==2)// Requisition
	{
		$sqlJob="SELECT a.id, a.requisition_number_prefix_num as job_no_prefix_num, a.requisition_number as po_number, a.company_id as  company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.season_buyer_wise, b.id as poid from sample_development_mst a, sample_development_fabric_acc b where a.id=b.sample_mst_id and a.sample_stage_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id='$po_id'";
		
		$dataJob=sql_select($sqlJob); 
		$supplierName=$companyArr[$dataJob[0][csf('company_name')]];
	}
	?>
    <div style="width:930px;">
        <table width="930" cellspacing="0" align="left">
        	<tr>
        		<td rowspan="4">
        			<img src="../../<?=$image_location; ?>" height="60" width="80">
        		</td>
        	</tr>
            <tr>
                <td colspan="5" align="center" style="font-size:22px;"><strong ><?=$companyArr[$lab_company]; ?></strong></td>
                <td align="center" style="text-align:right;"><strong><u><i>Client Copy</i></u></strong></td>
            </tr>
            <tr>
                <td colspan="5" align="center" style="font-size:14px"><?=show_company__($lab_company,'',''); ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" align="center" style="font-size:20px"><u><strong>Labdip Order and Submission Form</strong></u></td>
                <td></td>
            </tr>
            <tr style="font-size: 15px;">
                <td width="110"><strong>Sys. No.:</strong></td>
                <td width="170px"><?=$dataArray[0][csf('request_no')]; ?></td>
                <td width="110">&nbsp;</td>
                <td width="170px">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="font-size: 15px;">
                <td><strong>Order:</strong></td>
                <td><?=$dataJob[0][csf('po_number')]; ?></td>
                <td><strong>Style:</strong></td>
                <td><?=$dataJob[0][csf('style_ref_no')]; ?></td>
                <td><strong>Date:</strong></td>
                <td><?=change_date_format($dataArray[0][csf('labdip_send_date')]); ?></td>
            </tr>
            <tr style="font-size: 15px;">
                <td><strong>Season:</strong></td>
                <td><?=$seasonArr[$dataJob[0][csf('season_buyer_wise')]]; ?></td>
                <td><strong>Department:</strong></td>
                <td><?=$product_dept[$dataJob[0][csf('product_dept')]]; ?></td>
                <td><strong>Req. Company:</strong></td>
                <td><?=$supplierName; ?></td>
            </tr>
            <tr style="font-size: 15px;">
                <td><strong>Fabric:</strong></td>
                <td><?=$dataArray[0][csf('fabric_type')]; ?></td>
                <td><strong>Composition:</strong></td>
                <td ><?=$dataArray[0][csf('fabric_composition')]; ?></td>
                <td><strong>Client:</strong></td>
                <td ><?=$buyerArr[$dataArray[0][csf('buyer_id')]]; ?></td>
            </tr>
            <tr style="font-size: 15px;">
                <td><strong>Remarks:</strong></td>
                <td colspan="5"><?=$dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
    	<br>
        <table align="left" cellspacing="0" border="1" rules="all" class="rpt_table" style=" margin-top:20px;width: 25cm" >
        <? 
		for($k=1; $k<=$swatchNo; $k++) 
		{ 
			?>
            <thead bgcolor="#dddddd">
                <tr>
                    <th width="200">Color/Pantone NO</th>
                    <th style="width: 9cm;" >Labdip</th>
                    <th width="">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" style="word-break:break-all; height:95px;"><?=$colorArr[$dataArray[0][csf('color_id')]]; ?></td>
                    <td align="center" style="word-break:break-all; width:270px; height:270px" rowspan="2">
                    	<div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">Attatch Swatch</div>
                    </td>
                    <td align="center" style="word-break:break-all" rowspan="2"><div style="background:url({{blogthreadlist.blogUri}}) no-repeat;background-position:center;opacity:0.6;filter:alpha(opacity=60);z-index: -1">Comments</div></td>
                </tr>
                <tr>
                    <td align="center" style="word-break:break-all; height:95px;"><?=$colorRefArr[$dataArray[0][csf('colorref_id')]].$swatchFrom; ?></td>
                </tr>
            </tbody>
        	<?php
			$swatchFrom++;
		}
		?>
        </table>
    </div>
    <?
	echo signature_table(191, $lab_company, "930px");
	exit();
}

if($action=="swath_rcv_date_popup")
{
	echo load_html_head_contents("Swatch Receive Date", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
	var permission='<? echo $permission; ?>';
	function fnc_swatch_entry(operation)
	{
		var data="action=save_update_delete_swatch_date&operation="+operation+get_submitted_data_string('txt_swatch_date*dtls_id*req_type',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requset_lab_dip_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_swatch_entry_info;
	}

	function fnc_swatch_entry_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			if(reponse[0]==0)
			{
				reset_form('size_1','','','','','');
				release_freezing();
				document.getElementById('hidden_swatch_date').value=reponse[2];
				parent.emailwindow.hide();
				
			}
			else
			{
				release_freezing();
			}
		}
	}
		
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                	<td width="200">Swatch Receive Date</td>
                    <td align="center" >
                    	<input type="text" name="txt_swatch_date" id="txt_swatch_date" class="datepicker" style="width:120px;" />
                        <input type="hidden" name="dtls_id" ID="dtls_id" value="<? echo $dtls_id; ?>" />
                        <input type="hidden" name="req_type" ID="req_type" value="<? echo $req_type; ?>"/>
                        <input type="hidden" name="hidden_swatch_date" ID="hidden_swatch_date" value=""/>
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center" class="button_container">
                    <input type="button" name="btn_save" id="btn_save" class="formbutton" value="Save" onClick="fnc_swatch_entry(0);" style="width:100px" />
                        <?
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_swatch_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_swatch_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="save_update_delete_swatch_date")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$delete_swatch=execute_query("delete from lab_swatch_rcv where dtls_id=$dtls_id and req_type=$req_type");
		$id_mst=return_next_id( "id", "lab_swatch_rcv", 1 ) ;
		$field_array="id,dtls_id,req_type,swatch_rcv_date,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id_mst.",".$dtls_id.",".$req_type.",".$txt_swatch_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("lab_swatch_rcv",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID && $delete_swatch)
			{
				mysql_query("COMMIT");
				echo "0**".$id_mst."**".str_replace("'","",$txt_swatch_date);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id_mst;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_swatch)
			{
				oci_commit($con);
				echo "0**".$id_mst."**".change_date_format(str_replace("'","",$txt_swatch_date));
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id_mst;
			}
		}
		disconnect($con);
		die;

	}
}

?>