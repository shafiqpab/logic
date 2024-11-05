<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];




//------------------------------------------------------------------------------------------------------
/*if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company in($data) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/spare_parts_work_order_controller');",0 );
}*/

if($action=="supplier_name_popup")
{
	echo load_html_head_contents("Supplier Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			$('#hidden_supplier_info').val(str);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_supplier_info" id="hidden_supplier_info" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
							<tr>
								<th colspan="2"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th>Supplier Name</th>
							</tr>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:290px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
							<tbody>
								<?  
								   
									if($mst_id=="")
									{
										$data_sql=sql_select("SELECT a.id as ID, a.supplier_name as SUPPLIER_NAME, a.contact_person as CONTACT_PERSON from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company=$cbo_company_name and a.status_active=1 group by a.id,a.supplier_name, a.contact_person order by a.supplier_name");
									}else{
										$data_sql = sql_select("SELECT distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c 
										where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company in($cbo_company_name) and a.status_active IN(1) and a.is_deleted=0 
										union all
										select distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c, WO_NON_ORDER_INFO_MST d 
										where a.id=b.supplier_id and a.id=c.supplier_id and a.id = d.supplier_id and b.party_type in(3) and c.tag_company in($cbo_company_name) and a.status_active IN(1,3) and a.is_deleted=0 and d.id = $mst_id
										order by supplier_name");
									}
									// var_dump($data_sql);die;
									$i=1;
									foreach($data_sql as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[('ID')].'__'.$row[('SUPPLIER_NAME')].'__'.$row[('CONTACT_PERSON')];?>')">
											<td width="50" align="center"><?php echo $i; ?></td>
											<td><p><? echo $row[('SUPPLIER_NAME')]; ?></p></td>
										</tr>
										<?
										$i++;
									}
								?>
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
	exit();
}

if($action=="check_wo_is_approved")
{
    $data=explode("***", $data);
    $company_id=$data[0];
    $wo_no=$data[1];
    $sql="SELECT company_id, page_id from electronic_approval_setup where page_id='626' and company_id=$company_id and is_deleted=0 ";
    $sql_result =sql_select($sql);
    $wo_check = "select id from wo_non_orderinfo_mst where is_approved = 1 AND entry_form = 145 AND company_name = $company_id AND is_deleted = 0 AND a.wo_number='$wo_no'";

	//    $wo_check="SELECT a.id,a.company_name, a.wo_number, c.id AS approval_id, c.sequence_no, c.approved_by FROM wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c WHERE a.id = b.mst_id AND a.id = c.mst_id AND c.entry_form = 3 AND c.current_approval_status = 1 AND a.ready_to_approved = 1 AND a.entry_form = 145 AND a.company_name = $company_id AND a.is_approved IN (1, 3) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.wo_number='$wo_no' order by c.id desc";
    $wo_result=sql_select($wo_check);
    if(count($wo_result)>0 && count($sql_result)>0)
    {
        echo "1***".$wo_result[0][csf('sequence_no')]."***".$wo_result[0][csf('approved_by')];
        exit();
    }
    else{
        echo "0***".$sql."***".$wo_check;;
        exit();
    }
}


if ($action=="necessity_setup_variable_form_lib")
{
    //$data_ref=explode("***",$data);
    $date = date('m/d/Y');

    if($db_type==0){
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date,'yyyy-mm-dd')."' and company_id='$data')) and page_id=21 and status_active=1 and is_deleted=0 order by id desc";
    }else{
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date, "", "",1)."' and company_id='$data')) and page_id=21 and status_active=1 and is_deleted=0 order by id desc";
    }
    $necessity_setup_res=sql_select($necessity_setup_sql);
    $necessity_setup=$necessity_setup_res[0][csf("approval_need")];
    // $necessity_setup=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_importer_id and variable_list=23 and status_active=1","source");

    echo $necessity_setup;die;
}

if ($action=="load_details_container"){   //chemical & dyes

   	$explodeData = explode("**",$data);
	$woBasis = $explodeData[0];
	$company = $explodeData[1];
	//$category = $explodeData[2];

	//if($category==0) { echo ""; die; }

	if($woBasis==2) // independent
	{
		$i=1;

		$user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and item_category_id='$category' and status_active=1 and is_deleted=0");
		$itemAcctDoubleClick="";$itemDescDoubleClick="";
		if($user_given_code_status==1)
			$itemAcctDoubleClick = 'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
		else
			$itemDescDoubleClick = 	'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
 	?>

	<div style="width:1100px;">
			<table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
					<thead>
						<tr>
							<th>Item Account</th>
							<th class="must_entry_caption">Item Description</th>
                           <th>Item Category</th>
							<th>Item Size</th>
							<th>Item Group</th>
                            <th>Remarks</th>
                            <th>Order UOM</th>
							<th class="must_entry_caption">Quantity</th>
							<th class="must_entry_caption">Rate</th>
							<th class="must_entry_caption">Amount</th>
                            <th>Action</th>
						</tr>
					</thead>
                    <tbody>
                        <tr class="general" id="<? echo $i;?>">
                            <td width="80">
                                <input type="text" name="txt_item_acct_<? echo $i;?>" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px"  <? echo $itemAcctDoubleClick; ?> readonly  />
                                <input type="hidden" name="txt_item_id_<? echo $i;?>" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <!-- Only for show. not used for Independent -->
                                <input type="hidden" name="txt_req_no_<? echo $i;?>" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_no_id_<? echo $i;?>" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                                <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value=""  />
                                <!-- END -->
                            </td>
                            <td width="80">
                                <input type="text" name="txt_item_desc_<? echo $i;?>" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" <? echo $itemDescDoubleClick; ?> readonly />
                            </td>
                            <td width="100">
                            <?
                            echo create_drop_down( "cbo_item_category_".$i, 100, $item_category,"", 1, "-- Select --", 0, "",1,"5,6,7,23" );
                            ?>
                            </td>
                            <td width="100">
                                <input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" readonly />
                            </td>
                            <td width="50">
                                <?
                                    echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1 );
                                ?>
                            </td>
                            <td width="100">
                                <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px"  />
                            </td>
                            <td width="100">
                                <?
                                    echo create_drop_down( "cbouom_".$i, 80, $unit_of_measurement,"", 1, "Select", 0, "",1 );
                                ?>
                            </td>
                            <td width="50">
                                <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
                            </td>
                            <td width="50">
                                <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                            </td>
                            <td width="80">
                                <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_total_amount(2)" readonly />
                            </td>
                            <td width="80">
                                 <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                                 <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                            </td>
                        </tr>
                  	</tbody>
                    <tfoot class="tbl_bottom">
                        <tr>
                        	 <? if($val[csf("wo_basis_id")]==1)
                    		{
                        	?>
                       		<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<?
                    		}
							?>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<? if($val[csf("wo_basis_id")]==1)
                    		{
                        	?>
                       		<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<?
                    		}
							?>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>

                            <td align="right" colspan="2">Upcharge Remarks:</td>
                            	<td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Upcharge</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                    		{
                        	?>
                       		<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<?
                    		}
							?>
                        	<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2">Discount Remarks:</td>
							<td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                    		{
                        	?>
                       		<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<?
                    		}
							?>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Net Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
				</table>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</div>
	<?
		exit();
	}
	else //requisition container  header
	{
		?>
			<div style="width:1100px;">
                <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
                        <thead>
                            <tr id="0">
                                <th>Requisition No</th>
                                <th>Item Account</th>
                                <th>Item Description</th>
                                <th>Item Category</th>
                                <th>Item Size</th>
                                <th>Item Group</th>
                                <th>Remarks</th>
                               	<th>Order UOM</th>
                                <th>Req.Qnty</th>
                                <th>WO.Qnty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<!-- append here -->
                        </tbody>
                        <tfoot class="tbl_bottom">
                        <tr>
                        	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                         	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                            <td align="right" colspan="2">Upcharge Remarks:</td>
                            	<td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Upcharge</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                         	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2">Discount Remarks:</td>
							<td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Net Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
                <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
			</div>
		<?
		exit();
	}

}
if ($action=="append_load_details_container"){   //Yarn details append table row

 	$explodeData = explode("**",$data);
	$i = $explodeData[0];
	$company = $explodeData[1];
	//$category = $explodeData[2];

	$user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and status_active=1 and is_deleted=0");
	$itemAcctDoubleClick="";$itemDescDoubleClick="";
	if($user_given_code_status==1)
		$itemAcctDoubleClick = 'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
	else
		$itemDescDoubleClick = 	'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';

	?>

			<tr class="general" id="<? echo $i;?>">
                <td width="80">
                    <input type="text" name="txt_item_acct_<? echo $i;?>" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px"  <? echo $itemAcctDoubleClick; ?> />
                    <input type="hidden" name="txt_item_id_<? echo $i;?>" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <!-- Only for show. not used for Independent -->
                    <input type="hidden" name="txt_req_no_<? echo $i;?>" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_no_id_<? echo $i;?>" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                    <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value=""  />
                    <!-- END -->
                </td>
                <td width="80">
                    <input type="text" name="txt_item_desc_<? echo $i;?>" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" <? echo $itemDescDoubleClick; ?> />
                </td>
                <td width="100">
                    <?
                    echo create_drop_down( "cbo_item_category_".$i, 100, $item_category,"", 1, "-- Select --", 0, "",1,"5,6,7,23" );
                    ?>
                </td>
                <td width="100">
                    <input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                </td>
                <td width="50">
                    <?
                        echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1 );
                    ?>
                </td>
                <td width="100">
                    <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px"  />
                </td>
                <td width="100">
                    <?
                        echo create_drop_down( "cbouom_".$i, 80, $unit_of_measurement,"", 1, "Select", 0, "",1 );
                    ?>
                </td>
                <td width="50">
                    <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
                </td>
                <td width="50">
                    <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                </td>
                <td width="80">
                    <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
                </td>
                <td width="80">
                     <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
			</tr>

	<?
	exit();
}


if($action=="account_order_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>


	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));
			}
		}

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

	function js_set_value(id)
	{
		//alert (id);
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		str=str[1];
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );

		$('#item_1').val( id );

	}
        function showListWithCheck(){
            //var category = document.getElementById('cbo_item_category_id').value;
            if( form_validation('cbo_item_category_id','Item Category')==false )
            {
                    return;
            }
            show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value, 'account_order_popup_list_view', 'search_div', 'dyes_and_chemical_work_order_controller', 'setFilterGrid(\'list_view\',-1)');
        }

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:680px;">
            <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Item Category</th>
                    <th>Item Group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td width="100">
                            <?
                            echo create_drop_down( "cbo_item_category_id", 170, $item_category,"", 1, "-- Select --", 0, "",0,"5,6,7,23" );
                            ?>
                        </td>
                        <td align="center">
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td>
                        <td align="center">
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="showListWithCheck();" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="account_order_popup_list_view")
{
	//echo load_html_head_contents("Item Creation popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	list($company,$itemCategory,$item_description,$item_code,$item_group)=explode('**',$data);
	?>


	</head>
	<body>
		<div align="center" style="width:100%" >
        <form name="order_popup_1" id="order_popup_1" >
        <fieldset style="width:900px">
        <input type="hidden" id="item_1" />
        <?
        if($item_description!=""){$search_con=" and a.item_description like('%$item_description%')";}
        if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
		if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
		if($itemCategory){$search_con .=" and a.item_category_id='$itemCategory'";}
        if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
        $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,
        $sql="select min(a.id) as id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.order_uom as unit_of_measure, a.status_active, b.item_name, sum(a.current_stock) as current_stock, min(a.re_order_label) as re_order_label
        from product_details_master a, lib_item_group b
        where a.item_group_id=b.id and a.is_deleted=0 and company_id='$company'  $itemIDScond $search_con
        group by a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.order_uom, a.status_active, b.item_name";
        //echo $sql;//die;
        echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,ReOrder Labale,Product ID,Status", "120,100,140,80,100,80,80,100,50,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,unit_of_measure,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,unit_of_measure,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,1,0','',1 );
        ?>
        </fieldset>
		</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if ($action=="load_php_popup_to_form")
{
	$explode_data = explode("**",$data);
	$data=$explode_data[0];
	$i=$explode_data[1];
	//$item=$explode_data[2];
    if($data!="")
	{
		$nameArray=sql_select( "select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.order_uom as unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name
		from product_details_master a, lib_item_group b
		where a.id in ($data) and a.status_active=1 and a.item_group_id=b.id");

		$user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and status_active=1 and is_deleted=0");

		foreach ($nameArray as $val)
		{
		?>

			<tr class="general" id="<? echo $i;?>">
                <td width="80">
                    <input type="text" name="txt_item_acct_<? echo $i;?>" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_account")];?>" />
                    <input type="hidden" name="txt_item_id_<? echo $i;?>" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
                    <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                     <!-- Only for show. not used for Independent -->
                    <input type="hidden" name="txt_req_no_<? echo $i;?>" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_no_id_<? echo $i;?>" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                    <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="" />
                    <!----------------- END ------------------------>
                </td>
                <td width="80">
                    <input type="text" name="txt_item_desc_<? echo $i;?>" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_description")];?>"  />
                </td>
                <td width="100">
                    <?
                    echo create_drop_down( "cbo_item_category_".$i, 100, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,"5,6,7,23" );
                    ?>
                </td>
                <td width="100">
                    <input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $val[csf("item_size")];?>" readonly  />
                </td>
                <td width="50">
                    <?
                        echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1 );
                    ?>
                </td>
                 <td width="100">
                    <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px" value="" />
                </td>
                <td width="100">
                    <?
                        echo create_drop_down( "cbouom_".$i, 80, $unit_of_measurement,"", 1, "Select", $val[csf("unit_of_measure")], "",1 );
                    ?>
                </td>
                <td width="50">
                    <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" />
                </td>
                <td width="50">
                    <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                </td>
                <td width="80">
                    <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
                </td>
                <td width="80">
                     <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
            </tr>
         <?
			$i++;
		}
	}
	exit();
}


// buyer order popoup here
if($action=="requitision_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1);
	?>

	<script>
		var selected_id = new Array;
		var selected_number = new Array;
		var selected_dtlsID = new Array;
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				$('#tr_'+i).trigger('click');
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_row_data').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}


		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var kk=0;

		function js_set_value( strParam )
		{

				var chk_st=$('#check_all').is(":checked");
				if(chk_st==true)kk++;

				var splitArr = strParam.split("_");
				var str = splitArr[0];
				var numbers = splitArr[1];
				var ids = splitArr[2]; //requisition id
				var req_dtls_id = splitArr[3];  // item id
				var approval_status = splitArr[4];
				var setup_date_approval = splitArr[5];
				var partial_approve_status = splitArr[6];  // partial approval status (yes or no);
				//alert(setup_date_approval);

				if(setup_date_approval==1 && partial_approve_status==2 )
				{

						if(approval_status!=1)
						{
							if(kk<=1)
							{
								alert("Un-Approved Purchase Requisition Not Selected");
							}
							return;
						}
				}

				js_set_value_for_item(req_dtls_id);

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( ids, selected_id ) == -1 ) {
					selected_id.push( ids );
					selected_number.push( numbers );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == numbers ) break;
					}
					selected_id.splice( i, 1 );
					selected_number.splice( i, 1 );
				}

				var num = id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					num += selected_number[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				num = num.substr( 0, num.length - 1 );

				$('#txt_selected_ids').val( id );
				$('#txt_selected_numbers').val( num );
		}


		function js_set_value_for_item( strParam )
		{

				var req_dtls_id = strParam;  // item id
				//------------------item id-------------
				if( jQuery.inArray( req_dtls_id, selected_dtlsID ) == -1 ) {
					selected_dtlsID.push( req_dtls_id );
				}
				else {
					for( var i = 0; i < selected_dtlsID.length; i++ ) {
						if( selected_dtlsID[i] == req_dtls_id ) break;
					}
					selected_dtlsID.splice( i, 1 );
				}
				//--------------------------------------
				var dtls_id = '';
				for( var i = 0; i < selected_dtlsID.length; i++ ) {
					dtls_id += selected_dtlsID[i] + ',';
				}
				dtls_id = dtls_id.substr( 0, dtls_id.length - 1 );
				$('#txt_selected_dtls_id').val( dtls_id );
		}


		function reset_hidden()
		{
			$('#txt_selected_ids').val('');
			$('#txt_selected_numbers').val('');
			$('#txt_selected_dtls_id').val('');

			/*if($("#txt_selected_ids").val()!="")
			{
				var selectID = $('#txt_selected_ids').val().split(",");
				var selectName = $('#txt_selected_numbers').val().split(",");
				var selectedDtlsID = $('#txt_selected_dtls_id').val().split(",");
				for(var i=0;i<selectID.length;i++)
				{
					selected_id.push( selectID[i] );
					selected_number.push( selectName[i] );
				}
				for(var i=0;i<selectedDtlsID.length;i++)
				{
					selected_dtlsID.push( selectedDtlsID[i] );
				}
			}*/
		}

		</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >

	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="980" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="150">Company Name</th>
								<th width="150">Category Name</th>
								<th width="100">Reqsition No</th>
								<th width="100">Approval Type</th>
								<th width="200">Date Range</th>
								<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
							</thead>
							<tr>
								<td width="150">
									<?
										echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company, "",1 );
									?>
								</td>
								<td width="150">
									<?
										echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", $category, "",0,"5,6,7,23" );
									?>
								</td>
								<td  align="center"> <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px;" ></td>
								<td>
									<?
									if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
									else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
									$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=13 and status_active=1 and is_deleted=0";
									$app_need_setup=sql_select($approval_status);
									echo create_drop_down( "cbo_approval_type", 100, $yes_no, "", 1, "-- Select--", $app_need_setup[0][csf("approval_need")], "","","" );
									?>
								</td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									<input type="hidden" id="txt_selected_ids" name="txt_selected_ids" value="<? echo $req_numbers_id; ?>" />
									<input type="hidden" id="txt_selected_numbers" name="txt_selected_numbers" value="<? echo $req_numbers; ?>" />
									<input type="hidden" id="txt_selected_dtls_id" name="txt_selected_dtls_id" value="<? echo $txt_req_dtls_id; ?>" />
								</td>
								<td align="center">
									<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $txt_req_dtls_id; ?>'+'_'+'<? echo $garments_nature; ?>'+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('cbo_approval_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_requisition_search_list_view', 'search_div', 'dyes_and_chemical_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:100px;" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top" id="search_div"></td>
				</tr>
		</table>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_requisition_search_list_view")
{

 	extract($_REQUEST);
	$ex_data = explode("_",$data);
	$companyName = $ex_data[0];
	$itemCategory = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$req_dtls_id = $ex_data[4];
  	$garments_nature = $ex_data[5]; // not used here
	$req_no = $ex_data[6];
    $approval_type=$ex_data[7];
	$reqsition_year=$ex_data[8];

	$sql_cond="";
 	if($companyName!=0)
		$sql_cond = " and a.company_id = '".$companyName."'";
	if($itemCategory!=0)
		$sql_cond .= " and b.item_category = '".$itemCategory."'";
	/* 	if($txt_date_from!="" || $txt_date_to!="")
			$sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	*/  	//if($req_dtls_id!="") $sql_cond .= " and b.id NOT IN ($req_dtls_id)";

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		else if($db_type==2) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
	}

	if ($req_no!="")
	{
		$sql_cond .=" and a.requ_prefix_num=$req_no";
	}

    $approval_cond='';
    if ($approval_type==1) $approval_cond=" and a.is_approved in(1,3)";
    if ($approval_type==2) $approval_cond=" and a.is_approved=0";

	if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$reqsition_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$reqsition_year.""; }

	if($req_dtls_id=="") $req_dtls_id=0;
 	$prev_req_wo=return_library_array("SELECT requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity from  wo_non_order_info_dtls where status_active=1 and requisition_dtls_id<>0 and requisition_dtls_id not in($req_dtls_id) group by requisition_dtls_id  order by requisition_dtls_id","requisition_dtls_id","supplier_order_quantity");


	$setup_date_array=return_field_value("approval_need","approval_setup_mst a,approval_setup_dtls b"," a.company_id='".$companyName."' and a.id=b.mst_id and b.page_id=13  and setup_date=(select  MAX(setup_date) AS setup_date from approval_setup_mst where  company_id='".$companyName."' and status_active=1 and is_deleted=0)","approval_need");
	$allow_partial_status = return_field_value("allow_partial","approval_setup_mst a,approval_setup_dtls b"," a.company_id='".$companyName."' and a.id=b.mst_id and b.page_id=13  and setup_date=(select  MAX(setup_date) AS setup_date from approval_setup_mst where  company_id='".$companyName."' and status_active=1 and is_deleted=0)","allow_partial");
	//echo $setup_date_array;die;

	$sql = "select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.location_id, a.is_approved, c.item_account, c.item_description, c.item_group_id, c.item_size, b.id as req_dtls_id, b.quantity,a.ready_to_approve,d.current_approval_status
	from inv_purchase_requisition_mst a 
	left join approval_history d on a.id= d.mst_id and d.entry_form=1 and d.current_approval_status=1
	, inv_purchase_requisition_dtls b 
	left join product_details_master c on b.product_id = c.id and c.status_active in(1,3)
	where a.id=b.mst_id and a.status_active=1 and a.pay_mode<>4 and b.status_active=1 and a.is_deleted=0 and b.item_category in(5,6,7,23) $sql_cond $approval_cond
	order by requ_no,requisition_date";
	 //echo $sql;
	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
 	$item_name=return_library_array("select id,item_name from lib_item_group",'id','item_name');

	?>
    <div style="margin-top:10px; width:1120px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="60">Requisition No</th>
                <th width="65">Requisition Date</th>
                <th width="110">Company</th>
                <th width="110">Location</th>
                <th width="90">Item Account</th>
                <th width="140">Description</th>
                <th width="90">Item Group</th>
                <th width="80">Item Size</th>
                <th width="80">Requisition Qnty</th>
                <th width="80">Prev. WO Qnty</th>
                <th width="80">Balance</th>
                <th>Approval Status</th>
            </thead>
		</table>
		<div style="width:1120px; overflow-y:scroll; max-height:200px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="table_body">
                <?php
					$i = 1;
					$txt_row_data = "";
					$hidden_dtls_id = explode(",", $req_dtls_id);
					$nameArray = sql_select($sql);
					foreach ($nameArray as $selectResult)
					{

						if($setup_date_array==1 && $allow_partial_status == 2)
						{
							if ($selectResult[csf('is_approved')]==1){
								//$bgcolor = "#E9F3FF";
								$bgcolor = "#E9F3FF";
							}else {
								$bgcolor = "#FF0000";
							}
						}
						else
						{
							if ($i % 2 == 0) {
								$bgcolor = "#E9F3FF";
							} else {
								$bgcolor = "#FFFFFF";
							}
						}
						$balance=$selectResult[csf("quantity")]- $prev_req_wo[$selectResult[csf("req_dtls_id")]];
						if ($selectResult[csf("quantity")] > $prev_req_wo[$selectResult[csf("req_dtls_id")]])
						{
							$data = $i . "_" . $selectResult[csf('requ_no')] . "_" . $selectResult[csf('id')] . "_" . $selectResult[csf('req_dtls_id')] . "_" . $selectResult[csf('is_approved')] . "_" .$setup_date_array . "_" .$allow_partial_status;

							if (in_array($selectResult[csf('req_dtls_id')], $hidden_dtls_id)) {
								if ($txt_row_data == "") {
									$txt_row_data = $data;
								} else {
									$txt_row_data .= "," . $data;
								}
							}

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $data; ?>')">
								<td width="30" align="center"><?php echo "$i"; ?></td>
								<td width="60" align="center" title="<? echo $setup_date_array;?>"><p><?php echo $selectResult[csf('requ_prefix_num')]; ?></p></td>
								<td width="65" align="center"><?php echo change_date_format($selectResult[csf('requisition_date')]); ?></td>
								<td width="110"><p><?php echo $company[$selectResult[csf('company_id')]]; ?></p></td>
								<td width="110"><p><?php echo $location[$selectResult[csf('location_id')]]; ?>&nbsp;</p></td>
								<td width="90"><p><?php echo $selectResult[csf('item_account')]; ?>&nbsp;</p></td>
								<td width="140"><p><?php echo $selectResult[csf('item_description')]; ?></p></td>
								<td width="90"><p><?php echo $item_name[$selectResult[csf('item_group_id')]]; ?></p></td>
								<td width="80"><p><?php echo $selectResult[csf('item_size')]; ?></p></td>
                                <td width="80" align="right"><?php echo number_format($selectResult[csf('quantity')],2); ?></td>
                                <td width="80" align="right"><?php echo number_format($prev_req_wo[$selectResult[csf("req_dtls_id")]],2); ?></td>
                                <td width="80" align="right"><?php echo number_format($balance,2); ?></td>
                                <td align="center"><p>
                                    <?
                                    if ($selectResult[csf("is_approved")]==1) $approved_msg='Full Approved';
                                    else if ($selectResult[csf("is_approved")]==3) $approved_msg='Partial Approved';
                                    else $approved_msg='Not Approved';
                                    echo $approved_msg;
                                    ?>
                                </p></td>
							</tr>
							<?
							$i++;
						}
					}
				?>
				<input type="hidden" name="txt_row_data" id="txt_row_data" value="<?php echo $txt_row_data; ?>"/>
			</table>
		</div>
        <table width="1100" cellspacing="0" cellpadding="0" border="1" align="left">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
	<?
	/*$arr=array (2=>$company,3=>$location,6=>$item_name);

	echo  create_list_view("table_body", "Requisition No, Requisition Date, Company, Location, Item Account, Description, Item Group, Item Size", "120,80,100,110,110,100,90,80","900","200", 0, $sql, "js_set_value", "requ_no,id,req_dtls_id", "",1,"0,0,company_id,location_id,0,0,item_group_id,0", $arr, "requ_no,requisition_date,company_id,location_id,item_account,item_description,item_group_id,item_size","dyes_and_chemical_work_order_controller","setFilterGrid('table_body',-1)",'0,3,0,0,0,0,0,0',"",1) ;*/

	exit();
}



