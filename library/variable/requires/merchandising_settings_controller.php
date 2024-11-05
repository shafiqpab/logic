<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="users_name_list")
{
    echo load_html_head_contents("User Selection Form","../../../",1,1,$unicode,1,'');
    ?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

        function check_all_data(str) {
			 tbl_row_count=str.split(',');
			 for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}
	
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
	
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
	
	</script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="330px" value="" />
    <div>
        <div style="width:200px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                <thead>
                    <th width="48" align="left">SL No</th>
                    <th width="128" align="left">User Name</th>
                </thead>
            </table>
        </div>
        <div style="width:200px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" border="0" width="100%" class="rpt_table" rules="all" id="tbl_list_search" >
            <?php
            $i = 1;
            $nameArray = sql_select("select id,user_name from user_passwd where valid=1");
            //echo "select id,user_name from user_passwd where valid=1";
            foreach ($nameArray as $selectResult) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                
                if (in_array($selectResult[csf('id')], $cu)) {
                	$bgcolor = "#FFFF00";
                }
                
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $selectResult[csf('id')]; ?>" onclick="js_set_value(<?php echo $selectResult[csf('id')]; ?>)">
                    <td width="50" align="center"><?php echo "$i"; ?>
                        <input type="hidden" name="txt_individual" id="txt_individual<?=$selectResult[csf('id')]; ?>" value="<?=$selectResult[csf('user_name')]; ?>"/>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$selectResult[csf('id')]; ?>" value="<?=$selectResult[csf('id')]; ?>"/>
                    </td>
                    <td width="130">&nbsp;<?php echo $selectResult[csf('user_name')]; ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
            </table>
        </div>
        <div>
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                        	<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                        	<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                        </div>
                    </td>
                </tr>
        	</table>
        </div>
    </div>
    
    <script type="text/javascript">
		setFilterGrid("tbl_list_search",-1)
    </script>
    <script>
    var user_data='<? echo $data;?>';
    user_arr=user_data.split(',');
    for(var i=0;i<=user_arr.length;i++)
    {
    	js_set_value( user_arr[i] );
    }
    </script>
    <?
    exit();
}

