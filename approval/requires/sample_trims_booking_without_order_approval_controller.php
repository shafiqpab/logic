<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

/*if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}*/

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$company_fullName=return_library_array( "select id, company_name from lib_company",'id','company_name');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
//	echo "SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$data[1]' AND valid = 1";die;
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1"); 
	//print_r($log_sql);die;
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

if($action=='user_popup'){
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
?>	
<script>
  function js_set_value(id)
  { 
	document.getElementById('selected_id').value=id;
	  parent.emailwindow.hide();
  }
</script>

<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
		 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;

		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.SEQUENCE_NO";
			 //echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
}


function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr[lib_buyer_arr])));
	$lib_brand_id_string=implode(',',(array_keys($parameterArr[lib_brand_arr])));
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr[lib_item_cat_arr])));
	$lib_store_id_string=implode(',',(array_keys($parameterArr[lib_store_arr]))); 
	$product_dept_id_string=implode(',',(array_keys($parameterArr[product_dept_arr])));
 
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID,IS_DATA_LEVEL_SECURED,store_location_id as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows[ID]]=$rows;
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr[company_id]} AND PAGE_ID = {$parameterArr[page_id]} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		
		$rows[ITEM_CATE_ID]=$userDataArr[$rows[USER_ID]][ITEM_CATE_ID];
		$rows[STORE_ID]=$userDataArr[$rows[USER_ID]][STORE_ID];
		
		if($userDataArr[$rows[USER_ID]][BUYER_ID]!=''){
			$rows[BUYER_ID]=implode(',',(explode(',',$userDataArr[$rows[USER_ID]][BUYER_ID])+explode(',',$rows[BUYER_ID])));
		}
		if($userDataArr[$rows[USER_ID]][BRAND_ID]!=''){
			$rows[BRAND_ID]=implode(',',(explode(',',$userDataArr[$rows[USER_ID]][BRAND_ID])+explode(',',$rows[BRAND_ID])));
		}
		
		if($rows[BUYER_ID]==''){$rows[BUYER_ID]=$lib_buyer_id_string;}
		if($rows[BRAND_ID]==''){$rows[BRAND_ID]=$lib_brand_id_string;}
		if($rows[ITEM_CATE_ID]==''){$rows[ITEM_CATE_ID]=$lib_item_cat_id_string;}
		if($rows[STORE_ID]==''){$rows[STORE_ID]=$lib_store_id_string;}
		if($rows[DEPARTMENT]==''){$rows[DEPARTMENT]=$product_dept_id_string;}
		
		$dataArr[sequ_by][$rows[SEQUENCE_NO]]=$rows;
		$dataArr[user_by][$rows[USER_ID]]=$rows;
		$dataArr[sequ_arr][$rows[SEQUENCE_NO]]=$rows[SEQUENCE_NO];
	}
	
 
	//Condtion data ready.............
	$buyerArr=explode(',',$dataArr[user_by][$parameterArr[user_id]][BUYER_ID]);
	$brandArr=explode(',',$dataArr[user_by][$parameterArr[user_id]][BRAND_ID]);
	$itemCatArr=explode(',',$dataArr[user_by][$parameterArr[user_id]][ITEM_CATE_ID]);
	$storeArr=explode(',',$dataArr[user_by][$parameterArr[user_id]][STORE_ID]);
	$departmentArr=explode(',',$dataArr[user_by][$parameterArr[user_id]][DEPARTMENT]);
	
	
	for($sequ=($dataArr[user_by][$parameterArr[user_id]][SEQUENCE_NO]-1);$sequ>=1;$sequ--){
		
		//Buyer Condition.......................................................start;
		$buyerPreviousSequArr=explode(',',$dataArr[sequ_by][$sequ][BUYER_ID]);
		$remainingBuyerArr=array_diff($buyerArr,$buyerPreviousSequArr);
		foreach($buyerArr as $k=>$v){
			if($remainingBuyerArr[$k]!=$v){
				$dataArr[seq_buyer][$sequ][$k]=$v;
				if($dataArr[sequ_by][$sequ][BYPASS]==2){
					unset($buyerArr[$k]);
				}
			}
		}
		//.....................................end
		
		
		//Brand Condition.......................................................start;
		$brandPreviousSequArr=explode(',',$dataArr[sequ_by][$sequ][BRAND_ID]);
		$remainingBrandArr=array_diff($brandArr,$brandPreviousSequArr);
		foreach($brandArr as $k=>$v){
			if($remainingBrandArr[$k]!=$v){
				$dataArr[seq_brand][$sequ][$k]=$v;
				if($dataArr[sequ_by][$sequ][BYPASS]==2){
					unset($brandArr[$k]);
				}
			}
		}
		//.....................................end
		
		//item cat Condition.......................................................start;
		$itemCatPreviousSequArr=explode(',',$dataArr[sequ_by][$sequ][ITEM_CATE_ID]);
		$remainingItemCatArr=array_diff($itemCatArr,$itemCatPreviousSequArr);
		foreach($itemCatArr as $k=>$v){
			if($remainingItemCatArr[$k]!=$v){
				$dataArr[seq_item_cat][$sequ][$k]=$v;
				if($dataArr[sequ_by][$sequ][BYPASS]==2){
					unset($itemCatArr[$k]);
				}
			}
		}
		//.....................................end
		
		
		
		//store Condition.......................................................start;
		$storeIdPreviousSequArr=explode(',',$dataArr[sequ_by][$sequ][STORE_ID]);
		$remainingStoreIdArr=array_diff($storeArr,$storeIdPreviousSequArr);
		foreach($storeArr as $k=>$v){
			if($remainingStoreIdArr[$k]!=$v){
				$dataArr[seq_store_id][$sequ][$k]=$v;
				if($dataArr[sequ_by][$sequ][BYPASS]==2){
					unset($storeArr[$k]);
				}
			}
		}
		//.....................................end
		
		
		//department Condition.......................................................start;
		$departmentIdPreviousSequArr=explode(',',$dataArr[sequ_by][$sequ][DEPARTMENT]);
		$remainingDepartmentIdArr=array_diff($departmentArr,$departmentIdPreviousSequArr);
		foreach($departmentArr as $k=>$v){
			if($remainingDepartmentIdArr[$k]!=$v){
				$dataArr[seq_department_id][$sequ][$k]=$v;
				if($dataArr[sequ_by][$sequ][BYPASS]==2){
					unset($departmentArr[$k]);
				}
			}
		}
		//.....................................end
		
		
		
	}

	if(count($buyerArr)>0){$dataArr[seq_buyer][0]=$buyerArr;}
	if(count($brandArr)>0){$dataArr[seq_brand][0]=$brandArr;}
	if(count($itemCatArr)>0){$dataArr[seq_item_cat][0]=$itemCatArr;}
	if(count($storeArr)>0){$dataArr[seq_store_id][0]=$storeArr;}
	if(count($departmentArr)>0){$dataArr[seq_department_id][0]=$departmentArr;} 
	
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr[lib_buyer_arr])));
	$lib_brand_arr=implode(',',(array_keys($parameterArr[lib_brand_arr])));
	$lib_item_cat_arr=implode(',',(array_keys($parameterArr[lib_item_cat_arr])));
	$lib_store_arr=implode(',',(array_keys($parameterArr[lib_store_arr])));
	$product_dept_arr=implode(',',(array_keys($parameterArr[product_dept_arr])));
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows[ID]][BUYER_ID]=$rows[BUYER_ID];
		$userDataArr[$rows[ID]][BRAND_ID]=$rows[BRAND_ID];
		$userDataArr[$rows[ID]][ITEM_ID]=$rows[ITEM_ID];
		$userDataArr[$rows[ID]][STORE_ID]=$rows[STORE_ID];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr[company_id]} AND PAGE_ID = {$parameterArr[page_id]} AND IS_DELETED = 0  order by SEQUENCE_NO";
	    //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows[BUYER_ID]){$userDataArr[$rows[USER_ID]][BUYER_ID]=$rows[BUYER_ID];}
		if($rows[BRAND_ID]!=''){$userDataArr[$rows[USER_ID]][BRAND_ID]=$rows[BRAND_ID];}
		if($rows[DEPARTMENT]!=''){$userDataArr[$rows[USER_ID]][DEPARTMENT]=$rows[DEPARTMENT];}
		
		
		
		
		if($userDataArr[$rows[USER_ID]][BUYER_ID]==''){
			$userDataArr[$rows[USER_ID]][BUYER_ID]=$lib_buyer_arr;
		}
		if($userDataArr[$rows[USER_ID]][BRAND_ID]==''){
			$userDataArr[$rows[USER_ID]][BRAND_ID]=$lib_brand_arr;
		}
		if($userDataArr[$rows[USER_ID]][ITEM_ID]==''){
			$userDataArr[$rows[USER_ID]][ITEM_ID]=$lib_item_cat_arr;
		}
		if($userDataArr[$rows[USER_ID]][STORE_ID]==''){
			$userDataArr[$rows[USER_ID]][STORE_ID]=$lib_store_arr;
		}
		
		if($userDataArr[$rows[USER_ID]][DEPARTMENT]==''){
			$userDataArr[$rows[USER_ID]][DEPARTMENT]=$product_dept_arr;
		}
		
		
		$usersDataArr[$rows[USER_ID]][BUYER_ID]=explode(',',$userDataArr[$rows[USER_ID]][BUYER_ID]);
		$usersDataArr[$rows[USER_ID]][BRAND_ID]=explode(',',$userDataArr[$rows[USER_ID]][BRAND_ID]);
		$usersDataArr[$rows[USER_ID]][ITEM_ID]=explode(',',$userDataArr[$rows[USER_ID]][ITEM_ID]);
		$usersDataArr[$rows[USER_ID]][STORE_ID]=explode(',',$userDataArr[$rows[USER_ID]][STORE_ID]);
		$usersDataArr[$rows[USER_ID]][DEPARTMENT]=explode(',',$userDataArr[$rows[USER_ID]][DEPARTMENT]);
		
		$userSeqDataArr[$rows[USER_ID]]=$rows[SEQUENCE_NO];
	
	}
	
	 //print_r($usersDataArr[526][BUYER_ID]);die;
	
	$finalSeq=array();
	foreach($parameterArr[match_data] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				in_array($bbtsRows[buyer],$usersDataArr[$user_id][BUYER_ID])
				&& $bbtsRows[buyer]>0
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		


		}
	}

	 //var_dump($finalSeq);
	 //var_dump($usersDataArr[526][DEPARTMENT]);die;
	
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!=""){$user_id=$txt_alter_user_id;}
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	
	if($db_type==0)
	{
		if ($cbo_year==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$cbo_year";
	}
	elseif($db_type==2)
	{ 
		if ($cbo_year==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_year";
	}
	if(str_replace("'","",$txt_booking_no)!=''){$booking_con=" and a.booking_no_prefix_num=$txt_booking_no";}else{$booking_con="";}
	
	if($cbo_buyer_name!=0){$buyer_con=" and a.buyer_id=$cbo_buyer_name";}
 
	
	
 	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Sample Booking (Without order).</font>";
		die;
	}
	
	
	$electronicDataArr=getSequence(array(company_id=>$cbo_company_name,page_id=>$menu_id,user_id=>$user_id,lib_buyer_arr=>$buyer_arr,lib_brand_arr=>0,product_dept_arr=>0,lib_item_cat_arr=>0,lib_store_arr=>0));
	
	if($approval_type==0)
	{

		$i=1;
		for($seq=0;$seq<=count($electronicDataArr[sequ_arr]); $seq++ ){
		//foreach($electronicDataArr[seq_buyer] as $seq=>$buyer){
			$where_con[buyer]='';
			//buyer.........................................
			if(count($electronicDataArr[seq_buyer][$seq])){
				$where_con[buyer]= where_con_using_array($electronicDataArr[seq_buyer][$seq],0,'a.BUYER_ID');
			}
			//..................................end buyer;
			 
			if($where_con[buyer]){
				if($i>1){$sql .=" UNION ALL ";}
				$sql .= " select a.id,a.BOOKING_NO, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.IS_APPROVED from wo_non_ord_samp_booking_mst a where a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.item_category=4 and a.booking_type=5 and a.is_approved<>1 and a.READY_TO_APPROVED=1 and a.company_id=$cbo_company_name and a.APPROVED_SEQU_BY=$seq {$where_con[buyer]} $year_id_cond $booking_con $buyer_con";// and a.ENTRY_FORM=471
			$i++;
			}
		
		}
		
	}
	else
	{
		$sql = "select a.id,a.BOOKING_NO, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.IS_APPROVED from wo_non_ord_samp_booking_mst a,APPROVAL_MST b where  b.mst_id=a.id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.item_category=4 and a.booking_type=5 and b.SEQUENCE_NO={$electronicDataArr[user_by][$user_id][SEQUENCE_NO]} and a.APPROVED_SEQU_BY=b.SEQUENCE_NO  and a.company_id=$cbo_company_name   and b.ENTRY_FORM=55 $year_id_cond $booking_con $buyer_con";
	 

	}

	 //echo $sql;die;
	  
	  
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:840px; margin-top:10px">
        <legend>Sample Trims Booking [Without Order] Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="35">SL</th>
                    <th width="100">Booking No</th>
                    <th width="80">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:820px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="802" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            { 
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								
								$dataStr = "'".$row[BOOKING_NO]."',".$row[csf('company_id')].','.$row[IS_APPROVED];
								$print_button='<a href="javascript:generate_print('.$dataStr.')">'.$row[BOOKING_NO].'</a>';
								
								
								?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                        <td width="30" align="center" valign="middle">
                                            <input type="checkbox" name="tbl[]" id="tbl_<? echo $i;?>"  onClick="check_booking_approved(<? echo $i;?>);"/>
                                            <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                            <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
                                        </td>   
                                        <td width="35" align="center"><? echo $i; ?></td>
                                        <td width="100" align="center"><?= $print_button;?></td>
                                        <td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?></td>
                                        <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                        <td width="160"><p><?
										
											if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
											{
												echo $company_fullName[$row[csf('supplier_id')]];
											}
											else if($row[csf('pay_mode')]==1 || $row[csf('pay_mode')]==2 || $row[csf('pay_mode')]==4)
											{
												echo $supplier_arr[$row[csf('supplier_id')]];
											}
										?>&nbsp;</p></td>
                                        <td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                    </tr>
                                    <?
                                    $i++;
								}
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
						
							$isApp="";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="820" class="rpt_table">
				<tfoot>
                    <td width="30" align="center" style=" <?=$isApp; ?>">
                    	<input type="checkbox" id="all_check" onclick="check_all('all_check')" />
                    </td>
                    <td colspan="2">
                    	<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
                    </td>
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
	
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!=""){$user_id_approval=$txt_alter_user_id;}
	else{$user_id_approval=$user_id;}
	$booking_ids=str_replace("'","",$booking_ids);
	$booking_nos=str_replace("'","",$booking_nos);
	$approval_type=str_replace("'","",$approval_type);
	$booking_ids_arr = explode(',',$booking_ids);		
	
	
	if($approval_type==0)
	{
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=55 group by mst_id","mst_id","approved_no");
		
		//------------------
		$sql="select a.ID,a.BUYER_ID from wo_non_ord_samp_booking_mst a where a.id in($booking_ids)";
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row[ID]]=array('buyer'=>$row[BUYER_ID]);
		}

		$finalDataArr=getFinalUser(array(company_id=>$cbo_company_name,page_id=>$menu_id,lib_buyer_arr=>$buyer_arr,match_data=>$matchDataArr));
		
		$sequ_no_arr_by_sys_id = $finalDataArr[final_seq];
		$user_sequence_no = $finalDataArr[user_seq][$user_id_approval];

	 

		$app_mst_id=return_next_id( "id","approval_mst", 1 ) ;
		$app_his_mst_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($booking_ids_arr as $mst_id)
		{		
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$app_mst_id.",55,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$app_mst_id=$app_mst_id+1;
			
			
			$approved_no=$max_approved_no_arr[$mst_id]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$app_his_mst_id.",55,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$app_his_mst_id=$app_his_mst_id+1;
			
			
			
			//mst data.......................
			$approved=(max($finalDataArr[final_seq][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
			
			$approved_no_array[$mst_id]=$approved_no;
		
		}

		//print_r($finalDataArr[final_seq]);die;

		$con=connect();
		$flag=1;
		$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
		$rID=sql_insert("approval_mst",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}
		
		$field_array_up="is_approved*APPROVED_SEQU_BY"; 
		$rID1=execute_query(bulk_update_sql_statement( "wo_non_ord_samp_booking_mst", "id", $field_array_up, $data_array_up, $booking_ids_arr ));
		
		if($flag==1) 
		{
			if($rID1) $flag=1; else $flag=0; 
		}
	
		 
		
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=55 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=10;
		}
		
		
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";
		$rID3=sql_insert("approval_history",$field_array,$history_data_array,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}



	}
	else{
		
		$flag=1;
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","IS_APPROVED*APPROVED_SEQU_BY*READY_TO_APPROVED","2*0*2","id",$booking_ids,0); 
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}

		$query="delete from approval_mst  WHERE entry_form=55 and mst_id in ($booking_ids)";
		$rID1=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID1) $flag=1; else $flag=0; 
		}
		
		

		$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=55 and current_approval_status=1 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

	}//end;
	
	// echo "10**$rID**$rID1**$rID2**$rID3";die;
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con); 
			echo $flag."**".$booking_ids;
		}
		else
		{
			oci_rollback($con); 
			echo $flag."**".$booking_ids;
		}
	}
	disconnect($con);
	die;

	
	
	
	die;//.................................
	
	// echo $user_id_approval;die();
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	if($approval_type==0)
	{
		$response=$booking_ids;
		
		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=9 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, is_approved from wo_non_ord_samp_booking_mst where id in($booking_ids)","id","is_approved");

		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$book_nos='';

		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];
			
			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",9,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."','".$app_instru."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
			
		}
		
		if(count($approved_no_array)>0)
		{
			$approved_string="";
			
			if($db_type==0)
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
			}
			else
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
				}
			}
			
			$approved_string_mst="CASE booking_no ".$approved_string." END";
			$approved_string_dtls="CASE booking_no ".$approved_string." END";
			
			 $sql_insert="insert into wo_nonord_samboo_msthtry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,revised_date) 
				select	
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'".date('d-M-Y',time())."' from wo_non_ord_samp_booking_mst where booking_no in ($booking_nos) and (entry_form_id=140 or entry_form_id is null or entry_form_id=0)";
					
			 $sql_insert_dtls="insert into wo_nonor_sambo_dtl_hstry(id, approved_no, booking_dtls_id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,revised_date) 
				select	
				'', $approved_string_dtls, id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'".date('d-M-Y',time())."' from wo_non_ord_samp_booking_dtls where booking_no in ($booking_nos)";
					
			$sql_insert_yarn_cons="insert into wo_nonord_samyar_dtlhstry(id, approved_no, wo_nonord_sam_yarndtl_id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,revised_date) 
				select	
				'', $approved_string_dtls, id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,'".date('d-M-Y',time())."' from wo_non_ord_samp_yarn_dtls where booking_no in ($booking_nos)";
					
		
		}


		$con=connect();



		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,1); 
		if($rID) $flag=1; else $flag=0;
		
	
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=9 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($flag==1) 
		{
			if($rIDapp) $flag=1; else $flag=0; 
		} 
		
		$rID2=sql_insert("approval_history",$field_array,$data_array,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}

			$rID5=execute_query($sql_insert_yarn_cons,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($flag==1) $msg='19'; else $msg='21';
		
		// echo $rID.','.$rID2.','.$rID3.','.$rID4.','.$rID5;
	}
	else
	{
	
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved*ready_to_approved","0*0","id",$booking_ids,1);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=9 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		// $data=$user_id."*'".$pc_date_time."'";
		// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$rID4=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$response=$booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con); 
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con); 
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
	
}


if($action=="check_sales_order_approved")
{
	$last_update=return_field_value("is_approved","fabric_sales_order_mst","sales_booking_no='".trim($data)."'");
	echo $last_update;
	exit();	
}

if($action=="get_requisition_no_from_booking")
{
	$sql="SELECT distinct a.id
  	FROM sample_development_mst a, wo_non_ord_samp_booking_mst b ,wo_non_ord_samp_booking_dtls c
 	WHERE     a.status_active = 1
       AND a.is_deleted = 0
       AND b.status_active = 1
       AND b.is_deleted = 0
       and  c.status_active = 1
       AND c.is_deleted = 0
       and b.booking_no=c.booking_no
       and a.id=c.style_id
       AND b.id = $data ";
    $res=sql_select($sql);
    if(count($res))
    {
    	echo $res[0][csf('id')];
    }
    exit();
}
?>