if($action=="show_dtls_listview")
{
	extract($_REQUEST);
	$explodeData = explode("**",$data);
 	$requisition_numberID = str_replace("'","",$explodeData[0]);
	$requisition_numberID_arr=explode(",",$requisition_numberID);
	$reqDtlsID = $explodeData[1];
	$rowNo = $explodeData[2];
	$update_id = $explodeData[3];
	$update_cond="";
	if($update_id>0) $update_cond=" and mst_id!=$update_id";
    if($update_id>0) $wo_update_cond=" and e.mst_id=$update_id";
	if($reqDtlsID=="") return; // for empty request
	//echo "select requisition_no,sum(supplier_order_quantity) as order_quantity,requisition_dtls_id  from wo_non_order_info_dtls where requisition_no in ('".implode("','",$requisition_numberID_arr)."') and status_active=1 and is_deleted=0 $update_cond group by requisition_no,requisition_dtls_id";die;
	$item_group_arr=return_library_array( "SELECT id,item_name  from lib_item_group where status_active=1",'id','item_name');
	$sql=sql_select("select requisition_no,sum(supplier_order_quantity) as order_quantity,requisition_dtls_id  from wo_non_order_info_dtls where requisition_no in ('".implode("','",$requisition_numberID_arr)."') and status_active=1 and is_deleted=0 $update_cond group by requisition_no,requisition_dtls_id");
	$requisitionQnty=array();
	foreach($sql as $result)
	{
		$requisitionQnty[$result[csf("requisition_no")]][$result[csf("requisition_dtls_id")]]=$result[csf("order_quantity")];
	}

 	$sql = "select a.id as requisition_id,a.requ_no,b.id,c.item_account,c.id as item_id,c.item_description,b.item_category,c.item_size,c.item_group_id,c.unit_of_measure as cons_uom,b.quantity,b.rate,b.amount 
	from inv_purchase_requisition_mst a, product_details_master c, inv_purchase_requisition_dtls b 
	where a.status_active=1 and b.status_active=1 and c.status_active in(1,3) and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.id=b.mst_id and b.product_id=c.id and b.id in ($reqDtlsID)";
	//echo $sql;//die;
	$sqlResult = sql_select($sql);
	if( count($sqlResult)==0 ){ echo "No Data Found";die;}

    if ($update_id!="")
    {
        $wo_sql="SELECT e.requisition_no as requisition_id, e.requisition_dtls_id, e.id as wo_dtls_id, e.rate as wo_rate
        from wo_non_order_info_mst a, inv_purchase_requisition_dtls b, wo_non_order_info_dtls e
        where a.id=e.mst_id and e.requisition_dtls_id=b.id $wo_update_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id in($reqDtlsID)";
        //echo $wo_sql;
        $req_arr = array();
        $woSqlResult = sql_select($wo_sql);
        foreach($woSqlResult as $row)
        {
            $req_arr[$row[csf("requisition_id")]][$row[csf("requisition_dtls_id")]]['wo_dtls_id']=$row[csf("wo_dtls_id")];
            $req_arr[$row[csf("requisition_id")]][$row[csf("requisition_dtls_id")]]['wo_rate']=$row[csf("wo_rate")];
        }
    }

	$i=$rowNo+1; // row no increse 1
	foreach($sqlResult as $key=>$val)
	{
        $wo_dtls_id=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_dtls_id'];
        $wo_rate=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_rate'];
	    ?>
        <tbody>
			<tr class="general" id="<? echo $i;?>">
				<td width="80">
					<input type="text" name="txt_req_no_<? echo $i;?>" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
					<input type="hidden" name="txt_item_id_<? echo $i;?>" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
					<input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
					<input type="hidden" name="txt_req_no_id_<? echo $i;?>" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_id")];?>" readonly />
					<input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $wo_dtls_id;?>" />

				</td>
				<td width="80">
					<input type="text" name="txt_item_acct_<? echo $i;?>" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>"  />
				</td>
				<td width="80">
					<input type="text" name="txt_item_desc_<? echo $i;?>" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" readonly title="<? echo $val[csf("item_description")];?>" value="<? echo $val[csf("item_description")];?>"  />
				</td>
                <td width="80">
					<?
                    echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category")], "",1,"5,6,7,23" );
					//echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1 );
					?>
				</td>
				<td width="80">
					<input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" readonly title="<? echo $val[csf("item_size")];?>" value="<? echo $val[csf("item_size")];?>" />
				</td>
				<td width="50" title="<?echo $item_group_arr[$val[csf("item_group_id")]];?>">
					<?
						echo create_drop_down( "cbogroup_".$i, 80, $item_group_arr,"", 1, "Select", $val[csf("item_group_id")], "",1 );
					?>
				</td>
				<td width="90">
					<input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:90px"  value="" />
				</td>
				<td width="60">
					<?
						echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", $val[csf("cons_uom")], "",1 );
					?>
				</td>
				<td width="50">
					<? $quantityRemaing = $val[csf("quantity")] - $requisitionQnty[$val[csf("requisition_id")]][$val[csf("id")]];?>
					<input type="text" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="<? echo $quantityRemaing;?>" />
				</td>
				<td width="50">
					<input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? echo $quantityRemaing;?>" />	<!-- This is wo qnty here -->
				</td>
				<td width="50">
                    <?
                    if($wo_rate!=''){
                        $rate=$wo_rate;
                        $disabled="disabled";
                    }else{
                        $rate=$val[csf("rate")];
                        $disabled="";
                    }
                    ?>
					<input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  <? echo $disabled; ?>  style="width:50px;" value="<? echo $rate;?>" />
				</td>
				<td width="80">
					<input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo $rate*$quantityRemaing;?>" />
				</td>
				<td width="80">
					  <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
				</td>
			</tr>
	  </tbody>

	<?
		$i++;
		}
		 ?>
		 <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $val[csf("wo_amount")];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" colspan="2">Upcharge Remarks:</td>
                            	<td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $val[csf("up_charge")];?>" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td align="right" colspan="2">Discount Remarks:</td>
					<td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $val[csf("discount")];?>" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $val[csf("net_wo_amount")];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
           	</tfoot>
		 <?
	exit();
}



if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode,1);
	extract($_REQUEST);

	$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
	$terms_name = "";
	foreach( $terms_sql as $result )
	{
		$terms_name.= '{value:"'.str_replace('"',"&quot;",$result[csf('terms')]).'",id:'.$result[csf('id')]."},";
	}

	?>
	<script>

		function termsName(rowID)
		{
			$("#termsconditionID_"+rowID).val('');

			$(function() {
				var terms_name = [<? echo substr($terms_name, 0, -1); ?>];
				$("#termscondition_"+rowID).autocomplete({
					source: terms_name,
					select: function (event, ui) {
						$("#termscondition_"+rowID).val(ui.item.value); // display the selected text
						$("#termsconditionID_"+rowID).val(ui.item.id); // save selected id to hidden input
					}
				});
			});
		}


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
				$("#tbl_termcondi_details tr:last td:first").html(i);
				$('#termscondition_'+i).removeAttr("onKeyPress").attr("onKeyPress","termsName("+i+");");
				$('#termscondition_'+i).removeAttr("onKeyUp").attr("onKeyUp","termsName("+i+");");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$('#termsconditionID_'+i).val("");
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

		/*function fnc_work_order_terms_condition( operation )
		{
				var row_num=$('#tbl_termcondi_details tr').length-1;
				var data_all="";
				for (var i=1; i<=row_num; i++)
				{
					if (form_validation('termscondition_'+i,'Term Condition')==false)
					{
						return;
					}

					data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"../../../");
				}
				var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
				//freeze_window(operation);
				http.open("POST","dyes_and_chemical_work_order_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_yarn_order_terms_condition_reponse;
		}

		function fnc_yarn_order_terms_condition_reponse()
		{

			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
					if (reponse[0].length>2) reponse[0]=10;
					if(reponse[0]==0 || reponse[0]==1)
					{
						parent.emailwindow.hide();
					}
			}
		}*/


			function fnc_work_order_terms_condition( operation )
			{
				var row_num=$('#tbl_termcondi_details tr').length-1;
				var data_all="";
				for (var i=1; i<=row_num; i++)
				{
					if (form_validation('termscondition_'+i,'Term Condition')==false)
					{
						return;
					}

					data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"../../../");
				}
				var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
				//freeze_window(operation);
				http.open("POST","dyes_and_chemical_work_order_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_yarn_order_terms_condition_reponse;
			}

			function fnc_yarn_order_terms_condition_reponse()
			{
				if(http.readyState == 4)
				{
					//alert(http.responseText);
					var reponse=trim(http.responseText).split('**');
					if (reponse[0].length>2) reponse[0]=10;
					if(reponse[0]==0 || reponse[0]==1)
					{
						parent.emailwindow.hide();
					}
				}
			}


	</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
		<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<fieldset>
		<form id="termscondi_1" autocomplete="off">
				<input type="hidden" id="txt_wo_number" name="txt_wo_number" value="<? echo str_replace("'","",$update_id) ?>"/>
				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
						<thead>
							<tr>
								<th width="50">Sl</th><th width="530">Terms</th><th ></th>
							</tr>
						</thead>
						<tbody>
						<?
						$terms_and_conditionID = return_field_value("terms_and_condition","wo_non_order_info_mst","id = $update_id");
						$flag=0;
						if($terms_and_conditionID=="")
							$condd = " is_default=1";
						else
						{
							$condd = " id in ($terms_and_conditionID)";
							$flag=1;
						}
						$data_array=sql_select("select id, terms from lib_terms_condition where $condd order by id");
						if( count($data_array)>0 )
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
										<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" onKeyPress="termsName(<? echo $i;?>)" onKeyUp="termsName(<? echo $i;?>)" />
										<input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" value="<? echo $row[csf('id')]; ?>"  readonly />
										</td>
										<td>
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
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
										echo load_submit_buttons( $permission, "fnc_work_order_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
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
}


if($action=="save_update_delete_terms_condition")
{
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
		$terms_name = array();
		foreach( $terms_sql as $result )
		{
			$terms_name[$result[csf('terms')]] = $result[csf('id')];
		}

		 $id=return_next_id( "id", "lib_terms_condition", 1 );
		 $field_array = "id,terms"; $data_array = "";
		 $idsArr = "";$j=0;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition = "termscondition_".$i;
			 $termscondition = $$termscondition;
			 $termsconditionID = "termsconditionID_".$i;
			 $termsconditionID = $$termsconditionID;
			 if(str_replace("'","",$termsconditionID) == "")
			 {
				 $j++;
				 if ($j!=1){ $data_array .=",";}
				 $data_array .="(".$id.",".$termscondition.")";
				 $idsArr[]=$id;
				 $id=$id+1;
			 }
			 else
			 {
				 $idsArr[]=$termsconditionID;
			 }
		 }

 		if($data_array!="")
		{
			$CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,0);
		}

		foreach($idsArr as &$value){
		   $value = str_replace("'","",$value);
		}
		$idsArr = implode(",", $idsArr);
		$rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","id",$txt_wo_number,1);
		//echo $CondrID;die;
		//oci_commit($con); oci_rollback($con);
		if($db_type==0)
		{
			if( $rID && $data_array!="" && $CondrID){
				mysql_query("COMMIT");
				echo "0**";
			}
			else if($rID && $data_array==""){
				mysql_query("COMMIT");
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}

		if( $rID && $data_array!="" && $CondrID){
				oci_commit($con);
				echo "0**";
			}
			else if($rID && $data_array==""){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		disconnect($con);
		die;
	}
}


