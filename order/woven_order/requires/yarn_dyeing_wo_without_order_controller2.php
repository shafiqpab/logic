<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	include('../../../includes/common.php');
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];
	$user_id=$_SESSION['logic_erp']['user_id'];
	$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
//$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );



	if ($action=="variable_chack")
	{
		extract($_REQUEST);
	//echo "$company";
		$company=str_replace("'","",$company);
		$variable_setting=return_field_value("color_from_library","variable_order_tracking","variable_list=24 and company_name=$company");
		echo $variable_setting;
		exit();

	}
	if($action=="print_button_variable_setting")
	{
		$print_report_format=0;
		$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=29 and is_deleted=0 and status_active=1");
		echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
		echo "print_report_button_setting('".$print_report_format."');\n";
		exit();
	}
	if ($action=="load_drop_down_sample")
	{

		$sql="Select b.id, b.sample_name from  lib_sample b,  sample_development_dtls a where a.sample_name=b.id and b.status_active=1 and a.status_active=1 and a.sample_mst_id=$data";
		echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
		exit();
	}

	if ($action=="load_drop_down_sample_booking")
	{
		$sql="Select b.id, b.sample_name from  lib_sample b,  wo_non_ord_samp_booking_dtls a where a.sample_type=b.id and b.status_active=1 and a.status_active=1 and a.booking_no='$data' and a.color_type_id in(2,3,4,6,33,44,63,76)";
		echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
		exit();
	}

	if ($action=="load_drop_down_buyer")
	{
		echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}

	if($action=="load_drop_down_supplier")
	{
		$data=explode("_",$data);
		$paymode=$data[0];
		$com=$data[1];
		if($paymode==5 || $paymode==3){
		echo create_drop_down( "cbo_supplier_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
		}
		else{
			echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$com and a.status_active =1 and b.party_type in(21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
		}
		exit();
	}
	if($action=="load_drop_down_supplier_new")
	{
		$data=explode("_",$data);
		$paymode=$data[0];
		$com=$data[1];
		if($paymode==5 || $paymode==3){
		echo create_drop_down( "cbo_supplier_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
		}
		else{
			$sql = "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$com and a.status_active =1 and b.party_type in(21) group by a.id,a.supplier_name UNION ALL SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c ,WO_YARN_DYEING_MST d where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$com and a.status_active IN(1,3) and b.party_type in(21) and d.supplier_id = a.id  group by a.id,a.supplier_name   order by supplier_name";
			echo create_drop_down( "cbo_supplier_name", 140, "$sql","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
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
		$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
		echo "1"."_".$currency_rate;
		exit();
	}

$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

if($action=="color_popup_old")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            $arr = array(0=>$color_arr,1=>$color_arr);

           echo $sql="select c.color_number_id,c.stripe_color from wo_non_ord_samp_booking_dtls b,wo_sample_stripe_color c WHERE b.dtls_id = c.sample_fab_dtls_id and b.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0 " ;
            
            echo  create_list_view("list_view", "Body Color,Stripe Color", "150,150","400","450",0, $sql , "js_set_value", "stripe_color", "", 1, "color_number_id,stripe_color", $arr , "color_number_id,stripe_color", "",'setFilterGrid("list_view",-1);','0');
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}


if($action=="color_popup")
{	
	echo load_html_head_contents("Color  pop-up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var datas=data.split('_');
			document.getElementById('color_name').value=datas[1];
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form>
				<input type="hidden" id="color_name" name="color_name" />
				<input type="hidden" id="fab_nature_id" name="fab_des_id" />
			</form>
			<?
			
			$sql="select c.color_number_id,c.stripe_color from wo_non_ord_samp_booking_dtls b,wo_sample_stripe_color c WHERE b.dtls_id = c.sample_fab_dtls_id and b.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0";
			$data_array=sql_select($sql);
			?>
			<table class="rpt_table" width="425" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th width="150">Body Color</th>
						<th width="">Stripe Color</th>
					</tr>
				</thead>
			</table>
			<div id="" style="max-height:350px; width:420px; overflow-y:scroll">
				<table id="list_view" class="rpt_table" width="400" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
					<tbody>
						<?
						$i=1;
						foreach($data_array as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$stripe_color_name = $color_arr[$row[csf('stripe_color')]];
							$stipe_color_id = $row[csf('stripe_color')];
							?>
							<tr id="tr_<? echo $id; ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $stipe_color_id."_".$stripe_color_name; ?>')">
								<td width="50"><? echo $i; ?></td>
								<td width="150" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
								<td><? echo $color_arr[$row[csf('stripe_color')]]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="order_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $company;
	//echo $txt_sam_booking_no;die;
	?>
	<script>
		function js_set_value(str)
		{
		$("#hidden_tbl_id").val(str); // wo/pi id
		parent.emailwindow.hide();
	}
</script>

<div align="center" style="width:615px;" >
	<form name="searchjob"  id="searchjob" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<th width="145">Company</th>
				<th width="145">Buyer</th>
				<th width="100">Sample Id</th>
				<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('searchjob','search_div','')"  /></th>
			</thead>
			<tbody>
				<tr>
					<td>
						<?
						echo create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company)/*$selected */, "load_drop_down( 'yarn_dyeing_wo_without_order_controller2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
						?>&nbsp;
					</td>
					<td align="center" id="buyer_td">
						<?
						$blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
						//echo $blank_array;


						//echo create_drop_down( "cbo_buyer_name", 130, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
						echo create_drop_down( "cbo_buyer_name", 145, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_sample_id" id="txt_sample_id" class="text_boxes" style="width:90px" />
					</td>
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_id').value+'_<? echo $txt_sam_booking_no; ?>', 'create_sample_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller2', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" />
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div align="center" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
exit();
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
if ($action=="create_sample_search_list_view")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_sample_id=str_replace("'","",$data[2]);
	$txt_sam_booking_no=str_replace("'","",$data[3]);

	//echo $txt_sam_booking_no." jahid";die;

	//echo $cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no;

	if($cbo_company_name!=0) $cbo_company_name="and a.company_id='$cbo_company_name'"; else $cbo_company_name="";
	if($cbo_buyer_name!=0) $cbo_buyer_name="and a.buyer_name='$cbo_buyer_name'"; else $cbo_buyer_name="";
	if($txt_sample_id!="") $sample_cond="and a.id=$txt_sample_id"; else $sample_cond="";

	if($txt_sam_booking_no=="")
	{
		if($db_type==0)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,group_concat(distinct b.sample_name) as sample_name from   sample_development_mst a,  sample_development_dtls b where a.id=b.sample_mst_id $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id ";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name from   sample_development_mst a,  sample_development_dtls b where a.id=b.sample_mst_id $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id, a.company_id, a.buyer_name, a.style_ref_no";
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,group_concat(distinct b.sample_name) as sample_name
			from sample_development_mst a,  sample_development_dtls b, wo_non_ord_samp_booking_dtls c
			where a.id=b.sample_mst_id and b.sample_mst_id=c.style_id and c.color_type_id in(2,3,4,6,33,44,63,76) and c.booking_no='$txt_sam_booking_no' $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id ";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name
			from sample_development_mst a,  sample_development_dtls b, wo_non_ord_samp_booking_dtls c
			where a.id=b.sample_mst_id and b.sample_mst_id=c.style_id and c.color_type_id in(2,3,4,6,33,44,63,76) and c.booking_no='$txt_sam_booking_no' $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id, a.company_id, a.buyer_name, a.style_ref_no";
		}
	}



	//echo $sql;//die;
	//$arr=array(2=>$buyer_arr);


	//	echo  create_list_view("list_view", "Job No, Year ,Buyer, Style Ref.NO, Order No.","70,80,100,120,170","590","260",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,0,0", $arr, "job_no_prefix_num,company_name,buyer_name,style_ref_no,po_number", "",'','0,0,0,0,0,0') ;
	echo '<input type="hidden" id="hidden_tbl_id">';
	?>
	<div style="width:615px;"align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="60">Sample Id</th>
				<th width="150">Sample Name</th>
				<th width="150">Buyer</th>
				<th > Style Ref.NO</th>

			</thead>
		</table>
		<div style="width:615px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$sample_name=array_unique(explode(",",$selectResult[csf("sample_name")]));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>); ">

						<td width="40"><p> <? echo $i; ?></p></td>
						<td width="60"> <p><? echo $selectResult[csf("id")]; ?></p></td>
						<td width="150"> <p>
							<?
							$sample_name_result='';
							foreach($sample_name as $val)
							{
								if($sample_name_result=='') $sample_name_result=$sample_arr[$val]; else $sample_name_result=$sample_name_result.",".$sample_arr[$val];
							}
							echo $sample_name_result;
							?></p></td>
							<td width="150"><p><?  echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
							<td > <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>

						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
		<? 

		exit();

	}

	if ($action=="save_update_delete")
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));
		//echo $cbo_source;die;
		//echo $update_id;die;
		$txt_sample_id=str_replace("'","",$txt_sample_id);
		$cbo_sample_name=str_replace("'","",$cbo_sample_name);
		$txt_pro_id=str_replace("'","",$txt_pro_id);
		$cbo_count=str_replace("'","",$cbo_count);
		$txt_item_des=str_replace("'","",$txt_item_des);
		$cbo_color_range=str_replace("'","",$cbo_color_range);
		$txt_ref_no=str_replace("'","",$txt_ref_no);
		$txt_yern_color=str_replace("'","",$txt_yern_color);
		//echo $cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no."**".$cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no; die;
		if ($operation==0)
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			//$color_id=return_id( $txt_yern_color, $color_arr, "lib_color", "id,color_name");
			if (str_replace("'", "", trim($txt_yern_color)) != "") {
				//$color_id = return_id(str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name");
				if (!in_array(str_replace("'", "", trim($txt_yern_color)),$new_array_color))
				{
					$color_id = return_id( str_replace("'", "", trim($txt_yern_color)), $color_arr, "lib_color", "id,color_name","42");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_yern_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_yern_color)), $new_array_color);
			}
			else $color_id=0;
			/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls","count='$cbo_count' and yarn_color='$color_id' and yarn_comp_type1st=$cbo_composition and yarn_comp_percent1st=$txt_pacent  and yarn_type=$cbo_yarn_type and referance_no='$txt_ref_no' and entry_form=114");
			if($duplicate==1)
			{
				echo "11**Duplicate is Not Allow in Same Job Number.";
				die;
			}*/
			$sql_samp_booking=sql_select("select b.grey_fabric from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.booking_no=$txt_sam_booking_no and a.booking_type=4 and b.status_active=1");
			//echo "10**select b.grey_fabric from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.booking_no=$txt_sam_booking_no and a.booking_type=4 and b.status_active=1";die;
			$tot_booking_req_qty=0;
			foreach($sql_samp_booking  as $row)
			{
				$tot_booking_req_qty+=$row[csf('grey_fabric')];
			}

			$sql_yarn_dye=sql_select("select b.yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.booking_no=$txt_sam_booking_no and a.entry_form=114 and b.status_active=1");
			//echo "10**select b.yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id  and b.booking_no=$txt_sam_booking_no and a.entry_form=114 and b.status_active=1";die;
			$tot_prev_booking_qty=0;
			foreach($sql_yarn_dye  as $row)
			{
				$tot_prev_booking_qty+=$row[csf('yarn_wo_qty')];
			}
			$current_wo_qty=str_replace("'",'',$txt_wo_qty);
			$total_prev_booking_qty=$current_wo_qty+$tot_prev_booking_qty;
			if($total_prev_booking_qty>$tot_booking_req_qty)
			{
				echo "17**"."Booking is over than Req Qty"."**".$total_prev_booking_qty."**".$tot_booking_req_qty;
				 disconnect($con);die;
			}
			//echo "10**"."Booking is over than Req Qty"."**".$total_prev_booking_qty."**".$tot_booking_req_qty;die;

			/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls","count='$cbo_count' and yarn_color='$color_id' and yarn_comp_type1st=$cbo_composition and yarn_comp_percent1st=$txt_pacent  and yarn_type=$cbo_yarn_type and referance_no='$txt_ref_no' and entry_form=114");
			if($duplicate==1)
			{
				echo "11**Duplicate is Not Allow in Same Job Number.";
				die;
			}*/
			//$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls b,wo_yarn_dyeing_mst a"," a.id=b.mst_id and a.ydw_no=$txt_booking_no and b.count='$cbo_count' and b.yarn_color='$color_id' and b.yarn_comp_type1st=$cbo_composition  and b.yarn_type=$cbo_yarn_type  and b.entry_form=114 and b.status_active=1 and a.status_active=1");
			$duplicate = is_duplicate_field("b.id","wo_yarn_dyeing_dtls b,wo_yarn_dyeing_mst a"," a.id=b.mst_id and a.ydw_no=$txt_booking_no and b.booking_no=$txt_sam_booking_no  and b.count='$cbo_count' and b.yarn_color='$color_id' and b.yarn_comp_type1st=$cbo_composition  and b.yarn_type=$cbo_yarn_type  and b.entry_form=114 and b.status_active=1 and a.status_active=1");
		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Booking No, Count,Color,Composition and type.";
		 disconnect($con);	die;
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			$id= return_field_value("id"," wo_yarn_dyeing_mst","id=$update_id");//check sys id for update or insert
			$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*ready_to_approved*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*'".$user_id."'*'".$pc_date_time."'*1*0";
			//$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);
			$return_no=str_replace("'",'',$txt_booking_no);
		}

		else // new insert
		{
			$id = return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst", $con);
			$new_sys_number = explode("*", return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst",$con,1,$cbo_company_name,'YDW',999,date("Y",time()),0 ));
			//echo $new_sys_number[0];die;
			$field_array="id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,supplier_id,item_category_id,booking_date,delivery_date,delivery_date_end,dy_delivery_date_start,dy_delivery_date_end,currency,ecchange_rate,pay_mode,source,attention,tenor,ready_to_approved,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',114,".$cbo_company_name.",".$cbo_supplier_name.",".$cbo_item_category_id.",".$txt_booking_date.",".$txt_delivery_date.",".$txt_delivery_end.",".$dy_delevery_start.",".$dy_delevery_end.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$cbo_ready_to_approved.",'".$user_id."','".$pc_date_time."',1,0)";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,1);
			// inv_gate_in_mst master table entry here END---------------------------------------//
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no_id,booking_no,entry_form,sample_name,product_id,count,yarn_description,yarn_color,color_range,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,referance_no,status_active,is_deleted";
		$data_array_dts="(".$dtlsid.",".$id.",'".$txt_sample_id."',".$txt_sam_booking_no.",114,'".$cbo_sample_name."','".$txt_pro_id."','".$cbo_count."','".$txt_item_des."',".$color_id.",'".$cbo_color_range."',".$cbo_composition.",".$txt_pacent.",".$cbo_yarn_type.",".$cbo_uom.",".$txt_wo_qty.",".$txt_dyeing_charge.",".$txt_amount.",".$txt_bag.",".$txt_cone.",".$txt_min_req_cone.",".$txt_remarks.",'".$txt_ref_no."',1,0)";
 		//echo $field_array_dts."<br>".$data_array_dts;die;
		//echo "0**"."INSERT INTO wo_yarn_dyeing_dtls (".$field_array_dts.") VALUES ".$data_array_dts;die;
 		//$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);
		//echo "mm";die;

		// Test for insert all
		if(str_replace("'","",$update_id)!="") //update
		{
			$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,0);
		}
		$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);

		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				//echo "10**";
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		if($db_type==1 || $db_type==2)
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		disconnect($con);
		die;
	}

	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$countID=str_replace("'","",$cbo_count);
		$yarn_typeID=str_replace("'","",$cbo_yarn_type);
		$composition_id=str_replace("'","",$cbo_composition);

		//table lock here
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15"; disconnect($con);exit();
		}
		//$color_id=return_id( $txt_yern_color, $color_arr, "lib_color", "id,color_name");
		if (str_replace("'", "", trim($txt_yern_color)) != "") {
				//$color_id = return_id(str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name");
				if (!in_array(str_replace("'", "", trim($txt_yern_color)),$new_array_color))
				{
					$color_id = return_id( str_replace("'", "", trim($txt_yern_color)), $color_arr, "lib_color", "id,color_name","42");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_yern_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_yern_color)), $new_array_color);
			}
			else $color_id=0;

		/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls"," id!=$dtls_update_id and count='$cbo_count' and yarn_color='$color_id' and yarn_comp_type1st=$cbo_composition and yarn_comp_percent1st=$txt_pacent and yarn_type=$cbo_yarn_type and entry_form=114");

		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}*/
		//$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls","id!=$dtls_update_id and count='$countID' and yarn_color='$color_id' and yarn_comp_type1st=$composition_id  and yarn_type=$yarn_typeID  and entry_form=114");
		$duplicate = is_duplicate_field("b.id","wo_yarn_dyeing_dtls b,wo_yarn_dyeing_mst a"," a.id=b.mst_id and b.id!=$dtls_update_id  and a.ydw_no=$txt_booking_no and b.booking_no=$txt_sam_booking_no  and b.count='$cbo_count' and b.yarn_color='$color_id' and b.yarn_comp_type1st=$cbo_composition  and b.yarn_type=$cbo_yarn_type  and b.entry_form=114 and b.status_active=1 and a.status_active=1");
		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Booking No,Count,Color,Composition and type.";
			 disconnect($con);die;
		}
		
		
		$sql_samp_booking=sql_select("select b.grey_fabric from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.booking_no=$txt_sam_booking_no and a.booking_type=4 and b.status_active=1");
			//echo "10**select b.grey_fabric from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.booking_no=$txt_sam_booking_no and a.booking_type=4 and b.status_active=1";die;
			$tot_booking_req_qty=0;
			foreach($sql_samp_booking  as $row)
			{
				$tot_booking_req_qty+=$row[csf('grey_fabric')];
			}

			$sql_yarn_dye=sql_select("select b.yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.booking_no=$txt_sam_booking_no and a.entry_form=114 and b.status_active=1");
			//echo "10**select b.yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id  and b.booking_no=$txt_sam_booking_no and a.entry_form=114 and b.status_active=1";die;
			$tot_prev_booking_qty=0;
			foreach($sql_yarn_dye  as $row)
			{
				$tot_prev_booking_qty+=$row[csf('yarn_wo_qty')];
			}
			$current_wo_qty=str_replace("'",'',$txt_wo_qty);
			$total_prev_booking_qty=$current_wo_qty;
			if($total_prev_booking_qty>$tot_booking_req_qty)
			{
				echo "17**"."Booking is over than Req Qty"."**".$total_prev_booking_qty."**".$tot_booking_req_qty;
				 disconnect($con);die;
			}

		$wo_qty=str_replace("'","",$txt_wo_qty);
		$supplier_id=str_replace("'","",$cbo_supplier_name);
		$yern_color_id=str_replace("'","",$txt_yern_color);
		$company_id=str_replace("'","",$cbo_company_name);
		$prev_ywo = sql_select("select a.company_id, a.supplier_id  from wo_yarn_dyeing_mst a   where  a.ydw_no=$txt_booking_no");
		foreach ($prev_ywo as $row) {
			$prev_supplier_id=$row[csf("supplier_id")];
			$prev_company_id=$row[csf("company_id")];
		}

		$result_wo=sql_select("select a.company_id,a.supplier_id,sum(case when b.id<>$dtls_update_id then b.yarn_wo_qty else 0 end) as yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.ydw_no=$txt_booking_no  and b.status_active=1 and b.is_deleted=0 and b.entry_form=114 and b.yarn_color='$color_id' group by a.company_id,a.supplier_id");
		$tot_previous_wo_qnty=0;
		foreach($result_wo as $row)
		{
			$tot_previous_wo_qnty+=$row[csf("yarn_wo_qty")];

		}
		$tot_wo_qty=$tot_previous_wo_qnty+$wo_qty;
		$result_yarn=sql_select("select a.issue_number,b.cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.transaction_type=2 and a.company_id=$cbo_company_name and b.dyeing_color_id=$color_id and a.entry_form=3 and a.issue_purpose=2 and a.status_active=1 and b.status_active=1");
		$tot_issue_qty=0;
		foreach($result_yarn as $row)
		{
			$tot_issue_qty+=$row[csf("cons_quantity")];
			$yarn_issue_no=$row[csf("issue_number")];
		}
		//echo "10**".$yarn_issue_no; die;
		//echo '10**'.$tot_issue_qty.'='.$tot_wo_qty;die;
		//echo "10**".$yarn_issue.'='.$sql_yarn;die;
		if($tot_issue_qty>$tot_wo_qty)
		{
			if($yarn_issue_no!="")
			{
				$msg_txt = "Issue Qty is Over,Issue No=".$yarn_issue_no;
				$msg_txt .= "\nUpdate not allowed";
				echo "14**".$msg_txt; disconnect($con);die;
			}
		}
		if($yarn_issue_no != '' && $company_id!=$prev_company_id)
		{
			$msg_txt = "Issue Found=".$yarn_issue_no."";
				$msg_txt .= "\nCompany Change Not allowed";
				echo "14**".$msg_txt; disconnect($con);die;
		}
		else if($yarn_issue_no != '' &&  $supplier_id!=$prev_supplier_id)
		{
			$msg_txt = "Issue Found=".$yarn_issue_no."";
			$msg_txt .= "\nSupplier Change Not allowed";
			echo "14**".$msg_txt; disconnect($con);die;
		}

		//wo_yarn_dyeing_mst master table UPDATE here START----------------------//	".$txt_pro_id.",
		$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*ready_to_approved*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*'".$user_id."'*'".$pc_date_time."'*1*0";


		//inv_gate_in_mst master table UPDATE here END---------------------------------------//

		//inv_gate_in_dtls details table UPDATE here START-----------------------------------//


		$field_array_dtls = "job_no_id*booking_no*product_id*sample_name*count*yarn_description*yarn_color*color_range*yarn_comp_type1st*yarn_comp_percent1st*yarn_type*uom*yarn_wo_qty*dyeing_charge*amount*no_of_bag*no_of_cone*min_require_cone*remarks*referance_no";
		$data_array_dtls = "'".$txt_sample_id."'*".$txt_sam_booking_no."*'".$txt_pro_id."'*".$cbo_sample_name."*".$cbo_count."*'".$txt_item_des."'*".$color_id."*".$cbo_color_range."*".$cbo_composition."*".$txt_pacent."*".$cbo_yarn_type."*".$cbo_uom."*".$txt_wo_qty."*".$txt_dyeing_charge."*".$txt_amount."
		*".$txt_bag."*".$txt_cone."*".$txt_min_req_cone."*".$txt_remarks."*'".$txt_ref_no."'";
		//echo $field_array."<br>".$data_array;die;
		$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$update_id,1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_update_id,1);
		//inv_gate_in_dtls details table UPDATE here END-----------------------------------//

		//release lock table
		$return_no=str_replace("'",'',$txt_booking_no);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==2) // Delete Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0"; die;}
		$yarn_issue_no=return_field_value("a.issue_number as issue_number","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.booking_no='$txt_booking_no' and b.transaction_type=2 and a.company_id=$cbo_company_name and a.entry_form=3 and a.issue_purpose=2 and a.status_active=1 and b.status_active=1 ","issue_number");
		if($yarn_issue_no!="")
		{
			$msg_txt = "Issue Found=".$yarn_issue_no;
			$msg_txt .= "\nDelete not allowed";
			echo "14**".$msg_txt; disconnect($con);die;
		}

 		//$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls",'status_active*is_deleted','0*1',"id",$dtls_update_id,1);
		if($db_type==0 )
		{
			if($dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID)
			{
				oci_commit($con);
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');

if($action=="show_dtls_list_view")
{
	?>
	<table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="70">Sample Id</th>
				<th width="60">Count</th>
				<th width="100">Color</th>
				<th width="130">Composition</th>
				<th width="40">Comp. %</th>
				<th width="110">Yarn Type</th>
				<th width="60">UOM</th>
				<th width="80">WO QTY</th>
				<th width="80">Charge</th>
				<th width="100">Amount</th>
				<th width="80">No of Bag</th>
				<th width="80">No of Cone</th>
				<th width="100">Minimum Require Cone</th>
				<th >Ref NO</th>
			</tr>
		</thead>
		<tbody>
			<?
			$sql = sql_select("select id, job_no_id,count,yarn_description,yarn_color,color_range,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag, no_of_cone,min_require_cone,referance_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id='$data'");
			//$arr=array(1=>$count_arr,3=>$color_arr,4=>$unit_of_measurement);
			//"job_no_id,count,yarn_description,yarn_color,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,referance_no"
			$i=1;
			foreach($sql as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="get_php_form_data(<? echo $row[csf("id")]; ?>, 'child_form_input_data', 'requires/yarn_dyeing_wo_without_order_controller2')" style="cursor:pointer;">
					<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("job_no_id")]; ?>&nbsp;</p></td>
					<td><p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p></td>
					<td><p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("yarn_comp_percent1st")]; ?>&nbsp;</p></td>
					<td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
					<td><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("yarn_wo_qty")],0); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("dyeing_charge")],2); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("amount")],2); ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_bag")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_cone")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("referance_no")]; ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if($action=="child_form_input_data")
{
	//echo $data;

	$sql = "select a.id,a.mst_id,a.job_no,a.product_id,a.job_no_id,a.count,a.yarn_description,a.yarn_color,a.color_range,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.yarn_type,uom,a.yarn_wo_qty,a.dyeing_charge,a.amount,no_of_bag, a.no_of_cone,a.min_require_cone,a.remarks,a.referance_no,a.sample_name,a.booking_no , b.company_id , b.pay_mode  from wo_yarn_dyeing_dtls a , WO_YARN_DYEING_MST b  where a.id='$data'   AND a.mst_id = b.id ";
	//echo $sql; die();
	$sql_re=sql_select($sql);
	foreach($sql_re as $row)
	{
		$paymodeData=$row[csf("pay_mode")].'_'.$row[csf("company_id")];
		echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller2', '".$paymodeData."', 'load_drop_down_supplier_new', 'supplier_td' );\n";

		echo "$('#txt_sample_id').val('".$row[csf("job_no_id")]."');\n";
		if($row[csf("job_no_id")]>0)
		{
			echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller2', ".$row[csf("job_no_id")].", 'load_drop_down_sample', 'sample_td' );\n";
		}
		else if($row[csf("booking_no")]!='')
		{
			echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller2', '".$row[csf("booking_no")]."', 'load_drop_down_sample_booking', 'sample_td' );\n";
		}
		echo "$('#cbo_sample_name').val(".$row[csf("sample_name")].");\n";
		echo "$('#txt_sam_booking_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#txt_pro_id').val(".$row[csf("product_id")].");\n";
		$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");
		if($row[csf("product_id")]>0)
		{
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(".$row[csf("count")].").attr('disabled',true);\n";
			echo "$('#txt_item_des').val('".$row[csf("yarn_description")]."').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(".$row[csf("count")].");\n";
			echo "$('#txt_item_des').val('".$row[csf("yarn_description")]."');\n";
		}
		$job_ref=$row[csf("job_no")];
		//echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller2','$job_ref', 'load_drop_down_color', 'color_td' );\n";

		echo "$('#txt_yern_color').val('".$color_arr[$row[csf("yarn_color")]]."');\n";
		echo "$('#cbo_color_range').val(".$row[csf("color_range")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#txt_wo_qty').val(".$row[csf("yarn_wo_qty")].");\n";
		echo "$('#txt_dyeing_charge').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount').val(".$row[csf("amount")].");\n";
		echo "$('#txt_bag').val(".$row[csf("no_of_bag")].");\n";
		echo "$('#txt_cone').val(".$row[csf("no_of_cone")].");\n";
		echo "$('#txt_min_req_cone').val(".$row[csf("min_require_cone")].");\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_ref_no').val('".$row[csf("referance_no")]."');\n";
		echo "$('#cbo_composition').val(".$row[csf("yarn_comp_type1st")].");\n";
		echo "$('#txt_pacent').val('".$row[csf("yarn_comp_percent1st")]."');\n";
		echo "$('#cbo_yarn_type').val(".$row[csf("yarn_type")].");\n";

		$check_issue = return_field_value("issue_number","inv_issue_master a, inv_transaction b, product_details_master c","a.id=b.mst_id and b.prod_id=c.id and a.issue_basis=1 and a.issue_purpose in(2) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=".$row[csf("mst_id")]." and c.yarn_type=".$row[csf("yarn_type")]." and c.yarn_count_id=".$row[csf("count")]." and c.yarn_comp_type1st=".$row[csf("yarn_comp_type1st")]." and yarn_comp_percent1st=".$row[csf("yarn_comp_percent1st")]." and b.dyeing_color_id=".$row[csf("yarn_color")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		if($check_issue!="")
		{
			echo " $('#cbo_count,#txt_yern_color,#cbo_color_range,#cbo_composition,#txt_pacent,#cbo_yarn_type,#txt_ref_no,#cbo_sample_name').attr('disabled',true);\n";//#txt_dyeing_charge			
		}else{
			echo " $('#cbo_count,#txt_yern_color,#cbo_color_range,#cbo_composition,#txt_pacent,#cbo_yarn_type,#txt_ref_no,#cbo_sample_name').attr('disabled',false);\n";	
		}

		$check_receive = return_field_value("recv_number","inv_receive_master a, inv_transaction b, product_details_master c","a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.receive_purpose in(2) and a.item_category=1 and a.entry_form= 1 and b.receive_basis = 2 and b.item_category=1 and b.transaction_type=1 and a.booking_id=".$row[csf("mst_id")]." and c.yarn_type=".$row[csf("yarn_type")]." and c.yarn_count_id=".$row[csf("count")]." and c.yarn_comp_type1st=".$row[csf("yarn_comp_type1st")]." and yarn_comp_percent1st=".$row[csf("yarn_comp_percent1st")]." and b.dyeing_color_id=".$row[csf("yarn_color")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		if($check_receive!="")
		{
			echo " $('#txt_dyeing_charge').attr('disabled',true);\n";			
		}else{
			echo " $('#txt_dyeing_charge').attr('disabled',false);\n";	
		}

		//update id here
		echo "$('#dtls_update_id').val(".$row[csf("id")].");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_dyeing',1,0);\n";
	}


	exit();
}



if ($action=="yern_dyeing_booking_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "$company";die;
	if($db_type==0) $select_field_grp="group by a.id order by supplier_name";
	else if($db_type==2) $select_field_grp="group by a.id,a.supplier_name order by supplier_name";

	?>

	<script>
		function js_set_value(id)
		{
			$("#hidden_sys_number").val(id);
			parent.emailwindow.hide();
		}
	var permission= '<? echo $permission; ?>';
	</script>
	</head>
	<body>
		<div align="center" style="width:830px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="6">
								<?
								echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
								?>
							</th>
						</tr>
						<tr>
							<th width="120">Buyer Name</th>
							<th width="120">Supplier Name</th>
							<th width="100">Booking No</th>
	                        <th width="100">SMN Booking No</th>
							<th width="250">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active =1 and b.party_type in(21) $select_field_grp","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
								?>
							</td>
							<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
	                        <td><input name="txt_smn_booking_prifix" id="txt_smn_booking_prifix" class="text_boxes" style="width:100px"></td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_smn_booking_prifix').value, 'create_sys_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller2', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:50px;" />
							</td>
						</tr>
						<tr>
							<td align="center" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
								<input type="hidden" id="hidden_id" value="hidden_id" />
	                        </td>
	                    </tr>
	            </tbody>
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


$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');


if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$smn_booking=$ex_data[8];
	//echo $smn_booking.', ';
	//echo $buyer_val;die;
 	//$sql_cond=""; LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	//var_dump($order_no_arr);die;
	$company_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if($smn_booking!="" )  $smn_booking_cond="and b.booking_no like '%$smn_booking%'"; else  $smn_booking_cond="";
	if($db_type==0)
	{
		$booking_year_cond=" and year(a.insert_date)=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";


	if($ex_data[7]==4 || $ex_data[7]==0)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($ex_data[7]==1)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]' "; else $booking_cond="";
		}
   if($ex_data[7]==2)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($ex_data[7]==3)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
		}
	//TO_CHAR(insert_date,'YYYY')
	/*$sql = "select id, yarn_dyeing_prefix_num, ydw_no, company_id, supplier_id, booking_date, delivery_date, currency, ecchange_rate, pay_mode,source, attention from  wo_yarn_dyeing_mst where  status_active=1 and is_deleted=0 $supplier $company $sql_cond";*/

	 if($db_type==0)
	 {
		 $sql = "select
		 a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id,group_concat(distinct b.sample_name) as sample_name,  d.buyer_name
		 from
				wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where
				a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=114 and b.entry_form=114 $company $supplier  $smn_booking_cond $sql_cond  $buyer_cond  $booking_cond
		 group by a.id order by a.id DESC";
	 }
	 //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	 else if($db_type==2)
	 {
		 $sql = "select
		 a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id, LISTAGG(CAST(b.sample_name AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.sample_name) as sample_name, d.buyer_name
		 from
				wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where
				a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=114 and b.entry_form=114 $company $supplier $smn_booking_cond  $sql_cond  $buyer_cond  $booking_cond
		group by
				a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name order by a.id DESC";
	 }
	//echo $sql;
	?>	
	<div style="width:860px; "  align="center">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
	            <thead>
	                <th width="30">SL</th>
	                <th width="50">Booking no</th>
	                <th width="40">Year</th>
	                <th width="110">Sample Develop Id</th>
	                <th width="200">Sample Name</th>
	                <th width="100">Buyer Name</th>
	                <th width="150">Supplier Name</th>
	                <th width="70">Booking Date</th>
	                <th >Delevary Date</th>
	            </thead>
	        </table>
	        <div style="width:860px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search" >
	            <?

					$i=1;
					$nameArray=sql_select( $sql );
					//var_dump($nameArray);die;
					foreach ($nameArray as $selectResult)
					{
						$paymode = $selectResult[csf("pay_mode")];
						$sample_name=array_unique(explode(",",$selectResult[csf("sample_name")]));
						$sample_develop_id=implode(",",array_unique(explode(",",$selectResult[csf("job_no_id")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'); ">

	                     <td width="30" align="center"> <p><? echo $i; ?></p></td>
	                      <td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
	                      <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
	                      <td width="110"><p>
						  <?
						  	echo $sample_develop_id;
						  ?>
	                      </p></td>
	                      <td width="200"> <p>
						  <?
						  $sample_name_group="";
						  $sample_arr;
						  foreach($sample_name as $val)
						  {
							  if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val] ;
						  }
						  echo  $sample_name_group;
						  	//$po_no=implode(",",array_unique(explode(",",$order_no_arr[$job_no_id]))); echo $po_no;
						  ?>
	                      </p></td>
	                      <td width="100"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
	                      <td width="150"> <p><?
	                      		if($paymode==5 || $paymode==3){
	                      			echo $company_arr[$selectResult[csf('supplier_id')]];
	                      		}
	                      		else{
	                      			echo $supplier_arr[$selectResult[csf('supplier_id')]];
	                      		}

	                      ?></p></td>
	                      <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
	                      <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
	                    </tr>
	                <?
	                	$i++;
					}
				?>
	            </table>
	        </div>
	        </div>
	<?
	exit();
}

if($action=="populate_master_from_data")
{
	$sql="select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.is_approved, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention, a.tenor, a.ready_to_approved from wo_yarn_dyeing_mst a where  a.ydw_no='".$data."'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_booking_no').val('".$row[csf("ydw_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		//echo "$('#hidden_type').val(".$row[csf("piworeq_type")].");\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}

		if($is_approved==1)
		{
			echo "document.getElementById('app_msg').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_msg').innerHTML = '';\n";
		}
		echo "$('#approve_id').val('".$is_approved."');\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		//echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		$paymode_data=$row[csf("pay_mode")].'_'.$row[csf("company_id")];
		echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller2', '".$paymode_data."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "$('#cbo_supplier_name').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";

		echo "$('#txt_exchange_rate').val('".$row[csf("ecchange_rate")]."');\n";
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#txt_tenor').val('".$row[csf("tenor")]."');\n";
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n";
		echo "$('#txt_delivery_end').val('".change_date_format($row[csf("delivery_date_end")])."');\n";
		echo "$('#dy_delevery_start').val('".change_date_format($row[csf("dy_delivery_date_start")])."');\n";
		echo "$('#dy_delevery_end').val('".change_date_format($row[csf("dy_delivery_date_end")])."');\n";
		echo "$('#cbo_ready_to_approved').val(".$row[csf("ready_to_approved")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		//right side list view
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/get_out_entry_controller','');\n";
	}
	exit();
}

if($action=="dyeing_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	?>
	<script>
	function js_set_value(str)
	{
		$("#hidden_rate").val(str);
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
	    <div align="center" style="width:590px;" >
	        <fieldset>
	            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	                <table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	                    <thead>
	                        <tr>
	                            <th width="40">Sl No.</th>
	                            <th width="170">Const. Compo.</th>
	                            <th width="100">Process Name</th>
	                            <th width="100">Color</th>
	                            <th width="90">Rate</th>
	                            <th>UOM</th>
	                        </tr>
	                    </thead>
	                </table>
	                <?
					$sql="select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where comapny_id=$company and process_id=30 and status_active=1";
					//echo $sql;
					$sql_result=sql_select($sql);
					?>
	            	<div style="width:570px; overflow-y:scroll; max-height:240px;font-size:12px; overflow-x:hidden; cursor:pointer;">
	                    <table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="table_charge">
	                        <tbody>
	                        <?
							$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
								?>
	                            <tr bgcolor="<?  echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("in_house_rate")]; ?>)">
	                                <td width="40" align="center"><? echo $i;  ?></td>
	                                <td width="170"><? echo $row[csf("const_comp")]; ?></td>
	                                <td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
	                                <td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
	                                <td width="90" align="right"><? echo number_format($row[csf("in_house_rate")],2); ?></td>
	                                <td><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
	                            </tr>
	                            <?
								$i++;
							}
							?>
	                          <input type="hidden" id="hidden_rate" />
	                        </tbody>
	                    </table>
	                </div>
	            </form>
	        </fieldset>
	    </div>
	</body>
	<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>

    <?
	exit();
}

if ($action=="sam_book_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>

	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}

    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="830" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                   <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="3"></th>
                     </thead>
                    <thead>
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                        <th width="80">Style Desc.</th>
                        <th width="200">Date Range</th><th></th>
                    </thead>
        			<tr>
                    	<td>
                        <input type="hidden" id="selected_booking">
							<?
								//if($_SESSION['logic_erp']['company_id'])$company_cond=" and id in(".$_SESSION['logic_erp']['company_id'].")"; else $company_cond="";
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'sample_booking_non_order_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <?
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
					?>	</td>

                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller2','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <?
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div">

            </td>
        </tr>
    </table>

    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}

	if($db_type==0)
	{
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
	}
    if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
	}
    if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
		}
	if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
	}

	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library);

	$sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a, wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.color_type_id in(2,3,4,6,33,44,63,76)  order by booking_no";
	//echo $sql;
	echo create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Fabric Nature,Fabric Source,Supplier,Style,Style Desc.", "50,70,130,130,100,100,130,90","950","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,0", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,style_des", '','','0,3,0,0,0,0,0,0,0,0','','');
}


if($action=="lot_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
		?>
	<script>
		function js_set_value2(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>

	</head>
	<body>
		<div style="width:595px;" align="center" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>

                    <tr>
                        <th width="150">Lot</th>
                         <th width="">&nbsp;</th>
                     </tr>
                    </thead>
        			<tr>
                    <td>
                    <input name="txt_lot_search" id="txt_lot_search" class="text_boxes" style="width:150px" placeholder="Write">
                    </td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company ?>+'_'+document.getElementById('txt_lot_search').value, 'create_lot_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller22', 'setFilterGrid(\'table_charge\',-1)')" style="width:100px;" /></td>



             </table>
             <br>
             <table  width="595"  align="center">
             <tr>
            	<td align="center" valign="top" id="search_div">
            </td>
        	</tr>
             </table>
				</form>
			</fieldset>
		</div>
	</body>
	<!--<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>  -->
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	//}
}

if($action=="create_lot_search_list_view")
{

	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company=str_replace("'","",$data[0]);
	$lot_search=str_replace("'","",$data[1]);
	//echo $job_no;die;

	if($lot_search!='') $lot_cond="and lot ='$lot_search'";else  $lot_cond="";
	if($company!='') $com_cond="and company_id =$company";else  $com_cond="";
	?>

</head>
<body>
	<div style="width:585px;" >
		<fieldset>
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="585" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"  rules="all">
					<thead>
						<tr>
							<th width="30">Sl No.</th>
							<th width="100">Lot No</th>
							<th width="90">Brand</th>
							<th width="200">Product Name Details</th>
							<th width="80">Stock</th>
							<th>UOM</th>
						</tr>
					</thead>
				</table>
				<?

				$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where item_category_id=1 $com_cond $lot_cond";

					//echo $sql;
				$sql_result=sql_select($sql);
				?>
				<div style="width:585px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
					<table width="585" cellspacing="0" cellpadding="0" border="0" class="rpt_table"  style="cursor:pointer" rules="all" id="table_charge">
						<tbody>
							<?
							$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>*<? echo $row[csf("yarn_count_id")]; ?>*<? echo $row[csf("lot")]; ?>*<? echo $row[csf("id")]; ?>')">
									<td width="30" align="center"><p><? echo $i;  ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
									<td width="200">v<? echo $row[csf("product_name_details")]; ?></p></td>
									<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" id="hidden_product" style="width:100px;" />
						</tbody>
					</table>
				</div>
			</form>
		</fieldset>
	</div>
</body>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="lot_search_popup23")//Old
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div style="width:595px;" >
		<fieldset>
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="40">Sl No.</th>
							<th width="100">Lot No</th>
							<th width="90">Brand</th>
							<th width="200">Product Name Details</th>
							<th width="80">Stock</th>
							<th>UOM</th>
						</tr>
					</thead>
				</table>
				<?

				$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where company_id='$company' and item_category_id=1";

					//echo $sql;
				$sql_result=sql_select($sql);
				?>
				<div style="width:595px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
					<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_charge" style="cursor:pointer" rules="all">
						<tbody>
							<?
							$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
									<td width="40" align="center"><p><? echo $i;  ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
									<td width="200">v<? echo $row[csf("product_name_details")]; ?></p></td>
									<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" id="hidden_product" style="width:200px;" />
						</tbody>
					</table>
				</div>
			</form>
		</fieldset>
	</div>
</body>
<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	//}

exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$( "#tbl_termcondi_details tr:last" ).find( "td:first" ).html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo)
		{

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../");
			//alert(data_all);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	//	alert(data);
		//freeze_window(operation);
		http.open("POST","yarn_dyeing_wo_without_order_controller2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4)
		{
	   // alert(http.responseText);
	   var reponse=trim(http.responseText).split('**');
	   if (reponse[0].length>2) reponse[0]=10;
	   if(reponse[0]==0 || reponse[0]==1)
	   {
				//$('#txt_terms_condision_book_con').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fnc_fabric_booking_terms_condition',1,1);
			}
		}
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset>
			<form id="termscondi_1" autocomplete="off">
				<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>" class="text_boxes" readonly />
				<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con" >

				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
					<thead>
						<tr>
							<th width="50">Sl</th><th width="530">Terms</th><th ></th>
						</tr>
					</thead>
					<tbody>
						<?
					//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
							<tr id="settr_1" align="center">
								<td>
									<? echo $i;?>
								</td>
								<td>
									<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
						}
					}
					else
					{

					$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="center">
							<td>
								<? echo $i;?>
							</td>
							<td>
								<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
							</td>
							<td>
								<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
								<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
							</td>
						</tr>
						<?
					}
				}
				?>
			</tbody>
		</table>

		<table width="650" cellspacing="0" class="" border="0">
			<tr>
				<td align="center" height="15" width="100%"> </td>
			</tr>
			<tr>
				<td align="center" width="100%" class="button_container">
					<?
					echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
					?>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
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

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++)
		{
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		 	//echo "INSERT INTO wo_booking_terms_condition (".$field_array.") VALUES ".$data_array;die;
		//echo "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."";
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);
		//echo $rID_de3;
