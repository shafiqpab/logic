<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );  
	exit();	 
} 

if ($action=="load_drop_down_order_garment")
{
	$gmt_item_arr=return_library_array( "select gmts_item_id from wo_po_details_master where job_no='".$data."' and status_active=1",'id','gmts_item_id');
	//print_r($gmt_item_arr);
    $gmt_item_id=implode(",",$gmt_item_arr);
	if(count(explode(",",$gmt_item_id))==1)
	{
		echo create_drop_down( "cbogmtsitem", 130, $garments_item,"", 1, "-- Select Item --", $gmt_item_id, "","",$gmt_item_id);
	}
    else if(count(explode(",",$gmt_item_id))>1)
	{
		echo create_drop_down( "cbogmtsitem", 130, $garments_item,"", 1, "-- Select Item --", $selected, "","",$gmt_item_id);
	}
	else if(count(explode(",",$gmt_item_id))==0)
	{
		echo create_drop_down( "cbogmtsitem", 130, $blank_array,"", 1, "-- Select Item --", $selected, "","");
	}
	exit();
}

if($action=="color_search_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//echo $txt_job_no;die;
	
    ?>
	<script>
		function js_set_order(strCon ) 
		{
		document.getElementById('hidden_color_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <input type="hidden" id="hidden_color_id" />
    		<?php
                $color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
    			$sql_color="select a.color_number_id from wo_pre_stripe_color a where a.job_no='".$txt_job_no."' and a.item_number_id='".$cbogmtsitem."' and status_active=1 and is_deleted=0 group by a.color_number_id";
    			//echo $sql_color;die;
                $arr=array (0=>$color_arr);
                echo create_list_view("list_view", "Color Name","250","300","270",0, $sql_color , "js_set_order", "color_number_id", "", 1, "color_number_id", $arr, "color_number_id", "","setFilterGrid('list_view',-1)") ;	
             ?>   
        </form>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="twisted_color_search_popup")
{
    echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    //echo $txt_job_no;die;
    ?>
    <script>
        function js_set_value(strCon ) 
        {
            document.getElementById('hidden_sample_color_id').value =document.getElementById('hdn_sample_color_'+strCon).value;
            document.getElementById('hidden_strip_color_id').value  =document.getElementById('hdn_strip_color_'+strCon).value;;
            document.getElementById('hidden_twist_color_id').value  =document.getElementById('hdn_twist_color_'+strCon).value;;
            document.getElementById('hidden_ydw_id').value          =document.getElementById('hdn_ydw_id_'+strCon).value;;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <input type="hidden" id="hidden_sample_color_id" />
        <input type="hidden" id="hidden_strip_color_id" />
        <input type="hidden" id="hidden_ydw_id" />
        <input type="hidden" id="hidden_twist_color_id" />
            <?php
             $data_array_strip=sql_select("select a.sample_color, a.sample_per, a.stripe_color, a.measurement, a.sample_per from wo_pre_stripe_color a where a.job_no='".$txt_job_no."' and a.item_number_id=$cbogmtsitem and a.color_number_id=$gmt_color_id and a.status_active=1 and a.is_deleted=0 order by a.sample_color");
            $striple_colors="";
            foreach ($data_array_strip as $key => $value) {
                if($striple_colors) $striple_colors.=",";
                $striple_colors.=$value[csf('stripe_color')];
                $sample_color_arr[$value[csf('stripe_color')]]=$value[csf('sample_color')];
            }

            $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
            //$sql = "select b.ydw_no, c.mst_id, LISTAGG(CAST(a.color AS VARCHAR2(1000)), ',') WITHIN GROUP (ORDER BY a.id desc) as strip_colors, c.id, c.yarn_color from wo_yarn_dyeing_dtls a, wo_yarn_dyeing_mst b, wo_yarn_dyeing_dtls_fin_prod c where a.mst_id=b.id and b.id=c.mst_id and a.job_no='".$txt_job_no."' and a.color in ($striple_colors) group by b.ydw_no, c.mst_id, c.id, c.yarn_color";
			
			$sql = "select b.ydw_no, a.id, a.mst_id, a.color as strip_colors from wo_yarn_dyeing_dtls a, wo_yarn_dyeing_mst b where a.mst_id=b.id and a.job_no='".$txt_job_no."' and a.color in ($striple_colors) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            $color_size_result=sql_select($sql); $twiestmstYarnArr=array(); $mstId="";
			foreach($color_size_result as $trow)
			{
				$twiestmstYarnArr[$trow[csf('id')]]['yw']=$trow[csf('ydw_no')];
				$twiestmstYarnArr[$trow[csf('id')]]['color']=$trow[csf('strip_colors')];
				
				if($mstId=="") $mstId=$trow[csf('mst_id')]; else $mstId.=','.$trow[csf('mst_id')];
			}
			
			$sqlT="select id, mst_id, dtls_id, yarn_color from wo_yarn_dyeing_dtls_fin_prod where status_active=1 and is_deleted=0 and mst_id in ($mstId) and job_no='".$txt_job_no."'";
			
			$yarnColorTwiest=sql_select($sqlT); $twiestYarnArr=array();
			foreach($yarnColorTwiest as $yrow)
			{
				$dtlsidarr=explode(",", $yrow[csf('dtls_id')]);
				$ydwNo=""; $stripeColor="";
				foreach($dtlsidarr as $did)
				{
					if($ydwNo=="") $ydwNo=$twiestmstYarnArr[$did]['yw']; else $ydwNo.=','.$twiestmstYarnArr[$did]['yw'];
					if($stripeColor=="") $stripeColor=$twiestmstYarnArr[$did]['color']; else $stripeColor.=','.$twiestmstYarnArr[$did]['color'];
				}
				$ydwNoStr=implode(",",array_filter(array_unique(explode(",", $ydwNo))));
				
				$twiestYarnArr[$yrow[csf('id')]]['yw']=$ydwNoStr;
				$twiestYarnArr[$yrow[csf('id')]]['stripe_colors']=$stripeColor;
				$twiestYarnArr[$yrow[csf('id')]]['mstid']=$yrow[csf('mst_id')];
				$twiestYarnArr[$yrow[csf('id')]]['yarn_color']=$yrow[csf('yarn_color')];
			}
            ?> 

        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="650" id="tbl_size_wise_weight">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="120">Wo No</th>
                    <th width="80">Sample Color</th>
                    <th width="200" >Yarn Color</th>
                    <th width="200">Twisting Color</th>
                </tr>
            </thead>
            <tbody>
                <style type="text/css">
                hr {
                    border: 0; 
                    background-color: #789EA7;
                    height: 1px;
                }  
            </style>
            <?php
                $sl=1;
				
				foreach($twiestYarnArr as $cid=>$exdata)
				{ 
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
					?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$sl;?>" onClick="js_set_value(<?=$sl; ?>);"> 
							<td width="30" valign="middle" align="center"><?=$sl; ?> </td>
							<td width="120" valign="middle" align="center"><?=$exdata['yw'];// $row[csf('ydw_no')]; ?> </td>
							<td width="80"  align="center">
								
							 <?php
								$scolor_arr=array_filter(array_unique(explode(",", $exdata['stripe_colors'])));//$row[csf('strip_colors')];
								$spsl=1;
								$sample_color_string="";
								foreach ($scolor_arr as  $scolor) {
									if($spsl>1) echo "<hr>";
									if($spsl>1) $sample_color_string.=",";
									echo $color_arr[$sample_color_arr[$scolor]]."<br/>";
									$spsl++;
									$sample_color_string.=$sample_color_arr[$scolor];
								}
							  ?>
								<input type="hidden" name="hdn_sample_color[]" id="hdn_sample_color_<?=$sl; ?>" value="<?=$sample_color_string; ?>">
								<input type="hidden" name="hdn_strip_color[]" id="hdn_strip_color_<?=$sl; ?>" value="<?=$exdata['stripe_colors']; ?>">
								<input type="hidden" name="hdn_ydw_id[]" id="hdn_ydw_id_<?=$sl;?>" value="<?=$exdata['mstid']; ?>">
								<input type="hidden" name="hdn_twist_color[]" id="hdn_twist_color_<?=$sl; ?>" value="<?=$exdata['yarn_color']; ?>"> 
							</td>
							<td width="200"  align="center">
								<?php
								$scolor_arr=array_filter(array_unique(explode(",", $exdata['stripe_colors'])));//$row[csf('yarn_color')];
								$ssl=1;
								foreach ($scolor_arr as  $scolor) {
									if($ssl>1) echo "<hr>";
									echo $color_arr[$scolor]."";
									$ssl++;
								}
								?>
							</td>
							<td width="200" valign="middle" align="center"><?=$color_arr[$exdata['yarn_color']]; ?>  </td>
						</tr>
					<?
					$sl++;
				}
                ?> 
            </tbody>
            </table>
        </form>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="load_update_data_color_size")
{   list($update_id,$gmt_item_id,$gmt_color_id,$txt_job_no,$cbo_yarn_type)=explode('**',$data);
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$color_size_result=sql_select("select id, mst_id, color_id, gmt_size_id, sample_weight, production_weight, production_weight_actual, short_excess, plan_cut_qty, total_weight,  avg_weight from ppl_size_set_dtls where mst_id=$update_id and item_number_id=$gmt_item_id and color_id=$gmt_color_id and status_active=1 and is_deleted=0 order by id");
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]==0)	
		{
			$avg_sample_weight=$row[csf('sample_weight')];
			$avg_production_weight=$row[csf('production_weight')];
			$total_plancut_qty=$row[csf('plan_cut_qty')];
			$total_size_weight=$row[csf('total_weight')];
			$total_avg_weight=$row[csf('avg_weight')];
		}
	}
	?>
    <br/>
    <fieldset style="width:940px; margin-left:2px">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_size_wise_weight">
            <thead>
                <tr>
                    <th width="100">Garments Size</th>
                    <th width="150">Sample Weight(GM)/Pcs</th>
                    <th width="150" style="color:#0066FF">Production Weight(GM)/Pcs</th>
                    <th width="150">Short/Excess</th>
                    <th width="150">Plan Qty (Pcs)</th>
                    <th width="100">Total Weights (KG)</th>
                    <th width="">Average Weight %</th>
                </tr>
            </thead>
            <tbody>
            	<?
					$i=1;$total_plan_qty=0;
                	foreach($color_size_result as $row)
                    { 
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('gmt_size_id')]!=0)	
						{
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                               	<td width="100">
									 <? 
                                     echo create_drop_down( "cbo_gmt_size_".$i, 100,$size_arr,"", 1, "", $row[csf('gmt_size_id')], "" ,1);
                                     ?>
                                </td>
                                <td width="150">
                                    <input type="text" id="txt_sample_weight_<?php echo $i; ?>" name="txt_sample_weight[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($row[csf('sample_weight')],4,".",""); ?>" readonly  disabled/>
								</td>
                                <td width="150" align="right"><input type="text" id="txt_production_weight_<?php echo $i; ?>" name="txt_production_weight[]" class="text_boxes_numeric" style="width:120px" value="<?php echo number_format($row[csf('production_weight')],4,".",""); ?>" placeholder="<?php echo number_format($row[csf('production_weight_actual')],4,".",""); ?>" onKeyUp="fnc_production_weight_cal(<?php echo $i; ?>)" />
                                    <input type="hidden" id="txt_production_weight_actual_<?php echo $i; ?>" name="txt_production_weight_actual[]" class="text_boxes_numeric" style="width:120px" value="<?php echo number_format($row[csf('production_weight_actual')],4,".",""); ?>" onKeyUp="fnc_production_weight_cal(<?php echo $i; ?>)" />
                                </td>
                                <td width="150"><input type="text" id="txt_short_excess_<?php echo $i; ?>" name="txt_short_excess[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($row[csf('short_excess')],4,".",""); ?>" readonly  disabled/>
                                </td>
                                <td width="150" align="right">
                                    <input type="text" id="txt_plan_qty_<?php echo $i; ?>" name="txt_plan_qty[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo $row[csf('plan_cut_qty')]; ?>" readonly  disabled/>
                              
                                </td>
                                <td align="right" width="100"><input type="text" id="txt_total_size_weight_<?php echo $i; ?>" name="txt_total_size_weight[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($row[csf('total_weight')],2,".",""); ?>" readonly  disabled/></td>
                                <td align="right"><input type="text" id="txt_avg_weight_<?php echo $i; ?>" name="txt_avg_weight[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($row[csf('avg_weight')],2,".",""); ?>" readonly  disabled/></td>
                            </tr>
					<?
						$i++;
						}
                    } 
                    ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="100">Average Total weight per Pcs</th>
                    <th width="150">
                    	<input type="text" name="txt_total_sample_weight" id="txt_total_sample_weight" class="text_boxes_numeric" style="width:120px" value="<?=number_format($avg_sample_weight,4,".",""); ?>" readonly  disabled/></th>
                    <th width="150">
                    	<input type="text" name="txt_total_production_weight" id="txt_total_production_weight" class="text_boxes_numeric" style="width:120px"  value="<?=number_format($avg_production_weight,4,".",""); ?>" readonly  disabled/></th>
                    <th width="150">
                    	<input type="text" name="txt_total_short_excess" id="txt_total_short_excess" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></th>
                    <th width="150">
                    <input type="text" name="txt_total_plan_qty" id="txt_total_plan_qty" class="text_boxes_numeric" style="width:120px"  value="<?php echo $total_plancut_qty; ?>" readonly  disabled/></th>
                    <th width="100"><input type="text" name="txt_total_weight" id="txt_total_weight" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($total_size_weight,2,".",""); ?>" readonly  disabled/></th>
                    <th width=""><input type="text" name="txt_total_avg_weight" id="txt_total_avg_weight" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($total_avg_weight,2,".",""); ?>" readonly  disabled/></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
    <br/>
    
    <?
    if($cbo_yarn_type==2)
    {
        $production_percentage_disable="disabled";
        $i=1;
		$data_array_strip=sql_select("select a.sample_color, a.sample_per, a.stripe_color, a.measurement, a.sample_per from wo_pre_stripe_color a where a.job_no='".$txt_job_no."' and a.item_number_id=$gmt_item_id and a.color_number_id=$gmt_color_id and a.status_active=1 and a.is_deleted=0 order by a.sample_color");

		$data_array_strip_update=sql_select("select id, sample_color_ids, stripe_color_ids, yarn_color_id, sample_color_percentage, production_color_percentage, consumption, process_loss, actual_consumption, cons_per_dzn, ydw_id from ppl_size_set_consumption where mst_id=$update_id and item_number_id=$gmt_item_id and color_id=$gmt_color_id and status_active=1 and is_deleted=0 order by id");
        ?>
        <fieldset style="width:940px; margin-left:2px">
            <legend>For Regular Yarn</legend>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_sample_color">
                <thead>
                    <tr>
                        <th width="" colspan="2">Consumption Per Dozen</th>
                        <th  colspan="2"><input type="text" name="txt_con_per_dzn_kg" id="txt_con_per_dzn_kg" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format(($data_array_strip_update[0][csf('cons_per_dzn')]),4); ?>" readonly  disabled/>&nbsp; Kg</th>
                        <th  colspan="2"><input type="text" name="txt_con_per_dzn_lbs" id="txt_con_per_dzn_lbs" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format(($data_array_strip_update[0][csf('cons_per_dzn')]*2.2046226),4); ?>" readonly  disabled/>&nbsp; Lbs</th>
                    </tr>
                    <tr>
                        <th width="60">Sample Color</th>
                        <th width="250">Yarn Color</th>
                        <th width="60">Sample Color %</th>
                        <th width="100" style="color:#0066FF;">Production Color %</th>
                        <th width="80">Cons (Kg)</th>
                        <th width="80">Cons (Lbs)</th>
                        <th width="80">Process Loss %</th>
                        <th width="80">Avg. Actual Cons (Kg)</th>
                        <th>Avg. Actual Cons (Lbs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                        foreach($data_array_strip as $row)
                        { 
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                                    <td width="60">
                                         <? 
                                         echo create_drop_down( "cbo_sample_color_".$i, 60,$color_arr,"", 1, "", $row[csf('sample_color')], "" ,1);
                                         ?>
                                    </td>
                                    <td width="250">
                                        <? 
                                         echo create_drop_down( "cbo_stripe_color_".$i, 250,$color_arr,"", 1, "", $row[csf('stripe_color')], "" ,1);
                                         ?>
                                    </td>
                                    <td width="60" align="right"><input type="text" id="txt_sample_color_per_<?php echo $i; ?>" name="txt_sample_color_per[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_per')];?>" readonly  disabled />
                                    </td>
                                    <td width="100"><input type="text" id="txt_production_color_per_<?php echo $i; ?>" name="txt_production_color_per[]" class="text_boxes_numeric" style="width:80px"  value="" onKeyUp="fnc_production_color_calculation(<?php echo $i; ?>)" <?php echo $production_percentage_disable; ?> />
                                    </td>
                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_<?php echo $i; ?>" name="txt_yarn_cons[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_<?php echo $i; ?>" name="txt_yarn_cons_lbs[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                    </td>
                                   <td align="right" width="80">
                                        <input type="text" id="txt_process_loss_<?php echo $i; ?>" name="txt_process_loss[]" class="text_boxes_numeric" style="width:70px"  value="0" onKeyUp="fnc_processloss_calculation(<?php echo $i; ?>,1)" <?php echo $production_percentage_disable; ?>/>
                                   </td>
                                   
                                    <td align="right" width="80"><input type="text" id="txt_yarn_actual_cons_<?php echo $i; ?>" name="txt_yarn_actual_cons[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
                                    
                                    <td align="right" width=""><input type="text" id="txt_yarn_actual_cons_lbs_<?php echo $i; ?>" name="txt_yarn_actual_cons_lbs[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
                                 
                                </tr>
                            <?
                            $total_sample_color_percentage+=$row[csf('sample_per')];
                            $i++;
                        } 
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="60">Total</th>
                        <th width="250"></th>
                        <th width="60"><?php echo number_format($total_sample_color_percentage,2); ?></th>
                        <th width="100" id="total_production_color_per"><?php echo number_format($total_production_color_percentage,2,".",""); ?></th>
                        <th width="80" id="total_cons_per_kg"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                        <th width="80" id="total_cons_per_lbs"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                        <th width="80"></th>
                        <th width="" id="total_actual_cons_per_kg"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                        <th width="" id="total_actual_cons_per_lbs"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <br/>
        <fieldset style="width:940px; margin-left:2px">
            <legend>For Twisted Yarn</legend>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_sample_color_twisted">
                <thead>
                    <tr>
                        <th colspan="4">Consumption Per Dozen <input type="text" name="txt_con_per_dzn_kg_twisted" id="txt_con_per_dzn_kg_twisted" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format($data_array_strip_update[0][csf('cons_per_dzn')],4); ?>" readonly  disabled/>&nbsp; Kg &nbsp;<input type="text" name="txt_con_per_dzn_lbs_twisted" id="txt_con_per_dzn_lbs_twisted" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format(($data_array_strip_update[0][csf('cons_per_dzn')]*2.2046226),4); ?>" readonly  disabled/>&nbsp; Lbs</th>
                        <th colspan="5">Consumption Per PCS <input type="text" name="txt_con_per_pcs_kg_twisted" id="txt_con_per_pcs_kg_twisted" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format($data_array_strip_update[0][csf('cons_per_dzn')]/12,4); ?>" readonly  disabled/>&nbsp; Kg &nbsp;<input type="text" name="txt_con_per_pcs_lbs_twisted" id="txt_con_per_pcs_lbs_twisted" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format(($data_array_strip_update[0][csf('cons_per_dzn')]*2.2046226)/12,4); ?>" readonly  disabled/>&nbsp; Lbs</th>
                    </tr>
                    <tr>
                        <th width="60">Browse Sample Color</th>
                        <th width="60">Sample Color</th>
                        <th width="250">Yarn Color</th>
                        <th width="60">Sample Color %</th>
                        <th width="100" style="color:#0066FF;">Production Color %</th>
                        <th width="80">Cons (Kg)</th>
                        <th width="80">Cons (Lbs)</th>
                        <th width="80">Process Loss %</th>
                        <th width="80">Avg. Actual Cons (Kg)</th>
                        <th>Avg. Actual Cons (Lbs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                        $i=1;
      
                        foreach($data_array_strip_update as $row)
                        { 
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
                                $row[csf('sample_color_ids')]=implode("_",explode(",", $row[csf('sample_color_ids')]) );  
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="searchtwisting<? echo $row[csf('sample_color_ids')];?>">
                                    <td width="60">
                                        
                                         <input type="text" id="txt_sample_color_browse_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_sample_color_browse[]" class="text_boxes" style="width:50px" placeholder="Browse" onDblClick="openmypage_yarn_color_twist('searchtwisting<? echo $row[csf('sample_color_ids')];?>')"/>
                                    </td> 
                                    <td width="60">
                                        
                                         <input type="text" id="txt_sample_color_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_sample_color_twisted[]" class="text_boxes" style="width:50px" value="<?php 
                                            foreach(explode("_", $row[csf('sample_color_ids')]) as $sample_color_id)
                                            {
                                                echo $color_arr[$sample_color_id]."  ";
                                            } 

                                         ?>" readonly   />
                                    </td>
                                    <td width="250">
                                        <? 
                                         echo create_drop_down( "cbo_stripe_color_twisted_".$row[csf('sample_color_ids')], 250,$color_arr,"", 1, "", $row[csf('yarn_color_id')], "" ,1,"","","","","","","cbo_stripe_color_twisted[]");
                                         ?>
                                    </td>
                                    <td width="60" align="right"><input type="text" id="txt_sample_color_per_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_sample_color_per_twisted[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_color_percentage')];?>" readonly  disabled />
                                    </td>
                                    <td width="100"><input type="text" id="txt_production_color_per_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_production_color_per_twisted[]" class="text_boxes_numeric" style="width:80px"  value="<?php echo $row[csf('production_color_percentage')];?>" onKeyUp="fnc_production_color_calculation_twisted('<?php echo $row[csf('sample_color_ids')]; ?>')"  />
                                    </td>
                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_yarn_cons_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format(($row[csf('consumption')]),4,".",""); ?>" readonly  disabled/>
                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_yarn_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format(($row[csf('consumption')]*2.2046226),4,".",""); ?>" readonly  disabled/>
                                    </td>
                                   <td align="right" width="80">
                                        <input type="text" id="txt_process_loss_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_process_loss_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format($row[csf('process_loss')],2,".",""); ?>" onKeyUp="fnc_processloss_calculation_twisted('<?php echo $row[csf('sample_color_ids')]; ?>',1)" />
                                   </td>
                                   
                                    <td align="right" width="80"><input type="text" id="txt_yarn_actual_cons_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_yarn_actual_cons_twisted[]" class="text_boxes_numeric" style="width:70px" value="<?php echo number_format(($row[csf('actual_consumption')]),4,".",""); ?>"    readonly  disabled/></td>
                                    
                                    <td align="right" width=""><input type="text" id="txt_yarn_actual_cons_lbs_twisted_<?php echo $row[csf('sample_color_ids')]; ?>" name="txt_yarn_actual_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format(($row[csf('actual_consumption')]*2.2046226),4,".",""); ?>"   readonly  disabled/>
                                    <input type="hidden" id="hidden_strip_color_ids_<?php echo $row[csf('sample_color_ids')]; ?>" name="hidden_strip_color_ids[]" value="<?php echo $row[csf('stripe_color_ids')]; ?>">
                                    <input type="hidden" id="hidden_ydw_ids" name="hidden_ydw_ids[]" value="<?php echo $row[csf('ydw_id')]; ?>">

                                    </td>
                                 
                                </tr>
                        <?
                        	$total_sample_color_percentage_twisting+=$row[csf('sample_color_percentage')];
                            $total_production_color_percentage+=$row[csf('production_color_percentage')];
                            $total_cons_per_kg+=$row[csf('consumption')];
                            $total_actual_cons_per_kg+=$row[csf('actual_consumption')];
                            $i++;
                        } 
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="60"></th>
                        <th width="60"></th>
                        <th width="250">Total</th>
                        <th width="60"><?php echo number_format($total_sample_color_percentage_twisting,2); ?></th>
                        <th width="100" id="total_production_color_per_twisted"><?php echo number_format($total_production_color_percentage,2,".",""); ?></th>
                        <th width="80" id="total_cons_per_kg_twisted"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                        <th width="80" id="total_cons_per_lbs_twisted"><?php echo number_format(($total_cons_per_kg*2.2046226),4,".",""); ?></th>
                        <th width="80"></th>
                        <th width="" id="total_actual_cons_per_kg_twisted"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                        <th width="" id="total_actual_cons_per_lbs_twisted"><?php echo number_format(($total_actual_cons_per_kg*2.2046226),4,".",""); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php
    }
    else
    {
		$i=1;
		$data_array_strip=sql_select("select id, sample_color_id, yarn_color_id, sample_color_percentage, production_color_percentage, consumption, process_loss, actual_consumption , cons_per_dzn from ppl_size_set_consumption where mst_id=$update_id and item_number_id=$gmt_item_id and color_id=$gmt_color_id and status_active=1 and is_deleted=0 order by sample_color_id");
    	?>
        <fieldset style="width:940px; margin-left:2px">
       		<legend></legend>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_sample_color">
                <thead>
                	<tr>
                        <th  colspan="4">Consumption Per Dozen <input type="text" name="txt_con_per_dzn_kg" id="txt_con_per_dzn_kg" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format($data_array_strip[0][csf('cons_per_dzn')],4); ?>" readonly  disabled/> &nbsp Kg &nbsp <input type="text" name="txt_con_per_dzn_lbs" id="txt_con_per_dzn_lbs" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format(($data_array_strip[0][csf('cons_per_dzn')]*2.2046226),4); ?>" readonly  disabled/> &nbsp Lbs</th>
                        <th  colspan="5">Consumption Per PCS <input type="text" name="txt_con_per_pcs_kg" id="txt_con_per_pcs_kg" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format($data_array_strip[0][csf('cons_per_dzn')]/12,4); ?>" readonly  disabled/> &nbsp Kg &nbsp <input type="text" name="txt_con_per_pcs_lbs" id="txt_con_per_pcs_lbs" class="text_boxes_numeric" style="width:80px"  value="<?php echo number_format(($data_array_strip[0][csf('cons_per_dzn')]*2.2046226)/12,4); ?>" readonly  disabled/> &nbsp Lbs</th>
                    </tr>
                    <tr>
                        <th width="60">Sample Color</th>
                        <th width="250">Yarn Color</th>
                        <th width="60">Sample Color %</th>
                        <th width="100" style="color:#0066FF;">Production Color %</th>
                        <th width="80">Cons (Kg)</th>
                        <th width="80">Cons (Lbs)</th>
                        <th width="80">Process Loss %</th>
                        <th width="80">Avg. Actual Cons (Kg)</th>
                        <th>Avg. Actual Cons (Lbs)</th>
                    </tr>
                </thead>
                <tbody>
                	<?
    					foreach($data_array_strip as $row)
                        { 
    							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
    						?>
    							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                                   	<td width="60">
    									 <? 
                                         echo create_drop_down( "cbo_sample_color_".$i, 60,$color_arr,"", 1, "", $row[csf('sample_color_id')], "" ,1);
                                         ?>
                                    </td>
                                    <td width="250">
                                        <? 
                                         echo create_drop_down( "cbo_stripe_color_".$i, 250,$color_arr,"", 1, "", $row[csf('yarn_color_id')], "" ,1);
                                         ?>
    								</td>
                                    <td width="60" align="right"><input type="text" id="txt_sample_color_per_<?php echo $i; ?>" name="txt_sample_color_per[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_color_percentage')];?>" readonly  disabled />
                                    </td>
                                    <td width="100"><input type="text" id="txt_production_color_per_<?php echo $i; ?>" name="txt_production_color_per[]" class="text_boxes_numeric" style="width:80px"  value="<?php echo $row[csf('production_color_percentage')];?>" onKeyUp="fnc_production_color_calculation(<?php echo $i; ?>)" />
                                    </td>
                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_<?php echo $i; ?>" name="txt_yarn_cons[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format($row[csf('consumption')],4,".",""); ?>" readonly  disabled/>

                                    <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_<?php echo $i; ?>" name="txt_yarn_cons_lbs[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format(($row[csf('consumption')]*2.2046226),4,".",""); ?>" readonly  disabled/>
                                    </td>
                                   <td align="right" width="80">
                                   		<input type="text" id="txt_process_loss_<?php echo $i; ?>" name="txt_process_loss[]" class="text_boxes_numeric" style="width:70px"  value="<?php echo number_format($row[csf('process_loss')],2,".",""); ?>" onKeyUp="fnc_processloss_calculation(<?php echo $i; ?>,1)" />
                                   </td>
                                   
                                    <td align="right" width="80">
                                    	<input type="text" id="txt_yarn_actual_cons_<?php echo $i; ?>" name="txt_yarn_actual_cons[]" class="text_boxes_numeric" style="width:70px" value="<?php echo number_format($row[csf('actual_consumption')],4,".",""); ?>"   readonly  disabled/>
                                    </td>
                                    <td align="right" width=""><input type="text" id="txt_yarn_actual_cons_lbs_<?php echo $i; ?>" name="txt_yarn_actual_cons_lbs[]" class="text_boxes_numeric" style="width:70px" value="<?php echo number_format(($row[csf('actual_consumption')]*2.2046226),4,".",""); ?>"     readonly  disabled/></td>
                                 
                                </tr>
    					<?
    						$total_sample_color_percentage+=$row[csf('sample_color_percentage')];
    						$total_production_color_percentage+=$row[csf('production_color_percentage')];
    						$total_cons_per_kg+=$row[csf('consumption')];
    						$total_actual_cons_per_kg+=$row[csf('actual_consumption')];
    						$i++;
                        } 
                        ?>
                </tbody>
                 <tfoot>
                    <tr>
                        <th width="">Total</th>
                        <th width=""></th>
                        <th width=""><?php echo number_format($total_sample_color_percentage,2,".",""); ?></th>
                        <th width="" id="total_production_color_per"><?php echo number_format($total_production_color_percentage,2,".",""); ?></th>
                        <th width="" id="total_cons_per_kg"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                        <th width="80" id="total_cons_per_lbs"><?php echo number_format(($total_cons_per_kg*2.2046226),4,".",""); ?></th>
                        <th width=""></th>
                        <th width="" id="total_actual_cons_per_kg"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                        <th width="" id="total_actual_cons_per_lbs"><?php echo number_format(($total_actual_cons_per_kg*2.2046226),4,".",""); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php 
	}
	exit();
}

if ($action=="load_data_color_size")
{   
	list($job_no,$gmtsitemid,$gmt_color_id,$yarn_type)=explode('_',$data);
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	//$color_size_result=sql_select("select a.color_number_id, c.size_order, a.gmts_sizes, sum(a.cons) as cons, sum(a.pcs) as pcs, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_pre_cos_fab_co_avg_con_dtls a, wo_po_color_size_breakdown c where a.job_no=c.job_no_mst and a.job_no='$job_no' and a.po_break_down_id =c.po_break_down_id  and a.color_number_id=c.color_number_id and a.gmts_sizes=c.size_number_id and c.item_number_id='$gmtsitemid' and c.color_number_id='$gmt_color_id' and a.color_number_id='$gmt_color_id' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  a.color_number_id, c.color_number_id, c.size_number_id, c.size_order, a.gmts_sizes, a.cons, a.pcs order by c.size_order");
	
	$color_size_result=sql_select("select b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.CONS AS CONS, b.PCS AS PCS, c.SIZE_ORDER, SUM(c.PLAN_CUT_QNTY) AS PLAN_CUT_QNTY from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c where a.id=b.pre_cost_fabric_cost_dtls_id and b.job_id=c.job_id  and a.job_id=b.job_id and a.job_id=c.job_id and b.job_no='$job_no' and b.po_break_down_id =c.po_break_down_id and a.item_number_id=c.item_number_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and a.item_number_id='$gmtsitemid' and b.color_number_id='$gmt_color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  b.color_number_id, b.gmts_sizes, b.cons, b.pcs, c.size_order order by c.size_order");
	
	//echo "select b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.CONS AS CONS, b.PCS AS PCS, c.SIZE_ORDER, SUM(c.PLAN_CUT_QNTY) AS PLAN_CUT_QNTY from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c where a.id=b.pre_cost_fabric_cost_dtls_id and b.job_id=c.job_id  and a.job_id=b.job_id and a.job_id=c.job_id and b.job_no='$job_no' and b.po_break_down_id =c.po_break_down_id and a.item_number_id=c.item_number_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and a.item_number_id='$gmtsitemid' and b.color_number_id='$gmt_color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  b.color_number_id, b.gmts_sizes, b.cons, b.pcs, c.size_order order by c.size_order";

    if($yarn_type==2) $production_percentage_disable="disabled"; else $production_percentage_disable="";
	?>
    <br/>
    <fieldset style="width:940px; margin-left:2px">
        <legend></legend>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_size_wise_weight">
            <thead>
                <tr>
                    <th width="100">Garments Size</th>
                    <th width="150">Sample Weight(GM)/Pcs</th>
                    <th width="150"  style="color:#0066FF;">Production Weight(GM)/Pcs</th>
                    <th width="150">Short/Excess</th>
                    <th width="150">Plan Qty (Pcs)</th>
                    <th width="100">Total Weights (KG)</th>
                    <th>Average Weight %</th>
                </tr>
            </thead>
            <tbody>
            	<?
					$i=1;$total_plan_qty=0;

                	foreach($color_size_result as $row)
                    { 
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                               	<td width="100">
									 <? 
                                     echo create_drop_down( "cbo_gmt_size_".$i, 100,$size_arr,"", 1, "", $row[csf('gmts_sizes')], "" ,1);
                                     ?>
                                </td>
                                <td width="150">
                                    <input type="text" id="txt_sample_weight_<?php echo $i; ?>" name="txt_sample_weight[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format(($row[csf('cons')]/$row[csf('pcs')])*1000,4,".",""); ?>" readonly  disabled/>
								</td>
                                <td width="150" align="right"><input type="text" id="txt_production_weight_<?php echo $i; ?>" name="txt_production_weight[]" class="text_boxes_numeric" style="width:120px"  onKeyUp="fnc_production_weight_cal(<?php echo $i; ?>)"  />
                                    <input type="hidden" id="txt_production_weight_actual_<?php echo $i; ?>" name="txt_production_weight_actual[]" class="text_boxes_numeric" style="width:120px" value="" onKeyUp="fnc_production_weight_cal(<?php echo $i; ?>)" />
                                </td>
                                <td width="150"><input type="text" id="txt_short_excess_<?php echo $i; ?>" name="txt_short_excess[]" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/>
                                </td>
                                <td width="150" align="right">
                                    <input type="text" id="txt_plan_qty_<?php echo $i; ?>" name="txt_plan_qty[]" class="text_boxes_numeric" style="width:120px"  value="<?php echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                              
                                </td>
                                <td align="right" width="100"><input type="text" id="txt_total_size_weight_<?php echo $i; ?>" name="txt_total_size_weight[]" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></td>
                                <td align="right"><input type="text" id="txt_avg_weight_<?php echo $i; ?>" name="txt_avg_weight[]" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></td>
                            </tr>
					<?
						$total_size_weight+=$row[csf('plan_cut_qnty')]*($row[csf('cons')]/$row[csf('pcs')])*1000; 
						$total_plan_qty+=$row[csf('plan_cut_qnty')]; 
						$i++;
                    } 
                    ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="100">Average Total weight per Pcs</th>
                    <th width="150">
                    	<input type="text" name="txt_total_sample_weight" id="txt_total_sample_weight" class="text_boxes_numeric" style="width:120px"  value="<?php echo number_format($total_size_weight/$total_plan_qty,4,".",""); ?>" readonly  disabled/></th>
                    <th width="150">
                    	<input type="text" name="txt_total_production_weight" id="txt_total_production_weight" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></th>
                    <th width="150">
                    	<input type="text" name="txt_total_short_excess" id="txt_total_short_excess" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></th>
                    <th width="150">
                    <input type="text" name="txt_total_plan_qty" id="txt_total_plan_qty" class="text_boxes_numeric" style="width:120px"  value="<?php echo $total_plan_qty; ?>" readonly  disabled/></th>
                    <th width="100"><input type="text" name="txt_total_weight" id="txt_total_weight" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></th>
                    <th width=""><input type="text" name="txt_total_avg_weight" id="txt_total_avg_weight" class="text_boxes_numeric" style="width:120px"  value="" readonly  disabled/></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
    <br/>
    <fieldset style="width:940px; margin-left:2px">
   		<legend>For Regular Yarn</legend>
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_sample_color">
            <thead>
            	<tr>
                    <th colspan="4">Consumption Per Dozen <input type="text" name="txt_con_per_dzn_kg" id="txt_con_per_dzn_kg" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Kg&nbsp;<input type="text" name="txt_con_per_dzn_lbs" id="txt_con_per_dzn_lbs" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Lbs</th>
                    <th colspan="5">Consumption Per PCS <input type="text" name="txt_con_per_pcs_kg" id="txt_con_per_pcs_kg" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Kg&nbsp;<input type="text" name="txt_con_per_pcs_lbs" id="txt_con_per_pcs_lbs" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Lbs</th>
                </tr>
                <tr>
                    <th width="60">Sample Color</th>
                    <th width="250">Yarn Color</th>
                    <th width="60">Sample Color %</th>
                    <th width="100" style="color:#0066FF;">Production Color %</th>
                    <th width="80">Cons (Kg)</th>
                    <th width="80">Cons (Lbs)</th>
                    <th width="80">Process Loss %</th>
                    <th width="80">Actual Cons (Kg)</th>
                    <th width="">Actual Cons (Lbs)</th>
                </tr>
            </thead>
            <tbody>
            	<?
					$i=1;
					$data_array_strip=sql_select("select a.sample_color, a.sample_per, a.stripe_color, a.measurement, a.sample_per from wo_pre_stripe_color a where a.job_no='".$job_no."' and a.item_number_id='$gmtsitemid' and a.color_number_id=$gmt_color_id and a.status_active=1 and a.is_deleted=0 order by a.id");
                	foreach($data_array_strip as $row)
                    { 
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                               	<td width="60">
									 <? 
                                     echo create_drop_down( "cbo_sample_color_".$i, 60,$color_arr,"", 1, "", $row[csf('sample_color')], "" ,1);
                                     ?>
                                </td>
                                <td width="250">
                                    <? 
                                     echo create_drop_down( "cbo_stripe_color_".$i, 250,$color_arr,"", 1, "", $row[csf('stripe_color')], "" ,1);
                                     ?>
								</td>
                                <td width="60" align="right"><input type="text" id="txt_sample_color_per_<?php echo $i; ?>" name="txt_sample_color_per[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_per')];?>" readonly  disabled />
                                </td>
                                <td width="100"><input type="text" id="txt_production_color_per_<?php echo $i; ?>" name="txt_production_color_per[]" class="text_boxes_numeric" style="width:80px"  value="" onKeyUp="fnc_production_color_calculation(<?php echo $i; ?>)" <?php echo $production_percentage_disable; ?> />
                                </td>
                                <td width="80" align="right"><input type="text" id="txt_yarn_cons_<?php echo $i; ?>" name="txt_yarn_cons[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_<?php echo $i; ?>" name="txt_yarn_cons_lbs[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                </td>
                               <td align="right" width="80">
                               		<input type="text" id="txt_process_loss_<?php echo $i; ?>" name="txt_process_loss[]" class="text_boxes_numeric" style="width:70px"  value="0" onKeyUp="fnc_processloss_calculation(<?php echo $i; ?>,1)" <?php echo $production_percentage_disable; ?>/>
                               </td>
                               
                                <td align="right" width="80"><input type="text" id="txt_yarn_actual_cons_<?php echo $i; ?>" name="txt_yarn_actual_cons[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
                                
                                <td align="right" width=""><input type="text" id="txt_yarn_actual_cons_lbs_<?php echo $i; ?>" name="txt_yarn_actual_cons_lbs[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
                             
                            </tr>
					<?
						$total_sample_color_percentage+=$row[csf('sample_per')];
						$i++;
                    } 
                    ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="60">Total</th>
                    <th width="250"></th>
                    <th width="60"><?php echo number_format($total_sample_color_percentage,2); ?></th>
                    <th width="100" id="total_production_color_per"><?php echo number_format($total_production_color_percentage,2,".",""); ?></th>
                    <th width="80" id="total_cons_per_kg"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                    <th width="80" id="total_cons_per_lbs"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                    <th width="80"></th>
                    <th width="" id="total_actual_cons_per_kg"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                    <th width="" id="total_actual_cons_per_lbs"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <?php if($yarn_type==2)
    {
    ?>
    <br/>
    <fieldset style="width:940px; margin-left:2px">
        <legend>For Twisted Yarn</legend>
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_sample_color_twisted">
            <thead>
                <tr>
                    <th colspan="4">Consumption Per Dozen <input type="text" name="txt_con_per_dzn_kg_twisted" id="txt_con_per_dzn_kg_twisted" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Kg &nbsp;<input type="text" name="txt_con_per_dzn_lbs_twisted" id="txt_con_per_dzn_lbs_twisted" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Lbs</th>
                    <th colspan="5">Consumption Per PCS <input type="text" name="txt_con_per_pcs_kg_twisted" id="txt_con_per_pcs_kg_twisted" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Kg &nbsp;<input type="text" name="txt_con_per_pcs_lbs_twisted" id="txt_con_per_pcs_lbs_twisted" class="text_boxes_numeric" style="width:80px"  value="" readonly  disabled/>&nbsp; Lbs</th>
                </tr>
                <tr>
                    <th width="60">Browse Sample Color</th>
                    <th width="60">Sample Color</th>
                    <th width="250">Yarn Color</th>
                    <th width="60">Sample Color %</th>
                    <th width="100" style="color:#0066FF;">Production Color %</th>
                    <th width="80">Cons (Kg)</th>
                    <th width="80">Cons (Lbs)</th>
                    <th width="80">Process Loss %</th>
                    <th width="80">Actual Cons (Kg)</th>
                    <th width="">Actual Cons (Lbs)</th>
                </tr>
            </thead>
            <tbody>
                <?
                    $i=1;
                    $data_array_strip=sql_select("select a.sample_color, a.sample_per, a.stripe_color, a.measurement, a.sample_per from wo_pre_stripe_color a where a.job_no='".$job_no."' and a.item_number_id='$gmtsitemid' and a.color_number_id=$gmt_color_id and a.status_active=1 and a.is_deleted=0 order by a.id");
                    foreach($data_array_strip as $row)
                    { 
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="searchtwisting<? echo $row[csf('sample_color')];?>">
                                <td width="60">
                                    
                                     <input type="text" id="txt_sample_color_browse_<?php echo $row[csf('sample_color')]; ?>" name="txt_sample_color_browse[]" class="text_boxes" style="width:50px" placeholder="Browse" onDblClick="openmypage_yarn_color_twist('searchtwisting<? echo $row[csf('sample_color')];?>')"/>
                                </td> 
                                <td width="60">
                                    
                                     <input type="text" id="txt_sample_color_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_sample_color_twisted[]" class="text_boxes" style="width:50px" value="<?php echo $color_arr[$row[csf('sample_color')]];?>" readonly   />
                                </td>
                                <td width="250">
                                    <? 
                                     echo create_drop_down( "cbo_stripe_color_twisted_".$row[csf('sample_color')], 250,$color_arr,"", 1, "", $row[csf('stripe_color')], "" ,1,"","","","","","","cbo_stripe_color_twisted[]");
                                     ?>
                                </td>
                                <td width="60" align="right"><input type="text" id="txt_sample_color_per_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_sample_color_per_twisted[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_per')];?>" readonly  disabled />
                                </td>
                                <td width="100"><input type="text" id="txt_production_color_per_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_production_color_per_twisted[]" class="text_boxes_numeric" style="width:80px"  value="" onKeyUp="fnc_production_color_calculation_twisted('<?php echo $row[csf('sample_color')]; ?>')"  />
                                </td>
                                <td width="80" align="right"><input type="text" id="txt_yarn_cons_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_yarn_cons_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_yarn_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
                                </td>
                               <td align="right" width="80">
                                    <input type="text" id="txt_process_loss_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_process_loss_twisted[]" class="text_boxes_numeric" style="width:70px"  value="0" onKeyUp="fnc_processloss_calculation_twisted('<?php echo $row[csf('sample_color')]; ?>',1)" />
                               </td>
                               
                                <td align="right" width="80"><input type="text" id="txt_yarn_actual_cons_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_yarn_actual_cons_twisted[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
                                
                                <td align="right" width=""><input type="text" id="txt_yarn_actual_cons_lbs_twisted_<?php echo $row[csf('sample_color')]; ?>" name="txt_yarn_actual_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/>
                                <input type="hidden" id="hidden_strip_color_ids" name="hidden_strip_color_ids[]" value="<?php echo $row[csf('stripe_color')]; ?>">
                                <input type="hidden" id="hidden_ydw_ids" name="hidden_ydw_ids[]" value="<?php //echo $ydw_id; ?>">
                                </td>
                            </tr>
                    <?
                        $total_sample_color_percentage_twisting+=$row[csf('sample_per')];
                        $i++;
                    } 
                    ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="250">Total</th>
                    <th width="60"><?php echo number_format($total_sample_color_percentage_twisting,2); ?></th>
                    <th width="100" id="total_production_color_per_twisted"><?php echo number_format($total_production_color_percentage,2,".",""); ?></th>
                    <th width="80" id="total_cons_per_kg_twisted"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                    <th width="80" id="total_cons_per_lbs_twisted"><?php echo number_format($total_cons_per_kg,4,".",""); ?></th>
                    <th width="80"></th>
                    <th width="" id="total_actual_cons_per_kg_twisted"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                    <th width="" id="total_actual_cons_per_lbs_twisted"><?php echo number_format($total_actual_cons_per_kg,4,".",""); ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    
    <?php 
	}
	exit();
}

if ($action=="load_data_color_size_twisting")
{   
	list($job_no,$gmt_item_id,$gmt_color_id,$sample_color_ids,$strip_color_ids,$twisted_color_id,$ydw_id)=explode('_',$data);
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
  
    $i=1;
    $data_array_strip=sql_select("select sum(a.sample_per) as sample_per from wo_pre_stripe_color a where a.job_no='".$job_no."' and a.item_number_id=$gmt_item_id and a.color_number_id=$gmt_color_id and a.sample_color in ($sample_color_ids) and a.status_active=1 and a.is_deleted=0");
    foreach($data_array_strip as $row)
    { 
          $sample_color_ids=implode("_",explode(",", $sample_color_ids)) ;  
        ?>
           
        <td width="60">
             <input type="text" id="txt_sample_color_browse_<?php echo $sample_color_ids; ?>" name="txt_sample_color_browse[]" class="text_boxes" style="width:50px" disabled/>
        </td> 
        <td width="60">
            
             <input type="text" id="txt_sample_color_twisted_<?php echo $sample_color_ids; ?>" name="txt_sample_color_twisted[]" class="text_boxes" style="width:50px" value="<?php
                foreach(explode("_", $sample_color_ids) as $sample_color_id)
                {
                    echo $color_arr[$sample_color_id]."  ";
                } 
                ?>" readonly   />
        </td>
        <td width="250">
            <? 
            // echo create_drop_down( "cbo_stripe_color_twisted_".$sample_color_ids, 250,$color_arr,"", 1, "", $twisted_color_id, "" ,1,"","","","","","cbo_stripe_color_twisted[]");
             echo create_drop_down( "cbo_stripe_color_twisted_".$sample_color_ids, 250,$color_arr,"", 1, "", $twisted_color_id, "" ,1,"","","","","","","cbo_stripe_color_twisted[]");
             ?>
        </td>
        <td width="60" align="right"><input type="text" id="txt_sample_color_per_twisted_<?php echo $sample_color_ids; ?>" name="txt_sample_color_per_twisted[]" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf('sample_per')];?>" readonly  disabled />
        </td>
        <td width="100"><input type="text" id="txt_production_color_per_twisted_<?php echo $sample_color_ids; ?>" name="txt_production_color_per_twisted[]" class="text_boxes_numeric" style="width:80px"  value="" onKeyUp="fnc_production_color_calculation_twisted('<?php echo $sample_color_ids; ?>')"  />
        </td>
        <td width="80" align="right"><input type="text" id="txt_yarn_cons_twisted_<?php echo $sample_color_ids; ?>" name="txt_yarn_cons_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
        <td width="80" align="right"><input type="text" id="txt_yarn_cons_lbs_twisted_<?php echo $sample_color_ids; ?>" name="txt_yarn_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"  value="<?php //echo $row[csf('plan_cut_qnty')]; ?>" readonly  disabled/>
        </td>
       <td align="right" width="80">
            <input type="text" id="txt_process_loss_twisted_<?php echo $sample_color_ids; ?>" name="txt_process_loss_twisted[]" class="text_boxes_numeric" style="width:70px"  value="0" onKeyUp="fnc_processloss_calculation_twisted('<?php echo $sample_color_ids; ?>',1)" />
       </td>
       
        <td align="right" width="80"><input type="text" id="txt_yarn_actual_cons_twisted_<?php echo $sample_color_ids; ?>" name="txt_yarn_actual_cons_twisted[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/></td>
        
        <td align="right" width="">
            <input type="text" id="txt_yarn_actual_cons_lbs_twisted_<?php echo $sample_color_ids; ?>" name="txt_yarn_actual_cons_lbs_twisted[]" class="text_boxes_numeric" style="width:70px"    readonly  disabled/>
            <input type="hidden" id="hidden_strip_color_ids" name="hidden_strip_color_ids[]" value="<?php echo $strip_color_ids; ?>">
            <input type="hidden" id="hidden_ydw_ids" name="hidden_ydw_ids[]" value="<?php echo $ydw_id; ?>">
        </td>
    <?
    } 
    exit();
}


if($action=="job_search_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	
?>
	<script>
		function js_set_order(strCon ) 
		{
			//alert(strCon);die;
			document.getElementById('hidden_job_no').value=strCon;
			
			parent.emailwindow.hide();
			//return;
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1020" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="150">Company name</th>
                    <th width="150">Buyer name</th>
                    <th width="60">Job No</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Order No</th>
                     <th width="100">File No</th>
                    <th width="100">Internal Ref. No</th>
                    <th width="220">Date Range</th>
                    <th width=""><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td>
                          <? 
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", "", "load_drop_down( 'size_sheet_weight_calclution_controller', this.value, 'load_drop_down_buyer', 'buyer_popUp_td' );",0);
                         ?>
                           <input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
                            <input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
                            <input type="hidden" id="hidden_prifix" name="hidden_prifix" />
                            <input type="hidden" id="hidden_job_no" name="hidden_job_no" />
                    </td>
                    <td align="center" width="150" id="buyer_popUp_td" >
                             <?  
							  // $sql="select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$cbo_company_id order by a.buyer_name";
							echo create_drop_down( "cbo_buyer_name", 140,$blank_arr,"", 1, "-- Select --", 0, "", 0,"5,6,7","","","" );
                            ?>
                          
                    </td>
                    <td width="60">
                          <input style="width:50px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  />
                    </td>
                    <td width="100">
                          <input style="width:90px;" type="text"  class="text_boxes"   name="txt_style_no" id="txt_style_no"  />
                    </td>
                    <td width="100">
                          <input style="width:90px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_file_no" id="txt_file_no"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_internal_ref" id="txt_internal_ref"  />
                    </td> 
                    <td align="center" width="220">
                           <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                           <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                         <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style_no').value, 'create_job_search_list_view', 'search_div', 'size_sheet_weight_calclution_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                    </td>
            </tr>
        		<tr>                  
            	<td align="center" valign="middle" colspan="8">
					<? echo load_month_buttons(1);  ?>
                </td>
            </tr>   
            </tbody>
         </tr>         
        </table> 
          <div align="center" valign="top" id="search_div"> </div>  
        </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$file_no = $ex_data[7];
	$internal_reff = $ex_data[8];
	$style_reff = $ex_data[9];
	$job_cond="";
	
	if($company==0) {echo "<h2>Please Select Company First.</h2>";die;}
	
	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	
	if(str_replace("'","",$file_no)!="")  $file_cond="and a.file_no like '%".str_replace("'","",$file_no)."%' "; else $file_cond="";
	
	if(str_replace("'","",$style_reff)!="")  $style_cond="and b.style_ref_no like '%".str_replace("'","",$style_reff)."%' "; else $style_cond="";
	if(str_replace("'","",$internal_reff)!="")  $internal_reff_cond=" and a.grouping like '%".str_replace("'","",$internal_reff)."%' "; else $internal_reff_cond="";
	
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where  b.garments_nature=100 and a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.buyer_name,b.job_no,a.po_number ";  
	}
	
	if($db_type==2)
	{
		if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
		{
		  	$sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
	
	 
		$sql_order="SELECT b.job_no,b.buyer_name,b.dealing_marchant,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping,to_char(c.msmnt_break_down) as msmnt_break_down from wo_po_details_master b,wo_po_break_down a ,wo_pre_cost_fabric_cost_dtls c where a.job_no_mst=b.job_no and b.job_no=c.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.job_no,b.buyer_name, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, a.file_no,a.grouping,b.insert_date,b.dealing_marchant,to_char(c.msmnt_break_down) order by  year desc,job_no_prefix_num asc";  
	}
	//echo $sql_order;
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name,File No,Internal Ref. No, Order No,Shipment Date","60,60,150,150,100,100,150,100","1000","270",0, $sql_order , "js_set_order", "job_no,buyer_name,style_ref_no,dealing_marchant,msmnt_break_down", "", 1, "0,0,0,buyer_name,0,0,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,file_no,grouping,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;
	exit();	
}

if ($action=="get_sample_reference")
{
	 $sql="select id, requisition_number from sample_development_mst where  id=".$data."  ";
	// echo $sql;die;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txt_sample_ref').value  = '".$row[csf('requisition_number')]."';\n";
	}
	exit();
}

// need  for this page
if($action=="load_php_mst_form")
{
    $sql_data=sql_select("select a.id, a.sizeset_num_prefix_no, a.sizeset_no, a.yarn_type, a.extention_no, a.size_set_copy_from, a.job_no, a.sizeset_date, a.sizeset_no, a.job_no, a.buyer_id, a.sample_ref_id, a.style_ref, a.sample_ref, a.sample_size, a.yarn_controller, a.machanical_manager, a.deling_marchant, a.company_id FROM ppl_size_set_mst a where a.id=".$data." and  a.status_active=1 and a.is_deleted=0  ");

	foreach($sql_data as $val)
	{
		echo "document.getElementById('txt_system_no').value 		= '".($val[csf("sizeset_no")])."';\n";
		echo "document.getElementById('update_id').value 			= '".($val[csf("id")])."';\n";
		echo "document.getElementById('sample_size_id').value 		= '".($val[csf("sample_ref_id")])."';\n";

        echo "document.getElementById('txt_extantion_no').value            = '".($val[csf("extention_no")])."';\n";
        echo "document.getElementById('hidden_size_set_copy_form').value   = '".($val[csf("size_set_copy_from")])."';\n";

		echo "document.getElementById('txt_job_no').value 			= '".($val[csf("job_no")])."';\n";
		echo "load_drop_down( 'requires/size_sheet_weight_calclution_controller', ".$val[csf("company_id")].", 'load_drop_down_buyer', 'buyer_id' );"; 
		echo "load_drop_down( 'requires/size_sheet_weight_calclution_controller', '".$val[csf("job_no")]."', 'load_drop_down_order_garment', 'garmentitem_td' );"; 
		
		echo "document.getElementById('cbo_buyer_name').value 		= '".$val[csf("buyer_id")]."';\n"; 
        echo "document.getElementById('cbo_yarn_type').value       = '".$val[csf("yarn_type")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled',true);";
        echo "$('#cbo_yarn_type').attr('disabled',true);";
		echo "document.getElementById('txt_sample_ref').value 		= '".$val[csf("sample_ref")]."';\n"; 
		echo "document.getElementById('txt_style_no').value 		= '".$val[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_sample_size').value 		= '".$val[csf("sample_size")]."';\n";
		 
		echo "document.getElementById('txt_technical_manager').value= '".$val[csf("machanical_manager")]."';\n"; 
		echo "document.getElementById('txt_yarn_controller').value 	= '".$val[csf("yarn_controller")]."';\n"; 
		echo "document.getElementById('cbo_deling_marchan').value 	= '".$val[csf("deling_marchant")]."';\n"; 
		echo "document.getElementById('company_id').value 			= '".$val[csf("company_id")]."';\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_size_set_calculation_info',1);\n"; 
		echo "document.getElementById('txt_size_set_date').value 	= '".change_date_format($val[csf("sizeset_date")])."';\n"; 
	}
	exit();
}

if($action=="load_extantion_no")
{
    $data=explode("_",$data);

    $sql_data=sql_select("select a.sizeset_no FROM ppl_size_set_mst a,ppl_size_set_dtls b where a.id=b.mst_id and a.job_no='".$data[0]."' and b.color_id=".$data[1]." and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.sizeset_no ");
    $extantion_no=0;
    foreach($sql_data as $val)
    {
       $extantion_no+=1;
    }

    echo "document.getElementById('txt_extantion_no').value      = '".$extantion_no."';\n";
    exit();
}

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(strCon ) 
		{
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >


<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="130">System No</th>
                    <th width="130">Job No</th>
                    <th width="100">Style Ref.</th>
                    <th width="130" style="display:none">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr class="general">                    
                        <td>
                              <? 
                                   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --",$company_id, "",0);
                             ?>
                        </td>
                      
                        <td align="center" >
                                <input type="text" id="txt_system_no" name="txt_system_no" style="width:120px"  class="text_boxes_numeric"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
                        </td>
                          <td align="center">
                               <input name="txt_job_style" id="txt_job_style" class="text_boxes" style="width:100px"  />
                        </td>
                        <td align="center" style="display:none">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job_style').value, 'create_system_search_list_view', 'search_div', 'size_sheet_weight_calclution_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                    <td align="center" valign="middle" colspan="7"><?=load_month_buttons(1); ?></td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$system_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$system_year= $ex_data[5];
	$order_no= $ex_data[6];
	$style_no= $ex_data[7];
	//echo $style_no.'DD';
	if($company==0) {echo "<h2>Please Select Company First.</h2>";die;}
    if($db_type==2) { 
		$year_cond=" and extract(year from a.insert_date)=$system_year"; 
		$year=" extract(year from a.insert_date)";
	}
	
    if($db_type==0) {
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$system_year";
		$year=" SUBSTRING_INDEX(a.insert_date, '-', 1)";
	}
	if(str_replace("'","",$company)==0) $conpany_cond=""; 
	else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$system_no)=="") 	$system_cond=""; 
	else $system_cond="and a.sizeset_num_prefix_no='".str_replace("'","",$system_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond="";
	else $job_cond="and a.job_no like '%".str_replace("'","",$job_no)."%'";
	if(str_replace("'","",$style_no)=="") $style_cond="";
	else $style_cond="and a.style_ref='".str_replace("'","",$style_no)."'";

	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.sizeset_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.sizeset_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
    //sizeset_no job_no buyer_id sample_ref_id style_ref sample_ref sample_size
	$buyer_arr=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
    $color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

    $sql_order="select a.id, a.sizeset_num_prefix_no, a.job_no, a.sizeset_date, a.sizeset_no, a.buyer_id, a.sample_ref_id, a.style_ref, a.sample_ref, a.sample_size, $year as year, b.item_number_id, b.color_id 
    FROM ppl_size_set_mst a, ppl_size_set_dtls b 
    where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conpany_cond $style_cond $system_cond $job_cond $sql_cond 
    group by a.id, a.sizeset_num_prefix_no, a.job_no, a.sizeset_date, a.sizeset_no, a.buyer_id, a.sample_ref_id, a.style_ref, a.sample_ref, a.sample_size, $year, b.item_number_id, b.color_id
    order by id DESC";
    $sql_order_res=sql_select($sql_order);
	//echo $sql_order;die;	
	//$arr=array(3=>$buyer_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	//echo create_list_view("list_view", "System No,Year,Job No,Buyer,Style Reff,Sample Reff,Entry Date,Sample Size","80,70,90,100,150,150,80,120","900","270",0, $sql_order , "js_set_value", "id", "", 1, "0,0,0,buyer_id,0,0,0,0,0", $arr, "sizeset_num_prefix_no,year,job_no,buyer_id,style_ref,sample_ref,sizeset_date,sample_size", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,3,0");
    ?>
    <div style="width:990px;" align="left">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Sys. No</th>
            <th width="50">Year</th>
            <th width="100">Job No</th>
            <th width="100">Buyer</th>
            <th width="150">Style Ref.</th>
            <th width="100">Sample Ref.</th>
            <th width="120">Garments Item</th>
            <th width="120">Garments Color</th>
            <th width="70">Entry Date</th>
            <th>Sample Size</th>   
        </thead>
    </table>
    <div style="width:990px; max-height:300px; overflow-y:scroll" id="scroll_body" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="list_view">
            <?
            $i = 1;
            foreach ($sql_order_res as $row)
            {
                if ($i%2 == 0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                    <td width="30"><? echo $i; ?></td>
                    <td width="50" style="word-break:break-all"><? echo $row[csf('sizeset_num_prefix_no')]; ?></td>
                    <td width="50" style="word-break:break-all"><? echo $row[csf('year')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $row[csf('style_ref')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('sample_ref')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('sizeset_date')]); ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('sample_size')]; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    </div>      
    <?
	exit();
}

if ($action == "show_data_listview")
{
	$sql = "select a.job_no, a.style_ref, b.item_number_id, b.color_id, b.plan_cut_qty, b.production_weight, b.sample_weight, b.avg_weight from ppl_size_set_mst a, ppl_size_set_dtls b where a.id=$data and a.id=b.mst_id  and b.gmt_size_id=0 and b.short_excess is null  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	//echo $sql;
	$result = sql_select($sql);
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" >
		<thead>
			<th width="30">SL</th>
			<th width="100">Job NO</th>
			<th width="200">Style NO</th>
            <th width="150">Gmt. Item</th>
            <th width="200">Gmt. Color</th>
			<th>Sample Weight (GM)</th>
		
		</thead>
	</table>
	<div style="width:830px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="list_view">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="put_data_dtls_part('<? echo $row[csf('item_number_id')].'_'.$row[csf('color_id')]; ?>');">
                    <td width="30"><? echo $i; ?></td>
					<td width="100" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>
					<td width="200" style="word-break:break-all"><? echo $row[csf('style_ref')]; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
					<td width="200" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
					<td style="word-break:break-all"><? echo $row[csf('sample_weight')]; ?>&nbsp;</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//echo "10**";
		if(str_replace("'","",$update_id)=="")
		{
			if(str_replace("'","",$txt_extantion_no)!="") $extanCond="and a.extention_no=$txt_extantion_no"; else $extanCond="";
			
			if (is_duplicate_field( "a.sizeset_no", "ppl_size_set_mst a, ppl_size_set_dtls b", "a.id=b.mst_id and job_no=$txt_job_no and item_number_id=$cbogmtsitem and color_id=$gmt_color_id $extanCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ) == 1)
			{
				echo "11**0";
                disconnect($con);
				die;
			}
			
			$id=return_next_id("id", "ppl_size_set_mst", 1);
		
			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$company_id), '', 'SSWC', date("Y",time()), 5, "select max(sizeset_num_prefix) as sizeset_num_prefix,max(sizeset_num_prefix_no) as sizeset_num_prefix_no from ppl_size_set_mst where company_id=$company_id $mrr_cond  ", "sizeset_num_prefix", "sizeset_num_prefix_no" ));

            //echo "10**select max(sizeset_num_prefix) as sizeset_num_prefix,max(sizeset_num_prefix_no) as sizeset_num_prefix_no from ppl_size_set_mst where company_id=$company_id $mrr_cond  ";die;
			$field_array="id, sizeset_num_prefix, sizeset_num_prefix_no, sizeset_no, job_no, company_id, sizeset_date, buyer_id, sample_ref_id, style_ref, sample_ref, sample_size,  deling_marchant, machanical_manager, yarn_controller, yarn_type, size_set_copy_from, extention_no, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_sys_number[1]."',".$new_sys_number[2].",'".$new_sys_number[0]."',".$txt_job_no.",".$company_id.",".$txt_size_set_date.",".$cbo_buyer_name.",".$sample_size_id.",".$txt_style_no.",".$txt_sample_ref.",".$txt_sample_size.",".$cbo_deling_marchan.",".$txt_technical_manager.",".$txt_yarn_controller.",".$cbo_yarn_type.",".$hidden_size_set_copy_form.",".$txt_extantion_no.",'".$user_id."','".$pc_date_time."')";
			$txt_system_no=$new_sys_number[0];
		}
		else
		{
			if (is_duplicate_field( "a.sizeset_no", "ppl_size_set_mst a, ppl_size_set_dtls b", "a.id=b.mst_id and job_no=$txt_job_no and item_number_id=$cbogmtsitem and color_id=$gmt_color_id and a.id!=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ) == 1)
			{
				echo "11**0";
                disconnect($con);
				die;
			}
			
			$field_array="sizeset_date*buyer_id*sample_ref_id*style_ref*sample_ref*sample_size*deling_marchant*machanical_manager*yarn_controller*updated_by*update_date";
	
			$data_array="".$txt_size_set_date."*".$cbo_buyer_name."*".$sample_size_id."*".$txt_style_no."*".$txt_sample_ref."*".$txt_sample_size."*".$cbo_deling_marchan."*".$txt_technical_manager."*".$txt_yarn_controller."*'".$user_id."'*'".$pc_date_time."'";
		 }
		
		$detls_id= return_next_id("id", "ppl_size_set_dtls", 1);
		$field_array1=" id, mst_id, item_number_id, color_id, gmt_size_id, sample_weight, production_weight, production_weight_actual, short_excess, plan_cut_qty, total_weight,  avg_weight, inserted_by, insert_date";

		$add_comma=0;
		$field_array2="id, mst_id, item_number_id, color_id, sample_color_id, yarn_color_id, sample_color_percentage, production_color_percentage, consumption, process_loss,  actual_consumption, cons_per_dzn, inserted_by, insert_date";
		
		if(str_replace("'","",$update_id)!="") $master_id=$update_id; else $master_id=$id;  
			
		$actual_total_production_weight=0;	
		for($i=1; $i<=$size_row_num; $i++)
		{
			$cbo_gmt_size			      ="cbo_gmt_size_".$i;
			$txt_sample_weight		      ="txt_sample_weight_".$i;
			$txt_production_weight	      ="txt_production_weight_".$i;
            $txt_production_weight_actual ="txt_production_weight_actual_".$i;
			$txt_short_excess		      ="txt_short_excess_".$i;
			$txt_plan_qty			      ="txt_plan_qty_".$i;
			$txt_total_size_weight	      ="txt_total_size_weight_".$i;
			$txt_avg_weight			      ="txt_avg_weight_".$i;

            if(str_replace("'","",$txt_extantion_no)=="") $txt_production_weight_actual=$txt_production_weight;
			
		   	if ($add_comma!=0) $data_array1 .=",";
			$data_array1.="(".$detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",".$$cbo_gmt_size.",".$$txt_sample_weight.",".$$txt_production_weight.",".$$txt_production_weight_actual.",".$$txt_short_excess.",".$$txt_plan_qty.",".$$txt_total_size_weight.",".$$txt_avg_weight.",'".$user_id."','".$pc_date_time."')";   
			$actual_total_production_weight+=$$txt_production_weight_actual;
			$detls_id++;
			$add_comma++;
		}
		
		if ($add_comma!=0) $data_array1 .=",";
		$data_array1.="(".$detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",0,".$txt_total_sample_weight.",".$txt_total_production_weight.",".$actual_total_production_weight.",'',".$txt_total_plan_qty.",".$txt_total_weight.",".$txt_total_avg_weight.",'".$user_id."','".$pc_date_time."')";
		
		$con_detls_id= return_next_id("id", "ppl_size_set_consumption", 1);	
		$add_comma1=0;
		for($i=1; $i<=$yarn_color_row_num; $i++)
		{
			$cbo_sample_color			="cbo_sample_color_".$i;
			$cbo_stripe_color			="cbo_stripe_color_".$i;
			$txt_sample_color_per		="txt_sample_color_per_".$i;
			$txt_production_color_per	="txt_production_color_per_".$i;
			$txt_yarn_cons				="txt_yarn_cons_".$i;
			$txt_process_loss			="txt_process_loss_".$i;
			$txt_yarn_actual_cons		="txt_yarn_actual_cons_".$i;
			
		   if ($add_comma1!=0) $data_array2 .=",";
			$data_array2.="(".$con_detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",".$$cbo_sample_color.",".$$cbo_stripe_color.",".$$txt_sample_color_per.",".$$txt_production_color_per.",".$$txt_yarn_cons.",".$$txt_process_loss.",".$$txt_yarn_actual_cons.",".$txt_con_per_dzn_kg.",'".$user_id."','".$pc_date_time."')";   
			
			$con_detls_id++;
			$add_comma1++;
		}
		
        if(str_replace("'", "", $cbo_yarn_type)==2)
        {
            $add_comma2=0;
            $field_array3="id, mst_id, item_number_id, color_id, yarn_type, sample_color_ids, yarn_color_id, stripe_color_ids, sample_color_percentage,  production_color_percentage, consumption, process_loss, actual_consumption, cons_per_dzn, inserted_by, insert_date";
            for($j=1; $j<=$twisted_color_row_num; $j++)
            {
                $sampleColorIds                 ="sampleColorIds_".$j;
                $twistedColor                   ="twistedColor_".$j;
                $sampleColorPerTwisted          ="sampleColorPerTwisted_".$j;
                $productionColorPerTtwisted     ="productionColorPerTtwisted_".$j;
                $yarnConsTwisted                ="yarnConsTwisted_".$j;
                $yarnActualConsTwisted          ="yarnActualConsTwisted_".$j;
                $processLossTwisted             ="processLossTwisted_".$j;
                $stripColorIds                  ="stripColorIds_".$j;
                $hiddenYdwId                    ="hiddenYdwId_".$j;
                
                if ($add_comma2!=0) $data_array3 .=",";
                $data_array3.="(".$con_detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",2,'".implode(",",explode("_",$$sampleColorIds))."',".$$twistedColor.",'".$$stripColorIds."',".$$sampleColorPerTwisted.",".$$productionColorPerTtwisted.",".$$yarnConsTwisted.",".$$processLossTwisted.",".$$yarnActualConsTwisted.",".$txt_con_per_dzn_kg.",'".$user_id."','".$pc_date_time."')";   
   
                $con_detls_id++;
                $add_comma2++;
            }
        }
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID1=sql_insert(" ppl_size_set_mst",$field_array,$data_array,0);  
		}
		else
		{
			$rID1=sql_update("ppl_size_set_mst",$field_array,$data_array,"id",$update_id,0,0); 
		}
	
		if(count($data_array1)>0)  
		{
		   $rID2=sql_insert(" ppl_size_set_dtls",$field_array1,$data_array1,1); 
		}
		
		if($data_array2!="")
		{
			$rID3=sql_insert("ppl_size_set_consumption",$field_array2,$data_array2,0);
		}

        if($data_array3!="")
        {
            $rID3=sql_insert("ppl_size_set_consumption",$field_array3,$data_array3,0);
        }
		//echo "10**insert into ppl_size_set_mst($field_array)values".$data_array;die;
		//echo "10**".bulk_update_sql_statement("ppl_size_set_mst", "id",$field_array,$data_array,$update_id);
		//echo "10**".$rID1."**".$rID2."**".$rID3;die;
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$master_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3)
			{
				oci_commit($con);   
				echo "0**".str_replace("'","",$master_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();	
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		if(str_replace("'","",$txt_extantion_no)!="") $extanCond="and a.extention_no=$txt_extantion_no"; else $extanCond="";
		if (is_duplicate_field( "a.sizeset_no", "ppl_size_set_mst a, ppl_size_set_dtls b", "a.id=b.mst_id and job_no=$txt_job_no and item_number_id=$cbogmtsitem and color_id=$gmt_color_id and a.id!=$update_id $extanCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ) == 1)
		{
			echo "11**0";
            disconnect($con);
			die;
		}
		//hidden_gmt_color_id
		//master table update*********************************************************************
		$field_array="sizeset_date*buyer_id*sample_ref_id*style_ref*sample_ref*sample_size*deling_marchant* machanical_manager*yarn_controller*extention_no*updated_by*update_date";
		$data_array="".$txt_size_set_date."*".$cbo_buyer_name."*".$sample_size_id."*".$txt_style_no."*".$txt_sample_ref."*".$txt_sample_size."*".$cbo_deling_marchan."*".$txt_technical_manager."*".$txt_yarn_controller."*".$txt_extantion_no."*'".$user_id."'*'".$pc_date_time."'";
		
	   	$detls_id= return_next_id("id", "ppl_size_set_dtls", 1);
		$field_array1=" id, mst_id, item_number_id, color_id, gmt_size_id, sample_weight, production_weight,production_weight_actual, short_excess, plan_cut_qty, total_weight,  avg_weight, inserted_by, insert_date";
		
		
		$add_comma=0;
		$field_array2="id, mst_id, item_number_id, color_id, sample_color_id, yarn_color_id, sample_color_percentage, production_color_percentage, consumption, process_loss,  actual_consumption, cons_per_dzn, inserted_by, insert_date";
		$master_id=$update_id;
		$actual_total_production_weight=0;	
		for($i=1; $i<=$size_row_num; $i++)
		{
			$cbo_gmt_size			       ="cbo_gmt_size_".$i;
			$txt_sample_weight		       ="txt_sample_weight_".$i;
			$txt_production_weight	       ="txt_production_weight_".$i;
			$txt_short_excess		       ="txt_short_excess_".$i;
			$txt_plan_qty			       ="txt_plan_qty_".$i;
			$txt_total_size_weight	       ="txt_total_size_weight_".$i;
			$txt_avg_weight			       ="txt_avg_weight_".$i;
            $txt_production_weight_actual  ="txt_production_weight_actual_".$i;
			
            if(str_replace("'","",$txt_extantion_no)=="") $txt_production_weight_actual=$txt_production_weight;
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1.="(".$detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",".$$cbo_gmt_size.",".$$txt_sample_weight.",".$$txt_production_weight.",".$$txt_production_weight_actual.",".$$txt_short_excess.",".$$txt_plan_qty.",".$$txt_total_size_weight.",".$$txt_avg_weight.",'".$user_id."','".$pc_date_time."')";   
			$actual_total_production_weight+=$$txt_production_weight_actual;
			$detls_id++;
			$add_comma++;
		}
		
		if ($add_comma!=0) $data_array1 .=",";
		$data_array1.="(".$detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",0,".$txt_total_sample_weight.",".$txt_total_production_weight.",".$actual_total_production_weight.",'',".$txt_total_plan_qty.",".$txt_total_weight.",".$txt_total_avg_weight.",'".$user_id."','".$pc_date_time."')";
		
		$con_detls_id= return_next_id("id", "ppl_size_set_consumption", 1);	
		$add_comma1=0;
		for($i=1; $i<=$yarn_color_row_num; $i++)
		{
			$cbo_sample_color			="cbo_sample_color_".$i;
			$cbo_stripe_color			="cbo_stripe_color_".$i;
			$txt_sample_color_per		="txt_sample_color_per_".$i;
			$txt_production_color_per	="txt_production_color_per_".$i;
			$txt_yarn_cons				="txt_yarn_cons_".$i;
			$txt_process_loss			="txt_process_loss_".$i;
			$txt_yarn_actual_cons		="txt_yarn_actual_cons_".$i;
			
		   if ($add_comma1!=0) $data_array2 .=",";
			$data_array2.="(".$con_detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",".$$cbo_sample_color.",".$$cbo_stripe_color.",".$$txt_sample_color_per.",".$$txt_production_color_per.",".$$txt_yarn_cons.",".$$txt_process_loss.",".$$txt_yarn_actual_cons.",".$txt_con_per_dzn_kg.",'".$user_id."','".$pc_date_time."')";   
			
			$con_detls_id++;
			$add_comma1++;
		}
	   
		if(str_replace("'", "", $cbo_yarn_type)==2)
        {
            $add_comma2=0;
            $field_array3="id, mst_id, item_number_id, color_id, yarn_type, sample_color_ids, yarn_color_id,stripe_color_ids, sample_color_percentage,  production_color_percentage, consumption, process_loss,  actual_consumption,cons_per_dzn, inserted_by, insert_date";
            for($j=1; $j<=$twisted_color_row_num; $j++)
            {
                $sampleColorIds                 ="sampleColorIds_".$j;
                $twistedColor                   ="twistedColor_".$j;
                $sampleColorPerTwisted          ="sampleColorPerTwisted_".$j;
                $productionColorPerTtwisted     ="productionColorPerTtwisted_".$j;
                $yarnConsTwisted                ="yarnConsTwisted_".$j;
                $yarnActualConsTwisted          ="yarnActualConsTwisted_".$j;
                $processLossTwisted             ="processLossTwisted_".$j;
                $stripColorIds                  ="stripColorIds_".$j;
                $hiddenYdwId                    ="hiddenYdwId_".$j;
                
                if ($add_comma2!=0) $data_array3 .=",";
                $data_array3.="(".$con_detls_id.",".$master_id.",".$cbogmtsitem.",".$gmt_color_id.",2,'".implode(",",explode("_",$$sampleColorIds))."',".$$twistedColor.",'".$$stripColorIds."',".$$sampleColorPerTwisted.",".$$productionColorPerTtwisted.",".$$yarnConsTwisted.",".$$processLossTwisted.",".$$yarnActualConsTwisted.",".$txt_con_per_dzn_kg.",'".$user_id."','".$pc_date_time."')";   
   
                $con_detls_id++;
                $add_comma2++;
            }
        }
		
		$rID1=sql_update("ppl_size_set_mst",$field_array,$data_array,"id",$update_id,0);
		$delete_details=execute_query("update ppl_size_set_dtls set status_active=0,is_deleted=1 where item_number_id=".$cbogmtsitem." and color_id=".$gmt_color_id." and mst_id=".$master_id."",0);
		$delete_consumption=execute_query("update ppl_size_set_consumption set status_active=0,is_deleted=1 where item_number_id=".$cbogmtsitem." and color_id=".$gmt_color_id." and mst_id=".$master_id."",0);    
		if(count($data_array1)>0)  
		{
		   $rID2=sql_insert(" ppl_size_set_dtls",$field_array1,$data_array1,1); 
		}
		
		if($data_array2!="")
		{
			$rID3=sql_insert("ppl_size_set_consumption",$field_array2,$data_array2,0);
		}
        if($data_array3!="")
        {
            $rID3=sql_insert("ppl_size_set_consumption",$field_array3,$data_array3,0);
        }
		//echo "10**insert into ppl_size_set_mst($field_array)values".$data_array;die;	 
	   //echo  $rID1 . $rID2 . $rID3. $delete_details . $delete_consumption;die;
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 && $delete_details && $delete_consumption)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$master_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3 && $delete_details && $delete_consumption)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$master_id)."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
        $update_id=str_replace("'","",$update_id);
        $txt_system_no=str_replace("'","",$txt_system_no);
        $sql_check="SELECT cutting_no FROM ppl_cut_lay_mst WHERE size_set_no='$txt_system_no' AND status_active=1 and is_deleted=0";
        $check_res=sql_select($sql_check);
        if(count($check_res))
        {
            echo "121**".$check_res[0][csf('cutting_no')];
            exit();
        }
        $con = connect();   
        if($db_type==0) { mysql_query("BEGIN"); }

        $field_array="updated_by*update_date*status_active*is_deleted";
        $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
        $delete_size_set_dtls=sql_delete("ppl_size_set_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);

        $delete_size_set_consumption=sql_delete("ppl_size_set_consumption",$field_array,$data_array,"mst_id","".$update_id."",0);

         $delete_master=sql_delete("ppl_size_set_mst",$field_array,$data_array,"id","".$update_id."",0);

        if($db_type==0)
        {
            if($delete_size_set_dtls && $delete_size_set_consumption && $delete_master)
            {
                mysql_query("COMMIT");  
                echo "2**".$update_id."**".$txt_system_no."**".$delete_size_set_dtls."**".$delete_size_set_consumption."**".$delete_master;
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".$update_id."**".$txt_system_no;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($delete_size_set_dtls && $delete_size_set_consumption && $delete_master)
            {
                oci_commit($con);   
                echo "2**".$update_id."**".$txt_system_no."**".$delete_size_set_dtls."**".$delete_size_set_consumption."**".$delete_master;
            }
            else
            {
                oci_rollback($con);
                echo "10**".$update_id."**".$txt_system_no;
            }
        }
        disconnect($con);
        die;

	}		
}

if($action=="size_set_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="SELECT a.id, a.sizeset_num_prefix_no, a.sizeset_no, a.sizeset_date, a.job_no, a.buyer_id, a.sample_ref_id, a.style_ref, a.sample_ref, a.sample_size, a.yarn_controller, a.machanical_manager, a.deling_marchant, a.company_id, a.extention_no FROM ppl_size_set_mst a where a.id=".$data[1]." and  a.status_active=1 and a.is_deleted=0";
	//echo $sql;
	//echo "select id,buyer_name from lib_buyer where id=".$dataArray[0][csf('buyer_id')]." ";die;
	$dataArray=sql_select($sql);
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where id=".$dataArray[0][csf('buyer_id')]." ","id","buyer_name");
	$deling_marchan_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where id=".$dataArray[0][csf('deling_marchant')]." ","id","team_member_name");
	//print_r($buyer_arr);die;
    
	$company_library=return_library_array( "select id, company_name from lib_company where id=$data[0]", "id", "company_name"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $sampleSizeId= $dataArray[0][csf('sample_size')];;
	?>
	<div style="width:1200px;">
    	<table width="1100" cellspacing="0" align="left">
        <tr>
            <td colspan="5" align="center" style="font-size:20px">
            	<strong><? echo ' Company : '.$company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="5" align="center" style="font-size:14px">  
				<?
				foreach ($nameArray as $result)
				{ 
				?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Level No: <? echo $result[csf('level_no')] ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')]; ?>
					City No: <? echo $result[csf('city')]; ?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Province No: <?php echo $result[csf('province')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')]; ?>
					Website No: <? echo $result[csf('website')];
				}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="5" align="center" ><h3><? echo "Production Size Set Weight Calculation Sheet For Job:".$dataArray[0][csf('job_no')]." Style: ".$dataArray[0][csf('style_ref')];?></h3></td>
        </tr>
        <tr>
        	<td width="120"><strong>System ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('sizeset_no')]; ?></td>
            <td width="125"><strong>Size Set Date:</strong></td><td width="150px"><? echo change_date_format($dataArray[0][csf('sizeset_date')]); ?></td>
            <td width="140"><strong>Job No:</strong></td> <td width="250px"><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer Name:</strong></td> <td><? echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>Style Ref. :</strong></td><td><? echo $dataArray[0][csf('style_ref')]; ?></td>
			<td><strong>Del. Marchan. :</strong></td> <td><? echo $deling_marchan_arr[$dataArray[0][csf('deling_marchant')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Sample Ref.:</strong></td> <td><? echo $dataArray[0][csf('sample_ref')]; ?></td>
            <td><strong>Sample Size :</strong></td><td><? echo $dataArray[0][csf('sample_size')]; ?></td>
            <td><strong>GMT. Item:</strong></td><td><?=$garments_item[$data[4]]; ?></td>
        </tr>
        <tr>
        	<td><strong>GMT. Color:</strong></td><td><?=$color_arr[$data[3]]; ?></td>
            <td><strong>Tech. Manager :</strong></td><td><? echo $dataArray[0][csf('machanical_manager')]; ?>&nbsp;</td>
            <td><strong>Yarn Controller :</strong></td><td><? echo $dataArray[0][csf('yarn_controller')]; ?></td>
        </tr>
        <tr>
            <td><strong>Extantion No :</strong></td><td align="left"><? echo $dataArray[0][csf('extention_no')]; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
        <br>
	
    <?php
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$color_size_result=sql_select("SELECT id, mst_id, color_id, gmt_size_id, sample_weight, production_weight,  short_excess, plan_cut_qty, total_weight,  avg_weight from ppl_size_set_dtls where mst_id=$data[1] and item_number_id=$data[4] and color_id=$data[3] and status_active=1 and is_deleted=0 order by id");
	
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]==0)	
		{
			$avg_sample_weight=$row[csf('sample_weight')];
			$avg_production_weight=$row[csf('production_weight')];
			$total_plancut_qty=$row[csf('plan_cut_qty')];
			$total_size_weight=$row[csf('total_weight')];
			$total_avg_weight=$row[csf('avg_weight')];
		}
	}
	?>
  
    <div style="width:100%;">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_size_wise_weight" align="left" style="margin-top:20px;">
            <thead>
                <tr>
                    <th width="100">Garments Size</th>
                    <th width="150">Sample Weight(GM)/Pcs</th>
                    <th width="150">Production Weight(GM)/Pcs</th>
                    <th width="150">Short/Excess</th>
                    <th width="150">Plan Qty (Pcs)</th>
                    <th width="100">Total Weights (KG)</th>
                    <th width="">Average Weight %</th>
                </tr>
            </thead>
            <tbody>
			<?
                $i=1; $total_plan_qty=0; $sizeWiseProdQtyArr=array();
                foreach($color_size_result as $row)
                { 
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    if($row[csf('gmt_size_id')]!=0)	
                    {
						$sizeWiseProdQtyArr[$size_arr[$row[csf('gmt_size_id')]]]=$row[csf('production_weight')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                            <td width="100" align="lert"> <?  echo $size_arr[$row[csf('gmt_size_id')]]; ?></td>
                            <td width="150" align="right"><?php echo number_format($row[csf('sample_weight')],4); ?></td>
                            <td width="150" align="right"><?php echo number_format($row[csf('production_weight')],4); ?></td>
                            <td width="150" align="right"><?php echo number_format($row[csf('short_excess')],4); ?></td>
                            <td width="150" align="right"><?php echo $row[csf('plan_cut_qty')]; ?></td>
                            <td align="right" width="100"><?php echo number_format($row[csf('total_weight')],2); ?></td>
                            <td align="right"><?php echo number_format($row[csf('avg_weight')],2); ?></td>
                        </tr>
                <?
                    $i++;
                    }
                } 
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="100">Total</th>
                    <th width="150" align="right"><?php echo number_format($avg_sample_weight,4); ?></th>
                    <th width="150"align="right"><?php echo number_format($avg_production_weight,2); ?></th>
                    <th width="150"align="right"></th>
                    <th width="150" align="right"><?php echo $total_plancut_qty; ?></th>
                    <th width="100" align="right"><?php echo number_format($total_size_weight,2); ?></th>
                    <th width="" align="right"><?php echo number_format($total_avg_weight,2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <br/>
    
    
    <?
		$i=1;
		$data_array_strip=sql_select("SELECT id, sample_color_id, sample_color_ids, yarn_color_id, sample_color_percentage, production_color_percentage, consumption , process_loss, actual_consumption , cons_per_dzn from ppl_size_set_consumption where mst_id=$data[1] and item_number_id=$data[4] and color_id=$data[3] and status_active=1 and is_deleted=0 order by id");
        foreach ($data_array_strip as $value) {
            $consumtion_without_process_loss+=$value[csf('consumption')];
            $colspan+=1;
        }
	?>
    <div>
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="" align="left" id="" style="margin-top:20px;">
            <thead>
            	<tr>
                    <th width="350" colspan="2">Consumption Per Dozen Without Process Loss</th>
                    <th width="100" ><?php echo number_format($consumtion_without_process_loss,4); ?> Kg</th>
                    <th width="100"><?php echo number_format($consumtion_without_process_loss*2.2046226,4); $consumtion_without_process_loss_lbs=$consumtion_without_process_loss*2.2046226; ?> Lbs </th>
                    <th width="" colspan="5"></th>
                </tr>
                <tr>
                    <th width="100">Sample Color</th>
                    <th width="250">Yarn Color</th>
                    <th width="100">Sample Color %</th>
                    <th width="100">Production Color %</th>
                    <th width="100">Cons (Kg)</th>
                    <th width="100">Cons (Lbs)</th>
                    <th width="100">Process Loss %</th>
                    <th width="100">Avg. Actual Cons (Kg)</th>
                    <th width="100">Avg. Actual Cons (Lbs)</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$yarnColorArr=array();
					foreach($data_array_strip as $row)
                    { 
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
							<td width="100"><?
								if($row[csf('sample_color_ids')])
								{
									foreach (explode(",",$row[csf('sample_color_ids')]) as $value) {
										echo $color_arr[$value]." ";
									}
									//echo $row[csf('sample_color_ids')];
								}
								else
								{
									echo $color_arr[$row[csf('sample_color_id')]];
								}
								$yarnColorArr[$color_arr[$row[csf('yarn_color_id')]]]['prod_color_per']=$row[csf('production_color_percentage')];
								$yarnColorArr[$color_arr[$row[csf('yarn_color_id')]]]['process_loss']=$row[csf('process_loss')];
							?> </td>
							<td width="250"><? echo $color_arr[$row[csf('yarn_color_id')]];?></td>
							<td width="100" align="right"><?php echo $row[csf('sample_color_percentage')];?></td>
							<td width="100" align="right"><?php echo $row[csf('production_color_percentage')];?></td>
							<td width="100" align="right"><?php echo number_format($row[csf('consumption')],4); ?></td>
                            <td width="100" align="right"><?php echo number_format(($row[csf('consumption')]*2.2046),4); ?></td>
						   <td align="right"><?php echo number_format($row[csf('process_loss')],2); ?> </td>					   
						   <td align="right" width="100"><?php echo number_format($row[csf('actual_consumption')],4); ?></td>
						   <td align="right" width="100"><?php echo number_format(( $row[csf('actual_consumption')]*2.2046),4); ?></td>
						</tr>
						<?
					
						$i++;
                    } 
					
					$column=count($sizeWiseProdQtyArr);
					
					$tblWidth=100+($column*70);
                    ?>
            </tbody>
        </table>
        <br>
        <table><tr><td>
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<?=$tblWidth; ?>" align="left" style="margin-top:20px;">
            <thead>
            	<tr>
                    <th colspan="<?=$column+1; ?>">Size wise  Weight (Lbs)</th>
                </tr>
                <tr>
                    <th width="100">Yarn Color</th>
                    <?
					foreach($sizeWiseProdQtyArr as $sizeNmae=>$prodQty)
					{
						?>
                        <th width="70"><?=$sizeNmae; ?></th>
                        <?
					}
					?>
                </tr>
            </thead>
            <tbody>
            <? $yc=1; $sizeSummArr=array();
			foreach($yarnColorArr as $ycolor=>$ycolorVal)
			{
				if($yc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$yc; ?>"> 
                    <td width="100" style="word-break:break-all"><?=$ycolor; ?></td>
                    <?
					foreach($sizeWiseProdQtyArr as $sizeNmae=>$prodQty)
					{
						$colorSizeQty=0;
						$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
						?>
                        <td width="70" align="right" title="<?='(('.$prodQty.'*0.00220462262)*12)*('.$ycolorVal['prod_color_per'].'/100)*(1+('.$ycolorVal['process_loss'].'/100))'; ?>"><?=number_format($colorSizeQty,4); ?></td>
                        <?
						$sizeSummArr[$sizeNmae]+=$colorSizeQty;
					}
					?>
                 </tr>
                 <?
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
            	<td>Total:</td>
                <?
                foreach($sizeWiseProdQtyArr as $sizeNmae=>$prodQty)
				{
					?><td width="70" align="right"><?=number_format($sizeSummArr[$sizeNmae],4); ?></td><?
				}
				?>
            </tr>
        </tfoot>
    </table>
    </td></tr><tr><td>
    <br>
        <?php 


        $sql_wet_sheet="select b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$dataArray[0][csf('sample_ref')]."' and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

        $wet_sheet_result=sql_select($sql_wet_sheet);
        $bodypart_color_qty_arr=array();
        $knitting_gmm_total=0;
       
        foreach ($wet_sheet_result as  $value) {
            if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
           $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
           $knitting_gmm_total+=$value[csf('bodycolor')];
        }

        //echo $knitting_gmm_total;die;

        ?>
   
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="" align="left" id="" style="margin-top:20px;">
            <thead>
                <tr>
                   
                    <th colspan="<?php echo count($data_array_strip)+2; ?>">Body Part Wise Consumption Details Per Pcs (Gm) </th>
                </tr>
                <tr>
                    <th width="" ></th>                  
                    <th width="" colspan="<?php echo count($data_array_strip); ?>">Sample Color</th>
                    <th width="" ></th>
                </tr>
                <tr>
                    <th width="200">Body Parts</th>
                    <?php 
                        foreach ($data_array_strip as  $sample_color) {
                        	if($sample_color[csf('sample_color_ids')])
                        	{
                        		?>
	                            <th width="100">
	                            <?php
                        		foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                        			echo $color_arr[$sc_id]."  ";
								}
								?>
								 </th>
	                            <?php
                        	}
                        	else 
                        	{
	                            ?>
	                            <th width="100"><? echo $color_arr[$sample_color[csf('sample_color_id')]];?> </th>
	                            <?php
                        	}
                        }

                    ?>
                    
                    <th width="100">Total</th>
                </tr>
            </thead>
            <tbody>
                <?
                   
                    $SampleSizeColorGrandTotal= ($sizeSummArr[$sampleSizeId]/12)*453.592;
                    $bodypart_color_total_arr=array();
                    $color_bodypart_total_arr=array();
                    $consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
                    foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
                    { 
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                            <td width="200"><? echo $time_weight_panel[$body_part_id];?> </td>
                            <?php 
                                foreach ($data_array_strip as  $sample_color)
                                {
                                	
                                    /*if($sample_color[csf('sample_color_ids')])
                                	{
                                        
                                		foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                                			//$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                		//$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
		                                   // $color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
		                                   // $bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                	}
                                	}
                                	else
                                	{
                                        //echo "<pre>2</pre>";
	                                    //$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                    //$color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                    //$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                }
                                    */
                                    
                                        $color_cons=0;
                                        if($sample_color[csf('sample_color_ids')])
                                        {
                                            
                                            foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                                                $BodyPartColorTotal=$body_part_row[$sc_id];
                                                $Percentange=($BodyPartColorTotal/$knitting_gmm_total);
                                                $color_cons+=($SampleSizeColorGrandTotal*$Percentange);
                                                $title_string.="(".$BodyPartColorTotal."/".$knitting_gmm_total.")*".$SampleSizeColorGrandTotal."   ,  ";
                                            }
                                           
                                            $color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]+=$color_cons;
                                            $color_bodypart_grand_total_arr[$sample_color[csf('sample_color_ids')]]+=$color_cons;
                                            //echo number_format(($body_part_row[$sample_color[csf('sample_color_ids')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total),4);
                                        }
                                        else
                                        {
                                            
                                            $BodyPartColorTotal=$body_part_row[$sample_color[csf('sample_color_id')]];
                                            $Percentange=($BodyPartColorTotal/$knitting_gmm_total); 
                                            $color_cons=($SampleSizeColorGrandTotal*$Percentange);
                                            $color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]+=$color_cons;
                                            $color_bodypart_grand_total_arr[$sample_color[csf('sample_color_id')]]+=$color_cons;

                                            $title_string="(".$BodyPartColorTotal."/".$knitting_gmm_total.")*".$SampleSizeColorGrandTotal;
                                            //echo number_format(($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total),4);
                                        }
                                       
                                       // $title_string=$body_part_row[$sample_color[csf('sample_color_id')]]."*".$consumtion_without_process_loss_lbs_per_pcs."/".$knitting_gmm_total;

                                       
                                        
                                        $bodypart_color_total_arr[$body_part_id]+=$color_cons;
                                        $bodypart_main_total+=($color_cons);
                                        $bodypart_grand_total+=($color_cons);
                                     ?>
                                    <td width="100" align="right" title="<?=$title_string ?>"><?
                                        
                                        echo number_format( $color_cons,4);

                                         ?> 
                                    </td>
                                    <?php
                                }

                            ?>
                            <td width="100" align="right"><?php echo number_format($bodypart_color_total_arr[$body_part_id],4);?></td>                     
                          
                        </tr>
                        <?
                    
                        $i++;
                    } 
                    ?>
                    <tr> 
                        <td width="200"><strong>Body Fabric Total</strong></td>
                            <?php 
                                foreach ($data_array_strip as  $sample_color) {
                                  
                                    ?>
                                    <td width="100" align="right"><strong><?
                                    if($sample_color[csf('sample_color_ids')])
                                	{
                                		 echo  number_format(($color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]),4);
                                	}
                                	else 
                                	{
                                		 echo number_format(($color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]),4);
                                	}
                                     ?> </strong></td>
                                    <?php
                                }

                            ?>
                        <td width="100" align="right"><strong><?php echo number_format($bodypart_main_total,4);?></strong></td>                   
                    </tr>
                    <?
                    $bodypart_color_total_arr=array();
                    $color_bodypart_total_arr=array();
                    $bodypart_main_total=0;
                    foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
                    { 
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                            <td width="200"><? echo $time_weight_panel[$body_part_id];?> </td>
                            <?php 
                                foreach ($data_array_strip as  $sample_color) {
                                    /*
                                	if($sample_color[csf('sample_color_ids')])
                                	{
                                		foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                                			$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                		$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
		                                    $color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
		                                    $bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                	}
                                	}
                                	else
                                	{
	                                    $bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                    $color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                    $bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
	                                }
                                    */
                                        $color_cons=0;
                                        if($sample_color[csf('sample_color_ids')])
                                        {
                                            
                                            foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                                                $BodyPartColorTotal=$body_part_row[$sc_id];
                                                $Percentange=($BodyPartColorTotal/$knitting_gmm_total);
                                                $color_cons+=($SampleSizeColorGrandTotal*$Percentange);
                                                $title_string.="(".$BodyPartColorTotal."/".$knitting_gmm_total.")*".$SampleSizeColorGrandTotal."   ,  ";
                                            }
                                           
                                            $color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]+=$color_cons;
                                            $color_bodypart_grand_total_arr[$sample_color[csf('sample_color_ids')]]+=$color_cons;
                                            //echo number_format(($body_part_row[$sample_color[csf('sample_color_ids')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total),4);
                                        }
                                        else
                                        {
                                            
                                            $BodyPartColorTotal=$body_part_row[$sample_color[csf('sample_color_id')]];
                                            $Percentange=($BodyPartColorTotal/$knitting_gmm_total); 
                                            $color_cons=($SampleSizeColorGrandTotal*$Percentange);
                                            $color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]+=$color_cons;
                                            $color_bodypart_grand_total_arr[$sample_color[csf('sample_color_id')]]+=$color_cons;

                                            $title_string="(".$BodyPartColorTotal."/".$knitting_gmm_total.")*".$SampleSizeColorGrandTotal;
                                            //echo number_format(($body_part_row[$sample_color[csf('sample_color_id')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total),4);
                                        }

                                        $bodypart_color_total_arr[$body_part_id]+=$color_cons;
                                        
                                        $bodypart_main_total+=($color_cons);
                                        $bodypart_grand_total+=($color_cons);
                                       
                                    ?>
                                    <td width="100" align="right" title="<?=$title_string?>"><?
                                            echo number_format( $color_cons,4);
                                     ?> </td>
                                    <?php
                                }

                            ?>
                            <td width="100" align="right"><?php echo number_format($bodypart_color_total_arr[$body_part_id],4);?></td>                     
                          
                        </tr>
                        <?
                    
                        $i++;
                    } 
                    ?>
                    <tr> 
                        <td width="200"><strong>Trims Fabric Total</strong></td>
                            <?php 
                                foreach ($data_array_strip as  $sample_color) {
                                  
                                    ?>
                                    <td width="100" align="right"><strong><?
                                    if($sample_color[csf('sample_color_ids')])
                                	{
                                		 echo number_format(($color_bodypart_total_arr[$sample_color[csf('sample_color_ids')]]),4);
                                	}
                                	else
                                	{
                                     echo number_format(($color_bodypart_total_arr[$sample_color[csf('sample_color_id')]]),4);
									}
                                     ?></strong> </td>
                                    <?php
                                }

                            ?>
                        <td width="100" align="right"><strong><?php echo number_format($bodypart_main_total,4);?></strong></td>                   
                    </tr>

                    <tr> 
                        <td width="200"><strong>Grand Total</strong></td>
                            <?php 
                                foreach ($data_array_strip as  $sample_color) {
                                  
                                    ?>
                                    <td width="100" align="right"><strong><?
                                    if($sample_color[csf('sample_color_ids')])
                                    {
                                         echo number_format(($color_bodypart_grand_total_arr[$sample_color[csf('sample_color_ids')]]),4);
                                    }
                                    else
                                    {
                                     echo number_format(($color_bodypart_grand_total_arr[$sample_color[csf('sample_color_id')]]),4);
                                    }
                                     ?></strong> </td>
                                    <?php
                                }

                            ?>
                        <td width="100" align="right"><strong><?php echo number_format($bodypart_grand_total,4);?></strong></td>                   
                    </tr>
                </tbody>           
        </table>
        </td></tr></table>
    </div>
    
    <?php 

    $sql_color_size=sql_select("select size_number_id, color_number_id ,sum(plan_cut_qnty) as plan_cut_qty from wo_po_color_size_breakdown  where job_no_mst='".$dataArray[0][csf('job_no')]."' and item_number_id='$data[4]' and status_active=1 and is_deleted=0 group by size_number_id, color_number_id");
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $size_total_arr=array();
    $color_total_arr=array();
    $color_size_total_arr=array();
    $size_id_arr=array();
    $color_id_arr=array();
    $job_total=0;
    foreach ($sql_color_size as  $val) {
       $color_size_total_arr[$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('plan_cut_qty')];
       $size_total_arr[$val[csf('size_number_id')]]+=$val[csf('plan_cut_qty')];
       $color_total_arr[$val[csf('color_number_id')]]+=$val[csf('plan_cut_qty')];
       $size_id_arr[$val[csf('size_number_id')]]=$size_arr[$val[csf('size_number_id')]];
       $color_id_arr[$val[csf('color_number_id')]]=$color_arr[$val[csf('color_number_id')]];
       $job_total+=$val[csf('plan_cut_qty')];
    }
    ?>

    <div style="width:100%;">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_size_wise_weight" align="left" style="margin-top:20px;display: none;" >
            <thead>
                <tr>                  
                    <th colspan="<?php echo count($size_arr)+2; ?>">Color Size Breakdown </th>
                </tr>               
                <tr>
                    <th width="200">GMT. Color</th>
                    <?php 
                        foreach ($size_id_arr as  $size_id=>$size_name) {
                            ?>
                            <th width="100"><? echo $size_name;?> </th>
                            <?php
                        }
                    ?>                    
                    <th width="100">Total</th>
                </tr>
            </thead>
            <tbody>
                 <?php 
                 foreach ($color_id_arr as $color_id => $color_name) {
                                 # code...
                ?>           
                <tr>
                    <td width="200"><?php echo $color_name; ?></td>
                    <?php 
                        foreach ($size_id_arr as  $size_id=>$size_name) {
                            ?>
                            <td width="100" align="right"><? echo $color_size_total_arr[$color_id][$size_id];?> </td>
                            <?php
                        }

                    ?>                    
                    <td width="100" align="right"><? echo $color_total_arr[$color_id];?></td>
                </tr>
                <?php 
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="200">Size Total</th>
                    <?php 
                        foreach ($size_id_arr as  $size_id=>$size_name) {
                            ?>
                            <th width="100" align="right"><? echo $size_total_arr[$size_id];?> </th>
                            <?php
                        }

                    ?>                    
                    <th width="100" align="right"><? echo $job_total;?></th>
                </tr>
            </tfoot>
       
        </table>
    </div>
    <div>
		 <?
            echo signature_table(9, $data[0], "1100px");
         ?>
      </div>
   </div> 
    <script type="text/javascript" src="../../../js/jquery.js"></script>
  
     <?
	 exit(); 
}

?>