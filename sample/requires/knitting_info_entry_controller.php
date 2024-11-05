<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
 $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");

 if($action=="knitting_info_from_sample_populate")
{  
 	//  $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $dataArr=explode("_",$data);
	$booking_id=$dataArr[0];
	$booking_no=$dataArr[1];
	
	
	$res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
	 
	 
  	foreach($res_mst as $result)
	{ 
 		$booking_id=$result[csf('id')];
		$dtls_idArr[$result[csf('dtls_id')]]=$result[csf('dtls_id')];
		$req_id=$result[csf('req_id')];
		$booking_no=$result[csf('booking_no')];
		$company_id=$result[csf('company_id')];
         $style_ref_no=$result[csf('style_ref_no')];
         $buyer_name=$buyer_arr[$result[csf('buyer_name')]];
         $fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
         if($result[csf('sample_type')])
         {
            $sample_typeArr[$result[csf('sample_type')]]=$sample_type[$result[csf('sample_type')]];
         }
         $fabricDescArr[$result[csf('deter_id')]]=$result[csf('fabric_description')];
         if($result[csf('color_type_id')])
         {
          $colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
		   $colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
         }
       
         
    }
	  $sql_plan="select b.id as prog_no from ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where b.id=c.dtls_id and c.booking_no='$booking_no' and b.status_active=1  and c.status_active=1";
	$sql_plan=sql_select($sql_plan);
	foreach($sql_plan as $row)
	{ 
	 $prog_noArr[$row[csf('prog_no')]]=$row[csf('prog_no')];
	}
	 
	 
	
	
	 $fab_color_drop=create_drop_down( "cbo_knit_fab_color_code", 150, $fabric_colorArr,"", 1, "-- select --" );
     $prog_drop=create_drop_down( "cbo_prog_no", 400, $prog_noArr,"", 1, "-- select --","","fnc_knit_construction_load(this.value)" );
   //load_drop_down( 'yarn_allocation_controller', this.value, 'load_drop_down_yarn_composition', 'yarn_composition_td1' )
      
        echo "document.getElementById('knit_color_td').innerHTML = '".$fab_color_drop."';\n";
        echo "document.getElementById('prog_no_td').innerHTML = '".$prog_drop."';\n";
	 // echo "$('#txt_yarn_color_type').val('".implode(",",$colorTypeArr)."');\n";
		
	
 	exit();	
}	
if ($action=="load_drop_down_knit_construction")
{
	$data=explode("_",$data);

	$booking_no=$data[0];
	$color=$data[1];
	$prog_no=$data[2];
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0", 'id', 'machine_no');
	
	$sql_cons=sql_select("select b.id ,b.construction,c.machine_dia,c.machine_gg,c.width_dia_type,c.machine_id from lib_yarn_count_determina_mst b,ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls c where b.id=a.determination_id   and a.booking_no='$booking_no'  and a.dtls_id=c.id  and a.dtls_id='$prog_no' and b.status_active=1  and a.status_active=1  and c.status_active=1");
	
	 	$machine_noArr=array();
	 	foreach ($sql_cons as $row)
		{
			if($row[csf('machine_id')])
			{
				$machine_idArr=array_unique(explode(",",$row[csf('machine_id')]));
				foreach ($machine_idArr as $mid)
				{
						$machine_noArr[$mid]=$machine_arr[$mid];
				}
			}
			
			if($row[csf('construction')])
			{
			$knit_constructionArr[$row[csf('id')]]=$row[csf('construction')];
			}
			if($row[csf('machine_dia')])
			{
			$knit_diaArr[$row[csf('machine_dia')]]=$row[csf('machine_dia')];
			}
			if($row[csf('machine_gg')])
			{
			$knit_machine_ggArr[$row[csf('machine_gg')]]=$row[csf('machine_gg')];
			}
			if($row[csf('width_dia_type')])
			{
			$knit_dia_typeArr[$fabric_typee[$row[csf('width_dia_type')]]]=$fabric_typee[$row[csf('width_dia_type')]];
			}
			 
		}
		
		$cbo_knit_construction=create_drop_down( "cbo_construction", 200, $knit_constructionArr,"", 1, "-- select --","","" );
		echo "document.getElementById('construction_td').innerHTML = '".$cbo_knit_construction."';\n";
		
		 echo "$('#txt_dia_type').val('".implode(", ",array_unique($knit_dia_typeArr))."');\n";
		 echo "$('#txt_brand_dia_type').val('".implode(", ",array_unique($dia_typeArr))."');\n";
		 echo "$('#txt_mc_no').val('".implode(", ",array_unique($machine_noArr))."');\n";
		 echo "$('#txt_mc_gauge').val('".implode(", ",array_unique($knit_machine_ggArr))."');\n";
		 echo "$('#txt_brand_dia_type').val('".implode(", ",array_unique($knit_dia_typeArr))."');\n";
		 
	 
	exit();
}

 

