<?
/************************************************************************
|	Purpose			:	This Controller is for Field Level Access
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	26.08.2015
|	Updated by 		:   Md. Didarul Alam		
|	Update date		:   14.08.2016,21.08.2016 
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*************************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
include('../../includes/field_list_array.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);


if($action=="load_drop_down_item")
{
	$field_arr=get_fieldlevel_arr($data);
	echo create_drop_down( "cboFieldId_1",200,$field_arr,"",1,"----Select----",0,"set_hide_data(this.value+'**'+1);","","","","","","","","cbo_field_id" );
	exit();
}
if($action=="set_field_name")
{
	$data_ref=explode("**",$data);
	$field_val=$fieldlevel_arr[$data_ref[0]][$data_ref[1]];
	echo "$('#txtFieldName_".$data_ref[2]."').val('$field_val');\n";
}

if($action=="color_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1,'','','');
	extract($_REQUEST);	
	?> 
	<script>
	
	$(document).ready(function(e) {
        setFilterGrid('tbl_list_search',-1);
		set_all();
    });
	
	 var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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
		
        // Keep old selected user id until click on refresh button
		function set_all()
		{
			var old = document.getElementById( 'txt_user_row_id' ).value;          
			if(old !="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_user_id').val( id );
			$('#hidden_user_name').val( name );
		}
		
    </script>
    <input type="hidden" name="user_id" id="hidden_user_id" value="" />
    <input type="hidden" name="user_name" id="hidden_user_name" value="" />
    <div>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>User Name</th>
            </thead>
		</table>
		<div style="width:340px; max-height:280px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
            <?php 
				$i=1; $user_row_id=""; $user_id=explode(",",$user_id);
                $nameArray = sql_select( "select id,user_name from user_passwd where valid=1" );
				$i=0;
                foreach ($nameArray as $selectResult)
				{
					$i++;    
                    if ($i%2==0) { 
						$bgcolor="#E9F3FF";
                    } else {
						$bgcolor="#FFFFFF";	
                    } 
                    if(in_array($selectResult[csf('id')],$user_id)) 
					{
						if($user_row_id=="") $user_row_id=$i; else $user_row_id.=",".$i;
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                        <td width="50" align="center"><?php echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('user_name')]; ?>"/>
                        </td>	
                        <td><p><?php echo $selectResult[csf('user_name')];?></p></td>
                    </tr>
                    <?                   
                }
                ?>
               	<input type="hidden" name="txt_user_row_id" id="txt_user_row_id" value="<?php echo $user_row_id;?>"/>	
            </table>
        </div>
        <table width="340" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
    <?
	exit();
}

if($action=='save_update_delete')
{
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		$id=return_next_id( "id", "field_level_access", 1 ) ; 
		$mst_id=$id;
				
        $user_id = str_replace("'","",$text_user_id);// '132,133,134' 
        $duplicate_sql = "select id from field_level_access where company_id=$cbo_company_name and user_id in($user_id) and page_id=$cbo_page_id and status_active=1"; 
                
        $duplicate_result = sql_select($duplicate_sql); 
                
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete from field_level_access where company_id=$cbo_company_name and user_id in ($user_id) and page_id=$cbo_page_id",1);	
        }
        
        for($i=1;$i<=$total_row;$i++)
		{
			$cboFieldId="cboFieldId_".$i;
			
			if($i!=1)
				$duplicate_sql.="  or  field_id= ".$$cboFieldId."";
			else
				$duplicate_sql.=" and ( field_id=".$$cboFieldId ."";
		}
		$duplicate_sql.=" )";
		$duplicate_result=sql_select($duplicate_sql);
		foreach($duplicate_result as $row){
			$key=$row[csf('user_id')].$row[csf('page_id')].$row[csf('field_id')];
			$duplicateFillArr[$key]=$row[csf('field_id')];	
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			$field_array="id,mst_id,company_id,user_id,page_id,field_id,field_name,is_disable,defalt_value,inserted_by,insert_date";
			
            $user_ids = explode(',',$user_id);
            $data_array="";
            foreach ($user_ids as $userId) {                           
                for($i=1;$i<=$total_row;$i++)
                {             
                    $cboFieldId="cboFieldId_".$i;
                    $txtFieldName="txtFieldName_".$i;
                    $cboIsDisable="cboIsDisable_".$i;
                    $setDefaultVal="setDefaultVal_".$i;
                    
					$key = str_replace("'",'',$userId).str_replace("'",'',$cbo_page_id).str_replace("cboFieldId_","",$cboFieldId); 
					if($duplicateFillArr[$key]!=str_replace("cboFieldId_","",$cboFieldId) && $$cboFieldId !=0){							
						if ($i!=1) $data_array .=",";
						$data_array	.="(".$id.",".$mst_id.",".$cbo_company_name.",".$userId.",".$cbo_page_id.",'".$$cboFieldId."','".$$txtFieldName."','".$$cboIsDisable."','".$$setDefaultVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
                        $id++;
                    }                  
                }                            
            }          
		}
       	
		if ($data_array	!='') {
            $rID = sql_insert("field_level_access",$field_array,$data_array,1);
        } else {
             echo "20**Duplicate Field Name Not Allow in The Same Page.";
             die;
		} 
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$text_user_id;
			}
		}
		disconnect($con);
		die;		
	}
		//update
	if($operation==1)
	{
		$con = connect();
		$id=return_next_id( "id", "field_level_access", 1 ) ; 
		$mst_id=$id;
				
        $user_id = str_replace("'","",$text_user_id);// '132,133,134' 
        $duplicate_sql = "select id from field_level_access where company_id=$cbo_company_name and user_id = $user_id and page_id=$cbo_page_id and status_active=1"; 
                
        $duplicate_result = sql_select($duplicate_sql); 
                
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete from field_level_access where company_id=$cbo_company_name and user_id = $user_id and page_id=$cbo_page_id",1);	
        }
        
        for($i=1;$i<=$total_row;$i++)
		{
			$cboFieldId="cboFieldId_".$i;
			
			if($i!=1)
				$duplicate_sql.="  or  field_id= ".$$cboFieldId."";
			else
				$duplicate_sql.=" and ( field_id=".$$cboFieldId ."";
		}
		$duplicate_sql.=" )";
		$duplicate_result=sql_select($duplicate_sql);
		foreach($duplicate_result as $row){
			$key=$row[csf('user_id')].$row[csf('page_id')].$row[csf('field_id')];
			$duplicateFillArr[$key]=$row[csf('field_id')];	
		}
		
		if(str_replace("'","",$update_id)!="")
		{
			$field_array="id,mst_id,company_id,user_id,page_id,field_id,field_name,is_disable,defalt_value,updated_by,update_date";
			
            $user_ids = explode(',',$user_id);
            $data_array="";
            foreach ($user_ids as $userId) {                           
                for($i=1;$i<=$total_row;$i++)
                {             
                    $cboFieldId="cboFieldId_".$i;
                    $txtFieldName="txtFieldName_".$i;
                    $cboIsDisable="cboIsDisable_".$i;
                    $setDefaultVal="setDefaultVal_".$i;
                    
					$key = str_replace("'",'',$userId).str_replace("'",'',$cbo_page_id).str_replace("cboFieldId_","",$cboFieldId); 
					if($duplicateFillArr[$key]!=str_replace("cboFieldId_","",$cboFieldId) && $$cboFieldId !=0){							
						if ($i!=1) $data_array .=",";
						$data_array	.="(".$id.",".$mst_id.",".$cbo_company_name.",".$userId.",".$cbo_page_id.",'".$$cboFieldId."','".$$txtFieldName."','".$$cboIsDisable."','".$$setDefaultVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
                        $id++;
                    }                  
                }                            
            }          
		}
       	
		if ($data_array	!='') {
            $rID = sql_insert("field_level_access",$field_array,$data_array,1);
        } else {
             echo "20**Duplicate Field Name Not Allow in The Same Page.";
             die;
		} 
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "1**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$text_user_id;
			}
		}
		disconnect($con);
		die;		
		
	}
	
	//delete
	if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID = execute_query("delete from field_level_access where mst_id = $update_id",1);		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$cbo_user_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_user_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$cbo_user_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_user_id;
			}
		}
		disconnect($con);
		die;	
	}
}

if($action=='action_user_data')
{
	//echo "su..re";
	$data_ref=explode("**",$data);
	$com_id=$data_ref[0];
	$user_id=$data_ref[1];
	$page_id=$data_ref[2];
	
	$array=sql_select("select id, mst_id, field_id, field_name, is_disable, defalt_value from field_level_access where company_id=$com_id and user_id=$user_id and page_id=$page_id and status_active=1 and is_deleted=0");

    $i=1;
	$field_arr=get_fieldlevel_arr($page_id);
	if(count($array)>0)
	{
		foreach($array as $row)
		{
			if(count($array)==$i) $disable_anable=""; else $disable_anable=" display: none";
			?>
			<tr>
				<td align="center" id="fieldtd">
				  <? echo create_drop_down("cboFieldId_".$i,200,$field_arr,"",1,"----Select----",$row[csf("field_id")],"set_hide_data(this.value+'**'+". $i . ");","","","","","","",""); ?>
				</td>
				<td align="center">
				 <? echo create_drop_down("cboIsDisable_".$i,150,$yes_no,"",1,"-- Select --",$row[csf("is_disable")],"","","","","","","",""); ?> 
				 <input type="hidden" id="txtFieldName_<? echo $i;?>" name="txtFieldName[]" style="width:100px;" value="<? echo $row[csf("field_name")]; ?>" />  
				</td>
				<td align="center">
				  <input type="text" id="setDefaultVal_<? echo $i;?>" name="" style="width:100px" class="text_boxes" value="<? echo $row[csf("defalt_value")]; ?>" />
				  <input type="hidden" id="hideDtlsId_<? echo $i;?>" name="hideDtlsId[]" style="width:100px;" value="<? echo $row[csf("id")]; ?>" /> 
				</td>
				<td align="center" id="increment_<? echo $i;?>">
				<input style="width:30px; <? echo $disable_anable; ?>" type="button" id="incrementfactor_<? echo $i;?>" name="incrementfactor_<? echo $i;?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i;?>)"/>
				<input style="width:30px; <? echo $disable_anable; ?>" type="button" id="decrementfactor_<? echo $i;?>" name="decrementfactor_<? echo $i;?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?>)"/>&nbsp;
				</td>
			</tr>
			<?
			$i++;
		}
		?>
        <input type="hidden" id="button_status_check" value="1" />
        <input type="hidden" id="update_id" name="update_id" class="text_boxes" value="<? echo $array[0][csf("mst_id")];?>" readonly />
        <?
	}
	else
	{
		?>
        <tr>
            <td align="center" id="fieldtd">
              <? echo create_drop_down("cboFieldId_".$i,200,$field_arr,"",1,"----Select----",0,"set_hide_data(this.value+'**'+1);","","","","","","",""); ?>
            </td>
            <td align="center">
             <? echo create_drop_down("cboIsDisable_".$i,150,$yes_no,"",1,"-- Select --",0,"","","","","","","",""); ?> 
             <input type="hidden" id="txtFieldName_<? echo $i;?>" name="txtFieldName[]" style="width:100px;" />  
            </td>
            <td align="center">
              <input type="text" id="setDefaultVal_<? echo $i;?>" name="" style="width:100px" class="text_boxes" />
              <input type="hidden" id="hideDtlsId_<? echo $i;?>" name="hideDtlsId[]" style="width:100px;" value="" /> 
            </td>
            <td align="center" id="increment_<? echo $i;?>">
            <input style="width:30px;" type="button" id="incrementfactor_<? echo $i;?>" name="incrementfactor_<? echo $i;?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i;?>)"/>
            <input style="width:30px;" type="button" id="decrementfactor_<? echo $i;?>" name="decrementfactor_<? echo $i;?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?>)"/>&nbsp;
            </td>
        </tr>
        <input type="hidden" id="button_status_check" value="0" />
        <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly />
        <?
	}
	exit();
 }
?>