<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
require_once('../../mailer/class.phpmailer.php');
include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$dealing_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0",'id','team_member_name');
$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/sample_requisition_acknowledge_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_acknowledge_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); 

	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	
	echo create_drop_down( "cbo_brand_id", 110, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
 	echo create_drop_down( "cbo_season_name", 100, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	 
	extract(check_magic_quote_gpc( $_REQUEST )); 
	// $sample_booking_print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=2 and report_id=3 and is_deleted=0 and status_active=1 $cbo_company_cond", "template_name", "format_id");
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
 	$txt_style_ref=str_replace("'","",$txt_style_ref);
 	$txt_requisition_no=str_replace("'","",$txt_requisition_no);
	$txt_st_date=str_replace("'","",$txt_st_date);
	$txt_end_date=str_replace("'","",$txt_end_date);
 	$approval_type=str_replace("'","",$cbo_approval_type);
 	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
 	$variable_setting_app= "select b.approval_need as approval_need  from approval_setup_mst a ,approval_setup_dtls b where a.id=b.mst_id and b.page_id=26 and a.company_id ='$company_name' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.setup_date=(select max(setup_date) from approval_setup_mst where company_id='$company_name' and status_active=1 and is_deleted=0 )";
	$variable_setting_app_sql=sql_select($variable_setting_app);
	$variable_app_value=$variable_setting_app_sql[0][csf("approval_need")];
	$variable_cond=($variable_app_value==1)? " and is_approved=1 and req_ready_to_approved=1 and id in( select mst_id from approval_history where entry_form=25)  " : " and is_approved in (0,1) and req_ready_to_approved=1 ";
	$variable_cond2=($variable_app_value==1)? " and a.is_approved=1 and a.req_ready_to_approved=1 and a.id in( select mst_id from approval_history where entry_form=25)  " : " and a.is_approved in (0,1) and a.req_ready_to_approved=1 ";

  	if(str_replace("'","",$cbo_buyer_name)==0) 
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{
				$buyer_id_cond="";
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$cbo_buyer_name"; 
		$buyer_id_cond2=" and a.buyer_name=$cbo_buyer_name"; 
	}
	$season_cond="";
	if($cbo_season_name>0)
	{
		$season_cond=" and (season='$cbo_season_name' or (season is null and season_buyer_wise='$cbo_season_name')) ";
		$season_cond2=" and (a.season='$cbo_season_name' or (a.season is null and a.season_buyer_wise='$cbo_season_name')) ";
	}
	
	if (trim($txt_style_ref)!="") $style_id_cond=" and style_ref_no like '%$txt_style_ref%' "; else $style_id_cond="";
	if (trim($txt_requisition_no)!="") $requisition_no_cond=" and requisition_number_prefix_num ='$txt_requisition_no' "; else $requisition_no_cond="";
	$date_cond=""; $date_cond2="";
	if($db_type==2)
	{
		if (trim($txt_st_date)!="" && trim($txt_end_date!="")) $date_cond=" and requisition_date between '$txt_st_date' and '$txt_end_date' "; else $date_cond="";
		if (trim($txt_st_date)!="" && trim($txt_end_date!="")) $date_cond2=" and a.requisition_date between '$txt_st_date' and '$txt_end_date' "; else $date_cond2="";
	}

	$brand_cond1="";
	$brand_cond2="";
	if(!empty($cbo_brand_id))
	{
		$brand_cond1=" and brand_id=$cbo_brand_id";
		$brand_cond2=" and a.brand_id=$cbo_brand_id";
	}
		 
	 $style_id_cond2=str_replace("style_ref_no","a.style_ref_no",$style_id_cond);
	 $style_wise_booking = return_library_array("SELECT  style_id, booking_no_prefix_num   from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no and a.status_active=1 and b.is_deleted=0 ", "style_id", "booking_no_prefix_num");
	
	 $style_wise_booking_no = return_library_array("SELECT  b.style_id, a.booking_no  as booking from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no and a.status_active=1 and b.is_deleted=0 ", "style_id", "booking");

	 $style_wise_booking_approved = return_library_array("SELECT b.style_id, MAX (c.approved_no) AS approved_no
	 FROM wo_non_ord_samp_booking_mst   a,
		  wo_non_ord_samp_booking_dtls  b,
		  approval_history              c
	WHERE     a.booking_no = b.booking_no
		  AND a.id = c.mst_id
		  AND a.status_active = 1
		  AND b.is_deleted = 0
	group by style_id,approved_no ", "style_id", "approved_no");

		
	 $style_wise_booking_item = return_library_array("SELECT  b.style_id, a.item_category   from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b  where a.a.id = c.mst_id and a.status_active=1 and b.is_deleted=0 ", "style_id", "item_category");
	 $style_wise_booking_style = return_library_array("SELECT  style_id, booking_no_prefix_num   from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no and a.status_active=1 and b.is_deleted=0 ", "style_id", "booking_no_prefix_num"); 

	 $requisition_no_cond2=str_replace("requisition_number_prefix_num","a.requisition_number_prefix_num",$requisition_no_cond);
 	 $sql_req="SELECT entry_form_id, id, company_id, requisition_date, requisition_number_prefix_num, style_ref_no, buyer_name, season, season_buyer_wise, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, status_active, is_deleted, refusing_cause,brand_id	 from sample_development_mst where entry_form_id in (117,203,449) and company_id=$company_name $buyer_id_cond  $season_cond $style_id_cond $requisition_no_cond $date_cond $variable_cond $brand_cond1  and status_active=1 and is_deleted=0 and is_acknowledge=0 order by entry_form_id desc, id desc";
 	
 	$sql_req2="SELECT distinct(a.id), a.entry_form_id, a.company_id, a.requisition_date, a.requisition_number_prefix_num, a.style_ref_no, a.buyer_name, a.season, a.season_buyer_wise, a.product_dept, a.dealing_marchant, a.agent_name, a.buyer_ref, a.bh_merchant, a.estimated_shipdate, a.remarks, a.status_active, a.is_deleted, a.refusing_cause,b.sample_plan, b.confirm_del_end_date,a.brand_id from sample_development_mst a,sample_requisition_acknowledge b where a.entry_form_id in (117,203,449) and a.company_id=$company_name $style_id_cond2 $requisition_no_cond2 $buyer_id_cond2 $season_cond2 $brand_cond2 $date_cond2 and a.is_approved in (0,1) and a.is_acknowledge=1  and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and a.id=b.sample_mst_id and b.acknowledge_status =1 order by a.id desc";
 	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active =1 and is_deleted=0 ", "id", "brand_name"  );
	// echo $sql_req2;
	?>
	<script type="text/javascript">

	function openmypage_sample_plan(page_link,title,req_id)
	{
			var plan_data=$('#sampleplandata_'+req_id).val();
			var page_link = page_link + "&req_id="+req_id+"&sample_data="+plan_data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=280px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var sam_plan=this.contentDoc.getElementById("txt_sample_plan").value;
 				if (sam_plan!="")
				{
					$("#txtSampleplan_"+req_id).val( sam_plan );
					$("#sampleplandata_"+req_id).val( sam_plan );
					freeze_window(5);
					release_freezing();
				}
			}
	 }


	function openmypage_refusing_cause(page_link,title,req_id)
	{
			var page_link = page_link + "&req_id="+req_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=280px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
 				if (cause!="")
				{
					$("#txtCause_"+req_id).val( cause );
					freeze_window(5);
					release_freezing();
				}
			}
	 }

	 

	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1050px; margin-top:10px">
        <legend>Sample Requisition Acknowledge</legend>	
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1320" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="60">Booking No</th>
                    <th width="70">Date</th>
                    <th width="120">Company</th>
                    <th width="60">Dealing <br>Merchant</th>
                    <th width="90">Buyer</th>
                    <th width="100">Brand</th>
                    <th width="60">Season</th>
                    <th width="70">Style Ref</th>
                    <th width="70">Sample Qty</th>
                    <th width="50">Fabric Qty</th>
                    <th width="80">Embellishment</th>
					<th width="80">Sample Plan</th>
                    <th width="90">Confirm Del. End Date</th>
                    <th>Refusing Cause</th>
                </thead>
            </table>
            <div style="width:1320px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <? 
                            $i=1;
                            $nameArray="";
                            $appid_sql= sql_select("SELECT id, mst_id from approval_history where entry_form='25'");
							$app_id_arr=array();
							foreach ($variable as $row) {
								$app_id_arr[$row[csf('mst_id')]] = $row[csf('id')];
							}
                            if($approval_type==0)
                            {
 								$nameArray=sql_select($sql_req);
 							}
 							else
 							{
 								$nameArray=sql_select($sql_req2);
 							}
 							$sampQty=sql_select("SELECT b.sample_mst_id,sum(b.sample_prod_qty) as sd from sample_development_mst a join  sample_development_dtls b on a.id=b.sample_mst_id where  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and b.entry_form_id in (117,203,449) group by b.sample_mst_id");
 							foreach ($sampQty as $val)
							{
								$samplQtyArr[$val[csf('sample_mst_id')]]=$val[csf('sd')];
							}
							 $reqQty=sql_select("SELECT b.sample_mst_id,sum(b.required_qty) as rq from sample_development_mst a join sample_development_fabric_acc b on a.id=b.sample_mst_id where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and b.form_type=1 group by b.sample_mst_id");
							 foreach ($reqQty as $Reqval)
							{
								$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
							}

							$emb_sel=sql_select("SELECT b.sample_mst_id,count(b.id) as ide from sample_development_mst a join sample_development_fabric_acc b on a.id=b.sample_mst_id where a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0 group by b.sample_mst_id");
							foreach($emb_sel as $embVal)
							{
								$embArr[$embVal[csf('sample_mst_id')]]=$embVal[csf('ide')];
							}

                            foreach ($nameArray as $row)
                            { 
                            	$id=$row[csf('id')] ;
                            	$entry_form_id=$row[csf('entry_form_id')] ;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=''; $app_id='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									//if($db_type==0) $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='25' order by id desc limit 0,1");
									
									//else if($db_type==2) $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='25' and rownum<2 order by id desc");
									$app_id=$app_id_arr[$row[csf('id')]];
									$value=$row[csf('id')]."**".$app_id;
								}
								$booking_no=$style_wise_booking[$id];
								$booking=$style_wise_booking_no[$id];
								$is_approved=$style_wise_booking_approved[$id];
								$item_catagory=$style_wise_booking_item[$id];
								$styleId=$style_wise_booking_style[$id];
								
								$link_format=""; $buttonAction="";
								if($entry_form_id==117) 
								{
									$link_format="'../order/woven_order/requires/sample_requisition_controller'";
									$buttonAction="sample_requisition_print";
								}
								else if($entry_form_id==203) 
								{
									$link_format="'../order/woven_order/requires/sample_requisition_with_booking_controller'";
									$buttonAction="sample_requisition_print";
								}
								else if($entry_form_id==449) 
								{
									$link_format="'../order/woven_gmts/requires/sample_requisition_with_booking_controller'";
									$buttonAction="sample_requisition_print1";
								}
								$seasonName="";
								
								if($row[csf('season')]!="") $seasonName=$season_arr[$row[csf('season')]];
								else $seasonName=$season_arr[$row[csf('season_buyer_wise')]];
								$page_path=0;

								if($cbo_company!=0) $cbo_company_cond="and template_name=$cbo_company_name";else $cbo_company_cond="";
									//$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
										$dying_print_report_format_aop=return_field_value("format_id"," lib_report_template"," module_id=2 and report_id=90 and is_deleted=0 and status_active=1 $cbo_company_cond");
										//echo $dying_print_report_format_aop;
										$wvnreportArr= explode(',',$dying_print_report_format_aop);
										$wvnreport=$wvnreportArr[0];
										if($wvnreport==10){$reporAction="show_fabric_booking_report";}
										else if($wvnreport==17){$reporAction="show_fabric_booking_report_barnali";}
										else if($wvnreport==61){$reporAction="show_fabric_booking_report_micro";}

								?>
								</td></tr>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle"><p>
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row--> </p>
                                    </td>   
									<td width="40" align="center"><p><? echo $i; ?></p></td>
									<td width="80" align="center"><p><a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_id')]; ?>+'*'+<? echo $row[csf('id')]; ?>+'*'+<? echo $page_path; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $row[csf('requisition_number_prefix_num')]; ?></a></p></td>

									<td width="60" align="center"><p><a href='#' style='color:#000' onClick="generate_fabric_booking_report( '<? 
									echo $reporAction; ?>','<? echo $booking; ?>', '<? echo $row[csf('company_id')] ;?>', '<? echo $is_approved; ?>', '<? echo $item_catagory; ?>', '<? echo $id; ?>')"><? echo $booking_no; ?></a></p></td>


                                    <td width="70"><p><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></p></td>
									<td width="120" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
									<td width="60" align="center"><p><? echo $dealing_arr[$row[csf('dealing_marchant')]]; ?></p> </td>
									<td width="90" align="center"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="100" align="center"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
									<td width="60" align="center"><p><?=$seasonName; ?></p></td>
									<td width="70" align="center"><p><?=$row[csf('style_ref_no')]; ?></p></td>
									<td width="70" align="right"><p><? echo $samplQtyArr[$row[csf('id')]]; ?></p></td>
									<td width="50" align="right"><p><? echo  $reqQtyArr[$row[csf('id')]];?></p></td>
									<td width="80" align="center"><p><? if($embArr[$row[csf('id')]]>0) echo "&nbsp; YES"; else echo "&nbsp; NO"; ?></p></td>
									<td width="80"><p>
										<input style="width:70px;" type="text" class="text_boxes"  name="txtSampleplan_<? echo $row[csf('id')];?>" id="txtSampleplan_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_sample_plan('requires/sample_requisition_acknowledge_controller.php?action=sample_plan_popup','Sample Plan','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('sample_plan')];?>"/>
										<input type="hidden" id="sampleplandata_<? echo $row[csf('id')];?>" value="<? echo $row[csf('sample_plan')];?>"> </p>
									</td>
									<td width="90" align="center"><p>
									<input type="text" name="txt_confirm_del_end_date_<? echo $i;?>" id="txt_confirm_del_end_date_<? echo $i;?>" class="datepicker" placeholder="Select Date" autocomplete="off" readonly style="width:80px;" value="<?= change_date_format($row['CONFIRM_DEL_END_DATE']); ?>"  /> </p>
									</td>
									<td> <p><input style="width:80px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/sample_requisition_acknowledge_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></p></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
				<tr>
							<td  align="left" width="50"> </td>	
							<td  align="left" width="40"> </td>
							<td  align="left" width="80"> </td>
					        <td  align="left" width="60"> </td>
						    <td  align="left" width="70"> </td>
							<td  align="left" width="120"> </td>
							<td  align="left" width="60"> </td>
							<td  align="left" width="90"> </td>
							<td  align="left" width="100"> </td>
							<td  align="left" width="60"> </td>
						 	<td  width="70" align="right" ><b>Sample Qty Total</b></td>
                 		    <td  align="center" width="70" id="sample_qty"></td>						   
						    <td  align="left" width="50"> </td>
							<td  align="left" width="80"> </td>
							<td  align="left" width="80"> </td>
						    <td  align="left" width="90"> </td>
						    <td  align="left" width="110"> </td>
						

				</tr>
			</table>
				
            </div>
	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" style="margin-left: -18px;">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="15" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Acknowledge"; else echo "Acknowledge"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>  
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>       
	<?
	exit();	
}