//print_r();

		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$txt_booking_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_booking_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo "0**".$txt_booking_no;
			}

		}
		else{
			oci_rollback($con);
			echo "10**".$txt_booking_no;
		}
		disconnect($con);
		die;
	}
}


$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );



if($action=="show_with_multiple_job")
{
	//echo "xxxx";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:900px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id";
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];

		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="900" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="200"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="1080" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="30" align="center"><strong>Sl</strong></th>
				<th width="65" align="center"><strong>Color</strong></th>
				<th width="80" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="50"><strong>Ref No.</strong></th>
				<th  align="center" width="70"><strong>Style Ref.No.</strong></th>
				<th width="30" align="center"><strong>Yarn Count</strong></th>
				<th width="140" align="center"><strong>Yarn Description</strong></th>
				<th width="60" align="center"><strong>Brand</strong></th>
				<th width="50" align="center"><strong>Lot</strong></th>
				<th width="60" align="center"><strong>WO Qty</strong></th>
				<th width="50" align="center"><strong>Dyeing Rate</strong></th>
				<th width="80" align="center"><strong>Amount</strong></th>
				<th  align="center" width="50"><strong>Min Req. Cone</strong></th>
				<th  align="center" width="80"><strong>Sample Develop Id</strong></th>
				<th  align="center" width="80"><strong>Buyer</strong></th>
				<th  align="center" ><strong>Sample Name</strong></th>
			</tr>
		</thead>
		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type
			order by b.id";
		}
		else if($db_type==2)
		{
			 $sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color ,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type,b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
			order by b.id";
		}

			//echo $sql;die;
		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					if($row[csf("yarn_type")]>0) $yarntype=", ".$yarn_type[$row[csf("yarn_type")]];else $yarntype="";
					echo $composition[$row[csf("yarn_comp_type1st")]].$yarntype;
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?> &nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<tr>
				<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th align="right">&nbsp;</th>
				<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>

		<?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
			$mcurrency='Taka';
			$dcurrency='Paisa';
		}
		if($currency_id==2)
		{
			$mcurrency='USD';
			$dcurrency='CENTS';
		}
		if($currency_id==3)
		{
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
		?>
		<tr>
			<td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");?> </td>
		</tr>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="1080" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
	<? echo get_spacial_instruction($txt_booking_no,'1080px',114); ?>
</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "1080px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="show_with_multiple_job_without_rate")
{

	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">
				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="950" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="30" align="center"><strong>Sl</strong></th>
				<th width="65" align="center"><strong>Color</strong></th>
				<th width="80" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="50"><strong>Ref No.</strong></th>
				<th  align="center" width="70"><strong>Style Ref.No.</strong></th>
				<th width="30" align="center"><strong>Yarn Count</strong></th>
				<th width="140" align="center"><strong>Yarn Description</strong></th>
				<th width="60" align="center"><strong>Brand</strong></th>
				<th width="50" align="center"><strong>Lot</strong></th>
				<th width="60" align="center"><strong>WO Qty</strong></th>
				<th  align="center" width="50"><strong>Min Req. Cone</strong></th>
				<th  align="center" width="80"><strong>Sample Develop Id</strong></th>
				<th  align="center" width="80"><strong>Buyer</strong></th>
				<th  align="center" ><strong>Sample Name</strong></th>
			</tr>
		</thead>

		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no,,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range,,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type
			order by b.id";
		}
		else if($db_type==2)
		{
			 $sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type, a.ydw_no,b.id
			order by b.id";
		}

		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
				//var_dump($product_id);
			if($product_id)
			{
				$sql_brand=sql_select("select lot,brand from product_details_master where id in($product_id)");
				foreach($sql_brand as $row_barand)
				{
					$lot_amt=$row_barand[csf("lot")];
					$brand=$row_barand[csf("brand")];
				}

			}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
			$all_style_arr[]=$style_ref_sample[$row[csf("job_no_id")]];
			$all_job_arr[]=$row[csf("job_no")];

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					if($row[csf("yarn_type")]>0) $yarntype=", ".$yarn_type[$row[csf("yarn_type")]];else $yarntype="";
					echo $composition[$row[csf("yarn_comp_type1st")]].$yarntype;
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
			$yarn_count_des="";
			$style_no="";
		}
		?>
		<tfoot>
			<tr>
				<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>

	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($txt_booking_no,'950px',114); ?>


