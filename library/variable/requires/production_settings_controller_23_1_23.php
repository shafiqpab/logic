<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	//echo $type;
	$company_id = $explode_data[1];

	if ($type==1)   //Production_Production Update Areas
	{
			$nameArray= sql_select("SELECT id, company_name,variable_list,cutting_update_hcode,cutting_update,cutting_input, printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, fabric_roll_level_hcode, fabric_roll_level, fabric_machine_level_hcode,fabric_machine_level, batch_maintained_hcode,batch_maintained, iron_input,production_entry,leftover_maintained,leftover_country_maintained,leftover_source,finish_fabric_req_cutting,hang_tag_update,working_company_mandatory,smv_source from variable_settings_production where company_name='$company_id' and variable_list=1 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
 			?>
            <fieldset>
                <legend>Production Update Areas</legend>
                <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    <table cellspacing="5" width="100%">
                        <tr>
                            <td width="130" align="left" id="cutting_update">Cutting Update</td>
                            <td width="190">
                                  	<?
										echo create_drop_down( "cbo_cutting_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('cutting_update')], "","","1,2,3" );
									?>
                            </td>
                            <td width="160" align="left" id="printing_emb_production">Printing & Embrd. Prodiction</td>
                            <td width="180">
                                 	<?
										echo create_drop_down( "cbo_printing_emb_production", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('printing_emb_production')], "","","1,2,3" );
									?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" id="sewing_production">Sewing Production</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_sewing_production", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('sewing_production')], "","","1,2,3" );
									?>
                             </td>
                            <td align="left" id="finishing_update">Finishing Entry</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_finishing_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('finishing_update')], "","","1,2,3,4" );
									?>
                             </td>
                        </tr>
                        <tr>
                        	<td align="left" id="iron_update">Iron Output</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_iron_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('iron_update')], "","","1,2,3" );
									?>
                            </td>
                            <td align="left" id="ex_factory">Ex-Factory</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_ex_factory", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('ex_factory')], "","","1,2,3,4" );
									?>
                             </td>
                        </tr>
                        <tr>
                             <!-- <td align="left" id="production_entry">Production Entry</td>
                             <td>
                                 <?
										//$production_entry = array(1=>'Style Wise',2=>'Order Wise');
										//echo create_drop_down( "cbo_production_entry", 170, $production_entry,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "" );
									?>
                             </td> -->
                             <td id="cutting_delevery_entry">Cutting delivery to Input</td>
                             <td>
                                  <?
										echo create_drop_down( "cbo_cutting_update_to_input", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('cutting_input')], "","","1,2,3" );
									?>

                             </td>
                             <td id="cutting_delevery_entry">Left Over</td>
                             <td>
                                  <?
										echo create_drop_down( "cbo_leftover", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('leftover_maintained')], "","","1,2,3" );
									?>

                             </td>
                        </tr>
                        <tr>
                             <td id="cutting_delevery_entry">Left Over Country Maintain</td>
                             <td>
                                  <?
										echo create_drop_down( "cbo_leftover_country", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('leftover_country_maintained')], "" );
									?>

                             </td>

                             <td id="cutting_delevery_entry">Left Over Source</td>
                             <td>
                                  <?
                                  	$lftovr_source_arr = array(1 => 'Sewing Output' ,2=>'Poly Entry' );
										echo create_drop_down( "cbo_leftover_source", 170, $lftovr_source_arr,'', 1, '---- Select ----', $nameArray[0][csf('leftover_source')], "" );
									?>

                             </td>
                        </tr>
						<tr>

							<td align="left" id="iron_update">WO Company & Location Maintain(Left Over Rcv)</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_wo_maintain", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('working_company_mandatory')], "" );
									?>
                            </td>
							<td id="cutting_delevery_entry">Finish Fabric Requisition for Cutting</td>
							<td>
								<?
										echo create_drop_down( "cbo_finish_fabric_req_cutting", 170,  $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('finish_fabric_req_cutting')], "","","2,3" );
								?>
							</td>
						</tr>
						<tr>

							<td align="left" id="iron_update">Hang Tag Entry</td>
                            <td>
                                <?
										echo create_drop_down( "cbo_hang_tag_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('hang_tag_update')], "","","1,2,3");
									?>
                            </td>

							<td align="left" id="fin_gmt_transfer"> Fin Gmts Order to Order Transfer</td>
							<td>
								<?
										echo create_drop_down( "cbo_fin_gmt_transfer", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('smv_source')], "","","1,2,3" );
									?>
							</td>
						</tr>

                    </table>
                </div>
                <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="center" width="320">&nbsp;</td>
 						</tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
								<?
									echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
					</table>
				</div>
            </fieldset>

		<?

	}
	else if ($type==2)
	{ 	//Production_Excess Cutting Slab
  		?>
					<fieldset>
						<legend>Excess Cutting Slab</legend>
						 <div style="width:500px;" align="left">
							<table cellspacing="0" width="100%" >
								<tr>
								   <td width="150" align="center" id="order_quantityStart" colspan="2">Slab Range Start</td>
									<td width="150" align="center" id="order_quantityEnd" colspan="2">Slab Range End</td>
									<td width="150" align="center" id="excess_percent">Excess %</td>
								</tr>
							</table>
						</div>
						<div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
							<table cellspacing="0" width="100%" id="tbl_slab" >
								<?
									$i=0;
									$sub_nameArray= sql_select("select id,company_name,variable_list,slab_rang_start,slab_rang_end,excess_percent from variable_prod_excess_slab where company_name='$company_id' and variable_list=2 order by id");
 									foreach($sub_nameArray as $rows)
									{
										$i++;
								?>

								 <tr style="text-decoration:none">
									<td width="150"><input type="text" name="txt_slab_rang_start<? echo $i; ?>" id="txt_slab_rang_start<? echo $i; ?>" onchange="next_number(<? echo $i; ?>)" value="<? echo $rows[csf("slab_rang_start")]; ?>" class="text_boxes_numeric" style="width:150px;"/></td>
									<td width="150"><input type="text" name="txt_slab_rang_end<? echo $i; ?>" id="txt_slab_rang_end<? echo $i; ?>" value="<? echo $rows[csf("slab_rang_end")]; ?>" class="text_boxes_numeric" style="width:150px;"/></td>
									<td width="150"><input type="text" name="txt_excess_percent<? echo $i; ?>" id="txt_excess_percent<? echo $i; ?>" value="<? echo $rows[csf("excess_percent")]; ?>" class="text_boxes_numeric" onfocus="add_variable_row(<? echo $i; ?>)"  style="width:150px;"/></td>
								</tr>
								<? }

								if($i==0)
								{
									$i++;
								?>
 								<tr style="text-decoration:none">
									<td width="150"><input type="text" name="txt_slab_rang_start<? echo $i; ?>" id="txt_slab_rang_start<? echo $i; ?>" onchange="next_number(<? echo $i; ?>)" class="text_boxes_numeric"  style="width:150px;" value="" /></td>
									<td width="150"><input type="text" name="txt_slab_rang_end<? echo $i; ?>" id="txt_slab_rang_end<? echo $i; ?>" class="text_boxes_numeric" style="width:150px;"  value="" /></td>
									<td width="150"><input type="text" name="txt_excess_percent<? echo $i; ?>" id="txt_excess_percent<? echo $i; ?>" class="text_boxes_numeric" onfocus="add_variable_row(<? echo $i; ?>)"   style="width:150px;"  value="" /></td>
								</tr>
								<? } ?>
							</table>
						</div>
						 <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
							<table cellspacing="0" width="100%" >
                                <tr>
                                    <td align="center" width="320">&nbsp;</td>
                                </tr>
                                <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="">
                                        <?
                                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                        ?>
                                    </td>
                                </tr>
                            </table>
						</div>
					</fieldset>
		<?
	}
	else if ($type==3)
	{	//Production_Fabric in Roll Level

 			/*$nameArray= sql_select("select id, company_name,variable_list,cutting_update_hcode,cutting_update, printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, fabric_roll_level_hcode, fabric_roll_level, fabric_machine_level_hcode,fabric_machine_level, batch_maintained_hcode,batch_maintained, iron_input,production_entry from variable_settings_production where company_name='$company_id' and variable_list=3 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;*/

			$category_wise_array=array();
			$nameArray= sql_select("select id, company_name,variable_list,item_category_id,fabric_roll_level,page_upto_id from variable_settings_production where company_name='$company_id' and variable_list=3 and status_active=1 and is_deleted= 0 order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['roll']=$row[csf('fabric_roll_level')];
				$category_wise_array[$row[csf('item_category_id')]]['upto']=$row[csf('page_upto_id')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
			}
			/*echo '<pre>';
			print_r($category_wise_array); die;*/
     	?>

            <fieldset>
                <legend>Fabric in Roll Level</legend>
                <div style="width:400px; max-height:250px;" id="variable_list_cont3" align="center">
                	<table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                    	<thead>
                        	<th>Item Category</th>
                            <th>Fabric in Roll Level</th>
                            <th>Upto</th>
                        </thead>
                        <tr align="center">
							<td>
                                 <?
									echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '','13', "",'','13' );
								?>
							</td>
							<td>
                                 <?
								 $dd="search_populate(this.value)";
									echo create_drop_down( "cbo_fabric_roll_level_1", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[13]['roll'], $dd,'','' );
								?>
							</td>
                            <td id="upto_td">

                            <?  if($category_wise_array[13]['roll']==1) echo 'Upto Receive By Batch';else echo ''; ?>


							</td>
                            <input type="hidden" name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[13]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                 <?
								 $batch_category=array(50=>"Batch");
									echo create_drop_down( "cbo_item_category_2", 150, $batch_category,'', 0, '',$category_wise_array[50]['roll'], "",'','' );
								?>
							</td>
							<td>
                                 <?
								 //$dd="search_populate(this.value)";
									echo create_drop_down( "cbo_fabric_roll_level_2", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[50]['roll'], '','','' );
								?>
							</td>
                            <td>
                                 <?
								 $dd2="search_populate2(this.value)";
								 if($category_wise_array[13]['roll']==1) $disable_row=0;else  $disable_row=1;
									//$upto_receive_batch=array(1=>'Heat setting',2=>'Dyeing',3=>'Slitting/Squeezing',4=>'Stentering',5=>'Drying',6=>'Special Finish',7=>'Compacting',8=>'Cutting Lay');
									echo create_drop_down( "cbo_entry_form_roll_level_2", 150, $upto_receive_batch,'', 1, '---- Select ----',$category_wise_array[50]['upto'], $dd2,$disable_row,'' );
								?>
							</td>
                            <input type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[50]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category_3", 150, $item_category,'', 0, '', '2', "",'','2' );
								?>
							</td>
                            <td>
                                 <?

								  //$dd="search_populate(this.value)";
								   if($category_wise_array[2]['roll']==1) $disable_row=0;else  $disable_row=1;
									echo create_drop_down( "cbo_fabric_roll_level_3", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[2]['roll'], '',$disable_row,'' );
								?>
							</td>
                            <td>

							</td>

                            <input  type="hidden"name="update_id_3" id="update_id_3" value="<? echo $category_wise_array[2]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                <?
								$cut_lay_category=array(51=>"Cut and Lay Roll Wise");
								echo create_drop_down( "cbo_item_category_5", 150, $cut_lay_category,'', 0, '', '', "",'','' );
								?>
							</td>
                            <td>
                                 <?
								  //$dd="search_populate(this.value)";
									echo create_drop_down( "cbo_fabric_roll_level_5", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[51]['roll'],'','','' );
								?>
							</td>
                            <td>

							</td>

                            <input  type="hidden" name="update_id_5" id="update_id_5" value="<? echo $category_wise_array[51]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                 <?

									echo create_drop_down( "cbo_item_category_4", 150, $item_category,'', 0, '','3', "",'','3' );
								?>
							</td>
							<td>
                                 <?
									//$dd="search_populate(this.value)";
									echo create_drop_down( "cbo_fabric_roll_level_4", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[3]['roll'], '','','' );
								?>
							</td>
                            <td>
                                 <?
									//echo create_drop_down( "cbo_fabric_roll_level_3", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[3]['roll'], "",'','' );
								?>
							</td>
                            <input type="hidden" name="update_id_4" id="update_id_4" value="<? echo $category_wise_array[3]['update_id']; ?>">
						</tr>
					</table>
                    <!--<table cellspacing="0" width="100%" >
                        <tr>
                            <td width="130" align="left" id="fabric_roll_level" cbo_fabric_roll_level>Fabric in Roll Level</td>
                            <td width="190">
                                 <?//echo create_drop_down( "cbo_fabric_roll_level", 170, $yes_no,'', 0, '---- Select ----', $nameArray[0][csf('fabric_roll_level')], "",'','1' );
									//echo create_drop_down( "cbo_fabric_roll_level", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('fabric_roll_level')], "",'','' );
								?>
                            </td>
                        </tr>
                    </table>-->
                </div>
				<div style="width:400px; max-height:250px;" id="variable_list_cont3" align="center">
                    <table cellspacing="0" width="100%" >
                                <tr>
                                    <td align="center" width="320">&nbsp;</td>
                                </tr>
                                <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                        <?
                                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                        ?>
                                    </td>
                                </tr>
                     </table>
                </div>
                <script>
				$('#cbo_fabric_roll_level_3').val(2);
				</script>
            </fieldset>
		<?
		exit();
	}
	else if ($type==4)
	{
		//Production_Fabric in Machine Level

			$nameArray= sql_select("select id, company_name,variable_list,cutting_update_hcode,cutting_update, printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, fabric_roll_level_hcode, fabric_roll_level, fabric_machine_level_hcode,fabric_machine_level, batch_maintained_hcode,batch_maintained, iron_input,production_entry from variable_settings_production where company_name='$company_id' and variable_list=4 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Fabric in Machine Level</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="fabric_machine_level">Fabric in Machine Level</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_fabric_machine_level", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('fabric_machine_level')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

		<?
	}
	else if ($type==13)
	{//Production_Batch Maintained

			$nameArray= sql_select("select id, company_name,variable_list,cutting_update_hcode,cutting_update, printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, fabric_roll_level_hcode, fabric_roll_level, fabric_machine_level_hcode,fabric_machine_level, batch_maintained_hcode,batch_maintained, iron_input,production_entry from variable_settings_production where company_name='$company_id' and variable_list=13 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;

		?>

 			<fieldset>
				<legend>Batch Maintained</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td width="130" align="left" id="batch_maintained">Batch Maintained</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_batch_maintained", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('batch_maintained')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	else if ($type==15)
	{//Production Auto Faric Store Update

			$category_wise_array=array();
			$nameArray= sql_select("select id, company_name,variable_list,item_category_id,auto_update, distribute_qnty from variable_settings_production where company_name='$company_id' and variable_list= '$type' and status_active=1 and is_deleted=0 order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
				$category_wise_array[$row[csf('item_category_id')]]['excess_val']=$row[csf('distribute_qnty')];
			}
			if($category_wise_array[2]['auto']==1){
				$disable_st="style='display:none;'";
			}else{
				$disable_st="";
			}
		?>
 			<fieldset>
				<legend>Auto Fabric Store Update</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Item Category</th>
                            <th>Auto Update</th>
                            <th>Receive Basis</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '', '2', "",'','2' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_1", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[2]['auto'], "fnc_enable_disable_excess($type,this.value,1)",'','' );									
								?>
							</td>
							<td id="rcvBasisId" <? echo $disable_st; ?> >
								<?
									echo create_drop_down( "cbo_receive_basis_1", 150, $receive_basis_arr,'', 0, '', $category_wise_array[2]['excess_val'], "",'','9,16','' );
								?>
							</td>
                            <input  type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[2]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                 <?
									echo create_drop_down( "cbo_item_category_2", 150, $item_category,'', 0, '','13', "",'','13' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_2", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[13]['auto'], "",'','' );
								?>
							</td>
							<td>
								
							</td>
                            <input type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[13]['update_id']; ?>">
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings",$is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	else if($type==23)//Production Resource Allocation
	{
			$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend>Production Resource Allocation</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id="ProductionResourceAllocation">Production Resource Allocation</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_prod_resource", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('auto_update')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==24)
	{//Production Resource Allocation

		$nameArray=sql_select("select id, batch_no_creation,yd_batch_no_creation from variable_settings_production where company_name='$company_id' and variable_list=24 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Batch No</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_batch_no", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('batch_no_creation')], "",'','' );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="left" id="batch_no">Yarn Dyeing Batch No</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_yd_batch_no", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('yd_batch_no_creation')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==25)
	{//SMV Source

		$nameArray=sql_select("select id, smv_source from variable_settings_production where company_name='$company_id' and variable_list=25 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		$smv_source_arr=array(1=>"From Order Entry",2=>"From Pre-Costing",3=>"From GSD Entry");
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">SMV Source</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_source", 170, $smv_source_arr,'', 0, '---- Select ----', $nameArray[0][csf('smv_source')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==26)
	{//Sewing Production

		$dataArray=array();
		if($db_type==0)
		{
			$nameArray=sql_select("select id,shift_id,prod_start_time,lunch_start_time from variable_settings_production where company_name='$company_id' and variable_list=26 and status_active=1 and is_deleted=0");
		}
		else
		{
			$nameArray=sql_select("select id, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name='$company_id' and variable_list=26 and status_active=1 and is_deleted=0");
		}
		foreach($nameArray as $row)
		{
			$dataArray[$row[csf('shift_id')]]=$row[csf('id')]."**".$row[csf('prod_start_time')]."**".$row[csf('lunch_start_time')];
		}

		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Sewing Production </legend>
            <div style="width:420px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="100">Shift Name</th>
                        <th width="160">Production Start Time</th>
                        <th>Lunch Start Time</th>
                    </thead>
                    <?
						$i=1;
						foreach($shift_name  as $id=>$name)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$data=explode("**",$dataArray[$id]);
							$update_dtls_id=$data[0];
							$prod_start_time=$data[1];
							$lunch_start_time=$data[2];

							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle">
                                <td><b><? echo $name; ?></b></td>
                                <td>
                                	<input class="timepicker text_boxes" type="text" style="width:140px" name="txt_prod_start_time[]" id="txt_prod_start_time<? echo $i; ?>" value="<? echo $prod_start_time; ?>" onblur="fnc_valid_time(this.value,'txt_prod_start_time<? echo $i; ?>');"/>
                                </td>
                                <td>
                                  	<input class="timepicker text_boxes" type="text" style="width:140px" name="txt_lunch_start_time[]" id="txt_lunch_start_time<? echo $i; ?>" value="<? echo $lunch_start_time; ?>" onblur="fnc_valid_time(this.value,'txt_lunch_start_time<? echo $i; ?>');"/>
                                  	<input type="hidden"name="shift_id[]" id="shift_id<? echo $i; ?>" value="<? echo $id; ?>">
                                   	<input type="hidden"name="update_id[]" id="update_id<? echo $i; ?>" value="<? echo $update_dtls_id; ?>">
                                </td>
                            </tr>
						<?
						$i++;
						}
					?>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==27)
	{	//Barcode Generation

		$nameArray=sql_select("select id, smv_source from variable_settings_production where company_name='$company_id' and variable_list=27 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		$barcode_generation_arr=array(1=>"From System",2=>"External Device For Barcode");
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Barcode Generation</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 170, $barcode_generation_arr,'', 1, '---- Select ----', $nameArray[0][csf('smv_source')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if ($type==28)   //Production Update For Reject Qty
	{
		$nameArray= sql_select("select id, company_name, variable_list, cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update,hang_tag_update from variable_settings_production where company_name='$company_id' and variable_list=28 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Production Update Areas</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="left" id="cutting_update">Cutting Update</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_cutting_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('cutting_update')], "","","1,2,3" );
                            ?>
                        </td>
                        <td width="160" align="left" id="iron_update">Iron Output</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_iron_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('iron_update')], "","","1,2,3" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td width="160" align="left" id="printing_emb_production">Printing & Embrd. Prodiction</td>
                        <td width="180">
							<?
								echo create_drop_down("cbo_printing_emb_production",170,$production_update_areas,'',1,'--Select--',$nameArray[0][csf('printing_emb_production')], "","","1,2,3");
                            ?>
                        </td>
                        <td width="130" align="left" id="finishing_update">Finishing Entry</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_finishing_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('finishing_update')], "","","1,2,3");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="left" id="sewing_production">Sewing Production</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_sewing_production", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('sewing_production')], "","","1,2,3");
                            ?>
                        </td>
                        <td width="130" align="left" id="hang_tag_update">Hang Tag Entry</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_hangtag_production", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('hang_tag_update')], "","","1,2,3");
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
	<?
	}
	else if ($type==29)//Piece Rate WQ Limit
	{
		$nameArray= sql_select("select id, company_name, variable_list,piece_rate_wq_limit from variable_settings_production where company_name='$company_id' and variable_list=29 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Piece Rate WQ Limit Areas</legend>
            <table cellspacing="2" cellpadding="0" border="0" align="center" width="45%">
                <tr>
                    <td width="100" align="center">Piece Rate WQ Limit</td>
                    <td width="260">
                        <?
                            echo create_drop_down( "cbo_piece_rate_wo_limit", 250,$piece_rate_wq_limit_arr,'id,company_name', 1, '--- Select ---', $nameArray[0][csf('piece_rate_wq_limit')], "" );
                        ?>
                    </td>
               </tr>
            </table>
            <table cellspacing="0" width="80%" align="center">
                <tr>
                    <td align="center" width="320">&nbsp;</td>
                </tr>
                <tr>
                    <td height="40" valign="bottom" align="center" class="button_container">
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                        <?
                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                        ?>
                    </td>
                </tr>
            </table>

        </fieldset>
	<?
	}
	else if ($type==30)//Piece Rate Sefty%
	{
		$nameArray= sql_select("select id, company_name, variable_list,cut_sefty_parcent,sewing_sefty_parcent,iron_sefty_parcent,finish_sefty_parcent from variable_settings_production where company_name='$company_id' and variable_list=30 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;

		?>
        <fieldset>
        <legend>Piece Rate Sefty Areas</legend>
            <table cellspacing="2" cellpadding="0" border="0" align="center" width="45%">
                <tr>
                    <td width="200" align="center">Cutting Piece Rate Safety %</td>
                    <td><input type="text" class="text_boxes_numeric" id="txt_cut_sefty_parcent" name="txt_cut_sefty_parcent" style="width:45%;" value="<? echo $nameArray[0][csf('cut_sefty_parcent')]; ?>" /></td>
               </tr>

                <tr>
                    <td align="center">Sewing Piece Rate Safety %</td>
                    <td><input type="text" class="text_boxes_numeric" id="txt_sewing_sefty_parcent" name="txt_sewing_sefty_parcent" style="width:45%;" value="<? echo $nameArray[0][csf('sewing_sefty_parcent')]; ?>" /></td>
               </tr>

                <tr>
                    <td align="center">Ironning Piece Rate Safety %</td>
                    <td><input type="text" class="text_boxes_numeric" id="txt_iron_sefty_parcent" name="txt_iron_sefty_parcent" style="width:45%;" value="<? echo $nameArray[0][csf('iron_sefty_parcent')]; ?>" /></td>
               </tr>

                <tr>
                    <td align="center">Finishing Piece Rate Safety %</td>
                    <td><input type="text" class="text_boxes_numeric" id="txt_finish_sefty_parcent" name="txt_finish_sefty_parcent" style="width:45%;" value="<? echo $nameArray[0][csf('finish_sefty_parcent')]; ?>" /></td>
               </tr>

            </table>
            <table cellspacing="0" width="80%" align="center">
                <tr>
                    <td align="center" width="320">&nbsp;</td>
                </tr>
                <tr>
                    <td height="40" valign="bottom" align="center" class="button_container">
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                        <?
                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                        ?>
                    </td>
                </tr>
            </table>

        </fieldset>
	<?

	}
	else if($type==31)
	{//Booking Approval Needed For Knitting Plan

			$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=31 and status_active=1 and is_deleted=0");
 			if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
 			<fieldset>
				<legend>Booking Approval Needed For Knitting Plan</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id="ProductionResourceAllocation">Booking Approval Needed For Knitting Plan </td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_prod_resource", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('auto_update')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons($permission,"fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if ($type==32)
	{
 			$nameArray= sql_select("select id, company_name,variable_list,cut_panel_delevery_hcode,cut_panel_delevery from variable_settings_production where company_name='$company_id' and variable_list=32 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
     	?>
            <fieldset>
                <legend>Cut Panel Delivery Basis</legend>
                <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                    <table cellspacing="0" width="100%" >
                        <tr>
                            <td width="130" align="center" id="cut_panel_basis">Delevery Basis</td>
                            <td width="190">
                                 <?
									$cut_panel_basis=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number");
									echo create_drop_down( "cbo_cut_panel_basis", 170, $cut_panel_basis,'', 1, '---- Select ----', $nameArray[0][csf('cut_panel_delevery')], "",'','' );
								?>
                            </td>
                        </tr>
                    </table>
                </div>
                 <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                    <table cellspacing="0" width="100%" >
                                <tr>
                                    <td align="center" width="320">&nbsp;</td>
                                </tr>
                                <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                        <?
                                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                        ?>
                                    </td>
                                </tr>
                     </table>
                </div>
            </fieldset>

		<?
	}
	else if ($type==33)
	{

			$category_wise_array=array();



			//if(count($nameArray)>0) $is_update=1; else $is_update=0;

			/*foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('page_category_id')]]['auto']=$row[csf('is_control')];
				$category_wise_array[$row[csf('page_category_id')]]['update_id']=$row[csf('id')];
			}*/
		?>
 			<fieldset>
				<legend>Last Process Production Controll</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Category</th>
                            <th>Control</th>
                            <th>Preceding Process</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category", 150, $report_signeture_list,'',1, "---Select Category---", '', "fnc_load_preceding_process($('#cbo_auto_update').val()+'**'+this.value);", "",'28,29,30,31,32,91,103,123,116,117,250,251,252,256,260,268,269' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update", 150, $yes_no,'', 1, '---- Select ----', "", "fnc_load_preceding_process(this.value+'**'+$('#cbo_item_category').val());",'','' );
								?>
							</td>
                            <td id="preceding_td">
                                <?
									echo create_drop_down( "cbo_preceding_item_category", 150, $report_signeture_list,'',1, "---Plan Cut---", '', '', "",'28,29,30,31,32,91,103,116,117,123,250,251,252,260,268,269,288' );
								?>
							</td>
					              <input  type="hidden"name="update_id" id="update_id" value="">


						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        //echo load_submit_buttons( $permission, "fnc_production_variable_settings", 0,0 ,"reset_form('productionVariableSettings','','')",1);
										echo load_submit_buttons( $permission, "fnc_production_variable_settings", 0,0,"reset_form('productionVariableSettings','','')",0);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
                <div style="width:600px; min-height:20px; max-height:250px;" id="list_container" align="center" >

                        <?php
		$sql = "select id, company_name,variable_list,page_category_id,is_control,preceding_page_id from variable_settings_production where company_name='$company_id' and variable_list=33 order by id";
		$report_signeture_list[0] = '';
		$arr = array(0 => $report_signeture_list, 1 => $yes_no, 2 => $report_signeture_list);
		echo create_list_view("list_view", "Category,Control,Preceding Process", "150,150,150,", "500", "250", 0, $sql, "get_php_form_data", "id", "'last_preceding_from_data','requires/production_settings_controller'", 1, "page_category_id,is_control,preceding_page_id", $arr, "page_category_id,is_control,preceding_page_id", "", 'setFilterGrid("list_view",-1);', '0,0,0', '', "");
		?>

                </div>
			</fieldset>
 		<?
	}

	else if ($type==50)
	{

		$category_wise_array=array();
		?>
 			<fieldset>
				<legend>Auto Faric Store Update</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Category</th>
                            <th>Control</th>
                            <th>Preceding Process</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category", 150, $production_type_sweater,'',1, "---Select Category---", '', "fnc_load_preceding_process_sweater($('#cbo_auto_update').val()+'**'+this.value);", "",'' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update", 150, $yes_no,'', 1, '---- Select ----', "", "fnc_load_preceding_process_sweater(this.value+'**'+$('#cbo_item_category').val());",'','' );
								?>
							</td>
                            <td id="preceding_td">
                                <?
									echo create_drop_down( "cbo_preceding_item_category", 150, $production_type_sweater,'',1, "---Plan Cut---", '', '', "",'' );
								?>
							</td>
					              <input  type="hidden"name="update_id" id="update_id" value="">


						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        //echo load_submit_buttons( $permission, "fnc_production_variable_settings", 0,0 ,"reset_form('productionVariableSettings','','')",1);
										echo load_submit_buttons( $permission, "fnc_production_variable_settings", 0,0,"reset_form('productionVariableSettings','','')",0);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
                <div style="width:600px; min-height:20px; max-height:250px;" id="list_container" align="center" >

                        <?php
		$sql = "select id, company_name,variable_list,page_category_id,is_control,preceding_page_id from variable_settings_production where company_name='$company_id' and variable_list=50 order by id";
		$report_signeture_list[0] = '';
		$arr = array(0 => $production_type_sweater, 1 => $yes_no, 2 => $production_type_sweater);
		echo create_list_view("list_view", "Category,Control,Preceding Process", "150,150,150,", "500", "250", 0, $sql, "get_php_form_data", "id", "'last_preceding_from_data_sweater','requires/production_settings_controller'", 1, "page_category_id,is_control,preceding_page_id", $arr, "page_category_id,is_control,preceding_page_id", "", 'setFilterGrid("list_view",-1);', '0,0,0', '', "");
		?>

                </div>
			</fieldset>
 		<?
	}

	else if ($type==34)// for process costing maintain
	{

 			$nameArray= sql_select("select id, company_name,variable_list,process_costing_maintain,auto_update  from variable_settings_production
			where company_name='$company_id' and variable_list=34 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
     		?>
            <fieldset>
                <legend>Process Costing Maintain</legend>
                <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                    <table cellspacing="0" width="100%" >
                        <tr>
                            <td width="130" align="left" id="fabric_roll_level" cbo_fabric_roll_level>Process Costing Maintain</td>
                            <td width="90">
                                 <?
								 echo create_drop_down( "cbo_process_costing", 70, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('process_costing_maintain')], "",'','');
								?>
                            </td>
							<td width="130" align="left" id="rate_mandatory_td" >Rate Mandatory</td>
                            <td width="90">
                                 <?
								 echo create_drop_down( "cbo_rate_mandatory", 70, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('auto_update')], "",'','');
								?>
                            </td>
                        </tr>
						<tr>
                            <td width="130" align="left">&nbsp;</td>
                            <td width="90">
							&nbsp;
                            </td>
							<td width="220" align="left" colspan="2"><small style="color:blue">Rate mandatory variable is for process costing no/select and program wise sales order knitting production yarn lot popup rate</small></td>
                        </tr>
                    </table>
                </div>
                 <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                    <table cellspacing="0" width="100%" >
                                <tr>
                                    <td align="center" width="320">&nbsp;</td>
                                </tr>
                                <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                        <?
                                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                        ?>
                                    </td>
                                </tr>
                     </table>
                </div>
            </fieldset>
			<?
	}
	else if ($type==35)
	{//Production Auto Faric Store Update

			$category_wise_array=array();

			$nameArray= sql_select("select id, distribute_qnty, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list=35 and status_active=1 order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
				$category_wise_array[$row[csf('item_category_id')]]['excess_val']=$row[csf('distribute_qnty')];
			}

			$status= $category_wise_array[13]['auto'];
			$disable_st="style='display:none;'";
			if($status==1)$disable_st="";
		?>
 			<fieldset>
				<legend>Fabric Production Control</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Item Category</th>
                            <th>Production Control</th>
                            <th id="excess_title" style="display: block;" width="150">Over Percentage %</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '', '13', "",'','13' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_1", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[13]['auto'], "fnc_enable_disable_excess($type,this.value,1)",'','' );
								?>
							</td>
							<td>
								<input type="text" class="text_boxes_numeric" id="txtExcessPercent_1" value="<? echo $category_wise_array[13]['excess_val'];?>" <? echo $disable_st;?> style="width: 140px;">
							</td>
                            <input  type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[13]['update_id']; ?>">
						</tr>
                        <tr align="center" style="display:none">
							<td>
                                <?
									$dyeing_arr=array(100=>"Dyeing");
									echo create_drop_down( "cbo_item_category_2", 150, $dyeing_arr,'', 0, '','100', "",'','100' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_2", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[100]['auto'], "",'','' );
								?>
							</td>
                            <input type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[100]['update_id']; ?>">
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	else if ($type==36 || $type == 45)
	{//Production Auto Faric Store Update
		$permission=$_SESSION['page_permission'];
		$category_wise_array=array();
		$nameArray= sql_select("select id, company_name, variable_list, fabric_grade, get_upto_first, get_upvalue_first, get_upto_second, get_upvalue_second from variable_settings_production where company_name='$company_id' and variable_list =$type  order by id");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
		<fieldset>
			<legend>Fabric Production Control</legend>
			<div style="width:600px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" class="rpt_table" border="1" rules="all" id="table_body">
					<thead>
						<th>Fabric Grade</th>
						<th style="display:none">Get Upto</th>
						<th>Lower Value</th>
						<th style="display:none">Get Upto</th>
						<th>Upper Value</th>
						<th></th>
					</thead>
					<tbody>
                    <?
					$i=1;
					if(count($nameArray)>0)
					{
						foreach($nameArray as $row)
						{
							?>
							<tr align="center" id="tr_<? echo $i; ?>">
								<td><input type="text" class="text_boxes" id="fabricGrade_<? echo $i; ?>" name="fabricGrade_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("fabric_grade")]; ?>" /></td>
								<td  style="display:none">
									 <?
										echo create_drop_down( "cboGetUptoFirst_".$i, 80, $get_upto,"", 1, "- All -", $row[csf("get_upto_first")], "",0 );
									?>
								</td>
								<td><input type="text" class="text_boxes_numeric" id="valueFirst_<? echo $i; ?>" name="valueFirst_<? echo $i; ?>" style="width:70px;" value="<? echo $row[csf("get_upvalue_first")]; ?>" /></td>
								<td  style="display:none">
									 <?
										echo create_drop_down( "cboGetUptoSecond_".$i, 80, $get_upto,"", 1, "- All -", $row[csf("get_upto_second")], "",0 );
									?>
								</td>
								<td><input type="text" class="text_boxes_numeric" id="valueSecond_<? echo $i; ?>" name="valueSecond_<? echo $i; ?>" style="width:70px;" value="<? echo $row[csf("get_upvalue_second")]; ?>" /></td>
								<td>
								<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>);" />
								<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
							$i++;
						}
					}
					else
					{
						?>
						<tr align="center" id="tr_1">
							<td><input type="text" class="text_boxes" id="fabricGrade_1" name="fabricGrade_1" style="width:100px;" value="<? ?>" /></td>
							<td   style="display:none">
								 <?
									echo create_drop_down( "cboGetUptoFirst_1", 80, $get_upto,"", 1, "- All -", 0, "",0 );
								?>
							</td>
							<td><input type="text" class="text_boxes_numeric" id="valueFirst_1" name="valueFirst_1" style="width:70px;" value="<? ?>" /></td>
							<td   style="display:none">
								 <?
									echo create_drop_down( "cboGetUptoSecond_1", 80, $get_upto,"", 1, "- All -", 0, "",0 );
								?>
							</td>
							<td><input type="text" class="text_boxes_numeric" id="valueSecond_1" name="valueSecond_1" style="width:70px;" value="<? ?>" /></td>
							<td>
							<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
							<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1);" />
							</td>
						</tr>
                        <?
					}
					?>
					</tbody>

				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
						<tr>
							<td align="center" width="320">&nbsp;</td>
						</tr>
						<tr>
						   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
								<?
									echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
								?>
							</td>
						</tr>
				 </table>
			</div>
		</fieldset>
 		<?
	}
	else if($type==37)
	{//Bundle No Creation

		$nameArray=sql_select("select id, smv_source from variable_settings_production where company_name='$company_id' and variable_list=37 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		$bundle_no_generation_arr=array(1=>"Cutting No. Wise",2=>"Job No. Wise",3=>"Order No. Wise");
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="bundle_no">Bundle No. Creation</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 170, $bundle_no_generation_arr,'', 1, '---- Select ----', $nameArray[0][csf('smv_source')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 	<?
		exit();
	}
	else if($type==38) // cut and lay roll wise batch no
	{
		$nameArray=sql_select("SELECT id, is_control,is_locked from variable_settings_production where company_name='$company_id' and variable_list=38 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="bundle_no"> Batch No Mandatory</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('is_control')], "",'','' );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="left" id="bundle_no"> Batch Selection in Plies Popup</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_selection", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('is_locked')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==39)
	{//RMG. No Creation

		$nameArray=sql_select("select id, smv_source from variable_settings_production where company_name='$company_id' and variable_list=39 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		$rmg_no_generation_arr=array(1=>"Size Wise",2=>"Cutting No. Wise",3=>"Job No. Wise",4=>"Order No. Wise",5=>"Ratio/Pattern Wise",6=>"Up To 999(Job Wise)",7=>"Up To 9999(Job Wise)",8=>"Size Wise Multi Color");
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="bundle_no">RMG No. Creation</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 170, $rmg_no_generation_arr,'', 1, '---- Select ----', $nameArray[0][csf('smv_source')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 	<?
		exit();
	}
	else if($type==40)
	{//RMG. No Creation

		//$nameArray=sql_select("select id, service_process_id,is_serveice_rate_lib from variable_settings_production where company_name='$company_id' and variable_list=40 and status_active=1 and is_deleted=0");
		//if(count($nameArray)>0) $is_update=1; else $is_update=0;

		//$rmg_no_generation_arr=array(1=>"Size Wise",2=>"Cutting No. Wise",3=>"Job No. Wise",4=>"Order No. Wise");
		?>
        <fieldset>
            <legend>Service Rate Source</legend>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td width="120" align="left" id="bundle_no">Service Type</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_service_process", 120, $production_process,'', 1, '--- Select ---', $nameArray[0][csf('service_process_id')], "",'','' );
                            ?>
                        </td>
                        <td width="120" align="left" id="bundle_no">Status</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_service_rate", 100, $yes_no,'', 1, '-- Select --', $nameArray[0][csf('is_serveice_rate_lib')], "",'','' );
                            ?>
                        </td>

                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
            <div id="list_view_con" style="margin-top:15px">
				<?

			  $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
			  $arr=array (0=>$company_name_arr,1=>$production_process,2=>$yes_no);
			  echo  create_list_view ( "list_view", "Company Name,Service Type,Status", "150,150,150","470","220",0, "select id,company_name,service_process_id,is_serveice_rate_lib from  variable_settings_production where company_name='$company_id' and variable_list=40 and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,service_process_id,is_serveice_rate_lib", $arr , "company_name,service_process_id,is_serveice_rate_lib", "../variable/requires/production_settings_controller",'setFilterGrid("list_view",-1);' );
				  ?>
				</div>
        </fieldset>
 	<?
		exit();
	}
	else if ($type==41)
	{//Working Company Mandatory

			$nameArray= sql_select("select id, company_name,variable_list,working_company_mandatory from variable_settings_production where company_name='$company_id' and variable_list=41 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Working Company Mandatory</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="fabric_machine_level">Working Company</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_working_company_mandatory", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('working_company_mandatory')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

	<?
	}

	else if($type==42)//Qty. Source for Poly Entry
	{
		$qty_source_poly=array(1=>"Sewing Out",2=>"Iron");
		$nameArray= sql_select("select id, qty_source_poly from variable_settings_production where company_name='$company_id' and variable_list=42 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend>Qty. Source for Poly Entry</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id="qtySource">Qty. Source for Poly Entry</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_qtySource", 170, $qty_source_poly,'', 1, '---- Select ----', $nameArray[0][csf('qty_source_poly')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==43)//Qty. Source For Packing and Finishing
	{
		$qty_source_packing=array(1=>"Iron Entry",2=>"Poly Entry");
		$nameArray= sql_select("select id, qty_source_packing from variable_settings_production where company_name='$company_id' and variable_list=43 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend>Qty. Source for Packing and Finishing</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id="qtySource">Qty. Source for Packing and Finishing</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_qtySource", 170, $qty_source_packing,'', 1, '---- Select ----', $nameArray[0][csf('qty_source_packing')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==44)//Fabric Source For Batch
	{
		$qty_source_packing=array(1=>"Iron Entry",2=>"Poly Entry");
		$nameArray= sql_select("select id, dyeing_fin_bill from variable_settings_production where company_name='$company_id' and variable_list=44 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
            <legend>Fabric Source For Batch</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="left" id="bill_on">Source</td>
                        <td width="190">
							<?php
							echo create_drop_down("cbo_bill_on", 170, array(1 => "Receive", 2 => "Production", 3 => "Issue"), '', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "", '', '');
							?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
							<?php
							echo load_submit_buttons($permission, "fnc_production_variable_settings", $is_update, 0, "reset_form('productionVariableSettings','','')", 1);
							?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
		exit();
	}
	else if($type==46)//Finish Fabric rate source
	{
		//$qty_source_packing=array(1=>"Iron Entry",2=>"Poly Entry");
		$nameArray= sql_select("select id, process_wise_rate_source from variable_settings_production where company_name='$company_id' and variable_list=46 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
            <legend>Finish Fabric Rate Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="left" id="rate_source">Rate Source</td>
                        <td width="190">
							<?php
							echo create_drop_down("cbo_rate_source", 170, array(1 => "BOM", 2 => "Work Order"), '', 1, '---- Select ----', $nameArray[0][csf('process_wise_rate_source')], "", '', '');
							?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
							<?php
							echo load_submit_buttons($permission, "fnc_production_variable_settings", $is_update, 0, "reset_form('productionVariableSettings','','')", 1);
							?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
		exit();
	}
	else if ($type==47)
	{//Production Auto Production quantity update by QC

			$category_wise_array=array();
			$nameArray= sql_select("select id, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list= '$type' order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
			}
		?>
 			<fieldset>
				<legend>Auto Production quantity update by QC</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Item Category</th>
                            <th>Auto Update</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '', '2', "",'','2' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_1", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[2]['auto'], "",'','' );
								?>
							</td>
                            <input  type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[2]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                 <?
									echo create_drop_down( "cbo_item_category_2", 150, $item_category,'', 0, '','13', "",'','13' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_2", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[13]['auto'], "",'','' );
								?>
							</td>
                            <input type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[13]['update_id']; ?>">
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings",$is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	else if ($type==48)
	{//Mandatory QC For Delivery

			$category_wise_array=array();
			$nameArray= sql_select("select id, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list= '$type' order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
			}
		?>
 			<fieldset>
				<legend>Mandatory QC For Delivery</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Item Category</th>
                            <th>Auto Update</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '', '2', "",'','2' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_1", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[2]['auto'], "",'','' );
								?>
							</td>
                            <input  type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[2]['update_id']; ?>">
						</tr>
                        <tr align="center">
							<td>
                                 <?
									echo create_drop_down( "cbo_item_category_2", 150, $item_category,'', 0, '','13', "",'','13' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_auto_update_2", 150, $yes_no,'', 1, '---- Select ----',$category_wise_array[13]['auto'], "",'','' );
								?>
							</td>
                            <input type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[13]['update_id']; ?>">
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings",$is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	/* else if ($type==48)//Mandatory QC For Delivery
	{

			$nameArray= sql_select("select id, company_name,variable_list,qc_mandatory  from variable_settings_production  where company_name='$company_id' and variable_list=48 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;

		?>

 			<fieldset>
				<legend>Mandatory QC For Delivery</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td width="130" align="left" id="batch_maintained">QC Mandatory</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_qc_mandatory_for_delivery", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('qc_mandatory')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	} */

	else if ($type==49)//Piece Rate Sefty%
	{
		$nameArray= sql_select("select id, company_name, variable_list,max_roll_weight from variable_settings_production where company_name='$company_id' and variable_list=49 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;

		?>
        <fieldset>
        <legend>Piece Rate Sefty Areas</legend>
            <table cellspacing="2" cellpadding="0" border="0" align="center" width="45%">
                <tr>
                    <td width="200" align="center">Maximum Roll Weight : </td>
                    <td><input type="text" class="text_boxes_numeric" id="txt_max_roll_weight" name="txt_max_roll_weight" style="width:45%;" value="<? echo $nameArray[0][csf('max_roll_weight')]; ?>" /></td>
               </tr>

            </table>
            <table cellspacing="0" width="80%" align="center">
                <tr>
                    <td align="center" width="320">&nbsp;</td>
                </tr>
                <tr>
                    <td height="40" valign="bottom" align="center" class="button_container">
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                        <?
                            echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                        ?>
                    </td>
                </tr>
            </table>

        </fieldset>
	<?

	}
	else if ($type==51)
	{ 

			$category_wise_array=array();
			$nameArray= sql_select("SELECT id, distribute_qnty, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list= 51 order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
				$category_wise_array[$row[csf('item_category_id')]]['excess_val']=$row[csf('distribute_qnty')];
			}
			$item_category_list=array(1=>"Knitting",2=>"Finish Fabric",3=>"AOP");
			?>

			<fieldset>
				<legend>Auto Faric Store Update</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table" id="fabric_details_tbl">
						<thead>
							<tr>
								<th>Item Category </th>
								<th>Over Production</th>
								<th id="excess_title" style="display: block;">Over Percentage %</th>
							</tr>
						</thead>
						<body>
							<?

							foreach($item_category_list as $key=>$val)
							{
								$status=$category_wise_array[$key]['auto'];
								$excess_val=$category_wise_array[$key]['excess_val'];
								$disable_st="style='display:none;'";
								if($status==1)$disable_st="";

								if($key==2) {$yes_no = array(1 => "Yes", 2 => "No", 3=>"Unlimited");} else {$yes_no = array(1 => "Yes", 2 => "No");}
								?>
								<tr align="center">
									<td>
										<?
										echo create_drop_down( "cbo_item_category_$key", 150, $item_category_list,'', 0, '', "$key", "",'',"$key" );
										?>
									</td>
									<td>
										<?
										echo create_drop_down( "cbo_auto_update_$key", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[$key]['auto'], "fnc_enable_disable_excess($type,this.value,$key)",'','' );
										?>
									</td>
									<td>
										<input type="text" class="text_boxes_numeric" id="txtExcessPercent_<? echo $key;?>" value="<? echo $excess_val;?>" <? echo $disable_st;?> >
									</td>
									<input  type="hidden"  id="update_id_<? echo $key;?>" value="<? echo $category_wise_array[$key]['update_id']; ?>">
								</tr>
								<?

							}
							?>



						</body>


					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="center" width="320">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" height="40" valign="bottom" align="center" class="button_container">
								<?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings",$is_update,0 ,"reset_form('productionVariableSettings','','')",1);
								?>
							</td>
						</tr>
					</table>
				</div>
			</fieldset>
 		<?
	}

	 
	else if($type==52)
	{
		//Textile business concept

		$nameArray=sql_select("select id, textile_business_concept from variable_settings_production where company_name='$company_id' and variable_list=52 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Textile business concept</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="textile_business_con">Business Concept</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_textile_business", 170, $textile_business_concept,'', 1, '---- Select ----', $nameArray[0][csf('textile_business_concept')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 	<?
		exit();
	}
	else if($type==53) // Sample Delivery Source
    {
        $nameArray=sql_select( "select qty_source_sample, id from  variable_settings_production where company_name='$company_id' and variable_list=53 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>Sample Delivery Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left">Source in Sample Delivery Entry</td>
                        <td width="190">
                        <?
                        echo create_drop_down( "cbo_sample_delivery_source", 170, $sample_delivery_source,'', 1, '---- Select ----', $nameArray[0][csf('qty_source_sample')], "" );
                        ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                             ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
	else if($type==54)
	{  

		$nameArray=sql_select("select id, is_control from variable_settings_production where company_name='$company_id' and variable_list=54 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		//echo "select id, is_control from variable_settings_production where company_name='$company_id' and variable_list=54 and status_active=1 and is_deleted=0";
		?>
        <fieldset>
            <legend>Stock Check</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Stock Qty</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_stock_qty", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('is_control')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 	<?
		exit();
	}
	else if($type==55)
	{
		//Finish Fabric Production Validation With

		$nameArray=sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=55 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Validation With</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Validation With</td>
                        <td>
                            <?
                             	$validation_arr = array(1=>"Budget",2=>"Batch");
                                echo create_drop_down( "cbo_source", 170, $validation_arr,'', 1, '---- Select ----', $nameArray[0][csf('auto_update')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==56)
	{
		//Finish Fabric Production Validation With

		$nameArray=sql_select("select id, distribute_qnty from variable_settings_production where company_name='$company_id' and variable_list=56 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Validation With</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Textile Store</td>
                        <td>
                            <?
							echo create_drop_down( "cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company_id' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",$nameArray[0][csf('distribute_qnty')],"");
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==57)
	{
		//Finish Fabric Production Dyeing Charge Source

		$nameArray=sql_select("select id, distribute_qnty from variable_settings_production where company_name='$company_id' and variable_list=57 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Validation With</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Dyeing Charge Source</td>
                        <td>
                            <?
                            //to sync with $type(56) dropdown id is here cbo_store_name
                            echo create_drop_down( "cbo_store_name", 170, $dyeing_charge_source_arr,'', 1, '---- Select ----', $nameArray[0][csf('distribute_qnty')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if($type==58)//Projected PO By Garments Production
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=58 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend>Production Resource Allocation</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id=">projected_po_production_entry">Projected PO By Garments Production</td>
							<td width="190">
                                 <?
									echo create_drop_down( "cbo_production_entry", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==59)// Recipe Maintain Level
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=59 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend> Recipe Maintain Level</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id=">projected_po_production_entry"> Recipe Maintain Level</td>
							<td width="190">
                                 <?
								 $recipe_maintain_arr=array(1=>'Central Recipe',2=>'Dyeing and Finishing Separate Recipe');
									echo create_drop_down( "cbo_production_entry", 170, $recipe_maintain_arr,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==60)// Actual Production Resource Entry Style Ref.
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=60 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend> Actual Production Resource Entry Style Ref.</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id=">projected_po_production_entry"> Actual Production Resource Entry</td>
							<td width="190">
                                 <?
								 $recipe_maintain_arr=array(1=>'Order Level',2=>'Color and Size Level');
									echo create_drop_down( "cbo_production_entry", 170, $recipe_maintain_arr,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==61)//Booking Qnty Source of Fabric Sales Order/FSO
	{
		//$qty_source_packing=array(1=>"Iron Entry",2=>"Poly Entry");
		$nameArray= sql_select("select id, process_wise_rate_source from variable_settings_production where company_name='$company_id' and variable_list=61 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
            <legend>Finish Fabric Rate Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="left" id="rate_source">Based On</td>
                        <td width="190">
							<?php
							echo create_drop_down("cbo_rate_source", 170, array(1 => "Finish Qnty", 2 => "Grey Qnty"), '', 1, '---- Select ----', $nameArray[0][csf('process_wise_rate_source')], "", '', '');
							?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
							<?php
							echo load_submit_buttons($permission, "fnc_production_variable_settings", $is_update, 0, "reset_form('productionVariableSettings','','')", 1);
							?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
		exit();
	}
	else if ($type==62)//Hide QC Result from Knitting Production
	{
		$nameArray= sql_select("select id, company_name, variable_list, hide_qc_result from variable_settings_production where company_name='$company_id' and variable_list=62 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Hide QC Result from Knitting Production</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="hide_qc_result">Hide QC Result from Knitting Production</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_hide_qc_result", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('hide_qc_result')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                       <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
                                echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                 </table>
			</div>
		</fieldset>

		<?
	}

	else if ($type==63) // Dyeing Production Control based on chemical issue
	{
		$nameArray= sql_select("select id, company_name, variable_list, chemical_issue from variable_settings_production where company_name='$company_id' and variable_list=63 and is_deleted=0 and status_active=1 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Dyeing Production Control Based on Chemical Issue</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="chemical_issue">Dyeing Production Control Based on Chemical Issue</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_chemical_issue", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('chemical_issue')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                       <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
                                echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                 </table>
			</div>
		</fieldset>

		<?
	}
	else if ($type==64) // Dyeing Production Control based on chemical issue
	{
		$nameArray= sql_select("select id, company_name, variable_list, chemical_issue from variable_settings_production where company_name='$company_id' and variable_list=64 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Service Booking Mandatory For Outbound Subcontact Knitting</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="chemical_issue">Service Booking Mandatory For Outbound Subcontact Knitting</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_chemical_issue", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('chemical_issue')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                    <tr>
                        <td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                       <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
                                echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                 </table>
			</div>
		</fieldset>

		<?
	}
	else if($type==65)// Linking Input Production Source[Bundle]
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=65 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend> Linking Input Production Source[Bundle]</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left" id=">projected_po_production_entry"> Source</td>
							<td width="190">
                                 <?
								 $linking_source_arr=array(1=>'First Inspection',2=>'Bundle Issue To Linking',3=>'Bundle Receive  In Linking');
									echo create_drop_down( "cbo_production_entry", 170, $linking_source_arr,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==66)// Textile Sales Maintain
	{
		$nameArray= sql_select("select id, production_entry, process_loss_editable from variable_settings_production where company_name='$company_id' and variable_list=66 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		if($nameArray[0][csf('production_entry')]==2)$is_selected=0;else $is_selected=1;
		?>
 			<fieldset>
				<legend>Textile Sales Maintain</legend>
				<div style="width:550px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left"> Source</td>
							<td width="190">
                                 <?
								 echo create_drop_down( "cbo_production_entry", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "change_process_loss_editable_option(this.value);",'','',"","","1" );
								?>
							</td>
							<td align="left"> FSO Process Loss Editable</td>
							<td width="190">
                                 <?
								 echo create_drop_down( "cbo_process_loss_editable", 170, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf('process_loss_editable')], "",$is_selected,'',"","","" );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	else if($type==67)// Tube/Ref. No Setting
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=67 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
 			<fieldset>
				<legend>Tube/Ref. No Setting</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr>
							<td align="left">Tube/Ref. No Maintain</td>
							<td width="190">
                                <?
								 	echo create_drop_down( "cbo_production_entry", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','',"","","" );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
		exit();
	}
	
	else if($type==68)
	{
		//Production Resource Allocation

		$nameArray=sql_select("select id, SEWING_PRODUCTION from variable_settings_production where company_name='$company_id' and variable_list=68 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Replace field disable:</td>
                        <td>
                             <?
                                echo create_drop_down( "cbo_replace_field_disable", 170, $yes_no,'', 1, '-- Select --', $nameArray[0][csf('SEWING_PRODUCTION')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
        <span style="color:#FF0000;">Note: Alter, Spot value will not be calculated with QC Qty and Replace field will remain disable only for Apps.</span>
 		<?
		exit();
	}
	else if ($type==69)
	{
		//Woven Cutting Requisition Qty (Editable)

			$nameArray= sql_select("SELECT id, fabric_machine_level from variable_settings_production where company_name='$company_id' and variable_list=69 order by id");
 			if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Fabric in Machine Level</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="fabric_machine_level">Woven Cutting Req. Qty</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_fabric_machine_level", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('fabric_machine_level')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

		<?
	}
	else if ($type==70)
	{
		$nameArray= sql_select("select id, production_entry,auto_print,apply_for from variable_settings_production where company_name='$company_id' and variable_list=70 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		if($nameArray[0][csf('apply_for')]>0)$apply_for=1;else $apply_for=0;

 		?>

		<fieldset>			
			<legend>Auto Fabric Store Update</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Auto Barcode Generate (knitting produciton v2 auto save)</th>
                            <th>Auto Barcode Generate (knitting produciton v2 auto barcode print)</th>
                            <th>For Subcontract</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
									echo create_drop_down( "cbo_production_entry", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_production_entry_2", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('auto_print')], "",'','' );
								?>
							</td>
							<td>

								<?
									if($apply_for>0)
									{
										?>
											<input type="checkbox" id="apply_for_id" name="apply_for_id" value="<? echo $apply_for; ?>"  checked onclick="chk_fnc();">
										<?

									}
									else
									{
										?>
											<input type="checkbox" id="apply_for_id" name="apply_for_id" value="0" onclick="chk_fnc();">
										<?
									}
								?>
							</td>
							
						</tr>
                       
					</table>
				</div>



			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

		<?
	}
	else if ($type==71)
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=71 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Actual Production Resource Entry</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="">Line & Shift Wise Production Start Time Source</td>
						<td width="190">
 							<?
 							$source_arr = array(1=> 'Come From Library',2=> 'Come from Previously Save Data');
								echo create_drop_down( "cbo_production_entry", 170, $source_arr,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

		<?
	}
	else if ($type==72)
	{
		$nameArray= sql_select("select id, production_entry from variable_settings_production where company_name='$company_id' and variable_list=72 and status_active=1 and is_deleted=0");
 		if(count($nameArray)>0)$is_update=1;else $is_update=0;
 		?>

		<fieldset>
			<legend>Fabric in Machine Level</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr>
						<td width="130" align="left" id="">Style wise Quantity Popup</td>
						<td width="190">
 							<?
								echo create_drop_down( "cbo_production_entry", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "",'','' );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                 </table>
			</div>

		</fieldset>

		<?
	}
	else if($type==73)// Rate Source
	{
		$nameArray=sql_select("select id, smv_source from variable_settings_production where company_name='$company_id' and variable_list=73 and status_active=1 and is_deleted=0");
		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		$rate_source=array(1=>"Manual",2=>"From Budget");
		?>
        <fieldset>
            <legend>Batch No</legend>
            <div style="width:300px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="120" align="left" id="batch_no">Rate Source 
							<!-- <span class="tooltip-wrapper tooltip t-top" data-tooltip-text="For Garments Service Work Order Entry Page">
								<i class="fa fa-info-circle"></i>
							</span> -->
						</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 170, $rate_source,'', 1, '---- Select ----', $nameArray[0][csf('smv_source')], "",'','' );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:350px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                        <tr>
                            <td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                <?
                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                ?>
                            </td>
                        </tr>
                 </table>
            </div>
        </fieldset>
 		<?
		exit();
	}
	else if ($type==74)  // new work
	{//Knitting and Dyeing Rate Update

			$category_wise_array=array();
			$nameArray= sql_select("select id, company_name,variable_list,grey_recvd_basis,grey_rate_come_from, finish_recvd_basis,finish_rate_come_from from variable_settings_production where company_name='$company_id' and variable_list= '$type' and status_active=1 and is_deleted=0 order by id");

			if(count($nameArray)>0) $is_update=1; else $is_update=0;

			foreach($nameArray as $row)
			{
				$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
				$category_wise_array[$row[csf('item_category_id')]]['excess_val']=$row[csf('distribute_qnty')];
			}
			if($category_wise_array[2]['auto']==1){
				$disable_st="style='display:none;'";
			}else{
				$disable_st="";
			}
		?>
 			<fieldset>
				<legend>Knitting and Dyeing Rate Update</legend>
				<div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" class="rpt_table">
                    	<thead>
                        	<th>Grey Rcvd Basis</th>
                            <th>Rate Come From</th>
                            <th>Fini. Rcvd Basis</th>
                            <th>Rate Come From</th>
                        </thead>
						<tr align="center">
							<td>
                                <?
                                	$recivedBasisArr=array(1=>"Booking (Prod)",2=>"Service W/O");
                                	$rateComeFromArr=array(1=>"Pre Costing",2=>"Service W/O");
									echo create_drop_down( "cbo_item_category_A1", 150, $recivedBasisArr,'', 1, '---- Select ----', $nameArray[0][csf('grey_recvd_basis')], "",'','' );
								?>
							</td>
							<td>
                                 <?
									echo create_drop_down( "cbo_item_category_A2", 150, $rateComeFromArr,'', 1, '---- Select ----', $nameArray[0][csf('grey_rate_come_from')], "",'','' );									
								?>
							</td>
							<td>
								<?
									echo create_drop_down( "cbo_item_category_B1", 150, $recivedBasisArr,'', 1, '---- Select ----', $nameArray[0][csf('finish_recvd_basis')], "",'','' );
								?>
							</td>
							<td>
								<?
									echo create_drop_down( "cbo_item_category_B2", 150, $rateComeFromArr,'', 1, '---- Select ----',$nameArray[0][csf('finish_rate_come_from')], "",'','','' );
								?>
							</td>
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
						</tr>
					</table>
				</div>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
                            <tr>
                                <td align="center" width="320">&nbsp;</td>
                            </tr>
                            <tr>
                               <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                    <?
                                        echo load_submit_buttons( $permission, "fnc_production_variable_settings",$is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                                    ?>
                                </td>
                            </tr>
                	 </table>
				</div>
			</fieldset>
 		<?
	}
	else if ($type==75)// Allow Finish Fab. Rcv. Against Prod. Booking
	{
		$nameArray= sql_select("select id, company_name,variable_list,allow_fin_fab_rcv  from variable_settings_production
		where company_name='$company_id' and variable_list=75 order by id");
			if(count($nameArray)>0)$is_update=1;else $is_update=0;
			?>
	    <fieldset>
	        <legend>Allow Finish Fab. Rcv. Against Prod. Booking</legend>
	        <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
	            <table cellspacing="0" width="100%" >
	                <tr>
	                    <td width="130" align="left" id="fabric_allow_fabRecv">Allow Finish Fab. Rcv. Against Prod. Booking</td>
	                    <td width="190">
	                         <?
							 echo create_drop_down( "cbo_fabric_allow_fabRecv", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('allow_fin_fab_rcv')], "",'','');
							?>
	                    </td>
	                </tr>
	            </table>
	        </div>
	         <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
	            <table cellspacing="0" width="100%" >
	                        <tr>
	                            <td align="center" width="320">&nbsp;</td>
	                        </tr>
	                        <tr>
	                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
	                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
	                                <?
	                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
	                                ?>
	                            </td>
	                        </tr>
	             </table>
	        </div>
	    </fieldset>
		<?
	}
	else if ($type==76)// WIP valuation for Accounts
	{
		$nameArray= sql_select("select id, company_name,variable_list,allow_fin_fab_rcv  from variable_settings_production
		where company_name='$company_id' and variable_list=76 order by id");
			if(count($nameArray)>0)$is_update=1;else $is_update=0;
			?>
	    <fieldset>
	        <legend>WIP valuation for Accounts</legend>
	        <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
	            <table cellspacing="0" width="100%" >
	                <tr>
	                    <td width="130" align="left" id="fabric_allow_fabRecv">WIP valuation for Accounts</td>
	                    <td width="190">
	                         <?
							 echo create_drop_down( "cbo_fabric_allow_fabRecv", 170, $yes_no,'', 0, '---- Select ----',$nameArray[0][csf('ALLOW_FIN_FAB_RCV')], "",'','');
							?>
	                    </td>
	                </tr>
	            </table>
	        </div>
	         <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
	            <table cellspacing="0" width="100%" >
	                        <tr>
	                            <td align="center" width="320">&nbsp;</td>
	                        </tr>
	                        <tr>
	                           <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
	                                <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
	                                <?
	                                    echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
	                                ?>
	                            </td>
	                        </tr>
	             </table>
	        </div>
	    </fieldset>
		<?
	}
	

	exit();
}//end change on data condition


if ($action=="last_preceding_from_data")
{
	$nameArray=sql_select("select id, company_name,variable_list,page_category_id,is_control,preceding_page_id from variable_settings_production where id=$data and variable_list=33 order by id" );
	foreach ($nameArray as $inf)
	{
		$data= $inf[csf("is_control")]."**".$inf[csf("page_category_id")];
		echo "load_drop_down( 'requires/production_settings_controller','$data' , 'load_drop_down_preceding_process', 'preceding_td' );\n";
		echo "$('#cbo_item_category').val('".($inf[csf("page_category_id")])."');\n";
		echo "$('#cbo_auto_update').val('".($inf[csf("is_control")])."');\n";
		echo "$('#cbo_preceding_item_category').val('".($inf[csf("preceding_page_id")])."');\n";
		echo "$('#update_id').val('".($inf[csf("id")])."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_production_variable_settings',1);\n";

	}
	exit();
}
if ($action=="last_preceding_from_data_sweater")
{
	$nameArray=sql_select("select id, company_name,variable_list,page_category_id,is_control,preceding_page_id from variable_settings_production where id=$data and variable_list=50 order by id" );
	foreach ($nameArray as $inf)
	{
		$data= $inf[csf("is_control")]."**".$inf[csf("page_category_id")];
		echo "load_drop_down( 'requires/production_settings_controller','$data' , 'load_drop_down_preceding_process_sweater', 'preceding_td' );\n";
		echo "$('#cbo_item_category').val('".($inf[csf("page_category_id")])."');\n";
		echo "$('#cbo_auto_update').val('".($inf[csf("is_control")])."');\n";
		echo "$('#cbo_preceding_item_category').val('".($inf[csf("preceding_page_id")])."');\n";
		echo "$('#update_id').val('".($inf[csf("id")])."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_production_variable_settings',1);\n";

	}
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select id,variable_list,company_name,service_process_id,is_serveice_rate_lib from  variable_settings_production where  variable_list=40 and status_active=1 and is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name_production').value = '".($inf[csf("company_name")])."';\n";
		echo "document.getElementById('cbo_variable_list_production').value = '".($inf[csf("variable_list")])."';\n";
		echo "document.getElementById('cbo_service_process').value = '".($inf[csf("service_process_id")])."';\n";
		echo "document.getElementById('cbo_service_rate').value = '".($inf[csf("is_serveice_rate_lib")])."';\n";
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_production_variable_settings',1);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			//echo "10**".$cbo_variable_list_production;die;
		if(str_replace("'","",$cbo_variable_list_production)==2)
		{
			sql_select("delete from variable_prod_excess_slab where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production");

			$slab_id = return_next_id( "id", "variable_prod_excess_slab", 1);
			$field_array="id, company_name,variable_list, slab_rang_start, slab_rang_end, excess_percent, inserted_by,insert_date,status_active";

			for($i=1;$i<=$counter;$i++)
			{
				$txt_slab_rang_start = 'txt_slab_rang_start'.$i;
				$txt_slab_rang_end = 'txt_slab_rang_end'.$i;
				$txt_excess_percent = 'txt_excess_percent'.$i;
				if($i==1)
					$data_array.="(".$slab_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$txt_slab_rang_start.",".$$txt_slab_rang_end.",".$$txt_excess_percent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				else
					$data_array.=",(".$slab_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$txt_slab_rang_start.",".$$txt_slab_rang_end.",".$$txt_excess_percent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$slab_id++;

			}
			 $rID=sql_insert("variable_prod_excess_slab",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==36 || str_replace("'","",$cbo_variable_list_production)==45)
		{
			sql_select("delete from variable_settings_production where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production");
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name,variable_list, fabric_grade, get_upto_first, get_upvalue_first, get_upto_second, get_upvalue_second, inserted_by, insert_date, status_active";
			$data_array="";
			for($i=1;$i<=$row_num;$i++)
			{
				$fabricGrade = 'fabricGrade_'.$i;
				$cboGetUptoFirst = 'cboGetUptoFirst_'.$i;
				$valueFirst = 'valueFirst_'.$i;
				$cboGetUptoSecond = 'cboGetUptoSecond_'.$i;
				$valueSecond = 'valueSecond_'.$i;
				if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$fabricGrade.",".$$cboGetUptoFirst.",".$$valueFirst.",".$$cboGetUptoSecond.",".$$valueSecond.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;

			}
			 $rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==51)
		{

			 
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name,variable_list,  item_category_id, auto_update,distribute_qnty, inserted_by, insert_date, status_active"; 
			$data_array="";
			for($i=1;$i<=$total_row;$i++)
			{
				$item_category_id = 'cbo_item_category_'.$i;
				$cbo_auto_update = 'cbo_auto_update_'.$i;
				$txtExcessPercent = 'txtExcessPercent_'.$i;
				 
				if($data_array) $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$item_category_id.",".$$cbo_auto_update.",".$$txtExcessPercent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;

			}
			//echo "10**15** insert into variable_settings_production($field_array) values$data_array";die;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==15 )
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, item_category_id, auto_update, distribute_qnty, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_1.",".$cbo_auto_update_1.",".$cbo_receive_basis_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_2.",".$cbo_auto_update_2.","."0".",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "10**insert into variable_settings_production (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==35)
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, item_category_id, auto_update, distribute_qnty, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_1.",".$cbo_auto_update_1.",".$txtExcessPercent_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_2.",".$cbo_auto_update_2.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into variable_settings_production ".$field_array." values ".$data_array;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==47)
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, item_category_id, auto_update, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_1.",".$cbo_auto_update_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_2.",".$cbo_auto_update_2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into variable_settings_production ".$field_array." values ".$data_array;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==48)
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, item_category_id, auto_update, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_1.",".$cbo_auto_update_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_2.",".$cbo_auto_update_2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into variable_settings_production ".$field_array." values ".$data_array;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==3)
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, item_category_id, fabric_roll_level,page_upto_id, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_1.",".$cbo_fabric_roll_level_1.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_2.",".$cbo_fabric_roll_level_2.",".$cbo_entry_form_roll_level_2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_3.",".$cbo_fabric_roll_level_3.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_4.",".$cbo_fabric_roll_level_4.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_5.",".$cbo_fabric_roll_level_5.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into variable_settings_production ".$field_array." values ".$data_array;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==33)
		{
			$duplicate=is_duplicate_field( "page_category_id", "variable_settings_production", "page_category_id=$cbo_item_category and variable_list=33 and company_name=$cbo_company_name_production" );
 			if ($duplicate== 1)
			{
				echo "11**"." ";
				disconnect($con);
				die;
			}

			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, page_category_id, is_control,preceding_page_id, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category.",".$cbo_auto_update.",".$cbo_preceding_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==50)
		{
			$duplicate=is_duplicate_field( "page_category_id", "variable_settings_production", "page_category_id=$cbo_item_category and variable_list=50 and company_name=$cbo_company_name_production" );
 			if ($duplicate== 1)
			{
				echo "11**"." ";
				disconnect($con);
				die;
			}

			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, page_category_id, is_control,preceding_page_id, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category.",".$cbo_auto_update.",".$cbo_preceding_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==40)
		{
			$field_array="id, company_name, variable_list,service_process_id,is_serveice_rate_lib, inserted_by,insert_date,status_active";
			$id = return_next_id( "id", "variable_settings_production", 1);
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_service_process.",".$cbo_service_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			//echo "10***insert into variable_settings_production ".$field_array." values ".$data_array;die;
		}

		else if(str_replace("'","",$cbo_variable_list_production)==58 || str_replace("'","",$cbo_variable_list_production)==59 || str_replace("'","",$cbo_variable_list_production)==60  || str_replace("'","",$cbo_variable_list_production)==65 || str_replace("'","",$cbo_variable_list_production)==67 || str_replace("'","",$cbo_variable_list_production)==71 || str_replace("'","",$cbo_variable_list_production)==72)

		{
			$field_array="id, company_name, variable_list,production_entry,inserted_by,insert_date,status_active";
			$id = return_next_id( "id", "variable_settings_production", 1);
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_production_entry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			//echo "10***insert into variable_settings_production ".$field_array." values ".$data_array;die;
		}
		else if( str_replace("'","",$cbo_variable_list_production)==66)
		{

			$field_array="id, company_name, variable_list,production_entry,process_loss_editable,inserted_by,insert_date,status_active";
			$id = return_next_id( "id", "variable_settings_production", 1);
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_production_entry.",".$cbo_process_loss_editable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			//echo "10***insert into variable_settings_production ".$field_array." values ".$data_array;die;
		}
		else if( str_replace("'","",$cbo_variable_list_production)==70)
		{			
			$field_array="id, company_name, variable_list,production_entry,auto_print,apply_for,inserted_by,insert_date,status_active";
			$id = return_next_id( "id", "variable_settings_production", 1);
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_production_entry.",".$cbo_production_entry_2.",".$apply_for_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			//echo "10***insert into variable_settings_production ".$field_array." values ".$data_array;die;
		}
		else if(str_replace("'","",$cbo_variable_list_production)==74 )
		{
			$id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name, variable_list, grey_recvd_basis, grey_rate_come_from, finish_recvd_basis,finish_rate_come_from, inserted_by, insert_date";

			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_category_A1.",".$cbo_item_category_A2.",".$cbo_item_category_B1.",".$cbo_item_category_B2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "10**insert into variable_settings_production (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}
		else
		{
			if (is_duplicate_field( "company_name", "variable_settings_production", "company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production" ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			else
			{
				$id=return_next_id( "id", "variable_settings_production", 1 ) ;
				if(str_replace("'","",$cbo_variable_list_production)==1)
				{
					$field_array="id, company_name,variable_list,cutting_update_hcode,cutting_update,cutting_input_hcode,cutting_input,printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, production_entry_hcode,leftover_maintained,leftover_country_maintained,leftover_source,finish_fabric_req_cutting,hang_tag_update,working_company_mandatory,smv_source, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$cutting_update_html."',".$cbo_cutting_update.",'".$cutting_delevary_input_html."',".$cbo_cutting_update_to_input.",'".$printing_emb_production_html."',".$cbo_printing_emb_production.",'".$sewing_production_html."',".$cbo_sewing_production.",'".$iron_update_html."',".$cbo_iron_update.",'".$finishing_update_html."',".$cbo_finishing_update.",".$cbo_ex_factory.",'".$production_entry_html."',".$cbo_leftover.",".$cbo_leftover_country.",".$cbo_leftover_source.",".$cbo_finish_fabric_req_cutting.",".$cbo_hang_tag_update.",".$cbo_wo_maintain.",".$cbo_fin_gmt_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				/*else if(str_replace("'","",$cbo_variable_list_production)==3)
				{
					$field_array="id, company_name,variable_list, fabric_roll_level_hcode, fabric_roll_level, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$fabric_roll_level_html."',".$cbo_fabric_roll_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}*/

				else if(str_replace("'","",$cbo_variable_list_production)==32)
				{
					$field_array="id, company_name,variable_list, cut_panel_delevery_hcode, cut_panel_delevery, inserted_by,insert_date,status_active,
					is_deleted";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$cut_panel_basis_html."',".$cbo_cut_panel_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}

				else if(str_replace("'","",$cbo_variable_list_production)==4 || str_replace("'","",$cbo_variable_list_production)==69)
				{
					$field_array="id, company_name,variable_list, fabric_machine_level_hcode, fabric_machine_level, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$fabric_machine_level_html."',".$cbo_fabric_machine_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==13)
				{
					$field_array="id, company_name,variable_list, batch_maintained_hcode, batch_maintained, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$batch_maintained_html."',".$cbo_batch_maintained.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==23)
				{
					$field_array="id, company_name,variable_list, auto_update, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_prod_resource.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==24 )
				{
					$field_array="id, company_name,variable_list, batch_no_creation,yd_batch_no_creation,inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_batch_no.",".$cbo_yd_batch_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				
				else if(str_replace("'","",$cbo_variable_list_production)==68 )
				{
					$field_array="id, company_name,variable_list, SEWING_PRODUCTION, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_replace_field_disable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				
				
				
				else if(str_replace("'","",$cbo_variable_list_production)==52)
				{
					$field_array="id, company_name,variable_list, textile_business_concept, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_textile_business.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==53)
				{
					$field_array="id, company_name,variable_list, qty_source_sample, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_sample_delivery_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==54)//Stock Check
				{
					$field_array="id, company_name,variable_list, is_control, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_stock_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==25)
				{
					$field_array="id, company_name, variable_list, smv_source, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==26)
				{
					$field_array="id, company_name, variable_list, shift_id, prod_start_time, lunch_start_time, inserted_by,insert_date";
					$data_array='';
					for($i=1;$i<=$total_row;$i++)
					{
						$shift_id="shift_id".$i;
						$update_id="update_id".$i;
						$txt_prod_start_time="txt_prod_start_time".$i;
						$txt_lunch_start_time="txt_lunch_start_time".$i;

						if($db_type==0)
						{
							if($data_array!="") $data_array.=",";
							$data_array.="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$shift_id.",".$$txt_prod_start_time.",".$$txt_lunch_start_time.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}
						else if($db_type==2)
						{

							$prod_start_time="to_date(".$$txt_prod_start_time.",'HH24:MI:SS')";
							$lunch_start_time="to_date(".$$txt_lunch_start_time.",'HH24:MI:SS')";

							$data_array.="INTO variable_settings_production (".$field_array.") VALUES(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$shift_id.",".$prod_start_time.",".$lunch_start_time.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}

						$id=$id+1;
					}
					$query="INSERT ALL ".$data_array." SELECT * FROM dual";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==27)
				{
					$field_array="id, company_name, variable_list, smv_source, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==28)
				{
					$field_array="id, company_name, variable_list, cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update,hang_tag_update, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_cutting_update.",".$cbo_printing_emb_production.",".$cbo_sewing_production.",".$cbo_iron_update.",".$cbo_finishing_update.",".$cbo_hangtag_production.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}

				else if(str_replace("'","",$cbo_variable_list_production)==29)
				{
					$field_array="id, company_name, variable_list,piece_rate_wq_limit,inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_piece_rate_wo_limit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==30)
				{
					$field_array="id, company_name, variable_list,cut_sefty_parcent,sewing_sefty_parcent,iron_sefty_parcent,finish_sefty_parcent,inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$txt_cut_sefty_parcent.",".$txt_sewing_sefty_parcent.",".$txt_iron_sefty_parcent.",".$txt_finish_sefty_parcent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==31)
				{
					$field_array="id, company_name,variable_list, auto_update, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_prod_resource.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==34)
				{
					$field_array="id,company_name,variable_list,process_costing_maintain, auto_update, inserted_by,insert_date,status_active";
					echo $data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_process_costing.",".$cbo_rate_mandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==37 || str_replace("'","",$cbo_variable_list_production)==39)
				{
					$field_array="id, company_name, variable_list, smv_source, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==38)
				{
					$field_array="id, company_name, variable_list, is_control,is_locked, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$cbo_batch_selection.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==41)
				{
					$field_array="id, company_name,variable_list,working_company_mandatory, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_working_company_mandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==42)
				{
					$field_array="id, company_name, variable_list, qty_source_poly, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_qtySource.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==43)
				{
					$field_array="id, company_name, variable_list, qty_source_packing, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_qtySource.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==44)
				{
					$field_array="id, company_name, variable_list, dyeing_fin_bill, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_bill_on.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==46 || str_replace("'","",$cbo_variable_list_production)==61)
				{
					$field_array="id, company_name, variable_list, process_wise_rate_source, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_rate_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				/* else if(str_replace("'","",$cbo_variable_list_production)==48)
				{
					$field_array="id, company_name, variable_list, qc_mandatory, item_category_id, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_qc_mandatory_for_delivery.",13,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				} */

				else if(str_replace("'","",$cbo_variable_list_production)==49)
				{
					$field_array="id, company_name, variable_list,max_roll_weight, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$txt_max_roll_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==55 )
				{
					$field_array="id, company_name,variable_list, auto_update, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==56 || str_replace("'","",$cbo_variable_list_production)==57)
				{
					$field_array="id, company_name,variable_list, distribute_qnty, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==62)
				{
					$field_array="id, company_name,variable_list,hide_qc_result, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_hide_qc_result.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==63 || str_replace("'","",$cbo_variable_list_production)==64)
				{
					$field_array="id, company_name,variable_list,chemical_issue, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_chemical_issue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==73)
				{
					$field_array="id, company_name, variable_list, smv_source, inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==75)
				{
					$field_array="id,company_name,variable_list,allow_fin_fab_rcv,inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_fabric_allow_fabRecv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==76)
				{
					$field_array="id,company_name,variable_list,allow_fin_fab_rcv,inserted_by,insert_date,status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_fabric_allow_fabRecv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				

				//print_r($data_array);
				//echo "10insert into variable_settings_production ($field_array) values($data_array)";die;
				if(str_replace("'","",$cbo_variable_list_production)==26 && $db_type==2)
				{
					//echo $query; die;
					$rID=execute_query($query);
				}
				else
				{
					$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
				}
				//echo "10*******".$rID;die;
			}
		}


		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo 0;
			}
			else{
				mysql_query("ROLLBACK");
				echo 10;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
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
		if(str_replace("'","",$cbo_variable_list_production)==1)
		{
			$field_array="company_name*variable_list*cutting_update_hcode*cutting_update*cutting_input_hcode*cutting_input*printing_emb_production_hcode*printing_emb_production*sewing_production_hcode*sewing_production*iron_update_hcode*iron_update*finishing_update_hcode*finishing_update*ex_factory*production_entry_hcode*leftover_maintained*leftover_country_maintained*leftover_source*finish_fabric_req_cutting*hang_tag_update*working_company_mandatory*smv_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$cutting_update_html."'*".$cbo_cutting_update."*'".$cutting_delevary_input_html."'*".$cbo_cutting_update_to_input."*'".$printing_emb_production_html."'*".$cbo_printing_emb_production."*'".$sewing_production_html."'*".$cbo_sewing_production."*'".$iron_update_html."'*".$cbo_iron_update."*'".$finishing_update_html."'*".$cbo_finishing_update."*".$cbo_ex_factory."*'".$production_entry_html."'*".$cbo_leftover."*".$cbo_leftover_country."*".$cbo_leftover_source."*".$cbo_finish_fabric_req_cutting."*".$cbo_hang_tag_update."*".$cbo_wo_maintain."*".$cbo_fin_gmt_transfer."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==2)
		{

			sql_select("delete from variable_prod_excess_slab where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production");

			$slab_id = return_next_id( "id", "variable_prod_excess_slab", 1);
			$field_array="id, company_name,variable_list, slab_rang_start, slab_rang_end, excess_percent, inserted_by,insert_date,status_active";

			for($i=1;$i<=$counter;$i++)
			{

				$txt_slab_rang_start = 'txt_slab_rang_start'.$i;
				$txt_slab_rang_end = 'txt_slab_rang_end'.$i;
				$txt_excess_percent = 'txt_excess_percent'.$i;
				if($i==1)
					$data_array.="(".$slab_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$txt_slab_rang_start.",".$$txt_slab_rang_end.",".$$txt_excess_percent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				else
					$data_array.=",(".$slab_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$txt_slab_rang_start.",".$$txt_slab_rang_end.",".$$txt_excess_percent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$slab_id++;

			}
			 $rID=sql_insert("variable_prod_excess_slab",$field_array,$data_array,1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==36 || str_replace("'","",$cbo_variable_list_production)==45)
		{
			sql_select("delete from variable_settings_production where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production");
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name,variable_list, fabric_grade, get_upto_first, get_upvalue_first, get_upto_second, get_upvalue_second, inserted_by, insert_date, status_active";
			$data_array="";
			for($i=1;$i<=$row_num;$i++)
			{
				$fabricGrade = 'fabricGrade_'.$i;
				$cboGetUptoFirst = 'cboGetUptoFirst_'.$i;
				$valueFirst = 'valueFirst_'.$i;
				$cboGetUptoSecond = 'cboGetUptoSecond_'.$i;
				$valueSecond = 'valueSecond_'.$i;
				if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$fabricGrade.",".$$cboGetUptoFirst.",".$$valueFirst.",".$$cboGetUptoSecond.",".$$valueSecond.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;

			}
			 $rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==51)
		{
			//$dtls_row=execute_query("delete from variable_settings_production where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production");


			$updateIDs="";
			for($j=1;$j<=$total_row;$j++)
			{
				$update_id = 'update_id_'.$j;
				$updateIDs.= $$update_id.",";
			}
			$updateIDs=chop($updateIDs,",");
			
			$dtls_row=execute_query("update variable_settings_production set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production and id in($updateIDs)");
			
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			$field_array="id, company_name,variable_list,  item_category_id, auto_update,distribute_qnty, inserted_by, insert_date, status_active"; $data_array="";
			for($i=1;$i<=$total_row;$i++)
			{
				$item_category_id = 'cbo_item_category_'.$i;
				$cbo_auto_update = 'cbo_auto_update_'.$i;
				$txtExcessPercent = 'txtExcessPercent_'.$i;
				 
				if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$item_category_id.",".$$cbo_auto_update.",".$$txtExcessPercent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;

			}
			 $rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			 if($rID && $dtls_row)$rID=1; else $rID=0;


		}


		else if(str_replace("'","",$cbo_variable_list_production)==32)
		{
			$field_array="company_name*variable_list*cut_panel_delevery_hcode*cut_panel_delevery*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$cut_panel_basis_html."'*".$cbo_cut_panel_basis."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		/*else if(str_replace("'","",$cbo_variable_list_production)==3)
		{
			$field_array="company_name*variable_list*fabric_roll_level_hcode*fabric_roll_level*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$fabric_roll_level_html."'*".$cbo_fabric_roll_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		} */
		else if(str_replace("'","",$cbo_variable_list_production)==4 || str_replace("'","",$cbo_variable_list_production)==69)
		{
			$field_array="company_name*variable_list*fabric_machine_level_hcode*fabric_machine_level*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$fabric_machine_level_html."'*".$cbo_fabric_machine_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==13)
		{
			$field_array="company_name*variable_list*batch_maintained_hcode*batch_maintained*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$batch_maintained_html."'*".$cbo_batch_maintained."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==15)
		{
			$field_array="company_name*variable_list*item_category_id*auto_update*distribute_qnty*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_auto_update_1."*".$cbo_receive_basis_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_auto_update_2."*"."''"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==35)
		{
			$field_array="company_name*variable_list*item_category_id*auto_update*distribute_qnty*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);

			$txtExcessPercent_1 = str_replace("'","",$txtExcessPercent_1)*1;

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_auto_update_1."*".$txtExcessPercent_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_auto_update_2."*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==47)
		{
			$field_array="company_name*variable_list*item_category_id*auto_update*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_auto_update_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_auto_update_2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==48)
		{
			$field_array="company_name*variable_list*item_category_id*auto_update*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_auto_update_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_auto_update_2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==3)
		{
			$field_array="company_name*variable_list*item_category_id*fabric_roll_level*page_upto_id*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);
			$updateID_array[]=str_replace("'","",$update_id_3);
			$updateID_array[]=str_replace("'","",$update_id_4);
			$updateID_array[]=str_replace("'","",$update_id_5);
			$cbo_entry_form_roll_level_2=str_replace("'","",$cbo_entry_form_roll_level_2);

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_fabric_roll_level_1."*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_fabric_roll_level_2."*".$cbo_entry_form_roll_level_2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_3)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_3."*".$cbo_fabric_roll_level_3."*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_4)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_4."*".$cbo_fabric_roll_level_4."*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$data_array[str_replace("'","",$update_id_5)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_5."*".$cbo_fabric_roll_level_5."*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			//echo "10**".bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array);die;
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==33 || str_replace("'","",$cbo_variable_list_production)==50)
		{
			$field_array="company_name*variable_list*page_category_id*is_control*preceding_page_id*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id);

			$data_array[str_replace("'","",$update_id)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category."*".$cbo_auto_update."*".$cbo_preceding_item_category."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			//echo "10**".bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array);die;
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);
		//echo "10**0".$rID;die;
		}
		else if(str_replace("'","",$cbo_variable_list_production)==23)
		{
			$field_array="company_name*variable_list*auto_update*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_prod_resource."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==24  )
		{
			$field_array="company_name*variable_list*batch_no_creation*yd_batch_no_creation*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_batch_no."*".$cbo_yd_batch_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		
		else if(str_replace("'","",$cbo_variable_list_production)==68  )
		{
			$field_array="company_name*variable_list*SEWING_PRODUCTION*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_replace_field_disable."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==52)
		{
			$field_array="company_name*variable_list*textile_business_concept*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_textile_business."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==53)
		{
			$field_array="company_name*variable_list*qty_source_sample*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_sample_delivery_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==54)
		{
			$field_array="company_name*variable_list*is_control*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_stock_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==25)
		{
			$field_array="company_name*variable_list*smv_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==26)//sewing production
		{
			$field_array="shift_id*prod_start_time*lunch_start_time*updated_by*update_date";
			for($i=1;$i<=$total_row;$i++)
			{
				$shift_id="shift_id".$i;
				$update_id="update_id".$i;
				$txt_prod_start_time="txt_prod_start_time".$i;
				$txt_lunch_start_time="txt_lunch_start_time".$i;

				if($db_type==0)
				{
					$prod_start_time=$$txt_prod_start_time;
					$lunch_start_time=$$txt_lunch_start_time;
				}
				else
				{
					$prod_start_time="to_date(".$$txt_prod_start_time.",'HH24:MI:SS')";
					$lunch_start_time="to_date(".$$txt_lunch_start_time.",'HH24:MI:SS')";
				}

				$id_arr[]=str_replace("'",'',$$update_id);

				$data_array_update[str_replace("'",'',$$update_id)] = explode("*",($$shift_id."*".$prod_start_time."*".$lunch_start_time."*".$user_id."*'".$pc_date_time."'"));
			}
			//echo bulk_update_sql_statement( "variable_settings_production", "id", $field_array, $data_array_update, $id_arr );
			$rID=execute_query(bulk_update_sql_statement( "variable_settings_production", "id", $field_array, $data_array_update, $id_arr ));

			/*$field_array="id, company_name, variable_list, shift_id, prod_start_time, lunch_start_time, inserted_by,insert_date";
			$data_array='';
			$id=return_next_id( "id", "variable_settings_production", 1 ) ;
			for($i=1;$i<=$total_row;$i++)
			{
				$shift_id="shift_id".$i;
				$update_id="update_id".$i;
				$txt_prod_start_time="txt_prod_start_time".$i;
				$txt_lunch_start_time="txt_lunch_start_time".$i;

				if($db_type==0)
				{
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$shift_id.",".$$txt_prod_start_time.",".$$txt_lunch_start_time.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				else if($db_type==2)
				{
					$prod_start_time="to_date(".$$txt_prod_start_time.",'HH24:MI:SS')";
					$lunch_start_time="to_date(".$$txt_lunch_start_time.",'HH24:MI:SS')";

					$data_array.="INTO variable_settings_production (".$field_array.") VALUES(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$shift_id.",".$prod_start_time.",".$lunch_start_time.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

				$id=$id+1;
			}
			$query="INSERT ALL ".$data_array." SELECT * FROM dual";

			$delete_roll=execute_query( "delete from variable_settings_production where company_name=$cbo_company_name_production and variable_list=26",0);
			if($db_type==2)
			{
				//echo $query;
				$rID=execute_query($query);
			}
			else
			{
				$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			}*/
		}
		else if(str_replace("'","",$cbo_variable_list_production)==27)
		{
			$field_array="company_name*variable_list*smv_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==28)
		{
			$field_array="company_name*variable_list*cutting_update*printing_emb_production*sewing_production*iron_update*finishing_update*hang_tag_update*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_cutting_update."*".$cbo_printing_emb_production."*".$cbo_sewing_production."*".$cbo_iron_update."*".$cbo_finishing_update."*".$cbo_hangtag_production."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==29)
		{
			$field_array="company_name*variable_list*piece_rate_wq_limit*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_piece_rate_wo_limit."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==30)
		{
			$field_array="company_name*variable_list*cut_sefty_parcent*sewing_sefty_parcent*iron_sefty_parcent*finish_sefty_parcent*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$txt_cut_sefty_parcent."*".$txt_sewing_sefty_parcent."*".$txt_iron_sefty_parcent."*".$txt_finish_sefty_parcent."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==31)
		{
			$field_array="company_name*variable_list*auto_update*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_prod_resource."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==34)
		{
			$field_array="company_name*variable_list*process_costing_maintain*auto_update*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_process_costing."*".$cbo_rate_mandatory."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==37 || str_replace("'","",$cbo_variable_list_production)==39)
		{
			$field_array="company_name*variable_list*smv_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==38)
		{
			$field_array="company_name*variable_list*is_control*is_locked*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$cbo_batch_selection."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==40)
		{
			$field_array="company_name*variable_list*service_process_id*is_serveice_rate_lib*updated_by*update_date";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_service_process."*".$cbo_service_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==41)
		{
			$cbo_working_company_mandatory=str_replace("'","",$cbo_working_company_mandatory);
			$field_array="company_name*variable_list*working_company_mandatory*updated_by*update_date*status_active";

			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$cbo_working_company_mandatory."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";

			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==42)
		{
			$field_array="qty_source_poly*updated_by*update_date*status_active";
			$data_array="".$cbo_qtySource."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==43)
		{
			$field_array="qty_source_packing*updated_by*update_date*status_active";
			$data_array="".$cbo_qtySource."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==44)
		{
			$field_array="company_name*variable_list*dyeing_fin_bill*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_bill_on."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==46 || str_replace("'","",$cbo_variable_list_production)==61)
		{
			$field_array="company_name*variable_list*process_wise_rate_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_rate_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		/* else if(str_replace("'","",$cbo_variable_list_production)==48)
		{
			$field_array="company_name*variable_list*qc_mandatory*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_qc_mandatory_for_delivery."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		} */
		else if(str_replace("'","",$cbo_variable_list_production)==49)
		{
			$field_array="company_name*variable_list*max_roll_weight*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$txt_max_roll_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==55 )
		{
			$field_array="company_name*variable_list*auto_update*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==56 || str_replace("'","",$cbo_variable_list_production)==57)
		{
			$field_array="company_name*variable_list*distribute_qnty*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==58 || str_replace("'","",$cbo_variable_list_production)==59 || str_replace("'","",$cbo_variable_list_production)==60 || str_replace("'","",$cbo_variable_list_production)==65 || str_replace("'","",$cbo_variable_list_production)==67 || str_replace("'","",$cbo_variable_list_production)==71 || str_replace("'","",$cbo_variable_list_production)==72)
		{
			$field_array="company_name*variable_list*production_entry*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_production_entry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if( str_replace("'","",$cbo_variable_list_production)==66 )
		{
			$field_array="company_name*variable_list*production_entry*process_loss_editable*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_production_entry."*".$cbo_process_loss_editable."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if( str_replace("'","",$cbo_variable_list_production)==70 )
		{
			$field_array="company_name*variable_list*production_entry*auto_print*apply_for*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_production_entry."*".$cbo_production_entry_2."*".$apply_for_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			//echo "10**$data_array"."=".$update_id;die;

			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==62)
		{
			$field_array="company_name*variable_list*hide_qc_result*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_hide_qc_result."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==63 || str_replace("'","",$cbo_variable_list_production)==64)
		{
			$field_array="company_name*variable_list*chemical_issue*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_chemical_issue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==74 )
		{
			$field_array="company_name*variable_list*grey_recvd_basis*grey_rate_come_from*finish_recvd_basis*finish_rate_come_from*updated_by*update_date*status_active";

			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_A1."*".$cbo_item_category_A2."*".$cbo_item_category_B1."*".$cbo_item_category_B2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==73)
		{
			$field_array="company_name*variable_list*smv_source*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==75)
		{
			$field_array="company_name*variable_list*allow_fin_fab_rcv*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_fabric_allow_fabRecv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==76)
		{
			$field_array="company_name*variable_list*allow_fin_fab_rcv*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_fabric_allow_fabRecv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo 1;
			}
			else{
				mysql_query("ROLLBACK");
				echo 10;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			 if($rID )
			    {
					oci_commit($con);
					echo "1**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_drop_down_preceding_process")
{
	$data=explode('**', trim($data));
	/*if($data[0]==2)
	{
		$fixed_index="0";
	}
	else*/
	//{
	if($data[1]==28) // sewing input
	{
		$fixed_index="117,123";

	}
	else if($data[1]==29) // sewing output
	{
		$fixed_index="117,28";
	}
	else if($data[1]==30) // iron entry
	{
		$fixed_index="29,116,250";
	}
	else if($data[1]==256) // Hang Tag Entry
	{
		$fixed_index="30";
	}
	else if($data[1]==31) // Packing And Finishing or cartoon
	{
		$fixed_index="103,30,29,116,250,256";
	}
	else if($data[1]==32) // Ex-Factory
	{
		$fixed_index="29,31,91,260,103,30,276,277";
	}
	else if($data[1]==91) // Inspection
	{
		$fixed_index="29,31,260,103,30,276,277";
	}

	else if($data[1]==103) // Poly Entry
	{
		$fixed_index="29,30,116,250,256";
	}

	else if($data[1]==116) // Finishing Input
	{
		$fixed_index="29";
	}

	else if($data[1]==123) // cutting delivery to........
	{
		$fixed_index="117";
	}

	else if($data[1]==250) // woven finishing ........
	{
		$fixed_index="252,29";
	}

	else if($data[1]==251) // wash issue........
	{
		$fixed_index="29";
	}
	else if($data[1]==252) // wash receive........
	{
		$fixed_index="251";
	}
	else if($data[1]==269) // fin delivery.......
	{
		$fixed_index="29,31";
	}
	else if($data[1]==268) // finish receive........
	{
		$fixed_index="269,29,31";
	}
	else if($data[1]==260) // finish issue........
	{
		$fixed_index="269,268,29,31";
	}
	else if($data[1]==117) // cutting qc / entry ........
	{
		$fixed_index="288";
	}
	else
	{
		$report_signeture_list = array();
	}


//}
	echo create_drop_down( "cbo_preceding_item_category", 150, $report_signeture_list,'',1, "---Select---", '', '', "",$fixed_index );

}

if ($action=="load_drop_down_preceding_process_sweater")
{
	$data=explode('**', trim($data));
	$category=$data[1];
	
	if($category==1)
	{
		$fixed_index="0";

	}
	else if($category==100)
	{
		$fixed_index="1";
	}
	else if($category==4)
	{
		$fixed_index="1,100";
	}
	else if($category==111)
	{
		$fixed_index="1,100,4";
	}
	else if($category==112)
	{
		$fixed_index="4,111";
	}
	else if($category==3)
	{
		$fixed_index="111,112";
	}
	else if($category==11)
	{
		$fixed_index="3,112";
	}
	else if($category==5)
	{
		$fixed_index="3,112,11";
	}
	else if($category==114)
	{
		$fixed_index="5,112,11";
	}
	else if($category==113)
	{
		$fixed_index="5,11,114";
	}
	else if($category==67)
	{
		$fixed_index="3,5";
	}
	else if($category==8)
	{
		$fixed_index="3,67,5";
	}
	

	if(count(explode(",", $fixed_index))>1)
	{
		echo create_drop_down( "cbo_preceding_item_category", 150, $production_type_sweater,'',1, "---Select---", '', '', "",$fixed_index );
	}
	else
	{
		echo create_drop_down( "cbo_preceding_item_category", 150, $production_type_sweater,'',1, "---Plan Cut---", '', '', "",$fixed_index );
	}




}


?>