if ($action=="save_update_delete_knit")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "10**=A";die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
 		$id_mst=return_next_id( "id", "sample_archive_knitting_info", 1 ) ;
 		
 

 		$field_array="id, req_id,company_id,booking_no,booking_id,fabric_color_id,deter_id,  constuction,program_no,cons_dia,greige_dia,mc_no,mc_dia,mc_dia_type,mc_req_gsm,mc_gauge,mc_type,lycra_feeding,greige_gsm,mc_brand,brand_dia_type,remarks,cotton,polyester,modal,viscose,nylon,elastane,others,knit,binding,loop,yarn_dyed,no_of_color,repeat_size,no_of_feeder,inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_knit_fab_color_code.",".$cbo_construction.",'".$construction_desc."',".$cbo_prog_no.",".$txt_dia.",".$txt_greige_dia.",".$txt_mc_no.",".$txt_mc_dia.",".$txt_dia_type.",".$txt_required_gsm.",".$txt_mc_gauge.",".$txt_mc_type.",".$txt_lycra_feeding.",".$txt_greige_gsm.",".$txt_mc_brand.",".$txt_brand_dia_type.",".$txt_remarks.",".$txt_cotton.",".$txt_polyester.",".$txt_modal.",".$txt_viscose.",".$txt_nylon.",".$txt_elastane.",".$txt_others.",".$txt_knit.",".$txt_binding.",".$txt_loop.",".$txt_yarn_dyed.",".$txt_no_of_color.",".$txt_repeat.",".$txt_no_of_feeder.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_archive_knitting_info",$field_array,$data_array,1);
		  // echo "10**=A=insert into sample_archive_knitting_info ($field_array) values $data_array";die;
		 //echo $rID." data array ".$data_array; die;
		 if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0"."**".$id_mst."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10".$id_mst;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
  		$field_array_up="fabric_color_id*deter_id*constuction*program_no*cons_dia*greige_dia*mc_no*mc_dia*mc_dia_type*mc_req_gsm*mc_gauge*mc_type*lycra_feeding*greige_gsm*mc_brand*brand_dia_type*remarks*cotton*polyester*modal*viscose*nylon*elastane*others*knit*binding*loop*yarn_dyed*no_of_color*repeat_size*no_of_feeder*updated_by*update_date";
		//$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_knit_fab_color_code.",".$cbo_construction.",'".$construction_desc."',".$cbo_prog_no.",".$txt_dia.",".$txt_mc_no.",".$txt_mc_dia.",".$txt_dia_type.",".$txt_required_gsm.",".$txt_mc_gauge.",".$txt_mc_type.",".$txt_lycra_feeding.",".$txt_greige_gsm.",".$txt_mc_brand.",".$txt_brand_dia_type.",".$txt_remarks.",".$txt_cotton.",".$txt_polyester.",".$txt_modal.",".$txt_viscose.",".$txt_nylon.",".$txt_elastane.",".$txt_others.",".$txt_knit.",".$txt_binding.",".$txt_loop.",".$txt_yarn_dyed.",".$txt_no_of_color.",".$txt_repeat.",".$txt_no_of_feeder.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//,".$txt_dia.",".$txt_greige_dia."
		$data_array_up="".$cbo_knit_fab_color_code."*".$cbo_construction."*'".$construction_desc."'*".$cbo_prog_no."*".$txt_greige_dia."*".$cbo_prog_no."*".$txt_mc_no."*".$txt_mc_dia."*".$txt_dia_type."*".$txt_required_gsm."*".$txt_mc_gauge."*".$txt_mc_type."*".$txt_lycra_feeding."*".$txt_greige_gsm."*".$txt_mc_brand."*".$txt_brand_dia_type."*".$txt_remarks."*".$txt_cotton."*".$txt_polyester."*".$txt_modal."*".$txt_viscose."*".$txt_nylon."*".$txt_elastane."*".$txt_others."*".$txt_knit."*".$txt_binding."*".$txt_loop."*".$txt_yarn_dyed."*".$txt_no_of_color."*".$txt_repeat."*".$txt_no_of_feeder."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_knitting_info",$field_array_up,$data_array_up,"id","".$knitting_update_id."",1);
		 //echo "10**=".$rID.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$knitting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$knitting_update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_archive_knitting_info",$field_array,$data_array,"id","".$knitting_update_id."",0);
		 
	   if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$knitting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); die;
	}
}
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	echo $strQuery;die;
	if($return_query==1){return $strQuery ;}

		//return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