</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "950px");
	echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}



if($action=="show_without_rate_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		}

		$varcode_work_order_no=$work_order;
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<?
	$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
	$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
	from
	wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
	where
	a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
	$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
	foreach($sql_result as $row)
	{
		if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];
		if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];
		if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];
		if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];
	}

	?>

	<table width="950" style="" align="center">
		<tr  style="font-size:12px">
			<td width="150"><b>Sample Id</b></td>
			<td width="800" valign="top">:&nbsp;
				<?
				echo $total_samp_deve_id;
				?>
			</td>
		</tr>

		<tr style="font-size:12px">
			<td valign="top"><b>Sample Name</b></td>
			<td valign="top">: &nbsp;
				<?
				$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

				$all_order=explode(",",$total_order_no);
				$sample="";
				foreach($total_sample_name_arr as $row)
				{
					if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
				}
				echo $sample;
				?>
			</td>
		</tr>
		<tr style="font-size:12px">
			<td valign="top"><b>Buyer </b> </td>
			<td valign="top">:&nbsp;
				<?
				$total_buyer_array=array_unique(explode(",",$total_buyer));
				$view_buyer="";
				foreach($total_buyer_array as $row)
				{
					if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
				}
				echo $view_buyer;
				?>
			</td>
		</tr >
	</table>



	<table width="1030" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="40" align="center"><strong>Sl</strong></th>
				<th width="70" align="center"><strong>Color</strong></th>
				<th width="85" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="70"><strong>Ref NO.</strong></th>
				<th width="40" align="center"><strong>Yarn Count</strong></th>
				<th width="190" align="center"><strong>Yarn Description</strong></th>
				<th width="80" align="center"><strong>Yarn Type</strong></th>
				<th width="70" align="center"><strong>Brand</strong></th>
				<th width="65" align="center"><strong>Lot</strong></th>
				<th width="70" align="center"><strong>WO Qty</strong></th>
				<th  align="center" width="65" ><strong>Min Req. Cone</strong></th>
				<th  align="center" ><strong>Remarks</strong></th>
			</tr>
		</thead>
		<?

		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		if($db_type==0) $select_f_grp="group by count, yarn_color, color_range order by id";
		else if($db_type==2) $select_f_grp="group by yarn_color, color_range,id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,yarn_type,yarn_comp_type1st,remarks order by id ";

		$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_comp_type1st,yarn_type,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no,remarks
		from
		wo_yarn_dyeing_dtls
		where
		status_active=1 and id in($total_dtls_id) $select_f_grp";
			//echo $sql_color;die;
		$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}


			//$yarn_count=explode(" ",$row[csf("yarn_description")]);

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
                <!--<td align="center">
				<?
				 /*$style_no=return_field_value( "style_ref_no", " wo_po_details_master","job_no='".$row[csf("job_no")]."'");
				 echo $style_no;*/
				?>
			</td>-->
			<td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
			<td>
				<?
			if($row[csf("yarn_type")]>0) $yarntype=", ".$yarn_type[$row[csf("yarn_type")]];else $yarntype="";
				echo $composition[$row[csf("yarn_comp_type1st")]].$yarntype;
				 //echo $row[csf("yarn_description")];
				?>
			</td>
			<td align="center"><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</td>
			<td><? echo $brand_arr[$brand]; ?></td>
			<td align="center"><? echo $lot_amt; ?></td>
			<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
			<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
			<td align="center"><? echo $row[csf("remarks")]; ?></td>
		</tr>
		<?
		$i++;
	}
	?>
	<tfoot>
		<tr>
			<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
			<th align="right" ><b><? echo $total_qty; ?></b></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</tfoot>

