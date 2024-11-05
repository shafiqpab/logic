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
	
	
/*	$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
*/	

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		/*if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));
			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}*/
		$date_cond=" and b.buyer_dead_line between '$start_date' and '$end_date'";
	}
	//echo $date_cond;die;
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:2870px">
		<fieldset style="width:100%;">	
			<table width="2840">
				<tr class="form_caption">
					<td colspan="21" align="center">Sample Development Status Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
            
            <br />
            <table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">Sl</th>
                    <th width="120">Company</th>
                    <th width="90">Buyer</th>
                    <th width="130">Style</th>
                    <th width="60">Style ID</th>
                    <th width="100">Attacment</th>
                    <th width="60">Image</th>
                    <th width="120">Sample Name</th>
                    <th width="220">Sample Color</th>
                    <th width="100">Working Factory</th>
                    <th width="85">Recv Dt From Buyer</th>
                    <th width="85">Buyer Dead Line</th>
                    <th width="85">Dev. Dead Line</th>
                    <th width="85">Sent to Dev.Section</th>
                    <th width="85">Rcv.From Dev. Section</th>
                    <th width="85">Sent to Buyer</th>
                    <th width="100">Approval Status</th>
                    <th width="85">Status Date</th>
                    <th width="100">Gmts Items</th>
                    <th width="150">Fabrication</th>
                    <th width="150">Fabric Sorce</th>
                    <th width="100">Key Point</th>
                    <th width="100">Buyer Meeting</th>
                    <th width="140">Team Leader</th>
                    <th width="170">Dealing Marchan</th>
                    <th width="">Comments</th>
				</thead>
			</table>
			<div style="width:2870px; max-height:600px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
					$sql=sql_select("select 
					b.sample_mst_id,
					b.id,
					a.buyer_name,
					a.style_ref_no,
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
					b.fabric_sorce,
					b.key_point,
					b.buyer_meeting_date,
					a.team_leader,
					a.dealing_marchant,
					b.receive_date_from_factory,
					b.working_factory,
					b.comments
					from  
							sample_development_mst a, sample_development_dtls b 
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
					}
					
					$sample_arr=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
					$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
					$dealing_marchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
					
                    $i=1;$s=1;
                    $sql_mst="select a.id,a.buyer_name,a.style_ref_no,a.company_id from  sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id like '$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $style_cond $team_cond
					group by a.id,a.buyer_name,a.style_ref_no,a.company_id 
					order by a.id";//and a.buyer_name like '$buyer_name'
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
                            <td width="120" align="center" style="word-break:break-all"><p><? echo $company_library[$row_mst[csf('company_id')]]; ?></p></td>
                            <td width="90" align="center" style="word-break:break-all"><p><? echo $buyer_short_name_library[$row_mst[csf('buyer_name')]]; ?></p></td>
                           
                            <td width="130" align="center" style="word-break:break-all"><p><? echo $row_mst[csf('style_ref_no')]; ?></p></td>
                            <td width="60" align="center"><? echo $row_mst[csf('id')]; ?></td>
                            <td width="100" style="word-break:break-all">
                            <?
							//echo $image_library[$row_mst[csf('id')]];
							if($image_library[$row_mst[csf('id')]] !="")
							{
							?>
                            <input type="button" id="image_button" class="image_uploader" style="width:90px" value="Attachment" onClick="file_uploader ( '../../', <? echo $row_mst[csf('id')]; ?>,'', 'sample_development', 2 ,1,2)" />
                            <?
							}
							?>
                            </td>
                            <td width="60" style="word-break:break-all"><img onclick="openImageWindow( <? echo $row_mst[csf('id')]; ?> )"  src='../../<? echo $imge_arr[$row_mst[csf('id')]]; ?>' height='15' width='30' /></td>
                        
							<?
                            
                            //$sql="select a.buyer_name,a.style_ref_no,a.id,b.sample_name,b.sample_color,b.recieve_date_from_buyer,b.buyer_dead_line,b.factory_dead_line,b.sent_to_factory_date,a.company_id,b.sent_to_buyer_date,b.approval_status,b.status_date,a.item_name,b.fabrication,b.key_point,b.buyer_meeting_date,a.team_leader,a.dealing_marchant,b.receive_date_from_factory,b.working_factory, b.comments from  sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id='".$row_mst[csf('company_id')]."' and a.buyer_name='".$row_mst[csf('buyer_name')]."' and b.sample_mst_id='".$row_mst[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond order by b.buyer_dead_line,b.id";
							//echo $sql;die;
                            //$nameArray=sql_select($sql);
                            foreach($reference_arr[$row_mst[csf('id')]] as $row)
                            {
								if($k==1)
								{
								?>
                                    
									<td width="120" align="center" style="word-break:break-all">
										<?php 
											//$sample = return_field_value("sample_name","lib_sample","id=".$row[('sample_name')],"sample_name");
											$sample=$sample_arr[$row[('sample_name')]];
											echo $sample;
										?>
                                	</td>
                                    <td width="220" align="center" style="word-break:break-all"><p><? echo $color_name_library[$row[('sample_color')]]; ?></p></td>
                                    <td width="100" align="center" style="word-break:break-all"><? echo $row[('working_factory')]; ?></td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('recieve_date_from_buyer')]); ?> </td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('buyer_dead_line')]); ?> </td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('factory_dead_line')]); ?> </td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('sent_to_factory_date')]); ?> </td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('receive_date_from_factory')]); ?> </td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('sent_to_buyer_date')]); ?> </td>
                                    <td width="100" align="center"><? echo $approval_status[$row[('approval_status')]]; ?></td>
                                    <td width="85" align="center"> <? echo change_date_format($row[('status_date')]); ?></td>
                                    <td width="100" align="center" style="word-break:break-all"><? echo $garments_item[$row[('item_name')]]; ?></td>
                                    <td width="150" align="center" style="word-break:break-all"><p><? echo $row[('fabrication')]; ?></p></td>
									<td width="150" align="center" style="word-break:break-all"><p><? echo $fabric_source[$row[('fabric_sorce')]]; ?></p></td>                                    
                                    <td width="100" align="center" style="word-break:break-all"><? echo $row[('key_point')]; ?></td>
                                    <td width="100" align="center"> <? echo change_date_format($row[('buyer_meeting_date')]); ?></td>
                                    <td width="140" align="center" style="word-break:break-all">
                                        <?php 
                                            //$team_leader = return_field_value("team_leader_name","lib_marketing_team","id=".$row[('team_leader')],"team_leader_name");
											$team_leader=$team_leader_arr[$row[('team_leader')]];
                                            echo $team_leader;
                                        ?>
                                    </td>
                                    <td width="170" align="center" style="word-break:break-all">
                                        <?php 
                                           // $dealing_marchant = return_field_value("team_member_name","lib_mkt_team_member_info","id=".$row[('dealing_marchant')],"team_member_name");
										   $dealing_marchant=$dealing_marchant_arr[$row[('dealing_marchant')]];
                                            echo $dealing_marchant;
                                        ?>
                                    </td>
                                     <td width="" align="center" style="word-break:break-all"><? echo $row[('comments')]; ?></td>
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
                                    <? echo $company_library[$row_mst[csf('company_id')]]; ?>
                                </font>    
                            </td>
                            <td width="90">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
                                    <? echo $buyer_short_name_library[$row_mst[csf('buyer_name')]]; ?>
                                </font>
                            </td>
                            <td width="130">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
                                    <? echo $row_mst[csf('style_ref_no')]; ?>
                                </font>
                            </td>
                            <td width="60" align="center">
                                <font style="display:none" color="<? echo $bgcolor; ?>">
                                    <? echo $row_mst[csf('id')]; ?>
                                </font>
                            </td>
                            <td width="100">&nbsp;</td>
                            <td width="60"><img  onclick="openImageWindow( <? echo $row_mst[csf('id')]; ?> )" src='../../<? echo $imge_arr[$row_mst[csf('id')]]; ?>' height='15' width='30' /></td>
                            
                            <td width="120" align="center">
                                <?php 
                                    //$sample = return_field_value("sample_name","lib_sample","id=".$row[('sample_name')],"sample_name");
									$sample=$sample_arr[$row[('sample_name')]];
                                    echo $sample;
                                ?>
                            </td>
                           
                            <td width="220" align="center"><p><? echo $color_name_library[$row[('sample_color')]]; ?></p></td>
                            <td width="100" align="center"><? echo $row[('working_factory')]; ?></td>
                            <td width="85" align="center"> <? echo change_date_format($row[('recieve_date_from_buyer')]); ?> </td>
                            <td width="85" align="center"> <? echo change_date_format($row[('buyer_dead_line')]); ?> </td>
                            <td width="85" align="center"> <? echo change_date_format($row[('factory_dead_line')]); ?> </td>
                            <td width="85" align="center"> <? echo change_date_format($row[('sent_to_factory_date')]); ?> </td>
                            <td width="85" align="center"> <? echo change_date_format($row[('receive_date_from_factory')]); ?> </td>
                            <td width="85" align="center"> <? echo change_date_format($row[('sent_to_buyer_date')]); ?> </td>
                            <td width="100" align="center"><? echo $approval_status[$row[('approval_status')]]; ?></td>
                            <td width="85" align="center"> <? echo change_date_format($row[('status_date')]); ?></td>
                            <td width="100" align="center"><? echo $garments_item[$row[('item_name')]]; ?></td>
                            <td width="150" align="center"><p><? echo $row[('fabrication')]; ?></p></td>
                            
                            <td width="150" align="center"><p><? echo $fabric_source[$row[('fabric_sorce')]]; ?></p></td>
                            
                            
                            <td width="100" align="center"><? echo $row[('key_point')]; ?></td>
                            <td width="100" align="center"> <? echo change_date_format($row[('buyer_meeting_date')]); ?></td>
                            <td width="140" align="center"><p>
                                <?php 
                                    //$team_leader = return_field_value("team_leader_name","lib_marketing_team","id=".$row[('team_leader')],"team_leader_name");
									$team_leader=$team_leader_arr[$row[('team_leader')]];
                                    echo $team_leader;
                                ?></p>
                            </td>
                            <td width="170" align="center"><p>
                                <?php 
                                    //$dealing_marchant = return_field_value("team_member_name","lib_mkt_team_member_info","id=".$row[('dealing_marchant')],"team_member_name");
									$dealing_marchant=$dealing_marchant_arr[$row[('dealing_marchant')]];
                                    echo $dealing_marchant;
                                ?></p>
                            </td>
                             <td width="" align="center"><? echo $row[('comments')]; ?></td>
						</tr>
						<?
						}
						$k++;
						$s++;	
					}
					
					if(count($reference_arr[$row_mst[csf('id')]])<1)
					{
					?>
                       
						<td width="120"></td>
                        <td width="220"></td>
                        <td width="100"></td>
                        <td width="85"></td>
                        <td width="85"></td>
                       	<td width="85"></td>
                        <td width="85"></td>
                        <td width="85"></td>
                        <td width="85"></td>
                        <td width="100"></td>
                        <td width="85"></td>
                        <td width="100"></td>
                        <td width="150"></td>
                       	<td width="100"></td>
                       	<td width="100"></td>
                        <td width="140"></td>
                        <td width="170"></td>
                         <td width=""></td>
						</tr>
					<?
					$s++;
					}
				$i++;
				}
				
				?>
				</table>
				<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="40"></th>
						<th width="120"></th>
						<th width="90"></th>
						<th width="130"></th>
						<th width="60"></th>
                        <th width="100">&nbsp;</th>
                        <th width="60"></th>
						<th width="120" align="right" id="total_order_qnty1"><? //echo number_format($total_order_qnty,0); ?></th>
                        <th width="220"></th>
						<th width="100"></th>
						<th width="85" align="right" id="total_order_qnty_in_pcs1"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="85"></th>
						<th width="85"></th>
                        <th width="85"></th>
                        <th width="85"></th>
                        <th width="85"></th>
                        <th width="100"></th>
                        <th width="85"></th>
                        <th width="100"></th>
                        <th width="150"></th>
                        <th width="150"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="140"></th>
                        <th width="170"></th>
                         <th width=""></th>
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