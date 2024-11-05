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

	$body_type_arr=return_library_array( "select id,body_part_full_name from lib_body_part where  status_active=1", "id", "body_part_full_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$group_short_name=$lib_group_short[1];
	$part_type_arr=array(1=>"Within Group",2=>"In-Bound");
	/*$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
	
	
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$row[csf('mst_id')].','.$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
			$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
			$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
		}
	}
	unset($data_array);*/
	
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
	$data_array=sql_select($sql);
	$sysCodeArr=array();
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			$sys_code=$group_short_name.'-'.$row[csf('id')];
			$sysCodeArr[$row[csf('id')]]=$sys_code;
		}
	}
	
	//print_r($sysCodeArr);				
	$sql="select id, company_id, fabric_description, fabric_source, body_part_id, body_part_type, color_type, party_type, party_name, uom,rate_bdt, rate_usd, aop_type, aop_process_upto, no_of_color, effective_date, coverage_range_from, coverage_range_to, count_range_from, count_range_to, color_range from  process_finish_fabric_rate_chat where is_deleted=0 order by id DESC";				
	$arr=array (0=>$company_arr,1=>$composition_arr,2=>$fabric_source,3=>$body_type_arr,4=>$body_part_type,5=>$color_type,9=>$no_color_arr,12=>$conversion_cost_head_array,13=>$aop_process_arr,14=>$part_type_arr,15=>$company_arr,18=>$unit_of_measurement);
	echo  create_list_view ( "list_view", "Company Name,Fabric Name,Fabric Source,Body part,Body Part Type,Color Type,Count Range From,Count Range To,Color Range,No. Of Color,Coverage % from,Coverage % to,AOP Type,AOP Process Upto,Party Type,Party Name,Rate BDT,Rate USD,UOM,Effect Date", "100,200,70,80,70,80,50,50,50,50,50,50,50,50,60,60,60,80,60,70","1470","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,0,0,0,no_of_color,0,0,aop_type,aop_process_upto,party_type,0,0,0,uom", $arr , "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,count_range_from,count_range_to,color_range,no_of_color,coverage_range_from,coverage_range_to,aop_type,aop_process_upto,party_type,party_name,rate_bdt,rate_usd,uom,effective_date", "requires/process_wise_finish_fabric_rate_chart_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,2,0,2,2,2,0,0,0,0,2,2,0,3') ;
			
	exit();
}
   
