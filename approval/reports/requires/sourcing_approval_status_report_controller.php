<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<?=$type; ?>, 'create_job_booking_no_search_list_view', 'search_div', 'sourcing_approval_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
			$date_cond=" and a.sourcing_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and a.sourcing_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}

	$cbo_year=str_replace("'","",$cbo_year);
//    // echo $cbo_year;die();

	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

//if($cbo_year!=0){$year_cond=" and to_char($cbo_year,'YYYY') = $cbo_year";}

	
	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	//if($from_date>$to_date)
	$type = str_replace("'","",$cbo_type);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
	$buyer_id_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $job_cond=""; else $job_cond=" and a.job_no in('".implode("','",explode("*",$txt_job_no))."')";
	if($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping in('".implode("','",explode("*",$txt_ref_no))."')";
	if($cbo_company_name!=""){$com_con=" and a.COMPANY_NAME=$cbo_company_name";}

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
						
	$jobSql=sql_select("select a.id as job_id, a.dealing_marchant, a.job_no, b.id as po_id, b.grouping, b.po_number as po_number, min(b.pub_shipment_date) as min_ship_date, b.file_no from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst $job_cond $ref_cond $com_con group by a.id, a.dealing_marchant, b.id, a.job_no, b.po_number, a.style_ref_no, b.grouping, b.file_no");

	
	$jobArr=array();
	foreach($jobSql as $inf)
	{
		$jobArr[$inf[csf('job_no')]][order_no].=$inf[csf('po_number')].',';
		$jobArr[$inf[csf('job_no')]][grouping].=$inf[csf('grouping')].',';
		$jobArr[$inf[csf('job_no')]][order_id].=$inf[csf('po_id')].',';
		$jobArr[$inf[csf('job_no')]][file_no].=$inf[csf('file_no')].',';
		$jobArr[$inf[csf('job_no')]][pono].=$inf[csf('po_number')].'**';
		$jobArr[$inf[csf('job_no')]][min_ship_date]=$inf[csf('min_ship_date')];
		$jobArr[$inf[csf('job_no')]][dealing_marchant]=$inf[csf('dealing_marchant')];
		$po_array[$inf[csf('po_id')]]=$inf[csf('po_number')];
		
		$job_arr[$inf[csf('job_no')]]=$inf[csf('job_id')];		
	}
	unset($jobSql);
	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=47 and bypass=2", "user_id", "buyer_id" );
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	$signatory_data_arr=sql_select("select user_id as user_id, buyer_id, sequence_no,bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=47 order by sequence_no");	
	
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
	$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form =47 and un_approved_by=0";
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

	
	$sql_other = "select JOB_NO,fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where status_active=1 and  is_deleted=0 ".where_con_using_array($job_arr,0,'JOB_ID')."";
	//echo $sql_other;die();
		$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
		foreach( $pre_other_result as $row )
		{
			// $lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$order_job_qnty;
			// $currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $inspection=($row[csf('inspection')]/$order_price_per_dzn)*$order_job_qnty;
			// $comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty;
			
			// $freight=($row[csf('freight')]/$order_price_per_dzn)*$order_job_qnty;
			// $certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty;
			// $depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$order_job_qnty;
			
			// $tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
		 
			// $lab_test_dzn=$row[csf('lab_test')];
			// $fob_pcs=$row[csf('price_with_commn_pcs')];
			// $currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
			// $inspection_dzn=$row[csf('inspection')];
			// $comarcial_dzn=$row[csf('comm_cost')];
			// $wash_cost=$row[csf('wash_cost')];
			// $embel_cost=$row[csf('embel_cost')];
			
			$common_oh_dzn=$row[csf('common_oh')];
			$studio_pre_cost_dzn=$row[csf('studio_cost')];
			$design_pre_cost_dzn=$row[csf('design_cost')];
			$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
			
			$freight_dzn=$row[csf('freight')];
			$comm_cost_dzn=$row[csf('comm_cost')];
			$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
			$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
			$interest_pre_cost_dzn=$row[csf('interest_cost')];
			
			// $cm_cost_dzn=$row[csf('cm_cost')];
			// $cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
			// $cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
			// $tot_cm_qty_dzn=$row[csf('cm_cost')]*$offer_qty_dzn;
 
			
			 $tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;
			 //$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			// $summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;
			// $summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;
			// $summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;

			
			$tot_other_cost_dzn_arr[$row['JOB_NO']]=$tot_other_cost_dzn;
			$cm_cost_dzn_arr[$row['JOB_NO']]=$row[csf('cm_cost')];
			$wash_cost_arr[$row['JOB_NO']]=$row[csf('wash_cost')];
			$comm_cost_dzn_arr[$row['JOB_NO']]=$row[csf('comm_cost')];
			$inspection_dzn_arr[$row['JOB_NO']]=$row[csf('inspection')];
			$currier_pre_cost_dzn_arr[$row['JOB_NO']]=$row[csf('currier_pre_cost')];
			$lab_test_dzn_arr[$row['JOB_NO']]=$row[csf('lab_test')];
			$embel_cost_arr[$row['JOB_NO']]=$row[csf('embel_cost')];
				
		}

 
		
		$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where status_active=1 ".where_con_using_array($job_arr,0,'JOB_ID')."";

			//echo $sql_commi;die();
			$result_commi=sql_select($sql_commi);
			 foreach( $result_commi as $row )
				{
					$commission_type_id=$row[csf('particulars_id')];
					//$com_type_id=$row[csf('commission_base_id')];
					if($row[csf('commission_amount')]>0)
					{
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
					// //$summ_fob_value_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
					// $summ_fob_gross_value_amt+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
					// $summ_fob_pcs+=$row[csf('commission_amount')];
					// $summ_sourcing_tot_budget_dzn_val+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				
				
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_req_amt']=$row[csf('commission_amount')];
					}
				
				} 	


	
	//print_r($max_approval_date_array);
	ob_start();
	?>
    <fieldset style="width:1720px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3500" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
				<th width="100">Last Version</th>
                <th width="100">Year</th>
                <th width="120">Style Ref.</th>
                <th width="100">Style Qty</th>
				<th width="70">UOM</th>
				<th width="80">Season</th>
				<th width="70">FOB</th>
				<th width="100">CM Cost Budget</th>
				<th width="80">EPM Budget</th>
				<th width="70">SMV</th>
				<th width="80">Gmts.Wash</th>
				<th width="100">Embellishment</th>
				<th width="80">Test Charge</th>
				<th width="100">Currier Charge</th>
				<th width="100">Inspection Charge</th>
				<th width="100">Commercial Charge</th>
				<th width="100">Others Charge</th>
				<th width="100">UK Office Commission</th>
				<th width="100">Buying Commission</th>
                <th width="100">Internal Ref</th>
                <th width="80">Buyer Name</th>
                <th width="110">Dealing Merchant</th>
                <th width="50">Image</th>
                <th width="50">File</th>
                <th width="100">Comm File No</th>
                <th width="200">Order No</th>
                <th width="80">Shipment Date [Min.]</th>
                <th width="120">Sourcing Date</th>
                <th width="140">Signatory</th>
                <th width="130">Designation</th>
                <th  width="50">Can Bypass</th>
                <th width="100">IP Address</th>
                <th width="100">Approval Date</th>
                <th width="100">Approval Time</th>
                <th>Approve No</th>
            </thead>
        </table>
        <div style="width:3500px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3572" class="rpt_table" id="tbl_list_search">
                <tbody>
                    <? 
                    $print_reportSql="Select report_id, format_id from lib_report_template where template_name =".$cbo_company_name." and module_id=2 and report_id in(141) and is_deleted=0 and status_active=1";
					$print_reportSqlRes=sql_select( $print_reportSql);
                    foreach($print_reportSqlRes as $prow)
                    {
						$exformatid=explode(",",$prow[csf('format_id')]);
                        $format_id=$exformatid[0];
                    }
                    
					$i=1;
                        
                    $bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
                    if($type==2) $approved_cond=" and a.sourcing_approved=1";
                    elseif($type==1) $approved_cond=" and a.sourcing_approved=3";
                    else $approved_cond=" and a.sourcing_approved in (0,2)";
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
                     
                    $sql="select a.costing_per,a.id, b.garments_nature,b.job_quantity, b.company_name, b.buyer_name,b.style_ref_no,b.season_buyer_wise,b.season_year,b.avg_unit_price,b.set_smv,b.order_uom,a.entry_from, a.sourcing_date as costing_date, a.job_no, a.sourcing_approved as approved,
					to_char(a.insert_date,'YYYY') as year,(select max(h.approved_no) from approval_history h where a.id=h.mst_id and h.entry_form in(47) ) as revised_no,b.quotation_id from  wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sourcing_ready_to_approved=1 and a.sourcing_inserted_by>0 $buyer_id_cond $job_cond $approved_cond $date_cond $po_cond_for_in $year_cond  group by a.costing_per,a.id, b.garments_nature,b.job_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.season_buyer_wise,b.season_year,b.avg_unit_price,b.set_smv,b.order_uom,a.entry_from, a.sourcing_date, a.job_no, a.sourcing_approved, a.insert_date,b.quotation_id order by a.id desc";
					//echo $sql;die;
					$nameArray=sql_select( $sql);
					foreach ($nameArray as $row)
					{
						$full_approval='';

						$rowspanMain=$rowspan=0;
						$rowspanMain=count($signatory_main[$row[csf('buyer_name')]]);
						$rowspan=$rowspanMain;
						$refno=rtrim($jobArr[$row[csf('job_no')]][grouping],',');
						$internal_ref=implode(",",array_unique(explode(",",$refno)));
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
						
						if(((($type==1) || $row[csf('approved')]==2 || $row[csf('approved')]==0) || ($type==2)))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$dealing_merchant=$dealing_merchant_array[$jobArr[$row[csf('job_no')]][dealing_marchant]];
							$order_no=rtrim($jobArr[$row[csf('job_no')]][order_no],',');
							$file_no=rtrim($jobArr[$row[csf('job_no')]][file_no],',');
							$order_id=rtrim($jobArr[$row[csf('job_no')]][order_id],',');
							$poNos=array_unique(explode(",",$order_no));
							$fileno=array_unique(explode(",",$file_no));
						//	$order_id=array_unique(explode(",",$order_id));
							$poIds=array_unique(explode(",",$order_id));
											
							$z=0; 
							foreach($signatory_main[$row[csf('buyer_name')]] as $user_id=>$val)
							{
								$cm_cost_dzn = $cm_cost_dzn_arr[$row[csf('job_no')]];
								$wash_cost = $wash_cost_arr[$row[csf('job_no')]];
								$embel_cost = $embel_cost_arr[$row[csf('job_no')]];
								$lab_test_dzn = $lab_test_dzn_arr[$row[csf('job_no')]];
								$currier_pre_cost_dzn = $currier_pre_cost_dzn_arr[$row[csf('job_no')]];
								$inspection_dzn = $inspection_dzn_arr[$row[csf('job_no')]];
								$comm_cost_dzn = $comm_cost_dzn_arr[$row[csf('job_no')]];
								$tot_other_cost_dzn = $tot_other_cost_dzn_arr[$row[csf('job_no')]];
 								
								
								if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
								else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
								else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
								else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
								else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
								
							 
								
								
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
									<td width="100" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all">
									<?
									$rptformatName="";
									if($row[csf('garments_nature')]==2)//Knit BOM
									{
										if($format_idknit==313) $rptformatName="preCostRpt";//Cost Rpt
										else if($format_idknit==323) $rptformatName="preCostRpt2";//Cost Rpt2
									}
									else if($row[csf('garments_nature')]==3)//Woven BOM
									{
										if($format_id==313) $rptformatName="mkt_source_cost";//MKT Vs Source
										else if($format_id==323) $rptformatName="app_final_cost"; //Final App
									}
									else if($row[csf('garments_nature')]==100)//Sweater BOM
									{
										if($format_idsweater==51) $rptformatName="preCostRpt2";//Cost Rpt2
										else if($format_idsweater==211) $rptformatName="mo_sheet";//MO Sheet
									}
									if($rptformatName!="") 
									{ 
										?>
										<a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><? 
									}
									else echo $row[csf('job_no')];
									?>
                                    </td>
									<?
										//=====================revise no===================================	
											
										$function2="";
										if($row[csf('revised_no')]>0)
										{
											for($q=1; $version<=$row[csf('revised_no')]; $version++)
											{
												if($function2=="") $function2="<a href='#' onClick=\"history_budget_sheet(".$cbo_company_name.",'".$row[csf('job_no')]."',".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."','".mkt_source_cost."',".$row[csf('entry_from')].",".$row[csf('garments_nature')].",'".$version."'".")\"> ".$version."<a/>";
												else $function2.=", "."<a href='#' onClick=\"history_budget_sheet(".$cbo_company_name.",'".$row[csf('job_no')]."',".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."','".mkt_source_cost."',".$row[csf('entry_from')].",".$row[csf('garments_nature')].",'".$version."'".")\"> ".$version."<a/>";
												
											}
										}
										
									//=====================revise no===================================	
									
									?>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$function2;?>&nbsp;</td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$row[csf('year')]; ?></td>
                                    <td width="120" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?="mkt_source_cost"; ?>',<?=$row[csf('entry_from')]; ?>,<?=3; ?>);" ><?=$row[csf('style_ref_no')]; ?></a>&nbsp;</td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$row[csf("job_quantity")];?></td>
									<td width="70" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo $season_brand = $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2); ?>&nbsp;</td>
									<td width="70" align="center" rowspan="<?=$rowspan; ?>" 
									<?
									$avg=$row[csf("avg_unit_price")];
									?>
									style="word-break:break-all"><?=number_format($avg,6);?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$cm_cost_dzn;?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($cm_cost_dzn/12/$row[csf("set_smv")],6); ?></td>
									<td width="70" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($row[csf("set_smv")],6)?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($wash_cost,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($embel_cost,6)?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($lab_test_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($currier_pre_cost_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($inspection_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($comm_cost_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($tot_other_cost_dzn,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($commission_arr[$row[csf('job_no')]][2]['commi_amt'],6); ?></p></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($commission_arr[$row[csf('job_no')]][1]['commi_amt'],6); ?></p></td>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><?=$internal_ref; ?></p></td>
									<td width="80" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td width="110" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$dealing_merchant; ?></td>
									<td width="50" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','img');">View</a></td>
									<td width="50" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','file');">View</a></td>
									<td width="100" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?= implode(", ", $fileno); ?></td>
									<td width="200" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=implode(",",array_unique(array_filter(explode("**",$jobArr[$row[csf('job_no')]][pono])))); ?></td>
									<td width="80" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><?=change_date_format($jobArr[$row[csf('job_no')]][min_ship_date]); ?></td>
									<td width="120" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=change_date_format($row[csf('costing_date')]); ?></td>
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
									<td width="50" align="center" style="word-break:break-all"><?=$yes_no[$val]; ?>&nbsp;</td>
									<td width="100" align="center" style="word-break:break-all"><?=$user_ip; ?>&nbsp;</td>
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

if($action=="report_generate2")
{ 
	$process = array( &$_POST );

	
	extract(check_magic_quote_gpc( $process )); 
 
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.sourcing_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and a.sourcing_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}

	$cbo_year=str_replace("'","",$cbo_year);
    // echo $cbo_year;die();

	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.sourcing_inserted_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.sourcing_inserted_date,'YYYY')=$cbo_year"; else $year_cond="";
	}



	
	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	//if($from_date>$to_date)
	$type = str_replace("'","",$cbo_type);
	$txt_ref_no = str_replace("'","",$txt_ref_no);
	$buyer_id_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $job_cond=""; else $job_cond=" and a.job_no in('".implode("','",explode("*",$txt_job_no))."')";
	if($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping in('".implode("','",explode("*",$txt_ref_no))."')";
	if($cbo_company_name!=""){$com_con=" and a.COMPANY_NAME=$cbo_company_name";}

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
						
	$jobSql=sql_select("select a.id as job_id, a.dealing_marchant, a.job_no, b.id as po_id, b.grouping, b.po_number as po_number, min(b.pub_shipment_date) as min_ship_date, b.file_no from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst $job_cond $ref_cond $com_con group by a.id, a.dealing_marchant, b.id, a.job_no, b.po_number, a.style_ref_no, b.grouping, b.file_no");

	
	$jobArr=array();
	foreach($jobSql as $inf)
	{
		$jobArr[$inf[csf('job_no')]][order_no].=$inf[csf('po_number')].',';
		$jobArr[$inf[csf('job_no')]][grouping].=$inf[csf('grouping')].',';
		$jobArr[$inf[csf('job_no')]][order_id].=$inf[csf('po_id')].',';
		$jobArr[$inf[csf('job_no')]][file_no].=$inf[csf('file_no')].',';
		$jobArr[$inf[csf('job_no')]][pono].=$inf[csf('po_number')].'**';
		$jobArr[$inf[csf('job_no')]][min_ship_date]=$inf[csf('min_ship_date')];
		$jobArr[$inf[csf('job_no')]][dealing_marchant]=$inf[csf('dealing_marchant')];
		$po_array[$inf[csf('po_id')]]=$inf[csf('po_number')];
		
		$job_arr[$inf[csf('job_no')]]=$inf[csf('job_id')];		
	}
	unset($jobSql);
	
	$buyer_id_arr=return_library_array( "select user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=47 and bypass=2", "user_id", "buyer_id" );
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	$signatory_data_arr=sql_select("select user_id as user_id, buyer_id, sequence_no,bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=47 order by sequence_no");	
	
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
	$query="select entry_form, mst_id, approved_no, approved_by, approved_date, user_ip from approval_history where entry_form =47 and un_approved_by=0";
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

	
	$sql_other = "select JOB_NO,fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where status_active=1 and  is_deleted=0 ".where_con_using_array($job_arr,0,'JOB_ID')."";
	//echo $sql_other;die();
		$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
		foreach( $pre_other_result as $row )
		{
		
			$common_oh_dzn=$row[csf('common_oh')];
			$studio_pre_cost_dzn=$row[csf('studio_cost')];
			$design_pre_cost_dzn=$row[csf('design_cost')];
			$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];			
			$freight_dzn=$row[csf('freight')];
			$comm_cost_dzn=$row[csf('comm_cost')];
			$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
			$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
			$interest_pre_cost_dzn=$row[csf('interest_cost')];			
			$tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;		
			$tot_other_cost_dzn_arr[$row['JOB_NO']]=$tot_other_cost_dzn;
			$cm_cost_dzn_arr[$row['JOB_NO']]=$row[csf('cm_cost')];
			$wash_cost_arr[$row['JOB_NO']]=$row[csf('wash_cost')];
			$comm_cost_dzn_arr[$row['JOB_NO']]=$row[csf('comm_cost')];
			$inspection_dzn_arr[$row['JOB_NO']]=$row[csf('inspection')];
			$currier_pre_cost_dzn_arr[$row['JOB_NO']]=$row[csf('currier_pre_cost')];
			$lab_test_dzn_arr[$row['JOB_NO']]=$row[csf('lab_test')];
			$embel_cost_arr[$row['JOB_NO']]=$row[csf('embel_cost')];
				
		}

		$sql_fabric="select  b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_arr,0,'JOB_ID')."";//and b.fabric_source=2
		$sql_fabric_result=sql_select($sql_fabric);//$summ_fob_value_pcs=0;
		foreach($sql_fabric_result as $row )
		{
			$fabric_amount_arr[$row['JOB_NO']]+=$row[csf('amount')];
			$fabric_sourcing_amount_arr[$row['JOB_NO']]+=$row[csf('sourcing_amount')];				
		}

 		$sql_trim="select b.seq,b.id,c.trim_type,b.job_no,c.item_name,b.description,b.trim_group,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ".where_con_using_array($job_arr,0,'JOB_ID')."";

		$sql_trim_result=sql_select($sql_trim);
		foreach($sql_trim_result as $row )
		{
			$trim_amount_arr[$row['JOB_NO']]+=$row[csf('amount')];
			$trim_sourcing_amount_arr[$row['JOB_NO']]+=$row[csf('sourcing_amount')];				
		}

		 $sql_wash="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where b.emb_name in(3) and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($job_arr,0,'JOB_ID')."";

		$sql_wash_result=sql_select($sql_wash);//$summ_fob_value_pcs=0;
		foreach($sql_wash_result as $row )
		{
			$wash_amount_arr[$row['JOB_NO']]+=$row[csf('amount')];
			$wash_sourcing_amount_arr[$row['JOB_NO']]+=$row[csf('sourcing_amount')];				
		}

	 	 $sql_emb="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where b.emb_name not in(3) and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($job_arr,0,'JOB_ID')."";

		$sql_emb_result=sql_select($sql_emb);//$summ_fob_value_pcs=0;
		foreach($sql_emb_result as $row )
		{
			$emb_amount_arr[$row['JOB_NO']]+=$row[csf('amount')];
			$emb_sourcing_amount_arr[$row['JOB_NO']]+=$row[csf('sourcing_amount')];				
		}

 
		
		$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where status_active=1 ".where_con_using_array($job_arr,0,'JOB_ID')."";

			//echo $sql_commi;die();
			$result_commi=sql_select($sql_commi);
			 foreach( $result_commi as $row )
				{
					$commission_type_id=$row[csf('particulars_id')];
					//$com_type_id=$row[csf('commission_base_id')];
					if($row[csf('commission_amount')]>0)
					{
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
				
					$commission_arr[$row[csf('job_no')]][$commission_type_id]['commi_req_amt']=$row[csf('commission_amount')];
					}
				
				} 	


	
	//print_r($max_approval_date_array);
	ob_start();
	?>
    <fieldset style="width:3650px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><?=$company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3630" class="rpt_table" >
        <thead>
        <tr>
				<th colspan="12" width="1130"></th>
				<th width="100">CM Cost Costing</th>
				<th width="100">EPM Costing</th>
				<th width="100">CM Cost Budget</th>
				<th width="100">EPM Budget</th>
				<th width="100">CM Costing Variance</th>
				<th width="100">EPM Costing Variance </th>
				<th colspan="2" width="200">Fabric</th>
                <th width="100">Variance </th>
				<th colspan="2" width="200">Trims</th>
				<th width="100">Variance </th>
                <th colspan="2" width="200">Gmts.Wash</th>
				<th width="100">Variance </th>
				<th colspan="2" width="200">EMBEL</th>
                <th width="100">Variance</th>
				<th width="700" colspan="7"></th>
    </tr>
          <tr>  
                <th width="30">SL</th>
				<th width="100">Company</th>
				<th width="100">Buyer Name</th>
                <th width="100">Job No</th>
                <th width="100">Year</th>
				<th width="80">Season</th>
				<th width="120">Product Item</th>
                <th width="120">Style Ref.</th>
                <th width="100">Style Qty</th>	
				<th width="80">UOM</th>				
				<th width="100">FOB</th>
				<th width="100">SMV</th>
				<th width="200" colspan="2">Costing Amnt/Dzn </th>
				<th width="200" colspan="2">Budget Amnt/Dzn</th>
				<th width="200" colspan="2">Saving Amnt/Dzn</th>
				<th width="100">Costing Amnt/Dzn </th>
				<th width="100">Budget Amnt/Dzn</th>
				<th width="100">Saving Amnt/Dzn</th>
				<th width="100">Costing Amnt/Dzn </th>
                <th width="100">Budget Amnt/Dzn</th>
				<th width="100">Saving Amnt/Dzn</th>
				<th width="100">Costing Amnt/Dzn </th>
                <th width="100">Budget Amnt/Dzn</th>
				<th width="100">Saving Amnt/Dzn</th>
				<th width="100">Costing Amnt/Dzn </th>
                <th width="100">Budget Amnt/Dzn</th>
				<th width="100">Saving Amnt/Dzn</th>
				<th width="100">Test Charge</th>
				<th width="100">Currier Charge</th>
				<th width="100">Inspection Charge</th>
				<th width="100">Commercial Charge</th>
				<th width="100">Others Charge</th>
				<th width="100">UK Office Commission</th>
				<th width="100">Buying Commission</th>
              </tr>
            </thead>
      
                <tbody>
                    <? 
                    $print_reportSql="Select report_id, format_id from lib_report_template where template_name =".$cbo_company_name." and module_id=2 and report_id in(141) and is_deleted=0 and status_active=1";
					$print_reportSqlRes=sql_select( $print_reportSql);
                    foreach($print_reportSqlRes as $prow)
                    {
						$exformatid=explode(",",$prow[csf('format_id')]);
                        $format_id=$exformatid[0];
                    }
                    
					$i=1;
                        
                    $bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
                    if($type==2) $approved_cond=" and a.sourcing_approved=1";
                    elseif($type==1) $approved_cond=" and a.sourcing_approved=3";
                    else $approved_cond=" and a.sourcing_approved in (0,2)";
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
                     																 
				
			$sql_other = "select fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where  job_no='$txt_job_no'  and status_active=1 and  is_deleted=0";
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
			 foreach( $pre_other_result as $row )
			{
				$lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$order_job_qnty;
				$currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$inspection=($row[csf('inspection')]/$order_price_per_dzn)*$order_job_qnty;
				$comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$freight=($row[csf('freight')]/$order_price_per_dzn)*$order_job_qnty;
				$certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty;
				$depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
				//echo $tot_other_for_fob_value;
				$lab_test_dzn=$row[csf('lab_test')];
				$fob_pcs=$row[csf('price_with_commn_pcs')];
				$currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
				$inspection_dzn=$row[csf('inspection')];
				$comarcial_dzn=$row[csf('comm_cost')];
				
				$common_oh_dzn=$row[csf('common_oh')];
				$studio_pre_cost_dzn=$row[csf('studio_cost')];
				$design_pre_cost_dzn=$row[csf('design_cost')];
				$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
				
				$freight_dzn=$row[csf('freight')];
				//$comm_cost_dzn=$row[csf('comm_cost')];
				$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
				$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
				$interest_pre_cost_dzn=$row[csf('interest_cost')];
				
				$cm_cost_dzn=$row[csf('cm_cost')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$po_qty_dzn;
				//$lab_test_dzn=$row[csf('lab_test')];
				
				$tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;				
				$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;				
				$summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;				
				$summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;
				 
			}
			 $sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no." and status_active=1";
			$result_commi=sql_select($sql_commi);
		 	foreach( $result_commi as $row )
			{
				$commission_type_id=$row[csf('particulars_id')];
				$com_type_id=$row[csf('commission_base_id')];
				
				$commission_arr[$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$commission_arr[$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
				$commission_arr[$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
				//$summ_fob_value_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$summ_fob_pcs+=$row[csf('commission_amount')];
				$summ_sourcing_tot_budget_dzn_val+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
			} 
			//echo $summ_fob_pcs.'S';
			//$summ_fob_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
			$summ_sourcing_fob_pcs=$summ_sourcing_tot_budget_dzn_val/$order_job_qnty;
			$summ_tot_final_cm=($summ_fob_gross_value_amt-$summ_sourcing_tot_budget_dzn_val)/$offer_qty_dzn;
			//echo $summ_fob_pcs.'='.$order_price_per_dzn;
		
			//$summ_fob_pcs=$summ_fob_pcs-$summ_fob_pcs;
			$tot_summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;

			
                    
                    $sql="select a.costing_per,a.id, b.garments_nature,b.job_quantity, b.company_name, b.buyer_name,b.style_ref_no,b.season_buyer_wise,b.season_year,b.avg_unit_price,b.set_smv,b.order_uom,b.gmts_item_id,a.entry_from, a.sourcing_date as costing_date, a.job_no, a.sourcing_approved as approved,
					to_char(a.sourcing_inserted_date,'YYYY') as year from  wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sourcing_ready_to_approved=1 and a.sourcing_inserted_by>0 $buyer_id_cond $job_cond $approved_cond $date_cond $po_cond_for_in $year_cond  group by a.costing_per,a.id, b.garments_nature,b.job_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.season_buyer_wise,b.season_year,b.avg_unit_price,b.set_smv,b.order_uom,b.gmts_item_id,a.entry_from, a.sourcing_date, a.job_no, a.sourcing_approved, a.sourcing_inserted_date order by a.id desc";
					//echo $sql;
					$nameArray=sql_select( $sql);
					foreach ($nameArray as $row)
					{
						$full_approval='';

						$rowspanMain=$rowspan=0;
						$rowspanMain=count($signatory_main[$row[csf('buyer_name')]]);
						$rowspan=$rowspanMain;
						$refno=rtrim($jobArr[$row[csf('job_no')]][grouping],',');
						$internal_ref=implode(",",array_unique(explode(",",$refno)));
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
						
						if(((($type==1) || $row[csf('approved')]==2 || $row[csf('approved')]==0) || ($type==2)))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$dealing_merchant=$dealing_merchant_array[$jobArr[$row[csf('job_no')]][dealing_marchant]];
							$order_no=rtrim($jobArr[$row[csf('job_no')]][order_no],',');
							$file_no=rtrim($jobArr[$row[csf('job_no')]][file_no],',');
							$order_id=rtrim($jobArr[$row[csf('job_no')]][order_id],',');
							$poNos=array_unique(explode(",",$order_no));
							$fileno=array_unique(explode(",",$file_no));
						//	$order_id=array_unique(explode(",",$order_id));
							$poIds=array_unique(explode(",",$order_id));
											
							$z=0; 
							foreach($signatory_main[$row[csf('buyer_name')]] as $user_id=>$val)
							{
								$cm_cost_dzn = $cm_cost_dzn_arr[$row[csf('job_no')]];
								$wash_cost = $wash_cost_arr[$row[csf('job_no')]];
								$embel_cost = $embel_cost_arr[$row[csf('job_no')]];
								$lab_test_dzn = $lab_test_dzn_arr[$row[csf('job_no')]];
								$currier_pre_cost_dzn = $currier_pre_cost_dzn_arr[$row[csf('job_no')]];
								$inspection_dzn = $inspection_dzn_arr[$row[csf('job_no')]];
								$comm_cost_dzn = $comm_cost_dzn_arr[$row[csf('job_no')]];
								$tot_other_cost_dzn = $tot_other_cost_dzn_arr[$row[csf('job_no')]];
								
								$fabric_amount=$fabric_amount_arr[$row[csf('job_no')]];
								$fabric_sourcing_amount=$fabric_sourcing_amount_arr[$row[csf('job_no')]];

								$trim_amount=$trim_amount_arr[$row[csf('job_no')]];
								$trim_sourcing_amount=$trim_sourcing_amount_arr[$row[csf('job_no')]];
								$wash_sourcing_amount=$wash_sourcing_amount_arr[$row[csf('job_no')]];
								$emb_sourcing_amount=$emb_sourcing_amount_arr[$row[csf('job_no')]];
								
								if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
								else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
								else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
								else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
								else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
								
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
									<td width="30" rowspan="<?=$rowspan; ?>" align="center"><?=$i; ?></td>
									<td width="100" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><?=$company_arr[$row[csf('company_name')]]; ?></td>
									<td width="100" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td width="100" rowspan="<?=$rowspan; ?>" align="center" style="word-break:break-all">
									<?
									$rptformatName="";
									if($row[csf('garments_nature')]==2)//Knit BOM
									{
										if($format_idknit==313) $rptformatName="preCostRpt";//Cost Rpt
										else if($format_idknit==323) $rptformatName="preCostRpt2";//Cost Rpt2
									}
									else if($row[csf('garments_nature')]==3)//Woven BOM
									{
										if($format_id==313) $rptformatName="mkt_source_cost";//MKT Vs Source
										else if($format_id==323) $rptformatName="app_final_cost"; //Final App
									}
									else if($row[csf('garments_nature')]==100)//Sweater BOM
									{
										if($format_idsweater==51) $rptformatName="preCostRpt2";//Cost Rpt2
										else if($format_idsweater==211) $rptformatName="mo_sheet";//MO Sheet
									}
									if($rptformatName!="") 
									{ 
										?>
										<a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?=$rptformatName; ?>',<?=$row[csf('entry_from')]; ?>,<?=$row[csf('garments_nature')]; ?>);" ><?=$row[csf('job_no')]; ?></a><? 
									}
									else echo $row[csf('job_no')];
									?>
                                    </td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$row[csf('year')]; ?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo $season_brand = $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2); ?>&nbsp;</td>
									<td width="120" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?
                        			$gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                        			foreach($gmts_item_id as $item_id)
                        				{
                        					if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
                        				}
                        			echo $gmts_item;?></td>
                                    <td width="120" rowspan="<?=$rowspan; ?>" style="word-break:break-all" align="center"><a href="##" title="Pre Cost V2" onClick="generate_report(<?=$cbo_company_name; ?>,'<?=$row[csf('job_no')]; ?>','<? //=$pId; ?>',<?=$row[csf('buyer_name')]; ?>,'<?=$row[csf('style_ref_no')]; ?>','<?=$row[csf('costing_date')]; ?>','<?="mkt_source_cost"; ?>',<?=$row[csf('entry_from')]; ?>,<?=3; ?>);" ><?=$row[csf('style_ref_no')]; ?></a>&nbsp;</td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$row[csf("job_quantity")];?></td>
									<td width="80" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" 
									<?
									$avg=$row[csf("avg_unit_price")];
									?>
									style="word-break:break-all"><?=number_format($avg,6);?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($row[csf("set_smv")],6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=$cm_cost_dzn;?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? 
									echo number_format($cm_cost_dzn/12/$row[csf("set_smv")],6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? 
									$tot_cm_cost=$fabric_sourcing_amount+$trim_sourcing_amount+$wash_sourcing_amount+$emb_sourcing_amount+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comm_cost_dzn+$tot_other_cost_dzn+$commission_arr[$row[csf('job_no')]][2]['commi_amt']+$commission_arr[$row[csf('job_no')]][1]['commi_amt'];
									$tot_final_cm=($avg*$order_price_per_dzn)-$tot_cm_cost;
									
									echo number_format($tot_final_cm,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($tot_final_cm/$order_price_per_dzn/$row[csf("set_smv")],6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format(($cm_cost_dzn-$tot_final_cm),6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format(($cm_cost_dzn/12/$row[csf("set_smv")])-($tot_final_cm/$order_price_per_dzn/$row[csf("set_smv")]),6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($fabric_amount,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($fabric_sourcing_amount,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format(($fabric_amount-$fabric_sourcing_amount),6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($trim_amount,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($trim_sourcing_amount,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format(($trim_amount-$trim_sourcing_amount),6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><? echo number_format($wash_cost,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($wash_sourcing_amount,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format(($wash_cost-$wash_sourcing_amount),6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($embel_cost,6)?></td>

									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($emb_sourcing_amount,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format(($embel_cost-$emb_sourcing_amount),6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($lab_test_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($currier_pre_cost_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($inspection_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><?=number_format($comm_cost_dzn,6)?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($tot_other_cost_dzn,6); ?></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($commission_arr[$row[csf('job_no')]][2]['commi_amt'],6); ?></p></td>
									<td width="100" align="center" rowspan="<?=$rowspan; ?>" style="word-break:break-all"><p><? echo number_format($commission_arr[$row[csf('job_no')]][1]['commi_amt'],6); ?></p></td>
								
                                </tr>
									
								<?
								
								}
								$z++;
							}
							$i++;
						}
					}
					?>
                </tbody>
            </table>
       
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

if($action == "mkt_source_cost_bk") //Mkt vs Source 
{
   	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no==""){
		$job_no='';
	}else{
		$job_no=" and a.job_no=".$txt_job_no."";
	}

	if($cbo_company_name=="") {
		$company_name='';
	} else {
		 $company_name=" and a.company_name=".$cbo_company_name."";
	}

	if($cbo_buyer_name==""){
		 $cbo_buyer_name='';
	} else {
		$cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	}

	if($txt_style_ref==""){
		 $txt_style_ref='';
	} else {
		$txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	}

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_costing_date=="") {
		$txt_costing_date='';
	} else {
		$txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
	}
	$txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
	if(str_replace("'",'',$txt_po_breack_down_id)==""){
		$txt_po_breack_down_id_cond='';
	}
	else{
		$txt_po_breack_down_id_cond=" and d.id in(".$txt_po_breack_down_id.")";
	}
    //if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$revised_no=str_replace("'","",$revised_no);
 //echo $revised_no.'=A';
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $user_passArr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	 //pro_ex_factory_mst 
	 $sql_ex="select max(ex_factory_date) as ex_factory_date from pro_ex_factory_mst b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and d.status_active=1";
	  $exf_data_array=sql_select($sql_ex);
	 foreach( $exf_data_array as $row)
	 {
		 	$ex_factory_date=$row[csf("ex_factory_date")];
	 }
	  	
	$excess_cut=0;
	  $sql_excess="select b.excess_cut_perc from wo_po_color_size_breakdown b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and b.status_active=1 and d.status_active=1";
	  $excess_data_array=sql_select($sql_excess);
	 foreach( $excess_data_array as $row)
	 {
		 $excess_cut=$row[csf("excess_cut_perc")];
	 }
	  
	$sql_po="select  a.total_set_qnty as ratio,d.po_number,(d.po_quantity) as po_qnty,d.plan_cut, d.unit_price, d.pub_shipment_date,d.shipment_date,d.po_received_date,d.pack_handover_date  from wo_po_break_down d,wo_po_details_master a where   d.job_no_mst=a.job_no and d.job_no_mst=$txt_job_no and d.status_active=1 $txt_po_breack_down_id_cond";
	 $po_data_array=sql_select($sql_po);
	 	$order_job_qnty=0;$plan_cut=0;
		$leadtime_days_remian_cal="";
	 foreach( $po_data_array as $row)
	 {
		 	$po_received_dateArr.=$row[csf("po_received_date")].',';
			$pack_handover_dateArr.=$row[csf("pack_handover_date")].',';
			$shipment_date_dateArr.=$row[csf("shipment_date")].',';
			$order_job_qnty+=$row[csf("po_qnty")];
			$plan_cut+=$row[csf("plan_cut")];
			$days_tot=datediff('d',$row[csf("po_received_date")],$row[csf("pub_shipment_date")])-1;
			
			 $leadtime_days_remian_cal.=$days_tot.',';
	 }
	  	   $leadtime_days_remian_calArr=rtrim($leadtime_days_remian_cal,',');
		    $leadtime_days_remian_calArr=explode(",",$leadtime_days_remian_calArr);
			 $leadtime_days_remian=max($leadtime_days_remian_calArr);
		//  echo $leadtime_days_remian_cal;
	 $po_received_dateArr=rtrim($po_received_dateArr,',');
	 $po_received_dateArr=explode(",",$po_received_dateArr);
	  $po_received_date=max($po_received_dateArr);
	 $pack_handover_dateArr=rtrim($pack_handover_dateArr,',');
	 $pack_handover_dateArr=explode(",",$pack_handover_dateArr);
	//  $pack_handover_date=max($pack_handover_dateArr);

	foreach($pack_handover_dateArr as $date){
		$d=strtotime($date);
		$phd_arr[date('Y-m-d',$d)]=date('Y-m-d',$d);
	}
	
	$min_p_handover_date=min($phd_arr);

	  $shipment_date_dateArr=rtrim($shipment_date_dateArr,',');	  
	 $shipment_date_dateActual=explode(",",$shipment_date_dateArr);

	//   $shipment_dateActual=max($shipment_date_dateActual);
	foreach($shipment_date_dateActual as $date){
		$d=strtotime($date);
		$ship_arr[date('Y-m-d',$d)]=date('Y-m-d',$d);
	}
	
	$shipment_dateActual=min($ship_arr);

	  $brand_arr=return_library_array( "select id, brand_name from  lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
	// $leadtime_days_remian=datediff('d',$po_received_date,$shipment_dateActual)-1;
	 
	 $sql = "select a.job_no,a.company_name,a.body_wash_color,a.buyer_name,a.total_set_qnty,a.style_description,a.remarks,a.set_break_down,a.quotation_id,a.season_buyer_wise,a.season_year,a.brand_id,a.style_ref_no,a.set_smv, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price,b.sourcing_approved as approved,b.costing_date,b.sourcing_date, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg, a.total_set_qnty as ,a.working_company_id from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c  where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 and a.job_no=$txt_job_no $company_name $cbo_buyer_name $txt_style_ref";
          // echo $sql;
    $data_array=sql_select($sql);
	$working_company_arr=return_library_array("SELECT id,company_name from lib_company ","id","company_name");  
	$working_company='';
	foreach ($data_array as $row)
			{
				$working_company=$working_company_arr[$row[csf("working_company_id")]];
			}
 if(empty($path))
    {
    	$path="../../";
    }
    else{
    	$path=str_replace("'", "", $path);
	}
	$first_app_date=""; $last_app_date="";
	 
     $preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no=$txt_job_no and APPROVED_NO=$revised_no  and b.entry_form=47 group by a.id"); 
    if(count($preCost_approved)>0)
    {
      foreach($preCost_approved as $preCost_approved_row)
      {
        $approved_no_row=$preCost_approved_row[csf('approved_no')];
        $fst_date=$preCost_approved_row[csf('first_app_date')];
        $fstapp_date=$fst_date[0];
        
        $last_date=$preCost_approved_row[csf('last_app_date')];
        $lstapp_date=$last_date[0];
        $precost_id=$preCost_approved_row[csf('id')];
      }
	}
	ob_start();
    ?>
    <div style="width:1320px" align="center">  
     <style type="text/css" media="print">
   			table { page-break-inside:auto }
		
		/* p{ padding:0px !important; margin:0px !important;}*/

		</style>

		<div style="width:1320px; font-size:20px; font-weight:bold">
              <b style="float:left;"><?='Revised No:'.$revised_no; ?>  </b><br>
			  <b style="float:left;"><?='Revised Date And Time:'.$last_date; ?>  </b>
        </div>
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img src='<? echo $path .''. $imge_arr[str_replace("'","",$cbo_company_name)]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                            <b>  <?php      
                                    echo 'Sourcing Post Cost Report';
                              ?>
                              </b>
                            </td>
                        </tr>
                      </table>
					  <br>
					  <table width="100%" cellpadding="0" cellspacing="0">
                        <tr align="center">
                            <td> <b style="font-size:21px;"> PO Rec Unit: </b><span style="font-size:19px;"><?  echo $comp[str_replace("'","",$cbo_company_name)];?> </span></td>
							
							<td ><b style="font-size:21px;">Prod Unit: </b><span style="font-size:19px;"><?echo $working_company;?></span></td>
							
                        </tr>
                      </table>
                </td>       
            </tr>
       </table>
       <br>
            <?
             $order_price_per_dzn=0;
		
			foreach ($data_array as $row)
			{
				
				$avg_unit_price=$row[csf("avg_unit_price")];
				$sourcing_date=$row[csf("sourcing_date")];
				$buyer_name_id=$row[csf("buyer_name")];
				$remarks=$row[csf("remarks")];//a.remarks,a.set_break_down
				$set_break_down=$row[csf("set_break_down")];
				$order_uom=$row[csf("order_uom")];
				$total_set_qnty=$row[csf("total_set_qnty")];
				$approved=$row[csf("approved")];
			 
				$order_values = $order_job_qnty*$avg_unit_price;
				 if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_val=" PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				
					$quot_id=$row[csf("quotation_id")];
					$style_description=$row[csf("style_description")];
					if($style_description!='') $style_desc="(".$style_description.")";else $style_desc="";
					$sew_smv=$row[csf("set_smv")];
					$inserted_by=$user_passArr[$row[csf("inserted_by")]];
				?>
						<table align="left" border="0" cellpadding="0" cellspacing="0" style="width:450px; margin:5px;">
                        <tr>
                        <td>
                        
                        <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:550px; margin:5px;" rules="all">
							<tr>
								<td width="80">Job No</td>
								<td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
								<td width="90">Costing</td>
								
								</tr>
							<tr>
                            <tr>
								<td width="80">Body/Wash Color</td>
								<td width="80"><b><? echo $color_library[$row[csf("body_wash_color")]];; ?></b></td>
								<td width="100"><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
							</tr>
                            <tr>
							<td> Costing Date :  </td>
							<td colspan="2"><b><? echo $row[csf("costing_date")]; ?></b></td>
							</tr>
                             
							<tr>
								<td>Buyer </td>
								<td colspan="2"><b><? echo $buyer_arr[$row[csf("buyer_name")]];
								if($row[csf("brand_id")]>0)
								{
									echo '('.$brand_arr[$row[csf("brand_id")]].')';
								}
								?></b></td>
							 </tr> 
							 <tr>
								<td>Style </td>
								<td colspan="2"><b><? echo $row[csf("style_ref_no")].$style_desc; ?></b></td>
							 </tr>
							<tr>
								<td width="80">Item</td>
								<?
									$set_break_downArr=explode("__",$set_break_down);
									$smv_arr=array();
									foreach($set_break_downArr as $keyitemData)
									{
										$keyitemDataArr=explode("_",$keyitemData);
										$item_id=$keyitemDataArr[0];
										$smv=$keyitemDataArr[2];
										$smv_arr[$item_id]=$smv;
									}
									if($row[csf("order_uom")]==1)
									{
									  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]].' ('.$smv_arr[$row[csf("gmts_item_id")]].')';
									}
									else
									{
										$gmt_item=explode(',',$row[csf("gmts_item_id")]);
										foreach($gmt_item as $key=>$val)
										{
											$grmnt_items .=$garments_item[$val].' ('.$smv_arr[$val].')'.", ";
										}
									}
								?>
								<td width="100" colspan="2"><b><? echo rtrim($grmnt_items,', '); ?></b></td>
							</tr>
							<tr>
								<td>Season</td>
								<td colspan="2"><b><? //echo $season_name_arr[$row[csf("season_buyer_wise")]];
								
								echo $season_brand = $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2); ?></b></td>
							 </tr>
							<tr>
								<td>P.O. Qnty</td>
								<td><b><?  echo $order_job_qnty.' '.$unit_of_measurement[$order_uom];
								$offer_qty_dzn=$order_job_qnty/$order_price_per_dzn;
								 ?></b></td>
								<td  title="<? echo $offer_qty_dzn;?>"><b><? echo number_format($offer_qty_dzn,0).' '.$costing_val; ?></b></td>
							</tr>
							
							  <tr>
								<td>Plan Cut Qnty(<? echo $excess_cut.'%';?>)</td>
								<td><b><? echo $plan_cut.' '.$unit_of_measurement[$order_uom];
								$plan_offer_qty_dzn=$plan_cut/$order_price_per_dzn;
								 ?></b></td>
								<td  title="<? echo $plan_offer_qty_dzn;?>"><b><? echo number_format($plan_offer_qty_dzn,0).' '.$costing_val; ?></b></td>
							</tr>
                            <tr>
								<td>Remarks</td>
								<td colspan="2"><b><? echo  $remarks; //total_set_qnty?></b></td>
								 
							</tr>
						</table>
                        </td>
              <?
			} //master part end
		//	die;
           $condition= new condition();
			if(str_replace("'","",$txt_job_no) !=''){
				$condition->job_no("=$txt_job_no");
			}
			if(str_replace("'",'',$txt_po_breack_down_id) !="")
			{
				$condition->po_id("in($txt_po_breack_down_id)");
			}
			$condition->init();
			$fabric= new fabric($condition);
			$trim= new trims($condition);
			$wash= new wash($condition);
			$emblishment= new emblishment($condition);
			//echo $fabric->getQuery();die;
			$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//print_r($fabric_qty_arr);
			$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$sourcing_fabric_amount_arr=$fabric->getAmountArray_by_FabriccostidSourcing_knitAndwoven_greyAndfinish();
			
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_amountSourcing_arr=$trim->getAmountArray_precostdtlsidSourcing();
			//print_r($trim_amountSourcing_arr);
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			
			$emblishment_qtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			$emblishment_amountArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
			$wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		//	print_r($wash_qtyArr);
			$wash_amountArr=$wash->getAmountArray_by_jobAndEmblishmentid();
	           
			$sql_determin=sql_select("select a.id,a.type from lib_yarn_count_determina_mst a where  a.status_active=1  and a.entry_form=426");
			foreach($sql_determin as $row)
			{
				$determin_type_arr[$row[csf('id')]]=$row[csf('type')];	
			}
			
			
			// $pri_fab_arr="select a.quotation_id,b.fabric_source,b.lib_yarn_count_deter_id as deter_min_id,b.fabric_description as fab_desc,b.fabric_description,b.body_part_id,b.gsm_weight,b.uom, a.rate,a.amount, (a.requirment) as requirment,a.cons, (a.pcs) as pcs,a.process_loss_percent as p_loss from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 and a.quotation_id=$quot_id ";//and b.fabric_source=2
			//$pri_fab_result=sql_select($pri_fab_arr);
			//approved_no=$revised_no  b.approved_no=$revised_no and revised_no
			   $pre_fab_his_arr="select  b.approved_no,b.sourcing_rate,b.pre_cost_fabric_cost_dtls_id as id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls_h  b where  b.approved_no=$revised_no and  b.status_active=1 and b.is_deleted=0 and b.job_no=$txt_job_no order by id";
			   $his_pre_fab_result=sql_select($pre_fab_his_arr);
			   foreach($his_pre_fab_result as $row)
				{
					$determin_type=$determin_type_arr[$row[csf('deter_min_id')]];
					$body_partId=$body_part[$row[csf('body_part_id')]];
					$sourcing_rate=$row[csf('sourcing_rate')];
					//$fab_desc=$body_partId.','.$row[csf('fab_desc')];
					$fab_desc=$body_partId.','.$row[csf('fab_desc')];
					$fab_cost_dtls_id=$row[csf('id')];
					$his_p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_rate']=$sourcing_rate;
				}
				//print_r($his_p_fab_precost_arr);
			  
			  $pre_fab_arr="select  b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 and b.job_no=$txt_job_no order by id";//and b.fabric_source=2
			$pre_fab_result=sql_select($pre_fab_arr);
			
			$summ_fob_pcs=0;$summ_fob_gross_value_amt=$summ_sourcing_tot_budget_dzn_val=0;
			foreach($pre_fab_result as $row)
			{
				$determin_type=$determin_type_arr[$row[csf('deter_min_id')]];
				$body_partId=$body_part[$row[csf('body_part_id')]];
				//$fab_desc=$body_partId.','.$row[csf('fab_desc')];
				$fab_desc=$body_partId.','.$row[csf('fab_desc')];
				$fab_cost_dtls_id=$row[csf('id')];
				//echo $determin_type.'d';
				$tot_amt=$row[csf('avg_cons')]*$row[csf('rate')];
				$fab_req_qty=$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_req_amount=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				
				$fab_sourcing_rate_his=$his_p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_rate'];
				if($fab_sourcing_rate_his>0)
				{
					$sourcing_fab_req_amount=$fab_req_qty*$fab_sourcing_rate_his;
				}
				else
				{
				 	$sourcing_fab_req_amount=$sourcing_fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$sourcing_fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				}
				
				
				//$sourcing_fab_req_amount=$sourcing_fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$sourcing_fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				
				
				
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['req_qty']+=$fab_req_qty;
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['req_amount']+=$fab_req_amount;
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_req_amount']+=$sourcing_fab_req_amount;
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['cons']+=$row[csf('avg_finish_cons')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['tot_cons']+=$row[csf('avg_cons')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['amount']+=$row[csf('amount')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['p_loss']=$row[csf('avg_process_loss')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['fabric_source']=$row[csf('fabric_source')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['fab_desc']=$row[csf('construction')].','.$row[csf('composition')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$p_fab_precost_tot_row+=1;	
				//Summary
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$fab_req_qty*$row[csf('sourcing_rate')];
				
				$summ_fob_gross_value_amt+=$fab_req_amount;
			}
			//echo $summ_sourcing_tot_budget_dzn_val.', ';
		 $pre_trim_consarr="select b.id,c.trim_type,c.item_name,b.description,b.seq,b.trim_group,b.cons_dzn_gmts,b.cons_uom as uom,avg(d.excess_per) as  excess_per from wo_pre_cost_trim_cost_dtls b,lib_item_group c,wo_pre_cost_trim_co_cons_dtls d where  c.id=b.trim_group and b.id=d.wo_pre_cost_trim_cost_dtls_id and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and b.job_no=$txt_job_no group by b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.cons_dzn_gmts,b.cons_uom,b.seq order by b.seq";
		$pre_trim_cons_result=sql_select($pre_trim_consarr);
			foreach($pre_trim_cons_result as $row)
			{
				$trims_type=$row[csf('trim_type')];
				
				
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				if($trims_type==1) //Sewing
				{
						$his_p_sew_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];
				}
				else
				{
						$his_p_fin_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];;
				}
				
			}
		//	print_r($p_sew_trim_precost_excess_arr);
		
			//$summ_sourcing_tot_budget_dzn_val=0;
			$pre_trim_his_arr="select  b.approved_no,b.sourcing_rate,b.seq,b.pre_cost_trim_cost_dtls_id as id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls_his b,lib_item_group c where   b.approved_no=$revised_no and  c.id=b.trim_group and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.job_no=$txt_job_no order by b.seq";//and b.fabric_source=2
			$pre_trim_result_his=sql_select($pre_trim_his_arr);
			foreach($pre_trim_result_his as $row)
			{
				$trims_type=$row[csf('trim_type')];
				
				
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
				$his_p_sew_trim_precost_arr[$row[csf('id')]][$item_id]['sourcing_rate']=$row[csf('sourcing_rate')];
			}
			//print_r($his_p_sew_trim_precost_arr);
			
				$pre_trim_arr="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.job_no=$txt_job_no order by b.seq";//and b.fabric_source=2
			$pre_trim_result=sql_select($pre_trim_arr);
			
			$p_sew_trim_precost_arr=$p_fin_trim_precost_arr=array();
			foreach($pre_trim_result as $row)
			{
				$trims_type=$row[csf('trim_type')];
				
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
				$req_amt=$row[csf('cons_dzn_gmts')]*$row[csf('rate')];
				
				if($trims_type==1) //Sewing
				{
					$p_sew_loss=$row[csf('ex_per')];
					$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_sew_loss)/100);
					
					$trim_req_qty=$trim_qty_arr[$row[csf('id')]];
					//$trim_amountSourcing_arr
				$trim_req_amount=$trim_amount_arr[$row[csf('id')]];
				$t_sourcing_rate=$his_p_sew_trim_precost_arr[$row[csf('id')]][$item_id]['sourcing_rate'];
				if($t_sourcing_rate>0)
				{
					$trim_req_amountSourcing=$trim_req_qty*$t_sourcing_rate;
				}
				else
				{
				 	$trim_req_amountSourcing=$trim_amountSourcing_arr[$row[csf('id')]];
				}
				
				//$trim_req_amountSourcing=$trim_amountSourcing_arr[$row[csf('id')]];
				//$trim_req_amountSourcing=$his_p_sew_trim_precost_arr[$row[csf('id')]][$item_id]['sourcing_rate'];
				$p_sew_trim_precost_arr[$item_id]['req_qty']+=$trim_req_qty;
				$p_sew_trim_precost_arr[$item_id]['req_amount']+=$trim_req_amount;
				$p_sew_trim_precost_arr[$item_id]['req_amount_sourcing']+=$trim_req_amountSourcing;
				$p_sew_trim_precost_arr[$item_id]['cons']+=$row[csf('tot_cons')];
				$p_sew_trim_precost_arr[$item_id]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_sew_trim_precost_arr[$item_id]['amount']+=$row[csf('amount')];
				$p_sew_trim_precost_arr[$item_id]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_sew_trim_precost_arr[$item_id]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_sew_trim_precost_arr[$item_id]['p_loss']=$p_sew_loss;
				//$p_sew_trim_precost_arr[$item_id]['tot_row']+=1;
				$p_sew_trim_tot_row+=1;
				$p_sew_trim_precost_arr[$item_id]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];	
				$summ_sourcing_tot_budget_dzn_val+=$trim_req_amountSourcing;
				$summ_fob_gross_value_amt+=$trim_req_amount;
				}
				else //packing/Finish
				{
					$p_fin_loss=$row[csf('ex_per')];
				//	$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_fin_loss)/100);
				$trim_fin_req_qty=$trim_qty_arr[$row[csf('id')]];
				$trim_fin_req_amount=$trim_amount_arr[$row[csf('id')]];
				$sourcing_rate_his=$his_p_sew_trim_precost_arr[$row[csf('id')]][$item_id]['sourcing_rate'];
				if($sourcing_rate_his>0)
				{
					$trim_fin_req_amountSourcing=$trim_fin_req_qty*$his_p_sew_trim_precost_arr[$row[csf('id')]][$item_id]['sourcing_rate'];
				}
				else
				{
				 	$trim_fin_req_amountSourcing=$trim_amountSourcing_arr[$row[csf('id')]];
				}
				
				//echo $trim_fin_req_qty.'='.$row[csf('sourcing_rate')];
				$p_fin_trim_precost_arr[$item_id]['req_qty']+=$trim_fin_req_qty;
				$p_fin_trim_precost_arr[$item_id]['req_amount']+=$trim_fin_req_amount;
				$p_fin_trim_precost_arr[$item_id]['req_fin_amount_sourcing']+=$trim_fin_req_amountSourcing;
				$p_fin_trim_precost_arr[$item_id]['cons']+=$row[csf('tot_cons')];
				$p_fin_trim_precost_arr[$item_id]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_fin_trim_precost_arr[$item_id]['amount']+=$row[csf('amount')];
				if($row[csf('sourcing_rate')]>0)
				{
				$p_fin_trim_precost_arr[$item_id]['fin_sourcing_rate']=$row[csf('sourcing_rate')];
				}
				$p_fin_trim_precost_arr[$item_id]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_fin_trim_precost_arr[$item_id]['p_loss']=$p_fin_loss;
				$p_fin_trim_tot_row+=1;
				$p_fin_trim_precost_arr[$item_id]['uom']=$unit_of_measurement[$row[csf('uom')]];	
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$trim_fin_req_amountSourcing; 
				$summ_fob_gross_value_amt+=$trim_fin_req_amount;
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]*$order_price_per_dzn;
				
			}
			//print_r($p_sew_trim_precost_arr2);
			//echo $summ_sourcing_tot_budget_dzn_val.', ';
			
		$pre_wash_his_arr="select b.approved_no,b.sourcing_rate,b.job_no,b.pre_cost_embe_cost_dtls_id as id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls_his  b where  b.approved_no=$revised_no and b.status_active=1 and b.is_deleted=0  and b.job_no=$txt_job_no  order by b.emb_name";//and b.fabric_source=2
			$pre_wash_result_his=sql_select($pre_wash_his_arr);
			foreach($pre_wash_result_his as $row)
			{
				$emb_name_id=$row[csf('emb_name')];
				$sourcing_rate=$row[csf('sourcing_rate')];
				$his_p_wash_precost_arr[$row[csf('id')]][$emb_name]['sourcing_rate']=$sourcing_rate;
			}
			
			
			 $pre_wash_arr="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where  b.status_active=1 and b.is_deleted=0  and b.job_no=$txt_job_no  order by b.emb_name";//and b.fabric_source=2
			$pre_wash_result=sql_select($pre_wash_arr);
			
		//	$summ_sourcing_tot_budget_dzn_val=0;
		 
			foreach($pre_wash_result as $row)
			{
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
			
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				 
				$wash_req_amount=$emb_req_amount=0;
				$his_sourcing_rate=$his_p_wash_precost_arr[$row[csf('id')]][$emb_name]['sourcing_rate'];
				if($his_sourcing_rate>0) $his_sourcing_rate=$his_sourcing_rate;else $his_sourcing_rate=$$row[csf('sourcing_rate')];
				if($row[csf('emb_name')]==3) //Wash
				{
					
						
						if($row[csf('emb_type')]>0) $wash_emb_typeCond=", ".$emblishment_wash_type[$row[csf('emb_type')]];else $wash_emb_typeCond="";
						$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$wash_emb_typeCond;
						$wash_req_qty=$wash_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
						$wash_req_amount=$wash_amountArr[$row[csf('job_no')]][$row[csf('id')]];
						// echo $emb_name.'='.$wash_emb_typeCond.' <br>';
					 
				$p_wash_precost_arr[$emb_name]['req_qty']+=$wash_req_qty;
				$p_wash_precost_arr[$emb_name]['req_amount']+=$wash_req_amount;
				$p_wash_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_wash_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
				$p_wash_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
				$p_wash_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_wash_precost_arr[$emb_name]['pre_rate']=$row[csf('rate')];
				$p_wash_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_wash_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$wash_req_qty*$his_sourcing_rate;
				$p_wash_tot_row+=1;	
				}
				else
				{
				$emb_req_qty=$emblishment_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
				$emb_req_amount=$emblishment_amountArr[$row[csf('job_no')]][$row[csf('id')]];
				$p_embro_precost_arr[$emb_name]['req_qty']+=$emb_req_qty;
				$p_embro_precost_arr[$emb_name]['req_amount']+=$emb_req_amount;
				$p_embro_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_embro_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
				$p_embro_precost_arr[$emb_name]['pre_rate']+=$row[csf('rate')];
				$p_embro_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
				if($row[csf('sourcing_rate')]>0)
				{
				$p_embro_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
				}
				$p_embro_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_embro_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$emb_req_qty*$his_sourcing_rate;
				$p_embro_tot_row+=1;	
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=$emb_req_amount+$wash_req_amount;
			}
			//echo $summ_fob_gross_value_amt.'=';
				//echo $summ_fob_pcs.'A';
				//echo $summ_fob_value_pcs.'C,';
			//wo_pri_quo_comarcial_cost_dtls 
			 
			$sql_other = "select fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where  job_no=$txt_job_no  and status_active=1 and  is_deleted=0";
			
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
			 foreach( $pre_other_result as $row )
			{
				$lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$order_job_qnty;
				$currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$inspection=($row[csf('inspection')]/$order_price_per_dzn)*$order_job_qnty;
				$comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$freight=($row[csf('freight')]/$order_price_per_dzn)*$order_job_qnty;
				$certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty;
				$depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$order_job_qnt;
				$income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
				//echo $tot_other_for_fob_value;
				$lab_test_dzn=$row[csf('lab_test')];
				$fob_pcs=$row[csf('price_with_commn_pcs')];
				$currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
				$inspection_dzn=$row[csf('inspection')];
				$comarcial_dzn=$row[csf('comm_cost')];
				
				$common_oh_dzn=$row[csf('common_oh')];
				$studio_pre_cost_dzn=$row[csf('studio_cost')];
				$design_pre_cost_dzn=$row[csf('design_cost')];
				$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
				
				$freight_dzn=$row[csf('freight')];
				//$comm_cost_dzn=$row[csf('comm_cost')];
				$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
				$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
				$interest_pre_cost_dzn=$row[csf('interest_cost')];
				
				$cm_cost_dzn=$row[csf('cm_cost')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$offer_qty_dzn;
				//$lab_test_dzn=$row[csf('lab_test')];
				
				$tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;
				
				$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				//$summ_fob_value_pcs+=($tot_other_cost_dzn+$currier_pre_cost_dzn+$lab_test_dzn+$inspection_dzn+$comarcial_dzn)*$order_price_per_dzn+$cm_cost_pcs;
				
				// echo $tot_other_for_fob_value.'m';
				$summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;
				
				$summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;
				
				$summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;
				 
			}
			
			//echo $summ_fob_pcs.'S';
		 	//echo $summ_fob_gross_value_amt.'H';
			//echo $common_oh_dzn.'='.$studio_pre_cost_dzn.'='.$design_pre_cost_dzn.'='.$certificate_pre_cost_dzn.'='.$freight_dzn.'='.$depr_amor_pre_cost_dzn.'='.$income_tax_pre_cost_dzn.'='.$interest_pre_cost_dzn; 
			$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no." and status_active=1";
		$result_commi=sql_select($sql_commi);
		 foreach( $result_commi as $row )
			{
				$commission_type_id=$row[csf('particulars_id')];
				$com_type_id=$row[csf('commission_base_id')];
				
				$commission_arr[$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$commission_arr[$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
				$commission_arr[$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
				//$summ_fob_value_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$summ_fob_pcs+=$row[csf('commission_amount')];
				$summ_sourcing_tot_budget_dzn_val+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
			} 
		
		//	$summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;
				//echo $summ_fob_pcs.'S';
				$tot_summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;
			
			//echo $summ_fob_gross_value_amt.'='.$summ_sourcing_tot_budget_dzn_val.'='.$offer_qty_dzn.'d';;
			$summ_tot_final_cm=($summ_fob_gross_value_amt-$summ_sourcing_tot_budget_dzn_val)/$offer_qty_dzn;
			
			
			$summ_sourcing_fob_pcs=($summ_sourcing_tot_budget_dzn_val+$tot_cm_qty_dzn)/$order_job_qnty;
			//echo $summ_tot_final_cm.'='.$tot_cm_qty_dzn.'='.$summ_sourcing_tot_budget_dzn_val;
			
			 $supplier_library_arr=return_library_array( "select a.short_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id   and a.is_deleted=0  and a.status_active=1 group by a.id,a.short_name order by a.short_name", "id", "short_name");

				?>
                        <td valign="top">
                        	 <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:270px; margin:5px;" rules="all">
                             <caption> <b>Summary </b></caption>
							<tr>
								<td width="80"><b>Header </b></td>
                                <td width="80"><b>Pre Cost</b> </td>
								<td width="80"><b>Final Cost</b></td>
								
							</tr>
                            <tr>
								<td>Po Qty <? echo $unit_of_measurement[$order_uom];?></td>
								<td colspan="3" title="=<? echo $order_job_qnty.' '.$unit_of_measurement[$order_uom];?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($order_job_qnty,0); ?></b></td>
							 </tr> 
                             
                              <tr>
								<td>Unit Price/<? echo $unit_of_measurement[$order_uom];?></td>
								<td colspan="3" title="Price=<? echo $avg_unit_price;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($avg_unit_price,4); ?></b></td>
							 </tr> 
                             <tr>
								<td>SMV/<? echo $unit_of_measurement[$order_uom];?></td>
								<td colspan="3" title="=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($sew_smv,4); ?></b></td>
							 </tr> 
							<tr>
								<td>FOB/<? echo $unit_of_measurement[$order_uom];?></td>
								<td  title="FOBValue=<? echo $summ_fob_pcs;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;/$total_set_qnty
								echo  number_format($tot_summ_fob_pcs,4); ?></b></td>
                                <td  title="Total value(<? echo $summ_sourcing_tot_budget_dzn_val+$tot_cm_qty_dzn;?>)/PO Qty <? echo $unit_of_measurement[$order_uom];?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								//echo  number_format($summ_sourcing_fob_pcs,4); ?></b></td>
							 </tr> 
                             <tr>
								<td>Margin/<? echo $unit_of_measurement[$order_uom];?>(USD)</td>
								<td  title="Avg Rate-FoB Pcs=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								 $margin_pre=$avg_unit_price-$tot_summ_fob_pcs;
								 $margin_final=$avg_unit_price-$summ_sourcing_fob_pcs;
								echo  number_format($margin_pre,6); ?></b></td>
                                <td  title="Avg Rate-Sourcing FoB Pcs=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								//echo  number_format($margin_final,4); ?></b></td>
							 </tr> 
							<tr>
								<td>CM/Dzn(USD)</td>  
								<td><b><? echo number_format($cm_cost_dzn,6); ?></b></td>
                                <td title="Gross Fob-Sourcing Budget Dzn/PO Qty Dzn"><b><? echo number_format($summ_tot_final_cm,6); ?></b></td>
							 </tr> 
                             <tr>
								<td>E.P.M(USD)</td>
								<td title="CM/Costing Per/Sew SMV"><b><? echo number_format($cm_cost_dzn/$order_price_per_dzn/$sew_smv,6); ?></b></td>
                                <td title="CM Final /Costing Per/Sew SMV"><b><? echo number_format($summ_tot_final_cm/$order_price_per_dzn/$sew_smv,6); ?></b></td>
							 </tr> 
						</table>
                        </td>
                        <td valign="top">
                        <?
                        $nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id=".$txt_job_no." and file_type=1");
						//echo "SELECT image_location,real_file_name FROM common_photo_library where master_tble_id=".$txt_job_no." and file_type=1";
						?>
                <table width="210">
                <tr>
                <?
				 $path="../../";
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				     

					?>
					<td>
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path .''. $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <?
					  
					   ?>
					</td>
					<?

					$img_counter++;
				}
				?>
               	 </tr>
           		</table>
                        </td>
                         <td valign="top">
                        	 <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:260px; margin:1px;" rules="all">
                            
                            <tr>
								<td>Order Confirmation Date	</td>
								 <td><b><? echo $po_received_date; ?></b></td>
							 </tr> 
                             
                              <tr>
								<td>Sourcing Rcvd Date	</td>
								<td><b><? echo $sourcing_date; ?></b></td>
							 </tr> 
                              <tr>
								<td>Pack Handover Date</td>
								 <td><b><? $date=date_create("$min_p_handover_date"); echo strtoupper(date_format($date,"d-M-y"));?></b></td>
							 </tr> 
                              <tr>
								<td title="Ship Date">Garments Delivery Date	</td>
								<td><b><?  $date=date_create("$shipment_dateActual"); echo strtoupper(date_format($date,"d-M-y"));?></b></td>
							 </tr> 
                              <tr>
								<td>Total Garment Lead Time	</td>
								<td><b><? if($shipment_dateActual!="") echo $leadtime_days_remian.' Days';else echo " "; ?></b></td>
							 </tr> 
                             <tr>
								<td colspan="2" align="center"><b style="color: #F00; font-size:24px;">
											<? if( $approved==1)
											{
													echo "Approved";
											}
											else if( $approved==3)
											{
													echo "Partial Approved";
											}
											else echo " UN-Approved";
								  ?></b></td>
							 </tr> 
                             
						</table>
                        </td>
                        </tr>
                       
                        </table>
                        <style>
						#td_boder{ border-right:solid 3px;};
						</style>
					<?
			
			 //end first foearch
			
			 
			
			?>
            <div style="">
             	<table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <tr>
                <th colspan="11" style="background-color:#963;font-size:20px;" id="td_boder"> <b>Merchandiser's Part</b></th>
                 <th colspan="4"  style="background-color: #996;font-size:20px;"> <b>Budget- Sourcing Part</b></th>
                </tr>
                 <tr>
                    <th  width="20">SL </th>
                    <th  width="100" title="Fabric">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th  width="70">Wast % </th>
                    <th  width="70">Total Cons/Dzn</th>
                    <th  width="50">UOM </th>
                    <th  width="70">Req. Qty</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total <br>Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /<br>Excess</th>
                    <th  width="70">Supplier</th>
                   
                    </tr>
                    
                    <?
					$f=1;$tot_fab_amount=$tot_fab_amount_pcs=$tot_fab_req_amount=0;$ff=1;$tot_fab_req_sourcing_amount=$tot_fab_req_sourcing_bal_amount=0;
                    foreach($p_fab_precost_arr as $fab_cost_dtls_id=>$fab_cost_dtls_data)
					{
						foreach ($fab_cost_dtls_data as $fab_type=>$fab_data) 
						{
							 foreach($fab_data as $fab_desc=>$row)
							{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$nominated_supp_str=""; 
							 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
							 foreach($exnominated_supp as $supp)
							 {
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
							 }	
							 $stock='';
							 if($row[('fabric_source')]==4)
							 {
								$stock="<b style='color:red'> (Stock Fabric) </b>";
							 }
							   //echo $stock.' DD';
	                    ?>
	                	<tr bgcolor="<? echo $bgcolor;?>">
	                   
	                    <td width="20" align="center"><p><? echo $f; ?></p></td>
	                   
	                    <td width="100"><div style="word-break:break-all"><? echo $fab_desc.$stock;//echo $fab_type.','.$fab_desc; ?></div></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('cons')],6); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],6); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('tot_cons')],6); ?></p></td>
	                    <td width="50"><p><? echo $row[('uom')]; ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],6); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],6); ?></p></td>
	                    <td width="70"align="right"><p><? echo number_format($row[('amount')],6); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,6); ?></p></td>
	                    <td width="70"  id="td_boder" align="right"><p><? echo number_format($row[('req_amount')],6); ?></p></td>
	                    
	                   <td width="70" align="right"><p><? echo number_format($row[('sourcing_req_amount')]/$row[('req_qty')],6); ?></p></td>
	                    <td width="70"align="right" title="S.Rate=<? echo $row[('sourcing_rate')];?>"><p><? $sourcing_amount=$row[('sourcing_req_amount')];;echo number_format($sourcing_amount,6); ?></p></td>
	                    <td width="70" align="right" title="Marchandiser Amount-Sourcing Amount"><p><?  $bal_sourcing_amount=$row[('req_amount')]-$sourcing_amount;echo number_format($bal_sourcing_amount,6); ?></p></td>
	                    <td width="70" align="center"><div style="word-break:break-all"><?  echo $nominated_supp_str; ?></div></td>
	                   
	                    </tr>
						<?
							$f++;$ff++;
							$tot_fab_amount+=$row[('amount')];
							$tot_fab_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
							$tot_fab_req_amount+=$row[('req_amount')];
							$tot_fab_req_sourcing_amount+=$row[('sourcing_rate')]*$row[('req_qty')];
							$tot_fab_req_sourcing_bal_amount+=$bal_sourcing_amount;
							}
						}
					}
					?>
                     
                   
                      <tr style="font-size:17px; background-color:#CCC">
                        <td colspan="8"> <b style="float:left">A Total Fabric Cost</b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_amount_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_fab_req_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fab_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_req_sourcing_amount,6); ?></b></td>
                        <td  align="right"><b><?  echo number_format($tot_fab_req_sourcing_bal_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fab_req_amount,6); ?></b></td>
                        
                    </tr>
                     
                </table>
                <br>
                <?
               // die;
				?>
                <table class="rpt_table" align="left" border="1" cellpadding="1" cellspacing="1"  width="98%" style="margin:5px;" rules="all">
                
                 <thead>
                 <tr>
                 	
                    <th  width="20">SL </th>

                    <th  width="100" title="Trim sew">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th  width="70">Wast % </th>
                    <th  width="70">Total Cons/Dzn</th>
                    <th  width="50">UOM </th>
                    <th  width="70">Req. Qty</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total Budget</th>
                  
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /<br>Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                    </thead>
                     
                    <?
					$ts=1;$tot_sew_amount=$tot_amount_pcs=$tot_sew_amount_pcs=$tot_sew_req_amount=0;$ttts=1;$tot_sew_req_sourcing_amount=$tot_sew_req_sourcing_bal_amount=0;
                    foreach($p_sew_trim_precost_arr as $item_id=>$row)
					{
						
						if($ts%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$nominated_supp_str=""; 
						 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_supp as $supp)
						 {
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
						 $req_amountSourcing=$row[('req_amount_sourcing')];
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                   
                    <td width="20" align="center"><p><? echo $ts; ?></p></td>
                    <td width="100" ><div style="word-break:break-all"><? echo $item_id; ?></div></td> 
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],6); ?></p></td>
                    <td width="70" align="right"><p><?  if($row[('p_loss')]>0) echo number_format($row[('tot_cons')],6);else echo number_format($row[('cons')],6);?></p></td>
                    <td width="50" align="left"><p><? echo $row[('uom')]; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],6); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,6); ?></p></td>
                    <td width="70" id="td_boder" align="right"><p><? echo number_format($row[('req_amount')],6); ?></p></td>
                    
                    <td width="70" align="right"><p><? echo number_format($req_amountSourcing/$row[('req_qty')],6); ?></p></td>
                    <td width="70"align="right"  title="SouringAmt=<? echo $req_amountSourcing.', Rate'.$row[('sourcing_rate')];?>"><p><? $sourcing_amount=$req_amountSourcing;echo number_format($sourcing_amount,6); ?></p></td>
                    <td width="70" align="right"><p><?  $bal_sourcing_amout=$row[('req_amount')]-$sourcing_amount;echo number_format($bal_sourcing_amout,6); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $nominated_supp_str; ?></div></td>
                     
                    </tr>
					<?
						$ts++;$ttts++;
						$tot_sew_amount+=$row[('amount')];
						$tot_sew_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_sew_req_amount+=$row[('req_amount')];
						$tot_sew_req_sourcing_amount+=$sourcing_amount;//$row[('sourcing_rate')]*$row[('req_qty')];
						$tot_sew_req_sourcing_bal_amount+=$bal_sourcing_amout;
						
					}
					?>
                     

                     
                    <tr style="font-size:17px; background-color:#CCC">
                        <td colspan="8" align="left"><b>B-SubTotal for Sewing Trims Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_amount_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_sew_req_amount,6); ?></b></td>
                        
                         <td  align="right"><b><? //echo number_format($tot_sew_amount,6); ?></b></td>
                          <td  align="right"><b><? echo number_format($tot_sew_req_sourcing_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_req_sourcing_bal_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_sew_req_amount,6); ?></b></td>
                       
                        
                    </tr>
                    
                     
                </table>
                 <br>
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                 <tr>
                 	
                    <th width="20">SL </th>
                    <th  width="100" title="Trim Fin">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70">Rate(USD)</th>
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    </tr>
                     
                    <?
					$tf=1;$tot_fin_amount=$tot_fin_amount_pcs=$tot_fin_req_amount=0;$tttf=1;$tot_fin_req_sourcing_amount=$tot_fin_req_sourcing_bal_amount=0;
                    foreach($p_fin_trim_precost_arr as $item_id=>$row)
					{
						
						if($tf%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$nominated_supp_str=""; 
						 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_supp as $supp)
						 {
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }	
						  $req_fin_amountSourcing=$row[('req_fin_amount_sourcing')];		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">                     
                    <td width="20" align="center"><p><? echo $tf; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? echo  $item_id; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],6); ?></p></td>
                    <td width="70" align="right"><p><?   if($row[('p_loss')]>0) echo number_format($row[('tot_cons')],6);else echo number_format($row[('cons')],6); //echo number_format($row[('tot_cons')],6); ?></p></td>
                    <td width="50"><p><? echo $row[('uom')]; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],6); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,6); ?></p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($row[('req_amount')],6); ?></p></td>
                    
                    <td width="70" align="right" title="Amt=<? echo $req_fin_amountSourcing.',Rate'.$row[('fin_sourcing_rate')];?>"><p><? echo number_format($req_fin_amountSourcing/$row[('req_qty')],6); ?></p></td>
                     <td width="70"align="right"><p><? $fin_sourcing_amount=$req_fin_amountSourcing;//$row[('fin_sourcing_rate')]*$row[('req_qty')];
					 echo number_format($fin_sourcing_amount,6); ?></p></td>
                    <td width="70" align="right"><p><?  $fin_bal_sourcing_amout=$row[('req_amount')]-$fin_sourcing_amount;echo number_format($fin_bal_sourcing_amout,6); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $nominated_supp_str; ?></div></td>
                     
                    </tr>
                    
                    
					<?
						$tf++;$tttf++;
						$tot_fin_amount+=$row[('amount')];
						$tot_fin_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_fin_req_amount+=$row[('req_amount')];
						$tot_fin_req_sourcing_amount+=$fin_sourcing_amount;
						$tot_fin_req_sourcing_bal_amount+=$fin_bal_sourcing_amout;
						
					}
					?>
                     
                    
                     <tr style="font-size:17px; background-color:#CCC">
                      
                        <td colspan="8"> <b>C-Sub Total for Finishing & Packing Trims Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_fin_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fin_amount_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_fin_req_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                      
                        <td  align="right"><b><? echo number_format($tot_fin_req_sourcing_amount,6); ?></b></td>
                        <td  align="right"><b><?  echo number_format($tot_fin_req_sourcing_bal_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fin_req_amount,6); ?></b></td>
                        
                        
                    </tr>
                    
                    <tr style="font-size:17px; background-color:#CCC">
                       
                        <td colspan="8"><b>Total Trims Cost [B+C]:</b></td>
                        <td  align="right"><b><? $trim_sew_fin_amt=$tot_sew_amount+$tot_fin_amount;echo number_format($trim_sew_fin_amt,6); ?></b></td>
                        <td  align="right"><b><? $trim_sew_fin_amt_pcs=$tot_sew_amount_pcs+$tot_fin_amount_pcs; echo number_format($trim_sew_fin_amt_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? $trim_fin_req_amount=$tot_sew_req_amount+$tot_fin_req_amount; echo number_format($trim_fin_req_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                        
                        <td  align="right"><b><? $tot_sourcing_trim_sew_fin_amount=$tot_fin_req_sourcing_amount+$tot_sew_req_sourcing_amount; echo number_format($tot_sourcing_trim_sew_fin_amount,6); ?></b></td>
                        <td  align="right"><b><?  $tot_trim_source_amount_bal=$trim_fin_req_amount-$tot_sourcing_trim_sew_fin_amount;echo number_format($tot_trim_source_amount_bal,6); ?></b></td>
                        <td  align="right"><b><? //$trim_fin_req_amount=$tot_sew_req_amount+$tot_fin_req_amount; echo number_format($trim_fin_req_amount,6); ?></b></td>
                       
                        
                    </tr>
                  
                </table>
                <br>
                 <?
                // die;
				 ?>
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <caption><b style="float:left">Gmts Wash</b></caption>
                 <tr>
                  
                    <th width="20">SL </th>
                    <th width="100" title="Wash ">ITEM DESCRIPTION </th>
                    <th width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70"> Rate(USD)</th>
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                    
                    <?
					$w=1;$tot_wash_amount=$tot_wash_amount_pcs=$tot_wash_req_amount=$tot_wash_sourcing_req_amount=$tot_wash_sourcing_req_amount_bal=0;$ws=1;
                    foreach($p_wash_precost_arr as $embname_id=>$row)
					{
						
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wash_nominated_supp_str=""; 
						 $exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_suppArr as $supp)
						 {
							if($wash_nominated_supp_str=="") $wash_nominated_supp_str=$supplier_library_arr[$supp]; else $wash_nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p><? echo $w; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? $embname_id=explode(", ",$embname_id);echo $embname_id[1]; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],6); ?></p></td>
                    <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                    <td width="70" align="right"><p><?  echo number_format($row[('cons')],6) ?></p></td>
                    <td width="50"><p><? echo 'Dzn'; ?></p></td>
                    <td width="70" align="right" title="<? echo $row[('req_qty')];?>"><p><? echo number_format($row[('req_qty')],6); ?></p></td>
                    <td width="70" align="right" title="Rate=<? //echo $row[('req_amount')]/$row[('req_qty')];?>"><p><? echo $row[('pre_rate')]; //number_format($row[('req_amount')]/$row[('req_qty')],6); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],6); ?></p></td>
                    <td width="70" align="right"  ><p><? echo number_format($row[('amount')]/$order_price_per_dzn,6); ?></p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($row[('pre_rate')]*$row[('req_qty')],6); ?></p></td>
                    
                    <td width="70" align="right" title="S.Rate=<? echo $row[('sourcing_rate')];?>"><p><? echo number_format($row[('sourcing_rate')],6); ?></p></td>
                    <td width="70"align="right"><p><? $sourcing_tot_amount=$row[('req_qty')]*$row[('sourcing_rate')]; echo number_format($sourcing_tot_amount,6); ?></p></td>
                    <td width="70" align="right"><p><? $sourcing_tot_amount_bal=($row[('pre_rate')]*$row[('req_qty')])-$sourcing_tot_amount;echo number_format($sourcing_tot_amount_bal,6); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $wash_nominated_supp_str; ?></div></td>
                   
                    </tr>
                    
                    
					<?
						$w++;$ws++;
						$tot_wash_amount+=$row[('amount')];
						$tot_wash_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_wash_req_amount+=$row[('pre_rate')]*$row[('req_qty')];//$row[('req_amount')];
						$tot_wash_sourcing_req_amount+=$sourcing_tot_amount;
						$tot_wash_sourcing_req_amount_bal+=$sourcing_tot_amount_bal;
						
					}
					?>
                     
                     
                    <tr style="font-size:17px; background-color:#CCC">
                         
                        <td colspan="8"> <b>D-Total Wash Cost :</b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_amount_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_wash_req_amount,6); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                        
                         <td  align="right"><b><? echo number_format($tot_wash_sourcing_req_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_sourcing_req_amount_bal,6); ?>&nbsp;</b></td>
                        <td  align="right"><b><? //echo number_format($tot_wash_req_amount,6); ?></b></td>
                        
                    </tr>
                </table>

                <br>
               
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <caption><b style="float:left">Embellishment Cost</b></caption>
                 <tr>
                 	 
                    <th width="20">SL </th>
                    <th width="100" title="">ITEM DESCRIPTION </th>
                    <th width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70">Rate(USD)</th>
                    
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                      
                    <?
					$em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=$tot_emb_sourcing_req_amount=$tot_emb_sourcing_req_amount_bal=0;$emb=1;
                    foreach($p_embro_precost_arr as $embname_id=>$row)
					{
						
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$emb_nominated_supp_str=""; 
						 $emb_exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($emb_exnominated_suppArr as $supp)
						 {
							if($emb_nominated_supp_str=="") $emb_nominated_supp_str=$supplier_library_arr[$supp]; else $emb_nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                     
                    <td width="20" align="center"><p><? echo $em; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? echo $embname_id; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],6); ?></p></td>
                    <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                    <td width="70" align="right"><p><?  echo number_format($row[('cons')],6); ?></p></td>
                    <td width="50"><p><? echo 'Dzn'; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],6); ?></p></td>
                    
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],6); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,6); ?></p></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($row[('req_amount')],6); ?></b></td>
                    
                     <td width="70" align="right"><p><? echo number_format($row[('sourcing_rate')],6); ?></p></td>
                    <td width="70"align="right"><p><? $emb_sourcing_tot_amount=$row[('req_qty')]*$row[('sourcing_rate')]; echo number_format($emb_sourcing_tot_amount,6); ?></p></td>
                    <td width="70" align="right"><p><? $emb_sourcing_tot_amount_bal=$row[('req_amount')]-$emb_sourcing_tot_amount;echo number_format($emb_sourcing_tot_amount_bal,6); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $emb_nominated_supp_str; ?></div></td>
                    
                      
                    </tr>
					<?
						$em++;$emb++;
						$tot_embro_amount+=$row[('amount')];
						$tot_embro_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_embro_req_amount+=$row[('req_amount')];
						$tot_emb_sourcing_req_amount+=$emb_sourcing_tot_amount;
						$tot_emb_sourcing_req_amount_bal+=$emb_sourcing_tot_amount_bal;
						
					}
					?>
                     
                     
                     <tr style="font-size:17px; background-color:#CCC">
                         
                        <td colspan="8"> <b>E-Total Embellishment Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_amount_pcs,6); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_embro_req_amount,6); ?></b></td>
                        
                        <td  align="right"><b><? //echo number_format($tot_embro_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_emb_sourcing_req_amount,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_emb_sourcing_req_amount_bal,6); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_wash_req_amount,6); ?></b></td>
                    </tr>
                   
                </table>
                <br>
                 
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
               		 <caption><b style="float:left">Others Components</b></caption>
                 <tr>
                 	 
                    <th width="20">SL </th>
                    <th title="520"><b>Others Components</b> </th>
                   
                    <th width="70"><b>Cost/Dzn</b></th>
                    <th width="70"><b>Cost/Pc</b></th>
                    <th width="70" id="td_boder"><b>Total Budget</b></th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">&nbsp;</th>
                    </tr>
                    
                    <?
					//$em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=0;$emb=1;
						$bgcolor="#E9F3FF";
						$bgcolor2="#FFFFFF";//currier_pre_cost_dzn
					 
                      
                   // $tot_other_cost=0;
					$tot_other_cost_first=$tot_other_cost+$comarcial+$inspection+$currier_pre_cost+$lab_test;
					
						$total_other_cost_dzn=$tot_other_cost_dzn+$comarcial_dzn+$inspection_dzn+$lab_test_dzn+$currier_pre_cost_dzn;
						 $tot_other_cost_pcs=$tot_other_cost_dzn/$order_price_per_dzn;
						 $tot_comarcial_dzn_pcs=$comarcial_dzn/$order_price_per_dzn;
						 $tot_inspection_dzn_pcs=$inspection_dzn/$order_price_per_dzn;
						  $currier_pre_cost_dzn_pcs=$currier_pre_cost_dzn/$order_price_per_dzn;
						  $tot_lab_test_dzn_pcs=$lab_test_dzn/$order_price_per_dzn;
						 
						$total_other_cost_pcs=$tot_other_cost_pcs+$tot_comarcial_dzn_pcs+$tot_lab_test_dzn_pcs+$tot_inspection_dzn_pcs+$currier_pre_cost_dzn_pcs;
						
						
						
						$tot_other_cost_req_amount=$tot_other_cost_first;
						
					
					?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520" align="" ><b>Test Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($lab_test_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?   echo number_format($tot_lab_test_dzn_pcs,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                     
                    
                    </tr>
                    <tr>
                     <td width="20" align="center" ><p>2</p></td>
                    <td width="520"><b>Currier Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($currier_pre_cost_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($currier_pre_cost_dzn_pcs,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($currier_pre_cost,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($currier_pre_cost,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($currier_pre_cost,6); ?>&nbsp;</p></td>
                      
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    
                    <td width="20" align="center" ><p>3</p></td>
                    <td width="520"><b>Inspection Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($inspection_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_inspection_dzn_pcs,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($inspection,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($inspection,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($inspection,6); ?>&nbsp;</p></td>
                     
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center" ><p>4</p></td>
                    <td width="520"><b>Commercial Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($comarcial_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($tot_comarcial_dzn_pcs,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($comarcial,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($comarcial,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($comarcial,6); ?>&nbsp;</p></td>
                       
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                     
                    <td width="20" align="center"><p>5</p></td>
                    <td width="520" title="Freight+Certif.Cost+Design Cost+Studio Cost+Deprec.&Amort.+Operating Expenses+Deprec.&Amort.+Interest+Income Tax">
                    <b>Others Charge</b></td> 
                   
                     <td width="70"align="right"><p><? echo number_format($tot_other_cost_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost_pcs,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($tot_other_cost,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                     
                    </tr>
					
                   
                     
                     <tr style="font-size:17px; background-color:#CCC">
                         
                        <td width="540" colspan="2"> <b>F-Total Others Cost:</b></td>
                        <td  align="right"><b><? echo number_format($total_other_cost_dzn,6); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($total_other_cost_pcs,6); ?>&nbsp;</b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_other_cost_req_amount,6); ?>&nbsp;</b></td>
                        
                        
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($tot_other_cost_req_amount,6); ?>&nbsp;</b></td>
                        <td  align="right"><b><?   echo '0';?>&nbsp;</b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?>&nbsp;</b></td>
                        
                    </tr>
                   
                </table>
                <br>
             
                 <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
               		 <caption><b style="float:left"> Commission Cost:</b></caption>
                   <tr>
                 	 
                    <th  width="20">SL </th>
                    <th width="520" title="">Commission Cost </th>
                    <th width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total Budget</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">&nbsp;</th>
                    
                    </tr>
                   
                    <?
                     
						$tot_commission_amount=$commission_arr[1]['commi_amt']+$commission_arr[2]['commi_amt'];
						$tot_commission_amount_pcs=($commission_arr[1]['commi_amt']/$order_price_per_dzn)+($commission_arr[2]['commi_amt']/$order_price_per_dzn);
						$tot_commision_req_amount=$commission_arr[1]['commi_req_amt']+$commission_arr[2]['commi_req_amt'];
						
					
					?>

					 
                   <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520"  title="Local"><b>UK Office Commission</b></td> 
                    <td width="70"align="right"><p><? echo number_format($commission_arr[2]['commi_amt'],6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[2]['commi_amt']/$order_price_per_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($commission_arr[2]['commi_req_amt'],6); ?>&nbsp;</p></td>
                    
                     <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($commission_arr[2]['commi_req_amt'],6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                     
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                     
                    <td width="20" align="center"><p>2</p></td>
                    <td width="520"><b>Buying Commission</b></td> 
                    <td width="70"align="right"><p><? echo number_format($commission_arr[1]['commi_amt'],6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_amt']/$order_price_per_dzn,6); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($commission_arr[1]['commi_req_amt'],6); ?>&nbsp;</p></td>
                     <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_req_amt'],6); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                     
                    </tr>
					
                    <tr>
                         
                        <td width="540" colspan="2"> <b>G-Total Commission Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_commission_amount,6); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($tot_commission_amount_pcs,6); ?>&nbsp;</b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_commision_req_amount,6); ?>&nbsp;</b></td>
                        <td width="70" align="right"><b><? //echo number_format($lab_test,6); ?>&nbsp;</b></td>
                   		 <td width="70" align="right"><b><? echo number_format($tot_commision_req_amount,6); ?>&nbsp;</b></td>
                    	<td width="70" align="right"><b><? echo '0'; ?>&nbsp;</b></td>
                    	<td width="70" align="right"><b><? //echo number_format($lab_test,6); ?>&nbsp;</b></td>
                        
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520"><b>H-Total CM Cost</b></td> 
                    <td width="70"align="right"><b><? echo number_format($cm_cost_dzn,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? $tot_cm_cost_pcs=$cm_cost_dzn/$order_price_per_dzn;echo number_format($tot_cm_cost_pcs,6); ?>&nbsp;</b></td>
                    <td width="70" title="CM Dzn*PO Qty Dzn" id="td_boder" align="right"><b><? echo number_format($tot_cm_qty_dzn,6); ?>&nbsp;</b></td>
                    <td width="70" align="left"  title="Commision+OtherCost+Wash+Emblishmnet+Trims+Fabric">
                    <p><b>Total Final Amount</b></p></td>
                   	<td width="70" title="Commision+OtherCost+Wash+Emblishmnet+Trims+Fabric" align="right"><b> <? $total_final_amount=$tot_commision_req_amount+$tot_other_cost_req_amount+$tot_wash_sourcing_req_amount+$tot_emb_sourcing_req_amount+$tot_sourcing_trim_sew_fin_amount+$tot_fab_req_sourcing_amount;
					 echo number_format($total_final_amount,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? 
					$total_final_amount_bal=$tot_fab_req_sourcing_bal_amount+$tot_emb_sourcing_req_amount_bal+$tot_wash_sourcing_req_amount_bal+$tot_trim_source_amount_bal;
					echo number_format($total_final_amount_bal,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="540" colspan="2"><p> <b>Gross FOB Value [A+B+C+D+E+F+G+H]</b></p></td> 
                    <td width="70"align="right"><b><?  //tot_embro_amount_pcs tot_embro_req_amount
					$gross_fob_value_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_wash_amount+$tot_embro_amount+$tot_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
					$gross_fob_value_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;
					//echo $tot_fab_amount_pcs.'=='.$trim_sew_fin_amt_pcs.'=='.$tot_wash_amount_pcs.'=='.$tot_embro_amount_pcs.'=='.$tot_commission_amount_pcs.'=='.$tot_cm_cost_pcs;
					//$gross_fob_value_req=$tot_fab_req_amount+$trim_fin_req_amount+$tot_wash_req_amount+$tot_embro_req_amount+$tot_commision_req_amount+$tot_other_cost_req_amount+$cm_cost_req;
					
					$gross_fob_value_req=$tot_fab_req_amount+$trim_fin_req_amount+$tot_wash_req_amount+$tot_embro_req_amount+$tot_other_cost_req_amount+$tot_commision_req_amount+$tot_cm_qty_dzn;

					
					 //---fob value
					 $total_fob_gross_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;
	$total_fob_gross_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_wash_amount+$tot_embro_amount+$total_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
					$tot_gross_fob_pcs=$total_fob_gross_dzn/$order_price_per_dzn;
					 $gross_fob_value_dzn_without_commi=$total_fob_gross_dzn-$tot_commission_amount;
					$gross_fob_value_pcs_without_commi=$tot_gross_fob_pcs-$tot_commission_amount_pcs;
					$gross_fob_value_req_without_commi=$gross_fob_value_req-$tot_commision_req_amount;
					
					echo number_format($total_fob_gross_dzn,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? echo number_format($tot_gross_fob_pcs,6); ?>&nbsp;</b></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($gross_fob_value_req,6); ?>&nbsp;</b></td>
                     
                     <td width="70" align="left"><b>Total Final CM Amount</b></td>
                   	<td width="70" align="right" title="Gross FOB Value[A+B+C+D+E+F+G+H]-Total Final Amount"><b><? 
					$total_final_cm_amount=$gross_fob_value_req-$total_final_amount;echo number_format($total_final_cm_amount,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req,6); ?>&nbsp;</b></td>
                      <td width="70" align="right"><p><? //echo number_format($lab_test,6); ?>&nbsp;</p></td>
                   
                    </tr>
                    <?
                   
					?>
                     <tr bgcolor="<? echo $bgcolor2;?>">
                    <td width="540" colspan="2"><b>Net FOB Value (Without Commission)</b></td> 
                    <td width="70"align="right"><b><? echo number_format($gross_fob_value_dzn_without_commi,6); ?> </b></td>
                    <td width="70" align="right"><b><? echo number_format($gross_fob_value_pcs_without_commi,6); ?> </b></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($gross_fob_value_req_without_commi,6); ?> </b></td>
                   
                    <td width="70" align="left"><b>Final CM [Dzn]</b></td>
                   	<td width="70" align="right" title="Total Final CM Amount/PO Qty Dzn"><b><? echo number_format($total_final_cm_amount/$offer_qty_dzn,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req_without_commi,6); ?>&nbsp;</b></td>
                      <td width="70" align="right"><b><? //echo number_format($lab_test,6); ?>&nbsp;</b></td>
                    
                    </tr>
                    
                     <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="540" colspan="2"><b></b></td> 
                    <td width="70"align="right"><b><? //echo number_format($gross_fob_value_dzn_without_commi,6); ?> </b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_pcs_without_commi,6); ?> </b></td>
                    
                    <td width="140" colspan="2" align="right"><b>Total Value</b></td>
                   	<td width="70" title="CM tot Cost+Tot Final Amount" align="right"><b><? echo number_format($tot_cm_qty_dzn+$total_final_amount,6); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req_without_commi,6); ?>&nbsp;</b></td>
                      <td width="70" align="right"><b><? //echo number_format($lab_test,6); ?>&nbsp;</b></td>
                    </tr>
                </table>
             
              
           </div>
          
         
           <? 
		   
		 
		   $cbo_company_name=str_replace("'","",$cbo_company_name);
		   //echo signature_table(219, $cbo_company_name, "1320px"); 
		   if ($cbo_template_id != '') {
			   $template_id = " and template_id=$cbo_template_id ";
		   }
		   $sql = sql_select("select designation,name,user_id,prepared_by from variable_settings_signature where report_id=240 and company_id='$cbo_company_name'  $template_id order by sequence_no");
	   $signature_sql = sql_select("SELECT c.master_tble_id as MASTER_TBLE_ID,c.image_location as IMAGE_LOCATION  from variable_settings_signature a, electronic_approval_setup b, common_photo_library c where a.user_id=b.user_id and a.user_id=c.master_tble_id and a.report_id=240 and a.company_id='$cbo_company_name' and a.template_id=$cbo_template_id and b.page_id=2164 and b.entry_form=47 and b.company_id=$cbo_company_name and c.form_name='user_signature'");
	  
	   $signature_location=array();
	   foreach($signature_sql as $row)
	   {
		   $signature_location[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
	   }
	   if($sql[0][csf("prepared_by")]==1){
		   list($prepared_by,$activities)=explode('**',$prepared_by);
		   $sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		   $sql=$sql_2+$sql;
	   }
	   $count = count($sql);
	   $td_width = floor(1000 / $count);
	   $standard_width = $count * 150;
	   if ($standard_width > 1000) {
		   $td_width = 150;
	   }
	   $i = 1;
	   if ($count == 0) { echo "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	   else
	   {
		   echo '<table id="signatureTblId" width="1000" style="padding-top:50px;"><tr><td width="100%" height="50" colspan="' . $count . '">' . $message . '</td></tr><tr>';
		   foreach ($sql as $row) {
			   echo '<td width="' . $td_width . '" align="center" valign="bottom">';
			   if($signature_location[$row[csf("user_id")]]!='')
			   {
				   echo '<strong><img src="../../'.$signature_location[$row[csf("user_id")]].'" height="60" width="90" ></strong><br>';
			   }
			   else
			   {
				   echo '<span style="height:60px;width:90px;"></span><br>';
			   }
			   echo '<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
			   $i++;
		   }
		   echo '</tr></table>';
	   }
	   
		   


		//    $cbo_company_name=str_replace("'","",$cbo_company_name);
		//    echo signature_table(219, $cbo_company_name, "1320px"); ?>
            
     </div>
    
      <div style="clear:both"></div>
     
    <?
	$mailBody=ob_get_contents();
	ob_clean();
	echo $mailBody;
	
	//Mail send------------------------------------------
	list($msil_address,$is_mail_send)=explode('**',$mail_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $mailBody); 
			
		$mailToArr=array();
		if($msil_address){$mailToArr[]=$msil_address;}
			
		//$mailSql = "select c.TEAM_MEMBER_EMAIL,d.USER_EMAIL from wo_po_details_master a, wo_pre_cost_mst b,lib_mkt_team_member_info c,USER_PASSWD d where a.job_no=b.job_no and a.DEALING_MARCHANT=c.id and b.INSERTED_BY=d.id  and a.status_active=1 and a.job_no=$txt_job_no";
		
		$mailSql = "SELECT c.TEAM_LEADER_EMAIL, d.USER_EMAIL
  FROM wo_po_details_master  a,  wo_pre_cost_mst b, lib_marketing_team c, USER_PASSWD d
 WHERE a.job_no = b.job_no  AND a.TEAM_LEADER = c.id AND b.INSERTED_BY = d.id AND a.status_active = 1  AND a.job_no=$txt_job_no";
		
		
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[TEAM_LEADER_EMAIL]){$mailToArr[]=$rows[TEAM_LEADER_EMAIL];}
			if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
		}
		
		
		
		
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.page_id=1901 and a.entry_form=47 and a.company_id=$cbo_company_name order by a.SEQUENCE_NO";
		//echo $elcetronicSql;die;
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
			if($rows[BUYER_ID]!=''){
				 
				foreach(explode(',',$rows[BUYER_ID]) as $bi){
					if($rows[USER_EMAIL]!='' && $rows[BYPASS]==2 && $bi==$buyer_name_id){
						$mailToArr[100]=$rows[USER_EMAIL];break;
					}
				}
			}
			else{
			
				if($rows[SEQUENCE_NO]==1 && $rows[BYPASS]==2){
					if($rows[USER_EMAIL]){$mailToArr[100]=$rows[USER_EMAIL];}
				}
			}
			
			$elecDataArr[$rows[BYPASS]][]=$rows[USER_EMAIL];
		}
		
		if($elecDataArr[1][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[1][0];}
		elseif($elecDataArr[2][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[2][0];}
		
		$to=implode(',',$mailToArr);
		
		//echo $to;die;
		
		//Att file....
	/*		$imgSql="select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0  and MASTER_TBLE_ID=$txt_job_no and file_type=1";
		$imgSqlResult=sql_select($imgSql);
		foreach($imgSqlResult as $rows){
			$att_file_arr[]='../../../'.$rows[IMAGE_LOCATION].'**'.$rows[REAL_FILE_NAME];
		}
	*/		
		$subject="Sourcing Post Cost Report";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
	}
	
	//------------------------------------End;
	
	
	exit();
}

if ($action=='approve_no_popup')
{
	echo load_html_head_contents("Approve Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	 $sql="SELECT approval_no, approval_cause from fabric_booking_app_cause_source where booking_id=$job_id and entry_form=47 and approval_type=2 order by approval_no";
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
?>