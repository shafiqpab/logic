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

if($action=='user_popup')
{
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
		//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?> 
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script> 
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type =9
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") $user_id=$txt_alter_user_id;

	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}

	$txt_date=str_replace("'","",$txt_date);
	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0) $txt_date=change_date_format($txt_date,"yyyy-mm-dd"); else $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.wo_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.wo_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.wo_date='".$txt_date."'";
		else $date_cond = '';
	}
	// echo $date_cond;die;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
    $is_not_last_user = return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}

	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
		$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c where a.id = b.mst_id and a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id  and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and b.item_category_id=1 and a.is_approved in(1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and a.id=c.mst_id and c.entry_form=2 and a.entry_form=144 and c.current_approval_status=1 $sequence_no_cond group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id order by a.id";
		// echo $sql;
	}
	else if($approval_type==0)
	{

		/*$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.id = b.mst_id and a.company_name=$company_name and b.item_category_id=1 and a.is_approved=$approval_type and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0
		group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		order by a.id";*/

		$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$company_name");
        // echo $sequence_no;die('Tipu+Fiq-Add Company condition');
        //echo  "select max(sequence_no) from electronic_approval_setup where page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0";
		if($user_sequence_no==$min_sequence_no) // first approval authority
		{
			$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id
			from wo_non_order_info_mst a
			where a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0) and a.entry_form=144";
            // echo $sql;//die("with sumon");
		}
		else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
		{

			if($db_type==0)
			{
				$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id
				and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b","a.id=b.mst_id
				and a.company_name=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=2 and b.current_approval_status=1","batch_id");
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b",
				"a.id=b.mst_id and a.company_name=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=2 and
				b.current_approval_status=1","batch_id");
			}
			else
			{
				$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
				"electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");

				$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
				approval_history b","a.id=b.mst_id and a.company_name=$company_name  and b.sequence_no in ($sequence_no_by) and b.entry_form=2
				and b.current_approval_status=1","batch_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));

				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
				approval_history b","a.id=b.mst_id and a.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=2 and
				b.current_approval_status=1","batch_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}

			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);

			$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";

			/*$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where  a.id = b.mst_id and a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id  and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0,3) and a.entry_form=144 $booking_id_cond";*/

			$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id
			from wo_non_order_info_mst a
			where  a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id  and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0,3) and a.entry_form=144 $booking_id_cond";
			/*if($booking_id!="")
			{
				$sql.="UNION ALL
				SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where  a.id = b.mst_id and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and
				a.is_approved in (1,3) and a.id in($booking_id)";

			}*/
            //echo $sql;//a.is_approved=1    

		}
		else // bypass No
		{

			$user_sequence_no=$user_sequence_no-1;

			if($db_type==0)
			{
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and
				sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
			}
			else
			{
				$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
				"electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and
				is_deleted=0","sequence_no");
			}


			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
			else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
            //echo $sequence_no;die;
			$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id
			from wo_non_order_info_mst a, approval_history c
			where  a.id=c.mst_id and  a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and c.entry_form=2 and a.is_deleted=0 and a.status_active=1 and
			a.ready_to_approved = 1 and a.is_approved in(1,3) and c.current_approval_status=1 and a.entry_form=144 $sequence_no_cond";
            //echo $sql;//die("with kakku");
		}
	}
	else
	{

        /*if($is_not_last_user == ""){
			$sequence_no_cond = "  and c.id in ( select id from approval_history where approved_by not in( $user_id))";
		}else{
			$sequence_no_cond=" and c.approved_by='$user_id'";
		}*/

		/*$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
		where a.id = b.mst_id and a.id=c.mst_id and a.company_name=$company_name and b.item_category_id=1 and a.is_approved in(1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.entry_form=2 and c.current_approval_status =1  $sequence_no_cond
		group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		order by a.id";*/
		//echo $sql;//die("with bang bang");

		$sequence_no_cond=" and b.approved_by='$user_id'";
		$sql="SELECT a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id
		from  wo_non_order_info_mst a, approval_history b 
		where a.id=b.mst_id and b.entry_form=2 and b.current_approval_status=1 and a.company_name=$company_name $date_cond and a.supplier_id like $cbo_supplier_id and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved  in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and a.entry_form=144 $sequence_no_cond 
		order by id";
		// echo $sql;die("Tipu");
	}
		/*$sql="select a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.id = b.mst_id and a.company_name=$company_name and b.item_category_id=1 and a.is_approved=$approval_type and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0
		group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date
		order by a.id";*/
		//echo $sql;die;
		
	$submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');

	?>
    
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = (app_type==0)?'Not Appv. Cause':'Not Un-Appv. Cause';
			var page_link = 'requires/yarn_work_order_approval_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

	</script>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:950px; margin-top:10px">
        <legend>Yarn Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="921" class="rpt_table" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="70">Work Order No</th>
                    <th width="120">Supplier</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="100">WO Basis</th>
                    <th width="80">Source</th>
                    <th width="80">Pay Term</th>
                    <th width="80">Submitted By</th>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value='';
								if($approval_type==0) $value=$row[csf('id')];
								else
								{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='2' and un_approved_by=0 order by id desc");
									$value=$row[csf('id')]."**".$app_id;
								}
								 
								$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =45 and is_deleted=0 and status_active=1");
                            	$format_idss=explode(",",$print_report_format);    
       							//echo $format_ids;
       							foreach ($format_idss as $key => $format_ids) 
       							{
	                                if($format_ids == 78){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row[csf('id')]."*Yarn Purchase Order*0*3*".$row[csf('is_approved')]."*6&action=yarn_work_order_print' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 84){
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row[csf('id')]."*Yarn Purchase Order*1*0*".$row[csf('is_approved')]."&action=print_to_html_report' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 85){
	                                	//echo 'system';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row[csf('id')]."*Yarn Purchase Order*2*0*".$row[csf('is_approved')]."&action=print_to_html_report2' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }elseif($format_ids == 193){
	                                	//echo 'systemfalse';
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row[csf('id')]."*Yarn Purchase Order*4*0*".$row[csf('is_approved')]."&action=print_to_html_report4' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                                }else{
	                                    $variable = "<a href='../commercial/work_order/requires/yarn_work_order_controller.php?data=$company_name*".$row[csf('id')]."*Yarn Purchase Order*0*1*".$row[csf('is_approved')]."&action=yarn_work_order_print5' style='color:#000' target='_blank'>". $row[csf('wo_number_prefix_num')]."</a>";
	                            	}
                            	}
								if($row[csf('updated_by')]=="" || $row[csf('updated_by')]==0) $row[csf('updated_by')]=$row[csf('inserted_by')];
								
                            	?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                        
										<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                        
                                        
                                    </td>
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="70"><? echo $variable; ?></td>
                                    <td width="120" style="word-break:break-all"><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
									<td width="70" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td width="70" align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
                                    
                                    <td width="100" style="word-break:break-all"><? echo $wo_basis[$row[csf('wo_basis_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $source[$row[csf('source')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $pay_term[$row[csf('payterm_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $submittedByArr[$row[csf('updated_by')]]; ?></td>
                                    <td align="center">
                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i;?>)"></td>
								</tr>
								<?
								$i++;
							}
							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="921" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	list($wo_id,$app_type,$app_cause,$approval_id)=explode('_',$data);

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=2 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
		}
	}

	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","yarn_work_order_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing();
				 //alert(http.responseText);

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}

		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","yarn_work_order_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;

		}

		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split('**');
				release_freezing();
			}
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="cause_1" id="cause_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('cause_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('cause_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	

	if($approval_type==0)
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "10**reza_".$approved_no_history.'_'.$approved_no_cause; die;
			

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				 //echo "10**".$data_array; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
					
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			
			

		}

		if ($operation==1)  // Update Here
		{

		}

	}//type=0
	if($approval_type==1)
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

			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=2 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");


			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=2 and mst_id=$wo_id and approved_by=$user_id");

			if($unapproved_cause_id=="")
			{

				//echo "shajjad_".$unapproved_cause_id; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",2,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			else
			{

				//echo "10**entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=2 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*2*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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

	}//type=1
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
	// $user_id = $_SESSION['logic_erp']['user_id'];
	$msg=''; $flag=''; $response='';

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($_REQUEST['txt_alter_user_id']!="") $user_id_approval=$_REQUEST['txt_alter_user_id'];
	else $user_id_approval=$user_id;

	 //echo $user_id_approval.'User ID is '.$user_id.'In session '.$_SESSION['logic_erp']['user_id']; die();
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
    //echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";die;

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

    //echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";die;


	if($approval_type==0) //Approve button
	{
		$response=$req_nos;

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
        //echo "select sequence_no from electronic_approval_setup a where  a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 ";die;

        //echo $is_not_last_user;die;
        if($is_not_last_user ==""){
            $partial_approval=1;
        }else{
            // if($user_sequence_no == $min_sequence_no ){
            //     $partial_approval=3;
            // }else{
                $partial_approval=3;
            //}
        }

		$flag=1;


		$reqs_ids=explode(",",$req_nos);
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status, inserted_by, insert_date";
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;

		$approved_no_array=array();

		foreach($reqs_ids as $val)
		{
			$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
			$approved_no=$approved_no+1;

			if($i!=0) $data_array.=",";

			$data_array.="(".$id.",2,".$val.",".$approved_no.",".$user_id_approval.",'".$pc_date_time."',".$user_sequence_no.",1,".$user_id.",'".$pc_date_time."')";

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

		$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
			select
			'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";

		$rID3=execute_query($sql_insert,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
			select
			'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";

		$rID4=execute_query($sql_insert_dtls,1);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=2 and mst_id in ($req_nos)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved","$partial_approval","id",$req_nos,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

        $rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

       echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';
	}
	else if($approval_type==1)
	{
		$req_nos = explode(',',$req_nos);
        //print_r($req_nos);die;
		$reqs_ids=''; $app_ids='';

		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
            //print_r($data);die;
			$reqs_id=$data[0];
			$app_id=$data[1];

			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$reqs_ids,0);

		if($rID) $flag=1; else $flag=0;

		// $data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		// $rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,0);
		$data=$user_id_approval."*'".$pc_date_time."'*0*".$user_id."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date*current_approval_status*updated_by*update_date",$data,"id",$app_id,0);

        //echo $rID2;die;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}


		$response=$reqs_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=2 and mst_id in ($req_nos) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		/*$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
		if($db_type==2 && $book_ids>1000)
		{
			$bookingidCond=" and (";
			$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
			foreach($bookingnoIdArr as $ids)
			{
				$ids=implode(",",$ids);
				$bookingidCond.=" mst_id in($ids) or"; 
			}
			$bookingidCond=chop($bookingidCond,'or ');
			$bookingidCond.=")";
		}
		else $bookingidCond=" and mst_id in($booknoId)";*/ 
		
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$req_nos,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=2 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';
	}

	//echo "10**".$rID.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$app_ids;die;

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
?>
