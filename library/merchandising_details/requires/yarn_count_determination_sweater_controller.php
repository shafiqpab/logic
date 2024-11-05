<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

if ($action=="search_list_view")
{
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");	
	$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	/*$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	$group_short_name=$lib_group_short[1];

	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
			$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
		}
	}
	unset($data_array);*/
	/*$sql="SELECT id,fab_nature_id, type, rd_no, construction, gsm_weight, weight_type, design, fabric_ref, color_range_id,inserted_by,status_active,full_width,cutable_width from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=426 order by id DESC";				
	$arr=array (0=>$item_category, 4=>$sysCodeArr, 8=>$fabric_weight_type, 9=>$color_range,12=>$composition_arr,13=>$user_arr, 14=>$row_status);
	echo  create_list_view ( "list_view", "Fab Nature,Fabric Ref,RD NO,Sys. Code,Type,Construction,Design,Weight,Weight Type,Color Range,Full Width,Cutable Width,Composition,Insert By,Status", "100,100,50,100,80,100,100,50,50,50,50,50,300,100,60","1470","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,0,0,id,0,0,weight_type,color_range_id,0,0,id,inserted_by,status_active", $arr , "fab_nature_id,fabric_ref,rd_no,type,id,construction,design,gsm_weight,weight_type,color_range_id,full_width,cutable_width,id,inserted_by,status_active", "requires/yarn_count_determination_sweater_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,2,0,0,0,0,0,0,0') ;*/
	
	$sql="select id, fab_nature_id, rd_no, mill_ref, construction, color_range_id, gauge, process_loss, sequence_no, fab_composition, fabric_composition_id, count, yarn_type, inserted_by, status_active from lib_yarn_count_determina_mst where is_deleted=0 and entry_form=461 order by id DESC";				
	$arr=array (3=>$lib_yarn_count, 4=>$yarn_type, 8=>$user_arr, 9=>$row_status);
	echo  create_list_view ( "list_view", "Sys Code,Construction,Fab. Composition,Count,Yarn Type,RD No,Mill Ref.,Seq.,Insert By,Status", "70,120,150,70,70,80,80,50,100","900","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "0,0,0,count,yarn_type,0,0,0,inserted_by,status_active", $arr , "id,construction,fab_composition,count,yarn_type,rd_no,mill_ref,sequence_no,inserted_by,status_active", "requires/yarn_count_determination_sweater_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0') ;
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select id, fab_nature_id, rd_no, mill_ref, construction, color_range_id, gauge, process_loss, sequence_no, fab_composition, fabric_composition_id, count, yarn_type, status_active from lib_yarn_count_determina_mst where id='$data' and entry_form=461");
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");	
	foreach ($nameArray as $inf)
	{
		$group_short_name=$lib_group_short[1];
		$sys_code=$group_short_name.'-'.$inf[csf("id")];
		echo "document.getElementById('cbo_fabric_nature').value  	= '".$inf[csf("fab_nature_id")]."';\n";
		echo "document.getElementById('txt_sys_code').value  		= '".$sys_code."';\n";
		echo "document.getElementById('txtrdno').value  			= '".$inf[csf("rd_no")]."';\n";
		echo "document.getElementById('txtmillRef').value  			= '".$inf[csf("mill_ref")]."';\n";
		echo "document.getElementById('txtconstruction').value 		= '".$inf[csf("construction")]."';\n";
		echo "document.getElementById('txtcompone').value  			= '".$inf[csf("fab_composition")]."';\n";
		echo "document.getElementById('cbocomponeid').value  		= '".$inf[csf("fabric_composition_id")]."';\n";
		echo "document.getElementById('cbocountcotton').value  		= '".$inf[csf("count")]."';\n";
		echo "document.getElementById('cbotypecotton').value  		= '".$inf[csf("yarn_type")]."';\n";
		echo "document.getElementById('cbocolorrange').value  		= '".$inf[csf("color_range_id")]."';\n";
		echo "document.getElementById('cbo_gauge').value  			= '".$inf[csf("gauge")]."';\n";
		
		echo "document.getElementById('txt_process_loss').value  	= '".$inf[csf("process_loss")]."';\n";
		echo "document.getElementById('txt_seq_no').value  			= '".$inf[csf("sequence_no")]."';\n";
		echo "document.getElementById('cbo_status').value  			= '".$inf[csf("status_active")]."';\n";
		echo "document.getElementById('update_mst_id').value  		= '".$inf[csf("id")]."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_count_determination',1);\n";  
	    //echo "show_detail_form('".$inf[csf("id")]."');\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	?>
	<table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
            	<th width="150" class="must_entry_caption"><font color="blue">Composition</font></th>
                <th width="50" class="must_entry_caption"><font color="blue">%</font></th>
                <th width="150">Count</th>
                <th width="150">Type</th>
                <th>&nbsp;</th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("select id, copmposition_id, percent,count_id,type_id from  lib_yarn_count_determina_dtls where mst_id='$data' order by id asc");
				if ( count($data_array)>0)
				{
					$i=0;
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="yarncost_1" align="center">
                            <td width="150">
                            	<input type="text" id="txtcompone_<? echo $i; ?>"  name="txtcompone_<? echo $i; ?>"  class="text_boxes" style="width:140px" readonly placeholder="Browse" onDblClick="openmypage_comp(<? echo $i; ?>);" value="<? echo $composition[$row[csf("copmposition_id")]]; ?>" />
                                <input type="hidden" id="cbocompone_<? echo $i; ?>"  name="cbocompone_<? echo $i; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf("copmposition_id")]; ?>" />
                            
                            </td>
                            <td width="50"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="sum_percent()"  value="<? echo  $row[csf("percent")]; ?>" /></td>
                            <td width="70"><? echo create_drop_down( "cbocountcotton_".$i, 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --",  $row[csf("count_id")], "check_duplicate(".$i.",this.id )",'','' ); 
                            ?>
                            </td>
                            <td width="100">
                            	<? 
                           		echo create_drop_down( "cbotypecotton_".$i, 150, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "check_duplicate(".$i.",this.id )",'','','','',$ommitYarnType); 
                            	?>
                            </td>
                            <td> 
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                <input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=" <? echo $row[csf("id")]; ?>"  />  
                            </td>  
						</tr>
						<?
					}
				}
	            ?>
        </tbody>
	</table>
	<tr><td><input type="hidden" name="total_percent" id="total_percent" value=""></td></tr>
	<?
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
		/*echo "10**".__LINE__; die;*/
		$str_rep=array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#');
		$txtconstruction=str_replace($str_rep,'',str_replace("'",'',$txtconstruction));
		$txtrdno=str_replace($str_rep,'',$txtrdno);
		$txtmillRef=str_replace($str_rep,'',$txtmillRef);
		
		$duplicate_query=sql_select("SELECT id from lib_yarn_count_determina_mst where fab_nature_id=$cbo_fabric_nature and yarn_type='$cbotypecotton' and construction='$txtconstruction' and mill_ref=$txtmillRef and color_range_id=$cbocolorrange and entry_form=461 and is_deleted=0");
		if (count($duplicate_query)>0)
		{
			echo "11**0"; disconnect($con); die;
		}
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=426 and is_deleted=0"; die;
		$duplicate_data=sql_select("SELECT id from lib_yarn_count_determina_mst where rd_no='$txtrdno' and entry_form=461 and is_deleted=0");
		if (count($duplicate_data)>0)
		{
			echo "11**0"; disconnect($con); die;
		}
		else{					
			$id=return_next_id( "id", "lib_yarn_count_determina_mst", 1);
			$field_array1= "id, fab_nature_id, rd_no, mill_ref, construction, color_range_id, gauge, process_loss, sequence_no, fab_composition, fabric_composition_id,   entry_form, count, yarn_type, inserted_by, insert_date, status_active, is_deleted";
			$data_array1="(".$id.",".$cbo_fabric_nature.",'".$txtrdno."','".$txtmillRef."','".$txtconstruction."',".$cbocolorrange.",".$cbo_gauge.",".$txt_process_loss.",".$txt_seq_no.",".$txtcompone.",".$cbocomponeid.",461,".$cbocountcotton.",".$cbotypecotton.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			/*$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
			$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,inserted_by,insert_date,status_active,is_deleted";
			for ($i=1;$i<=$total_row;$i++)
			{
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbocountcotton="cbocountcotton_".$i;
				$cbotypecotton="cbotypecotton_".$i;
				$updateid="updateid_".$i;
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}*/
			
			//echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
			
			
			$rID=sql_insert("lib_yarn_count_determina_mst",$field_array1,$data_array1,0);
			//$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
			//check_table_status( $_SESSION['menu_id'],0);
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);  
					echo "0**".$rID;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$rID."**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;;
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
		
		$inqueryNo=return_field_value("a.system_number as sys_no", "wo_quotation_inquery a, wo_quotation_inquery_fab_dtls b", "a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=457 and constraction=".$update_mst_id."" ,"sys_no");
		
		if($inqueryNo!="")
		{
			echo "50**Some Entries Found For This Fabric Determination, Update/Delete Not Allowed, \n Buyer Inquiry [Sweater] : ".$inqueryNo;
			disconnect($con);
			die;
		}
		
		$duplicate_query=sql_select("SELECT id from lib_yarn_count_determina_mst where fab_nature_id=$cbo_fabric_nature and yarn_type='$cbotypecotton' and construction='$txtconstruction' and mill_ref=$txtmillRef and color_range_id=$cbocolorrange and entry_form=461 and is_deleted=0 and id<>$update_mst_id");
		
		if (count($duplicate_query)>0)
		{
			echo "11**0"; disconnect($con); die;
		}
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=426 and is_deleted=0 and id<>$update_mst_id"; die;
		$duplicate_data=sql_select("SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=461 and is_deleted=0 and id<>$update_mst_id");
		if (count($duplicate_data)>0)
		{
			echo "11**01"; disconnect($con); die;
		}
		
		if(str_replace("'","",$cbo_status)==2)
		{
			//$rID_1=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
			$rID=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where id =$update_mst_id",0);
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID)
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
		else
		{
			$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$update_mst_id";
			$pre_data_array=sql_select($pre_sql);
			 
			$sql="select a.detarmination_id as detarmination_id from product_details_master a where a.detarmination_id=$update_mst_id and a.is_deleted=0 and a.status_active=1"; 
			
			$data_array=sql_select($sql);
			$flag = 0;
			if(count($data_array)>0 || count($pre_data_array)>0)
			{
				//$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
				$rID=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where id =$update_mst_id",0);
				if($db_type==0)
				{
					if($rID){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID)
					{
						oci_commit($con);  
						echo "1**".$rID;
					}
					else{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			}
		}			
		$str_rep=array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#');
		$txtconstruction=str_replace($str_rep,'',str_replace("'",'',$txtconstruction));
		$txtrdno=str_replace($str_rep,'',$txtrdno);
		$txtmillRef=str_replace($str_rep,'',$txtmillRef);
		
		
		$field_array1= "rd_no*mill_ref*construction*color_range_id*gauge*process_loss*sequence_no*fab_composition*fabric_composition_id*count*yarn_type*updated_by*update_date*status_active";
		$data_array1="'".$txtrdno."'*'".$txtmillRef."'*'".$txtconstruction."'*".$cbocolorrange."*".$cbo_gauge."*".$txt_process_loss."*".$txt_seq_no."*".$txtcompone."*".$cbocomponeid."*".$cbocountcotton."*".$cbotypecotton."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";	
					
		/*$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
		
		$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,inserted_by,insert_date,status_active,is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompone="cbocompone_".$i;
			$percentone="percentone_".$i;
			$cbocountcotton="cbocountcotton_".$i;
			$cbotypecotton="cbotypecotton_".$i;
			$updateid="updateid_".$i;
			if ($i!=1) $data_array2 .=",";
			$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		}*/	

		
		$rID=sql_update("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",0);
		
		/*if($rID == 1)
		{
			$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
		}
		$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);*/
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
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
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$inqueryNo=return_field_value("a.system_number as sys_no", "wo_quotation_inquery a, wo_quotation_inquery_fab_dtls b", "a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=457 and constraction=".$update_mst_id."" ,"sys_no");
		
		if($inqueryNo!="")
		{
			echo "50**Some Entries Found For This Fabric Determination, Update/Delete Not Allowed, \n Buyer Inquiry [Sweater] : ".$inqueryNo;
			disconnect($con);
			die;
		}
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",1);
		
		/*$field_array2="updated_by*update_date*status_active*is_deleted";
		$data_array2="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID1=sql_delete("lib_yarn_count_determina_dtls",$field_array2,$data_array2,"mst_id","".$update_mst_id."",1);*/
		
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 	if($rID)
			{
				oci_commit($con);   
				echo "2**".$rID;
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

if($action=="composition_popup")
{
	echo load_html_head_contents("Composition Popup","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:430px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th>Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <? 
                    $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $comp_name; ?> </td> 						
                        </tr>
                    <? $i++; } ?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}
	
if($action=="check_yarn_count_determination")
{
	//$data=explode("**",$data);
	$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$price_data_array=sql_select($price_sql);

	$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$pre_data_array=sql_select($pre_sql);
	 
	 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
	{
		echo "1_";
	}
	else
	{
		echo "0_";
	}
	exit();	
}
?>