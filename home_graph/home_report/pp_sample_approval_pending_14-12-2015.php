<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create PP Sample Approval Pending for home page
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	12.11.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
 { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
extract($_REQUEST);
include('../includes/common.php');

echo load_html_head_contents("PP Sample Approval Pending", "../", "", $popup, $unicode, $multi_select, $amchart);

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= $cbo_company_name;
	
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  ); 
	$buyer_full_name_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library = return_library_array( "select id,color_name from lib_color order by id", "id", "color_name"  );
	$color_id_arr=return_library_array( "select color_mst_id, color_number_id from wo_po_color_size_breakdown", "color_mst_id", "color_number_id"  );
	//$sample_library = return_library_array( "select id,sample_name from lib_sample order by id", "id", "sample_name"  ); 
	//$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );

	if($cbo_company_name==0) $company_name_con=""; else $company_name_con=" and a.company_name=$company_name";
	
	//echo "select b.buyer_id from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and a.is_deleted=0 and a.sample_type=2 and b.sequ is NOT NULL  group by b.buyer_id order by b.buyer_id";
	
	if($db_type==0)
	{
		$buyer_con=return_field_value("group_concate(b.buyer_id) as buyer_id","lib_sample a,lib_buyer_tag_sample b","a.id=b.tag_sample and a.is_deleted=0 and a.sample_type=2 and b.sequ<>''","buyer_id");
		$buyer_con=implode(",",array_unique(explode(",",$buyer_con)));
	
		$sql = sql_select("SELECT po_break_down_id,color_number_id from wo_po_sample_approval_info where status_active=1 and is_deleted=0 group by po_break_down_id,color_number_id order by color_number_id,po_break_down_id");
		
		$master_sql = sql_select("select distinct a.style_ref_no, a.buyer_name, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.shipment_date from wo_po_details_master a, wo_po_break_down b, wo_po_sample_approval_info c
		where  
		a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.shiping_status!=3 and c.approval_status!=3 and a.buyer_name in ($buyer_con) and 			
		a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $company_name_con
		group by c.po_break_down_id,c.color_number_id order by b.shipment_date,b.id");
	}
	else if($db_type==2)
	{
		 $buyer_con=return_field_value("listagg(cast(b.buyer_id as varchar(4000)),',') within group(order by b.buyer_id) as buyer_id","lib_sample a,lib_buyer_tag_sample b","a.id=b.tag_sample and a.is_deleted=0 and a.sample_type=2 and b.sequ is NOT NULL","buyer_id");
		$buyer_con=implode(",",array_unique(explode(",",$buyer_con)));
	
		$sql = sql_select("SELECT po_break_down_id,color_number_id,sample_type_id from wo_po_sample_approval_info where  status_active=1 and is_deleted=0 group by po_break_down_id,color_number_id,sample_type_id order by color_number_id,po_break_down_id");
		
		$master_sql = sql_select("select distinct a.style_ref_no, a.buyer_name, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.shipment_date,b.po_received_date
		from wo_po_details_master a, wo_po_break_down b, wo_po_sample_approval_info c
		where  
		a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.shiping_status!=3 and c.approval_status!=3 and a.buyer_name in ($buyer_con) and  			
		a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $company_name_con
		group by  b.id, b.po_number, b.shipment_date, a.style_ref_no,a.buyer_name,a.dealing_marchant, a.job_no_prefix_num,b.po_received_date
		order by b.id");
	}
	
	$reference_arr=array();
	foreach($sql as $row)
	{
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['po_break_down_id']=$row[csf('po_break_down_id')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['color_number_id']=$row[csf('color_number_id')];
	}
	
	$sql_tna=sql_select("select job_no, po_number_id, task_number, task_finish_date from tna_process_mst where status_active=1 and is_deleted=0 and task_number='12'");
	$task_finish_date_arr= array();
	foreach($sql_tna as $row)
	{
		$task_finish_date_arr[$row[csf('po_number_id')]]=date("Y-m-d", strtotime($row[csf('task_finish_date')]));
	}
	
	
	/*$buyer_sample=sql_select("select b.buyer_id,b.TAG_SAMPLE,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and a.is_deleted=0 and a.sample_type=2 and b.sequ is NOT NULL order by b.buyer_id,b.tag_sample,b.sequ");
	$buyer_sample_arr=array();
	foreach($buyer_sample as $row_sample)
	{
		$buyer_sample_arr[$row_sample[csf('buyer_id')]][$row_sample[csf('TAG_SAMPLE')]]=$row_sample[csf('sequ')];
	}*/
	//var_dump($buyer_sample_arr);
	?>
    <style>
	hr
	{
		color:#666;
	}
	</style>
    <div align="center"> 
    <h3 style="top:1px;width:98%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> PP Sample Approval Pending </h3>
    </div>
	<br />
    <div align="center" style="height:100%; width:2000px; border:#FF0000; border-color:#F00;">
		 <div id="summery_div_show" align="left" style="float:left; margin-left:100px;"></div>
		 <div style="width:1000px; height:250px; float:right; position:relative; margin-top:2px; border:solid 1px">
             <div id="chartdiv" style="width:1200px; height:400px; float:right; background-color:#FFFFFF"></div>
		</div>
	</div>
    
    <div align="center" style="width:100px;"> &nbsp;&nbsp;&nbsp;</div>
	<br />
    
    <div id="month_summery_div_show" align="center"></div>
    <br />
    <div style="width:100%;" align="center">
    <fieldset style="width:857px;">
    <table width="840" border="1" rules="all"  class="rpt_table" align="left">
        <thead>
            <tr> 
                <th width="50">SL</th>
                <th width="150">Buyer Name<br><hr style="width:100%; border:1px dotted;">Job Number <br><hr style="width:100%; border:1px dotted">Order Number</th>
                <th width="150">Dealing Marchant</th>
                <th width="100">Style Ref.</th> 
                <th width="80">Shipment Date</th>
                <th width="100">Color</th>
                <th width="80">PO Received Date</th>
                <th width="50"> Days <br><hr style="width:100%; border:1px dotted;"> Today Vs PO Rev. Date</th>
                <th width="80">TNA Last Date</th>
             </tr>
    </thead>
    </table>
    <div style="width:857px; max-height:300px; overflow-y:scroll" >	
        <table align="left" id="tbl_details" width="840" border="1"  class="rpt_table" rules="all">
			<?	
            $k=0;
			$pp=0;
			$j=0;
			$po_array=array();
			$order_sum_arr=array();
			$pp_sum_arr=array();
			$tna_over_sum_arr=array();
			$order_res_month=array();
			$pp_res_month=array();
			$tna_over_res_month=array();
            foreach($master_sql as $row)  // Master Job  table queery ends here
            {
				$shipment_date=date("Y-m", strtotime($row[csf('shipment_date')]));
				$po_received_date=date("Y-m-d", strtotime($row[csf('po_received_date')]));										 
                foreach($reference_arr[$row[csf('id')]] as $row_result)
                {
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$j=$j+1;
						$st=0;
						if(!in_array($row[csf('po_number')],$po_array))
						{
							$po_array[$row[csf('po_number')]] = $row[csf('po_number')];
							$st=1;
							$k++;
							$order_sum_arr[$row[csf('buyer_name')]]+=1;	
							$order_res_month[$shipment_date][$row[csf('buyer_name')]]+=1;
							
							if($task_finish_date_arr[$row[csf('id')]]<$date)
							{ 
								$tna_over_sum_arr[$row[csf('buyer_name')]]+=1;	
								$tna_over_res_month[$shipment_date][$row[csf('buyer_name')]]+=1; 
							} 
						}
						if($task_finish_date_arr[$row[csf('id')]]=="") { $tnacolor=""; }
						else if($task_finish_date_arr[$row[csf('id')]]<$date)
						{
							 $tnacolor="#FF0000"; 
						} 
						else 
						{ 
							$tnacolor=""; 
						}
						if($st==0) $row_vanish = "style='color:$bgcolor'";else $row_vanish=""; 
						$pp++;
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr3_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr3_<? echo $j; ?>" >
								<td width="50" bgcolor="<? echo $tnacolor;?>"> <? if($st==1) echo $k;//."==".$pp;?> </td>
								<td width="150" align="center" <? echo $row_vanish; ?>>
								<? echo $buyer_library[$row[csf('buyer_name')]];//."==".$row[csf('buyer_name')]; ?> 
								<br><hr style="width:100%; border:1px dotted">
								<? echo $row[csf('job_no_prefix_num')]; ?>
								<br><hr style="width:100%; border:1px dotted">
								<? echo $row[csf('po_number')]; ?>
								</td>	
								<td width="150" align="center" <? echo $row_vanish; ?>><? echo $team_member_library[$row[csf('dealing_marchant')]]; ?></td>
								<td width="100" align="center" <? echo $row_vanish; ?>><p><? echo $row[csf('style_ref_no')]; ?></p></td>                                
								<td width="80" <? echo $row_vanish; ?>> <? echo change_date_format($row[csf('shipment_date')]); ?> </td>
								<?
								$color_mst_id = $row_result[('color_number_id')];		
								echo "<td width='100' align='left'><p>".$color_library[$color_id_arr[$color_mst_id]]."</p></td>";
								?>
                                <td width="80" <? echo $row_vanish; ?>><?  echo change_date_format($row[csf('po_received_date')]) ?></td>
                                <td align="center" width="50" <? echo $row_vanish; ?>><? echo datediff('d',$po_received_date,$date) //$date?></td>
								<td width="80" <? echo $row_vanish; ?>><?  echo change_date_format($task_finish_date_arr[$row[csf('id')]]) ?></td>
						</tr>
						<? 
						$pp_sum_arr[$row[csf('buyer_name')]]+=1;
						$pp_res_month[$shipment_date][$row[csf('buyer_name')]]+=1; 
                }
				
             }// Master Job  table queery ends here
            ?>
        </table>
        </div>						
    </fieldset> 
    </div>
    <br/>
    <div id="summery_div" style="display:none; visibility:hidden;">
        <fieldset style="width:480px;">
        <table width="480" class="rpt_table" border="1" rules="all" align="center">
            <thead>
                <tr>
                    <th colspan="5">Buyer Wise Summary</th>
                </tr>
                <tr>
                	<th width="30">SL</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">NO. OF Order</th>
                    <th width="100">PP Pending</th>
                    <th width="100">Pending TNA Over</th>
                </tr>
            </thead>
            <tbody>
				<?
				$catg="[";
				$pp_tna_stck .="[{ name: 'PP Pending', data:[";
				$i=0;
                $d=1; $order_sum=0; $pp_sum=0; $tna_over_sum=0;
				$co=count($order_sum_arr);
                foreach( $order_sum_arr as $buyer_id=>$value)
                {
                    if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $d; ?></td>
                        <td><? echo $buyer_full_name_library[$buyer_id]; ?></td>
                        <td align="right"><?  echo $value; $order_sum +=$value;?></td>
                        <td align="right"><?  echo $pp_sum_arr[$buyer_id]; $pp_sum+=$pp_sum_arr[$buyer_id];?></td>
                        <td align="right"><?  echo $tna_over_sum_arr[$buyer_id]; $tna_over_sum+=$tna_over_sum_arr[$buyer_id];?></td>
                    </tr>
                    <?
                    $d++;
					if($i!=$co-1) $catg .="'".$buyer_library[$buyer_id]."',"; else $catg .="'".$buyer_full_name_library[$buyer_id]."']";
					if($i!=$co-1)  $pp_tna_stck .=$pp_sum_arr[$buyer_id].","; else $pp_tna_stck .=$pp_sum_arr[$buyer_id]."";
					$i++;
                }
				 $pp_tna_stck .="], stack: 'conf'}, ";
				 
				$i=0;
				$pp_tna_stck .="{ name: 'Pending TNA Over', data:[";
				foreach( $order_sum_arr as $buyer_id=>$value)
                {
					if($i!=$co-1)  $pp_tna_stck .=$tna_over_sum_arr[$buyer_id].","; else $pp_tna_stck .=$tna_over_sum_arr[$buyer_id]."";
					$i++;
				}
				 $pp_tna_stck .="], stack: 'conf', color: 'red'}] ";
				//echo $pp_tna_stck;
                ?>
            </tbody>
            <tfoot>
            	<tr>
                    <th colspan="2" align="center">Total</th>
                    <th align="right"><? echo $order_sum; ?></th>
                    <th align="right"><? echo $pp_sum; ?></th>
                    <th align="right"><? echo $tna_over_sum; ?></th>
                </tr>
            </tfoot>
        </table>
        </fieldset>
  	</div>
    <script src="../ext_resource/hschart/hschart.js"></script>
    <script>
	
	
Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
	  
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
	  
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },

   // General
   background2: '#FF0000'
};
// Apply the theme
Highcharts.setOptions(Highcharts.theme);
	
	var datas=<? echo $pp_tna_stck; ?>;
	var msg="Total Values";
	var cur="";
	
	$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: 'Buyer Wise Summary'
			},
	
			xAxis: {
				categories: <? echo $catg; ?>
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': '+cur+" " + this.y + '<br/>' 
						+ 'Total: '+cur+" " + this.point.stackTotal;
				}
			},
	
			plotOptions: {
				column: {
					stacking: 'normal'
				}
			},
		
			series: datas
		});
	</script>
    
    <div id="month_summery_div" style="display:none; visibility:hidden;"> 
    <fieldset style="width:1200px;">
    <div align="center" style="width:100%; font-size:18px;"><b>Month Wise PP Sample Approval Pending Summary</b></div> 
    		<table width="1190" align="center">
            <tr>
				<?
				$s=0;	
				ksort($order_res_month);
                foreach( $order_res_month as $month_id=>$buyer_arr)
                {
					if($s%3==0) $tr="</tr><tr>"; else $tr=""; echo $tr;
					?>
					<td valign="top">
                        <div style="width:380px">
                            <table width="380" class="rpt_table" border="1" rules="all" align="center">
                                <thead>
                                    <tr>
                                        <th colspan="5" align="center" style="font-size:16px;"> Total Summary  <? echo $month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id)); ?> </th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="90">Buyer Name</th>
                                        <th width="90">NO. Of Order</th>
                                        <th width="80">PP Pending</th>
                                        <th width="">Pending TNA Over</th>
                                    </tr>
                            	</thead>
								<?
                                $d=1; 
                                foreach( $buyer_arr as $buyer_id=>$value)
                                {
									if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td><? echo $d; ?></td>
										<td><? echo $buyer_library[$buyer_id]; ?></td>
										<td align="right"><? echo $value; $month_wise_order_sum +=$value;?></td>
										<td align="right"><? echo $pp_res_month[$month_id][$buyer_id]; $month_wise_pp_sum +=$pp_res_month[$month_id][$buyer_id];?></td>
										<td align="right"><? echo $tna_over_res_month[$month_id][$buyer_id]; $month_wise_tna_over_sum +=$tna_over_res_month[$month_id][$buyer_id]; ?></td>
									</tr>
									<?
									$d++;
                                }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" align="right">Total</th>
                                        <th align="right"><? echo $month_wise_order_sum;  ?></th>
                                        <th align="right"><? echo $month_wise_pp_sum;  ?></th>
                                        <th align="right"><? echo $month_wise_tna_over_sum; ?></th>												
                                    </tr>
                                </tfoot>
                            </table>
                        </div>  
					</td> 
					<?
					$month_wise_order_sum=0;$month_wise_pp_sum=0; $month_wise_tna_over_sum=0;
					$s++;
                }
                ?>
            </tr>
        </table>
    </fieldset>
    </div>
    
<script>
	document.getElementById('summery_div_show').innerHTML=document.getElementById('summery_div').innerHTML;
	document.getElementById('summery_div').innerHTML="";
	
	document.getElementById('month_summery_div_show').innerHTML=document.getElementById('month_summery_div').innerHTML;
	document.getElementById('month_summery_div').innerHTML="";
</script>

	<?
	exit();
}

?>