if ($action=="listview_knit_info")
{
	$booking_id=$data;
	
	$sql_result =sql_select("SELECT id,req_id,company_id,booking_no,booking_id,fabric_color_id,deter_id,constuction,program_no,mc_no,mc_dia,mc_dia_type,mc_req_gsm,mc_gauge,mc_type,lycra_feeding,greige_gsm,mc_brand,brand_dia_type,remarks,cotton,polyester,modal,viscose,nylon,elastane,others,knit,binding,loop,yarn_dyed,no_of_color,repeat_size,no_of_feeder from sample_archive_knitting_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc");

	//echo "SELECT id,req_id,company_id,booking_no,booking_id,fabric_color_id,deter_id,constuction,program_no,mc_no,mc_dia,mc_dia_type,mc_req_gsm,mc_gauge,mc_type,lycra_feeding,greige_gsm,mc_brand,brand_dia_type,remarks,cotton,polyester,modal,viscose,nylon,elastane,others,knit,binding,loop,yarn_dyed,no_of_color,repeat_size,no_of_feeder from sample_archive_knitting_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc";
				
	?>
    <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
            <th width="100">Fab. Color/Code</th>
            <th width="100">Prog No</th>
            <th width="100">Construction</th>
            <th width="100">Composition</th>
            <th width="100">Req. GSM</th>
            <th width="">Req. Dia</th>
            
          
        </thead>
    </table>
    <div style="width:620px; overflow-y:scroll; max-height:180px;">
        <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_knit">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				   //cotton,polyester,modal,viscose,nylon,elastane,others
				   if($row[csf('cotton')]) $cotton_str=$row[csf('cotton')];else $cotton_str='';
				   if($row[csf('polyester')]) $poly_str=','.$row[csf('polyester')];else $poly_str='';
				   if($row[csf('modal')]) $modal_str=','.$row[csf('modal')];else $modal_str='';
				   if($row[csf('viscose')]) $viscose_str=','.$row[csf('viscose')];else $viscose_str='';
					if($row[csf('nylon')]) $nylon_str=','.$row[csf('nylon')];else $nylon_str='';
					if($row[csf('elastane')]) $elastane_str=','.$row[csf('elastane')];else $elastane_str='';
					if($row[csf('others')]) $others_str=','.$row[csf('others')];else $others_str='';
					
				  $compositionDes=$cotton_str.$poly_str.$poly_str.$modal_str.$viscose_str.$nylon_str.$elastane_str.$others_str;
				//  echo $compositionDes.'';
				   $compo_str_all=rtrim($compositionDes,',');
				   // $compo_str_all=array_unique(explode(",", $compo_str_all));
					
					
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_knitting_form_data', 'requires/knitting_info_entry_controller');"> 
						
                		<td width="100"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                         <td width="100" style="word-break:break-all"><p><? echo $row[csf('program_no')]; ?></p></td>
                         <td width="100"  style="word-break:break-all"><? echo $row[csf('constuction')]; ?>&nbsp;</td>
                        <td width="100"><p><? echo $compo_str_all; ?></p></td>
                         <td width="100"><p><? echo $row[csf('mc_req_gsm')]; ?></p></td>
                        <td align="center" title="Greige Gsm" width=""><p><? echo $row[csf('greige_gsm')]; ?></p></td>
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
if($action=="populate_details_knitting_form_data")
{  
 	
	$sql_result =sql_select("SELECT  id, req_id, company_id, booking_no, booking_id, fabric_color_id, deter_id,cons_dia,constuction,program_no,mc_no,mc_dia,mc_dia_type,mc_req_gsm,mc_gauge,mc_type,lycra_feeding,greige_gsm,mc_brand,brand_dia_type,remarks,cotton,polyester,modal,viscose,nylon,elastane,others,knit,binding,loop,yarn_dyed,no_of_color,repeat_size,no_of_feeder from sample_archive_knitting_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");
	 
	
	
	foreach($sql_result as $row)
	{ 
 		//echo "load_drop_down( 'requires/sample_checklist_controller', '".$result[csf('id')]."', 'load_drop_down_gmts', 'gmts_td' );\n";
 		$program_no=$row[csf('program_no')];
		echo "$('#knitting_update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_knit_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		echo "$('#cbo_prog_no').val('".$row[csf('program_no')]."');\n";
		
		echo "$('#txt_dia').val('".$row[csf('cons_dia')]."');\n";
		$deter_id=$row[csf('deter_id')]; 
		
   	}
	
	 
	echo "fnc_knit_construction_load(".$program_no.");\n";
	echo "$('#cbo_construction').val('".$row[csf('deter_id')]."');\n";
	
	echo "$('#txt_mc_no').val('".$row[csf('mc_no')]."');\n";
	echo "$('#txt_mc_dia').val('".$row[csf('mc_dia')]."');\n";
	echo "$('#txt_dia_type').val('".$row[csf('mc_dia_type')]."');\n";
	echo "$('#txt_greige_dia').val('".$row[csf('greige_gsm')]."');\n";
	
	 
	
	echo "$('#txt_mc_gauge').val('".$row[csf('mc_gauge')]."');\n";
	echo "$('#txt_mc_type').val('".$row[csf('mc_type')]."');\n";
	echo "$('#txt_dia_type').val('".$row[csf('mc_dia_type')]."');\n";
	echo "$('#txt_required_gsm').val('".$row[csf('mc_req_gsm')]."');\n";
	echo "$('#txt_lycra_feeding').val('".$row[csf('lycra_feeding')]."');\n";
	
	echo "$('#txt_greige_gsm').val('".$row[csf('greige_gsm')]."');\n";
	echo "$('#txt_mc_brand').val('".$row[csf('mc_brand')]."');\n";
	
	echo "$('#txt_brand_dia_type').val('".$row[csf('brand_dia_type')]."');\n";
	echo "$('#txt_remarks').val('".$row[csf('remarks')]."');\n";
	//cotton,polyester,modal,viscose,nylon,elastane,others,knit,binding,loop,yarn_dyed,no_of_color,repeat_size,no_of_feeder
	
	echo "$('#txt_cotton').val('".$row[csf('cotton')]."');\n";
	echo "$('#txt_polyester').val('".$row[csf('polyester')]."');\n";
	echo "$('#txt_modal').val('".$row[csf('modal')]."');\n";
	echo "$('#txt_viscose').val('".$row[csf('viscose')]."');\n";
	echo "$('#txt_nylon').val('".$row[csf('nylon')]."');\n";
	echo "$('#txt_elastane').val('".$row[csf('elastane')]."');\n";
	echo "$('#txt_others').val('".$row[csf('others')]."');\n";
	
	echo "$('#txt_knit').val('".$row[csf('knit')]."');\n";
	echo "$('#txt_binding').val('".$row[csf('binding')]."');\n";
	echo "$('#txt_loop').val('".$row[csf('loop')]."');\n";
	echo "$('#txt_yarn_dyed').val('".$row[csf('yarn_dyed')]."');\n";
	echo "$('#txt_no_of_color').val('".$row[csf('no_of_color')]."');\n";
	echo "$('#txt_repeat').val('".$row[csf('repeat_size')]."');\n";
	echo "$('#txt_no_of_feeder').val('".$row[csf('no_of_feeder')]."');\n";
	
	if(count($sql_result)>0)
	{
		
	 echo "$('#save3').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	
	 echo "$('#save3').removeAttr('onclick').attr('onclick','fnc_kntting_entry(0);')\n";
	 echo "$('#update3').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
     echo "$('#update3').removeAttr('onclick').attr('onclick','fnc_kntting_entry(1);')\n";
     echo "$('#Delete3').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
     echo "$('#Delete3').removeAttr('onclick').attr('onclick','fnc_kntting_entry(2);')\n"; 
		//echo "fnc_yarn_button_status(2);\n"; 
		
	}
	else
	{
		//echo "fnc_yarn_button_status(1);\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_kntting_entry',3);\n"; 
	}
	 	 
	
		
   	unlink($sql_result);
 	exit();	
}	



?>