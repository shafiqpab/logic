<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="load_drop_down_buyer_name")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","","","","","",4 ); 
	exit();
}

//=================BROWSE LIST VIEW==============
if ($action=="list_view_subcon_dying_charge")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "select id, company_short_name  from lib_company",'id','company_short_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,10=>$buyer_arr,11=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Cust. Rate,Buyer,Status", "90,130,70,70,70,80,60,40,60,70,80,50","970","250",1, "select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8)", "get_php_form_data", "id","'load_php_data_to_form'", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,0,buyer_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,customer_rate,buyer_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,2,0,0' );
	exit();
}

if ($action=="load_php_data_to_form")
{
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$nameArray=sql_select( "select id, comapny_id, const_comp, cons_comp_id, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id,status_active from lib_subcon_charge where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_id').value 		= '".$inf[csf("comapny_id")]."';\n";    
		echo "document.getElementById('text_const_compo').value 	= '".$inf[csf("const_comp")]."';\n"; 
		echo "document.getElementById('txt_cons_comp_id').value 	= '".$inf[csf("cons_comp_id")]."';\n"; 
		echo "document.getElementById('cbo_process_type').value  	= '".$inf[csf("process_type_id")]."';\n";
		echo "document.getElementById('cbo_process_id').value  		= '".$inf[csf("process_id")]."';\n";
		echo "document.getElementById('txt_color').value  			= '".$color_library_arr[$inf[csf("color_id")]]."';\n";
		echo "document.getElementById('cbo_dia_width').value  		= '".$inf[csf("width_dia_id")]."';\n"; 
		echo "document.getElementById('text_in_house_rate').value  	= '".$inf[csf("in_house_rate")]."';\n"; 
		echo "document.getElementById('cbo_uom').value  			= '".$inf[csf("uom_id")]."';\n";
		echo "document.getElementById('cbo_rate_type').value  		= '".$inf[csf("rate_type_id")]."';\n"; 
		echo "document.getElementById('txt_customer_rate').value  	= '".$inf[csf("customer_rate")]."';\n"; 
		echo "document.getElementById('cbo_buyer_id').value  		= '".$inf[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('cbo_status').value  			= '".$inf[csf("status_active")]."';\n"; 
		echo "document.getElementById('update_id').value  			= '".$inf[csf("id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_dyeing_charge',1);\n";  
	}
}
//=================SAVE UPDATE DELETE==============
$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)
	{
		$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		
		if (is_duplicate_field( "id", "lib_subcon_charge", "comapny_id=$cbo_company_id and const_comp=$text_const_compo and process_type_id=$cbo_process_type and process_id=$cbo_process_id and color_id=$color_tbl_id and width_dia_id=$cbo_dia_width and in_house_rate=$text_in_house_rate and uom_id=$cbo_uom and rate_type_id=$cbo_rate_type and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
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
			$id=return_next_id( "id", "lib_subcon_charge", 1 ) ; 
			$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
			$field_array="id, comapny_id, const_comp, cons_comp_id, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_id.",".$text_const_compo.",".$txt_cons_comp_id.",".$cbo_process_type.",".$cbo_process_id.",'".$color_tbl_id."',".$cbo_dia_width.",".$text_in_house_rate.",".$cbo_uom.",".$cbo_rate_type.",".$txt_customer_rate.",".$cbo_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_subcon_charge",$field_array,$data_array,1);
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
		/*if (is_duplicate_field( "id", "lib_subcon_charge", "comapny_id=$cbo_company_id and rate_type_id=$cbo_rate_type and  uom_id=$cbo_uom  and in_house_rate=$text_in_house_rate and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; die;
		}*/
		$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		if (is_duplicate_field( "id", "lib_subcon_charge", "comapny_id=$cbo_company_id and const_comp=$text_const_compo and process_type_id=$cbo_process_type and process_id=$cbo_process_id and color_id=$color_tbl_id and width_dia_id=$cbo_dia_width and in_house_rate=$text_in_house_rate and uom_id=$cbo_uom and rate_type_id=$cbo_rate_type and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
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
			$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
			$field_array="comapny_id*const_comp*cons_comp_id*process_type_id*process_id*color_id*width_dia_id*in_house_rate*uom_id*rate_type_id*customer_rate*buyer_id*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_id."*".$text_const_compo."*".$txt_cons_comp_id."*".$cbo_process_type."*".$cbo_process_id."*".$color_tbl_id."*".$cbo_dia_width."*".$text_in_house_rate."*".$cbo_uom."*".$cbo_rate_type."*".$txt_customer_rate."*".$cbo_buyer_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			
			$rID=sql_update("lib_subcon_charge",$field_array,$data_array,"id","".$update_id."",1);
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
		
		$rID=sql_delete("lib_subcon_charge",$field_array,$data_array,"id","".$update_id."",1);
		
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

if($action=="const_comp_popup")
{
	echo load_html_head_contents("Construction & Composition Popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('hddn_sll_data').value=val;
		parent.emailwindow.hide();
	}
	</script>
     <input type="hidden" id="hddn_sll_data" />
	<?
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	
	
	echo $sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
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
		}
	}
	unset($data_array);

	$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss,a.sequence_no, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id,a.status_active,b.status_active from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 order by b.id";
	$result = sql_select($sql); 
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$row_status);

	$i=1;
	?>
    <table width="865" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="35">SL</th>
        	<th width="80">Fab Nature</th>
            <th width="120">Construction</th>
            <th width="100">GSM/Weight</th>
            <th width="90">Color Range</th>
            <th width="80">Stich Length</th>
            <th width="80">Process Loss</th>
            <th width="150">Composition</th>
            <th width="80">Sequence No</th>
            <th width="50">Status</th>
        </thead>
    </table>
    <div style="width:885; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="865" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $row[csf("id")].'***'.$row[csf("construction")].', '.$composition_arr[$row[csf("id")]];//.'***'.$row[csf("gsm_weight")] ?>')" id="tr_<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $row[csf("fab_nature_id")]; ?></td>
                        <td width="120"><? echo $row[csf("construction")]; ?></td>
                        <td width="100"><? echo $row[csf("gsm_weight")]; ?></td>
                        <td width="90"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                        <td width="80"><? echo $row[csf("stich_length")]; ?></td>
                        <td width="80"><? echo $row[csf("process_loss")]; ?></td>
                        <td width="150"><? echo $yarn_type[$row[csf("copmposition_id")]]; ?></td>
                        <td width="80"><? echo $row[csf("sequence_no")]; ?></td>
                        <td width="50"><? echo $row[csf("status_active")]; ?></td>
                        
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <script> setFilterGrid("table_body",-1); </script>
    <?
	exit();					
}


if ($action=="Supplier_workorder_popup")
{
	echo load_html_head_contents("Production Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	
	if($cbo_rate_type==6) $party_type=25;
	else if($cbo_rate_type==7) $party_type=24;
	else $party_type=21;
	
	//echo $update_id."fdf";die;
?> 
	<script>
		var permission='<? echo $permission; ?>';
		//alert(permission);
		  
	
		function fnc_close()
		{
			var save_string='';
			
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var shiftId=$(this).find('input[name="shiftId[]"]').val();
				var txt_prod_start_time=$(this).find('input[name="txt_prod_start_time[]"]').val();
				var txt_lunch_start_time=$(this).find('input[name="txt_lunch_start_time[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();

				if(save_string=="")
				{
					save_string=shiftId+"_"+txt_prod_start_time+"_"+txt_lunch_start_time+"_"+txtRemarks;
				}
				else
				{
					save_string+=","+shiftId+"_"+txt_prod_start_time+"_"+txt_lunch_start_time+"_"+txtRemarks;
				}
			});
			
			$('#hide_save_string').val( save_string );
			
			parent.emailwindow.hide();
		}
		
		function fnc_lib_supplier_rate( operation )
		{   
			var update_mst_id=<? echo $update_id; ?>;
			
			var j=0; var dataString='';
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var updateDetailsId=$(this).find('input[name="update_details_id[]"]').val();
				var txt_supplier_id=$(this).find('input[name="txt_supplier_id[]"]').val();
				var txt_supplier_rate=$(this).find('input[name="txt_supplier_rate[]"]').val();
				//var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
				if(txt_supplier_rate!="")
				{
					j++;
					dataString+='&txt_supplier_id_' + j + '=' + txt_supplier_id + '&txt_supplier_rate_' + j + '=' + txt_supplier_rate + '&updateDetailsId_' + j + '=' + updateDetailsId;
				}
				
			});
			if(j<1)
			{
				alert('No data found');
				return;
			}
			
			var data1="action=save_update_delete_supplier&operation="+operation+"&row_num="+j+"&update_mst_id="+update_mst_id;
			
			var data=data1+dataString;
			//freeze_window(operation);
			http.open("POST","lib_subcontract_dyeing_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_lib_supplier_rate_reponse;
		}
		
		function fnc_lib_supplier_rate_reponse()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();;return;
				var reponse=trim(http.responseText).split('**');
				if(reponse[0]==0 || reponse[0]==1)
				{
					 if(reponse[0]==0)
					 {
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Save  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 }
					if(reponse[0]==1)
					{
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					}
					
					show_list_view(reponse[2]+"_"+<? echo $party_type; ?>,'show_suppler_view','supplier_body','lib_subcontract_dyeing_controller','');
					set_button_status(1, permission, 'fnc_lib_supplier_rate',1,1);
				 }
				 release_freezing();
			}
		} 
		
		
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
            <input type="hidden" name="hide_save_string" id="hide_save_string" class="text_boxes" value="">
            <div style="width:520px;max-height:450px;" align="center">
                <table cellspacing="0" width="450" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="35">SL</th>
                        <th width="300">Supplier Name</th>
                        <th width="">rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?
						$i=1; $updateArray=array();
						$update_sql=sql_select("select id, mst_id, supplier_id, supplier_rate from lib_subcon_supplier_rate where mst_id=$update_id");
						foreach($update_sql as $val)
						{
							$updateArray[$val[csf('supplier_id')]]['rate']=$val[csf('supplier_rate')];
							$updateArray[$val[csf('supplier_id')]]['id']=$val[csf('id')];
						}
						//print_r($updateArray);
						$supplier_sql=sql_select("select a.id as ID,a.supplier_name as NAME from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=$party_type");
					//echo $sql;	
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25"> 
								<td><?php echo $i; ?></td>
								<td align="left"><b><? echo $row['NAME']; ?></b>
									<input class="text_boxes" type="hidden"  name="txt_supplier_id[]" id="txt_supplier_id_<? echo $i; ?>" value="<? echo  $row['ID']; ?>">
								</td>
								<td>
									<input class="text_boxes_numeric" type="text" style="width:120px" name="txt_supplier_rate[]" id="txt_supplier_rate_<? echo $i; ?>" value="<? echo $updateArray[$row['ID']]['rate']; ?>" />
                                    <input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $updateArray[$row['ID']]['id']; ?>">
								</td>
					
							</tr>
							<? 
							$i++;
						}
                        ?>
                    </tbody>
                </table>
                <table width="500">
                     <tr>
                        <td align="center" >
                        	<? 
							if(count($updateArray)>0)
							{
								echo load_submit_buttons( $permission, "fnc_lib_supplier_rate", 1,0,"",1);
							}
							else
							{
								echo load_submit_buttons( $permission, "fnc_lib_supplier_rate", 0,0,"",1);
							}
							?>
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        </td>
                    </tr>
                </table>
            </div>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if ($action=="save_update_delete_supplier")
{
	$date= date('Y-m-d');
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "lib_subcon_supplier_rate", 1 ) ;
		$field_array="id, mst_id, supplier_id, supplier_rate,inserted_by, insert_date, status_active, is_deleted";
		for($j=1;$j<=$row_num;$j++)
		{ 
			$supplier_id="txt_supplier_id_".$j;
			$supplier_rate="txt_supplier_rate_".$j;
			//$bodyPart="updateDetailsId_".$j;
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id.",".$update_mst_id.",".$$supplier_id.",'".$$supplier_rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
	
		$rID=sql_insert("lib_subcon_supplier_rate",$field_array,$data_array_dtls,1);
		//echo $rID; die;
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$update_mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		 	if($rID )
			{
				oci_commit($con);   
				echo "0**".$rID."**".$update_mst_id;
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "lib_subcon_supplier_rate", 1 ) ;
		$field_array="id, mst_id, supplier_id, supplier_rate,inserted_by, insert_date, status_active, is_deleted";
		$field_array_update="supplier_rate*updated_by*update_date";
		for($j=1;$j<=$row_num;$j++)
		{ 	
			$supplier_id="txt_supplier_id_".$j;
			$supplier_rate="txt_supplier_rate_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			if(str_replace("'","",$$updateDetailsId)!="")
			{
				$updateDetailsId_arr[]=str_replace("'","",$$updateDetailsId);
				$update_array_dtls[str_replace("'","",$$updateDetailsId)]=explode("*",("".$$supplier_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id.",".$update_mst_id.",".$$supplier_id.",'".$$supplier_rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id=$id+1;
			}
		}
	
		
		$flag=0;  $rID=true;
		
	
		$rID=true;
		if(count($update_array_dtls)>0)
	 	{
			//echo bulk_update_sql_statement("lib_subcon_supplier_rate","id",$field_array_update,$update_array_dtls,$updateDetailsId_arr);
		    $rID=execute_query(bulk_update_sql_statement("lib_subcon_supplier_rate","id",$field_array_update,$update_array_dtls,$updateDetailsId_arr),1);
			
				if($rID) $flag=1; else $flag=0; 
	 	}
		
		if($data_array_dtls!="")
		{
			//echo "INSERT INTO lib_subcon_supplier_rate (".$field_array.") VALUES ".$data_array_dtls; die;
			$rID1=sql_insert("lib_subcon_supplier_rate",$field_array,$data_array_dtls,1);
			if($flag==1) 
			{
				if($rID1) $flag=1; else $flag=0; 
			}
		}
		//echo $flag."jhkjhk"; die; 
		if($db_type==0)
		{
			if($flag==1 )
			{
				mysql_query("COMMIT");  
				echo "1**".$rID."**".str_replace("'","",$update_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2)
		{
		    if($flag==1)
			{
				oci_commit($con);   
				echo "1**".$rID."**".str_replace("'","",$update_mst_id);
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
	else if ($operation==2)   // Delete Here
	{
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_subcon_charge",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID )
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
		if($db_type==2)
		{
	        if($rID )
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
		die;*/
	}
}


if($action=="show_suppler_view")
{
	$data=explode("_",$data);
	$dataArray=array();
	$update_sql=sql_select("select id, mst_id, supplier_id, supplier_rate from lib_subcon_supplier_rate where mst_id=$data[0]");
	foreach($update_sql as $val)
	{
		$updateArray[$val[csf('supplier_id')]]['rate']=$val[csf('supplier_rate')];
		$updateArray[$val[csf('supplier_id')]]['id']=$val[csf('id')];
	}
	
	
	$supplier_sql=sql_select("select a.id as ID,a.supplier_name as NAME from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=$data[1]");
	
	
//echo $sql;
	$i=1; 	
	foreach($supplier_sql as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
		
		
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25"> 
			<td><?php echo $i; ?></td>
			<td align="left"><b><? echo $row['NAME']; ?></b>
				<input class="text_boxes" type="hidden"  name="txt_supplier_id[]" id="txt_supplier_id_<? echo $i; ?>" value="<? echo $row['ID']; ?>">
			</td>
			<td>
				<input class="text_boxes_numeric" type="text" style="width:120px" name="txt_supplier_rate[]" id="txt_supplier_rate_<? echo $i; ?>" value="<? echo  $updateArray[$row['ID']]['rate']; ?>" />
				<input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $updateArray[$row['ID']]['id']; ?>">
			</td>

		</tr>
		<? 
		$i++;
	}
                        
}      

?>
        
