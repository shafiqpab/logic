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

if($action=="req_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Purchase Requisition Info", "../../../", 1, 1,'','','');
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
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Requisition No </th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                            <input type="hidden" name="hide_no" id="hide_no" value="" />
                            <input type="hidden" name="hide_id" id="hide_id" value="" />
	                </thead>
	                <tbody>
	                	<tr class="general">
	                        <td>
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								?>
	                        </td>                 
	                        <td >	
	                    	<?
								if($type==1) $search_by_arr=array(1=>"Requisition");// else $search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 	
	                        <td>
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_req_no_search_list_view', 'search_div', 'yarn_purchase_req_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_req_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string=trim($data[3]);
	$type=$data[4];
	if($type==1)	
	{
		if($search_by==1) $search_field="a.requ_prefix_num"; else $search_field="job_no";
		
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and b.buyer_id=$data[1]";
		$reqCond="";
		if($search_string!="") $reqCond="and a.requ_prefix_num='$search_string'"; else $reqCond="";
		
		if($db_type==0)
		{
			$buyerConcateCond="group_concat(b.buyer_id)";
			$styleConcateCond="group_concat(b.style_ref_no)";
			$year_cond="year(a.insert_date)";
		}
		else if ($db_type==2)
		{
			$buyerConcateCond="rtrim(xmlagg(xmlelement(e,b.buyer_id,',').extract('//text()') order by b.buyer_id).GetClobVal(),',')";
			$styleConcateCond="rtrim(xmlagg(xmlelement(e,b.style_ref_no,',').extract('//text()') order by b.style_ref_no).GetClobVal(),',')";
			$year_cond="to_char(a.insert_date,'YYYY')";
		}
		
		$sql= "select a.id, a.requ_prefix_num, a.requ_no, $year_cond as year, a.company_id, a.requisition_date, $buyerConcateCond as buyer_id, $styleConcateCond as style_ref_no 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.ready_to_approve=1 and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $reqCond $buyer_id_cond group by a.id, a.requ_prefix_num, a.requ_no, a.company_id, a.insert_date, a.requisition_date order by a.id DESC";
		
		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="60">Req. No </th>
					<th width="60">Year</th>
					<th width="170">Style</th>
					<th width="170">Buyer</th>
					<th>Req. Date</th>
				</thead>
			</table>
			<div style="width:580px; overflow-y:scroll; max-height:250px;" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table" id="tbl_list_search" >
				<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if($db_type==2)
						{
							$row[csf('buyer_id')]=$row[csf('buyer_id')]->load();
							$row[csf('style_ref_no')]= $row[csf('style_ref_no')]->load();
						}
						$buyerName=""; $styleRef="";
						
						$exBuyer=array_filter(array_unique(explode(",",$row[csf('buyer_id')])));
						
						foreach($exBuyer as $buyid)
						{
							if($buyerName=="") $buyerName=$buyer_arr[$buyid]; else $buyerName.=', '.$buyer_arr[$buyid];
						}
						
						$styleRef=implode(",",array_filter(array_unique(explode(",",$row[csf('style_ref_no')]))));
						
					?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<?=$i;?>" onClick="js_set_value('<?=$i.'_'.$row[csf('id')].'_'.$row[csf('requ_no')]; ?>')"> 
							<td width="30" align="center"><?=$i; ?></td>	
							<td width="60" align="center"><?=$row[csf('requ_prefix_num')]; ?></td>
							<td width="60" align="center"><?=$row[csf('year')]; ?></td>
							<td width="180" style="word-break: break-all;"><?=$styleRef; ?></td>
							<td width="180" style="word-break: break-all;"><?=$buyerName; ?></td>
							<td align="center"><?=change_date_format($row[csf('requisition_date')]); ?></td>	
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>           
		<?
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

 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="") $date_cond=" and a.requisition_date between $txt_date_from and $txt_date_to"; else $date_cond="";
	
	if(str_replace("'","",trim($txt_app_date))!="" )
	{
		if($db_type==0) $app_date_cond=" and approved_date between $txt_app_date and $txt_app_date";
		else $app_date_cond=" and approved_date between  $txt_app_date  and '".str_replace("'","",trim($txt_app_date)). " 11:59:59 PM'";
	}
	else $app_date_cond="";
	
 	if($template==1)
	{
		$type = str_replace("'","",$cbo_type);
		
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and b.buyer_id=$cbo_buyer_name";
		
		if(str_replace("'","",$hide_req_id)=="") $req_cond=""; else $req_cond=" and a.id in(".str_replace("'","",$hide_req_id).")";
		
		$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
		$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");
		
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
		$queryApp="select entry_form, mst_id, max(approved_no) as approved_no from approval_history where entry_form in(20) group by entry_form, mst_id";
		$resultApp=sql_select( $queryApp );
        foreach ($resultApp as $row)
		{
			$approved_no_array[$row[csf('mst_id')]]=$row[csf('approved_no')];
		}
		
		$buyer_id_arr=array();
		$buyerData=sql_select("select entry_form, user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form in(20) and bypass=2");
		foreach($buyerData as $row)
		{
			$buyer_id_arr[$row[csf('entry_form')]][$row[csf('user_id')]]=$row[csf('buyer_id')];
		}
		//print_r($buyer_id_arr[12]);
		
		if($db_type==0)
		{
			$signatory_data_arr=sql_select("select group_concat(case when entry_form=20 then user_id end) as user_ids, group_concat(case when entry_form=20 and bypass=2 then user_id end) as user_idsby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 order by sequence_no");
		}
		else
		{
			$signatory_data_arr=sql_select("select 
			 LISTAGG(case when entry_form=20 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_ids,
			 LISTAGG(case when entry_form=20 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idsby
			 from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=20 ORDER BY sequence_no");	
		}
		
		$signatory_user=$signatory_data_arr[0][csf('user_ids')];
		$bypass_no_user_id=$signatory_data_arr[0][csf('user_idsby')];
		
 		$user_approval_array=array(); $user_ip_array=array(); 
		$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form in(20)";
		$result=sql_select( $query );
        foreach ($result as $row)
		{
			$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
			$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('user_ip')];
		}
		//print_r($user_approval_array);
		
		if($app_date_cond!=''){
			$mst_id_arr=return_library_array( "select mst_id, mst_id from approval_history where entry_form in(20) $app_date_cond","mst_id","mst_id");
			$mst_id_con = " and a.id in(".implode(',',$mst_id_arr).")";
		}
		else $mst_id_con = "";	
		
		$approval_remarks_arr=array();
		$sql_remarks=sql_select("select approval_cause, booking_id, user_id, entry_form, approval_no from fabric_booking_approval_cause where entry_form in (20) and status_active=1 and is_deleted=0 ");
		foreach($sql_remarks as $inf)
		{
			$approval_remarks_arr[$inf[csf('booking_id')]][$inf[csf('approval_no')]]=$inf[csf('approval_cause')];
		}
		
		/*$print_report_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	 
		$print_report_id_short_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		
		$print_report_id_sample_fabric=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
		$print_report_format_par=return_field_value("format_id"," lib_report_template","template_name =".$cbo_company_name."  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	
		$print_report_format_part=explode(",",$print_report_format_par);
	 
		//echo $print_report_ids;
		$format_ids=explode(",",$print_report_ids);
		$print_report_id_short_fabric_arr=explode(",",$print_report_id_short_fabric);
		$print_report_id_sample_fabric_arr=explode(",",$print_report_id_sample_fabric);*/
		// print_r($format_ids);

		$print_report_format=0;
    	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
    	//echo '<pre>';print_r($print_report_format);
    	$print_format_ids=explode(',',$print_report_format);

		ob_start();
		?>
        <fieldset style="width:1370px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:18px"><strong><?=$report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><?=$company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1370" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="200">Buyer Name</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="200">Style Ref No</th>
                    <th width="130">Requisition No</th>
                    
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th width="80">Approve No</th>
                    <th>Remarks</th>
                </thead>
            </table>

			<div style="width:1370px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
							$i=1;
							$signatory_user=array_unique(explode(",",$signatory_user)); $rowspanUser=count($signatory_user);
							$bypass_no_user_id=explode(",",$bypass_no_user_id);
							
							if($type==2) $approved_cond=" and a.is_approved=1"; else $approved_cond="and a.is_approved!=1";
							if($db_type==0)
							{
								$buyerConcateCond="group_concat(b.buyer_id)";
								$styleConcateCond="group_concat(b.style_ref_no)";
								$year_cond="year(a.insert_date)";
							}
							else if ($db_type==2)
							{
								$buyerConcateCond="rtrim(xmlagg(xmlelement(e,b.buyer_id,',').extract('//text()') order by b.buyer_id).GetClobVal(),',')";
								$styleConcateCond="rtrim(xmlagg(xmlelement(e,b.style_ref_no,',').extract('//text()') order by b.style_ref_no).GetClobVal(),',')";
								$year_cond="to_char(a.insert_date,'YYYY')";
							}
							
							$sql="SELECT a.id, a.company_id, a.requ_no, a.dealing_marchant, a.is_approved, a.insert_date, a.requisition_date, $buyerConcateCond as buyer_id, $styleConcateCond as style_ref_no
							from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approve=1 $buyer_id_cond $req_cond $date_cond $mst_id_con $approved_cond
							group by a.id, a.company_id, a.requ_no, a.dealing_marchant, a.is_approved, a.insert_date, a.requisition_date
							order by a.insert_date DESC";
							//echo $sql; die; 
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$full_approval='';
								
								$signatory=$signatory_user;
								$rowspan=$rowspanUser;
								
								
								$full_approval=true;
								foreach($bypass_no_user_id as $uId)
								{
									//echo $approved_no_array[$row[csf('id')]].'-';
									//echo $user_approval_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$uId].'<br>';
									$buyer_ids=$buyer_id_arr[$uId];
									$buyer_ids_array=explode(",",$buyer_id_arr[$uId]);
									if($buyer_ids=="" || in_array($row[csf('buyer_id')],$buyer_ids_array))
									{
										$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$uId];
										if($approvedStatus=="")
										{
											$full_approval=false;
											break;
										}
									}
								}
								
								//echo $full_approval;
								$variable='';
								if($print_format_ids[0]==134)
								{
									$variable="<a href='#' onClick=\"print_report('".$row[csf('company_id')]."*".$row[csf('id')]."*Yarn Purchase Requisition*".$row[csf('is_approved')]."','yarn_requisition_print', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"><font color='blue'><b>".$row[csf('requ_no')]."</b></font></a>";
								} 
								else if($print_format_ids[0]==135)
								{
									$variable="<a href='#' onClick=\"print_report('".$row[csf('company_id')]."*".$row[csf('id')]."*Yarn Purchase Requisition*".$row[csf('is_approved')]."','yarn_requisition_print_2', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"><font color='blue'><b>".$row[csf('requ_no')]."</b></font></a>";
								}
								else if($print_format_ids[0]==136)
								{
									$variable="<a href='#' onClick=\"print_report('".$row[csf('company_id')]."*".$row[csf('id')]."*Yarn Purchase Requisition*".$row[csf('is_approved')]."','yarn_requisition_print_3', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"><font color='blue'><b>".$row[csf('requ_no')]."</b></font></a>";
								}
								else if($print_format_ids[0]==137)
								{
									$variable="<a href='#' onClick=\"print_report('".$row[csf('company_id')]."*".$row[csf('id')]."*Yarn Purchase Requisition*".$row[csf('is_approved')]."','yarn_requisition_print_4', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"><font color='blue'><b>".$row[csf('requ_no')]."</b></font></a>";
								}
								else if($print_format_ids[0]==64)
								{
									$variable="<a href='#' onClick=\"print_report('".$row[csf('company_id')]."*".$row[csf('id')]."*Yarn Purchase Requisition*".$row[csf('is_approved')]."','yarn_requisition_print_5', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"><font color='blue'><b>".$row[csf('requ_no')]."</b></font></a>";
								}

								if((($type==1 && $full_approval==false) || $row[csf('is_approved')]==0) || ($type==2 && $full_approval==true))
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$z=0; 
									foreach($signatory as $val)
									{
									?>
										<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>"> 
										<?
										if($z==0)
										{
											$date_all="R Date : ".change_date_format($row[csf('requisition_date')]);
											$dealing_merchant=$row[csf('dealing_marchant')];
									
											if($db_type==2)
											{
												$row[csf('buyer_id')]=$row[csf('buyer_id')]->load();
												$row[csf('style_ref_no')]= $row[csf('style_ref_no')]->load();
											}
											$buyerName=""; $styleRef="";
											
											$exBuyer=array_filter(array_unique(explode(",",$row[csf('buyer_id')])));
											
											foreach($exBuyer as $buyid)
											{
												if($buyerName=="") $buyerName=$buyer_arr[$buyid]; else $buyerName.=', '.$buyer_arr[$buyid];
											}
											
											$styleRef=implode(",",array_filter(array_unique(explode(",",$row[csf('style_ref_no')]))));
											?>
											<td width="40" rowspan="<?=$rowspan; ?>"><?=$i; ?></td>
											<td width="200" rowspan="<?=$rowspan; ?>" style="word-break: break-all;"><?=$buyerName; ?></td>
											<td width="110" rowspan="<?=$rowspan; ?>" style="word-break: break-all;"><?=$dealing_merchant; ?>&nbsp;</td>
											<td width="200" rowspan="<?=$rowspan; ?>" style="word-break: break-all;"><?=$styleRef; ?>&nbsp;</td>
                                            <td width="130" rowspan="<?=$rowspan; ?>" style="word-break: break-all;"><?= $variable; ?><br><?= $date_all; ?></td>
											<?
										}
										
										$approved_no=''; $user_ip='';
										$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
										$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
										if($approval_date!="") $approved_no=$approved_no_array[$row[csf('id')]];
											
										//$approval_remarks=$approval_remarks_arr[$row[csf('id')]][$val][$approved_no];
										$approval_remarks=$approval_remarks_arr[$row[csf('id')]][$approved_no];
										$adate=''; $atime=''; 
										if($approval_date!="") 
										{
											$adate=date("d-M-Y",strtotime($approval_date)); 
											$atime=date("h:i:s A",strtotime($approval_date)); 
										}
										
										?>
											<td width="140" style="word-break: break-all;"><?=$user_name_array[$val]['full_name']." (".$user_name_array[$val]['name'].")"; ?>&nbsp;</td>
                                            <td width="130" style="word-break: break-all;"><?=$user_name_array[$val]['designation']; ?>&nbsp;</td>
											<td width="100" align="center" style="word-break: break-all;"><? if($row[csf('is_approved')]!=0) echo $adate;//."=".$row[csf('id')];// print_r($user_approval_array[$row[csf('id')]]); ?>&nbsp;</td>
											<td width="100" align="center" style="word-break: break-all;"><? if($row[csf('is_approved')]!=0) echo $atime; ?>&nbsp;</td>
											<td width="80" style="word-break: break-all;">
												<? 
                                                    if($row[csf('is_approved')]!=0) 
													{
														echo $approved_no;
														if($approved_no>0) echo getOrdinalSuffix($approved_no);
													}
                                                ?>
                                            &nbsp;</td>
                                            <td style="word-break: break-all;"><? if($row[csf('is_approved')]!=0) echo $approval_remarks; ?>&nbsp;</td>
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