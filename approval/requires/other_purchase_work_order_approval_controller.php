<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$menu_id=$_SESSION['menu_id'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

if($action=='user_popup')
{
	echo load_html_head_contents("Approval User Info","../../",1, 1,'',1,'');
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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=628 order by b.sequence_no";
			 //echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
    $data = explode('_',$data); 
    echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(1,7) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
    //and b.party_type =9
    exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id=($alter_user_id!='')?$alter_user_id:$user_id;
   
    $company_name=str_replace("'","",$cbo_company_name);
    $approval_type=str_replace("'","",$cbo_approval_type);
    $cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
    if($cbo_supplier_id!=0){$supplier_cond="and a.supplier_id=$cbo_supplier_id";}
    //echo $cbo_supplier_id; die;
    $txt_wo_no=str_replace("'","",$txt_wo_no);
	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="") $date_cond=" and a.wo_date between $txt_date_from and $txt_date_to"; else $date_cond="";
	
   
	if($company_name>0){$all_company_arr[$company_name]=$company_name;}
	else{
		$all_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}
	
	
?>
<form name="requisitionApproval_2" id="requisitionApproval_2">
    <fieldset style="width:740px; margin-top:10px">
    <legend>Other Purchase Work Order Approval</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table"  align="left" >
            <thead>
                <th width="50"></th>
                <th width="60">SL</th>
                <th width="60">Company</th>
                <th width="150">Work Order No</th>
                <th width="120">Supplier</th>
                <th width="120">Work Order Date</th>
                <th>Delivery Date</th>
                <? if($approval_type==1){?>
                <? }else{ ?>
                <th width="80">Refusing Cause</th>
                <? } ?>
            </thead>
        </table>
        <div style="width:760px; overflow-y:scroll; max-height:330px; margin:0 auto;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
     <?	
	
	
	$i=1;
	foreach($all_company_arr as $company_name=>$company){
	
	
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
    $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted=0","seq");
    
    $OtherUserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');
    if($user_sequence_no=="")
    {
        echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pro Forma Invoice.</font>";
        die;
    }
    $all_category = array(8,9,10,15,16,17,18,19,20,21,22,32,34,36,35,37,38,39,23,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,69,70,89,90,91,92,93,94);

    $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    $user_crediatial_item_cat_cond = "";
    if($item_cate_id != "")
    {
        $category_array = explode(",",$item_cate_id);
        $user_crediatial_item_cat_cond = " and c.item_category_id in ($item_cate_id)";
        $user_crediatial_item_cat_cond2 = " and b.item_category_id in ($item_cate_id)";
    }else{
        $category_array = $all_category;
    }
      
    $reference_res = sql_select("SELECT a.id as booking_id , b.item_category_id,c.approved_by
    FROM wo_non_order_info_mst a, wo_non_order_info_dtls b,approval_history c
    WHERE a.id = b.mst_id and a.id = c.mst_id and a.company_name=$company_name $supplier_cond and a.entry_form = 147 and a.is_approved=1 and a.status_active=1 and a.ready_to_approved =1 and c.entry_form = 17 and a.is_deleted=0 
    ORDER by a.id");

    foreach($reference_res as $value)
    {
        if($reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] == "")
        {
            $reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] = $value[csf("booking_id")];
        }else{
            $reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] .= ",". $value[csf("booking_id")];
        }
        
    }

    if($approval_type == 0) // unapproval process start
    {
        if($user_sequence_no == $min_sequence_no) // First user
        {     
            if($db_type==0)
            {
                $select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
            }else{
                $select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
            }

            $sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
            FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
            WHERE a.id = b.mst_id and a.entry_form=147 and a.company_name=$company_name $user_crediatial_item_cat_cond2 $supplier_cond and a.is_approved=$approval_type and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0 and b.item_category_id not in(1,4,5,6,7,11,23) and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond
            order by a.id";
            // echo $sql; 
        }

        else // Next user
        {
            $sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
            if($sequence_no=="") // bypass if previous user Yes
            {
                $sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
                from wo_non_order_info_mst a, wo_non_order_info_dtls b 
                where a.id =b.mst_id and a.entry_form=147 and a.company_name=$company_name $user_crediatial_item_cat_cond2 $supplier_cond and b.item_category_id not in(1,4,5,6,7,11,23) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond
                order by a.id";
                //echo $sql;
            }

            else // bypass No
            {
                $user_sequence_no=$user_sequence_no-1;
                // echo $sequence_no.'Tipu';
                if($sequence_no==$user_sequence_no) 
                {
                    // echo $sequence_no.'=='.$user_sequence_no.'if';
                    $sequence_no_by_pass=$sequence_no;
                    $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
                }
                else
                {
                    // echo $sequence_no.'=='.$user_sequence_no.'else';
                    if($db_type==0) 
                    {
                        $sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");

                    }
                    else if($db_type==2) 
                    {
                        $sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
                    }
                    
                    if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
                    else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
                }
                $sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
                from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
                where a.id=b.mst_id and a.id = c.mst_id and a.entry_form=147 and a.ready_to_approved =1 and b.entry_form=17 and a.company_name=$company_name $user_crediatial_item_cat_cond $supplier_cond and c.item_category_id not in(1,4,5,6,7,11,23) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond $sequence_no_cond
                order by a.id";
                // echo $sql;
            }
        }
    }
    else // approval process start
    {
        $sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
        $sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
        FROM wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
        WHERE a.id=b.mst_id and a.id = c.mst_id and a.entry_form=147 and b.entry_form=17 and a.company_name=$company_name $user_crediatial_item_cat_cond $supplier_cond and c.item_category_id not in(1,4,5,6,7,11,23) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond $sequence_no_cond
        ORDER by a.id";
      
    }

	  // echo $sql;
	
	
	
	
	
                        $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =30 and is_deleted=0 and status_active=1");
                    	 
						$format_idsArr=explode(",",$print_report_format);
						$format_ids=$format_idsArr[0];

                            // echo ($sql);
                            $j=0;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

                                $variable='';
    							if($format_ids==84)
    							{
    								$action="spare_parts_work_order_print2";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
    							}
    							else if($format_ids==85)
    							{
    								$action="spare_parts_work_order_print3";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
    							}
    							else if($format_ids==134)
    							{
    								$action="spare_parts_work_order_po_print";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";

    							}
								else if($format_ids==354)
    							{
    								$action="spare_parts_work_print_8";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";

    							}
                                else if($format_ids==235)
                                {

                                    $action="spare_parts_work_order_print9";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                } else if($format_ids==274)
                                {

                                    $action="spare_parts_work_order_print10";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }

                                else if($format_ids==227)
                                {

                                    $action="spare_parts_work_order_print8";
                                    $type=8;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==191)
                                {

                                    $action="spare_parts_work_print_urmi";
                                    $type=7;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==137)
                                {

                                    $action="spare_parts_work_print";
                                    $type=5;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==732)
                                {

                                    $action="spare_parts_work_order_po_print";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==129)
                                {

                                    $action="spare_parts_work_print";
                                    $type=6;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==430)
                                {

                                    $action="spare_parts_work_order_po_print2";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }

                                else 
                                {
                                    $action="spare_parts_work_order_print2";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                }
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									if($db_type==0)
                                    {
                                    $app_id=return_field_value("max(id) as id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='17' and approved_by = '$user_id' order by id desc limit 0,1","id");
                                    }
                                    if($db_type==2 || $db_type==1)
                                    {
                                    $app_id=return_field_value("max(id) as id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='17' and approved_by = '$user_id' ","id"); //and ROWNUM=1
                                    }
									$value=$row[csf('id')]."**".$app_id;
								}

								
								//echo "select max(id) as id from approval_history where mst_id ='".$row[csf('id')]."' and entry_form='17' and approved_by = '$user_id' ";
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" style="width:30px;" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                        
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('id')]."*".$app_id.'*'.$company_name; ?>" />
                                        
                                        
                                        
                                    </td>
									<td width="60" align="center"><? echo $i; ?></td>
                                    <td width="60"><?=$company_arr[$row[csf('company_name')]]; ?></td>
									<td width="150" align="center">
                                    	<p><? echo $variable; //$row[csf('wo_number_prefix_num')]; ?></p>
                                    </td>
                                    <td width="120"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="120" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
									<? if($approval_type==1){?>
                  
                                    <? }else{ ?>
                                        <td width="80" align="center"> <input style="width:60px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/other_purchase_work_order_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
                                    <? } ?>  
								</tr>
								<?
								$i++;$j;
							}
						}//end company loof
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" align="left">
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


