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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=627 order by b.sequence_no";
			 //echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}


// if ($action=="load_supplier_dropdown")
// {
// 	$data = explode('_',$data);	
// 	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
// 	//and b.party_type =9
// 	exit();
// }
if ($action=="load_supplier_dropdown")
{

    $data=explode("_",$data);
    if($data[1]==3 || $data[1]==5){
        echo create_drop_down( "cbo_supplier_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );
    }else{
        echo create_drop_down( "cbo_supplier_id", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(5,8) and a.status_active=1 and a.is_deleted=0 and c.tag_company ='$data[0]' group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/stationary_work_order_controller');",0 );
    }
    
}




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id=($alter_user_id!='')?$alter_user_id:$user_id;
	
	$company_name=str_replace("'","",$cbo_company_name);
    $supplier_id=str_replace("'","",$cbo_supplier_id);

    $approval_type=str_replace("'","",$cbo_approval_type);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="") $date_cond=" and a.wo_date between $txt_date_from and $txt_date_to"; else $date_cond="";
	/*$signature_sql ="SELECT DISTINCT template_id FROM variable_settings_signature WHERE company_id=$company_name and report_id=55";
    $signature_res = sql_select($signature_sql);
    print_r($signature);*/
    $all_company_arr=array();
	if($company_name>0)
    {
        $all_company_arr[$company_name]=$company_name;
    }
	else
    {
		$all_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:740px; margin-top:10px">
        <legend>Stationary Work Order Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" align="left" >
                <thead>
                	<th width="50"></th>
                    <th width="60">SL</th>
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
            <div style="width:740px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?php
                        $i=1; $j=0;
                        foreach($all_company_arr as $company_name=>$company)
                        {
                            $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
                            $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted=0","seq");
                            // echo $user_sequence_no.'='.$min_sequence_no;
                            // echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";die;
                            $OtherUserCatCredFromIdArr=return_library_array( "select id, item_cate_id from  user_passwd",'id','item_cate_id');
                        
                            if ($supplier_id >0){
                                $supplier_cond=" and a.supplier_id=$supplier_id";
                            }else{
                                $supplier_cond="";
                            }

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
                            }
                            else $category_array = $all_category;
                    
                            $reference_res = sql_select("SELECT a.id as booking_id , b.item_category_id,c.approved_by
                            FROM wo_non_order_info_mst a, wo_non_order_info_dtls b,approval_history c
                            WHERE a.id = b.mst_id and a.id = c.mst_id and a.company_name=$company_name and a.entry_form = 146 and a.is_approved=1 and a.status_active=1 and a.ready_to_approved =1 and c.entry_form = 5 and a.is_deleted=0  
                            ORDER by a.id");

                            foreach($reference_res as $value)
                            {
                                if($reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] == "")
                                {
                                    $reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] = $value[csf("booking_id")];
                                }
                                else
                                {
                                    $reference_arr[$value[csf("approved_by")]][$value[csf("item_category_id")]] .= ",". $value[csf("booking_id")];
                                }                                
                            }

                            //echo $approval_type.'=='. $user_sequence_no .'=='. $min_sequence_no;die;

                            if($approval_type == 0) // unapproval process start
                            {
                                if($user_sequence_no == $min_sequence_no) // First user
                                {     
                                    if($db_type==0) $select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
                                    else $select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
                                    $sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id,a.payterm_id,a.remarks,a.contact,a.tenor
                                    FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
                                    WHERE a.id = b.mst_id and a.company_name=$company_name and a.entry_form = 146 and a.is_approved=$approval_type and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0  $date_cond  $supplier_cond
                                    order by a.id";
                                    // echo $sql;
                                }
                                else // Next user
                                { 
                                    $sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
                                  //  echo "select max(sequence_no) from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0";

                                    if($sequence_no=="") // bypass if previous user Yes
                                    {
                                        $sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id,a.payterm_id,a.remarks,a.contact,a.tenor
                                        from wo_non_order_info_mst a, wo_non_order_info_dtls b 
                                        where a.id =b.mst_id and a.company_name=$company_name and a.entry_form = 146 $user_crediatial_item_cat_cond2 and b.item_category_id not in(1,2,3,12,13,14) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond  $supplier_cond and a.id not in(select a.id from wo_non_order_info_mst a,APPROVAL_HISTORY b where a.id=b.mst_id and b.ENTRY_FORM=5 AND CURRENT_APPROVAL_STATUS = 1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%'  AND a.entry_form = 146 and a.is_approved in(0,3) and a.company_name=$company_name and a.IS_DELETED=0 and b.APPROVED_BY=$user_id)
                                        order by a.id";
                                        // echo $sql;
                                    }
                                    else // bypass No
                                    {
                                        $user_sequence_no=$user_sequence_no-1;
                                        //echo $sequence_no.' Tipu '.$user_sequence_no;
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
                                                $sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name  and IS_DELETED=0 and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
                                            }
                                            else if($db_type==2) 
                                            {
                                                $sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id  and IS_DELETED=0 and sequence_no between $sequence_no and $user_sequence_no and bypass in(1,2)","sequence_no");
                                            }
                                            
                                           // echo "select listagg(sequence_no,',') within group (order by sequence_no) as sequence_no from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1";
                                            
                                            if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
                                            else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
                                        }

                                        $sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id,a.payterm_id,a.remarks,a.contact,a.tenor
                                        from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
                                        where a.id=b.mst_id and a.id = c.mst_id and a.entry_form = 146 and a.ready_to_approved =1 and b.entry_form=5 and a.company_name=$company_name $user_crediatial_item_cat_cond and c.item_category_id not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved in (1,3) $sequence_no_cond $date_cond  $supplier_cond
                                        order by a.id";
                                        // echo $sql;
                                    }
                                }                
                                //echo $sql;                
                            }
                            else // approval process start
                            {
                                $sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
                                $sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id,a.payterm_id,a.remarks,a.contact,a.tenor
                                FROM wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
                                WHERE a.id=b.mst_id and a.id = c.mst_id and a.entry_form = 146 and b.entry_form=5 and a.company_name=$company_name $user_crediatial_item_cat_cond and c.item_category_id not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved in (1,3) $sequence_no_cond $date_cond  $supplier_cond
                                ORDER by a.id";
                                // echo $sql;
                            }
            
                            $sign_temp_id = 1;
                            // print_r($signature_res);
                            $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =61 and is_deleted=0 and status_active=1");
                            $format_ids=explode(",",$print_report_format);
                            // print_r($format_ids).'Tipu';
                          
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            
                            //echo $sql;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; 
                                else $bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
								    if($db_type==0)
									{
									   $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='5' order by id desc limit 0,1");
									}
								    if($db_type==2 || $db_type==1) 
									{
									   $app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and current_approval_status=1 and entry_form='5' and ROWNUM=1 order by id desc");
									}
									$value=$row[csf('id')]."**".$app_id;
								}

								$variable='';
                                if($format_ids[$j]==134)
                                {
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"generate_worder_report('".$row[csf('company_name')]."','".$row[csf('wo_number')]."','".$row[csf('item_category')]."','".$row[csf('supplier_id')]."','".$row[csf('wo_date')]."','".$row[csf('currency_id')]."','".$row[csf('wo_basis_id')]."','".$row[csf('pay_mode')]."','".$row[csf('source')]."','".$row[csf('delivery_date')]."','".$row[csf('attention')]."','".$row[csf('requisition_no')]."','".$row[csf('delivery_place')]."','".$row[csf('id')]."','".$row[csf('location_id')]."','".$row[csf('payterm_id')]."','".$row[csf('remarks')]."','".$row[csf('contact')]."','".$row[csf('tenor')]."','".$sign_temp_id."','".$format_ids[$j]."','stationary_work_print')\"> ".$row[csf('wo_number_prefix_num')]." <a/>";
                                }
                                else if($format_ids[$j]==66)
                                {
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"generate_worder_report('".$row[csf('company_name')]."','".$row[csf('wo_number')]."','".$row[csf('item_category')]."','".$row[csf('supplier_id')]."','".$row[csf('wo_date')]."','".$row[csf('currency_id')]."','".$row[csf('wo_basis_id')]."','".$row[csf('pay_mode')]."','".$row[csf('source')]."','".$row[csf('delivery_date')]."','".$row[csf('attention')]."','".$row[csf('requisition_no')]."','".$row[csf('delivery_place')]."','".$row[csf('id')]."','".$row[csf('location_id')]."','".$row[csf('payterm_id')]."','".$row[csf('remarks')]."','".$row[csf('contact')]."','".$row[csf('tenor')]."','".$sign_temp_id."','".$format_ids[$j]."','stationary_work_order_print')\"> ".$row[csf('wo_number_prefix_num')]." <a/>";
                                }
                                else if($format_ids[$j]==129)
                                {
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"generate_worder_report('".$row[csf('company_name')]."','".$row[csf('wo_number')]."','".$row[csf('item_category')]."','".$row[csf('supplier_id')]."','".$row[csf('wo_date')]."','".$row[csf('currency_id')]."','".$row[csf('wo_basis_id')]."','".$row[csf('pay_mode')]."','".$row[csf('source')]."','".$row[csf('delivery_date')]."','".$row[csf('attention')]."','".$row[csf('requisition_no')]."','".$row[csf('delivery_place')]."','".$row[csf('id')]."','".$row[csf('location_id')]."','".$row[csf('payterm_id')]."','".$row[csf('remarks')]."','".$row[csf('contact')]."','".$row[csf('tenor')]."','".$sign_temp_id."','".$format_ids[$j]."','stationary_work_order_print5')\"> ".$row[csf('wo_number_prefix_num')]." <a/>";
                                }
                                else if($format_ids[$j]==137)
                                {
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"generate_worder_report('".$row[csf('company_name')]."','".$row[csf('wo_number')]."','".$row[csf('item_category')]."','".$row[csf('supplier_id')]."','".$row[csf('wo_date')]."','".$row[csf('currency_id')]."','".$row[csf('wo_basis_id')]."','".$row[csf('pay_mode')]."','".$row[csf('source')]."','".$row[csf('delivery_date')]."','".$row[csf('attention')]."','".$row[csf('requisition_no')]."','".$row[csf('delivery_place')]."','".$row[csf('id')]."','".$row[csf('location_id')]."','".$row[csf('payterm_id')]."','".$row[csf('remarks')]."','".$row[csf('contact')]."','".$row[csf('tenor')]."','".$sign_temp_id."','".$format_ids[$j]."','stationary_work_order_print4')\"> ".$row[csf('wo_number_prefix_num')]." <a/>";
                                   
                                }
                                else if($format_ids[$j]==430)
                                {
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"generate_worder_report('".$row[csf('company_name')]."','".$row[csf('wo_number')]."','".$row[csf('item_category')]."','".$row[csf('supplier_id')]."','".$row[csf('wo_date')]."','".$row[csf('currency_id')]."','".$row[csf('wo_basis_id')]."','".$row[csf('pay_mode')]."','".$row[csf('source')]."','".$row[csf('delivery_date')]."','".$row[csf('attention')]."','".$row[csf('requisition_no')]."','".$row[csf('delivery_place')]."','".$row[csf('id')]."','".$row[csf('location_id')]."','".$row[csf('payterm_id')]."','".$row[csf('remarks')]."','".$row[csf('contact')]."','".$row[csf('tenor')]."','".$sign_temp_id."','".$format_ids[$j]."','stationary_work_order_po_print2')\"> ".$row[csf('wo_number_prefix_num')]." <a/>";
                                   
                                }
                                else
                                {
                                    $format_ids[$j] = "";
                                    /*$ac = "onClick='print_report(".$row[csf(company_name)]."*".$row[csf(id)]."','dyes_chemical_work_print', '../commercial/work_order/requires/dyes_and_chemical_work_order_controller')'";
                                    $variable='<td width="150">
                                        <p><a href="##" style="color:#000" '.$ac.'>'.$row[csf("wo_number_prefix_num")].'</a></p>
                                        </td>';*/
                                }
                                //if($variable=="") $variable="".$row[csf('yarn_dyeing_prefix_num')]."";
                                // echo $variable;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('id')]."*".$app_id.'*'.$company_name; ?>" />
                                    </td>   
									<td width="60" align="center"><? echo $i; ?></td>
                                    <td width="150" align="center"><p><? echo $variable ;//$row[csf('wo_number_prefix_num')]; ?></p></td>		
                                    <td width="120"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="120" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
								    <? if($approval_type==1){?>              
                                    <? }else{ ?>
                                    <td width="80" align="center"> <input style="width:60px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/stationary_work_order_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
                                    <? } ?>                                    
								</tr>
								<?
								$i++;
							}
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" align="left">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="3" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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
			http.open("POST","stationary_work_order_approval_controller.php",true);
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
		$entry_form=5;
		
		
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

