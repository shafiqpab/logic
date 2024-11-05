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
		$company_id = $explode_data[1];

	if ($type==1)   //Production_Production Update Areas
	{
		$nameArray= sql_select("select id, company_name,variable_list,cutting_update_hcode,cutting_update,cutting_input, printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, fabric_roll_level_hcode, fabric_roll_level, fabric_machine_level_hcode,fabric_machine_level, batch_maintained_hcode,batch_maintained, iron_input,production_entry from variable_settings_production where company_name='$company_id' and variable_list=1 order by id");
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
							echo create_drop_down( "cbo_cutting_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('cutting_update')], "" );
							?>
						</td>
						<td width="160" align="left" id="printing_emb_production">Printing & Embrd. Prodiction</td>
						<td width="180">
							<? 
							echo create_drop_down( "cbo_printing_emb_production", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('printing_emb_production')], "" );
							?>
						</td>
					</tr>
					<tr> 
						<td width="130" align="left" id="sewing_production">Sewing Production</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_sewing_production", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('sewing_production')], "" );
							?>
						</td>
						<td width="130" align="left" id="finishing_update">Finishing Entry</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_finishing_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('finishing_update')], "" );
							?>
						</td>
					</tr>
					<tr>
						<td width="160" align="left" id="iron_update">Iron Output</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_iron_update", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('iron_update')], "" );
							?>
						</td>  
						<td width="160" align="left" id="ex_factory">Ex-Factory</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_ex_factory", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('ex_factory')], "" );
							?>
						</td>
					</tr>
					<tr> 
						<td width="130" align="left" id="production_entry">Production Entry</td>
						<td width="190">
							<? 
							$production_entry = array(1=>'Style Wise',2=>'Order Wise');
							echo create_drop_down( "cbo_production_entry", 170, $production_entry,'', 1, '---- Select ----', $nameArray[0][csf('production_entry')], "" );
							?>
						</td>
						<td width="130" id="cutting_delevery_entry">Cutting delivery to Input</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_cutting_update_to_input", 170, $production_update_areas,'', 1, '---- Select ----', $nameArray[0][csf('cutting_input')], "" );
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
	{ //Production_Excess Cutting Slab
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
	{

 			$category_wise_array=array();
 			$nameArray= sql_select("select id, company_name,variable_list,item_category_id,fabric_roll_level,page_upto_id from variable_settings_production where company_name='$company_id' and variable_list=3 and item_category_id in (2,3,13,50,51) and status_active=1 and is_deleted= 0 order by id");

 			if(count($nameArray)>0) $is_update=1; else $is_update=0;

 			foreach($nameArray as $row)
 			{
 				$category_wise_array[$row[csf('item_category_id')]]['roll']=$row[csf('fabric_roll_level')];
 				$category_wise_array[$row[csf('item_category_id')]]['upto']=$row[csf('page_upto_id')];
 				$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
 			}
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
 							<input type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[13]['update_id']; ?>">
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

 							<input  type="hidden"name="update_id_5" id="update_id_5" value="<? echo $category_wise_array[51]['update_id']; ?>">
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
 							<input type="hidden"name="update_id_4" id="update_id_4" value="<? echo $category_wise_array[3]['update_id']; ?>">
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
                				<input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
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
	{//Production_Fabric in Machine Level
		
		$nameArray= sql_select("select id, planning_board_strip_caption from variable_settings_production where variable_list=4 order by id"); //company_name='$company_id' and 
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        

		<fieldset>
			<legend>Planning Board Strip Caption</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="fabric_machine_level">Planning Board Strip Caption</td>
						<td width="190">
							<? $arr=array(1=>'Style Ref',2=>'Int. Ref',3=>'Job No',4=>'Order No',5=>'Buyer Name',6=>'Plan Quantity');
							echo create_drop_down( "cbo_fabric_machine_level", 170, $arr,'', 0, '---- Select ----', $nameArray[0][csf('planning_board_strip_caption')], "",'','' );
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
							<input  type="hidden"name="planning_board_strip_caption_val" id="planning_board_strip_caption_val" value="<? echo $nameArray[0][csf('planning_board_strip_caption')]; ?>">
							<? 
							echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
							?>
						</td>					
					</tr>
				</table>
			</div>
            
            <b style="color:#FF0000">Note: Please set this variable for only one company.</b>
            
             

		</fieldset>
			
		<?
	}
	else if ($type==5)
	{ // PLANNING VARIABLE SETTINGS
		$nameArray= sql_select("select id, distribute_qnty from variable_settings_production where company_name='$company_id' and variable_list=5 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Distribute Quantity</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="distribute_qnty">Distribute Quantity</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_is_distribute_qnty", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('distribute_qnty')], "",'','' );
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
	else if ($type==6)
	{ // PLANNING VARIABLE SETTINGS
		$nameArray= sql_select("select id, auto_allocate_yarn_from_requis from variable_settings_production where company_name='$company_id' and variable_list=6 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Auto  Allocate Yarn From Requisition</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Auto  Allocate Yarn From Requisition</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_is_auto_allocate_yarn", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('auto_allocate_yarn_from_requis')], "",'','' );
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
	else if ($type==7)
	{ // PLANNING VARIABLE SETTINGS
		$nameArray= sql_select("select id, rms_integretion from variable_settings_production where company_name='$company_id' and variable_list=7 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Auto  Allocate Yarn From Requisition</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Auto  Allocate Yarn From Requisition</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_is_rms_integretion", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('rms_integretion')], "",'','' );
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
	else if ($type==8)
	{ 

		$nameArray= sql_select("SELECT id, color_type_mandatory from variable_settings_production where company_name='$company_id' and variable_list=8 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">

			<fieldset>
				<legend>Color Type Mandatory</legend>
				<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
					<table cellspacing="0" width="100%" >
						<tr> 
							<td width="130" align="left" id="auto_allocate_yarn">Color Type Mandatory</td>
							<td width="190">
								<? $arr=array(1=>'yes',2=>'No');
								echo create_drop_down( "cbo_is_color_mandatory", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('color_type_mandatory')], "",'','' );
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
		</form>

		<?
	}
	else if ($type==9)
	{  
		$nameArray= sql_select("select id, work_study_integrated,smv_type from variable_settings_production where company_name='$company_id' and variable_list=9 order by id");
		
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<script>
		// Add an event listener to the first dropdown
		// var firstDropdown = document.getElementById("cbo_is_work_study");
		// firstDropdown.addEventListener("change", function() {
		//     // If "yes" is selected, enable the second dropdown
		//     if (this.value === "yes") {
		//         document.getElementById("cbo_smv").disabled = false;
		//     } else {
		//         // If "no" is selected, disable the second dropdown
		//         document.getElementById("cbo_smv").disabled = true;
		//     }
		// });
		
		</script>
		<fieldset>
			<legend>Work Study Integrated In Planning</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="distribute_qnty">Work Study Integrated</td>
						<td id="first_dropdown" width="190">
							<? $arr=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_is_work_study", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('work_study_integrated')], "enable_disable(this.value)",'','' );
							?>
						</td>

					</tr>
	                   </br>
					<tr>
						<td width="130" align="left" id="distribute_qnty">SMV Bulletin Type </td>
						<td id="second_dropdown" width="190" >
							<? $arr_smv=array(3=>'Budget',4=>'Production');
							
							echo create_drop_down( "cbo_smv", 170, $arr_smv,'', 1, '---- Select ----', $nameArray[0][csf('smv_type')], "",'','' );
	                          
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
	else if ($type==10)
	{  
		$nameArray= sql_select("SELECT id, work_study_integrated from variable_settings_production where company_name='$company_id' and variable_list=10 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Size Disable Status</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="distribute_qnty">Size Disable Status</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_is_work_study", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('work_study_integrated')], "",'','' );
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
	else if ($type==11)//SMV Editable In Work Study
	{  
		
		$nameArray= sql_select("SELECT id,company_name,variable_list, bulletin_type,smv_editable from variable_settings_production where variable_list=$type order by id");//company_name='$company_id' and 
		
		foreach($nameArray as $row){
			//$bulletintypearr[$row[csf('bulletin_type')]]=$bulletin_type_arr[$row[csf('bulletin_type')]];
			$bulletinTypeYesNoArr[$row[csf('bulletin_type')]]=$row[csf('smv_editable')];
			$bulletinTypeIdArr[$row[csf('bulletin_type')]]=$row[csf('id')];
		}	
		
		if(count($nameArray)>0){$is_update=1;}else{$is_update=0;$bulletintypearr=$bulletin_type_arr;}
		?>

		<fieldset>
			<legend>SMV Editable In Work Study</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="5" width="100%" id="tbl_list_search">
					
					<? foreach($bulletin_type_arr as $bulletin_type_id=>$bulletin_type_val){ ?>
					<tr> 
						<td width="130" align="right" id="bulletin_type"><? echo $bulletin_type_val; ?></td>
						<td width="190">
							<input type="hidden" id="txt_bulletin_type_<? echo $bulletin_type_id;?>" value="<? echo $bulletin_type_id;?>" name="txt_bulletin_type_<? echo $bulletin_type_id;?>">
                            <input  type="hidden" name="update_id" id="update_id_<? echo $bulletin_type_id;?>" value="<? echo $bulletinTypeIdArr[$bulletin_type_id]; ?>">
							<?
							echo create_drop_down( "cbo_is_editiable_".$bulletin_type_id, 170, $yes_no,'', 0, '---- Select ----', $bulletinTypeYesNoArr[$bulletin_type_id], "",'','' );
							?>
						</td>
					</tr>
				<? } ?>


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
	else if ($type==12)//Plan Level
	{  
		
		$nameArray= sql_select("SELECT id,company_name,variable_list, bulletin_type from variable_settings_production where variable_list=$type order by id");//company_name='$company_id' and 
		
		foreach($nameArray as $row){
			$bulletin_type=$row[csf('bulletin_type')];
			$update_id=$row[csf('id')];
		}	
		
		if(count($nameArray)>0){$is_update=1;}else{$is_update=0;}
		?>

		<fieldset>
			<legend>Plan Level</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="5" width="100%" id="tbl_list_search">
					
					<tr> 
						<td width="130" align="right" id="bulletin_type">Plan Level Entry</td>
						<td width="190">
                            <input  type="hidden" name="update_id" id="update_id" value="<? echo $update_id; ?>">
							<?
							$ComplexityLevelArr=array(1=>"PO Level",2=>"Color Level",3=>"Country Level");
							echo create_drop_down( "txt_bulletin_type", 170, $ComplexityLevelArr,'', 1, '---- Select ----', $bulletin_type, "",'','' );
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

	else if ($type==154)//Learning Curve Method
	{  
		
		$nameArray= sql_select("SELECT id,company_name,variable_list, bulletin_type from variable_settings_production where variable_list=$type order by id");//company_name='$company_id' and 
		
		foreach($nameArray as $row){
			$bulletin_type=$row[csf('bulletin_type')];
			$update_id=$row[csf('id')];
		}	
		
		if(count($nameArray)>0){$is_update=1;}else{$is_update=0;}
		?>

		<fieldset>
			<legend>Learning Curve Method</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="5" width="100%" id="tbl_list_search">
					
					<tr> 
						<td width="130" align="right" id="bulletin_type">Learning Effect By

		</td>
						<td width="190">
                            <input  type="hidden" name="update_id" id="update_id" value="<? echo $update_id; ?>">
							<?
							$ComplexityLevelArr=array(1=>"Fixed Quantity",2=>"Efficiency Percantage");
							echo create_drop_down( "txt_bulletin_type", 170, $ComplexityLevelArr,'', 1, '---- Select ----', $bulletin_type, "",'','' );
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
		$nameArray= sql_select("select id, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list=15 order by id");

		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		foreach($nameArray as $row)
		{
			$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
			$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
		}
		?>
		<fieldset>
			<legend>Auto Faric Store Update</legend>
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
		
		$nameArray=sql_select("select id, batch_no_creation from variable_settings_production where company_name='$company_id' and variable_list=24 and status_active=1 and is_deleted=0");
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
	{//Barcode Generation
		
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
		$nameArray= sql_select("select id, company_name, variable_list, cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name='$company_id' and variable_list=28 order by id");
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
							echo create_drop_down( "cbo_cutting_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('cutting_update')], "" );
							?>
						</td>
						<td width="160" align="left" id="iron_update">Iron Output</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_iron_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('iron_update')], "" );
							?>
						</td>
					</tr>
					<tr> 
						<td width="160" align="left" id="printing_emb_production">Printing & Embrd. Prodiction</td>
						<td width="180">
							<? 
							echo create_drop_down("cbo_printing_emb_production",170,$production_update_areas,'',1,'--Select--',$nameArray[0][csf('printing_emb_production')],"");
							?>
						</td>
						<td width="130" align="left" id="finishing_update">Finishing Entry</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_finishing_update", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('finishing_update')], "" );
							?>
						</td>
					</tr>
					<tr>
						<td width="130" align="left" id="sewing_production">Sewing Production</td>
						<td width="190">
							<? 
							echo create_drop_down( "cbo_sewing_production", 170, $production_update_areas,'', 1, '--Select--', $nameArray[0][csf('sewing_production')], "" );
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
								echo create_drop_down( "cbo_item_category", 150, $report_signeture_list,'',1, "---Select Category---", '', "fnc_load_preceding_process($('#cbo_auto_update').val()+'**'+this.value);", "",'28,29,30,31,32,91,103,116' );
								?>
							</td>
							<td>
								<? 
								echo create_drop_down( "cbo_auto_update", 150, $yes_no,'', 1, '---- Select ----', "", "fnc_load_preceding_process(this.value+'**'+$('#cbo_item_category').val());",'','' );
								?>
							</td>
							<td id="preceding_td">  
								<? 
								echo create_drop_down( "cbo_preceding_item_category", 150, $report_signeture_list,'',1, "---Plan Cut---", '', '', "",'28,29,30,31,32,91,103,116,117,123' );
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
					$sql= "select id, company_name,variable_list,page_category_id,is_control,preceding_page_id from variable_settings_production where company_name='$company_id' and variable_list=33 order by id";
					$report_signeture_list[0]='';
					$arr=array (0=>$report_signeture_list,1=>$yes_no,2=>$report_signeture_list);
					echo  create_list_view ( "list_view","Category,Control,Preceding Process", "150,150,150,","500","250",0, $sql, "get_php_form_data", "id", "'last_preceding_from_data','requires/production_settings_controller'", 1, "page_category_id,is_control,preceding_page_id", $arr , "page_category_id,is_control,preceding_page_id", "", 'setFilterGrid("list_view",-1);','0,0,0','',""); 
					?>

				</div>
			</fieldset>
			<?
		}
	else if ($type==34)// for process costing maintain
	{

		$nameArray= sql_select("select id, company_name,variable_list,process_costing_maintain  from variable_settings_production 
			where company_name='$company_id' and variable_list=34 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
			<legend>Process Costing Maintain</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="fabric_roll_level" cbo_fabric_roll_level>Process Costing Maintain</td>
						<td width="190"> 
							<? 
							echo create_drop_down( "cbo_process_costing", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('process_costing_maintain')], "",'','');
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
	else if ($type==35)
	{//Production Auto Faric Store Update
		
		$category_wise_array=array();

		$nameArray= sql_select("select id, company_name,variable_list,item_category_id,auto_update from variable_settings_production where company_name='$company_id' and variable_list=35 order by id");

		if(count($nameArray)>0) $is_update=1; else $is_update=0;

		foreach($nameArray as $row)
		{
			$category_wise_array[$row[csf('item_category_id')]]['auto']=$row[csf('auto_update')];
			$category_wise_array[$row[csf('item_category_id')]]['update_id']=$row[csf('id')];
		}
		?>
		<fieldset>
			<legend>Fabric Production Control</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" class="rpt_table">
					<thead>
						<th>Item Category</th>
						<th>Production Control</th>
					</thead>
					<tr align="center"> 
						<td>
							<? 
							echo create_drop_down( "cbo_item_category_1", 150, $item_category,'', 0, '', '13', "",'','13' );
							?>
						</td>
						<td>
							<? 
							echo create_drop_down( "cbo_auto_update_1", 150, $yes_no,'', 1, '---- Select ----', $category_wise_array[13]['auto'], "",'','' );
							?>
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
	else if ($type==36)
	{//Production Auto Faric Store Update
		$permission=$_SESSION['page_permission'];
		$category_wise_array=array();
		
		$nameArray= sql_select("select id, company_name, variable_list, fabric_grade, get_upto_first, get_upvalue_first, get_upto_second, get_upvalue_second from variable_settings_production where company_name='$company_id' and variable_list=36 order by id");
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
	else if($type==38)
	{//Bundle No Creation
		
		$nameArray=sql_select("select id, is_control from variable_settings_production where company_name='$company_id' and variable_list=38 and status_active=1 and is_deleted=0");
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
		
		$rmg_no_generation_arr=array(1=>"Size Wise",2=>"Cutting No. Wise",3=>"Job No. Wise",4=>"Order No. Wise");
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
	}else if($type==44)//Fabric Source For Batch
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
							echo create_drop_down( "cbo_bill_on", 170, array(1=>"Receive",2=>"Production",3=>"Issue"),'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' );
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
	else if ($type==53)
	{ 
		// Color mixing in knitting plan		
		$nameArray= sql_select("select id, color_mixing_in_knitting_plan,coller_cuf_size_planning from variable_settings_production where company_name='$company_id' and variable_list=53 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">	
		<fieldset>
			<legend>Color Mixing In Knitting Plan</legend>
			<div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Color Mixing In Knitting Plan</td>
						<td width="190">
							<? $arr=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_is_color_mixing", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('color_mixing_in_knitting_plan')], "",'','' );
							?>
						</td>
						<td width="130" align="left" id="auto_allocate_coller_and_cuff">Collar and Cuff Wise Program Qty</td>
						<td width="190">
							<? //$arr=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_is_coller_cuff", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('coller_cuf_size_planning')], "",'','' );
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
		</form>	
		<?
	}		
	else if ($type==54)
	{ 
		// Color mixing in knitting plan		
		$nameArray= sql_select("select id, capacity_allocation from variable_settings_production where company_name='$company_id' and variable_list=54 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">	
		<fieldset>
			<legend>Capacity Allocation</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Capacity Allocation</td>
						<td width="190">
							<? $arr=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_capacity_allocation", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('capacity_allocation')], "",'','' );
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
		</form>	
		<?
	}
    else if ($type==155)
	{
		//Pattern Numbering Sequence

		$nameArray= sql_select("SELECT id, company_name,batch_maintained from variable_settings_production where company_name='$company_id' and variable_list=155 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Pattern Numbering Sequence(Cutting)</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="fabric_machine_level">Pattern Numbering Sequence(Cutting)</td>
						<td width="190">
							<? $arr=array(1=>'Size Wise',2=>'A to Z');
							echo create_drop_down( "cbo_pattern_numbering_sequence", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('batch_maintained')], "",'','' );
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
	else if ($type==156) // Age wise Yarn Selection in Yarn Allocation
	{ 
		$nameArray= sql_select("select id, allocation_control, minimum_available_qty, age_limit from variable_settings_production where company_name='$company_id' and variable_list=156 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">	
		<fieldset>
			<legend>Age wise Yarn Selection in Yarn Allocation</legend>
			<div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellpadding="2" cellspacing="0" width="100%" class="rpt_table" id="tbl_variable_list" >
					<thead>
					<tr>
						<th width="170">Allocation Control</th>
						<th width="170">Minimum Available Qty (Kg)</th>
						<th width="170">Age Limit (Days)</th>
					</tr>
					</thead>     
					<tbody> 
						<tr> 
							<td width="170" align="center">
								<? $arr=array(1=>'Yes',2=>'No');
								echo create_drop_down( "cbo_allocation_control", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('allocation_control')], "",'','' );
								?> 
							</td>
							<td width="170" align="center">
								<input type="text" name="txt_minimum_available_qty" id="txt_minimum_available_qty" value="<? echo $nameArray[0][csf('minimum_available_qty')]; ?>" class="text_boxes_numeric" style="width:160px"/>
							</td>
							<td width="170" align="center">
								<input type="text" name="txt_age_limit" id="txt_age_limit" value="<? echo $nameArray[0][csf('age_limit')]; ?>" class="text_boxes_numeric" style="width:160px"/>
							</td>
						</tr>
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
							<input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
							<? 
							echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
							?>
						</td>					
					</tr>
				</table>
			</div>
		</fieldset>
		</form>	
		<?
	}

	/* else if ($type==159) // Sewing Planning Quantity Limite
	{ 
		$nameArray= sql_select("select id, sewing_value, sewing_pcq from variable_settings_production where company_name='$company_id' and variable_list=159 order by id");

		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">	
		<fieldset>
			<legend>Sewing Planning Quantity Limit</legend>
			<div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellpadding="1" cellspacing="0" width="100%" class="rpt_table" id="tbl_variable_list" >    

						<tr> 
						<th width="170">Control by PCQ</th>
							<td width="170" align="center">
								<? $arr=array(1=>'Yes',2=>'No');
								echo create_drop_down( "cbo_pcq", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('sewing_pcq')], "enable_disable_plan(this.value)",'','' );
								?> 
								
							</td>
							<th width="170">Value Field</th>
							<td width="170" align="center">
								<input type="text" name="txt_value" id="txt_value" value="<? echo $nameArray[0][csf('sewing_value')]; ?>" class="text_boxes_numeric" style="width:160px"/>
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
		</form>	
		<?
	} */

	else if ($type==159)//Sewing Planning Quantity Limit
	{ 		
		$nameArray= sql_select("select id, sewing_value, sewing_pcq from variable_settings_production where company_name='$company_id' and variable_list=159 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<form id="productionVariableSettings" name="productionVariableSettings">	
		<fieldset>
			<legend>Sewing Planning Quantity Limit</legend>
			<div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Control by PCQ</td>
						<td width="190">
						<? $arr=array(1=>'Yes',2=>'No');
								echo create_drop_down( "cbo_pcq", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('sewing_pcq')], "enable_disable_plan(this.value)",'','' );
								?> 
						</td>
						<td width="130" align="left" id="auto_allocate_coller_and_cuff">Value Field</td>
						<td width="190">
							<?
                              $value = $nameArray[0][csf('sewing_value')];
							  $formattedValue = number_format($value, 2);
							?>
						<input type="text" name="txt_value" id="txt_value" value="<? echo $value; ?>" class="text_boxes_numeric"  style="width:160px"/>%
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
		</form>	
		<?
	}
	
	
	else if ($type==157)
	{ // PLANNING Machine Mixing
		$nameArray= sql_select("select id, machine_mixing from variable_settings_production where company_name='$company_id' and variable_list=157 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Machine Mixing in Planning Sales Order</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Machine Mixing in Planning Sales Order</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_machine_mixing", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('machine_mixing')], "",'','' );
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
	else if ($type==158)
	{ // Auto Balance For Planning Board
		$nameArray= sql_select("select id, auto_balancing from variable_settings_production where company_name='$company_id' and variable_list=158 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Machine Mixing in Planning Sales Order</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="auto_allocate_yarn">Machine Mixing in Planning Sales Order</td>
						<td width="190">
							<? $auto_balancing_arr=array(0=>'Without push',1=>'With Push');
							echo create_drop_down( "cbo_auto_balancing", 170, $auto_balancing_arr,'', 1, '---- Select ----', $nameArray[0][csf('auto_balancing')], "",'','' );
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
	else if ($type==160) //Cut and Lay Fab. Conj. Validation
	{
		$nameArray= sql_select("SELECT id, distribute_qnty from variable_settings_production where company_name='$company_id' and variable_list=160 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Distribute Quantity</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="distribute_qnty">Distribute Quantity</td>
						<td width="190">
							<? $arr=array(1=>'yes',2=>'No');
							echo create_drop_down( "cbo_is_distribute_qnty", 170, $arr,'', 1, '---- Select ----', $nameArray[0][csf('distribute_qnty')], "",'','' );
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
	
	else if ($type==161) //Woven Cut and Lay Country Sequence
	{
		$nameArray= sql_select("SELECT id, country_seq from variable_settings_production where company_name='$company_id' and variable_list=161 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Woven Cut and Lay Country Sequence</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="country_sequence">Maintain Country Sequence</td>
						<td width="190">
							<?$country_sequence=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_country_sequence", 170, $country_sequence,'', 1, '---- Select ----', $nameArray[0][csf('country_seq')], "",'','' );
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
	else if ($type==162) //Woven Cut and Lay Country  Order Priority
	{
		$nameArray= sql_select("SELECT id, order_priority from variable_settings_production where company_name='$company_id' and variable_list=162 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>

		<fieldset>
			<legend>Woven Cut and Lay Order Sequence</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
				<table cellspacing="0" width="100%" >
					<tr> 
						<td width="130" align="left" id="order_priority">Maintain Order Sequence</td>
						<td width="190">
							<?$order_priority=array(1=>'Yes',2=>'No');
							echo create_drop_down( "cbo_order_priority", 170, $order_priority,'', 1, '---- Select ----', $nameArray[0][csf('order_priority')], "",'','' );
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
		else if(str_replace("'","",$cbo_variable_list_production)==11)
		{
			$field_array="id,company_name,variable_list,bulletin_type,smv_editable,inserted_by,insert_date,status_active"; 
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			
			foreach ($bulletin_type_arr as $key => $value) {
				$bulletin_type='txt_bulletin_type_'.$key;
				$is_editiable='cbo_is_editiable_'.$key;
				
				if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$$bulletin_type.",".$$is_editiable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;						
			}
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1); 
			//echo "10**".$data_array;die;

		}
		else if(str_replace("'","",$cbo_variable_list_production)==12)
		{
			$field_array="id,company_name,variable_list,bulletin_type,inserted_by,insert_date,status_active"; 
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			
			if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$txt_bulletin_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;						
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1); 

		}
		else if(str_replace("'","",$cbo_variable_list_production)==154)
		{
			$field_array="id,company_name,variable_list,bulletin_type,inserted_by,insert_date,status_active"; 
			$variable_id = return_next_id( "id", "variable_settings_production", 1);
			
			if($data_array!="") $data_array.=",";
				$data_array.="(".$variable_id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$txt_bulletin_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$variable_id++;						
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1); 

		}
		else if(str_replace("'","",$cbo_variable_list_production)==36)
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
		else if(str_replace("'","",$cbo_variable_list_production)==15 || str_replace("'","",$cbo_variable_list_production)==35)
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
		else if(str_replace("'","",$cbo_variable_list_production)==40)
		{
			$field_array="id, company_name, variable_list,service_process_id,is_serveice_rate_lib, inserted_by,insert_date,status_active"; 
			$id = return_next_id( "id", "variable_settings_production", 1);
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_service_process.",".$cbo_service_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$rID=sql_insert("variable_settings_production",$field_array,$data_array,1);
			//echo "10***insert into variable_settings_production ".$field_array." values ".$data_array;die;
		}
		
		else if(str_replace("'","",$cbo_variable_list_production)==4)
		{
			sql_select("delete from variable_settings_production where variable_list=$cbo_variable_list_production");
			$id=return_next_id( "id", "variable_settings_production", 1 ) ;
			$field_array="id, company_name,variable_list, fabric_machine_level_hcode, planning_board_strip_caption, inserted_by,insert_date,status_active"; 
			$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$fabric_machine_level_html."',".$cbo_fabric_machine_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			 //echo "10***insert into variable_settings_production (".$field_array.") values ".$data_array;die;
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
					$field_array="id, company_name,variable_list,cutting_update_hcode,cutting_update,cutting_input_hcode,cutting_input,printing_emb_production_hcode, printing_emb_production, sewing_production_hcode, sewing_production, iron_update_hcode, iron_update, finishing_update_hcode, finishing_update, ex_factory, production_entry_hcode, production_entry, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$cutting_update_html."',".$cbo_cutting_update.",'".$cutting_delevary_input_html."',".$cbo_cutting_update_to_input.",'".$printing_emb_production_html."',".$cbo_printing_emb_production.",'".$sewing_production_html."',".$cbo_sewing_production.",'".$iron_update_html."',".$cbo_iron_update.",'".$finishing_update_html."',".$cbo_finishing_update.",".$cbo_ex_factory.",'".$production_entry_html."',".$cbo_production_entry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==32)
				{
					$field_array="id, company_name,variable_list, cut_panel_delevery_hcode, cut_panel_delevery, inserted_by,insert_date,status_active,
					is_deleted"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$cut_panel_basis_html."',".$cbo_cut_panel_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
				
				else if(str_replace("'","",$cbo_variable_list_production)==5)
				{
					$field_array="id, company_name,variable_list, distribute_qnty_hcode, distribute_qnty, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$distribute_qnty_html."',".$cbo_is_distribute_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==9 || str_replace("'","",$cbo_variable_list_production)==10)
				{
					$field_array="id, company_name,variable_list, work_study_integrated,smv_type,inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_is_work_study.",".$cbo_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==6)
				{
					$field_array="id, company_name,variable_list, auto_allocate_yarn_from_requis, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_is_auto_allocate_yarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==7)
				{
					$field_array="id, company_name,variable_list, rms_integretion, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_is_rms_integretion.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==8)
				{
					$field_array="id, company_name,variable_list, color_type_mandatory, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_is_color_mandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
				else if(str_replace("'","",$cbo_variable_list_production)==24)
				{
					$field_array="id, company_name,variable_list, batch_no_creation, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_batch_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
					$field_array="id, company_name, variable_list, cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update, inserted_by, insert_date, status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_cutting_update.",".$cbo_printing_emb_production.",".$cbo_sewing_production.",".$cbo_iron_update.",".$cbo_finishing_update.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
					$field_array="id,company_name,variable_list,process_costing_maintain,inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_process_costing.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==37 || str_replace("'","",$cbo_variable_list_production)==39)
				{
					$field_array="id, company_name, variable_list, smv_source, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==38)
				{
					$field_array="id, company_name, variable_list, is_control, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
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
				else if(str_replace("'","",$cbo_variable_list_production)==53)
				{
					$field_array="id, company_name,variable_list, color_mixing_in_knitting_plan,coller_cuf_size_planning, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_is_color_mixing.",".$cbo_is_coller_cuff.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

				}
				
				else if(str_replace("'","",$cbo_variable_list_production)==54)
				{
					$field_array="id, company_name,variable_list, capacity_allocation, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_capacity_allocation.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				
				else if(str_replace("'","",$cbo_variable_list_production)==155)
				{
					$field_array="id, company_name,variable_list,batch_maintained, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_pattern_numbering_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==156)
				{
					$field_array="id,company_name,variable_list,allocation_control,minimum_available_qty,age_limit,inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_allocation_control.",".$txt_minimum_available_qty.",".$txt_age_limit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

				}

				else if(str_replace("'","",$cbo_variable_list_production)==159)
				{
					$field_array="id,company_name,variable_list,sewing_pcq,sewing_value,inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_pcq.",".$txt_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

				}

				else if(str_replace("'","",$cbo_variable_list_production)==157)
				{
					$field_array="id, company_name,variable_list, machine_mixing, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_machine_mixing.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}	
				else if(str_replace("'","",$cbo_variable_list_production)==158)
				{
					$field_array="id, company_name,variable_list, auto_balancing, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_auto_balancing.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}				
				else if(str_replace("'","",$cbo_variable_list_production)==160)
				{
					$field_array="id, company_name,variable_list, distribute_qnty_hcode, distribute_qnty, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",'".$distribute_qnty_html."',".$cbo_is_distribute_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}	
				else if(str_replace("'","",$cbo_variable_list_production)==161)
				{
					$field_array="id, company_name,variable_list, country_seq, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_country_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==162)
				{
					$field_array="id, company_name,variable_list, order_priority, inserted_by,insert_date,status_active"; 
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_order_priority.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}		

				
				// echo "10**insert into variable_settings_production ($field_array) values $data_array";die;

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
				echo "0**".$rID."**".$id;
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
			$field_array="company_name*variable_list*cutting_update_hcode*cutting_update*cutting_input_hcode*cutting_input*printing_emb_production_hcode*printing_emb_production*sewing_production_hcode*sewing_production*iron_update_hcode*iron_update*finishing_update_hcode*finishing_update*ex_factory*production_entry_hcode*production_entry*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$cutting_update_html."'*".$cbo_cutting_update."*'".$cutting_delevary_input_html."'*".$cbo_cutting_update_to_input."*'".$printing_emb_production_html."'*".$cbo_printing_emb_production."*'".$sewing_production_html."'*".$cbo_sewing_production."*'".$iron_update_html."'*".$cbo_iron_update."*'".$finishing_update_html."'*".$cbo_finishing_update."*".$cbo_ex_factory."*'".$production_entry_html."'*".$cbo_production_entry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
		else if(str_replace("'","",$cbo_variable_list_production)==4)
		{
			$field_array="company_name*variable_list*fabric_machine_level_hcode*planning_board_strip_caption*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$fabric_machine_level_html."'*".$cbo_fabric_machine_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==5)
		{
			$field_array="company_name*variable_list*distribute_qnty_hcode*distribute_qnty*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$distribute_qnty_html."'*".$cbo_is_distribute_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==6)
		{
			$field_array="company_name*variable_list*auto_allocate_yarn_from_requis*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_is_auto_allocate_yarn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==7)
		{
			$field_array="company_name*variable_list*rms_integretion*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_is_rms_integretion."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==8)
		{
			$field_array="company_name*variable_list*color_type_mandatory*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_is_color_mandatory."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==9 || str_replace("'","",$cbo_variable_list_production)==10)
		{
			$field_array="company_name*variable_list*work_study_integrated*smv_type*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_is_work_study."*".$cbo_smv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}		
		else if(str_replace("'","",$cbo_variable_list_production)==11)
		{
			$field_array="bulletin_type*smv_editable*updated_by*update_date"; 
			foreach ($bulletin_type_arr as $key => $value) {
				$is_editiable='cbo_is_editiable_'.$key;
				$bulletin_type='txt_bulletin_type_'.$key;
				$update_id='update_id_'.$key;
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_array_update[str_replace("'",'',$$update_id)] = explode("*",($$bulletin_type."*".$$is_editiable."*".$user_id."*'".$pc_date_time."'"));
			}
		
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array_update,$id_arr),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==12)
		{
			$field_array="bulletin_type*updated_by*update_date";
			$data_array="".str_replace("'","",$txt_bulletin_type)."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==154)
		{
			$field_array="bulletin_type*updated_by*update_date";
			$data_array="".str_replace("'","",$txt_bulletin_type)."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==13)
		{
			$field_array="company_name*variable_list*batch_maintained_hcode*batch_maintained*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$batch_maintained_html."'*".$cbo_batch_maintained."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==15 || str_replace("'","",$cbo_variable_list_production)==35)
		{
			$field_array="company_name*variable_list*item_category_id*auto_update*updated_by*update_date"; 
			
			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);
			
			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_1."*".$cbo_auto_update_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category_2."*".$cbo_auto_update_2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==23)
		{
			$field_array="company_name*variable_list*auto_update*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_prod_resource."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==24)
		{
			$field_array="company_name*variable_list*batch_no_creation*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_batch_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
			$field_array="company_name*variable_list*cutting_update*printing_emb_production*sewing_production*iron_update*finishing_update*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_cutting_update."*".$cbo_printing_emb_production."*".$cbo_sewing_production."*".$cbo_iron_update."*".$cbo_finishing_update."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
		else if(str_replace("'","",$cbo_variable_list_production)==32)
		{
			$field_array="company_name*variable_list*cut_panel_delevery_hcode*cut_panel_delevery*updated_by*update_date*status_active*is_deleted"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$cut_panel_basis_html."'*".$cbo_cut_panel_basis."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==33)
		{
			$field_array="company_name*variable_list*page_category_id*is_control*preceding_page_id*updated_by*update_date"; 
			
			$updateID_array[]=str_replace("'","",$update_id);
			
			$data_array[str_replace("'","",$update_id)]=explode("*",("".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_category."*".$cbo_auto_update."*".$cbo_preceding_item_category."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			//echo "10**".bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array);die;
			$rID=execute_query(bulk_update_sql_statement("variable_settings_production","id",$field_array,$data_array,$updateID_array),1);
			//echo "10**0".$rID;die;
		}
		else if(str_replace("'","",$cbo_variable_list_production)==34)
		{
			$field_array="company_name*variable_list*process_costing_maintain*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_process_costing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		} 
		else if(str_replace("'","",$cbo_variable_list_production)==36)
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
		else if(str_replace("'","",$cbo_variable_list_production)==37 || str_replace("'","",$cbo_variable_list_production)==39)
		{
			$field_array="company_name*variable_list*smv_source*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==38)
		{
			$field_array="company_name*variable_list*is_control*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
		else if(str_replace("'","",$cbo_variable_list_production)==53)
		{
			$field_array="company_name*variable_list*color_mixing_in_knitting_plan*coller_cuf_size_planning*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_is_color_mixing."*".$cbo_is_coller_cuff."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==54)
		{
			$field_array="company_name*variable_list*capacity_allocation*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_capacity_allocation."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==155)
		{
			$field_array="company_name*variable_list*batch_maintained*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_pattern_numbering_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==156)
		{
			$field_array="company_name*variable_list*allocation_control*minimum_available_qty*age_limit*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_allocation_control."*".$txt_minimum_available_qty."*".$txt_age_limit."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==159)
		{
			$field_array="company_name*variable_list*sewing_pcq*sewing_value*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_pcq."*".$txt_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}

		else if(str_replace("'","",$cbo_variable_list_production)==157)
		{
			$field_array="company_name*variable_list*machine_mixing*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_machine_mixing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==158)
		{
			$field_array="company_name*variable_list*auto_balancing*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_auto_balancing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==160)
		{
			$field_array="company_name*variable_list*distribute_qnty_hcode*distribute_qnty*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*'".$distribute_qnty_html."'*".$cbo_is_distribute_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==161)
		{
			$field_array="company_name*variable_list*country_seq*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_country_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_settings_production",$field_array,$data_array,"id","".$update_id."",1);

		}
		else if(str_replace("'","",$cbo_variable_list_production)==162)
		{
			$field_array="company_name*variable_list*order_priority*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_order_priority."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
				echo "1**".$rID."**".str_replace("'","",$update_id);
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
		$fixed_index="29,116";			
	}
	else if($data[1]==31) // Packing And Finishing or cartoon
	{
		$fixed_index="103,30,29,116";			
	}
	else if($data[1]==32) // Ex-Factory
	{
		$fixed_index="31,91";			
	}
	else if($data[1]==91) // Inspection
	{
		$fixed_index="31";			
	}

	else if($data[1]==103) // Poly Entry
	{
		$fixed_index="29,30,116";			
	}

	else if($data[1]==116) // Finishing Input
	{
		$fixed_index="29";			
	}


//}
	echo create_drop_down( "cbo_preceding_item_category", 150, $report_signeture_list,'',1, "---Plan Cut---", '', '', "",$fixed_index );

}

?>