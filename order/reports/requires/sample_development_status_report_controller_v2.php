<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$group_concat="group_concat";
	$select_year="year";
	$year_con="";
	$defalt_date_format="0000-00-00";
}
else
{
	$group_concat="wm_concat";
	$select_year="to_char";
	$year_con=",'YYYY'";
	$defalt_date_format="";
}
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$image_library=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='sample_development'", "master_tble_id", "image_location"  );
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='sample_development' and file_type=1",
'master_tble_id','image_location');
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}

if($action=="image_view_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
//echo "select master_tble_id,image_location from   common_photo_library where form_name='sample_development' and file_type=1 and master_tble_id=$id";
$imge_data=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_development' and file_type=1 and master_tble_id=$id");
?>
<table>
<tr>
<?
foreach($imge_data as $row)
{
?>
<td><img   src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
<?
}
?>

</tr>

</table>

<?

}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(str_replace("'","",$cbo_company_name)==0) $company_name="%%"; else $company_name=str_replace("'","",$cbo_company_name);
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$style_cond="";
	if(str_replace("'","",$txt_style)!="") $style_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style)."%'  ";
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
/*	$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
*/	

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond=" and a.requisition_date between '$start_date' and '$end_date'";
	}
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:2070px">
		<fieldset style="width:100%;">	
			<table width="2070">
				<tr class="form_caption">
					<td colspan="21" align="center">Sample Development Status Report V2</td>
				</tr>
				<tr class="form_caption">
					<td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
            
            <br />
            <table class="rpt_table" width="2070" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">Sl</th>
                    <th width="120">Requisition Date</th>
                    <th width="90">Merchandiser</th>
                    <th width="100">Buyer</th>
                    <th width="100">Job/Requisition No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Sample Color</th>
					<th width="150">Fabrication</th>
					<th width="100">Fabric Source</th>
					<th width="100">Trims Source</th>
                    <th width="120">Sample Type</th>
                    <th width="80">Sample Qty</th>
                    <th width="100">Sample Size</th>
                    <th width="100">Sample Submission Date</th>
                    <th width="100">Sample Required Date</th>
                    <th width="100">Sample Received Actual Date</th>
                    <th width="85">Approved/Reject Comments</th>
                    <th width="85">Remarks</th>
                    <th width="100">Priority Sending Date</th>
                    <th width="100">Production Free Date</th>
                    <th width="100">Shipment Date</th>
				</thead>
			</table>
			<div style="width:2100px; max-height:600px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2070" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
					$sql=sql_select("select 
					b.sample_mst_id,
					b.id,
					a.buyer_name,
					a.style_ref_no,
					a.remarks,
					a.refusing_cause,
					a.estimated_shipdate,
					b.sample_name,
					b.sample_color,
					b.recieve_date_from_buyer,
					b.buyer_dead_line,
					b.factory_dead_line,
					b.sent_to_factory_date,
					a.company_id,
					b.sent_to_buyer_date,
					b.approval_status,
					b.status_date,
					a.item_name,
					b.fabrication,
					b.sample_prod_qty,
					b.fabric_sorce,
					b.key_point,
					b.buyer_meeting_date,
					a.team_leader,
					a.dealing_marchant,
					b.receive_date_from_factory,
					b.working_factory,
					b.comments,
					b.delv_start_date,
					b.delv_end_date,
					c.size_id
					from  
							sample_development_mst a, sample_development_dtls b left join sample_development_size c on b.sample_mst_id=c.mst_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0
					where 
							a.id=b.sample_mst_id and a.company_id like '$company_name' $buyer_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond 
					order by 
							b.buyer_dead_line,b.id");
					$reference_arr=array();
					foreach($sql as $row)
					{
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sample_mst_id"]=$row[csf("sample_mst_id")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sample_name"]=$row[csf("sample_name")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sample_color"]=$row[csf("sample_color")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["recieve_date_from_buyer"]=$row[csf("recieve_date_from_buyer")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["buyer_dead_line"]=$row[csf("buyer_dead_line")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["factory_dead_line"]=$row[csf("factory_dead_line")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sent_to_factory_date"]=$row[csf("sent_to_factory_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["company_id"]=$row[csf("company_id")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sent_to_buyer_date"]=$row[csf("sent_to_buyer_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["approval_status"]=$row[csf("approval_status")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["status_date"]=$row[csf("status_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["item_name"]=$row[csf("item_name")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["fabrication"]=$row[csf("fabrication")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["fabric_sorce"]=$row[csf("fabric_sorce")];
						
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["key_point"]=$row[csf("key_point")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["buyer_meeting_date"]=$row[csf("buyer_meeting_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["team_leader"]=$row[csf("team_leader")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["receive_date_from_factory"]=$row[csf("receive_date_from_factory")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["working_factory"]=$row[csf("working_factory")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["comments"]=$row[csf("comments")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["delv_start_date"]=$row[csf("delv_start_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["delv_end_date"]=$row[csf("delv_end_date")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["remarks"]=$row[csf("remarks")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["refusing_cause"]=$row[csf("refusing_cause")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sample_prod_qty"]=$row[csf("sample_prod_qty")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["estimated_shipdate"]=$row[csf("estimated_shipdate")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["size_id"].=$size_arr[$row[csf("size_id")]].',';
					}

					$sql_delivery_date=sql_select("select b.id,b.sample_mst_id,c.delivery_date from sample_development_mst a, sample_development_dtls b, sample_ex_factory_dtls c where a.id=b.sample_mst_id and a.id=c.sample_development_id and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $style_cond $team_cond");
					foreach($sql_delivery_date as $row)
					{
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["delivery_date"]=$row[csf("delivery_date")];
					}
					unset($sql_delivery_date);
					$sql_cutting_date=sql_select("select b.id,b.sample_mst_id,d.sewing_date from sample_development_mst a, sample_development_dtls b, sample_sewing_output_mst c, sample_sewing_output_dtls d where a.id=b.sample_mst_id and a.id=c.sample_development_id and c.id=d.sample_sewing_output_mst_id and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $style_cond $team_cond");
					foreach($sql_cutting_date as $row)
					{
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["sewing_date"]=$row[csf("sewing_date")];
					}
					unset($sql_cutting_date);
					$sql_required_fabric=sql_select("select b.id,b.sample_mst_id,c.fabric_description,c.fabric_source from sample_development_mst a, sample_development_dtls b, sample_development_fabric_acc c where a.id=b.sample_mst_id and a.id=c.sample_mst_id  and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.form_type = 1 $buyer_id_cond $date_cond $style_cond $team_cond");
					foreach($sql_required_fabric as $row)
					{
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["fabric_description"]=$row[csf("fabric_description")];
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["fabric_source"]=$row[csf("fabric_source")];
					}
					unset($sql_required_fabric);
					$sql_required_acc=sql_select("select b.id,b.sample_mst_id,c.fabric_source as trims_source from sample_development_mst a, sample_development_dtls b, sample_development_fabric_acc c where a.id=b.sample_mst_id and a.id=c.sample_mst_id  and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.form_type = 2 $buyer_id_cond $date_cond $style_cond $team_cond");
					foreach($sql_required_acc as $row)
					{
						$reference_arr[$row[csf("sample_mst_id")]][$row[csf("id")]]["trims_source"]=$row[csf("trims_source")];
					}
					unset($sql_required_acc);
					$sample_arr=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
					$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
					$dealing_marchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
					$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
					
                    $i=1;$s=1;
                    $sql_mst="select a.id,a.buyer_name,a.style_ref_no,a.company_id,a.requisition_number,a.requisition_date,a.dealing_marchant from  sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $style_cond $team_cond group by a.id,a.buyer_name,a.style_ref_no,a.company_id,a.requisition_number,a.requisition_date,a.dealing_marchant order by a.requisition_number"; 
					//echo $sql_mst;die;	
					$nameArray_mst=sql_select($sql_mst);
					$tot_rows=count($nameArray_mst);
					foreach($nameArray_mst as $row_mst)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$k=1;
						$r=0;
						
						?>
                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $s; ?>">
                        
                            <td width="40" align="center"><? echo $i;?></td>
                            <td width="120" align="center" style="word-break:break-all"><p><? echo $row_mst[csf('requisition_date')]; ?></p></td>
							<td width="90" align="center" style="word-break:break-all"><p><? 
							$dealing_marchant=$dealing_marchant_arr[$row_mst[csf('dealing_marchant')]];
							echo $dealing_marchant; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all"><p><? echo $buyer_short_name_library[$row_mst[csf('buyer_name')]]; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[csf('requisition_number')]; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[csf('style_ref_no')]; ?></p></td>
							<?
                            foreach($reference_arr[$row_mst[csf('id')]] as $row)
                            {
								if($k==1)
								{
								?>
                                    
                                    <td width="100" align="center" style="word-break:break-all"><p><? echo $color_name_library[$row[('sample_color')]]; ?></p></td>
									<td width="150" align="center" style="word-break:break-all"><p><? echo $row[('fabric_description')]; ?></p></td>
									<td width="100" align="center" style="word-break:break-all"><p><? echo $fabric_source[$row[('fabric_source')]]; ?></p></td>  
									<td width="100" align="center" style="word-break:break-all"><p><? echo $fabric_source[$row[('trims_source')]]; ?></p></td> 
									<td width="120" align="center" style="word-break:break-all">
										<?php 
											$sample=$sample_arr[$row[('sample_name')]];
											echo $sample;
										?>
                                	</td>
                                    <td width="80" align="center" style="word-break:break-all"><? echo $row[('sample_prod_qty')]; ?></td>
                                    <td width="100" align="center" style="word-break:break-all"><? 
									$size_no=implode(",",array_unique(explode(",",$row["size_id"])));
									echo $row["size_id"]; ?> </td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('delv_start_date')]); ?> </td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('delv_end_date')]); ?> </td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('delivery_date')]); ?> </td>
                                    <td width="85" align="center" style="word-break:break-all"> <? echo $row[('refusing_cause')]; ?> </td>
									<td width="85" align="center" style="word-break:break-all"><? echo $row[('remarks')]; ?></td>
                                    <td width="100" align="center"> <? //echo change_date_format($row[('sewing_date')]); ?> </td>
									<td width="100" align="center"> <? echo change_date_format($row[('sewing_date')]); ?></td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('estimated_shipdate')]); ?></td>
                        </tr>
                        <?	
						}
						else
						{
						?>
                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $s; ?>">
                        
                            <td width="40" align="center">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
                                    <? echo $i;?>
                                </font>
                            </td>
                            <td width="120">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
								<? echo $row_mst[csf('requisition_date')]; ?>
                                </font>    
                            </td>
                            <td width="90">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
								<? $dealing_marchant=$dealing_marchant_arr[$row_mst[csf('dealing_marchant')]];
									echo $dealing_marchant; ?>
                                </font>
                            </td>
                            <td width="100">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
								<? echo $buyer_short_name_library[$row_mst[csf('buyer_name')]]; ?>
                                </font>
                            </td>
                            <td width="100" align="center">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
								<? echo $row_mst[csf('requisition_number')]; ?>
                                </font>
                            </td>
                            <td width="100"><font style="display:none" color="<? echo $bgcolor; ?>">
								<? echo $row_mst[csf('style_ref_no')]; ?>
                                </font>
							</td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $color_name_library[$row[('sample_color')]]; ?></p></td>
									<td width="150" align="center" style="word-break:break-all"><p><? echo $row[('fabrication')]; ?></p></td>
									<td width="100" align="center" style="word-break:break-all"><p><? echo $fabric_source[$row[('fabric_sorce')]]; ?></p></td>  
									<td width="100" align="center" style="word-break:break-all"><p><?// echo $fabric_source[$row[('fabric_sorce')]]; ?></p></td> 
									<td width="120" align="center" style="word-break:break-all">
										<?php 
											//$sample = return_field_value("sample_name","lib_sample","id=".$row[('sample_name')],"sample_name");
											$sample=$sample_arr[$row[('sample_name')]];
											echo $sample;
										?>
                                	</td>
                                    <td width="80" align="center" style="word-break:break-all"><? echo $row[('sample_prod_qty')]; ?></td>
									<td width="100" align="center" style="word-break:break-all"><? 
									$size_no=implode(",",array_unique(explode(",",$row["size_id"])));
									echo $row["size_id"]; ?></td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('delv_start_date')]); ?> </td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('delv_end_date')]); ?> </td>
									<td width="100" align="center"> <? echo change_date_format($row[('delivery_date')]); ?> </td>
                                    <td width="85" align="center" style="word-break:break-all"> <? echo change_date_format($row[('refusing_cause')]); ?> </td>
									<td width="85" align="center" style="word-break:break-all"><? echo $row[('remarks')]; ?></td>
                                    <td width="100" align="center"> <? //echo change_date_format($row[('sent_to_buyer_date')]); ?> </td>
									<td width="100" align="center"> <? echo change_date_format($row[('sewing_date')]); ?></td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('estimated_shipdate')]); ?></td>
						</tr>
						<?
						}
						$k++;
						$s++;	
					}
					
					if(count($reference_arr[$row_mst[csf('id')]])<1)
					{
					?>
                       
					   <th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="85">&nbsp;</th>
						<th width="85">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						</tr>
					<?
					$s++;
					}
				$i++;
				}
				
				?>
				</table>
				<table class="rpt_table" width="2070" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="40">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="85">&nbsp;</th>
						<th width="85">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
					</tfoot>
				</table>
			</div>
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