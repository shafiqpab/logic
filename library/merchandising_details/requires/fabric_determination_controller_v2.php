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
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	 $group_short_name=$lib_group_short[1];
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
	
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
			$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
			$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
		}
	}
	unset($data_array);
	

	$sql="select id,fab_nature_id, type, rd_no, construction, gsm_weight, weight_type, design, fabric_ref, color_range_id,inserted_by,status_active,full_width,cutable_width,shrinkage_l,shrinkage_w from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=581 order by id DESC";				
	$arr=array (0=>$item_category, 3=>$sysCodeArr, 8=>$fabric_weight_type, 9=>$color_range,14=>$composition_arr,15=>$user_arr, 16=>$row_status);
	echo  create_list_view ( "list_view", "Fab Nature,Fabric Ref,RD NO,Sys Code,Type,Construction,Design,Weight,Weight Type,Color Range,Full Width,Cutable Width,Shrinkage L %,Shrinkage W %,Composition,Insert By,Status", "100,100,50,80,100,100,100,50,50,50,50,50,60,60,300,100,50","1570","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,0,id,0,0,0,0,weight_type,color_range_id,0,0,0,0,id,inserted_by,status_active", $arr , "fab_nature_id,fabric_ref,rd_no,id,type,construction,design,gsm_weight,weight_type,color_range_id,full_width,cutable_width,shrinkage_l,shrinkage_w,id,inserted_by,status_active", "requires/fabric_determination_controller_v2",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,2,0,0,0,0,0,0,0') ;
				
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select id,fab_nature_id, type, construction,grey_construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, entry_form, inserted_by,insert_date,status_active,is_deleted,full_width,cutable_width,shrinkage_l,shrinkage_w,fabric_construction_id,grey_construction_id,grey_width,fabric_ref from lib_yarn_count_determina_mst where id='$data' and entry_form=581");
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");	
	foreach ($nameArray as $inf)
	{
		$group_short_name=$lib_group_short[1];
		$sys_code=$group_short_name.'-'.$inf[csf("id")];
		echo "document.getElementById('cbo_fabric_nature').value  	= '".($inf[csf("fab_nature_id")])."';\n";
		echo "document.getElementById('txt_sys_code').value  	= '".($sys_code)."';\n";
		echo "document.getElementById('txttype').value  			= '".($inf[csf("type")])."';\n";
		echo "document.getElementById('txtconstruction').value  	= '".($inf[csf("construction")])."';\n";
		echo "document.getElementById('txtgreyconstruction').value  	= '".($inf[csf("grey_construction")])."';\n";
		echo "document.getElementById('txtweight').value 			= '".($inf[csf("gsm_weight")])."';\n";
		echo "document.getElementById('cboweighttype').value  		= '".($inf[csf("weight_type")])."';\n";
		echo "document.getElementById('txtdesign').value  			= '".($inf[csf("design")])."';\n";
		echo "document.getElementById('txtrdno').value  			= '".($inf[csf("rd_no")])."';\n";
		echo "document.getElementById('txtfabricref').value  			= '".($inf[csf("fabric_ref")])."';\n";
		echo "document.getElementById('txt_full_width').value  		= '".($inf[csf("full_width")])."';\n";
		echo "document.getElementById('txt_cutable_width').value  	= '".($inf[csf("cutable_width")])."';\n";
		echo "document.getElementById('txt_grey_width').value  		= '".($inf[csf("grey_width")])."';\n";
		echo "document.getElementById('txt_shrinkage_l').value  	= '".($inf[csf("shrinkage_l")])."';\n";
		echo "document.getElementById('txt_shrinkage_w').value  	= '".($inf[csf("shrinkage_w")])."';\n";
		
		echo "document.getElementById('cbocolortype').value  		= '".($inf[csf("color_range_id")])."';\n";
		echo "document.getElementById('cbo_status').value  			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_mst_id').value  		= '".($inf[csf("id")])."';\n";
		echo "document.getElementById('fab_construction_id').value  			= '".($inf[csf("fabric_construction_id")])."';\n";
		echo "document.getElementById('grey_construction_id').value  			= '".($inf[csf("grey_construction_id")])."';\n";
		$fabric_construction_id=$inf[csf("fabric_construction_id")];
		$grey_construction_id=$inf[csf("grey_construction_id")];
		
		$fabric_construction=return_field_value("epi || '*' || ppi ||'*' || warp_count || '*' || wrap_spandex || '*' || weft_count || '*' || weft_spandex as fab_con", "lib_fabric_construction", "id =$fabric_construction_id", "fab_con");
		echo "document.getElementById('fab_construction').value  			= '".$fabric_construction."';\n";
		
		$grey_construction=return_field_value("epi || '*' || ppi ||'*' || warp_count || '*' || wrap_spandex || '*' || weft_count || '*' || weft_spandex as fab_con", "lib_fabric_construction", "id =$grey_construction_id", "fab_con");

		echo "document.getElementById('grey_construction').value  			= '".$grey_construction."';\n";
		// $fabric_construction = sql_select("select id, fabric_construction_name,epi,ppi,warp_count,weft_count from  lib_fabric_construction where status_active=1 and is_deleted=0 order by fabric_construction_name");
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_count_determination',1);\n";  
	    echo "show_detail_form('".$inf[csf("id")]."');\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	?>
	<table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
            	<th width="150" class="must_entry_caption">
            	<font color="blue">Composition</font>
            	</th>
            	<th width="50" class="must_entry_caption">
            		<font color="blue">%</font>
            	</th>
            	<th width="150">Count</th>
            	<th width="150">Type</th>
            	<th width="">  </th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("select id, copmposition_id, percent,count_id,type_id from  lib_yarn_count_determina_dtls where mst_id='$data' and comp_type = 1  order by id asc");
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
if($action =="show_detail_form1")
{
	?>
	
	<table width="100%" border="0" id="tbl_yarn_count1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
            	<th width="150" class="must_entry_caption">
            		<font color="blue">Composition</font>
            	</th>
            	<th width="50" class="must_entry_caption">
            		<font color="blue">%</font>
            	</th>
            	<th width="150">Count</th>
            	<th width="150">Type</th>
            	<th width="">  </th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("select id, copmposition_id, percent,count_id,type_id from  lib_yarn_count_determina_dtls where mst_id='$data' and comp_type = 2 order by id asc");
				if ( count($data_array)>0)
				{
					$i=0;
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="yarncost_1" align="center">
                            <td width="150">
                            	<input type="text" id="wetxtcompone_<? echo $i; ?>"  name="wetxtcompone_<? echo $i; ?>"  class="text_boxes" style="width:140px" readonly placeholder="Browse" onDblClick="we_openmypage_comp(<? echo $i; ?>);" value="<? echo $composition[$row[csf("copmposition_id")]]; ?>" />
                                <input type="hidden" id="wecbocompone_<? echo $i; ?>"  name="wecbocompone_<? echo $i; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf("copmposition_id")]; ?>" />
                            
                            </td>
                            <td width="50">
                            	<input type="text" id="wepercentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="sum_percent()"  value="<? echo  $row[csf("percent")]; ?>" />
                            </td>
                            <td width="70"><? echo create_drop_down( "wecbocountcotton_".$i, 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --",  $row[csf("count_id")], "we_check_duplicate(".$i.",this.id )",'','' ); 
                            ?>
                            </td>
                            <td width="100">
                            	<? 
                           		echo create_drop_down( "wecbotypecotton_".$i, 150, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "check_duplicate(".$i.",this.id )",'','','','',$ommitYarnType); 
                            	?>
                            </td>
                            <td> 
                                <input type="button" id="weincrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="we_add_break_down_tr(<? echo $i; ?>)" />
                                <input type="button" id="wedecrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:we_fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                <input type="hidden" id="weupdateid_<? echo $i; ?>" name="weupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=" <? echo $row[csf("id")]; ?>"  />  
                            </td>  
						</tr>
						<?
					}
				}
	            ?>
        </tbody>
	</table>
	<tr><td><input type="hidden" name="wetotal_percent" id="wetotal_percent" value=""></td></tr>
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
		$construction=str_replace("'",'',$txtconstruction);
		$txtconstruction=str_replace($str_rep,'',$construction);
		$greyconstruction=str_replace("'",'',$txtgreyconstruction);
		$txtgreyconstruction=str_replace($str_rep,'',$greyconstruction);
		$txttype=str_replace($str_rep,'',$txttype);
		$txtdesign=str_replace($str_rep,'',$txtdesign);
		$txtfabricref=str_replace($str_rep,'',$txtfabricref);
		$txtrdno=str_replace($str_rep,'',$txtrdno);
		$fab_construction_id=str_replace("'",'',$fab_construction_id);
		$grey_construction_id=str_replace("'",'',$grey_construction_id);
		$rIDCon=true;
		$rIDCon1=true;
		$rIDConFlag=true;
		$rIDCon3=true;
		$rIDCon4=true;
		$rIDConFlag2=true;
		$duplicate_query=sql_select("SELECT id from lib_yarn_count_determina_mst where fab_nature_id=$cbo_fabric_nature and type='$txttype' and construction='$txtconstruction' and gsm_weight=$txtweight and weight_type=$cboweighttype and design='$txtdesign'  and color_range_id=$cbocolortype and entry_form=581 and is_deleted=0");
		if (count($duplicate_query)>0)
		{
			echo "11**0"; disconnect($con); die;
		}
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=581 and is_deleted=0"; die;
		$duplicate_data=sql_select("SELECT id from lib_yarn_count_determina_mst where rd_no='$txtrdno' and entry_form=581 and is_deleted=0");

		if(empty($fab_construction_id))
		{
			$fab_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
			if(empty($fab_construction_id))
			{
				$exp_fab = str_replace("'",'',$fab_construction);
				$fab_construction=explode("*",$exp_fab);

				$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

				$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
				$data_array="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rIDCon=sql_insert("lib_fabric_construction",$field_array,$data_array,1);


				$wrap_details = explode(",",$fab_construction[2]);
				$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
				$field_details="id,mst_id,type,counts,count_type";
				$data_details="";
				foreach($wrap_details as $wrap_d)
				{
					$wr_exp = explode("_",$wrap_d);
					if(!empty($data_details))
					{
						$data_details .=",";
					}
					$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."')";
					
					$fab_dts_id++;
				}
				$weft_details = explode(",",$fab_construction[4]);
				foreach($weft_details as $wrap_d)
				{
					$wr_exp = explode("_",$wrap_d);
					if(!empty($data_details))
					{
						$data_details .=",";
					}
					$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."')";
					$fab_dts_id++;
				}

				$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
				if($rIDConFlag == false || $rIDCon1 == false)
				{
					$rIDConFlag = false;
				}
			}
		}
		if(empty($grey_construction_id))
		{
			$grey_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtgreyconstruction'",  "id");
			if(empty($grey_construction_id))
			{
				$exp_fab = str_replace("'",'',$grey_construction);
				$grey_construction=explode("*",$exp_fab);

				$grey_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

				$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,construction_type,status_active,inserted_by,insert_date";
				$data_array="(".$grey_construction_id.",'".$txtgreyconstruction."','".$grey_construction[0]."','".$grey_construction[1]."','".$grey_construction[2]."','".$grey_construction[4]."','".$grey_construction[3]."','".$grey_construction[5]."',2,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rIDCon3=sql_insert("lib_fabric_construction",$field_array,$data_array,1);

				//echo "10**INSERT INTO lib_fabric_construction(".$field_array.") VALUES ".$data_array;die;
				$wrap_details = explode(",",$grey_construction[2]);
				$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
				$field_details="id,mst_id,type,counts,count_type,construction_type";
				$data_details="";
				foreach($wrap_details as $wrap_d)
				{
					$wr_exp = explode("_",$wrap_d);
					if(!empty($data_details))
					{
						$data_details .=",";
					}
					$data_details.="(".$fab_dts_id.",".$grey_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
					
					$fab_dts_id++;
				}
				$weft_details = explode(",",$grey_construction[4]);
				foreach($weft_details as $wrap_d)
				{
					$wr_exp = explode("_",$wrap_d);
					if(!empty($data_details))
					{
						$data_details .=",";
					}
					$data_details.="(".$fab_dts_id.",".$grey_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
					$fab_dts_id++;
				}

				$rIDCon4=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
				if($rIDConFlag2 == false || $rIDCon4 == false)
				{
					$rIDConFlag2 = false;
				}
			}
		}
		
		if (count($duplicate_data)>0) 
		{
			echo "11**0"; disconnect($con); die;
		}
		else{					
			$id=return_next_id( "id", "lib_yarn_count_determina_mst", 1);
			$field_array1= "id, fab_nature_id, type, construction,fabric_construction_id, grey_construction,grey_construction_id, gsm_weight, weight_type, design, rd_no, color_range_id, entry_form, full_width, cutable_width,grey_width,shrinkage_l,shrinkage_w,fabric_ref, inserted_by, insert_date, status_active, is_deleted";
			$data_array1="(".$id.",".$cbo_fabric_nature.",'".$txttype."','".$txtconstruction."','".$fab_construction_id."','".$txtgreyconstruction."',".$grey_construction_id.",".$txtweight.",".$cboweighttype.",'".$txtdesign."','".$txtrdno."',".$cbocolortype.",581,".$txt_full_width.",".$txt_cutable_width.",".$txt_grey_width.",'".str_replace("'", "", $txt_shrinkage_l)."','".str_replace("'", "", $txt_shrinkage_w)."','".str_replace("'", "", $txtfabricref)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
			$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,comp_type,inserted_by,insert_date,status_active,is_deleted";
			for ($i=1;$i<=$total_row;$i++)
			{
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbocountcotton="cbocountcotton_".$i;
				$cbotypecotton="cbotypecotton_".$i;
				$updateid="updateid_".$i;
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}

			for ($i=1;$i<=$total_row1;$i++)
			{
				$cbocompone="wecbocompone_".$i;
				$percentone="wepercentone_".$i;
				$cbocountcotton="wecbocountcotton_".$i;
				$cbotypecotton="wecbotypecotton_".$i;
				$updateid="weupdateid_".$i;
				if (!empty($data_array2)) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}
			
			//echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
			
			
			$rID=sql_insert("lib_yarn_count_determina_mst",$field_array1,$data_array1,0);
			$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
			//echo "10**".$rID.'=='.$rID_1;die;
			//check_table_status( $_SESSION['menu_id'],0);
			if($db_type==0)
			{
				if($rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDCon3 && $rIDCon4 && $rIDConFlag && $rIDConFlag2){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**$rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDCon3 && $rIDCon4 && $rIDConFlag && $rIDConFlag2";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDCon3 && $rIDCon4 && $rIDConFlag && $rIDConFlag2)
				{
					oci_commit($con);  
					echo "0**".$rID;
				}
			else{
					oci_rollback($con); 
					echo "10**$rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDCon3 && $rIDCon4 && $rIDConFlag && $rIDConFlag2";
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
		
		$inqueryNo=return_field_value("a.system_number as sys_no", "wo_quotation_inquery a, wo_quotation_inquery_fab_dtls b", " a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=581 and constraction=".$update_mst_id."" ,"sys_no");
		
		if($inqueryNo!="")
		{
			echo "50**Some Entries Found For This Fabric Determination, Update/Delete Not Allowed, \n Buyer Inquiry Woven : ".$inqueryNo;
			disconnect($con);
			die;
		}
		
		$duplicate_query=sql_select("SELECT id from lib_yarn_count_determina_mst where fab_nature_id=$cbo_fabric_nature and type='$txttype' and construction='$txtconstruction' and gsm_weight=$txtweight and weight_type=$cboweighttype and design='$txtdesign'  and color_range_id=$cbocolortype and entry_form=581 and is_deleted=0 and id<>$update_mst_id");
		if (count($duplicate_query)>0)
		{
			echo "11**0"; disconnect($con); die;
		}
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=581 and is_deleted=0 and id<>$update_mst_id"; die;

			$duplicate_data=sql_select("SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=581 and is_deleted=0 and id<>$update_mst_id");
			if (count($duplicate_data)>0)
			{
				echo "11**01"; disconnect($con); die;
			}
		
			if(str_replace("'","",$cbo_status)==2)
			{
				$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
				$rID_1=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
				if($db_type==0)
					{
						if($rID && $rID_1){
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
						if($rID && $rID_1)
						{
							oci_commit($con);  
							echo "1**".$rID;
						}
						else{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
				die;
			}
			else
			{
				$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$update_mst_id";
				$pre_data_array=sql_select($pre_sql);
				 
				 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$update_mst_id and a.is_deleted=0 and a.status_active=1"; 
				
				$data_array=sql_select($sql);
				$flag = 0;
				if(count($data_array)>0 || count($pre_data_array)>0)
				{
					$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
					$rID_1=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
					if($db_type==0)
					{
						if($rID && $rID_1){
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
						if($rID && $rID_1)
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
			$construction=str_replace("'",'',$txtconstruction);
			$txtconstruction=str_replace($str_rep,'',$construction);
			$greyconstruction=str_replace("'",'',$txtgreyconstruction);
			$txtgreyconstruction=str_replace($str_rep,'',$greyconstruction);
			$txttype=str_replace($str_rep,'',$txttype);
			$txtdesign=str_replace($str_rep,'',$txtdesign);
			$txtrdno=str_replace($str_rep,'',$txtrdno);

			$fab_construction_id=str_replace("'",'',$fab_construction_id);
			$grey_construction_id=str_replace("'",'',$grey_construction_id);
			$rIDCon=true;
			$rIDCon1=true;
			$rIDConFlag=true;
			$rIDCon3=true;
			$rIDCon4=true;
			$rIDConFlag2=true;
			if(empty($fab_construction_id))
			{
				$fab_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
				if(empty($fab_construction_id))
				{
					$exp_fab = str_replace("'",'',$fab_construction);
					$fab_construction=explode("*",$exp_fab);

					$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
					$data_array="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rIDCon=sql_insert("lib_fabric_construction",$field_array,$data_array,1);


					$wrap_details = explode(",",$fab_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."')";
						
						$fab_dts_id++;
					}
					$weft_details = explode(",",$fab_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."')";
						
						$fab_dts_id++;
					}
					$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						//echo "10**".$rIDCon1;die;
					if($rIDConFlag == false || $rIDCon1 == true)
					{
						$rIDConFlag = false;
					}
				}
			}
			else
			{
				$fabric_construction_name=return_field_value("fabric_construction_name", "lib_fabric_construction","id='$fab_construction_id' and (epi is not null or ppi is not null or warp_count is not null or weft_count is not null)",  "fabric_construction_name");
				if($fabric_construction_name != $txtconstruction)
				{
					$exp_fab = str_replace("'",'',$fab_construction);
					$fab_construction=explode("*",$exp_fab);

					$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,construction_type,status_active,inserted_by,insert_date";
					$data_array="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',2,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rIDCon=sql_insert("lib_fabric_construction",$field_array,$data_array,1);

					
					$wrap_details = explode(",",$fab_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type,construction_type";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
						
						$fab_dts_id++;
					}
					 
					$weft_details = explode(",",$fab_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
						
						$fab_dts_id++;
					}
						$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						//echo "10**INSERT INTO lib_fab_construction_dtls(".$field_details.") VALUES ".$data_details;die;
						if($rIDConFlag != true || $rIDCon1 != true)
						{
							$rIDConFlag = false;
						}
				}
			}
			
			if(empty($grey_construction_id))
			{
				$grey_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtgreyconstruction'",  "id");
				if(empty($grey_construction_id))
				{
					$exp_fab = str_replace("'",'',$grey_construction);
					$grey_construction=explode("*",$exp_fab);

					$grey_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
					$data_array="(".$grey_construction_id.",'".$txtgreyconstruction."','".$grey_construction[0]."','".$grey_construction[1]."','".$grey_construction[2]."','".$grey_construction[4]."','".$grey_construction[3]."','".$grey_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rIDCon3=sql_insert("lib_fabric_construction",$field_array,$data_array,1);


					$wrap_details = explode(",",$grey_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."')";
						
						$fab_dts_id++;
					}
					$weft_details = explode(",",$fab_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."')";
						
						$fab_dts_id++;
					}
					$rIDCon4=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						//echo "10**".$rIDCon1;die;
					if($rIDConFlag2 == false || $rIDCon4 == true)
					{
						$rIDConFlag2 = false;
					}
				}
			}
			else
			{
				$fabric_construction_name=return_field_value("fabric_construction_name", "lib_fabric_construction","id='$grey_construction_id' and (epi is not null or ppi is not null or warp_count is not null or weft_count is not null)",  "fabric_construction_name");
				if($fabric_construction_name != $txtgreyconstruction)
				{
					$exp_fab = str_replace("'",'',$fab_construction);
					$fab_construction=explode("*",$exp_fab);

					$grey_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
					$data_array="(".$grey_construction_id.",'".$txtgreyconstruction."','".$grey_construction[0]."','".$grey_construction[1]."','".$grey_construction[2]."','".$grey_construction[4]."','".$grey_construction[3]."','".$grey_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rIDCon3=sql_insert("lib_fabric_construction",$field_array,$data_array,1);


					$wrap_details = explode(",",$grey_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."')";
						$rIDCon4=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						
						if($rIDConFlag2 != true || $rIDCon4 != true)
						{
							$rIDConFlag2 = false;
						}
						$fab_dts_id++;
					}
					$weft_details = explode(",",$grey_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."')";
						$rIDCon4=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						if($rIDConFlag2 != true || $rIDCon4 != true)
						{
							$rIDConFlag2 = false;
						}
						$fab_dts_id++;
					}
				}
			}
			$field_array1= "fab_nature_id*type*construction*fabric_construction_id*grey_construction*grey_construction_id*gsm_weight*weight_type*design*rd_no*color_range_id*full_width*cutable_width*grey_width*shrinkage_l*shrinkage_w*fabric_ref*updated_by*update_date*status_active*is_deleted";

			$data_array1="".$cbo_fabric_nature."*'".$txttype."'*'".$txtconstruction."'*'".$fab_construction_id."'*'".$txtgreyconstruction."'*".$grey_construction_id."*".$txtweight."*".$cboweighttype."*'".$txtdesign."'*'".$txtrdno."'*".$cbocolortype."*".$txt_full_width."*".$txt_cutable_width."*".$txt_grey_width."*'".str_replace("'", "", $txt_shrinkage_l)."'*'".str_replace("'", "", $txt_shrinkage_w)."'*'".str_replace("'", "", $txtfabricref)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";				
			$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
			
			$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,comp_type,inserted_by,insert_date,status_active,is_deleted";
			for ($i=1;$i<=$total_row;$i++)
			{
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbocountcotton="cbocountcotton_".$i;
				$cbotypecotton="cbotypecotton_".$i;
				$updateid="updateid_".$i;
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}
			for ($i=1;$i<=$total_row1;$i++)
			{
				$cbocompone="wecbocompone_".$i;
				$percentone="wepercentone_".$i;
				$cbocountcotton="wecbocountcotton_".$i;
				$cbotypecotton="wecbotypecotton_".$i;
				$updateid="weupdateid_".$i;
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}				
			$rID=sql_update("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",0);
			if($rID == 1)
			{
				$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
			}
			$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
			if($db_type==0)
			{
				if($rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDConFlag){
					mysql_query("COMMIT");  
					echo "1**".$rID ."&&". $rID_1 ."&&". $rIDCon;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**$rID && $rID_1 && $rIDCon && $rIDCon1 && $rIDConFlag";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				
				if($rID && $rID_1 && $rIDCon && $rIDCon1  && $rIDConFlag)
				{
					oci_commit($con);  
					echo "1**".$rID ."&&". $rID_1 ."&&". $rIDCon;
				}
				else{
					oci_rollback($con); 
					echo "10**$rID && $rID_1 && $rIDCon && $rIDCon1   && $rIDConFlag";
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
		$inqueryNo=return_field_value("a.system_number as sys_no", "wo_quotation_inquery a, wo_quotation_inquery_fab_dtls b", " a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=581 and constraction=".$update_mst_id."" ,"sys_no");
		
		if($inqueryNo!="")
		{
			echo "50**Some Entries Found For This Fabric Determination, Update/Delete Not Allowed, \n Buyer Inquiry Woven : ".$inqueryNo;
			disconnect($con);
			die;
		}
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",1);
		$field_array2="updated_by*update_date*status_active*is_deleted";
		$data_array2="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID1=sql_delete("lib_yarn_count_determina_dtls",$field_array2,$data_array2,"mst_id","".$update_mst_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID1 ){
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
		 if($rID && $rID1 )
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
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
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
if($action == "load_gsm_variable")
{
	$nameArray=sql_select( "select excut_source,id from  variable_order_tracking where  variable_list=88 ORDER BY CASE WHEN update_date IS NULL THEN insert_date ELSE update_date END DESC" );
	echo $nameArray[0][csf('excut_source')];
	exit();	
}
if($action=="fabric_construction_popup")
{
	echo load_html_head_contents("Material Construction Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		var spandexarr = "";
		<?
			$data_array= json_encode( $spandex_arr );
			echo "spandexarr = ". $data_array . ";\n";
		?>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function js_set_value(id,name,epi,ppi,warp_count,warp_spandex,weft_count,weft_spandex,str)
		{
			console.log(id +'='+ name);
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			document.getElementById('hidfabconspid').value=id;
			document.getElementById('hidfabconsname').value=name;
			document.getElementById('txt_epi').value=epi;
			document.getElementById('txt_ppi').value=ppi;
			document.getElementById('txt_wrap_spandex').value=warp_spandex;
			document.getElementById('txt_weft_spandex').value=weft_spandex;

			warp_count = warp_count.split(",");
			var j = 1;
			for( let i = 0; i < warp_count.length; i++)
			{
				var wrap = warp_count[i].split("_");
				$("#txtWarpCount_"+j).val(wrap[0]);
				$("#cboWarpType_"+j).val(wrap[1]);
				if(j < warp_count.length)
				{
					add_wrap(j);
				}
				j++;

			}

			weft_count = weft_count.split(",");
			var j = 1;
			for( let i = 0; i < weft_count.length; i++)
			{
				var wrap = weft_count[i].split("_");
				$("#txtWeftCount_"+j).val(wrap[0]);
				$("#cboWeftType_"+j).val(wrap[1]);
				if(j < weft_count.length)
				{
					add_weft(j);
				}
				j++;
			}

			
			
			//parent.emailwindow.hide();
		}
		function ClosePopup()
		{
			var txt_epi = document.getElementById('txt_epi').value ;
			var txt_ppi = document.getElementById('txt_ppi').value ;
			var calculated_gsm = 0;
			var row_num=$('#tbl_warp_list tbody tr').length;
			var wrap_str = "";
			var wrap_id_str = "";
			var s1 = "<sub>";
			var s2 = "</sub>";
			for(var i = 1; i <= row_num; i++)
			{
				var txtWarpCount = $("#txtWarpCount_"+i).val() * 1;
				var cboWarpType  = $("#cboWarpType_"+i).val() * 1;
				if( i > 1)
				{
					wrap_str += '+';
					wrap_id_str += ',';
				}
				wrap_str += txtWarpCount + 'x' + spandexarr[cboWarpType];
				wrap_id_str += txtWarpCount + "_"+cboWarpType;
				calculated_gsm += ( ( txt_epi * 1 ) / ( txtWarpCount * 1) ) * 23.25;
			}

			row_num=$('#tbl_weft_list tbody tr').length;
			var weft_str = "";
			var weft_id_str = "";

			for(var i = 1; i <= row_num; i++)
			{
				var txtWeftCount = $("#txtWeftCount_"+i).val() * 1;
				var cboWeftType  = $("#cboWeftType_"+i).val() * 1;
				if( i > 1)
				{
					weft_str += '+';
					weft_id_str += ',';
				}
				weft_str += txtWeftCount + 'x' + spandexarr[cboWeftType];
				weft_id_str += txtWarpCount + "_"+cboWeftType;
				calculated_gsm += ( ( txt_ppi * 1 ) / ( txtWeftCount * 1) ) * 23.25;
			}

			var txt_wrap_spandex = document.getElementById('txt_wrap_spandex').value;
			var wrap_spn = '';
			if(txt_wrap_spandex.length > 0 )
			{ 
				wrap_spn = "+" +txt_wrap_spandex + "D" ;
			}

			var txt_weft_spandex = document.getElementById('txt_weft_spandex').value;
			var weft_spn = '';
			if(txt_weft_spandex.length > 0 )
			{
				weft_spn = "+" +txt_weft_spandex + "D" ;
			}

			document.getElementById('hidfabconsname').value = txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn;
			console.log(txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn);
			document.getElementById('fab_construction').value = txt_epi + "*" + txt_ppi + "*" + wrap_id_str + "*" + txt_wrap_spandex + "*" + weft_id_str + "*" + txt_weft_spandex;
			document.getElementById('txt_calculated_gsm').value = calculated_gsm;
			parent.emailwindow.hide();
		}

		function add_wrap(i) 
		{
			var row_num=$('#tbl_warp_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_warp_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_warp_list tbody");
				$('#wrapInc_'+i).removeAttr("onClick").attr("onClick","add_wrap("+i+");");
				$('#wrapDecre_'+i).removeAttr("onClick").attr("onClick","delete_wrap("+i+");");
			}
		}

		function delete_wrap(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_warp_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_warp_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_warp_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}

		function add_weft(i)
		{

			var row_num=$('#tbl_weft_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_weft_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_weft_list tbody");
				$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
				$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
			}
		}

		function delete_weft(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_weft_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_weft_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_weft_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}
		


    </script>
	</head>
	<body>
		
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="850" class="rpt_table">
	        		<thead>
	        			<tr>
	        				<th>EPI</th>
	        				<th>PPI</th>
	        				<th>Warp Count</th>
	        				<th>Weft Count</th>
	        					<input type="hidden" name="hidfabconspid" id="hidfabconspid" value="" >
	                            <input type="hidden" name="hidfabconsname" id="hidfabconsname" value="" >
	                            <input type="hidden" name="fab_construction" id="fab_construction" value="" >
	                            <input type="hidden" name="txt_calculated_gsm" id="txt_calculated_gsm" value="" >
	        			</tr>
	        		</thead>
	        		<tbody>
	        			
	        			<tr>
	        				<td>
	        					<input type="text" name="txt_epi" class="text_boxes" id="txt_epi" value="" style="width:70px">
	        				</td>
	        				<td>
	        					<input type="text" name="txt_ppi" class="text_boxes" id="txt_ppi" value="" style="width:70px">
	        				</td>
	        				<td width="330">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="230" class="rpt_table" id="tbl_warp_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>
	        											
	        											<th>Action</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWarpCount_1" class="text_boxes" id="txtWarpCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												
	        												<? echo create_drop_down( "cboWarpType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											
	        											<td>
	        												<input type="button" name="wrapInc_1" id="wrapInc_1" class="formbutton" value="+" onclick="add_wrap(1)" style="width:30px;">
	        												<input type="button" name="wrapDecre_1" id="wrapDecre_1" class="formbutton" value="-" onclick="delete_wrap(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_wrap_spandex" id="txt_wrap_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
	        					
	        				</td>
	        				<td width="330">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="230" class="rpt_table" id="tbl_weft_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>
	        											
	        											<th>Action</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWeftCount_1" class="text_boxes" id="txtWeftCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												<? echo create_drop_down( "cboWeftType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											
	        											<td>
	        												<input type="button" name="weftInc_1" id="weftInc_1" class="formbutton" value="+" onclick="add_weft(1)" style="width:30px;">
	        												<input type="button" name="weftDecre_1" id="weftDecre_1" class="formbutton" value="-" onclick="delete_weft(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_weft_spandex" id="txt_weft_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
		        			</td>
		        			
	        			</tr>
	        			
	        		</tbody>
	        	</table>  
	        </form>
	    </fieldset>
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" >
        	<thead>
                <tr>
                	<th width="30">SL</th>
                	<th>Material Construction</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:240px;overflow-y: scroll;width: 850px;">
	        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="fab_cons_tbl">
	            <tbody >

	                <? 
	                
	                $fabric_construction = sql_select("select id, fabric_construction_name,epi,ppi,warp_count,weft_count,lakra,wrap_spandex,weft_spandex from  lib_fabric_construction where status_active=1 and is_deleted=0 order by fabric_construction_name");
	                $i=1; 
	                $epi = '';
	                $ppi = '';
	                $warp_count = '';
	                $weft_count = '';
	                $wrap_spandex = '';
	                $weft_spandex = '';
	                $fab_cons_name = '';
	                foreach($fabric_construction as $row) 
	                { 
	                	if($i%2==0) $bgcolor="#E9F3FF"; 
	                	else $bgcolor="#FFFFFF";
	                	$id= $row[csf('id')];
	                	
	           
	                	if($fab_construction_id == $id)
	                	{
	                		$bgcolor        ="yellow";
	                		$fab_cons_name  = $row[csf('fabric_construction_name')];
		                	$epi 			= $row[csf('epi')];
		                	$ppi 			= $row[csf('ppi')];
		                	$warp_count 	= $row[csf('warp_count')];
		                	$weft_count 	= $row[csf('weft_count')];
		                	$wrap_spandex 	= $row[csf('wrap_spandex')];
		                	$weft_spandex 	= $row[csf('weft_spandex')];
	                	} 
	                	$fab_cons 			= $row[csf('fabric_construction_name')];
	                	$repi 				= $row[csf('epi')];
	                	$rppi 				= $row[csf('ppi')];
	                	$rwarp_count 		= $row[csf('warp_count')];
	                	$rweft_count 		= $row[csf('weft_count')];
	                	$rwrap_spandex 		= $row[csf('wrap_spandex')];
	                	$rweft_spandex 		= $row[csf('weft_spandex')];
	                	?>
	                	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_cons; ?>','<? echo $repi; ?>','<? echo $rppi; ?>','<? echo $rwarp_count; ?>','<? echo $rwrap_spandex; ?>','<? echo $rweft_count; ?>','<? echo $rweft_spandex; ?>',<? echo $i;?>)">
	                        <td width="30"><? echo $i; ?></td>
	                        <td><? echo $fab_cons; ?> </td> 						
	                    </tr>
	                	<? 
	                	$i++; 
	            	} 
	            	?>
	            </tbody>
	    	</table>
	    </div>
    	<center><input type="button" value="Close" class="formbutton" onclick="ClosePopup()"></center>
    	
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	setFilterGrid('fab_cons_tbl',-1);
    	<?
    		if(!empty($fab_construction_id))
    		{
    			?>
    			js_set_value('<? echo $fab_construction_id; ?>','<? echo $fab_cons_name; ?>','<? echo $epi; ?>','<? echo $ppi; ?>','<? echo $warp_count; ?>','<? echo $wrap_spandex; ?>','<? echo $weft_count; ?>','<? echo $weft_spandex; ?>');
    			<?
    		}
    	?>
    	
    	
    </script>
	</html>
	<?
	exit();
}
?>