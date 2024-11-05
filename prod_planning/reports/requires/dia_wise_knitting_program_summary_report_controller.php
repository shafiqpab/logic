<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
 	if($template==1)
	{
		$company_name=$cbo_company_name;
		//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
					$buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
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
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
			$buyer_id_cond2=" and buyer_id=$cbo_buyer_name";
		}
		
		ob_start();
		
		$plan_array=array();
		$sql_plan=sql_select("select booking_no, po_id, dia, yarn_desc, sum(program_qnty) as program_qnty from ppl_planning_entry_plan_dtls where company_id=$company_name and status_active=1 and is_deleted=0 $buyer_id_cond2 group by booking_no, po_id, dia, yarn_desc"); //and buyer_id like '$buyer_name'

		foreach($sql_plan as $planDataArray)
		{
			$plan_array[$planDataArray[csf('booking_no')]][$planDataArray[csf('po_id')]][$planDataArray[csf('dia')]][$planDataArray[csf('yarn_desc')]]=$planDataArray[csf('program_qnty')];
		}
		$prod_array=array();
		$sql_prod=sql_select("select b.machine_dia, 
		sum(CASE WHEN a.knitting_source=1 AND b.body_part_id not in(2,3,4,8,22) THEN b.grey_receive_qnty ELSE 0 END) AS prod_qnty_in,
		sum(CASE WHEN a.knitting_source=3 AND b.body_part_id not in(2,3,4,8,22) THEN b.grey_receive_qnty ELSE 0 END) AS prod_qnty_out,
		sum(CASE WHEN a.knitting_source=1 AND b.body_part_id not in(4,8,22) THEN b.grey_receive_qnty ELSE 0 END) AS prod_qnty_in_rib,
		sum(CASE WHEN a.knitting_source=3 AND b.body_part_id not in(4,8,22) THEN b.grey_receive_qnty ELSE 0 END) AS prod_qnty_out_rib
		
		from pro_grey_prod_entry_dtls b,inv_receive_master a where a.id=b.mst_id and a.company_id=$company_name and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond2 group by b.machine_dia"); //and buyer_id like '$buyer_name'

		foreach($sql_prod as $row)
		{
			$prod_array[$row[csf('machine_dia')]]['prod_in']=$row[csf('prod_qnty_in')];
			$prod_array[$row[csf('machine_dia')]]['prod_out']=$row[csf('prod_qnty_out')];
			$prod_array[$row[csf('machine_dia')]]['prod_in_rib']=$row[csf('prod_qnty_in_rib')];
			$prod_array[$row[csf('machine_dia')]]['prod_out_rib']=$row[csf('prod_qnty_out_rib')];
		}
		//print_r($plan_array);
		//echo $plan_array['ASL-Fb-13-00009'][577][34][22];
		
		$block_qnty_rib=0; $block_qnty_sj=0; $confirmed_qnty_rib=0; $confirmed_qnty_sj=0;
		
		$pre_cost_array=return_library_array( "select id, body_part_id from wo_pre_cost_fabric_cost_dtls", "id", "body_part_id"  );
		$po_array=return_library_array( "select id, is_confirmed from wo_po_break_down", "id", "is_confirmed"  );
		
		if($db_type==0)
		{
			$machine_dia=return_field_value("group_concat(distinct(b.machine_dia) order by b.machine_dia) as machine_dia","ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b","a.id=b.mst_id and a.company_id=$company_name and a.planning_status=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond","machine_dia");//and a.buyer_id like '$buyer_name' order by cast(b.machine_dia as unsigned)
		}
		else
		{
			$sql_data=sql_select("select b.machine_dia as machine_dia from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.planning_status=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond");
			$machind_dia_ids='';
			foreach($sql_data as $row)
			{
				
				if($machind_dia_ids=='') $machind_dia_ids=$row[csf('machine_dia')];else $machind_dia_ids.=",".$row[csf('machine_dia')];
			}
			//$machine_dia=return_field_value("LISTAGG(b.machine_dia, ',') WITHIN GROUP (ORDER BY b.machine_dia) as machine_dia","ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b","a.id=b.mst_id and a.company_id=$company_name and a.planning_status=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond","machine_dia");
			$machine_dia=implode(",",array_unique(explode(",",$machind_dia_ids)));	
		}

		$sql="select a.booking_no, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.dia_width, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by b.dia_width"; //and a.buyer_id like '$buyer_name'
		$result=sql_select($sql);
		//echo $sql; //
		
		$fabric_dia_array=array(); $fabric_dia_array_sj=array(); $fabric_dia_array_rib=array();
		
		foreach($result as $row)
		{
			$program_qnty=$plan_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('dia_width')]][$row[csf('pre_cost_fabric_cost_dtls_id')]];
			$body_part_id=$pre_cost_array[$row[csf('pre_cost_fabric_cost_dtls_id')]];
			$is_confirmed=$po_array[$row[csf('po_break_down_id')]];
			
			$pending_qnty=$row[csf('qnty')]-$program_qnty;
			
			if(!($body_part_id==2 || $body_part_id==3) && $pending_qnty>0)
			{
				if($body_part_id==4 || $body_part_id==8 || $body_part_id==22)
				{
					//$fabric_dia_array[$row[csf('dia_width')]]['rib']+=$pending_qnty;
					$fabric_dia_array_rib[$row[csf('dia_width')]]+=$pending_qnty;
					if($is_confirmed==1) $confirmed_qnty_rib+=$pending_qnty; else $block_qnty_rib+=$pending_qnty;
				}
				else
				{
					//$fabric_dia_array[$row[csf('dia_width')]]['sj']+=$pending_qnty;
					$fabric_dia_array_sj[$row[csf('dia_width')]]+=$pending_qnty;
					if($is_confirmed==1) $confirmed_qnty_sj+=$pending_qnty; else $block_qnty_sj+=$pending_qnty;
				}
				
				$fabric_dia_array[$row[csf('dia_width')]]=$row[csf('dia_width')];
				
			} //end if condition
		}// end for each
		
		$machine_dia=array_filter(explode(",",$machine_dia));
		
		$tbl_width=300+count($machine_dia)*100;
		$colspan=count($machine_dia);
		?>
        <fieldset style="width:100%;margin-left:10px">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="<? echo count($fabric_dia_array)+2; ?>" style="font-size:16px"><strong>Total Dia Wise Knitting Program</strong></td>
                </tr>
            </table>
            <div align="left" style="margin-left:10px"><b><u>Program Done</u></b></div>
            <table style="margin-left:10px" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-20; ?>" class="rpt_table" >
                <thead>
                    <th width="120">Machine Dia</th>
                    <?
						foreach($machine_dia as $val)
						{
							echo "<th width='100'>".$val."</th>";//\"
						}
					?>
                    <th>Total</th>
                </thead>
            </table>
			<div style="width:<? echo $tbl_width-20; ?>px; max-height:330px;margin-left:10px" id="scroll_div" align="center"> <!--overflow-y:scroll;-->
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-20; ?>" class="rpt_table" id="tbl_list_search">
                <?
					$i=0; $total_dia=count($machine_dia); $dia_array=array(); $sql= "SELECT ";
					if($total_dia>0)
					{
						foreach($machine_dia as $dia)
						{
							if($i==0) $add_comma=""; else $add_comma=",";
							
							$sql.="$add_comma sum(CASE WHEN b.machine_dia='$dia' and b.knitting_source='1' and a.body_part_id not in(2,3,4,8,22) THEN b.program_qnty END) AS qnty_inside_sj_$dia,
									sum(CASE WHEN b.machine_dia='$dia' and b.knitting_source='3' and a.body_part_id not in(2,3,4,8,22) THEN b.program_qnty END) AS qnty_outside_sj_$dia,
									sum(CASE WHEN b.machine_dia='$dia' and b.knitting_source='1' and a.body_part_id in(4,8,22) THEN b.program_qnty END) AS qnty_inside_rib_$dia,
									sum(CASE WHEN b.machine_dia='$dia' and b.knitting_source='3' and a.body_part_id in(4,8,22) THEN b.program_qnty END) AS qnty_outside_rib_$dia ";
							$i++;
						}
				 
						$sql.="from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.planning_status=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond";// and a.buyer_id like '$buyer_name'
						//echo $sql;
						$dataArray=sql_select($sql);
					}
					else $dataArray=array();
					?>
                    <tr bgcolor="#FFFFFF" id="tr_1" onclick="change_color('tr_1','#FFFFFF')">
                        <td width="120">S/J Inside</td>
                        <?
                            $total_qnty_sj_in=0;
                            foreach($machine_dia as $dia)
                            { 
                                 $prod_in_qty=$prod_array[$dia]['prod_in'];
								echo "<td width='100' align='right'>".number_format($dataArray[0][csf('qnty_inside_sj_'.$dia)]-$prod_in_qty,2,'.','')."</td>";
                                $total_qnty_sj_in+=$dataArray[0][csf('qnty_inside_sj_'.$dia)]-$prod_in_qty;
                                $dia_array[$dia]+=$dataArray[0][csf('qnty_inside_sj_'.$dia)]-$prod_in_qty;
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_sj_in,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF" id="tr_2" onclick="change_color('tr_2','#E9F3FF')">
                        <td width="120">S/J Outside</td>
                        <?
                            $total_qnty_sj_out=0;
                            foreach($machine_dia as $dia)
                            { 
                                $prod_out_qty=$prod_array[$dia]['prod_out'];
								echo "<td width='100' align='right'>".number_format($dataArray[0][csf('qnty_outside_sj_'.$dia)]-$prod_out_qty,2,'.','')."</td>";
                                $total_qnty_sj_out+=$dataArray[0][csf('qnty_outside_sj_'.$dia)]-$prod_out_qty;
                                $dia_array[$dia]+=$dataArray[0][csf('qnty_outside_sj_'.$dia)]-$prod_out_qty;
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_sj_out,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF" id="tr_3" onclick="change_color('tr_3','#FFFFFF')">
                        <td width="120">RIB Inside</td>
                        <?
                            $total_qnty_rib_in=0;
                            foreach($machine_dia as $dia)
                            { 
                                // $prod_in_rib_qty=$prod_array[$dia]['prod_in_rib'];
								echo "<td width='100' align='right'>".number_format($dataArray[0][csf('qnty_inside_rib_'.$dia)]-$prod_in_rib_qty,2,'.','')."</td>";
                                $total_qnty_rib_in+=$dataArray[0][csf('qnty_inside_rib_'.$dia)]-$prod_in_rib_qty;
                                $dia_array[$dia]+=$dataArray[0][csf('qnty_inside_rib_'.$dia)]-$prod_in_rib_qty;
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_rib_in,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF" id="tr_4" onclick="change_color('tr_4','#E9F3FF')">
                        <td width="120">RIB Outside</td>
                        <?
                            $total_qnty_rib_out=0;
                            foreach($machine_dia as $dia)
                            { 
                                //$prod_out_rib_qty=$prod_array[$dia]['prod_out_rib'];
								echo "<td width='100' align='right'>".number_format($dataArray[0][csf('qnty_outside_rib_'.$dia)]-$prod_out_rib_qty,2,'.','')."</td>";
                                $total_qnty_rib_out+=$dataArray[0][csf('qnty_outside_rib_'.$dia)]-$prod_out_rib_qty;
                                $dia_array[$dia]+=$dataArray[0][csf('qnty_outside_rib_'.$dia)]-$prod_out_rib_qty;
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_rib_out,2,'.',''); ?></td>
                    </tr>
                    <tfoot>
                        <th>Sub Total</th>
                        <?
                            $sub_total_qnty=0;
                            foreach($machine_dia as $dia)
                            { 
                                echo "<th width='100' align='right'>".number_format($dia_array[$dia],2,'.','')."</th>";
                                $sub_total_qnty+=$dia_array[$dia];
                            }
                        ?>
                        <th align="right"><? echo number_format($sub_total_qnty,2,'.',''); ?></th>
                    </tfoot>
                </table>
			</div>
            <br />
            <?
				$tbl_width=260+count($fabric_dia_array)*100;
			?>
            <div align="left" style="margin-left:10px"><b><u>Pending Program/ Unprogram</u></b></div>
            <table style="margin-left:10px" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
                <thead>
                    <th width="120">Fabric Dia</th>
                    <?
						foreach($fabric_dia_array as $val)
						{
							echo "<th width='100'>".$val."</th>";
						}
					?>
                    <th>Total</th>
                </thead>
                <tbody>
                    <tr bgcolor="#FFFFFF" id="trr_1" onclick="change_color('trr_1','#FFFFFF')">
                        <td width="120">S/J</td>
                        <? 
                            $total_qnty_sj_pending=0; $fb_dia_array=array();
                            foreach($fabric_dia_array as $fb_dia)
                            {  
                                echo "<td width='100' align='right'>".number_format($fabric_dia_array_sj[$fb_dia],2,'.','')."</td>";
                                $total_qnty_sj_pending+=$fabric_dia_array_sj[$fb_dia];
                                $fb_dia_array[$fb_dia]+=$fabric_dia_array_sj[$fb_dia];
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_sj_pending,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF" id="trr_2" onclick="change_color('trr_2','#E9F3FF')">
                        <td width="120">RIB</td>
                        <?
                            $total_qnty_rib_pending=0;
                            foreach($fabric_dia_array as $fb_dia)
                            { 
                                echo "<td width='100' align='right'>".number_format($fabric_dia_array_rib[$fb_dia],2,'.','')."</td>";
                                $total_qnty_rib_pending+=$fabric_dia_array_rib[$fb_dia];
                                $fb_dia_array[$fb_dia]+=$fabric_dia_array_rib[$fb_dia];
                            }
                        ?>
                        <td align="right"><? echo number_format($total_qnty_rib_pending,2,'.',''); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <th>Sub Total</th>
                    <?
                        $sub_total_qnty_pending=0;
                        foreach($fabric_dia_array as $fb_dia)
                        { 
                            echo "<th width='100' align='right'>".number_format($fb_dia_array[$fb_dia],2,'.','')."</th>";
                            $sub_total_qnty_pending+=$fb_dia_array[$fb_dia];
                        }
                    ?>
                    <th align="right"><? echo number_format($sub_total_qnty_pending,2,'.',''); ?></th>
                </tfoot>
            </table>
            <br />
            <?
				$tot_inside=$total_qnty_sj_in+$total_qnty_rib_in;
				$tot_outside=$total_qnty_sj_out+$total_qnty_rib_out;
				$tot_program_qnty=$tot_inside+$tot_outside;
				$tot_inside_perc=($tot_inside/$tot_program_qnty)*100;
				$tot_outside_perc=($tot_outside/$tot_program_qnty)*100;
				
				$tot_block_qnty=$block_qnty_rib+$block_qnty_sj;
				$tot_confirmed_qnty=$confirmed_qnty_rib+$confirmed_qnty_sj;
				
				$tot_sj=$block_qnty_sj+$confirmed_qnty_sj;
				$tot_rib=$block_qnty_rib+$confirmed_qnty_rib;
				
				$tot_pending_qnty=$tot_sj+$tot_rib;
				
				$tot_block_perc=($tot_block_qnty/$tot_pending_qnty)*100;
				$tot_confirmed_perc=($tot_confirmed_qnty/$tot_pending_qnty)*100;
				
				$sj_perc=($tot_sj/$tot_pending_qnty)*100;
				$rib_perc=($tot_rib/$tot_pending_qnty)*100;
				
				$grand_tot_booking_qnty=$tot_pending_qnty+$tot_program_qnty;
				$grand_tot_pending_qnty=$grand_tot_booking_qnty-$tot_program_qnty;
				
				$grand_program_perc=($tot_program_qnty/$grand_tot_booking_qnty)*100;
				$grand_pending_perc=($grand_tot_pending_qnty/$grand_tot_booking_qnty)*100;
			?>
            <table style="margin-left:10px" width="1500" border="0">
            	<tr>
                	<td valign="top" width="360">
                    	<div align="left"><b><u>Program/Plan Done</u></b></div>
                    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
                        	<thead>
                                <th width="120">Inside</th>
                                <th width="120">Outside</th>
                                <th>Total</th>
               				</thead>
                        	<tr bgcolor="#FFFFFF" id="trd_1" onclick="change_color('trd_1','#FFFFFF')">
                            	<td align="center"><? echo number_format($tot_inside,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="center"><? echo number_format($tot_outside,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="center"><? echo number_format($tot_program_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#E9F3FF" id="trd_2" onclick="change_color('trd_2','#E9F3FF')">
                            	<td align="center"><? echo number_format($tot_inside_perc,2,'.','')."%"; ?>&nbsp;&nbsp;</td>
                                <td align="center"><? echo number_format($tot_outside_perc,2,'.','')."%"; ?>&nbsp;&nbsp;</td>
                                <td align="center"><? echo number_format($tot_inside_perc+$tot_outside_perc,2,'.','')."%"; ?>&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td></td>
                    <td valign="top" width="560">
                    	<div align="left"><b><u>Pending Program/Plan</u></b></div>
                    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table">
                        	<thead>
                                <th width="120">Particulars</th>
                                <th width="110">S/J</th>
                                <th width="110">RIB</th>
                                <th width="120">Total</th>
                                <th>%</th>
               				</thead>
                        	<tr bgcolor="#FFFFFF" id="trd_3" onclick="change_color('trd_3','#FFFFFF')">
                            	<td>Block Booking</td>
                            	<td align="right"><? echo number_format($block_qnty_sj,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($block_qnty_rib,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_block_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_block_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#E9F3FF" id="trd_4" onclick="change_color('trd_4','#E9F3FF')">
                            	<td>Confirm Order</td>
                            	<td align="right"><? echo number_format($confirmed_qnty_sj,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($confirmed_qnty_rib,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_confirmed_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_confirmed_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#FFFFFF" id="trd_5" onclick="change_color('trd_5','#FFFFFF')">
                            	<td>Total</td>
                            	<td align="right"><? echo number_format($tot_sj,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_rib,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_pending_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($tot_block_perc+$tot_confirmed_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#E9F3FF" id="trd_6" onclick="change_color('trd_6','#E9F3FF')">
                            	<td>In %</td>
                            	<td align="right"><? echo number_format($sj_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($rib_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($sj_perc+$rib_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right">&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td></td>
                    <td valign="top">
                    	<div align="left"><b><u>Grand Summary</u></b></div>
                    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table">
                        	<thead>
                                <th width="200">Particulars</th>
                                <th width="120">Qnty</th>
                                <th>%</th>
               				</thead>
                        	<tr bgcolor="#FFFFFF" id="trd_7" onclick="change_color('trd_7','#FFFFFF')">
                            	<td>Total Booking Qnty.</td>
                                <td align="right"><? echo number_format($grand_tot_booking_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($grand_program_perc+$grand_pending_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#E9F3FF" id="trd_8" onclick="change_color('trd_8','#E9F3FF')">
                            	<td>Total Program Qnty.</td>
                                <td align="right"><? echo number_format($tot_program_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($grand_program_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                            <tr bgcolor="#FFFFFF" id="trd_9" onclick="change_color('trd_9','#FFFFFF')">
                            	<td>Total Pending Program Qnty.</td>
                                <td align="right"><? echo number_format($grand_tot_pending_qnty,2,'.',''); ?>&nbsp;&nbsp;</td>
                                <td align="right"><? echo number_format($grand_pending_perc,2,'.',''); ?>&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
      	</fieldset>      
	<? 
	}
	
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


?>