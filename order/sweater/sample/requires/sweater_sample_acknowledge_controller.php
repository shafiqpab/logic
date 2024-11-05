<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer_season")
{
	echo create_drop_down( "cbo_season", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select --", "", "" );
	exit();
}


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/sweater_sample_acknowledge_controller',this.value, 'load_drop_down_buyer_season', 'td_season' );" );
	exit();
}



/*


$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );*/

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company where id=$cbo_company_name",'id','company_name');	
	$dealing_mar_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name", "id", "team_member_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$team_name_arr=return_library_array( "select id, team_name from lib_sample_production_team", "id", "team_name"  );
	
	
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_season=str_replace("'","",$cbo_season);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_acknowledge_type=str_replace("'","",$cbo_acknowledge_type);


	if($cbo_season>0){$where_cond =" and a.season=$cbo_season";}
	if($cbo_buyer_name>0){$where_cond .=" and a.buyer_name=$cbo_buyer_name";}
	if($cbo_company_name>0){$where_cond2 .=" and a.COMPANY_ID=$cbo_company_name";}
	if($txt_style_ref!=""){$where_cond .=" and a.style_ref_no like('%$txt_style_ref%')";}
	if($txt_req_no!=""){$where_cond .=" and a.requisition_number like('%$txt_req_no')";}


	if($cbo_season>0){$where_cond2 =" and a.season=$cbo_season";}
	if($cbo_buyer_name>0){$where_cond2 .=" and a.buyer_name=$cbo_buyer_name";}
	if($txt_style_ref!=""){$where_cond2 .=" and a.style_ref_no like('%$txt_style_ref%')";}
	if($txt_req_no!=""){$where_cond2 .=" and a.requisition_number like('%$txt_req_no')";}



	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));
			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}
		
		$where_cond .=" and a.REQUISITION_DATE between '$start_date' and '$end_date'";
		$where_cond2 .=" and  a.REQUISITION_DATE between '$start_date' and '$end_date'";
	}




	if($cbo_acknowledge_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";

		$sql="select a.ID AS UPDATE_ID,a.SAMPLE_MST_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.COMPANY_ID,a.BUYER_NAME,a.STYLE_REF_NO,a.DEALING_MARCHANT,a.CONFIRM_DEL_END_DATE,a.REFUSING_CAUSE,a.EMBELLISHMENT_STATUS_ID,a.SAMPLE_QTY,a.YARN_QTY as REQUIRED_QTY,a.DELV_START_DATE,a.DELV_END_DATE,a.SEASON,TEAM_LEADER from SAMPLE_REQUISITION_ACKNOWLEDGE a left join SAMPLE_DEVELOPMENT_DTLS b on a.sample_mst_id=b.sample_mst_id and b.status_active=1 and b.is_deleted=0 where a.ENTRY_FORM=345  and a.status_active=1 and a.is_deleted=0 $where_cond2";
	}
	else
	{
		$sql="SELECT a.ID AS SAMPLE_MST_ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.SAMPLE_TEAM_ID as TEAM_LEADER,a.REFUSING_CAUSE,
		sum(b.SAMPLE_PROD_QTY) as SAMPLE_QTY,max(b.DELV_START_DATE) as DELV_START_DATE, min(b.DELV_END_DATE) as DELV_END_DATE,
		
		LISTAGG(CAST(b.EMBELLISHMENT_STATUS_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as EMBELLISHMENT_STATUS_ID,
		
		sum(c.REQUIRED_QTY) AS REQUIRED_QTY
		
		 FROM SAMPLE_DEVELOPMENT_MST a,SAMPLE_DEVELOPMENT_DTLS b,SAMPLE_DEVELOPMENT_FABRIC_ACC c WHERE a.id=b.SAMPLE_MST_ID and b.SAMPLE_MST_ID=c.SAMPLE_MST_ID  and a.entry_form_id=341 and c.FORM_TYPE=1 and a.is_acknowledge <> 1 and b.acknowledge_status not in(1) and a.company_id=$cbo_company_name $where_cond
		group by a.ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.SAMPLE_TEAM_ID,a.REFUSING_CAUSE
		";
	}
	//and b.acknowledge_status not in(1)
	$width=1500;
	?>
    <form name="sample_acknowledgement_2" id="sample_acknowledgement_2">
        <div style="width:<? echo $width+30;?>px; float:left;">
        <fieldset style="width:<? echo $width+20;?>px; margin-top:10px">
        <legend>Pre-Costing Approval</legend>
            <table cellspacing="0" align="left" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" >
                <thead>
                    <th width="20"></th>
                    <th width="35">SL</th>
                    <th width="100">Requisition No</th>
                    <th width="80">Req. Date</th>
                    <th width="100">Company</th>
                    <th width="100">Dealing Mer.</th>
                    <th width="100">Buyer</th>
                    <th width="60">Season</th>
                    <th width="120">Style Ref</th>
                    <th width="80">Sample Qty</th>
                    <th width="80">Yarn Qty</th>
                    <th width="80">Embellishment</th>
                    <th width="80">Delv St Date</th>
                    <th width="80">Delv End Date</th>
                    <th width="80">Sample Team</th>
                    <th width="100">Confirm Del. End Date</th>
                    <th>Refusing Cause</th>
                </thead>
            </table>
            
            <div style="width:<? echo $width+20;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0"  align="left" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                         $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="20" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" value="1" />
                                       
                                        <input id="update_id_<? echo $i;?>" name="update_id[]" type="hidden" value="<? echo $row[UPDATE_ID]; ?>" />
                                        <input id="sample_req_id_<? echo $i;?>" name="sample_req_id[]" type="hidden" value="<? echo $row[SAMPLE_MST_ID]; ?>" />
                                        <input id="sample_req_no_<? echo $i;?>" name="sample_req_no[]" type="hidden" value="<? echo $row[REQUISITION_NUMBER]; ?>" />
                                        <input id="req_date_<? echo $i;?>" name="req_date[]" type="hidden" value="<? echo $row[REQUISITION_DATE]; ?>" />
                                        
                                        <input id="company_id_<? echo $i;?>" name="company_id[]" type="hidden" value="<? echo $row[COMPANY_ID]; ?>" />
                                        <input id="dealing_marchant_<? echo $i;?>" name="dealing_marchant[]" type="hidden" value="<? echo $row[DEALING_MARCHANT]; ?>" />
                                        <input id="buyer_id_<? echo $i;?>" name="buyer_id[]" type="hidden" value="<? echo $row[BUYER_NAME]; ?>" />
                                        <input id="season_<? echo $i;?>" name="season[]" type="hidden" value="<? echo $row[SEASON]; ?>" />
                                        <input id="style_ref_<? echo $i;?>" name="style_ref[]" type="hidden" value="<? echo $row[STYLE_REF_NO]; ?>" />
                                        <input id="sample_qty_<? echo $i;?>" name="sample_qty[]" type="hidden" value="<? echo $row[SAMPLE_QTY]; ?>" />
                                        <input id="required_qty_<? echo $i;?>" name="required_qty[]" type="hidden" value="<? echo $row[REQUIRED_QTY]; ?>" />
                                        <input id="embellishment_status_id_<? echo $i;?>" name="embellishment_status_id[]" type="hidden" value="<? echo $row[EMBELLISHMENT_STATUS_ID]; ?>" />
                                        <input id="delv_start_date_<? echo $i;?>" name="delv_start_date[]" type="hidden" value="<? echo $row[DELV_START_DATE]; ?>" />
                                        <input id="delv_end_date_<? echo $i;?>" name="delv_end_date[]" type="hidden" value="<? echo $row[DELV_END_DATE]; ?>" />
                                        <input id="team_leader_<? echo $i;?>" name="team_leader[]" type="hidden" value="<? echo $row[TEAM_LEADER]; ?>" />
                                    
                                    </td>
									<td width="35" align="center"><? echo  $i; ?></td>
                                    <td width="100"><? echo $row[REQUISITION_NUMBER]; ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[REQUISITION_DATE]); ?></td>
                                    <td width="100"><? echo $company_arr[$row[COMPANY_ID]]; ?></td>
                                    <td width="100"><p><? echo $dealing_mar_arr[$row[DEALING_MARCHANT]]; ?></p></td>
                                    <td width="100"><? echo $buyer_arr[$row[BUYER_NAME]]; ?></td>
                                    <td width="60"><? echo $season_arr[$row[SEASON]]; ?></td>
                                    <td width="120"><p><? echo $row[STYLE_REF_NO]; ?></p></td>
                                    <td width="80"><? echo $row[SAMPLE_QTY]; ?></td>
                                    <td width="80"><? echo $row[REQUIRED_QTY]; ?></td>
                                    <td width="80"><? echo $row[EMBELLISHMENT_STATUS_ID]?"Yes":"No"; ?></td>
                                    <td width="80"><? echo change_date_format($row[DELV_START_DATE]); ?></td>
                                    <td width="80"><? echo change_date_format($row[DELV_END_DATE]); ?></td>
                                    <td width="80"><? echo $team_name_arr[$row[TEAM_LEADER]]; ?></td>
                                    <td width="100">
                                    	<input type="text" name="txt_confirm_del_end_date_<? echo $i;?>" id="txt_confirm_del_end_date_<? echo $i;?>" class="datepicker" placeholder="Select Date" autocomplete="off" readonly style="width:80px" value="<? echo $row[CONFIRM_DEL_END_DATE]; ?>" onchange="fn_check_acknowledge(<? echo $i;?>)" />
                                    </td>
                                    <td><input type="text" name="txt_refusing_cause_<? echo $i;?>" id="txt_refusing_cause_<? echo $i;?>" class="text_boxes" style="width:90%" value="<? echo $row[REFUSING_CAUSE]; ?>" /></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" align="left" cellpadding="0" border="0" rules="all" width="<? echo $width;?>" class="rpt_table">
				<tfoot>
                    <td width="20" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? echo ($cbo_acknowledge_type==1)?"Un-Acknowledge":"Acknowledge"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $cbo_acknowledge_type; ?>,<? echo ($cbo_acknowledge_type==2)?0:1; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
        </div>
    </form>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}





