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
                    <th>Search By</th>
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
	
	if(str_replace("'","",trim($txt_app_date))!="" )
	{
		if($db_type==0){
			$app_date_cond=" and approved_date between $txt_app_date and $txt_app_date";
		}
		else
		{
			$app_date_cond=" and approved_date between  $txt_app_date  and '".str_replace("'","",trim($txt_app_date)). " 11:59:59 PM'";
		}
	}
	else
	{
		$app_date_cond="";
	}
	
	
	
	
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
		$approved_no_array=array();
		$queryApp="select entry_form, mst_id, max(approved_no) as approved_no from approval_history where entry_form in(7,12,13) group by entry_form, mst_id";
		$resultApp=sql_select( $queryApp );
        foreach ($resultApp as $row)
		{
			$approved_no_array[$row[csf('entry_form')]][$row[csf('mst_id')]]=$row[csf('approved_no')];
		}
		
		$buyer_id_arr=array();
		$buyerData=sql_select("select entry_form, user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form in(7,12,13) and bypass=2");
		foreach($buyerData as $row)
		{
			$buyer_id_arr[$row[csf('entry_form')]][$row[csf('user_id')]]=$row[csf('buyer_id')];
		}
		//print_r($buyer_id_arr[12]);
		
		if($db_type==0)
		{
			$signatory_data_arr=sql_select("select group_concat(case when entry_form=7 then user_id end) as user_idm, group_concat(case when entry_form=12 then user_id end) as user_ids, group_concat(case when entry_form=13 then user_id end) as user_idsm, group_concat(case when entry_form=7 and bypass=2 then user_id end) as user_idmby, group_concat(case when entry_form=12 and bypass=2 then user_id end) as user_idsby, group_concat(case when entry_form=13 and bypass=2 then user_id end) as user_idsmby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 order by sequence_no");
		}
		else
		{
			$signatory_data_arr=sql_select("select LISTAGG(case when entry_form=7 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idm, LISTAGG(case when entry_form=12 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_ids, LISTAGG(case when entry_form=13 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsm, LISTAGG(case when entry_form=7 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idmby, LISTAGG(case when entry_form=12 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsby, LISTAGG(case when entry_form=13 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsmby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 ORDER BY sequence_no");	
		}
		
		$signatory_main=$signatory_data_arr[0][csf('user_idm')];
		$signatory_short=$signatory_data_arr[0][csf('user_ids')];
		$signatory_sample=$signatory_data_arr[0][csf('user_idsm')];
		
		$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idmby')];
		$bypass_no_user_id_short=$signatory_data_arr[0][csf('user_idsby')];
		$bypass_no_user_id_sample=$signatory_data_arr[0][csf('user_idsmby')];
		
		//$last_user_id=return_field_value("max(user_id) as user_id", "electronic_approval_setup", "entry_form=7 and is_deleted=0","user_id" );
		
		$user_approval_array=array(); $user_ip_array=array(); 
		$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form in(7,12,13)"; // $app_date_cond  echo $query;
		$result=sql_select( $query );
        foreach ($result as $row)
		{
			//$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
			$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('approved_date')];
			$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('user_ip')];
		}
		
		if($app_date_cond!=''){
			$mst_id_arr=return_library_array( "select mst_id, mst_id from approval_history where entry_form in(7,12,13) $app_date_cond","mst_id","mst_id");
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
        <fieldset style="width:1720px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1800" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Job No</th>
                    <th width="80">Buyer Name</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="170">Order No</th>
                    <th width="80">Shipment Date (Min)</th>
                    <th width="120">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Fabric Source</th>
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th width="100">IP Address</th>
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th width="80">Approve No</th>
                    <th>Remarks</th>
                </thead>
            </table>

			<div style="width:1800px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
							$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
							$signatory_main=array_unique(explode(",",$signatory_main)); $rowspanMain=count($signatory_main);
							//$signatory_main=array_unique(explode(",",$signatory_main)); $rowspanMain=count($signatory_main);
							$signatory_short=array_unique(explode(",",$signatory_short)); $rowspanShort=count($signatory_short);
							$signatory_sample=array_unique(explode(",",$signatory_sample)); $rowspanSample=count($signatory_sample);
							
							$bypass_no_user_id_sample=explode(",",$bypass_no_user_id_sample);
							$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
							$bypass_no_user_id_short=explode(",",$bypass_no_user_id_short);
							
							if($type==2) $approved_cond=" and a.is_approved=1"; else $approved_cond="";
							
							$sql="select a.id, a.company_id, a.booking_no, a.entry_form,a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0 $buyer_id_cond $booking_cond $job_cond $approved_cond $date_cond $booking_type_cond $mst_id_con group by a.id, a.company_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date order by a.insert_date desc";
							//echo $sql;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$full_approval='';
								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type=3; 
									$booking_type_text="Sample";
									$signatory=$signatory_sample;
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
									$booking_type=$row[csf('is_short')];
									if($row[csf('is_short')]==1) 
									{
										$booking_type_text="Short";
										$signatory=$signatory_short;
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
										$booking_type_text="Main"; 
										$signatory=$signatory_main;
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
								
								if((($type==1 && $full_approval==false) || $row[csf('is_approved')]==0) || ($type==2 && $full_approval==true))
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
									 //echo $row[csf('is_short')].'='.$row[csf('booking_type')].',';	 
										
									if($row[csf('is_short')]==2 and $row[csf('booking_type')]==1){

										$print_booking=''; $print_booking2='';
										$fabric_nature=2;
										$row_id=$format_ids[0];
										

											//echo "sure".$row_id;die;
											if($row_id==1)
											{ 
												
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==2)
											{ 
											 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											
											 }
											else if($row_id==3)
											{ 
											 
											 // $variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";

											  $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";


											}
											else if($row_id==4)
											{ 
											 	//$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\">" .$row[csf('booking_no_prefix_num')]. "<a/>";

											  	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
										   	else if($row_id==5)
											{ 
											

											 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											else if($row_id==6)
											{ 
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											 	//$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
											}
										   	else if($row_id==7)
											{
												 echo	 $print_booking."***".$row_id;die;
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											 	
											  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

											 	//$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										   	}
											 if($row_id==45) //Urmi //	
												{ 
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												  
												  $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}	
												if($row_id==53) //JK 
												{ 
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}
											   if($row_id==93) //Libas
												{ 
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												
												 }
												else if($row_id==73)
												{ 
												 

												 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}
											    else if($row_id==85)
												{ 
												 	//$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

												 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												 }
												else if($row_id==143)
												{ 
													$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

									
												 }
												else if($row_id==220)
												{ 
													$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";

												 	
												 }
												 else if($row_id==160)
												{ 
													$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row[csf('entry_form')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												   $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('entry_form')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											
												 }
												if($variable=="") $variable="".$row[csf('booking_no')].""; 										
										
									}
									if($row[csf('entry_form')]==108){
										$print_booking='';$print_booking2=''; 
										foreach($print_report_format_part as $row_id)
										{ 
											
											 if($row_id==85) //partial //	
												{ 
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												}	
												if($row_id==84) //partial //	
												{ 
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi_per_job','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												}	
												if($row_id==151) //partial //	
												{ 
												 	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_advance_attire_ltd','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												} 									
												
										}
									}
									
									if($row[csf('is_short')]==1 and $row[csf('booking_type')]==1){
										$print_booking=''; $print_booking2='';	
										foreach($print_report_id_short_fabric_arr as $row_id)
										{
											if($row_id==46){//URMI Print Button;
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
											}
											else
											{
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
											}
											
										}
									}
									
									if($row[csf('booking_type')]==4){
										$print_booking=''; $print_booking2='';	
										
										$row_id=$print_report_id_sample_fabric_arr[0];
										$row_id1=$print_report_id_sample_fabric_arr[1];
												if($row_id1){
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id1."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
												}
										
												if($row_id){
												 $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
												}									
										
									}
	
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
											<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
											<td width="80" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                            <td width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','file');">View</a></td>
											<td width="170" rowspan="<? echo $rowspan; ?>">
                                            	<p>
                                                	<?
														if($type==2)
														{
														?>
                                                			<a href='##' style='color:#000' onClick="generate_fabric_report2(<? echo $booking_type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>')"><? echo $po_no; ?></a>
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
											<td width="120" rowspan="<? echo $rowspan; ?>">
												<p>
                                                	<? echo rtrim($print_booking2,', ')."<br>".$date_all; ?>
                                               </p>
											</td>
                                            <td width="80" rowspan="<? echo $rowspan; ?>" align="center">
                                            	<p>
                                                	<? 
														echo $booking_type_text;
													?>
                                                </p>
                                            </td>
                                             <td width="100" rowspan="<? echo $rowspan; ?>" align="center">
                                            	<p> 
                                                	<?
														echo $print_booking;
													?>
                                                  
                                                </p>
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
                                            <td width="100" align="center"><p><? echo $user_ip; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row[csf('is_approved')]!=0) echo $date;//."=".$row[csf('id')];// print_r($user_approval_array[$row[csf('id')]]); ?>&nbsp;</p></td>
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