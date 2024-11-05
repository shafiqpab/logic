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
            $i=1;
            $nameArray=sql_select( "select id,user_name from user_passwd where valid=1" );
            foreach ($nameArray as $selectResult)
            {      
               $id_arr[]=$selectResult[csf('id')];              
               if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";	
            if(in_array($selectResult[csf('id')],$cu)) $bgcolor="#FFFF00";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $selectResult[csf('id')]; ?>" onclick="js_set_value(<?php echo $selectResult[csf('id')]; ?>)"> 
                <td width="50" align="center"><?php echo "$i"; ?>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('user_name')]; ?>"/>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	                                                 
                </td>	
                <td width="130">&nbsp;
                    <?php echo split_string($selectResult[csf('user_name')],13); ?>
                </td>                   			
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

}

if ($action=="on_change_data")
{

$explode_data = explode("_",$data);
$type = $explode_data[0];
$company_id = $explode_data[1];
if( $type==1) // Allow Ship Date on Off Day
{

$nameArray=sql_select( "select publish_shipment_date,id from  variable_order_tracking where company_name='$company_id' and variable_list=46 order by id" );
if(count($nameArray)>0)$is_update=1;else $is_update=0;
?>
<fieldset>
 <legend>Ship Date on Off Day</legend>
 <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
    <table cellspacing="0" width="100%" >
        <tr> 
            <td width="100" align="left">Master Batch Allow</td>
            <td width="190">

                <? 
                echo create_drop_down( "cbo_ship_date", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('publish_shipment_date')], "",'','' );
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
                            //$permission=$_SESSION['page_permission'];
            echo load_submit_buttons( $permission, "fnc_order_tracking_variable_settings", $is_update,0 ,"reset_form('aopsettings_1','','',1)");
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



if ($action=="save_update_delete_material_control")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$item_category_id=explode(",",$item_category_id);
	$exeed_budget_qty=explode(",",$exeed_budget_qty);
	$exeed_budget_amt=explode(",",$exeed_budget_amt);
	$amt_exceed_lavel=explode(",",$amt_exceed_lavel);
	$cbo_exceed_qty_level=explode(",",$cbo_exceed_qty_level);
	
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
			$field_array="id,company_name,variable_list,exeed_budge_qty,exeed_budge_amount,amount_exceed_level,item_category_id,exceed_qty_level,inserted_by,insert_date,status_active,is_deleted";
			for($i=0;$i<count($item_category_id);$i++)
			{			 
				if( $id=="" ) $id = return_next_id( "id", "variable_order_tracking", 1 ); else $id = $id+1;
				if($i==0)
					$data_array .= "(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$exeed_budget_qty[$i]."','".$exeed_budget_amt[$i]."',".$amt_exceed_lavel[$i].",'".$item_category_id[$i]."','".$cbo_exceed_qty_level[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				else
					$data_array .= ",(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".$exeed_budget_qty[$i]."','".$exeed_budget_amt[$i]."',".$amt_exceed_lavel[$i].",'".$item_category_id[$i]."','".$cbo_exceed_qty_level[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
   }

	else if ($operation==1)   // Update Here
	{

     $update_id=explode(",",$update_id); 
     $con = connect();
     if($db_type==0)
     {
        mysql_query("BEGIN");
    }

    for($i=0;$i<count($item_category_id);$i++) 	 
    {						
        $field_array="company_name*variable_list*exeed_budge_qty*exeed_budge_amount*amount_exceed_level*item_category_id*exceed_qty_level*updated_by*update_date";
        $data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".$exeed_budget_qty[$i]."'*'".$exeed_budget_amt[$i]."'*".$amt_exceed_lavel[$i]."*".$item_category_id[$i]."*".$cbo_exceed_qty_level[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        $rID=sql_update("variable_order_tracking",$field_array,$data_array,"id","".$update_id[$i]."",1);
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

if ($action=="save_update_delete")
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
            
			$id=return_next_id( "id", "variable_order_tracking", 1 ) ;
			
            if(str_replace("'","", $cbo_variable_list_wo)==56)
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
            else
            {
                $field_array="id, company_name, variable_list, sales_year_started, tna_integrated, profit_calculative, process_loss_method, item_category_id, consumption_basis, copy_quotation, cm_cost_method, color_from_library, publish_shipment_date, style_from_library,commercial_cost_method, commercial_cost_percent, editable, gmt_num_rep_sty, duplicate_ship_date, image_mandatory, tna_process_type, po_update_period, po_current_date, inquery_id_mandatory, trim_rate, cm_cost_method_quata, budget_exceeds_quot, lab_test_rate_update, colar_culff_percent, pre_cost_approval,price_quo_approval, report_date_catagory, tna_process_start_date, default_fabric_nature, default_fabric_source, bom_page_setting, cost_control_source, user_id, cm_cost_method_based_on, work_study_mapping_id, cm_cost_compulsory,fabric_source_aop_id,yarn_iss_with_serv_app,textile_tna_process_base,excut_source, inserted_by, insert_date, status_active"; //cbo_cm_cost_compulsory
				
				if(str_replace("'",'',$cbo_variable_list_wo)==22) $is_editable=str_replace("'",'',$cbo_cm_cost_editable);
				else $is_editable=str_replace("'",'',$cbo_editable);

            $data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",'".str_replace("'",'',$cbo_sales_year_started_date)."','".str_replace("'",'',$cbo_tna_integrated)."','".str_replace("'",'',$cbo_profit_calculative)."','".str_replace("'",'',$process_loss_methods)."','".str_replace("'",'',$item_category_ids)."','".str_replace(",",'',$cbo_consumption_basis)."','".str_replace(",",'',$cbo_copy_quotation)."','".str_replace("'",'',$cbo_cm_cost_method)."','".str_replace("'",'',$cbo_color_from_library)."','".str_replace("'",'',$publish_shipment_date)."','".str_replace("'",'',$style_from_library)."','".str_replace("'",'',$cbo_commercial_cost_method)."','".str_replace("'",'',$txt_commercial_cost_percent)."','".$is_editable."','".str_replace("'",'',$txt_size_wise_repeat)."','".str_replace("'",'',$cbo_duplicate_ship_date)."','".str_replace("'",'',$image_mandatory)."','".str_replace("'",'',$tna_process_type)."','".str_replace("'",'',$update_period)."','".str_replace("'",'',$po_current_date)."','".str_replace("'",'',$inquery_id_mandatory)."','".str_replace("'",'',$cbo_trim_rate)."','".str_replace("'",'',$cbo_cm_cost_method_quata)."','".str_replace("'",'',$cbo_budget_exceeds_quot)."','".str_replace("'",'',$cbo_lab_test_rate)."','".str_replace("'",'',$cbo_colar_culff_percent)."','".str_replace("'",'',$cbo_pre_cost_approval)."','".str_replace("'",'',$cbo_price_quo_approval)."','".str_replace("'",'',$cbo_report_date_catagory)."','".str_replace("'",'',$txt_tna_process_start_date)."','".str_replace("'",'',$cbo_default_febric_nature)."','".str_replace("'",'',$cbo_default_fabric_source)."','".str_replace("'",'',$cbo_bom_page)."','".str_replace("'",'',$cbo_cost_control_source)."','".str_replace("'",'',$user_hidden_id)."','".str_replace("'",'',$cbo_cm_cost_method_based_on)."','".str_replace("'",'',$cbo_work_study_mapping)."','".str_replace("'",'',$cbo_cm_cost_compulsory)."','".str_replace("'",'',$cbo_fabric_source_aop_id)."','".str_replace("'",'',$cbo_yarn_iss_with_serv_app)."','".str_replace("'",'',$cbo_textile_tna_process_base)."','".str_replace("'",'',$cbo_excesscut_per_level)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
            }

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
		if(str_replace("'", "", $cbo_variable_list_wo)==56)
		{
			$field_array_up="company_name*embellishment_id*embellishment_budget_id*variable_list*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
            {  
				$cbo_embellishment_type="cboEmbellishmentTypeHidden_".$i;
				$embellishmentName="embellishmentName_".$i;
				$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
			  

				if ($data_array!='') $data_array .=",";

				$data_array .="(".$id.",". $cbo_company_name_wo.",".$$cbo_embellishment_type.",".$$embellishmentName.",". $cbo_variable_list_wo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						
				$id=$id+1;
				if (str_replace("'",'',$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					
					$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$cbo_company_name_wo."*".$$cbo_embellishment_type."*".$$embellishmentName."*".$cbo_variable_list_wo."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
            }
            if($data_array_up!="")
            {
                $rID=execute_query(bulk_update_sql_statement("variable_order_tracking", "id",$field_array_up,$data_array_up,$id_arr ));
            }
		 }
		 else
		 {
			$field_array="company_name*variable_list*sales_year_started*tna_integrated*profit_calculative*process_loss_method*item_category_id*consumption_basis*copy_quotation*cm_cost_method*color_from_library*publish_shipment_date*style_from_library*commercial_cost_method*commercial_cost_percent*editable*gmt_num_rep_sty*duplicate_ship_date*image_mandatory*tna_process_type*po_update_period*po_current_date*inquery_id_mandatory*trim_rate*cm_cost_method_quata*budget_exceeds_quot*lab_test_rate_update*colar_culff_percent*pre_cost_approval*price_quo_approval*report_date_catagory*tna_process_start_date*default_fabric_nature*default_fabric_source*bom_page_setting*cost_control_source*user_id*cm_cost_method_based_on*work_study_mapping_id*cm_cost_compulsory*fabric_source_aop_id*yarn_iss_with_serv_app*textile_tna_process_base*excut_source*updated_by*update_date";
			
			if(str_replace("'",'',$cbo_variable_list_wo)==22) $is_editable=str_replace("'",'',$cbo_cm_cost_editable);
			else $is_editable=str_replace("'",'',$cbo_editable);
	
			$data_array="".$cbo_company_name_wo."*".$cbo_variable_list_wo."*'".str_replace("'",'',$cbo_sales_year_started_date)."'*'".str_replace("'",'',$cbo_tna_integrated)."'*'".str_replace("'",'',$cbo_profit_calculative)."'*'".str_replace("'",'',$process_loss_methods)."'*'".str_replace("'",'',$item_category_ids)."'*'".str_replace("'",'',$cbo_consumption_basis)."'*'".str_replace("'",'',$cbo_copy_quotation)."'*'".str_replace("'",'',$cbo_cm_cost_method)."'*'".str_replace("'",'',$cbo_color_from_library)."'*'".str_replace("'",'',$publish_shipment_date)."'*'".str_replace("'",'',$style_from_library)."'*'".str_replace("'",'',$cbo_commercial_cost_method)."'*'".str_replace("'",'',$txt_commercial_cost_percent)."'*'".$is_editable."'*'".str_replace("'",'',$txt_size_wise_repeat)."'*'".str_replace("'",'',$cbo_duplicate_ship_date)."'*'".str_replace("'",'',$image_mandatory)."'*'".str_replace("'",'',$tna_process_type)."'*'".str_replace("'",'',$update_period)."'*'".str_replace("'",'',$po_current_date)."'*'".str_replace("'",'',$inquery_id_mandatory)."'*'".str_replace("'",'',$cbo_trim_rate)."'*'".str_replace("'",'',$cbo_cm_cost_method_quata)."'*'".str_replace("'",'',$cbo_budget_exceeds_quot)."'*'".str_replace("'",'',$cbo_lab_test_rate)."'*'".str_replace("'",'',$cbo_colar_culff_percent)."'*'".str_replace("'",'',$cbo_pre_cost_approval)."'*'".str_replace("'",'',$cbo_price_quo_approval)."'*'".str_replace("'",'',$cbo_report_date_catagory)."'*'".str_replace("'",'',$txt_tna_process_start_date)."'*'".str_replace("'",'',$cbo_default_febric_nature)."'*'".str_replace("'",'',$cbo_default_fabric_source)."'*'".str_replace("'",'',$cbo_bom_page)."'*'".str_replace("'",'',$cbo_cost_control_source)."'*'".str_replace("'",'',$user_hidden_id)."'*'".str_replace("'",'',$cbo_cm_cost_method_based_on)."'*'".str_replace("'",'',$cbo_work_study_mapping)."'*'".str_replace("'",'',$cbo_cm_cost_compulsory)."'*'".str_replace("'",'',$cbo_fabric_source_aop_id)."'*'".str_replace("'",'',$cbo_yarn_iss_with_serv_app)."'*'".str_replace("'",'',$cbo_textile_tna_process_base)."'*'".str_replace("'",'',$cbo_excesscut_per_level)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			 //
			 //Check the value of cbo_report_date_catagory on eorror 
	
			$rID=sql_update("variable_order_tracking", $field_array, $data_array,"id","".$update_id."",1);  
				//   '".$cbo_color_from_library."'*'".$publish_shipment_date."'* 	
				//echo  "10**".$data_array.'SS';die;
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
			elseif($db_type==2 || $db_type==1 )
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
			elseif($db_type==2 || $db_type==1 )
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
			elseif($db_type==2 || $db_type==1 )
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
			
			$field_array="id, company_name, variable_list, buyer_allocation_maintain,capacity_exceed_level, inserted_by, insert_date, status_active"; 
			
			$data_array="(".$id.",".$cbo_company_name_wo.",".$cbo_variable_list_wo.",".$cbo_buyer_allocation_maintain.",".$cbo_capacity_exceed_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";   
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
			elseif($db_type==2 || $db_type==1 )
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
		$field_array="buyer_allocation_maintain*capacity_exceed_level*updated_by*update_date";
		
		$data_array="".$cbo_buyer_allocation_maintain."*".$cbo_capacity_exceed_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
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
            elseif($db_type==2 || $db_type==1 )
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


?>