</table>


<!--==============================================AS PER GMTS COLOR START=========================================  -->
<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

	<? echo get_spacial_instruction($txt_booking_no,'950px',114); ?>
</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "950px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="show_work_order_repot")
{
	//echo "hello work_order!";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$sample_name=return_library_array( "Select id, sample_name from  lib_sample where  status_active=1",'id','sample_name');
	$lib_comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$supp_arr=array();
	$supp_sql="select id, contact_person, contact_no, email, address_1 from lib_supplier where is_deleted=0 and status_active=1";
	$sup_sql_res=sql_select($supp_sql);
	foreach($sup_sql_res as $jrow)
	{

		$supp_arr[$jrow[csf("id")]]['contact_person']=$jrow[csf("contact_person")];
		$supp_arr[$jrow[csf("id")]]['contact']=$jrow[csf("contact_no")];
		$supp_arr[$jrow[csf("id")]]['email']=$jrow[csf("email")];
		$supp_arr[$jrow[csf("id")]]['address_1']=$jrow[csf("address_1")];
	}
//print_r($supp_arr);die;


	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" >
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order Without Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.pay_mode,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
        //print_r($nameArray);die;
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$pay_mode_id=$result[csf('pay_mode')];
		}
		$varcode_work_order_no=$work_order;
		?>

		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  style="font-size:16px"><b>Wo No.</b>   </td>
							<td  style="font-size:16px">:&nbsp;&nbsp; <b><? echo $work_order;?></b>    </td>
						</tr>

						<tr>
							<td   width="120" style="font-size:16px"><b>To</b>   </td>
							<td width="230" style="font-size:16px">:&nbsp;&nbsp; <b><?
							if($pay_mode_id==3 || $pay_mode_id==5)
							{
							 echo $lib_comp[$supplier_id];
							}
							else  echo $supplier_arr[$supplier_id];
							 ?></b></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $supp_arr[$result[csf("supplier_id")]]['contact_person'] ?></td>
						</tr>

						<tr>
							<td style="font-size:12px">Phone</td>
							<td >:&nbsp;&nbsp; <? echo $supp_arr[$result[csf("supplier_id")]]['contact'] ?></td>
						</tr>

						<tr>
							<td style="font-size:12px">Mail</td>
							<td >:&nbsp;&nbsp;<? echo  $supp_arr[$result[csf("supplier_id")]]['email'] ?></td>
						</tr>

						<tr>
							<td style="font-size:12px">Address</td>
							<td >:&nbsp;&nbsp; <? echo $supp_arr[$result[csf("supplier_id")]]['address_1'] ?></td>
						</tr>

						<?
            /*$multi_job_arr=array();
            $style_no=sql_select("select a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

             foreach($style_no as $row_s)
             {

            $multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
            $multi_job_arr[$row_s[csf('job_no')]]['po_no']=$row_s[csf('po_number')];
        }	*/

        //$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$buyer_sample=sql_select("select id, buyer_name, season,style_ref_no from sample_development_mst ");
		$sample_arr=array();
		foreach($buyer_sample as $row)
		{
			$sample_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
			$sample_arr[$row[csf("id")]]['season']=$row[csf("season")];
			$sample_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}
		unset($buyer_sample);