if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_place_of_delivery=str_replace("'","",$txt_place_of_delivery); 
	$cbo_lc_type = str_replace("'","",$cbo_lc_type);

    if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        $txt_place_of_delivery=str_replace("'","",$hidden_delivery_info_dtls);
    }

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}


		//-----------------------------------------------wo_non_order_info_mst table insert START here----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		$id=return_next_id("id", "wo_non_order_info_mst", 1);

		if($db_type==2 || $db_type==1 )
		{
				$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
		}
		else if ($db_type==0)
		{
				$mrr_date_check="and year(insert_date)=".date('Y',time())."";
		}
		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and entry_form = 145 $mrr_date_check order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));

		$field_array_mst ="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, entry_form,requisition_no,delivery_place, wo_date, supplier_id, attention, wo_basis_id, currency_id, delivery_date, source, pay_mode, wo_amount, up_charge, discount, net_wo_amount,upcharge_remarks,discount_remarks,contact,wo_type,remarks,reference, inserted_by, insert_date,ready_to_approved, inco_term_id,payterm_id,tenor,port_of_loading,place_of_delivery,lc_type";

		//echo $field_array."<br>".$data_array;die;,txt_delivery_place
 		//$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
 		//-----------------------------------------------wo_non_order_info_mst table insert END here-------------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//



		//-----------------------------------------------wo_non_order_info_dtls table insert START here----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		$total_row = str_replace("'","",$total_row);
		$field_array="id, mst_id, requisition_dtls_id, requisition_no, item_id,remarks, uom, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount,item_category_id, inserted_by, insert_date";
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		$dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
		$data_array=""; $req_no_id_mst='';
		$check_item_id=array();
		for($i=1;$i<=$total_row;$i++)
		{
			if($i>1) $data_array.=",";
			$req_no_id	 	= "txt_req_no_id_".$i;
			$req_dtls_id	= "txt_req_dtls_id_".$i;
			$item_id 	 	= "txt_item_id_".$i;
			$item_acct 	 	= "txt_item_acct_".$i;
			$item_desc	 	= "txt_item_desc_".$i;
            $item_category	= "cbo_item_category_".$i;
			$item_size	 	= "txt_item_size_".$i;
			$cbogroup	 	= "cbogroup_".$i;
			$txt_remarks    = "txt_remarks_".$i;
			$cbouom	 		= "cbouom_".$i;
			$txt_req_qnty   = "txt_req_qnty_".$i; 	//reuisition qnty
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$txt_amount  	= "txt_amount_".$i;
            if( str_replace("'","",$cbo_wo_basis) == 1 && str_replace("'","",$hid_approval_necessity_setup) != 1)
			{
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) ){
                    echo "11**Work Order Qty Can't over than Requisition Qty";check_table_status( 175,0);disconnect($con);die;
                }
            }

            if(str_replace("'","",$cbo_wo_basis)==2) // Independent Basis
            {
                if($check_item_id[str_replace("'","",$$item_id)]!=""){
                    echo "11**Duplicate Item Not Allow In Same WO**0";
                    check_table_status( 175,0);
                    disconnect($con);die;
                }
            }

			/*if($check_item_id[str_replace("'","",$$item_id)]!="")
			{
				echo "11**Duplicate Item Not Allow In Same WO**0";
				check_table_status( 175,0);
				disconnect($con);die;
			}*/

			$check_item_id[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);


			$perc=(str_replace("'","",$$txt_amount)/str_replace("'","",$txt_total_amount))*100;
			$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
			$net_rate=$net_amount/str_replace("'","",$$txt_quantity);

			$net_rate=number_format($net_rate,4,'.','');
			$net_amount=number_format($net_amount,4,'.','');


			if( str_replace("'","",$$txt_quantity) != "" )
			{
				$data_array.="(".$dtlsid.",".$id.",".$$req_dtls_id.",".$$req_no_id.",".$$item_id.",".$$txt_remarks.",".$$cbouom.",".$$txt_req_qnty.",".$$txt_quantity.",'".$net_rate."','".$net_amount."',".$$txt_rate.",".$$txt_amount.",".$$item_category.",'".$user_id."','".$pc_date_time."')";
				$dtlsid=$dtlsid+1;
                $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
			}
		}
        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));

		// echo "50**".$txt_place_of_delivery; die;'".$txt_place_of_delivery."'

        $data_array_mst ="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",145,'".$req_no_id_mst."',".$delivery_address.",".$txt_wo_date.",".$cbo_supplier.",".$txt_attention.",".$cbo_wo_basis.",".$cbo_currency.",".$txt_delivery_date.",".$cbo_source.",".$cbo_pay_mode.",".$txt_total_amount.",".$txt_upcharge.",".$txt_discount.",".$txt_total_amount_net.",".$txt_up_remarks.",".$txt_discount_remarks.",".$txt_contact.",".$cbo_wo_type.",".$txt_remarks_mst.",".$txt_ref.",'".$user_id."','".$pc_date_time."',".$cbo_ready_to_approved.",".$cbo_inco_term.",".$cbo_payterm_id.",".$txt_tenor.",".$txt_port_of_loading.",'".$txt_place_of_delivery."',".$cbo_lc_type.")";
         //echo "10**insert into wo_non_order_info_mst(".$field_array_mst.") values ".$data_array_mst.""; check_table_status( 175,0); die;
		$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array,$data_array,1);
		//-----------------------------------------------wo_non_order_info_dtls table insert END here-----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		// echo "10**".$rID ."_". $dtlsrID;
        // check_table_status( 175,0); die;



		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".$new_wo_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".$new_wo_number[0]."**".$id."**".$dtlsid_check;;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
			//echo $dtlsrID."**".$new_wo_number[0]."**".$id."**".$dtlsid_check;//
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		if($db_type==0)	{ mysql_query("BEGIN");}
		//table lock here
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}

		$update_check=str_replace("'","",$update_id);
		if($update_check<1)
		{
			echo "15**0";check_table_status( 175,0);disconnect($con);die;
		}

		$mst_sql=sql_select("select supplier_id, pay_mode, currency_id from wo_non_order_info_mst where id=$update_check and status_active=1");
		$prev_supplier=$mst_sql[0][csf("supplier_id")];
		$prev_pay_mode=$mst_sql[0][csf("pay_mode")];
		$prev_currency_id=$mst_sql[0][csf("currency_id")];


	//		if($update_check>0 && $pay_mode==2)
	//		{
	//			$pi_sql=sql_select("select a.id as pi_id, a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=11 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$update_check");
	//			if(count($pi_sql)>0)
	//			{
	//				echo "11**PI Number Found :".$pi_sql[0][csf("pi_number")]." \n So Update/Delete Not Possible.";check_table_status( 175,0);disconnect($con);die;
	//			}
	//		}
	//		//echo "10**jahid".$update_check."==".$cbo_pay_mode;check_table_status( 175,0);die;
	//
	//		if($update_check>0 && $pay_mode!=2)
	//		{
	//			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number from inv_receive_master a where a.entry_form=20 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.booking_id=$update_check");
	//			if(count($mrr_sql)>0)
	//			{
	//				echo "11**Receive Number Found :".$mrr_sql[0][csf("recv_number")]." \n So Update/Delete Not Possible.";check_table_status( 175,0);disconnect($con);die;
	//			}
	//		}

		$wo_data=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$item_id 	 	= "txt_item_id_".$i;
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$wo_data[str_replace("'","",$$item_id)]["quantity"]+=str_replace("'","",$$txt_quantity);
			$wo_data[str_replace("'","",$$item_id)]["rate"]=str_replace("'","",$$txt_rate);
		}

		$pi_mrr_check=0;
		if($prev_pay_mode==2)
		{
			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.item_category_id in(5,6,7,23) and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$update_check");
			if(count($pi_sql)>0)
			{

				$pi_mrr_check=1;
				$pi_data=array();
				foreach($pi_sql as $row)
				{
					$pi_data[$row[csf("item_prod_id")]]["quantity"]+=$row[csf("quantity")];
					$pi_data[$row[csf("item_prod_id")]]["rate"]=$row[csf("rate")];
				}
				foreach($pi_data as $prod_id=>$prod_pi_val)
				{
					//if($wo_data[$prod_id]["quantity"]< $prod_pi_val["quantity"] && $wo_data[$prod_id]["rate"]!=$prod_pi_val["rate"])
					if($wo_data[$prod_id]["quantity"] < $prod_pi_val["quantity"])
					{
						echo "11**PI Number Found, WO Quantity Not Allow Less Then PI Quantity  Or Rate Change Not Allow. \n So Update/Delete Not Possible.**$update_check";check_table_status( 175,0);disconnect($con);die;
					}
				}

			}
		}
		else
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=4 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$update_check");
			if(count($mrr_sql)>0)
			{
				$pi_mrr_check=2;
				$mrr_data=array();
				foreach($mrr_sql as $row)
				{
					$mrr_data[$row[csf("prod_id")]]["quantity"]+=$row[csf("order_qnty")];
					$mrr_data[$row[csf("prod_id")]]["rate"]=$row[csf("order_rate")];
				}

				foreach($mrr_data as $prod_id=>$prod_mrr_val)
				{
					//if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"] && $wo_data[$prod_id]["rate"]!=$prod_mrr_val["rate"])
					if($wo_data[$prod_id]["quantity"] < $prod_mrr_val["quantity"])
					{
						echo "11**Receive Number Found, WO Quantity Or Rate  Not Allow Less Then MRR Quantity and Rate,  \n So Update/Delete Not Possible.**$update_check";check_table_status( 175,0);disconnect($con);die;
					}
				}
			}

		}
		//echo "10**jahid".$update_check."==".$cbo_pay_mode;check_table_status( 175,0);die;


		//echo $update_id;die;
		//-----------------------------------------------wo_non_order_info_mst table UPDATE START here----------------------------------//".$txt_delivery_place."
		//-------------------------------------------------------------------------------------------------------------------------------//
		if( $pi_mrr_check>0 && ($prev_supplier!=str_replace("'","",$cbo_supplier) || $prev_pay_mode!=str_replace("'","",$cbo_pay_mode) || $prev_currency_id!=str_replace("'","",$cbo_currency)) )
		{
			if($pi_mrr_check==1)
			{
				echo "11**Pi Found, Master Part Update Not Allow.";
				check_table_status( 175,0);
				disconnect($con);die;
			}
			else
			{
				echo "11**Mrr Found, Master Part Update Not Allow.";
				check_table_status( 175,0);
				disconnect($con);die;
			}
		}

		$update_check = return_field_value("id","wo_non_order_info_mst","id=$update_check");

		//*

		//echo $field_array."<br />".$data_array;die;
		//$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$update_check,0);
 		//-----------------------------------------------wo_non_order_info_mst table UPDATE END here-------------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//



		//-----------------------------------------------wo_non_order_info_dtls table UPDATE START here----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		$total_row = str_replace("'","",$total_row);
		$txt_delete_row = str_replace("'","",$txt_delete_row);
		//echo $txt_delete_row;die;
		/*if($txt_delete_row!="")
		{
			$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",0);
			//$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
		}*/
		//echo $delete_details;die;

 		$field_array_insert="id, mst_id, requisition_dtls_id, requisition_no, item_id, uom, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount,item_category_id, status_active, is_deleted, inserted_by, insert_date";
 		$field_array="requisition_dtls_id*requisition_no*item_id*remarks*uom*req_quantity*supplier_order_quantity*gross_rate*gross_amount*rate*amount*item_category_id*status_active*is_deleted*updated_by*update_date";
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		$dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
		$data_array=array(); $req_no_id_mst='';
		for($i=1;$i<=$total_row;$i++)
		{

			$req_no_id	 	= "txt_req_no_id_".$i;
			$req_dtls_id	= "txt_req_dtls_id_".$i;
			$item_id 	 	= "txt_item_id_".$i;
			$item_acct 	 	= "txt_item_acct_".$i;
			$item_desc	 	= "txt_item_desc_".$i;
			$item_size	 	= "txt_item_size_".$i;
			$cbogroup	 	= "cbogroup_".$i;
			$txt_remarks    = "txt_remarks_".$i;
			$cbouom	 		= "cbouom_".$i;
			$txt_req_qnty   = "txt_req_qnty_".$i; 	//reuisition qnty
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$txt_amount  	= "txt_amount_".$i;
			$item_category	 	= "cbo_item_category_".$i;
			$dtls_ID  		= "txt_row_id_".$i;
			$dtlsID 		= str_replace("'","",$$dtls_ID);
			 
            if( str_replace("'","",$cbo_wo_basis) == 1 && str_replace("'","",$hid_approval_necessity_setup) != 1){
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) ){
                    echo "11**Work Order Qty Can't over than Requisition Qty";check_table_status( 175,0);disconnect($con);die;
                }
            }

            if(str_replace("'","",$cbo_wo_basis)==2) // Independent Basis
            {
                if($check_item_id[str_replace("'","",$$item_id)]!=""){
                    echo "11**Duplicate Item Not Allow In Same WO**0";
                    check_table_status( 175,0);
                    disconnect($con);die;
                }
            }

			/*if($check_item_id[str_replace("'","",$$item_id)]!="")
			{
				echo "11**Duplicate Item Not Allow In Same WO**$update_check";
				check_table_status( 175,0);
				disconnect($con);die;
			}*/

			$check_item_id[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);

			$perc=(str_replace("'","",$$txt_amount)/str_replace("'","",$txt_total_amount))*100;
			$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
			$net_rate=$net_amount/str_replace("'","",$$txt_quantity);

			$net_rate=number_format($net_rate,4,'.','');
			$net_amount=number_format($net_amount,4,'.','');


			//echo $dtlsID."==";
			if($dtlsID>0) //update
			{
				$updatedtls_ID[]=$dtlsID;
				$data_array[$dtlsID]=explode("*",("".$$req_dtls_id."*".$$req_no_id."*".$$item_id."*".$$txt_remarks."*".$$cbouom."*".$$txt_req_qnty."*".$$txt_quantity."*".$$txt_rate."*".$$txt_amount."*".$net_rate."*".$net_amount."*".$$item_category."*1*0*'".$user_id."'*'".$pc_date_time."'"));
 			}
			else  // new insert
			{
				if( str_replace("'","",$$txt_quantity) !="" )
				{
					//$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
					if($data_array_insert!="")$data_array_insert .=",";
					$data_array_insert .="(".$dtlsid.",".$update_check.",".$$req_dtls_id.",".$$req_no_id.",".$$item_id.",".$$cbouom.",".$$txt_req_qnty.",".$$txt_quantity.",'".$net_rate."','".$net_amount."',".$$txt_rate.",".$$txt_amount.",".$$item_category.",1,0,'".$user_id."','".$pc_date_time."')";
					$dtlsid=$dtlsid+1;
				}
			}
            $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
		}
        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));
		//supplier_id*pay_mode*wo_basis_id*currency_id
		//*".$cbo_supplier."*".$cbo_pay_mode."*".$cbo_wo_basis."*".$cbo_currency."
		if($pi_mrr_check>0)
		{
			
			$field_array_mst="requisition_no*delivery_place*wo_date*attention*delivery_date*pay_mode*source*currency_id*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*contact*wo_type*remarks*reference*updated_by*update_date*ready_to_approved*inco_term_id*payterm_id*tenor*port_of_loading*place_of_delivery*lc_type";  

        	$data_array_mst="'".$req_no_id_mst."'*".$delivery_address."*".$txt_wo_date."*".$txt_attention."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_currency."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_discount_remarks."*".$txt_contact."*".$cbo_wo_type."*".$txt_remarks_mst."*".$txt_ref."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_payterm_id."*".$txt_tenor."*".$txt_port_of_loading."*'".$txt_place_of_delivery."'*".$cbo_lc_type."";
		}
		else
		{
			 
			$field_array_mst="supplier_id*pay_mode*requisition_no*delivery_place*wo_date*attention*delivery_date*pay_mode*source*currency_id*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*contact*wo_type*remarks*reference*updated_by*update_date*ready_to_approved*inco_term_id*payterm_id*tenor*port_of_loading*place_of_delivery*lc_type";

        	$data_array_mst="".$cbo_supplier."*".$cbo_pay_mode."*'".$req_no_id_mst."'*".$delivery_address."*".$txt_wo_date."*".$txt_attention."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_currency."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_discount_remarks."*".$txt_contact."*".$cbo_wo_type."*".$txt_remarks_mst."*".$txt_ref."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_payterm_id."*".$txt_tenor."*".$txt_port_of_loading."*'".$txt_place_of_delivery."'*".$cbo_lc_type."";
		}

		// echo $data_array_mst; die;
		// echo $field_array_mst."<br>".$data_array_mst."*";die;
		// print_r($field_array_mst);die;
		$rID=$delete_details=$dtlsrIDI=$dtlsrID=true;
		$field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
	    $data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$delete_details=sql_delete("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"mst_id",$update_check,1);
		//$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE mst_id=$update_check",0);
		if($update_check>0)
		{
			
			$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$update_check,0);
		}

		/*if($txt_delete_row!="")
		{
			$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",0);
		}*/

		if($data_array_insert!="")
		{
			$dtlsrIDI=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,0);
		}

		if(count($updatedtls_ID)>0)
		{
			$dtlsrID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$updatedtls_ID),1);
		}
		//-----------------------------------------------wo_non_order_info_dtls table UPDATE END here-----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//


		// echo $rID."**".$dtlsrID."**".$delete_details."**".$dtlsrIDI; die;
		//echo "1**".$txt_wo_number."**".$update_check."**".$dtlsid_check; die;



		if($db_type==0)
		{
			if($rID && $dtlsrID && $delete_details && $dtlsrIDI)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_wo_number)."**".$update_check."**".$dtlsid_check;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $delete_details && $dtlsrIDI)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_wo_number)."**".$update_check."**".$dtlsid_check;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
			//echo "1**".str_replace("'","",$txt_wo_number)."**".$update_check."**".$dtlsid_check;
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$update_id);

		if($mst_id>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$mst_id and status_active=1","pay_mode");
		if($mst_id>0 && $pay_mode==2)
		{

			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$mst_id and a.item_category_id in(5,6,7,23)");
			if(count($pi_sql)>0)
			{
				echo "13**PI Number ".$pi_sql[0][csf("pi_number")]." Found . \n So Delete Not Possible.**$mst_id";disconnect($con);die;
			}
		}

		if($mst_id>0 && $pay_mode!=2)
		{

			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=4 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$mst_id");
			if(count($mrr_sql)>0)
			{
				echo "13**Receive Number ".$mrr_sql[0][csf("recv_number")]." Found .  \n So Delete Not Possible.**$mst_id";disconnect($con);die;
			}
		}

		if($mst_id=="" || $mst_id==0){ echo "15**0"; die;}

		$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_wo_number)."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 )
		{
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "2**".str_replace("'","",$txt_wo_number)."**".$mst_id;
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



if($action=="wo_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>

	<script>

		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });

		function search_populate(str)
		{
			//alert(str);
			if(str==1) // wo number
			{
				document.getElementById('search_by_th_up').innerHTML="Enter WO Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3) // req number
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Req Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==2) // supplier
			{
				var supplier_name = '<option value="0">--- Select ---</option>';
				<?php
				$supplier_arr = return_library_array("select id,supplier_name from lib_supplier where FIND_IN_SET(2,party_type) order by supplier_name", 'id', 'supplier_name');
				foreach ($supplier_arr as $key => $val) {
					echo "supplier_name += '<option value=\"$key\">" . ($val) . "</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
			}

		}

	function js_set_value(wo_number)
	{
		$("#hidden_wo_number").val(wo_number);
		parent.emailwindow.hide();
	}

    function stringSerach(){
            let searcharr = {"1":"Exact", "2":"Starts with", "3":"Ends with", "4":"Contents"};
            var appender = '<tr><th colspan="11"><select name="cbo_string_search_type" id="cbo_string_search_type" class="combo_boxes " style="width:130px" onchange="">';
            $.each(searcharr, function (index, val){
                if(index == 4)
                    appender += '<option data-attr="" value="'+index+'" selected>'+val+'</option>';
                else
                    appender += '<option data-attr="" value="'+index+'">'+val+'</option>';
            });
            appender += '</select></th></tr>';
            $('#rpt_tablelist_view').find('thead').prepend(appender);
    }

    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >

	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="100">Item Category</th>
								<th width="130">Search By</th>
								<th width="150" align="center" id="search_by_th_up">Enter Order Number</th>
								<th width="200">WO Date Range</th>
								<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
							</thead>
							<tr>
								<td width="100">
								<?
									echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", 0, "",0,"5,6,7,23");
								?>
								</td>
								<td width="130">
								<?
								$searchby_arr=array(1=>"WO Number",2=>"Supplier",3=>"Requisition No");
								echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 0, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
								?>
								</td>
								<td width="150" align="center" id="search_by_td">
									<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
								</td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td>
								<td align="center">
									<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'dyes_and_chemical_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('');stringSerach();" style="width:100px;" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
					</td>
				</tr>
				<tr>
				<td align="center" valign="top" id="search_div">

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

if($action=="create_wo_search_list_view")
{

 	extract($_REQUEST);
	$ex_data = explode("_",$data);
	$itemCategory = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
 	$garments_nature = $ex_data[6];
 	$wo_year = $ex_data[7];

	$sql_cond="";
	if(trim($itemCategory)!=0) $sql_cond .= " and b.item_category_id='$itemCategory'";
	if(trim($txt_search_common)!="")
	{

		if(trim($txt_search_by)==1)
			$sql_cond.= " and a.wo_number like '%".trim($txt_search_common)."'";
		else if(trim($txt_search_by)==2)
			$sql_cond.= " and a.supplier_id=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			//$sql_cond.= " and a.requisition_no=trim('$txt_search_common')";
			//$sql_cond.= " and d.REQU_NO_PREFIX=trim('$txt_search_common')";
			$sql_cond.= " and d.REQU_NO like '%".trim($txt_search_common)."'";
 	}
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date)); $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	if($db_type==0)
	{
		if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else
	{
		if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	}
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$wo_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$wo_year.""; }

 	/*$sql = "select id, wo_number_prefix_num, wo_number, company_name, buyer_po,wo_date,supplier_id,attention,wo_basis_id,item_category,currency_id,delivery_date,source,pay_mode
			from
				wo_non_order_info_mst
			where
				status_active=1 and
				is_deleted=0
				$sql_cond order by id"; //and garments_nature=$garments_nature
        */
		// b.item_category_id,
		if($txt_search_by==3){
			$sql = "SELECT a.id, a.wo_number_prefix_num, a.wo_number,a.requisition_no, a.company_name, a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
			a.currency_id,a.delivery_date,a.source,a.pay_mode, a.inserted_by, a.ready_to_approved, a.is_approved
			from wo_non_order_info_mst a, wo_non_order_info_dtls b,INV_PURCHASE_REQUISITION_DTLS c,INV_PURCHASE_REQUISITION_MST d
			where a.id = b.mst_id and b.REQUISITION_DTLS_ID = c.id and c.mst_id = d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.entry_form = 145 $sql_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number,a.requisition_no, a.company_name, a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
			a.currency_id,a.delivery_date,a.source,a.pay_mode, a.inserted_by, a.ready_to_approved, a.is_approved
			order by a.wo_number_prefix_num desc";
		}
		else
		{
			$sql = "SELECT a.id, a.wo_number_prefix_num, a.wo_number,a.requisition_no, a.company_name, a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
			a.currency_id,a.delivery_date,a.source,a.pay_mode, a.inserted_by, a.ready_to_approved, a.is_approved
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and entry_form = 145 $sql_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number,a.requisition_no, a.company_name, a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
			a.currency_id,a.delivery_date,a.source,a.pay_mode, a.inserted_by, a.ready_to_approved, a.is_approved
			order by a.wo_number_prefix_num desc";
		}
	// echo $sql;//die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Yes');

	$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$wo_basis,6=>$source,7=>$user_library,8=>$is_approved_arr,9=>$is_approved_arr);

	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )
	echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source, Insert By, Ready To Approve,Approval Status", "150,80,100,100,130,100,80,120,80,80","1080","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", "",'','0,0,3,0,0,0,0,0,0,0,0');

 	exit();
}