if ($action == "load_drop_down_body_type") {

	$nameArray = sql_select("select id, body_part_full_name, body_part_short_name,entry_page_id, body_part_type,status,is_emplishment from lib_body_part where is_deleted=0 and id='$data'");
	foreach ($nameArray as $inf) 
	{
		echo "document.getElementById('cbo_body_part_type_id').value  = '" . $inf[csf("body_part_type")] . "';\n";
	}
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company_id order by comp.company_name";die;
		echo create_drop_down("cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", 0, "", 0);
	} else if ($data[0] == 2) {

	$buyer_data=sql_select("select id, buyer_name,party_type from lib_buyer where status_active=1  order by id");
	foreach($buyer_data as $val){
		$party_id=explode(",",$val[csf("party_type")]);
		foreach($party_id as $row){
			if($row==2 || $row==3){
				$buyer_arr[$val[csf("id")]]=$val[csf("id")];
			}
		}
	}
	// echo count($buyer_arr);

	echo create_drop_down("cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy where status_active=1  ".where_con_using_array($buyer_arr,1,'buy.id')." order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, $blank_arr,"", 1, "-- Select --", '', "",$disabled,"" );
	}
	
	exit();
}
if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	if($data[4]==1)
	{
		$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	}
	else
	{
		$queryText = "select conversion_rate from currency_conversion_rate_buyer where effective_date<='" . $cdate . "' and currency=$cid and status_active=1 and is_deleted=0 and buyer_id='$data[2]' order by effective_date desc";
		//echo $queryText; die;
		$nameArray = sql_select($queryText);
		if (count($nameArray) > 0) {
			foreach ($nameArray as $result) {
				if ($result[csf('conversion_rate')] != "") {
					$currency_rate=$result[csf("conversion_rate")];
				} else {
					$currency_rate="0";
				}
			}

		} else {
			$currency_rate="0";
		}
	}
	$usd_rate=fn_number_format($data[3]/$currency_rate,4);
	echo "1"."_".$usd_rate;
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select id, company_id, fabric_description, fabric_source, body_part_id, body_part_type, color_type, party_type, party_name, uom, rate_bdt, rate_usd, aop_type, aop_process_upto, no_of_color, effective_date, coverage_range_from, coverage_range_to, count_range_from, count_range_to,color_range from  process_finish_fabric_rate_chat  where id='$data'");

	/*$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
	$data_array=sql_select($sql);
	$sysCodeArr=array();
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
			$sys_code=$group_short_name.'-'.$row[csf('id')];
			$sysCodeArr[$row[csf('id')]]=$sys_code;
		}
	}*/

	foreach ($nameArray as $inf)
	{
		$fab_id=$inf[csf("fabric_description")];
		$fab_desc_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.id, a.fabric_composition_id from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0 and a.id='$fab_id'");
		$fab_desc=$fab_desc_data[0][csf("construction")].','.$composition_arr[$fab_desc_data[0][csf("id")]];
		
		echo "load_drop_down( 'requires/process_wise_finish_fabric_rate_chart_controller', '".$inf[csf("party_type")]."_".$inf[csf("company_id")]."', 'load_drop_down_party', 'party_id' );\n";
		echo "document.getElementById('cbo_company_name').value  	= '".($inf[csf("company_id")])."';\n";
		echo "document.getElementById('txtcompone').value  	= '".$fab_desc."';\n";
		echo "document.getElementById('cbocompone').value  	= '".$fab_id."';\n";
		echo "document.getElementById('cbo_fabric_source').value  	= '".($inf[csf("fabric_source")])."';\n";
		echo "document.getElementById('cbo_body_part_id').value  	= '".($inf[csf("body_part_id")])."';\n";
		echo "document.getElementById('cbo_body_part_type_id').value= '".($inf[csf("body_part_type")])."';\n";
		echo "document.getElementById('cbo_party_type').value  		= '".($inf[csf("party_type")])."';\n";
		echo "document.getElementById('cbo_color_type').value  		= '".($inf[csf("color_type")])."';\n";
		echo "document.getElementById('cbo_party_name').value  		= '".($inf[csf("party_name")])."';\n";
		echo "document.getElementById('cbo_color_range').value  	= '".($inf[csf("color_range")])."';\n";
		echo "document.getElementById('cbo_aop_type').value  		= '".($inf[csf("aop_type")])."';\n";
		echo "document.getElementById('cbo_aop_process_upto').value = '".($inf[csf("aop_process_upto")])."';\n";

		echo "document.getElementById('cbo_no_color').value  	= '".($inf[csf("no_of_color")])."';\n";
		echo "document.getElementById('txt_effective_date').value  	= '".change_date_format($inf[csf("effective_date")],'dd-mm-yyyy','-')."';\n";
		
		echo "document.getElementById('txt_coverage_from').value 	= '".($inf[csf("coverage_range_from")])."';\n";			
		echo "document.getElementById('txt_coverage_to').value  	= '".($inf[csf("coverage_range_to")])."';\n";			
		echo "document.getElementById('txt_count_range_from').value = '".($inf[csf("count_range_from")])."';\n";			
		echo "document.getElementById('txt_count_range_to').value  	= '".($inf[csf("count_range_to")])."';\n";
		echo "document.getElementById('txt_rate_bdt').value  		= '".($inf[csf("rate_bdt")])."';\n";	
		echo "document.getElementById('txt_rate_usd').value  		= '".($inf[csf("rate_usd")])."';\n";		
		echo "document.getElementById('cbo_uom').value  		= '".($inf[csf("uom")])."';\n";	
		echo "document.getElementById('update_id').value  			= '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_count_determination',1);\n";  

	}
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller_v2');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('process_loss').value=trim(data[4]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			var yarn =fabric_yarn_description_arr[1].split("_");
			if(yarn[1]*1==0 || yarn[1]==""){
				alert("Composition not set in yarn count determination");
				return;
			}
			document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
			</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'pre_cost_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
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
		$color_range=str_replace("'","",$cbo_color_range);
		$uom=str_replace("'","",$cbo_uom);
		$no_color=str_replace("'","",$cbo_no_color);
		$aop_process_upto=str_replace("'","",$cbo_aop_process_upto);
		
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=426 and is_deleted=0"; die;
						
		$id=return_next_id( "id", "process_finish_fabric_rate_chat", 1);
		$field_array= "id, company_id, fabric_description, fabric_source, body_part_id, body_part_type, color_type, party_type, party_name, uom, rate_bdt, rate_usd, aop_type, aop_process_upto, no_of_color, effective_date, coverage_range_from, coverage_range_to, count_range_from, count_range_to, color_range, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbocompone.",".$cbo_fabric_source.",".$cbo_body_part_id.",".$cbo_body_part_type_id.",".$cbo_color_type.",".$cbo_party_type.",".$cbo_party_name.",".$uom.",".$txt_rate_bdt.",".$txt_rate_usd.",".$cbo_aop_type.",".$aop_process_upto.",".$no_color.",".$txt_effective_date.",".$txt_coverage_from.",".$txt_coverage_to.",".$txt_count_range_from.",".$txt_count_range_to.",'".$color_range."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
		//   echo "10**INSERT INTO process_finish_fabric_rate_chat(".$field_array.") VALUES ".$data_array;die;
		
		$rID=sql_insert("process_finish_fabric_rate_chat",$field_array,$data_array,0);
		
		//echo "10**".$rID.'=='.$rID_1;die;
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$rID;
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
		$color_range=str_replace("'","",$cbo_color_range);
		$uom=str_replace("'","",$cbo_uom);
		$no_color=str_replace("'","",$cbo_no_color);
		$aop_process_upto=str_replace("'","",$cbo_aop_process_upto);
		$update_id=str_replace("'","",$update_id);
		
		//echo "10**SELECT id from lib_yarn_count_determina_mst where rd_no=$txtrdno and entry_form=426 and is_deleted=0 and 
		
		$field_array= "fabric_description*fabric_source*body_part_id*body_part_type*color_type*party_type*party_name*uom*rate_bdt*rate_usd*aop_type*aop_process_upto*no_of_color*effective_date*coverage_range_from*coverage_range_to*count_range_from*count_range_to*color_range*updated_by*update_date";

		$data_array="".$cbocompone."*".$cbo_fabric_source."*".$cbo_body_part_id."*".$cbo_body_part_type_id."*".$cbo_color_type."*".$cbo_party_type."*".$cbo_party_name."*".$uom."*".$txt_rate_bdt."*".$txt_rate_usd."*".$cbo_aop_type."*".$aop_process_upto."*".$no_color."*".$txt_effective_date."*".$txt_coverage_from."*".$txt_coverage_to."*".$txt_count_range_from."*".$txt_count_range_to."*'".$color_range."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("process_finish_fabric_rate_chat",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID ){
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
		
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("process_finish_fabric_rate_chat",$field_array1,$data_array1,"id","".$update_id."",1);
	
		
		if($db_type==0)
		{
			if($rID ){
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
		<?

		$composition_arr=array();
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
		$group_short_name=$lib_group_short[1];
		$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);


		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
		$data_array=sql_select($sql);
		$sysCodeArr=array();
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
				$sys_code=$group_short_name.'-'.$row[csf('id')];
				$sysCodeArr[$row[csf('id')]]=$sys_code;
			}
		}
		?>
		<fieldset style="width:600px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
	                <thead>
					<th width="50">SL No</th>
					<th width="100">Fab Nature</th>
					<th width="100">Construction</th>
					<th width="170">Composition</th>
					<th width="80">Fabric Composition</th>
					<th width="100">GSM/Weight</th>
					
					<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
					<input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                    </th>
	                </thead>
                </table>
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table" id="comp_tbl">
                    <tbody>
                    <? 
					$sql_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.id, a.fabric_composition_id from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0 
					 group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.fabric_composition_id 
					 order by a.id");

                    $i=1; 
					foreach($sql_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('construction')].",".$composition_arr[$row[csf('id')]]; ?>')">
							<td width="50"><? echo $i; ?></td>
							<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
							<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
							<td width="170"><? echo $composition_arr[$row[csf('id')]]; ?></td>
							<td width="80" align="right"><? echo $fabric_composition[$row[csf('fabric_composition_id')]]; ?></td>
							<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
						</tr>
						<?
						$i++;
					}?>
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

