<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$dealing_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0",'id','team_member_name');
$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/sample_requisition_approval_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );  
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
 	echo create_drop_down( "cbo_season_name", 70, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
}

if($action=='user_popup'){
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
?>	

<script>

// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
  { 
 // alert(id)
	document.getElementById('selected_id').value=id;
	  parent.emailwindow.hide();
  }

// avobe script for multy select data------------------------------------------------------------------------------end;

</script>

<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
			// echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
 	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_st_date=str_replace("'","",$txt_st_date);
	$txt_end_date=str_replace("'","",$txt_end_date);
 	$approval_type=str_replace("'","",$cbo_approval_type);
	//echo "000dsdreud   ".$company_name."  ".$txt_season."   ".$txt_style_ref."   ".$txt_st_date."   ".$txt_end_date."   ".$approval_type;die;
 	if(str_replace("'","",$cbo_buyer_name)==0) 
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$cbo_buyer_name"; 
	}
	$season_cond="";
	if($cbo_season_name>0)
	{
		$season_cond=" and season='$cbo_season_name' ";
	}
 		if (trim($txt_style_ref)!="") $style_id_cond=" and style_ref_no like '%$txt_style_ref%' "; else $style_id_cond="";
 		$date_cond="";
		if($db_type==2)
		{
			if (trim($txt_st_date)!="" && trim($txt_end_date!="")) $date_cond=" and requisition_date between '$txt_st_date' and '$txt_end_date' "; else $date_cond="";
		}

		 

 	$sql_req="select id,company_id,requisition_date,requisition_number_prefix_num,style_ref_no,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,status_active,is_deleted from sample_development_mst where entry_form_id=117 and company_id=$company_name $buyer_id_cond $season_cond $style_id_cond $date_cond and is_approved=$approval_type and req_ready_to_approved=1 and status_active=1 and is_deleted=0 order by id";
	//echo $sql_req;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:950px; margin-top:10px">
        <legend>Sample Requisition Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="50">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="70">Date</th>
                    <th width="120">Company</th>
                    <th width="60">Dealing Mer.</th>
                    <th width="90">Buyer</th>
                    <th width="120">Season</th>
                    <th width="70">Style Ref</th>
                    <th width="70">Sample Qty</th>
                    <th width="50">Fabric Qty</th>
                    <th>Embellishment</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
 							$nameArray=sql_select($sql_req);
 							 $sampQty=sql_select("select sample_mst_id,sum(sample_prod_qty) as sd from sample_development_dtls where  status_active=1 and is_deleted=0 and entry_form_id=117 group by sample_mst_id");
 							 foreach ($sampQty as $val)
								{
									$samplQtyArr[$val[csf('sample_mst_id')]]=$val[csf('sd')];
								}
								 $reqQty=sql_select("select sample_mst_id,sum(required_qty) as rq from sample_development_fabric_acc where status_active=1 and is_deleted=0 and form_type=1 group by sample_mst_id");
								 foreach ($reqQty as $Reqval)
								{
									//$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
									$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
								}

								$emb_sel=sql_select("select sample_mst_id,count(id) as ide from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 group by sample_mst_id");
								foreach($emb_sel as $embVal)
								{
									$embArr[$embVal[csf('sample_mst_id')]]=$embVal[csf('ide')];
								}

                            foreach ($nameArray as $row)
                            { 
                            	$id=$row[csf('id')] ;
                             	 //$sampQty=return_field_value("sum(sample_prod_qty)","sample_development_dtls","sample_mst_id=$id and status_active=1 and is_deleted=0 and entry_form_id=117");
                            	
                            	
                            	//print_r($sampQty);die;
                             	 // $reqQty=return_field_value("sum(required_qty)","sample_development_fabric_acc","sample_mst_id=$id and status_active=1 and is_deleted=0 and form_type=1");
                            	//$emb_sel=sql_select("select count(id) as id from sample_development_fabric_acc where sample_mst_id=$id and form_type=3 and status_active=1 and is_deleted=0");
 
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									if($db_type==0) $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='25' order by id desc limit 0,1");
									
									else if($db_type==2) $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='25' and rownum<2 order by id desc");
									
									$value=$row[csf('id')]."**".$app_id;
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                    </td>   
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="80" align="center"> <a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_id')]; ?>+'*'+<? echo $row[csf('id')]; ?>,'sample_requisition_print', '../order/woven_order/requires/sample_requisition_controller')">
									<? echo $row[csf('requisition_number_prefix_num')]; ?></a>

									 
									</td>
                                    <td width="70"><p><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></p></td>
									<td width="120" align="left"> <? echo $company_arr[$row[csf('company_id')]]; ?></td>
									<td align="left" width="60"> <? echo $dealing_arr[$row[csf('dealing_marchant')]]; ?></td>
									
									<td width="90"> <? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td width="120"><p><? echo $season_arr[$row[csf('season')]]; ?></p></td>
									<td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td width="70" align="right">
									<p>
									<?
 									 echo $samplQtyArr[$row[csf('id')]];
 									 ?>
 									 </p></td>
									<td width="50" align="right"><p><? echo  $reqQtyArr[$row[csf('id')]];?></p></td>
									<td>
									 <? 
										if($embArr[$row[csf('id')]]>0)
										{
											echo "&nbsp; YES";
										}
											else
											{
												echo "&nbsp; NO";
											}

									?>  
									</td>
									 
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
<?
	exit();	
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response=''; $requisition='';
	// echo $txt_alter_user_id;die();
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	if($approval_type==0)
	{
		$response=trim($req_nos);
		//echo $req_nos;die;		
		$reqs_ids=explode(",",trim($req_nos));

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		  
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date,inserted_by,insert_date"; 
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$approved_no_array=array();
		
		foreach($reqs_ids as $val)
		{
			$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val' and entry_form=25");
			$approved_no=$approved_no+1;
			
 			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",25,".$val.",".$approved_no.",".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
			
			$approved_no_array[$val]=$approved_no;
				
			$id=$id+1;
			$i++;
		}
 		$flag=1;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);

		if($flag==1) 
		{
			if($rID2) 
				{
					$flag=1;
					$rID=sql_multirow_update("sample_development_mst","is_approved",1,"id",trim($req_nos),0);
					if($rID) $flag=1; else $flag=0;

				}
				 else 
				 {
				 	$flag=0; 
				 }
		} 

		$approved_string="";
		
		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}
		//print_r($approved_no_array);die;


		$approved_string_mst="CASE id ".$approved_string." END"; 
		//CASE id  WHEN 538 THEN 2 END
		$approved_string_dtls="CASE sample_mst_id ".$approved_string." END";
		$approved_string_dtls_fab="CASE sample_mst_id ".$approved_string." END";
		
		$sql_insert="insert into sample_development_mst_history(id,hist_mst_id,approved_no,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date) 
			select	
			'',id,$approved_string_mst,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date from  sample_development_mst where id in ($req_nos)";
				
		$rID3=execute_query($sql_insert,0);

 		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 

		 $sql_insert_dtls="insert into sample_development_dtls_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date) 
			select	
			'', $approved_string_dtls,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date from  sample_development_dtls where sample_mst_id in ($req_nos) and entry_form_id=117";

			$sql_insert_dtls_fab="insert into sample_development_fabric_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re) 
			select	
			'',$approved_string_dtls_fab,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id in ($req_nos)";
			//	echo "part1 ".$approved_string_dtls_fab."  part2  ".$req_nos;die;
				
		$rID4=execute_query($sql_insert_dtls,1);
		// echo $rID4.' addds ';die;

		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
 		$rID5=execute_query($sql_insert_dtls_fab,1);
  		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		//echo ' a '.$rID3.$rID4.$rID5.' bddee';


		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
  		$arrs=explode(',', $req_nos);
  		$val="";
  		$requisition_number="";
  		foreach ($arrs as $value)
 		 {
			$arrs2=explode('**', $value);//concate(buyer_name,'_',contact_person
			$is_exits_ack=return_field_value("is_acknowledge","sample_development_mst","id=$arrs2[0]");
			
   			if($is_exits_ack==1)
 			{
 				if($val=="") $val.=$is_exits_ack."t";else $val .=','.$is_exits_ack.'t';
 				$req_no=return_field_value("requisition_number","sample_development_mst","id=$arrs2[0]");
 				if($requisition_number=="") $requisition_number.=$req_no;else $requisition_number .=','.$req_no;
 			}	
 		 }
 		 $requisition=$requisition_number;
  		// $cc=strlen($val);  
 		 if(strpos($val, 't')==true)
 		 {
  		 	  $msg='308';
   		 }
  		 else 
 		 {
 		$req_nos = explode(',',$req_nos); 
 		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			//echo $reqs_id." Part two ".$app_id;die;
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		$rID=sql_multirow_update("sample_development_mst","is_approved",0,"id",$reqs_ids,0);
 		if($rID) $flag=1; else $flag=0;
 		//1*'04-Feb-2017 05:23:03 PM'22**502
		
		// $data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		// 	$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"mst_id",$reqs_ids,0);

 		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"mst_id",$reqs_ids,0);

 		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$response=$reqs_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
 		 }
 		
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response."**".$requisition;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response."**".$requisition;
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response."**".$requisition;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response."**".$requisition;
		}
	}
	disconnect($con);
	die;
	
}
?>