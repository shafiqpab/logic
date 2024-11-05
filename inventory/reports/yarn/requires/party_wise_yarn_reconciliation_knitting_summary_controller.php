<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=34 and is_deleted=0 and status_active=1");
	if($print_report_format=='') $print_report_format=0;else $print_report_format=$print_report_format;
	echo "document.getElementById('hidden_report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}

//for party_popup
if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
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
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str )
		{
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
    <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
    <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?
	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
   exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		
	$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
	$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	$type=str_replace("'","",$type);
	
	//for knitting party condition
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	if ($txt_knitting_com_id=="")
		$knitting_company_cond_1="";
	else
		$knitting_company_cond_1=" AND a.knit_dye_company in ($txt_knitting_com_id)";
		
	//for knitting source condition
	if (str_replace("'","",$cbo_knitting_source)==0)
	{
		$knitting_source_cond="";
		$knitting_source_rec_cond="";
	}
	else
	{
		$knitting_source_cond=" AND a.knit_dye_source=$cbo_knitting_source";
		$knitting_source_rec_cond=" AND a.knitting_source=$cbo_knitting_source";
	}
	ob_start();
	
	//for summary button
	if($type==1)
	{
		?>
		<fieldset style="width:1270px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th width="100">Balance</th>
					<th width="100">Process Loss</th>
					<th>After Process Loss Balance</th>
				</thead>
			</table>
			<div style="width:1260px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1240" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" AND a.supplier_id in (".$txt_knitting_com_id.")";
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_req="SELECT c.KNIT_ID, c.REQUISITION_NO, d.REF_CLOSING_STATUS FROM ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d WHERE c.knit_id = d.id AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";
				$sql_req_rslt = sql_select($sql_req);
				$prog_arr = array();
				$req_arr = array();
				$req_data_arr = array();
				$prog_data_arr = array();
				$ref_closing_arr = array();
				foreach($sql_req_rslt as $row)
				{
					$prog_arr[$row['KNIT_ID']] = $row['KNIT_ID'];
					$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
					
					$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
					$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
					
					$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
					$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
				}
				
				$sql_iss="SELECT a.ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, b.ID AS TRANS_ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.REQUISITION_NO FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." AND a.issue_basis = 3".$knitting_company_cond_1.$knitting_source_cond;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$popupIssueIdArr = array();
				$duplicate_check = array();
				foreach($sql_iss_res as $rowIss)
				{
					if(in_array($rowIss['REQUISITION_NO'], $req_arr))
					{
						if($duplicate_check[$rowIss['TRANS_ID']] != $rowIss['TRANS_ID'])
						{
							$duplicate_check[$rowIss['TRANS_ID']] = $rowIss['TRANS_ID'];
							$trns_date='';
							$date_frm='';
							$date_to='';
							$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
			
							if($trns_date<$date_frm)
							{
								$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
							}
							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								$popupIssueIdArr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
								
								//for ref closing
								if($ref_closing_arr['req'][$rowIss['REQUISITION_NO']] == 1)
								{
									$issueIdArrRef[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_qty'] += $rowIss['CONS_QUANTITY'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_reject_qty'] += $rowIss['RETURN_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data); die;
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED = 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$zs_receiveIdArr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($zs_receiveIdArr);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED != 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for issue return and ref closing issue return
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form = 9 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$fabRcvIdArr = array();
				$popupYarnReturnIdArr = array();
				$popupYarnReturnRefIdArr = array();
				$requisitionNoArr = array();
				//$zs_receiveIdArr = array();
				foreach($sql_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $req_arr))
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						$item_category = $row['ITEM_CATEGORY'];
						if($trns_date < $date_frm)
						{
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY']+$row['RETURN_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
						}
		
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
							
							/*
							|--------------------------------------------------------------------------
							| for Yarn Issue Return
							| if receive_basis = 3(Requisition) and
							| entry_form = 9(Yarn Issue Return) and
							| item_category = 1(Yarn) then
							| tbl inv_receive_master booking_id/booking_no = requisition_no
							|--------------------------------------------------------------------------
							*/
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$popupYarnReturnIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
								$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
	
								//for ref closing
								if($ref_closing_arr['req'][$row['BOOKING_NO']] == 1)
								{
									$popupYarnReturnRefIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_return_qty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				/*echo "<pre>";
				print_r($refCloseDataArr);
				echo "</pre>";*/
				
				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
				foreach($party_data as $party_id=>$party_datas)
				{
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
				}
				oci_commit($con);
				
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store information
				|--------------------------------------------------------------------------
				*/
				/*$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
				FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1)";
				// AND c.party_id = ".$party_id."
				//echo $sql_gds;
				$sql_gds_rslt = sql_select($sql_gds);
				$gpe_info = array();
				foreach($sql_gds_rslt as $row)
				{
					$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
				}*/
				
				$sql_gds_1 = "SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_gds_1_rslt = sql_select($sql_gds_1);
				$gds_1_arr = array();
				foreach($sql_gds_1_rslt as $row)
				{
					$gds_1_arr[$row['ID']] = $row['ID'];
				}
				
				$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
				FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";			
				$sql_gds_rslt = sql_select($sql_gds);
				$gpe_info = array();
				foreach($sql_gds_rslt as $row)
				{
					if($gds_1_arr[$row['ID']])
					{
						$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
					}
				}				
				//echo "<pre>";
				//print_r($gpe_info);
				//echo "</pre>";
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive_1 = "SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_receive_1_rslt=sql_select($sql_receive_1);
				$receive_1_arr = array();
				foreach($sql_receive_1_rslt as $row)
				{
					$receive_1_arr[$row['ID']] = $row['ID'];
				}
				
				//$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				
				$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$duplicate_check = array();
				foreach($sql_receive_rslt as $row)
				{
					if($receive_1_arr[$row['BOOKING_ID']])
					{
						/*
						|--------------------------------------------------------------------------
						| for Knit Grey Fabric Receive
						| if receive_basis = 9(Production) and
						| entry_form = 22(Knit Grey Fabric Receive) and
						| item_category = 13(Grey Fabric) then
						|--------------------------------------------------------------------------
						*/
						if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
						{
							if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
							{
								$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
								$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
								$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
								
								if(in_array($row['REQUISITION_NO'], $req_arr))
								{
									$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
									$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
	
									//for ref closing
									if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
									{
										$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
										$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
									}
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				//echo "<pre>";
				//print_r($delivery_basis_receive_id);
				//echo "</pre>";
			
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store roll information
				|--------------------------------------------------------------------------
				*/
				$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				// AND c.party_id = $party_id
				//echo $sql_prog;
				$sql_prog_rslt = sql_select($sql_prog);
				$prog_data = array();
				foreach($sql_prog_rslt as $row)
				{
					$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
				}
				
				$sql_rcv_zs_1 = " SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_rcv_zs_1_rslt = sql_select($sql_rcv_zs_1);
				$rcv_zs_1_arr = array();
				foreach($sql_rcv_zs_1_rslt as $row)
				{
					$rcv_zs_1_arr[$row['ID']] = $row['ID'];
				}
				
				//$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				
				$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_rcv_zs;
				$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
				$duplicate_check = array();
				foreach($sql_rcv_zs_rslt as $row)
				{
					if($rcv_zs_1_arr[$row['BOOKING_ID']])
					{
						if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
						{
							$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
							$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
							$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];			
					
							if(in_array($row['REQUISITION_NO'], $req_arr))
							{
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
								
								if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
								{
									$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_rcv_zs_rslt);				

				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					//$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-$party_opening_arr[$party_id]['recOpening'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for reference close
					$process_loss = $refCloseDataArr[$party_id]['issue_qty']-($refCloseDataArr[$party_id]['issue_return_qty']+$refCloseDataArr[$party_id]['grey_receive_qnty']+$refCloseDataArr[$party_id]['reject_fabric_receive']);
					$balance_after_process_loss = $balance-$process_loss;
					//echo $refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive'];
					
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popupIssueIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popupYarnReturnIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}
					
					//for issue ref id
					foreach($issueIdArrRef[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '4', '".$party_id."')");
					}

					//for issue return ref id
					foreach($popupYarnReturnRefIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '5', '".$party_id."')");
					}
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_issue_qty('<?php echo $party_id; ?>')"><? echo number_format($yarn_issue,2); ?><a/></td>                                
						<td width="100" align="right"><a href="#" onClick="func_onclick_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($grey_receive_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($reject_fabric_receive,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_reject_qnty,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($balance,2); ?></td> 
						<td width="100" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><a href="#" onClick="func_onclick_process_loss('<?php echo $party_id; ?>')"><? echo number_format($process_loss,2); ?><a/></td>
						<td align="right"><a href="#" onClick="func_onclick_balance_after_process_loss('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance_after_process_loss,2); ?><a/></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_process_loss,2); ?></td>
					<td style="padding-right:18px;" align="right"><? echo number_format($tot_balance_after_process_loss,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
	//for sample summary button
	else if($type==2)
	{
		?>
		<fieldset style="width:1070px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1060px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" and a.supplier_id in (".$txt_knitting_com_id.")";
					
					/*
					$sql_yrec="select a.id, a.supplier_id, a.receive_date, a.ref_closing_status, sum(b.cons_quantity) as cons_quantity
					from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=".$cbo_company_name." and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond_2 group by a.id, a.supplier_id, a.receive_date, a.ref_closing_status"; //$knitting_source_rec_cond
					//echo $sql_yrec; die;
					$sql_yrec_res=sql_select($sql_yrec);
					foreach($sql_yrec_res as $rowyRec)
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						
						if($trns_date<$date_frm)
						{
							$party_opening_arr[$rowyRec[csf('supplier_id')]]['yrOpening']+=$rowyRec[csf('cons_quantity')];
						}
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							$party_data[$rowyRec[csf('supplier_id')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
							$yarnRcvIdArr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('id')]] =$rowyRec[csf('id')];
						}
					}
					unset($sql_yrec_res);
					*/
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_iss="SELECT a.id AS ID, a.knit_dye_company AS KNIT_DYE_COMPANY, a.issue_date AS ISSUE_DATE, b.cons_quantity AS CONS_QUANTITY, b.return_qnty AS RETURN_QNTY FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." $knitting_company_cond_1 $knitting_source_cond";
				//echo $sql_iss; die;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$opening_balance_issue_id_arr=array();
				$issue_id_arr=array();
				$popup_issue_id_arr = array();
				foreach($sql_iss_res as $rowIss)
				{
					$trns_date='';
					$date_frm='';
					$date_to='';
					$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));
	
					if($trns_date<$date_frm)
					{
						$opening_balance_issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
					}
					
					if($trns_date>=$date_frm && $trns_date<=$date_to)
					{
						$issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$popup_issue_id_arr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data);
				//echo "</pre>";
				//die;
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED FROM inv_receive_master a WHERE a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM IN(2) AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive;
				$sql_receive_rslt=sql_select($sql_receive);
				$receive_id_arr = array();
				foreach($sql_receive_rslt as $row)
				{
					if($row['ROLL_MAINTAINED'] ==1)
					{
						$receive_id_arr['roll_rcv'][$row['ID']] = $row['ID'];
					}
					else
					{
						$receive_id_arr['bulk_rcv'][$row['ID']] = $row['ID'];
					}
				}
				/*echo "<pre>";
				print_r($receive_id_arr);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['roll_rcv'], '0', 'b.mst_id');
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;


								$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_QNTY'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($party_datas);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['bulk_rcv'], '0', 'b.mst_id');
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['GREY_RECEIVE_QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_FABRIC_RECEIVE'];
							}
						}						
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for yarn issue return
				|--------------------------------------------------------------------------
				*/
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($opening_balance_issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date < $date_frm)
						{
							$opening_balance_rec = 0;
							$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY'];
							$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$popup_issue_return_id_arr = array();
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$duplicate_check[$row['ID']] = $row['ID'];
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$popup_issue_return_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
							$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/

				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					//echo $party_opening_arr[$party_id]['issOpening'].'='.$party_opening_arr[$party_id]['recOpening'].'='.$party_opening_arr[$party_id]['recOpenings'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for receive id
					foreach($popup_receive_id_arr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popup_issue_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popup_issue_return_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}

					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_issue,2); ?></td>                                
						<td width="100" align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
						<td align="right"><a href="#" onClick="func_onclick_sample_balance('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance,2); ?></a></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td align="right" style="padding-right:18px;"><? echo number_format($tot_balance,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
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

if($action=="report_generate_16022022")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		
	$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
	$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	$type=str_replace("'","",$type);
	
	//for knitting party condition
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	if ($txt_knitting_com_id=="")
		$knitting_company_cond_1="";
	else
		$knitting_company_cond_1=" AND a.knit_dye_company in ($txt_knitting_com_id)";
		
	//for knitting source condition
	if (str_replace("'","",$cbo_knitting_source)==0)
	{
		$knitting_source_cond="";
		$knitting_source_rec_cond="";
	}
	else
	{
		$knitting_source_cond=" AND a.knit_dye_source=$cbo_knitting_source";
		$knitting_source_rec_cond=" AND a.knitting_source=$cbo_knitting_source";
	}
	ob_start();
	
	//for summary button
	if($type==1)
	{
		?>
		<fieldset style="width:1270px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th width="100">Balance</th>
					<th width="100">Process Loss</th>
					<th>After Process Loss Balance</th>
				</thead>
			</table>
			<div style="width:1260px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1240" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" AND a.supplier_id in (".$txt_knitting_com_id.")";
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_req="SELECT c.KNIT_ID, c.REQUISITION_NO, d.REF_CLOSING_STATUS FROM ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d WHERE c.knit_id = d.id AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";
				$sql_req_rslt = sql_select($sql_req);
				$prog_arr = array();
				$req_arr = array();
				$req_data_arr = array();
				$prog_data_arr = array();
				$ref_closing_arr = array();
				foreach($sql_req_rslt as $row)
				{
					$prog_arr[$row['KNIT_ID']] = $row['KNIT_ID'];
					$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
					
					$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
					$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
					
					$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
					$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
				}
				
				$sql_iss="SELECT a.ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, b.ID AS TRANS_ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.REQUISITION_NO FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." AND a.issue_basis = 3".$knitting_company_cond_1.$knitting_source_cond;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$popupIssueIdArr = array();
				$duplicate_check = array();
				foreach($sql_iss_res as $rowIss)
				{
					if(in_array($rowIss['REQUISITION_NO'], $req_arr))
					{
						if($duplicate_check[$rowIss['TRANS_ID']] != $rowIss['TRANS_ID'])
						{
							$duplicate_check[$rowIss['TRANS_ID']] = $rowIss['TRANS_ID'];
							$trns_date='';
							$date_frm='';
							$date_to='';
							$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
			
							if($trns_date<$date_frm)
							{
								$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
							}
							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								$popupIssueIdArr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
								
								//for ref closing
								if($ref_closing_arr['req'][$rowIss['REQUISITION_NO']] == 1)
								{
									$issueIdArrRef[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_qty'] += $rowIss['CONS_QUANTITY'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_reject_qty'] += $rowIss['RETURN_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data); die;
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED = 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$zs_receiveIdArr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($party_datas);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED != 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for issue return and ref closing issue return
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form = 9 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$fabRcvIdArr = array();
				$popupYarnReturnIdArr = array();
				$popupYarnReturnRefIdArr = array();
				$requisitionNoArr = array();
				//$zs_receiveIdArr = array();
				foreach($sql_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $req_arr))
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						$item_category = $row['ITEM_CATEGORY'];
						if($trns_date < $date_frm)
						{
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY']+$row['RETURN_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
						}
		
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
							
							/*
							|--------------------------------------------------------------------------
							| for Yarn Issue Return
							| if receive_basis = 3(Requisition) and
							| entry_form = 9(Yarn Issue Return) and
							| item_category = 1(Yarn) then
							| tbl inv_receive_master booking_id/booking_no = requisition_no
							|--------------------------------------------------------------------------
							*/
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$popupYarnReturnIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
								$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
	
								//for ref closing
								if($ref_closing_arr['req'][$row['BOOKING_NO']] == 1)
								{
									$popupYarnReturnRefIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_return_qty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				/*echo "<pre>";
				print_r($refCloseDataArr);
				echo "</pre>";*/
				
				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
				foreach($party_data as $party_id=>$party_datas)
				{
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
				}
				oci_commit($con);
				
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store information
				|--------------------------------------------------------------------------
				*/
				/*$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
				FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1)";
				// AND c.party_id = ".$party_id."
				//echo $sql_gds;
				$sql_gds_rslt = sql_select($sql_gds);
				$gpe_info = array();
				foreach($sql_gds_rslt as $row)
				{
					$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
				}*/
				
				$sql_gds_1 = "SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_gds_1_rslt = sql_select($sql_gds_1);
				$gds_1_arr = array();
				foreach($sql_gds_1_rslt as $row)
				{
					$gds_1_arr[$row['ID']] = $row['ID'];
				}
				
				$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
				FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";			
				$sql_gds_rslt = sql_select($sql_gds);
				$gpe_info = array();
				foreach($sql_gds_rslt as $row)
				{
					if($gds_1_arr[$row['ID']])
					{
						$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
					}
				}				
				//echo "<pre>";
				//print_r($gpe_info);
				//echo "</pre>";
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive_1 = "SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_receive_1_rslt=sql_select($sql_receive_1);
				$receive_1_arr = array();
				foreach($sql_receive_1_rslt as $row)
				{
					$receive_1_arr[$row['ID']] = $row['ID'];
				}
				
				//$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				
				$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$duplicate_check = array();
				foreach($sql_receive_rslt as $row)
				{
					if($receive_1_arr[$row['BOOKING_ID']])
					{
						/*
						|--------------------------------------------------------------------------
						| for Knit Grey Fabric Receive
						| if receive_basis = 9(Production) and
						| entry_form = 22(Knit Grey Fabric Receive) and
						| item_category = 13(Grey Fabric) then
						|--------------------------------------------------------------------------
						*/
						if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
						{
							if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
							{
								$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
								$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
								$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
								
								if(in_array($row['REQUISITION_NO'], $req_arr))
								{
									$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
									$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
	
									//for ref closing
									if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
									{
										$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
										$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
									}
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				//echo "<pre>";
				//print_r($delivery_basis_receive_id);
				//echo "</pre>";
			
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store roll information
				|--------------------------------------------------------------------------
				*/
				$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				// AND c.party_id = $party_id
				//echo $sql_prog;
				$sql_prog_rslt = sql_select($sql_prog);
				$prog_data = array();
				foreach($sql_prog_rslt as $row)
				{
					$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
				}
				
				$sql_rcv_zs_1 = " SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				$sql_rcv_zs_1_rslt = sql_select($sql_rcv_zs_1);
				$rcv_zs_1_arr = array();
				foreach($sql_rcv_zs_1_rslt as $row)
				{
					$rcv_zs_1_arr[$row['ID']] = $row['ID'];
				}
				
				//$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				
				$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_rcv_zs;
				$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
				$duplicate_check = array();
				foreach($sql_rcv_zs_rslt as $row)
				{
					if($rcv_zs_1_arr[$row['BOOKING_ID']])
					{
						if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
						{
							$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
							$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
							$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];			
					
							if(in_array($row['REQUISITION_NO'], $req_arr))
							{
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
								
								if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
								{
									$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_rcv_zs_rslt);				

				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					//$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-$party_opening_arr[$party_id]['recOpening'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for reference close
					$process_loss = $refCloseDataArr[$party_id]['issue_qty']-($refCloseDataArr[$party_id]['issue_return_qty']+$refCloseDataArr[$party_id]['grey_receive_qnty']+$refCloseDataArr[$party_id]['reject_fabric_receive']);
					$balance_after_process_loss = $balance-$process_loss;
					//echo $refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive'];
					
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popupIssueIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popupYarnReturnIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}
					
					//for issue ref id
					foreach($issueIdArrRef[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '4', '".$party_id."')");
					}

					//for issue return ref id
					foreach($popupYarnReturnRefIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '5', '".$party_id."')");
					}

					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_issue_qty('<?php echo $party_id; ?>')"><? echo number_format($yarn_issue,2); ?><a/></td>                                
						<td width="100" align="right"><a href="#" onClick="func_onclick_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($grey_receive_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($reject_fabric_receive,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_reject_qnty,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($balance,2); ?></td> 
						<td width="100" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><a href="#" onClick="func_onclick_process_loss('<?php echo $party_id; ?>')"><? echo number_format($process_loss,2); ?><a/></td>
						<td align="right"><a href="#" onClick="func_onclick_balance_after_process_loss('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance_after_process_loss,2); ?><a/></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_process_loss,2); ?></td>
					<td style="padding-right:18px;" align="right"><? echo number_format($tot_balance_after_process_loss,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
	//for sample summary button
	else if($type==2)
	{
		?>
		<fieldset style="width:1070px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1060px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" and a.supplier_id in (".$txt_knitting_com_id.")";
					
					/*
					$sql_yrec="select a.id, a.supplier_id, a.receive_date, a.ref_closing_status, sum(b.cons_quantity) as cons_quantity
					from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=".$cbo_company_name." and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond_2 group by a.id, a.supplier_id, a.receive_date, a.ref_closing_status"; //$knitting_source_rec_cond
					//echo $sql_yrec; die;
					$sql_yrec_res=sql_select($sql_yrec);
					foreach($sql_yrec_res as $rowyRec)
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						
						if($trns_date<$date_frm)
						{
							$party_opening_arr[$rowyRec[csf('supplier_id')]]['yrOpening']+=$rowyRec[csf('cons_quantity')];
						}
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							$party_data[$rowyRec[csf('supplier_id')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
							$yarnRcvIdArr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('id')]] =$rowyRec[csf('id')];
						}
					}
					unset($sql_yrec_res);
					*/
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_iss="SELECT a.id AS ID, a.knit_dye_company AS KNIT_DYE_COMPANY, a.issue_date AS ISSUE_DATE, b.cons_quantity AS CONS_QUANTITY, b.return_qnty AS RETURN_QNTY FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." $knitting_company_cond_1 $knitting_source_cond";
				//echo $sql_iss; die;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$opening_balance_issue_id_arr=array();
				$issue_id_arr=array();
				$popup_issue_id_arr = array();
				foreach($sql_iss_res as $rowIss)
				{
					$trns_date='';
					$date_frm='';
					$date_to='';
					$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));
	
					if($trns_date<$date_frm)
					{
						$opening_balance_issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
					}
					
					if($trns_date>=$date_frm && $trns_date<=$date_to)
					{
						$issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$popup_issue_id_arr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data);
				//echo "</pre>";
				//die;
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED FROM inv_receive_master a WHERE a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM IN(2) AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive;
				$sql_receive_rslt=sql_select($sql_receive);
				$receive_id_arr = array();
				foreach($sql_receive_rslt as $row)
				{
					if($row['ROLL_MAINTAINED'] ==1)
					{
						$receive_id_arr['roll_rcv'][$row['ID']] = $row['ID'];
					}
					else
					{
						$receive_id_arr['bulk_rcv'][$row['ID']] = $row['ID'];
					}
				}
				/*echo "<pre>";
				print_r($receive_id_arr);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['roll_rcv'], '0', 'b.mst_id');
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_QNTY'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($party_datas);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['bulk_rcv'], '0', 'b.mst_id');
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['GREY_RECEIVE_QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_FABRIC_RECEIVE'];
							}
						}						
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for yarn issue return
				|--------------------------------------------------------------------------
				*/
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($opening_balance_issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date < $date_frm)
						{
							$opening_balance_rec = 0;
							$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY'];
							$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$popup_issue_return_id_arr = array();
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$duplicate_check[$row['ID']] = $row['ID'];
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$popup_issue_return_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
							$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/

				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					//echo $party_opening_arr[$party_id]['issOpening'].'='.$party_opening_arr[$party_id]['recOpening'].'='.$party_opening_arr[$party_id]['recOpenings'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for receive id
					foreach($popup_receive_id_arr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popup_issue_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popup_issue_return_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}

					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_issue,2); ?></td>                                
						<td width="100" align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
						<td align="right"><a href="#" onClick="func_onclick_sample_balance('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance,2); ?></a></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td align="right" style="padding-right:18px;"><? echo number_format($tot_balance,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
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

if($action=="report_generate_29012022")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		
	$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
	$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	$type=str_replace("'","",$type);
	
	//for knitting party condition
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	if ($txt_knitting_com_id=="")
		$knitting_company_cond_1="";
	else
		$knitting_company_cond_1=" AND a.knit_dye_company in ($txt_knitting_com_id)";
		
	//for knitting source condition
	if (str_replace("'","",$cbo_knitting_source)==0)
	{
		$knitting_source_cond="";
		$knitting_source_rec_cond="";
	}
	else
	{
		$knitting_source_cond=" AND a.knit_dye_source=$cbo_knitting_source";
		$knitting_source_rec_cond=" AND a.knitting_source=$cbo_knitting_source";
	}
	ob_start();
	
	//for summary button
	if($type==1)
	{
		?>
		<fieldset style="width:1270px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th width="100">Balance</th>
					<th width="100">Process Loss</th>
					<th>After Process Loss Balance</th>
				</thead>
			</table>
			<div style="width:1260px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1240" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" AND a.supplier_id in (".$txt_knitting_com_id.")";
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_req="SELECT c.KNIT_ID, c.REQUISITION_NO, d.REF_CLOSING_STATUS FROM ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d WHERE c.knit_id = d.id AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";
				$sql_req_rslt = sql_select($sql_req);
				$prog_arr = array();
				$req_arr = array();
				$req_data_arr = array();
				$prog_data_arr = array();
				$ref_closing_arr = array();
				foreach($sql_req_rslt as $row)
				{
					$prog_arr[$row['KNIT_ID']] = $row['KNIT_ID'];
					$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
					
					$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
					$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
					
					$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
					$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
				}

				$sql_iss="SELECT a.ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, b.ID AS TRANS_ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.REQUISITION_NO FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." AND a.issue_basis = 3".$knitting_company_cond_1.$knitting_source_cond;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$popupIssueIdArr = array();
				$duplicate_check = array();
				foreach($sql_iss_res as $rowIss)
				{
					if(in_array($rowIss['REQUISITION_NO'], $req_arr))
					{
						if($duplicate_check[$rowIss['TRANS_ID']] != $rowIss['TRANS_ID'])
						{
							$duplicate_check[$rowIss['TRANS_ID']] = $rowIss['TRANS_ID'];
							$trns_date='';
							$date_frm='';
							$date_to='';
							$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
							$date_frm=date('Y-m-d',strtotime($from_date));
							$date_to=date('Y-m-d',strtotime($to_date));
			
							if($trns_date<$date_frm)
							{
								$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
							}
							if($trns_date>=$date_frm && $trns_date<=$date_to)
							{
								$popupIssueIdArr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
								$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
								
								//for ref closing
								if($ref_closing_arr['req'][$rowIss['REQUISITION_NO']] == 1)
								{
									$issueIdArrRef[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_qty'] += $rowIss['CONS_QUANTITY'];
									$refCloseDataArr[$rowIss['KNIT_DYE_COMPANY']]['issue_reject_qty'] += $rowIss['RETURN_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data); die;
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED = 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$zs_receiveIdArr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($party_datas);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.REF_CLOSING_STATUS, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 2 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.ROLL_MAINTAINED != 1 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".$knit_source_cond.$party_cond_1.$issue_challan_cond;
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $prog_arr))
					{
						if($duplicate_check[$row['ID']] != $row['ID'])
						{
							$duplicate_check[$row['ID']] = $row['ID'];
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								if(in_array($row['BOOKING_NO'], $prog_arr))
								{
									$opening_balance_rec = 0;
									$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
									$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
								}
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							}
						}
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/				
				
				/*
				|--------------------------------------------------------------------------
				| for issue return and ref closing issue return
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form = 9 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$fabRcvIdArr = array();
				$popupYarnReturnIdArr = array();
				$popupYarnReturnRefIdArr = array();
				$requisitionNoArr = array();
				//$zs_receiveIdArr = array();
				foreach($sql_receive_rslt as $row)
				{
					if(in_array($row['BOOKING_NO'], $req_arr))
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						$item_category = $row['ITEM_CATEGORY'];
						if($trns_date < $date_frm)
						{
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY']+$row['RETURN_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
						}
		
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$zs_receiveIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
							
							/*
							|--------------------------------------------------------------------------
							| for Yarn Issue Return
							| if receive_basis = 3(Requisition) and
							| entry_form = 9(Yarn Issue Return) and
							| item_category = 1(Yarn) then
							| tbl inv_receive_master booking_id/booking_no = requisition_no
							|--------------------------------------------------------------------------
							*/
							if($row['ENTRY_FORM'] == 9 && $row['RECEIVE_BASIS'] == 3 && $row['ITEM_CATEGORY'] == 1)
							{
								$popupYarnReturnIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
								$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
	
								//for ref closing
								if($ref_closing_arr['req'][$row['BOOKING_NO']] == 1)
								{
									$popupYarnReturnRefIdArr[$row['KNITTING_COMPANY']][$row['ID']] = $row['ID'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_return_qty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				/*echo "<pre>";
				print_r($refCloseDataArr);
				echo "</pre>";*/
				
				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
				foreach($party_data as $party_id=>$party_datas)
				{
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
				}
				oci_commit($con);
				
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store information
				|--------------------------------------------------------------------------
				*/
				$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
				FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1)";
				// AND c.party_id = ".$party_id."
				//echo $sql_gds;
				$sql_gds_rslt = sql_select($sql_gds);
				$gpe_info = array();
				foreach($sql_gds_rslt as $row)
				{
					$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
				}
				//echo "<pre>";
				//print_r($gpe_info);
				//echo "</pre>";
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_receive; die;
				$sql_receive_rslt=sql_select($sql_receive);
				$duplicate_check = array();
				foreach($sql_receive_rslt as $row)
				{
					/*
					|--------------------------------------------------------------------------
					| for Knit Grey Fabric Receive
					| if receive_basis = 9(Production) and
					| entry_form = 22(Knit Grey Fabric Receive) and
					| item_category = 13(Grey Fabric) then
					|--------------------------------------------------------------------------
					*/
					if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
					{
						if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
						{
							$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
							$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
							$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
							
							if(in_array($row['REQUISITION_NO'], $req_arr))
							{
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];

								//for ref closing
								if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
								{
									$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
									$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
								}
							}
						}
					}
				}
				unset($sql_rec_res);
				//echo "<pre>";
				//print_r($delivery_basis_receive_id);
				//echo "</pre>";
			
				/*
				|--------------------------------------------------------------------------
				| for grey delivery to store roll information
				|--------------------------------------------------------------------------
				*/
				$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1";
				// AND c.party_id = $party_id
				//echo $sql_prog;
				$sql_prog_rslt = sql_select($sql_prog);
				$prog_data = array();
				foreach($sql_prog_rslt as $row)
				{
					$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
				}
				
				$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1) ORDER BY a.knitting_company, a.receive_date, a.recv_number";
				// AND c.party_id = ".$party_id."
				//echo $sql_rcv_zs;
				$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
				$duplicate_check = array();
				foreach($sql_rcv_zs_rslt as $row)
				{
					if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
					{
						$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
						$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
						$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];			
				
						if(in_array($row['REQUISITION_NO'], $req_arr))
						{
							$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['CONS_QUANTITY'];
							$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
							
							if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
							{
								$refCloseDataArr[$row['KNITTING_COMPANY']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
								$refCloseDataArr[$row['KNITTING_COMPANY']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
							}
						}
					}
				}
				unset($sql_rcv_zs_rslt);				

				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					//$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-$party_opening_arr[$party_id]['recOpening'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for reference close
					$process_loss = $refCloseDataArr[$party_id]['issue_qty']-($refCloseDataArr[$party_id]['issue_return_qty']+$refCloseDataArr[$party_id]['grey_receive_qnty']+$refCloseDataArr[$party_id]['reject_fabric_receive']);
					$balance_after_process_loss = $balance-$process_loss;
					//echo $refCloseDataArr[$party_id]['issue_qty'].'='.$refCloseDataArr[$party_id]['issue_return_qty'].'='.$refCloseDataArr[$party_id]['grey_receive_qnty'].'='.$refCloseDataArr[$party_id]['reject_fabric_receive'];
					
					//for receive id
					foreach($zs_receiveIdArr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popupIssueIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popupYarnReturnIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}
					
					//for issue ref id
					foreach($issueIdArrRef[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '4', '".$party_id."')");
					}

					//for issue return ref id
					foreach($popupYarnReturnRefIdArr[$party_id] as $key=>$val)
					{
						$tmp_issue_ref_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '5', '".$party_id."')");
					}

					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_issue_qty('<?php echo $party_id; ?>')"><? echo number_format($yarn_issue,2); ?><a/></td>                                
						<td width="100" align="right"><a href="#" onClick="func_onclick_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($grey_receive_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_fabric_receive('<?php echo $party_id; ?>')"><? echo number_format($reject_fabric_receive,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_qnty,2); ?><a/></td>
						<td width="100" align="right"><a href="#" onClick="func_onclick_reject_yarn_return('<?php echo $party_id; ?>')"><? echo number_format($yarn_return_reject_qnty,2); ?><a/></td>
						<td width="100" align="right"><? echo number_format($balance,2); ?></td> 
						<td width="100" align="right" title="Process Loss Qty Formula: [Yarn Issue qty-(Issue Return Qnty+Knitting Qnty+Reject Fabric Qnty)]"><a href="#" onClick="func_onclick_process_loss('<?php echo $party_id; ?>')"><? echo number_format($process_loss,2); ?><a/></td>
						<td align="right"><a href="#" onClick="func_onclick_balance_after_process_loss('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance_after_process_loss,2); ?><a/></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_process_loss,2); ?></td>
					<td style="padding-right:18px;" align="right"><? echo number_format($tot_balance_after_process_loss,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
	//for sample summary button
	else if($type==2)
	{
		?>
		<fieldset style="width:1070px">
			<table width="1260" cellpadding="0" cellspacing="0" id="caption">
				<tr>
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?> (Summary)</strong></td>
				</tr>  
				<tr> 
				   <td align="center" width="100%" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<br />
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="150">Party Name</th>
					<th width="60">UOM</th>
					<th width="100" title="Iss.-Rec.">Opening Balance</th>
					<th width="100">Yarn Issued</th>
					<th width="100">Fabric Received</th>
					<th width="100">Reject Fabric Received</th>
					<th width="100">DY/TW/ WX/RCon Rec.</th>
					<th width="100">Yarn Returned</th>
					<th width="100">Reject Yarn Returned</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1060px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1040" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
				<?
				$party_data=array();
				$party_opening_arr=array();
				$yarnRcvIdArr = array();
				
				//for Out-bound Subcontract
				if (str_replace("'","",$cbo_knitting_source)==3)
				{
					if ($txt_knitting_com_id=='')
						$party_cond_2="";
					else
						$party_cond_2=" and a.supplier_id in (".$txt_knitting_com_id.")";
					
					/*
					$sql_yrec="select a.id, a.supplier_id, a.receive_date, a.ref_closing_status, sum(b.cons_quantity) as cons_quantity
					from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id=".$cbo_company_name." and a.receive_purpose in (2,12,15,38) and a.item_category =1 and b.item_category =1 and a.entry_form=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond_2 group by a.id, a.supplier_id, a.receive_date, a.ref_closing_status"; //$knitting_source_rec_cond
					//echo $sql_yrec; die;
					$sql_yrec_res=sql_select($sql_yrec);
					foreach($sql_yrec_res as $rowyRec)
					{
						$trns_date=''; $date_frm=''; $date_to='';
						$trns_date=date('Y-m-d',strtotime($rowyRec[csf('receive_date')]));
						$date_frm=date('Y-m-d',strtotime($from_date));
						$date_to=date('Y-m-d',strtotime($to_date));
						
						if($trns_date<$date_frm)
						{
							$party_opening_arr[$rowyRec[csf('supplier_id')]]['yrOpening']+=$rowyRec[csf('cons_quantity')];
						}
						if($trns_date>=$date_frm && $trns_date<=$date_to)
						{
							$party_data[$rowyRec[csf('supplier_id')]]['yarn_rec']+=$rowyRec[csf('cons_quantity')];
							$yarnRcvIdArr[$rowyRec[csf('supplier_id')]][$rowyRec[csf('id')]] =$rowyRec[csf('id')];
						}
					}
					unset($sql_yrec_res);
					*/
				}
				//for Out-bound Subcontract end
	
				if ($txt_knitting_com_id=='')
					$party_cond_1="";
				else
					$party_cond_1=" AND a.knitting_company in ($txt_knitting_com_id)";
					
				if (str_replace("'","",$cbo_knitting_source)==0)
					$knit_source_cond="";
				else
					$knit_source_cond=" AND a.knitting_source = $cbo_knitting_source";
	
				/*
				|--------------------------------------------------------------------------
				| for issue
				|--------------------------------------------------------------------------
				*/
				$sql_iss="SELECT a.id AS ID, a.knit_dye_company AS KNIT_DYE_COMPANY, a.issue_date AS ISSUE_DATE, b.cons_quantity AS CONS_QUANTITY, b.return_qnty AS RETURN_QNTY FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name." $knitting_company_cond_1 $knitting_source_cond";
				//echo $sql_iss; die;
				$sql_iss_res=sql_select($sql_iss);
				$issue_qty_arr=array();
				$opening_balance_issue_id_arr=array();
				$issue_id_arr=array();
				$popup_issue_id_arr = array();
				foreach($sql_iss_res as $rowIss)
				{
					$trns_date='';
					$date_frm='';
					$date_to='';
					$trns_date=date('Y-m-d',strtotime($rowIss['ISSUE_DATE']));
					$date_frm=date('Y-m-d',strtotime($from_date));
					$date_to=date('Y-m-d',strtotime($to_date));
	
					if($trns_date<$date_frm)
					{
						$opening_balance_issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$party_opening_arr[$rowIss['KNIT_DYE_COMPANY']]['issOpening']+=$rowIss['CONS_QUANTITY'];
					}
					
					if($trns_date>=$date_frm && $trns_date<=$date_to)
					{
						$issue_id_arr[$rowIss['ID']] = $rowIss['ID'];
						$popup_issue_id_arr[$rowIss['KNIT_DYE_COMPANY']][$rowIss['ID']] = $rowIss['ID'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['issue_qnty'] += $rowIss['CONS_QUANTITY'];
						$party_data[$rowIss['KNIT_DYE_COMPANY']]['return_qnty'] += $rowIss['RETURN_QNTY'];
					}
				}
				unset($sql_iss_res);
				//echo "<pre>";
				//print_r($party_data);
				//echo "</pre>";
				//die;
				
				/*
				|--------------------------------------------------------------------------
				| for receive
				|--------------------------------------------------------------------------
				*/
				$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED FROM inv_receive_master a WHERE a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM IN(2) AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_receive;
				$sql_receive_rslt=sql_select($sql_receive);
				$receive_id_arr = array();
				foreach($sql_receive_rslt as $row)
				{
					if($row['ROLL_MAINTAINED'] ==1)
					{
						$receive_id_arr['roll_rcv'][$row['ID']] = $row['ID'];
					}
					else
					{
						$receive_id_arr['bulk_rcv'][$row['ID']] = $row['ID'];
					}
				}
				/*echo "<pre>";
				print_r($receive_id_arr);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for roll receive
				|--------------------------------------------------------------------------
				*/
				$sql_roll_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['roll_rcv'], '0', 'b.mst_id');
				//echo $sql_roll_receive;
				$sql_roll_receive_rslt = sql_select($sql_roll_receive);
				$popup_receive_id_arr = array();
				$duplicate_check = array();
				foreach($sql_roll_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['QNTY']+$row['REJECT_QNTY'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_QNTY'];
							}
						}
					}
				}
				unset($sql_roll_receive_rslt);
				/*echo "<pre>";
				print_r($party_datas);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for bulk receive
				|--------------------------------------------------------------------------
				*/
				$sql_bulk_receive = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$cbo_company_name." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['bulk_rcv'], '0', 'b.mst_id');
				//echo $sql_bulk_receive;
				$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
				$duplicate_check = array();
				foreach($sql_bulk_receive_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$expBooking = array();
						$expBooking = explode('-', $row['BOOKING_NO']);
						if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
						{
							$trns_date = '';
							$date_frm = '';
							$date_to = '';
							$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
							$date_frm = date('Y-m-d',strtotime($from_date));
							$date_to = date('Y-m-d',strtotime($to_date));
							
							if($trns_date < $date_frm)
							{
								$opening_balance_rec = 0;
								$opening_balance_rec = $row['GREY_RECEIVE_QNTY']+$row['REJECT_FABRIC_RECEIVE'];
								$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
							}
							
							if($trns_date >= $date_frm && $trns_date <= $date_to)
							{
								$duplicate_check[$row['ID']] = $row['ID'];
								$popup_receive_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
								$party_data[$row['KNITTING_COMPANY']]['fRec'] += $row['GREY_RECEIVE_QNTY'];
								$party_data[$row['KNITTING_COMPANY']]['rej_fab'] += $row['REJECT_FABRIC_RECEIVE'];
							}
						}						
					}
				}
				unset($sql_bulk_receive_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				/*
				|--------------------------------------------------------------------------
				| for yarn issue return
				|--------------------------------------------------------------------------
				*/
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($opening_balance_issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date < $date_frm)
						{
							$opening_balance_rec = 0;
							$opening_balance_rec = $row['CONS_QUANTITY']+$row['CONS_REJECT_QNTY'];
							$party_opening_arr[$row['KNITTING_COMPANY']]['recOpening'] += $opening_balance_rec;
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				
				//$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond." AND a.issue_id IN(SELECT a.ID FROM inv_issue_master a, inv_transaction b WHERE a.id=b.mst_id AND a.entry_form=3 AND a.item_category=1 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=".$cbo_company_name.$knitting_company_cond_1.$knitting_source_cond.") ORDER BY a.knitting_company, a.receive_date";
				
				$sql_issue_return = "SELECT a.ID AS MST_ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.id=b.mst_id AND a.item_category =1 AND a.entry_form = 9 AND a.receive_basis = 1 AND a.company_id = ".$cbo_company_name." AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ".$knit_source_cond.$party_cond_1.$issue_challan_cond.where_con_using_array($issue_id_arr, '0', 'a.issue_id')." ORDER BY a.knitting_company, a.receive_date";
				//echo $sql_issue_return;
				$sql_issue_return_rslt = sql_select($sql_issue_return);
				$popup_issue_return_id_arr = array();
				$duplicate_check = array();
				foreach($sql_issue_return_rslt as $row)
				{
					if($duplicate_check[$row['ID']] != $row['ID'])
					{
						$duplicate_check[$row['ID']] = $row['ID'];
						$trns_date = '';
						$date_frm = '';
						$date_to = '';
						$trns_date = date('Y-m-d',strtotime($row['RECEIVE_DATE']));
						$date_frm = date('Y-m-d',strtotime($from_date));
						$date_to = date('Y-m-d',strtotime($to_date));
						
						if($trns_date >= $date_frm && $trns_date <= $date_to)
						{
							$popup_issue_return_id_arr[$row['KNITTING_COMPANY']][$row['MST_ID']] = $row['MST_ID'];
							$party_data[$row['KNITTING_COMPANY']]['ret_yarn']+=$row['CONS_QUANTITY'];
							$party_data[$row['KNITTING_COMPANY']]['rej_yarn']+=$row['CONS_REJECT_QNTY'];
						}
					}
				}
				unset($sql_issue_return_rslt);
				/*echo "<pre>";
				print_r($party_data);
				echo "</pre>";
				die;*/

				/*
				|--------------------------------------------------------------------------
				| tmp_trans_id table data deleting
				|--------------------------------------------------------------------------
				*/
				$con = connect();
				$delete_rslt = execute_query("DELETE FROM tmp_trans_id WHERE userid=".$user_id);
				oci_commit($con);
	
				$i=1;
				foreach($party_data as $party_id=>$party_datas)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(str_replace("'","",$cbo_knitting_source)==1)
						$knitting_party=$company_arr[$party_id];
					else if(str_replace("'","",$cbo_knitting_source)==3)
						$knitting_party=$supplier_arr[$party_id];
					else
						$knitting_party="&nbsp;";
	
					$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					$opening_balance= $party_opening_arr[$party_id]['issOpening']-($party_opening_arr[$party_id]['recOpening']+$party_opening_arr[$party_id]['yrOpening']);
					//echo $party_opening_arr[$party_id]['issOpening'].'='.$party_opening_arr[$party_id]['recOpening'].'='.$party_opening_arr[$party_id]['recOpenings'];
	
					$yarn_issue=$party_datas['issue_qnty'];
					$yarn_returnable_qty=$party_datas['return_qnty'];
					
					$dy_tx_wx_rcon=$party_datas['yarn_rec'];
					$grey_receive_qnty=$party_datas['fRec'];
					$reject_fabric_receive=$party_datas['rej_fab'];
					
					$yarn_return_qnty=$party_datas['ret_yarn'];
					$yarn_return_reject_qnty=$party_datas['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					
					$balance=($opening_balance+$yarn_issue)-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					
					//for receive id
					foreach($popup_receive_id_arr[$party_id] as $key=>$val)
					{
						$tmp_rcv_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '1', '".$party_id."')");
					}
					
					//for issue id
					foreach($popup_issue_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '2', '".$party_id."')");
					}
					
					//for issue return id
					foreach($popup_issue_return_id_arr[$party_id] as $key=>$val)
					{
						$tmp_issue_rtrn_rslt = execute_query("insert into tmp_trans_id (id, userid, type, party_id) values ('".$val."', '".$user_id."', '3', '".$party_id."')");
					}

					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="150"><p><? echo $knitting_party; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo 'KG'; ?>&nbsp;</td>
						<td width="100" align="right"><? echo number_format($opening_balance,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_issue,2); ?></td>                                
						<td width="100" align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
						<td width="100" align="right"><? echo number_format($dy_tx_wx_rcon,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
						<td align="right"><a href="#" onClick="func_onclick_sample_balance('<?php echo $knitting_party; ?>', '<?php echo $opening_balance; ?>', '<?php echo $party_id; ?>')"><? echo number_format($balance,2); ?></a></td> 
					</tr>
					<?
					$i++;
					
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
					$tot_returnable+=$yarn_returnable_qty;
					$tot_process_loss+=$process_loss;
					$tot_balance_after_process_loss += $balance_after_process_loss;
				}
				//unset($result);
				oci_commit($con);
				?>
			</table>       
			</div>
			<table width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="60">Total</th>
					<td width="100" align="right"><? echo number_format($tot_opening_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_receive,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_rejFab_rec,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_dy_tx_wx_rcon,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_return,2); ?></td>
					<td width="100" align="right"><? echo number_format($tot_yarn_retReject,2); ?></td>
					<td align="right" style="padding-right:18px;"><? echo number_format($tot_balance,2); ?></td>
				</tr>
			</table>
		</fieldset>      
		<?
	}
	
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

//action_issue_qty
if($action=="action_issue_qty")
{
	echo load_html_head_contents("Issue Qty", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	
	$sql_req="SELECT c.KNIT_ID, c.REQUISITION_NO, d.REF_CLOSING_STATUS FROM ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d WHERE c.knit_id = d.id AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0";
	$sql_req_rslt = sql_select($sql_req);
	$req_arr = array();
	$ref_closing_arr = array();
	foreach($sql_req_rslt as $row)
	{
		$prog_arr[$row['KNIT_ID']] = $row['KNIT_ID'];
		$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
		$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
	}
	
	//for requisition information
	$sqlReq="SELECT c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO, d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID FROM ppl_yarn_requisition_entry c, product_details_master d WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0";
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;

	$yarn_issue="SELECT a.ID, a.ISSUE_NUMBER, a.KNIT_DYE_COMPANY AS KNIT_COMPANY, a.ISSUE_DATE, a.BOOKING_NO, a.ISSUE_BASIS, b.ID AS TRANS_ID, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY, b.PROD_ID FROM inv_issue_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND a.knit_dye_company = e.party_id AND e.userid = ".$user_id." AND e.type = 2 AND e.party_id = ".$party_id." AND a.item_category=1 AND a.entry_form=3 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND a.company_id = ".$company_id." AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.issue_number ASC";
	$sql_iss_res=sql_select($yarn_issue);
	$issue_qty_arr=array();
	$duplicate_check = array();
	foreach($sql_iss_res as $row)
	{
		$issue_date =date('d-m-Y',strtotime($row['ISSUE_DATE']));
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			if(in_array($row['REQ_NO'], $req_arr))
			{
				$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['req_no'] = $row['REQ_NO'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
				$dataArr[$issue_date][$row['ISSUE_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
			}
		}
	}
	unset($sql_iss_res);
	?>
</head>
<body>
	<div align="center">
        <table width="940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Issue Date</th>
                	<th width="100">Issue No</th>
                	<th width="70">Req. No</th>
                	<th width="120">Prog. No</th>
                	<th width="70">Count</th>
                	<th width="100">Yarn Composition</th>
                	<th width="100">Yarn Supplier</th>
                	<th width="100">Yarn Type</th>
                	<th width="100">Yarn Lot</th>
                	<th width="80">Issue Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
				
				$sl = 0;
                foreach($dataArr as $issueDate=>$issueDateArr)
				{
					foreach($issueDateArr as $issueNo=>$row)
					{
						if($row['issue_qnty']*1 > 0)
						{
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
								<td align="center"><?php echo $sl; ?></td>
								<td align="center"><?php echo $issueDate; ?></td>
								<td align="center"><?php echo $issueNo; ?></th>
								<td align="center"><?php echo $row['req_no']; ?></th>
								<td align="center"><?php echo $row['prog_no']; ?></th>
								<td align="center"><?php echo $row['yarn_count_id']; ?></td>
								<td><p><?php echo $row['yarn_composition']; ?></p></td>
								<td><p><?php echo $row['supplier_id']; ?></p></td>
								<td><p><?php echo $row['yarn_type']; ?></p></td>
								<td><?php echo $row['lot']; ?></td>
								<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
							</tr>
							<?php
							$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="10" align="right">Total</th>
                	<th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_fabric_receive
if($action=="action_fabric_receive")
{
	echo load_html_head_contents("Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$zs_receive_id = explode(',', $popupFabricReceiveId);

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.id AS ID, a.recv_number AS RECV_NUMBER, a.knitting_company AS KNITTING_COMPANY, a.receive_date AS RECEIVE_DATE, a.receive_basis AS RECEIVE_BASIS, a.ref_closing_status AS REF_CLOSING_STATUS, a.entry_form AS ENTRY_FORM, d.dtls_id AS PROG_NO, d.booking_no AS BOOKING_NO, d.fabric_desc AS FABRIC_DESC, d.gsm_weight AS GSM_WEIGHT, d.dia AS DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
		$gpe_info[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$gpe_info[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$gpe_info[$row['ID']]['dia'] = $row['DIA'];
		$gpe_info[$row['ID']]['challan_no'] = $row['RECV_NUMBER'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $gpe_info[$row['BOOKING_ID']]['fabric_desc'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['gsm'] = $gpe_info[$row['BOOKING_ID']]['gsm'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['dia'] = $gpe_info[$row['BOOKING_ID']]['dia'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['challan_no'] = $gpe_info[$row['BOOKING_ID']]['challan_no'];
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		$prog_data[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$prog_data[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$prog_data[$row['ID']]['dia'] = $row['DIA'];
		$prog_data[$row['ID']]['challan_no'] = $row['SYS_NUMBER'];
	}
	//echo $prog_data[9601].'=';
	
	//$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM , b.ID AS TRANS_ID, b.QNTY AS CONS_QUANTITY, b.REJECT_QNTY AS CONS_REJECT_QNTY, b.BOOKING_NO AS PROG_NO,a.CHALLAN_NO as CHALLAN_NO_NEW FROM inv_receive_master a, PRO_ROLL_DETAILS b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.entry_form = 58 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			//$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['FABRIC_DESC'] = $prog_data[$row['BOOKING_ID']]['fabric_desc'];
			$row['GSM_WEIGHT'] = $prog_data[$row['BOOKING_ID']]['gsm'];
			$row['DIA'] = $prog_data[$row['BOOKING_ID']]['dia'];
			$row['CHALLAN_NO'] = $prog_data[$row['BOOKING_ID']]['challan_no'];

			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['CHALLAN_NO'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no_new'] = $row['CHALLAN_NO_NEW'];
		}
	}
	unset($sql_rcv_zs_rslt);
	?>
</head>
<body>
	<div align="center">
        <table width="880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="10"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Challan No</th>
                	<th width="100">Receive Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($party_data as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							if($row['receive_qty']*1 > 0)
							{
								$sl++;
								if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
									<td align="center"><?php echo $sl; ?></td>
									<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>
									<td align="center"><?php echo $progNo; ?></td>
									<td><?php echo $rcvNo; ?></td>
									<td><p><?php echo $row['fabric_desc']; ?></p></td>
									<td align="center"><?php echo $row['gsm']; ?></td>
									<td align="center"><?php echo $row['dia']; ?></td>
									<td><?php echo $row['challan_no']; ?></td>
									<td><?php echo $row['challan_no_new']; ?></td>
									<td align="right"><?php echo number_format($row['receive_qty'],2); ?></td>
								</tr>
								<?php
								$totalReceiveQty +=number_format($row['receive_qty'],2,'.','');
							}
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="9" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="action_fabric_receive_16022022")
{
	echo load_html_head_contents("Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$zs_receive_id = explode(',', $popupFabricReceiveId);

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.id AS ID, a.recv_number AS RECV_NUMBER, a.knitting_company AS KNITTING_COMPANY, a.receive_date AS RECEIVE_DATE, a.receive_basis AS RECEIVE_BASIS, a.ref_closing_status AS REF_CLOSING_STATUS, a.entry_form AS ENTRY_FORM, d.dtls_id AS PROG_NO, d.booking_no AS BOOKING_NO, d.fabric_desc AS FABRIC_DESC, d.gsm_weight AS GSM_WEIGHT, d.dia AS DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
		$gpe_info[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$gpe_info[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$gpe_info[$row['ID']]['dia'] = $row['DIA'];
		$gpe_info[$row['ID']]['challan_no'] = $row['RECV_NUMBER'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $gpe_info[$row['BOOKING_ID']]['fabric_desc'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['gsm'] = $gpe_info[$row['BOOKING_ID']]['gsm'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['dia'] = $gpe_info[$row['BOOKING_ID']]['dia'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['challan_no'] = $gpe_info[$row['BOOKING_ID']]['challan_no'];
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		$prog_data[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$prog_data[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$prog_data[$row['ID']]['dia'] = $row['DIA'];
		$prog_data[$row['ID']]['challan_no'] = $row['SYS_NUMBER'];
	}
	//echo $prog_data[9601].'=';
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['FABRIC_DESC'] = $prog_data[$row['BOOKING_ID']]['fabric_desc'];
			$row['GSM_WEIGHT'] = $prog_data[$row['BOOKING_ID']]['gsm'];
			$row['DIA'] = $prog_data[$row['BOOKING_ID']]['dia'];
			$row['CHALLAN_NO'] = $prog_data[$row['BOOKING_ID']]['challan_no'];

			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['CHALLAN_NO'];
		}
	}
	unset($sql_rcv_zs_rslt);
	?>
</head>
<body>
	<div align="center">
        <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="9"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Receive Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($party_data as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							if($row['receive_qty']*1 > 0)
							{
								$sl++;
								if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
									<td align="center"><?php echo $sl; ?></td>
									<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>
									<td align="center"><?php echo $progNo; ?></td>
									<td><?php echo $rcvNo; ?></td>
									<td><p><?php echo $row['fabric_desc']; ?></p></td>
									<td align="center"><?php echo $row['gsm']; ?></td>
									<td align="center"><?php echo $row['dia']; ?></td>
									<td><?php echo $row['challan_no']; ?></td>
									<td align="right"><?php echo number_format($row['receive_qty'],2); ?></td>
								</tr>
								<?php
								$totalReceiveQty +=number_format($row['receive_qty'],2,'.','');
							}
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="8" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_reject_fabric_receive
if($action=="action_reject_fabric_receive")
{
	echo load_html_head_contents("Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$zs_receive_id = explode(',', $popupFabricReceiveId);

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.id AS ID, a.recv_number AS RECV_NUMBER, a.knitting_company AS KNITTING_COMPANY, a.receive_date AS RECEIVE_DATE, a.receive_basis AS RECEIVE_BASIS, a.ref_closing_status AS REF_CLOSING_STATUS, a.entry_form AS ENTRY_FORM, d.dtls_id AS PROG_NO, d.booking_no AS BOOKING_NO, d.fabric_desc AS FABRIC_DESC, d.gsm_weight AS GSM_WEIGHT, d.dia AS DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
		$gpe_info[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$gpe_info[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$gpe_info[$row['ID']]['dia'] = $row['DIA'];
		$gpe_info[$row['ID']]['challan_no'] = $row['RECV_NUMBER'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $gpe_info[$row['BOOKING_ID']]['fabric_desc'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['gsm'] = $gpe_info[$row['BOOKING_ID']]['gsm'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['dia'] = $gpe_info[$row['BOOKING_ID']]['dia'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['challan_no'] = $gpe_info[$row['BOOKING_ID']]['challan_no'];
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		$prog_data[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$prog_data[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$prog_data[$row['ID']]['dia'] = $row['DIA'];
		$prog_data[$row['ID']]['challan_no'] = $row['SYS_NUMBER'];
	}
	//echo $prog_data[9601].'=';
	
	//$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM , b.ID AS TRANS_ID, b.QNTY AS CONS_QUANTITY, b.REJECT_QNTY AS CONS_REJECT_QNTY, b.BOOKING_NO AS PROG_NO FROM inv_receive_master a, PRO_ROLL_DETAILS b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.entry_form = 58 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			//$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['FABRIC_DESC'] = $prog_data[$row['BOOKING_ID']]['fabric_desc'];
			$row['GSM_WEIGHT'] = $prog_data[$row['BOOKING_ID']]['gsm'];
			$row['DIA'] = $prog_data[$row['BOOKING_ID']]['dia'];
			$row['CHALLAN_NO'] = $prog_data[$row['BOOKING_ID']]['challan_no'];

			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['CHALLAN_NO'];
		}
	}
	unset($sql_rcv_zs_rslt);
	?>
</head>
<body>
	<div align="center">
        <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="9"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Receive Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($party_data as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							if($row['rej_fab']*1 > 0)
							{
								$sl++;
								if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
									<td align="center"><?php echo $sl; ?></td>
									<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>
									<td align="center"><?php echo $progNo; ?></td>
									<td><?php echo $rcvNo; ?></td>
									<td><p><?php echo $row['fabric_desc']; ?></p></td>
									<td align="center"><?php echo $row['gsm']; ?></td>
									<td align="center"><?php echo $row['dia']; ?></td>
									<td><?php echo $row['challan_no']; ?></td>
									<td align="right"><?php echo number_format($row['rej_fab'],2); ?></td>
								</tr>
								<?php
								$totalReceiveQty +=number_format($row['rej_fab'],2,'.','');
							}
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="8" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="action_reject_fabric_receive_16022022")
{
	echo load_html_head_contents("Fabric Receive", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$zs_receive_id = explode(',', $popupFabricReceiveId);

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.id AS ID, a.recv_number AS RECV_NUMBER, a.knitting_company AS KNITTING_COMPANY, a.receive_date AS RECEIVE_DATE, a.receive_basis AS RECEIVE_BASIS, a.ref_closing_status AS REF_CLOSING_STATUS, a.entry_form AS ENTRY_FORM, d.dtls_id AS PROG_NO, d.booking_no AS BOOKING_NO, d.fabric_desc AS FABRIC_DESC, d.gsm_weight AS GSM_WEIGHT, d.dia AS DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
		$gpe_info[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$gpe_info[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$gpe_info[$row['ID']]['dia'] = $row['DIA'];
		$gpe_info[$row['ID']]['challan_no'] = $row['RECV_NUMBER'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $gpe_info[$row['BOOKING_ID']]['fabric_desc'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['gsm'] = $gpe_info[$row['BOOKING_ID']]['gsm'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['dia'] = $gpe_info[$row['BOOKING_ID']]['dia'];
			$party_data[$row['RECEIVE_DATE']][$row['BOOKING_NO']][$row['RECV_NUMBER']]['challan_no'] = $gpe_info[$row['BOOKING_ID']]['challan_no'];
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		$prog_data[$row['ID']]['fabric_desc'] = $row['FABRIC_DESC'];
		$prog_data[$row['ID']]['gsm'] = $row['GSM_WEIGHT'];
		$prog_data[$row['ID']]['dia'] = $row['DIA'];
		$prog_data[$row['ID']]['challan_no'] = $row['SYS_NUMBER'];
	}
	//echo $prog_data[9601].'=';
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['FABRIC_DESC'] = $prog_data[$row['BOOKING_ID']]['fabric_desc'];
			$row['GSM_WEIGHT'] = $prog_data[$row['BOOKING_ID']]['gsm'];
			$row['DIA'] = $prog_data[$row['BOOKING_ID']]['dia'];
			$row['CHALLAN_NO'] = $prog_data[$row['BOOKING_ID']]['challan_no'];

			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['receive_qty'] += $row['CONS_QUANTITY'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
			
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['fabric_desc'] = $row['FABRIC_DESC'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['gsm'] = $row['GSM_WEIGHT'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['dia'] = $row['DIA'];
			$party_data[$row['RECEIVE_DATE']][$row['PROG_NO']][$row['RECV_NUMBER']]['challan_no'] = $row['CHALLAN_NO'];
		}
	}
	unset($sql_rcv_zs_rslt);
	?>
</head>
<body>
	<div align="center">
        <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="9"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="70">Receive Date</th>
                	<th width="70">Prog. No</th>
                	<th width="120">Receive Id</th>
                	<th width="150">Fabric Description</th>
                	<th width="70">Fab. GSM</th>
                	<th width="70">Receive MC/F.Dia</th>
                	<th width="100">Receive Ch. No</th>
                	<th width="100">Receive Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
                foreach($party_data as $rcvDate=>$rcvDateArr)
				{
					foreach($rcvDateArr as $progNo=>$progNoArr)
					{
						foreach($progNoArr as $rcvNo=>$row)
						{
							if($row['rej_fab']*1 > 0)
							{
								$sl++;
								if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
									<td align="center"><?php echo $sl; ?></td>
									<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>
									<td align="center"><?php echo $progNo; ?></td>
									<td><?php echo $rcvNo; ?></td>
									<td><p><?php echo $row['fabric_desc']; ?></p></td>
									<td align="center"><?php echo $row['gsm']; ?></td>
									<td align="center"><?php echo $row['dia']; ?></td>
									<td><?php echo $row['challan_no']; ?></td>
									<td align="right"><?php echo number_format($row['rej_fab'],2); ?></td>
								</tr>
								<?php
								$totalReceiveQty +=number_format($row['rej_fab'],2,'.','');
							}
						}
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="8" align="right">Total</th>
                	<th align="right"><?php echo number_format($totalReceiveQty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_yarn_return
if($action=="action_yarn_return")
{
	echo load_html_head_contents("Yarn Return", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();
	
	//for reference close yarn issue return
	$yarn_issue_ret="SELECT a.KNITTING_COMPANY AS KNIT_COMPANY, a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_NO, a.RECEIVE_BASIS, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY AS CONS_REJECT_QTY, b.PROD_ID FROM inv_receive_master a, inv_transaction b, tmp_trans_id c WHERE a.id=b.mst_id AND a.id = c.id AND b.mst_id = c.id AND c.userid = ".$user_id." AND c.type = 3 AND c.party_id = ".$party_id." AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.recv_number ASC";
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	$dataArr = array();
	$reqNoArr = array();
	$prodIdArr = array();
	foreach($yarn_ret_result as $row)
	{
		if($row['REQ_NO'] != '' && $row['REQ_NO'] != '0')
		{
			$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
		}
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$reqNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}
	}
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for requisition information
	$sqlReq="SELECT c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO, d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID FROM ppl_yarn_requisition_entry c, product_details_master d WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($reqNoArr, '0', 'c.requisition_no');
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;
	
	foreach($yarn_ret_result as $row)
	{
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$row['REQ_NO'] = $row['BOOKING_NO'];
		}
		
		if(!empty($reqData[$row['REQ_NO']]))
		{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = $row['REQ_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
		}
	}
	unset($yarn_ret_result);
	?>
</head>
<body>
    <div align="center">
        <table width="890" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="70">Issue Date</th>
                    <th width="100">Issue No</th>
                    <th width="70">Req. No</th>
                    <th width="70">Prog. No</th>
                    <th width="70">Count</th>
                    <th width="100">Yarn Composition</th>
                    <th width="100">Yarn Supplier</th>
                    <th width="100">Yarn Type</th>
                    <th width="100">Yarn Lot</th>
                    <th width="80">Issue Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
                $sl = 0;
                foreach($dataArr as $receiveDate=>$receiveDateArr)
                {
                    foreach($receiveDateArr as $receiveNo=>$row)
                    {
						if($row['issue_qnty']*1 > 0)
						{
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
								<td align="center"><?php echo $sl; ?></td>
								<td align="center"><?php echo $receiveDate; ?></td>
								<td align="center"><?php echo $receiveNo; ?></th>
								<td align="center"><?php echo $row['req_no']; ?></th>
								<td align="center"><?php echo $row['prog_no']; ?></th>
								<td align="center"><?php echo $row['yarn_count_id']; ?></td>
								<td><p><?php echo $row['yarn_composition']; ?></p></td>
								<td><p><?php echo $row['supplier_id']; ?></p></td>
								<td><p><?php echo $row['yarn_type']; ?></p></td>
								<td><?php echo $row['lot']; ?></td>
								<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
							</tr>
							<?php
							$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
						}
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_reject_yarn_return
if($action=="action_reject_yarn_return")
{
	echo load_html_head_contents("Reject Yarn Return", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$yarn_count_dtls = get_yarn_count_array();
	$supplier_dtls = get_supplier_array();

	//for reference close yarn issue return
	$yarn_issue_ret="SELECT a.KNITTING_COMPANY AS KNIT_COMPANY, a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_NO, a.RECEIVE_BASIS, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY AS CONS_REJECT_QTY, b.PROD_ID FROM inv_receive_master a, inv_transaction b, tmp_trans_id c WHERE a.id=b.mst_id AND a.id = c.id AND b.mst_id = c.id AND c.userid = ".$user_id." AND c.type = 3 AND c.party_id = ".$party_id." AND a.receive_basis = 3 AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.recv_number";
	//echo $yarn_issue_ret;
	$yarn_ret_result = sql_select($yarn_issue_ret);
	$dataArr = array();
	$reqNoArr = array();
	$prodIdArr = array();
	foreach($yarn_ret_result as $row)
	{
		if($row['REQ_NO'] != '' && $row['REQ_NO'] != '0')
		{
			$reqNoArr[$row['REQ_NO']] = $row['REQ_NO'];
		}
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$reqNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}
	}
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for requisition information
	$sqlReq="SELECT c.requisition_no AS REQ_NO, c.knit_id AS PROG_NO, d.lot AS LOT, d.yarn_comp_type1st AS YARN_COMP_TYPE1ST, d.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, d.yarn_comp_type2nd AS YARN_COMP_TYPE2ND, d.yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, d.yarn_count_id AS YARN_COUNT_ID, d.yarn_type AS YARN_TYPE, d.color AS  COLOR, d.supplier_id AS SUPPLIER_ID FROM ppl_yarn_requisition_entry c, product_details_master d WHERE c.prod_id=d.id AND c.status_active=1 AND c.is_deleted=0".where_con_using_array($reqNoArr, '0', 'c.requisition_no');
	//echo $sqlReq;
	$sqlReqRslt=sql_select($sqlReq);
	$reqData = array();
	foreach($sqlReqRslt as $row)
	{
		//for composition
		$composition_str = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$composition_str = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		//for composition end
		
		$reqData[$row['REQ_NO']]['PROG_NO'] = $row['PROG_NO'];
		$reqData[$row['REQ_NO']]['COMPOSITION'] = $composition_str;
		$reqData[$row['REQ_NO']]['YARN_COUNT_ID'] = $yarn_count_dtls[$row['YARN_COUNT_ID']];
		$reqData[$row['REQ_NO']]['SUPPLIER_ID'] = $supplier_dtls[$row['SUPPLIER_ID']];
		$reqData[$row['REQ_NO']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$reqData[$row['REQ_NO']]['LOT'] = $row['LOT'];
	}
	//echo "<pre>";
	//print_r($reqData); die;
	
	foreach($yarn_ret_result as $row)
	{
		$receive_date =date('d-m-Y',strtotime($row['RECEIVE_DATE']));
		
		if($row['RECEIVE_BASIS'] == 3)
		{
			$row['REQ_NO'] = $row['BOOKING_NO'];
		}
		
		//if(!empty($reqData[$row['REQ_NO']]))
		//{
			$dataArr[$receive_date][$row['RECV_NUMBER']]['issue_qnty'] += $row['CONS_REJECT_QTY'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['req_no'] = $row['REQ_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['prog_no'] = $reqData[$row['REQ_NO']]['PROG_NO'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_count_id'] = $reqData[$row['REQ_NO']]['YARN_COUNT_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_composition'] = $reqData[$row['REQ_NO']]['COMPOSITION'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['supplier_id'] = $reqData[$row['REQ_NO']]['SUPPLIER_ID'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['yarn_type'] = $reqData[$row['REQ_NO']]['YARN_TYPE'];
			$dataArr[$receive_date][$row['RECV_NUMBER']]['lot'] = $reqData[$row['REQ_NO']]['LOT'];
		//}
	}
	unset($yarn_ret_result);
	?>
</head>
<body>
    <div align="center">
        <table width="940" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
                <tr>
                    <th width="30">Sl</th>
                	<th width="70">Issue Return Date</th>
                	<th width="100">Issue Return No</th>
                	<th width="70">Req. No</th>
                	<th width="70">Prog. No/ Booking No</th>
                	<th width="70">Count</th>
                	<th width="100">Yarn Composition</th>
                	<th width="100">Yarn Supplier</th>
                	<th width="100">Yarn Type</th>
                	<th width="100">Yarn Lot</th>
                	<th width="80">Rej. Yarn Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if(empty($dataArr))
				{
					?>
                    <tr><td colspan="11"><?php echo get_empty_data_msg(); ?></td></tr>
                    <?php
					die;
				}
                $sl = 0;
                foreach($dataArr as $receiveDate=>$receiveDateArr)
                {
                    foreach($receiveDateArr as $receiveNo=>$row)
                    {
						if($row['issue_qnty']*1 > 0)
						{
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?php echo $bgcolor;?>" height="20" valign="middle">
								<td align="center"><?php echo $sl; ?></td>
								<td align="center"><?php echo $receiveDate; ?></td>
								<td align="center"><?php echo $receiveNo; ?></th>
								<td align="center"><?php echo $row['req_no']; ?></th>
								<td align="center"><?php echo $row['prog_no']; ?></th>
								<td align="center"><?php echo $row['yarn_count_id']; ?></td>
								<td><p><?php echo $row['yarn_composition']; ?></p></td>
								<td><p><?php echo $row['supplier_id']; ?></p></td>
								<td><p><?php echo $row['yarn_type']; ?></p></td>
								<td><?php echo $row['lot']; ?></td>
								<td align="right"><?php echo number_format($row['issue_qnty'],2); ?></td>
							</tr>
							<?php
							$total_issue_qnty +=number_format($row['issue_qnty'],2,'.','');
						}
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><?php echo number_format($total_issue_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_process_loss
if($action=="action_process_loss")
{
	echo load_html_head_contents("Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	
	/*
	|--------------------------------------------------------------------------
	| for issue
	|--------------------------------------------------------------------------
	*/
	$sql_req="SELECT a.BOOKING_NO, b.REF_CLOSING_STATUS, c.KNIT_ID, c.REQUISITION_NO FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id = b.mst_id AND b.id = c.knit_id AND a.status_active=1 AND a.is_deleted=0  AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=".$company_id;
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	$req_arr = array();
	$req_data_arr = array();
	$prog_data_arr = array();
	$ref_closing_arr = array();
	foreach($sql_req_rslt as $row)
	{
		$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
		
		$prog_data_arr[$row['KNIT_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
		
		$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
		$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
	}
	
	$sql_issue="SELECT a.BOOKING_ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, a.ISSUE_BASIS, a.ISSUE_PURPOSE, b.ID, b.REQUISITION_NO, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_issue_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND a.knit_dye_company = e.party_id AND e.userid = ".$user_id." AND e.type = 2 AND e.party_id = ".$party_id." AND a.item_category=1 AND a.entry_form=3 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND a.company_id = ".$company_id." AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
	//echo $sql_issue; die;
	$sql_issue_rslt = sql_select($sql_issue);
	$duplicate_check = array();
	foreach($sql_issue_rslt as $row)
	{
		if(in_array($row['REQUISITION_NO'], $req_arr))
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$row['KNIT_ID'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];
				
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['KNIT_ID']]['issue_qty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['KNIT_ID']]['issue_reject_qty'] += $row['CONS_REJECT_QNTY'];
				}
			}
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>"; die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	$duplicate_check = array();
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
			{
				$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
				$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
				$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
				
				if(in_array($row['REQUISITION_NO'], $req_arr))
				{
					if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
					{
						$refCloseDataArr[$row['PROG_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
						$refCloseDataArr[$row['PROG_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
					}
				}
			}
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		
	}
	//echo $prog_data[9601].'=';
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];			
	
			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
				{
					$refCloseDataArr[$row['PROG_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['PROG_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
				}
			}
		}
	}
	unset($sql_rcv_zs_rslt);

	/*
	|--------------------------------------------------------------------------
	| for issue return and ref closing issue return
	|--------------------------------------------------------------------------
	*/
	$yarn_issue_ret="SELECT a.KNITTING_COMPANY AS KNIT_COMPANY, a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_NO, a.RECEIVE_BASIS, b.ID, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY AS CONS_REJECT_QTY, b.PROD_ID FROM inv_receive_master a, inv_transaction b, tmp_trans_id c WHERE a.id=b.mst_id AND a.id = c.id AND b.mst_id = c.id AND c.userid = ".$user_id." AND c.type = 3 AND c.party_id = ".$party_id." AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.recv_number ASC";
	//echo $yarn_issue_return;
	$yarn_issue_return_rslt = sql_select($yarn_issue_ret);
	$duplicate_check = array();
	foreach($yarn_issue_return_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$row['REQUISITION_NO'] = $row['BOOKING_NO'];
			$row['PROG_NO'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];

			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['PROG_NO']]['issue_return_qty'] += $row['CONS_QTY'];
					$refCloseDataArr[$row['PROG_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
				}
			}
		}
	}
	unset($yarn_issue_return_rslt);
	//echo "<pre>";
	//print_r($party_data);
	//echo "</pre>";	
	
	?>
</head>
<body>
	<div align="center">
        <table width="230" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="5"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<!--<th width="100">Receive Date</th>-->
                	<th width="100">Prog. No</th>
                	<th width="100">Process Loss Qty</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$sl = 0;
				foreach($refCloseDataArr as $progNo=>$row)
				{
					//echo $refCloseDataArr[$progNo]['issue_qty'].'='.$refCloseDataArr[$progNo]['issue_return_qty'].'='.$row['grey_receive_qnty'].'='.$row['reject_fabric_receive'];
					$process_loss = $refCloseDataArr[$progNo]['issue_qty']-($refCloseDataArr[$progNo]['issue_return_qty']+$row['grey_receive_qnty']+$row['reject_fabric_receive']);
					if($process_loss*1 != 0)
					{
						$sl++;
						if ($sl%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						?>
						<tr bgcolor="<?php echo $bgcolor;?>" height="20">
							<td align="center"><?php echo $sl; ?></td>
							<!--<td align="center"><?php echo date('d-m-Y', strtotime($rcvDate)); ?></td>-->
							<td align="center"><?php echo $progNo; ?></th>
							<td align="right"><?php echo number_format($process_loss,2); ?></td>
						</tr>
						<?php
						$totalProcessLoss += number_format($process_loss,2,'.','');
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="2">Total</th>
                    <th align="right"><?php echo number_format($totalProcessLoss,2); ?></th>
                </tr>
            </tfoot>
        </table>
    
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_balance_after_process_loss
if($action=="action_balance_after_process_loss")
{
	echo load_html_head_contents("Balance After Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('rpt_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<?php
	/*
	|--------------------------------------------------------------------------
	| for issue
	|--------------------------------------------------------------------------
	*/
	$sql_req="SELECT a.BOOKING_NO, b.REF_CLOSING_STATUS, c.KNIT_ID, c.REQUISITION_NO FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id = b.mst_id AND b.id = c.knit_id AND a.status_active=1 AND a.is_deleted=0  AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=".$company_id;
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	$req_arr = array();
	$req_data_arr = array();
	$prog_data_arr = array();
	$ref_closing_arr = array();
	foreach($sql_req_rslt as $row)
	{
		$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
		
		$prog_data_arr[$row['KNIT_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
		
		$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
		$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
	}
	
	$sql_issue="SELECT a.BOOKING_ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, a.ISSUE_BASIS, a.ISSUE_PURPOSE, b.ID, b.REQUISITION_NO, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_issue_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND a.knit_dye_company = e.party_id AND e.userid = ".$user_id." AND e.type = 2 AND e.party_id = ".$party_id." AND a.item_category=1 AND a.entry_form=3 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND a.company_id = ".$company_id." AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
	//echo $sql_issue; die;
	$sql_issue_rslt = sql_select($sql_issue);
	$duplicate_check = array();
	foreach($sql_issue_rslt as $row)
	{
		if(in_array($row['REQUISITION_NO'], $req_arr))
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$row['BOOKING_NO'] = $req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'];
				$row['KNIT_ID'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];
				
				$party_data[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_qnty'] += $row['CONS_QUANTITY'];
				$party_data[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['return_qnty'] += $row['RETURN_QNTY'];
				
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_qty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
				}
			}
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>"; die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	$duplicate_check = array();
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
			{
				$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
				$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['booking_no'];
				$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
				$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
				
				if(in_array($row['REQUISITION_NO'], $req_arr))
				{
					$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['fRec'] += $row['CONS_QUANTITY'];
					$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
					
					if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
					{
						$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
						$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
					}
				}
			}
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	/*
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		
	}
	//echo $prog_data[9601].'=';
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	*/
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM , b.ID AS TRANS_ID, b.QNTY AS CONS_QUANTITY, b.REJECT_QNTY AS CONS_REJECT_QNTY, b.BOOKING_NO AS PROG_NO FROM inv_receive_master a, PRO_ROLL_DETAILS b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.entry_form = 58 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			//$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];	
			$row['BOOKING_NO'] = $prog_data_arr[$row['PROG_NO']]['BOOKING_NO'];
	
			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['fRec'] += $row['CONS_QUANTITY'];
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
				
				if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
				}
			}
		}
	}
	unset($sql_rcv_zs_rslt);

	/*
	|--------------------------------------------------------------------------
	| for issue return and ref closing issue return
	|--------------------------------------------------------------------------
	*/
	$yarn_issue_ret="SELECT a.KNITTING_COMPANY AS KNIT_COMPANY, a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_NO, a.RECEIVE_BASIS, b.ID, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY AS CONS_REJECT_QTY, b.PROD_ID FROM inv_receive_master a, inv_transaction b, tmp_trans_id c WHERE a.id=b.mst_id AND a.id = c.id AND b.mst_id = c.id AND c.userid = ".$user_id." AND c.type = 3 AND c.party_id = ".$party_id." AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.recv_number ASC";
	//echo $yarn_issue_return;
	$yarn_issue_return_rslt = sql_select($yarn_issue_ret);
	$duplicate_check = array();
	foreach($yarn_issue_return_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$row['REQUISITION_NO'] = $row['BOOKING_NO'];
			$row['BOOKING_NO'] = $req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'];
			$row['PROG_NO'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];

			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['ret_yarn'] += $row['CONS_QTY'];
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_yarn'] += $row['CONS_REJECT_QTY'];
				
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['issue_return_qty'] += $row['CONS_QTY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
				}
			}
		}
	}
	unset($yarn_issue_return_rslt);
	//echo "<pre>";
	//print_r($party_data);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for booking information
	|--------------------------------------------------------------------------
	*/
	$bookingNoArray = array();
	$sampleBookingNoArray = array();
	foreach($party_data as $key=>$val)
	{
		$expBooking = array();
		$expBooking = explode('-', $key);
		if($expBooking[1] != 'SMN')
		{
			$bookingNoArray[$key] = $key;
		}
	}
	
	//for booking information
	$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME FROM WO_BOOKING_DTLS a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, LIB_BUYER d WHERE a.JOB_NO = b.JOB_NO AND a.PO_BREAK_DOWN_ID = c.ID AND b.BUYER_NAME=d.id".where_con_using_array($bookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	$bookingInfo = array();
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['po_no'][$row['PO_NUMBER']] = $row['PO_NUMBER'];
		$bookingInfo[$row['BOOKING_NO']]['style_no'][$row['STYLE_REF_NO']] = $row['STYLE_REF_NO'];
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'][$row['BUYER_NAME']] = $row['BUYER_NAME'];
	}
	unset($sql_result);
	?>
</head>
<body>
    <p id="btn_container" style="margin-left:10px; margin-top:10px; margin-bottom:10px;"></p>
    <?php ob_start(); ?>
    <div align="center" id="rpt_container">
        <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="14"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="100">Booking No</th>
                	<th width="100">Progam No</th>
                	<th width="100">Requisition No</th>
                	<th width="100">Buyer</th>
                	<th width="100">Style Ref.</th>
                	<th width="100">Yarn Issued</th>
                	<th width="100">Fabric Received</th>
                	<th width="100">Reject Fabric Received</th>
                	<th width="100">Yarn Returned</th>
                	<th width="100">Reject Yarn Returned</th>
                	<th width="100">Balance</th>
                	<th width="100">Process Loss Qty.</th>
                	<th width="100">After Process Loss Balance</th>
                </tr>
            </thead>
            <tbody>
				<tr style="font-weight:bold;">
                	<td colspan="11">Party Name : <?php echo $knitting_party; ?></td>
                	<td colspan="2" align="right">Opening Balanced&nbsp;</td>
                	<td align="right"><?php echo number_format($opening_balance,2); ?></td>
                </tr>
				
				<?php
				$i=0;
				foreach($party_data as $bookingNo=>$bookingNoArr)
				{
					//if($bookingNo != '')
					//{
					foreach($bookingNoArr as $prog_no=>$prog_noArr)
					{
						foreach($prog_noArr as $reqNo=>$row)
						{
							$i++;
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
		
							//$opening_balance=0;
							$yarn_issue=0;
							$yarn_returnable_qty=0;
							$dy_tx_wx_rcon=0;
							
							$yarn_issue=$row['issue_qnty'];
							$yarn_returnable_qty=$row['return_qnty'];
							
							$dy_tx_wx_rcon=$row['yarn_rec'];
							$grey_receive_qnty=$row['fRec'];
							$reject_fabric_receive=$row['rej_fab'];
							
							$yarn_return_qnty=$row['ret_yarn'];
							$yarn_return_reject_qnty=$row['rej_yarn'];
							$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
							$balance=$yarn_issue-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
							
							//for reference close
							//echo $refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_qty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_return_qty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['grey_receive_qnty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['reject_fabric_receive']."<br>";
							$process_loss = $refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_qty']-($refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_return_qty']+$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['grey_receive_qnty']+$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['reject_fabric_receive']);
							$balance_after_process_loss = $balance-$process_loss;
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><? echo $i; ?></td>
								<td><? echo $bookingNo; ?></td>
								<td><? echo $prog_no; ?></td>
								<td><? echo $reqNo; ?></td>
								<td><? echo implode(', ', $bookingInfo[$bookingNo]['buyer_name']); ?></td>
								<td><? echo implode(', ', $bookingInfo[$bookingNo]['style_no']); ?></td>                                
								<td align="right"><? echo number_format($yarn_issue,2); ?></td>
								<td align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
								<td align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
								<td align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
								<td align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
								<td align="right"><? echo number_format($balance,2); ?></td>
								<td align="right"><? echo number_format($process_loss,2); ?></td>
								<td align="right"><? echo number_format($balance_after_process_loss,2); ?></td>
							</tr>
							<?php
							$tot_opening_bal+=$opening_balance;
							$tot_issue+=$yarn_issue;
							$tot_receive+=$grey_receive_qnty;
							$tot_rejFab_rec+=$reject_fabric_receive;
							$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
							$tot_yarn_return+=$yarn_return_qnty;
							$tot_yarn_retReject+=$yarn_return_reject_qnty;
							$tot_balance+=$balance;
							//$tot_returnable+=$yarn_returnable_qty;
							$tot_process_loss+=$process_loss;
							$tot_balance_after_process_loss += $balance_after_process_loss;
						}
					}
					//}
				}
				
				$gtot_balance = number_format($opening_balance,2,'.','')+number_format($tot_issue,2,'.','')-(number_format($tot_dy_tx_wx_rcon,2,'.','')+number_format($tot_receive,2,'.','')+number_format($tot_rejFab_rec,2,'.','')+number_format($tot_yarn_return,2,'.','')+number_format($tot_yarn_retReject,2,'.',''));
				$gtot_balance_after_process_loss = $gtot_balance-$tot_process_loss;
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="6">Total</th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance_after_process_loss,2); ?></th>
                </tr>
            	<tr>
                	<th colspan="5">Openning With Party Total Balanced</th>
                    <th align="right"><?php echo number_format($opening_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance_after_process_loss,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<?  	
$html=ob_get_contents();
ob_flush();

foreach (glob("$user_id*.xls") as $filename) 
{
	@unlink($filename);
}

$name=time();
$filename=$user_id."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
ob_end_clean();
?>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$(document).ready(function(e){
	document.getElementById('btn_container').innerHTML='<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;<a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
});	
</script>
</html>
<?
exit();
}

if($action=="action_balance_after_process_loss_16022022")
{
	echo load_html_head_contents("Balance After Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('rpt_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<?php
	/*
	|--------------------------------------------------------------------------
	| for issue
	|--------------------------------------------------------------------------
	*/
	$sql_req="SELECT a.BOOKING_NO, b.REF_CLOSING_STATUS, c.KNIT_ID, c.REQUISITION_NO FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id = b.mst_id AND b.id = c.knit_id AND a.status_active=1 AND a.is_deleted=0  AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=".$company_id;
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	$req_arr = array();
	$req_data_arr = array();
	$prog_data_arr = array();
	$ref_closing_arr = array();
	foreach($sql_req_rslt as $row)
	{
		$req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'] = $row['KNIT_ID'];
		
		$prog_data_arr[$row['KNIT_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$prog_data_arr[$row['KNIT_ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
		
		$ref_closing_arr['prog'][$row['KNIT_ID']] = $row['REF_CLOSING_STATUS'];
		$ref_closing_arr['req'][$row['REQUISITION_NO']] = $row['REF_CLOSING_STATUS'];
	}
	
	$sql_issue="SELECT a.BOOKING_ID, a.KNIT_DYE_COMPANY, a.ISSUE_DATE, a.ISSUE_BASIS, a.ISSUE_PURPOSE, b.ID, b.REQUISITION_NO, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_issue_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND a.knit_dye_company = e.party_id AND e.userid = ".$user_id." AND e.type = 2 AND e.party_id = ".$party_id." AND a.item_category=1 AND a.entry_form=3 AND a.issue_basis = 3 AND a.issue_purpose = 1 AND a.company_id = ".$company_id." AND b.item_category=1 AND b.transaction_type=2 AND b.requisition_no is not null AND b.requisition_no != 0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
	//echo $sql_issue; die;
	$sql_issue_rslt = sql_select($sql_issue);
	$duplicate_check = array();
	foreach($sql_issue_rslt as $row)
	{
		if(in_array($row['REQUISITION_NO'], $req_arr))
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$row['BOOKING_NO'] = $req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'];
				$row['KNIT_ID'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];
				
				$party_data[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_qnty'] += $row['CONS_QUANTITY'];
				$party_data[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['return_qnty'] += $row['RETURN_QNTY'];
				
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_qty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['KNIT_ID']][$row['REQUISITION_NO']]['issue_reject_qty'] += $row['CONS_REJECT_QTY'];
				}
			}
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>"; die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store information
	|--------------------------------------------------------------------------
	*/
	$sql_gds = "SELECT a.ID, a.RECV_NUMBER, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, d.DTLS_ID AS PROG_NO, d.BOOKING_NO, d.FABRIC_DESC, d.GSM_WEIGHT, d.DIA
	FROM inv_receive_master a, ppl_planning_entry_plan_dtls d WHERE a.booking_id=d.dtls_id AND a.item_category in(13) AND a.entry_form in(2) AND a.receive_basis = 2 AND a.status_active=1 AND a.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0 AND a.id IN( SELECT b.ID FROM inv_receive_master b, tmp_trans_id c WHERE b.id = c.id AND b.RECEIVE_BASIS = 2 AND b.ITEM_CATEGORY = 13 AND b.ENTRY_FORM = 2 AND b.ROLL_MAINTAINED != 1 AND b.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.")";
	//echo $sql_gds;
	$sql_gds_rslt = sql_select($sql_gds);
	$gpe_info = array();
	foreach($sql_gds_rslt as $row)
	{
		$gpe_info[$row['ID']]['booking_no'] = $row['BOOKING_NO'];
		$gpe_info[$row['ID']]['prog_no'] = $row['PROG_NO'];
	}
	//echo "<pre>";
	//print_r($gpe_info);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 9 AND a.item_category = 13 AND a.entry_form = 22 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT d.ID FROM inv_receive_master d, tmp_trans_id c WHERE d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED != 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_receive; die;
	$sql_receive_rslt=sql_select($sql_receive);
	$duplicate_check = array();
	foreach($sql_receive_rslt as $row)
	{
		/*
		|--------------------------------------------------------------------------
		| for Knit Grey Fabric Receive
		| if receive_basis = 9(Production) and
		| entry_form = 22(Knit Grey Fabric Receive) and
		| item_category = 13(Grey Fabric) then
		|--------------------------------------------------------------------------
		*/
		if($row['ENTRY_FORM'] == 22 && $row['RECEIVE_BASIS'] == 9 && $row['ITEM_CATEGORY'] == 13 && $row['RECEIVE_DATE'] != '')
		{
			if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
			{
				$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
				$row['BOOKING_NO'] = $gpe_info[$row['BOOKING_ID']]['booking_no'];
				$row['PROG_NO'] = $gpe_info[$row['BOOKING_ID']]['prog_no'];
				$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];
				
				if(in_array($row['REQUISITION_NO'], $req_arr))
				{
					$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['fRec'] += $row['CONS_QUANTITY'];
					$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
					
					if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
					{
						$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
						$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
					}
				}
			}
		}
	}
	unset($sql_rec_res);
	//echo "<pre>";
	//print_r($delivery_basis_receive_id);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for grey delivery to store roll information
	|--------------------------------------------------------------------------
	*/
	$sql_prog = "SELECT f.ID, d.BOOKING_NO, g.FABRIC_DESC, g.GSM_WEIGHT, g.DIA, f.SYS_NUMBER FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, ppl_planning_entry_plan_dtls g, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.booking_id = g.dtls_id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id;
	//echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);
	$prog_data = array();
	foreach($sql_prog_rslt as $row)
	{
		$prog_data[$row['ID']]['prog_no'] = $row['BOOKING_NO'];
		
	}
	//echo $prog_data[9601].'=';
	
	$sql_rcv_zs = "SELECT a.ID, a.RECV_NUMBER, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.REF_CLOSING_STATUS, a.ENTRY_FORM, b.ID AS TRANS_ID, b.ITEM_CATEGORY, b.CONS_QUANTITY, b.RETURN_QNTY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.receive_basis = 10 AND a.item_category = 13 AND a.entry_form = 58 AND b.receive_basis = 10 AND b.item_category = 13 AND b.transaction_type = 1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.BOOKING_ID IN( SELECT f.ID FROM inv_receive_master d, pro_grey_prod_delivery_dtls e, pro_grey_prod_delivery_mst f, tmp_trans_id c WHERE d.id = e.grey_sys_id AND e.mst_id = f.id AND d.id = c.id AND d.RECEIVE_BASIS = 2 AND d.ITEM_CATEGORY = 13 AND d.ENTRY_FORM = 2 AND d.ROLL_MAINTAINED = 1 AND d.knitting_company = c.party_id AND c.userid = ".$user_id." AND c.type = 1 AND c.party_id = ".$party_id.") ORDER BY a.knitting_company, a.receive_date, a.recv_number";
	//echo $sql_rcv_zs;
	$sql_rcv_zs_rslt = sql_select($sql_rcv_zs);
	$duplicate_check = array();
	foreach($sql_rcv_zs_rslt as $row)
	{
		if($duplicate_check[$row['TRANS_ID']] != $row['TRANS_ID'])
		{
			$duplicate_check[$row['TRANS_ID']] = $row['TRANS_ID'];
			$row['PROG_NO'] = $prog_data[$row['BOOKING_ID']]['prog_no'];
			$row['REQUISITION_NO'] = $prog_data_arr[$row['PROG_NO']]['REQUISITION_NO'];	
			$row['BOOKING_NO'] = $prog_data_arr[$row['PROG_NO']]['BOOKING_NO'];
	
			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['fRec'] += $row['CONS_QUANTITY'];
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_fab'] += $row['CONS_REJECT_QNTY'];
				
				if($ref_closing_arr['prog'][$row['PROG_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['grey_receive_qnty'] += $row['CONS_QUANTITY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['reject_fabric_receive'] += $row['CONS_REJECT_QNTY'];
				}
			}
		}
	}
	unset($sql_rcv_zs_rslt);

	/*
	|--------------------------------------------------------------------------
	| for issue return and ref closing issue return
	|--------------------------------------------------------------------------
	*/
	$yarn_issue_ret="SELECT a.KNITTING_COMPANY AS KNIT_COMPANY, a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_NO, a.RECEIVE_BASIS, b.ID, b.REQUISITION_NO AS REQ_NO, b.CONS_QUANTITY AS CONS_QTY, b.CONS_REJECT_QNTY AS CONS_REJECT_QTY, b.PROD_ID FROM inv_receive_master a, inv_transaction b, tmp_trans_id c WHERE a.id=b.mst_id AND a.id = c.id AND b.mst_id = c.id AND c.userid = ".$user_id." AND c.type = 3 AND c.party_id = ".$party_id." AND a.receive_basis = 3 AND a.item_category = 1 AND a.entry_form=9 AND b.transaction_type=4 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 ORDER BY a.recv_number ASC";
	//echo $yarn_issue_return;
	$yarn_issue_return_rslt = sql_select($yarn_issue_ret);
	$duplicate_check = array();
	foreach($yarn_issue_return_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$row['REQUISITION_NO'] = $row['BOOKING_NO'];
			$row['BOOKING_NO'] = $req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'];
			$row['PROG_NO'] = $req_data_arr[$row['REQUISITION_NO']]['KNIT_ID'];

			if(in_array($row['REQUISITION_NO'], $req_arr))
			{
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['ret_yarn'] += $row['CONS_QTY'];
				$party_data[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['rej_yarn'] += $row['CONS_REJECT_QTY'];
				
				//for ref closing
				if($ref_closing_arr['req'][$row['REQUISITION_NO']] == 1)
				{
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['issue_return_qty'] += $row['CONS_QTY'];
					$refCloseDataArr[$row['BOOKING_NO']][$row['PROG_NO']][$row['REQUISITION_NO']]['issue_reject_return_qty'] += $row['CONS_REJECT_QTY'];
				}
			}
		}
	}
	unset($yarn_issue_return_rslt);
	//echo "<pre>";
	//print_r($party_data);
	//echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for booking information
	|--------------------------------------------------------------------------
	*/
	$bookingNoArray = array();
	$sampleBookingNoArray = array();
	foreach($party_data as $key=>$val)
	{
		$expBooking = array();
		$expBooking = explode('-', $key);
		if($expBooking[1] != 'SMN')
		{
			$bookingNoArray[$key] = $key;
		}
	}
	
	//for booking information
	$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME FROM WO_BOOKING_DTLS a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, LIB_BUYER d WHERE a.JOB_NO = b.JOB_NO AND a.PO_BREAK_DOWN_ID = c.ID AND b.BUYER_NAME=d.id".where_con_using_array($bookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	$bookingInfo = array();
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['po_no'][$row['PO_NUMBER']] = $row['PO_NUMBER'];
		$bookingInfo[$row['BOOKING_NO']]['style_no'][$row['STYLE_REF_NO']] = $row['STYLE_REF_NO'];
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'][$row['BUYER_NAME']] = $row['BUYER_NAME'];
	}
	unset($sql_result);
	?>
</head>
<body>
    <p id="btn_container" style="margin-left:10px; margin-top:10px; margin-bottom:10px;"></p>
    <?php ob_start(); ?>
    <div align="center" id="rpt_container">
        <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="14"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="100">Booking No</th>
                	<th width="100">Progam No</th>
                	<th width="100">Requisition No</th>
                	<th width="100">Buyer</th>
                	<th width="100">Style Ref.</th>
                	<th width="100">Yarn Issued</th>
                	<th width="100">Fabric Received</th>
                	<th width="100">Reject Fabric Received</th>
                	<th width="100">Yarn Returned</th>
                	<th width="100">Reject Yarn Returned</th>
                	<th width="100">Balance</th>
                	<th width="100">Process Loss Qty.</th>
                	<th width="100">After Process Loss Balance</th>
                </tr>
            </thead>
            <tbody>
				<tr style="font-weight:bold;">
                	<td colspan="11">Party Name : <?php echo $knitting_party; ?></td>
                	<td colspan="2" align="right">Opening Balanced&nbsp;</td>
                	<td align="right"><?php echo number_format($opening_balance,2); ?></td>
                </tr>
				
				<?php
				$i=0;
				foreach($party_data as $bookingNo=>$bookingNoArr)
				{
					//if($bookingNo != '')
					//{
					foreach($bookingNoArr as $prog_no=>$prog_noArr)
					{
						foreach($prog_noArr as $reqNo=>$row)
						{
							$i++;
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
		
							//$opening_balance=0;
							$yarn_issue=0;
							$yarn_returnable_qty=0;
							$dy_tx_wx_rcon=0;
							
							$yarn_issue=$row['issue_qnty'];
							$yarn_returnable_qty=$row['return_qnty'];
							
							$dy_tx_wx_rcon=$row['yarn_rec'];
							$grey_receive_qnty=$row['fRec'];
							$reject_fabric_receive=$row['rej_fab'];
							
							$yarn_return_qnty=$row['ret_yarn'];
							$yarn_return_reject_qnty=$row['rej_yarn'];
							$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
							$balance=$yarn_issue-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
							
							//for reference close
							//echo $refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_qty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_return_qty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['grey_receive_qnty'].'='.$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['reject_fabric_receive']."<br>";
							$process_loss = $refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_qty']-($refCloseDataArr[$bookingNo][$prog_no][$reqNo]['issue_return_qty']+$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['grey_receive_qnty']+$refCloseDataArr[$bookingNo][$prog_no][$reqNo]['reject_fabric_receive']);
							$balance_after_process_loss = $balance-$process_loss;
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><? echo $i; ?></td>
								<td><? echo $bookingNo; ?></td>
								<td><? echo $prog_no; ?></td>
								<td><? echo $reqNo; ?></td>
								<td><? echo implode(', ', $bookingInfo[$bookingNo]['buyer_name']); ?></td>
								<td><? echo implode(', ', $bookingInfo[$bookingNo]['style_no']); ?></td>                                
								<td align="right"><? echo number_format($yarn_issue,2); ?></td>
								<td align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
								<td align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
								<td align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
								<td align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
								<td align="right"><? echo number_format($balance,2); ?></td>
								<td align="right"><? echo number_format($process_loss,2); ?></td>
								<td align="right"><? echo number_format($balance_after_process_loss,2); ?></td>
							</tr>
							<?php
							$tot_opening_bal+=$opening_balance;
							$tot_issue+=$yarn_issue;
							$tot_receive+=$grey_receive_qnty;
							$tot_rejFab_rec+=$reject_fabric_receive;
							$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
							$tot_yarn_return+=$yarn_return_qnty;
							$tot_yarn_retReject+=$yarn_return_reject_qnty;
							$tot_balance+=$balance;
							//$tot_returnable+=$yarn_returnable_qty;
							$tot_process_loss+=$process_loss;
							$tot_balance_after_process_loss += $balance_after_process_loss;
						}
					}
					//}
				}
				
				$gtot_balance = number_format($opening_balance,2,'.','')+number_format($tot_issue,2,'.','')-(number_format($tot_dy_tx_wx_rcon,2,'.','')+number_format($tot_receive,2,'.','')+number_format($tot_rejFab_rec,2,'.','')+number_format($tot_yarn_return,2,'.','')+number_format($tot_yarn_retReject,2,'.',''));
				$gtot_balance_after_process_loss = $gtot_balance-$tot_process_loss;
				?>

            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="6">Total</th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance_after_process_loss,2); ?></th>
                </tr>
            	<tr>
                	<th colspan="5">Openning With Party Total Balanced</th>
                    <th align="right"><?php echo number_format($opening_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_process_loss,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance_after_process_loss,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<?  	
$html=ob_get_contents();
ob_flush();

foreach (glob("$user_id*.xls") as $filename) 
{
	@unlink($filename);
}

$name=time();
$filename=$user_id."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
ob_end_clean();
?>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$(document).ready(function(e){
	document.getElementById('btn_container').innerHTML='<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;<a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
});	
</script>
</html>
<?
exit();
}
//action_sample_balance
if($action=="action_sample_balance")
{
	echo load_html_head_contents("Balance After Process Loss", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('rpt_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<?php
	/*
	|--------------------------------------------------------------------------
	| for issue
	|--------------------------------------------------------------------------
	*/
	$sql_issue="SELECT a.BOOKING_NO, b.ID, b.CONS_QUANTITY, b.RETURN_QNTY FROM inv_issue_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND e.userid = ".$user_id." AND e.type = 2 AND e.party_id = ".$party_id." AND a.item_category=1 AND a.entry_form=3 AND a.company_id = ".$company_id." AND b.item_category=1 AND b.transaction_type=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.issue_basis = 1 AND a.issue_purpose in(4,8) ORDER BY a.BOOKING_NO DESC";
	//echo $sql_issue; die;
	$sql_issue_rslt = sql_select($sql_issue);
	$duplicate_check = array();
	foreach($sql_issue_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$party_data[$row['BOOKING_NO']]['issue_qnty'] += $row['CONS_QUANTITY'];
			$party_data[$row['BOOKING_NO']]['return_qnty'] += $row['RETURN_QNTY'];
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>"; die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for receive
	|--------------------------------------------------------------------------
	*/
	$sql_receive = "SELECT a.ID, a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED FROM inv_receive_master a, tmp_trans_id e WHERE a.id = e.id AND e.userid = ".$user_id." AND e.type = 1 AND e.party_id = ".$party_id." AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM IN(2) AND a.COMPANY_ID = ".$company_id." AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 ORDER BY a.knitting_company, a.receive_date";
	//echo $sql_receive;
	$sql_receive_rslt=sql_select($sql_receive);
	$receive_id_arr = array();
	foreach($sql_receive_rslt as $row)
	{
		if($row['ROLL_MAINTAINED'] ==1)
		{
			$receive_id_arr['roll_rcv'][$row['ID']] = $row['ID'];
		}
		else
		{
			$receive_id_arr['bulk_rcv'][$row['ID']] = $row['ID'];
		}
	}
	/*echo "<pre>";
	print_r($receive_id_arr);
	echo "</pre>";
	die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for roll receive
	|--------------------------------------------------------------------------
	*/
	$sql_roll_receive = "SELECT a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.QNTY, b.REJECT_QNTY FROM INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$company_id." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.ENTRY_FORM = 2 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['roll_rcv'], '0', 'b.mst_id')." ORDER BY a.BOOKING_NO DESC";
	//echo $sql_roll_receive;
	$sql_roll_receive_rslt = sql_select($sql_roll_receive);
	$popup_receive_id_arr = array();
	$duplicate_check = array();
	foreach($sql_roll_receive_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$expBooking = array();
			$expBooking = explode('-', $row['BOOKING_NO']);
			if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$party_data[$row['BOOKING_NO']]['fRec'] += $row['QNTY'];
				$party_data[$row['BOOKING_NO']]['rej_fab'] += $row['REJECT_QNTY'];
			}
		}
	}
	unset($sql_roll_receive_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>";
	die;*/
	
	/*
	|--------------------------------------------------------------------------
	| for bulk receive
	|--------------------------------------------------------------------------
	*/
	$sql_bulk_receive = "SELECT a.BOOKING_ID, a.BOOKING_NO, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ENTRY_FORM, a.ROLL_MAINTAINED, b.ID, b.GREY_RECEIVE_QNTY, b.REJECT_FABRIC_RECEIVE FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b WHERE a.id = b.mst_id AND a.RECEIVE_BASIS = 1 AND a.ITEM_CATEGORY = 13 AND a.ENTRY_FORM = 2 AND a.COMPANY_ID = ".$company_id." AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0".where_con_using_array($receive_id_arr['bulk_rcv'], '0', 'b.mst_id')." ORDER BY a.BOOKING_NO DESC";
	//echo $sql_bulk_receive;
	$sql_bulk_receive_rslt = sql_select($sql_bulk_receive);
	$duplicate_check = array();
	foreach($sql_bulk_receive_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$expBooking = array();
			$expBooking = explode('-', $row['BOOKING_NO']);
			if($expBooking[1] != 'fb' && $expBooking[1] != 'Fb' && $expBooking[1] != 'FB')
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$party_data[$row['BOOKING_NO']]['fRec'] += $row['GREY_RECEIVE_QNTY'];
				$party_data[$row['BOOKING_NO']]['rej_fab'] += $row['REJECT_FABRIC_RECEIVE'];
			}
		}
	}
	unset($sql_bulk_receive_rslt);
	/*echo "<pre>";
	print_r($party_data);
	echo "</pre>";
	die;*/

	/*
	|--------------------------------------------------------------------------
	| For Yarn Issue Return
	|--------------------------------------------------------------------------
	*/
	//$yarn_issue_return = "SELECT a.BOOKING_NO, b.ID, b.CONS_QUANTITY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b, tmp_trans_id e WHERE a.id=b.mst_id AND a.id = e.id AND b.mst_id = e.id AND e.userid = ".$user_id." AND e.type = 3 AND e.party_id = ".$party_id." AND a.item_category in(1) AND a.entry_form in(9) AND a.receive_basis in(1) AND b.item_category in(1) AND b.transaction_type in(4) AND b.receive_basis in(1) AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.issue_id IN(SELECT ID FROM tmp_trans_id WHERE userid = ".$user_id." AND type = 2 AND party_id = ".$party_id.")";
	
	$yarn_issue_return = "SELECT a.BOOKING_NO, b.ID, b.CONS_QUANTITY, b.CONS_REJECT_QNTY FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id AND a.item_category in(1) AND a.entry_form in(9) AND a.receive_basis in(1) AND b.item_category in(1) AND b.transaction_type in(4) AND b.receive_basis in(1) AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.issue_id IN(SELECT ID FROM tmp_trans_id WHERE userid = ".$user_id." AND type = 2 AND party_id = ".$party_id.") ORDER BY a.BOOKING_NO DESC";
	//echo $yarn_issue_return;
	$yarn_issue_return_rslt = sql_select($yarn_issue_return);
	$duplicate_check = array();
	foreach($yarn_issue_return_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$party_data[$row['BOOKING_NO']]['ret_yarn'] += $row['CONS_QUANTITY'];
			$party_data[$row['BOOKING_NO']]['rej_yarn'] += $row['CONS_REJECT_QNTY'];
		}
	}
	unset($yarn_issue_return_rslt);
	//echo "<pre>";
	//print_r($party_data);
	//echo "</pre>";
	
	/*
	|--------------------------------------------------------------------------
	| for booking information
	|--------------------------------------------------------------------------
	*/
	$bookingNoArray = array();
	$sampleBookingNoArray = array();
	foreach($party_data as $key=>$val)
	{
		$expBooking = array();
		$expBooking = explode('-', $key);
		if($expBooking[1] != 'SMN')
		{
			$bookingNoArray[$key] = $key;
		}
		else
		{
			$sampleBookingNoArray[$key] = $key;
		}
	}

	//for booking information
	/*$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME FROM WO_BOOKING_DTLS a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, LIB_BUYER d WHERE a.JOB_NO = b.JOB_NO AND a.PO_BREAK_DOWN_ID = c.ID AND b.BUYER_NAME=d.id".where_con_using_array($bookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, c.PO_NUMBER, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	$bookingInfo = array();
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['style_no'] = $row['STYLE_REF_NO'];
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'] = $row['BUYER_NAME'];
	}
	unset($sql_result);*/
	
	//for sample booking information
	$sql = "SELECT a.BOOKING_NO, d.BUYER_NAME FROM WO_NON_ORD_SAMP_BOOKING_MST a, LIB_BUYER d WHERE a.BUYER_ID=d.id".where_con_using_array($sampleBookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['buyer_name'] = $row['BUYER_NAME'];
	}
	unset($sql_result);
	
	$sql = "SELECT a.BOOKING_NO, b.STYLE_REF_NO, d.BUYER_NAME FROM WO_NON_ORD_SAMP_BOOKING_DTLS a, SAMPLE_DEVELOPMENT_MST b, LIB_BUYER d WHERE a.STYLE_ID = b.ID AND b.BUYER_NAME=d.id".where_con_using_array($sampleBookingNoArray, '1', 'a.BOOKING_NO')." GROUP BY a.BOOKING_NO, b.STYLE_REF_NO, d.BUYER_NAME";
	//echo $sql;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$bookingInfo[$row['BOOKING_NO']]['style_no'] = $row['STYLE_REF_NO'];
	}
	unset($sql_result);
	?>
</head>
<body>
    <p id="btn_container" style="margin-left:10px; margin-top:10px; margin-bottom:10px;"></p>
    <?php ob_start(); ?>
    <div align="center" id="rpt_container">
        <table width="930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"> 
            <thead>
            	<tr>
                	<th colspan="14"><?php echo $company_arr[$company_id]; ?></th>
                </tr>
                <tr>
                	<th width="30">Sl</th>
                	<th width="100">Booking No</th>
                	<th width="100">Buyer</th>
                	<th width="100">Style Ref.</th>
                	<th width="100">Yarn Issued</th>
                	<th width="100">Fabric Received</th>
                	<th width="100">Reject Fabric Received</th>
                	<th width="100">Yarn Returned</th>
                	<th width="100">Reject Yarn Returned</th>
                	<th width="100">Balance</th>
                </tr>
            </thead>
            <tbody>
				<tr style="font-weight:bold;">
                	<td colspan="7">Party Name : <?php echo $knitting_party; ?></td>
                	<td colspan="2" align="right">Opening Balanced&nbsp;</td>
                	<td align="right"><?php echo number_format($opening_balance,2); ?></td>
                </tr>
				
				<?php
				$i=0;
				foreach($party_data as $bookingNo=>$row)
				{
					$i++;
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					//$opening_balance=0;
					$yarn_issue=0;
					$yarn_returnable_qty=0;
					$dy_tx_wx_rcon=0;
					
					$yarn_issue=$row['issue_qnty'];
					$yarn_returnable_qty=$row['return_qnty'];
					
					$dy_tx_wx_rcon=$row['yarn_rec'];
					$grey_receive_qnty=$row['fRec'];
					$reject_fabric_receive=$row['rej_fab'];
					
					$yarn_return_qnty=$row['ret_yarn'];
					$yarn_return_reject_qnty=$row['rej_yarn'];
					$returnable_balance=$yarn_returnable_qty-$yarn_return_qnty;
					$balance=$yarn_issue-($dy_tx_wx_rcon+$grey_receive_qnty+$reject_fabric_receive+$yarn_return_qnty+$yarn_return_reject_qnty);
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
						<td><? echo $i; ?></td>
						<td><? echo $bookingNo; ?></td>
						<td><? echo $bookingInfo[$bookingNo]['buyer_name']; ?></td>
						<td><? echo $bookingInfo[$bookingNo]['style_no']; ?></td>                                
						<td align="right"><? echo number_format($yarn_issue,2); ?></td>
						<td align="right"><? echo number_format($grey_receive_qnty,2); ?></td>
						<td align="right"><? echo number_format($reject_fabric_receive,2); ?></td>
						<td align="right"><? echo number_format($yarn_return_qnty,2); ?></td>
						<td align="right"><? echo number_format($yarn_return_reject_qnty,2); ?></td>
						<td align="right"><? echo number_format($balance,2); ?></td>
					</tr>
					<?php
					$tot_opening_bal+=$opening_balance;
					$tot_issue+=$yarn_issue;
					$tot_receive+=$grey_receive_qnty;
					$tot_rejFab_rec+=$reject_fabric_receive;
					$tot_dy_tx_wx_rcon+=$dy_tx_wx_rcon;
					$tot_yarn_return+=$yarn_return_qnty;
					$tot_yarn_retReject+=$yarn_return_reject_qnty;
					$tot_balance+=$balance;
				}
				
				$gtot_balance = (number_format($opening_balance,2,'.','')+number_format($tot_issue,2,'.',''))-(number_format($tot_dy_tx_wx_rcon,2,'.','')+number_format($tot_receive,2,'.','')+number_format($tot_rejFab_rec,2,'.','')+number_format($tot_yarn_return,2,'.','')+number_format($tot_yarn_retReject,2,'.',''));
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="4">Total</th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($tot_balance,2); ?></th>
                </tr>
            	<tr>
                	<th colspan="3">Openning With Party Total Balanced</th>
                    <th align="right"><?php echo number_format($opening_balance,2); ?></th>
                    <th align="right"><?php echo number_format($tot_issue,2); ?></th>
                    <th align="right"><?php echo number_format($tot_receive,2); ?></th>
                    <th align="right"><?php echo number_format($tot_rejFab_rec,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_return,2); ?></th>
                    <th align="right"><?php echo number_format($tot_yarn_retReject,2); ?></th>
                    <th align="right"><?php echo number_format($gtot_balance,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
<?  	
$html=ob_get_contents();
ob_flush();

foreach (glob("$user_id*.xls") as $filename) 
{
	@unlink($filename);
}

$name=time();
$filename=$user_id."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
ob_end_clean();
?>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$(document).ready(function(e){
	document.getElementById('btn_container').innerHTML='<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;<a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
});	
</script>
</html>
<?
exit();
}
?>