if($action=="populate_data_from_search_popup")
{
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sql = "SELECT id,requisition_no,delivery_place,company_name,buyer_po,wo_date,supplier_id,attention,wo_basis_id,item_category,currency_id,delivery_date,source,pay_mode,is_approved,ready_to_approved,inco_term_id,payterm_id,tenor,port_of_loading,contact,wo_type,remarks,reference,lc_type,PLACE_OF_DELIVERY
	from wo_non_order_info_mst where id='$data'";
	//echo $sql;die;
	$result = sql_select($sql);
	$prev_pay_mode=$result[0][csf("pay_mode")];
	$pi_mrr_check="";

	if($prev_pay_mode==2)
	{
		$pi_mrr_check=return_field_value("b.id as dtls_id","com_pi_master_details a, com_pi_item_details b","a.id=b.pi_id and a.item_category_id in(5,6,7,23) and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$data","dtls_id");
	}
	else
	{
		$pi_mrr_check=return_field_value("b.id as dtls_id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.entry_form=4 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$data","dtls_id");
	}
	foreach($result as $resultRow)
	{
		$location_id_address=$resultRow[csf("delivery_place")];

		echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
		//echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
		//echo "$('#cbo_item_category').attr('disabled',true);\n";
		echo "$('#txt_supplier_name').val('".$supplier_arr[$resultRow[csf("supplier_id")]]."');\n";
		echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";
		echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
		echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";

		$hdn_delivery=explode('__',$resultRow[csf("PLACE_OF_DELIVERY")]);

		echo "$('#txt_place_of_delivery').val('".$hdn_delivery[0]."');\n";
        if(count($hdn_delivery)>1)
        {
            echo "$('#hidden_delivery_info_dtls').val('".$resultRow[csf("PLACE_OF_DELIVERY")]."');\n";
        }
		echo "set_multiselect('delivery_address','0','1','" . $location_id_address . "','0');\n";
		//echo "$('#delivery_address').val('".$resultRow[csf("delivery_place")]."');\n";

		echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
		echo "$('#cbo_wo_basis').attr('disabled',true);\n";
		echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";
		echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
		echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
		echo "$('#txt_req_numbers_id').val('".$resultRow[csf("requisition_no")]."');\n";
		//echo "$('#txt_delivery_place').val('".$resultRow[csf("PLACE_OF_DELIVERY")]."');\n";
		 echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
		 echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
		echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
		echo "$('#txt_port_of_loading').val('".$resultRow[csf("port_of_loading")]."');\n";
		 echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term_id")]."');\n";
		 echo "$('#txt_contact').val('".$resultRow[csf("contact")]."');\n";
		 echo "$('#cbo_wo_type').val('".$resultRow[csf("wo_type")]."');\n";
		 echo "$('#txt_remarks_mst').val('".$resultRow[csf("remarks")]."');\n";
		 echo "$('#txt_ref').val('".$resultRow[csf("reference")]."');\n";
		 echo "$('#cbo_lc_type').val('".$resultRow[csf("lc_type")]."');\n";

		//echo "$('#update_id').val(".$resultRow[csf("id")].");\n";

		if($pi_mrr_check!="")
		{
			echo "$('#cbo_supplier').attr('disabled',true);\n";
			echo "$('#txt_supplier_name').attr('onDblClick',false);\n";
			echo "$('#cbo_currency').attr('disabled',true);\n";
			echo "$('#cbo_pay_mode').attr('disabled',true);\n";
		}

		$requNumber="";$i=0;
		if($resultRow[csf("wo_basis_id")]==1) // requisition basis
		{
			$sqlResult = sql_select("select requ_no from inv_purchase_requisition_mst where id in (".$resultRow[csf("requisition_no")].")");
			//print_r($sqlResult);
			foreach($sqlResult as $res)
			{
				if( $i>0 ) $requNumber .= ",";
				$requNumber .= $res[csf("requ_no")];
				$i++;
			}
		}
		echo "$('#txt_req_numbers').val('".$requNumber."');\n";
		if($resultRow[csf("wo_basis_id")]!=1) echo "$('#txt_req_numbers').attr('disabled',true);\n";
		else echo "$('#txt_req_numbers').attr('disabled',false);\n";

		$req_dtls_id="";$i=0;
		$sqlResult = sql_select("select requisition_dtls_id from wo_non_order_info_dtls where mst_id =".$resultRow[csf("id")]." and status_active=1");
 		foreach($sqlResult as $res)
		{
			if( $i>0 ) $req_dtls_id .= ",";
			$req_dtls_id .= $res[csf("requisition_dtls_id")];
			$i++;
		}
		echo "$('#txt_req_dtls_id').val('".$req_dtls_id."');\n";

		echo "document.getElementById('is_approved').value = '".$resultRow[csf("is_approved")]."';\n";

	  if($resultRow[csf("is_approved")]==1)
	  {
		 echo "$('#approved').text('Approved');\n";
	  }
	  else if($resultRow[csf("is_approved")] == 3)
	  {
	  	echo "$('#approved').text('Partial Approved');\n";
	  }
	  else
	  {
		 echo "$('#approved').text('');\n";
	  }
	}
	exit();
}


if($action=="show_dtls_listview_update")
{

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$item_group_arr=return_library_array( "SELECT id,item_name  from lib_item_group where status_active=1",'id','item_name');

	$wo_pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$data","pay_mode");
	$pi_mrr_data=array();
	if($wo_pay_mode==2)
	{
		$pi_mrr_sql="select b.item_prod_id as prod_id, b.quantity as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$data";
	}
	else
	{
		$pi_mrr_sql="select b.prod_id as prod_id, b.order_qnty as quantity from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.entry_form=4 and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$data";
	}
	$pi_mrr_result=sql_select($pi_mrr_sql);
	foreach($pi_mrr_result as $row)
	{
		$pi_mrr_data[$row[csf("prod_id")]]+=$row[csf("quantity")];
	}

	$sql = "SELECT b.id,a.wo_basis_id, b.requisition_dtls_id, b.po_breakdown_id, b.requisition_no, b.item_id,p.item_account, p.item_description,p.item_category_id, p.item_size, p.item_group_id as item_group, b.req_quantity, b.color_name, b.supplier_order_quantity, b.uom, b.gross_rate, b.gross_amount, c.requ_no, b.remarks,a.wo_amount, a.up_charge, a.discount, a.net_wo_amount ,a.upcharge_remarks,a.discount_remarks 
	from product_details_master p, wo_non_order_info_mst a, wo_non_order_info_dtls b left join  inv_purchase_requisition_mst c on b.requisition_no=c.id 
	where a.id='$data' and a.id=b.mst_id and b.item_id=p.id and b.is_deleted=0 and b.status_active=1 and p.status_active in(1,3)";
	//echo $sql;die;//b.item_category,
	$result = sql_select($sql);
	$i=1;
	foreach($result as $val)
	{
		if($i==1)
		{
		  ?>
        	<div style="width:1100px;" >
				<table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
					<thead>
						<tr id="0">
							<? if($val[csf("wo_basis_id")]==1 ){?>
                            <th>Requisition No</th>
                            <? } ?>
                            <th>Item Account</th>
                            <th>Item Description</th>
                            <th>Item Category</th>
                            <th>Item Size</th>
                            <th>Item Group</th>
                            <th>Remarks</th>
                            <th>Order UOM</th>
                            <? if($val[csf("wo_basis_id")]==1 ){?>
                            <th>Req.Qnty</th>
                            <? } ?>

                            <th>WO.Qnty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Action</th>
						</tr>
					</thead>
                    <tbody>
         <? } ?>

                        <tr class="general" id="<? echo $i;?>">

                            <!---- This is for requisition number selected in WO Basis START ---->
                            <? if($val[csf("wo_basis_id")]==1){
                                echo "<td width=\"80\">";
                            }

							if($pi_mrr_data[$val[csf("item_id")]]>0) $disable_field='disabled="disabled"'; else $disable_field='';
							?>
                                <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_no_<? echo $i;?>" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
                                 <input type="hidden" name="txt_item_id_<? echo $i;?>" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
                                 <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_dtls_id")];?>" readonly />
                                <input type="hidden" name="txt_req_no_id_<? echo $i;?>" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_no")];?>" readonly />
                                <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $val[csf("id")]; ?>" />

                            <? if($val[csf("wo_basis_id")]==1){
                            echo "</td>";
                            } ?>
                             <!---- This is for requisition number selected in WO Basis END ---->


                            <td width="80">
                                <input type="text" name="txt_item_acct_<? echo $i;?>" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>"  />
                            </td>
                            <td width="80">
                                <input type="text" name="txt_item_desc_<? echo $i;?>" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" readonly title="<? echo $val[csf("item_description")];?>" value="<? echo $val[csf("item_description")];?>"  />
                            </td>
                            <td width="80">
                                <?
                                echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,"5,6,7,23" );
                                ?>
                            </td>
                            <td width="80">
                                <input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" readonly title="<? echo $val[csf("item_size")];?>" value="<? echo $val[csf("item_size")];?>"  />
                            </td>
                            <td width="50" title="<?echo $item_group_arr[$val[csf("item_group")]];?>">
                                <?
                                    echo create_drop_down( "cbogroup_".$i, 80, $item_group_arr,"", 1, "Select", $val[csf("item_group")], "",1);
                                ?>
                            </td>
                            <td width="50">
                                <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:80px" title="<? echo $val[csf('remarks')]; ?>" value="<? echo $val[csf('remarks')]; ?>"  />
                            </td>
                            <td width="60">
                                <?
                                    echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", $val[csf("uom")], "",1 );
                                ?>
                            </td>

                            <? if($val[csf("wo_basis_id")]==1){
                                echo "<td width=\"80\">";
                            } ?>
                                <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="<? echo $val[csf("req_quantity")];?>" />
                            <? if($val[csf("wo_basis_id")]==1){
                                echo "</td>";
                            } ?>

                            <td width="50">
                                <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? echo $val[csf("supplier_order_quantity")];?>" />	<!-- This is wo qnty here -->
                            </td>
                            <td width="50">
                                <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" value="<? echo $val[csf("gross_rate")];?>" <? echo $disable_field;?> />
                            </td>
                            <td width="80">
                                <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo $val[csf("gross_amount")];?>" />
                            </td>
                            <? if($val[csf("wo_basis_id")]==1){?>
                             <td width="80">
                                  <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                            </td>
                            <? }else{  ?>
                            <td width="80">
                                 <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                                 <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                            </td>
                            <? } ?>
                        </tr>
        				<?
						$i++;
					}
					?>
    			</tbody>
                    <tfoot class="tbl_bottom">
                    <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                        {
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <?
                        } 	?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Total</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $val[csf("wo_amount")];?>" style="width:90px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                        {
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <?
                        } 	?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right" colspan="2">Upcharge Remarks:</td>
                            	<td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" value="<? echo $val[csf("upcharge_remarks")];?>" /> </td>
                        <td>Upcharge</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $val[csf("up_charge")];?>" style="width:90px;" onKeyUp="calculate_total_amount(2)" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                        {
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <?
                        } 	?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td align="right" colspan="2">Discount Remarks:</td>
						<td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" value="<? echo $val[csf("discount_remarks")];?>" /></td>
                        <td>Discount</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $val[csf("discount")];?>" style="width:90px;" onKeyUp="calculate_total_amount(2)" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <? if($val[csf("wo_basis_id")]==1)
                        {
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <?
                        } 	?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Net Total</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $val[csf("net_wo_amount")];?>" style="width:90px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
			</table>
    	<?
	exit();
}

?>

