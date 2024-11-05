<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$department_arr=return_library_array( "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','DEPARTMENT_NAME');
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="search_popup")
{
	extract($_REQUEST);
	if($type==1) $tittle="Booking"; else $tittle="Job";
	echo load_html_head_contents($tittle." No Info", "../../../", 1, 1,'','','');
	
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_id').val( id );
			$('#hide_no').val( name );
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
	                    <th>Search By.</th>
	                    <th id="search_by_td_up" width="170">Please Enter <? echo $tittle; ?> No </th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_no" id="hide_no" value="" />
	                    <input type="hidden" name="hide_id" id="hide_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
								if($type==1) $search_by_arr=array(1=>"Booking No",2=>"Job No"); else $search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'fabric_booking_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    </td>
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

if($action=="create_job_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$type=$data[4];

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($type==1)	
	{
		if($search_by==1) $search_field="booking_no"; else $search_field="job_no";
		
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and buyer_id=$data[1]";
		
		$sql= "select id, booking_no, booking_no_prefix_num, job_no, company_id, buyer_id from wo_booking_mst where status_active=1 and is_deleted=0 and ready_to_approved=1 and company_id=$company_id and $search_field like '$search_string' and item_category in(2,3,13) $buyer_id_cond order by booking_no";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No", "140,140,140","600","240",0, $sql , "js_set_value", "id,booking_no", "", 1, "company_id,buyer_id,0,0", $arr , "company_id,buyer_id,booking_no,job_no", "",'','0,0,0,0','',1) ;
	}
	else
	{
		if($search_by==1) $search_field="job_no"; else $search_field="style_ref_no";
		
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and buyer_name=$data[1]";
		
		$sql= "select id, job_no, company_name, buyer_name, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No,", "120,120,120","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no", "",'','0,0,0,0','',1) ;
	}
   exit(); 
} 

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

 	if(str_replace("'","",trim($cbo_date_by))==1)
	{
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
		{
			$date_cond=" and a.booking_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$date_cond="";
		}
	}
	else if(str_replace("'","",trim($cbo_date_by))==2)
	{
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
		{
			$date_cond=" and a.insert_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$date_cond="";
		}
	}
	else if(str_replace("'","",trim($cbo_date_by))==3)
	{
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
		{
			$app_date_cond=" and approved_date between  $txt_date_from  and '".str_replace("'","",trim($txt_date_to)). " 11:59:59 PM'";
		}
		else
		{
			$app_date_cond="";
		}
	}

	
	$ascending_by=str_replace("'","",trim($cbo_ascending_by));

	

	// if(str_replace("'","",trim($txt_app_date))!="" )
	// {
	// 	if($db_type==0){
	// 		$app_date_cond=" and approved_date between $txt_app_date and $txt_app_date";
	// 	}
	// 	else
	// 	{
	// 		$app_date_cond=" and approved_date between  $txt_app_date  and '".str_replace("'","",trim($txt_app_date)). " 11:59:59 PM'";
	// 	}
	// }
	// else
	// {
	// 	$app_date_cond="";
	// }
	
	
	
	
 	if($template==1)
	{
		$type = str_replace("'","",$cbo_type);
		$booking_type = str_replace("'","",$cbo_booking_type);
		
		if($booking_type==1) $booking_type_cond=" and a.booking_type=1 and a.is_short=2";
		else if($booking_type==2) $booking_type_cond=" and a.booking_type=1 and a.is_short=1"; 
		else if($booking_type==3) $booking_type_cond=" and a.booking_type=4"; 
		else $booking_type_cond="";
		
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		}
		
		if(str_replace("'","",$hide_booking_id)=="") $booking_cond=""; else $booking_cond=" and a.id in(".str_replace("'","",$hide_booking_id).")";
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($txt_job_no=="") $job_cond=""; else $job_cond=" and a.job_no in('".implode("','",explode("*",$txt_job_no))."')";

		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		if($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping in('".implode("','",explode("*",$txt_internal_ref))."')";
		//echo $internal_ref_cond.' Tipu';
		
		$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
		$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");
		
		$po_array=array();
		$poData=sql_select( "select id, po_number, pub_shipment_date from wo_po_break_down");
		foreach($poData as $po_row)
		{
			$po_array[$po_row[csf('id')]]['no']=$po_row[csf('po_number')];
			$po_array[$po_row[csf('id')]]['ship_date']=change_date_format($po_row[csf('pub_shipment_date')]);
		}
		
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
		
		$user_name_array=array();
		$userData=sql_select( "select id, user_name, user_full_name, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
			$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
			$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
		}
		
		//$approved_no_array=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=7 group by mst_id","mst_id","approved_no");

		$buyer_id_arr=array();
		$buyerData=sql_select("select entry_form, user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form in(7,12,13) and bypass=2");
		foreach($buyerData as $row)
		{
			$buyer_id_arr[$row[csf('entry_form')]][$row[csf('user_id')]]=$row[csf('buyer_id')];
		}
		//print_r($buyer_id_arr[12]);
		
	
		$signatory_data_arr=sql_select("select LISTAGG(case when entry_form=7 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idm,LISTAGG(case when entry_form=7 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idm, LISTAGG(case when entry_form=12 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_ids, LISTAGG(case when entry_form=13 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsm, LISTAGG(case when entry_form=7 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idmby, LISTAGG(case when entry_form=12 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsby, LISTAGG(case when entry_form=13 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsmby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 ORDER BY sequence_no");	

	
		
		
		$signatory_main=$signatory_data_arr[0][csf('user_idm')];
		$signatory_short=$signatory_data_arr[0][csf('user_ids')];
		$signatory_sample=$signatory_data_arr[0][csf('user_idsm')];
		
		$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idmby')];
		$bypass_no_user_id_short=$signatory_data_arr[0][csf('user_idsby')];
		$bypass_no_user_id_sample=$signatory_data_arr[0][csf('user_idsmby')];
		
		//$last_user_id=return_field_value("max(user_id) as user_id", "electronic_approval_setup", "entry_form=7 and is_deleted=0","user_id" );

		$signatory_data_arr_bypass = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and page_id=410 order by sequence_no");


		foreach($signatory_data_arr_bypass as $sval)
		{
			$signatory_main_bypass[$sval[csf('user_id')]]=$sval[csf('bypass')];
			$userArr[$sval[csf('user_id')]]=$sval[csf('user_id')];
		}
 
		if($app_date_cond!=''){
			if($type == 2){$fullAppCon = " and CURRENT_APPROVAL_STATUS=1";}
			$mst_id_arr=return_library_array( "select mst_id, mst_id from approval_history where entry_form in(7,12,13) $app_date_cond $fullAppCon ","mst_id","mst_id");
			$mst_id_con = " and a.id in(".implode(',',$mst_id_arr).")";
		}
		else
		{
			$mst_id_con = "";	
		}
		

		
		//print_r($user_approval_array[1061]);
		//and approved_date between '14-May-2017  11:59:59 PM' and '16-May-2017 11:59:59 PM' order by mst_id
		
		$approval_remarks_arr=array();
		$sql_remarks=sql_select("select approval_cause,booking_id,user_id,entry_form,approval_no from fabric_booking_approval_cause where entry_form in (7,12,13) and status_active=1 and is_deleted=0 ");
		foreach($sql_remarks as $inf)
		{
			//$approval_remarks_arr[$inf[csf('booking_id')]][$inf[csf('user_id')]][$inf[csf('approval_no')]]=$inf[csf('approval_cause')];
			$approval_remarks_arr[$inf[csf('booking_id')]][$inf[csf('approval_no')]]=$inf[csf('approval_cause')];
		}
		//echo "select format_id from lib_report_template where template_name='".$cbo_company_name."' and module_id=2 and report_id=1 and is_deleted=0 and status_active=1";
		
		$print_report_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	 
		$print_report_id_short_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		
		$print_report_id_sample_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
		$print_report_format_par=return_field_value("format_id"," lib_report_template","template_name =".$cbo_company_name."  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	
	$print_report_format_part=explode(",",$print_report_format_par);
	 
	 
	 //echo $print_report_ids;
	$format_ids=explode(",",$print_report_ids);
	$print_report_id_short_fabric_arr=explode(",",$print_report_id_short_fabric);
	$print_report_id_sample_fabric_arr=explode(",",$print_report_id_sample_fabric);
	// print_r($format_ids);

		ob_start();
		?>
        <fieldset style="width:2450px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2620" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Job No.</th>
                    <th width="100">Style Name</th>
                   <th width="100">Internal Ref</th>
                    <th width="80">Buyer Name</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="170">Order No.</th>
                    <th width="80">Shipment Date (Min)</th>
                    <th width="120">Booking No</th>
					<th width="120">Booking Date</th>
					<th width="100">Profit Center</th>
					<th width="100"> Department</th>
					<th width="100">Lead Time</th>
                    <th width="80">Type</th>
                    <th width="100">Fabric Source</th>
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th width="100">Can Bypass</th>
                    <th width="100">IP Address</th>
                    <th width="100">Approval Status</th>
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th width="80">Approve No</th>
                    <th>Remarks</th>
                </thead>
            </table>

			<div style="width:2620px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2620" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 

							// $sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status";

							// $data_array_po=sql_select($sql_po);
							// foreach ($data_array_po as $rows)
							// {
							// 	$daysInHand.=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
							// }


							$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
							$signatory_main=array_unique(explode(",",$signatory_main)); $rowspanMain=count($signatory_main);
							$signatory_short=array_unique(explode(",",$signatory_short)); $rowspanShort=count($signatory_short);
							$signatory_sample=array_unique(explode(",",$signatory_sample)); $rowspanSample=count($signatory_sample);



							
							$bypass_no_user_id_sample=explode(",",$bypass_no_user_id_sample);
							$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
							$bypass_no_user_id_short=explode(",",$bypass_no_user_id_short);
							
							// if($type==2) $approved_cond=" and a.is_approved=1"; else if($type==1) $approved_cond=" and a.is_approved=0";else  $approved_cond=" and a.is_approved=3";

							if($type==2) $approved_cond=" and a.is_approved=1"; else if($type==1) $approved_cond=" and a.is_approved=0"; else if($type==3) $approved_cond=" and a.is_approved=3";else  $approved_cond=" and a.is_approved in(0,1,3)";
							
							if ($db_type==0) 
							{
								$internal_ref="group_concat(c.grouping) as internal_ref";
								$job_no="group_concat(c.job_no_mst) as job_no";
							}
							else
							{
								$internal_ref="LISTAGG(CAST( c.grouping  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.grouping) as internal_ref";
								//$job_no="LISTAGG(CAST( c.job_no_mst  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.job_no_mst) as job_no";
								//$internal_ref="LISTAGG(c.grouping, ',') WITHIN GROUP (ORDER BY c.grouping) as internal_ref";
								$job_no="rtrim(xmlagg(xmlelement(e,c.job_no_mst,', ').extract('//text()') order by c.job_no_mst).getclobval(),', ')  as job_no";
							}
							if($ascending_by==2){
								//$app_date_cond=" and d.approved_date between $txt_date_from and $txt_date_to";

								$app_date_cond=" and d.approved_date between  $txt_date_from  and '".str_replace("'","",trim($txt_date_to)). " 11:59:59 PM'";

								$sql="SELECT max(d.APPROVED_DATE),a.id, a.company_id,a.profit_center,a.department_id, a.booking_no, a.entry_form,a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date,d.style_ref_no, $internal_ref, $job_no from wo_booking_mst a left join approval_history d on a.id=d.mst_id, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d	where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and c.job_no_mst=d.job_no and b.job_no=d.job_no  a.company_id=$cbo_company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0 $app_date_cond $approved_cond $buyer_id_cond $booking_cond $job_cond $internal_ref_cond  $date_cond $booking_type_cond $mst_id_con group by a.id, a.company_id,a.profit_center,a.department_id,a.entry_form, a.booking_no,d.style_ref_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date  order by max(d.APPROVED_DATE) desc
								";

							}else{
								$sql="SELECT a.ID, a.company_id,a.profit_center,a.department_id, a.booking_no, a.entry_form,a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date,d.style_ref_no, $internal_ref, $job_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d
								where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.company_id=$cbo_company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0 $buyer_id_cond $booking_cond $job_cond $internal_ref_cond $approved_cond $date_cond $booking_type_cond $mst_id_con group by a.id, a.company_id,a.profit_center,a.department_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source,d.style_ref_no, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date 
								order by a.insert_date desc";
							}
							
							 //echo $sql;die();
							
							$nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
									$row[csf('job_no')]= $row[csf('job_no')]->load();
									$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
									$job_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
									$booking_id_arr[$row['ID']]=$row['ID'];
							}


							$user_approval_array=array(); $user_ip_array=array(); $approved_no_array=array();
							$query="select entry_form, mst_id,  approved_by, user_ip,max(approved_no) as approved_no, max(approved_date) as approved_date from approval_history where entry_form in(7,12,13 ) $app_date_cond ".where_con_using_array($booking_id_arr,0,'mst_id')." group by entry_form, mst_id,  approved_by, user_ip"; // $app_date_cond  
							// echo $query;
							$result=sql_select( $query );
							foreach ($result as $row)
							{
								$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('approved_date')];
								$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('user_ip')];
					
								$approved_no_array[$row[csf('entry_form')]][$row[csf('mst_id')]]=$row[csf('approved_no')];
							}
					


								$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,id from wo_po_break_down  where status_active=1 ".where_con_using_array($job_arr,1,'job_no_mst')."  group by po_number,id";
								

								$data_array_po=sql_select($sql_po);
								foreach ($data_array_po as $rows)
								{
									$daysInHand=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1);
									$po_wise_lead_time[$rows[csf('id')]]['lead_time']=$daysInHand;
								}

									// echo "<pre>";
									// print_r($po_wise_lead_time);


                            //$nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$row[csf('job_no')]= $row[csf('job_no')]->load();
                            	$internal_ref_no= implode(",", array_unique(explode(",",$row[csf('internal_ref')])));

								$tempArr=array();
                            	foreach (explode(",",$row[csf('job_no')]) as $value) {
									$tempArr[trim($value)]=ltrim($value);
								}
								$job_no= implode(",", $tempArr);

								
								if($row[csf('booking_type')]==4) 
								{
									$tmpSigArr=array();
									foreach($signatory_sample as $uId){
										$buyer_ids_array=explode(",",$buyer_id_arr[13][$uId]);
										if($buyer_id_arr[13][$uId] =='' || in_array($row[csf('buyer_id')],$buyer_ids_array))
										{
											$tmpSigArr[] = $uId;
										}

									}
									$signatory = $tmpSigArr;
									
									
									$booking_type=3; 
									$booking_type_text="Sample";
									//$signatory=$signatory_sample;
									$rowspan=$rowspanSample;
									
									$full_approval=true;
									foreach($bypass_no_user_id_sample as $uId)
									{
										$buyer_ids=$buyer_id_arr[13][$uId];
										$buyer_ids_array=explode(",",$buyer_id_arr[13][$uId]);
										if($buyer_ids=="" || in_array($row[csf('buyer_id')],$buyer_ids_array))
										{
											$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[13][$row[csf('id')]]][$uId][13];
											if($approvedStatus=="")
											{
												$full_approval=false;
												break;
											}
										}
									}
								}
								else 
								{
									$tmpSigArr=array();
									foreach($signatory_short as $uId){
										$buyer_ids_array=explode(",",$buyer_id_arr[12][$uId]);
										if($buyer_id_arr[12][$uId] =='' || in_array($row[csf('buyer_id')],$buyer_ids_array))
										{
											$tmpSigArr[] = $uId;
										}

									}
									$signatory = $tmpSigArr;

									//print_r($signatory);
									
									
									
									
									$booking_type=$row[csf('is_short')];
									if($row[csf('is_short')]==1) 
									{
										$booking_type_text="Short";
										//$signatory=$signatory_short;
										$rowspan=$rowspanShort;
										
										$full_approval=true;
										foreach($bypass_no_user_id_short as $uId)
										{
											$buyer_ids=$buyer_id_arr[12][$uId];
											$buyer_ids_array=explode(",",$buyer_id_arr[12][$uId]);
											if($buyer_ids=="" || in_array($row[csf('buyer_id')],$buyer_ids_array))
											{
												$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[12][$row[csf('id')]]][$uId][12];
												if($approvedStatus=="")
												{
													$full_approval=false;
													break;
												}
											}
										}
									}
									else 
									{
										
										$tmpSigArr=array();
										foreach($signatory_main as $uId){
											$buyer_ids_array=explode(",",$buyer_id_arr[7][$uId]);
											if($buyer_id_arr[7][$uId] =='' || in_array($row[csf('buyer_id')],$buyer_ids_array))
											{
												$tmpSigArr[] = $uId;
											}

										}
										$signatory = $tmpSigArr;
										
										$booking_type_text="Main"; 
										//$signatory=$signatory_main;
										$rowspan=$rowspanMain;
										
										$full_approval=true;
										foreach($bypass_no_user_id_main as $uId)
										{
											$buyer_ids=$buyer_id_arr[7][$uId];
											$buyer_ids_array=explode(",",$buyer_id_arr[7][$uId]);
											if($buyer_ids=="" || in_array($row[csf('buyer_id')],$buyer_ids_array))
											{
												$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[7][$row[csf('id')]]][$uId][7];
												if($approvedStatus=="")
												{
													$full_approval=false;
													break;
												}
											}
										}
									}
								}
								
								if((($type==1 && $full_approval==false) || $row[csf('is_approved')]==0 ||  $row[csf('is_approved')]==1 ) ||$row[csf('is_approved')]==3|| ($type==2 && $full_approval==true))
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
									$po_id=explode(",",$row[csf('po_break_down_id')]);
									$po_no=''; $min_ship_date='';
									//echo $row[csf('booking_type')].'='.$row[csf('is_short')].'='.$row[csf('entry_form')].'m';
									foreach($po_id as $val)
									{
										if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];
										
										$ship_date=$po_array[$val]['ship_date'];
										
										if($min_ship_date=="")
										{
											$min_ship_date=$ship_date;
										}
										else
										{
											if($min_ship_date>$ship_date) $min_ship_date=$ship_date;
										}
									}
									 $title_hover='is_short='.$row[csf('is_short')].',booking_type='.$row[csf('booking_type')].',entry_form='.$row[csf('entry_form')];	 
										
									if($row[csf('is_short')]==2 and $row[csf('booking_type')]==1)
									{

										$print_booking=''; $print_booking2='';
										$fabric_nature=2;
										$row_id=$format_ids[0];
										

											 //echo "sure".$row_id;die;
											if($row_id==1)
											{ 
												
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==2)
											{ 
											 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											
											 }
											else if($row_id==3)
											{ 
											  $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";


											}
											else if($row_id==4)
											{ 
									
											  	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
										   	else if($row_id==5)
											{ 
											

											 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==6)
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											 	//$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
											}
										   	else if($row_id==7)
											{
												
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

											 	//$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										   	}
										   	else if($row_id==193)
										   	{
										   		$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
										   		$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print4','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										   	}
											else if($row_id==45) //Urmi //	
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}	
											else if($row_id==53) //JK 
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==93) //Libas
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											
												}
											else if($row_id==73)
											{ 
												

												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==85)
											{ 
												//$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}
											else if($row_id==143)
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

								
											}
											else if($row_id==220)
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

												
											}
											else if($row_id==160)
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row[csf('entry_form')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
										
											}	
											else if($row_id==269) //FOR KNIT ASIA
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
					
											if($variable=="") $variable="".$row[csf('booking_no')].""; 										
										
									}
									if($row[csf('entry_form')]==108){
										$print_booking='';$print_booking2=''; 
										foreach($print_report_format_part as $row_id)
										{ 
											
											if($row_id==85) //partial //	
											{ 
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											}	
											if($row_id==84) //partial //	
											{ 
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi_per_job','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											}	
											if($row_id==151) //partial //	
											{ 
												$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_advance_attire_ltd','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											} 									
												
										}
									}
									
									if($row[csf('is_short')]==1 and $row[csf('booking_type')]==1){
										$print_booking=''; $print_booking2='';	
										foreach($print_report_id_short_fabric_arr as $row_id)
										{
											if($row_id==46){//URMI Print Button;
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											}
											else
											{
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											
										}
									}
									else if($row[csf('booking_type')]==4){
										$print_booking=''; $print_booking2='';	
										
										$row_id=$print_report_id_sample_fabric_arr[0];
										$row_id1=$print_report_id_sample_fabric_arr[1];
												if($row_id1){
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id1."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												}
										
												if($row_id){
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}									
										
									}
									
									$booking_type_text="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$booking_type_text." <a/>";
									$z=0; 

									
									
									
									foreach($signatory as $val)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<?
										if($z==0)
										{
											if(str_replace("'","",trim($cbo_date_by))==1)
											{
												$date_all="B Date : ".change_date_format($row[csf('booking_date')]);
											}
											else if(str_replace("'","",trim($cbo_date_by))=='2')
											{
												$insert_date=$row[csf('insert_date')];
												$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
											}
										 ?>
											<td width="40" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
											<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p>
												<a href="javascript:generate_mkt_report('<?=$job_no; ?>','<?=$row[csf('booking_no')]; ?>','<?=$all_po_id; ?>','<?=$row[csf('item_category')]; ?>','<?=$job_ids; ?>','show_fabric_approval_report')">
													<? echo $job_no; ?>
												</a>
											</p></td>
											<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
											<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $internal_ref_no; ?></p></td>
											<td width="80" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                            <td width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $job_no;?>','img');">View</a></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $job_no;?>','file');">View</a></td>
											<td width="170" rowspan="<? echo $rowspan; ?>">
                                            	<p>
                                                	<?
														if($type==2)
														{
														?>
                                                			<a href='##' style='color:#000' onClick="generate_fabric_report2(<? echo $booking_type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $job_no; ?>','<? echo $row[csf('is_approved')]; ?>')"><? echo $po_no; ?></a>
                                                		<?
														}
														else
														{
															echo $po_no;
														}
													?>
                                                </p>
                                            </td>
                                            <td width="80" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $min_ship_date; ?></p></td>
											<td align="center" width="120" rowspan="<? echo $rowspan; ?>">
												<p>
												<? echo $row[csf('booking_no')];?>
                                               </p>
											</td>
											<td align="center" width="120" rowspan="<? echo $rowspan; ?>">
											<? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>
											</td>
											<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('profit_center')]; ?>&nbsp;</p></td>

											<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $department_arr[$row[csf('department_id')]]; ?>&nbsp;</p></td>

											<td width="100"  style="word-break: break-all"  rowspan="<? echo $rowspan; ?>" title="<?=$row[csf('po_break_down_id')];?>">
										
												<? 
													$po_arr=explode(",",$row[csf('po_break_down_id')]);	
													$lead_time="";
													foreach($po_arr as $pid){
															if($pid){
															$lead_time .=$po_wise_lead_time[$pid]['lead_time']."Days,";
															}
													}
													echo $lead_time;
												?>
                                               
											</td>
                                            <td width="80" rowspan="<? echo $rowspan; ?>" align="center" title='<?=$title_hover;?> <?=$row_id?>'>
                                            	<p>
                                                	<? 
														echo $booking_type_text;
													?>
                                                </p>
                                            </td>
                                             <td width="100" rowspan="<? echo $rowspan; ?>" align="center">
                                            	<p><? echo $print_booking; ?></p>
                                            </td>
                                            
										<?
										}
										
										$approved_no=''; $user_ip='';
										if($row[csf('booking_type')]==4)
										{
											$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[13][$row[csf('id')]]][$val][13];
											$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[13][$row[csf('id')]]][$val][13];
											if($approval_date!="") $approved_no=$approved_no_array[13][$row[csf('id')]];
										}
										else
										{
											if($row[csf('is_short')]==1) 
											{
												$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[12][$row[csf('id')]]][$val][12];
												$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[12][$row[csf('id')]]][$val][12];
												if($approval_date!="") $approved_no=$approved_no_array[12][$row[csf('id')]];
											}
											else
											{
												$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[7][$row[csf('id')]]][$val][7];
												$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[7][$row[csf('id')]]][$val][7];
												if($approval_date!="") $approved_no=$approved_no_array[7][$row[csf('id')]];
											}
										}
										
										//$approval_remarks=$approval_remarks_arr[$row[csf('id')]][$val][$approved_no];
										$approval_remarks=$approval_remarks_arr[$row[csf('id')]][$approved_no];
										$date=''; $time=''; 
										if($approval_date!="") 
										{
											$date=date("d-M-Y",strtotime($approval_date)); 
											$time=date("h:i:s A",strtotime($approval_date)); 
										}
										
										?>
											<td width="140"><p><? echo $user_name_array[$val]['full_name']." (".$user_name_array[$val]['name'].")"; ?>&nbsp;</p></td>
                                            <td width="130"><p><? echo $user_name_array[$val]['designation']; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><?=$yes_no[$signatory_main_bypass[$val]]?></td>
                                            <td width="100" align="center"><p><? echo $user_ip; ?>&nbsp;</p></td>
                                            <td width="100" align="center"><? if($row[csf('IS_APPROVED')]==1){
									       {echo " Approved";}
									    }
										else if($row[csf('IS_APPROVED')]==3){echo "Partial Approved";}
									    else {echo "Pending";}
										?>
										</td>
											<td width="100" align="center"><p><? if($row[csf('is_approved')]!=0) echo $date; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row[csf('is_approved')]!=0) echo $time; ?>&nbsp;</p></td>
											<td width="80"><p>&nbsp;
												<? 
                                                    if($row[csf('is_approved')]!=0) 
													{
														echo $approved_no;
														if($approved_no>0) echo getOrdinalSuffix($approved_no);
													}
                                                ?>
                                            &nbsp;</p></td>
                                            <td><p>&nbsp;<? if($row[csf('is_approved')]!=0) echo $approval_remarks; ?>&nbsp;</p></td>
										</tr>
										<?
										$z++;
									}
								$i++;
								}
							}
							?>
                    </tbody>
                </table>
			</div>
      	</fieldset>      
	<?
	}
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
 	
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td align="center"><img width="300px" height="180px" src="../../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

