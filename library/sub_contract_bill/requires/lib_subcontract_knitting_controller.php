<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
/*
$pc_time= add_time(date("H:i:s",time()),360);
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));*/
extract($_REQUEST);

if ($action=="load_drop_down_buyer_name")
{
	echo create_drop_down( "cbo_buyer_id", 171, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","","","","","",4 );
	exit();
}

if ($action=="list_container_subcont")
{
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array (0=>$buyer_arr,1=>$body_part,2=>$fabric_genetic_nameArr,9=>$unit_of_measurement,13=>$row_status);
	echo  create_list_view ( "list_view", "Buyer Name,Body Part,Fabric Generic Name,Construction,Composition,GSM,Gauge,Yarn Description,In House Rate,UOM,Spandex Category,Dyeing Type,Customer Rate,Status", "120,100,100,150,150,60,60,100,70,70,100,100,70,60","1380","220",1, "select id, body_part,spandex_category,dyeing_type,fabric_genetic,composition, const_comp, gsm,gauge, yarn_description, uom_id, status_active, customer_rate, buyer_id, in_house_rate from lib_subcon_charge where is_deleted=0 and rate_type_id=2 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,body_part,fabric_genetic,0,0,0,0,0,0,uom_id,0,0,0,status_active", $arr , "buyer_id,body_part,fabric_genetic,const_comp,composition,gsm,gauge,yarn_description,in_house_rate,uom_id,spandex_category,dyeing_type,customer_rate,status_active", "requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,2,0' ) ;
	 exit();
}

if ($action=="load_php_data_to_form")
{
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$nameArray=sql_select( "select id, comapny_id, body_part, cons_comp_id, const_comp, gsm,gauge, yarn_description, in_house_rate, uom_id, status_active, customer_rate, buyer_id ,color_id,spandex_category,dyeing_type,fabric_genetic,composition from lib_subcon_charge where id='$data' " );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".$inf[csf("comapny_id")]."';\n";
		echo "document.getElementById('cbo_body_part').value = '".$inf[csf("body_part")]."';\n";
		echo "document.getElementById('txt_cons_comp_id').value = '".$inf[csf("cons_comp_id")]."';\n";
		echo "document.getElementById('text_cons_comp').value  	= '".$inf[csf("const_comp")]."';\n";

		echo "document.getElementById('text_spandex_cat').value  	= '".$inf[csf("spandex_category")]."';\n";
		echo "document.getElementById('cbo_fabric_genetic').value  	= '".$inf[csf("fabric_genetic")]."';\n";
		echo "document.getElementById('text_dyeing_type').value  	= '".$inf[csf("dyeing_type")]."';\n";
		echo "document.getElementById('text_composition').value  	= '".$inf[csf("composition")]."';\n";

		echo "document.getElementById('text_gsm').value = '".$inf[csf("gsm")]."';\n";
		echo "document.getElementById('text_gauge').value = '".$inf[csf("gauge")]."';\n";
		echo "document.getElementById('text_yarn_description').value = '".$inf[csf("yarn_description")]."';\n";
		echo "document.getElementById('text_inhouse_rate').value = '".$inf[csf("in_house_rate")]."';\n";
		echo "document.getElementById('cbo_uom').value = '".$inf[csf("uom_id")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$inf[csf("status_active")]."';\n";
		echo "document.getElementById('txt_customer_rate').value = '".$inf[csf("customer_rate")]."';\n";
		echo "load_drop_down( 'requires/lib_subcontract_knitting_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_name', 'buyer_td' );\n";
		echo "document.getElementById('cbo_buyer_id').value = '".$inf[csf("buyer_id")]."';\n";
		echo "document.getElementById('update_id').value = '".$inf[csf("id")]."';\n";
		echo "document.getElementById('cbo_color_id').value = '".$inf[csf("color_id")]."';\n";
		echo "document.getElementById('cbo_color').value = '".$color_library[$inf[csf("color_id")]]."';\n";
		//echo "fnc_variable_settings_check(".$inf[csf("comapny_id")])";\n";
		echo "fnc_variable_settings_check(".$inf[csf("comapny_id")].");\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_lib_subcontract_knitting',1);\n";
	}
}

if($action == "load_variable_settings"){
	$nameArray=sql_select("select id, textile_business_concept from variable_settings_production where company_name='$data' and variable_list=52 and status_active=1 and is_deleted=0");
	echo $nameArray[0][csf('textile_business_concept')];
	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            if($buyer_name=="" || $buyer_name==0)
            {
            	$sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0";
            }
            else
            {
            	$sql="select a.id, a.color_name FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and status_active=1 and is_deleted=0";
            }
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name,id", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$date= date('Y-m-d');
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//
		$id=return_next_id( "id", "lib_subcon_charge", 1 ) ;
		$field_array="id, comapny_id, body_part, cons_comp_id, const_comp,spandex_category,dyeing_type,fabric_genetic,composition, gsm,gauge, yarn_description, uom_id, in_house_rate, customer_rate, buyer_id, inserted_by, insert_date, status_active, is_deleted, rate_type_id,color_id";
		$data_array="(".$id.",".trim($cbo_company_name).",".trim($cbo_body_part).",".trim($txt_cons_comp_id).",".trim($text_cons_comp).",".trim($text_spandex_cat).",".trim($text_dyeing_type).",".$cbo_fabric_genetic.",".trim($text_composition).",".trim($text_gsm).",".$text_gauge.",".trim($text_yarn_description).",".trim($cbo_uom).",".trim($text_inhouse_rate).",".$txt_customer_rate.",".$cbo_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".trim($cbo_status).",'0','2',".$cbo_color_id.")";
		//echo "INSERT INTO lib_subcon_charge (".$field_array.") VALUES ".$data_array; die;
		$rID=sql_insert("lib_subcon_charge",$field_array,$data_array,1);
		//echo $rID; die;
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$rID;
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);//text_spandex_cat*cbo_fabric_genetic*text_dyeing_type*text_composition
		$field_array="comapny_id*body_part*cons_comp_id*const_comp*spandex_category*dyeing_type*fabric_genetic*composition*gsm*gauge*yarn_description*uom_id*in_house_rate*customer_rate*buyer_id*color_id*updated_by*update_date*status_active*is_deleted";
		$data_array="".trim($cbo_company_name)."*".trim($cbo_body_part)."*".trim($txt_cons_comp_id)."*".trim($text_cons_comp)."*".trim($text_spandex_cat)."*".trim($text_dyeing_type)."*".trim($cbo_fabric_genetic)."*".trim($text_composition)."*".trim($text_gsm)."*".$text_gauge."*".trim($text_yarn_description)."*".trim($cbo_uom)."*".trim($text_inhouse_rate)."*".trim($txt_customer_rate)."*".trim($cbo_buyer_id)."*".$cbo_color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".trim($cbo_status)."*0";

		$rID=sql_update("lib_subcon_charge",$field_array,$data_array,"id","".$update_id."",1);
		//echo $rID; die;
		if($db_type==0)
		{
			if($rID )
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
		if($db_type==2)
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
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
		die;
	}
}