if ($action=="approve")
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

	$acknowledge_id_arr=array();
	$id_mst=return_next_id( "id", "sample_requisition_acknowledge", 1 ) ;
	$field_array="id,sample_mst_id,requisition_number,requisition_date,company_id,buyer_name,style_ref_no,dealing_marchant,team_leader,confirm_del_end_date,refusing_cause,embellishment_status_id,sample_qty,yarn_qty,delv_start_date,delv_end_date,entry_form,season,inserted_by, insert_date,status_active,is_deleted";
	
	
		for($i=1;$i<$total_row;$i++)
		{	
			$is_checked='tbl_'.$i;
			
			$update_id='update_id_'.$i;
			$sample_req_id='sample_req_id_'.$i;
			$sample_req_no='sample_req_no_'.$i;
			$req_date='req_date_'.$i;
			$company_id='company_id_'.$i;
			$buyer_id='buyer_id_'.$i;
			$season='season_'.$i;
			$style_ref='style_ref_'.$i;
			$sample_qty='sample_qty_'.$i;
			$required_qty='required_qty_'.$i;
			$embellishment_status_id='embellishment_status_id_'.$i;
			$delv_start_date='delv_start_date_'.$i;
			$delv_end_date='delv_end_date_'.$i;
			$team_leader='team_leader_'.$i;
			$confirm_del_end_date='txt_confirm_del_end_date_'.$i;
			$refusing_cause='txt_refusing_cause_'.$i;
			$dealing_marchant='dealing_marchant_'.$i;
			
			
			if(str_replace("'","",$$confirm_del_end_date)!='' && str_replace("'","",$$is_checked)==1){
				$sample_req_id_arr[str_replace("'","",$$sample_req_id)]=str_replace("'","",$$sample_req_id);
				
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id_mst.",".$$sample_req_id.",".$$sample_req_no.",".$$req_date.",".$$company_id.",".$$buyer_id.",".$$style_ref.",".$$dealing_marchant.",".$$team_leader.",".$$confirm_del_end_date.",".$$refusing_cause.",".$$embellishment_status_id.",".$$sample_qty.",".$$required_qty.",".$$delv_start_date.",".$$delv_end_date.",345,".$$season.",".$user_id.",'".$pc_date_time."',1,0)";
				
				$acknowledge_id_arr[$id_mst]=$id_mst;
				$id_mst=$id_mst+1;
				
			}
			
			
			//echo $data_array;die;
			//insert into (70,2398,'SSL-20-00012','23-JAN-20','20','250','TUB','68','57','01-FEB-20','','0','16','4','23-JAN-20','01-FEB-20',345,'142',1,'23-Jan-2020 10:26:05 AM',1,0)
			
			
			if(str_replace("'","",$$refusing_cause)!=''){
				$id_arr[]=str_replace("'","",$$sample_req_id);
				$data_array_up[str_replace("'",'',$$sample_req_id)] =explode("*",("".$$refusing_cause.""));
			}
			
		}
		
		
		
		if(count($sample_req_id_arr)>0){
			//$rID_delete=execute_query( "delete from sample_requisition_acknowledge where sample_mst_id in(".implode(",",$sample_req_id_arr).") and entry_form=345",0);
			
			$rID_delete=execute_query( "update sample_requisition_acknowledge set status_active=0,is_deleted=1  where id in(".implode(",",$sample_req_id_arr).") and entry_form=345",0);
			
			
		}
		
		$flag=0;
		$rID=sql_insert("sample_requisition_acknowledge",$field_array,$data_array,1);
		if($rID){$flag=1;}else{$flag=0;}
	
		//echo "10**".$data_array;oci_rollback($con);die;
		
		
		
		if(count($sample_req_id_arr)>0 && $flag==1){
			$fields="is_acknowledge";
			$rID_update=sql_multirow_update("sample_development_mst",$fields,"1","id",implode(",",$sample_req_id_arr),0);
		}
		if($rID_update){$flag=1;}else{$flag=0;}
	
	
		if(count($id_arr)>0){
			$field_array_up="refusing_cause";
			$rID_ref_update=execute_query(bulk_update_sql_statement("sample_development_mst", "id",$field_array_up,$data_array_up,$id_arr ));
			$flag=1;
		}
	
	
		
		
		if($db_type==0)
		{
			if($flag==1 )
			{
				mysql_query("COMMIT");
				echo "1**".implode(",",$acknowledge_id_arr)."**mail_send";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".implode(",",$acknowledge_id_arr);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1 )
			{
				oci_commit($con);
				echo "1**".implode(",",$acknowledge_id_arr)."**mail_send";
			}
			else
			{
				oci_rollback($con);
				echo "10**".implode(",",$acknowledge_id_arr);
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
		$acknowledge_id_arr=array();
	
		for($i=1;$i<$total_row;$i++)
		{
			$update_id='update_id_'.$i;
			$sample_req_id='sample_req_id_'.$i;
			$sample_req_no='sample_req_no_'.$i;
			$req_date='req_date_'.$i;
			$company_id='company_id_'.$i;
			$buyer_id='buyer_id_'.$i;
			$season='season_'.$i;
			$style_ref='style_ref_'.$i;
			$sample_qty='sample_qty_'.$i;
			$required_qty='required_qty_'.$i;
			$embellishment_status_id='embellishment_status_id_'.$i;
			$delv_start_date='delv_start_date_'.$i;
			$delv_end_date='delv_end_date_'.$i;
			$team_leader='team_leader_'.$i;
			$confirm_del_end_date='txt_confirm_del_end_date_'.$i;
			$refusing_cause='txt_refusing_cause_'.$i;
			
			if(str_replace("'","",$$sample_req_id)!=''){
				$update_id_arr[str_replace("'","",$$update_id)]=str_replace("'","",$$update_id);
				$sample_req_id_arr[str_replace("'","",$$sample_req_id)]=str_replace("'","",$$sample_req_id);
				
				$id_arr[]=str_replace("'","",$$sample_req_id);
				$data_array_up[str_replace("'",'',$$sample_req_id)] =explode("*",("2*".$$refusing_cause.""));
				$acknowledge_id_arr[str_replace("'","",$$update_id)]=str_replace("'","",$$update_id);
			}
		}
		
		
		
		
		$flag=0;
		if(count($update_id_arr)>0){
			//$rID_delete=execute_query( "delete from sample_requisition_acknowledge where id in(".implode(",",$update_id_arr).") and entry_form=345",0);
			$rID_delete=execute_query( "update sample_requisition_acknowledge set status_active=0,is_deleted=1  where id in(".implode(",",$update_id_arr).") and entry_form=345",0);

		}
		if($rID_delete){$flag=1;}else{$flag=0;}
		
		if(count($id_arr)>0 && $flag==1){
			$field_array_up="is_acknowledge*refusing_cause";
			$rID_ref_update=execute_query(bulk_update_sql_statement("sample_development_mst", "id",$field_array_up,$data_array_up,$id_arr ));
		}
		if($rID_ref_update){$flag=1;}else{$flag=0;}
		
		
		
		if($db_type==0)
		{
			if($flag==1 )
			{
				mysql_query("COMMIT");
				//echo "1**0**0";
				echo "1**".implode(",",$acknowledge_id_arr)."**mail_send";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**0**0";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1 )
			{
				oci_commit($con);
				//echo "1**0**0";
				echo "1**".implode(",",$acknowledge_id_arr)."**mail_send";
			}
			else
			{
				oci_rollback($con);
				echo "10**0**0";
			}
		}
		
		disconnect($con);
		die;
	}
	
	
	

}





if ($action=="acknowledge_capacity"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$row_no=str_replace("'","",$row_no);
	
	$team_leader='team_leader_'.$row_no;
	$txt_confirm_del_end_date='txt_confirm_del_end_date_'.$row_no;
	$company_id='company_id_'.$row_no;

	if($db_type==0)
	{
		$confirm_del_end_date=change_date_format(str_replace("'","",$$txt_confirm_del_end_date),"yyyy-mm-dd","");
	}
	else
	{
		$confirm_del_end_date=date("j-M-Y",strtotime(str_replace("'","",$$txt_confirm_del_end_date)));
	}
	
	
	$req_arr=return_field_value("STYLE_CAPACITY","LIB_SAMPLE_PRODUCTION_TEAM","id =".$$team_leader." and PRODUCT_CATEGORY=6 and is_deleted=0 and status_active=1");	
	
	$req_acknowledge_arr=return_library_array("select SAMPLE_MST_ID, SAMPLE_MST_ID from SAMPLE_REQUISITION_ACKNOWLEDGE where company_id=".$$company_id." and entry_form=345 and status_active=1 and is_deleted=0 and team_leader=".$$team_leader." and CONFIRM_DEL_END_DATE='$confirm_del_end_date'", "SAMPLE_MST_ID", "SAMPLE_MST_ID"  );

	echo $req_arr.','.count($req_acknowledge_arr).','.$row_no;
	
	exit();	
}




?>

