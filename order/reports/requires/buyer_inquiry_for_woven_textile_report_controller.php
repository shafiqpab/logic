<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);

$permission=$_SESSION['page_permission'];

include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_system_id=str_replace("'","",$txt_system_id);
	$cbo_type=str_replace("'","",$cbo_type);
	$style_ref=str_replace("'","",$txt_style_ref);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	
    $system_id_cond="";
	if($txt_system_id!="") $system_id_cond=" and a.system_number_prefix_num=$txt_system_id";
	if($style_ref!="") $style_ref_cond=" and a.style_refernce=$style_ref";
	if($cbo_buyer_name > 0) $buyer_name_cond=" and a.buyer_id=$cbo_buyer_name";
	
	$date_cond='';
    if($txt_date_from !="" && $txt_date_to!="") $date_cond=" and a.inquery_date between '".$txt_date_from."' and '".$txt_date_to."'";
	if($txt_date_from !="" && $txt_date_to!="") $date_cond2=" and a.requisition_date between '".$txt_date_from."' and '".$txt_date_to."'";
	if($txt_date_from !="" && $txt_date_to!="") $date_cond3=" and a.delivery_date between '".$txt_date_from."' and '".$txt_date_to."'";
 
    $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$wash_types = array(1=>"Wash",2=>"Non-Wash",3=>"Garmnets Wash",4=>"Enzyme Wash");
	$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
	$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
  

	if($cbo_type==1){

		$sql="SELECT  a.id, a.system_number_prefix, a.system_number_prefix_num, a.system_number, a.company_id,a.buyer_id,
		a.style_description,a.inquery_date,a.team_leader,a.dealing_marchant ,b.constuction_id,b.composition_id,b.grey_constuction_id,b.weave,b.design,b.wash_type,b.color_id,b.determination_id,b.warp_yarn_type, b.weft_yarn_type,b.product_type,b.finish_type, b.fabric_weight, b.fabric_weight_type, b.finish_width, b.cutable_width,b.grey_width,b.offer_qnty from wo_buyer_inquery a ,wo_buyer_inquery_dtls b where a.id=b.mst_id and a.status_active=1   AND a.company_id = $cbo_company_name $system_id_cond $date_cond $style_ref_cond $buyer_name_cond  order by a.id desc";	

		$title="Inquery No";
		$title2="Inquery Date";
		$title3="Inquery";
	}else if($cbo_type==2){

		$sql="SELECT a.system_number,a.team_leader,a.dealing_marchant,a.requisition_date inquery_date,b.id as dtls_id, b.mst_id,b.inquiry_dtls_id, b.constuction_id, b.product_type, b.composition_id, b.weave_design, b.finish_type, b.color_id, b.fabric_weight, b.fabric_weight_type, b.finish_width, b.cutable_width, b.wash_type, b.offer_qnty, b.uom,b.buyer_target_price,b.amount,hl_no,b.determination_id,b.remarks,b.yarn_type,b.warp_yarn_type,b.weft_yarn_type,b.weave,design from wo_sample_requisition_mst a,wo_sample_requisition_dtls b where a.id=b.mst_id  AND a.company_id = $cbo_company_name $date_cond2 $style_ref_cond $buyer_name_cond and  b.is_deleted=0  and b.status_active=1 order by b.id ASC";	

		$title="Requisition No";
		$title2="Requisition Date";
		$title3="Requisition";
	}else{

		$sql="SELECT a.system_number,a.delivery_date inquery_date,a.team_leader,a.dealing_marchant,b.id, b.mst_id,b.inquiry_dtls_id, b.constuction_id, b.product_type, b.composition_id, b.weave_design, b.finish_type, b.color_id, b.fabric_weight, b.fabric_weight_type, b.finish_width, b.cutable_width, b.wash_type, b.offer_qnty, b.uom,b.buyer_target_price,b.amount,b.hl_no,b.determination_id,b.warp_yarn_type,b.weft_yarn_type,b.weave,b.design,b.type,b.remarks from wo_hand_loom_requisition_mst a,wo_hand_loom_requisition_dtls b where   b.is_deleted=0   and b.status_active=1 AND a.company_id = $cbo_company_name $date_cond3 $style_ref_cond $buyer_name_cond order by b.id ASC";

		$title="HL/LD/SO No";
		$title2="HL/LD/SO Date";	
		$title3="HL/LD/SO";
	}
	
	//echo $sql;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:2650px; margin-top:10px">
        <legend>Buyer Inquiry Report </legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2630" class="rpt_table" >
                <thead>
                	 
                    <th width="40">SL</th>                   
                    <th width="120"><?=$title;?></th>
					<th width="100"><?=$title2;?></th>
					<th width="100">Team Leader</th>
					<th width="100">Dealing Merchant</th>
					<th width="200">Fin. Construction</th>
					<th width="200">Grey Construction</th>
					<th width="100">Product Type</th>
					<th width="200">Fab. Composition</th>
					<th width="150">Warp Yarn Type</th>
					<th width="150">Weft Yarn Type</th>
					<th width="100">Weave</th>
					<th width="100">Design</th>
					<th width="100">Finish Type</th>
					<th width="100">Fab. Color</th>
					<th width="100">Fabric Weight</th>
					<th width="100">F.Weight Type</th>
					<th width="100">Finished Width</th>
					<th width="100">Cutable Width</th>
					<th width="100">Grey Width</th>
					<th width="100"><?=$title3;?> QTY</th>
					<th>Wash</th>
                    
                </thead>
            </table>
            <div style="width:2630px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2610" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );
                           
                            foreach ($nameArray as $row)
                            {
								$constuction_id=$row[csf("constuction_id")];
								$fabric_construction=return_field_value("epi || '*' || ppi ||'*' || warp_count || '*' || wrap_spandex || '*' || weft_count || '*' || weft_spandex as fab_con", "lib_fabric_construction", "id =$constuction_id", "fab_con");
								$composition_str = "";
								if(!empty($row[csf('determination_id')]))
								{
									$composition_str = $composition_arr[$row[csf('determination_id')]];
								}
								else
								{
									$compos = explode(",",$row[csf('composition_id')]);
									foreach($compos as $comp)
									{
										if($composition_str != "") $composition_str .= ",";
										$composition_str .= $composition[$comp];
									}
								}
								
								$approval_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								
								$warp_yarn_type_arr=explode(",",$row[csf('warp_yarn_type')]);
								foreach($warp_yarn_type_arr as $val){
									$warp_yarn_type.=$yarn_type[$val].",";
								}
								$weft_yarn_type_arr=explode(",",$row[csf('weft_yarn_type')]);
								foreach($weft_yarn_type_arr as $val){
									$weft_yarn_type.=$yarn_type[$val].",";
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	 
									<td width="40" align="center"><? echo $i; ?></td>
									
                                    <td width="120" align="center"><? echo $row[csf('system_number')]; ?></td>
									<td width="100"><? echo change_date_format($row[csf('inquery_date')]); ?></td>
									<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
									<td width="100"><? echo $marchentrArr[$row[csf('dealing_marchant')]]; ?></td>
									<td width="200"><? echo $fabric_construction_name_arr[$row[csf('constuction_id')]];; ?></td>									
									<td width="200"><? echo $fabric_construction_name_arr[$row[csf('grey_constuction_id')]]; ?></td>
									<td width="100"><? echo $color_type[$row[csf('product_type')]]; ?></td>
									<td width="200" style="word-break:break-all"><? echo $composition_str; ?></td>
									<td width="150" style="word-break:break-all"><? echo $warp_yarn_type; ?></td>
									<td width="150" style="word-break:break-all"><? echo $weft_yarn_type; ?></td>
									<td width="100"><? echo $row[csf('weave')]; ?></td>
									<td width="100"><? echo $row[csf('design')]; ?></td>
									<td width="100"><? echo $finish_types[$row[csf('finish_type')]]; ?></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $row[csf('fabric_weight')]; ?></td>
									<td width="100"><? echo $fabric_weight_type[$row[csf('fabric_weight_type')]]; ?></td>
									<td width="100"><? echo $row[csf('finish_width')]; ?></td>
									<td width="100"><? echo $row[csf('cutable_width')]; ?></td>
									<td width="100"><? echo $row[csf('grey_width')]; ?></td>
									<td width="100"><? echo $row[csf('offer_qnty')]; ?></td>
									
									<td  ><? echo $wash_types[$row[csf('wash_type')]]; ?></td>
								 
                                
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
             
        </fieldset>
    </form>         
	<?
	exit();	
}

 

if ($action == "load_drop_down_buyer") {
	 
	$company_id = $data;
	echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);

	exit();
}
?>