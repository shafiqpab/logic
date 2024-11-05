<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="load_drop_down_machine_no")
{
	echo create_drop_down( "cbo_machine_no", 150, "select a.id,machine_no from lib_machine_name a where a.category_id in (2,4) and a.company_id = '$data' and a.status_active = 1 and a.is_deleted=0 order by machine_no","id,machine_no", 1, "-- Select Machine --", $selected, "","","","","","",4 ); 
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,$unicode,'1','',1);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#hidden_com_id').val(data);
            parent.emailwindow.hide();
		}	
	</script>
	</head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        	<input type="hidden" name="hidden_com_id" id="hidden_com_id" class="text_boxes" value="">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+'<? echo $txt_cons_comp_id; ?>', 'fabric_description_popup_search_list_view', 'search_div', 'lib_process_wise_finishing_charge_controller', 'setFilterGrid(\'list_view_comp\',-1)');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($company_id, $construction, $gsm_weight, $string_search_type,$txt_cons_comp_id) = explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count" );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	}	

	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id, b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	?>
    <table class="rpt_table" width="720" cellspacing="0" cellpadding="0" border="0" rules="all">
        <thead>
            <tr>
                <th width="50">S/L</th>
                <th width="100">Construction</th>
                <th width="100">GSM/Weight</th>
                <th>Composition</th>
            </tr>
       </thead>
   </table>
   <div id="" style="max-height:300px; width:720px; overflow-y:scroll">
   		<table id="list_view_comp" class="rpt_table" width="700" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
	        <tbody>
				<?
				$sql_data=sql_select("select a.id, a.construction, a.gsm_weight from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0 $search_con order by a.id");
				$i=1;
				foreach($sql_data as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$composition_description = $composition_arr[$row[csf('id')]];
					if($txt_cons_comp_id==$row[csf('id')])
					{
						$bgcolor="#FFFF00";
					}
					?>
			        <tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$composition_description; ?>')">
			            <td width="50"><? echo $i; ?></td>
			            <td width="100" align="left"><p><? echo $row[csf('construction')]; ?></p></td>
			            <td width="100" align="right"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
			            <td><p><? echo $composition_description; ?></p></td>
			        </tr>
					<?
			        $i++;
			    }
			    ?>
			</tbody>
    	</table>
	</div>
	<?
	exit();
}

if ($action=="list_view_subcon_dying_charge")
{
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$company_arr=return_library_array( "select id, company_short_name  from lib_company",'id','company_short_name');
	$arr= array(0=>$company_arr,2=>$conversion_cost_head_array,3=>$machine_arr,5=>$unit_of_measurement,6=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Name,Machine No, In House Rate,UOM,Status", "90,130,70,70,100,60,60","650","250",1, "select id, company_id, const_comp, process_id, machine_no, rate, uom_id, status_active from lib_finish_process_charge where status_active!=0 and is_deleted=0 ", "get_php_form_data", "id","'load_php_data_to_form'", 1, "company_id,0,process_id,machine_no,0,uom_id,status_active", $arr, "company_id,const_comp,process_id,machine_no,rate,uom_id,status_active","requires/lib_process_wise_finishing_charge_controller", 'setFilterGrid("list_view",-1);',"0,0,0,0,2,0,0");
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, company_id, const_comp, cons_comp_id, process_id, machine_no, rate, uom_id, status_active from lib_finish_process_charge where id='$data'" );

	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_id').value 		= '".$inf[csf("company_id")]."';\n";    
		echo "document.getElementById('text_const_compo').value 	= '".$inf[csf("const_comp")]."';\n"; 
		echo "document.getElementById('txt_cons_comp_id').value 	= '".$inf[csf("cons_comp_id")]."';\n"; 
		echo "document.getElementById('cbo_process_id').value  		= '".$inf[csf("process_id")]."';\n";
		echo "load_drop_down('requires/lib_process_wise_finishing_charge_controller', ".$inf[csf("company_id")].", 'load_drop_down_machine_no', 'machine_td' );\n";

		echo "document.getElementById('cbo_machine_no').value  		= '".$inf[csf("machine_no")]."';\n";
		echo "document.getElementById('text_in_house_rate').value  	= '".$inf[csf("rate")]."';\n"; 
		echo "document.getElementById('cbo_uom').value  			= '".$inf[csf("uom_id")]."';\n";
		echo "document.getElementById('cbo_status').value  			= '".$inf[csf("status_active")]."';\n"; 
		echo "document.getElementById('update_id').value  			= '".$inf[csf("id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_dyeing_charge',1);\n";  
	}
}
//=================SAVE UPDATE DELETE==============
$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)
	{		
		if (is_duplicate_field( "id", "lib_finish_process_charge", "company_id=$cbo_company_id and const_comp=$text_const_compo and process_id=$cbo_process_id  and rate=$text_in_house_rate and cbo_machine_no= $cbo_machine_no and uom_id=$cbo_uom and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; die; 
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_finish_process_charge", 1 ) ; 
			//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
			if (str_replace("'", "", trim($txt_color)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color))
				{
					$color_tbl_id = return_id( str_replace("'", "", trim($txt_color)), $color_library_arr, "lib_color", "id,color_name","0");
					$new_array_color[$color_tbl_id]=str_replace("'", "", trim($txt_color));
				}
				else $color_tbl_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
			}
			else $color_tbl_id=0;
			$field_array="id, company_id, const_comp, cons_comp_id, process_id, machine_no, rate, uom_id, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_id.",".$text_const_compo.",".$txt_cons_comp_id.",".$cbo_process_id.",". $cbo_machine_no .",".$text_in_house_rate.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";

			//echo "10**insert into lib_finish_process_charge (".$field_array.") values ".$data_array;die;

			$rID=sql_insert("lib_finish_process_charge",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'",'',$id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			else if($db_type==2)
			{
				if($rID)
				{
					oci_commit($con);   
					echo "0**".$rID;
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
	}
	//=================UPDATE==============
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "id", "lib_finish_process_charge", "company_id=$cbo_company_id and const_comp=$text_const_compo and process_id=$cbo_process_id and rate=$text_in_house_rate and uom_id=$cbo_uom and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=str_replace("'",'',$update_id);
			$field_array="company_id*const_comp*cons_comp_id*process_id*machine_no*rate*uom_id*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_id."*".$text_const_compo."*".$txt_cons_comp_id."*".$cbo_process_id."*".$cbo_machine_no."*".$text_in_house_rate."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			
			$rID=sql_update("lib_finish_process_charge",$field_array,$data_array,"id","".$update_id."",1);
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2)
			{
				if($rID)
				{
					oci_commit($con);   
					echo "1**".$rID;
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
	}
	//=================DELETE==============
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$date."'*'0'*'1'";
		
		$rID=sql_delete("lib_finish_process_charge",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$rID;
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
}  

?>

