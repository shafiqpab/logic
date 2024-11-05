<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name   order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Team Member-", $selected, "" ); 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_style_name = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strcon )
	{
		$('#txt_job_no').val( strcon );
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_job_no" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";
	
	//echo $sql;die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',"") ;
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping='$ref_no' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$file_no' ";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to); 
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	if(str_replace("'","",$txt_job_no)!="" || str_replace("'","",$txt_job_no)!=0) $jobcond="and a.job_no_prefix_num=".$txt_job_no.""; else $jobcond="";
	
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	
	if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";

	ob_start();
	
	if($template==1)
	{
		
	?>
        <div style="width:3050px">
        <fieldset style="width:100%;">
          <table width="3030">
              <tr class="form_caption">
                    <td colspan="34" align="center">Cost Breakdown Report</td>
                </tr>
                <tr class="form_caption">
                    <td colspan="34" align="center"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Job No</th>
                    <th width="50">Year</th>
                    <th width="70">Buyer</th>
                    <th width="70">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="100">Team</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="110">Order No</th>
                    <th width="110">Style Ref.</th>
                    <th width="110">Garments Item</th>
                    <th width="90">Order Qnty</th>
                    <th width="50">UOM</th>
                    <th width="90">Qnty (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="220">Fabric Description</th>
                    <th width="70">Knit Fab. Cons</th>
                    <th width="60">Knit Fab. Rate</th>
                    <th width="70">Woven Fab. Cons</th>
                    <th width="65">Woven Fab. Rate</th>
                    <th width="80">Fab. Cost</th>
                    <th width="80">Trims cost</th>
                    <th width="80">Print/Emb cost</th>
                    <th width="80">CM cost</th>
                    <th width="85">Commission</th>
                    <th width="80">Other Cost</th>
                    <th width="80">Tot. cost</th>
                    <th width="100">Total CM cost</th>
                    <th width="100">Total Cost</th>
                    <th width="65">Cost Per unit</th>
                    <th width="65">Order Price</th>
                    <th width="100">Order Value</th>
                    <th width="100">Margin</th>
                    <th width="100">Total Trims Cost</th>
                    <th>Total Emb/Print Cost</th>
                </thead>
            </table>
            <div style="width:3050px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
                $i=1; $total_order_qnty=0; $total_order_qnty_in_pcs=0; $grand_tot_cm_cost=0; $grand_tot_cost=0; $tot_order_value=0; $tot_margin=0; $grand_tot_trims_cost=0; $grand_tot_embell_cost=0; $tot_knit_charge=0; $tot_yarn_dye_charge=0; $tot_dye_finish_charge=0; $yarn_desc_array=array(); $fabriccostArray=array(); $trims_cons_cost_array=array();
				$prodcostArray=array(); $fabricArray=array(); $yarncostArray=array();
                
				$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_description, a.fabric_source, a.rate, a.avg_cons as avg_finish_cons, b.yarn_amount, b.conv_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); //UG-19-00084 wo_pre_cos_fab_co_avg_con_dtls
				foreach($fabricDataArray as $fabricRow)
				{
					$fabricArray[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_description')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('avg_finish_cons')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('conv_amount')].",";
				}
				
				$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as qnty, sum(avg_cons_qnty) as cons_qnty, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				//$trimscostDataArray=sql_select("select a.job_no, b.po_break_down_id, sum(b.cons*a.rate) as total,sum(b.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 by a.job_no,a.trim_group");
				$trimscostDataArray=sql_select("select a.job_no,sum(a.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a where  a.status_active=1 and a.is_deleted=0 group by a.job_no");
				//echo "select a.job_no,sum(a.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a where  a.status_active=1 and a.is_deleted=0 group by a.job_no";
				foreach($trimscostDataArray as $trimsRow)
				{
					 //$trims_cons_cost_array[$trimsRow[csf('job_no')]][$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
					 $trims_po_cost_array[$trimsRow[csf('job_no')]]=$trimsRow[csf('amount_dzn')];
				}
				 
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process not in(1,2,30) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}					  
				 
				if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
				else $year_field="";//defined Later
				
               $sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, 
b.grouping,b.file_no,b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobcond $yearCond $date_cond $buyer_id_cond $team_name_cond $team_member_cond $ref_cond $file_no_cond order 
			by b.pub_shipment_date, b.id";// b.id, b.pub_shipment_date
			
                $nameArray=sql_select($sql);
                $tot_rows=count($nameArray);
                foreach($nameArray as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$costing_date=$costing_library[$row[csf('job_no')]];
                ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="70"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" align="center"><? echo $row[csf('grouping')]; ?></td>
                        <td width="100"><p><? echo $team_library[$row[csf('team_leader')]]; ?></p></td>
                        <td width="110"><p><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></p></td>
                        <td width="110"><p><a href="##" onclick="generate_pre_cost_report('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('po_number')]; ?></a></p></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="110">
                            <p>
                                <?
                                    $gmts_item='';
                                    $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                                    foreach($gmts_item_id as $item_id)
                                    {
                                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                    }
                                    echo $gmts_item;
                                ?>
                            </p>
                        </td>
                        <td width="90" align="right" >
                            <? 
                                echo fn_number_format($row[csf('po_quantity')],0,'.',''); 
                                $total_order_qnty+=$row[csf('po_quantity')];
                            ?>
                        </td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="90" align="right">
                        <? 
                            $order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
							$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
                            echo fn_number_format($order_qnty_in_pcs,0,'.',''); 
                            $total_order_qnty_in_pcs+=$order_qnty_in_pcs;
                        ?>
                        </td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <?
                        $fabric_desc=''; $fabric_cost_per_dzn=0; $knit_fabric_rate=0; $knit_fabric_amnt=0; $yarn_cost=0;$conversion_cost=0;
						$knit_fabric_purc_amnt=0; $woven_fabric_cons=0; $woven_fabric_rate=0; $woven_fabric_amnt=0; $other_cost=0;
                        $tot_cost_per_dzn=0; $tot_cm_cost=0; $tot_cost=0; $tot_trims_cost=0; $tot_embell_cost=0; $cost_per_unit=0; $margin=0;

						$fabricData=explode(",",substr($fabricArray[$row[csf('job_no')]],0,-1));
                        foreach($fabricData as $fabricRow)
                        {
							$knit_fabric_cons=0; 
							$fabricRow=explode("**",$fabricRow);
							$fab_nature_id=$fabricRow[0];
							$fabric_description=$fabricRow[1];
							$fabric_source=$fabricRow[2];
							$rate=$fabricRow[3];
							$avg_finish_cons=$fabricRow[4];
						//	echo $avg_finish_cons.'=='.$rate;
							$yarn_amount=$fabricRow[5];
							$conv_amount=$fabricRow[6];
							
                            if($fabric_desc=="") $fabric_desc=$fabric_description; else $fabric_desc.=",".$fabric_description;
                            if($fab_nature_id==2)
                            {
                                $knit_fabric_cons+=$avg_finish_cons;
                                if($fabric_source==2)
                                {
                                    $knit_fabric_purc_amnt+=$avg_finish_cons*$rate;	
                                }
                            }
                            else if($fab_nature_id==3)
                            {
								$woven_fabric_cons+=$avg_finish_cons;
                                if($fabric_source==2)
                                { 
                                    $woven_fabric_amnt+=$avg_finish_cons*$rate;
                                }
                            }
                            
                            $yarn_cost=$yarn_amount;
                            $conversion_cost=$conv_amount;
                        }
						
                        $knit_fabric_amnt=$knit_fabric_purc_amnt+$yarn_cost+$conversion_cost;
                        $knit_fabric_rate=$knit_fabric_amnt/$knit_fabric_cons;
                        $woven_fabric_rate=$woven_fabric_amnt/$woven_fabric_cons;
                        $fabric_cost_per_dzn=$knit_fabric_amnt+$woven_fabric_amnt;

					 	$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                        if($costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						
						
						if($costing_per_id==2) //Pcs
						{
							//$fabric_cost_per_dzn=($fabric_cost_per_dzn*12)*$row[csf('ratio')];
							//$fabric_cost_per_dzn=($fabric_cost_per_dzn/12)*$row[csf('ratio')];
							$cost_per_qnty='Pcs';
						}
						else
						{
							$cost_per_qnty='Dzn';
						}
						//else $fabric_cost_per_dzn=$fabric_cost_per_dzn;
						//echo $dzn_qnty.'='.$fabric_cost_per_dzn.'='.$costing_per_id.',';
						
						$other_cost=$fabriccostArray[$row[csf('job_no')]]['common_oh']+$fabriccostArray[$row[csf('job_no')]]['lab_test']+$fabriccostArray[$row[csf('job_no')]]['inspection']+$fabriccostArray[$row[csf('job_no')]]['freight']+$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
                        
						$trims_cons_cost=$trims_po_cost_array[$row[csf('job_no')]];//$trims_cons_cost_array[$row[csf('job_no')]][$row[csf('id')]];
						//$trims_dzn_cost=$trims_po_cost_array[$row[csf('id')]];

                        $tot_cost_per_dzn=$fabric_cost_per_dzn+$trims_cons_cost+$fabriccostArray[$row[csf('job_no')]]['cm_cost']+$fabriccostArray[$row[csf('job_no')]]['commission']+$fabriccostArray[$row[csf('job_no')]]['embel_cost']+$other_cost;
                        $cost_per_unit=$tot_cost_per_dzn/$dzn_qnty;
                        
                        $tot_cm_cost=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
                        $tot_cost=($order_qnty_in_pcs/$dzn_qnty)*$tot_cost_per_dzn;
                        $tot_trims_cost=($order_qnty_in_pcs/$dzn_qnty)*$trims_cons_cost;
                        $tot_embell_cost=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
                        $margin=$row[csf('po_total_price')]-$tot_cost;
						
						$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
						foreach($yarnData as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$type_id=$yarnRow[1];
							$cons_qnty=$yarnRow[2];
							$amount=$yarnRow[3];
													
							$yarn_desc=$yarn_count_library[$count_id]."**".$yarn_type[$type_id];
							$req_qnty=($plan_cut_qnty/$dzn_qnty)*$cons_qnty;
							$req_amnt=($plan_cut_qnty/$dzn_qnty)*$amount;
							 
							$yarn_desc_array[$yarn_desc]['qnty']+=$req_qnty;
							$yarn_desc_array[$yarn_desc]['amnt']+=$req_amnt;
						}
						 
						$tot_knit_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
						$tot_yarn_dye_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge']; 
						$tot_dye_finish_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];			  
                        ?>
                        <td width="220">
                            <p>
                                <? $fabric_desc=explode(",",$fabric_desc); echo join(",<br>",array_unique($fabric_desc)); ?>
                            </p>
                        </td>
                        <td width="70" align="right"><? echo fn_number_format($knit_fabric_cons,2,'.',''); ?></td>
                        <td width="60" align="right"><? echo fn_number_format($knit_fabric_rate,2,'.',''); ?></td>
                        <td width="70" align="right"><? echo fn_number_format($woven_fabric_cons,2,'.',''); ?></td>
                        <td width="65" align="right"><? echo fn_number_format($woven_fabric_rate,2,'.',''); ?></td>
                        <?
							if($fabric_cost_per_dzn>0) $td_color=""; else $td_color="#FF0000";
						?>
                        <td width="80" align="right" title="<? echo $costing_per[$costing_per_id];?>" bgcolor="<? echo $td_color; ?>">
                        <? 
                            if($fabric_cost_per_dzn) echo fn_number_format($fabric_cost_per_dzn,2,'.','').' '.$cost_per_qnty;else echo '0'; 
                            $fabric_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$fabric_cost_per_dzn;
                        ?>
                        </td>
                        <?
						 	if($trims_cons_cost>0) $trims_td_color=""; else $trims_td_color="#FF0000";
							
							$po_id=$row[csf('id')]; $po_qnty=$order_qnty_in_pcs; $po_no=$row[csf('po_number')];  $job_no=$row[csf('job_no')];
						?>
                        <td width="80" align="right" bgcolor="<? echo $trims_td_color; ?>"><? echo "<a href='#report_details' onclick= \"openmypage($po_id,'$po_qnty','$po_no','$job_no','trims_cost','Trims Cost Info');\">".fn_number_format($trims_cons_cost,2,'.','').' '.$cost_per_qnty."</a>"; ?></td>
                        <td width="80" align="right"><?  if($fabriccostArray[$row[csf('job_no')]]['embel_cost']) echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['embel_cost'],2,'.','').' '.$cost_per_qnty;else echo '0'; ?></td>
                        <?
							if($fabriccostArray[$row[csf('job_no')]]['cm_cost']>0) $cm_td_color=""; else $cm_td_color="#FF0000";
						?>
                        <td width="80" align="right" bgcolor="<? echo $cm_td_color; ?>"><? if($fabriccostArray[$row[csf('job_no')]]['cm_cost']) echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['cm_cost'],2,'.','').' '.$cost_per_qnty;else echo '0'; ?></td>
                        <td width="85" align="right">
							<? 
                                echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['commission'],2,'.',''); 
                                $comm_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
                            ?>
                        </td>
                        <td width="80" align="right">
							<? 
                                echo "<a href='#report_details' onclick= \"openmypage($po_id,'$po_qnty','$po_no','$job_no','other_cost','Other Cost Info');\">".fn_number_format($other_cost,2,'.','')."</a>";
                                $other_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$other_cost;
                            ?>
                        </td>
                        <td width="80" align="right"><? echo fn_number_format($tot_cost_per_dzn,2,'.',''); ?></td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_cm_cost,2,'.',''); 
                                $grand_tot_cm_cost+=$tot_cm_cost;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_cost,2,'.','');
                                $grand_tot_cost+=$tot_cost; 
                            ?>
                        </td>
                        <td width="65" align="right"><? echo fn_number_format($cost_per_unit,2,'.',''); ?></td>
                        <td width="65" align="right"><? echo fn_number_format($row[csf('unit_price')],2); ?></td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($row[csf('po_total_price')],2,'.',''); 
                                $tot_order_value+=$row[csf('po_total_price')];
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($margin,2,'.','');
                                $tot_margin+=$margin; 
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_trims_cost,2,'.',''); 
                                $grand_tot_trims_cost+=$tot_trims_cost;
                            ?>
                        </td>
                        <td align="right">
                            <? 
                                echo fn_number_format($tot_embell_cost,2,'.','');
                                $grand_tot_embell_cost+=$tot_embell_cost; 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                </table>
                <table class="rpt_table" width="3030" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="40"></th>
                        <th width="70"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110" align="right">Total</th>
                        <th width="90" align="right" id="total_order_qnty"><? echo fn_number_format($total_order_qnty,0); ?></th>
                        <th width="50"></th>
                        <th width="90" align="right" id="total_order_qnty_in_pcs"><? echo fn_number_format($total_order_qnty_in_pcs,0); ?></th>
                        <th width="80"></th>
                        <th width="220"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="70"></th>
                        <th width="65"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="85"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="100" align="right" id="value_tot_cm_cost"><? echo fn_number_format($grand_tot_cm_cost,2); ?></th>
                        <th width="100" align="right" id="value_tot_cost"><? echo fn_number_format($grand_tot_cost,2); ?></th>
                        <th width="65"></th>
                        <th width="65"></th>
                        <th width="100" align="right" id="value_order"><? echo fn_number_format($tot_order_value,2); ?></th>
                        <th width="100" align="right" id="value_margin"><? echo fn_number_format($tot_margin,2); ?></th>
                        <th width="100" align="right" id="value_tot_trims_cost"><? echo fn_number_format($grand_tot_trims_cost,2); ?></th>
                        <th align="right" id="value_tot_embell_cost"><? echo fn_number_format($grand_tot_embell_cost,2); ?></th>
                    </tfoot>
                </table>
            </div>
            <table>
                <tr><td height="15"></td></tr>
            </table>
            <table style="margin-left:20px" width="1500">
                <tr>
                    <td width="400" valign="top"><b><u>Cost Summary</u></b>
                        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="140">Particulars</th>
                                <th width="160">Amount</th>
                                <th>Percentage</th>
                            </thead>
                            <tr bgcolor="#E9F3FF">
                                <td>Fabric Cost</td>
                                <td align="right"><? echo fn_number_format($fabric_cost_summary,2); ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($fabric_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Trims Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_trims_cost,2); ?></td>
                               <td align="right"><? echo fn_number_format((($grand_tot_trims_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Embellish Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_embell_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format((($grand_tot_embell_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Commision Cost</td>
                                <td align="right"><? echo fn_number_format($comm_cost_summary,2); ?></td>
                                <td align="right"><? echo fn_number_format((($comm_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Other Cost</td>
                                <td align="right"><? echo fn_number_format($other_cost_summary,2); ?></td>
                                <td align="right"><? echo fn_number_format((($other_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Total Cost</td>
                                <td align="right"><? $total_cost_summ=$grand_tot_cost-$grand_tot_cm_cost; echo fn_number_format($total_cost_summ,2); ?></td>
                                <td align="right"><? echo fn_number_format((($total_cost_summ*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Total Order Value</td>
                                <td align="right"><? echo fn_number_format($tot_order_value,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_order_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>CM Value</td>
                                <td align="right">
                                    <? 
                                        $cm_value=$tot_order_value-$total_cost_summ;
                                        echo fn_number_format($cm_value,2); 
                                    ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($cm_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>CM Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_cm_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format((($grand_tot_cm_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Margin</td>
                                <td align="right">
                                    <? 
                                        $margin_value=$cm_value-$grand_tot_cm_cost;
                                        echo fn_number_format($margin_value,2); 
                                    ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($margin_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50"></td>
                    <td width="570" valign="top"><b><u>Yarn Summary</u></b>
                    	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                            	<th width="30">SL</th>
                                <th width="80">Yarn Count</th>
                                <th width="120">Type</th>
                                <th width="120">Req. Qnty</th>
                                <th width="80">Avg. rate</th>
                                <th>Amount</th>
                            </thead>
                            <?
							$s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
							
							foreach($yarn_desc_array as $key=>$value)
							{
								
								if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$yarn_desc=explode("**",$key);
								
								if($yarn_desc[0]!="")
								{
									$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty']; 
									$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
								?>
									<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
										<td><? echo $s; ?></td>
										<td align="center"><? echo $yarn_desc[0]; ?></td>
										<td><? echo $yarn_desc[1]; ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
									</tr>
								<?	
								$s++;
								}
							}
							?>
                            <tfoot>
                            	<th colspan="3" align="right">Total</th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_qnty,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_amnt,2); ?></th>
                            </tfoot>
                    	</table>  
                    </td>
                    <td width="50"></td>
                    <td width="450" valign="top"><b><u>Fabric Production Charge</u></b>
                    	<?
							$tot_prod_charge=$tot_knit_charge+$tot_yarn_dye_charge+$tot_dye_finish_charge;	  
						?>
                    	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                           		<th width="30">SL</th>
                                <th width="160">Particulars</th>
                                <th width="140">Amount</th>
                                <th>Percentage</th>
                            </thead>
                            <tr bgcolor="#E9F3FF">
                            	<td>1</td>
                                <td>Knitting Charge</td>
                                <td align="right"><? echo fn_number_format($tot_knit_charge,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_knit_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                            	<td>2</td>
                                <td>Yarn Dyeing Charge</td>
                                <td align="right"><? echo fn_number_format($tot_yarn_dye_charge,2); ?></td>
                               <td align="right"><? echo fn_number_format((($tot_yarn_dye_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                            	<td>3</td>
                                <td>Dyeing & Finishing Charge</td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_charge,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_dye_finish_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tfoot>
                            	<th colspan="2" align="right">Total</th>
                                <th align="right"><? echo fn_number_format($tot_prod_charge,2); ?></th>
                                <th align="right"><? echo fn_number_format((($tot_prod_charge*100)/$tot_prod_charge),2); ?></th>
                            </tfoot>
                    	</table>        
                    </td>
                </tr>
            </table>
            <br />
            <table>
                <tr>
                	<?
					$tot_order_value=fn_number_format($tot_order_value,2,'.','');
					$fabric_cost_summary=fn_number_format($fabric_cost_summary,2,'.','');
					$grand_tot_trims_cost=fn_number_format($grand_tot_trims_cost,2,'.','');
					$grand_tot_embell_cost=fn_number_format($grand_tot_embell_cost,2,'.','');
					$comm_cost_summary=fn_number_format($comm_cost_summary,2,'.','');
					$other_cost_summary=fn_number_format($other_cost_summary,2,'.','');
					$grand_tot_cm_cost=fn_number_format($grand_tot_cm_cost,2,'.','');
					$margin_value=fn_number_format($margin_value,2,'.','');

					$chart_data_qnty="Order Value;".$tot_order_value."\nFabric Cost;".$fabric_cost_summary."\nTrims Cost;".$grand_tot_trims_cost."\nEmbellishment Cost;".$grand_tot_embell_cost."\nCommission Cost;".$comm_cost_summary."\nOthers Cost;".$other_cost_summary."\nCM Cost;".$grand_tot_cm_cost."\nMargin;".$margin_value."\n";
					 
					?>
                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                    <td colspan="5" id="chartdiv"></td>
                </tr>
            </table>
            </fieldset>
        </div>
<?
	}


	 
	//echo "$total_data****requires/$filename****$tot_rows";

	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****1****$type";
	exit();	
}

if($action=="trims_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div>
        <fieldset style="width:600px;">
        <div style="width:600px" align="center">	
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="130">Item Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="80">Rate</th>
                    <th width="110">Trims Cost/Dzn</th>
                    <th>Total Trims Cost</th>
                </thead>
            </table>
            </div>
            <div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                        
					$dzn_qnty=0;
					if($costing_per==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//and b.po_break_down_id='$po_id' 
					$sql="select a.trim_group, a.amount,a.rate, a.cons_dzn_gmts as cons from wo_pre_cost_trim_cost_dtls a where   a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
					$trimsArray=sql_select($sql);
					$i=1;
					foreach($trimsArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="130"><div style="width:130px; word-wrap:break-word"><? echo $item_library[$row[csf('trim_group')]]; ?></div></td>
							<td width="90" align="right"><? echo fn_number_format($row[csf('cons')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('rate')],2); ?></td>
							<td width="110" align="right">
								<?
                                    $trims_cost_per_dzn=$row[csf('cons')]*$row[csf('rate')]; 
                                    echo fn_number_format($trims_cost_per_dzn,2);
									$tot_trims_cost_per_dzn+=$trims_cost_per_dzn; 
                                ?>
                            </td>
							<td align="right">
								<?
                                	$trims_cost=($po_qnty/$dzn_qnty)*$trims_cost_per_dzn;
									echo fn_number_format($trims_cost,2);
									$tot_trims_cost+=$trims_cost;
                                ?>
                            </td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo fn_number_format($tot_trims_cost_per_dzn,2); ?></th>
                        <th><? echo fn_number_format($tot_trims_cost,2); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
}

if($action=="other_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div align="center">
        <fieldset style="width:600px;">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Particulars</th>
                    <th width="90">Cost/Dzn</th>
                    <th>Total Cost</th>
                </thead>
				<?
                $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                    
                $dzn_qnty=0;
                if($costing_per==1)
                {
                    $dzn_qnty=12;
                }
                else if($costing_per==3)
                {
                    $dzn_qnty=12*2;
                }
                else if($costing_per==4)
                {
                    $dzn_qnty=12*3;
                }
                else if($costing_per==5)
                {
                    $dzn_qnty=12*4;
                }
                else
                {
                    $dzn_qnty=1;
                }
                    
                $sql="select common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
                $fabriccostArray=sql_select($sql);
                ?>
                <tr bgcolor="#E9F3FF">
                    <td>Commercial Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('comm_cost')],2); ?></td>
                    <td align="right">
                        <?
                            $comm_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('comm_cost')]; 
                            echo fn_number_format($comm_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Lab Test Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('lab_test')],2); ?></td>
                    <td align="right">
                        <?
                            $lab_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('lab_test')]; 
                            echo fn_number_format($lab_cost,2);
                        ?>
                    </td>
                </tr>
                 <tr bgcolor="#E9F3FF">
                    <td>Inspection Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('inspection')],2); ?></td>
                    <td align="right">
                        <?
                            $inspection_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('inspection')]; 
                            echo fn_number_format($inspection_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Freight Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('freight')],2); ?></td>
                    <td align="right">
                        <?
                            $freight_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('freight')]; 
                            echo fn_number_format($freight_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>Common OH Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('common_oh')],2); ?></td>
                    <td align="right">
                        <?
                            $common_oh_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('common_oh')]; 
                            echo fn_number_format($common_oh_cost,2);
							
							$tot_cost_per_dzn=$fabriccostArray[0][csf('comm_cost')]+$fabriccostArray[0][csf('lab_test')]+$fabriccostArray[0][csf('inspection')]+$fabriccostArray[0][csf('freight')]+$fabriccostArray[0][csf('common_oh')];
							$tot_cost=$comm_cost+$lab_cost+$inspection_cost+$freight_cost+$common_oh_cost;
                        ?>
                    </td>
                </tr>
                <tfoot>
                    <th>Total</th>
                    <th><? echo fn_number_format($tot_cost_per_dzn,2); ?></th>
                    <th><? echo fn_number_format($tot_cost,2); ?></th>
                </tfoot>    
            </table>
        </fieldset>
    </div>
<?
}
disconnect($con);
?>