//wo_non_ord_samp_booking_mst
	 //  $buyer_booking=return_library_array( "select booking_no, buyer_id from  wo_non_ord_samp_booking_mst",'booking_no','buyer_id');
	   $style_id=return_library_array( "select booking_no, style_id from  wo_non_ord_samp_booking_dtls",'booking_no','style_id');
	   $lib_sampleArr=return_library_array( "select id, sample_name from  lib_sample",'id','sample_name');

	  $non_order_data = sql_select("select booking_no,buyer_id, grouping from  wo_non_ord_samp_booking_mst");
	   foreach ($non_order_data as $key => $row) {
	   		$buyer_booking[$row[csf('booking_no')]]= $row[csf('buyer_id')];
			$int_ref_booking[$row[csf('booking_no')]]= $row[csf('grouping')];
	   }

        if($db_type==0)
        {
        	$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.booking_no
        	from
        	wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
        	where
        	a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
        }
        else if($db_type==2)
        {
        	$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.booking_no
        	from
        	wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
        	where
        	a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
        }


        // $sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.booking_no,c.season
        //     from
        //             wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b , sample_development_mst c
        //     where
        //             a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and b.sample_name=c.id and a.id=$update_id";
        //echo $sql;die;
        $sql_result=sql_select($sql);

        $total_samp_deve_id="";
        $total_buyer="";
        $total_dtls_id='';
        $total_sample_name='';
        $total_booking_no="";
        $season="";
        foreach($sql_result as $row)
        {
        	if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

        	if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];
			if($row[csf("booking_no")]=="")
			{
				if($row[csf("job_no_id")]!="")
				{
					if($total_buyer=="") $total_buyer=$sample_arr[$row[csf("job_no_id")]]['buyer_name']; else $total_buyer=$total_buyer.",".$sample_arr[$row[csf("job_no_id")]]['buyer_name'];
					$total_style[$row[csf("job_no_id")]] = $sample_arr[$row[csf("job_no_id")]]['style_ref_no'];
				}
			}
			else
			{
				if($total_buyer=="") $total_buyer=$buyer_booking[$row[csf("booking_no")]]; else $total_buyer=$total_buyer.",".$buyer_booking[$row[csf("booking_no")]];
				$total_style[$row[csf("booking_no")]] = $sample_arr[$style_id[$row[csf("booking_no")]]]['style_ref_no'];
				//$total_style[$row[csf("booking_no")]] = $style_id[$row[csf("booking_no")]];
			}

        	if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

        	if($total_booking_no=="") $total_booking_no=$row[csf("booking_no")]; else $total_booking_no=$total_booking_no.",".$row[csf("booking_no")];
        	$season.=$sample_arr[$row[csf("job_no_id")]]['season'].",";
        	//$total_booking_no=$row[csf("booking_no")];
        }


        ?>
        <tr>
        	<td width="120"><b>Sample Id</b></td>
        	<td width="230" valign="top">:&nbsp;
        		<?
                                    //echo chop($total_samp_deve_id," , ");
        		$total_sample_id_arr=array_unique(explode(",",$total_samp_deve_id));

        		$sample_id="";
        		foreach($total_sample_id_arr as $row)
        		{
        			if($sample_id=="") $sample_id=$row; else $sample_id=$sample_id.",".$row;
        		}
        		echo chop($sample_id," , ");
        		?>
        	</td>
        </tr>

        <tr>
        	<td valign="top"><b>Sample Name</b></td>
        	<td valign="top">: &nbsp;
        		<? //echo"skhdfkjhdfkh";
        		$total_sample_name_arr=explode(",",$total_sample_name);
                //print_r ($total_sample_name_arr);
        		$sample=array();
        		foreach($total_sample_name_arr as $row)
        		{
        			$sample[$row]=$lib_sampleArr[$row];
        		}
        		echo implode(",",$sample);
        		?>
        	</td>
        </tr>
       
    </table>
