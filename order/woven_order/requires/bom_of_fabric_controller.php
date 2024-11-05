<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	?>
	<script>
		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                 <th width="150" colspan="3">&nbsp;</th>
                    <th><?=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                  <th width="150" colspan="4">&nbsp;</th>
                </tr>
                <tr>
                    <th width="130">Buyer Name</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref </th>
                    <th width="100">Internal Ref </th>
                    <th width="100">File No </th>
                    <th width="100">Order No</th>
                    <th width="160">Ship. Date Range</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tr class="general">
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" ); ?></td>
                <td>
                	<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px" >
                	<input type="hidden" id="selected_job">
                </td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'bom_of_fabric_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1); ?>
                </td>
            </tr>
     	</table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$job_cond=""; $order_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'"; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'"; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'"; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[9]);
	$file_no = str_replace("'","",$data[10]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		$insert_year="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}
	
	$lib_variable_data=lib_variable_data($data[0]);
	$cm_cost_predefined_data=explode("___",$lib_variable_data);
	
	$cm_cost_predefined_method=$cm_cost_predefined_data[0];
	$cm_cost_editable=$cm_cost_predefined_data[1];
	$currier_cost_per=$cm_cost_predefined_data[3];
	
	$currier_cm_cost_data=$cm_cost_predefined_method.'___'.$cm_cost_editable.'___'.$cm_cost_predefined_data[2];
	
	$is_manula_approved=$cm_cost_predefined_data[4];
	$copy_quotation_id=$cm_cost_predefined_data[5];
	$budget_exceeds_quot_id=$cm_cost_predefined_data[6];
	$cost_control_source_id=$cm_cost_predefined_data[7];
	$trim_rate_source_id=$cm_cost_predefined_data[8];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);
	?>
	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" align="left" class="rpt_table" >
 			<thead>
 				<th width="40">SL</th>
 				<th width="50">Year</th>
 				<th width="50">Job No</th>               
 				<th width="120">Company</th>
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref. No</th>
 				<th width="100">Internal Ref</th>
 				<th width="100">File No</th>               
 				<th width="80">Job Qty.</th>
 				<th width="90">PO number</th>
                <th width="80">PO Quantity</th>
 				<th width="70">Shipment Date</th>
                <th width="70">Approve Status</th>
 				<th width="">Precost id</th>               
 			</thead>
 		</table>
        <div style="width:1150px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="list_view">  
 				<?
				$sql= "select $insert_year as year, a.id, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.quotation_id, a.currency_id, a.job_quantity, b.po_number, b.grouping, b.file_no, b.po_quantity, b.shipment_date, a.job_no, c.id as pre_id, c.exchange_rate, c.costing_per, c.approved  from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and a.job_no=b.job_no_mst and a.garments_nature=2 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer_id_cond $job_cond $style_cond $order_cond $file_no_cond $internal_ref_cond $year_cond order by a.id DESC";
				$result=sql_select($sql);
 				$i=1;
 				foreach ($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
 					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<?=$row[csf('job_no')].'_'.$row[csf('buyer_name')].'_'.$row[csf('quotation_id')].'_'.$row[csf('currency_id')].'_'.$row[csf('exchange_rate')].'_'.$row[csf('costing_per')].'_'.$row[csf('approved')].'_'.$row[csf('id')].'_'.$cost_control_source_id.'_'.$budget_exceeds_quot_id.'_'.$copy_quotation_id; ?>');"> 
                            <td width="40"><? echo $i; ?>  </td>  
                            <td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                            <td width="120"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('file_no')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('job_quantity')]; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td> 
                            <td width="80"><p><? echo $row[csf('po_quantity')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('shipment_date')]); ?></p></td> 
                            <td width="70"><p><? echo $pre_Cost_approv_status[$row[csf('approved')]]; ?></p></td>
                            <td><p><? echo $row[csf('pre_id')]; ?></p></td>
                        </tr> 
                        <? 
                        $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>
    <?php
	exit();
}