if ($action=="save_update_delete_supplier")
{
	$date= date('Y-m-d');
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

		//echo "INSERT INTO lib_subcon_supplier_rate (".$field_array.") VALUES ".$data_array_dtls; die;
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
		if($data_array_dtls!="")
		{
			//echo "INSERT INTO lib_subcon_supplier_rate (".$field_array.") VALUES ".$data_array_dtls; die;
			$rID=sql_insert("lib_subcon_supplier_rate",$field_array,$data_array_dtls,1);
			if($rID) $flag=1; else $flag=0;
		}

		$rIDl=true;
		if(count($update_array_dtls)>0)
	 	{
			//echo bulk_update_sql_statement("lib_subcon_supplier_rate","id",$field_array_update,$update_array_dtls,$updateDetailsId_arr);
		    $rIDl=execute_query(bulk_update_sql_statement("lib_subcon_supplier_rate","id",$field_array_update,$update_array_dtls,$updateDetailsId_arr),1);
			if($flag==1)
			{
				if($rIDl) $flag=1; else $flag=0;
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
		$con = connect();
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
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
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
	//print_r($composition_arr);
	$sql="select a.id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 group by a.id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss order by a.id DESC";
	$result = sql_select($sql); $i=1;
	?>
    <table width="750" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="120">Construction</th>
            <th width="100">GSM/Weight</th>
            <th width="90">Color Range</th>
            <th width="80">Stich Length</th>
            <th>Composition</th>
        </thead>
    </table>
    <div style="width:750; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'***'.$row[csf("construction")].', '.$composition_arr[$row[csf("id")]].'***'.$row[csf("gsm_weight")]; ?>')" id="tr_<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="120"><? echo $row[csf("construction")]; ?></td>
                        <td width="100"><? echo $row[csf("gsm_weight")]; ?></td>
                        <td width="90"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                        <td width="80"><? echo $row[csf("stich_length")]; ?></td>
                        <td><? echo $composition_arr[$row[csf("id")]]; ?></td>
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
			http.open("POST","lib_subcontract_knitting_controller.php",true);
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

					show_list_view(reponse[2],'show_suppler_view','supplier_body','lib_subcontract_knitting_controller','');
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
						$supplier_sql=sql_select("select a.id as ID,a.supplier_name as NAME from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20");
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

if($action=="show_suppler_view")
{

	$dataArray=array();
	$update_sql=sql_select("select id, mst_id, supplier_id, supplier_rate from lib_subcon_supplier_rate where mst_id=$data");
	foreach($update_sql as $val)
	{
		$updateArray[$val[csf('supplier_id')]]['rate']=$val[csf('supplier_rate')];
		$updateArray[$val[csf('supplier_id')]]['id']=$val[csf('id')];
	}


	$supplier_sql=sql_select("select a.id as ID,a.supplier_name as NAME from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20");


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
