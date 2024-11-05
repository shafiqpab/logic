<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
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
						    $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Internal Ref");
							$dd="change_search_event(this.value, '0*0*', '0*0*', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'dyes_chemical_po_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$type_id=$data[4];

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($search_by==1) $search_field="a.job_no"; 
	else if($search_by==2) $search_field="a.style_ref_no"; 
	else $search_field="b.grouping";
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
		
	if($type_id==2)
	{
		$sql= "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by a.id DESC";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No,", "120,120,120","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no", "",'','0,0,0,0','',1) ;
	}
	else
	{
		 $sql= "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.grouping from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond group by a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.grouping  order by a.id DESC";
		
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No,Internal Ref. No", "120,120,120,100","600","240",0, $sql , "js_set_value", "id,grouping", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,grouping", "",'','0,0,0,0','',1) ;
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
 
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.costing_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and a.insert_date between $txt_date_from and $txt_date_to";
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
	$txt_requistion_no = str_replace("'","",$txt_requistion_no);
	$buyer_id_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $job_cond=""; else $job_cond=" and a.job_no in('".implode("','",explode("*",$txt_job_no))."')";
	if($txt_requistion_no=="") $ref_cond=""; else $ref_cond=" and b.grouping in('".implode("','",explode("*",$txt_requistion_no))."')";
	//echo $ref_cond.'d';die;
	
	if(str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and b.buyer_name=$cbo_buyer_name"; else  $buyer_id_cond="";

	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation" );
	
	$user_name_array=array();
	$userData=sql_select( "select id, user_name, user_full_name, designation, buyer_id from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];
		$user_name_array[$user_row[csf('id')]]['buyer_id']=$user_row[csf('buyer_id')];	
	}
	if($db_type==0) $group_con="group_concat( distinct b.id) AS po_id";
	if($db_type==2) $group_con="listagg(b.id ,',') within group (order by b.id) AS po_id";
						
	$jobSql=sql_select("select a.id as job_id, a.dealing_marchant, a.job_no, b.id as po_id, b.grouping, b.po_number as po_number, min(b.pub_shipment_date) as min_ship_date from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst $job_cond $ref_cond group by a.id, a.dealing_marchant, b.id, a.job_no, b.po_number, a.style_ref_no, b.grouping");
//	echo "select a.id as job_id,a.dealing_marchant,a.job_no,a.style_ref_no,b.id as po_id,b.grouping,b.po_number as po_number, min(b.pub_shipment_date) as min_ship_date from  wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst $job_cond $ref_cond group by a.id,a.dealing_marchant, b.id,a.job_no,b.po_number,a.style_ref_no,b.grouping";
	$jobArr=array();
	foreach($jobSql as $inf)
	{
		$jobArr[$inf[csf('job_no')]][order_no].=$inf[csf('po_number')].',';
		$jobArr[$inf[csf('job_no')]][grouping].=$inf[csf('grouping')].',';
		$jobArr[$inf[csf('job_no')]][order_id].=$inf[csf('po_id')].',';
		$jobArr[$inf[csf('job_no')]][pono].=$inf[csf('po_number')].'**';
		$jobArr[$inf[csf('job_no')]][min_ship_date]=$inf[csf('min_ship_date')];
		$jobArr[$inf[csf('job_no')]][dealing_marchant]=$inf[csf('dealing_marchant')];
		$po_array[$inf[csf('po_id')]]=$inf[csf('po_number')];
		
		$job_arr[$inf[csf('job_no')]]=$inf[csf('job_id')];		
	}
	unset($jobSql);
	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=15 and bypass=2", "user_id", "buyer_id" );
	
	$signatory_data_arr=sql_select("select user_id as user_id, buyer_id, sequence_no,bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=15 order by sequence_no");	
	
	foreach($signatory_data_arr as $sval)
	{
		if($sval[csf('buyer_id')]!="")
		{
			$exbid=explode(",",$sval[csf('buyer_id')]);
			foreach($exbid as $elecBid)
			{
				$signatory_main[$elecBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
			}
		}
		else
		{
			$adminUserBuyerId=$user_name_array[$sval[csf('user_id')]]['buyer_id'];
			if($adminUserBuyerId!="")
			{
				$exadminbid=explode(",",$adminUserBuyerId);
				foreach($exadminbid as $adminBid)
				{
					$signatory_main[$adminBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
				}
			}
			else
			{
				foreach($buyer_arr as $libBid=>$libbname)
				{
					$signatory_main[$libBid][$sval[csf('user_id')]]=$sval[csf('bypass')];
				}
			}
		}
	}
	//print_r($signatory_main);die;
	//$signatory_main=$signatory_data_arr[0][csf('user_id')];
	$bypass_no_user_id_main=$signatory_data_arr[0][csf('user_idby')];

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array(); $approved_no_array=array();
	$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form =15 and un_approved_by=0";
	$result=sql_select( $query );
	foreach ($result as $row)
	{
		//$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_no')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]][$row[csf('approved_date')]]=$row[csf('approved_no')];
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
		$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('user_ip')];
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
    <fieldset style="width:1040px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <!-- <th width="110">Job No</th>       -->
                <th width="80">Buyer Name</th>             
                <th width="170">Order No</th>               
                <th width="140">Signatory</th>
                <th width="130">Designation</th>
                <th width="100">Approval Date</th>
                <th width="100">Approval Time</th>
                <th>Approve No</th>
            </thead>
        </table>
        <div style="width:1080px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">
                <tbody>
                    <? 
                    $print_reportSql="Select report_id, format_id from lib_report_template where template_name =".$cbo_company_name." and module_id=2 and report_id in(22,43,122) and is_deleted=0 and status_active=1";
					$print_reportSqlRes=sql_select( $print_reportSql);
                    foreach($print_reportSqlRes as $prow)
                    {
						if($prow[csf('report_id')]==22)//BOM Old knit
                        { 
                            $exformatid=explode(",",$prow[csf('format_id')]);
                            $format_idknitold=$exformatid[0];
                        }
                        if($prow[csf('report_id')]==43)//BOM V2 knit
                        { 
                            $exformatid=explode(",",$prow[csf('format_id')]);
                            $format_idknit=$exformatid[0];
                        }
                        else if($prow[csf('report_id')]==122)//BOM V2 Woven
                        {
                             $exformatid=explode(",",$prow[csf('format_id')]);
                             $format_idwvn=$exformatid[0];
                        }
						else//BOM V2 Sweater
                        {
                             $exformatid=explode(",",$prow[csf('format_id')]);
                             $format_idsweater=$exformatid[0];
                        }
                    }
                    //echo $report_precost.'='.$report_precost_v2;
                    //$print_report_ids=$print_report_ids[0];
                    //echo $print_report_precost.'='.$print_report_precost_v2;
					$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
					//$rowspanMain=count($signatory_main);
                        
                    $bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
                    if($type==2) $approved_cond=" and a.approved=1";
                    elseif($type==1) $approved_cond=" and a.approved=3";
                    else $approved_cond=" and a.approved in (0,2)";
                    $job_id=implode(",",$job_arr);
                    $poIds=chop($job_id,','); $po_cond_for_in=""; 
                    $po_ids=count(array_unique(explode(",",$job_id)));
                    if($db_type==2 && $po_ids>1000)
                    {
                        $po_cond_for_in=" and (";
                        $poIdsArr=array_chunk(explode(",",$poIds),999);
                        foreach($poIdsArr as $ids)
                        {
                            $ids=implode(",",$ids);
                            $po_cond_for_in.=" b.id in($ids) or"; 
                        }
                        $po_cond_for_in=chop($po_cond_for_in,'or ');
                        $po_cond_for_in.=")";
                    }
                    else
                    {
                        $poIds=implode(",",array_unique(explode(",",$poIds)));
                        $po_cond_for_in=" and b.id in($poIds)";
                    }
                    
                    $sql="select a.id, b.garments_nature, b.company_name, b.buyer_name, b.style_ref_no, a.entry_from, a.costing_date, a.job_no, a.approved, a.insert_date from  wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $buyer_id_cond $job_cond $approved_cond $date_cond $po_cond_for_in group by a.id, b.garments_nature, b.company_name, b.buyer_name, b.style_ref_no, a.entry_from, a.costing_date, a.job_no, a.approved, a.insert_date order by a.id desc";
					//echo $sql;
					$nameArray=sql_select( $sql);
					foreach ($nameArray as $row)
					{
						$full_approval='';
						//print_r($bypass_no_user_id_main);
						//$signatory=$signatory_main;
						$rowspanMain=$rowspan=0;
						$rowspanMain=count($signatory_main[$row[csf('buyer_name')]]);
						$rowspan=$rowspanMain;
						$refno=rtrim($jobArr[$row[csf('job_no')]][grouping],',');
						$internal_ref=implode(",",array_unique(explode(",",$refno)));
						//echo $internal_ref.'X';
						$full_approval=true; $approvedStatus="";
						foreach($bypass_no_user_id_main as $uId)
						{
							$buyer_ids=$buyer_id_arr[$uId];
							$buyer_ids_array=explode(",",$buyer_id_arr[$uId]);
						}
						
						if($cbo_date_by==3 && $from_date!="" && $to_date!="")
						{
							$max_approved_date=$max_approval_date_array[$row[csf('id')]];
							if($max_approved_date>=$from_date && $max_approved_date<=$to_date)
							{
								$print_cond=1;
							}
							else $print_cond=0;
						}
						else $print_cond=1;
						
						if(((($type==1) || $row[csf('approved')]==2) || ($type==2)))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$dealing_merchant=$dealing_merchant_array[$jobArr[$row[csf('job_no')]][dealing_marchant]];
							$order_no=rtrim($jobArr[$row[csf('job_no')]][order_no],',');
							$order_id=rtrim($jobArr[$row[csf('job_no')]][order_id],',');
							$poNos=array_unique(explode(",",$order_no));
						//	$order_id=array_unique(explode(",",$order_id));
							$poIds=array_unique(explode(",",$order_id));
											
							$z=0; 
							foreach($signatory_main[$row[csf('buyer_name')]] as $user_id=>$val)
							{
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>"> 
								<?
								if($z==0)
								{
									if(str_replace("'","",trim($cbo_date_by))==1)
									{
										$date_all="C Date : ".change_date_format($row[csf('costing_date')]);
									}
									else if(str_replace("'","",trim($cbo_date_by))=='2')
									{
										$insert_date=$row[csf('insert_date')];
										$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
									}
									?>
									<td width="40" rowspan="<?=$rowspan; ?>" align="center"><?=$i; ?></td>
									<!-- <td width="100" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all">
									<?
									$rptformatName="";
									if($row[csf('entry_from')]==158)//Pre Cost V2
									{
										if($row[csf('garments_nature')]==2)//Knit BOM
										{
											if($format_idknit==50) $rptformatName="preCostRpt";//Cost Rpt
											else if($format_idknit==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idknit==52) $rptformatName="bomRpt";//BOM Rpt
											else if($format_idknit==63) $rptformatName="bomRpt2";//BOM Rpt 2
											else if($format_idknit==156) $rptformatName="accessories_details";//Acce. Dtls
											else if($format_idknit==157) $rptformatName="accessories_details2";//Acce. Dtls 2
											else if($format_idknit==158) $rptformatName="preCostRptWoven";//Cost Woven
											else if($format_idknit==159) $rptformatName="bomRptWoven";//Bom Woven
											else if($format_idknit==170) $rptformatName="preCostRpt3";//Cost Rpt3
											else if($format_idknit==171) $rptformatName="preCostRpt4";//Cost Rpt4
											else if($format_idknit==142) $rptformatName="preCostRptBpkW";//Rpt Bpkw
											else if($format_idknit==192) $rptformatName="checkListRpt";//BOM Dtls
											else if($format_idknit==197) $rptformatName="bomRpt3";//BOM Rpt 3
											else if($format_idknit==211) $rptformatName="mo_sheet";//MO Sheet
											else if($format_idknit==221) $rptformatName="fabric_cost_detail";//Fab. Pre-Cost
											else if($format_idknit==173) $rptformatName="preCostRpt5";//Cost Rpt5
											else if($format_idknit==238) $rptformatName="summary";//Summary
											else if($format_idknit==215) $rptformatName="budget3_details";//Budget3 Details
											else if($format_idknit==270) $rptformatName="preCostRpt6";//Cost Rpt6
											else if($format_idknit==581) $rptformatName="costsheet";//Cost sheet
										}
										else if($row[csf('garments_nature')]==3)//Woven BOM
										{
											if($format_idwvn==311) $rptformatName="bom_epm_woven";//BOM EPM
											else if($format_idwvn==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idwvn==158) $rptformatName="preCostRptWoven";//Cost Woven
											else if($format_idwvn==159) $rptformatName="bomRptWoven";//Bom Woven
											else if($format_idwvn==192) $rptformatName="checkListRpt";//BOM Dtls
											else if($format_idwvn==307) $rptformatName="basic_cost";//Basic Cost
											else if($format_idwvn==313) $rptformatName="mkt_source_cost";//MKT Vs Source
										}
										else if($row[csf('garments_nature')]==100)//Sweater BOM
										{
											if($format_idsweater==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idsweater==211) $rptformatName="mo_sheet";//MO Sheet
										}
										if($rptformatName!="") 
										{ 
											?><a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><? 
										}
										else echo $row[csf('job_no')];
									}
									else //Pre Cost
									{
										if($row[csf('garments_nature')]==2)//Knit BOM
										{
											if($format_idknitold==50) $rptformatName="preCostRpt";//Cost Rpt
											else if($format_idknitold==51) $rptformatName="preCostRpt2";//Cost Rpt2
											else if($format_idknitold==52) $rptformatName="bomRpt";//BOM Rpt
											else if($format_idknitold==63) $rptformatName="bomRpt2";//BOM Rpt 2
											else if($format_idknitold==142) $rptformatName="preCostRptBpkW";//Acce. Dtls
											else if($format_idknitold==173) $rptformatName="preCostRpt5";//Acce. Dtls 2
										}
										if($rptformatName!="") 
										{
											?><a href="##" title="Pre Cost Old" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><?
										}
										else echo $row[csf('job_no')]; 
									} ?>
                                    </td>                                -->
									<td width="80" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</td>
									<td width="170" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=implode(",",array_unique(array_filter(explode("**",$jobArr[$row[csf('job_no')]][pono])))); ?></td>
									
								<?
								}
								$approved_no=''; $user_ip='';
								$approval_date=$user_approval_array[$row[csf('id')]][$user_id];
								$user_ip=$user_ip_array[$row[csf('id')]][$user_id];
								if($approval_date!="") $approved_no=$approved_no_array[$row[csf('id')]][$user_id][$approval_date];
								
								$date=''; $time=''; 
								if($approval_date!="") 
								{
									$date=date("d-M-Y",strtotime($approval_date)); 
									$time=date("h:i:s A",strtotime($approval_date)); 
								}
								?>
									<td width="140" title="<?=$user_id; ?>" style="word-break:break-all"><?=$user_name_array[$user_id]['full_name']." [".$user_name_array[$user_id]['name']."];"; ?>&nbsp;</td>
									<td width="130" style="word-break:break-all"><?=$user_name_array[$user_id]['designation']; ?>&nbsp;</td>			
									
									<td width="100" align="center" style="word-break:break-all"><? if($row[csf('approved')]!=0) echo $date; ?>&nbsp;</td>
									<td width="100" align="center" style="word-break:break-all"><? if($row[csf('approved')]!=0) echo $time; ?>&nbsp;</td>
									<td align="center" style="word-break:break-all"><a href="##" onClick="openApproved_no('<?=$row[csf('id')];?>','approve_no_popup');"><? if($row[csf('approved')]!=0) echo $approved_no; ?></a></td>
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

if ($action=='approve_no_popup')
{
	echo load_html_head_contents("Approve Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql="SELECT approval_no, approval_cause from fabric_booking_approval_cause where booking_id=$job_id and entry_form=15 and approval_type=2 order by approval_no";
	$sql_res=sql_select($sql);
	?>
	<fieldset style="width:620px;">
        <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
        	<thead>
        		<tr>
	                <th colspan="3">Un Approve Request</th>               
                </tr>
        		<tr>
	                <th width="50">SL</th>
	                <th width="200">Approve No</th>
	                <th>Un-approve Request Cause</th>
                </tr>
            </thead>
            <tbody>
            	<?
            	$i=1;
            	foreach ($sql_res as $row)
            	{
            		?>
	            	<tr>
	                    <td width="50" align="center"><? echo $i; ?></td>                 
	                    <td width="200" align="center"><? echo $row[csf('approval_no')]; ?></td>
	                    <td><? echo $row[csf('approval_cause')]; ?></td> 
	                </tr>
	                <?
	                $i++;
	            }
	            ?>    
        	</tbody>
       	</table>
	</fieldset>
	<?
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


if($action=="po_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');	
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
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str )
		{			
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[4] );
				selected_name.push( str[1] );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[4] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_id').val(id);
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
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter <? echo $tittle; ?> </th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
	                    <input type="hidden" name="hide_no" id="hide_no" value="" />
	                    <input type="hidden" name="hide_id" id="hide_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                    	<?
							    $search_by_arr=array(1=>"Requistion No",2=>"PO No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_requisition_no_search_list_view', 'search_div', 'dyes_chemical_po_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_requisition_no_search_list_view")
{
    $data=explode('**',$data);
    //print_r($data);
	$company_id=$data[0];
	$search_by=$data[1];
	$search_string=trim($data[2]);
	$arr=array (0=>$company_arr);
	if($search_by==1)
	{
        if ($search_string != "") {
            $search_field_cond=" and requ_prefix_num in($search_string)"; 
        }else{
            $search_field_cond=""; 

        }
	} else if($search_by==2)
	{
		if ($search_string != "") {
            $search_field_cond=" and wo_number_prefix_num in($search_string)"; 
        }else{
            $search_field_cond=""; 

        }

	}

		
	
  

   


   if($search_by==1)
	{
		$sql= "SELECT id,requ_no,requ_prefix_num,company_id,requisition_date from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond order by id desc";
		//echo $sql;
		echo create_list_view("tbl_list_search", "Importer,Requisition No, Requisition Date,Prefix Num,System ID", "120,120,120,100,120","700","240",0, $sql , "js_set_value", "requ_no,requisition_date,requ_prefix_num,id", "", 1, "company_id,0,0,0", $arr , "company_id,requ_no,requisition_date,requ_prefix_num,id", "",'','0,0,0,0','',1);
	} else if($search_by==2)
	{
		$sql = "select distinct a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,a.currency_id,a.delivery_date,
		a.source,a.pay_mode from wo_non_order_info_mst a where  a.status_active=1 and a.is_deleted=0 $search_field_cond and a.company_name='$company_id'
		  order by a.wo_date desc";
//	echo $sql;die;	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array(0=>$company_arr);


	// echo  create_list_view("tbl_list_search", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis", "150,100,100,100,150,100","900","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id", "",'','0,0,3,0,0,0,0');


	echo create_list_view("tbl_list_search", "Company,WO Number, WO Date,Prefix Num,System ID", "120,120,120,100,120","700","240",0, $sql , "js_set_value", "wo_number,wo_date,wo_number_prefix_num,id", "", 1, "company_name,0,0,0", $arr , "company_name,wo_number,wo_date,wo_number_prefix_num,id", "",'','0,0,0,0','',1);

		
	}
		exit();
} 

?>