</td>
<td width="350"  style="font-size:12px">
	<table width="350" style="" align="left">


		<tr>
			<td style="font-size:12px">WO Date</td>
			<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
		</tr>

		<tr style="font-size:12px">
			<td valign="top">Buyer</td>
			<td valign="top">:&nbsp;
				<?
				$total_buyer_array=array_unique(explode(",",$total_buyer));
				$view_buyer="";
				foreach($total_buyer_array as $row)
				{
					if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
				}
				echo $view_buyer;
				?>
			</td>
		</tr >

		<tr>
			<td  width="120">Season</td>
                                <td width="230" >:&nbsp;&nbsp; <?  //echo rtrim($season,",");

								/*$sql="select DISTINCT a.job_no_id AS JOB_NO_ID, a.status_active AS STATUS_ACTIVE, a.is_deleted AS IS_DELETED, b.id AS ID, b.season_buyer_wise AS SEASON_BUYER_WISE, c.id AS ID, c.season_name AS SEASON_NAME from
								wo_yarn_dyeing_dtls a, sample_development_mst b , lib_buyer_season c

								 where a.status_active=1 and a.is_deleted=0 and a.job_no_id=b.id and b.season_buyer_wise=c.id

								 "; //and a.id=$update_id";*/

								$sql="select DISTINCT b.job_no_id AS JOB_NO_ID, b.status_active AS STATUS_ACTIVE, b.is_deleted AS IS_DELETED, c.id AS ID, c.season_buyer_wise AS SEASON_BUYER_WISE, d.id AS ID, d.season_name AS SEASON_NAME from wo_yarn_dyeing_mst a ,wo_yarn_dyeing_dtls b, sample_development_mst c , lib_buyer_season d where a.status_active=1 and a.is_deleted=0 and b.job_no_id=c.id and c.season_buyer_wise=d.id  and a.id=b.mst_id and a.id=$update_id"; //and a.id=$update_id";

									//echo $sql; die;
								$sql_result=sql_select($sql);
									//print_r($sql_result); die;
								foreach($sql_result as $data){
									echo $data[csf("season_name")].",";
								}

								?>

							</td>
						</tr>

						<tr>
							<td style="font-size:12px">Currency</td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>

						<tr>
							<td style="font-size:15px"><b>Booking No</b></td>
							<td >:&nbsp;&nbsp;<?
							$total_booking_no_arr=array_unique(explode(",",$total_booking_no));
							$booking_no="";$int_ref_no="";
							$booking_no2="";
							//echo "<pre>";
							//print_r($total_booking_no_arr);die;
							foreach($total_booking_no_arr as $row)
							{
								if($booking_no=="") $booking_no=$row; else $booking_no=$booking_no.",".$row;
								if($booking_no2=="") $booking_no2="'".$row."'"; else $booking_no2=$booking_no2.",'".$row."'";
								if($int_ref_no=="") $int_ref_no=$int_ref_booking[$row];else $int_ref_no=",".$int_ref_booking[$row];
								
							
	                                    //echo $row.",";
							}
							echo chop($booking_no," , ");
							//echo "SELECT sum(GREY_FABRIC) as grey_fabric FROM WO_NON_ORD_SAMP_BOOKING_DTLS Where BOOKING_NO in($booking_no2) and status_active=1 ";
							$booking_no2= chop($booking_no2," , ");
							$booking_sql=sql_select("SELECT booking_no, sum(GREY_FABRIC) as grey_fabric FROM WO_NON_ORD_SAMP_BOOKING_DTLS Where BOOKING_NO in($booking_no2) and status_active=1 group by  booking_no ");
							//$booking_qnty=$booking_sql[0][csf("grey_fabric")];


							//echo "SELECT sum(YARN_WO_QTY) as qnty  FROM wo_yarn_dyeing_dtls Where MST_ID = $update_id".'hfjkf';
							$dyeing_wo_sql=sql_select("SELECT  booking_no,sum(YARN_WO_QTY) as qnty  FROM wo_yarn_dyeing_dtls Where BOOKING_NO in($booking_no2) and status_active=1 group by booking_no");

							$dyeing_qnty_sum=array();
							foreach($dyeing_wo_sql as $v)
							{
								$dyeing_qnty_sum[$v[csf("booking_no")]]+=$v[csf("qnty")];
							}

							?>
						</td>
					</tr>
					<tr>
						<td style="font-size:12px">Style Name</td>
						<td>: &nbsp;<? echo implode(',', $total_style) ?></td>
					</tr>
                    <tr>
						<td style="font-size:12px">Internal Ref. No</td>
						<td>: &nbsp;<? echo $int_ref_no; ?></td>
					</tr>
				</table>
			</td>


			<td width="350"  style="font-size:12px">
				<table width="350" style="" align="left">
					<tr>
						<td>
							<?
							$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
							?>
							<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>

	<table width="950"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="30" align="center"><strong>Sl</strong></th>
				<th width="70" align="center"><strong>Color</strong></th>
				<th width="80" align="center"><strong>Gmr. Color/Combo</strong></th>
				<th  align="center" width="50"><strong>Ref No.</strong></th>
				<th width="30" align="center"><strong>Yarn Count</strong></th>
				<th width="160" align="center"><strong>Yarn Description</strong></th>
				<th width="60" align="center"><strong>Brand</strong></th>
				<th width="60" align="center"><strong>WO Qty</strong></th>
				<th width="50" align="center"><strong>Dyeing Rate</strong></th>
				<th width="80" align="center"><strong>Amount</strong></th>
				<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
				<th  align="center" ><strong>Remarks</strong></th>
			</tr>
		</thead>
		<?

		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}

		if($db_type==0) $select_f_grp="group by count, yarn_color, color_range order by id";
		else if($db_type==2) $select_f_grp="group by yarn_color, color_range,
			id,product_id,job_no,job_no_id,yarn_description,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,count,dyeing_charge,min_require_cone,referance_no, remarks order by id ";

		$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
		from
		wo_yarn_dyeing_dtls
		where
		status_active=1 and id in($total_dtls_id) $select_f_grp ";
			//echo $sql_color;die;
		$sql_result=sql_select($sql_color);

		$total_qty=0;
		$total_amount=0;
		$i=1;
		$buyer=0;
		$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
				//var_dump($product_id);
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			$yarn_description=$composition[$row[csf("yarn_comp_type1st")]];
			$yarn_typee=$yarn_type[$row[csf("yarn_type")]];
				//echo $yarn_type;die;
			$yarn_percent=$row[csf("yarn_comp_percent1st")];

			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
					<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
					<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				   <!-- <td align="center">
					<?
					 /*$style_no=return_field_value( "style_ref_no", " wo_po_details_master","job_no='".$row[csf("job_no")]."'");
					 echo $style_no;*/
					?>
				</td>-->
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>

				<td><? echo $yarn_description.",".$yarn_typee.",".$yarn_percent; ?></td>

				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("remarks")]; ?></td>
			</tr>
		</tbody>
		<?
		$i++;
		$yarn_count_des="";
		$style_no="";
	}
	?>
	<tfoot>
		<tr>
			<th colspan="7" align="left"><strong>Total:</strong>&nbsp;&nbsp;</th>
			<th align="right" ><b><? echo $total_qty; ?></b></th>
			<th align="right">&nbsp;</th>
			<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<th colspan="12" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </th>
		</tr>
	</tfoot>

	<?
	$mcurrency="";
	$dcurrency="";
	if($currency_id==1)
	{
		$mcurrency='Taka';
		$dcurrency='Paisa';
	}
	if($currency_id==2)
	{
		$mcurrency='USD';
		$dcurrency='CENTS';
	}
	if($currency_id==3)
	{
		$mcurrency='EURO';
		$dcurrency='CENTS';
	}
	?>
