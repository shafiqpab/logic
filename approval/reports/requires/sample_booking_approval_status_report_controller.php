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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="") 
		$date_cond=" and a.insert_date between $txt_date_from and $txt_date_to"; 
	else $date_cond="";
	$cbo_sample_type=str_replace("'","",$cbo_sample_type);
	$cbo_sample_type_cond="";
	if($cbo_sample_type>0) $cbo_sample_type_cond= "and a.item_category='$cbo_sample_type'";
	
	$type = str_replace("'","",$cbo_type);
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
	
	$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
	
	$user_name_array=array();
	$userData=sql_select( "select id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}
	$approved_no_array=return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where entry_form in (8,9) group by mst_id","mst_id","approved_no");
	
	if($db_type==0)
	{
		$signatory_data_arr=sql_select("select group_concat(user_id) as user_id, group_concat(case when bypass=2 then user_id end) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and entry_form=9 and is_deleted=0 order by sequence_no asc");
	}
	else
	{
		$signatory_data_arr=sql_select("select LISTAGG(user_id, ',') WITHIN GROUP (ORDER BY sequence_no) as user_id, LISTAGG(case when bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and  entry_form=9 and is_deleted=0 order by sequence_no asc");	
	}
	
	$signatory=$signatory_data_arr[0][csf('user_id')];
	$bypass_no_user_id=$signatory_data_arr[0][csf('user_idby')];
	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=9 and bypass=2", "user_id", "buyer_id" );
	
	$user_approval_array=array(); $user_ip_array=array();$user_approval_array_trims=array();

	$query_trims="select mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form=8 ";
	$res_trims=sql_select($query_trims);
	foreach($res_trims as $vals)
	{
		$user_approval_array_trims[$vals[csf('mst_id')]][$vals[csf('approved_no')]][$vals[csf('approved_by')]]=$vals[csf('approved_date')];
		$user_ip_array_trims[$vals[csf('mst_id')]][$vals[csf('approved_no')]][$vals[csf('approved_by')]]=$vals[csf('user_ip')];
	}
	//echo "<pre>"; print_r($user_approval_array_trims);die;
	
	$query="select mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form=9";
	$result=sql_select( $query );
	foreach ($result as $row)
	{
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
		$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('user_ip')];
	}
	
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$style_dealing_merchant_array = return_library_array("select id, dealing_marchant from sample_development_mst","id","dealing_marchant");
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
	$print_report_non=explode(",",$print_report_format);
	$print_report_id=$print_report_non[0];
	//echo $print_report_id.'kkkkkkkk';
	
	ob_start();
	?>
	<fieldset style="width:1150px;">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
			   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr>
			   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="130">Booking No</th>
				<th width="100">Booking Date</th>
				<th width="100">Sample Type</th>
				<th width="120">Dealing Merchant</th>
				<th width="140">Signatory</th>
				<th width="110">Designation</th>
				<th width="100">IP Address</th>
				<th width="100">Approval Date</th>
				<th width="100">Approval Time</th>
				<th>Approve No</th>
			</thead>
		</table>
		<div style="width:1140px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1122" class="rpt_table" id="tbl_list_search">
				<tbody>
					<? 
						$i=1; $signatory=array_unique(explode(",",$signatory)); $rowspan=count($signatory); $bypass_no_user_id=explode(",",$bypass_no_user_id);

						$trims=$item_category[4];
						$fabric=$item_category[2];
						
						if($type==2) $approved_cond=" and a.is_approved=1"; else $approved_cond="";
						
						if($db_type==0) 
						{
							$sql="select a.id, a.company_id,a.item_category, a.booking_no, a.booking_no_prefix_num, a.buyer_id, a.booking_date, a.is_approved, a.insert_date, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $buyer_id_cond $booking_cond $approved_cond $date_cond $cbo_sample_type_cond  group by a.id,a.item_category order by a.insert_date desc";
						}
						else
						{
							$sql="select a.id, a.company_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, a.booking_date, a.is_approved, a.insert_date, min(b.style_id) as style_id  from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $buyer_id_cond $booking_cond $approved_cond $date_cond $cbo_sample_type_cond group by a.id, a.company_id,a.item_category, a.booking_no, a.booking_no_prefix_num, a.buyer_id, a.booking_date, a.is_approved, a.insert_date order by a.insert_date desc";
						}

						//echo $sql;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							//$full_approval=$user_approval_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$last_user_id];
							$full_approval=true;
							foreach($bypass_no_user_id as $uId)
							{
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
							
							//if((($type==1 && $full_approval=="") || $row[csf('is_approved')]==0) || ($type==2 && $full_approval!=""))
							if((($type==1 && $full_approval==false) || $row[csf('is_approved')]==0) || ($type==2 && $full_approval==true))
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$z=0;
								
								$insert_date=$row[csf('insert_date')];
								$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
								
								if($print_report_id==64)
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report5','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
							   else if($print_report_id==34) //1 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								 else if($print_report_id==35) //2 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report2','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								else if($print_report_id==36) //3 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								 else if($print_report_id==37) //4 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report4','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								 else if($print_report_id==72) //6 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report6','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								 else if($print_report_id==174) //7 Button
								{

								 $button_variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_approved')]."','show_fabric_booking_report7','".$print_report_id."')\"> ".$row[csf('booking_no_prefix_num')]."<br>".$date_all."<a/>";
								 }
								 else $button_variable=$row[csf('booking_no_prefix_num')]."<br>".$date_all;
											 
									
								foreach($signatory as $val)
								{
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
									<?
									if($z==0)
									{
										$dealing_merchant=$dealing_merchant_array[$style_dealing_merchant_array[$row[csf('style_id')]]];
									?>
										<td width="40" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
										<td width="130" rowspan="<? echo $rowspan; ?>" align="center">
											<p>
												<?
													if($type==2)
													{
													?>
														<!--<a href='##' style='color:#000' onClick="generate_worder_report('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('is_approved')]; ?>')"><? //echo $row[csf('booking_no_prefix_num')]."<br>".$date_all; ?></a>-->
													<?
													}
													else
													{
														
													?>
														<!--<a href='##' style='color:#000' onClick="generate_worder_report('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('is_approved')]; ?>')"><? //echo $row[csf('booking_no_prefix_num')]."<br>".$date_all; ?></a>-->
													<?
														//echo $row[csf('booking_no_prefix_num')]."<br>".$date_all;
													}
													echo $button_variable;
												?>
											</p>
										</td>
										<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
										<td width="100" rowspan="<? echo $rowspan; ?>"><p><? if($cbo_sample_type==2){echo "Fabric";}else if($cbo_sample_type==4){echo "Trims";} ?></p></td>
										<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_merchant; ?></p></td>
									<?
									}
									
									$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
									$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
									$approval_date_trims=$user_approval_array_trims[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
									$approval_ip_trims=$user_ip_array_trims[$row[csf('id')]][$approved_no_array[$row[csf('id')]]][$val];
									$date=''; $time=''; $approved_no='';

									//$approval_date_trims=$user_approval_array_trims[$vals[csf('mst_id')]][$vals[csf('approved_no')]][$vals[csf('approved_by')]]=$vals[csf('approved_date')];
									//$approval_ip_trims=$user_ip_array_trims[$vals[csf('mst_id')]][$vals[csf('approved_no')]][$vals[csf('approved_by')]]=$vals[csf('user_ip')];

									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
										$approved_no=$approved_no_array[$row[csf('id')]];
									}
									if($approval_date_trims!="" && $cbo_sample_type==4)
									{
										
										$date=date("d-M-Y",strtotime($approval_date_trims)); 
										$time=date("h:i:s A",strtotime($approval_date_trims)); 
										$approved_no=$approved_no_array[$row[csf('id')]];
									}
									?>
										<td width="140"><p><? echo $user_name_array[$val]['full_name']." (".$user_name_array[$val]['name'].")"; ?>&nbsp;</p></td>
										<td width="110"><p><? echo $user_name_array[$val]['designation']; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $user_ip; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? if($row[csf('is_approved')]!=0) echo $date; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? if($row[csf('is_approved')]!=0) echo $time; ?>&nbsp;</p></td>
										<td><p>&nbsp;<? if($row[csf('is_approved')]!=0) echo $approved_no; ?>&nbsp;</p></td>
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


if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Booking No Info", "../../../", 1, 1,'','','');
	
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
							$search_by_arr=array(1=>"Booking No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_sample_booking_no_search_list_view', 'search_div', 'sample_booking_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_sample_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$type=$data[4];

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$search_field="booking_no";
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and buyer_id=$data[1]";
	
	$sql= "select id, booking_no, booking_date, company_id, buyer_id from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 and ready_to_approved=1 and company_id=$company_id and $search_field like '$search_string' $buyer_id_cond order by booking_no";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Booking Date", "140,140,140","600","240",0, $sql , "js_set_value", "id,booking_no", "", 1, "company_id,buyer_id,0,0", $arr , "company_id,buyer_id,booking_no,booking_date", "",'','0,0,0,3','',1) ;
	
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
                    	<td width="100" align="center"><a href="../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
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

?>