if ($action=="load_drop_down_brand")
{
	$exdata=explode("_",$data);
	
	if($exdata[1]=="" || $exdata[1]==0) $exdata[1]=$selected;
	echo create_drop_down( "cbo_brand_id", 80, "select id, brand_name from lib_buyer_brand brand where buyer_id='$exdata[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-",  $exdata[1], "" );
	exit();
}

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
    $garments_nature = $explode_data[2];

    //print_r($explode_data);die;
	if($type=="12") // Sales Year started
	{
		$nameArray=sql_select( "select sales_year_started,id from  variable_order_tracking where company_name='$company_id' and variable_list=12 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Sales Year started</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="sales_year_started">Sales Year started</td>
                        <td width="190"><? echo create_drop_down( "cbo_sales_year_started_date", 170, $months,'', 1, '---- Select ----', $nameArray[0][csf('sales_year_started')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','')",1); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==14) //TNA Integrated
	{
		$nameArray=sql_select( "select tna_integrated,id from  variable_order_tracking where company_name='$company_id' and variable_list=14 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>TNA Integrated</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">TNA Integrated</td>
                        <td width="190"><? echo create_drop_down( "cbo_tna_integrated", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('tna_integrated')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}

	if($type==15) //Pre Costing : Profit Calculative
	{
		$nameArray=sql_select( "select profit_calculative, id from variable_order_tracking where company_name='$company_id' and variable_list=15 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Pre Costing : Profit Calculative</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="profit_calculative_td" class="must_entry_caption">Profit Calculative</td>
                        <td width="190">
                        	<? echo create_drop_down("cbo_profit_calculative", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('profit_calculative')], "" );?>
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
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                        <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                    </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==18) //Process Loss Method
	{
		$nameArray=sql_select( "select process_loss_method,item_category_id,id from  variable_order_tracking where company_name='$company_id' and variable_list=18 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<div>
			<fieldset>
                <legend>Process Loss Method</legend>
                <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                    <table cellspacing="0" width="100%" class="rpt_table">
                        <thead>
                            <th width="200" align="left">Item Category</th>
                            <th width="190" align="left">Process Loss Method</th>
                        </thead>
                        <?
                        $i=1;
                        $id_req=array(2,3,4,12,13,14,95,100);
                        foreach($item_category as $key=>$value)
                        {
							if (in_array($key,$id_req))
							{
								$resultRow= sql_select("select id,item_category_id,process_loss_method from variable_order_tracking where company_name='$company_id' and item_category_id='$key' and variable_list=18 order by id");
								?>
								<tr align="center">
                                    <td><?=create_drop_down( "item_category_id_".$i, 170, $item_category,'', 0, '', $resultRow[0][csf('item_category_id')], "",'',$key ); ?></td>
                                    <td><?=create_drop_down( "process_loss_method_".$i, 150, $process_loss_method,'', 0, '',$resultRow[0][csf('process_loss_method')],"" ); ?></td>
								</tr>
								<?
								if($i==1) $updids .= $resultRow[0][csf('id')]; else $updids .= ",".$resultRow[0][csf('id')];
								$i++;
							}
                        }
                        ?>
                    </table>
                </div>
                <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                    <table cellspacing="0" width="100%" >
                        <tr>
                        	<td align="center" width="320">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" valign="bottom" align="center" class="button_container">
                                <input type="hidden" name="update_id" id="update_id" value="<? echo $updids; ?>">
                                <?=load_submit_buttons($permission,"fnc_order_tracking_variable_settings", $is_update,0,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                            </td>
                        </tr>
                    </table>
                </div>
			</fieldset>
		</div>
		<?
	}

	if($type==19 || $type==103) //Consumtion Basis=19; QC Yarn. Cons. Come From[Sweater]=103
	{
		$nameArray=sql_select( "select id, consumption_basis from  variable_order_tracking where company_name='$company_id' and variable_list='$type' order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		if($type==19) $thCaption="Consumption Basis";
		else if($type==103) $thCaption="QC Yarn. Cons. Come From[Sweater]";
		else $thCaption="";
		
		if($type==103) $consBasisArr=array(1 => "Knitting Weight [Lbs] Without Process Loss", 2 => "Knitting Weight [Lbs] With Process Loss", 3 => "Actual Cons [Lbs]");
		else $consBasisArr=$consumtion_basis;
		?>
		<fieldset>
            <legend><?=$thCaption; ?></legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="consumption_td"><?=$thCaption; ?></td>
                        <td width="190"><? echo create_drop_down( "cbo_consumption_basis", 170, $consBasisArr,'', 1, '---- Select ----', $nameArray[0][csf('consumption_basis')], "" ); ?>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==20 || $type==78 || $type==79 || $type==96) //Copy Quotation 20	: PO Entry Control With Pre-Costing Approval 78 : Is Use Sourcing Post Cost Sheet 79
	{
		$nameArray=sql_select( "select copy_quotation, id from  variable_order_tracking where company_name='$company_id' and variable_list='$type' order by id" );
		if(count($nameArray)>0) $is_update=1; else $is_update=0;
		if($type==20) $caption="Copy Quotation";
		if($type==78) $caption="PO Entry Control With Pre-Costing Approval";
		if($type==79) $caption="Is Use Sourcing Post Cost Sheet";
        if($type==96) $caption="PO Entry Control With Booking Approval";
		
		if($type==78) $yesNoArr=array(1 => "Yes", 2 => "No", 3 => "Only Qty changing with BOM Approval");
		else $yesNoArr=$yes_no;
		?>
		<fieldset>
            <legend><?=$caption; ?></legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="180" align="left" id="copy_quotation_td"><?=$caption; ?></td>
                        <td width="190"><?=create_drop_down( "cbo_copy_quotation", 170, $yesNoArr,'', 1, '---- Select ----', $nameArray[0][csf('copy_quotation')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<?=$nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==21) //Conversion Charge From Chart
	{
		$nameArray=sql_select( "select conversion_from_chart, rate_type, id from  variable_order_tracking where company_name='$company_id' and variable_list=21 order by id" );
		?>
		<fieldset>
            <legend>Conversion Charge From Chart</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" border="1" class="rpt_table" rules="all">
                    <thead>
                        <th width="100" align="center">Rate Type</th>
                        <th width="120">Conversion From Chart</th>
                    </thead>
                    <tbody>
                    <?
                    $default = array(1=>'3',2=>'4',3=>'6',4=>'7',5=>'25'); $i=1;
                    if(count($nameArray)==0)
                    {
						foreach($default as $key=>$id)
						{
							$is_update=0;
							?>
							<tr id="tr_<? echo $key; ?>">
                                <td width="110" align="center"><? echo create_drop_down( "cbo_rate_type".$key, 100, $production_process,'', 1, "--Select Type--",$id , "","","3,4,6,7,25"); ?></td>
                                <td align="center">
									<? echo create_drop_down( "cbo_conversion_from_chart".$key, 80, $yes_no,'', 1, '--Select--', $selected, "" ); ?>
                                    <input  type="hidden"name="update_id<? echo $key; ?>" id="update_id<? echo $key; ?>" value="">
                                </td>
							</tr>
							<? 
						} 
					} else {
						foreach($nameArray as $row)
						{
							$is_update=1;
							?>
							<tr id="tr_<? echo $i; ?>">
								<td width="110" align="center"><? echo create_drop_down( "cbo_rate_type".$i, 100, $production_process,'', 1, "--Select Type--",$row[csf('rate_type')] , "","","3,4,6,7,25"); ?></td>
								<td align="center">
									<? echo create_drop_down( "cbo_conversion_from_chart".$i, 80, $yes_no,'', 1, '--Select--', $row[csf('conversion_from_chart')], "" ); ?>
									<input type="hidden"name="update_id<? echo $i; ?>" id="update_id<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
								</td>
							</tr>
							<? $i++;  
						} 
					} ?>
                    </tbody>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
		exit();
	}

	if($type==22) //CM Cost Predefined Method (Pre-cost)
	{
		$based_on_date=array(0=>"-select-",1=>"Costing Date",2=>"Min Shipment Date",3=>"Max Shipment Date",4=>"Min Pub Shipment Date",5=>"Max Pub Shipment Date");
		
		$nameArray=sql_select( "select cm_cost_compulsory, cm_cost_method, cm_cost_method_based_on, id, editable from  variable_order_tracking where company_name='$company_id' and variable_list=22 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>CM Cost Predefined Method (Pre-cost)</legend>
            <div style="width:800px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="cbo_cm_cost_method_td">CM Cost Predefined Method</td>
                        <td width="190">
                        <? echo create_drop_down( "cbo_cm_cost_method", 600, $cm_cost_predefined_method,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_method')], "" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" align="left" id="cbo_cm_cost_method_td">Based On</td>
                        <td width="190"><? echo create_drop_down( "cbo_cm_cost_method_based_on", 600, $based_on_date,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_method_based_on')], "" ); ?></td>
                    </tr>
                    <tr>
                        <td width="170" align="left" >CM Cost Compulsory</td>
                        <td width="150"><? echo create_drop_down( "cbo_cm_cost_compulsory", 100, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_compulsory')], "" ); ?></td>
                    </tr>
                    <tr>
                        <td width="170" align="left">Always Editable</td>
                        <td width="150"><? echo create_drop_down( "cbo_cm_cost_editable", 100, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==23) //Color From Library
	{
		$nameArray=sql_select( "select color_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=23 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Color From Library</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="color_from_library_td">Color From Library</td>
                        <td width="190"><? echo create_drop_down( "cbo_color_from_library", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('color_from_library')], "" ); ?></td>
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
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==24) //Yarn Dyeing Charge (In WO) from Chart
	{
		$nameArray=sql_select( "select color_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=24 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Yarn Dyeing Charge (In WO) from Chart</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="yarn_dyeing_charge_td">Yarn Dyeing Charge (In WO) from Chart</td>
                        <td width="190"><? echo create_drop_down( "cbo_yarn_dyeing_charge", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('color_from_library')],""); ?></td>
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
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==25) //Publish Shipment Date
	{
		$nameArray=sql_select( "select publish_shipment_date,duplicate_ship_date,id from  variable_order_tracking where company_name='$company_id' and variable_list=25 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Publish Shipment Date Enable Disable Check</legend>
            <div style="width:450px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="180" align="left" id="publish_shipment_date_td">Publish Shipment Date: Enable/Disable Check</td>
                        <td width="190"><? echo create_drop_down( "publish_shipment_date", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('publish_shipment_date')], "" ); ?></td>
                    </tr>
                    <tr>
                        <td width="180" align="left" id="publish_shipment_date_td">Next Process: Enable/Disable Check</td>
                        <td width="190"><? echo create_drop_down( "cbo_next_process_shipdate", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('duplicate_ship_date')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==26) //Material Control
	{
		$nameArray=sql_select( "select exeed_budge_qty,exeed_budge_amount,amount_exceed_level,item_category_id,id from  variable_order_tracking where company_name='$company_id' and variable_list=26 and status_active=1 and is_deleted=0 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Process Loss Method</legend>
            <div style="width:600px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" class="rpt_table">
                    <thead>
                        <th width="150" align="left">Item Category</th>
                        <th width="150" align="left">Exceed Budget Qty(%)</th>
                        <th width="150" align="left">Exceed Budget Amount(%)</th>
                        <th width="150" align="left">Amount Exceed Level</th>
                        <th width="150" align="left">Qty/Amount Level Than Exceed Budget Qty/Amount(%)</th>
                    </thead>
                    <?
                    $i=1; $id_req=array(1,4);
                    foreach($item_category as $key=>$value)
                    {
						if (in_array($key,$id_req))
						{
							$resultRow= sql_select("select id,item_category_id, exeed_budge_qty,exeed_budge_amount,amount_exceed_level,exceed_qty_level from variable_order_tracking where company_name='$company_id' and item_category_id='$key' and variable_list=26 and status_active=1 and is_deleted=0 order by id");
							
							$dis="";
							if($resultRow[0][csf('exceed_qty_level')]==1) $dis="disabled"; else $dis="";
							?>
							<tr align="center">
                                <td><? echo create_drop_down( "item_category_id_".$i, 150, $item_category,'', 0, '', $resultRow[0][csf('item_category_id')], "",1,$key ); ?></td>
                                <td><input type="text" name="txt_exeed_qty_<? echo $i; ?>" id="txt_exeed_qty_<? echo $i; ?>"  class="text_boxes_numeric" value="<? echo $resultRow[0][csf('exeed_budge_qty')]; ?>" <? echo $dis; ?>/></td>
                                <td><input type="text" name="txt_exeed_amount_<? echo $i; ?>" id="txt_exeed_amount_<? echo $i; ?>"  class="text_boxes_numeric" value="<? echo $resultRow[0][csf('exeed_budge_amount')]; ?>" /></td>
                                <td>
                                <?
                                    if($key==4)
                                    {
                                        $amount_exeed_lavel=array(1=>"Total Amount",2=>"Item Amount");
                                    }
                                    else
                                    {
                                        $amount_exeed_lavel=array(1=>"Total Amount");
                                    }
									
									echo create_drop_down( "cbo_exceed_level_".$i, 150, $amount_exeed_lavel,'', 1, '---------Select---------', $resultRow[0][csf('amount_exceed_level')], "",0,"");
                                ?>
                                </td>
                                <td><? echo create_drop_down( "cbo_exceed_qty_level_".$i, 150, $yes_no,'', 1, '---------Select---------', $resultRow[0][csf('exceed_qty_level')], "ena_dib(this.value,$i)",0,""); ?></td>
							</tr>
							<?
							if($i==1) $updids .= $resultRow[0][csf('id')]; else $updids .= ",".$resultRow[0][csf('id')];
							$i++;
						}
                    }
                    ?>
                </table>
            </div>
            <div style="width:600px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="100%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $updids; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==27 || $type==57 || $type==58 || $type==84)//27 Commercial Cost Predefined Method-Pre-Costing, 57 Currier Cost Predefined Method, 58 Commercial Cost Predefined Method-Price Quotation, 84 Commercial Cost Predefined Method-QC
	{
		$tdstyle="display:none";
		if($type==27)
		{
			$caption_th="Predefined Method-Pre-Costing"; $setting_type=27; $setting_method="1,2,3,5,6,7,9";
		}
		else if( $type==57)
		{
			$caption_th="Predefined Method"; $setting_type=57; $setting_method="1,2,3"; $tdstyle="";
		}
		else if( $type==58)
		{
			$caption_th="Predefined Method-Price Quotation"; $setting_type=58;
			if($garments_nature == 3) $setting_method="1,2,4,5,6,7"; else $setting_method="1,2,4";
		}
		else if( $type==84)
		{
			$caption_th="Predefined Method-QC"; $setting_type=84;
			if($garments_nature == 3) $setting_method="1,2,8"; else $setting_method="0";
		}
		$variableCond="";
		if($type==27 || $type==57) $variableCond=" id='$explode_data[3]'"; else $variableCond="company_name='$company_id' and variable_list=$setting_type";
		 
		$nameArray=sql_select("select id, commercial_cost_method, commercial_cost_percent, editable, tna_integrated as buyer_id, profit_calculative as brand_id, copy_quotation as based_on, excut_source from variable_order_tracking where $variableCond order by id");
		
		$basedontypearr=array(1=>"Percent",2=>"Fix Amount");
		
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		
		if($nameArray[0][csf('based_on')]==2) $tdvaltype="Fix Amount"; else $tdvaltype="Percent";
		?>
		<fieldset>
            <legend><?=$caption_th;?></legend>
            <div style="width:800px; min-height:20px; max-height:250px;" id="variable_list_cont2">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<? if( $type==27 || $type==57) { //ISD-23-12374 ?>
                    	<td width="60">Buyer</td>
                        <td width="100"><?=create_drop_down( "cbo_buyer_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-All Buyer-", $nameArray[0][csf('buyer_id')], "load_drop_down( 'requires/merchandising_settings_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
                        <td width="60">Brand</td>
                        <? if($is_update==1)
						{
							?><td width="80" id="brand_td"><?=create_drop_down( "cbo_brand_id", 80, "select id, brand_name from lib_buyer_brand brand where buyer_id='".$nameArray[0][csf('buyer_id')]."' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-", $nameArray[0][csf('brand_id')],""); ?></td><?
						}
						else
						{
							?><td width="80" id="brand_td"><?=create_drop_down( "cbo_brand_id", 80, $blank_array,"", 1, "-Brand-", $selected, "" ); ?></td>
                        <? } } ?>
                        <td width="300" align="center" id="cbo_commercial_cost_method_td"><?=$caption_th; ?></td>
                        <td width="150"><?=create_drop_down( "cbo_commercial_cost_method", 150, $commercial_cost_predefined_method,'', 1, '---- Select ----', $nameArray[0][csf('commercial_cost_method')], "","",$setting_method ); ?></td>
                        <td width="70" align="center" style=" <?=$tdstyle; ?>">Based On</td>
                        <td style=" <?=$tdstyle; ?>"><? echo create_drop_down( "cbo_based_on", 60, $basedontypearr,'', 1, '--Select--', $nameArray[0][csf('based_on')], "fnc_valuetype(this.value);" ); ?></td>
                        <td width="80" align="center" id="tdvaltype"><?=$tdvaltype; ?></td>
                        <td width="70"> <input type="text" style="width:60px;" name="txt_commercial_cost_percent" id="txt_commercial_cost_percent"  class="text_boxes_numeric" value="<? echo $nameArray[0][csf('commercial_cost_percent')]; ?>" /></td>
                        <td width="70" align="center">Editable</td>
                        <td><? echo create_drop_down( "cbo_editable", 60, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "" ); ?></td>
                        
                    </tr>

                    <tr>
                    <? if( $type==27) {  ?>
                        <td colspan="3">Commercial Cost Compulsory</td>
                        <td><? echo create_drop_down( "cbo_compulsory", 100, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "" ); ?></td>
                        <? } ?>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==28) //Gmt Number repeat style
	{
		$nameArray=sql_select( "select gmt_num_rep_sty,id from  variable_order_tracking where company_name='$company_id' and variable_list=28 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Gmt Number repeat style</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="copy_quotation_td">Size wise repeat</td>
                        <td width="190"><? echo create_drop_down( "txt_size_wise_repeat", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('gmt_num_rep_sty')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==29) //Duplicate Ship Date
	{
		$nameArray=sql_select( "select duplicate_ship_date,id from  variable_order_tracking where company_name='$company_id' and variable_list=29 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Duplicate Ship Date</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Duplicate Ship</td>
                        <td width="190"><? echo create_drop_down( "cbo_duplicate_ship_date", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('duplicate_ship_date')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==30) //Image Mandatory
	{
		$nameArray=sql_select( "select image_mandatory,id from  variable_order_tracking where company_name='$company_id' and variable_list=30 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
			<legend>Image Mandatory</legend>
			<div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100">Mandatory</td>
                        <td width="190"><? echo create_drop_down( "cbo_image_mandatory", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('image_mandatory')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
			</div>
		</fieldset>
		<?
	}

	if($type==31) //TNA Process type
	{ 
		$nameArray=sql_select( "select tna_process_type,textile_tna_process_base,id from  variable_order_tracking where company_name='$company_id' and variable_list=31 order by id" );
         
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>TNA Process type</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellpadding="0" cellspacing="2" align="center">
                    <tr>
                        <td width="80">Tna Process type</td>
                        <td width="120">
                            <? $tna_process_type_arr=array('1'=>'Template Base','2'=>'Percent Base');
                            echo create_drop_down( "txt_tna_process_type", 170, $tna_process_type_arr,'', 1, '---- Select ----', $nameArray[0][csf('tna_process_type')], "",'','' );
                            ?>
                        <br/>
                        </td>
                    </tr> 
                    <tr>
                        <td width="80">Based On</td>
                        <td width="120">
                            <? $based_on_arr=array('1'=>'Pub Ship Date','2'=>'Actual Ship Date');
                            echo create_drop_down( "cbo_based_on", 170, $based_on_arr,'', 1, '---- Select ----', $nameArray[0][csf('textile_tna_process_base')], "",'','' );
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==32) //Po Update Period
	{
		$nameArray=sql_select( "select po_update_period,id,user_id from  variable_order_tracking where company_name='$company_id' and variable_list=32 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		
		$user_ex=explode(",",$nameArray[0][csf('user_id')]);
		$user_array=return_library_array( "select id,user_name from user_passwd where valid=1",'id','user_name');
		
		foreach($user_ex as $key)
		{
			$user.=",".$user_array[$key];
		}
		?>
		<fieldset>
            <legend>Po Update Period</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Update Period(Hr)</td>
                        <td width="190"><input  type="text" name="update_period" id="update_period" class="text_boxes_numeric"   style="width:100px;" value="<? echo $nameArray[0][csf('po_update_period')];?>" /></td>
                        <td>User:</td>
                        <td><input  type="text" name="users_name_id" id="users_name_id" onClick="users_popup('requires/merchandising_settings_controller.php?action=users_name_list','User')" class="text_boxes" value="<? echo ltrim($user,","); ?>" />
                        	<input  type="hidden" name="user_hidden_id" id="user_hidden_id" value="<? echo $nameArray[0][csf('user_id')];?>" />
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<script>set_multiselect('users_name_id','1','0','','0');</script>
		<?
	}

	if($type==33 || $type==85) //33--Po Receive Date; 85--Requisition Maintain
	{
		$nameArray=sql_select( "select po_current_date, id from variable_order_tracking where company_name='$company_id' and variable_list=33 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		
		?>
		<fieldset>
            <legend>Po Receive Date</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">PO Current Date </td>
                        <td width="190"><? echo create_drop_down( "cbo_po_current_date", 170, $yes_no,'', 1,'---- Select ----', $nameArray[0][csf('po_current_date')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==34) //Inquery ID Mandatory
	{
		$nameArray=sql_select( "select inquery_id_mandatory,id from  variable_order_tracking where company_name='$company_id' and variable_list=34 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Inquery ID Mandatory</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="yarn_dyeing_charge_td">Inquery ID Mandatory</td>
                        <td width="190"><? echo create_drop_down( "cbo_inquery_id_mandatory", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('inquery_id_mandatory')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==35) //Trim Rate
	{
		$trims_sup_buyer_tag=array(1=>"When no relation between Item and Supplier", 2=>"When relation between Item and Supplier", 3=>"When relation amoung Item,Supplier and Buyer", 4=>"When relation Item, Item Description, Supplier and Rate");
		$nameArray=sql_select( "select trim_rate, id from variable_order_tracking where company_name='$company_id' and variable_list=35 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Trim Rate</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="yarnrate_td">Trim Rate</td>
                        <td width="190"><? echo create_drop_down( "cbo_trim_rate", 170, $trims_sup_buyer_tag,'', 1, '---- Select ----', $nameArray[0][csf('trim_rate')],""); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==36) //CM Cost Predefined Method (Price Quotation)
	{
		$nameArray=sql_select( "select cm_cost_method_quata,cm_cost_compulsory as cm_cost_compulsory,id from  variable_order_tracking where company_name='$company_id' and variable_list=36 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>CM Cost Predefined Method (Price Quotation)</legend>
            <div style="width:800px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="170" align="left" id="cbo_cm_cost_method_td">CM Cost Predefined Method</td>
                        <td width="150"><?=create_drop_down( "cbo_cm_cost_method_quata", 600, $cm_cost_predefined_method,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_method_quata')], ""); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="170" align="left">CM Cost Compulsory</td>
                        <td width="150"><?=create_drop_down( "cbo_cm_cost_compulsory", 100, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_compulsory')], "" ); ?>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==37) //Budget Validation
	{
		$nameArray=sql_select( "select budget_exceeds_quot,id from variable_order_tracking where company_name='$company_id' and variable_list=37 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Budget Validation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="budgetexceedsquot_td"> Budget Exceeds Quot</td>
                        <td width="190">
							<?
                            $yes_no_quot=array(1=>"Yes",2=>"No",3=>"No All Item");
                            echo create_drop_down( "cbo_budget_exceeds_quot", 170, $yes_no_quot,'', 1, '---- Select ----', $nameArray[0][csf('budget_exceeds_quot')], "" );
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==38) //Short Febric Booking Before Main Febric Booking..S.F. Booking Before M.F. 100%
	{
		$nameArray=sql_select( "select s_f_booking_befor_m_f,id from  variable_order_tracking where company_name='$company_id' and variable_list=38 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>S.F. Booking Before M.F. 100%</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">S.F. Booking Before M.F.</td>
                        <td width="190"><?=create_drop_down( "cbo_s_f", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('s_f_booking_befor_m_f')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==39) //Lab Test Rate Update
	{
		$nameArray=sql_select( "select lab_test_rate_update,id from  variable_order_tracking where company_name='$company_id' and variable_list=39 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Lab Test Rete Update</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="lab_test_rate_update_td">Lab Test Rate Update</td>
                        <td width="190"><?=create_drop_down( "cbo_lab_test_rate", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('lab_test_rate_update')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==40) //Collar Cuff Percent
	{
		$nameArray=sql_select( "select colar_culff_percent,id from  variable_order_tracking where company_name='$company_id' and variable_list=40 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Collar Cuff Percent</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="colar_culff_percent_td">In Master Part</td>
                        <td width="190"><?=create_drop_down( "cbo_colar_culff_percent", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('colar_culff_percent')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==41) //Pre-cost Approval
	{
		$nameArray=sql_select( "select pre_cost_approval,id from  variable_order_tracking where company_name='$company_id' and variable_list=41 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Per-Cost Approval</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Per-Cost Approval</td>
                        <td width="190"><?=create_drop_down( "cbo_pre_cost_approval", 170, $pre_cost_approval,'', 1, '---- Select ----', $nameArray[0][csf('pre_cost_approval')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
	if($type==42) //Report Date Catagory
	{
		$nameArray=sql_select( "select report_date_catagory,id from  variable_order_tracking where company_name='$company_id' and variable_list=42 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Report Date Catagory</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Report Date Catagory</td>
                        <td width="190"><?=create_drop_down( "cbo_report_date_catagory", 170, $report_date_catagory,'', 1, '---- Select ----', $nameArray[0][csf('report_date_catagory')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<?=$nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
	if($type==43) //TNA Process Start Date
	{
		$nameArray=sql_select( "select tna_process_start_date,id from  variable_order_tracking where company_name='$company_id' and variable_list=43 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>TNA Process Start Date</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">TNA Process Start Date</td>
                        <td width="190"><input name="txt_tna_process_start_date" id="txt_tna_process_start_date" class="datepicker"  style="width:100px;" value="<? echo change_date_format($nameArray[0][csf('tna_process_start_date')]); ?>" ></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==44 || $type==63 || $type==64) //44 Order Tracking Season Mandatory, 63 Sequence validation with Booking , 64 Sew Comp. and location mandatory
	{
		if($type==64) $th_caption="";
		
		$nameArray=sql_select( "select id, season_mandatory from variable_order_tracking where company_name='$company_id' and variable_list='$type' order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		if($type==44) $season_text='Season Mandatory';
		else if($type==63) $season_text='Sequence validation with Booking';
		else $season_text='Sew Company and Location mandatory  in order entry';
		?>
		<fieldset>
            <legend><? echo $season_text; ?></legend>
            <div style="width:450px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left"  id="season_mandatory_td"><? echo $season_text; ?></td>
                        <td width="190"><? echo create_drop_down( "cbo_season_mandatory", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('season_mandatory')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==45) //Excess Cut Source in Order Entry
	{
	
		$nameArray=sql_select( "select id, excut_source,editable from variable_order_tracking where company_name='$company_id' and variable_list=45 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$excut_source= $nameArray[0][csf('excut_source')];
		if($excut_source==1 || $excut_source==3) $disabled_td=1; else $disabled_td='';
		?>
		<fieldset>
            <legend>Excess Cut Source in Order Entry</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="180" align="left"  id="season_mandatory_td">Excess Cut Source in Order Entry</td>
                        <td width="100"><? echo create_drop_down( "cbo_excess_cut_source", 150, $exCut_source,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "fnc_check_yes_no(this.value)",'','' ); ?></td>
                    </tr>
                    <tr>
                        <td width="50" align="left"  id="season_mandatory_td">Is Editable</td>
                        <td width="90"><? echo create_drop_down( "cbo_editable_id",150, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",$disabled_td,'' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==46) // Allow Ship Date on Off Day
	{
		$nameArray=sql_select( "select publish_shipment_date,id from  variable_order_tracking where company_name='$company_id' and variable_list=46 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Allow Ship Date on Off Day</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Ship Date on Off Day </td>
                        <td width="190"><? echo create_drop_down( "cbo_ship_date", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('publish_shipment_date')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==47) // Style & SMV Source/Combinations
	{
		$nameArray=sql_select( "select id, publish_shipment_date, style_from_library, editable from variable_order_tracking where company_name='$company_id' and variable_list=47 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Style & SMV Source/Combinations</legend>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="180" align="left" id="tna_integrated_td">Style & SMV Source/Combinations</td>
                        <td>
							<?
                            $smv_in_order_entry=array(1=>"OE+PC",2=>"PQ+OE+PC",3=>"WS+OE+PC",4=>"WS+PQ+OE+PC",5=>"QI+PQ+OE+PC",6=>"QI+WS+PQ+OE+PC",7=>"QC+OE+PC",8=>"QC+WS+OE+PC",9=>"QI+QC+WS+OE+PC"); //,10=>"SR+TW+OE+PC" remove by kausar if need pls contract with me. 08-06-2022
                            echo create_drop_down( "cbo_smv_in_order_entry", 170, $smv_in_order_entry,'', 1, '---- Select ----', $nameArray[0][csf('publish_shipment_date')], "",'','' );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Style From Library</td>
                        <td><?=create_drop_down( "cbo_style_from_library", 100, $yes_no,'', 0, ' Select', $nameArray[0][csf('style_from_library')], "" ); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Style Editable In OE</td>
                        <td><?=create_drop_down( "cbo_style_editable", 100, $yes_no,'', 1, ' Select', $nameArray[0][csf('editable')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<?=$nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	//default fabric nature
	if($type==48) // Default Fabric Nature
	{
		$nameArray=sql_select( "select default_fabric_nature,id from  variable_order_tracking where company_name='$company_id' and variable_list=48 order by id" );
		//echo "select default_fabric_nature,id from  variable_order_tracking where company_name='$company_id' and variable_list=48 order by id";
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Default fabric nature</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Default fabric nature </td>
                        <td width="190"><? echo create_drop_down( "cbo_default_febric_nature", 170, $item_category,'', 1, '---- Select ----', $nameArray[0][csf('default_fabric_nature')], "",'','2,3','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	//default fabric Source
	if($type==49) // Default Fabric Source
	{
		$nameArray=sql_select( "select default_fabric_source,id from  variable_order_tracking where company_name='$company_id' and variable_list=49 order by id" );
		//echo "select default_fabric_nature,id from  variable_order_tracking where company_name='$company_id' and variable_list=48 order by id";
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Default Fabric Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Default fabric source </td>
                        <td width="190"><? echo create_drop_down( "cbo_default_fabric_source", 170, $fabric_source,'', 1, '---- Select ----', $nameArray[0][csf('default_fabric_source')], "",'','1,2,3','' ); ?> </td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
	}

	if($type==50) //BOM Page Setting
	{
		$nameArray=sql_select( "select bom_page_setting,id from  variable_order_tracking where company_name='$company_id' and variable_list=50 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>BOM Page Setting</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">TNA Integrated</td>
                        <td width="190">
							<?
                            $bom_pages_arr=array(1=>"Pre-Costing V1",2=>"Pre-Costing V2");
                            echo create_drop_down( "cbo_bom_page", 170, $bom_pages_arr,'', 1, '---- Select ----', $nameArray[0][csf('bom_page_setting')], "",'','' );
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==51 || $type==95) //51=>Min Lead Time Control, 95=>Maximum acc wo lead time control
	{
		$nameArray=sql_select( "select id, min_lead_time_control from variable_order_tracking where company_name='$company_id' and variable_list=$type order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
        if($type==51){
            $title="Min Lead Time Control";
        }
        else{
            $title="Maximum acc wo lead time control";
        }
		?>
		<fieldset>
            <legend><?= $title ?></legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="season_mandatory_td"><?= $title ?></td>
                        <td width="190"><? echo create_drop_down( "cbo_min_lead_time_control", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('min_lead_time_control')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==52) // PO Entry Limit On Capacity
	{
		$nameArray=sql_select( "select id, buyer_allocation_maintain,capacity_exceed_level, exeed_budge_qty from variable_order_tracking where company_name='$company_id' and variable_list=52 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$buyer_maintain=$nameArray[0][csf('buyer_allocation_maintain')];
		
		if($buyer_maintain==2 || $buyer_maintain==0) $disable_con="1";else $disable_con="";
		?>
		<fieldset>
            <legend>PO Entry Limit On Capacity</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="season_mandatory_td">Capacity Calculation</td>
                        <td width="190"><? echo create_drop_down( "cbo_buyer_allocation_maintain", 170, $capacity_control_withArr,'', 1, '---- Select ----', $nameArray[0][csf('buyer_allocation_maintain')], "fnc_check_field(this.value)",'','' ); ?> </td>
                    </tr>
                    <tr>
                        <td width="100" align="left"  id="season_mandatory_td">Capacity Exceed Level On</td>
                        <td width="190"><? echo create_drop_down( "cbo_capacity_exceed_level", 170, $capacity_exceed_level,'', 1, '---- Select ----', $nameArray[0][csf('capacity_exceed_level')], "",$disable_con,'' ); ?> </td>
                    </tr>
                    <tr>
                        <td width="100" align="left">Allow Exceed PO Qty In Actual PO</td>
                        <td width="190"><? echo create_drop_down( "cbo_actpo_exceed_level", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('exeed_budge_qty')], "",'','' ); ?> </td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==53) //Cost Control Source
	{
		$nameArray=sql_select( "select cost_control_source, id from variable_order_tracking where company_name='$company_id' and variable_list=53 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Cost Control Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Cost Control Source</td>
                        <td width="190">
							<?
                            $bom_cost_control_source_arr=array(1=>"Quick Costing",2=>"Price Quotation",3=>"Without Control",4=>"Quick Costing[WVN]",5=>"Short Quotation V2",6=>"Short Quotation V3",7=>"Short Quotation [Sweater]",8=>"Short Quotation -V6");
                            echo create_drop_down( "cbo_cost_control_source", 170, $bom_cost_control_source_arr,'', 1, '---- Select ----', $nameArray[0][csf('cost_control_source')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==54) //Efficiency Source For Pre-Cost
	{
		$nameArray=sql_select( "select efficiency_source_for_pre_cost, id from variable_order_tracking where company_name='$company_id' and variable_list=54 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Efficiency Source For Pre-Cost</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Efficiency Source</td>
                        <td width="190">
							<?
                            $bom_efficiency_source_for_pre_cost_arr=array(1=>"Manual",2=>"Work Study",3=>"Efficiency Slab");
                            echo create_drop_down( "cbo_efficiency_source_for_pre_cost", 170, $bom_efficiency_source_for_pre_cost_arr,'', 1, '---- Select ----', $nameArray[0][csf('efficiency_source_for_pre_cost')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==55) //Work Study Mapping
	{
		$nameArray=sql_select( "select work_study_mapping_id, id from variable_order_tracking where company_name='$company_id' and variable_list=55 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Work Study Mapping</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Work Study Mapping</td>
                        <td width="190">
							<?
                            $work_study_mapping_arr=array(1=>"Quick Costing",2=>"Quotation Inquery",3=>"Manual",4=>"Order Entry By Matrix");
                            echo create_drop_down( "cbo_work_study_mapping", 170, $work_study_mapping_arr,'', 1, '---- Select ----', $nameArray[0][csf('work_study_mapping_id')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==92) //Theoretical MP calculation method
	{
		$nameArray=sql_select( "select work_study_mapping_id, id from variable_order_tracking where company_name='$company_id' and variable_list=92 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Theoretical MP calculation method</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Theoretical MP calculation method</td>
                        <td width="190">
							<?
                            $work_study_mapping_arr=array(1=>"Operation SMV/Pitch Time",2=>"(Operation SMV/Pitch Time)*Efficiency%");
                            echo create_drop_down( "cbo_work_study_mapping", 170, $work_study_mapping_arr,'', 1, '---- Select ----', $nameArray[0][csf('work_study_mapping_id')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==56) //Embellishment Budget On
	{
		$nameArray=sql_select( "select embellishment_id,embellishment_budget_id, id from  variable_order_tracking where company_name='$company_id' and variable_list=56 order by id" );
		foreach($nameArray as $vals)
		{
		$embel_arr[$vals[csf("embellishment_id")]]['id']=$vals[csf("id")];
		$embel_arr[$vals[csf("embellishment_id")]]['budget']=$vals[csf("embellishment_budget_id")];
		}
		
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Embellishment Budget On</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" class="rpt_table" id="embellishment_tbl">
                    <thead>
                        <th width="200" align="left">Embellishment Type</th>
                        <th width="190" align="left">Embellishment Budget On</th>
                    </thead>
                    <tbody>
						<?
                        $i=1;
                        foreach($emblishment_name_array as $key=>$value)
                        {
							?>
							<tr align="center" id="<? echo $i;?>">
                                <td>
                                    <input type="text" class="text_boxes" name="cboEmbellishmentType" id="cboEmbellishmentType_<?=$i; ?>" value="<?=$value; ?>" disabled>
                                    <input type="hidden" id="cboEmbellishmentTypeHidden_<?=$i; ?>" value="<?=$key; ?>">
                                    <input type="hidden" id="updateidRequiredEmbellishdtl_<?=$i; ?>" value="<?=$embel_arr[$key]['id'];?>">
                                </td>
                                <td><?=create_drop_down( "embellishmentName_".$i, 150, $embellishment_budget_on, '', 0, '', $embel_arr[$key]['budget'] ); ?></td>
							</tr>
							<?
							$i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <input  type="hidden" name="update_id" id="update_id" value="<? echo $updids; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
	if($type==59) //Fabric Source For AOP
	{
		$nameArray=sql_select( "select  fabric_source_aop_id, id from variable_order_tracking where company_name='$company_id' and variable_list=59 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Fabric Source For AOP</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Fabric Source For AOP</td>
                        <td width="190">
							<?
                            $fabric_source_aop_arr=array(1=>"Grey Fabric Qty",2=>"Finish Fabric Qty");
                            echo create_drop_down( "cbo_fabric_source_aop_id", 170, $fabric_source_aop_arr,'', 1, '---- Select ----', $nameArray[0][csf('fabric_source_aop_id')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==60) //Yarn Issue Validation Based on Service Approval
	{
		$nameArray=sql_select( "select yarn_iss_with_serv_app,id from  variable_order_tracking where company_name='$company_id' and variable_list=60 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Yarn Issue Validation Based on Service Approval</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Yarn Issue Validation Based on Service Approval</td>
                        <td width="190"><? echo create_drop_down( "cbo_yarn_iss_with_serv_app", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('yarn_iss_with_serv_app')], "",'','' ); ?>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
    if($type==61) //Price Quotation Approval
	{
		$nameArray=sql_select( "select price_quo_approval,id from  variable_order_tracking where company_name='$company_id' and variable_list=61 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Price Quotation Approval</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Price Quotation Approval</td>
                        <td width="190"><? echo create_drop_down( "cbo_price_quo_approval", 170, $pre_cost_approval,'', 1, '---- Select ----', $nameArray[0][csf('price_quo_approval')], "",'','' ); ?> </td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
	if($type==62) //Textile TNA Baseed On
	{
		$nameArray=sql_select( "select textile_tna_process_base,id from  variable_order_tracking where company_name='$company_id' and variable_list=62 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Textile TNA Baseed On</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100">Textile Tna Process Base</td>
                        <td width="190">
                        <? $tna_textile_process_base_arr=array('1'=>'Booking','2'=>'Sales Order');
                        echo create_drop_down( "cbo_textile_tna_process_base", 170, $tna_textile_process_base_arr,'', 1, '---- Select ----', $nameArray[0][csf('textile_tna_process_base')], "",'','' );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==65) //Excess Cut % Level in Order Entry
	{
		$nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=65 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Excess Cut % Level in Order Entry</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100">Excess Cut % Level in Order Entry</td>
                        <td width="190"><? echo create_drop_down( "cbo_excesscut_per_level", 170, $excess_cut_per_level,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?>
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
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                        <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==66) //Fabric Req. Qty. Source
	{
		$nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=66 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Fabric Req. Qty. Source in Service Booking</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    <td width="100">Fabric Req. Qty. Source</td>
                    <td width="190"><? echo create_drop_down( "cbo_excesscut_per_level", 170, $fab_req_qty_source,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	
    if($type==67) //Location Wise Financial Parameter
	{
		$nameArray=sql_select( "select yarn_iss_with_serv_app,id from  variable_order_tracking where company_name='$company_id' and variable_list=67 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Location Wise Financial Parameter</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="cost_per_minute_td">Location Wise Cost Per Minute Setting:</td>
                        <td width="190"><? echo create_drop_down( "cbo_yarn_iss_with_serv_app", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('yarn_iss_with_serv_app')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

	if($type==68) //QC Cons. From
	{
		$nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=68 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$qcConsFrom=array(1=>"Cons",2=>"Tot. Cons");
		?>
		<fieldset>
            <legend>QC Cons. From</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                	<tr>
                        <td width="100">QC Cons. From</td>
                        <td width="190"><? echo create_drop_down( "cbo_excesscut_per_level", 170, $qcConsFrom,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==69) // Yarn Dyeing Work Order Used
	{
		$nameArray=sql_select( "select yd_wo_used, id from  variable_order_tracking where company_name='$company_id' and variable_list=69 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Yarn Dyeing Work Order Used</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="yarn_dyeing_lot_used_td">Yarn Dyeing Work Order</td>
                        <td width="190">
                        <?
                        $lot_type=array(1=>"With Lot",2=>"Without Lot");
                        echo create_drop_down( "cbo_yarn_dyeing_lot_used", 170, $lot_type,'', 1, '---- Select ----', $nameArray[0][csf('yd_wo_used')], "" );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==70) //
	{
		$nameArray=sql_select( "select editable, id from  variable_order_tracking where company_name='$company_id' and variable_list=70 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Knitting Charge Source </legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="cbo_knitting_charge_source_td">Knitting Charge Source</td>
                        <td width="190">
                        <?
                        $knitting_charge_type=array(1=>"Knitting Charge from budget",2=>"Yarn Count Determination",3=>"From Fabric Sales Order");
                        echo create_drop_down( "cbo_knitting_charge_source", 170, $knitting_charge_type,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "" );
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==71) //Fabric Ref. Automation. It will effect Fabric Ref input field of Fabric Determination entry page in Library Module.
    {
        $nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=71 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        $fab_req_qty_source = array(1 => "Manual", 2 => "Sys.Generated");
        ?>
        <fieldset>
            <legend>Fabric Ref. Automation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    <td width="100">Fabric Ref. Automation</td>
                    <td width="190"><? echo create_drop_down( "cbo_fabric_ref_automation", 170, $fab_req_qty_source,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
	
	if($type==72)
	{
		$nameArray=sql_select( "select EXCUT_SOURCE, ITEM_CATEGORY_ID, id from  variable_order_tracking where company_name='$company_id' and variable_list=72 and status_active= 1 order by id" );
		?>
		<fieldset>
            <legend>Conversion Charge From Chart</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" border="1" class="rpt_table" rules="all">
                    <thead>
                        <th align="center">Category</th>
                        <th>Source</th>
                    </thead>
                    <tbody>
                    <?
                    $itemCatArr = array(4,3,2,12,8,15,16);
                    $sourceArr = array(1=>"Budget",2=>"Sourching Post Cost",3=>"CS",4=>"Supplier Wise Rate");
                    $generalSourceArr = array(1=>"Manual",2=>"Supplier Wise Rate");
                    $i=1;
                    //echo count($nameArray).'system';
					if(count($nameArray)==0)
                    {
						foreach($itemCatArr as $id)
						{
							$is_update=0;
                           // echo $id.'=<br>';
                            if($id==15 || $id==16) $item_source_cond="1,2";
                            else $item_source_cond="";
							?>
							<tr id="tr_<? echo $i; ?>">
                                <td align="center"><? echo create_drop_down( "cbo_booking_type".$i, 150, $item_category_type_arr,'', 1, "--Select Type--",$id , "","1",implode(',',$itemCatArr)); ?></td>
                                <td align="center">
									<? 
                                    if ($id != 8) echo create_drop_down( "cbo_source_id".$i, 150, $sourceArr,'', 1, '--Select--', $selected, "","","$item_source_cond" );
                                    else echo create_drop_down( "cbo_source_id".$i, 150, $generalSourceArr,'', 1, '--Select--', $selected, "" );
                                    ?>
                                    <input  type="hidden" name="update_id<? echo $i; ?>" id="update_id<? echo $i; ?>" value="">
                                </td>
							</tr>
							<?
							$i++;
						} 
					} 
                    else 
                    {                        
						foreach($nameArray as $row)
						{
							$is_update=1;
                            $itemCategoryArr[]=$row[csf('ITEM_CATEGORY_ID')];
                            $itemId=$row[csf('ITEM_CATEGORY_ID')];
                            if($itemId==15 || $itemId==16) $item_source_cond="1,2";
                            else $item_source_cond="";

							?>
							<tr id="tr_<? echo $i; ?>">
								<td align="center"><? echo create_drop_down( "cbo_booking_type".$i, 150, $item_category_type_arr,'', 1, "--Select Type--",$row[csf('ITEM_CATEGORY_ID')] , "","1",implode(',',$itemCatArr)); ?></td>
								<td align="center">
									<? 
                                    if ($row[csf('ITEM_CATEGORY_ID')] != 8) echo create_drop_down( "cbo_source_id".$i, 150, $sourceArr,'', 1, '--Select--', $row[csf('EXCUT_SOURCE')], "","","$item_source_cond" ); 
                                    else echo create_drop_down( "cbo_source_id".$i, 150, $generalSourceArr,'', 1, '--Select--', $row[csf('EXCUT_SOURCE')], "" );
                                    ?>
									<input type="hidden" name="update_id<? echo $i; ?>" id="update_id<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
								</td>
							</tr>
							<? $i++;  
						}
                        $category_diff=array_diff($itemCatArr, $itemCategoryArr);
                        foreach($category_diff as $id)
                        {
                            $is_update=1;
                            if($itemidId==15 || $id==16) $item_source_cond="1,2";
                            else $item_source_cond="";
                            ?>
                            <tr id="tr_<? echo $i; ?>">
                                <td align="center"><? echo create_drop_down( "cbo_booking_type".$i, 150, $item_category_type_arr,'', 1, "--Select Type--",$id , "","1",implode(',',$itemCatArr)); ?></td>
                                <td align="center">
                                    <? 
                                    if ($id != 8) echo create_drop_down( "cbo_source_id".$i, 150, $sourceArr,'', 1, '--Select--', $selected, "","","$item_source_cond" );
                                    else echo create_drop_down( "cbo_source_id".$i, 150, $generalSourceArr,'', 1, '--Select--', $selected, "" );
                                    ?>
                                    <input  type="hidden" name="update_id<? echo $i; ?>" id="update_id<? echo $i; ?>" value="">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }

					} 
                    ?>
                    </tbody>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
		exit();
	}
	if($type==73) // 
	{
		$nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=73 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Fabric Booking Control With SC/LC:</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    <td width="100"> Check Buyer PO With LC/SC :</td>
                    <td width="190"><? echo create_drop_down( "cbo_excesscut_per_level", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==74) // Sample delivery date calculation
	{
		$nameArray=sql_select( "select id, excut_source,exceed_qty_level,editable from variable_order_tracking where company_name='$company_id' and variable_list=74 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Sample delivery date calculation:</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%"  border="1" class="rpt_table" rules="all" >
                    <tr>
                    <td width="50">1</td>
                    <td width="220">For without Embellishment:</td>
                    <td width="100">
			<input  type="text" name="txt_without_emblish" id="txt_without_emblish" value="<? echo $nameArray[0][csf('excut_source')]; ?>"  class="text_boxes_numeric" style="width:110px;">
					</td>
                      <td width="100">Day</td>
                    </tr>
                     <tr>
                    <td width="50">2</td>
                    <td width="220">For with Embellishment:</td>
                    <td width="110">
			<input  type="text" name="txt_with_emblish" id="txt_with_emblish" value="<? echo $nameArray[0][csf('exceed_qty_level')]; ?>"  class="text_boxes_numeric" style="width:110px;">
					</td>
                      <td width="100">Day</td>
                    </tr>
                    <tr>
                        <td width="50"></td>
                        <td width="220" align="left"  id="txt_auto_acknowledge">Auto Acknowledge</td>
                        <td width="110"><? echo create_drop_down( "cbo_auto_acknowledge", 110, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==75) //Fabric Budget On
	{
		$nameArray=sql_select( "select embellishment_id, embellishment_budget_id, id from variable_order_tracking where company_name='$company_id' and variable_list=75 order by id" );
		foreach($nameArray as $vals)
		{
			$embel_arr[$vals[csf("embellishment_id")]]['id']=$vals[csf("id")];
			$embel_arr[$vals[csf("embellishment_id")]]['budget']=$vals[csf("embellishment_budget_id")];
		}
		
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Fabric Budget On</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" class="rpt_table" id="embellishment_tbl">
                    <thead>
                        <th width="200" align="left">Fabric Type</th>
                        <th width="190" align="left">Fabric Budget On</th>
                    </thead>
                    <tbody>
						<?
                        $i=1; $newitem_category=array(2 => "Knit",3 => "Woven",100 => "Sweater",);
                        foreach($newitem_category as $key=>$value)
                        {
							?>
							<tr align="center" id="<?=$i; ?>">
                                <td>
                                    <input type="text" class="text_boxes" name="cboEmbellishmentType" id="cboEmbellishmentType_<?=$i; ?>" value="<?=$value; ?>" disabled>
                                    <input type="hidden" id="cboEmbellishmentTypeHidden_<?=$i; ?>" value="<?=$key; ?>">
                                    <input type="hidden" id="updateidRequiredEmbellishdtl_<?=$i; ?>" value="<?=$embel_arr[$key]['id'];?>">
                                </td>
                                <td><?=create_drop_down("embellishmentName_".$i, 150, $embellishment_budget_on, '', 0, '', $embel_arr[$key]['budget'] ); ?></td>
							</tr>
							<?
							$i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <input  type="hidden" name="update_id" id="update_id" value="<?=$updids; ?>"><?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==76) // Budget Un-Approved
	{
		$nameArray=sql_select( "select embellishment_budget_id, id from variable_order_tracking where company_name='$company_id' and variable_list=76 order by id" );
		/*foreach($nameArray as $vals)
		{
			$embel_arr[$vals[csf("embellishment_id")]]['id']=$vals[csf("id")];
			$embel_arr[$vals[csf("embellishment_id")]]['budget']=$vals[csf("embellishment_budget_id")];
		}*/
		
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Budget Un-Approved Validation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" class="rpt_table" id="embellishment_tbl">
                    
                    <tbody>
						<?
						$i=1;
                         $newitem_category=array(1 => "Sourcing",2 => "Booking");
                        
							?>
							<tr align="center" id="<?=$i; ?>">
                                <td>
                                    Budget Un-Approved Validation On:
                                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                                </td>
                                <td><?=create_drop_down("cbo_validation_".$i, 150, $newitem_category, '', 1, '--Select--', $nameArray[0][csf('embellishment_budget_id')] ); ?></td>
							</tr>
							<?
							 
                         
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <input  type="hidden" name="update_id" id="update_id" value="<?=$updids; ?>"><?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
    if($type==77) //Sample Style source
    {
        $nameArray=sql_select( "select style_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=77 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>Sample Style Source</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="publish_shipment_date_td">Style From Buyer inquiry</td>
                        <td width="190"><? echo create_drop_down( "style_from_library", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('style_from_library')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
	if($type==80) //pre costing V2 mandatory field
    {
        $nameArray=sql_select( "select style_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=80 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>Pre costing v2 mandatory</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="publish_shipment_date_td">Width /Dia Type</td>
                        <td width="190"><? echo create_drop_down( "style_from_library_1", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('style_from_library')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
	if($type==81) //Lab Test Budget  Validation 
    {
        $nameArray=sql_select( "select style_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=81 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>Lab Test Budget Validation for Pre costing V2</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="publish_shipment_date_td">Lab Test Budget Exceeds Value</td>
                        <td width="190"><? echo create_drop_down( "style_from_library_1", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('style_from_library')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
    if($type==82) //BOM of Yarn Approval
	{
		$nameArray=sql_select( "select bom_yarn_approval,id from  variable_order_tracking where company_name='$company_id' and variable_list=82 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>BOM of Yarn Approval</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">BOM of Yarn Approval</td>
                        <td width="190"><? echo create_drop_down( "cbo_bom_yarn_approval", 170, $pre_cost_approval,'', 1, '---- Select ----', $nameArray[0][csf('bom_yarn_approval')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
    if($type==83) //Sales Forecast
    {
        $nameArray=sql_select( "select excut_source,style_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=83 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        $sales_forecast=array(1 => "Buyer", 2 => "Brand");
        ?>
        <fieldset>
            <legend>Sales Forecast</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Sales Forecast</td>
                        <td width="190"><? echo create_drop_down( "cbo_excut_source", 170, $sales_forecast,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
                        <td width="50" align="left">Popup</td>
                        <td width="80"><? echo create_drop_down( "cbo_style_from_library", 100, $yes_no,'', 1, ' Select', $nameArray[0][csf('style_from_library')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
	if($type==86) // Negative Margin Allow In Budget  
    {
        $nameArray=sql_select( "select style_from_library,id from  variable_order_tracking where company_name='$company_id' and variable_list=86 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend> Negative Margin Allow In Budget </legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="150" align="left" id="publish_shipment_date_td"> Negative Margin Allow In Budget </td>
                        <td width="190"><? echo create_drop_down( "style_from_library_1", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('style_from_library')], "" ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
    if($type==87) //Stripe Yarn Details Calculation
	{
		$nameArray=sql_select( "select stripe_yarn_dtls_cal,id from  variable_order_tracking where company_name='$company_id' and variable_list=87 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
        $stripe_yarn_details_cal = array(1 => "Grey", 2 => "Finish");
		?>
		<fieldset>
            <legend>Stripe Yarn Details Calculation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Stripe Yarn Details Calculation</td>
                        <td width="190"><? echo create_drop_down( "cbo_stripe_yarn_details_calculation", 170, $stripe_yarn_details_cal,'', 1, '---- Select ----', $nameArray[0][csf('stripe_yarn_dtls_cal')], "",'','' ); ?> </td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
    if($type==88) //GSM Calculation Setting
    {
        $nameArray=sql_select( "select excut_source,id from  variable_order_tracking where company_name='$company_id' and variable_list=88 order by id" );
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
            <legend>GSM Calculation Setting</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" >Is GSM Writable</td>
                        <td width="190">
                            <?
                                echo create_drop_down( "cbo_gsm", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('excut_source')], "",'','' );
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
    if($type==89) // Short Quatation Validate On Budget
	{
		$nameArray=sql_select( "select short_quatation_on_budget,id from  variable_order_tracking where company_name='$company_id' and variable_list=89 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Short Quatation  On Budget</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="tna_integrated_td">Short Quatation On Budget </td>
                        <td width="190"><? echo create_drop_down( "cbo_short_quatation_on_budget", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('short_quatation_on_budget')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if($type==90) //Fabric Required Source For AOP From
	{
		$nameArray=sql_select( "select id, excut_source from variable_order_tracking where company_name='$company_id' and variable_list=90 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$fab_req_qty_source_fromArr = array(1 => "Fabric Cost", 2 => "Conversion Cost");
		?>
		<fieldset>
            <legend>Fabric Required Source For AOP From</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    <td width="160">Fabric Req. Qty. Source For AOP From</td>
                    <td width="190"><? echo create_drop_down( "cbo_excesscut_per_level", 170, $fab_req_qty_source_fromArr,'', 1, '---- Select Budget ----', $nameArray[0][csf('excut_source')], "",'','' ); ?></td>
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
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
    if($type==91) //Service Booking Dyeing Amount Validation
	{
		$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=91 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Service Booking Dyeing Amount validation </legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="txt_editable_id">Service Booking Dyeing Amount validation </td>
                        <td width="190"><? echo create_drop_down( "cbo_service_booking_dying_amount_vali", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==93) //Actual PO
	{
		$nameArray=sql_select( "select cm_cost_method,id from  variable_order_tracking where company_name='$company_id' and variable_list=93 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Actual PO Version</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="cbo_act_po_td">Use New Actual PO</td>
                        <td width="190"><? echo create_drop_down( "cbo_act_po", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('cm_cost_method')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==94) //Short Trims booking before 100% Trims booking
	{
		$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=94 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Short Trims booking before 100% Trims booking </legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="txt_editable_id">Short Trims booking before 100% Trims booking</td>
                        <td width="190"><? echo create_drop_down( "cbo_validation_yes_no", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==97) //color sensivity
	{
        $size_color_sensitive = array(1 => "As per Gmts. Color", 3 => "Contrast Color");
		$nameArray=sql_select( "select id, color_sensivity,editable from variable_order_tracking where company_name='$company_id' and variable_list=97 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$color_sensivity= $nameArray[0][csf('color_sensivity')];
       
		//if($excut_source==1 || $excut_source==3) $disabled_td=1; else $disabled_td='';
		?>
		<fieldset>
            <legend>Color Sensivity</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="180" align="left">Color Sensivity</td>
                        <td width="100"><? echo create_drop_down( "cbo_color_sensivity", 150, $size_color_sensitive,'', 1, '---- Select ----', $nameArray[0][csf('color_sensivity')], "",'','' ); ?></td>
                    </tr>
                    <tr>
                        <td width="50" align="left">Is Disable</td>
                        <td width="90"><? echo create_drop_down( "cbo_editable_id",150, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",$disabled_td,'' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}

    if($type==98){

        $nameArray=sql_select( "select editable,gmt_num_rep_sty,id from  variable_order_tracking where company_name='$company_id' and variable_list=98 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Thread Consumption Calculation Method</legend>
            <div style="width:740px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <?php
                        $RequiredArr = array(1=>'(Seam Length*Thread Length)+{(Seam Length*Thread Length)/100}*Allowance', 2=>'(Thread Length*Frequency*Allowance)');
                        $ThreadLengthArr = array(1=>'(Seam Length*Consumption Factor*Needle/Bobbin Thread %)');
                        ?>
                        <td width="230" align="left" id="txt_editable_id">Required Calculation Method Type</td>
                        <td width="190"><? echo create_drop_down("cbo_required_calculation_method", 170, $RequiredArr,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
                        <td width="250" align="left" id="txt_editable_id">Thread Length Calculation Method Type</td>
                        <td width="200"><? echo create_drop_down("cbo_thread_length_calculation_method", 170, $ThreadLengthArr,'', 1, '---- Select ----', $nameArray[0][csf('gmt_num_rep_sty')], "",'','');?></td>
                    </tr>
                </table>
            </div>
            <div style="width:740px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
    }
    if($type==99) //Cost percentage Calculation
	{
		$nameArray=sql_select( "select cost_control_source,id from  variable_order_tracking where company_name='$company_id' and variable_list=99 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
        $calculationtypeArr = array(1=>'Total Cost', 2=>'Price Before Commn/ 1 Dzn');
		?>
		<fieldset>
            <legend>Price Quotation Cost percentage Calculation</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left" id="copy_quotation_td">Calculation Based On</td>
                        <td width="190"><? echo create_drop_down( "cbo_percentage_calculation", 170, $calculationtypeArr,'', 1, '---- Select ----', $nameArray[0][csf('cost_control_source')], "" ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
    if($type==100) //Short trims booking available
	{
		$nameArray=sql_select( "select short_booking_available,id from  variable_order_tracking where company_name='$company_id' and variable_list=100 order by id" );
        
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Avaialable To Short  Trims Booking Approval Page</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="short_booking_td">Avaialable To Short Short Trims Booking Approval Page</td>
                        <td width="190"><? echo create_drop_down( "cbo_short_booking", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('short_booking_available')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==101) // Color Update After Batch
	{
		$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=101 order by id" );
        
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Color Update After Batch</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="txt_editable_id">Color Update After Batch</td>
                        <td width="190"><? echo create_drop_down( "cbo_color_update_afb", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==102) //  Fabric Change after Knitting
	{
		$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=102 order by id" );
        
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend> Fabric Change after Knitting</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="txt_editable_id"> Fabric Change after Knitting</td>
                        <td width="190"><? echo create_drop_down( "cbo_fabric_change_af_knitting", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}
    if($type==104) //Yarn Additional Booking Before 100% Yarn Booking
	{
		$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=104 order by id" );
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
		<fieldset>
            <legend>Yarn Additional Booking Before 100% Yarn Booking </legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="txt_editable_id">Yarn Additional Booking Before 100% Yarn Booking</td>
                        <td width="190"><? echo create_drop_down( "cbo_validation_yes_no", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('editable')], "",'','' ); ?></td>
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
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?=load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('ordertrackingvariablesettings_1','','',1)"); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
		<?
	}


	exit();
}

if ($action=="save_update_delete_material_control")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$item_category_id=explode(",",$item_category_id);
	$exeed_budget_qty=explode(",",$exeed_budget_qty);
	$exeed_budget_amt=explode(",",$exeed_budget_amt);
	$amt_exceed_lavel=explode(",",$amt_exceed_lavel);
	$cbo_exceed_qty_level=explode(",",$cbo_exceed_qty_level);

	if ($operation==0 || $operation==1 )  // Insert Here
	{
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $rID2 = true;
        
        if( $operation==1 ) 
        {
            $nameArray=sql_select( "select exeed_budge_qty,exeed_budge_amount,amount_exceed_level,item_category_id,id from  variable_order_tracking where company_name=$cbo_company_name_wo and variable_list=26 and status_active=1 and is_deleted=0 order by id" );
		    if(count($nameArray)>0)
            {
                $existing_setup_deleted_sql = " UPDATE variable_order_tracking SET status_active=0 , is_deleted=1 WHERE company_name=$cbo_company_name_wo and variable_list=26";
                $rID2 =  execute_query($existing_setup_deleted_sql);
                if(!$rID2)
                {
                    echo "UPDATE variable_order_tracking SET status_active=0 , is_deleted=1 WHERE company_name=$cbo_company_name_wo and variable_list=26";
                }
            }          
        }
   
        $data_array="";
        $field_array="id,company_name,variable_list,exeed_budge_qty,exeed_budge_amount,amount_exceed_level,item_category_id,exceed_qty_level,inserted_by,insert_date,status_active,is_deleted";
        
        for($i=0;$i<count($item_category_id);$i++)
        {
            if ( $operation==0 && is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo and item_category_id=".$item_category_id[$i]." and status_active=1 and is_deleted=0 " ) == 1)
            {
                echo 11; die;
            }
            else
            {
                if( $id=="" ) $id = return_next_id( "id", "variable_order_tracking", 1 ); else $id = $id+1;
                if($i==0)
                    $data_array .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$exeed_budget_qty[$i]."','".$exeed_budget_amt[$i]."',".$amt_exceed_lavel[$i].",'".$item_category_id[$i]."','".$cbo_exceed_qty_level[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
                else
                    $data_array .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$exeed_budget_qty[$i]."','".$exeed_budget_amt[$i]."',".$amt_exceed_lavel[$i].",'".$item_category_id[$i]."','".$cbo_exceed_qty_level[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
            }
        }
        //echo $data_array;
        //echo "10**INSERT INTO variable_order_tracking (".$field_array.") VALUES ".$data_array.""; die;

        $rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);
        
        if($db_type==0)
        {
            if($rID && $rID2){
                mysql_query("COMMIT");
                echo 0;
            }
            else{
                mysql_query("ROLLBACK");
                echo 10;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2)
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
	/* else if ($operation==1)   // Update Here
	{
        $update_id=explode(",",$update_id);
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $field_array="company_name*variable_list*exeed_budge_qty*exeed_budge_amount*amount_exceed_level*item_category_id*exceed_qty_level*updated_by*update_date";

        for($i=0;$i<count($item_category_id);$i++)
        {
            $data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".$exeed_budget_qty[$i]."'*'".$exeed_budget_amt[$i]."'*".$amt_exceed_lavel[$i]."*".$item_category_id[$i]."*".$cbo_exceed_qty_level[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
           
            if($update_id[$i]>0)
            {
                $rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id[$i]."",1);
            }
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
        else  if($db_type==2 || $db_type==1 )
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

    } */
}

if($action=="save_update_delete_s_f_before_m_f")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1 ) ;
			$field_array="id,company_name,variable_list,s_f_booking_befor_m_f,inserted_by,insert_date,status_active";
			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_s_f.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

			//echo "5**insert into variable_order_tracking ($field_array) values $data_array"; die;

            $rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

            if($db_type==0)
            {
                if($rID )
                {
                   mysql_query("COMMIT");
                   echo 0;
               }
               else{
                   mysql_query("ROLLBACK");
                   echo 10;
               }
           }
          else  if($db_type==2 || $db_type==1 )
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
}
	else if ($operation==1)   // Update Here
	{

     $con = connect();
     if($db_type==0)
     {
        mysql_query("BEGIN");
    }

    $field_array="company_name*variable_list*s_f_booking_befor_m_f*updated_by*update_date";
    $data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*".$cbo_s_f."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id."",1);  //   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
			else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete")
{
   $process = array( &$_POST );
   extract(check_magic_quote_gpc( $process ));

    // print_r(str_replace("'", "", $cbo_variable_list_wo));
    //    die;
	if ($operation==0)  // Insert Here
	{
		if(str_replace("'","", $cbo_variable_list_wo)==27 || str_replace("'","", $cbo_variable_list_wo)==57)
		{
			if(is_duplicate_field( "company_name", "variable_order_tracking","company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo and tna_integrated=$cbo_buyer_id and profit_calculative=$cbo_brand_id") == 1)
			{
				echo 11; die;
			}
		}
		else
		{
			if(is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
			{
				echo 11; die;
			}
		}
			
		$con = connect();

		$id=return_next_id( "id", "variable_order_tracking", 1 ) ;

		if(str_replace("'","", $cbo_variable_list_wo)==56 || str_replace("'","", $cbo_variable_list_wo)==75)
		{
			$field_array="id,company_name ,embellishment_id,embellishment_budget_id,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array="";

			for ($i=1;$i<=$total_row;$i++)
			{
				$cbo_embellishment_type="cboEmbellishmentTypeHidden_".$i;
				$embellishmentName="embellishmentName_".$i;

				if ($data_array!='') $data_array .=",";

				$data_array .="(".$id.",". $cbo_company_name_wo.",".$$cbo_embellishment_type.",".$$embellishmentName.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$id=$id+1;
			}
		}
		else if(str_replace("'","", $cbo_variable_list_wo)==76)
		{
			$field_array="id,company_name ,embellishment_budget_id,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array="";

			for ($i=1;$i<=$total_row;$i++)
			{
				//$cbo_embellishment_type="cboEmbellishmentTypeHidden_".$i;
				$cbo_validation="cbo_validation_".$i;

				if ($data_array!='') $data_array .=",";

				$data_array .="(".$id.",". $cbo_company_name_wo.",".$$cbo_validation.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$id=$id+1;
			}
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==77)
		{
			$field_array="id, company_name, style_from_library, variable_list, inserted_by, insert_date, status_active, is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$style_from_library.",".$cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==80 ) //budget mandatory
		{
			$field_array="id, company_name, style_from_library, variable_list, inserted_by, insert_date, status_active, is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$style_from_library_1.",".$cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==81 || str_replace("'","", $cbo_variable_list_wo)==86) //budget Validation// Negative Margin Allow In Budget 
		{
			$field_array="id, company_name, style_from_library, variable_list, inserted_by, insert_date, status_active, is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$style_from_library_1.",".$cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==71)
		{
			$field_array="id,company_name,excut_source,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$cbo_fabric_ref_automation.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==74)
		{
		   //exceed_qty_level excut_source
			$field_array="id,company_name,excut_source,exceed_qty_level,editable,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$txt_without_emblish.",".$txt_with_emblish.",".$cbo_auto_acknowledge.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==83)
		{
		   //exceed_qty_level excut_source
			$field_array="id,company_name,excut_source,style_from_library,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$cbo_excut_source.",".$cbo_style_from_library.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==88)
		{
		   //exceed_qty_level excut_source
			$field_array="id,company_name,excut_source,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$cbo_gsm.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";                
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==91)
		{
			$is_editable=str_replace("'",'',$cbo_service_booking_dying_amount_vali);
		   //exceed_qty_level excut_source
			$field_array="id,company_name,editable,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$is_editable.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}

      
        elseif(str_replace("'","", $cbo_variable_list_wo)==98)
		{
			$is_editable=str_replace("'",'',$cbo_required_calculation_method);
			$gmt_num_rep_sty=str_replace("'",'',$cbo_thread_length_calculation_method);

		   //exceed_qty_level excut_source
			$field_array="id,company_name,editable,gmt_num_rep_sty,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$is_editable.",".$gmt_num_rep_sty.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
		}

        
		else if(str_replace("'","", $cbo_variable_list_wo)==94 || str_replace("'","", $cbo_variable_list_wo)==104)
		{
			$is_editable=str_replace("'",'',$cbo_validation_yes_no);
		   //exceed_qty_level excut_source
			$field_array="id,company_name,editable,variable_list,inserted_by,insert_date,status_active,is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",".$is_editable.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}
		else if(str_replace("'","", $cbo_variable_list_wo)==27 || str_replace("'","", $cbo_variable_list_wo)==57)
		{
			$field_array="id, company_name, variable_list, commercial_cost_method, tna_integrated, profit_calculative, excut_source, copy_quotation, editable, commercial_cost_percent, inserted_by, insert_date, status_active, is_deleted";
			$data_array ="(".$id.",". $cbo_company_name_wo.",". $cbo_variable_list_wo.",". $cbo_commercial_cost_method.",". $cbo_buyer_id.",". $cbo_brand_id.",".$cbo_compulsory.",". $cbo_based_on.",".$cbo_editable.",'".$txt_commercial_cost_percent."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}
        else if(str_replace("'","", $cbo_variable_list_wo)==31)
        {
            $field_array="id,company_name,variable_list,tna_process_type,textile_tna_process_base,inserted_by, insert_date";
            $data_array ="(".$id.",". $cbo_company_name_wo.",". $cbo_variable_list_wo.",". $tna_process_type.",". $cbo_based_on.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
            //$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
        }
        else if(str_replace("'","", $cbo_variable_list_wo)==99)
        {
            $field_array="id,company_name,variable_list,cost_control_source,inserted_by,insert_date";
            $data_array ="(".$id.",". $cbo_company_name_wo.",". $cbo_variable_list_wo.",". $percentage_calculation.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
        }
		else
		{
			$field_array="id, company_name, variable_list, sales_year_started, tna_integrated, profit_calculative, process_loss_method, item_category_id, consumption_basis, copy_quotation, cm_cost_method, color_from_library, publish_shipment_date, short_quatation_on_budget, style_from_library, commercial_cost_method, commercial_cost_percent, editable, gmt_num_rep_sty, duplicate_ship_date, image_mandatory, tna_process_type, po_update_period, po_current_date, inquery_id_mandatory, trim_rate, cm_cost_method_quata, budget_exceeds_quot, lab_test_rate_update, colar_culff_percent, pre_cost_approval, price_quo_approval, report_date_catagory, tna_process_start_date, default_fabric_nature, default_fabric_source, bom_page_setting, cost_control_source, user_id, cm_cost_method_based_on, work_study_mapping_id, cm_cost_compulsory, fabric_source_aop_id, yarn_iss_with_serv_app, textile_tna_process_base, excut_source, yd_wo_used, bom_yarn_approval, stripe_yarn_dtls_cal, inserted_by, insert_date, status_active"; //cbo_cm_cost_compulsory
		   
			if(str_replace("'",'',$cbo_variable_list_wo)==22) $is_editable=str_replace("'",'',$cbo_cm_cost_editable);
			else if(str_replace("'",'',$cbo_variable_list_wo)==47) $is_editable=str_replace("'",'',$style_editable);
		   
			else $is_editable=str_replace("'",'',$cbo_editable);
			if(str_replace("'",'',$cbo_variable_list_wo)==25) $cbo_duplicate_ship_date=str_replace("'",'',$cbo_next_process_shipdate);
			else $cbo_duplicate_ship_date=str_replace("'",'',$cbo_duplicate_ship_date);

			if(str_replace("'",'',$cbo_variable_list_wo)==93) $cbo_cm_cost_method=$cbo_act_po;

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".str_replace("'",'',$cbo_sales_year_started_date)."','".str_replace("'",'',$cbo_tna_integrated)."','".str_replace("'",'',$cbo_profit_calculative)."','".str_replace("'",'',$process_loss_methods)."','".str_replace("'",'',$item_category_ids)."','".str_replace(",",'',$cbo_consumption_basis)."','".str_replace(",",'',$cbo_copy_quotation)."','".str_replace("'",'',$cbo_cm_cost_method)."','".str_replace("'",'',$cbo_color_from_library)."','".str_replace("'",'',$publish_shipment_date)."','".str_replace("'",'',$short_quatation_on_budget)."','".str_replace("'",'',$style_from_library)."','".str_replace("'",'',$cbo_commercial_cost_method)."','".str_replace("'",'',$txt_commercial_cost_percent)."','".$is_editable."','".str_replace("'",'',$txt_size_wise_repeat)."','".str_replace("'",'',$cbo_duplicate_ship_date)."','".str_replace("'",'',$image_mandatory)."','".str_replace("'",'',$tna_process_type)."','".str_replace("'",'',$update_period)."','".str_replace("'",'',$po_current_date)."','".str_replace("'",'',$inquery_id_mandatory)."','".str_replace("'",'',$cbo_trim_rate)."','".str_replace("'",'',$cbo_cm_cost_method_quata)."','".str_replace("'",'',$cbo_budget_exceeds_quot)."','".str_replace("'",'',$cbo_lab_test_rate)."','".str_replace("'",'',$cbo_colar_culff_percent)."','".str_replace("'",'',$cbo_pre_cost_approval)."','".str_replace("'",'',$cbo_price_quo_approval)."','".str_replace("'",'',$cbo_report_date_catagory)."','".str_replace("'",'',$txt_tna_process_start_date)."','".str_replace("'",'',$cbo_default_febric_nature)."','".str_replace("'",'',$cbo_default_fabric_source)."','".str_replace("'",'',$cbo_bom_page)."','".str_replace("'",'',$cbo_cost_control_source)."','".str_replace("'",'',$user_hidden_id)."','".str_replace("'",'',$cbo_cm_cost_method_based_on)."','".str_replace("'",'',$cbo_work_study_mapping)."','".str_replace("'",'',$cbo_cm_cost_compulsory)."','".str_replace("'",'',$cbo_fabric_source_aop_id)."','".str_replace("'",'',$cbo_yarn_iss_with_serv_app)."','".str_replace("'",'',$cbo_textile_tna_process_base)."','".str_replace("'",'',$cbo_excesscut_per_level)."','".str_replace("'",'',$cbo_yarn_lot_used_from_library)."','".str_replace("'",'',$cbo_bom_yarn_approval)."','".str_replace("'",'',$cbo_stripe_yarn_details_calculation)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		
		//echo "10**INSERT INTO variable_order_tracking (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);
		
		//echo "10**$rID"; disconnect($con); die;
		 
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
		if(str_replace("'","", $cbo_variable_list_wo)==27 || str_replace("'","", $cbo_variable_list_wo)==57)
		{
			if(is_duplicate_field( "company_name", "variable_order_tracking","company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo and tna_integrated=$cbo_buyer_id and profit_calculative=$cbo_brand_id and id!=$update_id") == 1)
			{
				echo 11; die;
			}
		}
		
		$con = connect();
		$rID1=true;
        if(str_replace("'", "", $cbo_variable_list_wo)==56 || str_replace("'","", $cbo_variable_list_wo)==75)
        {
            $field_array_up="company_name*embellishment_id*embellishment_budget_id*variable_list*updated_by*update_date";
            $field_array="id,company_name ,embellishment_id,embellishment_budget_id,variable_list,inserted_by,insert_date,status_active,is_deleted";
            $data_array="";
            $id=return_next_id( "id", "variable_order_tracking", 1 ) ;
            for ($i=1;$i<=$total_row;$i++)
            {
                $cbo_embellishment_type="cboEmbellishmentTypeHidden_".$i;
                $embellishmentName="embellishmentName_".$i;
                $updateIdDtls="updateidRequiredEmbellishdtl_".$i;
                if (str_replace("'",'',$$updateIdDtls)!="")
                {
                    $id_arr[]=str_replace("'",'',$$updateIdDtls);

                    $data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$cbo_company_name_wo."*".$$cbo_embellishment_type."*".$$embellishmentName."*".$cbo_variable_list_wo."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                }
                else
                {
                     if ($data_array!='') $data_array .=",";
                     $data_array .="(".$id.",". $cbo_company_name_wo.",".$$cbo_embellishment_type.",".$$embellishmentName.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
                     $id=$id+1;
                }
            }
			//echo "10**".bulk_update_sql_statement("variable_order_tracking", "id",$field_array_up,$data_array_up,$id_arr ); die;
            if($data_array_up!="")
            {
                $rID=execute_query(bulk_update_sql_statement("variable_order_tracking", "id",$field_array_up,$data_array_up,$id_arr ));
            }
            if($data_array!='')
            {
                 $rID1=sql_insert("variable_order_tracking",$field_array,$data_array,1);
            }
        }
		else if(str_replace("'","", $cbo_variable_list_wo)==76)//Budget UnApproved Validation
		{
			$update_id=str_replace("'",'',$update_id);
			$field_array_up="company_name*embellishment_budget_id*variable_list*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
            {
				//$cbo_embellishment_type="cboEmbellishmentTypeHidden_".$i;
				$cbo_validation="cbo_validation_".$i;
				//$updateIdDtls="updateidRequiredEmbellishdtl_".$i;

				$id=$id+1;
				if (str_replace("'",'',$update_id)!="")
				{
					$id_arr[]=str_replace("'",'',$update_id);

					$data_array_up[str_replace("'",'',$update_id)] =explode("*",("".$cbo_company_name_wo."*".$$cbo_validation."*".$cbo_variable_list_wo."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
            }
            if($data_array_up!="")
            {
               $rID=execute_query(bulk_update_sql_statement("variable_order_tracking", "id",$field_array_up,$data_array_up,$id_arr ));
				// $rID=sql_update("variable_order_tracking", $field_array_up, $data_array_up,"id","".$update_id."",1);  
            }
		}
		//
        elseif(str_replace("'", "", $cbo_variable_list_wo)==77)
        {
            $field_array="company_name*style_from_library*variable_list*updated_by*update_date";           

            $data_array ="". $cbo_company_name_wo."*".$style_from_library."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);    
        }
		elseif(str_replace("'", "", $cbo_variable_list_wo)==80)
        {
            $field_array="company_name*style_from_library*variable_list*updated_by*update_date";           

            $data_array ="". $cbo_company_name_wo."*".$style_from_library_1."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);    
        }
		elseif(str_replace("'", "", $cbo_variable_list_wo)==81 || str_replace("'", "", $cbo_variable_list_wo)==86)
        {
            $field_array="company_name*style_from_library*variable_list*updated_by*update_date";           

            $data_array ="". $cbo_company_name_wo."*".$style_from_library_1."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);    
        }
        elseif(str_replace("'", "", $cbo_variable_list_wo)==71) //,".$txt_without_emblish.",".$txt_with_emblish."
        {
            $field_array="company_name*excut_source*variable_list*updated_by*update_date";           

            $data_array ="". $cbo_company_name_wo."*".$cbo_fabric_ref_automation."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);    
        }
		elseif(str_replace("'", "", $cbo_variable_list_wo)==74) //
        {
            $field_array="company_name*excut_source*exceed_qty_level*editable*variable_list*updated_by*update_date";           

            $data_array ="". $cbo_company_name_wo."*".$txt_without_emblish."*".$txt_with_emblish."*".$cbo_auto_acknowledge."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);    
        }
        elseif(str_replace("'","", $cbo_variable_list_wo)==83)
        {
            $field_array="company_name*excut_source*style_from_library*variable_list*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*".$cbo_excut_source."*".$cbo_style_from_library."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);                
        }
        elseif(str_replace("'","", $cbo_variable_list_wo)==88)
        {
            $field_array="company_name*excut_source*variable_list*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*".$cbo_excut_source."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);               
        } 
		elseif(str_replace("'","", $cbo_variable_list_wo)==91)
        {
            $is_editable=str_replace("'",'',$cbo_service_booking_dying_amount_vali);
            $field_array="company_name*editable*variable_list*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*".$is_editable."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);               
        } 


        elseif(str_replace("'","", $cbo_variable_list_wo)==98)
		{
            $is_editable=str_replace("'",'',$cbo_required_calculation_method);
            $gmt_num_rep_sty=str_replace("'",'',$cbo_thread_length_calculation_method);
            $field_array="company_name*editable*gmt_num_rep_sty*variable_list*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*".$is_editable."*".$gmt_num_rep_sty."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		}
		elseif(str_replace("'","", $cbo_variable_list_wo)==94 || str_replace("'","", $cbo_variable_list_wo)==104)
        {
            $is_editable=str_replace("'",'',$cbo_validation_yes_no);
            $field_array="company_name*editable*variable_list*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*".$is_editable."*". $cbo_variable_list_wo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
           $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);               
        }
		else if(str_replace("'","", $cbo_variable_list_wo)==27 || str_replace("'","", $cbo_variable_list_wo)==57)
        {
            $field_array="company_name*variable_list*commercial_cost_method*tna_integrated*profit_calculative*excut_source*copy_quotation*editable*commercial_cost_percent*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*". $cbo_variable_list_wo."*". $cbo_commercial_cost_method."*". $cbo_buyer_id."*". $cbo_brand_id."*". $cbo_compulsory."*". $cbo_based_on."*".$cbo_editable."*'". $txt_commercial_cost_percent."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);               
        }
        else if(str_replace("'","", $cbo_variable_list_wo)==31)
        {
            $field_array="company_name*variable_list*tna_process_type*textile_tna_process_base*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*". $cbo_variable_list_wo."*". $tna_process_type."*". $cbo_based_on."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);               
        }
        else if(str_replace("'","", $cbo_variable_list_wo)==99)
        {
            $field_array="company_name*variable_list*cost_control_source*updated_by*update_date";
            $data_array ="". $cbo_company_name_wo."*". $cbo_variable_list_wo."*". $percentage_calculation."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1); 
        }
		else //exceed_qty_level excut_source
		{ 
			$field_array="company_name*variable_list*sales_year_started*tna_integrated*profit_calculative*process_loss_method*item_category_id*consumption_basis*copy_quotation*cm_cost_method*color_from_library*publish_shipment_date*short_quatation_on_budget*style_from_library*commercial_cost_method*commercial_cost_percent*editable*gmt_num_rep_sty*duplicate_ship_date*image_mandatory*tna_process_type*po_update_period*po_current_date*inquery_id_mandatory*trim_rate*cm_cost_method_quata*budget_exceeds_quot*lab_test_rate_update*colar_culff_percent*pre_cost_approval*price_quo_approval*report_date_catagory*tna_process_start_date*default_fabric_nature*default_fabric_source*bom_page_setting*cost_control_source*user_id*cm_cost_method_based_on*work_study_mapping_id*cm_cost_compulsory*fabric_source_aop_id*yarn_iss_with_serv_app*textile_tna_process_base*excut_source*yd_wo_used*bom_yarn_approval*stripe_yarn_dtls_cal*updated_by*update_date";
            
			if(str_replace("'",'',$cbo_variable_list_wo)==22) $is_editable=str_replace("'",'',$cbo_cm_cost_editable);
            else if(str_replace("'",'',$cbo_variable_list_wo)==47) $is_editable=str_replace("'",'',$style_editable);
			else $is_editable=str_replace("'",'',$cbo_editable);
			if(str_replace("'",'',$cbo_variable_list_wo)==25) $cbo_duplicate_ship_date=str_replace("'",'',$cbo_next_process_shipdate);
			else $cbo_duplicate_ship_date=str_replace("'",'',$cbo_duplicate_ship_date);
            
            if(str_replace("'",'',$cbo_variable_list_wo)==93) $cbo_cm_cost_method=$cbo_act_po;
			
			$data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".str_replace("'",'',$cbo_sales_year_started_date)."'*'".str_replace("'",'',$cbo_tna_integrated)."'*'".str_replace("'",'',$cbo_profit_calculative)."'*'".str_replace("'",'',$process_loss_methods)."'*'".str_replace("'",'',$item_category_ids)."'*'".str_replace("'",'',$cbo_consumption_basis)."'*'".str_replace("'",'',$cbo_copy_quotation)."'*'".str_replace("'",'',$cbo_cm_cost_method)."'*'".str_replace("'",'',$cbo_color_from_library)."'*'".str_replace("'",'',$publish_shipment_date)."'*'".str_replace("'",'',$short_quatation_on_budget)."'*'".str_replace("'",'',$style_from_library)."'*'".str_replace("'",'',$cbo_commercial_cost_method)."'*'".str_replace("'",'',$txt_commercial_cost_percent)."'*'".$is_editable."'*'".str_replace("'",'',$txt_size_wise_repeat)."'*'".str_replace("'",'',$cbo_duplicate_ship_date)."'*'".str_replace("'",'',$image_mandatory)."'*'".str_replace("'",'',$tna_process_type)."'*'".str_replace("'",'',$update_period)."'*'".str_replace("'",'',$po_current_date)."'*'".str_replace("'",'',$inquery_id_mandatory)."'*'".str_replace("'",'',$cbo_trim_rate)."'*'".str_replace("'",'',$cbo_cm_cost_method_quata)."'*'".str_replace("'",'',$cbo_budget_exceeds_quot)."'*'".str_replace("'",'',$cbo_lab_test_rate)."'*'".str_replace("'",'',$cbo_colar_culff_percent)."'*'".str_replace("'",'',$cbo_pre_cost_approval)."'*'".str_replace("'",'',$cbo_price_quo_approval)."'*'".str_replace("'",'',$cbo_report_date_catagory)."'*'".str_replace("'",'',$txt_tna_process_start_date)."'*'".str_replace("'",'',$cbo_default_febric_nature)."'*'".str_replace("'",'',$cbo_default_fabric_source)."'*'".str_replace("'",'',$cbo_bom_page)."'*'".str_replace("'",'',$cbo_cost_control_source)."'*'".str_replace("'",'',$user_hidden_id)."'*'".str_replace("'",'',$cbo_cm_cost_method_based_on)."'*'".str_replace("'",'',$cbo_work_study_mapping)."'*'".str_replace("'",'',$cbo_cm_cost_compulsory)."'*'".str_replace("'",'',$cbo_fabric_source_aop_id)."'*'".str_replace("'",'',$cbo_yarn_iss_with_serv_app)."'*'".str_replace("'",'',$cbo_textile_tna_process_base)."'*'".str_replace("'",'',$cbo_excesscut_per_level)."'*'".str_replace("'",'',$cbo_yarn_lot_used_from_library)."'*'".str_replace("'",'',$cbo_bom_yarn_approval)."'*'".str_replace("'",'',$cbo_stripe_yarn_details_calculation)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
			//echo  "10**".$cbo_duplicate_ship_date.'SS';die;
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
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

if ($action=="save_update_delete_process_loss_method")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$data_array="";

			if(str_replace("'",'',$cbo_variable_list_wo)==18)
			{
				$item_category_id=explode(",",$item_category_id);
				$process_loss_method=explode(",",$process_loss_method);
				$field_array="id, company_name, variable_list, sales_year_started_hcode, sales_year_started, tna_integrated, profit_calculative, process_loss_method, item_category_id, inserted_by, insert_date, status_active";
				for($i=0;$i<count($item_category_id);$i++)
				{
					if( $id=="" ) $id = return_next_id( "id", "variable_order_tracking", 1 ); else $id = $id+1;
					if($i==0)
						$data_array .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$sales_year_started."','".$cbo_sales_year_started_date."','".$cbo_tna_integrated."','".$cbo_profit_calculative."','".$process_loss_method[$i]."','".$item_category_id[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
					else
						$data_array .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$sales_year_started."','".$cbo_sales_year_started_date."','".$cbo_tna_integrated."','".$cbo_profit_calculative."','".$process_loss_method[$i]."','".$item_category_id[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
			}
			else if(str_replace("'",'',$cbo_variable_list_wo)==21)
			{
				$field_array="id, company_name, variable_list, rate_type, conversion_from_chart, inserted_by, insert_date, status_active";
				$rate_type=explode(",",$rate_type);
				$conversion_from_chart=explode(",",$conversion_from_chart);
				for($i=0;$i<count($rate_type);$i++)
				{
					if( $id=="" ) $id = return_next_id( "id", "variable_order_tracking", 1 ); else $id = $id+1;
					if($i==0)
						$data_array .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$rate_type[$i]."','".$conversion_from_chart[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
					else
						$data_array .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$rate_type[$i]."','".$conversion_from_chart[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
			}
			//echo $data_array;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);
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
			else if($db_type==2 || $db_type==1 )
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
    }
	else if ($operation==1)   // Update Here
	{
		$update_id=explode(",",$update_id);
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'",'',$cbo_variable_list_wo)==18)
		{
			$item_category_id=explode(",",$item_category_id);
			$process_loss_method=explode(",",$process_loss_method);
			for($i=0;$i<count($item_category_id);$i++)
			{
				$field_array="company_name*variable_list*process_loss_method*item_category_id*updated_by*update_date";
				$data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".$process_loss_method[$i]."'*".$item_category_id[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                 if($update_id[$i])
				$rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id[$i]."",1);
			}
		}
		else if(str_replace("'",'',$cbo_variable_list_wo)==21)
		{
			$field_array="company_name*variable_list*rate_type*conversion_from_chart*updated_by*update_date";
			$rate_type=explode(",",$rate_type);
			$conversion_from_chart=explode(",",$conversion_from_chart);
			for($i=0;$i<count($rate_type);$i++)
			{
				$data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".$rate_type[$i]."'*".$conversion_from_chart[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id[$i]."",1);
			}
		}
		//print_r( $data_array);
		//echo "10**";die;

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
		else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete_season_mandatory")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, season_mandatory, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_season_mandatory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="season_mandatory*updated_by*update_date";

		$data_array="".$cbo_season_mandatory."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete_trims_booking")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, short_booking_available, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_short_booking.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="short_booking_available*updated_by*update_date";

		$data_array="".$cbo_short_booking."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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
if ($action=="save_update_delete_color_update_after_batch")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, editable, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_color_update_afb.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="editable*updated_by*update_date";

		$data_array="".$cbo_color_update_afb."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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
if ($action=="save_update_delete_excess_cut_source")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, excut_source,editable, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_excess_cut_source.",".$cbo_editable_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="excut_source*editable*updated_by*update_date";

		$data_array="".$cbo_excess_cut_source."*".$cbo_editable_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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
if ($action=="save_update_delete_color_update_fabric_change_af_knitting")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, editable, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_fabric_change_af_knitting.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="editable*updated_by*update_date";

		$data_array="".$cbo_fabric_change_af_knitting."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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
if ($action=="save_update_delete_color_sensivity")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, color_sensivity,editable, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_color_sensivity.",".$cbo_editable_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="color_sensivity*editable*updated_by*update_date";

		$data_array="".$cbo_color_sensivity."*".$cbo_editable_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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

if ($action=="min_lead_time_control")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, min_lead_time_control, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_min_lead_time_control.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="min_lead_time_control*updated_by*update_date";

		$data_array="".$cbo_min_lead_time_control."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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
// NEW

if ($action=="po_entry_limit_on_capacity")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_order_tracking", 1) ;

			$field_array="id, company_name, variable_list, buyer_allocation_maintain,capacity_exceed_level,exeed_budge_qty, inserted_by, insert_date, status_active";

			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_buyer_allocation_maintain.",".$cbo_capacity_exceed_level.",".$cbo_actpo_exceed_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			//echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo 0;
				}
				else{
					mysql_query("ROLLBACK");
					echo 10;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="buyer_allocation_maintain*capacity_exceed_level*exeed_budge_qty*updated_by*update_date";

		$data_array="".$cbo_buyer_allocation_maintain."*".$cbo_capacity_exceed_level."*".$cbo_actpo_exceed_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
		//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
		else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete_effeciency_slab")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0)  // Insert Here
    {
        if ( is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
        {
            echo 11; die;
        }
        else
        {
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }
            $id=return_next_id( "id", "variable_order_tracking", 1) ;

            $field_array="id, company_name, variable_list, efficiency_source_for_pre_cost, inserted_by, insert_date, status_active";

            $data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_efficiency_source_for_pre_cost.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
            //echo "10**insert into variable_order_tracking($field_array)values".$data_array;die;
            $rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);

            if($db_type==0)
            {
                if($rID )
                {
                    mysql_query("COMMIT");
                    echo 0;
                }
                else{
                    mysql_query("ROLLBACK");
                    echo 10;
                }
            }
            else if($db_type==2 || $db_type==1 )
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
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        $field_array="efficiency_source_for_pre_cost*updated_by*update_date";

        $data_array="".$cbo_efficiency_source_for_pre_cost."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);
        //   '".$cbo_color_from_library."'*'".$publish_shipment_date."'*
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
        else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete_booking_source")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "company_name", "variable_order_tracking", "company_name=$cbo_company_name_wo and variable_list=$cbo_variable_list_wo" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$data_array="";

			if(str_replace("'",'',$cbo_variable_list_wo)==72)
			{
				$field_array="id, company_name, variable_list, ITEM_CATEGORY_ID, EXCUT_SOURCE, inserted_by, insert_date, status_active";
				$source_id_arr=explode(",",$source_id);
				$booking_type_arr=explode(",",$booking_type);
				
				for($i=0;$i<count($booking_type_arr);$i++)
				{
					if( $id=="" ) $id = return_next_id( "id", "variable_order_tracking", 1 ); else $id = $id+1;
					if($i==0){
						$data_array .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$booking_type_arr[$i]."','".$source_id_arr[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
					}
					else{
						$data_array .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$booking_type_arr[$i]."','".$source_id_arr[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
					}
					$id++;
				}
			}
			//echo $data_array;
			$rID=sql_insert("variable_order_tracking",$field_array,$data_array,1);
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
			else if($db_type==2 || $db_type==1 )
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
    }
	else if ($operation==1)   // Update Here
	{
		$update_id=explode(",",rtrim($update_id,','));
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'",'',$cbo_variable_list_wo)==72)
		{
			$field_array="company_name*variable_list*ITEM_CATEGORY_ID*EXCUT_SOURCE*updated_by*update_date";
            $field_array_insert="id, company_name, variable_list, ITEM_CATEGORY_ID, EXCUT_SOURCE, inserted_by, insert_date, status_active";
			$source_id_arr=explode(",",$source_id);
			$booking_type_arr=explode(",",$booking_type);
            $k=0;
            $rID2=true;
			for($i=0;$i<count($booking_type_arr);$i++)
			{
                if ($update_id[$i] != '')
                {
                    $data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".$booking_type_arr[$i]."'*".$source_id_arr[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                    $rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id[$i]."",1);
                }
                else
                {
                    $id = return_next_id( "id", "variable_order_tracking", 1 );
                    if($k==0){
                        $data_array_insert .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$booking_type_arr[$i]."','".$source_id_arr[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
                    }
                    else{
                        $data_array_insert .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$booking_type_arr[$i]."','".$source_id_arr[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
                    }
                    $id++;
                    $k++;
                }   
				
			}

            if ($data_array_insert != ''){
                $rID2=sql_insert("variable_order_tracking",$field_array_insert,$data_array_insert,1);                
            }
		}

		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");
				echo 1;
			}
			else{
				mysql_query("ROLLBACK");
				echo 10;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
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


if ($action=="buyer_brand_wise_commercial_currier_list_view")
{
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array("select id, brand_name from lib_buyer_brand","id","brand_name");
	$exdata=explode("_",$data);
	$sql="select id, commercial_cost_method, commercial_cost_percent, editable, tna_integrated as buyer_id, profit_calculative as brand_id from variable_order_tracking where company_name='$exdata[1]' and variable_list='$exdata[0]' order by id";
	?>
	 <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">Buyer</th>
                <th width="80">Brand</th>
                <th width="70">Percent</th>
                <th>Editable</th>
            </thead>
     	</table>
     </div>
     <div style="width:400px; max-height:200px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="show_list_view('<?=$exdata[0].'_'.$exdata[1].'_0_'.$row[csf("id")]; ?>','on_change_data','variable_settings_container','requires/merchandising_settings_controller','');">
                    <td width="30" align="center"><?=$i; ?></td>
                    <td width="110" style="word-break:break-all"><?=$buyer_arr[$row[csf("buyer_id")]]; ?></td>
                    <td width="80" style="word-break:break-all"><?=$brand_arr[$row[csf("brand_id")]]; ?></td>
                    <td width="70" align="center"><?=$row[csf("commercial_cost_percent")]; ?></td>
                    <td><?=$yes_no[$row[csf("editable")]]; ?></td>
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



?>