if ($action=="approve") // NEW
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
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
    //print_r($appCompanyArr);die;
    foreach($appCompanyArr as $cbo_company_name)
    {
        $req_nos=implode(",",$appNoArr[$cbo_company_name]);
        //echo $req_nos.'system';
        $approval_ids=implode(',',$appIdArr[$cbo_company_name]);

    	$alter_user_id = str_replace("'","",$txt_alter_user_id);
    	$user_id=($alter_user_id!='')?$alter_user_id:$user_id; 	    	
    	
        $msg=''; $flag=''; $response='';
        $user_id_approval=$user_id;
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id and company_id = $cbo_company_name and is_deleted = 0");

        $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");
        //echo $user_sequence_no."Tipu="."page_id=$menu_id and user_id=$user_id";die; //OK
        if($approval_type==0)
        {
            $response=$req_nos;

            $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0"); 
    		//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0";die;	
    		//echo $is_not_last_user;die;
            if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;		
    		/*$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",1,"id",$req_nos,0);
            if($rID) $flag=1; else $flag=0;*/
            
            $reqs_ids=explode(",",$req_nos);

            // $field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date"; 
            $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";
            $i=0;
            $id=return_next_id( "id","approval_history", 1 ) ;
            
            $approved_no_array=array();
            $data_array="";       
            foreach($reqs_ids as $val)
            {
                $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=5","approved_no");
                $approved_no=$approved_no+1;
            
                if($i!=0) $data_array.=",";            
                // $data_array.="(".$id.",5,".$val.",".$approved_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
                $data_array.="(".$id.",5,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";            
                $approved_no_array[$val]=$approved_no;                
                $id=$id+1;
                $i++;
            }
            //print_r($data_array);die;
            /*$rID2=sql_insert("approval_history",$field_array,$data_array,0);
            if($flag==1) 
            {
                if($rID2) $flag=1; else $flag=0; 
            }*/ 
            
            $approved_string="";        
            foreach($approved_no_array as $key=>$value)
            {
                $approved_string.=" WHEN $key THEN $value";
            }
            
            $approved_string_mst="CASE id ".$approved_string." END";
            $approved_string_dtls="CASE mst_id ".$approved_string." END";
            
            $sql_insert="INSERT into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
            SELECT  
            '', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";
                    
            /* $rID3=execute_query($sql_insert,0);
            if($flag==1) 
            {
                if($rID3) $flag=1; else $flag=0; 
            } */
            
            $sql_insert_dtls="INSERT into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
            SELECT  
            '', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";
            
            $rID=sql_multirow_update("wo_non_order_info_mst","is_approved",$partial_approval,"id",$req_nos,0);    
            if($rID) $flag=1; else $flag=0;
            //echo "10**$flag";die;
            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=5 and mst_id in ($req_nos)";
            $rIDapp=execute_query($query,1);
            if($flag==1) 
            {
                if($rIDapp) $flag=1; else $flag=0; 
            } 
            
            /*if($approval_ids!="")
            {
                $rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
                if($rID) $flag=1; else $flag=0;
            }*/
        
            $rID2=sql_insert("approval_history",$field_array,$data_array,0);
            
            //echo $rID2;die;
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
            //echo "10**$flag";die;
            if($flag==1) $msg='19'; else $msg='21';        
        }
        else
        {
            $req_nos = explode(',',$req_nos);        
            $reqs_ids=''; $app_ids='';
            //echo $req_nos.'system';
            foreach($req_nos as $value)
            {
                $data = explode('**',$value);
                $reqs_id=$data[0];
                $app_id=$data[1];
                
                if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
                if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
            }

            $rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$reqs_ids,0);
            if($rID) $flag=1; else $flag=0;

            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=5 and mst_id in ($reqs_ids)";
            $rID2=execute_query($query,1);
            if($flag==1) 
            {
                if($rID2) $flag=1; else $flag=0; 
            } 
            
            $data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,0);
            if($flag==1) 
            {
                if($rID2) $flag=1; else $flag=0; 
            } 
            //echo $flag.'system';
            $response=$reqs_ids;        
            if($flag==1) $msg='20'; else $msg='22';
        }
    
        if($db_type==0)
        { 
            if($flag==1)
            {
                mysql_query("COMMIT");  
                //echo $msg."**".$response;
            }
            else
            {
                mysql_query("ROLLBACK"); 
                //echo $msg."**".$response;
            }
        }
        
        if($db_type==2 || $db_type==1 )
        {
            if($flag==1)
            {
                oci_commit($con);
                //echo $msg."**".$response;
            }
            else
            {
                oci_rollback($con);
                //echo $msg."**".$response;
            }
        }
    }//end company loof;
    echo $msg."**".str_replace("'","",$response);
    disconnect($con);
    die;    
}   
?>