</table>
<br>


<!--==============================================AS PER GMTS COLOR START=========================================  -->



	<? echo get_spacial_instruction($txt_booking_no,'950px',114); ?>
	<br>
	<br>
	<table width="950" style="" align="center"><tr><td><strong>Comments</strong></td></tr></table>
	<table width="600" cellspacing="0" cellpadding="0" border="1" align="left" rules="all" class="rpt_table">
	<thead>
		<tr>
			<th width="150">Booking No</th>
			<th width="150">Booking Qty(Grey)</th>
			<th width="150">Cumulative Y/D W/O QTY</th>
			<th width="150">Balance</th>
		</tr>
	</thead>
	<tbody>
		<?
		foreach($booking_sql as $row)
        {
		 ?>

		<tr>
			<td align="left"><? echo $row[csf("booking_no")]; ?></td>
			<td align="right"><? echo $grey=$row[csf("grey_fabric")];?></td>
			<td align="right"><? echo $dyeing=$dyeing_qnty_sum[$row[csf("booking_no")]];?></td>
			<td align="right"><?
			 $cal=$grey - $dyeing;
			 echo $cal;
			?></td>
		</tr>
		<?
		$i++;
	    }
		?>
	</tbody>

</table>

</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "950px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="show_trim_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" >
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
		}
		$varcode_work_order_no=$work_order;
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<?
	  			/*$multi_job_arr=array();
				$style_no=sql_select("select a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

				 foreach($style_no as $row_s)
				 {

				$multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
				$multi_job_arr[$row_s[csf('job_no')]]['po_no']=$row_s[csf('po_number')];
			}	*/
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
			$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

				if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

			}
		//var_dump($total_dtls_id);
		//die;

			?>

			<table width="950" style="" align="center">
				<tr  style="font-size:12px">
					<td width="150"><b>Sample Id</b></td>
					<td width="800" valign="top">:&nbsp;
						<?
						echo $total_samp_deve_id;
						?>
					</td>
				</tr>

				<tr style="font-size:12px">
					<td valign="top"><b>Sample Name</b></td>
					<td valign="top">: &nbsp;
						<?
						$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

						$all_order=explode(",",$total_order_no);
						$sample="";
						foreach($total_sample_name_arr as $row)
						{
							if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
						}
						echo $sample;
						?>
					</td>
				</tr>
				<tr style="font-size:12px">
					<td valign="top"><b>Buyer </b> </td>
					<td valign="top">:&nbsp;
						<?
						$total_buyer_array=array_unique(explode(",",$total_buyer));
						$view_buyer="";
						foreach($total_buyer_array as $row)
						{
							if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
						}
						echo $view_buyer;
						?>
					</td>
				</tr >
			</table>



			<table width="1030"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" align="center"><strong>Sl</strong></th>
						<th width="70" align="center"><strong>Color</strong></th>
						<th width="80" align="center"><strong>Color Range</strong></th>
						<th  align="center" width="50"><strong>Ref No.</strong></th>
						<th width="30" align="center"><strong>Yarn Count</strong></th>
						<th width="160" align="center"><strong>Yarn Description</strong></th>
						<th width="80" align="center"><strong>Yarn Type</strong></th>
						<th width="60" align="center"><strong>Brand</strong></th>
						<th width="60" align="center"><strong>Lot</strong></th>
						<th width="60" align="center"><strong>WO Qty</strong></th>
						<th width="50" align="center"><strong>Dyeing Rate</strong></th>
						<th width="80" align="center"><strong>Amount</strong></th>
						<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
						<th  align="center" ><strong>Remarks</strong></th>
					</tr>
				</thead>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0) $select_f_grp="group by count, yarn_color, color_range,yarn_comp_type1st,yarn_comp_percent1st,yarn_type order by id";
				else if($db_type==2) $select_f_grp="group by yarn_color, color_range,
					id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,yarn_comp_type1st,yarn_comp_percent1st,yarn_type, remarks order by id ";

				$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
				from
				wo_yarn_dyeing_dtls
				where
				status_active=1 and id in($total_dtls_id) $select_f_grp ";
			//echo $sql_color;die;
				$sql_result=sql_select($sql_color);

				$total_qty=0;
				$total_amount=0;
				$i=1;$buyer=0;
				$order_no="";
				foreach($sql_result as $row)
				{
					$product_id=$row[csf("product_id")];
				//var_dump($product_id);
					if($row[csf("product_id")]!="")
					{
						$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
						$brand=$product_lot[$row[csf("product_id")]]['brand'];
					}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
						<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
						<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				   <!-- <td align="center">
					<?
					 /*$style_no=return_field_value( "style_ref_no", " wo_po_details_master","job_no='".$row[csf("job_no")]."'");
					 echo $style_no;*/
					?>
				</td>-->
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					
					if($row[csf("yarn_type")]>0) $yarntype=",".$yarn_type[$row[csf("yarn_type")]];else $yarntype="";
					
					echo $composition[$row[csf("yarn_comp_type1st")]].$yarntype;
					?>
				</td>
				<td align="center"><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("remarks")]; ?></td>
			</tr>
			<?
			$i++;
			$yarn_count_des="";
			$style_no="";
		}
		?>
		<tfoot>
			<tr>
				<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th align="right">&nbsp;</th>
				<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<th colspan="14" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </th>
			</tr>
		</tfoot>

		<?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
			$mcurrency='Taka';
			$dcurrency='Paisa';
		}
		if($currency_id==2)
		{
			$mcurrency='USD';
			$dcurrency='CENTS';
		}
		if($currency_id==3)
		{
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
		?>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

	<? echo get_spacial_instruction($txt_booking_no,'950px',114); ?>

</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "950px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}


?>