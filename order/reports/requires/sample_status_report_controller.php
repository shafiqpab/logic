<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-Select-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "load_drop_down( 'requires/sample_status_report_controller', this.value, 'load_drop_down_brand', 'brand_td');");
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 70, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

//--------------------------------------------------------------------------------------------------------------------
if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
	$imge_data=sql_select("select master_tble_id,image_location from common_photo_library where form_name='sample_development' and file_type=1 and master_tble_id=$id");
	?>
	<table>
        <tr>
        <?
        foreach($imge_data as $row)
        {
			?>
			<td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
			<?
        }
        ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="ir_popup")
{
  	echo load_html_head_contents("Internal Ref Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_id').val(id);
			$('#hidden_name').val(name);
		}
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px; margin-left:10px">
            <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_name" id="hidden_name" class="text_boxes" value="">
            <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            	<? if($type==1) $tdCaption="Int. Ref/Control No"; ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                    <thead>
                        <th width="50">SL</th>
                        <th><? echo $tdCaption; ?></th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:300px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                    <?
                        $i=1;					
						if($type==1)
						{
							$internal_ref_arr=return_library_array( "select id,internal_ref from sample_development_mst where internal_ref is not null",'id','internal_ref');
							foreach($internal_ref_arr as $id=>$name)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									</td>	
									<td style="word-break:break-all"><? echo $name; ?></td>
								</tr>
								<?
								$i++;
							}
						}
                    ?>
                    </table>
                </div>
                 <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%"> 
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$sample_arr=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	
	//if(str_replace("'","",$cbo_company_name)==0) $company_name="%%"; else $company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_location_name)!=0) $locationCond=" and a.location_id=$cbo_location_name"; else $locationCond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	if(str_replace("'","",$cbo_brand)!=0) $brandCond=" and a.brand_id=$cbo_brand"; else $brandCond="";
	if(str_replace("'","",$txt_req_no)!="") $reqCond=" and a.requisition_number_prefix_num=$txt_req_no"; else $reqCond="";
	if(str_replace("'","",$txt_style)!="") $styleCond=" and a.style_ref_no=$txt_style"; else $styleCond="";
	if(str_replace("'","",$txt_req_no_id)!="") $interRef_cond=" and a.id in ($txt_req_no_id)"; else $interRef_cond="";

	/* $txt_req_no_id=str_replace("'","",$txt_req_no_id);
	if(trim($txt_req_no_id)!="") $interRef_cond=" and a.internal_ref in ($txt_req_no_id)"; else $interRef_cond=""; */
	
	//if(str_replace("'","",$cbo_comp_status)!=0) $compStatusCond=" and a.brand_id=$cbo_comp_status"; else $compStatusCond="";
	
	$dateCond=$caption=$start_date=$end_date='';
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	if($start_date!="" && $end_date!="")
	{
		if(str_replace("'","",$cbo_date_type)==1) { $dateCond=" and a.requisition_date between '$start_date' and '$end_date'"; $caption="Requisition Date: "; }
		else if(str_replace("'","",$cbo_date_type)==2) { $dateCond=" and b.delv_end_date between '$start_date' and '$end_date'"; $caption="Delivery Date: "; }
	}
	
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:1960px">
		<fieldset style="width:100%;">	
			<table width="1960px" cellspacing="0">
            	<tr class="form_caption" style="border:none;">
                    <td colspan="22" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="22" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <?=$caption.change_date_format($start_date)." To ".change_date_format($end_date); ?>
                    </td>
                </tr>
            </table>
            <table class="rpt_table" width="1960" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
                    <th width="110">Req. No</th>
					<th width="100">System Booking No.</th>
					<th width="100">IR/CN</th>
                    <th width="100">Buyer</th>
                    <th width="80">Brand</th>
                    <th width="110">Master/Style Ref.</th>
                    <th width="60">Guage</th>
                    <th width="220">Yarn Composition</th>
                    <th width="70">GMTS Qty, [Pcs]</th>
					<th width="70">Fin Fabric Qty (kg)</th>
					<th width="70">Grey Fabric Qty (kg)</th>
                    <th width="70">Req. Date</th>
                    <th width="70">Delivery Date.</th>
                    <th width="70">Actual Del. Date</th>
                    <th width="100">Sample Type</th>
                    <th width="90">Designer</th>
                    <th width="90">Programer</th>
                    <th width="70">Time</th>
                    <th width="70">Sample Weight [Lbs]</th>
                    <th width="80">Sample Status</th>
                    <th>Remark</th>
				</thead>
			</table>
			<div style="width:1960px; max-height:300px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
					$sql="select a.id,a.company_id, a.entry_form_id, a.requisition_number_prefix_num, a.requisition_number, a.buyer_name,a.internal_ref, a.brand_id, a.style_ref_no, a.requisition_date, a.remarks, b.sample_name, b.delv_end_date, b.sample_prod_qty from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id in(117,203,459) $buyer_id_cond $brandCond $reqCond $styleCond $dateCond $interRef_cond order by a.requisition_date desc";
					//echo $sql;die;
					$sqlRes=sql_select($sql);
					$sampleMstDataArr=array(); $mstIdArr=array(); $reqDataArr=array();
					foreach($sqlRes as $row)
					{
						$mstIdArr[$row[csf('id')]]=$row[csf('id')];
						$entry_form_id=$row[csf('entry_form_id')] ;
						$sampleMstDataArr[$row[csf("id")]]["data"]=$row[csf("requisition_number")].'__'.$row[csf("buyer_name")].'__'.$row[csf("brand_id")].'__'.$row[csf("style_ref_no")].'__'.$row[csf("requisition_date")].'__'.$row[csf("remarks")].'__'.$row[csf("sample_name")].'__'.$row[csf("delv_end_date")].'__'.$row[csf("internal_ref")].'__'.$row[csf("company_id")].'__'.$row[csf("id")];
						$reqDataArr[$row[csf('id')]]["reqQty"]+=$row[csf("sample_prod_qty")];

						$link_format=""; $buttonAction="";$page_path=0;
								if($entry_form_id==117) 
								{
									$link_format="'../../order/woven_order/requires/sample_requisition_controller'";
									$buttonAction="sample_requisition_print";
								}
								else if($entry_form_id==203) 
								{
									$link_format="'../../order/woven_order/requires/sample_requisition_with_booking_controller'";
									$buttonAction="sample_requisition_print";
								}
								else if($entry_form_id==449) 
								{
									$link_format="'../../order/woven_gmts/requires/sample_requisition_with_booking_controller'";
									$buttonAction="sample_requisition_print1";
								}
					}
					unset($sqlRes);
					
					$mstidCond="";
					if(count($mstIdArr)>0)
					{
						$mstid=array_chunk($mstIdArr,999, true);
						$ji=0;
						foreach($mstid as $key=>$value)
						{
							if($ji==0)
							{
								$confirmidCond=" and sample_mst_id in(".implode(",",$value).")"; 
							}
							else
							{
								$confirmidCond.=" or sample_mst_id in(".implode(",",$value).")";
							}
							$ji++;
						}
					}
                    
                    $sqlYarn="select sample_mst_id, gauge, fabric_description, required_qty from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 $mstidCond";
					//echo $sqlYarn;die;	
					$sqlYarnRes=sql_select($sqlYarn); $yarnDataArr=array();
					foreach($sqlYarnRes as $rowy)
					{
						$yarnDataArr[$rowy[csf("sample_mst_id")]]["gauge"].=','.$rowy[csf("gauge")];
						$yarnDataArr[$rowy[csf("sample_mst_id")]]["des"].='__'.$rowy[csf("fabric_description")];
					}
					unset($sqlYarnRes);

					$sqlQty="select sample_mst_id,SUM(fin_fab_qnty) as fin_fab_qnty,SUM(grey_fab_qnty) as grey_fab_qnty from sample_development_fabric_acc where form_type=1 and status_active=1 and is_deleted=0 $mstidCond group by sample_mst_id";
					//echo $sqlYarn;die;	
					$sqlQtyRes=sql_select($sqlQty); $samDataArr=array();
					foreach($sqlQtyRes as $rowy)
					{
						$samDataArr[$rowy[csf("sample_mst_id")]]["fin_fab_qnty"]+=$rowy[csf("fin_fab_qnty")];
						$samDataArr[$rowy[csf("sample_mst_id")]]["grey_fab_qnty"]+=$rowy[csf("grey_fab_qnty")];
					}
					unset($sqlQtyRes);

					$sqlBooking="select a.sample_mst_id,b.booking_no from sample_development_fabric_acc a,wo_non_ord_samp_booking_dtls b where a.form_type=1 and  a.id=b.dtls_id and  b.style_id=a.sample_mst_id  and a.determination_id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 $mstidCond ";
					//echo $sqlYarn;die;	
					$sqlBookingRes=sql_select($sqlBooking); $bookingDataArr=array();
					foreach($sqlBookingRes as $rowy)
					{
						$bookingDataArr[$rowy[csf("sample_mst_id")]]["booking_no"]=$rowy[csf("booking_no")];
					}
					unset($sqlBookingRes);
					
					$sqlTnaComm="select ORDER_ID, TASK_ID, COMMENTS from TNA_PROGRESS_COMMENTS where task_type=5 ".where_con_using_array($mstIdArr,0,'ORDER_ID')."";
					//echo $sql; die;
					$tnaCommArray=sql_select( $sqlTnaComm ); $tnaTimeLbsDataArr=array();
					foreach ($tnaCommArray as $rows)
					{
						$tnaTimeLbsDataArr[$rows[ORDER_ID]][$rows[TASK_ID]][$rows[COMMENTS]]=$rows[COMMENTS];	
					}
					unset($tnaCommArray);
					
					$tna_sql="select ID, PO_NUMBER_ID, TASK_NUMBER, ACTUAL_START_DATE from tna_process_mst where task_type=5 ".where_con_using_array($mstIdArr,0,'PO_NUMBER_ID')." and task_number=9 and is_deleted=0 and status_active=1";	
					 //echo $tna_sql;
					$tnaDataArray=sql_select( $tna_sql ); $tnaDateArr=array();
					foreach ($tnaDataArray as $row){
						$tnaDateArr[$row[PO_NUMBER_ID]]=$row[ACTUAL_START_DATE];
					}
					unset($tnaDataArray);
					$comp_status_arr=array(1=>"Pending",2=>"Complete");
					
					$i=1; $tot_rows=0;
					foreach($sampleMstDataArr as $smpid=>$rowd)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$exData=explode("__",$rowd["data"]);
						$reqNo=$buyerId=$brandId=$styleRef=$reqDate=$remarks=$sampleName=$delvDate=$inetrRef=$companyId=$rowId="";
						//$row[csf("requisition_number")].'__'.$row[csf("buyer_name")].'__'.$row[csf("brand_id")].'__'.$row[csf("style_ref_no")].'__'.$row[csf("requisition_date")].'__'.$row[csf("remarks")].'__'.$row[csf("sample_name")].'__'.$row[csf("delv_end_date")];
						
						$reqNo=$exData[0];
						$buyerId=$exData[1];
						$brandId=$exData[2];
						$styleRef=$exData[3];
						$reqDate=$exData[4];
						$remarks=$exData[5];
						$sampleName=$exData[6];
						$delvDate=$exData[7];
						$inetrRef=$exData[8];
						$companyId=$exData[9];
						$rowId=$exData[10];
						
						$exGauge=array_filter(array_unique(explode(",",$yarnDataArr[$smpid]["gauge"])));
						$gaugeName="";
						foreach($exGauge as $gid)
						{
							if($gaugeName=="") $gaugeName=$gauge_arr[$gid]; else $gaugeName.=', '.$gauge_arr[$gid];
						}
						
						$exYdes=array_filter(array_unique(explode("__",$yarnDataArr[$smpid]["des"])));
						$yarnDes="";
						foreach($exYdes as $ydes)
						{
							if($yarnDes=="") $yarnDes=$ydes; else $yarnDes.=', '.$ydes;
						}
						
						$reqQty=$reqQtyLbs=0;
						$reqQty=$reqDataArr[$smpid]["reqQty"];
						$finQty=$samDataArr[$smpid]["fin_fab_qnty"];
						$greyQty=$samDataArr[$smpid]["grey_fab_qnty"];
						$reqQtyLbs=array_sum($tnaTimeLbsDataArr[$smpid][12]);
						$booking_no=implode(",",array_filter(array_unique(explode(",",$bookingDataArr[$smpid]["booking_no"]))));
						?>
                        <tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('tr_d<?=$i; ?>','<?=$bgcolor;?>');" id="tr_d<?=$i; ?>">
                            <td width="30" align="center" title="<?=$smpid; ?>"><?=$i; ?></td>
                            <td width="110" style="word-break:break-all"><?=$reqNo; ?></td>
							<td width="100" align="center"><p><a href='##' style='color:#000' onClick="print_report(<? echo $companyId; ?>+'*'+<? echo $rowId; ?>+'*'+<? echo $page_path; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $booking_no; ?></a></p></td>
							<td width="100" style="word-break:break-all"><?=$inetrRef; ?></td>
                            <td width="100" style="word-break:break-all"><?=$buyer_arr[$buyerId]; ?></td>
                            <td width="80" style="word-break:break-all"><?=$brand_arr[$brandId]; ?></td>
                            <td width="110" style="word-break:break-all"><?=$styleRef; ?></td>
                            <td width="60" style="word-break:break-all"><?=$gaugeName; ?></td>
                            <td width="220" style="word-break:break-all"><?=$yarnDes; ?></td>
                            <td width="70" align="center"><?=number_format($reqQty,0,".",""); ?></td>
							<td width="70" align="center"><?=number_format($finQty,0,".",""); ?></td>
							<td width="70" align="center"><?=number_format($greyQty,0,".",""); ?></td>
                            <td width="70" style="word-break:break-all"><?=change_date_format($reqDate); ?></td>
                            <td width="70" style="word-break:break-all"><?=change_date_format($delvDate); ?></td>
                            <td width="70" style="word-break:break-all"><?=change_date_format($tnaDateArr[$smpid]); ?></td>
                            <td width="100" style="word-break:break-all"><?=$sample_arr[$sampleName]; ?></td>
                            <td width="90" style="word-break:break-all"><?=implode(',',$tnaTimeLbsDataArr[$smpid][9]); ?></td>
                            <td width="90" style="word-break:break-all"><?=implode(',',$tnaTimeLbsDataArr[$smpid][10]); ?></td>
                            <td width="70" align="center"><?=implode(',',$tnaTimeLbsDataArr[$smpid][11]); ?></td>
                            <td width="70" align="right"><?=$reqQtyLbs; ?></td>
                            <td width="80" style="word-break:break-all"><?=($tnaDateArr[$smpid])?$comp_status_arr[2]:$comp_status_arr[1]; ?></td>
                            <td style="word-break:break-all"><?=$remarks; ?></td>
                        </tr>
                        <?
						$i++; $tot_rows++;
						$totReqQty+=$reqQty;
						$totfinQty+=$finQty;
						$totgreyQty+=$greyQty;
						$totReqQtylbs+=$reqQtyLbs;
					}
				?>
				</table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1960" rules="all">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="110">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="220">&nbsp;</th>
                        <th width="70" id="td_reqQty"><?=number_format($totReqQty,0,".",""); ?></th>
						<th width="70" id="td_finQty"><?=number_format($totfinQty,0,".",""); ?></th>
						<th width="70" id="td_greyQty"><?=number_format($totgreyQty,0,".",""); ?></th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70" id="value_reqQtyLbs"><?=number_format($totReqQtylbs,2,".",""); ?></th>
                        <th width="80">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
			</fieldset>
		</div>
	<?
	}
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}
disconnect($con);
?>