<!--Dyes And Chemical Order Report-->
<?
if ($action=="dyes_chemical_work_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	//print_r ($data); die;
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
    ?>
	<style>
    @media print{
        html>body table.rpt_table {
        --border:solid 1px;
        margin-left:12px;
        }

    }
        .rpt_table tbody tr td, thead th {
                font-size: 12pt !important;
        }
        .headTable tr td {
            font-size: 12pt !important;
        }
    </style>
    <?

	$sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
		$com_name=$sql_company[0][csf("company_name")];
		$company_short_name=$sql_company[0][csf("company_short_name")];
		$plot_no=$sql_company[0][csf("plot_no")];
		$level_no=$sql_company[0][csf("level_no")];
		$road_no=$sql_company[0][csf("road_no")];
		$block_no=$sql_company[0][csf("block_no")];
		$city=$sql_company[0][csf("city")];
		$zip_code=$sql_company[0][csf("zip_code")];

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value('location_name','lib_location',"company_id='$data[0]'",'location_name' );
	//$address=return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $address=return_field_value("city as address","lib_company","id=$data[0]",'address');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');


	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, insert_date, is_approved,inserted_by, is_approved,inserted_by,tenor, payterm_id,inco_term_id, pi_issue_to,port_of_loading,upcharge_remarks, discount_remarks, discount, up_charge FROM  wo_non_order_info_mst WHERE id = $data[1]");

	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_name=$source[$row[csf("source")]];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$delivery_place=$row[csf("delivery_place")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by=$row[csf("inserted_by")];
		$tenor= $row[csf("tenor")];
		$payterm_id= $row[csf("payterm_id")];
		$inco_term= $row[csf("inco_term_id")];
		$pi_issue_to= $lib_company_arr[$row[csf("pi_issue_to")]];
		$source_id= $row[csf("source")];
		$port_of_loading= $row[csf("port_of_loading")];
		$upcharge_remarks= $row[csf("upcharge_remarks")];
        $up_charge= $row[csf("up_charge")];
        $discount= $row[csf("discount")];
        $discount_remarks= $row[csf("discount_remarks")];


        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }

    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        echo $is_approved;
        if($is_approved==1){ echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';}
        else{ echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';}
    }



	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
	{//contact_no
		$row_mst[csf('supplier_id')];

		//if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
		//if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')];else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')];else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')];else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		/*$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
		*/
		$supplier_address = $address_1;
		$supplier_address2 = $address_2;
        $supplier_country =$country;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
		$supplier_contact_person = $contact_person;
	}
	//$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );


	//$sql_mst = sql_select("select id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks from  com_pi_master_details where id= $pi_mst_id");

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$i = 0;
	$total_ammount = 0;
	$varcode_booking_no=$work_order_no;
	?>
    <table cellspacing="0" width="1000" >
        <tr>
            <td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><strong>
            <?
            if($city!="") echo $city;
            ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><strong><? if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
        </tr>
         <tr>
            <td colspan="2" align="center">&nbsp;</td>
        </tr>
    </table>
    <table cellspacing="0" width="1000" >
        <tr>
            <td width="300" align="left" rowspan="6"><strong>To</strong>,&nbsp;<br> <?  echo $supplier_name_library[$supplier_id]; ?>
                <br>
                <?
                $supplier_address=explode("%",$supplier_address);
                foreach($supplier_address as $rowadress)
                {
                echo $rowadress."<br>";
                }
                echo $contact_no ;
                ?>
            </td>
            <td width="150"><strong>PO Number :</strong></td>
            <td width="150" align="left"> <?  echo $work_order_no; ?>
            </td>
            <td width="150" align="left" ><strong>Incoterm :</strong></td>
            <td width="150" align="left"><? echo $incoterm[$inco_term];//$lib_country_arr[$country]; ?></td>
        </tr>
        <tr>
            <td ><strong>Po Date :</strong></td>
            <td><? echo change_date_format($work_order_date); ?></td>
            <td align="left"><strong>Delivery Date :</strong></td>
            <td align="left" ><? echo change_date_format($delivery_date); ?></td>
        </tr>
        <tr>
            <td><strong>Pay Mode :</strong></td>
            <td align="left"><? echo $pay_mode[$pay_mode_id]; ?></td>
            <td align="left"><strong>Port of Loading :</strong></td>
            <td align="left" ><? echo $port_of_loading; ?></td>
        </tr>
         <tr>
            <td><strong>Currency :</strong></td>
            <td align="left" ><? echo $currency[$currency_id]; ?></td>
            <td align="left" ><strong>Port of Discharge :</strong></td>
            <td align="left" ><? echo $delivery_place; ?></td>

        </tr>

        <tr>
            <td><strong>Pay Term :</strong></td>
            <td align="left" ><? echo $pay_term[$payterm_id]; ?></td>
            <td align="left" ><strong>Tenor :</strong></td>
            <td align="left" ><? echo $tenor;?></td>

        </tr>
        <tr>
            <td><strong>Source :</strong></td>
            <td align="left" ><? echo $source[$source_id]; ?></td>
            <td align="left" ><strong>PO Basis :</strong></td>
            <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>

        </tr>
        <tr>
            <td colspan="5"><strong style="width: 150px;">Contact Person :</strong><? echo $supplier_contact_person; echo "<br>";
                $supplier_address22=explode("%",$supplier_address2);
                foreach($supplier_address22 as $rowadress2)
                {
                    echo $rowadress2."<br>";
                }?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
		<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="150" align="center">Requisition No</th>
                <th width="80" align="center">Code</th>
                <th width="120" align="center">Item Category</th>
                <th width="80" align="center">Item Group</th>
                <th width="120" align="center">Item Description</th>
                <th width="70" align="center">UOM</th>
                <th width="70" align="center">Req.Qty</th>
                <th width="70" align="center">PO Qty</th>
                <th width="60" align="center">Rate</th>
                <th width="90" align="center">PO Amount</th>
                <th width="100" align="center">Remarks</th>
            </thead>
     <?
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $i=1;
    $sql_result= sql_select("select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id
    from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond ");
	foreach($sql_result as $row)
    {
    	if ($i%2==0)
    		$bgcolor="#E9F3FF";
    	else
    		$bgcolor="#FFFFFF";

    		$req_quantity=$row[csf('req_quantity')];
    		$req_quantity_sum += $req_quantity;

    		$supplier_order_quantity=$row[csf('supplier_order_quantity')];
    		$supplier_order_quantityl_sum += $supplier_order_quantity;

    		$amount=$row[csf('gross_amount')];
    		$total_amount+= $amount;
            $for_test_para.=$row[csf('item_category_id')].'_'.$row[csf('item_group_id')].'_'.$row[csf('item_description')].'#';
            $category.=$row[csf('item_category_id')].',';
            $group.=$row[csf('item_group_id')].',';
            $desc.="'".$row[csf('item_description')]."',";
            ?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td><? echo $i; ?></td>
            <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
            <td><? echo $row[csf('item_account')]; ?></td>
            <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
            <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
            <td><? echo $row[csf('item_description')]; ?></td>
            <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td align="right"><? echo number_format($row[csf('req_quantity')],2,".",","); ?></td>
            <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2,".",","); ?></td>
            <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",","); ?></td>
            <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",",");?></td>
            <td><? echo $row[csf('remarks')]; ?></td>
            <?
                $carrency_id=$row[csf('currency_id')];
                if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
            ?>
        </tr>
    <?php
    $i++;
	}
    $category=chop($category,',');
    $group=chop($group,',');
    $desc=chop($desc,',');
    $test_parameter = sql_select("SELECT a.id,a.item_id, a.tech_charecteristics,  a.standard_value, b.item_category_id , b.item_group_id, b.item_description FROM  product_details_test_parameter a, product_details_master b WHERE a.item_id=b.id and b.company_id=$data[0] and b.item_category_id in($category) and b.item_group_id in($group) and b.item_description in($desc) and a.status_active=1 and b.status_active=1");
    foreach($test_parameter as $row)
    {
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['item_id']=$row[csf('item_id')];
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['tech_charecteristics']=$row[csf('tech_charecteristics')];
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['standard_value']=$row[csf('standard_value')];
    }

    $rowspan_arr=array();
    foreach($testPara_arr  as $category_id => $category_id_data_arr)
    {
        foreach($category_id_data_arr as $item_group_id => $item_group_id_data_arr)
        {
            foreach($item_group_id_data_arr as $item_description => $item_description_data_arr)
            {
                $rowspan=0;
                foreach($item_description_data_arr  as $id => $row)
                {
                    $rowspan++;
                }
                $rowspan_arr[$category_id][$item_group_id][$item_description]=$rowspan;
            }
        }

    }
    $word_total_amount=number_format($total_amount, 2, '.', '');
    $word_net_total_amount=number_format($total_amount+$up_charge-$discount, 2, '.', '')
    //echo "<pre>";
    //print_r($testPara_arr);
	?>
	<tr >
        <td align="right">&nbsp;  </td>
        <td align="right" colspan="6"><strong>WO Qty Total:</strong></td>
        <td align="right"><? echo number_format($req_quantity_sum,0,'',',') ?></td>
        <td align="right"><? echo number_format($supplier_order_quantityl_sum,0,'',',') ?></td>
        <td align="right"><strong>Total : </strong></td>
        <td align="right"><strong><? echo number_format($word_total_amount,2,".",",");?></strong></td>
        <td align="right">&nbsp;  </td>
	</tr>
	<tr>
	   <td align="right">&nbsp;  </td>
        <td align="left" colspan="8" ><strong> Upcharge Remarks: </strong><? echo $upcharge_remarks ;?></td>
        <td align="right"><strong>Upcharge:</strong></td>
        <td align="right"><strong><? echo number_format($up_charge, 2, '.', ''); ?></strong></td>

	</tr>
    <tr>
       <td align="right">&nbsp;  </td>
        <td align="left" colspan="8" ><strong> Discount Remarks: </strong><? echo $discount_remarks ;?></td>
        <td align="right"><strong>Discount:</strong></td>
        <td align="right"><strong><? echo number_format($discount, 2, '.', ''); ?></strong></td>

    </tr>
    <tr>
       <td align="right">&nbsp;  </td>
        <td align="right" colspan="8" >&nbsp;</td>
        <td align="right" ><strong> Net Total:</strong></td>
        <td align="right"><strong><? echo number_format($word_net_total_amount, 2, '.', ''); ?></strong></td>
    </tr>
    <tr>
        <td align="left" colspan="12" ><strong> Amount in words: </strong><? echo number_to_words($word_net_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
    </tr>
	</table>

	<br/>
	<style type="text/css">
	/* [class^="box_"]{
				width: 48%;
				float: left;
				margin: 0;
				padding: 0;
			min-height: 150px;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			} */

			.box_odd{
				width: 50%;
				float: right;
				margin: 0;
				padding: 0;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			}

			.box_even{
				width: 50%;
				float: left;
				margin: 0;
				padding: 0;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			}
	</style>
		<?
			echo get_spacial_instruction($work_order_no,'1000px',145);
			$testPara=count($testPara_arr);
			?>
			<br/>
			<?
			$i=1; $row_no=0;
			?>
			<div style="width: 1000px">
				<fieldset>
					<legend>Test Parameter</legend>
					<?
					foreach ($testPara_arr as $category_id => $catData_arr)
					{
						foreach ($catData_arr as $group_id => $groupData_arr)
						{
							if ($row_no%2==0)
								$div_odd_even='even';
							else
								$div_odd_even='odd';
							?>
							<div  class="box_<? echo $div_odd_even; ?>">
							<table align="left" cellspacing="0" style="border: 0px;width: 99% !important;" rules="all" class="rpt_table" >
								<thead bgcolor="#dddddd" align="center">
									<th width="95" align="center">Item Group</th>
									<th width="130" align="center">Item Name</th>
									<th width="130" align="center">Technical Charecteristics</th>
									<th width="130" align="center">Standard Value</th>
								</thead>
							<?
							foreach ($groupData_arr as $desc => $desc_arr)
							{
								$x=1; $a=1; $rowspan=$rowspan_arr[$category_id][$group_id][$desc];
								//echo $rowspan."==";
								foreach ($desc_arr as $id => $row)
								{
									if ($a%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<?
											if($x==1)
											{
												?>
												<td width="95" rowspan="<? echo $rowspan; ?>"><? echo $item_name_arr[$group_id]; ?></td>
												<td width="130" rowspan="<? echo $rowspan; ?>"><? echo $desc; ?></td>
												<?
												$x++;
											}
											?>
											<td width="130"><? echo $row['tech_charecteristics']; ?></td>
											<td width="130"><? echo $row['standard_value']; ?></td>
										</tr>

									<?
									$a++;
								}
							}
							$i++;
							?>
							</table>
							</div>

							<?
							$row_no++;
						}
					}
					?>
				</fieldset>
			</div>
			<?
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=3 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
		foreach ($approved_sql as $key => $value){
			$approved_by = $value[csf("approved_by")];
		}
		echo "<b>Approved by:</b>".$user_lib_name[$approved_by ];
			echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
		?>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
		</script>
		<?
	exit();
}

if ($action=="dyes_chemical_work_po_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[4];
	$lc_type=$data[5]; 
	$lc_type_arr=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT'];
	// print_r ($data); die;
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
    ?>
	<style>
    @media print{
        html>body table.rpt_table {
        --border:solid 1px;
        margin-left:0px;
        }

    }
        .rpt_table tbody tr td, thead th {
                font-size: 11pt ;
        }
        .headTable tr td {
            font-size: 11pt;
        }
		.bordertbl{
			border: 1px solid;
			padding: 3px;
		}
		.paddingtbl{
			padding: 3px 0px;
		}

    </style>
    <?

		$sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,tin_number,bin_no from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
		$com_name=$sql_company[0][csf("company_name")];
		$company_short_name=$sql_company[0][csf("company_short_name")];
		$plot_no=$sql_company[0][csf("plot_no")];
		$level_no=$sql_company[0][csf("level_no")];
		$road_no=$sql_company[0][csf("road_no")];
		$block_no=$sql_company[0][csf("block_no")];
		$city=$sql_company[0][csf("city")];
		$zip_code=$sql_company[0][csf("zip_code")];
		$tin_num=$sql_company[0][csf("tin_number")];
		$bin_num=$sql_company[0][csf("bin_no")];

		$com_address='';
		if($plot_no !=''){ $com_address.=$plot_no;}
		if($level_no !=''){ $com_address.=", ".$level_no;}
		if($road_no !=''){ $com_address.=", ".$road_no;}
		if($block_no !=''){ $com_address.=", ".$block_no;}
		if($city !=''){ $com_address.=", ".$city;}
		if($zip_code !=''){ $com_address.=", ".$zip_code;}

	// $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value('location_name','lib_location',"company_id='$data[0]'",'location_name' );
	//$address=return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $address=return_field_value("city as address","lib_company","id=$data[0]",'address');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$requisition_department=return_library_array( "select id,department_id from  inv_purchase_requisition_mst", "id","department_id"  );
	$department=return_library_array( "select id,department_name from lib_department ", "id","department_name"  );
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, contact, wo_date, currency_id, supplier_id, attention, buyer_name, style, item_category,PLACE_OF_DELIVERY, delivery_date, remarks,is_approved, is_approved,inserted_by, payterm_id,reference,wo_type,up_charge,discount,tenor  FROM  wo_non_order_info_mst WHERE id = $data[1]");
	$inserted_by=$user_lib_name[$sql_data[0][csf("inserted_by")]];
	// echo "<pre>";
    // print_r($sql_data);
    // echo "</pre>";
	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$payterm_id= $row[csf("payterm_id")];
		$tenor_day= $row[csf("tenor")];
		$source_id= $row[csf("source")];
		$contact_per= $row[csf("contact")];
		$wo_type= $row[csf("wo_type")];
		$reference= $row[csf("reference")];
		$upcharge= $row[csf("up_charge")];
		$discount= $row[csf("discount")];
		$remarks= $row[csf("remarks")];
		$delivery_addrs=$row[csf("PLACE_OF_DELIVERY")];

        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
	{
		$row_mst[csf('supplier_id')];
		//if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
		//if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')];else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')];else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')];else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];
		$supplier_address = $address_1;
		$supplier_address2 = $address_2;
        $supplier_country =$country;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
		$supplier_contact_person = $contact_person;
	}
	$sql_group=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
	$i = 0;
	$total_ammount = 0;
	if($is_approved==1){$approved_note="Approved";}else{$approved_note="Un Approved";}
	$req_no= explode(",",$data[2]);
	$req_num='';
	foreach($req_no as $value){
		if($req_num!=''){$req_num.=", ".$requisition_library[$value];}else{$req_num=$requisition_library[$value];}
	}
	$department_no='';
	foreach($req_no as $value){
		if($department_no!=''){$department_no.=",".$requisition_department[$value];}else{$department_no=$requisition_department[$value];}
	}
	$dep=array_unique(explode(",",$department_no));
	$department_num='';
	foreach($dep as $value){
		if($department_num!=''){$department_num.=", ".$department[$value];}else{$department_num=$department[$value];}

	}
	$group_logo=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");
	?>
	<div class="fontincrease">
    <table cellspacing="0" width="1000" align="center" >
        <tr>
            <td rowspan="2" width="100"><img src="<?= "../../".$group_logo;?>" height="60" width="90" alt="Group Logo"></td>
            <td colspan="3" style="font-size:25pt;" align="center"><strong><? echo $sql_group;?></strong></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:21pt;"><strong> Purchase Order- Dyes & Chemicals</strong></td>
        </tr>
         <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>
    </table>
    <table cellspacing="0" width="1000" class="headTable" >
        <tr>
            <td width="350" colspan="2" style="font-size:17pt;"><strong><?= $com_name;?></strong></td>
            <td width="200" align="left" class="bordertbl" style="font-size:16pt;"><strong>Purchase Type:</strong></td>
            <td width="250" align="left" class="bordertbl" style="font-size:16pt;"><strong><? echo $wo_type_array[$wo_type]; ?></strong></td>
        </tr>
        <tr>
            <td valign="top"><strong>Address:</strong></td>
            <td valign="top"><? echo $com_address; ?></td>
			<td align="left" class="bordertbl"><strong>P.O. Number:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td><strong>BIN:</strong></td>
            <td align="left"><? echo $bin_num; ?></td>
            <td align="left" class="bordertbl"><strong>P.O. Date:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_date; ?></strong></td>
        </tr>
         <tr>
            <td><strong>TIN:</strong></td>
            <td align="left" ><? echo $tin_num; ?></td>
			<td align="left" valign="top" class="bordertbl" rowspan="2"><strong>Req No:</strong></td>
            <td align="left" valign="top" class="bordertbl" rowspan="2"><strong><? echo $req_num; ?></strong></td>
        </tr>
		<tr>
        <td colspan="2"><strong>Delivery Addr.:</strong> <? echo  $delivery_addrs; ?><br>         
        </tr>
        <tr>
            <td><strong>Contact:</strong></td>
            <td align="left" ><? echo $contact_per; ?></td>
			<td align="left" class="bordertbl"><strong>Quotation No.:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td>
            <td align="left" style="font-size:15pt;"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
            <td align="left" class="bordertbl"><strong>RFQ Number:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Address:</strong></td>
            <td align="left" ><? echo $supplier_address ; ?></td>
            <td align="left" class="bordertbl"><strong>Reference: </strong></td>
            <td align="left" class="bordertbl"><? echo $reference; ?></td>
        </tr>
        <tr>
            <td><strong>Attn:</strong></td>
            <td align="left" valign="top" rowspan="2" ><?
			$attn= explode(",",$attention);
			foreach($attn as $value){
				echo "<div class='paddingtbl'>".$value."</div>";
			}
			?></td>
            <td align="left" class="bordertbl"><strong>LC / Payment Terms:</strong></td>
            <td align="left" class="bordertbl"><?
			if($lc_type>0 && $payterm_id!=0){
				echo $lc_type_arr[$lc_type]." / ";
			}
			if($lc_type>0 && $payterm_id==0){
				echo $lc_type_arr[$lc_type];
			}
			//if($cbo)			
			if($payterm_id==2)
			{
				echo "LC ".$tenor_day." Days";
			}
			else if($payterm_id!=2)
			{
				echo $pay_term[$payterm_id];
			}
			?></td>
        </tr>
        <tr>
            <td><strong>Contact No:</strong></td>
            <td align="left" class="bordertbl"><strong>PO Status:</strong></td>
            <td align="left" class="bordertbl"><? echo $approved_note; ?></td>
        </tr>
        <tr>
            <td><strong>Department:</strong></td>
            <td align="left" ><? echo $department_num ; ?></td>
            <td align="left" class="bordertbl"><strong>Currency:</strong></td>
            <td align="left" class="bordertbl"><? echo $currency[$currency_id]; ?></td>
        </tr>
        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
		<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="150" >Item</th>
                <th width="100" >Item Group</th>
                <th width="120" >Declaration Details</th>
                <th width="120" >Item Category</th>
                <th width="130">Narration</th>
                <th width="50" >Unit</th>
                <th width="80">Quantity</th>
                <th width="60" >Rate</th>
                <th >Amount</th>
            </thead>
			<tbody>
     <?
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $i=1;
	
	/*echo "select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id,e.brand_name,e.origin
    from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d,inv_purchase_requisition_dtls e
    where a.id=b.mst_id and b.item_id=d.id and b.requisition_dtls_id=e.id and b.status_active=1 and b.is_deleted=0 $cond ";*/

    $sql_result= sql_select("select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id,e.brand_name,e.origin from wo_non_order_info_mst a,wo_non_order_info_dtls b left join inv_purchase_requisition_dtls e on b.requisition_dtls_id=e.id,product_details_master d where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond ");
	 
	foreach($sql_result as $row)
    {
		$req_quantity=$row[csf('req_quantity')];
		$req_quantity_sum += $req_quantity;

		$supplier_order_quantity=$row[csf('supplier_order_quantity')];
		$supplier_order_quantityl_sum += $supplier_order_quantity;

		$amount=$row[csf('gross_amount')];
		$total_amount+= $amount;
		$for_test_para.=$row[csf('item_category_id')].'_'.$row[csf('item_group_id')].'_'.$row[csf('item_description')].'#';
		$category.=$row[csf('item_category_id')].',';
		$group.=$row[csf('item_group_id')].',';
		$desc.="'".$row[csf('item_description')]."',";
		?>
        <tr bgcolor="#FFFFFF">
            <td align="center" ><? echo $i; ?></td>
            <td ><? echo $row[csf('item_description')]; ?></td>
			<td ><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
			<td ><? echo $row[csf('remarks')]; ?></td>
			<td ><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
			<td ><? echo $row[csf('brand_name')];
					if($lib_country_arr[$row[csf('origin')]]!=''){
						echo ', '.$lib_country_arr[$row[csf('origin')]];
					}
			?></td>
            <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td align="right"><? echo $row[csf('supplier_order_quantity')]; ?></td>
            <td align="right"><? $gross_rate=number_format($row[csf('gross_rate')],3); echo $currency_sign_arr[$row[csf('currency_id')]]." ". $gross_rate; ?></td>
            <td align="right"><? $gross_amount=number_format($row[csf('gross_amount')],2); echo $currency_sign_arr[$row[csf('currency_id')]]." ". $gross_amount;?></td>
            <?
                $carrency_id=$row[csf('currency_id')];
                if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
            ?>
        </tr>
    <?php
    $i++;
	}

    $sub_total_amount= $total_amount + $upcharge - $discount;
    $word_total_amount=number_format($sub_total_amount, 2);

    $upcharge=number_format($upcharge,2);
    $discount=number_format($discount,2);
    $total_amount=number_format($total_amount,2);
    //echo "<pre>";
    //print_r($testPara_arr);
	?>
	<tr >
        <td align="left" colspan="7" rowspan="4"></td>
        <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $total_amount; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2" ><strong>Discount</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $discount; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2"><strong>PO Charge</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $upcharge; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2" style="font-size:15pt;"><strong>Total Amount </strong></td>
        <td align="right" style="font-size:15pt;"><strong><? echo $currency_sign_arr[$currency_id]." ". $word_total_amount; ?></strong></td>
	</tr>
	<tr >
        <td align="left" colspan="9" ><strong style="font-size:15pt;"> Amount in words: </strong><? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
	</tr>
	</table>
	</tbody>
	<br/>
	<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	<tr >
        <td align="left" colspan="9" ><strong style="font-size:15pt;">Special Comments: </strong><? echo $remarks; ?></td>
	</tr>
	</table>
	<br/>
	<br/>
	<div><strong style="font-size:15pt;">Terms & Conditions:</strong></div>
	<?
	    $sql_term= sql_select("select terms from wo_booking_terms_condition where entry_form=145 and booking_no='$work_order_no' ");
		$i=1;
	foreach ($sql_term as $value) {
		echo $i.". ".$value[csf('terms')]."</br>";
		$i++;
	}
	?>
	<br/>

			<?
			if ($cbo_template_id != '') {
				$template_id = " and template_id=$cbo_template_id ";
			}

			$sql = sql_select("select designation,name,user_id,prepared_by from variable_settings_signature where report_id=59 and company_id='$data[0]'  $template_id order by sequence_no");

			$signature_sql = sql_select("SELECT c.master_tble_id as MASTER_TBLE_ID,c.image_location as IMAGE_LOCATION  from variable_settings_signature a, electronic_approval_setup b, common_photo_library c where a.user_id=b.user_id and a.user_id=c.master_tble_id and a.report_id=59 and a.company_id='$data[0]' and a.template_id=$cbo_template_id and b.page_id=626 and b.entry_form=3 and b.company_id=$data[0] and c.form_name='user_signature'");
			$signature_location=array();
			foreach($signature_sql as $row)
			{
				$signature_location[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
			}
			if($sql[0][csf("prepared_by")]==1){
				list($prepared_by,$activities)=explode('**',$prepared_by);
				$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME =>$inserted_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
				$sql=$sql_2+$sql;
			}
			$count = count($sql);
			$td_width = floor(1000 / $count);
			$standard_width = $count * 150;
			if ($standard_width > 1000) {
				$td_width = 150;
			}
			$i = 1;
			if ($count == 0) { echo "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
			else
			{
				echo '<table id="signatureTblId" width="1000" style="padding-top:50px;"><tr><td width="100%" height="50" colspan="' . $count . '">' . $message . '</td></tr><tr>';
				foreach ($sql as $row) {
					echo '<td width="' . $td_width . '" align="center" valign="bottom">';
					if($signature_location[$row[csf("user_id")]]!='')
					{
						echo '<strong><img src="../../'.$signature_location[$row[csf("user_id")]].'" height="60" width="90" ></strong><br>';
					}
					else
					{
						echo '<span style="height:60px;width:90px;"></span><br>';
					}
					echo '<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
					$i++;
				}
				echo '</tr></table>';
			}

		?>
    </div>
		<?
	exit();
}

if ($action=="dyes_chemical_work_printbackup")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[3];
	//print_r ($data); die;
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
    ?>
	<style>
    @media print{
        html>body table.rpt_table {
        --border:solid 1px;
        margin-left:12px;
        }

    }
        .rpt_table tbody tr td, thead th {
                font-size: 12pt !important;
        }
        .headTable tr td {
            font-size: 12pt !important;
        }
    </style>
    <?
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value('location_name','lib_location',"company_id='$data[0]'",'location_name' );
	//$address=return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $address=return_field_value("city as address","lib_company","id=$data[0]",'address');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');


	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, insert_date, is_approved,inserted_by  FROM  wo_non_order_info_mst WHERE id = $data[1]");
	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_name=$source[$row[csf("source")]];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$delivery_place=$row[csf("delivery_place")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by=$row[csf("inserted_by")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }

    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        echo $is_approved;
        if($is_approved==1){ echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';}
        else{ echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';}
    }



	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
	{//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	//$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );


	//$sql_mst = sql_select("select id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks from  com_pi_master_details where id= $pi_mst_id");

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$i = 0;
	$total_ammount = 0;
	$varcode_booking_no=$work_order_no;
	?>
    <table align="center" cellspacing="0" width="1000" >
        <tr>
        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="7" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td rowspan="3" colspan="2" id="barcode_img_id"> </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size: 11pt;"><? echo $location.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size: 11pt;"><? echo $data[2]; ?></td>
        </tr>
    </table>

    <table align="center" cellspacing="0" width="1000" class="headTable">
        <tr>
            <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="100"><strong>PO Number</strong></td>
            <td width="100" align="left">:
			<?
			echo $work_order_no;
			?>
            </td>
            <td width="60"></td>
            <td width="100" align="left" ><strong>PO Date </strong></td>
            <td width="200" align="left">: <? echo $work_order_date; ?></td>
        </tr>
        <tr>
            <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
            <td ><strong>Delivery Date </strong></td>
            <td>: <? echo change_date_format($delivery_date); ?></td>
            <td></td>
            <td align="left"><strong>Place of Delivery</strong></td>
            <td align="left" >: <? echo $delivery_place; ?></td>
        </tr>
        <tr>
            <td><strong>Currency</strong></td>
            <td align="left">: <? echo $currency[$currency_id]; ?></td>
            <td></td>
            <td align="left" ><strong>PO Basis</strong></td>
            <td align="left" >: <? echo $wo_basis[$wo_basis_id]; ?></td>

        </tr>
         <tr>
            <td><strong>Pay Mode</strong></td>
            <td align="left" >: <? echo $pay_mode[$pay_mode_id]; ?></td>
            <td></td>

            <td align="left"><strong>Source</strong></td>$source_name
            <td align="left" >: <? echo $source_name; ?></td>
        </tr>
        <tr>
            <td align="right" colspan="6" >&nbsp;</td>
        </tr>
        <tr>
            <td align="right" colspan="6" >&nbsp;</td>
        </tr>
    </table>
		<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="150" align="center">Requisition No</th>
                <th width="80" align="center">Code</th>
                <th width="120" align="center">Item Category</th>
                <th width="80" align="center">Item Group</th>
                <th width="120" align="center">Item Description</th>
                <th width="70" align="center">UOM</th>
                <th width="70" align="center">Req.Qty</th>
                <th width="70" align="center">PO Qty</th>
                <th width="60" align="center">Rate</th>
                <th width="90" align="center">PO Amount</th>
                <th width="100" align="center">Remarks</th>
            </thead>
     <?
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $i=1;
    $sql_result= sql_select("select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id
    from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond ");
	foreach($sql_result as $row)
    {
    	if ($i%2==0)
    		$bgcolor="#E9F3FF";
    	else
    		$bgcolor="#FFFFFF";

    		$req_quantity=$row[csf('req_quantity')];
    		$req_quantity_sum += $req_quantity;

    		$supplier_order_quantity=$row[csf('supplier_order_quantity')];
    		$supplier_order_quantityl_sum += $supplier_order_quantity;

    		$amount=$row[csf('gross_amount')];
    		$total_amount+= $amount;
            $for_test_para.=$row[csf('item_category_id')].'_'.$row[csf('item_group_id')].'_'.$row[csf('item_description')].'#';
            $category.=$row[csf('item_category_id')].',';
            $group.=$row[csf('item_group_id')].',';
            $desc.="'".$row[csf('item_description')]."',";
            ?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td><? echo $i; ?></td>
            <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
            <td><? echo $row[csf('item_account')]; ?></td>
            <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
            <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
            <td><? echo $row[csf('item_description')]; ?></td>
            <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td align="right"><? echo $row[csf('req_quantity')]; ?></td>
            <td align="right"><? echo $row[csf('supplier_order_quantity')]; ?></td>
            <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
            <td align="right"><? echo number_format($row[csf('gross_amount')],2,".","");?></td>
            <td><? echo $row[csf('remarks')]; ?></td>
            <?
                $carrency_id=$row[csf('currency_id')];
                if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
            ?>
        </tr>
    <?php
    $i++;
	}
    $category=chop($category,',');
    $group=chop($group,',');
    $desc=chop($desc,',');
    $test_parameter = sql_select("SELECT a.id,a.item_id, a.tech_charecteristics,  a.standard_value, b.item_category_id , b.item_group_id, b.item_description FROM  product_details_test_parameter a, product_details_master b WHERE a.item_id=b.id and b.company_id=$data[0] and b.item_category_id in($category) and b.item_group_id in($group) and b.item_description in($desc) and a.status_active=1 and b.status_active=1");
    foreach($test_parameter as $row)
    {
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['item_id']=$row[csf('item_id')];
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['tech_charecteristics']=$row[csf('tech_charecteristics')];
        $testPara_arr[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('id')]]['standard_value']=$row[csf('standard_value')];
    }

    $rowspan_arr=array();
    foreach($testPara_arr  as $category_id => $category_id_data_arr)
    {
        foreach($category_id_data_arr as $item_group_id => $item_group_id_data_arr)
        {
            foreach($item_group_id_data_arr as $item_description => $item_description_data_arr)
            {
                $rowspan=0;
                foreach($item_description_data_arr  as $id => $row)
                {
                    $rowspan++;
                }
                $rowspan_arr[$category_id][$item_group_id][$item_description]=$rowspan;
            }
        }

    }
    $word_total_amount=number_format($total_amount, 2, '.', '')
    //echo "<pre>";
    //print_r($testPara_arr);
	?>
	<tr >
        <td align="right">&nbsp;  </td>
        <td align="left" colspan="8" ><strong> Amount in words:</strong><? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
        <td align="right"><strong>Total : </strong></td>
        <?php /*?><td align="right"><? echo number_format($req_quantity_sum,0,'',',') ?></td>
               <td align="right"><? echo number_format($supplier_order_quantityl_sum,0,'',',') ?></td><?php */?>
        <td align="right"><strong><? echo $word_total_amount; ?></strong></td>
        <td align="right">&nbsp;  </td>
	</tr>
	</table>

	<br/>
	<style type="text/css">
	/* [class^="box_"]{
				width: 48%;
				float: left;
				margin: 0;
				padding: 0;
			min-height: 150px;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			} */

			.box_odd{
				width: 50%;
				float: right;
				margin: 0;
				padding: 0;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			}

			.box_even{
				width: 50%;
				float: left;
				margin: 0;
				padding: 0;
				position: relative;
				overflow: hidden;
				margin-bottom: 10px;
			}
	</style>
		<?
			echo get_spacial_instruction($work_order_no,'1000px',145);
			$testPara=count($testPara_arr);
			?>
			<br/>
			<?
			$i=1; $row_no=0;
			?>
			<div style="width: 1000px">
				<fieldset>
					<legend>Test Parameter</legend>
					<?
					foreach ($testPara_arr as $category_id => $catData_arr)
					{
						foreach ($catData_arr as $group_id => $groupData_arr)
						{
							if ($row_no%2==0)
								$div_odd_even='even';
							else
								$div_odd_even='odd';
							?>
							<div  class="box_<? echo $div_odd_even; ?>">
							<table align="left" cellspacing="0" style="border: 0px;width: 99% !important;" rules="all" class="rpt_table" >
								<thead bgcolor="#dddddd" align="center">
									<th width="95" align="center">Item Group</th>
									<th width="130" align="center">Item Name</th>
									<th width="130" align="center">Technical Charecteristics</th>
									<th width="130" align="center">Standard Value</th>
								</thead>
							<?
							foreach ($groupData_arr as $desc => $desc_arr)
							{
								$x=1; $a=1; $rowspan=$rowspan_arr[$category_id][$group_id][$desc];
								//echo $rowspan."==";
								foreach ($desc_arr as $id => $row)
								{
									if ($a%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<?
											if($x==1)
											{
												?>
												<td width="95" rowspan="<? echo $rowspan; ?>"><? echo $item_name_arr[$group_id]; ?></td>
												<td width="130" rowspan="<? echo $rowspan; ?>"><? echo $desc; ?></td>
												<?
												$x++;
											}
											?>
											<td width="130"><? echo $row['tech_charecteristics']; ?></td>
											<td width="130"><? echo $row['standard_value']; ?></td>
										</tr>

									<?
									$a++;
								}
							}
							$i++;
							?>
							</table>
							</div>

							<?
							$row_no++;
						}
					}
					?>
				</fieldset>
			</div>
			<?
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=3 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
		foreach ($approved_sql as $key => $value){
			$approved_by = $value[csf("approved_by")];
		}
		echo "<b>Approved by:</b>".$user_lib_name[$approved_by ];
			echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
		?>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
		</script>
		<?
	exit();
}

if ($action=="dyes_chemical_work_print2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[4];
	//print_r ($data); die;
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value('location_name as location_name','lib_location',"company_id='$data[0]'",'location_name' );
	$address=return_field_value("address as address","lib_location","company_id=$data[0]",'address');
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$data[0]",'bin_no');
	//$address=return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');

	$sql="select id from electronic_approval_setup where company_id=$data[0] and page_id in(2840,626,3009) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
	  $approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks,wo_amount,up_charge,discount,upcharge_remarks,net_wo_amount, insert_date, is_approved,inserted_by FROM  wo_non_order_info_mst WHERE id = $data[1]");
	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$is_approved=$row[csf("is_approved")];
		$source=$row[csf("source")];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$delivery_place=$row[csf("delivery_place")];

		$wo_amount=$row[csf("wo_amount")];
		$up_charge=$row[csf("up_charge")];
		$discount=$row[csf("discount")];
		$net_wo_amount=$row[csf("net_wo_amount")];
		$upcharge_remarks=$row[csf("upcharge_remarks")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by=$row[csf("inserted_by")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=21 and status_active=1 and is_deleted=0";
    }

    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        echo $is_approved;
        if($is_approved==1){ echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';}
        else{ echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';}
    }


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
	{//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	if($db_type==2)
	{
		//$quot_ref=return_field_value(" rtrim(xmlagg(xmlelement(e,requ_prefix_num,',').extract('//text()') order by requ_prefix_num).GetClobVal(),',') AS our_ref ","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
       // $quot_ref = $quot_ref->load();
		$quot_ref=return_field_value(" listagg(cast(requ_prefix_num as varchar(4000)),',') within group(order by id) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
	}
	else
	{
		$quot_ref=return_field_value("group_concat(requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
	}
	$req_no="";

	$req_no_id=array_unique(explode(",",$quot_ref));
    foreach($req_no_id as $reg_id)
    {
	if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;

    }
	//echo " select system_id as system_id from inv_quot_evalu_mst where requ_no_id=$data[2]";die;
	if($db_type==0)
	{
		$quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id in($data[2])","value" );
	}
	else
	{
		//$quot_factor_val=return_field_value("rtrim(xmlagg(xmlelement(e,c.value,',').extract('//text()') order by c.value).GetClobVal(),',') as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id in($data[2])","value" );
        //$quot_factor_val = $quot_factor_val->load();
		$quot_factor_val=return_field_value("listagg(cast(c.value as varchar(4000)),',') within group(order by id) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id in($data[2])","value" );
	}
	//echo "test4";die;
	$quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id in($data[2])","system_id" );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$i = 0;
	$total_ammount = 0;
	?>
    <table align="center" cellspacing="0" width="675" >
        <tr>
        	<td width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="9" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="9" align="center"><? echo $location.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="9" align="center"><? //echo $data[2]; ?></td>
        </tr>
    </table>
    <table align="center" cellspacing="0" width="675" >
        <tr>
            <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="150"></td>
            <td width="150" align="left">

            </td>
            <td width="150" align="left" ><strong>Date</strong></td>
            <td width="150" align="left">: &nbsp;&nbsp;<?
			 echo change_date_format($work_order_date);
			?></td>
        </tr>
        <tr>
            <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
            <td ></td>
            <td></td>
            <td align="left"><strong>WO Number</strong></td>
            <td align="left" >:&nbsp;&nbsp;<? echo  $work_order_no; ?></td>
        </tr>
        <tr>
            <td></td>
            <td align="left"></td>
            <td align="left"><strong>Place of Delivery</strong></td>
            <td align="left" >:&nbsp;&nbsp;<? echo $delivery_place; ?></td>
        </tr>
         <tr>
            <td></td>
            <td align="left" ></td>
            <td align="left" ><strong>Our Ref.</strong></td>
            <td align="left" >:&nbsp;&nbsp;<? echo $req_no; ?></td>
        </tr>
         <tr>
            <td></td>
            <td align="left"></td>
            <td align="left"><strong>Quotation ID</strong></td>
            <td align="left">: &nbsp;&nbsp;<? echo $quot_sys_id; ?></td>
        </tr>
         <tr>
            <td></td>  <td></td>
            <td align="left"></td>
            <td align="left"><strong>Currency </strong></td>
            <td align="left">: &nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
        </tr>
        <tr>
            <td></td>  <td></td>
            <td align="left"></td>
            <td align="left"><strong><? if($bin_no) echo 'BIN';else echo ''; ?> </strong></td>
            <td align="left">: &nbsp;&nbsp;<? if($bin_no) echo $bin_no;else echo ''; ?></td>
        </tr>
        <tr>
            <td><strong><? //echo $data[16];?></strong></td>
            <td colspan="3" align="left"><strong><font size="4px"><? echo $data[3];?></font></strong></td>
        </tr>
        <tr>
           <td colspan="5" align="left">Dear Concern,<br/><strong><? echo $company_library[$data[0]]; ?></strong> is Pleased to inform You that Your price offer has been accepted with the following terms .
	</td>
        </tr>
    </table>
    <br>
		<table align="center" cellspacing="0" width="675"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="180" align="center">Item Description</th>
                <th width="70" align="center">Specification</th>
                <th width="50" align="center">Order UOM</th>

                <th width="70" align="center">WO.Qty</th>
                <th width="80" align="center">Rate</th>
                <th width="95" align="center">Amount</th>
                <th width="80" align="center">Remarks</th>
            </thead>
			 <?
            $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id in($data[2])");
			$remark_data_arr=array();
			foreach($sql_result as $row)
			{
				 $remark_data_arr[$row[csf('product_id')]]=$row[csf('remarks')];
			}
			$cond="";
			if($data[1]!="") $cond .= " and a.id='$data[1]'";
			//if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
			$i=1;
			//echo "select a.id,a.wo_number,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.amount,b.rate,d.item_description,d.item_size,d.item_group_id,d.item_account
			//from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
			//where a.id=b.mst_id and b.item_id=d.id $cond";
			$sql_result= sql_select("select b.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, d.id as prod_id, b.supplier_order_quantity, b.remarks, b.gross_amount, b.gross_rate, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d
			where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond ");
			foreach($sql_result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

					$req_quantity=$row[csf('req_quantity')];
					$req_quantity_sum += $req_quantity;

					$supplier_order_quantity=$row[csf('supplier_order_quantity')];
					$supplier_order_quantityl_sum += $supplier_order_quantity;

					$amount=$row[csf('gross_amount')];
					$total_amount+= $amount;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $row[csf('id')]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                <td><? echo $quot_factor_val; ?></td>
                <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>

                <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                <td align="right"><? echo number_format($row[csf('gross_amount')],2);?></td>
                <td><? echo $row[csf('remarks')];//$remark_data_arr[$row[csf('prod_id')]]; ?></td>
                 <?
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
			</tr>
			<?php
			    $tot_wo_qty += $row[csf('supplier_order_quantity')];
				$i++;
			}
			?>
        <tr>
            <td align="right" colspan="6" ><strong>Total :</strong> </td>
           <?php /*?> <td align="right"><? //echo number_format($tot_wo_qty,2) ?></td>
	<td align="right"><? // echo number_format($total_amount,0,'',',') ?></td><?php */?>
            <td align="right"><? echo $word_total_amount=number_format($total_amount,2,".",""); ?></td>
        </tr>
        <tr>
            <td colspan="5">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
            <td align="right" >Upcharge :&nbsp;</td>
            <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
        </tr>
        <tr>
            <td align="right" colspan="6">Discount :&nbsp;</td>
            <td align="right"><? echo number_format($discount,2,".","");  ?></td>
        </tr>
        <tr>
            <td align="right" colspan="6"><strong>Net Total : </strong>&nbsp;</td>
            <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
        </tr>
	</table>
           <table width="675" align="center">
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
                <tr>
					<td colspan="8"><strong> Amount in words:</strong><? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
				</tr>
                 <tr>
					<td colspan="8">&nbsp;</td>
				</tr>

 			</table>


            <?
                 echo get_spacial_instruction($work_order_no,'675px',145);
            ?>
        	<table width="675" align="center">
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
                <tr>
					<td colspan="8">Your scheduled delivery with quality and co-operation will be highly appreciated.<br><br>
                    Thank you
 				</td>
				</tr>
                 <tr>
					<td colspan="8">&nbsp;</td>
				</tr>

 			</table>
			 <br>
				<table width="380" align="left">
						<tr>
							<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
									<?
									if(count($approval_arr)>0)
									{				
										if($is_approved == 0){echo "Draft";}else{}
									}
									?>
							</div>
						</tr>
				</table>
            <br>

     		<?
	  $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	  $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=3 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
	 foreach ($approved_sql as $key => $value){
		$approved_by = $value[csf("approved_by")];
	 }
	  echo "<b>Approved by:</b>".$user_lib_name[$approved_by ];
	  echo signature_table(59, $data[0], "675px",$cbo_template_id,1,$user_lib_name[$inserted_by]);
	  ?>
 	</div>
      <?
}

if ($action=="dyes_chemical_work_print3")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	$cbo_template_id=$data[4];
    ?>
	<style>
		@media print{
			html>body table.rpt_table {
			--border:solid 1px;
			margin-left:12px;
			}

		}
        .rpt_table tbody tr td, thead th {
                font-size: 12pt !important;
        }
        .headTable tr td {
            font-size: 12pt !important;
        }
    </style>
    <?
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$lib_country_arr=return_library_array( "SELECT id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$requisition_library=return_library_array( "SELECT id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_designation=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_designation_name=return_library_array("SELECT id,system_designation from lib_designation", "id", "system_designation");

	$sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,country_id, contact_no, email, website,contract_person,bin_no from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
	$com_name=$sql_company[0][csf("company_name")];
	$company_short_name=$sql_company[0][csf("company_short_name")];
	$company_contact_no=$sql_company[0][csf("contact_no")];
	$company_email=$sql_company[0][csf("email")];
	$company_website=$sql_company[0][csf("website")];
	$company_bin=$sql_company[0][csf("bin_no")];
	$company_contract_person=$sql_company[0][csf("contract_person")];
	if($sql_company[0][csf("plot_no")]){$com_address.=$sql_company[0][csf("plot_no")];}
	if($sql_company[0][csf("level_no")]){$com_address.=', '.$sql_company[0][csf("level_no")];}
	if($sql_company[0][csf("road_no")]){$com_address.=', '.$sql_company[0][csf("road_no")];}
	if($sql_company[0][csf("block_no")]){$com_address.=', '.$sql_company[0][csf("block_no")];}
	if($sql_company[0][csf("city")]){$com_address.=', '.$sql_company[0][csf("city")];}
	if($sql_company[0][csf("country_id")]){$com_address.=', '.$lib_country_arr[$sql_company[0][csf("country_id")]];}

	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, insert_date, is_approved,inserted_by, inserted_by,tenor, payterm_id,inco_term_id, pi_issue_to,port_of_loading,upcharge_remarks,discount_remarks,wo_type,reference,up_charge,discount,contact  FROM  wo_non_order_info_mst WHERE id = $data[1]");

	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_name=$source[$row[csf("source")]];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$delivery_place=$row[csf("delivery_place")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by=$row[csf("inserted_by")];
		$tenor= $row[csf("tenor")];
		$payterm_id= $row[csf("payterm_id")];
		$inco_term= $row[csf("inco_term_id")];
		$pi_issue_to= $lib_company_arr[$row[csf("pi_issue_to")]];
		$source_id= $row[csf("source")];
		$port_of_loading= $row[csf("port_of_loading")];
		$upcharge_remarks= $row[csf("upcharge_remarks")];
		$discount_remarks= $row[csf("discount_remarks")];
		$remarks= $row[csf("remarks")];
		$wo_type= $row[csf("wo_type")];
		$reference= $row[csf("reference")];
		$up_charge= $row[csf("up_charge")];
		$discount= $row[csf("discount")];
		$contact_to= $row[csf("contact")];


        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,contact_person FROM  lib_supplier WHERE id = $supplier_id");

    foreach($sql_supplier as $supplier_data)
	{
		$row_mst[csf('supplier_id')];
		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
		$country = $supplier_data[csf('country_id')];
		$supplier_name = $supplier_data[csf('supplier_name')];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
		$supplier_contact_person = $contact_person;
	}

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$i = 0;
	$total_ammount = 0;
	$varcode_booking_no=$work_order_no;
	?>
    <table cellspacing="0" width="1100" >
        <tr>
            <td rowspan="4" width="70"><img src="../../<? echo $image_location; ?>" height="50" width="60"></td>
            <td style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td align="center"><? echo $com_address; ?></td>
        </tr>
        <tr>
            <td align="center">TEL: <?echo $company_contact_no;?>, Email: <?echo $company_email;?>, Website: <?echo $company_website;?></td>
        </tr>
        <tr>
            <td style="font-size:large;" align="center"><strong><? echo "Dyes And Chemical Purchase Order: ".$work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center">&nbsp;</td>
        </tr>
    </table>
	<table cellspacing="0" width="1100" border="1" rules="all" class="rpt_table">
		<tr>
			<td colspan="7"><b>Beneficiary/Supplier/Seller: </b></td>
			<td colspan="5"><b>Consignee/Buyer:</b></td>
		</tr>
		<tr>
			<td valign="top" colspan="7">
				<b><? echo $supplier_name; ?></b>
				<?
					echo '<br>'.$supplier_address."<br><b>TEL#:</b> ".$contact_no."<br><b>E-mail:</b> ".$supplier_email."<br><b>Contact Person:</b> ".$supplier_contact_person.'<br><br>' ;
				?>
			</td>
			<td valign="top" colspan="5">
				<b><? echo $company_library[$data[0]]; ?></b>
				<?
					echo '<br>'.$com_address."<br><b>TEL#:</b> ".$company_contact_no."<br><b>E-mail:</b> ".$company_email."<br><b>Contact To:</b> ".$contact_to.'<br><br>' ;
				?>
			</td>
		</tr>
		<tr>
			<td valign="top" width="60px"><b>WO Date</b></td>
			<td valign="top" width="80px"><? echo change_date_format($work_order_date); ?></td>
			<td valign="top" width="120px"><b>Delivery Date</b></td>
			<td valign="top" width="100px"><? echo change_date_format($delivery_date); ?></td>
			<td valign="top" width="80px"><b>Pay Mode</b></td>
			<td valign="top" width="60px"><? echo $pay_mode[$pay_mode_id]; ?></td>
			<td valign="top" width="80px"><b>WO Type</b></td>
			<td valign="top" width="120px"><? echo $wo_type_array[$wo_type]; ?></td>
			<td valign="top" width="80px"><b>Pay Terms</b></td>
			<td valign="top" width="80px"><? echo $pay_term[$payterm_id]; ?></td>
			<td valign="top" width="80px"><b>Currency</b></td>
			<td valign="top"><? echo $currency[$currency_id]; ?></td>
		</tr>
		<tr>
			<td valign="top"><b>Incoterm</b></td>
			<td valign="top"><? echo $incoterm[$inco_term];?></td>
			<td valign="top"><b>Port Of Loading</b></td>
			<td valign="top"><? echo $port_of_loading; ?></td>
			<td valign="top"><b>Tenor</b></td>
			<td valign="top"><? echo $tenor;?></td>
			<td valign="top"><b>WO Basis</b></td>
			<td valign="top"><? echo $wo_basis[$wo_basis_id]; ?></td>
			<td valign="top"><b>Reference</b></td>
			<td valign="top"><?echo $reference;?></td>
			<td valign="top"><b>BIN</b></td>
			<td valign="top"><?echo $company_bin;?></td>
		</tr>
		<tr>
			<td valign="top" colspan="2"><b>Place of Delivery</b></td>
			<td valign="top" colspan="4"><? echo $delivery_place; ?></td>
			<td valign="top"><b>Remarks</b></td>
			<td valign="top" colspan="5"><? echo $remarks; ?></td>
		</tr>
		<tr>
			<td colspan="12" align="center"><?if($is_approved){echo "<span style='color: red;'><b>APPROVED</b></span>";}?></td>
		</tr>
	</table>
	<br>
	<table align="left" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<th width="50">SL</th>
			<th width="150" align="center">Requisition No</th>
			<th width="120" align="center">Item Category</th>
			<th width="110" align="center">Item Group</th>
			<th width="170" align="center">Item Description</th>
			<th width="70" align="center">Req.Qty</th>
			<th width="70" align="center">WO Qty</th>
			<th width="50" align="center">UOM</th>
			<th width="50" align="center">Rate</th>
			<th width="90" align="center">PO Amount</th>
			<th align="center">Remarks</th>
		</thead>
		<?
		$cond="";
		if($data[1]!="") $cond .= " and a.id='$data[1]'";
		$i=1; $wo_qty_tot = 0;
		$sql_result= sql_select("select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id
		from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
		where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond ");
		foreach($sql_result as $row)
		{
			if ($i%2==0){ $bgcolor="#E9F3FF";} else{ $bgcolor="#FFFFFF";}

				$req_quantity=$row[csf('req_quantity')];
				$req_quantity_sum += $req_quantity;

				$supplier_order_quantity=$row[csf('supplier_order_quantity')];
				$supplier_order_quantityl_sum += $supplier_order_quantity;

				$amount=$row[csf('gross_amount')];
				$total_amount+= $amount;
				?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
				<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
				<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td><? echo $row[csf('item_description')]; ?></td>
				<td align="right"><? echo $row[csf('req_quantity')]; ?></td>
				<td align="right"><? echo $row[csf('supplier_order_quantity')]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
				<td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
				<td align="right"><? echo number_format($row[csf('gross_amount')],2,".","");?></td>
				<td><? echo $row[csf('remarks')]; ?></td>
				<?
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				?>
			</tr>
			<?php
			$i++;
            $wo_qty_tot +=$row[csf('supplier_order_quantity')];
		}
		$word_net_total_amount=number_format($total_amount+$up_charge-$discount, 2, '.', '')
		?>
		<tr >
			<td  colspan="6" align="right"><strong>WO Qty Total:</strong></td>
			<td align="right"><strong><?=$wo_qty_tot?></strong></td>
            <td></td>
			<td  align="right"><strong>Total:</strong></td>

            <td align="right"><strong><? echo number_format($total_amount, 2, '.', ''); ?></strong></td>
			<td rowspan="4">&nbsp;  </td>
		</tr>
		<tr>
            <td colspan="8" >Upcharge Remarks: <? echo $upcharge_remarks ;?></td>
            <td align="right"><strong>Upcharge:</strong></td>
            <td align="right"><strong><? echo number_format($up_charge, 2, '.', ''); ?></strong></td>

        </tr>
        <tr>
            <td colspan="8" >Discount Remarks: <? echo $discount_remarks ;?></td>
            <td align="right"><strong>Discount:</strong></td>
            <td align="right"><strong><? echo number_format($discount, 2, '.', ''); ?></strong></td>
        </tr>
        <tr>
            <td align="left" colspan="8" ></td>
            <td align="right"><strong>Net Total:</strong></td>
            <td align="right"><strong><? echo $word_net_total_amount; ?></strong></td>
        </tr>
        <tr>
            <td align="left" colspan="11" ><strong>Amount in words: </strong><? echo number_to_words($word_net_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
        </tr>
	</table>

	<br/>
	<style type="text/css">
		.box_odd{
			width: 50%;
			float: right;
			margin: 0;
			padding: 0;
			position: relative;
			overflow: hidden;
			margin-bottom: 10px;
		}
		.box_even{
			width: 50%;
			float: left;
			margin: 0;
			padding: 0;
			position: relative;
			overflow: hidden;
			margin-bottom: 10px;
		}
	</style>
	<?
		echo get_spacial_instruction($work_order_no,'1100px',145);

		$approved_sql=sql_select("SELECT mst_id, approved_by as APPROVED_BY ,approved_date as APPROVED_DATE from approval_history where entry_form=3 AND  mst_id ='$data[1]' order by approved_by");
		if(count($approved_sql)>0)
		{
			?>	<br/>
				<table cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
							<th colspan="4">Purchase Order Approval Status</th>
						</tr>
						<tr>
							<th width="50">SL</th>
							<th width="200">Name</th>
							<th width="120">Designation</th>
							<th>Approval Date</th>
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($approved_sql as $row)
							{
								?>
									<tr>
										<td><?=$i;?></td>
										<td><?echo $user_lib_name[$row["APPROVED_BY"]];?></td>
										<td><?echo $user_designation_name[$user_designation[$row["APPROVED_BY"]]];?></td>
										<td><?echo change_date_format($row["APPROVED_DATE"]);?></td>
									</tr>
								<?
								$i++;
							}
						?>
					</tbody>
				</table>
			<?
		}
		echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<?
	exit();
}

if ($action=="dyes_chemical_work_po_print2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=$data[1];
	$rpt_title=$data[3];
	$cbo_template_id=$data[4];
	echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');

	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$lib_company_arr=return_library_array( "SELECT id,company_name from lib_company","id", "company_name"  );
	$lib_store_name=return_library_array("SELECT id,store_name from lib_store_location where company_id=$data[0]", "id", "store_name");
	$user_lib_name=return_library_array("SELECT id,user_name from user_passwd", "id", "user_name");

	$sql_data = sql_select("SELECT a.wo_number as WO_NUMBER, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.supplier_id as SUPPLIER_ID, a.wo_basis_id as WO_BASIS_ID, a.requisition_no as REQUISITION_NO, a.delivery_place as DELIVERY_PLACE, a.wo_type as WO_TYPE, a.payterm_id as PAYTERM_ID, a.pay_mode as PAY_MODE, a.currency_id as CURRENCY_ID, a.remarks as REMARKS, a.is_approved as IS_APPROVED, to_char(a.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, b.user_full_name as USER_FULL_NAME, c.custom_designation as CUSTOM_DESIGNATION, a.up_charge as UP_CHARGE, a.discount as DISCOUNT, a.upcharge_remarks as UPCHARGE_REMARKS, a.discount_remarks as DISCOUNT_REMARKS from wo_non_order_info_mst a left join user_passwd b on b.id = a.inserted_by left join lib_designation c on c.id = b.designation WHERE a.id = $mst_id");
	$work_order_no=$sql_data[0]["WO_NUMBER"];
	$delivery_place=$sql_data[0]["DELIVERY_PLACE"];
    $carrency_id=$sql_data[0]['CURRENCY_ID'];
    $approved_id=$sql_data[0]['IS_APPROVED'];

    if($approved_id==1){ $approved_status="Full Approved";}
	else if($approved_id==3){ $approved_status="Partial Approved"; }
    else{ $approved_status="Not Approved"; }

	if($sql_data[0]["WO_BASIS_ID"]==1)
	{
		$sqlResult = sql_select("SELECT REQU_NO, STORE_NAME, LOCATION_ID from inv_purchase_requisition_mst where id in (".$sql_data[0]["REQUISITION_NO"].")");
		$i=0;
		foreach($sqlResult as $res)
		{
			if( $i>0 ){ $requNumber .= ",";$store_name .= ","; }
			$requNumber .= $res["REQU_NO"];
			$store_name .= $lib_store_name[$res["STORE_NAME"]];
			$location_id=$res["LOCATION_ID"];
			$i++;
		}
		$location_add=sql_select("SELECT ADDRESS,CONTACT_NO from lib_location where id=$location_id");
	}

	$supplier_sql=sql_select("SELECT SUPPLIER_NAME,ADDRESS_1,CONTACT_PERSON,CONTACT_NO,EMAIL from lib_supplier where id=".$sql_data[0]['SUPPLIER_ID']);
    $electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 626 and company_id=$data[0] and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 3 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 3 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 3 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_non_order_info_mst on wo_non_order_info_mst.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where mst_id = $mst_id and approval_history.entry_form = 3 and wo_non_order_info_mst.is_approved = 1 and approval_history.current_approval_status = 1 and approval_history.sequence_no =".max($electronic_sequence_arr));

	?>
	<div style="width:930px;">
		<table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td width="500" style="font-size:24px;" ><strong><? echo $lib_company_arr[$data[0]];  ?></strong><br></td>
                    <td rowspan="2"><strong style="font-size:24px; border: 1px dashed #000; padding: 2px 4px;"><? echo "Purchase Order - Dyes & Chemical";?></strong></td>
                </tr>
                <tr>
                    <td style="font-size:20px; vertical-align: top;" ><? echo $location_add[0]['ADDRESS']; ?></td>
                </tr>
                <tr>
                    <td style="font-size:20px; vertical-align: top;" ><? echo "Phone No: ".$location_add[0]['CONTACT_NO']; ?></td>
                    <td style="font-size:18px; vertical-align: top;"><strong style="padding-left: 100px;">Work Order Type: </strong><?=isset($wo_type_array[$sql_data[0]['WO_TYPE']]) ? $wo_type_array[$sql_data[0]['WO_TYPE']] : ''?><br><strong style="padding-left: 100px;">Pay Term: </strong><?=isset($pay_term[$sql_data[0]['PAYTERM_ID']]) ? $pay_term[$sql_data[0]['PAYTERM_ID']] : ''?></td>

                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td colspan="3" style="font-size:20px;border-bottom: 1px solid black;" ><strong>Supplier's Details</strong></td>
                    <td ></td>
                    <td style="font-size:20px;border-bottom: 1px solid black;" ><strong>Delivery Address</strong></td>
                </tr>
                <tr>
                    <td width="150" style="font-size:18px;" >Company Name</td>
                    <td width="20" >:</td>
                    <td width="250" style="font-size:18px;"><?=$supplier_sql[0]['SUPPLIER_NAME'];?></td>
                    <td width="180" align="center" rowspan="5" style="font-size:20px;color:red;"><strong><?=$approved_status;?> </strong></td>
                    <td rowspan="5" valign="top" style="font-size:18px;">
                        <?php
                        $format_delivery_address = explode('_', $delivery_place);
                        if(count($format_delivery_address) > 0){
                            $trim_delivery_address = array();
                            foreach ($format_delivery_address as $address){
                                if(!empty($address)){
                                    array_push($trim_delivery_address, trim($address, '_'));
                                }
                            }
                            echo implode(", ",  $trim_delivery_address);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Contact Person</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['CONTACT_PERSON'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" valign="top">Address</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['ADDRESS_1'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Cell</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['CONTACT_NO'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Email</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['EMAIL'];?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div style="border: 1px solid black; width:900px;">
            <table align="center" cellspacing="0" width="900" >
                <tbody>
                    <tr>
                        <td width="150" style="font-size:18px;"><b>REQ Number</b></td>
                        <td width="20" >:</td>
                        <td width="350" style="font-size:18px;"><?echo $requNumber;?></td>
                        <td width="150" style="font-size:18px;"><b>Delivery Date</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo change_date_format($sql_data[0]['DELIVERY_DATE']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Order Number</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $sql_data[0]['WO_NUMBER']; ?></td>
                        <td style="font-size:18px;"><b>Pay Mode</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $pay_mode[$sql_data[0]['PAY_MODE']]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Order Date</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo change_date_format($sql_data[0]['WO_DATE']); ?></td>
                        <td style="font-size:18px;"><b>Currency</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $currency[$sql_data[0]['CURRENCY_ID']]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Notes</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $sql_data[0]['REMARKS']; ?></td>
                        <td style="font-size:18px;"><b>Warehouse</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $store_name; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
		<table align="left" cellspacing="0" width="901.5"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr></tr>
                <th width="30" style="font-size:18px;">SL</th>
                <th width="180" style="font-size:18px;" >Item Description</th>
                <th width="100" style="font-size:18px;" >Model</th>
                <th width="100" style="font-size:18px;" >Brand</th>
                <th width="50" style="font-size:18px;" >Unit</th>
                <th width="70" style="font-size:18px;">Qty</th>
                <th width="90" style="font-size:18px;">Unit Price</th>
                <th width="100" style="font-size:18px;">Amount</th>
                <th style="font-size:18px;">Remarks</th>
            </thead>
            <tbody>
                <?

                    $sql_dtls= "SELECT a.id, a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.model as MODEL, b.brand_name as BRAND_NAME, b.unit_of_measure as UOM
                    from wo_non_order_info_dtls a, product_details_master b
					where a.mst_id=$mst_id and a.item_id=b.id and b.status_active=1 and a.status_active=1 ";
                    // echo $sql_dtls;
                    $sql_result= sql_select($sql_dtls);
                    $i=1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td style="font-size:18px;" ><? echo $i; ?></td>
                            <td style="font-size:18px;" ><? echo $row['ITEM_DESCRIPTION']; ?></td>
                            <td style="font-size:18px;" ><? echo $row['MODEL']; ?></td>
                            <td style="font-size:18px;" ><? echo $row['BRAND_NAME']; ?></td>
                            <td style="font-size:18px;" ><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                            <td style="font-size:18px;" align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                            <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$carrency_id].' '.number_format($row['RATE'],4,".",","); ?></td>
                            <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$carrency_id].' '.number_format($row['AMOUNT'],2,".",","); ?></td>
                            <td style="font-size:18px;" >&nbsp;<? echo $row['REMARKS']; ?></td>
                        </tr>
                        <?php
                        $tot_wo_amount += $row['AMOUNT'];
                        $i++;
                    }
                ?>
                <tr>
                    <td colspan="2" style="font-size:18px;"><strong>Upcharge Remarks</strong></td>
                    <td colspan="4" style="font-size:18px;"><?=$sql_data[0]['UPCHARGE_REMARKS']?></td>
                    <td style="font-size:18px;" align="right"><strong>Upcharge :</strong></td>
                    <td style="font-size:18px;" align="right"><?=$currency_sign_arr[$carrency_id].' '.number_format($sql_data[0]['UP_CHARGE'], 2, '.', ',')?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:18px;"><strong>Discount Remarks</strong></td>
                    <td colspan="4" style="font-size:18px;"><?=$sql_data[0]['DISCOUNT_REMARKS']?></td>
                    <td style="font-size:18px;" align="right"><strong>Discount :</strong></td>
                    <td style="font-size:18px;" align="right"><?=$currency_sign_arr[$carrency_id].' '.number_format($sql_data[0]['DISCOUNT'], 2, '.', ',')?></td>
                    <td></td>
                </tr>
                <tr>
                    <td  colspan="7" align="right" style="font-size:18px;"><strong>Total :</strong></td>
                    <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$carrency_id].' '.number_format($tot_wo_amount+$sql_data[0]['UP_CHARGE']-$sql_data[0]['DISCOUNT'], 2, '.', ','); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="9" style="font-size:18px;"><strong> Amount (in word):&nbsp;</strong><?echo number_to_words(number_format($tot_wo_amount+$sql_data[0]['UP_CHARGE']-$sql_data[0]['DISCOUNT'], 2, '.', ''),$currency[$carrency_id]);?></td>
                </tr>
            </tbody>
        </table>
		<br>
		<? echo get_spacial_instruction($work_order_no,'901.5px',145);	?>
        <br/>
        <table id="signatureTblId" width="901.5" style="padding-top:70px;">
            <tr>
                <td style="text-align: center; font-size: 18px;" width="230">
                    <strong><?=$sql_data[0]['USER_FULL_NAME']?></strong>
                    <br>
                    <strong><?=$sql_data[0]['CUSTOM_DESIGNATION']?></strong>
                    <br>
                    <?=$sql_data[0]['INSERT_DATE']?>
                </td>
                <td width="95"></td>
                <td style="text-align: center; font-size: 18px;" width="230">
                    <strong><?=isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['USER_FULL_NAME'] : ''?></strong>
                    <br>
                    <strong><?=$sql_get_checked_user[0]['CUSTOM_DESIGNATION']?></strong>
                    <br>
                    <?= isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['APPROVED_DATE'] : ""?>
                </td>
                <td width="95"></td>
                <td style="text-align: center; font-size: 18px;" width="230">
                    <strong><?=isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['USER_FULL_NAME'] : ''?></strong>
                    <br>
                    <strong><?=$sql_get_approved_user[0]['CUSTOM_DESIGNATION']?></strong>
                    <br>
                    <?= isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['APPROVED_DATE'] : ""?>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared by</strong></td>
                <td width="75"></td>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Checked by</strong></td>
                <td width="75"></td>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
            </tr>
        </table>

	</div>

	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<?
	exit();
}

if ($action=="dyes_chemical_work_print4")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$cbo_template_id=$data[4];
	// print_r ($data); die;
	echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
    ?>
	<style>
    @media print{
        html>body table.rpt_table {
        --border:solid 1px;
        margin-left:0px;
        }

    }
        .rpt_table tbody tr td, thead th {
                font-size: 11pt ;
        }
        .headTable tr td {
            font-size: 11pt;
        }
		.bordertbl{
			border: 1px solid;
			padding: 3px;
		}
		.paddingtbl{
			padding: 3px 0px;
		}

    </style>
    <?

		$sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,tin_number,bin_no from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
		$com_name=$sql_company[0][csf("company_name")];
		$company_short_name=$sql_company[0][csf("company_short_name")];
		$plot_no=$sql_company[0][csf("plot_no")];
		$level_no=$sql_company[0][csf("level_no")];
		$road_no=$sql_company[0][csf("road_no")];
		$block_no=$sql_company[0][csf("block_no")];
		$city=$sql_company[0][csf("city")];
		$zip_code=$sql_company[0][csf("zip_code")];
		$tin_num=$sql_company[0][csf("tin_number")];
		$bin_num=$sql_company[0][csf("bin_no")];

		$com_address='';
		if($plot_no !=''){ $com_address.=$plot_no;}
		if($level_no !=''){ $com_address.=", ".$level_no;}
		if($road_no !=''){ $com_address.=", ".$road_no;}
		if($block_no !=''){ $com_address.=", ".$block_no;}
		if($city !=''){ $com_address.=", ".$city;}
		if($zip_code !=''){ $com_address.=", ".$zip_code;}

	// $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value('location_name','lib_location',"company_id='$data[0]'",'location_name' );
	//$address=return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $address=return_field_value("city as address","lib_company","id=$data[0]",'address');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$requisition_department=return_library_array( "select id,department_id from  inv_purchase_requisition_mst", "id","department_id"  );
	$department=return_library_array( "select id,department_name from lib_department ", "id","department_name"  );
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, contact, wo_date, currency_id, supplier_id, attention, buyer_name, style, item_category, delivery_date, remarks,is_approved, is_approved,inserted_by, payterm_id,reference,wo_type,up_charge,discount,tenor  FROM  wo_non_order_info_mst WHERE id = $data[1]");
	$inserted_by=$user_lib_name[$sql_data[0][csf("inserted_by")]];

	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$payterm_id= $row[csf("payterm_id")];
		$tenor_day= $row[csf("tenor")];
		$source_id= $row[csf("source")];
		$contact_per= $row[csf("contact")];
		$wo_type= $row[csf("wo_type")];
		$reference= $row[csf("reference")];
		$upcharge= $row[csf("up_charge")];
		$discount= $row[csf("discount")];
		$remarks= $row[csf("remarks")];

        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
	{
		$row_mst[csf('supplier_id')];
		//if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
		//if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')];else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')];else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')];else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];
		$supplier_address = $address_1;
		$supplier_address2 = $address_2;
        $supplier_country =$country;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
		$supplier_contact_person = $contact_person;
	}
	$sql_group=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
	$i = 0;
	$total_ammount = 0;
	if($is_approved==1){$approved_note="Approved";}else{$approved_note="Un Approved";}
	$req_no= explode(",",$data[2]);
	$req_num='';
	foreach($req_no as $value){
		if($req_num!=''){$req_num.=", ".$requisition_library[$value];}else{$req_num=$requisition_library[$value];}
	}
	$department_no='';
	foreach($req_no as $value){
		if($department_no!=''){$department_no.=",".$requisition_department[$value];}else{$department_no=$requisition_department[$value];}
	}
	$dep=array_unique(explode(",",$department_no));
	$department_num='';
	foreach($dep as $value){
		if($department_num!=''){$department_num.=", ".$department[$value];}else{$department_num=$department[$value];}

	}
	$group_logo=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");


	$barcode_booking_no=$work_order_no;
	?>
	<div class="fontincrease">
    <table cellspacing="0" width="1000" align="center" >
        <tr>
            <td rowspan="2" width="100"><img src="<?= "../../".$group_logo;?>" height="60" width="90" alt="Group Logo"></td>
            <td colspan="3" style="font-size:25pt;" align="center"><strong><? echo $sql_group;?></strong></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:21pt;"><strong> Purchase Order- Dyes & Chemicals</strong></td>
        </tr>
         <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>
    </table>
    <table cellspacing="0" width="1000" class="headTable" >
        <tr>
            <td width="350" colspan="2" style="font-size:17pt;"><strong><?= $com_name;?></strong></td>
            <td width="200" align="left" class="bordertbl" style="font-size:16pt;"><strong>Purchase Type:</strong></td>
            <td width="250" align="left" class="bordertbl" style="font-size:16pt;"><strong><? echo $wo_type_array[$wo_type]; ?></strong></td>
        </tr>
        <tr>
            <td valign="top"><strong>Address:</strong></td>
            <td valign="top"><? echo $com_address; ?></td>
			<td align="left" class="bordertbl"><strong>P.O. Number:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td>
            <td align="left" style="font-size:15pt;"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
            <td align="left" class="bordertbl"><strong>P.O. Date:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_date; ?></strong></td>
        </tr>
         <tr>
            <td><strong>Address:</strong></td>
            <td align="left" ><? echo $supplier_address ; ?></td>
			<td align="left" valign="top" class="bordertbl"><strong>Req No:</strong></td>
            <td align="left" valign="top" class="bordertbl"><strong><? echo $req_num; ?></strong></td>
        </tr>
        <tr>
			<td><strong>Attn: </strong></td>
			<td align="left" valign="top"  ><?
			$attn= explode(",",$attention);
			foreach($attn as $value){
				echo "<div class='paddingtbl'>".$value."</div>";
			}
			?></td>
			<td align="left" class="bordertbl"><strong>Payment Terms:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Contact No: </strong></td>
            <td align="left" ></td>
            <td align="left" class="bordertbl"><strong>PO Status:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Department:</strong></td>
            <td align="left" ><? echo $department_num ; ?></td>
            <td align="left" class="bordertbl"><strong>Currency: </strong></td>
            <td align="left" class="bordertbl"><? echo $currency[$currency_id]; ?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
		<tr>
            <td align="left" colspan="2" >&nbsp;</td>
			<td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
    </table>
		<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="150" >Item</th>
                <th width="100" >Item Group</th>
                <th width="120" >Declaration Details</th>
                <th width="120" >Item Category</th>
                <th width="130">Narration</th>
                <th width="50" >Unit</th>
                <th width="80">Quantity</th>
                <th width="60" >Rate</th>
                <th >Amount</th>
            </thead>
			<tbody>
     <?
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $i=1;

    $sql_result= sql_select("select b.id, a.wo_number,a.currency_id,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account, b.remarks,b.item_category_id,e.brand_name,e.origin
    from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d,inv_purchase_requisition_dtls e
    where a.id=b.mst_id and b.item_id=d.id and b.requisition_dtls_id=e.id and b.status_active=1 and b.is_deleted=0 $cond ");
	foreach($sql_result as $row)
    {
		$req_quantity=$row[csf('req_quantity')];
		$req_quantity_sum += $req_quantity;

		$supplier_order_quantity=$row[csf('supplier_order_quantity')];
		$supplier_order_quantityl_sum += $supplier_order_quantity;

		$amount=$row[csf('gross_amount')];
		$total_amount+= $amount;
		$for_test_para.=$row[csf('item_category_id')].'_'.$row[csf('item_group_id')].'_'.$row[csf('item_description')].'#';
		$category.=$row[csf('item_category_id')].',';
		$group.=$row[csf('item_group_id')].',';
		$desc.="'".$row[csf('item_description')]."',";
		?>
        <tr bgcolor="#FFFFFF">
            <td align="center" ><? echo $i; ?></td>
            <td ><? echo $row[csf('item_description')]; ?></td>
			<td ><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
			<td ><? echo $row[csf('remarks')]; ?></td>
			<td ><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
			<td >
				<?
				if($row[csf('origin')])
				{
					echo $row[csf('brand_name')].', '.$lib_country_arr[$row[csf('origin')]];
				}
				else
				{
					echo $row[csf('brand_name')];
				}

				?>
			</td>
            <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td align="right"><? echo $row[csf('supplier_order_quantity')]; ?></td>
            <td align="right"><? $gross_rate=number_format($row[csf('gross_rate')],3); echo $currency_sign_arr[$row[csf('currency_id')]]." ". $gross_rate; ?></td>
            <td align="right"><? $gross_amount=number_format($row[csf('gross_amount')],2); echo $currency_sign_arr[$row[csf('currency_id')]]." ". $gross_amount;?></td>
            <?
                $carrency_id=$row[csf('currency_id')];
                if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
            ?>
        </tr>
    <?php
    $i++;
	}

    $sub_total_amount= $total_amount + $upcharge - $discount;
    $word_total_amount=number_format($sub_total_amount, 2);

    $upcharge=number_format($upcharge,2);
    $discount=number_format($discount,2);
    $total_amount=number_format($total_amount,2);
    //echo "<pre>";
    //print_r($testPara_arr);
	?>
	<tr >
        <td align="left" colspan="7" rowspan="4"></td>
        <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $total_amount; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2" ><strong>Discount</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $discount; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2"><strong>PO Charge</strong></td>
        <td align="right"><? echo $currency_sign_arr[$currency_id]." ". $upcharge; ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2" style="font-size:15pt;"><strong>Total Amount </strong></td>
        <td align="right" style="font-size:15pt;"><strong><? echo $currency_sign_arr[$currency_id]." ". $word_total_amount; ?></strong></td>
	</tr>
	<tr >
        <td align="left" colspan="9" ><strong style="font-size:15pt;"> Amount in words: </strong><? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
	</tr>
	</table>
	</tbody>
	<br/>
	<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	<tr >
        <td align="left" colspan="9" ><strong style="font-size:15pt;">Special Comments: </strong><? echo $remarks; ?></td>
	</tr>
	</table>
	<br/>
	<br/>
	<div><strong style="font-size:15pt;">Terms & Conditions:</strong></div>
	<?
	    $sql_term= sql_select("select terms from wo_booking_terms_condition where entry_form=145 and booking_no='$work_order_no' ");
		$i=1;
	foreach ($sql_term as $value) {
		echo $i.". ".$value[csf('terms')]."</br>";
		$i++;
	}
	?>
	<br/>

			<?
			if ($cbo_template_id != '') {
				$template_id = " and template_id=$cbo_template_id ";
			}

			$sql = sql_select("select designation,name,user_id,prepared_by from variable_settings_signature where report_id=59 and company_id='$data[0]'  $template_id order by sequence_no");

			$signature_sql = sql_select("SELECT c.master_tble_id as MASTER_TBLE_ID,c.image_location as IMAGE_LOCATION  from variable_settings_signature a, electronic_approval_setup b, common_photo_library c where a.user_id=b.user_id and a.user_id=c.master_tble_id and a.report_id=59 and a.company_id='$data[0]' and a.template_id=$cbo_template_id and b.page_id=626 and b.entry_form=3 and b.company_id=$data[0] and c.form_name='user_signature'");
			$signature_location=array();
			foreach($signature_sql as $row)
			{
				$signature_location[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
			}
			if($sql[0][csf("prepared_by")]==1){
				list($prepared_by,$activities)=explode('**',$prepared_by);
				$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME =>$inserted_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
				$sql=$sql_2+$sql;
			}
			$count = count($sql);
			$td_width = floor(1000 / $count);
			$standard_width = $count * 150;
			if ($standard_width > 1000) {
				$td_width = 150;
			}
			$i = 1;
			if ($count == 0) { echo "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
			else
			{
				echo '<table id="signatureTblId" width="1000" style="padding-top:10px;"><tr><td width="100%" height="50" colspan="' . $count . '">' . $message . '</td></tr><tr>';
				foreach ($sql as $row) {
					echo '<td width="' . $td_width . '" align="center" valign="bottom">';
					if($signature_location[$row[csf("user_id")]]!='')
					{
						echo '<strong><img src="../../'.$signature_location[$row[csf("user_id")]].'" height="60" width="90" ></strong><br>';
					}
					else
					{
						echo '<span style="height:60px;width:90px;"></span><br>';
					}
					echo '<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
					$i++;
				}
				echo '</tr></table>';
			}

		?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	fnc_generate_Barcode('<? echo $barcode_booking_no; ?>','barcode_img_id');
	</script>
		<?
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=19 and report_id in(132) and is_deleted=0 and status_active=1");
    //echo $print_report_format; die;
    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="delivery_info_popup")
{
  	echo load_html_head_contents("Place Of Delivery Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$txt_delivery_info_dtls_ref=explode("__",str_replace("'","",$hidden_delivery_info_dtls));  
	?>
	     
	<script>
		function js_set_value()
		{
	 		var txt_supply_name=$('#txt_supply_name').val();
			var txt_address_name=$('#txt_address_name').val();
			var txt_contact_person=$('#txt_contact_person').val();
			var txt_designation_name=$('#txt_designation_name').val();
			var txt_contact_no=$('#txt_contact_no').val();
			var txt_email=$('#txt_email').val();
            
            if(txt_supply_name!='' || txt_address_name!='' || txt_contact_person!='' || txt_designation_name!='' || txt_contact_no!='' || txt_email!='')
            {
                $('#hdn_delivery_info_dtls').val("__"+txt_supply_name+"__"+txt_address_name+"__"+txt_contact_person+"__"+txt_designation_name+"__"+txt_contact_no+"__"+txt_email);
            }else{
                $('#hdn_delivery_info_dtls').val(null);
            }

			parent.emailwindow.hide();
		}

	</script>

	</head>

	<body>
	<div align="center" style="width:400px;">
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off" >
    <legend>Place Of Delivery Info</legend>
	<table width="380" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            <tbody>
                <tr>
                	<td width="30" align="center" >1</td>
                	<td width="130" >SUPPLY TO :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_supply_name" id="txt_supply_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[1];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">2</td>
                	<td width="130" >ADDRESS :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_address_name" id="txt_address_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[2];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">3</td>
                	<td width="130" >CONTACT PERSON :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_contact_person" id="txt_contact_person" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[3];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">4</td>
                	<td width="130" >DESIGNATION :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_designation_name" id="txt_designation_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[4];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">5</td>
                	<td width="130">CONTACT NO. :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_contact_no" id="txt_contact_no" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[5];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">6</td>
                	<td width="130" >E-MAIL :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_email" id="txt_email" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[6];?>" /></td> 
            	</tr>

                <tr><td>&nbsp;</td></tr>
                <tr>
                	<td colspan="4" align="center">
                    <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="js_set_value();" />
                    <input type="hidden" id="hdn_delivery_info_dtls" name="hdn_delivery_info_dtls" />
                    </td>
                </tr>
            </tbody>         
    </table>    
    </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}
?>