if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	?>
    <script>
 	var permission='<? echo $permission; ?>';

	function set_values( cause )
	{
		var refusing_cause = document.getElementById('txt_refusing_cause').value;
		if(refusing_cause == '')
		{
			document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			alert("Please save refusing cause first or empty");
			return;
		}

	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Refusing Cause')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id;
			http.open("POST","other_purchase_work_order_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				alert("Refusing Cause saved successfully");
				document.getElementById('txt_refusing_cause').value =response[1];
				parent.emailwindow.hide();
			}
			else
			{
				alert("Refusing Cause not saved");
				return;
			}
		}
	}

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:470px;">
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Refusing Cause</td>
					<td >
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
				        ?>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>
				</tr>
		   </table>
			</form>
		</fieldset>
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}

if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		$entry_form=17;
		
		
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =$entry_form and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",$entry_form,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_non_order_info_mst set ready_to_approved=0 ,IS_APPROVED=0, updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where id='$quo_id'");
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id']." ,un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =$entry_form and current_approval_status=1");
		}
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}




if ($action=="approve")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    //$user_id=7;
    $con = connect();
    if($db_type==0)
    {
        mysql_query("BEGIN");
    }
	
	$appCompanyArr=array();$appIdArr=array();$appNoArr=array();
	foreach(explode(',',$mst_id_company_ids) as $ic){
		list($bno,$bid,$company)=explode('*',$ic);
		$appCompanyArr[$company]=$company;
		$appIdArr[$company][$bid]=$bid;
		$appNoArr[$company][$bno]=$bno;
	}
	//print_r($appIdArr);die;
	
	$flag=0;$msg='';  $response=$req_nos;
	foreach($appCompanyArr as $cbo_company_name){
	$req_nos=implode(",",$appNoArr[$cbo_company_name]);
	$approval_ids=implode(',',$appIdArr[$cbo_company_name]);
	
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id=($alter_user_id!='')?$alter_user_id:$user_id;
	
	
	
    
    
    $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id and company_id = $cbo_company_name and is_deleted = 0");
   
    $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");
   

    if($approval_type==0)
    {
        

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

        $reqs_ids=explode(",",$req_nos);

        $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved, approved_by, approved_date";
        
        $i=0;
        $id=return_next_id( "id","approval_history", 1 ) ;
        
        $approved_no_array=array();$data_array='';
        foreach($reqs_ids as $val)
        {
            $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=17","approved_no");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";
             
            $data_array.="(".$id.",17,".$val.",".$approved_no.",".$user_sequence_no.",1,".$partial_approval.",".$user_id.",'".$pc_date_time."')";            
            
            $approved_no_array[$val]=$approved_no;
                
            $id=$id+1;
            $i++;
        }
        
        

        $approved_string="";
    
        foreach($approved_no_array as $key=>$value)
        {
            $approved_string.=" WHEN $key THEN $value";
        }
        
        $approved_string_mst="CASE id ".$approved_string." END";
        $approved_string_dtls="CASE mst_id ".$approved_string." END";
        
        $sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name,buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
            select
            '', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)"; 



         $sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
            select
            '', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";
                

        $rID=sql_multirow_update("wo_non_order_info_mst","is_approved",$partial_approval,"id",$req_nos,0);    
        if($rID) $flag=1; else $flag=0;

        //echo $approval_ids;die;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($req_nos)";
        $rIDapp=execute_query($query,1);
        if($flag==1) 
        {
            if($rIDapp) $flag=1; else $flag=0; 
        }

        $rID2=sql_insert("approval_history",$field_array,$data_array,0);
        if($flag==1) 
        {
            if($rID2) $flag=1; else $flag=0; 
            
        }
        $rID3=execute_query($sql_insert,0);
        if($flag==1) 
        {
            if($rID3) $flag=1; else $flag=0; 
            
        }       
        $rID4=execute_query($sql_insert_dtls,1);
        if($flag==1) 
        {
            if($rID4) $flag=1; else $flag=0; 
            
        } 
        
        if($flag==1) $msg='19'; else $msg='21'; 
		
		
		 //echo '21*'.$rID.'*'.$rIDapp.'*'.$rID2.'*'.$rID3.'*'.$rID4.',';
		//echo $data_array.'==';
		
		
		
    }
    else
    {
 		
        $rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved",'0*2',"id",$req_nos,0);
        if($rID) $flag=1; else $flag=0;
        
        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($req_nos)";
        $rID2=execute_query($query,1);
        if($flag==1) 
        {
            if($rID2) $flag=1; else $flag=0; 
        } 
            
        $data=$user_id."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
        $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
        if($flag==1) 
        {
            if($rID3) $flag=1; else $flag=0; 
        } 
        
      
	  // echo "10**".$rID.'**'.$rID2.'**'.$rID3;oci_rollback($con);die;
	   
	   
	   
	    $response=$reqs_ids;
        
        if($flag==1) $msg='20'; else $msg='22';
    }
    
    if($db_type==0)
    { 
        if($flag==1)
        {
            mysql_query("COMMIT");  
           // echo $msg."**".$response;
        }
        else
        {
            mysql_query("ROLLBACK"); 
           // echo $msg."**".$response;
        }
    }
    
    if($db_type==2 || $db_type==1 )
    {
        
        if($flag==1)
        {
            oci_commit($con);
           // echo $msg."**".$response;
        }
        else
        {
            oci_rollback($con);
           // echo $msg."**".$response;
        }
    }
	
	}//end company loof;
    
	
	echo $msg."**".str_replace("'","",$response);
	disconnect($con);
    die;
    
}