if ($action=="approve")
{
	$process = array( &$_POST ); 
	extract(check_magic_quote_gpc( $_REQUEST )); 
	//extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$sample_plandata_arr=array();
	for ($i=1;$i<=$total_tr;$i++)
	{
		$plandata="sampleplan_".$i;
		$plandata_arr=explode("_",str_replace("'",'',$$plandata));
		if($plandata_arr[0]!=''){
			$sample_plandata_arr[$plandata_arr[1]]=$plandata_arr[0];
		}
	}
	/* echo "10**<pre>";
	print_r($sample_plandata_arr); die; */
	
	$msg=''; $flag=''; $response='';
	$ready_to_approve_mess='';
	
	if($approval_type==0)
	{
 		//,unacknowledge_by,unacknowledge_date
		 $id_mst=return_next_id( "id", "sample_requisition_acknowledge", 1 ) ;
		// echo '34='.$id_mst.'SDD';die;
 		$req=explode(",", $req_nos);
		 $confirm_del_end_array=explode(",", $confirm_del_end_date);
		 $o=0;
		 foreach ($confirm_del_end_array as $value) {
			$confirm_data_arr = explode("_", $value);
			if($db_type==0)
			{
				$confirm_del_end_arr[$confirm_data_arr[1]][$o]="'".change_date_format($confirm_data_arr[0],"yyyy-mm-dd","")."'";
			}
			else if($db_type==2)
			{
				$confirm_del_end_arr[$confirm_data_arr[1]][$o]="'".change_date_format($confirm_data_arr[0],"","",1)."'";				
			}
			
			//$o++;	
		 }
		 $sample_plan_array=explode(",", $sample_plan_date);
		 $s=0;
		 foreach ($sample_plan_array as $value) {
			$sample_data_arr = explode("_", $value);
			$sample_plan_arr[$sample_data_arr[1]][$s]="'".$sample_data_arr[0]."'";				
		 }
		
		 $sql_check="select requisition_number from  sample_development_mst where id in ($req_nos) and  status_active=1 and req_ready_to_approved=2";
		 $check_res=sql_select($sql_check);
		 if(count($check_res))
		 {
		 	
		 	foreach ($check_res as $row) {
		 		
		 		if(!empty($ready_to_approve_mess))
		 		{
		 			$ready_to_approve_mess.=",".$row[csf('requisition_number')];
		 		}
		 		else
		 		{
		 			$ready_to_approve_mess=$row[csf('requisition_number')];
		 		}
		 	}
		 }
		 $field_array="id, sample_mst_id, requisition_number, company_id, location_id, buyer_name, style_ref_no, agent_name, dealing_marchant, entry_form, sample_plan, inserted_by, insert_date, status_active, is_deleted, acknowledge_status";
		 
		$userId=$_SESSION['logic_erp']['user_id'];
		$sql_sample_req="select id, company_id, requisition_number, location_id, buyer_name, style_ref_no, agent_name, dealing_marchant, sample_stage_id, quotation_id from sample_development_mst where id in ($req_nos)";
		$result_sample_req=sql_select($sql_sample_req);
		foreach($result_sample_req as $row)
		{
			$req_arr[$row[csf('id')]]['req_no']=$row[csf('requisition_number')];
			$req_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$req_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];
			$req_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$req_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$req_arr[$row[csf('id')]]['agent_name']=$row[csf('agent_name')];
			$req_arr[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$req_arr[$row[csf('id')]]['sampleplan']=$sample_plandata_arr[$row[csf('id')]];
		}
		// echo "10**<pre>"; print_r($req_arr); die;
		unset($result_sample_req);
		
		foreach($req_arr as $req_id=>$val)
		{
			$sample_req_no=$req_arr[$req_id]['req_no'];
			$company_id=$req_arr[$req_id]['company_id'];
			$location_id=$req_arr[$req_id]['location_id'];
			$buyer_id=$req_arr[$req_id]['buyer_name'];
			$style_ref=$req_arr[$req_id]['style_ref_no'];
			$agent_name=$req_arr[$req_id]['agent_name'];
			$dealing_marchant=$req_arr[$req_id]['dealing_marchant'];
			$sampleplan=$req_arr[$req_id]['sampleplan'];
			
			if($data_array!="") $data_array.=",";
				$data_array.="(".$id_mst.",".$req_id.",'".$sample_req_no."',".$company_id.",".$location_id.",".$buyer_id.",'".$style_ref."','".$agent_name."','".$dealing_marchant."',54,'".$sampleplan."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,1)";
				
			//$acknowledge_id_arr[$id_mst]=$id_mst;
			$id_mst=$id_mst+1;
		}
		// echo "10=**insert into sample_requisition_acknowledge (".$field_array.") values ".$data_array;die;
		$flag=0;
		$rID1=sql_insert("sample_requisition_acknowledge",$field_array,$data_array,1);
		if($rID1){$flag=1;}else{$flag=0;}

		 	
		$rID2=sql_multirow_update("sample_development_mst","is_acknowledge",1,"id",$req_nos,0);


		if(count($req)>0){
			$field_array_up="confirm_del_end_date";
			// echo bulk_update_sql_statement("sample_requisition_acknowledge", "sample_mst_id",$field_array_up,$confirm_del_end_arr,$sample_plan_arr,$req );die;
			$rID_ref_update=execute_query(bulk_update_sql_statement("sample_requisition_acknowledge", "sample_mst_id",$field_array_up,$confirm_del_end_arr,$req ));
			$flag=1;
		}
		//echo $rID_ref_update;die;
		//$rID3 = sql_multirow_updatess("sample_requisition_acknowledge","confirm_del_end_date",$conf_del_data_arr,"sample_mst_id",$req_nos,0);
 		  //echo "10**=".$rID1.'='.$rID2.'='.$rID_ref_update.'='.$ready_to_approve_mess.'='.$flag;die;
		 
		if($rID1 && $rID2 && $rID_ref_update && empty($ready_to_approve_mess)) $flag=1; else $flag=0; 
		  
 		//if($flag==1) $msg='19'; else $msg='21';
		if($flag==1) $msg='32'; else $msg='34';
	}
	else
	{
		$field_array_up="acknowledge_status*unacknowledge_by*unacknowledge_date*sample_plan";
		$req_nos = explode(',',$req_nos); 
		$data_value="";
		foreach($req_nos as $vl)
		{
			$arr2=explode('**',$vl);
			if($data_value=="") $data_value.=$arr2[0];else $data_value .=','.$arr2[0];
			$req_id=str_replace("'",'',$arr2[0]);
			$id_arr[]=$req_id;
			$data_array_up[str_replace("'",'',$arr2[0])] =explode(",",("0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$sample_plandata_arr[$req_id]."'"));
		}
		$receive_mrr=0;
		$sqlre=sql_select("select a.recv_number from inv_receive_master a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.style_id in ($data_value) and a.booking_without_order=1 and a.entry_form=2 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$data_value)."**".$receive_mrr;
			disconnect($con); die;
		}


		//$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//$rID=sql_multirow_update("sample_requisition_acknowledge","acknowledge_status*unacknowledge_by*unacknowledge_date",'0*'.$data,"sample_mst_id",$data_value,0);
		/* echo "10**<pre>";
		print_r($data_array_up); die; */
		$rID=execute_query(bulk_update_sql_statement( "sample_requisition_acknowledge", "sample_mst_id", $field_array_up, $data_array_up, $id_arr ));

		$rID2=sql_multirow_update("sample_development_mst","is_acknowledge*req_ready_to_approved","0*0","id",$data_value,0);

  		if($rID && $rID2) $flag=1; else $flag=0;
 		if($flag==1) $msg='33'; else $msg='35';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response."**".$ready_to_approve_mess;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response."**".$ready_to_approve_mess;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response."**".$ready_to_approve_mess;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response."**".$ready_to_approve_mess;
		}
	}
	disconnect($con);
	die;
}

if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	$causes=return_field_value("refusing_cause","sample_development_mst","status_active=1 and is_deleted=0 and id=$req_id ",""); 
	?>
    <script>
 	var permission='<? echo $permission; ?>';
 	var is_cause_exists='<? echo $causes; ?>';

	function set_values( cause )
	{
		document.getElementById('txt_refusing_cause').value=document.getElementById('txt_refusing_cause').value;  
		parent.emailwindow.hide();
	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var req_id=$("#hidden_req_id").val();
  		if (form_validation('txt_refusing_cause','Refusing Cause')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&req_id="+req_id;
 			freeze_window(operation);
			http.open("POST","sample_requisition_acknowledge_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	
	function check_button_status()
	{
		if(is_cause_exists)
	 	{
	 		set_button_status(1, permission, 'fnc_cause_info',1);
	 	}
	}

	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4) 
		{  
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				alert("data saved successfully");
				set_button_status(1, permission, 'fnc_cause_info',1);
			}
			if(response[0]==1)
			{
				alert("data Updated successfully");
				set_button_status(1, permission, 'fnc_cause_info',1);
			}
			release_freezing();
		}
	}

    </script>
    <body  onload="set_hotkey();check_button_status();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
	<fieldset style="width:470px;">
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Refusing Cause</td>
					<td >
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<? echo $causes;?>" />
						<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
 					</td>				
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>				
				</tr>
		   </table>
			</form> 
		</fieldset>	
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}

