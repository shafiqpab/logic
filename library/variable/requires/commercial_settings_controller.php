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
	
	if($type=="5") // Commercial_Garments Export Capacity
	{
	
		$nameArray=sql_select( "select id,capacity_in_value,currency_id from  variable_settings_commercial where company_name='$company_id' and variable_list=5 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
			<legend>Garments Export Capacity</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
				<table cellspacing="0" width="100%" >
                    <tr> 
                        <td width="130" align="left" id="capacity_in_value">Capacity In Value</td>
                        <td width="190" align="left">
                        	<input type="text" name="txt_capacity_value" id="txt_capacity_value" value="<? echo $nameArray[0][csf('capacity_in_value')]; ?>" class="text_boxes_numeric" style="width:158px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="left" id="currency">Currency</td>
                        <td width="190">
                           		<? 
									echo create_drop_down( "cbo_currency_id", 170, $currency,'', 1, '---- Select -----', $nameArray[0][csf('currency_id')], "" );
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
								echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','')",1);
                            ?>
                        </td>					
                    </tr>
				</table>
			</div>
		</fieldset>
        <?				
			
	}

	if( $type==6 ) //Commercial_Max BTB Limit
	{
			 
		$nameArray=sql_select( "select id,max_btb_limit,currency_id, cost_heads_status, pi_source_btb_lc from  variable_settings_commercial where company_name='$company_id' and variable_list=6 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
			<legend>Max BTB Limit</legend>
			<div style="width:750px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
				<table cellspacing="0" width="100%" >
                    <tr> 
                        <td width="100" align="left" id="max_btb_limit">BTB Control</td>
                        <td width="150" align="left">
                        	<?
							echo create_drop_down( "cbo_contorll_status", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("cost_heads_status")], "fn_btb_data_source(this.value)" );
							?>
                        </td>
                        <td width="100" align="left" id="max_btb_limit">Date Source</td>
                        <td width="150" align="left">
                        	<?
							$btb_data_source=array(1=>"Library", 2=>"LC/SC");
							echo create_drop_down( "cbo_data_source", 130, $btb_data_source,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "fn_btb_limit(this.value)", 1 );
							?>
                        </td>
                        <td width="100" align="left" id="max_btb_limit">Limit</td>
                        <td align="left">
                        	<input type="text" name="txt_max_btb_limit" id="txt_max_btb_limit" value="<? echo $nameArray[0][csf('max_btb_limit')]; ?>" class="text_boxes_numeric" style="width:110px;" disabled="disabled"/>%
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
								echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','')",1);
                            ?>
                        </td>					
                    </tr>
				</table>
			</div>
		</fieldset>
        <?    				
			
	}

	if( $type==7 ) //Commercial_Max PC Limit
	{
	
		$nameArray=sql_select( "select id,max_pc_limit,currency_id from  variable_settings_commercial where company_name='$company_id' and variable_list=7 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
			<legend>Max PC Limit</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
				<table cellspacing="0" width="100%" >
                    <tr> 
                        <td width="130" align="left" id="max_pc_limit">Max PC Limit</td>
                        <td width="190" align="left">
                        	<input type="text" name="txt_max_pc_limit" id="txt_max_pc_limit" value="<? echo $nameArray[0][csf('max_pc_limit')]; ?>" class="text_boxes_numeric" style="width:158px;"/>%
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
								echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','')",1);
                            ?>
                        </td>					
                    </tr>
				</table>
			</div>
		</fieldset>
        <?    				
			
	}

	if( $type==17 )//Commercial Possible heads for BTB
	{
			
		/*$nameArray=sql_select( "select id,company_name,cost_heads,cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=17 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;*/
		$sql="select id,company_name,cost_heads,cost_heads_status,variable_list from variable_settings_commercial where company_name='$company_id' and variable_list=17 order by id" ;
		?>
            <div>
               <fieldset>
                    <legend>Possible Heads For BTB</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table">
                            <thead> 
                                <th width="200" align="left">Cost Heads</th>
                                <th width="190" align="left">Status</th>
                            </thead>                                
                                <tr align="center">
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_cost_heads", 155, $cost_heads_for_btb,'', 1, '---- Select ----', "0", "" );
                                            ?>
                                    </td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_cost_heads_status", 130, $yes_no,'', 1, '---- Select ----',"0", "" );
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
                                    <input  type="hidden" name="update_id" id="update_id" value="">
									<? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", 0,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>					
                            </tr>
                        </table>                            
                            <?
                            	//create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path , $filter_grid_fnc, $fld_type_arr )
						$com_name=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
						$arr=array (0=>$com_name,1=>$cost_heads_for_btb,2=>$yes_no);
						echo  create_list_view ( "list_view", "Company Name,Cost Heads,Status", "150,150,150","470","220",0, $sql, "get_php_form_data", "id,variable_list", "'load_php_data_to_form'", 1, "company_name,cost_heads,cost_heads_status", $arr , "company_name,cost_heads,cost_heads_status", "requires/commercial_settings_controller", 'setFilterGrid("list_view",-1);' ) ;
								
						?>   
                    </div>
                   
                </fieldset>
            </div>
        <?    				
			
	}

	if( $type==18 )//Commercial Invoice Rate Controll
	{
			
		$nameArray=sql_select( "select id, cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=18 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Invoice Rate Controll</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;">Is Disable: &nbsp;</td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_rate_status", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("cost_heads_status")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}
	
	if( $type==33 || $type==36)//Commercial LC Rate Controll
	{
			
		$nameArray=sql_select( "select id, cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=$type order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Invoice Rate Controll</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;"><? if($type==33) echo "Is Editable:"; else echo "Category mixing:";?> &nbsp;</td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_rate_status", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("cost_heads_status")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}
	
	if( $type==19 )//Document Monitoring Standard Controll
	{
			
		$nameArray=sql_select( "select id, monitor_head_id, monitoring_standard_day from  variable_settings_commercial where company_name='$company_id' and variable_list=19 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Document Monitoring Standard</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1" id="tbl_monitor">
                        	<thead>
                            	<tr>
                                	<th width="150">Monitoring Head</th>
                                    <th width="150">Monitoring Standard Day</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="details_part_list">
                            <?
							if(count($nameArray)>0)
							{
								$i=1;
								foreach($nameArray as $row)
								{
									?>
                                    <tr id="tr_<? echo $i;?>">
                                        <td align="center">
                                        <? 
                                            $monitor_head=array(1=>"BL Standard",2=>"GSP Standard",3=>"CO Standard");
                                            echo create_drop_down( "monitorhead_".$i, 140, $monitor_head,'', 1, '---- Select ----',$row[csf("monitor_head_id")], "" );
                                        ?>
                                        <input type="hidden" name="hiderow_<? echo $i;?>" id="hiderow_<? echo $i;?>" class="text_boxes" value="0" style="width:70px;" />
                                        </td>
                                        <td align="center">
                                        <input type="text" id="txtmonday_<? echo $i;?>" name="txtmonday_<? echo $i;?>" class="text_boxes_numeric" style="width:140px"	onClick="add_break_down_tr(<? echo $i;?>)" value="<? echo $row[csf("monitoring_standard_day")]; ?>" />											
                                        </td>
                                        <td>
                                        <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
                                        </td>
                                     </tr> 
                                     <?
									 $i++;
								}
								?>
                                <tr id="tr_<? echo $i;?>">
                                    <td align="center">
                                    <? 
                                    $monitor_head=array(1=>"BL Standard",2=>"GSP Standard",3=>"CO Standard");
                                    echo create_drop_down( "monitorhead_".$i, 140, $monitor_head,'', 1, '---- Select ----',0, "" );
                                    ?>
                                    <input type="hidden" name="hiderow_<? echo $i;?>" id="hiderow_<? echo $i;?>" class="text_boxes" value="0" style="width:70px;" />
                                    </td>
                                    <td align="center">
                                    <input type="text" id="txtmonday_<? echo $i;?>" name="txtmonday_<? echo $i;?>" class="text_boxes_numeric" style="width:140px"	onClick="add_break_down_tr(<? echo $i;?>)" />											
                                    </td>
                                    <td>
                                    <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
                                    </td>
                                </tr>
                                <?
							}
							else
							{
								?>
                            	<tr id="tr_1">
                                    <td align="center">
                                    <? 
										$monitor_head=array(1=>"BL Standard",2=>"GSP Standard",3=>"CO Standard");
                                        echo create_drop_down( "monitorhead_1", 140, $monitor_head,'', 1, '---- Select ----',0, "" );
                                    ?>
                        			<input type="hidden" name="hiderow_1" id="hiderow_1" class="text_boxes" value="0" style="width:70px;" />
                                    </td>
                                    <td align="center">
									<input type="text" id="txtmonday_1" name="txtmonday_1" class="text_boxes_numeric" style="width:140px"	onClick="add_break_down_tr(1)" />											
                                    </td>
                                    <td>
                                    <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1);" />
                                    </td>
                                 </tr> 
                                 <?
							}
							?>
                            </tbody>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}
	
	if( $type==20 )//Commercial Internal File Source
	{
			
		$nameArray=sql_select( "select id, internal_file_source from  variable_settings_commercial where company_name='$company_id' and variable_list=20 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Internal File Source</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;">Source From: &nbsp;</td>
                                    <td>
                                            <?
                                                $source_arr = array(1=>"Order Entry", 2=>"Manual", 3=>"Library");
                                                echo create_drop_down( "cbo_file_status", 130, $source_arr,'', 1, '---- Select ----',$nameArray[0][csf("internal_file_source")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}
	
	if( $type==21 )//Attach Approved PI Controll
	{
			
		$nameArray=sql_select( "select id, attach_approval_pi from  variable_settings_commercial where company_name='$company_id' and variable_list=21 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Approved PI Controll</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;">Status: &nbsp;</td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_file_status", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("attach_approval_pi")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}

	if( $type==22 )//Export Invoice Qty Source
	{
			
		$nameArray=sql_select( "select id, export_invoice_qty_source from  variable_settings_commercial where company_name='$company_id' and variable_list=22 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Invoice Qty Source</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                        	<thead>
                                <tr>
                                    <th align="right" style="width:150px;">Source : &nbsp;</th>
                                    <th>
                                            <? 
                                                echo create_drop_down( "cbo_file_status", 150, $export_invoice_qty_source,'', 1, '---- Select ----',$nameArray[0][csf("export_invoice_qty_source")], "" );
                                            ?>												
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?    				
			
	}

	if( $type==23 )//after goods receive data source
	{
			
		$nameArray=sql_select( "select id, export_invoice_qty_source from  variable_settings_commercial where company_name='$company_id' and variable_list=23 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>After Goods Receive Data source</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                        	<thead>
                                <tr>
                                    <th align="right" style="width:150px;">Source : &nbsp;</th>
                                    <th>
                                            <?
												$goods_receive_source=array(1=>"Work Order",2=>"Receive"); 
                                                echo create_drop_down( "cbo_file_status", 150, $goods_receive_source,'', 1, '---- Select ----',$nameArray[0][csf("export_invoice_qty_source")], "" );
                                            ?>												
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?    				
			
	}
	
	if( $type==24 )//Yarn Purchase Order Controll
	{
			
		$nameArray=sql_select( "select id, export_invoice_qty_source from  variable_settings_commercial where company_name='$company_id' and variable_list=24 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Yarn Purchase Order Controll</legend>
                    <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                        	<thead>
                                <tr>
                                    <th align="right" style="width:250px;">Check Buyer PO With LC/SC : &nbsp;</th>
                                    <th>
                                            <?													
                                                echo create_drop_down( "cbo_file_status", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("export_invoice_qty_source")], "" );
                                            ?>												
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?    				
			
	}

    if( $type==25 ) // PI Source BTB LC
    {            
        $nameArray=sql_select( "SELECT id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=25 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>PI Source BTB LC</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;">Source : &nbsp;</th>
                                    <th>
                                        <?
                                            $btb_source=array(1=>"PI",2=>"Office Note"); 
                                            echo create_drop_down( "cbo_pi_source", 150, $btb_source,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }

	if($type==26 )//Commission source at Export Invoice
	{
			
		//echo "select id,export_invoice_qty_source   from  variable_settings_commercial where company_name='$company_id' and variable_list=26 order by id" ;
		
		$nameArray=sql_select( "select id,export_invoice_qty_source   from  variable_settings_commercial where company_name='$company_id' and variable_list=26 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Source At Export Invoice</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                        	<thead>
                                <tr>
                                    <th align="right" style="width:150px;">Source : &nbsp;</th>
                                    <th>
                                            <? 
                                                echo create_drop_down( "cbo_file_status", 150, $commission_source_at_export_invoice,'', 1, '---- Select ----',$nameArray[0][csf("export_invoice_qty_source")], "" );
                                            ?>												
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                          // echo  $permission=$_SESSION['page_permission']; die;
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?    				
			
	}

    if($type==27 )//Export PI No From System
    {
            
        //echo "select id,pi_source_btb_lc  from  variable_settings_commercial where company_name='$company_id' and variable_list=27 order by id" ;
        //echo 'system';die;
        
        $nameArray=sql_select( "select id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=27 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Export PI No From System</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;">Export PI No From System : &nbsp;</th>
                                    <th>
                                            <? 
                                                echo create_drop_down( "cbo_export_pino_status", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                            ?>                                              
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                          // echo  $permission=$_SESSION['page_permission']; die;
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','',1)");
                                        ?>
                                    </td>                   
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                  
            
    }
	
	if($type==28 )//Yarn Purchase Order Rate Control With Budget
    {
            
        //echo "select id,pi_source_btb_lc  from  variable_settings_commercial where company_name='$company_id' and variable_list=27 order by id" ;
        //echo 'system';die;
        
        $nameArray=sql_select( "select id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=28 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Yarn Purchase Order Rate Control With Budget</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;">Yarn Purchase Order Rate Control With Budget : &nbsp;</th>
                                    <th>
                                            <? 
                                                echo create_drop_down( "cbo_export_pino_status", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                            ?>                                              
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                          // echo  $permission=$_SESSION['page_permission']; die;
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','',1)");
                                        ?>
                                    </td>                   
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                  
            
    }

    if( $type==29 ) // Commercial Office Note Signature Source
    {            
        $nameArray=sql_select( "SELECT id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=29 order by id" );
        if(count($nameArray)>0) $is_update=1; 
        else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Office Note Signature Source</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;">Source : &nbsp;</th>
                                    <th>
                                        <?
                                            $com_office_note_source=array(1=>"From Library",2=>"From Approval");
                                            echo create_drop_down( "cbo_office_note_source", 150, $com_office_note_source,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }

    if( $type==30 ) // Control PI Sent for Approval Without SC/LC
    {            
        $sql="select id, company_name, item_category, pi_source_btb_lc, variable_list from variable_settings_commercial where company_name='$company_id' and variable_list=30 order by id";
        ?>
            <div>
               <fieldset>
                    <legend>Check Buyer PO From Attached WO</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table">
                            <thead> 
                                <th width="200" align="left">Item Category</th>
                                <th width="190" align="left">Do Control</th>
                            </thead>                                
                                <tr align="center">
                                    <td>
                                        <? 
                                            echo create_drop_down( "cbo_item_category", 155, $item_category,'', 1, '---- Select ----', "0", "", "", "1,2,3,4,12,25,24,31" );
                                        ?>
                                    </td>
                                    <td>
                                        <? 
                                            echo create_drop_down( "cbo_do_control", 130, $yes_no,'', 1, '---- Select ----',"0", "" );
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
                                    <input  type="hidden" name="update_id" id="update_id" value="">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", 0,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>
                        </table>                            
                        <?
                        $com_name=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
                        $arr=array (0=>$com_name,1=>$item_category,2=>$yes_no);
                        echo  create_list_view ( "list_view", "Company Name,Item Category,Do Control", "150,150,150","470","220",0, $sql, "get_php_form_data", "id,variable_list", "'load_php_data_to_form'", 1, "company_name,item_category,pi_source_btb_lc", $arr , "company_name,item_category,pi_source_btb_lc", "requires/commercial_settings_controller", 'setFilterGrid("list_view",-1);' ) ;
                                
                        ?>   
                    </div>                   
                </fieldset>
            </div>
        <?                          
    }

    if( $type==31 ) // Control PI Entry After Last Ship Date
    {            
        $nameArray=sql_select( "SELECT id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=31 order by id" );
        if(count($nameArray)>0) $is_update=1; 
        else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Control PI Entry After Last Ship Date</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;" title="Category Yarn, Knit Finish Fabric, Woven Fabric, Accessories, Services-embellishment, Services-yarn dyeing, Services-lab test, Services-fabric">From SC/LC (Attached from Internal File field) : &nbsp;</th>
                                    <th>
                                        <?
                                            echo create_drop_down( "cbo_sc_lc_attachInternalFile", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }

    if( $type==32 ) // Sales Contract Entry Contract Number
    {            
        $nameArray=sql_select( "SELECT id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=32 order by id" );
        if(count($nameArray)>0) $is_update=1; 
        else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Sales Contract Entry Contract Number</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;" title="Company Name/Buyer Name/SC Number/Month Year">Contract Number Status: &nbsp;</th>
                                    <th>
                                        <?
                                            echo create_drop_down( "cbo_contract_number_status", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }

    if($type==35) // General Category Budget Validation
    {
        $nameArray=sql_select("select id,budget_validation_status,budget_validation_page from  variable_settings_commercial where company_name='$company_id' and variable_list=35 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>General Category Budget Validation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="left" id="budget_validation">Category Budget validation</td>
                        <td width="190" align="left">
                            <?
                            echo create_drop_down( "txt_budget_value", 170, $yes_no,'', 1, '-- Select --', $nameArray[0]["BUDGET_VALIDATION_STATUS"], "budget_validation_change()" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="left" id="validate_with">Validate with</td>
                        <td width="190">
                            <?
                            $arr = array(1 => "Purchase Requisition", 2 => "Purchase Order", 3 => "Proforma Invoice");
                            echo create_drop_down( "validate_page_name", 170, $arr,'', 1, '-- Select --',  $nameArray[0]["BUDGET_VALIDATION_PAGE"], "" );
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
                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?

    }

    if( $type==37 ) // Actual Cost Entry
    {            
        $nameArray=sql_select( "SELECT id, actual_cost_status from variable_settings_commercial where company_name='$company_id' and variable_list=37 order by id" );
        if(count($nameArray)>0) $is_update=1; 
        else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Actual Cost Entry</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;">CM Value Integrated with Accounts : &nbsp;</th>
                                    <th>
                                        <?
                                            $com_office_note_source=array(1=>"Yes",2=>"No");
                                            echo create_drop_down( "cbo_actual_cost_source", 150, $com_office_note_source,'', 1, '---- Select ----',$nameArray[0][csf("actual_cost_status")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }
    if($type==38 )//Export PI No From System
    {
            
        //echo "select id,pi_source_btb_lc  from  variable_settings_commercial where company_name='$company_id' and variable_list=27 order by id" ;
        //echo 'system';die;
        
        $nameArray=sql_select( "select id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=38 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend> Validation With</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;"> Validation With: &nbsp;</th>
                                    <th>
                                        <? 
                                            echo create_drop_down( "cbo_export_pino_status", 150, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("pi_source_btb_lc")], "" );
                                        ?>                                              
                                    </th>
                                 </tr> 
                                 </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                          // echo  $permission=$_SESSION['page_permission']; die;
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('commercialvariablesettings_1','','',1)");
                                        ?>
                                    </td>                   
                                </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                  
            
    }

    if( $type==39 )//Export Invoice using new actual po
	{
			
		$nameArray=sql_select( "select id, cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=39 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Invoice Rate Controll</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;">Is Disable: &nbsp;</td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_rate_status", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("cost_heads_status")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}

    if( $type==40 )//Commercial Invoice Rate Controll
	{
			
		$nameArray=sql_select( "select id, cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=40 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
            <div>
               <fieldset>
                    <legend>Buyer Mixing Allowed in Yarn Procurement</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                <tr>
                                    <td align="right" style="width:150px;">Is Allowed: &nbsp;</td>
                                    <td>
                                            <? 
                                                echo create_drop_down( "cbo_mixing_allowed", 130, $yes_no,'', 1, '---- Select ----',$nameArray[0][csf("cost_heads_status")], "" );
                                            ?>												
                                    </td>
                                 </tr> 
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                                 <tr>
                                   <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                        <? 
                                            $permission=$_SESSION['page_permission'];
                                            echo load_submit_buttons( $permission, "fnc_variable_settings_commercial", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)");
                                        ?>
                                    </td>					
                                </tr>  
                        </table>
                    </div>
                    
                   
                </fieldset>
            </div>
        <?    				
			
	}
	exit();
	
}


if ($action=="list_view")
{
	echo  create_list_view ( "list_view", "Company Name,Cost Heads,Status", "","470","220",0, "select id,company_name,cost_heads,cost_heads_status from  variable_settings_commercial where company_name='$company_id' and variable_list=17 order by id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,sample_type,status_active", '' , "sample_name,sample_type,status_active", "requires/commercial_settings_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if($action=="load_php_data_to_form")
{
    list($id, $variable_list) = explode("_", $data);

    if ($variable_list==17)
    {
        $sql="select cost_heads,cost_heads_status from variable_settings_commercial where id=$id";
        $data_array=sql_select($sql);
        foreach ($data_array as $row)
        {
            echo "document.getElementById('cbo_cost_heads').value = '".$row[csf("cost_heads")]."';\n";  
            echo "document.getElementById('cbo_cost_heads_status').value = '".$row[csf("cost_heads_status")]."';\n";  
            echo "document.getElementById('update_id').value = '".$id."';\n";  
            
            echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_commercial',1);\n";    
        }
    }
    else if ($variable_list==30)
    {
        $sql="select item_category,pi_source_btb_lc from variable_settings_commercial where id=$id";
        $data_array=sql_select($sql);
        foreach ($data_array as $row)
        {
            echo "document.getElementById('cbo_item_category').value = '".$row[csf("item_category")]."';\n";  
            echo "document.getElementById('cbo_do_control').value = '".$row[csf("pi_source_btb_lc")]."';\n";  
            echo "document.getElementById('update_id').value = '".$id."';\n";  
            
            echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_commercial',1);\n";    
        }
    }  
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST);
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="";
		$data_array="";
		$id=return_next_id( "id", "variable_settings_commercial", 1 ) ;
		if($cbo_variable_list=="'5'")
		{
			$field_array="id,company_name,variable_list,capacity_in_value_hcode,capacity_in_value,currency_hcode,currency_id,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",'".$capacity_in_value."',".$txt_capacity_value.",'".$currency."',".$cbo_currency_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		if($cbo_variable_list=="'6'")
		{
			$field_array="id,company_name,variable_list,max_btb_limit_hcode,max_btb_limit,cost_heads_status,pi_source_btb_lc,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",'".$max_btb_limit."',".$txt_max_btb_limit.",".$cbo_contorll_status.",".$cbo_data_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		if($cbo_variable_list=="'7'")
		{
			$field_array="id,company_name,variable_list,max_pc_limit_hcode,max_pc_limit,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",'".$max_pc_limit."',".$txt_max_pc_limit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		if($cbo_variable_list=="'17'")
		{
			if (is_duplicate_field( "company_name", "variable_settings_commercial", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and cost_heads=$cbo_cost_heads" ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			
			$field_array="id,company_name,variable_list,cost_heads,cost_heads_status,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_cost_heads.",".$cbo_cost_heads_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		if($cbo_variable_list=="'18'" || $cbo_variable_list=="'33'" || $cbo_variable_list=="'36'")
		{
			$field_array="id,company_name,variable_list,cost_heads_status,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_rate_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
        if($cbo_variable_list=="'40'")
		{
			$field_array="id,company_name,variable_list,cost_heads_status,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_mixing_allowed.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		if($cbo_variable_list=="'19'")
		{
			$field_array="id,company_name,variable_list,monitor_head_id,monitoring_standard_day,inserted_by,insert_date,status_active";
			for($i=1; $i<=$total_row; $i++)
			{
				$monitorhead="monitorhead_".$i;
				$txtmonday="txtmonday_".$i;
				$hiderow="hiderow_".$i;
				
				if(str_replace("'","",$$hiderow)!=1)
				{
					if(str_replace("'","",$$txtmonday)>0)
					{
						if ($i!=1) $data_array .=",";
						$data_array .="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$monitorhead.",".$$txtmonday.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
						$id=$id+1;
					}
				}
			
			}
		}
		
		if($cbo_variable_list=="'20'")
		{
			$field_array="id,company_name,variable_list,internal_file_source,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_file_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		
		if($cbo_variable_list=="'21'")
		{
			$field_array="id,company_name,variable_list,attach_approval_pi,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_file_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}	
		
		if($cbo_variable_list=="'22'" || $cbo_variable_list=="'23'" || $cbo_variable_list=="'24'" || $cbo_variable_list=="'26'")
		{
			$field_array="id,company_name,variable_list,export_invoice_qty_source,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_file_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}

        if($cbo_variable_list=="'25'")
        {
            $field_array="id,company_name,variable_list,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_pi_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'27'" || $cbo_variable_list=="'28'"  || $cbo_variable_list=="'38'")
        {
            $field_array="id,company_name,variable_list,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_export_pino_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'29'")
        {
            $field_array="id,company_name,variable_list,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_office_note_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'30'")
        {
            if (is_duplicate_field( "company_name", "variable_settings_commercial", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category=$cbo_item_category" ) == 1)
            {
                echo 11; disconnect($con); die;
            }
            
            $field_array="id,company_name,variable_list,item_category,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_do_control.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'31'")
        {
            $field_array="id,company_name,variable_list,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_sc_lc_attachInternalFile.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'32'")
        {
            $field_array="id,company_name,variable_list,pi_source_btb_lc,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_contract_number_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }
        if($cbo_variable_list=="'35'")
        {
            $field_array="id,company_name,variable_list,budget_validation_status,budget_validation_page,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$txt_budget_value.",".$validate_page_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

        if($cbo_variable_list=="'37'")
        {
            $field_array="id,company_name,variable_list,actual_cost_status,inserted_by,insert_date,status_active";
            $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_actual_cost_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
        }

		// print_r($data_array);die;
		$rID=sql_insert("variable_settings_commercial",$field_array,$data_array,1);
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
			
			if($cbo_variable_list=="'5'")
			{
				$field_array="company_name*variable_list*capacity_in_value*currency_id*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$txt_capacity_value."*".$cbo_currency_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}
			else if($cbo_variable_list=="'6'")
			{
				$field_array="company_name*variable_list*max_btb_limit_hcode*max_btb_limit*cost_heads_status*pi_source_btb_lc*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*'".$max_btb_limit."'*".$txt_max_btb_limit."*".$cbo_contorll_status."*".$cbo_data_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
				//echo "update set variable_settings_commercial (".$field_array.") values ".$data_array;die;
			}
			else if($cbo_variable_list=="'7'")
			{
				$field_array="company_name*variable_list*max_pc_limit_hcode*max_pc_limit*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*'".$max_pc_limit."'*".$txt_max_pc_limit."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}
			else if($cbo_variable_list=="'17'")
			{
				if (is_duplicate_field( "company_name", "variable_settings_commercial", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and cost_heads=$cbo_cost_heads and id<>$update_id" ) == 1)
				{
					echo 11; disconnect($con); die;
				}
				$field_array="company_name*variable_list*cost_heads*cost_heads_status*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_cost_heads."*".$cbo_cost_heads_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}
			else if($cbo_variable_list=="'18'" || $cbo_variable_list=="'33'" || $cbo_variable_list=="'36'")
			{
				$field_array="company_name*variable_list*cost_heads_status*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_rate_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}

            else if($cbo_variable_list=="'40'")
			{
				$field_array="company_name*variable_list*cost_heads_status*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_mixing_allowed."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}

			else if($cbo_variable_list=="'19'")
			{
				$delete_row=execute_query("delete variable_settings_commercial where company_name=$cbo_company_name and variable_list=$cbo_variable_list");
				$id=return_next_id( "id", "variable_settings_commercial", 1 ) ;
				$field_array="id,company_name,variable_list,monitor_head_id,monitoring_standard_day,inserted_by,insert_date,status_active";
				for($i=1; $i<=$total_row; $i++)
				{
					$monitorhead="monitorhead_".$i;
					$txtmonday="txtmonday_".$i;
					$hiderow="hiderow_".$i;
					
					if(str_replace("'","",$$hiderow)!=1)
					{
						if(str_replace("'","",$$txtmonday)>0)
						{
							if ($i!=1) $data_array .=",";
							$data_array .="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$monitorhead.",".$$txtmonday.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
							$id=$id+1;
						}
					}
				
				}
				$rID=sql_insert("variable_settings_commercial",$field_array,$data_array,1);
				
			}
			
			else if($cbo_variable_list=="'20'")
			{
				$field_array="company_name*variable_list*internal_file_source*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_file_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}
			else if($cbo_variable_list=="'21'")
			{
				$field_array="company_name*variable_list*attach_approval_pi*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_file_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}
			
			
			else if($cbo_variable_list=="'22'" || $cbo_variable_list=="'23'" || $cbo_variable_list=="'24'" || $cbo_variable_list=="'26'")
			{
				$field_array="company_name*variable_list*export_invoice_qty_source*updated_by*update_date";
				$data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_file_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
			}

            else if($cbo_variable_list=="'25'")
            {
                $field_array="company_name*variable_list*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_pi_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'27'" || $cbo_variable_list=="'28'" || $cbo_variable_list=="'38'")
            {
                $field_array="company_name*variable_list*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_export_pino_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'29'")
            {
                $field_array="company_name*variable_list*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_office_note_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'30'")
            {
                if (is_duplicate_field( "company_name", "variable_settings_commercial", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category=$cbo_item_category and id<>$update_id" ) == 1)
                {
                    echo 11; disconnect($con); die;
                }
                $field_array="company_name*variable_list*item_category*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_do_control."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'31'")
            {
                $field_array="company_name*variable_list*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_sc_lc_attachInternalFile."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'32'")
            {
                $field_array="company_name*variable_list*pi_source_btb_lc*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_contract_number_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            else if($cbo_variable_list=="'37'")
            {
                $field_array="company_name*variable_list*actual_cost_status*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$cbo_actual_cost_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
            }
            if($cbo_variable_list=="'35'")
            {
                $field_array="company_name*variable_list*budget_validation_status*budget_validation_page*updated_by*update_date";
                $data_array=$cbo_company_name."*".$cbo_variable_list."*".$txt_budget_value."*".$validate_page_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $rID=sql_update("variable_settings_commercial",$field_array,$data_array,"id","".$update_id."",1);
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



?>