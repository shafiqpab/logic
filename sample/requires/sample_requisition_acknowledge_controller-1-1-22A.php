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

	 $requisition_no_cond2=str_replace("requisition_number_prefix_num","a.requisition_number_prefix_num",$requisition_no_cond);
 	 $sql_req="SELECT entry_form_id, id, company_id, requisition_date, requisition_number_prefix_num, style_ref_no, buyer_name, season, season_buyer_wise, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, remarks, status_active, is_deleted, refusing_cause,brand_id	 from sample_development_mst where entry_form_id in (117,203,449) and company_id=$company_name $buyer_id_cond  $season_cond $style_id_cond $requisition_no_cond $date_cond $variable_cond $brand_cond1  and status_active=1 and is_deleted=0 and is_acknowledge=0 order by entry_form_id desc, id desc";
 	 //echo $sql_req;
 	
 	$sql_req2="SELECT distinct(a.id), a.entry_form_id, a.company_id, a.requisition_date, a.requisition_number_prefix_num, a.style_ref_no, a.buyer_name, a.season, a.season_buyer_wise, a.product_dept, a.dealing_marchant, a.agent_name, a.buyer_ref, a.bh_merchant, a.estimated_shipdate, a.remarks, a.status_active, a.is_deleted, a.refusing_cause, b.confirm_del_end_date,a.brand_id from sample_development_mst a,sample_requisition_acknowledge b where a.entry_form_id in (117,203,449) and a.company_id=$company_name $style_id_cond2 $requisition_no_cond2 $buyer_id_cond2 $season_cond2 $brand_cond2 $date_cond2 and a.is_approved in (0,1) and a.is_acknowledge=1  and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and a.id=b.sample_mst_id order by   a.id desc";
 	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active =1 and is_deleted=0 ", "id", "brand_name"  );
	
	?>
	<script type="text/javascript">
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
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1240" class="rpt_table" >
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
                    <th width="90">Confirm Del. End Date</th>
                    <th>Refusing Cause</th>
                </thead>
            </table>
            <div style="width:1240px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" id="tbl_list_search">
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
								//$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
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
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="80" align="center"> <a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_id')]; ?>+'*'+<? echo $row[csf('id')]; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $row[csf('requisition_number_prefix_num')]; ?></a></td>

									<td width="60" align="center"> <a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_id')]; ?>+'*'+<? echo $row[csf('id')] ; ?>+'*'+<? echo $booking_no; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $booking_no; ?></a></td>
                                    <td width="70"><p><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></p></td>
									<td width="120" align="center"> <? echo $company_arr[$row[csf('company_id')]]; ?></td>
									<td width="60" align="center"> <? echo $dealing_arr[$row[csf('dealing_marchant')]]; ?></td>
									<td width="90" align="center"> <? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td width="100" align="center"> <? echo $brand_arr[$row[csf('brand_id')]]; ?></td>
									<td width="60" align="center"><p><?=$seasonName; ?></p></td>
									<td width="70" align="center"><p><?=$row[csf('style_ref_no')]; ?></p></td>
									<td width="70" align="right"><p><? echo $samplQtyArr[$row[csf('id')]]; ?></p></td>
									<td width="50" align="right"><p><? echo  $reqQtyArr[$row[csf('id')]];?></p></td>
									<td width="80" align="center"><? if($embArr[$row[csf('id')]]>0) echo "&nbsp; YES"; else echo "&nbsp; NO"; ?></td>
									<td width="90" align="center">
										<input type="text" name="txt_confirm_del_end_date_<? echo $i;?>" id="txt_confirm_del_end_date_<? echo $i;?>" class="datepicker" placeholder="Select Date" autocomplete="off" readonly style="width:80px;" value="<?= change_date_format($row[CONFIRM_DEL_END_DATE]); ?>"  /> 
										<!-- onchange="fn_check_acknowledge(<? //echo $i;?>);//value="<? //echo $row[CONFIRM_DEL_END_DATE]; ?>""  -->
										<!-- <input id="team_leader_<? //echo $i;?>" name="team_leader[]" type="hidden" value="<? //echo $row[TEAM_LEADER]; ?>" /> -->
									</td>
									<td> <input style="width:80px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/sample_requisition_acknowledge_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
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
	
	$msg=''; $flag=''; $response='';
	$ready_to_approve_mess='';
	
	if($approval_type==0)
	{
 		//,unacknowledge_by,unacknowledge_date
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
		 //print_r($confirm_del_end_arr);die;
		$sql_insert="insert into sample_requisition_acknowledge(id,sample_mst_id,company_id,location_id,buyer_name,style_ref_no,agent_name,dealing_marchant,inserted_by,insert_date,status_active,is_deleted,acknowledge_status) 
			select	
			'',id,company_id,location_id,buyer_name,style_ref_no,agent_name,dealing_marchant,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0,1 from  sample_development_mst where id in ($req_nos)";
		//echo $sql_insert;die;
		$rID1=execute_query($sql_insert,0);

		//	echo $rID1;die;
		$rID2=sql_multirow_update("sample_development_mst","is_acknowledge",1,"id",$req_nos,0);


		//print_r($req); die;
		// $conf_del_data_arr="";
		// for ($i=0; $i < count($req); $i++) { 
		// 	$conf_del_data_arr.= $confirm_del_end_arr[$req[$i]]."*";
		// }

		if(count($req)>0){
			$field_array_up="confirm_del_end_date";
			//echo bulk_update_sql_statement("sample_requisition_acknowledge", "sample_mst_id",$field_array_up,$confirm_del_end_arr,$req );die;
			$rID_ref_update=execute_query(bulk_update_sql_statement("sample_requisition_acknowledge", "sample_mst_id",$field_array_up,$confirm_del_end_arr,$req ));
			$flag=1;
		}
		//echo $rID_ref_update;die;
		//$rID3 = sql_multirow_updatess("sample_requisition_acknowledge","confirm_del_end_date",$conf_del_data_arr,"sample_mst_id",$req_nos,0);
 		 
		if($rID1 && $rID2 && $rID_ref_update && empty($ready_to_approve_mess)) $flag=1; else $flag=0; 
		  
 		//if($flag==1) $msg='19'; else $msg='21';
		if($flag==1) $msg='32'; else $msg='34';
	}
	else
	{
		$req_nos = explode(',',$req_nos); 
		$data_value="";
		foreach($req_nos as $vl)
		{
		$arr2=explode('**',$vl);
		if($data_value=="") $data_value.=$arr2[0];else $data_value .=','.$arr2[0];
		}
		$receive_mrr=0;
		//echo "10**select a.recv_number from inv_receive_master a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.style_id in ($data_value) and a.booking_without_order=1 and a.entry_form=2 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number"; disconnect($con); die;
		$sqlre=sql_select("select a.recv_number from inv_receive_master a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.style_id in ($data_value) and a.booking_without_order=1 and a.entry_form=2 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$data_value)."**".$receive_mrr;
			disconnect($con); die;
		}

		$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //print_r($data_value);disconnect($con); die;
		$rID=sql_multirow_update("sample_requisition_acknowledge","acknowledge_status*unacknowledge_by*unacknowledge_date",'0*'.$data,"sample_mst_id",$data_value,0);

		$rID2=sql_multirow_update("sample_development_mst","is_acknowledge*req_ready_to_approved","0*0","id",$data_value,0);

		//echo $rID."__".$rID2;disconnect($con); die;
  		if($rID && $rID2) $flag=1; else $flag=0;
 		//if($flag==1) $msg='20'; else $msg='22';
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