if($action=="show_fabric_approval_report")
{
	extract($_REQUEST);
	

	$txt_job_no=$job_no;
	$all_job_nostr="'".implode("','",explode(',',trim($job_no)))."'";
	
	 
	
	//$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	//if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	//if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	//if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	//if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	
 	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeArr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$supplierArr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$trimGroupArr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	$season_nameArr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", "id", "season_name");
	
	//foreach($all_job_noArr as $job_no )
	//{
		
	/*$costingPerid=return_field_value("costing_per", "wo_pre_cost_mst", "job_id in($job_ids)  and status_active=1 and is_deleted=0");
	
	$costingPerQty=12;
	if($costingPerid==1) $costingPerQty=12;
	elseif($costingPerid==2) $costingPerQty=1;	
	elseif($costingPerid==3) $costingPerQty=24;
	elseif($costingPerid==4) $costingPerQty=36;
	elseif($costingPerid==5) $costingPerQty=48;
	else $costingPerQty=12;*/
	//echo $gsm_weight_bottom.'DD';
	$gmtsitem_ratio_array=array();
	$grmnt_items = "";
    $grmts_sql = sql_select("select job_no,gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no in($all_job_nostr) ");
	foreach($grmts_sql as $key=>$val)
	{
		$grmnt_itemsArr[$val[csf('job_no')]] .=$garments_item[$val[csf("gmts_item_id")]].'::'.$val[csf("set_item_ratio")].",";
		$gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];	
	}
	
	
	 $sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.avg_unit_price, b.id, b.po_number, b.pub_shipment_date, c.country_id, c.item_number_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty, c.pack_qty,b.details_remarks,a.season_buyer_wise,c.article_number from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.job_id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no in($all_job_nostr) $poidCond order by c.size_order ASC";
	 
	   
	 
	

	$data_array=sql_select($sql); $poColorSizeArr=array(); $jobSizeArr=array(); $poItemColorSizeArr=array(); $poCountryItemColorSizeArr=array(); $orderNo=""; $poQtyPcs=0; $packQty=0;
	foreach($data_array as $row)
	{
		$PoNoArr[$row[csf("id")]]=$row[csf("po_number")];
		$jobSizeArr[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
		$poColorSizeArr[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poColorSizeArr[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['planqty']+=$row[csf("plan_cut_qnty")];
		
		
		
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['planqty']+=$row[csf("plan_cut_qnty")];
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['article_number']=$row[csf("article_number")];
		$poCountryItemColorSizeArr[$row[csf("id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poArr[$row[csf("job_no")]].=$row[csf("po_number")].',';
		$JobStyleArr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
		$JobStyleArr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$poQtyPcsArr[$row[csf("job_no")]]+=$row[csf("order_quantity")];
		$packQtyArr[$row[csf("job_no")]]+=$row[csf("pack_qty")];
		$company_name=$row[csf("company_name")];

		$po_wise_remarks[$row[csf("id")]]['details_remarks']=$row[csf("details_remarks")];
		$po_wise_ship_date[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
		$season_buyer_wise=$season_nameArr[$row[csf("season_buyer_wise")]];
	}
	//$styleref=$data_array[0][csf('style_ref_no')];
	//$buyerid=$data_array[0][csf('buyer_name')];
	//$styleref=$data_array[0][csf('style_ref_no')];
	
	unset($data_array);
	//$countSize=count($jobSizeArr);
	//$colorsizetablewtd=450+($countSize*60);
	if ($zero_value==1) $exclucolor="#FFFF00"; else $exclucolor="";
	
	
			$sqlContrast="Select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where  job_no in($all_job_nostr)  and status_active=1 and is_deleted=0";
			$sqlContrastRes=sql_select($sqlContrast); $contrastColorArr=array();
			foreach($sqlContrastRes as $crow)
			{
				$contrastColorArr[$crow[csf('pre_cost_fabric_cost_dtls_id')]][$crow[csf('gmts_color_id')]]=$crow[csf('contrast_color_id')];
			}
			unset($sqlContrastRes);
			 $sqlfab="select b.id as avg_dtls_id,a.id, a.job_no, a.item_number_id,a.costing_per, a.body_part_id,a.color_type_id, a.lib_yarn_count_deter_id, a.fabric_description, a.gsm_weight, a.nominated_supp_multi, a.budget_on, a.color_size_sensitive, a.uom, b.po_break_down_id, b.color_number_id, b.gmts_sizes, b.cons, b.cons_pcs, b.remarks, b.requirment,a.fabric_source,b.dia_width,b.gmts_sizes from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and b.cons>0 and b.cons_pcs>0 and a.job_no in($all_job_nostr) $poidFabCond";

			
			$sqlfabRes=sql_select($sqlfab); $fabricGmtsFabricColorArr=array();
			foreach($sqlfabRes as $frow)
			{
				$poQty=$set_item_ratio=$rowReqQtyPcs=$rowReqPlanQtyPcs=$planQty=0;
				$set_item_ratio=$gmtsitem_ratio_array[$frow[csf('job_no')]][$frow[csf('item_number_id')]];
				if($frow[csf("budget_on")]==2)//Plan
					$poQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['planqty'];
				else
					$poQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['poqty'];
				$planQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['planqty'];	
				//$rowReqQtyPcs=($poQty/$set_item_ratio)*($frow[csf("cons")]/$costingPerQty);
				$cons=0;
				//if ($zero_value==1) $cons=$frow[csf("cons_pcs")]/12; else $cons=$frow[csf("requirment")];
				//$rowReqQtyPcs=($poQty/$set_item_ratio)*($cons/$costingPerQty);
				$costingPerid=$frow[csf("costing_per")];
				$article_number=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['article_number'];
				
				$costingPerQty=12;
				if($costingPerid==1) $costingPerQty=12;
				elseif($costingPerid==2) $costingPerQty=1;	
				elseif($costingPerid==3) $costingPerQty=24;
				elseif($costingPerid==4) $costingPerQty=36;
				elseif($costingPerid==5) $costingPerQty=48;
				else $costingPerQty=12;
				
				$cons=$frow[csf("cons_pcs")]/12;
				$rowReqQtyPcs=($poQty)*($cons/$costingPerQty);
				$rowReqPlanQtyPcs=($planQty)*($cons/$costingPerQty);
				$str=""; 
				$str=$frow[csf("id")].'_'.$frow[csf("body_part_id")].'_'.$frow[csf("item_number_id")].'_'.$frow[csf("nominated_supp_multi")].'_'.$frow[csf("gsm_weight")].'_'.$frow[csf("uom")].'_'.$frow[csf("fabric_description")].'_'.$frow[csf("color_type_id")].'_'.$frow[csf("dia_width")].'_'.$frow[csf("gmts_sizes")].'_'.$frow[csf("po_break_down_id")].'_'.$frow[csf("avg_dtls_id")];
				if($frow[csf("color_size_sensitive")]==3)
				{
					$fabriccolor=$contrastColorArr[$frow[csf("id")]][$frow[csf("color_number_id")]];
				}
				else $fabriccolor=$frow[csf("color_number_id")];
				$job_no=$frow[csf('job_no')];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['req']+=$rowReqQtyPcs;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['fin_dzn_cons']=$frow[csf("cons_pcs")];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['reqPlan']+=$rowReqPlanQtyPcs;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['po']+=$poQty;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['article_number']=$article_number;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['remarks']=$frow[csf("remarks")];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['fabric_source']=$frow[csf("fabric_source")];
			}
			unset($sqlfabRes);
			//if ($zero_value==1) $captd="Total Req. Qty"; else $captd="Qty Incl. Allow.";
			if ($zero_value==1) 
			{
				$dispexacc="display:none";
				$acccolspn=2;
			}
			else 
			{
				$dispexacc="";
				$acccolspn=4; 
			}
			$captd="GMT Size";
			
			foreach($fabricGmtsFabricColorArr as $job_no=>$fabricGmtsFabricArr)
			{
				$grmnt_items='';
				$grmnt_itemsJob=$grmnt_itemsArr[$job_no];
				$grmnt_items = rtrim($grmnt_itemsJob,",");
				$poArrAll=rtrim($poArr[$job_no],',');
				$orderNos=implode(",",array_unique(explode(",",$poArrAll)));
				$JobStyle=$JobStyleArr[$job_no]['style'];
				$buyerid=$JobStyleArr[$job_no]['buyer_name'];
				//$orderNo=implode(",",$poArr);
			$img_path="../../../";	
		?>
 <div style="width:972px; margin:0 auto">
	 
    <div style="width:972px; margin:0 auto">
        <div style="width:970px; font-size:20px; font-weight:bold" align="center"><b style="float:left"><img src='<?=$img_path.$imge_arr[$company_name]; ?>' height='40px' width='100px' /></b><?=$comp[str_replace("'","",$company_name)]; ?><b style="float:right; font-size:14px; font-weight:bold"><?='&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';?> </b></div>
        <div style="width:970px; font-size:18px; font-weight:bold" align="center"><b style="float:left"></b>Bill Of Materials [BOM] Report For Style Ref. : <?=$JobStyle.' ['.str_replace("'","",$job_no).'] '; if ($zero_value==1) echo "[Total Req. Qty]"; else echo "[Qty Inclu. Allowance]"; ?></div>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px" rules="all">
            <tr>
                <td width="100px">Job No:</td>
                <td width="100px"><b><?=$job_no; ?></b></td>
                <td width="100px">Buyer:</td>
                <td width="120px" style="word-break:break-all"><b><?=$buyer_arr[$buyerid]; ?></b></td>
                <td width="100px">Garments Item:</td>
                <td style="word-break:break-all"><b><?=$grmnt_items; ?></b></td>
            </tr>
            <tr>
            	<td width="100px">PO No:</td>
                <td style="word-break:break-all" colspan="5"><b><?=$orderNos; ?></b></td>
            </tr>
            <tr> 
                <td>PO Qty.:</td>
                <td colspan="3"><b><?=fn_number_format($poQtyPcsArr[$job_no],0).'-[PCS]; '.fn_number_format(($poQtyPcsArr[$job_no]/12),2).'-[DZN]; '.fn_number_format($packQtyArr[$job_no],0).'-[Pack];'; ?></b></td>
				<td>Season:</td>
				<td><?=$season_buyer_wise;?></td>
            </tr>
        </table>
        <br>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1050px" rules="all">
        	<thead>
            	<tr>
                	<th colspan="10"><b>Fabric Details</b></th>
                </tr>
                <tr>
                	<th width="130">Gmts. Color</th>
                    <th width="130">Fabric Color</th>
                    <th width="130">Body Part</th>
                  
                    <th width="80">Color Type</th>
                    <th width="80">Fabric Source</th>
                    <th width="80">PO NO</th>
                    <th width="80">Dia</th>
					<th width="80">Article No</th>
                    <th width="80"><?=$captd; ?></th>
                    <th width="80" bgcolor="<?=$exclucolor; ?>">Fin Cons.[Dzn]</th>
					
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;$fin_dzn_consTotal=0;$str_array=array();
			foreach($fabricGmtsFabricArr as $strval=>$strdata)
			{
				$fabricTotal=0;
				foreach($strdata as $gmtcolor=>$gmtdata)
				{
					foreach($gmtdata as $fabriccolor=>$fabricdata)
					{
						 $exstr=explode("_",$strval);
						 
						 $nomisupp="";
						 if($exstr[3]!="")
						 {
						 	$exsupp=explode("_",$exstr[3]);
						 	foreach($exsupp as $supid)
							{
								if($nomisupp=="") $nomisupp=$supplierArr[$supid]; else $nomisupp.=', '.$supplierArr[$supid];
							}
						 }
						 $color_typeId=$exstr[7];
						 $fab_dia=$exstr[8];
						 $gmt_size=$exstr[9];
						 $po_id=$exstr[10];
						 $avg_dtlsId=$exstr[11];
						  $fab_id=$exstr[0];
						 						 
						 if (!in_array($fab_id,$str_array) )
						 {
							?>
                            <tr bgcolor="#FFFFFF">
                                <td style="word-break:break-all"><?=$garments_item[$exstr[2]]; ?></td>
                                <td style="word-break:break-all" colspan="6"><?=$exstr[6].', '.$exstr[4].' GSM; '.$color_type[$exstr[7]].' UOM: '.$unit_of_measurement[$exstr[5]]; ?></td>
                                <td style="word-break:break-all" colspan="2"><?=$nomisupp; ?></td>
                            </tr>
                            <?
							$str_array[]=$fab_id;            
                        	$i++; 
						 }
						 $poqtyDzn=($fabricdata['po']/12);
						 $reqqtyDzn=($fabricdata['req']/$fabricdata['po'])*12;
						 $fin_dzn_cons=$fabricdata['fin_dzn_cons'];
						// $reqPlanQty=$fabricdata['reqPlan'];
						 //$fabricTotal+=$reqQty;
						 $fin_dzn_consTotal+=$fin_dzn_cons;
						 ?>
                         <tr>
                            <td style="word-break:break-all"><?=$colorArr[$gmtcolor]; ?></td>
                            <td style="word-break:break-all"><?=$colorArr[$fabriccolor]; ?></td>
                            <td style="word-break:break-all"><?=$body_part[$exstr[1]]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$color_type[$color_typeId]; ?></td>
                            <td style="word-break:break-all" title="poNo=<?=$PoNoArr[$po_id]; ?>" align="center"><?=$fabric_source[$fabricdata['fabric_source']]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$PoNoArr[$po_id]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$fab_dia; ?></td>
							<td style="word-break:break-all" align="center"><?=$fabricdata['article_number']; ?></td>
                            <td style="word-break:break-all" align="center"><?=$sizeArr[$gmt_size]; ?></td>
                            <td style="word-break:break-all" align="center" bgcolor="<?=$exclucolor; ?>"><?=fn_number_format($fin_dzn_cons,5); ?></td>
							
                        </tr>
                        <?
						//$i++;
					}
				}
				?>
                <!--<tr bgcolor="#CCCCCC">
                    <td colspan="7" align="right">Sub Total=</td>
                    <td style="word-break:break-all" align="center"><? //fn_number_format($fin_dzn_consTotal,2); ?></td>
					
                </tr>-->
                <?
			}
			//echo $i;
			?>
            </tbody>
        </table>
        <br>
       
     <? //signature_table(237, $cbo_company_name, "970px"); ?>
     </div>
     
	 <?
	} //Job End
	?>
    <br>
         <?

		 $lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$booking_no' and b.entry_form=7 order by b.id asc");
	 
	// echo "select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$booking_no' and b.entry_form=7 order by b.id asc";

 	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="4" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor;?>">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                <td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td>
                <td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
    </div>
    <?
	
	 disconnect($con);
	 exit();
}


if($action=="file")
{
	echo load_html_head_contents("File View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

function getOrdinalSuffix($number) 
{
    $number = abs($number);
    $lastChar = substr($number, -1, 1);
    switch ($lastChar) {
        case '1' : return ($number == '11') ? 'th' : 'st';
        case '2' : return ($number == '12') ? 'th' : 'nd';
        case '3' : return ($number == '13') ? 'th' : 'rd'; 
    }
    return 'th';  
} 

?>