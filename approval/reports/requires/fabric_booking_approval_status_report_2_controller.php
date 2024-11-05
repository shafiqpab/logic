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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'fabric_booking_approval_status_report_2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		
		$sql= "select id, booking_no, booking_no_prefix_num, job_no, company_id, buyer_id from wo_booking_mst where status_active=1 and is_deleted=0 and company_id=$company_id and $search_field like '$search_string' and item_category in(2,3,13) $buyer_id_cond order by booking_no";
			
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
		
		if($booking_type==1) $booking_type_cond=" and a.booking_type=1 and a.is_short=2 ";
		else if($booking_type==2) $booking_type_cond=" and a.booking_type=1 and a.is_short=1 "; 
		else if($booking_type==3) $booking_type_cond=" and a.booking_type=4  "; 
		else if($booking_type==4) $booking_type_cond=" and a.booking_type=1 and a.is_short=2 and a.entry_form=108";
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
		$userData=sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
			$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
			$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
		}		

		$print_report_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");	 
		$print_report_id_short_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");		
		$print_report_id_sample_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
		$print_report_format_par=return_field_value("format_id"," lib_report_template","template_name =".$cbo_company_name."  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	
	    $print_report_format_part=explode(",",$print_report_format_par);
		$format_ids=explode(",",$print_report_ids);
		$print_report_id_short_fabric_arr=explode(",",$print_report_id_short_fabric);
		$print_report_id_sample_fabric_arr=explode(",",$print_report_id_sample_fabric);

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
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table" >
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
                    <? if ($type == 1) { ?>
                        <th width="140">Submitted By</th>
                    <? } else { ?>
                        <th width="140">Approved By</th>
                    <? } ?>    
                    <th width="130">Designation</th>
                    <? if ($type == 1) { ?>
                    	<th width="140" style="word-break: break-all;">Submission Date & Time</th>
                    	<th width="140" style="word-break: break-all;">Not Appv. Cause</th>
                    	<th>Remarks</th>
                    <? } else { ?>
                    	<th width="140" style="word-break: break-all;">App Date & Time</th>
                    	<th width="140" style="word-break: break-all;">Not Un-Appv. Cause</th>
                    	<th style="word-break: break-all;">Un-approve Request Reason</th>
                    <? } ?>
                </thead>
            </table>

			<div style="width:1700px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
							$i=1; 
							if ($type == 1)
							{
								$sql="SELECT a.id, a.company_id, a.booking_no, a.entry_form, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date, a.update_date, a.inserted_by, b.entry_form as app_entry_form
								from wo_booking_mst a left join approval_history b on (a.id=b.mst_id and b.entry_form in (7,12,13))
								where a.company_id=$cbo_company_name and a.item_category in(2,3,13)  and a.is_approved IN(0,2) and a.status_active=1 and a.is_deleted=0  $buyer_id_cond $booking_cond $job_cond $date_cond $booking_type_cond  
								group by a.id, a.company_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date, a.update_date, a.inserted_by, b.entry_form
								order by a.insert_date desc";
							}
							else
							{
								$sql="SELECT a.id, a.company_id, a.booking_no, a.entry_form, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.booking_date, b.inserted_by, b.approved_date, b.entry_form as app_entry_form
								from wo_booking_mst a, approval_history b 
								where a.id=b.mst_id and b.entry_form in (7,12,13) and b.current_approval_status=1 and a.is_approved=1 and a.company_id=$cbo_company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1  $buyer_id_cond $booking_cond $job_cond $date_cond $booking_type_cond 
								group by a.id, a.company_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.booking_date, b.inserted_by, b.approved_date, b.entry_form 
								order by b.approved_date desc";
							}
							//echo $sql;die;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type=3; 
									$booking_type_text="Sample";
								}elseif($row[csf('booking_type')]==1 && $row[csf('is_short')]==2 && $row[csf('entry_form')]==108){
									$booking_type_text="Partial";
								}
								else 
								{
									$booking_type=$row[csf('is_short')];
									if($row[csf('is_short')]==1) 
									{
										$booking_type_text="Short";
										
									}
									else 
									{
										$booking_type_text="Main"; 
										
									}
								}

								
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
								if($row[csf('is_short')]==2 and $row[csf('booking_type')]==1)
								{
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
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									 	
									  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
									else if($row_id==4)
									{ 
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
									}
								   	else if($row_id==7)
									{
										echo $print_booking."***".$row_id;die;
										$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									 	
									  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
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
									else if($row_id==78)
									{
									 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
								    else if($row_id==85)
									{ 
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
									else if($row_id==269) //FOR KNIT ASIA
									{ 
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
									if($variable=="") $variable="".$row[csf('booking_no')]."";									
								}

								if($row[csf('entry_form')]==108)
								{
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
									
								if($row[csf('is_short')]==1 and $row[csf('booking_type')]==1)
								{
									$print_booking=''; $print_booking2='';	
									foreach($print_report_id_short_fabric_arr as $row_id)
									{
										if($row_id==46)//URMI Print Button;
										{   
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}
										else
										{
											$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
										}												
									}
								}
									
								if($row[csf('booking_type')]==4)
								{
									$print_booking=''; $print_booking2='';										
									$row_id=$print_report_id_sample_fabric_arr[0];
									$row_id1=$print_report_id_sample_fabric_arr[1];
									if($row_id1)
									{
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id1."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									}
							
									if($row_id)
									{
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}										
								}
							    ?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								    <?
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
									<td width="40"><? echo $i; ?></td>
									<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                    <td width="110"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','file');">View</a></td>
									<td width="170"><p>
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
                                    </p></td>
                                    <td width="80" align="center"><p><? echo $min_ship_date; ?></p></td>
									<td width="120"><p><? echo rtrim($print_booking2,', ')."<br>".$date_all; ?></p></td>
                                    <td width="80" align="center"><p><? echo $booking_type_text; ?></p></td>
                                    <td width="100" align="center"><p><? echo $print_booking; ?></p></td>
                                    <td width="140">
                                    	<p><? echo $user_name_array[$row[csf('inserted_by')]]['full_name']." (".$user_name_array[$row[csf('inserted_by')]]['name'].")";?></p>
                                    </td>
                                    <td width="130">
                                    	<p><? echo $user_name_array[$row[csf('inserted_by')]]['designation'];?></p>
                                    </td>
                                    <td width="140" align="center"><p>
                                    	<? 
                                    		if ($type == 1){
                                    			echo date("d-M-Y h:i:s A",strtotime($row[csf('insert_date')]));
                                    		} else {
                                    			echo date("d-M-Y h:i:s A",strtotime($row[csf('approved_date')]));
                                    		} 
                                    	?>
                                    </p></td>
									<td width="140" align="center"><p>
										<? 
											//$usr_id=$row[csf("approved_by")];
											if ($type == 1)
											{
												$app_cond=" and b.approval_type=0";
											}
											else
											{
												$app_cond=" and b.approval_type=1";
											}	
											$user_id=$row[csf("inserted_by")];
                                    		$sql_reason="select max(b.id) as id, b.user_id, b.approval_cause 
                                    			from fabric_booking_approval_cause b 
                                    			where b.entry_form in(7,12,13) and b.user_id='$user_id' and b.booking_id=".$row[csf("id")]." $app_cond and b.status_active=1 and b.is_deleted=0 
                                    			group by b.user_id, b.approval_cause 
                                    			order by id desc";
                                    		$sql_rslt = sql_select($sql_reason);
                                    		echo $sql_rslt[0][csf('approval_cause')];
										?>
									</p></td>
                                    <td><p>
                                    	<? 
                                    		$user_id=$row[csf("inserted_by")];
                                    		$sql_request="select MAX(id) as id, approval_cause from fabric_booking_approval_cause where entry_form in(7,12,13) and user_id='$user_id' and booking_id=".$row[csf("id")]." and approval_type=2 and status_active=1 and is_deleted=0 group by approval_cause order by id desc";
                                    		$sql_result = sql_select($sql_request);
                                    		if ($type == 1){
                                    			echo '';
                                    		} else {
                                    			echo $sql_result[0][csf('approval_cause')];
                                    		} 
                                    	?>                                    		
                                    </p></td>
								</tr>
							    <?
								$i++;								
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
if($action=="report_generate2")
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
		
		if($booking_type==1) $booking_type_cond=" and a.booking_type=1 and a.is_short=2 ";
		else if($booking_type==2) $booking_type_cond=" and a.booking_type=1 and a.is_short=1 "; 
		else if($booking_type==3) $booking_type_cond=" and a.booking_type=4  "; 
		else if($booking_type==4) $booking_type_cond=" and a.booking_type=1 and a.is_short=2 and a.entry_form=108";
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
		$job_insert_date_arr= return_library_array("select job_no, insert_date from wo_po_details_master","job_no","insert_date");
		
		$po_array=array();
		$poData=sql_select( "select id, po_number, pub_shipment_date,insert_date,grouping from wo_po_break_down");
		foreach($poData as $po_row)
		{
			$po_array[$po_row[csf('id')]]['no']=$po_row[csf('po_number')];
			$po_array[$po_row[csf('id')]]['ship_date']=change_date_format($po_row[csf('pub_shipment_date')]);
			$po_array[$po_row[csf('id')]]['insert_date']=change_date_format($po_row[csf('insert_date')]);
			$po_array[$po_row[csf('id')]]['internal_ref']=$po_row[csf('grouping')];
		}
		
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
		
		$user_name_array=array();
		$userData=sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
			$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
			$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
		}		

		$print_report_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");	 
		$print_report_id_short_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");		
		$print_report_id_sample_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
		$print_report_format_par=return_field_value("format_id"," lib_report_template","template_name =".$cbo_company_name."  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	
	    $print_report_format_part=explode(",",$print_report_format_par);
		$format_ids=explode(",",$print_report_ids);
		$print_report_id_short_fabric_arr=explode(",",$print_report_id_short_fabric);
		$print_report_id_sample_fabric_arr=explode(",",$print_report_id_sample_fabric);

		ob_start();
		?>
        <fieldset style="width:2020px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Job No</th>
                    <th width="80">Buyer Name</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="170">Order No</th>
                    <th width="80">Shipment Date (Min)</th>
					<th width="100">Internal Ref</th>
					<th width="100">Order Insert Date</th>
                    <th width="120">Booking No</th>
					<th width="100">Day Count</th>
                    <th width="80">Type</th>
                    <th width="100">Fabric Source</th>
                    <? if ($type == 1) { ?>
                        <th width="140">Submitted By</th>
                    <? } else { ?>
                        <th width="140">Approved By</th>
                    <? } ?>    
                    <th width="130">Designation</th>
                    <? if ($type == 1) { ?>
                    	<th width="140" style="word-break: break-all;">Submission Date & Time</th>
                    	<th width="140" style="word-break: break-all;">Not Appv. Cause</th>
                    	<th>Remarks</th>
                    <? } else { ?>
                    	<th width="140" style="word-break: break-all;">App Date & Time</th>
                    	<th width="140" style="word-break: break-all;">Not Un-Appv. Cause</th>
                    	<th style="word-break: break-all;">Un-approve Request Reason</th>
                    <? } ?>
                </thead>
            </table>

			<div style="width:2000px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1980" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
							$i=1; 
							if ($type == 1)
							{
								$sql="SELECT a.id, a.company_id, a.booking_no, a.entry_form, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date, a.update_date, a.inserted_by, b.entry_form as app_entry_form
								from wo_booking_mst a left join approval_history b on (a.id=b.mst_id and b.entry_form in (7,12,13))
								where a.company_id=$cbo_company_name and a.item_category in(2,3,13)  and a.is_approved IN(0,2) and a.status_active=1 and a.is_deleted=0  $buyer_id_cond $booking_cond $job_cond $date_cond $booking_type_cond  
								group by a.id, a.company_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.insert_date, a.booking_date, a.update_date, a.inserted_by, b.entry_form
								order by a.insert_date desc";
							}
							else
							{
								$sql="SELECT a.id, a.company_id, a.booking_no, a.entry_form, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.booking_date, b.inserted_by, b.approved_date, b.entry_form as app_entry_form
								from wo_booking_mst a, approval_history b 
								where a.id=b.mst_id and b.entry_form in (7,12,13) and b.current_approval_status=1 and a.is_approved=1 and a.company_id=$cbo_company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1  $buyer_id_cond $booking_cond $job_cond $date_cond $booking_type_cond 
								group by a.id, a.company_id,a.entry_form, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.is_approved, a.booking_date, b.inserted_by, b.approved_date, b.entry_form 
								order by b.approved_date desc";
							}
							//echo $sql;die;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type=3; 
									$booking_type_text="Sample";
								}elseif($row[csf('booking_type')]==1 && $row[csf('is_short')]==2 && $row[csf('entry_form')]==108){
									$booking_type_text="Partial";
								}
								else 
								{
									$booking_type=$row[csf('is_short')];
									if($row[csf('is_short')]==1) 
									{
										$booking_type_text="Short";
										
									}
									else 
									{
										$booking_type_text="Main"; 
										
									}
								}

								
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";								
								$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
								$po_id=explode(",",$row[csf('po_break_down_id')]);
								$po_no=''; $min_ship_date='';
								//echo $row[csf('booking_type')].'='.$row[csf('is_short')].'='.$row[csf('entry_form')].'m';
								foreach($po_id as $val)
								{
									if($po_no==''){ 
										$po_no=$po_array[$val]['no'];
										$po_insert_date=$po_array[$val]['insert_date'];
										$po_internal_ref=$po_array[$val]['internal_ref'];
									}else{
										$po_no.=",".$po_array[$val]['no'];										
										$po_insert_date.=",".$po_array[$val]['insert_date'];
										$po_internal_ref.=",".$po_array[$val]['internal_ref'];
									}
									
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
								if($row[csf('is_short')]==2 and $row[csf('booking_type')]==1)
								{
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
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									 	
									  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
									else if($row_id==4)
									{ 
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
									}
								   	else if($row_id==7)
									{
										echo $print_booking."***".$row_id;die;
										$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									 	
									  	$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
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
									if($row_id==370)  
									{ 
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print19','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print19','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
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
									else if($row_id==78)
									{
									 	$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
								    else if($row_id==85)
									{ 
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
									else if($row_id==269) //FOR KNIT ASIA
									{ 
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}
									if($variable=="") $variable="".$row[csf('booking_no')]."";									
								}

								if($row[csf('entry_form')]==108)
								{
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
									
								if($row[csf('is_short')]==1 and $row[csf('booking_type')]==1)
								{
									$print_booking=''; $print_booking2='';	
									foreach($print_report_id_short_fabric_arr as $row_id)
									{
										if($row_id==46)//URMI Print Button;
										{   
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}
										else
										{
											$print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
										}												
									}
								}
									
								if($row[csf('booking_type')]==4)
								{
									$print_booking=''; $print_booking2='';										
									$row_id=$print_report_id_sample_fabric_arr[0];
									$row_id1=$print_report_id_sample_fabric_arr[1];
									if($row_id1)
									{
									    $print_booking="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id1."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
									}
							
									if($row_id)
									{
									    $print_booking2="<a href='#' onClick=\"generate_worder_report3('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no')]." <a/>";
									}										
								}
							    ?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								    <?
									if(str_replace("'","",trim($cbo_date_by))==1)
									{
										$date_all="B Date : ".change_date_format($row[csf('booking_date')]);
									}
									else if(str_replace("'","",trim($cbo_date_by))=='2')
									{
										$insert_date=$row[csf('insert_date')];
										$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
									}
									// change_date_format($row[csf('booking_date')]);
												$job_date=change_date_format($job_insert_date_arr[$row[csf('job_no')]]);
												$booking_date=change_date_format($row[csf('booking_date')]);

												
												$diff = abs(strtotime($job_date) - strtotime($booking_date));
												
												
												$years = floor($diff / (365*60*60*24));
												$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
												$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60));
												$count_day = floor($diff / (60*60*24));
											


								    ?>
									<td width="40"><? echo $i; ?></td>
									<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                    <td width="110"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','file');">View</a></td>
									<td width="170"><p>
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
                                    </p></td>
									
                                    <td width="80" align="center"><p><? echo $min_ship_date; ?></p></td>								
									<td width="100" align="center"><p><? echo implode(",", array_unique(explode(",", $po_internal_ref))); ?></p></td>
									<td width="100" align="center"><p><? echo implode(",", array_unique(explode(",", $po_insert_date))); ?></p></td>
									<td width="120"><p><? echo rtrim($print_booking2,', ')."<br>".$date_all; ?></p></td>
									<td width="100" align="center"><p><? if($job_date){ echo $count_day." Days"; }?></p></td>
                                    <td width="80" align="center"><p><? echo $booking_type_text; ?></p></td>
                                    <td width="100" align="center"><p><? echo $print_booking; ?></p></td>
                                    <td width="140">
                                    	<p><? echo $user_name_array[$row[csf('inserted_by')]]['full_name']." (".$user_name_array[$row[csf('inserted_by')]]['name'].")";?></p>
                                    </td>
                                    <td width="130">
                                    	<p><? echo $user_name_array[$row[csf('inserted_by')]]['designation'];?></p>
                                    </td>
                                    <td width="140" align="center"><p>
                                    	<? 
                                    		if ($type == 1){
                                    			echo date("d-M-Y h:i:s A",strtotime($row[csf('insert_date')]));
                                    		} else {
                                    			echo date("d-M-Y h:i:s A",strtotime($row[csf('approved_date')]));
                                    		} 
                                    	?>
                                    </p></td>
									<td width="140" align="center"><p>
										<? 
											//$usr_id=$row[csf("approved_by")];
											if ($type == 1)
											{
												$app_cond=" and b.approval_type=0";
											}
											else
											{
												$app_cond=" and b.approval_type=1";
											}	
											$user_id=$row[csf("inserted_by")];
                                    		$sql_reason="select max(b.id) as id, b.user_id, b.approval_cause 
                                    			from fabric_booking_approval_cause b 
                                    			where b.entry_form in(7,12,13) and b.user_id='$user_id' and b.booking_id=".$row[csf("id")]." $app_cond and b.status_active=1 and b.is_deleted=0 
                                    			group by b.user_id, b.approval_cause 
                                    			order by id desc";
                                    		$sql_rslt = sql_select($sql_reason);
                                    		echo $sql_rslt[0][csf('approval_cause')];
										?>
									</p></td>
                                    <td><p>
                                    	<? 
                                    		$user_id=$row[csf("inserted_by")];
                                    		$sql_request="select MAX(id) as id, approval_cause from fabric_booking_approval_cause where entry_form in(7,12,13) and user_id='$user_id' and booking_id=".$row[csf("id")]." and approval_type=2 and status_active=1 and is_deleted=0 group by approval_cause order by id desc";
                                    		$sql_result = sql_select($sql_request);
                                    		if ($type == 1){
                                    			echo '';
                                    		} else {
                                    			echo $sql_result[0][csf('approval_cause')];
                                    		} 
                                    	?>                                    		
                                    </p></td>
								</tr>
							    <?
								$i++;								
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