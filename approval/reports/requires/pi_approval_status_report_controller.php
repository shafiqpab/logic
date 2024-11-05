	<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	//echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );    
	echo create_drop_down( "cbo_supplier_name", 130,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39)
	exit();
}

if($action=="search_popup")
{
	extract($_REQUEST);

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
                    <th>Supplier</th>
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
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
                        	 	echo create_drop_down( "cbo_supplier_name", 130,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplierID,'',0);
							?>
                        </td>                 
                        <td align="center">	
                    	<?
						    $search_by_arr=array(1=>"PI No",2=>"System No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_supplier_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_pi_search_list_view', 'search_div', 'pi_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_pi_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$search_cond = "";
	if($search_by==1) $search_cond .=" and pi_number like '$search_string'"; else $search_cond .=" and id = ".trim($data[3]);

	if($data[1]) $search_cond .=" and supplier_id=$data[1]";
	//echo $sql= "select id, job_no, company_name, buyer_name, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";
	$sql= "select id, pi_number, importer_id, supplier_id,pi_date from com_pi_master_details where status_active=1 and is_deleted=0 and importer_id=$company_id $search_cond order by pi_number";
	echo create_list_view("tbl_list_search", "Importer,Supplier Name,PI No,PI date,System Id,", "120,120,120,120","600","240",0, $sql , "js_set_value", "id,pi_number", "", 1, "importer_id,supplier_id,0,0,0", $arr , "importer_id,supplier_id,pi_number,pi_date,id", "",'','0,0,0,3,0','',1) ;
	
   exit(); 
} 

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
 	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}
	
	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	
	$cbo_type = str_replace("'","",$cbo_type);
	$supplier_id_cond="";
	
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	$pi_cond=$cate_cond="";
	if($txt_pi_no !="") $pi_cond=" and a.pi_number in('".implode("','",explode("*",$txt_pi_no))."')";
	if($cbo_item_category_id!="") $cate_cond=" and a.item_category_id in($cbo_item_category_id)";
	if(str_replace("'","",$cbo_supplier_name)!=0) $supplier_id_cond=" and a.supplier_id=$cbo_supplier_name"; else  $supplier_id_cond="";
	
	
	if ($type == 1) // show 
	{
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
	
		$user_name_array=array();
		$userData=sql_select( "select id, user_name, user_full_name, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
			$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
			$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
		}


		$approved_no_array=array();
		$queryApp="select entry_form, mst_id, max(approved_no) as approved_no from approval_history where entry_form=27 group by entry_form, mst_id"; // and mst_id=6819
		$resultApp=sql_select( $queryApp );
		foreach ($resultApp as $row)
		{
			$approved_no_array[$row[csf('entry_form')]][$row[csf('mst_id')]]=$row[csf('approved_no')];
		}
		//echo $cbo_type.'sdffgg';die;
		//$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=27 and bypass=2", "user_id", "buyer_id" );
		//print_r($buyer_id_arr);
		$UserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');
		
		if($db_type==0)
		{
			$signatory_data_arr=sql_select("select group_concat(case when entry_form=27 then user_id end) as user_id, group_concat(case when entry_form=27 and bypass=2 then user_id end) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 order by sequence_no");
		}
		else
		{
			$signatory_data_arr=sql_select("select LISTAGG(case when entry_form=27 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_id, LISTAGG(case when entry_form=27 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY sequence_no) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 order by sequence_no");	
		}
		$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
		//print_r($signatory_data_arr);

		$signatory_main=$signatory_data_arr[0][csf('user_id')];
		$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idby')];

		$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
		$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form =27";// and mst_id=6819 order by approved_date desc
		$result=sql_select( $query );
		foreach ($result as $row)
		{
			$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('approved_date')];
			$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('user_ip')];
			$approved_date=date("Y-m-d",strtotime($row[csf('approved_date')]));
			
			if($max_approval_date_array[$row[csf('mst_id')]]=="")
			{
				$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
			}
			else
			{
				if($approved_date>$max_approval_date_array[$row[csf('mst_id')]])
				{
					$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
				}
			}
		}
		// echo "<pre>"; print_r($user_approval_array);die;
		ob_start();
		?>
	        <fieldset style="width:1480px;">
	        	<table cellpadding="0" cellspacing="0" width="100%">
	                <tr>
	                   <td align="center" width="100%" colspan="12" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>
	                <tr>
	                   <td align="center" width="100%" colspan="12" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr>
	            </table>	
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1460" class="rpt_table" >
	                <thead>
	                    <th width="40">SL</th>
	                    <th width="100">System Id</th>
	                    <th width="100">PI No</th>
	                    <th width="100">PI Value</th>
	                    <th width="100">Importer Name</th>
	                    <th width="100">Supplier Name</th>
	                    <th width="100">PI Date</th>
	                    <th width="140">Signatory</th>
	                    <th width="130">Designation</th>
	                    <th width="130">Dealing Merchandiser</th>
	                    <th width="100">IP Address</th>
	                    <th width="100">Approval Date</th>
	                    <th width="100">Approval Time</th>
	                    <th>Approve No</th>
	                </thead>
	            </table>
				<div style="width:1460px; overflow-y:scroll; max-height:330px;" id="scroll_body">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1442" class="rpt_table" id="tbl_list_search" align="left">
	                    <tbody>
	                        <? 					
							$i=1;
							$signatory_main=explode(",",$signatory_main); $rowspanMain=count($signatory_main);
							
							$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
							
							if($cbo_type==2) $approved_cond=" and a.approved=1"; else if($cbo_type==3) $approved_cond=" and a.approved=3"; else $approved_cond=" and a.approved=0";
							if($db_type==0)
							{
								$select_pi_cat = " group_concat(b.item_category_id)";
							}else{
								$select_pi_cat = " listagg(b.item_category_id,',') within group(order by b.item_category_id)";
							}

							$sql="SELECT a.id, a.importer_id,a.supplier_id, a.pi_date,a.pi_number,approved,$select_pi_cat as item_category_ids,a.net_total_amount 
							from com_pi_master_details a, com_pi_item_details b 
							where a.id = b.pi_id and a.importer_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.ready_to_approved = 1 $supplier_id_cond $pi_cond $date_cond $cate_cond and a.entry_form not in (1,0) $approved_cond 
							group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, approved, a.net_total_amount";

							//echo $sql;die;
							//$dealing_sql="select c.id as pi_id,a.DEALING_MARCHANT  from wo_po_details_master a,WO_BOOKING_DTLS b,com_pi_item_details c where a.job_no=b.job_no and b.booking_no=c. WORK_ORDER_NO group by  c.id ,a.DEALING_MARCHANT  ";
							
							$dealing_sql="select c.pi_id as pi_id,a.dealing_marchant  from wo_po_details_master a,wo_booking_dtls b,com_pi_item_details c where a.job_no=b.job_no and b.booking_no=c.work_order_no group by  c.pi_id,a.dealing_marchant
							union all
							select c.pi_id ,a.dealing_marchant from wo_po_details_master a, wo_non_order_info_dtls b ,com_pi_item_details c where a.job_no=b.job_no and b.mst_id=c.work_order_id group by  c.pi_id,a.dealing_marchant
							";
							
							foreach(sql_select($dealing_sql) as $vals)
							{
								if($dealing_arr[$vals[csf("pi_id")]])
								$dealing_arr[$vals[csf("pi_id")]].=','.$dealing_merchant_array[$vals[csf("dealing_marchant")]];
								else
								$dealing_arr[$vals[csf("pi_id")]].=$dealing_merchant_array[$vals[csf("dealing_marchant")]];
							}
							
                            $nameArray=sql_select( $sql);
                            foreach ($nameArray as $row)
                            {
								$full_approval='';
								$piCategoryArr = array_filter(array_unique(explode(",",$row[csf("item_category_ids")])));
								$rowspan=$rowspanMain;
								
								$full_approval=true; $approvedStatus=""; $UserCatArr = array();$UserCatIds="";
								foreach($bypass_no_user_id_main as $uId)
								{		
									$UserCatIds = $UserCatCredFromIdArr[$uId];	
									$UserCatArr=explode(",",$UserCatCredFromIdArr[$uId]);
									// echo $UserCatIds.'='.$UserCatArr;
									if($UserCatIds == "" || count(array_intersect($UserCatArr,$piCategoryArr)))
									{
										//echo $row[csf('id')].'='.$approved_no_array[27][$row[csf('id')]].'='.$uId.'='.'27'.'<br>';
										$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[27][$row[csf('id')]]][$uId][27];
										//echo $approvedStatus.'=Tipu=<br>';
										//17-SEP-20 11.08.16 AM=Tipu=17-SEP-20 11.10.25 AM=Tipu=Tipu= 
										if($approvedStatus=="")
										{
											$full_approval=false;
											break;
										}
									}
								}
								
								if($cbo_date_by==2 && $from_date!="" && $to_date!="")
								{
									$max_approved_date=$max_approval_date_array[$row[csf('id')]];
									if($max_approved_date>=$from_date && $max_approved_date<=$to_date)
									{
										$print_cond=1;
									}
									else
									{
										$print_cond=0;
									}
								}
								else
								{
									$print_cond=1;
								}
								
								//echo $cbo_type." __ ".$full_approval." __ ".$row[csf('approved')]." __ ".$print_cond;die; //2 __ 22__ 1 __ 1

								if(((($cbo_type == 1 && $full_approval == false) || ($row[csf('approved')] == 3 || $row[csf('approved')] == 0)) || ($cbo_type == 2 && $full_approval == true))  && $print_cond == 1)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																						
									$z=0; 
									foreach($signatory_main as $val)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<?
											if($z==0)
											{
												?>
												<td width="40" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
												<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><?	echo $row[csf("id")];?></p></td>
	                                            <td width="100" rowspan="<? echo $rowspan; ?>" align="center"><a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$row[csf("id")].'*'.implode(',',array_unique(explode(",",$row[csf("item_category_ids")])));?>','print', '../../commercial/import_details/requires/pi_print_urmi')"><p><?	echo $row[csf("pi_number")];?></p></a></td>
	                                            <td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><?	echo $row[csf("net_total_amount")];?></p></td>
												<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><?	echo $company_arr[$row[csf("importer_id")]];?></p></td>
												<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $buyer_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
												<td width="100" rowspan="<? echo $rowspan; ?>">
	                                            	<p>
	                                                	<?	echo $row[csf("pi_date")];?>
	                                                </p>
	                                            </td>
	                                         
												<?
											}
											
											$approved_no=''; $user_ip='';
										
											$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[27][$row[csf('id')]]][$val][27];
											$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[27][$row[csf('id')]]][$val][27];
											if($approval_date!="") $approved_no=$approved_no_array[27][$row[csf('id')]];
											
											
											$date=''; $time=''; 
											if($approval_date!="") 
											{
												$date=date("d-M-Y",strtotime($approval_date)); 
												$time=date("h:i:s A",strtotime($approval_date)); 
											}
											?>
											<td width="140"><p><? echo $user_name_array[$val]['full_name']." (".$user_name_array[$val]['name'].")"; ?>&nbsp;</p></td>
                                            <td width="130" align="center"><p><? echo $user_name_array[$val]['designation']; ?>&nbsp;</p></td>
                                            <td width="130" align="center"><p><? echo $dealing_arr[$row[csf("id")]]; ?>&nbsp;</p></td>
                                            <td width="100" align="center"><p><? echo $user_ip; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row[csf('approved')]) echo $date; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row[csf('approved')]) echo $time; ?>&nbsp;</p></td>
											<td align="center"><p>&nbsp;<? if($row[csf('approved')]) echo $approved_no; ?>&nbsp;</p></td>
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
	else if ($type == 2) // show 2
	{
		$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
		$supplier_arr=return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id","custom_designation");

		if($cbo_type==2) $approved_cond=" and a.approved=1";		
		else if($cbo_type==1) $approved_cond=" and a.approved=3"; 
		else if($cbo_type==3) $approved_cond=" and a.approved=3"; 
		else $approved_cond=" and a.approved in (0,1,3)";
		
 

		$date_cond=""; $from_date=""; $to_date="";
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	   	{
		   if(str_replace("'","",trim($cbo_date_by))==1)
		   {
			   $date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
		   }
		   else if(str_replace("'","",trim($cbo_date_by))==2)
		   {
			   $date_cond	=" and a.APPROVED_DATE between '".str_replace("'","",trim($txt_date_from))."' and '".str_replace("'","",trim($txt_date_to))." 11:59:59 PM'";

		   }
	   	}

 

		if($db_type==0)	$select_pi_cat = " group_concat(b.item_category_id)";
		else $select_pi_cat = " listagg(b.item_category_id,',') within group(order by b.item_category_id)";

		// $sql="SELECT a.ID, a.IMPORTER_ID, a.SUPPLIER_ID, a.PI_DATE, a.PI_NUMBER, a.APPROVED, $select_pi_cat as ITEM_CATEGORY_IDS, a.INSERTED_BY, a.INSERT_DATE, a.REMARKS, a.NET_TOTAL_AMOUNT
		// from com_pi_master_details a, com_pi_item_details b 
		// where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $supplier_id_cond $pi_cond $date_cond $cate_cond $approved_cond and a.entry_form not in (1,0)  
		// group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, a.inserted_by, a.insert_date, a.remarks, a.net_total_amount";
		$sql="SELECT a.ID, a.IMPORTER_ID, a.SUPPLIER_ID, a.PI_DATE, a.PI_NUMBER, a.APPROVED, a.IMPORT_PI, b.item_category_id as ITEM_CATEGORY_IDS, a.INSERTED_BY, a.INSERT_DATE, a.REMARKS, a.NET_TOTAL_AMOUNT
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $approved_cond $supplier_id_cond $pi_cond $date_cond $cate_cond  and a.entry_form not in (1,0)  
		group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, a.import_pi, a.inserted_by, a.insert_date, a.remarks, a.net_total_amount,b.item_category_id";
		// echo $sql;die;
		$sql_res=sql_select($sql);

		foreach ($sql_res as $row) {
			$pi_id .= $row['ID'].',';
		}
		$pi_ids = rtrim($pi_id,',');

				
				// echo "<pre>";
				// print_r($pro_forma_invoice_app_arr);

		$pi_id_cond=where_con_using_array(array_filter(array_unique(explode(",", $pi_ids))),0,"a.id");
		
	    $sql_buyer_style = "SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 1 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 2 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
	    and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 $pi_id_cond
	    union all 
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 3 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 4 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 5 as TYPE 
	    from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
	    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 $pi_id_cond";

	    //echo $sql_buyer_style;
		

	    $sql_buyer_style_res=sql_select($sql_buyer_style); // and a.pi_id=6201

	    $buyer_style_arr=array();
	    $work_order_qty_arr=array();
	    $pi_id_arr=array();
	    $order_ids='';
	    $tot_rows=0;
	    foreach($sql_buyer_style_res as $row)
	    {
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'].',';
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']].=$row['PI_ID'].',';	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }
	    //echo '<pre>'; print_r($work_order_qty_arr);die;

	    if ($order_ids != '')
	    {
	        $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
	        $order_id_cond = '';

	        if($db_type==2 && $tot_rows>1000)
	        {
	            $order_id_cond = ' and (';
	            $orderNoArr = array_chunk($orderIds,999);
	            foreach($orderNoArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
	            }
	            $order_id_cond = rtrim($order_id_cond,'or ');
	            $order_id_cond .= ')';
	        }
	        else
	        {
	            $orderIds = implode(',', $orderIds);
	            $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
	        }
	    }
	    //echo $order_id_cond;
	    $sql_lcSc="SELECT a.wo_po_break_down_id as ORDER_ID, b.export_lc_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 1 as TYPE from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.status_active=1 
	    $order_id_cond
	    group by a.wo_po_break_down_id, b.export_lc_no, b.internal_file_no 
	    union all
	    select a.wo_po_break_down_id as ORDER_ID, b.contract_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 2 as TYPE from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 $order_id_cond
	    group by a.wo_po_break_down_id, b.contract_no, b.internal_file_no";
		
	    $sql_lcSc_res=sql_select($sql_lcSc);
	    $lcSc_arr=array();
	    foreach ($sql_lcSc_res as $row) 
	    {
			$pi_id_loop=array_unique(explode(",",chop($pi_id_arr[$row['ORDER_ID']],',')));
			foreach($pi_id_loop as $val)
			{
				$lcSc_arr[$val]['LAST_SHIPMENT_DATE'].=change_date_format($row['LAST_SHIPMENT_DATE']).',';
				$lcSc_arr[$val]['LS_SC_NO'].=$row['LS_SC_NO'].',';
				$lcSc_arr[$val]['FILE_NO'].=$row['FILE_NO'].',';
			}
	    }


	    $user_name_array=array();
		$userData=sql_select( "select ID, USER_NAME, USER_FULL_NAME, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
			$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
			$user_name_array[$user_row['ID']]['DESIGNATION']=$designation_array[$user_row['DESIGNATION']];	
		}

		$sql_electronic_app="select USER_ID, APPROVED_BY, SEQUENCE_NO from electronic_approval_setup where company_id=$cbo_company_name and entry_form=27  and is_deleted=0 order by sequence_no";
		$sql_electronic_app_res=sql_select($sql_electronic_app);
		$max_electronic_setup=0;
		$electronic_user_arr=array();
	    foreach ($sql_electronic_app_res as $val)
	    { 
	    	$electronic_user_arr[$val['USER_ID']] = $val['USER_ID'];   	
	    	$max_electronic_setup++;
			$userArr[$val[csf('user_id')]]=$val[csf('user_id')];

	    }
	   // echo $max_electronic_setup;
	    //echo '<pre>';print_r($user_name_array);

	    $user_approval_array=array(); $max_approval_date_array=array();$user_approval_no=array();
		$sql_approval="select ENTRY_FORM, MST_ID, APPROVED_BY,approved_no, APPROVED_DATE, CURRENT_APPROVAL_STATUS from approval_history where entry_form=27 ".where_con_using_array($userArr,1,'approved_by')." order by id asc"; // and mst_id in($pi_ids) and current_approval_status=1

		// $approved_no="select b.PI_NUMBER, MAX(a.approved_no), a.approved_by from approval_history a left join com_pi_master_details b on a.MST_ID=b.ID where a.entry_form=27 ".where_con_using_array($userArr,1,'a.approved_by')." group by a.MST_ID, a.APPROVED_BY, B.PI_NUMBER ORDER BY a.MST_ID";		 
		// $approved_no_data=sql_select($approved_no);
		// echo "<pre>";
		// 	print_r($approved_no_data);
		// echo "</pre>";
		
		$sql_approval_res=sql_select($sql_approval);
		foreach ($sql_approval_res as $row)
		{
			$user_approval_array[$row['MST_ID']][$row['ENTRY_FORM']][$row['APPROVED_BY']]=$row['APPROVED_DATE'];
			$user_approval_no[$row['MST_ID']][$row['ENTRY_FORM']][$row['APPROVED_BY']]=$row['APPROVED_NO'];
			$user_approval_mst_count[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_by')];
		}

		//echo $pi_ids.'system';

		/*$sql_app="select max(id) as ID, max(booking_id) as PI_ID from fabric_booking_approval_cause where page_id=867 and entry_form=27 and booking_id in ($pi_ids) and approval_type=0 and status_active=1 and is_deleted=0";
		$sql_app_res=sql_select($sql_app);		
		foreach ($sql_app_res as $val) {
			$approv_cause_id .=$val['ID'].',';
		}
		$approv_cause_ids = rtrim($approv_cause_id,',');*/

		$app_cause=sql_select("select BOOKING_ID, USER_ID, APPROVAL_CAUSE from fabric_booking_approval_cause where status_active=1 and is_deleted=0");
		// /id in ($approv_cause_ids)
		$approv_pi_arr=array();
		foreach ($app_cause as $val) {
			$approv_pi_arr[$val['BOOKING_ID']][$val['USER_ID']]=$val['APPROVAL_CAUSE'];
		}

		$data_file=sql_select("select IMAGE_LOCATION, MASTER_TBLE_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$file_arr=array();
		foreach($data_file as $row)
		{
			$file_arr[$row['MASTER_TBLE_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($data_file);
		 
		// $table_width=2450+$max_electronic_setup*400;
		$table_width=3650;
		$table_width=2250+$max_electronic_setup*500;
		echo $table_width;

		
		foreach ($sql_res as $row) {
		
			// $user_app=count($user_approval_mst_count[$row[csf('id')]]);
			
			// $user_no=count($userArr);
			//  if( $user_no>$user_app && $user_app>0 && $cbo_type==3) {
			// 	 $app_type=3;//partial

			//  }elseif ($user_no==$user_app && $cbo_type==2) {
			// 	$app_type=2;//full
			
			//  }elseif ($user_no>$user_app && $user_app==0 && $cbo_type==1) {
			// 	$app_type=1;//pending

			//  }else{
			// 	$app_type=0;
			//  }


			 $app_type=$cbo_type;		


			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['company_name']=$row[csf('id')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['importer_id']=$row[csf('importer_id')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['import_pi']=$row[csf('IMPORT_PI')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['approved']=$row[csf('approved')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['net_total_amount']=$row[csf('net_total_amount')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['item_category_ids']=$row[csf('item_category_ids')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['remarks']=$row[csf('remarks')];
			$pro_forma_invoice_app_arr[$app_type][$row[csf('id')]]['insert_date']=$row[csf('insert_date')];
       }


	  //    echo "<pre>";
	 //    print_r($pro_forma_invoice_app_arr);



		ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
		</style>
		<div style="width:<?= $table_width?>px; max-height:350px;">
        	<table cellpadding="0" cellspacing="0" width="<?= $table_width?>">
                <tr>
                   <td align="center" width="<?= $table_width?>" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="<?= $table_width?>" colspan="19" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
                <thead>
                	<tr>
	                    <th width="30" rowspan="2">SL</th>
	                    <th width="100" rowspan="2">System ID</th>
	                    <th width="100" rowspan="2">PI No</th>
						<th width="100" rowspan="2">Item Category</th>
	                    <th width="100" rowspan="2">PI Date</th>
	                    <th width="100" rowspan="2">Supplier Name</th>
	                    <th width="100" rowspan="2">View File</th>
	                    <th width="100" rowspan="2">Factory Name</th>
	                    <th width="120" rowspan="2">Buyer</th>
	                    <th width="120" rowspan="2">Style</th>
	                    <th width="120" rowspan="2">Last Shipment Date</th>
	                    <th width="100" rowspan="2">SC/LC NO</th>
	                    <th width="100" rowspan="2">File No</th>
	                    <th width="100" rowspan="2">Insert By</th>
	                    <th width="120" rowspan="2">Insert Date & Time</th>
	                    <th width="120" rowspan="2">Remarks</th>
	                    <th width="100" rowspan="2">Total WO Value</th>
	                    <th width="100" rowspan="2">Total PI Value</th>
	                    <th width="100" rowspan="2">Surplus/(Deficit)</th>
	                    <?
						foreach($electronic_user_arr as $val)
						{			
							?>
							<th width="400" colspan="3"><?= $user_name_array[$val]['DESIGNATION']; ?>(<?= $user_name_array[$val]['FULL_NAME']; ?>)</th>
							<?
						}
						?>
                    </tr>
                    <tr>
                    	<?
						for($k=1;$k<=$max_electronic_setup;$k++)
						{			
							?>
							<th width="150">Approved Date & Time</th>
							<th width="250">Comments</th>
							<th >Approve No</th>
							<?
						}
						?>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                    	<? 					
						$i=1;
						$tot_work_order_qty=0;
						$tot_pi_qty=0;
						$tot_surplus_deficit=0;
						
						foreach ($pro_forma_invoice_app_arr[$cbo_type] as $pi_id=>$row) 
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$work_order_qty = $work_order_qty_arr[$pi_id]['WORK_ORDER_DTLS_ID'];
							$surplus_deficit = $work_order_qty-$row['NET_TOTAL_AMOUNT'];
							$approval_date=$user_approval_array[$pi_id][$approved_no_array[27][$pi_id]][$val][27];								
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><?= $i; ?></td>
			                    <td width="100"><p><?= $pi_id; ?></p></td>
			                    <td width="100" style='color:#000' align="center"><a href='##' onClick="print_report('<?= $company_name.'*'.$pi_id.'*'.implode(',',array_unique(explode(",",$row['item_category_ids'])));?>','print', '../../commercial/import_details/requires/pi_print_urmi')"><p><font color="blue"><b><?= $row["pi_number"];?></p></b></font></a></td>			          
								<td width="100" align="center"><p><?=$item_category[$row['item_category_ids']];?></p></td>
			                    <td width="100" align="center"><p><?= change_date_format($row['pi_date']); ?></p></td>
			                    <td width="100"><p><? if ($row['import_pi']==1) echo $company_arr[$row['supplier_id']]; else echo $supplier_arr[$row['supplier_id']]; ?></p></td>
			                    <td width="100" align="center"><p>
			                    	<a href="javascript:void()" onClick="downloiadFile('<?= $pi_id; ?>');">
                                <? if ($file_arr[$pi_id]['FILE'] != '') echo 'View File'; ?></a>
			                    </p></td>
			                    <td width="100"><p><?= $company_arr[$row['importer_id']]; ?></p></td>
			                    <td width="120"><p>
			                    	<? 
			                    	$buyer_id=chop($buyer_style_arr[$pi_id]['BUYER_NAME'],',');
                                    $buyer_name=array_unique(explode(',', $buyer_id));
                                    $comma_separate_buyer="";
                                    foreach ($buyer_name as $key => $val) 
                                    {
                                        if ($comma_separate_buyer=="") 
                                        {
                                           $comma_separate_buyer.=$buyer_arr[$val];
                                        }
                                        else
                                        {
                                            $comma_separate_buyer.=','.$buyer_arr[$val];
                                        }
                                    }
                                    echo $comma_separate_buyer;
                                    ?>
                                </p></td>
			                    <td width="120"><p><?= chop($buyer_style_arr[$pi_id]['STYLE_REF_NO'],','); ?></p></td>
			                    <td width="120" align="center"><p><?=implode(", ",array_unique(explode(",",chop($lcSc_arr[$pi_id]['LAST_SHIPMENT_DATE'],',')))); ?>&nbsp;</p></td>
			                    <td width="100"><p><?=implode(", ",array_unique(explode(",",chop($lcSc_arr[$pi_id]['LS_SC_NO'],',')))); ?></p></td>
			                    <td width="100"><p><?=implode(", ",array_unique(explode(",",chop($lcSc_arr[$pi_id]['FILE_NO'],',')))); ?></p></td>
			                    <td width="100"><p><?= $user_name_array[$row['inserted_by']]['FULL_NAME']; ?></p></td>
			                    <td width="120"><p><?= $row['insert_date']; ?></p></td>
			                    <td width="120" class="wrd_brk"><p><?= $row['remarks']; ?></p></td>
			                    <td width="100" align="right"><p><?= number_format($work_order_qty,2); ?></p></td>
			                    <td width="100" align="right"><p><?= number_format($row['net_total_amount'],2); ?></p></td>
			                    <td width="100" align="right"><p><?= number_format($surplus_deficit,2); ?></p></td>
			                    <?
								foreach($electronic_user_arr as $val)
								{
									$approved_dateTime = $user_approval_array[$pi_id][27][$val];
									$approved_no=$user_approval_no[$pi_id][27][$val];
									$app_cause=$approv_pi_arr[$pi_id][$val];
									?>
									<td width="150" align="center"><?= $approved_dateTime; ?></td>
									<td width="250" class="wrd_brk"><?= $app_cause; ?></td>
									<td  class="wrd_brk"><?= $approved_no; ?></td>
									<?
								}
								?>
		                    </tr>
		                	<?
		                	$i++;
		                	$tot_work_order_qty += $work_order_qty;
		                	$tot_pi_qty += $row['net_total_amount'];
		                	$tot_surplus_deficit += $surplus_deficit;
		                }	
		                ?>    								
                    </tbody> 
                </table>
			</div>
			<table class="rpt_table" border="1" rules="all" width="<?= $table_width?>" cellpadding="0" cellspacing="0">
				<tfoot>					
					<tr>
						<th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120"><p>Total</p></th>
						<th width="100"><p><? echo number_format($tot_work_order_qty,2); ?></p></th>
						<th width="100"><p><? echo number_format($tot_pi_qty,2); ?></p></th>
						<th width="100" ><p><? echo number_format($tot_surplus_deficit,2); ?></p></th>
						<?
						for($k=1;$k<=$max_electronic_setup;$k++)
						{			
							?>
							<th width="150"><p></p></th>
							<th width="250"><p></p></th>
							<th ><p></p></th>
							<?
						}
						?>
					</tr>
				</tfoot>
			</table>
	    </div>
		<?
	}

	else if ($type == 3) // Report
	{
		$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
		$print_report_ids_short=explode(",",$print_report_format_ids_short);
		//echo "Select format_id from lib_report_template where template_name=".$cbo_company_name." and module_id=5 and report_id=183 and is_deleted=0 and status_active=1";

			$date_cond=""; $from_date=""; $to_date="";
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	   	{
		   if(str_replace("'","",trim($cbo_date_by))==1)
		   {
			   $date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
		   }
		   else if(str_replace("'","",trim($cbo_date_by))==2)
		   {
			   $date_cond	=" and a.APPROVED_DATE between '".str_replace("'","",trim($txt_date_from))."' and '".str_replace("'","",trim($txt_date_to))." 11:59:59 PM'";

		   }
	   	}

		$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
		$supplier_arr=return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id","custom_designation");

		// if($cbo_type==2) $approved_cond=" and a.approved=1 and a.ready_to_approved=1";		
		// else if($cbo_type==3) $approved_cond=" and a.approved=3 and a.ready_to_approved=1"; 
		// else if($cbo_type==1) $approved_cond=" and a.approved=0 and a.ready_to_approved=1"; 
		// else $approved_cond=" ";	

		// full - ready_to_app=1 is_app=1
		// partial- ready_to_app=1 is_app=3
		// panding- ready_to_app=1
		
		if($cbo_type==2) $approved_cond=" and a.approved=1 and a.ready_to_approved=1";		
		else if($cbo_type==3) $approved_cond=" and a.approved=3 and a.ready_to_approved=1"; 
		else if($cbo_type==1) $approved_cond=" and a.ready_to_approved=1 and a.approved not in(1,3)"; 
		else $approved_cond=" ";	

		if($db_type==0)	$select_pi_cat = " group_concat(b.item_category_id)";
		else $select_pi_cat = " listagg(b.item_category_id,',') within group(order by b.item_category_id)";

		// $sql="SELECT a.ID, a.IMPORTER_ID, a.SUPPLIER_ID, a.PI_DATE, a.PI_NUMBER, a.APPROVED, $select_pi_cat as ITEM_CATEGORY_IDS, a.INSERTED_BY, a.INSERT_DATE, a.REMARKS, a.NET_TOTAL_AMOUNT
		// from com_pi_master_details a, com_pi_item_details b 
		// where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $supplier_id_cond $pi_cond $date_cond $cate_cond $approved_cond and a.entry_form not in (1,0)  
		// group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, a.inserted_by, a.insert_date, a.remarks, a.net_total_amount";
		$sql="SELECT a.ID, a.IMPORTER_ID, a.SUPPLIER_ID, a.PI_DATE, a.PI_NUMBER, a.APPROVED,b.item_category_id as ITEM_CATEGORY_IDS, a.INSERTED_BY, a.INSERT_DATE, a.REMARKS, a.NET_TOTAL_AMOUNT,a.READY_TO_APPROVED,b.WORK_ORDER_NO,b.WORK_ORDER_ID
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $supplier_id_cond $pi_cond $date_cond $cate_cond $approved_cond and a.entry_form not in (1,0)  
		group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, a.inserted_by, a.insert_date, a.remarks, a.net_total_amount,b.item_category_id,a.ready_to_approved,b.WORK_ORDER_NO,b.WORK_ORDER_ID";
		//  echo $sql;die;
		$sql_res=sql_select($sql);

		$wo_order_dtl_no_arr=array();
		foreach ($sql_res as $row) {
			$pi_id .= $row['ID'].',';

			array_push($wo_order_dtl_no_arr, $row[csf('work_order_no')]);
		}
		$pi_ids = rtrim($pi_id,',');

		$pi_id_cond=where_con_using_array(array_filter(array_unique(explode(",", $pi_ids))),0,"a.id");

	
		
	    $sql_buyer_style = "SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 1 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 and d.item_category_id=1 $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 2 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
	    and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 $pi_id_cond
	    union all 
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 3 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 4 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) $pi_id_cond
	    union all
	    SELECT b.PI_ID, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 5 as TYPE 
	    from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
	    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 $pi_id_cond";

			$sql_buyer_style_res=sql_select($sql_buyer_style); // and a.pi_id=6201

			$pi_id_arr_new=array();
	
			foreach($sql_buyer_style_res as $row)
			{
				$pi_id_arr_new[$row['ORDER_ID']].=$row['PI_ID'].',';	        
			}

			// print_r($pi_id_arr)."<br>";

	

	    $sql_buyer_style1 = "SELECT b.PI_ID, b.work_order_dtls_id, 1 as TYPE
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.item_category_id=1  $pi_id_cond ";

	    //echo $sql_buyer_style1;
	   	$res_buyer1=sql_select($sql_buyer_style1);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer1 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row[csf('work_order_dtls_id')]);
	   		$po_id_data[$row[csf('work_order_dtls_id')]]=$row[csf('PI_ID')];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtls_id_arr)),0,"d.id");
	   	$sql_wo1 = "SELECT  e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 1 as TYPE
	    from  wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where   d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $company_name  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and d.item_category_id=1 $wo_cond";

	    //echo $sql_wo1;

	    $res_wo1=sql_select($sql_wo1);

	    $buyer_style_arr=array();
	    $work_order_qty_arr=array();
	    $pi_id_arr=array();
	    $order_ids='';
	    $tot_rows=0;
	    foreach($res_wo1 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['WORK_ORDER_DTLS_ID']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }


	    $sql_buyer_style2 = "SELECT b.PI_ID as PI_ID, b.work_order_id as WORK_ORDER_ID,b.quantity,b.rate, 2 as TYPE
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
	    and b.item_category_id=1 $pi_id_cond";

	    //echo $sql_buyer_style2;
	   	$res_buyer2=sql_select($sql_buyer_style2);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer2 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row['WORK_ORDER_ID']);
	   		$po_id_data[$row['WORK_ORDER_ID']]=$row['PI_ID'];
			   $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row[csf('quantity')]*$row[csf('rate')];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtls_id_arr)),0,"c.id");
	   	$sql_wo2 = "SELECT e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 2 as TYPE, c.id as WORK_ORDER_ID
	    from  wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where  c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
	    and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284  $wo_cond";


	   // echo $sql_wo2;
	    $res_wo2=sql_select($sql_wo2);

	   
	    foreach($res_wo2 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['WORK_ORDER_ID']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }






	    $sql_buyer_style3 = "SELECT b.PI_ID,b.WORK_ORDER_NO  ,3 as TYPE
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in(4,12,25) $pi_id_cond";
	    //echo $sql_buyer_style3;
	   	$res_buyer3=sql_select($sql_buyer_style3);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer3 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row['WORK_ORDER_NO']);
	   		$po_id_data[$row['WORK_ORDER_NO']]=$row['PI_ID'];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtls_id_arr)),1,"c.booking_no");
	   	$sql_wo3 = " SELECT  e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 3 as TYPE,c.BOOKING_NO 
	    from  wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where  c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0   $wo_cond";
	   // echo $sql_wo3;
	    $res_wo3=sql_select($sql_wo3);

	   
	    foreach($res_wo3 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['BOOKING_NO']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }


	     $sql_buyer_style3 = "SELECT b.PI_ID,b.WORK_ORDER_NO  ,3 as TYPE
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in(4,12,25) $pi_id_cond";
	    //echo $sql_buyer_style3;
	   	$res_buyer3=sql_select($sql_buyer_style3);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer3 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row['WORK_ORDER_NO']);
	   		$po_id_data[$row['WORK_ORDER_NO']]=$row['PI_ID'];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtls_id_arr)),1,"c.booking_no");
	   	$sql_wo3 = " SELECT  e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 3 as TYPE,c.BOOKING_NO 
	    from  wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where  c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0   $wo_cond";
	   // echo $sql_wo3;
	    $res_wo3=sql_select($sql_wo3);

	   
	    foreach($res_wo3 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['BOOKING_NO']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }


	    $sql_buyer_style4 = "SELECT b.PI_ID,b.WORK_ORDER_NO, 4 as TYPE
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.item_category_id in(2,3,13,14) $pi_id_cond";
	    //echo $sql_buyer_style3;
	   	$res_buyer4=sql_select($sql_buyer_style4);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer5 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row['WORK_ORDER_NO']);
	   		$po_id_data[$row['WORK_ORDER_NO']]=$row['PI_ID'];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtl_no_arr)),1,"c.booking_no");
	   	$sql_wo5 = "SELECT e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 4 as TYPE,c.BOOKING_NO,c.BOOKING_MST_ID
	    from  wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where  c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0    $wo_cond";
	//    echo $sql_wo5;
	    $res_wo5=sql_select($sql_wo5);
 
	   
	    foreach($res_wo5 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['BOOKING_NO']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['BOOKING_MST_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['BOOKING_MST_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['BOOKING_MST_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['BOOKING_MST_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['BOOKING_MST_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['BOOKING_MST_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['BOOKING_MST_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['BOOKING_MST_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['BOOKING_MST_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }


	    $sql_buyer_style5 = "SELECT b.PI_ID, b.WORK_ORDER_DTLS_ID, 5 as TYPE 
	    from com_pi_master_details a, com_pi_item_details b
	    where a.id=b.pi_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.item_category_id=24 $pi_id_cond";
	    //echo $sql_buyer_style3;
	   	$res_buyer5=sql_select($sql_buyer_style5);


	   	$wo_order_dtls_id_arr=array();

	   	$po_id_data=array();

	   	foreach ($res_buyer5 as $row) 
	   	{
	   		array_push($wo_order_dtls_id_arr, $row['WORK_ORDER_DTLS_ID']);
	   		$po_id_data[$row['WORK_ORDER_DTLS_ID']]=$row['PI_ID'];
	   	}

	   	$wo_cond=where_con_using_array(array_filter(array_unique($wo_order_dtls_id_arr)),0,"c.id");
	   	$sql_wo5 = "SELECT  e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 5 as TYPE 
	    from  wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
	    where  c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$company_name and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0   $wo_cond";
	   // echo $sql_wo3;
	    $res_wo5=sql_select($sql_wo5);

	   
	    foreach($res_wo5 as $row)
	    {
	    	$row['PI_ID']=$po_id_data[$row['WORK_ORDER_DTLS_ID']];
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].',';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'];
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }
		
	    // $sql_buyer_style_res=sql_select($sql_buyer_style); // and a.pi_id=6201

	   
	    //echo '<pre>'; print_r($work_order_qty_arr);die;

	    if ($order_ids != '')
	    {
	        $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
	        $order_id_cond = '';

	        if($db_type==2 && $tot_rows>1000)
	        {
	            $order_id_cond = ' and (';
	            $orderNoArr = array_chunk($orderIds,999);
	            foreach($orderNoArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
	            }
	            $order_id_cond = rtrim($order_id_cond,'or ');
	            $order_id_cond .= ')';
	        }
	        else
	        {
	            $orderIds = implode(',', $orderIds);
	            $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
	        }
	    }
	    //echo $order_id_cond;
	    $sql_lcSc="SELECT a.wo_po_break_down_id as ORDER_ID, b.export_lc_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 1 as TYPE from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.status_active=1 
	    $order_id_cond
	    group by a.wo_po_break_down_id, b.export_lc_no, b.internal_file_no 
	    union all
	    select a.wo_po_break_down_id as ORDER_ID, b.contract_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 2 as TYPE from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 $order_id_cond
	    group by a.wo_po_break_down_id, b.contract_no, b.internal_file_no";
		
	    // $sql_lcSc_res=sql_select($sql_lcSc);
	    // $lcSc_arr=array();
	    // foreach ($sql_lcSc_res as $row) 
	    // {
	    //     $last_sip_date=$lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['LAST_SHIPMENT_DATE']=$row['LAST_SHIPMENT_DATE'];
	    //    $lc_data= $lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['LS_SC_NO']=$row['LS_SC_NO'];
	    //     $lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['FILE_NO']=$row['FILE_NO'];
	    // }

		$sql_lcSc_res=sql_select($sql_lcSc);
	    $lcSc_arr=array();
	    foreach ($sql_lcSc_res as $row) 
	    {
			$pi_id_loop=array_unique(explode(",",chop($pi_id_arr_new[$row['ORDER_ID']],',')));
			foreach($pi_id_loop as $val)
			{
				$lcSc_arr[$val]['LAST_SHIPMENT_DATE'].=change_date_format($row['LAST_SHIPMENT_DATE']).',';
				$lcSc_arr[$val]['LS_SC_NO'].=$row['LS_SC_NO'].',';
				$lcSc_arr[$val]['FILE_NO'].=$row['FILE_NO'].',';
			}
	    }
		// print_r($lcSc_arr);die;
		// echo $lc_data;die;


	    $user_name_array=array();
		$userData=sql_select( "select ID, USER_NAME, USER_FULL_NAME, designation from user_passwd");
		foreach($userData as $user_row)
		{
			$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
			$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
			$user_name_array[$user_row['ID']]['DESIGNATION']=$designation_array[$user_row['DESIGNATION']];	
		}

		$sql_electronic_app="select USER_ID, APPROVED_BY, SEQUENCE_NO from electronic_approval_setup where company_id=$cbo_company_name and entry_form=27  and is_deleted=0 order by sequence_no";
		//echo $sql_electronic_app;
		$sql_electronic_app_res=sql_select($sql_electronic_app);
		$max_electronic_setup=0;
		$electronic_user_arr=array();
	    foreach ($sql_electronic_app_res as $val)
	    { 
	    	$electronic_user_arr[$val['USER_ID']] = $val['USER_ID'];   	
	    	$max_electronic_setup++;
	    }
	   // echo $max_electronic_setup;
	    //echo '<pre>';print_r($user_name_array);
	    $pi_id_mst_id_cond=str_replace("a.id", "mst_id", $pi_id_cond);
	    $user_approval_array=array(); $max_approval_date_array=array();
		$sql_approval="select ENTRY_FORM, MST_ID, APPROVED_BY, APPROVED_DATE, CURRENT_APPROVAL_STATUS,APPROVED_NO from approval_history where entry_form in (27) $pi_id_mst_id_cond order by APPROVED_NO asc"; // and current_approval_status=1
		//echo $sql_approval;
		$sql_approval_res=sql_select($sql_approval);
		foreach ($sql_approval_res as $row)
		{
			$user_approval_array[$row['MST_ID']][$row['ENTRY_FORM']][$row['APPROVED_BY']]=$row['APPROVED_DATE'];
			$approval_status_arr[$row['MST_ID']][$row['ENTRY_FORM']]['approved_no']=$row[csf('CURRENT_APPROVAL_STATUS')];
			$approval_by_arr[$row['MST_ID']][$row['ENTRY_FORM']]['approved_by']=$row[csf('APPROVED_BY')];
			$approval_by_arr[$row['MST_ID']][$row['ENTRY_FORM']]['approved_date']=$row[csf('APPROVED_DATE')];
		}

		//echo $pi_ids.'system';
		$pi_id_booking_id_cond=str_replace("a.id", "booking_id", $pi_id_cond);
		$sql_app="select max(id) as ID, max(booking_id) as PI_ID from fabric_booking_approval_cause where  entry_form=27 $pi_id_booking_id_cond and approval_type=0 and status_active=1 and is_deleted=0";
		$sql_app_res=sql_select($sql_app);		
		foreach ($sql_app_res as $val) {
			$approv_cause_id .=$val['ID'].',';
		}
		$approv_cause_ids = rtrim($approv_cause_id,',');

		$approv_cause_cond=where_con_using_array(array_filter(array_unique(explode(",", $approv_cause_ids))),0,"id");

		$app_cause=sql_select("select BOOKING_ID, USER_ID, APPROVAL_CAUSE from fabric_booking_approval_cause where  status_active=1 and is_deleted=0 $approv_cause_cond");
		$approv_pi_arr=array();
		foreach ($app_cause as $val) {
			$approv_pi_arr[$val['BOOKING_ID']][$val['USER_ID']]=$val['APPROVAL_CAUSE'];
		}

		$data_file=sql_select("select IMAGE_LOCATION, MASTER_TBLE_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$file_arr=array();
		foreach($data_file as $row)
		{
			$file_arr[$row['MASTER_TBLE_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($data_file);
		$table_width=1900+$max_electronic_setup*400;

		ob_start();
		?>
		<div style="width:<?= $table_width; ?>px; max-height:350px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
                <thead>
                	<tr>
	                    <th width="30" >SL</th>
	                    <th width="70" >System ID</th>
	                    <th width="100" >PI No</th>
						<th width="100" >Item Category</th>
	                    <th width="100" >PI Date</th>
	                    <th width="100" >Supplier Name</th>
	                    <th width="100" >View File</th>
						<th width="120" >Buyer</th>
						<th width="120" >Style</th>	                                  
	                    <th width="100" >Insert By</th>	  
						<th width="90" >Ready for Approval</th>	 
						<th width="100" >Approval Status</th>	                 
						<th width="100" >Approved by</th>							
	                    <th width="100" >Total WO Value</th>
	                    <th width="100" >Total PI Value</th>
	                    <th width="100" >Surplus/(Deficit)</th>
	                    <th width="100" >SC/LC NO</th>	  
						<th width="120" >Last Shipment Date</th>                
						<th width="120" >Remarks</th>
                    </tr>                   
                </thead>
            </table>
            <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                    	<? 					
						$i=1;
						$tot_work_order_qty=0;
						$tot_pi_qty=0;
						$tot_surplus_deficit=0;
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						foreach ($sql_res as $row) 
						{
							// $lcSc_arr[$row['ID']]['LS_SC_NO'];

							$work_order_qty = $work_order_qty_arr[$row['WORK_ORDER_ID']]['WORK_ORDER_DTLS_ID'];
							$surplus_deficit = $work_order_qty-$row['NET_TOTAL_AMOUNT'];
							$approval_date=$user_approval_array[$row['ID']][$approved_no_array[27][$row[csf('id')]]][$val][27];	

							$row_id=$print_report_ids_short[0];
							$print_name='print';
							if($row_id==86)
							{
								$print_name='print';
							}
							else if($row_id==116 && $row['ITEM_CATEGORY_IDS']==3)
							{
								$print_name='print_wf';
							}
							else if($row_id==751 && $row['ITEM_CATEGORY_IDS']==4 )
							{
								$print_name='print_pi';
							}
							$cbo_item_category_id=$row['ITEM_CATEGORY_IDS'];
							$entry_form='';
							if( $cbo_item_category_id == "1")
							{
								 $entry_form = "165";
							}
							else if( $cbo_item_category_id == "2" ||  $cbo_item_category_id == "3" ||  $cbo_item_category_id == "13" ||  $cbo_item_category_id == "14")
							{
								 $entry_form = "166";
							}
							else if( $cbo_item_category_id == "4")
							{
								 $entry_form = "167";
							}
							else if( $cbo_item_category_id == "12")
							{
								 $entry_form = "168";
							}
							else if( $cbo_item_category_id == "24")
							{
								 $entry_form = "169";
							}
							else if( $cbo_item_category_id == "25" || $cbo_item_category_id == "102" || $cbo_item_category_id == "103")
							{
								 $entry_form = "170";
							}
							else if( $cbo_item_category_id == "30")
							{
								 $entry_form = "197";
							}
							else if( $cbo_item_category_id == "31")
							{
								 $entry_form = "171";
							}
							else if( $cbo_item_category_id == "5" ||  $cbo_item_category_id == "6" ||  $cbo_item_category_id == "7" ||  $cbo_item_category_id == "23")
							{
								 $entry_form = "227";
							}
							else
							{
								 $entry_form = "172";
							}

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><?= $i; ?></td>
			                    <td width="70"><p><?= $row['ID']; ?></p></td>
			                    <td width="100" style='color:#000' align="center"><a href='##' onClick="print_report('<?= $company_name.'*'.$row['ID'].'*'.$entry_form.'*'.implode(',',array_unique(explode(",",$row['ITEM_CATEGORY_IDS'])));?>','<?=$print_name;?>', '../../commercial/import_details/requires/pi_print_urmi')"><p><font color="blue"><b><?= $row["PI_NUMBER"];?></p></b></font></a></td>			          
								<td width="100" align="center"><p><?=$item_category[$row['ITEM_CATEGORY_IDS']];?></p></td>
			                    <td width="100" align="center"><p><?= change_date_format($row['PI_DATE']); ?></p></td>
			                    <td width="100"><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
			                    <td width="100" align="center"><p>
			                    	<a href="javascript:void()" onClick="downloiadFile('<?= $row['ID']; ?>');">
                                <? if ($file_arr[$row['ID']]['FILE'] != '') echo 'View File'; ?></a>
			                    </p></td>
								<td width="120"><p>
			                    	<? 
			                    	$buyer_id=chop($buyer_style_arr[$row['WORK_ORDER_ID']]['BUYER_NAME'],',');
                                    $buyer_name=array_unique(explode(',', $buyer_id));
                                    $comma_separate_buyer="";
                                    foreach ($buyer_name as $key => $val) 
                                    {
                                        if ($comma_separate_buyer=="") 
                                        {
                                           $comma_separate_buyer.=$buyer_arr[$val];
                                        }
                                        else
                                        {
                                            $comma_separate_buyer.=','.$buyer_arr[$val];
                                        }
                                    }
                                    echo $comma_separate_buyer;
                                    ?>
                                </p></td>
								<td width="120"><p><?=$buyer_style_arr[$row['WORK_ORDER_ID']]['STYLE_REF_NO']; ?></p></td>			                   
			                   
			                    <td width="100" align="center"><p><?= $user_name_array[$row['INSERTED_BY']]['FULL_NAME']."<br>".$row['INSERT_DATE']; ?></p></td>
			                    <td width="90" align="center"><p><? if($row[csf('ready_to_approved')]==1){echo "Yes";}else{ echo "No";}; ?></p></td>
								<td width="100"  align="center"><p><? 	
									if($row[csf('ready_to_approved')]==1 && $row[csf('approved')]==1){
										echo "Full Approved";
									}elseif($row[csf('ready_to_approved')]==1 && $row[csf('approved')]==3){
										echo "Partial Approved";
									}else{
										echo "Pending";
									} 
								 ?></p></td>
								<?
							
									// $approved_dateTime = $user_approval_array[$row['ID']][27][$val];
									$approved_dateTime=$approval_by_arr[$row['ID']][27]['approved_date'];
									$app_cause=$approv_pi_arr[$row['ID']][$val];
									$approval_by=$approval_by_arr[$row['ID']][27]['approved_by'];
									
									?>
									<td width="100" align="center"><?
										if($approval_status_arr[$row['ID']][27]['approved_no']==1){
									echo $user_name_array[$approval_by]['FULL_NAME']."<br>".$approved_dateTime;} ?></td>
									
									<?
									
								?>
			                    <td width="100" align="right"><p><?= number_format($work_order_qty,2); ?></p></td>
			                    <td width="100" align="right"><p><?= number_format($row['NET_TOTAL_AMOUNT'],2); ?></p></td>
			                    <td width="100" align="right"><p><?= number_format($surplus_deficit,2); ?></p></td>			                   
			                    <td width="100"><p><?=implode(", ",array_unique(explode(",",chop($lcSc_arr[$row['ID']]['LS_SC_NO'],','))));  //$lcSc_arr[$row['ID']]['LS_SC_NO']; ?></p></td>
								<td width="120" align="center"><p><?=implode(", ",array_unique(explode(",",chop($lcSc_arr[$row['ID']]['LAST_SHIPMENT_DATE'],','))));////$lcSc_arr[$row['ID']]['LAST_SHIPMENT_DATE']); ?>&nbsp;</p></td>
								<td width="120"><p><?= $row['REMARKS']; ?></p></td>
		                    </tr>
		                	<?
		                	$i++;
		                	$tot_work_order_qty += $work_order_qty;
		                	$tot_pi_qty += $row['NET_TOTAL_AMOUNT'];
		                	$tot_surplus_deficit += $surplus_deficit;
		                }	
		                ?>    								
                    </tbody> 
                </table>
			</div>
			<table class="rpt_table" border="1" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0">
				<tfoot>					
					<tr>
						<th width="30">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>					
						<th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>				
						<th width="100"><p>Total</p></th>
						<th width="100"><p><? echo number_format($tot_work_order_qty,2); ?></p></th>
						<th width="100"><p><? echo number_format($tot_pi_qty,2); ?></p></th>
						<th width="100"><p><? echo number_format($tot_surplus_deficit,2); ?></p></th>					
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120"></th>
					</tr>
				</tfoot>
			</table>
	    </div>
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

if($action=="get_user_pi_file")
{
    extract($_REQUEST);  
    $img_sql = "SELECT ID, IMAGE_LOCATION, MASTER_TBLE_ID, REAL_FILE_NAME from common_photo_library where form_name='proforma_invoice' and master_tble_id='$id'";
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file='.urlencode($img["IMAGE_LOCATION"]).'"><img src="../../../file_upload/blank_file.png" width="89px" height="97px"></a><br>'.$img["REAL_FILE_NAME"].'</p>';   
    }
}

if($action=="downloiadFile")
{
    if(isset($_REQUEST["file"]))
    {        
        $file = urldecode($_REQUEST["file"]); // Decode URL-encoded string   
        
        $filepath = "../../../" . $file;  
        // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        }
    }
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=184 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){
		if($id==147)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" />';	
		if($id==195)$buttonHtml.='<input type="button" id="show_button2" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(2);" />';
		if($id==107)$buttonHtml.='<input type="button" id="show_button3" class="formbutton" style="width:50px" value="Report" onClick="fn_report_generated(3);" />';	
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

?>