if($action=="sample_plan_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Plan Info","../../", 1, 1, $unicode);
	//echo $sample_data; die;
	$sample_plans=explode("**",$sample_data);
	?>
    <script>
 	var permission='<? echo $permission; ?>';
 	//var is_sample_plan='<? //echo $sample_plans; ?>';
	/*  function set_values( sam_plan )
	{
		var row_num=$('#tbl_list tr').length-1;
		if (row_num!=sam_plan)
		{
			return false;
		}
		var samp_plan_srt=document.getElementById('cbo_sample_name').value+'**'+document.getElementById('txt_pattern_date').value+'**'+document.getElementById('txt_pattern_date').value+'**'+document.getElementById('txt_cutting_date').value+'**'+document.getElementById('txt_sewing_date').value+'**'+document.getElementById('txt_wash_send_date').value+'**'+document.getElementById('txt_wash_rec_date').value+'**'+document.getElementById('txt_finishing_date').value;
		document.getElementById('txt_sample_plan').value=samp_plan_srt;
		parent.emailwindow.hide();
	} */
/* 	function set_values( sam_plan )
	{
		var samp_plan_srt=document.getElementById('cbo_sample_name').value+'**'+document.getElementById('txt_pattern_date').value+'**'+document.getElementById('txt_pattern_date').value+'**'+document.getElementById('txt_cutting_date').value+'**'+document.getElementById('txt_sewing_date').value+'**'+document.getElementById('txt_wash_send_date').value+'**'+document.getElementById('txt_wash_rec_date').value+'**'+document.getElementById('txt_finishing_date').value;
		document.getElementById('txt_sample_plan').value=samp_plan_srt;
		parent.emailwindow.hide();
	} */ 
	
	const fn_add_row = rowId =>{
		console.clear();
		console.log(`rowId = ${rowId}`);
		rowId++;
		
		$("#tbl_list tr:last").clone().find("input,select").each(function(){
			var classes = $(this).attr('class');
			console.log(`classes : ${classes}`);
			if(classes!=undefined &&  classes.includes('datepicker'))
			{
				$(this).trigger('change');
			}
			$(this).attr({
			'id': function(_, id) {
				 	var id=id.split("_"); 
					var id_new = "";
					id.forEach((val,index,arr)=>{
						if(index < arr.length - 1) 
						{
							if(id_new.length > 0 ) id_new = id_new + "_";
							id_new = id_new+val;
						}
					});
					return id_new+"_"+rowId;
					//return id[0] +"_"+ rowId 
				},
			'name': function(_, name) { 
				var name=name.split("_"); 
				var name_new = "";
				name.forEach((val,index,arr)=>{
					if(index < arr.length - 1) 
					{
						if(name_new.length > 0 ) name_new = name_new + "_";
						name_new = name_new+val;
					}
				});
				return name_new+"_"+rowId;
			},
			'value': function(_, value) { 
					console.log($(this).attr("type"));
					if($(this).attr("type") == "select" || $(this).attr("type") == undefined){
						console.log($(this).attr("type"));
						return 0;
					}  
					return "" ;
				},
				
				//$(this).trigger('change');
				
			});
			

			}).end().appendTo("#tbl_list");
			//$('#txt_pattern_date_'+rowId).val('');
			$('#txt_pattern_date_'+rowId).removeAttr("class").attr("class","datepicker");
			$('#txt_cutting_date_'+rowId).removeAttr("class").attr("class","datepicker");
			$('#txt_sewing_date_'+rowId).removeAttr("class").attr("class","datepicker");
			$('#txt_wash_send_date_'+rowId).removeAttr("class").attr("class","datepicker");
			$('#txt_wash_rec_date_'+rowId).removeAttr("class").attr("class","datepicker");
			$('#txt_finishing_date_'+rowId).removeAttr("class").attr("class","datepicker");
			
			
				$('#increaseset_'+rowId).removeAttr("value").attr("value","+");
				$('#decreaseset_'+rowId).removeAttr("value").attr("value","-");
				$('#increaseset_'+rowId).removeAttr("onclick").attr("onclick","fn_add_row("+rowId+");");
				$('#decreaseset_'+rowId).removeAttr("onclick").attr("onclick","fn_deleteRow("+rowId+");");
				set_all_onclick();
	}

	function fn_deleteRow(rowNo)
    {

              var k=rowNo-1;

              $("#tbl_list tr:eq("+k+")").remove();
               var numRow = $('#tbl_list tr').length;

				for(i = rowNo;i <= numRow;i++)
                {
                	//$('#txtSL_'+(i-1)).val(i);
                	$("#tbl_list tr:eq("+rowId+")").find("input,select").each(function() {
                		$('#txtSL_'+(rowId-1)).val(rowId);
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ rowId },
						'value': function(_, value) { return value }
					});
					$("#tbl_list tr:eq("+rowId+")").removeAttr('id').attr('id','row_'+rowId);
					$('#increaseset_'+rowId).removeAttr("onClick").attr("onClick","fn_add_row("+rowId+");");
					$('#decreaseset_'+rowId).removeAttr("onClick").attr("onClick","fn_deleteRow("+rowId+");");


					});




                }
                for(rowId=1;rowId<=numRow;rowId++)
                {
                 	$('#txtSL_'+(rowId)).val(rowId);
                }



    }
	function fnc_close(sam_plan)
		{
			var rowCount = $('#tbl_list tr').length;
			//alert( rowCount );return;
			var breck_down_data="";
			for(var i=1; i<=rowCount; i++)
			{
				if(breck_down_data=="")
				{
					breck_down_data+=$('#cbo_sample_name_'+i).val()+'**'+$('#txt_pattern_date_'+i).val()+'**'+$('#txt_cutting_date_'+i).val()+'**'+$('#txt_sewing_date_'+i).val()+'**'+$('#txt_wash_send_date_'+i).val()+'**'+$('#txt_wash_rec_date_'+i).val()+'**'+$('#txt_finishing_date_'+i).val();
				}
				else
				{
					breck_down_data+="----"+$('#cbo_sample_name_'+i).val()+'**'+$('#txt_pattern_date_'+i).val()+'**'+$('#txt_cutting_date_'+i).val()+'**'+$('#txt_sewing_date_'+i).val()+'**'+$('#txt_wash_send_date_'+i).val()+'**'+$('#txt_wash_rec_date_'+i).val()+'**'+$('#txt_finishing_date_'+i).val();
				}
			}
			document.getElementById('txt_sample_plan').value=breck_down_data;
			parent.emailwindow.hide();
		}


	
    </script>
    <body  onload="set_hotkey();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
	<input type="hidden" name="txt_sample_plan" id="txt_sample_plan">
	<fieldset style="width:720px;">
		<form name="sampleplaninfo_1" id="sampleplaninfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="720px" id="tbl_list">
			
				<?
				echo $data_array;
				 $data_array=explode('----',$sample_data);

				// echo "hhh".$breck_down_data;die;
				     /*  echo '<pre>';
                    print_r($data_array); die;   */
				  if($data_array[0]=="")
                    {
                        $data_array=array();
                    }

					if ( count($data_array)>0)
                    {
                        $rowId=0;
                        foreach( $data_array as $row )
                        {
                           $sample_plans=explode('**',$row);
							?>
							<tr id="settr_<?=$rowId; ?>" align="center">
								<td width="80" align="center">Sample Plan<?
								$sql="select a.id,a.sample_name  from lib_sample a,sample_development_mst b,sample_development_dtls c  where a.id=c.sample_name and b.id=c.sample_mst_id and b.id=$req_id group by a.id,a.sample_name  ";

								echo create_drop_down( "cbo_sample_name_".$rowId, 80, $sql,"id,sample_name", 1, "Select Sample",$sample_plans[0] , "");?>
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td> 
								<td align="center" width="80">Pattern Date<input type="text" name="txt_pattern_date_<?=$rowId; ?>" id="txt_pattern_date_<?=$rowId; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?=change_date_format($sample_plans[1]); ?>" />
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td> 
								<td align="center" width="80">Cutting Date<input type="text" name="txt_cutting_date_<?=$rowId; ?>" id="txt_cutting_date_<?=$rowId; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[2]);?>" />
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
								<td align="center" width="80">Sewing Date<input type="text" name="txt_sewing_date_<?=$rowId; ?>" id="txt_sewing_date_<?=$rowId; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[3]);?>" />
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
								<td align="center" width="100">Wash Send Date<input type="text" name="txt_wash_send_date_<?=$rowId; ?>" id="txt_wash_send_date_<?=$rowId; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[4]);?>" /></td>
								<td align="center" width="80">Wash Rec Date<input type="text" name="txt_wash_rec_date_<?=$rowId; ?>" id="txt_wash_rec_date_<?=$rowIdi; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[5]);?>" />
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
								<td align="center" width="80">Finishing Date<input type="text" name="txt_finishing_date_<?=$rowId; ?>" id="txt_finishing_date_<?=$rowId; ?>" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[6]);?>" />
								<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
								<td>
							
								<input type="button" name="increaseset_<?=$rowId; ?>" id="increaseset_<?=$rowId; ?>" style="width:30px" class="formbutton" value="+" onClick="fn_add_row(<?=$rowId; ?>);" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
								<input type="button" name="decreaseset_<?=$rowId; ?>"id="decreaseset_<?=$rowId; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deleteRow(<?=$rowId; ?> ,'tbl_list' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>

								</td>
							</tr>
						<?
						 $rowId++;
						}
					}	
					else
                    {
                        ?>
                    	<tr id="settr_1" align="center">
							<td width="80" align="center">Sample Plan<?
							$sql="select a.id,a.sample_name  from lib_sample a,sample_development_mst b,sample_development_dtls c  where a.id=c.sample_name and b.id=c.sample_mst_id and b.id=$req_id group by a.id,a.sample_name  ";
							echo create_drop_down( "cbo_sample_name_1", 80, $sql,"id,sample_name", 1, "Select Sample", $sample_plans[0], "");?>
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td> 
							<td align="center" width="80">Pattern Date<input type="text" name="txt_pattern_date_1" id="txt_pattern_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[1]); ?>" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td> 
							<td align="center" width="80">Cutting Date<input type="text" name="txt_cutting_date_1" id="txt_cutting_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[2]);?>" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
							<td align="center" width="80">Sewing Date<input type="text" name="txt_sewing_date_1" id="txt_sewing_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[3]);?>" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
							<td align="center" width="100">Wash Send Date<input type="text" name="txt_wash_send_date_1" id="txt_wash_send_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[4]);?>" /></td>
							<td align="center" width="80">Wash Rec Date<input type="text" name="txt_wash_rec_date_1" id="txt_wash_rec_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[5]);?>" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
							<td align="center" width="80">Finishing Date<input type="text" name="txt_finishing_date_1" id="txt_finishing_date_1" class="datepicker" style="width:60px;" placeholder="Select Date" value="<?= change_date_format($sample_plans[6]);?>" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" value="<? echo $req_id;?>"></td>
							<td>
							
							<input type="button" name="increaseset_1"  id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="fn_add_row(1);" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
							<input type="button" name="decreaseset_1" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deleteRow(1);" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
							</td>
						</tr>
					<? 
					 $rowId++;
                    } 
                    ?>
					  
							
				
		   </table>
		   <table>
		   <tr>
					<td colspan="8" align="center" class="button_container">
						 <? 
					     //echo load_submit_buttons( $permission, "fnc_sample_plan_info", 0,0 ,"reset_form('sampleplaninfo_1','','')",1);
				        ?> </br> 
				       <!--  <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;"> -->
						<input type="button" name="close_buttons" class="formbutton" value="Close" id="close_buttons" onClick="fnc_close();" style="width:50px;height: 35px;" />
 					</td>				
				</tr>
				</table>
			</form> 
		</fieldset>	
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}