/*if ($action=="approve______")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//$approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val'","approved_no");
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
        $mst_id_approve_arr=array();
	$sql_data=sql_select("select max(approved_no) as approved_no,mst_id from approval_history group by mst_id");
	foreach($sql_data as $row)
	{
		$mst_id_approve_arr[$row[csf('mst_id')]]['approved_no']=$row[csf('approved_no')];
	}
	$approved_status_arr = return_library_array("select id, is_approved from wo_non_order_info_mst where id in($req_nos)","id","is_approved");
	$msg=''; $flag=''; $response='';

	if($approval_type==0)
	{
		$response=$req_nos;

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        //echo $is_not_last_user;die;
        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

		$reqs_ids=explode(",",$req_nos);
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date";
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;

		$approved_no_array=array();

		foreach($reqs_ids as $val)
		{
			$approved_no=$mst_id_approve_arr[$val]['approved_no'];
            $approved_status=$approved_status_arr[$val];
            if($approved_status == 0)
            {
                $approved_no=$approved_no+1;
                $approved_no_array[$val]=$approved_no;
            }
			if($i!=0) $data_array.=",";

			$data_array.="(".$id.",17,".$val.",".$approved_no.",".$user_sequence_no.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$i++;
		}

                if(count($approved_no_array)>0)
		{
		$approved_string="";

		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}

		$approved_string_mst="CASE id ".$approved_string." END";
		$approved_string_dtls="CASE mst_id ".$approved_string." END";

		 $sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
			select
			'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";



		 $sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
			select
			'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";
                }
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",$partial_approval,"id",$req_nos,0);
		//echo $req_nos;die;
		if($rID) $flag=1; else $flag=0;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($req_nos)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=20;

		}
                if(count($approved_no_array)>0)
		{
                    $rID3=execute_query($sql_insert,0);
                    if($flag==1)
                    {
                            if($rID3) $flag=1; else $flag=30;

                    }
                    $rID4=execute_query($sql_insert_dtls,1);
                    if($flag==1)
                    {
                            if($rID4) $flag=1; else $flag=10;

                    }
                }
		//echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4;die;

		if($flag==1) $msg='19'; else $msg='21';
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

			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		//echo "sql_multirow_update  approval_history (".$field_array.") Values ".$data_array."";die;
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$reqs_ids,0);

		if($rID) $flag=1; else $flag=0;

		$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,0);

                $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($reqs_ids)";
		$rID3=execute_query($query,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		} //echo $data;die;
		//echo "10**".$rID."=".$rID2."=".$rID3;die;
		$response=$reqs_ids;

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
			oci_commit($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;

}*/


?>