if ($action=="process_wise_rate_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	var permission='<? echo $permission; ?>';
	function fnc_process_rate_entry(operation)
	{
		var tot_row=$('#tbl_process_rate_details tr').length-2;
		var mst_id=document.getElementById('mst_id').value;
		var tot_rate=document.getElementById('tot_rate').value;
		
		var data_all=''; var z=1;
		for(i=1; i<=tot_row; i++)
		{
			if( ($('#txtrate_'+i).val()*1)>0)
			{
				data_all+="&processid_" + z + "='" + $('#processid_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'";
				z++;
			}
		}
		if(data_all=='')
		{
			alert("No Data Select");	
			return;
		}
		
		var data="action=save_update_delete_process_rate&operation="+operation+'&total_row='+z+get_submitted_data_string('mst_id*tot_rate',"../../../")+data_all;
		
		//alert(data);
		freeze_window(operation);
		http.open("POST","process_wise_finish_fabric_rate_chart_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_process_rate_entry_response;
	}
		
	function fnc_process_rate_entry_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			$("#mst_id").val(reponse[1]);
			set_button_status(1, permission, 'fnc_process_rate_entry',1);
			release_freezing();	
			fn_close();
		}
	}
		
	function fn_sum()
	{
		var tot_rate=totRate=0;
		var tot_row=$('#tbl_process_rate_details tr').length-2;
		//alert(tot_row)
		for(i=1; i<=tot_row; i++){
			var tot_rate=$("#txtrate_"+i).val()*1;
			totRate+=tot_rate;
		}
		$("#tot_rate").val(totRate);
	}
	
	function fn_close(str)
	{
		parent.emailwindow.hide(); 
	}
		
	</script>
    </head>
    <body>
    
	 <?
     $sql_up_data=sql_select("select id, mst_id, rate, process_id from lib_process_wise_rate where mst_id=$mst_id and status_active=1 and is_deleted=0");
	 //echo "select id, mst_id, rate, process_id from lib_process_wise_rate where mst_id=$mst_id and status_active=1 and is_deleted=0";
	 $processRateArr=array();
     foreach($sql_up_data as $row)
	 {
		 $processRateArr[$row[csf("process_id")]]=$row[csf("rate")];
	 }
	 //unset($sql_up_data);
     //var_dump($sql_up_data);
     ?>   
    
    <form name="rate_1" id="rate_1">
		<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="200">Process</th>
                    <th>Rate &#2547;</th>
                </tr>
            </thead>
    	</table>
        <table width="400" cellspacing="0" class="rpt_table" border="0" id="tbl_process_rate_details" rules="all">
        <?
        $i=1;
        foreach($conversion_cost_head_array as $process_id=>$process_val)
        {
            ?>
            <tr>
                <td width="30" align="center"><?=$i;?></td>
                <td width="200" style="word-break:break-all"><?=$process_val; ?><input type="hidden" id="processid_<?=$i; ?>" name="processid_<?=$i; ?>" value="<?=$process_id; ?>"/></td>
                <td align="center"><input type="text" id="txtrate_<?=$i; ?>" style="width:80px" name="txtrate_<?=$i; ?>" class="text_boxes_numeric" value="<?= $processRateArr[$process_id]; ?>" onBlur="fn_sum();" /></td>
            </tr>
            <?
            $i++;
        }
        ?>
        <tr style="background:#CCC">
        	<td align="right" colspan="2"><b>Total:</b></td>
            <td align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="tot_rate" name="tot_rate" disabled /></td>
        </tr>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0"rules="all">
            <tr>
                <td colspan="3" align="center">
                <input type="hidden" id="mst_id" name="mst_id" value="<?=$mst_id; ?>" />
                <?
                if(count($sql_up_data)==0)
                {
                    echo load_submit_buttons($permission, "fnc_process_rate_entry", 0,0,"reset_form('rate_1','','','','','');",1);
                }
                else
                {
                    echo load_submit_buttons($permission, "fnc_process_rate_entry", 1,0,"reset_form('rate_1','','','','','');",1);
                }
                ?>
                </td>
            </tr>
        </table>
    </form>
    </body>  
	<script>
		var tableFilters = 	{					
							col_0: "none",
							col_2: "none",
						};
		setFilterGrid("tbl_process_rate_details",tableFilters,-1)
		fn_sum();
    </script>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_process_rate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sqlDate=sql_select("select id, effective_date, company_id from process_finish_fabric_rate_chat where id=$mst_id");
		
	$effDate=$sqlDate[0][csf("effective_date")];
	$company_id=$sqlDate[0][csf("company_id")];
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "lib_process_wise_rate", 1 ) ;
		$field_array= "id,mst_id,process_id,rate,insert_by,insert_date,status_active,is_deleted";
		$k=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$txtrate="txtrate_".$i;
			if( (str_replace("'","",$$txtrate)*1)>0)
			{
				if ($k!=1) $data_array .=",";
				$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$txtrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id++; $k++;
			}
		}
		$tot_rate=str_replace("'","",$tot_rate);
		$conversion_date=change_date_format($effDate, "d-M-y", "-",1);
	
		$currency_rate=set_conversion_rate( 2, $conversion_date, $company_id );
		$usd_rate=fn_number_format($tot_rate/$currency_rate,4);
		
		
		$field_array_mst= "rate_bdt*rate_usd*updated_by*update_date";
		$data_array_mst ="'".$tot_rate."'*'".$usd_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID_mst=sql_update("process_finish_fabric_rate_chat",$field_array_mst,$data_array_mst,"id","".$mst_id."",0);
		$rID=sql_insert("lib_process_wise_rate",$field_array,$data_array,1);
		//echo "10*".$rID_mst.'='.$rID;die;
		if($db_type==0)
		{
			if($rID && $rID_mst){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_mst)
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
		//echo "DELETE  from conversion_process_loss WHERE mst_id=".$mst_id."";die;
		$rID=execute_query("DELETE  from lib_process_wise_rate WHERE mst_id=".$mst_id."");

		$id=return_next_id( "id", "lib_process_wise_rate", 1 ) ;
		$field_array= "id,mst_id,process_id,rate,insert_by,insert_date,status_active,is_deleted";
		$k=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$txtrate="txtrate_".$i;
			if( (str_replace("'","",$$txtrate)*1)>0)
			{
				if ($k!=1) $data_array .=",";
				$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$txtrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id++; $k++;
			}
		}
		$tot_rate=str_replace("'","",$tot_rate);
		$conversion_date=change_date_format($effDate, "d-M-y", "-",1);
	
		$currency_rate=set_conversion_rate( 2, $conversion_date, $company_id );
		$usd_rate=fn_number_format($tot_rate/$currency_rate,4);
		
		
		//if($tot_process_loss) $tot_process_loss=$tot_process_loss;else $tot_process_loss=0;
		
		$field_array_mst= "rate_bdt*rate_usd*updated_by*update_date";
		$data_array_mst ="'".$tot_rate."'*'".$usd_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID_mst=sql_update("process_finish_fabric_rate_chat",$field_array_mst,$data_array_mst,"id","".$mst_id."",0);
		$rID=sql_insert("lib_process_wise_rate",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID && $rID_mst){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_mst)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
		else{
				oci_rollback($con); 
				echo "10**".$mst_id;
			}
		}
		disconnect($con);
		die;
	}
}
?>