if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	 
	extract(check_magic_quote_gpc( $_REQUEST )); 

	
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$update_req_master_part=execute_query("update sample_development_mst set refusing_cause='$refusing_cause' where id='$req_id'");
		if($db_type==0)
		{
			if($update_req_master_part)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($update_req_master_part)
			{
				oci_commit($con);   
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$update_req_master_part=execute_query("update sample_development_mst set refusing_cause='$refusing_cause' where id='$req_id'");
		$email=sql_select("select a.team_member_email as email from lib_mkt_team_member_info a,sample_development_mst b where a.id=b.dealing_marchant and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id in (117,203,449) and b.id='$req_id'");

		if($db_type==0)
		{
			if($update_req_master_part)
			  {
				mysql_query("COMMIT");  
				echo "1**";
			   }
			else
			  {
				mysql_query("ROLLBACK"); 
				echo "10**";
			  }
		}
		else if($db_type==2 || $db_type==1 )
	    {
			if($update_req_master_part)
			{
				oci_commit($con);   
				echo "1**";
				ob_start();
				$from_mail="";
				$subject="Refusing Cause";
				$to=$email[0][csf("email")]; 
				$message="$refusing_cause";
				$message=ob_get_contents();
				ob_clean();
				$header=mail_header();
				if($to!="" ){echo send_mail_mailer( $to, $subject, $message, $from_mail );} 		
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
	    }
		disconnect($con);
		die;
	}		
}


if ($action=="acknowledge_capacity"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$row_no=str_replace("'","",$row_no);
	
	$team_leader='team_leader_'.$row_no;
	$txt_confirm_del_end_date='txt_confirm_del_end_date_'.$row_no;
	$company_id='company_id_'.$row_no;

	if($db_type==0)
	{
		$confirm_del_end_date=change_date_format(str_replace("'","",$$txt_confirm_del_end_date),"yyyy-mm-dd","");
	}
	else
	{
		$confirm_del_end_date=date("j-M-Y",strtotime(str_replace("'","",$$txt_confirm_del_end_date)));
	}
	
	
	$req_arr=return_field_value("STYLE_CAPACITY","LIB_SAMPLE_PRODUCTION_TEAM","id =".$$team_leader." and PRODUCT_CATEGORY=6 and is_deleted=0 and status_active=1");	
	
	$req_acknowledge_arr=return_library_array("select SAMPLE_MST_ID, SAMPLE_MST_ID from SAMPLE_REQUISITION_ACKNOWLEDGE where company_id=".$$company_id." and entry_form=345 and team_leader=".$$team_leader." and CONFIRM_DEL_END_DATE='$confirm_del_end_date'", "SAMPLE_MST_ID", "SAMPLE_MST_ID"  );

	echo $req_arr.','.count($req_acknowledge_arr).','.$row_no;
	
	exit();	
}
?>