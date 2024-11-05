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
						    $search_by_arr=array(1=>"Booking No",2=>"Job No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'trims_approval_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$search_string=$data[3];

	  $arr=array (0=>$company_arr,1=>$buyer_arr);
	
	  if($search_by==1)
	  {
	    if($search_string!='' ) $search_field="and b.booking_no_prefix_num=$search_string";
		else  $search_field="";
	  }
	  else if($search_by==2)
	  {
	    if($search_string!='' ) $search_field="and a.job_no_prefix_num=$search_string";
		else  $search_field="";
	  }
	   //else $search_field="a.job_no_prefix_num LIKE '%".$search_string."%'";
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and b.buyer_id=$data[1]";
		
		$sql= "select b.id,b.booking_no_prefix_num,c.job_no,b.company_id,b.buyer_id from wo_booking_mst b ,wo_po_details_master a,wo_booking_dtls c where a.job_no=c.job_no and b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id   and b.item_category=4 $buyer_id_cond $search_field group by b.id,b.booking_no_prefix_num,c.job_no,b.company_id,b.buyer_id order by id";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No", "120,120,120","600","240",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "company_id,buyer_id,0,0", $arr , "company_id,buyer_id,booking_no_prefix_num,job_no", "",'','0,0,0,0','',1) ;
	
   exit(); 
} 

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
 
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.booking_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and a.booking_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}
	
	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	//if($from_date>$to_date)
	
	
	$type = str_replace("'","",$cbo_type);
	$buyer_id_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$hide_booking_id=str_replace("'","",$hide_booking_id);
	$hide_job_id=str_replace("'","",$hide_job_id);
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	
	if($hide_job_id!='')
	{
		if($txt_job_no=="") $job_cond=""; else $job_cond=" and b.job_no_prefix_num in('".implode("','",explode("*",$txt_job_no))."')";
	}
	else
	{
		if($txt_job_no=="") $job_cond=""; else $job_cond=" and b.job_no_prefix_num=$txt_job_no";
	}
	
	if($hide_booking_id!='')
	{
		if($txt_booking_no=="") $booking_cond=""; else $booking_cond=" and a.booking_no_prefix_num in('".implode("','",explode("*",$txt_booking_no))."')";
	}
	else
	{
		if($txt_booking_no=="") $booking_cond=""; else $booking_cond=" and a.booking_no_prefix_num=$txt_booking_no";
	}
	if($cbo_booking_type==1) 
		{
			$booking_type_cond="";
			//$type=3;
		}
		else if($cbo_booking_type==2) //Short
		{
			$booking_type_cond="and a.is_short=2";
			//$type=3;
		}
		else if($cbo_booking_type==3) //Main
		{
			$booking_type_cond="and a.is_short=1";
			//$type=3;
		}
		else if($cbo_booking_type==4) //Trims Sample
		{
			$booking_type_cond="and a.booking_type=5";
			//$type=3;
		}
		
	
	if(str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and b.buyer_name=$cbo_buyer_name"; else  $buyer_id_cond="";

	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");
	
	/*$po_array=array();
	$poData=sql_select( "select id, po_number, pub_shipment_date from wo_po_break_down");
	foreach($poData as $po_row)
	{
		$po_array[$po_row[csf('id')]]['no']=$po_row[csf('po_number')];
		$po_array[$po_row[csf('id')]]['ship_date']=change_date_format($po_row[csf('pub_shipment_date')]);
	}*/
	
	$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
	
	$user_name_array=array();
	$userData=sql_select( "select id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}
	if($db_type==0)
	{
		$job_details_sql=sql_select("select a.job_no,a.style_ref_no,group_concat(b.po_number) as po_number, min(b.pub_shipment_date) as min_ship_date from  wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst group by a.job_no,a.style_ref_no");
	}
	else
	{
		$job_details_sql=sql_select("select a.job_no,a.style_ref_no,LISTAGG((cast(b.po_number as varchar2(4000) )), ',') WITHIN GROUP (ORDER BY po_number)   as po_number, min(b.pub_shipment_date) as min_ship_date from  wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst group by a.job_no,a.style_ref_no");
	}
	$job_details_arr=array();
	foreach($job_details_sql as $inf)
	{
		$job_details_arr[$inf[csf('job_no')]][order_no]=$inf[csf('po_number')];
		$job_details_arr[$inf[csf('job_no')]][min_ship_date]=$inf[csf('min_ship_date')];
		$job_details_arr[$inf[csf('job_no')]][style_ref_no]=$inf[csf('style_ref_no')];		
	}
	//print_r($job_details_arr);die;
	//$approved_no_array=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=7 group by mst_id","mst_id","approved_no");
	$approved_no_array=array();
	$queryApp="select entry_form, mst_id, max(approved_no) as approved_no from approval_history where entry_form=8 group by entry_form, mst_id";
	$resultApp=sql_select( $queryApp );
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('entry_form')]][$row[csf('mst_id')]]=$row[csf('approved_no')];
	}
	//echo ($approved_no_array[15][2388]).uuui;
	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=15 and bypass=2", "user_id", "buyer_id" );
	//print_r($buyer_id_arr);
	
	/*if($db_type==0)
	{
		$signatory_data_arr=sql_select("select group_concat(case when entry_form=8 then user_id end) as user_id, group_concat(case when entry_form=8 and bypass=2 then user_id end) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0");
	}
	else
	{
		$signatory_data_arr=sql_select("select LISTAGG(case when entry_form=8 then user_id end, ',') WITHIN GROUP (ORDER BY user_id) as user_id,  LISTAGG(case when entry_form=8 and bypass=2 then user_id end, ',') WITHIN GROUP (ORDER BY user_id) as user_idby from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0");	
	}
	
	$signatory_main=$signatory_data_arr[0][csf('user_id')];
	$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idby')];*/
	
	
	$signatory_data_arr=sql_select("select user_id, sequence_no from electronic_approval_setup where company_id=$cbo_company_name and entry_form=8 and is_deleted=0");	
	foreach($signatory_data_arr as $row)
	{
		if($row[csf("user_id")]>0)
		{
			$signatory_main[$row[csf("sequence_no")]]=$row[csf("user_id")];
			$bypass_no_user_id_main[$row[csf("sequence_no")]]=$row[csf("user_id")];
		}
	}
	//$bypass_no_user_id_main=chop($bypass_no_user_id_main,",");
	//$signatory_main=$signatory_data_arr[0][csf('user_id')];
	//$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idby')];
	
	

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form =8";
	$result=sql_select( $query );
	foreach ($result as $row)
	{
		//$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
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
	//print_r($max_approval_date_array);
	ob_start();
	?>
        <fieldset style="width:1660px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1640" class="rpt_table" >
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
                    <th width="80">Booking Type</th>
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th width="100">Approve No</th>
                    <th width="">Remark</th>
                </thead>
            </table>
			<div style="width:1640px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1622" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
						
							$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
							$rowspanMain=count($signatory_main);
							//$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
							
							if($type==2) $approved_cond=" and a.is_approved=1"; else $approved_cond="";
							// and a.booking_type in(2,5)
							 $sql="select a.id,b.company_name,a.is_short,a.booking_no, b.buyer_name,a.booking_date, b.job_no, a.is_approved as approved, a.insert_date,a.remarks from  wo_booking_mst a, wo_po_details_master b,wo_booking_dtls c where c.job_no=b.job_no and a.booking_no=c.booking_no and b.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and c.booking_type in(2,5) $buyer_id_cond  $job_cond $approved_cond $date_cond  $booking_cond $booking_type_cond group by a.id,b.company_name, b.buyer_name,a.booking_date,a.is_short,a.booking_no, b.job_no, a.is_approved,a.remarks, a.insert_date order by a.insert_date desc";
							//echo $sql;
							
							
                            $nameArray=sql_select( $sql);
                            foreach ($nameArray as $row)
                            {
								$full_approval='';
								//print_r($bypass_no_user_id_main);
								//$signatory=$signatory_main;
								if($row[csf('booking_type')]==4) 
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
									$type=$row[csf('is_short')];
								}
								
								$rowspan=$rowspanMain;
								
								/*$full_approval=true; $approvedStatus="";
								foreach($bypass_no_user_id_main as $uId)
								{
									$buyer_ids=$buyer_id_arr[$uId];
									$buyer_ids_array=explode(",",$buyer_id_arr[$uId]);
									//echo $row[csf('buyer_name')]."<br>";
									//print_r($buyer_ids_array);//die;
									//.'=';print_r($buyer_ids_array);
									if($buyer_ids=="" || in_array($row[csf('buyer_name')],$buyer_ids_array))
									{
										//$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]][$row[csf('entry_form')]]=$row[csf('approved_date')];
										//print_r($user_approval_array[5437][1][137]);
										//echo "<br>";
										//echo $row[csf('id')]."=".$approved_no_array[8][$row[csf('id')]]."=".$uId;
										$approvedStatus=$user_approval_array[$row[csf('id')]][$approved_no_array[8][$row[csf('id')]]][$uId];
										if($approvedStatus=="")
										{
											//echo $approvedStatus.'sdsd';
											$full_approval=false;
											break;
										}
									}
								}*/
								
								//die;
								
								if($cbo_date_by==3 && $from_date!="" && $to_date!="")
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
								//echo $type.'='.$full_approval.'='.$row[csf('approved')].'='.$print_cond;die;
								//if($full_approval==false) echo "sdsd";
								//if(((($type==1 && $full_approval==false) || $row[csf('approved')]==2) || ($type==2 && $full_approval==true))  && $print_cond==1)
								//{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									//echo 'dss';
									$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
									$z=0; 
									foreach($signatory_main as $val)
									{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<?
										if($z==0)
										{
											if(str_replace("'","",trim($cbo_date_by))==1)
											{
												$date_all="C Date : ".change_date_format($row[csf('booking_date')]);
											}
											else if(str_replace("'","",trim($cbo_date_by))=='2')
											{
												$insert_date=$row[csf('insert_date')];
												$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
											}
										?>
											<td width="40" rowspan="<? echo $rowspan; ?>" title="<? echo $approvedStatus; ?>"><? echo $i; ?></td>
											<td width="100" rowspan="<? echo $rowspan; ?>" align="center"><p><a href="##" onClick="generate_report(<? echo $cbo_company_name; ?>,'<? echo $row[csf('job_no')]; ?>',<? echo $row[csf('buyer_name')]; ?>,'<? echo $job_details_arr[$row[csf('job_no')]][style_ref_no]; ?>','<? echo $row[csf('booking_date')]; ?>','preCostRpt')"  ><? echo $row[csf('job_no')]; ?></a></p></td>
											<td width="80" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                            <td width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
                                            <td width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','file');">View</a></td>
											<td width="170" rowspan="<? echo $rowspan; ?>">
                                            	<p>
                                                	<?
														if($type==2)
														{
														?>
                                                			<a href="##" onClick="generate_report(<? echo $cbo_company_name; ?>,'<? echo $row[csf('job_no')]; ?>',<? echo $row[csf('buyer_name')]; ?>,'<? echo $job_details_arr[$row[csf('job_no')]][style_ref_no]; ?>','<? echo $row[csf('booking_date')]; ?>','preCostRpt2')"  ><? echo $job_details_arr[$row[csf('job_no')]][order_no]; ?></a>
                                                		<?
														}
														else
														{
															echo $job_details_arr[$row[csf('job_no')]][order_no];
														}
													?>
                                                </p>
                                            </td>
                                            <td width="80" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo change_date_format($job_details_arr[$row[csf('job_no')]][min_ship_date]); ?></p></td>
											<td width="120" rowspan="<? echo $rowspan; ?>">
												<p>
                                                	<?
														echo $row[csf('booking_no')];
														/*if($type==2)
														{
														?>
                                                			<a href="##" onClick="generate_report(<? echo $cbo_company_name; ?>,'<? echo $row[csf('job_no')]; ?>',<? echo $row[csf('buyer_name')]; ?>,'<? echo $job_details_arr[$row[csf('job_no')]][style_ref_no]; ?>','<? echo $row[csf('booking_date')]; ?>','bomRpt')"  ><? echo $row[csf('booking_date')]; ?></a>
                                                		<?
														}
														else
														{
															echo 	$row[csf('booking_no')];
														}*/
													?>
                                                	
                                                </p>
											</td>
                                            <td width="80" rowspan="<? echo $rowspan; ?>" align="center"><p><? echo $booking_type; ?></p></td>
                                            
										<?
										}
										
										$approved_no=''; $user_ip='';
									
										$approval_date=$user_approval_array[$row[csf('id')]][$approved_no_array[8][$row[csf('id')]]][$val];
										$user_ip=$user_ip_array[$row[csf('id')]][$approved_no_array[8][$row[csf('id')]]][$val];
										if($approval_date!="") $approved_no=$approved_no_array[8][$row[csf('id')]];
										
										
										$date=''; $time=''; 
										if($approval_date!="") 
										{
											$date=date("d-M-Y",strtotime($approval_date)); 
											$time=date("h:i:s A",strtotime($approval_date)); 
										}
										?>
											<td width="140" title="<? echo $row[csf('id')]."==".$approved_no_array[8][$row[csf('id')]]."==".$val;?> "><p><? echo $user_name_array[$val]['full_name']." (".$user_name_array[$val]['name'].")"; ?>&nbsp;</p></td>
                                            <td width="130"><p><? echo $user_name_array[$val]['designation']; ?>&nbsp;</p></td>
                                          
											<td width="100" align="center"><p><? if($row[csf('approved')]!=0) echo $date; ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row[csf('approved')]!=0) echo $time; ?>&nbsp;</p></td>
											<td width="100" ><p>&nbsp;<? if($row[csf('approved')]!=0) echo $approved_no; ?>&nbsp;</p></td>
										  <td  align="center"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                                        </tr>
									<?
										$z++;
									}
								$i++;
								//}
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