if ($action=="comment_popup")
{
	echo load_html_head_contents("Auto Mail Comments","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	// echo $job_no."=>".$txt_comments;
	?>
    <script type="text/javascript">
    	function fnc_close()
		{
			mail_comment= $("#txt_mail_comment").val();
			var job_no=<?="'".$job_no."'";?>
			// alert(unappv_request);
			document.getElementById('hidden_mail_comment').value=mail_comment;
			get_php_form_data( job_no+'**'+$("#txt_mail_comment").val(), "save_mail_comments", "bom_of_fabric_controller" );

			parent.emailwindow.hide();
		}
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
						<?
							$comment=sql_select("select mail_comments from  wo_pre_cost_mst where job_no='$job_no'");
						?>
                    	<textarea name="txt_mail_comment" id="txt_mail_comment" class="text_area" style="width:430px; height:100px;" maxlength="300" title="Maximum 300 Character"  ><? if(count($comment)>0){ echo $comment[0][csf('mail_comments')];}else{ echo $txt_comments;};?></textarea>
                        <input type="hidden" name="hidden_mail_comment" id="hidden_mail_comment" class="text_boxes /">
                    </td>
                </tr>
            </table>
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();fnc_fabric_yarn_cost_dtls(5);" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
     
		</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="save_mail_comments"){
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$data=explode("**",$data);

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}


		$field_array="mail_comments";
		$data_array="'".$data[1]."'";
		 $rID=sql_update("wo_pre_cost_mst",$field_array,$data_array,"job_no*status_active*is_deleted","'".$data[0]."'*1*0",1);		
		
		if($db_type==0)
		{
			if($rID )
			{
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
			if($rID )
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
if ($action=="yarn_cost_auto_mail_send")
{
	$data=explode("**",$data); $component_approved=0; $approved_message="";
	$sql=sql_select("select current_approval_status as approved from co_com_pre_costing_approval where cost_component_id=1 and job_no='$data[0]'");
	foreach($sql as $row){
		if($row[csf('approved')]==1 || $row[csf('approved')]==3) { 
			$component_approved=1; $approved_message="<marquee width='500'><p style='color:#f00;'> Approved</p></marquee> ";
		}
	}
	
	$mandatory_sql=sql_select("select variable_list, style_from_library, default_fabric_source, color_from_library, conversion_from_chart, rate_type from variable_order_tracking where status_active=1 and variable_list in (21,23,49,80) and company_name=$data[3]");
	$var_dia_width_type=2; $de_fab_source=1; $color_from_library=2; $conversion_from_chart=2;
	foreach($mandatory_sql as $row){
		if($row[csf('variable_list')]==80) $var_dia_width_type=$row[csf('style_from_library')];
		if($row[csf('variable_list')]==49) $de_fab_source=$row[csf('default_fabric_source')];
		if($row[csf('variable_list')]==23) $color_from_library=$row[csf('color_from_library')];
		if($row[csf('variable_list')]==21) $conversion_from_chart=$row[csf('conversion_from_chart')];
	}
	unset($mandatory_sql);
	if($var_dia_width_type==1) $dia_type='blue'; else $dia_type='black';
	?>

		<?php ob_start();	?>
     
	   <h2 style="width:1020px;" align="left" id="accordion_h_yarn" class="accordion_h">Subject : Yarn Count information of job No: <?=$data[0];?></h2>
	   <p align="left">Dear Concern,</p>
	   <p align="left">Please be informed that, Bellow yarn information are confirmed.</p>


       <div id="content_yarn_cost">
    	<fieldset style="width:1000px;">
        	<form id="yarnccost_4" autocomplete="off">
            	<table width="1000" cellspacing="0" class="rpt_table" border="0" id="tbl_yarn_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="60">Count</th>
                            <th width="100" class="must_entry_caption">Comp 1</th>
                            <th width="50" class="must_entry_caption">%</th>
                            <th width="100">Color</th>
                            <th width="110">Type</th>
                            <th width="80">Yarn Finish</th>
                        	<th width="80">Yarn Spinning System</th>
                        	<th width="80">Certification</th>
                            <th width="80" class="must_entry_caption">Cons Qty.</th>
                            <th width="120">Supplier</th>
                            <th width="50">Rate</th>
                            <th width="90">Amount</th>                           
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$lib_yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
					
					if($color_from_library==1)
					{
						$readonly="readonly='readonly'"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup($data[4],this.id)'";
					}
					else
					{
						$readonly=""; $plachoder=""; $onClick="";
					}

					$save_update=1;
                    $data_array=sql_select("select id, fabric_cost_dtls_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two,color, type_id, cons_ratio, cons_qnty, avg_cons_qnty,supplier_id, rate, amount, status_active, yarn_finish, yarn_spinning_system, certification from wo_pre_cost_fab_yarn_cost_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0 order by id");
					if(count($data_array)<1 && $data[2]==1  && $data[5]==2)
					{
						$data_array=sql_select("select id as pri_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty,supplier_id, rate, amount, status_active, yarn_finish, yarn_spinning_system from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$data[1]' and status_active=1 and is_deleted=0 order by id");
						$save_update=0;
					}
					if (count($data_array) < 1 && $data[2]==1 && $data[5]==5 )//Short Quotation V2
					{
						//$data_array=sql_select("select id, fabric_cost_dtls_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two,color, type_id, cons_ratio, cons_qnty, avg_cons_qnty,supplier_id, rate, amount, status_active, yarn_finish, yarn_spinning_system, certification from wo_pre_cost_fab_yarn_cost_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0 order by id");
						$data_array=sql_select("select id as pri_id, '' as fabric_cost_dtls_id, count as count_id, composition as copm_one_id, percent as percent_one, 0 as copm_two_id, 0 as percent_two, '' as color, yarn_type as type_id, 0 as cons_ratio, cons as cons_qnty, tot_cons as avg_cons_qnty, nominated_supp_multi as supplier_id, rate, value as amount, status_active, 0 as yarn_finish, 0 as yarn_spinning_system, 0 as certification from qc_fab_yarn_conv where qc_no='$data[1]' and type=2 and cons>0 and status_active=1 and is_deleted=0 order by id");
						//echo "select id as pri_id, '' as fabric_cost_dtls_id, count as count_id, composition as copm_one_id, percent as percent_one, 0 as copm_two_id, 0 as percent_two, '' as color, yarn_type as type_id, 0 as cons_ratio, cons as cons_qnty, tot_cons as avg_cons_qnty, nominated_supp_multi as supplier_id, rate, value as amount, status_active, 0 as yarn_finish, 0 as yarn_spinning_system, 0 as certification from qc_fab_yarn_conv where qc_no='$data[1]' and type=2 and cons>0 and status_active=1 and is_deleted=0 order by id";
						$save_update=0;
						// $sqlFab="select id, type, yarn_lib_id, fab_des, count, composition, percent, yarn_type, process, fab_id, cons, exper, tot_cons, rate, value,nominated_supp_multi from qc_fab_yarn_conv where qc_no='$qc_no' and cons_rate_dtls_id='$consRateId' and status_active=1 and is_deleted=0";
					}

					if ( count($data_array)>0)
					{
						$i=0;
						$tot_cons=0;
						foreach( $data_array as $row )
						{
							$i++;
							$tot_cons=$tot_cons+$row[csf("cons_qnty")];
							$disabled=0; $btn_disabled=0;
							
							if($approved==1) $disabled=1; else $disabled=0;
							if($component_approved==1)
							{
								$disabled=1; $btn_disabled=1;
							}
							
							$is_apply_last_update=$applyLastUpdateArr[$row[csf("fabric_cost_dtls_id")]]; $changedmsgcolor=""; $is_last_app_msg="";$is_last_app_msg_id=0;
							if($is_apply_last_update==2)
							{
								$is_last_app_msg="Color Size Changed.";
								$is_last_app_msg_id="1";
								$changedmsgcolor="Red";
							}
							if($row[csf("rate")]=="" || $row[csf("rate")]==0) $yarnchangeFound="Red"; else $yarnchangeFound="";
							
							$isDisBomOfFab=0; $cboDis=0; $cboDis2=0;
							if($data[6]==1)//Bom Of Fabric ISD-21-29440
							{
								$uid=$_SESSION['logic_erp']['user_id'];
								$sqlUserDept=sql_select("select b.department_name from user_passwd a, lib_department b where a.department_id=b.id and a.id='$uid' and a.valid = 1");
								$userDepartment=$sqlUserDept[0][csf('department_name')];
								//$userDepartment="Merchandising";
								$user_level=$_SESSION['logic_erp']["user_level"];
							}
							if($data[6]==1 && $user_level==2)//Bom Of Fabric and admin user ISD-21-29440
							{
								$isDisBomOfFab=1;
								$cboDis=0;
								$cboDis2=0; 
							}
							else if($data[6]==1 && $user_level!=2 && strtolower(trim($userDepartment))==strtolower("Knitting"))//Bom Of Fabric and Knitting Dept. user ISD-21-29440
							{
								$isDisBomOfFab=1;
								$cboDis=0;
								$cboDis2=1; 
							}
							else if($data[6]==1 && $user_level!=2 && strtolower(trim($userDepartment))==strtolower("Merchandising"))//Bom Of Fabric and Merchandising Dept. user ISD-21-29440
							{
								$isDisBomOfFab=1;
								$cboDis=1;
								$cboDis2=0; 
							}
							else if ($disabled!=0)
							{
								$isDisBomOfFab=1;
								$cboDis=$disabled;
								$cboDis2=$disabled;
							}
							//$cboDis=$disabled;
							?>
                            <tr id="yarncost_<?=$i; ?>" align="center">
                                <td><?=$lib_yarn_count[$row[csf("count_id")]]; ?> </td>
                                <td><?=$composition[$row[csf("copm_one_id")]] ?></td>
                                <td><?=$row[csf("percent_one")]; ?></td>
                                <td><?=$color_library[$row[csf("color")]]; ?></td>
                                <td><?=$yarn_type[$row[csf("type_id")]]; ?></td>
                                <td><?=$yarn_finish_arr[$row[csf("yarn_finish")]]; ?></td>
                                <td><?=$yarn_spinning_system_arr[$row[csf("yarn_spinning_system")]]; ?></td>
                                <td><?=$certification_arr[$row[csf("certification")]]; ?></td>
                                <td><?=number_format($row[csf("cons_qnty")],6,'.',''); ?></td>
                                <td><?=$supplier_library_yarn[$row[csf("supplier_id")]]; ?></td>
                                <td><?=$row[csf("rate")]; ?></td>
                                <td><?=$row[csf("amount")]; ?></td>                                                           
                            </tr>
                            <?
						}
					}
					else
					{
						$save_update=0;
						$data_array_p=sql_select("select id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,avg_cons_qnty,supplier_id from wo_pre_cost_fab_yarnbreakdown where job_no='$data[0]'  and status_active=1 and is_deleted=0  order by id");
						if ( count($data_array)>0)
					    {
							$i=0;
							$tot_cons=0;
							foreach( $data_array_p as $row_p )
							{
								$i++;
								$tot_cons=$tot_cons+$row_p[csf("cons_qnty")];
								?>
								<tr id="yarncost_<?=$i; ?>" align="center">
                                   <td><?=$lib_yarn_count[$row_p[csf("count_id")]]; ?></td>
                                   <td><?=$composition[$row_p[csf("copm_one_id")]]; ?></td>
                                   <td><?=$row_p[csf("percent_one")]; ?></td>
                                   <td><?=$row_p[csf("color")];?></td>
                                   <td><?=$yarn_type[$row_p[csf("type_id")]]; ?></td>
                                   <td><?=$yarn_finish_arr[$row_p[csf("type_id")]]; ?></td>
                                   <td><?=$yarn_spinning_system_arr[$row_p[csf("type_id")]]; ?></td>
                                   <td><?=$certification_arr[$row_p[csf("security")]]; ?></td>
                                   <td><?=number_format($row_p[csf("cons_qnty")],6,'.',''); ?> </td>
                                   <td><?=$supplier_library_yarn[$row_p[csf("supplier_id")]]; ?></td>
                                 
                                </tr>
								<?
							}
						}
						else
						{
							?>
							<tr id="yarncost_1" align="center">
                                <td><?=create_drop_down( "cbocount_1", 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Item --", $row_p[csf("count_id")], '','','' ); ?></td>
                                <td><?=$composition[$row_p[csf("copm_one_id")]]; ?></td>
                                <td><input type="text" id="percentone_1" name="percentone_1" class="text_boxes" style="width:38px" onChange="control_composition( 1, this.id, 'percent_one');" value="100" readonly /></td>
                                <td><input type="text" id="color_1" name="color_1" class="text_boxes" style="width:88px" <?=$onClick; echo $readonly; echo $plachoder; ?> value="<? //echo $row_p[csf("color")]; ?>" /></td>
                                <td><?=$yarn_type[$row_p[csf("type_id")]]; ?></td>
                                <td><?=$yarn_finish_arr[$row_p[csf("yarn_finish")]]; ?></td>
                                <td><?=$yarn_spinning_system_arr[$row_p[csf("yarn_spinning_system")]]; ?></td>
                                <td><?=$certification_arr[$row_p[csf("security")]]; ?></td>
                                <td><? echo number_format($row_p[csf("cons_qnty")],6,'.',''); ?>    </td>
                                <td><? echo $supplier_library_yarn[$row_p[csf("supplier_id")]]; ?></td>
                               
                            </tr>
                        <?
						}
					}
					?>
						
                	</tbody>
					<tfoot>
                        <tr>
                        	<th width="60">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                        	<th width="80">&nbsp;</th>
                        	<th width="80">SUM :</th>
                            <th width="80"><?=number_format($tot_cons,6,'.',''); ?></th>
                            <th width="120">&nbsp;</th>
                            <th width="50">&nbsp;</th>
							<th width="90">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
              
            </form>
        </fieldset>
        </div>
		<?
		$user_id=$_SESSION['logic_erp']['user_id'];
		 $user_details=sql_select("select user_full_name,designation  from user_passwd  where id=$user_id order by id asc");
	
		?>
		<p align="left"><b>Comments:-</b><?=$data[5];?></p>
		<p align="left"><b>Sending By </b></p>
		<p align="left"><?=$user_details[0][csf('user_full_name')];?></p>
		<p align="left"><?=$user_details[0][csf('designation')];?></p>
		<?php
			$emailBody=ob_get_contents();
			
			//ob_clean();
			//if($is_mail_send==1){
				/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
				$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
				if($req_approved && $is_approved==1){
					$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
				}
				elseif($req_approved && $is_approved==0){
					$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
				}
			*/		
				
				$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
				$mail_sql_res=sql_select($sql);
				
				$mailArr=array();
				foreach($mail_sql_res as $row)
				{
					$mailArr[$row[EMAIL]]=$row[EMAIL]; 
				}
				
				$supplier_id=$nameArray[0][csf('supplier_id')];
				$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

				
				$mailArr=array();
				if($mail_id!=''){$mailArr[]=$mail_id;}
				if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
				
				$to=implode(',',$mailArr);
				$subject="BOM of Fabric Auto Mail";
				
				
				if($to!=""){
					require_once('../../../mailer/class.phpmailer.php');
					require_once('../../../auto_mail/setting/mail_setting.php');
					$header=mailHeader();
					sendMailMailer( $to, $subject, $emailBody );
				}
		//}
		?>
     
       
	<?
	exit();
}

if ($action=="save_update_delet_fabric_yarn_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$is_copy_quatation=str_replace("'","",$copy_quatation_id);
	/*if($is_copy_quatation==1)
	{
		$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and b.page_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		$app_nessity=2; $validate_page=0; $allow_partial=2;
		foreach($sql as $row){
			$app_nessity=$row[csf('approval_need')];
			$validate_page=$row[csf('validate_page')];
			$allow_partial=$row[csf('allow_partial')];
		}
		if(str_replace("'","",$txt_cost_control_source)==2)
		{
			$sql=sql_select("select approved from wo_price_quotation where id=$txt_quotation_id");
			$quatation_approved=2;
			foreach($sql as $row){
				$quatation_approved=$row[csf('approved')];
			}
		
			if($app_nessity==1  && $validate_page==2){
				if($quatation_approved==2 || $quatation_approved==0 || $quatation_approved==3){
					echo "quataNotApp**".str_replace("'","",$txt_job_no);
					disconnect($con);die;
				}
			}
		}
	}
	
	$component_approved=return_field_value("current_approval_status", "co_com_pre_costing_approval", "job_no=$update_id and cost_component_id=1");
	if($component_approved==3){
		$component_approved=1;
	}
	$approved=0; $ready_to_approve=0;
	$sql=sql_select("select approved, ready_to_approved from wo_pre_cost_mst where job_no=$update_id and status_active=1 and is_deleted=0");
	foreach($sql as $row){
		//if($row[csf('approved')]==3) $approved=1; else $approved=$row[csf('approved')];
		$approved=$row[csf('approved')];
		$ready_to_approve=$row[csf('ready_to_approved')];
	}
	if($approved==1 || $component_approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
	}
	if($approved==3){
		echo "papproved**".str_replace("'","",$update_id);
		disconnect($con);die;
	}
	if($ready_to_approve==1){
		echo "readyapproved**".str_replace("'","",$update_id);
		disconnect($con);die;
	}*/
	
	$sqlPurReq=sql_select("select a.requ_no from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	//echo "10**select a.requ_no from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; die;
	$purReqArr=array();
	
	foreach($sqlPurReq as $prow)
	{
		$purReqArr[$prow[csf('requ_no')]]=$prow[csf('requ_no')];
	}
	
	if(implode(",",$purReqArr)!="")
	{
		echo "purReq**".implode(",",$purReqArr);
		disconnect($con);die;
	}

	if($operation==1)
	{
	    $con = connect();
		if($db_type==0) mysql_query("BEGIN");

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array_up="job_no*count_id*copm_one_id*type_id*updated_by*update_date*status_active*is_deleted";
		for ($i=1; $i<=$total_row; $i++)
		{
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $cbotype="cbotype_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			 
			if(str_replace("'",'',$$updateidyarncost)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidyarncost);
				$data_array_up[str_replace("'",'',$$updateidyarncost)] =explode(",",("".$update_id.",".$$cbocount.",".$$cbocompone.",".$$cbotype.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0"));
			}
		}
		$flag=1;
		$rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_fab_yarn_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($rID==1) $flag=1; else $flag=0;
		//echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
		//echo "10**".bulk_update_sql_statement( "wo_pre_cost_fab_yarn_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ); check_table_status( $_SESSION['menu_id'],0); die;
		if($flag==1)
		{
			$sql_quo_yarn_dtls_del="insert into wo_pre_cost_bomoffabric( id, yarnid, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, yarnupdate_by, yarndelete_date)
	select
			'', id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$update_id." and status_active=1 and is_deleted=0";

			$rID_de1=execute_query($sql_quo_yarn_dtls_del,0);
			if($rID_de1==1) $flag=1; else $flag=0;
		}

		//=======================sum End =================
 		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".$update_id."**".$flag."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$update_id."**".$flag."**".$tot_com_amount;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".$update_id."**".$flag."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id."**".$flag."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
}

function lib_variable_data($data)
{
	$copy_quotation=$cm_cost_method=$cm_cost_editable=$compulsory=$is_manual_app=$currier_cost_per_percent=$budget_exceeds_quot=$cost_control_source=$trim_rate_source=0;
	//$cm_cost_method=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=22 and status_active=1 and is_deleted=0");
	$cm_cost_sql=sql_select("select variable_list, copy_quotation, commercial_cost_percent, cm_cost_method, editable, budget_exceeds_quot, cm_cost_compulsory, cost_control_source, pre_cost_approval,trim_rate from variable_order_tracking where company_name=$data  and variable_list in (20,22,37,41,53,57,35) and status_active=1 and is_deleted=0");
	foreach($cm_cost_sql as $row)
	{
		if($row[csf('variable_list')]==20)
		{
			if($row[csf('copy_quotation')]=="") $copy_quotation=0; else $copy_quotation=$row[csf('copy_quotation')];
		}
		else if($row[csf('variable_list')]==22)
		{
			if($row[csf('cm_cost_method')]=="") $cm_cost_method=0; else $cm_cost_method=$row[csf('cm_cost_method')];
			if($row[csf('editable')]=="") $cm_cost_editable=0; else $cm_cost_editable=$row[csf('editable')];
			if($row[csf('cm_cost_compulsory')]=="") $compulsory=0; else $compulsory=$row[csf('cm_cost_compulsory')];
		}
		else if($row[csf('variable_list')]==37)
		{
			if($row[csf('budget_exceeds_quot')]=="") $budget_exceeds_quot=0; else $budget_exceeds_quot=$row[csf('budget_exceeds_quot')];
		}
		else if($row[csf('variable_list')]==41)
		{
			if($row[csf('pre_cost_approval')]=="") $is_manual_app=0; else $is_manual_app=$row[csf('pre_cost_approval')];
		}
		else if($row[csf('variable_list')]==53)
		{
			if($row[csf('cost_control_source')]=="") $cost_control_source=0; else $cost_control_source=$row[csf('cost_control_source')];
		}
		else if($row[csf('variable_list')]==57)
		{
			if($row[csf('cm_cost_compulsory')]=="") $currier_cost_per_percent=0; else $currier_cost_per_percent=$row[csf('commercial_cost_percent')];
		}
		else if($row[csf('variable_list')]==35)
		{
			if($row[csf('trim_rate')]=="") $trim_rate_source=0; else $trim_rate_source=$row[csf('trim_rate')];
		}
	}
	return $cm_cost_method.'___'.$cm_cost_editable.'___'.$compulsory.'___'.$currier_cost_per_percent.'___'.$is_manual_app.'___'.$copy_quotation.'___'.$budget_exceeds_quot.'___'.$cost_control_source.'___'.$trim_rate_source;
	exit();
}
