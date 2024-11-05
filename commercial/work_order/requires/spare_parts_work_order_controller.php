<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$category_general_row=implode(",",array_flip($general_item_category)).",97,101,105,106,114";
if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = " and lib_location.id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
$item_cat_other=implode(",",array_flip($general_item_category));
$item_cat_other.=",97,101,105,106,114";
if($item_cate_id !='') {
	$item_cate_other_arr=explode(",",$item_cat_other);
	$item_cate_cre_id_arr=explode(",",$item_cate_id);
	$item_cate_credential_cond="";
	foreach($item_cate_cre_id_arr as $cre_cat_id)
	{
		if(in_array($cre_cat_id,$item_cate_other_arr))
		{
			$item_cate_credential_cond.=$cre_cat_id.",";
		}
	}
    $item_cate_credential_cond = chop($item_cate_credential_cond,",");
}
else
{
     $item_cate_credential_cond=$item_cat_other;
}
//echo $item_cate_id."=".$item_cat_other."=".$item_cate_credential_cond;die;

if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========

$mrr_date_check="";
if($db_type==2 || $db_type==1 )
{
    $mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
}
else if ($db_type==0)
{
    $mrr_date_check="and year(insert_date)=".date('Y',time())."";
}


if ($action=="load_drop_down_location")
{
    $data=explode("_",$data);
    echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where company_id='$data[0]' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "");
    die;
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 110,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'spare_parts_work_order_controller', this.value, 'load_drop_down_section','section_td');" );
	die;
}

if ($action=="load_drop_down_section")
{
    echo create_drop_down( "cbo_section_name", 110,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	die;
}


if ($action=="load_drop_down_supplier_____111111111")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,6,7,30,36,37,39,92) and c.tag_company in($data) $supplier_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	die;
}

if ($action=="load_drop_down_supplier")
{
    echo create_drop_down('cbo_supplier', 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,7) and a.status_active=1 and a.is_deleted=0 and c.tag_company ='$data' group by a.id, a.supplier_name order by a.supplier_name", 'id,supplier_name', 1, '-- Select --', 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/spare_parts_work_order_controller');",0 );
    exit();
}

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
									$data_sql=sql_select("SELECT a.id as ID,a.supplier_name as SUPPLIER_NAME from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,7,6) and a.status_active=1 and a.is_deleted=0 and c.tag_company=$cbo_company_name group by a.id, a.supplier_name order by a.supplier_name");
									// var_dump($data_sql);die;
									$i=1; 
									foreach($data_sql as $row)
									{										
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[('ID')].'__'.$row[('SUPPLIER_NAME')];?>')"> 
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


if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}
if ($action=="load_drop_down_season")
{
    $exdata = explode("_",$data);
    $buyer_id=$exdata[0];
    $row_id=$exdata[1];
    echo create_drop_down( "cbo_season_".$row_id, 60, "select id, season_name from lib_buyer_season where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select-",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$row_id );
    exit();
}

if ($action=="necessity_setup_variable_form_lib")
{
    //$data_ref=explode("***",$data);
    $date = date('m/d/Y');
        
    if($db_type==0){
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date,'yyyy-mm-dd')."' and company_id='$data')) and page_id=22 and status_active=1 and is_deleted=0 order by id desc";
    }else{
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date, "", "",1)."' and company_id='$data')) and page_id=22 and status_active=1 and is_deleted=0 order by id desc";
    }
    $necessity_setup_res=sql_select($necessity_setup_sql);
    $necessity_setup=$necessity_setup_res[0][csf("approval_need")];
    // $necessity_setup=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_importer_id and variable_list=23 and status_active=1","source");
    
    echo $necessity_setup;die;
}

if($action=="load_drop_down_attention")
{
    $supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
    echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
    die;
}
if($action=="load_drop_down_brand")
{
    $exdata = explode("_",$data);
    $buyer_id=$exdata[0];
    $row_id=$exdata[1];
    // echo "select id,buyer_id, brand_name from LIB_BUYER_BRAND where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by brand_name ASC";
    echo create_drop_down( "txt_item_brand_".$row_id, 60, "select id,buyer_id, brand_name from LIB_BUYER_BRAND where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Select-",0, "",0,"","","","","","","txt_item_brand[]","txt_item_brand_".$row_id );
	die;
}

if ($action=="load_details_container"){   //chemical & dyes

    // echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    $explodeData = explode("**",$data);
    $woBasis = $explodeData[0];
    $company = $explodeData[1];
    //$category = $explodeData[2];
    //if($category==0) { echo ""; die; }

    if($woBasis==2) // independent
    {
        $i=1;

        $user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and status_active=1 and is_deleted=0");
        $itemAcctDoubleClick="";$itemDescDoubleClick="";
        if($user_given_code_status==1)
            $itemAcctDoubleClick = 'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
        else
            $itemDescDoubleClick =  'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
    ?>

    <div style="width:1500px;" align="left">
            <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                        	<th class="must_entry_caption">Item Group</th>
                            <th>Item Account</th>
                            <th class="must_entry_caption">Item Description</th>
                            <th>Item Category</th>
                            <th>Item Size</th>
                            <th>Origin</th>
                            <th>Nature</th>
                            <th>Profit Center</th>
                            <th>Model</th>
                            <th>Buyer</th>
                            <th>Brand</th>
                            <th>Season</th>
                            <th class="must_entry_caption">Order UOM</th>
                            <th class="must_entry_caption">Quantity</th>
                            <th class="must_entry_caption">Rate</th>
                            <th class="must_entry_caption">Amount</th>
                            <th>Available Budget</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general" id="<? echo $i;?>">
                            <td width="50">
                                <?
                                    echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1,"","","","","","","cbogroup[]","cbogroup_".$i );

                                ?>
                            </td>
                            <td width="80">
                                <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px"  <? echo $itemAcctDoubleClick; ?> />
                                <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <!-- Only for show. not used for Independent -->
                                <input type="hidden" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                                <input type="hidden" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                                <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value=""  />
                                <input type="hidden" name="txt_item_number[]" id="txt_item_number_<? echo $i;?>" value="" />
                                <!-- END -->
                            </td>
                            <td width="80">
                                <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" <? echo $itemDescDoubleClick; ?> />
                            </td>
                            <td width="80">
                                <?
                                    echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", "", "",1,$item_cate_credential_cond,"","","","","","cbo_item_category[]","cbo_item_category_".$i);

                                ?>
                            </td>
                            <td width="100">
                                <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                            </td>
                           
                            <td width="70"  align="center">
                                <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i );?>
                            </td>
                            <td width="70"  align="center">
							<? $nature=array(1=>"CAPEX",2=>"OPEX",3=>"Raw Materials");
                            if($val[csf("item_category_id")]==22)
                            {
                                echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 3, "",1,"","","","","","","cbonature[]","cbonature_".$i );
                            }
                            else
                            {
                                echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 0, "",0,"","","","","","","cbonature[]","cbonature_".$i );
                            }
                            ?>
                            </td>
                            <td width="70"  align="center">
                                <?
                                echo create_drop_down( "cboProfitCanter_".$i, 60, "select a.id, a.profit_center_name from  lib_profit_center a where a.status_active =1 and a.is_deleted=0 and a.company_id='$company' order by a.profit_center_name","id,profit_center_name", 1, "Select", 0, "",0,"","","","","","","cboProfitCanter[]","cboProfitCanter_".$i );
                                ?>
                            </td>
                            
                            <td width="70"  align="center">
                                <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px"/>
                            </td>
                            
                            <td width="60"  align="center">
                                <?
							   	    echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-Select-",0, "load_drop_down( 'requires/spare_parts_work_order_controller',this.value+'_'+$i, 'load_drop_down_brand', 'brand_td_$i' ); load_drop_down( 'requires/spare_parts_work_order_controller',this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i' );",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );
 							    ?>
                            </td>
                            <td width="70"  id="brand_td_<?php echo $i ; ?>"  align="center">
                                <!-- <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px"/> -->
                                <? echo create_drop_down( "txt_item_brand_".$i, 60, "","", 1, "Select", 0, "",0,"","","","","","","txt_item_brand[]","txt_item_brand_".$i );?>
                            </td>

                            <td width="60" id="season_td_<? echo $i;?>"  align="center">
                                <?
							   	    echo create_drop_down( "cbo_season_".$i, 60, "", 1, "-Select-",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

 							    ?>
                            </td>
                                        
                            <td width="100">
                                <?
                                    echo create_drop_down( "cbouom_".$i, 80, $unit_of_measurement,"", 1, "Select", 0, "",1,"","","","","","","cbouom[]","cbouom_".$i );

                                ?>
                            </td>
                            <td width="50">
                                <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
                            </td>
                            <td width="50">
                                <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                            </td>
                            <td width="80">
                                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_total_amount(1)"readonly />
                            </td>
                            <td width="100">
                                <input type="text" name="txt_avail_badget[]" id="txt_avail_badget_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" />
                            </td>
                            <td width="100">
                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px"  onDblClick="openmypage_remarks(1)"/>
                            </td>
                            <td width="80">
                                 <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                                 <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                            </td>
                        </tr>
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right" colspan="8">Upcharge Remarks:</td>
                            <td colspan="3" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Upcharge</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right" colspan="8">Discount Remarks:</td>
                            <td colspan="3" align="center"><input type="text" id="txt_dis_remarks" name="txt_dis_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>

                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Net Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
            </table>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
            <script type="text/javascript">
               // alert(2222);
               // setFieldLevelAccess(3);
            </script>
        </div>
    <?
        exit();
    }
    else //requisition container  header
    {
        ?>
            <div style="width:1500px;" align="left">
                <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all" align="left" >
                        <thead>
                            <tr id="0">
                                <th>Requisition No</th>
                                <th>Item Group</th>
                                <th>Item Account</th>
                                <th>Item Description</th>
                                <th>Item Category</th>
                                <th>Item Size</th>
                                <th>Brand</th>
                                <th>Origin</th>
                                <th>Nature</th>
                                <th>Profit Center</th>
                                <th>Model</th>     
                                <th>Buyer</th>
                                <th>Season</th>                     
                                <th>Order UOM</th>
                                <th>Req. Bal. Qnty</th>
                                <th>WO.Qnty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Available Budget</th>
                                <th>Remarks</th>
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
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Total</td>
                                <td style="text-align:center">
                                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
                                <td align="right" colspan="5">Upcharge Remarks:</td>
                                <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                                <td>Upcharge</td>
                                <td style="text-align:center">
                                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)" />
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
                                <td align="right" colspan="5">Discount Remarks:</td>
                                <td colspan="4" align="center"><input type="text" id="txt_dis_remarks" name="txt_dis_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                                <td>Discount</td>
                                <td style="text-align:center">
                                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)" />
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Net Total</td>
                                <td style="text-align:center">
                                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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
    $category = $explodeData[2];

    $user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and item_category_id='$category' and status_active=1 and is_deleted=0");
    $itemAcctDoubleClick="";$itemDescDoubleClick="";
    if($user_given_code_status==1)
        $itemAcctDoubleClick = 'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
    else
        $itemDescDoubleClick =  'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';

    ?>
    <tr class="general" id="<? echo $i;?>">
        <td width="50">
            <?
                echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1,"","","","","","","cbogroup[]","cbogroup_".$i);

            ?>
        </td>
        <td width="80">
            <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px"  <? echo $itemAcctDoubleClick; ?> />
            <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
            <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
            <!-- Only for show. not used for Independent -->
            <input type="hidden" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
            <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
            <input type="hidden" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
            <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value=""  />
            <!-- END -->
        </td>                        
        <td width="80">
            <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" <? echo $itemDescDoubleClick; ?> />
        </td>
        <td width="80">
        <?
            echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", "", "",1,$item_cate_credential_cond,"","","","","","cbo_item_category[]","cbo_item_category_".$i );

        ?>
        </td>
        <td width="100" >
            <input type="text" name="txt_item_size_<? echo $i;?>" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"/>
        </td>
        <!-- <td width="70"  align="center">
        <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? //echo $val[csf("brand")];?>" />
        </td> -->
        <td width="70"  align="center">
            <?
            echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i );
            ?>
        </td>

        <td width="70"  align="center">
            <? $nature=array(1=>"CAPEX",2=>"OPEX",3=>"Raw Materials");
            if($val[csf("item_category_id")]==22)
            {
                echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 3, "",1,"","","","","","","cbonature[]","cbonature_".$i );
            }
            else
            {
                echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 0, "",0,"","","","","","","cbonature[]","cbonature_".$i );
            }
            ?>
        </td>
        <td width="70"  align="center">
            <?
            echo create_drop_down( "cboProfitCanter_".$i, 60, "select a.id, a.profit_center_name from  lib_profit_center a where a.status_active =1 and a.is_deleted=0 and a.company_id='$company' order by a.profit_center_name","id,profit_center_name", 1, "Select", 0, "",0,"","","","","","","cboProfitCanter[]","cboProfitCanter_".$i );
            ?>
        </td>

        <td width="70"  align="center">
        <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("model")];?>" />
        </td>
        <td width="60"  align="center">
        <?
            echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-Select-",0, "load_drop_down( 'requires/spare_parts_work_order_controller',this.value+'_'+$i, 'load_drop_down_brand', 'brand_td_$i' ); load_drop_down( 'requires/spare_parts_work_order_controller',this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i' );",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );
        ?>
        </td>

        <td width="70" id="brand_td_<?php echo $i ; ?>"  align="center">
          
            <? echo create_drop_down( "txt_item_brand_".$i, 60, "","", 1, "Select", 0, "",0,"","","","","","","txt_item_brand[]","txt_item_brand_".$i );?>
         </td>
        <td width="60" id="season_td_<? echo $i;?>"  align="center">
            <?
                echo create_drop_down( "cbo_season_".$i, 60, " ", 1, "-Select-",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

            ?>
        </td>
        <td width="100">
            <?
                echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", 0, "",1,"","","","","","","cbouom[]","cbouom_".$i );

            ?>
        </td>
        <td width="50">
            <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
        </td>
        <td width="50">
            <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
        </td>
        <td width="80">
            <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_total_amount(1)" readonly />
        </td>
        <td width="100">
            <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
        </td>
        <td width="80">
             <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
             <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
        </td>
    </tr>

    <?
    die;
}

if($action=="load_drop_down_item_group")
{
    extract($data);
 
     
    echo create_drop_down( "cbogroup", 110,"SELECT id,item_name  from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", $selected, "" );
	die;
    
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
  
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                        <th colspan="5">
                            <?
                                echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4);
                            ?>
                        </th>
                    </tr>
                    <tr>
                        <th>Item Category</th>
                        <th>Item Group</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                            <td width="130">
                            <?
						//	print_r($item_cate_credential_cond);
                            //echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1 );
                            //echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "-- Select --", $item_cate_credential_cond, "","","");
							echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "-- Select --", "", "load_drop_down( '../requires/spare_parts_work_order_controller', this.value, 'load_drop_down_item_group', 'item_group_id_td' );",0,$item_cate_credential_cond,"","","","","");
                            ?>
                            </td>
                            <td width="120" id="item_group_id_td">
                            <?
                            echo create_drop_down( "cbogroup", 120, "","id,item_name", 1, "Select", "", "","" );
                            ?>
                            </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                             <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('cbogroup').value+'**'+document.getElementById('cbo_string_search_type').value, 'account_order_popup_list_view', 'search_div', 'spare_parts_work_order_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
    list($company,$itemCategory,$item_description,$item_code,$item_group,$search_type)=explode('**',$data);
    //echo $company.'**'.$itemCategory.'**'.$item_description.'**'.$item_code.'**'.$item_group;
    ?>

    <script>


    </script>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <form name="order_popup_1"  id="order_popup_1">
            <fieldset style="width:900px">
            <input type="hidden" id="item_1" />
                <?

                if($search_type==0 || $search_type==4)
                {

                    if($item_description!=""){$search_con=" and a.item_description like'%$item_description%'";}
                    if($item_code!=""){$search_con .= " and a.item_code like'%$item_code%'";}
                }
                else if($search_type==1)
                {

                    if($item_description!=""){$search_con=" and a.item_description = '$item_description'";}
                    if($item_code!=""){$search_con .= " and a.item_code = '$item_code'";}
                }
                else if($search_type==2)
                {

                    if($item_description!=""){$search_con=" and a.item_description like '$item_description%'";}
                    if($item_code!=""){$search_con .= " and a.item_code like '$item_code%'";}
                }
                else if($search_type==3)
                {

                    if($item_description!=""){$search_con=" and a.item_description like '%$item_description'";}
                    if($item_code!=""){$search_con .= " and a.item_code like '%$item_code'";}
                }

                if($item_group){$search_con .= " and a.item_group_id=$item_group";}
                if($itemCategory){$search_con .= " and a.item_category_id=$itemCategory";}

                if($itemCategory==4){
                    $entry_form=20;
                }else{
                    $entry_form=0;
                }
                
                if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
                $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,
                $sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.order_uom as unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name 
                from product_details_master a, lib_item_group b 
                where a.item_group_id=b.id and a.is_deleted=0 and a.item_category_id in (".$category_general_row.") AND a.entry_form = $entry_form  and company_id='$company' $itemIDScond $search_con";
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
	die;
}


if ($action=="load_php_popup_to_form")
{
    $explode_data = explode("**",$data);
    $data=$explode_data[0];
    $i=$explode_data[1];
    $comapny=$explode_data[2];
    //$item=$explode_data[2];

    if($data!="")
    {
        $nameArray=sql_select( "select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.order_uom as unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.brand_name as brand, a.origin, a.model, a.item_number
		from product_details_master a, lib_item_group b 
		where a.id in ($data) and a.status_active=1 and a.item_group_id=b.id and a.company_id=$comapny");

        $user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and status_active=1 and is_deleted=0");
        //$i=1;
        foreach ($nameArray as $val)
        {
        	?>
            <tr class="general" id="<? echo $i;?>">
                <td width="50">
                    <?
                        echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1,"","","","","","","cbogroup[]","cbogroup_".$i );

                    ?>
                </td>
                <td width="80">
                    <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_account")];?>" />
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                     <!-- Only for show. not used for Independent -->
                    <input type="hidden" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="" />
                    <input type="hidden" name="txt_item_number[]" id="txt_item_number_<? echo $i;?>" value="<? echo $val[csf("item_number")];?>" />
                    <!----------------- END ------------------------>
                </td>
                <td width="80">
                    <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_description")];?>" readonly />
                </td>
                <td width="80">
                <?
                //echo create_drop_down( "cbogroup_".$i, 80, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1 );
                echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,$item_cate_credential_cond,"","","","","","cbo_item_category[]","cbo_item_category_".$i );

				?>
                </td>
                <td width="100">
                    <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $val[csf("item_size")];?>" />
                </td>
                <!-- <td width="70"  align="center">
                    <input type="text" name="txt_item_brand[]" id="txt_item_brand_<?// echo $i;?>" class="text_boxes" style="width:65px" value="<?// echo $val[csf("brand")];?>" />
                </td> -->
                <td width="100">
                    <?
                    echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", $val[csf("origin")], "",0,"","","","","","","cboorigin[]","cboorigin_".$i );
                    ?>
                </td>
                <td width="70"  align="center">
                    <? $nature=array(1=>"CAPEX",2=>"OPEX",3=>"Raw Materials");
					if($val[csf("item_category_id")]==22)
					{
						echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 3, "",1,"","","","","","","cbonature[]","cbonature_".$i );
					}
					else
					{
						echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 0, "",0,"","","","","","","cbonature[]","cbonature_".$i );
					}
					?>
                </td>
                <td width="70"  align="center">
                    <?
                     echo create_drop_down( "cboProfitCanter_".$i, 60, "select a.id, a.profit_center_name from  lib_profit_center a where a.status_active =1 and a.is_deleted=0 and a.company_id='$comapny' order by a.profit_center_name","id,profit_center_name", 1, "Select", 0, "fn_budget_amt('".$i."',this.value)",0,"","","","","","","cboProfitCanter[]","cboProfitCanter_".$i );
                     ?>
                  </td>

                <td width="70"  align="center">
                    <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("model")];?>" />
                </td>


                <td width="70"  align="center">
					 <?
                    echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$comapny' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",0, "load_drop_down( 'requires/spare_parts_work_order_controller', this.value+'_'+$i, 'load_drop_down_brand', 'brand_td_$i');load_drop_down( 'requires/spare_parts_work_order_controller', this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i') ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );
               
                ?>
                </td>
                <td width="70" id="brand_td_<?php echo $i ; ?>"  align="center">
          
                 <? echo create_drop_down( "txt_item_brand_".$i, 60, "select buyer_id,brand_name from LIB_BUYER_BRAND where  status_active =1 and is_deleted=0 order by brand_name","buyer_id,brand_name", 1, "Select", 0, "",0,"","","","","","","txt_item_brand[]","txt_item_brand_".$i );?>
                 </td>
            <td width="60" id="season_td_<? echo $i;?>"  align="center">
            <?
            echo create_drop_down( "cbo_season_".$i, 60, " "," ", 1, "-Select-",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

            ?>
            </td>

                <td width="100">
                    <?
                        echo create_drop_down( "cbouom_".$i, 80, $unit_of_measurement,"", 1, "Select", $val[csf("unit_of_measure")], "",1,"","","","","","","cbouom[]","cbouom_".$i );

                    ?>
                </td>
                <td width="50">
                    <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" />
                </td>
                <td width="50">
                    <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                </td>
                <td width="80">
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_total_amount(1)" readonly />
                </td>
                <td width="100">
                    <input type="text" name="txt_avail_badget[]" id="txt_avail_badget_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" />
                </td>
                <td width="100">
                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:80px" value="" />
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


if ($action=="load_budget_data")
{
    $explode_data = explode("**",$data);
    $comapny=$explode_data[0];
    $wo_date=change_date_format($explode_data[1],'','',1);
    $cbo_item_category=$explode_data[2];
	$profit_center=$explode_data[3];
	
	
	$wo_date_arr=explode('-', str_replace("'", "",$wo_date));
	$wo_apply_month=$wo_date_arr[1]."-".$wo_date_arr[2];
	$up_wo_conds="";
	//if(str_replace("'","",$update_id)>0) $up_wo_conds=" and a.id <> $update_id ";
	$previous_wo_sql="select b.PROFIT_CENTER, b.ITEM_CATEGORY_ID, b.AMOUNT from WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.COMPANY_NAME=$comapny and b.NATURE=2 and b.PROFIT_CENTER > 0 and b.PROFIT_CENTER is not null and to_char(a.WO_DATE, 'Mon-YYYY')='$wo_apply_month' $up_wo_conds";
	//echo "10 ** $previous_wo_sql";die;
	$previous_wo_sql_result=sql_select($previous_wo_sql);
	$prev_wo_datas=array();
	foreach($previous_wo_sql_result as $val)
	{
		$prev_wo_datas[$val["PROFIT_CENTER"]][$val["ITEM_CATEGORY_ID"]]+=$val["AMOUNT"];
	}
	unset($previous_wo_sql_result);
	
	$lib_budge_sql="select b.PROFIT_CENTER, b.CATEGORY_MIX_ID from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.STATUS_ID=1 and CATEGORY_MIX_ID is not null and a.COMPANY_ID=$comapny and to_char(a.applying_date_from, 'Mon-YYYY')='$wo_apply_month'";
	//echo "10**$lib_budge_sql";die;
	$lib_budge_sql_result=sql_select($lib_budge_sql);
	$lib_budge_data=array();
	foreach($lib_budge_sql_result as $val)
	{
		$cat_wise_amt_arr=explode(",",$val["CATEGORY_MIX_ID"]);
		foreach($cat_wise_amt_arr as $cat_val)
		{
			$cat_amt_arr=explode("_",$cat_val);
			$lib_budge_data[$val["PROFIT_CENTER"]][$cat_amt_arr[0]]+=$cat_amt_arr[1];
		}
	}
	unset($lib_budge_sql_result);
	//print_r($lib_budge_data);print_r($prev_wo_datas);die;
    //$item=$explode_data[2];
	$cu_budget_amt=$lib_budge_data[$profit_center][$cbo_item_category]-$prev_wo_datas[$profit_center][$cbo_item_category];
	echo $cu_budget_amt;die;
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
		var item_category_arr = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
            tbl_row_count = tbl_row_count - 1;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                $('#tr_'+i).trigger('click');
            }
        }

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
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

        function js_set_value( strParam )
        {
			var splitArr = strParam.split("_");
			var str = splitArr[0];
			var numbers = splitArr[1];
			var ids = splitArr[2]; //requisition id
			var req_dtls_id = splitArr[3];  // item id
			var is_approved = splitArr[4];  // is_approved
			var approval_need = splitArr[5];  // is_approved
			var allow_partial = trim(splitArr[6]);  // is_approved
			
			var item_category_id = splitArr[7]; 
			var variable_category_mix = splitArr[8];
			
			if(variable_category_mix==2)
			{
				if(item_category_arr.length==0)
				{
					item_category_arr.push( item_category_id );
				}
				else if( jQuery.inArray( item_category_id, item_category_arr )==-1)
				{
					alert("Category Mixed is Not Allow");
					return;
				}
			}
			
			//alert(variable_category_mix);return;
			//$data=$i."_".$selectResult[csf('requ_no')]."_".$selectResult[csf('id')]."_".$selectResult[csf('req_dtls_id')]."_".$selectResult[csf('is_approved')]."_".$approval_need."_".$allow_partial."_".$selectResult[csf('item_category')]."_".$variable_category_mix;
			if (is_approved==0  && approval_need == 1) 
			{
				alert('Please Approved First!!');
				return;
			}
			else if (is_approved==3 && allow_partial != 1)
			{
				alert('Partial Approved Not Allow!!');
				return;
			}    
			js_set_value_for_item(req_dtls_id);
			//alert(selected_id);
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( ids, selected_id ) == -1 )
			{
				selected_id.push( ids );
				selected_number.push( numbers );
				//alert(1);
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == numbers ) break;
				}
				selected_id.splice( i, 1 );
				selected_number.splice( i, 1 );
				//alert(selected_id);
			}

			var num =''; var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
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
                if( jQuery.inArray( req_dtls_id, selected_dtlsID ) == -1 )
                {
                    selected_dtlsID.push( req_dtls_id );
                }
                else
                {
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
        <table width="1150" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <tr>
                    <td align="center" width="100%">
                        <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                            <thead>
                                <th width="140">Company Name</th>
                                <th width="140">Category Name</th>
                                <th width="100">Reqsition No</th>
                                <th width="100">For Division</th>
                                <th width="100">For Department</th>
                                <th width="100">For Section</th>
                                <th width="100">Approval Type</th>
                                <th width="160">Date Range</th>
                                <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                            </thead>
                            <tr>
                                <td align="center">
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company, "",1 );
                                    ?>
                                </td>
                                <td  align="center">
                                    <?
                                        echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", "", "","",$item_cate_credential_cond,"","","" );
                                    ?>
                                </td>
                                
                                <td  align="center"> <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px;" ></td>
                                <td>
                                    <? 
                                   echo create_drop_down( "cbo_division_name", 110,"select id,division_name from lib_division where company_id=$company and is_deleted=0  and status_active=1","id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'spare_parts_work_order_controller', this.value, 'load_drop_down_department','department_td');" );
                                    ?> 	
                                </td>
                                <td id="department_td">
                                    <? echo create_drop_down( "cbo_department_name", 110,$blank_array,"", 1, "-- Select --", $selected, "" );
                                    ?> 	
                                </td>
                                <td id="section_td">
                                    <? echo create_drop_down( "cbo_section_name", 110,$blank_array,"", 1, "-- Select --", $selected, "" );
                                    ?> 	
                                </td>

                                <td>
                                    <?
                                    if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
                                    else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
                                    $approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=13 and status_active=1 and is_deleted=0";
                                    $app_need_setup=sql_select($approval_status);
                                    $approval_need=$app_need_setup[0][csf("approval_need")];
                                    $allow_partial=$app_need_setup[0][csf("allow_partial")];
                                    //echo $approval_need.'**'.$allow_partial.'system';
                                    echo create_drop_down( "cbo_approval_type", 100, $yes_no, "", 1, "-- Select--", $approval_need, "","","" );
                                    ?>
                                </td>
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                                    <input type="hidden" id="txt_selected_ids" name="txt_selected_ids" value="<? echo $req_numbers_id; ?>" />
                                    <input type="hidden" id="txt_selected_numbers" name="txt_selected_numbers" value="<? echo $req_numbers; ?>" />
                                    <input type="hidden" id="txt_selected_dtls_id" name="txt_selected_dtls_id" value="<? echo $txt_req_dtls_id; ?>" />
                                </td>
                                <td align="center">
                                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $txt_req_dtls_id; ?>'+'_'+'<? echo $garments_nature; ?>'+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_approval_type').value+'_'+'<? echo $approval_need; ?>'+'_'+'<? echo $allow_partial; ?>'+'_'+document.getElementById('cbo_division_name').value+'_'+document.getElementById('cbo_department_name').value+'_'+document.getElementById('cbo_section_name').value, 'create_requisition_search_list_view', 'search_div', 'spare_parts_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:80px;" />
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
                    <td id="search_div"></td>
                </tr>
            </table>
        </form>


    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
         $('#cbo_division_name').val(0);
    </script>
    </html>
    <?
	exit();
}

if($action=="create_requisition_search_list_view")
{

    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $companyName = $ex_data[0];
    $itemCategory = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $req_dtls_id = str_replace("'","",$ex_data[4]);
    $garments_nature = $ex_data[5]; // not used here
    $req_no = $ex_data[6];
    $cbo_year=$ex_data[7];
    $approval_type=$ex_data[8];
    $approval_need=$ex_data[9];
    $allow_partial=$ex_data[10];
    $cbo_division_id=$ex_data[11];
    $cbo_department_id=$ex_data[12];
    $cbo_section_id=$ex_data[13];
	
	$variable_category_mix=return_field_value("cost_heads_status","variable_settings_commercial","status_active=1 and company_name=$companyName and variable_list=36","cost_heads_status");
    if ($db_type == 0)
    {
        /*$year_field = "YEAR(a.insert_date) as year";*/
    $year_cond = " and YEAR(a.insert_date) = $cbo_year ";
    }
    else if ($db_type == 2)
    {
        /*$year_field = "to_char(a.insert_date,'YYYY') as year";*/
    $year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
    }

    $sql_cond="";
    if($companyName!=0)
        $sql_cond = " and a.company_id = '".$companyName."'";
    if($itemCategory!=0)
        $sql_cond .= " and b.item_category = '".$itemCategory."'";
    if($txt_date_from!="" || $txt_date_to!="")
    {
        if($db_type==0) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        else if($db_type==2) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd','',-1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd','',-1)."'";
    }

    if ($req_no!="")
    {
        $sql_cond .=" and a.requ_prefix_num=$req_no";
    }

    $approval_cond='';
    if ($approval_type==1) $approval_cond=" and a.is_approved in(1,3)";
    if ($approval_type==2) $approval_cond=" and a.is_approved=0";

    $division_cond=''; $department_cond=''; $section_cond=''; 
    if ($cbo_division_id!=0) {$division_cond=" and a.division_id=$cbo_division_id";}
    if ($cbo_department_id!=0) {$department_cond=" and a.department_id=$cbo_department_id";}
    if ($cbo_section_id!=0) {$section_cond=" and a.section_id=$cbo_section_id";}

    /*$approval_need=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id = b.mst_id
    and b.page_id = 13 and a.company_id = $companyName
    and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $companyName )");
    if($approval_need==1)
    {
        $approval_cond = " and a.is_approved = '1'";
    }else{
        $approval_cond="";
    }*/


    //$sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        //$sql_cond .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
    //if($req_dtls_id!="") $sql_cond .= " or b.id IN ($req_dtls_id)";
    //if($req_dtls_id!="") $sql_cond .= " and b.id NOT IN ($req_dtls_id)";

    if($req_dtls_id=="") $req_dtls_id=0;
    $prev_req_wo=return_library_array("SELECT requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity from  wo_non_order_info_dtls where status_active=1 and requisition_dtls_id<>0 and requisition_dtls_id not in($req_dtls_id) group by requisition_dtls_id  order by requisition_dtls_id","requisition_dtls_id","supplier_order_quantity");

    $sql = "select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.location_id, a.is_approved, c.item_account, c.item_description, c.item_group_id, c.item_size, b.id as req_dtls_id, b.quantity,a.division_id,a.department_id,a.section_id, b.item_category
    from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b left join product_details_master c on  b.product_id = c.id and c.status_active in(1,3)
	where a.pay_mode <> 4 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.item_category not in(114) $sql_cond $year_cond and b.item_category in ($item_cate_credential_cond) $approval_cond $division_cond $department_cond $section_cond
	union all
	select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.location_id, a.is_approved, null as item_account, b.service_details as item_description, 0 as item_group_id, null as item_size, b.id as req_dtls_id, b.quantity, a.division_id, a.department_id, a.section_id, b.item_category
    from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
	where a.pay_mode <> 4 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.item_category in(114) $sql_cond $year_cond $approval_cond $division_cond $department_cond $section_cond
	order by requ_no";
    //echo $sql;
    $company=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $location=return_library_array("select id,location_name from lib_location",'id','location_name');
    $item_name=return_library_array("select id,item_name from lib_item_group",'id','item_name');
    $division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
    $department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
    $section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');

    ?>
    <div style="margin-top:10px; width:1400px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1380" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="50">Req. No</th>
                <th width="65">Req. Date</th>
                <th width="110">Company</th>
                <th width="110">Location</th>
                <th width="100">Division</th>
                <th width="100">Department</th>
                <th width="100">Section</th>
                <th width="90">Item Account</th>
                <th width="150">Description</th>
                <th width="90">Item Group</th>
                <th width="80">Item Size</th>
                <th width="80">Requisition Qnty</th>
                <th width="80">Prev. WO Qnty</th>
                <th width="80">Balance</th>
                <th>Approval Status</th>
            </thead>
         </table>
         <div style="width:1400px; overflow-y:scroll; max-height:200px">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1380" class="rpt_table" id="table_body">
                <?
                 $i=1; $txt_row_data=""; $hidden_dtls_id=explode(",",$req_dtls_id);
                 $nameArray=sql_select( $sql );
                 if(count($nameArray)<1)
                 {
					 echo "<tr style='text-align:center;font-size:15px;'><td><b>No Data Found.</b></td></tr>";
					 die;
                 }
                 foreach ($nameArray as $selectResult)
                 {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$balance=$selectResult[csf("quantity")]- $prev_req_wo[$selectResult[csf("req_dtls_id")]];
                    if($selectResult[csf("quantity")]>$prev_req_wo[$selectResult[csf("req_dtls_id")]])
                    {
                        $data=$i."_".$selectResult[csf('requ_no')]."_".$selectResult[csf('id')]."_".$selectResult[csf('req_dtls_id')]."_".$selectResult[csf('is_approved')]."_".$approval_need."_".$allow_partial."_".$selectResult[csf('item_category')]."_".$variable_category_mix;
                        if(in_array($selectResult[csf('req_dtls_id')],$hidden_dtls_id))
                        {
                            if($txt_row_data=="") $txt_row_data=$data; else $txt_row_data.=",".$data;
                        }

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $data; ?>')">
                            <td width="30" align="center"><?php echo "$i"; ?></td>
                            <td width="50" align="center"><p><?php echo $selectResult[csf('requ_prefix_num')]; ?>&nbsp;</p></td>
                            <td width="65"  align="center"><p><?php echo change_date_format($selectResult[csf('requisition_date')]); ?>&nbsp;</p></td>
                            <td width="110"><p><?php echo $company[$selectResult[csf('company_id')]]; ?>&nbsp;</p></td>
                            <td width="110"><p><?php echo $location[$selectResult[csf('location_id')]]; ?>&nbsp;</p></td>
                            <td width="100"><p><?php echo $division_arr[$selectResult[csf('division_id')]]; ?>&nbsp;</p></td>
                            <td width="100"><p><?php echo $department_arr[$selectResult[csf('department_id')]]; ?>&nbsp;</p></td>
                            <td width="100"><p><?php echo $section_arr[$selectResult[csf('section_id')]]; ?>&nbsp;</p></td>
                            <td width="90"><p><?php echo $selectResult[csf('item_account')]; ?>&nbsp;</p></td>
                            <td width="150"><p><?php echo $selectResult[csf('item_description')]; ?></p></td>
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
        <table width="1380" cellspacing="0" cellpadding="0" border="1" align="left">
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

    //function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )

    echo  create_list_view("table_body", "Requisition No, Requisition Date, Company, Location, Item Account, Description, Item Group, Item Size", "70,80,100,110,110,100,90,80","900","200", 0, $sql, "js_set_value", "requ_no,id,req_dtls_id", "",1,"0,0,company_id,location_id,0,0,item_group_id,0", $arr, "requ_prefix_num,requisition_date,company_id,location_id,item_account,item_description,item_group_id,item_size","spare_parts_work_order_controller","setFilterGrid('table_body',-1)",'1,3,0,0,0,0,0,0',"",1) ;*/
    exit();
}



if($action=="show_dtls_listview")
{
    extract($_REQUEST);
    $explodeData = explode("**",$data);
    $requisition_numberID = $explodeData[0];
    $requisition_numberID_arr=explode(",",$requisition_numberID);
    $reqDtlsID = str_replace("'","",$explodeData[1]);
    $rowNo = $explodeData[2];
    $update_id = $explodeData[3];
    $update_cond="";
    if($update_id>0) $update_cond=" and mst_id!=$update_id";
    if($update_id>0) $wo_update_cond=" and e.mst_id=$update_id";
    if($reqDtlsID=="") return; // for empty request
	
    $sql=sql_select("select requisition_no, supplier_order_quantity as order_quantity, requisition_dtls_id, mst_id  
	from wo_non_order_info_dtls where requisition_no in ('".implode("','",$requisition_numberID_arr)."') and status_active=1 and is_deleted=0");
    $requisitionQnty=array();$current_wo_data=array();
    foreach($sql as $result)
    {
		if(str_replace("'","",$update_id)==$result[csf("mst_id")])
		{
			 $current_wo_data[$result[csf("requisition_no")]][$result[csf("requisition_dtls_id")]]+=$result[csf("order_quantity")];
		}
		else
		{
			 $requisitionQnty[$result[csf("requisition_no")]][$result[csf("requisition_dtls_id")]]+=$result[csf("order_quantity")];
		}
    }
	//print_r($current_wo_data);
    $sql = "select a.id as requisition_id, a.requ_no, b.id, c.item_account, c.id as item_id, c.item_description, c.item_size, c.item_group_id, b.cons_uom, b.quantity, b.rate, b.amount, b.item_category as item_category_id, b.remarks, b.brand_name as brand, b.origin, c.model, a.company_id, c.item_number
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
	where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.id in ($reqDtlsID) and b.item_category<>114
	union all
	select a.id as requisition_id, a.requ_no, b.id, null as item_account, 0 as item_id, b.service_details as item_description, null as item_size, 0 asitem_group_id, b.cons_uom, b.quantity, b.rate, b.amount, b.item_category as item_category_id, b.remarks, b.brand_name as brand, b.origin, null as model,a.company_id, null as item_number
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.id in ($reqDtlsID) and b.item_category=114";
	//echo $sql;die;
	
    $sqlResult = sql_select($sql);
    if( count($sqlResult)==0 ){ echo "No Data Found";die;}
    if ($update_id!="") 
    {
       $wo_sql="SELECT e.requisition_no as requisition_id, e.requisition_dtls_id, e.id as wo_dtls_id, e.gross_rate as wo_rate
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
    $lib_item_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1",'id','item_name');
    $i=$rowNo+1; // row no increse 1
    foreach($sqlResult as $key=>$val)
    {
        $wo_dtls_id=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_dtls_id'];
        $wo_rate=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_rate'];
		$quantityRemaing = $val[csf("quantity")] - $requisitionQnty[$val[csf("requisition_id")]][$val[csf("id")]];
		$cu_wo_qnty=0;
		$cu_wo_qnty = $current_wo_data[$val[csf("requisition_id")]][$val[csf("id")]];
        $company=$val[csf("company_id")];
        ?>
        <tr class="general" id="<? echo $i;?>">
            <td width="80">
                <input type="text" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
                <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
                <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
                <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_id")];?>" readonly />
                <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="<? echo $wo_dtls_id; ?>" />
                <input type="hidden" name="txt_item_number[]" id="txt_item_number_<? echo $i;?>" value="<? echo $val[csf("item_number")];?>" />
            </td>
            <td width="80" title="<? echo $lib_item_arr[$val[csf("item_group_id")]];?>" >
                <? echo create_drop_down( "cbogroup_".$i, 80, $lib_item_arr,"", 1, "Select", $val[csf("item_group_id")], "",1,"","","","","","","cbogroup[]","cbogroup_".$i );  ?>
            </td>
            <td width="100" title="<? echo $val[csf("item_account")];?>">
                <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>" />
            </td>
            <td width="100" title="<? echo $val[csf("item_description")];?>">
                <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_description")];?>" />
            </td>
            <td width="80" title="<?=$item_category[$val[csf("item_category_id")]];?>">
                <? echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,'',"","","","","","cbo_item_category[]","cbo_item_category_".$i ); ?>
            </td>
            <td width="100">
            <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"  value="<? echo $val[csf("item_size")];?>" />
            </td>
            <td width="70"  align="center">
            <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("brand")];?>"/>
            </td>
            <td width="70"  align="center">
            <?
            echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", $val[csf("origin")], "",0,"","","","","","","cboorigin[]","cboorigin_".$i );
            ?>
            </td>
            <td width="70"  align="center">
                <? $nature=array(1=>"CAPEX",2=>"OPEX",3=>"Raw Materials");
				if($val[csf("item_category_id")]==22)
				{
					echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 3, "",1,"","","","","","","cbonature[]","cbonature_".$i );
				}
				else
				{
					echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 0, "",0,"","","","","","","cbonature[]","cbonature_".$i );
				}
                
                ?>
            </td>
            <td width="70"  align="center">
                <?
                 echo create_drop_down( "cboProfitCanter_".$i, 60, "select a.id, a.profit_center_name from  lib_profit_center a where a.status_active =1 and a.is_deleted=0 and a.company_id='$company' order by a.profit_center_name","id,profit_center_name", 1, "Select", 0, "fn_budget_amt('".$i."',this.value)",0,"","","","","","","cboProfitCanter[]","cboProfitCanter_".$i );?>
            </td>
            <td width="70"  align="center">
            <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("model")];?>"/>
            </td>

            <td width="70"  align="center">
                <?
                    echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",0, "load_drop_down( 'requires/stationary_work_order_controller', this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i'); ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

                ?>
            </td>
            <td width="70" id="season_td_<? echo $i;?>"  align="center">
                <?
                    echo create_drop_down( "cbo_season_".$i, 60, " "," ", 1, "-- Select --",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

                ?>
            </td>
            
            <td width="100">
            <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "Select", $val[csf("cons_uom")], "",1,"","","","","","","cbouom[]","cbouom_".$i );

            ?>
            </td>
            <td width="50">
            <input type="text" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="<? echo number_format($quantityRemaing,2,'.',''); ?>" />
            </td>
            <td width="50">
            <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? if($cu_wo_qnty>0) echo number_format($cu_wo_qnty,2,'.',''); else echo number_format($quantityRemaing,2,'.',''); ?>" />   <!-- This is wo qnty here -->
            </td>
            <td width="50"><?
				if($wo_rate!=''){
					$rate=$wo_rate;
					$disabled="disabled"; 
				}else{
					$rate=$val[csf("rate")];
					$disabled=""; 
				}
			?>
            <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" <? echo $disabled; ?> value="<? echo number_format($rate,4,'.',''); ?>" />
            </td>
            <td width="80">
            <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo number_format($rate*$quantityRemaing,4,'.',''); ?>" />
            </td>
            <td width="100">
                <input type="text" name="txt_avail_badget[]" id="txt_avail_badget_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" />
            </td>
            <td width="100">
            <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:80px"  value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
            </td>
            <td width="80">
            <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
            </td>
        </tr>
        <?
        $i++;
        }
        exit();
}



if($action=="terms_condition_popup")
{
    echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode,1);
    extract($_REQUEST);

    /*$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
    $terms_name = "";
    foreach( $terms_sql as $result )
    {
        $terms_name.= '{value:"'.$result[csf('terms')].'",id:'.$result[csf('id')]."},";
    }*/

    ?>
    <script>
    var terms_name = [<? echo substr(return_library_autocomplete( "select id,terms from lib_terms_condition order by id", "terms" ), 0, -1); ?>];

    function termsName(rowID)
    {
        $("#termsconditionID_"+rowID).val('');

        $(function() {
            //var terms_name = [<? //echo substr($terms_name, 0, -1); ?>];
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

                data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"");
            }
            var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
            //freeze_window(operation);
            http.open("POST","spare_parts_work_order_controller.php",true);
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
    }

    */
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
                http.open("POST","spare_parts_work_order_controller.php",true);
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
                        //echo "select terms_and_condition from wo_non_order_info_mst where id = $update_id";
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
	exit();
}


if($action=="save_update_delete_terms_condition")
{
    $process = array( &$_POST );

    extract(check_magic_quote_gpc( $process ));
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");

        //if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

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
        $rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","id",str_replace("'","",$txt_wo_number),1);
        //echo $rID."jahid";die;
        //check_table_status( $_SESSION['menu_id'],0);
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
        //oci_commit($con); oci_rollback($con);
        if($db_type==2 || $db_type==1 )
        {
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
        }
        disconnect($con);
        die;
    }
}

if($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_lc_type = str_replace("'","",$cbo_lc_type);
     

    if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        $txt_delivery_place=$hidden_delivery_info_dtls;
    }
    if($db_type==0)
    {
        $new_wo_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_wo_date)));
        $txt_wo_date = "'".$new_wo_date."'";
        $new_delivery_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_delivery_date)));
        $txt_delivery_date = "'".$new_delivery_date."'";
    }
    if($db_type==2 || $db_type==1 )
    {
        $txt_wo_date;
        $txt_delivery_date;
    }
	$all_item_id_arr=array();
	for($i=1;$i<=$total_row;$i++)
	{
		$item_id        = "txt_item_id_".$i;
		if(str_replace("'","",$$item_id)>0) $all_item_id_arr[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);
	}
	
	//cbo_company_name
	$company_id=str_replace("'", "",  $cbo_company_name);
	$wo_date_arr=explode('-', str_replace("'", "",$txt_wo_date));
	$wo_apply_month=$wo_date_arr[1]."-".$wo_date_arr[2];
	$up_wo_conds="";
	if(str_replace("'","",$update_id)>0) $up_wo_conds=" and a.id <> $update_id ";
	$previous_wo_sql="select b.PROFIT_CENTER, b.ITEM_CATEGORY_ID, b.AMOUNT from WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.COMPANY_NAME=$company_id and b.NATURE=2 and b.PROFIT_CENTER > 0 and b.PROFIT_CENTER is not null and to_char(a.WO_DATE, 'Mon-YYYY')='$wo_apply_month' $up_wo_conds";
	//echo "10 ** $previous_wo_sql";die;
	$previous_wo_sql_result=sql_select($previous_wo_sql);
	$prev_wo_datas=array();
	foreach($previous_wo_sql_result as $val)
	{
		$prev_wo_datas[$val["PROFIT_CENTER"]][$val["ITEM_CATEGORY_ID"]]+=$val["AMOUNT"];
	}
	unset($previous_wo_sql_result);
	
	$lib_budge_sql="select b.PROFIT_CENTER, b.CATEGORY_MIX_ID from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.STATUS_ID=1 and CATEGORY_MIX_ID is not null and a.COMPANY_ID=$company_id and to_char(a.applying_date_from, 'Mon-YYYY')='$wo_apply_month'";
	//echo "10**$lib_budge_sql";die;
	$lib_budge_sql_result=sql_select($lib_budge_sql);
	$lib_budge_data=array();
	foreach($lib_budge_sql_result as $val)
	{
		$cat_wise_amt_arr=explode(",",$val["CATEGORY_MIX_ID"]);
		foreach($cat_wise_amt_arr as $cat_val)
		{
			$cat_amt_arr=explode("_",$cat_val);
			$lib_budge_data[$val["PROFIT_CENTER"]][$cat_amt_arr[0]]+=$cat_amt_arr[1];
		}
	}
	unset($lib_budge_sql_result);
	
	//echo "10**<pre>";print_r($lib_budge_data);die;
	
	$item_create_setting =return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$company_id and variable_list=45 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	
	$prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where id in(".implode(",",$all_item_id_arr).")";
	$prod_sql_result=sql_select($prod_sql);
	$prod_category=$prod_group=array();$prod_sub_group=$prod_description="";
	foreach($prod_sql_result as $row)
	{
		$prod_com=$row["COMPANY_ID"];
		$prod_category[$row["ITEM_CATEGORY_ID"]]=$row["ITEM_CATEGORY_ID"];
		$prod_group[$row["ITEM_GROUP_ID"]]=$row["ITEM_GROUP_ID"];
		if($row["SUB_GROUP_NAME"]!="") $prod_sub_group.="'".$row["SUB_GROUP_NAME"]."',";
		if($row["ITEM_DESCRIPTION"]!="") $prod_description.="'".$row["ITEM_DESCRIPTION"]."',";
	}
	$prod_sub_group=chop($prod_sub_group,",");
	$prod_description=chop($prod_description,",");
	$des_cond="";
	if($prod_sub_group!="") $des_cond.=" and SUB_GROUP_NAME in($prod_sub_group)";
	if($prod_description!="") $des_cond.=" and ITEM_DESCRIPTION in($prod_description)";
	// $des_cond
	$des_wise_prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where status_active=1 and is_deleted=0 and COMPANY_ID='$prod_com' and ITEM_CATEGORY_ID in(".implode(",",$prod_category).") and ITEM_GROUP_ID in(".implode(",",$prod_group).")";
	$des_wise_prod_sql_result=sql_select($des_wise_prod_sql);
	$prod_data_arr=array();
	foreach($des_wise_prod_sql_result as $val)
	{
		$prod_data_arr[$val["COMPANY_ID"]][$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][trim($val["ITEM_DESCRIPTION"])][trim($val["ITEM_SIZE"])][trim($val["MODEL"])][trim($val["ITEM_NUMBER"])]=$val["ID"];
	}
	//echo "10**<pre>";print_r($prod_data_arr);die;
	unset($des_wise_prod_sql_result);
	
	//echo "10**";print_r($prod_data_arr);die;


    if ($operation==0) // Insert Here----------------------------------------------------------
    {
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }
        //table lock here
        if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}

        //echo "10**$txt_up_remarks";die;
        //-----------------------------------------------wo_non_order_info_mst table insert START here----------------------------------//
        //-------------------------------------------------------------------------------------------------------------------------------//
        $id=return_next_id("id", "wo_non_order_info_mst", 1);
        //$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and item_category in(8,9,10,15,16,17,18,19,20,21,22,32,34,36,35,37,38,39,23,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,69,70,89,90,91,92,93,94) $mrr_date_check order by id desc", "wo_number_prefix", "wo_number_prefix_num" ));
        $new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and entry_form = 147 $mrr_date_check order by id desc", "wo_number_prefix", "wo_number_prefix_num" ));
        $field_array_mst="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name,entry_form, requisition_no,delivery_place, wo_date, supplier_id, attention, wo_basis_id,  currency_id, delivery_date, source, pay_mode, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,location_id, discount_remarks, inserted_by, insert_date, ready_to_approved, inco_term_id,payterm_id,tenor,pi_issue_to,port_of_loading,reference,contact,wo_type,remarks,lc_type,contact_no";
        
        //echo $field_array."<br>".$data_array;die;
        //$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);,payterm_id,tenor,pi_issue_to,port_of_loading,".$cbo_payterm_id.",".$txt_tenor.",".$cbo_pi_issue_to.",".$txt_port_of_loading."
        //-----------------------------------------------wo_non_order_info_mst table insert END here-------------------------------------//
        //-------------------------------------------------------------------------------------------------------------------------------//



        //-----------------------------------------------wo_non_order_info_dtls table insert START here----------------------------------//
        //-------------------------------------------------------------------------------------------------------------------------------//
        $total_row = str_replace("'","",$total_row);
        $field_array="id, mst_id, requisition_dtls_id, requisition_no, item_id, remarks, uom, item_category_id, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount, inserted_by, insert_date, item_size, brand, origin, model,service_details,nature, profit_center, BUYER_ID, SEASON_ID";
		$req_dtls_field="product_id*updated_by*update_date";
        $dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
        $dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
        $data_array=""; $req_no_id_mst='';
        $check_item_id=array();$prod_scrtipt=true;$category_porfit_wise_total=array();
        for($i=1;$i<=$total_row;$i++)
        {
            if($i>1) $data_array.=",";
            $req_no_id      = "txt_req_no_id_".$i;
            $req_dtls_id    = "txt_req_dtls_id_".$i;
            $item_id        = "txt_item_id_".$i;
            $item_acct      = "txt_item_acct_".$i;
            $item_desc      = "txt_item_desc_".$i;
            $cbo_item_category      = "cbo_item_category_".$i;
			$txt_item_number      = "txt_item_number_".$i;
            $item_size      = "txt_item_size_".$i;
			$item_brand     = "txt_item_brand_".$i;
			$item_origin    = "cboorigin_".$i;
			$cbonature      = "cbonature_".$i;
			$cboProfitCanter= "cboProfitCanter_".$i;
			$item_model     = "txt_item_model_".$i;
            $cbogroup       = "cbogroup_".$i;
            $txt_remarks    = "txt_remarks_".$i;
            $cbouom         = "cbouom_".$i;
            $txt_req_qnty   = "txt_req_qnty_".$i;   //reuisition qnty
            $txt_quantity   = "txt_quantity_".$i;   //work order qnty
            $txt_rate       = "txt_rate_".$i;
            $txt_amount     = "txt_amount_".$i;
            $cbo_buyer      = "cbo_buyer_".$i;
            $cbo_season     = "cbo_season_".$i;
			$previous_prod_id=str_replace("'","",$$item_id);
		
            if( str_replace("'","",$cbo_wo_basis) == 1 )
			{
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) )
				{
                    echo "11**Work Order Qty Can't over than Requisition ddd Qty";disconnect($con);check_table_status( 175,0);die;
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
			
			if(str_replace("'","",$$cbonature)==2)
			{  
                $cb_profit = str_replace("'","",$$cboProfitCanter) ;
                $item_cat = str_replace("'","",$$cbo_item_category) ;
				$lib_budget_amt= $lib_budge_data[$cb_profit][$item_cat];
               
				$prev_wo_amt=$prev_wo_datas[$cb_profit][$item_cat];
				$tot_wo_amt=$prev_wo_amt+str_replace("'","",$$txt_amount);
				$available_balance=$lib_budget_amt-$prev_wo_amt;
				
				if($prev_wo_amt_check[$cb_profit][$item_cat]=="")
				{
					$prev_wo_amt_check[$cb_profit][$item_cat]=$cb_profit;
					$category_porfit_wise_total[$cb_profit][$item_cat]+=$tot_wo_amt;
				}
				else
				{
					$category_porfit_wise_total[$cb_profit][$item_cat]+=str_replace("'","",$$txt_amount);
				}
				 
				
				if($category_porfit_wise_total[$cb_profit][$item_cat]>$lib_budget_amt)
				{
					echo "11**Work Order Amount Not Allow Over Budget Amount Of This Month \n Item Category : ".$item_category[str_replace("'","",$$cbo_item_category)].", Item Description : ".str_replace("'","",$$item_desc)." \n Budget Amount = $lib_budget_amt  \n Previous WO Amount = $prev_wo_amt  \n Current WO Amount =".str_replace("'","",$$txt_amount)." \n Available Balance = $available_balance = $prev_wo_amt";disconnect($con);check_table_status( 175,0);die;  
				}
			}
			
			
			
            if( str_replace("'","",$$txt_quantity) != "" )
            {
				             
				if($item_create_setting==1 && str_replace("'","",$$cbo_item_category)!=114)
				{
					//echo "10**".str_replace("'","",$cbo_company_name)."=".str_replace("'","",$$cbo_item_category)."=".str_replace("'","",$$cbogroup)."=".trim(str_replace("'","",$$item_desc))."=".trim(str_replace("'","",$$item_size))."=".trim(str_replace("'","",$$item_model))."=".trim(str_replace("'","",$$txt_item_number));check_table_status( 175,0);disconnect($con);die;
					$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$cbo_item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))][trim(str_replace("'","",$$txt_item_number))];
					if($prod_id>0)
					{
						$prod_id=$prod_id;
					}
					else
					{
						$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$prod_id=$txt_product_id;
						$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
						if($prod_scrtipt==false)
						{
							echo "10**insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";oci_rollback($con);disconnect($con);die;
						}
					}
				}
				else
				{
					$prod_id=$previous_prod_id;
				}
				
	
				$check_item_id[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);
	
				$perc=(str_replace("'","",$$txt_amount)/str_replace("'","",$txt_total_amount))*100;
				$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
				$net_rate=$net_amount/str_replace("'","",$$txt_quantity);
				$net_rate=number_format($net_rate,12,'.','');
				$net_amount=number_format($net_amount,12,'.','');
				
                $data_array.="(".$dtlsid.",".$id.",'".$$req_dtls_id."','".$$req_no_id."','".$prod_id."','".$$txt_remarks."','".$$cbouom."','".$$cbo_item_category."','".$$txt_req_qnty."','".$$txt_quantity."','".$net_rate."','".$net_amount."','".$$txt_rate."','".$$txt_amount."','".$user_id."','".$pc_date_time."','".$$item_size."','".$$item_brand."','".$$item_origin."','".$$item_model."','".$$item_desc."','".$$cbonature."','".$$cboProfitCanter."',".$$cbo_buyer.",".$$cbo_season.")";
                $dtlsid=$dtlsid+1;
				if(str_replace("'","",$$req_dtls_id))
				{
					$req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
					$req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
				}
            }
            $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
        }

        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));
        $data_array_mst="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",147,'".$req_no_id_mst."',".$txt_delivery_place.",".$txt_wo_date.",".$cbo_supplier.",".$txt_attention.",".$cbo_wo_basis.",".$cbo_currency.",".$txt_delivery_date.",".$cbo_source.",".$cbo_pay_mode.",".$txt_total_amount.",".$txt_upcharge.",".$txt_discount.",".$txt_total_amount_net.",".$txt_up_remarks.",".$cbo_location.",".$txt_dis_remarks.",'".$user_id."','".$pc_date_time."',".$cbo_ready_to_approved.",".$cbo_inco_term.",".$cbo_payterm_id.",".$txt_tenor.",".$cbo_pi_issue_to.",".$txt_port_of_loading.",".$txt_reference.",".$txt_contact.",".$cbo_wo_type.",".$txt_remarks_mst.",".$cbo_lc_type.",".$txt_contact_no.")";
	
	
	
		//echo "11**<pre>";print_r($category_porfit_wise_total);disconnect($con);check_table_status( 175,0);die;
        //echo "10**".$field_array."<br>".$data_array;die;
        // echo "10**insert into wo_non_order_info_dtls ($field_array) values".$data_array;disconnect($con);die;
        $rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
        $dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array,$data_array,1);
		$req_dtlsrID=true;
		if(count($req_dtla_id_arr)>0 && $item_create_setting==1)
        {
            // bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
            $req_dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$req_dtls_field,$req_dtls_data,$req_dtla_id_arr),1);
        }
        //-----------------------------------------------wo_non_order_info_dtls table insert END here-----------------------------------//
        //-------------------------------------------------------------------------------------------------------------------------------//

        //echo "10**ttt**$rID**$dtlsrID**$prod_scrtipt**$req_dtlsrID=".count($req_dtla_id_arr);check_table_status( 175,0);die;


        if($db_type==0)
        {
            if($rID && $dtlsrID && $prod_scrtipt && $req_dtlsrID)
            {
                mysql_query("COMMIT");
                echo "0**".$new_wo_number[0]."**".$id."**".$dtlsid_check;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID && $prod_scrtipt && $req_dtlsrID)
            {
                oci_commit($con);
                echo "0**".str_replace("'","",$new_wo_number[0])."**".$id."**".$dtlsid_check;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo $dtlsrID."**".$new_wo_number[0]."**".$id."**".$dtlsid_check;
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
        if($db_type==0) { mysql_query("BEGIN"); }

        $sql_general_bill_arr = sql_select("select id from subcon_outbound_bill_mst where status_active = 1 and is_deleted = 0 and WO_NON_ORDER_INFO_MST_ID=$update_id");
		if($sql_general_bill_arr[0][csf('id')]>0){
		   echo "24**";die;disconnect($con); oci_rollback($con);
		}
		

        $update_id=str_replace("'","",$update_id);
        $approved_sql = "select a.is_approved from wo_non_order_info_mst a where a.id=$update_id  and a.entry_form=147 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }

     
        //table lock here
        if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}

        $update_check=str_replace("'","",$update_id);
        if($update_check>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$update_check and status_active=1","pay_mode");


        $wo_data=array();
        for($i=1;$i<=$total_row;$i++)
        {

            $item_id        = "txt_item_id_".$i;
            $txt_quantity   = "txt_quantity_".$i;   //work order qnty
            $txt_rate       = "txt_rate_".$i;
            $wo_data[str_replace("'","",$$item_id)]["quantity"]+=str_replace("'","",$$txt_quantity);
            $wo_data[str_replace("'","",$$item_id)]["rate"]=str_replace("'","",$$txt_rate);
        }
		
		$next_opt_check=0;

        

        if($update_check>0 && $pay_mode==2)
        {
            $pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
            from com_pi_master_details a, com_pi_item_details b
            where a.id=b.pi_id and a.item_category_id in($item_cate_credential_cond) and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$update_check");
            if(count($pi_sql)>0)
            {
				$next_opt_check=1;
                $pi_data=array();
                foreach($pi_sql as $row)
                {
                    $pi_data[$row[csf("item_prod_id")]]["quantity"]+=$row[csf("quantity")];
                    $pi_data[$row[csf("item_prod_id")]]["rate"]=$row[csf("rate")];
                }
				
                foreach($pi_data as $prod_id=>$prod_pi_val)
                {
                    if($wo_data[$prod_id]["quantity"]<$prod_pi_val["quantity"] && number_format($wo_data[$prod_id]["rate"],2,'.','')!=number_format($prod_pi_val["rate"],2,'.',''))
                    {
                        echo "11**PI Number Found, WO Quantity Not Allow Less Then PI Quantity  Or Rate Change Not Allow. \n So Update/Delete Not Possible.**$update_check";check_table_status( 175,0);disconnect($con);die;
                    }
                }
            }
        }
        //echo "10**jahid".$update_check."==".$cbo_pay_mode;check_table_status( 175,0);die;

        if($update_check>0 && $pay_mode!=2)
        {
            $mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
            from inv_receive_master a, inv_transaction b
            where a.id=b.mst_id and b.transaction_type=1 and a.entry_form in(20,263) and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$update_check");
            if(count($mrr_sql)>0)
            {
				$next_opt_check=1;
                $mrr_data=array();
                foreach($mrr_sql as $row)
                {
                    $mrr_data[$row[csf("prod_id")]]["quantity"]+=$row[csf("order_qnty")];
                    $mrr_data[$row[csf("prod_id")]]["rate"]=$row[csf("order_rate")];
					$mrr_data[$row[csf("prod_id")]]["recv_number"]=$row[csf("recv_number")];
                }

                foreach($mrr_data as $prod_id=>$prod_mrr_val)
                {
                    //if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"]) 
                    if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"] || number_format($wo_data[$prod_id]["rate"],2,'.','')!=number_format($prod_mrr_val["rate"],2,'.',''))
                    {
                        echo "11**Receive Number ". $prod_mrr_val["recv_number"]." Found, WO Quantity Or Rate Not Allow Less Then MRR Quantity and Rate, \n So Update/Delete Not Possible.**$update_check";check_table_status( 175,0);disconnect($con);die;
                    }
                }
            }
        }

        $mst_id=str_replace("'","",$update_id);
        $total_row = str_replace("'","",$total_row);
        $txt_delete_row = str_replace("'","",$txt_delete_row);
        /*if($txt_delete_row!="")
        {
            $delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
            //$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
        }*/

        $field_array_insert="id, mst_id, requisition_dtls_id, requisition_no, item_id, uom, item_category_id, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount, status_active, is_deleted, inserted_by, insert_date, item_size, brand, origin, model, service_details,nature,profit_center,BUYER_ID,SEASON_ID";

        $field_array="requisition_dtls_id*requisition_no*item_id*remarks*uom*req_quantity*supplier_order_quantity*rate*amount*gross_rate*gross_amount*status_active*is_deleted*updated_by*update_date*item_size*brand*origin*model*nature*profit_center*BUYER_ID*SEASON_ID";
		$req_dtls_field="product_id*updated_by*update_date";
        $dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
        $dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
        $data_array=array(); $req_no_id_mst='';$prod_scrtipt=true;$category_porfit_wise_total=array();
       
        for($i=1;$i<=$total_row;$i++)
        {          
            $req_no_id      = "txt_req_no_id_".$i;
            $req_dtls_id    = "txt_req_dtls_id_".$i;
            $item_id        = "txt_item_id_".$i;
            $item_acct      = "txt_item_acct_".$i;
            $item_desc      = "txt_item_desc_".$i;
            $cbo_item_category      = "cbo_item_category_".$i;
			$txt_item_number      = "txt_item_number_".$i;
            $item_size      = "txt_item_size_".$i;
			$item_brand     = "txt_item_brand_".$i;
			$item_origin    = "cboorigin_".$i;
            $cbonature      = "cbonature_".$i; 
            $cboProfitCanter= "cboProfitCanter_".$i;
			$item_model     = "txt_item_model_".$i;           
            $cbogroup       = "cbogroup_".$i;
            $txt_remarks    = "txt_remarks_".$i;
            $cbouom         = "cbouom_".$i;
            $txt_req_qnty   = "txt_req_qnty_".$i;   //reuisition qnty
            $txt_quantity   = "txt_quantity_".$i;   //work order qnty
            $txt_rate       = "txt_rate_".$i;
            $txt_amount     = "txt_amount_".$i;
            $cbo_buyer      = "cbo_buyer_".$i;
            $cbo_season     = "cbo_season_".$i;

            $dtls_ID        = "txt_row_id_".$i;
            $dtlsID_up      = str_replace("'","",$$dtls_ID);
			$previous_prod_id= str_replace("'","",$$item_id);

            $perc=(str_replace("'","",$$txt_amount)/str_replace("'","",$txt_total_amount))*100;
            $net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
            $net_rate=$net_amount/str_replace("'","",$$txt_quantity);

            $net_rate=number_format($net_rate,12,'.','');
            $net_amount=number_format($net_amount,12,'.','');
            if( str_replace("'","",$cbo_wo_basis) == 1 ){
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) ){
                    echo "11**Work Order Qty Can't over than Requisition Qty"; check_table_status( 175,0);disconnect($con);die;
                }
            }
			
			if(str_replace("'","",$$cbonature)==2)
			{
				$lib_budget_amt=$lib_budge_data[str_replace("'","",$$cboProfitCanter)][str_replace("'","",$$cbo_item_category)];
				$prev_wo_amt=$prev_wo_datas[str_replace("'","",$$cboProfitCanter)][str_replace("'","",$$cbo_item_category)];
				$tot_wo_amt=$prev_wo_amt+str_replace("'","",$$txt_amount);
				$available_balance=$lib_budget_amt-$prev_wo_amt;
				
				if($prev_wo_amt_check[$cb_profit][$item_cat]=="")
				{
					$prev_wo_amt_check[$cb_profit][$item_cat]=$cb_profit;
					$category_porfit_wise_total[$cb_profit][$item_cat]+=$tot_wo_amt;
				}
				else
				{
					$category_porfit_wise_total[$cb_profit][$item_cat]+=str_replace("'","",$$txt_amount);
				}
				
				if($category_porfit_wise_total[$cb_profit][$item_cat]>$lib_budget_amt)
				{
					echo "11**Work Order Amount Not Allow Over Budget Amount Of This Month \n Item Category : ".$item_category[str_replace("'","",$$cbo_item_category)].", Item Description : ".str_replace("'","",$$item_desc)." \n Budget Amount = $lib_budget_amt  \n Previous WO Amount = $prev_wo_amt  \n Current WO Amount =".str_replace("'","",$$txt_amount)." \n Available Balance = $available_balance";disconnect($con);check_table_status( 175,0);die;  
				}
			}
			
           
            if($dtlsID_up>0) //update
            {
				if($item_create_setting==1 && str_replace("'","",$$cbo_item_category)!=114)
				{
					$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$cbo_item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))][trim(str_replace("'","",$$txt_item_number))];
					if($prod_id>0)
					{
						$prod_id=$prod_id;                   
					}
					else
					{
						$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$prod_id=$txt_product_id;
						$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
						if($prod_scrtipt==false)
						{
							echo "10**insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";oci_rollback($con);disconnect($con);die;
						}
					}
				}
				else
				{
					$prod_id=$previous_prod_id;
				}
				

                $update_ID[]=$dtlsID_up;
                $data_array[$dtlsID_up]=explode("*",("'".$$req_dtls_id."'*'".$$req_no_id."'*'".$prod_id."'*'".$$txt_remarks."'*'".$$cbouom."'*'".$$txt_req_qnty."'*'".$$txt_quantity."'*'".$net_rate."'*'".$net_amount."'*'".$$txt_rate."'*'".$$txt_amount."'*1*0*'".$user_id."'*'".$pc_date_time."'*'".$$item_size."'*'".$$item_brand."'*'".$$item_origin."'*'".$$item_model."'*'".$$cbonature."'*'".$$cboProfitCanter."'*'".$$cbo_buyer."'*'".$$cbo_season."'"));
				if(str_replace("'","",$$req_dtls_id))
				{
					$req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
					$req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
				}
				
            }
            else if( str_replace("'","",$$txt_quantity) != "" ) // new insert
            {
				if($item_create_setting==1 && str_replace("'","",$$cbo_item_category)!=114)
				{
					$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$cbo_item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))][trim(str_replace("'","",$$txt_item_number))];
					if($prod_id>0)
					{
						$prod_id=$prod_id;
					}
					else
					{
						$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$prod_id=$txt_product_id;
						$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
						if($prod_scrtipt==false)
						{
							echo "10**insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$item_model)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";oci_rollback($con);disconnect($con);die;
						}
					}
				}
				else
				{
					$prod_id=$previous_prod_id;
				}
				
				
                //$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
                if($data_array_insert!="")$data_array_insert .=",";
                $data_array_insert.="(".$dtlsid.",".$mst_id.",'".$$req_dtls_id."','".$$req_no_id."','".$prod_id."','".$$cbouom."','".$$cbo_item_category."','".$$txt_req_qnty."','".$$txt_quantity."','".$net_rate."','".$net_amount."','".$$txt_rate."','".$$txt_amount."',1,0,'".$user_id."','".$pc_date_time."','".$$item_size."','".$$item_brand."','".$$item_origin."','".$$item_model."','".$$item_desc."','".$$cbonature."','".$$cboProfitCanter."',".$$cbo_buyer.",".$$cbo_season.")";
				if(str_replace("'","",$$req_dtls_id)) 
				{
					$req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
					$req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
				}
				
                $dtlsid=$dtlsid+1;
            }
            $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
        }
        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));
        //echo "10**".$req_no_id_mst; die;
        if($mst_id>0)
        {//*

            //*".",payterm_id,tenor,pi_issue_to,port_of_loading,".$cbo_payterm_id.",".$txt_tenor.",".$cbo_pi_issue_to.",".$txt_port_of_loading."
			if($next_opt_check)
			{
				$field_array_mst="requisition_no*delivery_place*wo_date*supplier_id*attention*wo_basis_id*currency_id*delivery_date*source*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*location_id*updated_by*update_date*ready_to_approved*inco_term_id*payterm_id*tenor*pi_issue_to*port_of_loading*reference*contact*wo_type*remarks*lc_type*contact_no";
            	$data_array_mst="'".$req_no_id_mst."'*".$txt_delivery_place."*".$txt_wo_date."*".$cbo_supplier."*".$txt_attention."*".$cbo_wo_basis."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_dis_remarks."*".$cbo_location."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_payterm_id."*".$txt_tenor."*".$cbo_pi_issue_to."*".$txt_port_of_loading."*".$txt_reference."*".$txt_contact."*".$cbo_wo_type."*".$txt_remarks_mst."*".$cbo_lc_type."*".$txt_contact_no."";
			}
			else
			{
				$field_array_mst="requisition_no*delivery_place*wo_date*supplier_id*attention*wo_basis_id*currency_id*delivery_date*source*pay_mode*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*location_id*updated_by*update_date*ready_to_approved*inco_term_id*payterm_id*tenor*pi_issue_to*port_of_loading*reference*contact*wo_type*remarks*lc_type*contact_no";
            	$data_array_mst="'".$req_no_id_mst."'*".$txt_delivery_place."*".$txt_wo_date."*".$cbo_supplier."*".$txt_attention."*".$cbo_wo_basis."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$cbo_pay_mode."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_dis_remarks."*".$cbo_location."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_payterm_id."*".$txt_tenor."*".$cbo_pi_issue_to."*".$txt_port_of_loading."*".$txt_reference."*".$txt_contact."*".$cbo_wo_type."*".$txt_remarks_mst."*".$cbo_lc_type."*".$txt_contact_no."";
			}
            
            //echo $field_array."<br />".$data_array;die;
            //$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
        }
        //echo $field_array_insert."<br>".$data_array_insert;die;
        //print_r($field_array);die;
        $rID=$delete_details=$dtlsrIDI=$dtlsrID=true;
        $field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
        $data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
        $delete_details=sql_delete("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"mst_id",$mst_id,1);

        //$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE mst_id=$mst_id",1);

        if($mst_id>0)
        {
            $rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
        }
        //echo "10**$rID";die;
        /*if($txt_delete_row!="")
        {
            $delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
            //$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
        }*/

        if($data_array_insert!="")
        {
            $dtlsrIDI=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,1);

        }
        if(count($update_ID)>0)
        {
            // bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
            $dtlsrID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$update_ID),1);
        }
		
		$req_dtlsrID=true;
		if(count($req_dtla_id_arr)>0 && $item_create_setting==1)
        {
            // bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
            $req_dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$req_dtls_field,$req_dtls_data,$req_dtla_id_arr),1);
        }
        //-----------------------------------------------wo_non_order_info_dtls table UPDATE END here-----------------------------------//
        //-------------------------------------------------------------------------------------------------------------------------------//
        // echo "10**$rID && $dtlsrID && $delete_details && $dtlsrIDI && $prod_scrtipt && $req_dtlsrID";die;

        if($db_type==0)
        {
            if($rID && $dtlsrID && $delete_details && $dtlsrIDI && $prod_scrtipt && $req_dtlsrID)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'","",$txt_wo_number)."**".$mst_id."**".$dtlsid_check;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID && $delete_details && $dtlsrIDI && $prod_scrtipt && $req_dtlsrID)
            {
                oci_commit($con);
                echo "1**".str_replace("'","",$txt_wo_number)."**".$mst_id."**".$dtlsid_check;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "1**".$txt_wo_number."**".$update_check."**".$dtlsid_check;
        }
        //release lock table
        check_table_status( 175,0);
        disconnect($con);
        die;
    }
    else if ($operation==2) // Delete Here----------------------------------------------------------
    {
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }

        $sql_general_bill_arr = sql_select("select id from subcon_outbound_bill_mst where status_active = 1 and is_deleted = 0 and WO_NON_ORDER_INFO_MST_ID=$update_id");
		if($sql_general_bill_arr[0][csf('id')]>0){
		   echo "24**";die;disconnect($con); oci_rollback($con);
		}
        
        $mst_id=str_replace("'","",$update_id);
        $txt_wo_number=str_replace("'","",$txt_wo_number);

        $update_id=str_replace("'","",$update_id);
        $approved_sql = "select a.is_approved from wo_non_order_info_mst a where a.id=$update_id  and a.entry_form=147 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }

        $sql = "select b.ENTRY_FORM from wo_non_order_info_mst a, inv_receive_master b where a.id=$update_id and a.id=b.booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form=263";

        // echo  $sql;die;
	    $wo_to_rec_qty=sql_select($sql);

         if($wo_to_rec_qty[0]['ENTRY_FORM']==263){
            echo "13**Update Or Delete not allowed";
            die;
         }
     

        $mst_sql=sql_select("select id, pay_mode from wo_non_order_info_mst where status_active=1 and entry_form=147 and wo_number = '$txt_wo_number'");
        $mst_id = $mst_sql[0][csf("id")];
		if($mst_id=="" || $mst_id==0){ echo "15**Work Order Not Found To Delete";disconnect($con);die;}
        //echo $txt_wo_number."_".$mst_id;die;

        $pi_arr = sql_select("select a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.work_order_no = '$txt_wo_number' and b.work_order_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

        $pi_number = $pi_arr[0][csf('pi_number')];
        //echo "10**$pi_number";disconnect($con);die;
        if($pi_number)
        {
            echo "11**Work Order Attached To Pro Forma Invoice No. ".str_replace("'", "", $pi_number);disconnect($con);die;
        }

        $rcv_arr = sql_select("select a.recv_number from inv_receive_master a, wo_non_order_info_mst b where a.booking_id = b.id and a.entry_form=20 and a.receive_basis = 2 and a.booking_id = $mst_id and a.company_id = $cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
        $rcv_no = $rcv_arr[0][csf('recv_number')];
		//echo "10**".$rcv_no;die;
        if($rcv_no)
        {
            echo "11**Receive No Found againts this Work Order No. ".$rcv_no;disconnect($con);die;
        }

        $rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,0);
		$dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
        // echo "10**".$rID."**".$dtlsrID.oci_commit($con);die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		//oci_commit($con); oci_rollback($con);
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
			//echo "2**".$rID;
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
                    document.getElementById('search_by_td').innerHTML='<input   type="text" name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common" value=""  />';
                }
                else if(str==2) // supplier
                {
                    var supplier_name = '<option value="0">--- Select ---</option>';
                    <?php
    $supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(1,7) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", 'id', 'supplier_name');

    //$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where FIND_IN_SET(2,party_type) order by supplier_name",'id','supplier_name');
    //select id, supplier_id from lib_supplier_party_type where party_type = 2 order by supplier_id
    /*
    $supplier_arr="select supplier.id, supplier.supplier_name
    from lib_supplier supplier
    join lib_supplier_party_type supp_party on supplier.id = supp_party.supplier_id
    where supp_party.party_type = 2 order by supplier_name";
    */

    foreach ($supplier_arr as $key => $val) {
        echo "supplier_name += '<option value=\"$key\">" . ($val) . "</option>';";
    }
    ?>
                    document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
                    document.getElementById('search_by_td').innerHTML='<select  name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
                }

            }

            function js_set_value(wo_number)
            {
                // alert('ok');
                $("#hidden_wo_number").val(wo_number);
                // $("#Print4").removeAttr("disabled");
                // $("#Print4").removeClass('formbutton_disabled');
                // $("#Print4").addClass('formbutton');

                parent.emailwindow.hide();
            }

        </script>

    </head>

    <body>
    <div align="center" style="width:100%;" >

    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
                                echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", "", "","",$item_cate_credential_cond,"","","4");
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
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'spare_parts_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:100px;" />
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
    $cond_year = $ex_data[7];


    $sql_cond="";
    if(trim($itemCategory)) $sql_cond .= " and b.item_category_id='$itemCategory'";
    if(trim($txt_search_common)!="")
    {

        if(trim($txt_search_by)==1)
            $sql_cond.= " and a.wo_number like '%".trim($txt_search_common)."'";
        else if(trim($txt_search_by)==2)
            $sql_cond.= " and a.supplier_id=trim('$txt_search_common')";
        else if(trim($txt_search_by)==3)
            $sql_cond.= " and d.requ_no like '%".trim($txt_search_common)."'";
    }
    //print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
    if($db_type==2)
    {
        if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
        $select_year="to_char(a.insert_date,'YYYY') as wo_year";
                $sql_cond .= " and to_char(a.insert_date,'YYYY') = $cond_year";
    }
    else if($db_type==0)
    {
        if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."'";
        $select_year="year(a.insert_date) as wo_year";
                $sql_cond .= " and year(a.insert_date) = $cond_year";
    }
    if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	
	if($itemCategory==0 && trim($txt_search_common)=="" && $txt_date_from=="" && $txt_date_to=="")
	{
		echo "Please Select Date Range";die;
	}
    /*$sql = "select
                id, wo_number, wo_number_prefix_num, $select_year, company_name, buyer_po, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode
            from
                wo_non_order_info_mst
            where
                status_active=1 and
                is_deleted=0
                $sql_cond order by id";*/ //and garments_nature=$garments_nature 8,9,10,15,16,17,18,19,20,21,22
    if(trim($txt_search_by)==3)
    {
        $sql = "SELECT a.id, a.wo_number, a.wo_number_prefix_num, $select_year, a.company_name, a.buyer_po, a.wo_date, a.supplier_id, a.attention, a.wo_basis_id, a.item_category, a.currency_id, a.delivery_date, a.source, a.pay_mode ,a.entry_form, a.inserted_by, a.ready_to_approved, a.is_approved
        from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c, inv_purchase_requisition_mst d 
        where a.id = b.mst_id and b.requisition_dtls_id=c.id and c.mst_id=d.id and a.wo_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond and a.entry_form = 147 and b.item_category_id in ($item_cate_credential_cond) 
        group by a.id, a.wo_number, a.wo_number_prefix_num, a.insert_date, a.company_name, a.buyer_po, a.wo_date, a.supplier_id, a.attention, a.wo_basis_id, a.item_category, a.currency_id, a.delivery_date, a.source, a.pay_mode ,a.entry_form, a.inserted_by, a.ready_to_approved, a.is_approved
        order by a.wo_date desc";
    }
    else
    {
        $sql = "SELECT a.id, a.wo_number, a.wo_number_prefix_num, $select_year, a.company_name, a.buyer_po, a.wo_date, a.supplier_id, a.attention, a.wo_basis_id, a.item_category, a.currency_id, a.delivery_date, a.source, a.pay_mode ,a.entry_form, a.inserted_by, a.ready_to_approved, a.is_approved 
        from wo_non_order_info_mst a, wo_non_order_info_dtls b
        where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and a.entry_form = 147 and b.item_category_id in ($item_cate_credential_cond) 
        group by a.id, a.wo_number, a.wo_number_prefix_num, a.insert_date, a.company_name, a.buyer_po, a.wo_date, a.supplier_id, a.attention, a.wo_basis_id, a.item_category, a.currency_id, a.delivery_date, a.source, a.pay_mode ,a.entry_form, a.inserted_by, a.ready_to_approved, a.is_approved
        order by a.wo_date desc";
    }

    // echo $sql;
    $result = sql_select($sql);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
    $yes_no_arr=array(0=>"No",1=>"Yes",2=>"No",3 => "Yes");
    $arr=array(0=>$company_arr,4=>$pay_mode,5=>$supplier_arr,6=>$wo_basis,7=>$source,8=>$user_arr,9=>$yes_no_arr,10=>$yes_no_arr);

    //function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )
    echo  create_list_view("list_view", "Company, WO Year, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source,Insert Users,Ready To Approved,Approval Status", "150,50,50,70,80,150,100,80,100,80,80","1060","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,0,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", $arr , "company_name,wo_year,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", "",'','0,0,0,3,0,0,0,0,0,0,0,0');

    exit();
}



if($action=="populate_data_from_search_popup")
{
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

    $sql = "SELECT id, requisition_no, delivery_place, company_name, buyer_po, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, is_approved, ready_to_approved, location_id, inco_term_id, payterm_id, tenor, pi_issue_to, port_of_loading, reference, contact, wo_type, remarks,lc_type,contact_no
    from wo_non_order_info_mst where id='$data'";

    //echo $sql;die;payterm_id,tenor,pi_issue_to,port_of_loading,".$cbo_payterm_id.",".$txt_tenor.",".$cbo_pi_issue_to.",".$txt_port_of_loading."
    $result = sql_select($sql);
    
    $sql_rcv = "SELECT id, booking_id from inv_receive_master where booking_id='$data' and entry_form=20 and status_active =1 and is_deleted = 0";
    $sql_rcv_result = sql_select($sql_rcv);
 
    foreach($result as $resultRow)
    {
        echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
        echo "$('#cbo_company_name').attr('disabled',true);\n";
        echo "$('#cbo_location').val('".$resultRow[csf("location_id")]."');\n";
        echo "$('#cbo_location').attr('disabled',true);\n";
        //echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
        echo "$('#cbo_item_category').attr('disabled',true);\n";
        echo "$('#txt_supplier_name').val('".$supplier_arr[$resultRow[csf("supplier_id")]]."');\n";
        echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";
        echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
      
        if(count($sql_rcv_result)>0){
           // echo "$('#cbo_currency').attr('disabled',true);\n";
            echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."').attr('disabled',true);\n";
        }
        else{
            echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";
        }

        echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
        echo "$('#cbo_wo_basis').attr('disabled',true);\n";
        echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";
        echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
        echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
        echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
        echo "$('#txt_req_numbers_id').val('".$resultRow[csf("requisition_no")]."');\n";
        echo "$('#cbo_lc_type').val('".$resultRow[csf("lc_type")]."');\n";
        // echo "$('#txt_delivery_place').val('".$resultRow[csf("delivery_place")]."');\n";
        $hdn_delivery=explode('__',$resultRow[csf("delivery_place")]);

		echo "$('#txt_delivery_place').val('".$hdn_delivery[0]."');\n";
        if(count($hdn_delivery)>1)
        {
            echo "$('#hidden_delivery_info_dtls').val('".$resultRow[csf("delivery_place")]."');\n";
        }

		echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
		echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
		echo "$('#cbo_pi_issue_to').val('".$resultRow[csf("pi_issue_to")]."');\n";
		echo "$('#txt_port_of_loading').val('".$resultRow[csf("port_of_loading")]."');\n";
		echo "$('#txt_reference').val('".$resultRow[csf("reference")]."');\n";
		echo "$('#txt_contact').val('".$resultRow[csf("contact")]."');\n";
        echo "$('#txt_contact_no').val('".$resultRow[csf("contact_no")]."');\n";
		echo "$('#cbo_wo_type').val('".$resultRow[csf("wo_type")]."');\n";
		echo "$('#txt_remarks_mst').val('".$resultRow[csf("remarks")]."');\n";

        echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
        echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term_id")]."');\n";
        if($resultRow[csf("pay_mode")]=='2'){
            $chkNextTran = return_field_value("a.id as pi_id", "com_pi_master_details a, com_pi_item_details b", "a.id =b.pi_id and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id=$data and a.status_active=1 and b.status_active=1", "pi_id"); 
        }else{
            $chkNextTran = return_field_value("id", "inv_receive_master", "booking_id=$data and entry_form=20 and receive_basis=2  and status_active=1", "id");
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
        //if($resultRow[csf("wo_basis_id")]!=1) echo "$('#txt_req_numbers').attr('disabled',true);\n";
        //else echo "$('#txt_req_numbers').attr('disabled',false);\n";

        $req_dtls_id="";$i=0;
        $sqlResult = sql_select("select requisition_dtls_id from wo_non_order_info_dtls where mst_id = ".$resultRow[csf("id")]." and status_active=1");
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
            echo "$('#approved').text('Full Approved');\n";

        }
        else if($resultRow[csf("is_approved")]==0)
        {
            echo "$('#approved').text('');\n";
        }
        else{

            echo "$('#approved').text('Partial Approved');\n";
        }

        if($chkNextTran != '' || $resultRow[csf("is_approved")] > 0){
            echo "$('#txt_supplier_name').attr('onDblClick',false);\n";
            echo "$('#cbo_supplier').attr('disabled',true);\n";
            echo "$('#cbo_location').attr('disabled',true);\n";
        }else {
            echo "$('#cbo_supplier').attr('disabled',false);\n";
            echo "$('#cbo_location').attr('disabled',false);\n";
        }
	  

    }
	
	 if($resultRow[csf("ready_to_approved")]!=1){
	   $refusing_cause = return_field_value("REFUSING_REASON","REFUSING_CAUSE_HISTORY","MST_ID = ".$resultRow[csf("id")]." and ENTRY_FORM=17 order by id desc");
        if($refusing_cause!=''){
            echo "$('#refusing_cause').text('".$refusing_cause."');\n";
        }
	 }

	
	
    exit();
}


if($action=="show_dtls_listview_update")
{
	$data_ref=explode("**",$data);
	$wo_id=$data_ref[0];
	$company_id=$data_ref[1];
	$wo_date=change_date_format($data_ref[2],'','',1);
	
	$wo_date_arr=explode('-', str_replace("'", "",$wo_date));
	$wo_apply_month=$wo_date_arr[1]."-".$wo_date_arr[2];
	$up_wo_conds="";
	if(str_replace("'","",$wo_id)>0) $up_wo_conds=" and a.id <> $wo_id ";
	$previous_wo_sql="select b.PROFIT_CENTER, b.ITEM_CATEGORY_ID, b.AMOUNT from WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.COMPANY_NAME=$company_id and b.NATURE=2 and b.PROFIT_CENTER > 0 and b.PROFIT_CENTER is not null and to_char(a.WO_DATE, 'Mon-YYYY')='$wo_apply_month' $up_wo_conds";
	//echo "10 ** $previous_wo_sql";die;
	$previous_wo_sql_result=sql_select($previous_wo_sql);
	$prev_wo_datas=array();
	foreach($previous_wo_sql_result as $val)
	{
		$prev_wo_datas[$val["PROFIT_CENTER"]][$val["ITEM_CATEGORY_ID"]]+=$val["AMOUNT"];
	}
	unset($previous_wo_sql_result);
	
	$lib_budge_sql="select b.PROFIT_CENTER, b.CATEGORY_MIX_ID from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.STATUS_ID=1 and CATEGORY_MIX_ID is not null and a.COMPANY_ID=$company_id and to_char(a.applying_date_from, 'Mon-YYYY')='$wo_apply_month'";
	//echo "10**$lib_budge_sql";die;
	$lib_budge_sql_result=sql_select($lib_budge_sql);
	$lib_budge_data=array();
	foreach($lib_budge_sql_result as $val)
	{
		$cat_wise_amt_arr=explode(",",$val["CATEGORY_MIX_ID"]);
		foreach($cat_wise_amt_arr as $cat_val)
		{
			$cat_amt_arr=explode("_",$cat_val);
			$lib_budge_data[$val["PROFIT_CENTER"]][$cat_amt_arr[0]]+=$cat_amt_arr[1];
		}
	}
	unset($lib_budge_sql_result);
	
    // $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $lib_item_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1",'id','item_name');
    

    $wo_pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$wo_id","pay_mode");
    $pi_mrr_data=array();
    if($wo_pay_mode==2)
    {
        $pi_mrr_sql="select b.item_prod_id as prod_id, b.quantity as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$wo_id";
    }
    else
    {
        $pi_mrr_sql="select b.prod_id as prod_id, b.order_qnty as quantity from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.entry_form=20 and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$wo_id";
    }

    $pi_mrr_result=sql_select($pi_mrr_sql);
    foreach($pi_mrr_result as $row)
    {
        $pi_mrr_data[$row[csf("prod_id")]]+=$row[csf("quantity")];
    }

    $sql = "SELECT a.wo_amount, a.up_charge, a.discount, a.net_wo_amount, b.id,a.wo_basis_id, b.requisition_dtls_id, b.po_breakdown_id, b.requisition_no, b.item_id, p.item_account, p.item_description, b.item_category_id, p.item_size, p.item_group_id as item_group, b.BRAND as brand, b.origin, p.model, b.req_quantity, b.color_name, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.gross_rate, b.gross_amount, c.requ_no, b.remarks, a.upcharge_remarks,a.discount_remarks, b.nature, b.profit_center, a.company_name, p.item_number,b.season_id,b.buyer_id
	from product_details_master p, wo_non_order_info_mst a, wo_non_order_info_dtls b left join inv_purchase_requisition_mst c on b.requisition_no=c.id
	where a.id=$wo_id and a.id=b.mst_id and b.item_id=p.id and b.status_active=1 and b.is_deleted=0 and p.status_active in(1,3) and b.item_category_id<>114
	union all
	select a.wo_amount, a.up_charge, a.discount, a.net_wo_amount, b.id, a.wo_basis_id, b.requisition_dtls_id, b.po_breakdown_id, b.requisition_no, b.item_id, null as item_account, b.service_details as item_description, b.item_category_id, null as item_size, 0 as item_group, b.BRAND as brand, b.origin, null as model, b.req_quantity, b.color_name, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.gross_rate, b.gross_amount, c.requ_no, b.remarks, a.upcharge_remarks, a.discount_remarks, b.nature, b.profit_center, a.company_name, null as item_number, b.season_id, b.buyer_id
	from wo_non_order_info_mst a, wo_non_order_info_dtls b left join inv_purchase_requisition_mst c on b.requisition_no=c.id
	where a.id=$wo_id and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.item_category_id=114";

    //echo $sql;
    $result = sql_select($sql);
	$com_id=$result[0][csf("company_name")];
	$lib_profit_center=return_library_array("select id, profit_center_name from lib_profit_center where status_active = 1 and is_deleted = 0 and COMPANY_ID=$com_id","id","profit_center_name");
    $i=1;
    ?>
    <tbody>
    <?
    foreach($result as $val)
    {
        $wo_basis=$val[csf("wo_basis_id")];
        if($i==1)
        {
			?>
            <div style="width:1300px;">
                <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all">
                    <thead>
                        <tr id="0">
                            <? if($val[csf("wo_basis_id")]==1 ){?>
                            <th>Requisition No</th>
                            <? } ?>
                            <th>Item Group</th>
                            <th>Item Account</th>
                            <th>Item Description</th>
                            <th>Item Category</th>
                            <th>Item Size</th>
                            
                            <th>Origin</th>
                            <th>Nature</th>
                            <th>Profit Center</th>
                            <th>Model</th>
                            <th>Buyer</th>
                            <th>Brand</th>
                            <th>Season</th>
                            
                            <th>Order UOM</th>
                            <? if($val[csf("wo_basis_id")]==1 ){?>
                            <th>Req. Bal. Qnty</th>
                            <? } ?>

                            <th>WO.Qnty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Available Budget</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
         	<? 
		}
		
		$budge_bal=$lib_budge_data[$val[csf("profit_center")]][$val[csf("item_category_id")]]-$prev_wo_datas[$val[csf("profit_center")]][$val[csf("item_category_id")]];
		?>
        <tr class="general" id="<? echo $i;?>">
            <!---- This is for requisition number selected in WO Basis START ---->
            <? if($val[csf("wo_basis_id")]==1)
			{
                echo "<td width=\"80\">";
            }
            if($pi_mrr_data[$val[csf("item_id")]]>0) $disable_field='disabled="disabled"'; else $disable_field='';

            ?>
                <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
                 <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
                 <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_dtls_id")];?>" readonly />
                <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_no")];?>" readonly />
                <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="<? echo $val[csf("id")]; ?>" />
                <input type="hidden" name="txt_item_number[]" id="txt_item_number_<? echo $i;?>" value="<? echo $val[csf("item_number")];?>" />
            <? if($val[csf("wo_basis_id")]==1)
			{
            echo "</td>";
            } ?>
            <td width="80" title="<? echo $lib_item_arr[$val[csf("item_group")]];?>">
                <?
                    echo create_drop_down( "cbogroup_".$i, 80, $lib_item_arr,"", 1, "Select", $val[csf("item_group")], "",1,"","","","","","","cbogroup[]","cbogroup_".$i );

                ?>
            </td>
            <!---- This is for requisition number selected in WO Basis END ---->
            <td width="100" title="<? echo $val[csf("item_account")];?>">
                <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>"  />
            </td>
            <td width="100" title="<? echo $val[csf("item_description")];?>">
                <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_description")];?>"  />
            </td>
            <td width="80" title="<? echo $item_category[$val[csf("item_category_id")]];?>">
            <?
              // $item_cate_credential_cond
                echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,'',"","","","","","cbo_item_category[]","cbo_item_category_".$i );
            ?>
            </td>
            <td width="80">
                <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $val[csf("item_size")];?>" />
            </td>
            <!-- <td width="70"  align="center">
                <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? //echo $val[csf("brand")];?>" />
            </td> -->
            <td width="60"  align="center">
                <?
                echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", $val[csf("origin")], "",0,"","","","","","","cboorigin[]","cboorigin_".$i );
                ?>
            </td>
            <td width="60"  align="center">
                <? $nature=array(1=>"CAPEX",2=>"OPEX",3=>"Raw Materials");
				if($val[csf("item_category_id")]==22)
				{
					echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", 3, "",1,"","","","","","","cbonature[]","cbonature_".$i );
				}
				else
				{
					echo create_drop_down( "cbonature_".$i, 60, $nature,"", 1, "Select", $val[csf("nature")], "",0,"","","","","","","cbonature[]","cbonature_".$i );
				}
                ?>
            </td>
            <td width="60"  align="center">
                <? 
                echo create_drop_down( "cboProfitCanter_".$i, 60, $lib_profit_center,"", 1, "Select", $val[csf("profit_center")], "fn_budget_amt('".$i."',this.value)",0,"","","","","","","cboProfitCanter[]","cboProfitCanter_".$i );?>
            </td>
            <td width="60"  align="center">
                <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:55px" value="<? echo $val[csf("model")];?>" />
            </td>

            <td width="60"  align="center">
				 <?
                 $company=$val[csf("company_name")];$buyer_id=$val[csf("buyer_id")];$season_id=$val[csf("season_id")];
                 echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",$buyer_id, "load_drop_down( 'requires/spare_parts_work_order_controller',this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i' );load_drop_down( 'requires\spare_parts_work_order_controller.php', this.value+'_'+$i, 'load_drop_down_brand', 'brand_td_$i'); ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

                ?>
            </td>
             <td width="70" id="brand_td_<?php echo $i ; ?>"  align="center">
            
              <? echo create_drop_down( "txt_item_brand_".$i, 60, "select buyer_id,brand_name from LIB_BUYER_BRAND where  status_active =1 and is_deleted=0 order by brand_name","buyer_id,brand_name", 1, "Select", 0, "",0,"","","","","","","txt_item_brand[]","txt_item_brand_".$i );?>  
            </td>
            <td width="60" id="season_td_<? echo $i;?>"  align="center">
                <?
                echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-- Select --",$season_id, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );
                ?>
            </td>

            <td width="60">
                <?
                    echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", $val[csf("uom")], "",1,"","","","","","","cbouom[]","cbouom_".$i );
                ?>
            </td>

            <? if($val[csf("wo_basis_id")]==1){
                echo "<td width=\"80\">";
            } ?>
                <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="<? echo number_format($val[csf("req_quantity")],2,'.',''); ?>" />
            <? if($val[csf("wo_basis_id")]==1){
                echo "</td>";
            } ?>

            <td width="50">
                <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($val[csf("supplier_order_quantity")],4,'.','');?>" />   <!-- This is wo qnty here -->
            </td>
            <td width="50">
                <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" value="<? echo number_format($val[csf("gross_rate")],8,'.','');?>" <? echo $disable_field; ?> />
            </td>
            <td width="80">
                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo number_format($val[csf("gross_amount")],4,'.','');?>" />
            </td>
            <td width="100">
                <input type="text" name="txt_avail_badget[]" id="txt_avail_badget_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $budge_bal;?>"  />
            </td>
            <td width="100">
                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:90px"  value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
            </td>
            <? 
			if($val[csf("wo_basis_id")]==1)
			{
				?>
                 <td width="80">
                      <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
            <? 
			}else{  
				?>
				<td width="80">
					 <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
					 <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
				</td>
				<? 
			} 
			?>
        </tr>
        <?
        $i++;
	}
	?>
	</tbody>
	<tfoot class="tbl_bottom">
		<tr>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
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
			<td>&nbsp;</td>
			<td>&nbsp;</td> 
            <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
			
			<td>Total</td>
			<td style="text-align:center">
				<input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo number_format($result[0][csf("wo_amount")],4,'.',''); ?>" style="width:75px;" readonly/>
			</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
		</tr>
		<tr>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
			<td>&nbsp;</td>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right" colspan="4">Upcharge Remarks:</td>
            
			<td colspan="5" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" value="<? echo $result[0][csf("upcharge_remarks")]; ?>" maxlength="100" placeholder="Maximum 100 Character" /></td>
			<td>Upcharge</td>
			<td style="text-align:center">
				<input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $result[0][csf("up_charge")]; ?>" style="width:75px;" onKeyUp="calculate_total_amount(2)" <? echo $disable_field; ?>/>
			</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
			<td>&nbsp;</td>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right" colspan="4">Discount Remarks:</td>
            
			<td colspan="5" align="center"><input type="text" id="txt_dis_remarks" name="txt_dis_remarks" class="text_boxes" style="width:90%;" value="<? echo $result[0][csf("discount_remarks")]; ?>" maxlength="100" placeholder="Maximum 100 Character" /></td>
			<td>Discount</td>
			<td style="text-align:center">
				<input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $result[0][csf("discount")]; ?>" style="width:75px;" onKeyUp="calculate_total_amount(2)" <? echo $disable_field; ?>/>
			</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
		</tr>
		<tr>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
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
			<td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<? if($wo_basis==1)
			{
				echo "<td>&nbsp;</td>";
			}
			?>
			<td>&nbsp;</td>
			<td>Net Total</td>
			<td style="text-align:center">
				<input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo number_format($result[0][csf("net_wo_amount")],4,'.',''); ?>" style="width:75px;" readonly/>
			</td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
            <td>&nbsp;</td>
		</tr>
	</tfoot>
	</table>
	<?
	exit();
}

?>

<?
if ($action=="spare_parts_work_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company=$data[0];
    $location=$data[3];
    echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
    // print_r ($data); die;
   	$cbo_template_id=$data[5];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$company",'bin_no');
	
	if($data[4] == 6)
    {
		// $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
		// $com_name=$sql_company[0][csf("company_name")];
		// $company_short_name=$sql_company[0][csf("company_short_name")];
		// $plot_no=$sql_company[0][csf("plot_no")];
		// $level_no=$sql_company[0][csf("level_no")];
		// $road_no=$sql_company[0][csf("road_no")];
		// $block_no=$sql_company[0][csf("block_no")];
		// $city=$sql_company[0][csf("city")];
		// $zip_code=$sql_company[0][csf("zip_code")];

		$location_arr=return_library_array( "select id,location_name from lib_location","id", "location_name"  );
		$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
		$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
		$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
		$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
		$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
		$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

        $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,discount_remarks, insert_date, is_approved, inserted_by, tenor, payterm_id, inco_term_id, pi_issue_to,port_of_loading 
		FROM  wo_non_order_info_mst WHERE id = $data[1]");
        //payterm_id,tenor,pi_issue_to,port_of_loading,delivery_place

        foreach($sql_data as $row)
        {
            $work_order_no=$row[csf("wo_number")];
            $work_order_id=$row[csf("id")];
            $item_category_id=$row[csf("item_category")];
            $supplier_id=$row[csf("supplier_id")];
            $work_order_date=$row[csf("wo_date")];
            $currency_id=$row[csf("currency_id")];
            $wo_basis_id=$row[csf("wo_basis_id")];
            $pay_mode_id=$row[csf("pay_mode")];
            //$source=$row[csf("source")];
            $delivery_date=$row[csf("delivery_date")];
            $attention=$row[csf("attention")];
            $requisition_no=$row[csf("requisition_no")];
            $delivery_place=$row[csf("delivery_place")];
            $wo_item_category=$row[csf("item_category")];
            $wo_amount=$row[csf("wo_amount")];
            $up_charge=$row[csf("up_charge")];
            $discount=$row[csf("discount")];
            $net_wo_amount=$row[csf("net_wo_amount")];
            $upcharge_remarks=$row[csf("upcharge_remarks")];
            $discount_remarks=$row[csf("discount_remarks")];
            $insert_date = $row[csf("insert_date")];
    		$inserted_by= $row[csf("inserted_by")];
    		$tenor= $row[csf("tenor")];
    		$payterm_id= $row[csf("payterm_id")];
    		$inco_term= $row[csf("inco_term_id")];
    		$pi_issue_to= $lib_company_arr[$row[csf("pi_issue_to")]];
    		$source_id= $row[csf("source")];
    		$port_of_loading= $row[csf("port_of_loading")];
        }



        if($requisition_no!="" && $wo_basis_id==1)
        {
            $req_location_data=array();
            $sql_requisition=sql_select("select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)");
            foreach($sql_requisition as $row)
            {
                $req_location_data[$row[csf("id")]]=$row[csf("location_id")];
            }
        }
        //echo "<pre>";print_r($req_location_data);

        $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");

        foreach($sql_supplier as $supplier_data)
        {//contact_no
            $row_mst[csf('supplier_id')];

            if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
            if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')];else $address_2='';
            if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')];else $address_3='';
            if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')];else $address_4='';
            if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
    		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
            if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
            if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
            //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
            $country = $supplier_data['country_id'];

            $supplier_address = $address_1;
    		$supplier_address2 = $address_2;
            $supplier_country =$country;
            $supplier_phone =$contact_no;
            $supplier_email = $email;
    		$supplier_contact_person = $contact_person;
        }
        //$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        $i = 0;
        $total_ammount = 0;
        $varcode_booking_no=$work_order_no;
        $com_dtls = fnc_company_location_address($company, $location, 2);
        ?>
        <style>
            .font{
              font-size: 24px;
            }
        </style>
        <table cellspacing="0" width="1000" >
            <tr>
                <td rowspan="3" width="70"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
                <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>
                <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
            </tr>
           <tr>
                <td colspan="2" align="center"><strong>
                <?
                echo $com_dtls[1]; //if($city!="") echo $city;
                ?></strong></td>
            </tr>
            <tr>
            <td colspan="2" align="center" class="font"><strong>
                <?
                echo " Purchase Order"; //if($city!="") echo $city;
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
                <td width="300" align="left"><strong>To</strong>,&nbsp;&nbsp;<br>
    			<? echo $supplier_name_library[$supplier_id]; ?></td>
                <td width="150"><strong>PO Number :</strong></td>
                <td width="150" align="left"> <?  echo $work_order_no; ?>
                </td>
                <td width="150" align="left" ><strong>Incoterm:</strong></td>
                <td width="150" align="left"><? echo $incoterm[$inco_term];?></td>
            </tr>
            <tr>
                <td rowspan="6"><?
    				$supplier_address=explode("%",$supplier_address);
    				foreach($supplier_address as $rowadress)
    				{
    					echo $rowadress."<br>";
    				}
    			echo $contact_no;
    			 ?></td>
                <td ><strong>Po Date:</strong></td>
                <td><? echo change_date_format($work_order_date); ?></td>
                <td align="left"><strong>Delivery Date:</strong></td>
                <td align="left" ><? echo change_date_format($delivery_date); ?></td>
            </tr>
            <tr>
                <td><strong>Pay Mode:</strong></td>
                <td align="left"><? echo $pay_mode[$pay_mode_id]; ?></td>
                <td align="left"><strong>Port of Loading:</strong></td>
                <td align="left" ><? echo $port_of_loading; ?></td>
            </tr>
             <tr>
                <td><strong>Currency:</strong></td>
                <td align="left" ><? echo $currency[$currency_id]; ?></td>
                <td align="left" ><strong>Port of Discharge:</strong></td>
                <td align="left" ><? echo $delivery_place; ?></td>

            </tr>

            <tr>
                <td><strong>Pay Term:</strong></td>
                <td align="left" ><? echo $pay_term[$payterm_id]; ?></td>
                <td align="left" ><strong>Tenor :</strong></td>
                <td align="left" ><? echo $tenor;?></td>

            </tr>
            <tr>
                <td><strong>Source:</strong></td>
                <td align="left" ><? echo $source[$source_id]; ?></td>
                <td align="left" ><strong>PO Basis:</strong></td>
                <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>

            </tr>
            <tr>
                <td align="left" colspan="1">&nbsp; </td>
                <td align="left" colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td rowspan="6"><? echo $supplier_contact_person; echo "<br>";
    			$supplier_address22=explode("%",$supplier_address2);
    			foreach($supplier_address22 as $rowadress2)
    			{
    				echo $rowadress2."<br>";
    			} ?></td>
                <td ><strong>Pi Issue to :</strong></td>
                <td><?  echo $pi_issue_to; ?></td>
                <td align="left">&nbsp; </td>
                <td align="left">&nbsp; </td>
            </tr>
            <tr
             	<td><strong><?  if($bin_no) echo 'BIN'; else echo '';?> :</strong></td>
                <td><?  if($bin_no) echo $bin_no;else echo '';; ?></td>
                <td align="right" colspan="3" >&nbsp;</td>
            </tr>
        </table>
        <table cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="150" align="center">Requisition No</th>
                <th width="100" align="center">Item Group</th>
                <th width="60" >Brand</th>
                <th width="60" >Origin</th>
                <th width="180" align="center">Item Description</th>
                <th width="80" align="center">Item Size</th>
                <th width="70" align="center">UOM</th>
                <th width="70" align="center">Nature</th>
                <th width="90" align="center">Profit Center</th>
                <th width="90" align="center">Req. Qty</th>
                <th width="70" align="center">PO Qty</th>
                <th width="70" align="center">Rate</th>
                <th width="90" align="center">PO Amount</th>
                <th align="center">Remarks</th>
            </thead>
            <?
            $cond="";
            if($data[1]!="") $cond .= " and a.id='$data[1]'";
            $i=1;

            //$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
            
            $profit_center=return_library_array("select id, profit_center_name from  lib_profit_center",'id','profit_center_name');

            $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code, d.brand_name,d.origin, b.nature, b.PROFIT_CENTER
            from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d
            where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");

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

                $amount=$row[csf('amount')];
                $amount_sum += $amount;
                ?>
                <tr>
                    <td><? echo $i; ?></td>
                    <td>
                    <?
                    $requesition_no="";
                    $requisition_arr=array_unique(explode(",",$row[csf('requisition_no')]));
                    $req_location="";
                    foreach($requisition_arr as $req_id)
                    {
                        if($requesition_no=="")  $requesition_no=$requisition_library[$req_id]; else $requesition_no=$requesition_no.",".$requisition_library[$req_id];
                        if($req_location=="")  $req_location=$location_arr[$req_location_data[$req_id]]; else $req_location=$req_location.",".$location_arr[$req_location_data[$req_id]];
                    }
                    echo  $requesition_no;
                    ?></td>
                    <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td ><p><? echo $row[csf("brand_name")];?></p></td>
				    <td ><p><? echo $lib_country_arr[$row[csf("origin")]];?></p></td>
                    <td><? echo $row[csf('item_description')];?></td>
                    <td><? echo $row[csf('item_size')];?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]];?></td>
                    <td align="center"><? echo $nature_mode[$row[csf('nature')]];?></td>
                    <td align="center"><? echo $profit_center[$row[csf('PROFIT_CENTER')]];?></td>
                    <td align="right"><? echo number_format($row[csf('req_quantity')],2); ?></td>
                    <td align="right"><? echo  number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2);$carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                    <td align="right"><? echo $row[csf('remarks')]; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <th align="right" colspan="10" >Total :</th>
                <th align="right"><? echo number_format($req_quantity_sum,0) ?></th>
                <th align="right"><? echo number_format($supplier_order_quantityl_sum,0) ?></th>
                <th align="right" colspan="2"><? echo $word_amount=number_format($amount_sum,2);  ?></th>
            </tr>

            <tr>
                <td colspan="12">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                <td align="right" >Upcharge :&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="12">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
                <td align="right">Discount :&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr>
                <td align="right" colspan="13"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
                 <td align="right">&nbsp;</td>
            </tr>
        </table>
       <table width="1100">
            <tr>
                <td colspan="10"> Amount in words:&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
        </table>
        <br>
        <?
        	echo get_spacial_instruction($work_order_no,"1100px",147);
            echo signature_table(152, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
        ?>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>

          <?

        exit();
    }
	if($data[4] == 4)
    {
        /*$sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
        $com_name=$sql_company[0][csf("company_name")];
        $company_short_name=$sql_company[0][csf("company_short_name")];
        $plot_no=$sql_company[0][csf("plot_no")];
        $level_no=$sql_company[0][csf("level_no")];
        $road_no=$sql_company[0][csf("road_no")];
        $block_no=$sql_company[0][csf("block_no")];
        $city=$sql_company[0][csf("city")];
        $zip_code=$sql_company[0][csf("zip_code")];*/


        $location_arr=return_library_array( "select id,location_name from lib_location","id", "location_name"  );
        $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
        $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
        $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
        $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
        $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');

        $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,discount_remarks, insert_date, is_approved,inserted_by FROM  wo_non_order_info_mst WHERE id = $data[1]");

        foreach($sql_data as $row)
        {
            $work_order_no=$row[csf("wo_number")];
            $work_order_id=$row[csf("id")];
            $item_category_id=$row[csf("item_category")];
            $supplier_id=$row[csf("supplier_id")];
            $work_order_date=$row[csf("wo_date")];
            $currency_id=$row[csf("currency_id")];
            $wo_basis_id=$row[csf("wo_basis_id")];
            $pay_mode_id=$row[csf("pay_mode")];
            $source=$row[csf("source")];
            $delivery_date=$row[csf("delivery_date")];
            $attention=$row[csf("attention")];
            $requisition_no=$row[csf("requisition_no")];
            $delivery_place=$row[csf("delivery_place")];
            $wo_item_category=$row[csf("item_category")];
            $wo_amount=$row[csf("wo_amount")];
            $up_charge=$row[csf("up_charge")];
            $discount=$row[csf("discount")];
            $net_wo_amount=$row[csf("net_wo_amount")];
            $upcharge_remarks=$row[csf("upcharge_remarks")];
            $discount_remarks=$row[csf("discount_remarks")];
            $insert_date = $row[csf("insert_date")];
    		$inserted_by= $row[csf("inserted_by")];
            if($row[csf('is_approved')]==3){
                $is_approved=1;
            }else{
                $is_approved=$row[csf('is_approved')];
            }
        }

        $approved_msg = '';
        if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
        }
        $approval_status=sql_select($approval_status);

        if($approval_status[0][csf('approval_need')] == 1)
        {
            if($is_approved==1){
                echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
                $approved_msg = "Approved";
            }
            else{
                echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
                $approved_msg = "Draft";
            }
        }


        if($requisition_no!="" && $wo_basis_id==1)
        {
            $req_location_data=array();
            // echo "select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)";
            $sql_requisition=sql_select("select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)");
            foreach($sql_requisition as $row)
            {
                $req_location_data[$row[csf("id")]]=$row[csf("location_id")];
            }
        }
        //echo "<pre>";print_r($req_location_data);

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
        $com_dtls = fnc_company_location_address($company, $location, 2);
        ?>
        <table cellspacing="0" width="1000" >
            <tr>
                <td rowspan="3" width="70"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
                <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>
                <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
            </tr>
            <tr>
                <td colspan="2" align="center"><strong>
                <? echo $com_dtls[1]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><strong><? if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
            </tr>
        </table>
        <table cellspacing="0" width="1000" >
            <tr>
                <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
                <td width="150"><strong>WO Number:</strong></td>
                <td width="150" align="left">
                <?
                echo $work_order_no;
                ?>
                </td>
                <td width="150" align="left" ><strong>Date :</strong></td>
                <td width="150" align="left"><? echo $work_order_date; ?></td>
            </tr>
            <tr>
                <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
                <td ><strong>Delivery Date :</strong></td>
                <td><? echo change_date_format($delivery_date); ?></td>
                <td align="left"><strong>Place of Delivery:</strong></td>
                <td align="left" ><? echo $delivery_place; ?></td>
            </tr>
            <tr>
                <td><strong>Currency:</strong></td>
                <td align="left"><? echo $currency[$currency_id]; ?></td>
                <td align="left"><strong>Item Category:</strong></td>
                <td align="left" ><? echo $item_category[$item_category_id]; ?></td>
            </tr>
             <tr>
                <td><strong>Pay Mode:</strong></td>
                <td align="left" ><? echo $pay_mode[$pay_mode_id]; ?></td>
                <td align="left" ><strong>WO Basis:</strong></td>
                <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>

            </tr>
            <tr>
                <td align="left" colspan="1"><strong>Location</strong></td>
                <td align="left" colspan="3"><? echo $location_arr[$data[3]];?></td>
            </tr>
            <tr>
                <td><strong>BIN :</strong></td>
                <td><?  echo $bin_no; ?></td>
                <td align="right" colspan="3" >&nbsp;</td>
            </tr>
        </table>
        <table cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="110" align="center">Requisition No</th>
                <th width="100" align="center">Location</th>
                <th width="80" align="center">Code</th>
                <th width="80" align="center">Item Number</th>
                <th width="80" align="center">Item Category</th>
                <th width="180" align="center">Item Name & Description</th>
                <th width="70" align="center">Item Size</th>
                <th width="100" align="center">Remarks</th>
                <th width="50" align="center">Order UOM</th>
                <th width="70" align="center">Req.Qty</th>
                <th width="70" align="center">WO.Qty</th>
                <th width="60" align="center">Rate</th>
                <th align="center">Amount</th>
            </thead>
            <?
            $cond="";
            if($data[1]!="") $cond .= " and a.id='$data[1]'";
            $i=1;


            $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code, d.item_number,e.short_name as category_name
            from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d,lib_item_category_list e
            where a.id=b.mst_id and b.item_id=d.id and d.item_category_id=e.category_id and b.is_deleted=0 and b.status_active=1 $cond");

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

                $amount=$row[csf('amount')];
                $amount_sum += $amount;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td>
                    <?
                    $requesition_no="";
                    $requisition_arr=array_unique(explode(",",$row[csf('requisition_no')]));
                    $req_location="";
                    foreach($requisition_arr as $req_id)
                    {
                        if($requesition_no=="")  $requesition_no=$requisition_library[$req_id]; else $requesition_no=$requesition_no.",".$requisition_library[$req_id];
                        
                        if($req_location=="")  $req_location=$location_arr[$req_location_data[$req_id]]; else $req_location=$req_location.",".$location_arr[$req_location_data[$req_id]];
                    }
                    echo  $requesition_no;
                    ?></td>
                    <td><? echo $req_location; ?></td>
                    <td><? echo $row[csf('item_code')]; ?></td>
                    <td><? echo $row[csf('item_number')]; ?></td>
                    <td><? echo $row[csf('category_name')]; ?></td>
                    <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                    <td><? echo $row[csf('item_size')]; ?></td>
                    <td><? echo $row[csf('remarks')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('req_quantity')],2); ?></td>
                    <td align="<? echo $align_cond;?>"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <th align="right" colspan="10" >Total :</th>
                <th align="right"><? echo number_format($req_quantity_sum,0) ?></th>
                <th align="<? echo $align_cond;?>"><? echo number_format($supplier_order_quantityl_sum,0) ?></th>
                <th align="right" colspan="3"><? echo $word_amount=number_format($amount_sum,2);  ?></th>
            </tr>

            <tr>
                <td colspan="12">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                <td align="right" >Upcharge :&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
            </tr>
            <tr>
                <td colspan="12">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
                <td align="right">Discount :&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
            </tr>
            <tr>
                <td align="right" colspan="13"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
            </tr>



        </table>
       <table width="1100">
            <tr>
                <td colspan="12"> Amount in words:&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
        </table>
        <br>

        <?
        echo get_spacial_instruction($work_order_no,"1100px",147);


    	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id =$work_order_id  group by mst_id, approved_by order by  approved_by");
        $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=17 AND  mst_id =$work_order_id  order by  approved_no,approved_date");
        $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
        $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
    	?>
    	 <? if(count($approved_sql)>0)
            {
            $sl=1;
            ?>
            <div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                    <label><b>Others Purchase Order Approval Status </b></label>
                    <thead>
    	                <tr style="font-weight:bold">
    	                    <th width="20">SL</th>
    	                    <th width="250">Name</th>
    	                    <th width="200">Designation</th>
    	                    <th width="100">Approval Date</th>
    	                </tr>
                	</thead>
                    <? foreach ($approved_sql as $key => $value)
                    {
                        ?>
                        <tr>
                            <td width="20"><? echo $sl; ?></td>
                            <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                            <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                            <td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

    						echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                        </tr>
                        <?
                        $sl++;
                    }
                    ?>
                </table>
            </div>
            <?
        }
        ?>
        <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
        <? if(count($approved_his_sql) > 0)
        {
            $sl=1;
            ?>
            <div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                    <label><b>Others Purchase Order Approval / Un-Approval History </b></label>
                    <thead>
    	                <tr style="font-weight:bold">
    	                    <th width="20">SL</th>
    	                    <th width="150">Approved / Un-Approved</th>
    	                    <th width="150">Designation</th>
    	                    <th width="50">Approval Status</th>
    	                    <th width="150">Reason for Un-Approval</th>
    	                    <th width="150">Date</th>
    	                </tr>
                	</thead>
                    <? foreach ($approved_his_sql as $key => $value)
                    {
                    	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                            <td width="20"><? echo $sl; ?></td>
                            <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                            <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                            <td width="50">Yes</td>
                            <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                            <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

    						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                        </tr>
                            <?
                            $sl++;
                            $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                            $un_approved_date=$un_approved_date[0];
                            if($db_type==0) //Mysql
                            {
                                if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                            }
                            else
                            {
                                if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                            }

                            if($un_approved_date!="")
                            {
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                            <td width="20"><? echo $sl; ?></td>
                            <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                            <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                            <td width="50">No</td>
                            <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                            <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

    						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                        </tr>

    						<?
    						$sl++;

    					}

                    }
                    ?>
                </table>
            </div>
            <?
    		$last_approved_by=$value[csf("approved_by")];
        }
    		echo "<br><b>Approved By: </b>".$user_lib_name[$last_approved_by];
        ?>
         <br/>
        <?
            echo signature_table(152, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
        ?>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>

          <?

        exit();
    } //print1 end
    if($data[4] == 5)
	{
    //echo $data[0]; die;
    // $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, country_id, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    // $com_name=$sql_company[0][csf("company_name")];
    // $company_short_name=$sql_company[0][csf("company_short_name")];
    // $plot_no=$sql_company[0][csf("plot_no")];
    // $level_no=$sql_company[0][csf("level_no")];
    // $road_no=$sql_company[0][csf("road_no")];
    // $block_no=$sql_company[0][csf("block_no")];
    // $city=$sql_company[0][csf("city")];
    // $country_n=$sql_company[0][csf("country_id")];
    // $zip_code=$sql_company[0][csf("zip_code")];

    $location_arr=return_library_array( "select id,location_name from lib_location","id", "location_name"  );
    $country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $brand_arr=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');

    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,discount_remarks,insert_date, is_approved,inserted_by FROM  wo_non_order_info_mst WHERE id = $data[1]");

    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by= $row[csf("inserted_by")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }


    }
    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
    }

    if($requisition_no!="" && $wo_basis_id==1)
    {
        $req_location_data=array();
        $sql_requisition=sql_select("select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)");
        foreach($sql_requisition as $row)
        {
            $req_location_data[$row[csf("id")]]=$row[csf("location_id")];
        }
    }
    //echo "<pre>";print_r($req_location_data);

    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,contact_person,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
    //print_r($sql_supplier);

   foreach($sql_supplier as $supplier_data)
    {//contact_no
        $row_mst[csf('supplier_id')];

        if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
        if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
        if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
        if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';

        if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].'.'.' ';else $contact_no='';

        if($supplier_data[csf('contact_person')]!='')$attention_name = $supplier_data[csf('contact_person')].'.'.' ';else $attention_name='';
        if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].'.'.' ';else $web_site='';
        if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].'.'.' ';else $email='';
        if($supplier_data[csf('country_id')]!=0)$supp_country = $country_arr[$supplier_data[csf('country_id')]].'.'.' ';else $supp_country='';
        $supplier_address = $address_1;
        $contact_person =$attention_name;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
    }
    //$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );

    //$sql_mst = sql_select("select id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks from  com_pi_master_details where id= $pi_mst_id");
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $i = 0;
    $total_ammount = 0;
    $varcode_booking_no=$work_order_no;
    $com_dtls = fnc_company_location_address($company, $location, 2);
    ?>
    <table align="center" cellspacing="0" width="1150" >
        <tr>
            <td rowspan="3" width="70"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>

        </tr>
        <tr>
            <td colspan="2" align="center"><strong>
            <?
                echo $com_dtls[1];
            //if($city!="") echo $city;

           /*if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
            if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code.", ";
           if($country_arr[$country_n]!="") echo $country_arr[$country_n].". ";*/
            //echo $country_arr[$result[csf('country_id')]];
            ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" style="font-size:22px;" align="center"><strong><? echo 'Purchase Order'; ?></strong></td>
        </tr>

        <tr>
            <td colspan="2" align="center"><strong><? if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
        </tr>
    </table>
    <table align="center" cellspacing="0" width="1150" style="border: 1px solid black; margin-bottom: 5px" >
        <tr>
            <td width="150" align="left"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
            <td></td>
            <td></td>
            <td width="150">PO Number</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td width="100" align="left">Address</td>
            <td align="center" width="50">:</td>
            <td><? echo $supplier_address." ".$supp_country; ?></td>

            <td width="150">PO Date</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo change_date_format($work_order_date); ?></strong></td>
        </tr>
        <tr>
            <td width="100" align="left">Attention</td>
            <td align="center" width="50">:</td>
            <td><strong><? echo $attention; ?></strong></td>
            <td width="150">Delivery Date</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo change_date_format($delivery_date); ?></strong></td>
        </tr>
        <tr>
            <td width="100" align="left">Contact</td>
            <td align="center" width="50">:</td>
            <td><strong><? echo $supplier_phone; ?></strong></td>
            <td width="150">Currency</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $currency[$currency_id]; ?><strong></td>
        </tr>
        <tr>
            <td width="100" align="left">Email</td>
            <td align="center" width="50">:</td>
            <td><? echo $supplier_email; ?></td>
            <td width="150">Pay Mode</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $pay_mode[$pay_mode_id]; ?></strong></td>
        </tr>
        <tr>
            <td width="100" align="left">BIN</td>
            <td align="center" width="50">:</td>
            <td><? echo $bin_no; ?></td>
            <td width="150">PO Basis</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $wo_basis[$wo_basis_id]; ?></strong></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td width="150">Business Unit</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $location_arr[$data[3]];?><strong></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td width="150">Place of Delivery</td>
            <td align="center" width="50">:</td>
            <td width="150" align="left"><strong><? echo $delivery_place; ?><strong></td>
        </tr>

    </table>
    <table align="center" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th colspan="21" align="center" ><strong>Item Details</strong></th>
            </tr>
            <th width="50">SL</th>
            <th width="150" align="center">Item Category</th>
            <th width="180" align="center">Item Description</th>
            <th width="120" align="center">Item Size</th>
            <th width="70" align="center">Brand</th>
            <th width="50" align="center">UOM</th>
            <th width="70" align="center">QTY</th>
            <th width="80" align="center">Rate</th>
            <th width="95" align="center">Amount</th>
            <th width="100" align="center">Remarks</th>
        </thead>
        <?
        //$reg_no=explode(',',$data[11]);
        $cond="";
        if($data[1]!="") $cond .= " and a.id='$data[1]'";
        //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
        $i=1;
        //echo "select a.id,a.wo_number,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.amount,b.rate,d.item_description,d.item_size,d.item_group_id,d.item_account
        //from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
        //where a.id=b.mst_id and b.item_id=d.id $cond";

        $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code,d.brand,d.item_category_id
        from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d
        where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");
        //print_r($sql_result);die;

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

            $amount=$row[csf('amount')];
            $amount_sum += $amount;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                <td align="center"><? echo $row[csf('item_size')]; ?></td>
                <td align="center"><? echo $brand_arr[$row[csf('brand')]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td align="center"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>

                <td><? echo $row[csf('remarks')]; ?></td>

            </tr>
            <?
            $i++;
        }
        ?>

        <tr>
            <td colspan="7"> <strong>In word:</strong>&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            <td align="right"><strong>Total : </strong>&nbsp;</td>
            <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
        </tr>



    </table>

    <br>
    <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>

    <?

		$cbo_template_id=$data[5];
        echo get_spacial_instruction($work_order_no,"1150px",147);
        echo signature_table(152, $data[0], "1150px",$cbo_template_id,70,$user_lib_name[$inserted_by]);

      ?>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>

      <?

    exit();
    }
}


if ($action=="spare_parts_work_print_8")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company=$data[0];
    $location=$data[3];
    echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
    // print_r ($data); die;
   	$cbo_template_id=$data[5];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$location_arr=return_library_array( "select id,location_name from lib_location","id", "location_name"  );
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $lib_buyer=return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id","buyer_name");
     

    $name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$company and status_active=1 and module_id=5 and menu_id=118");
	

	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,discount_remarks, insert_date, is_approved,inserted_by FROM  wo_non_order_info_mst WHERE id = $data[1]");

	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$work_order_id=$row[csf("id")];
		$item_category_id=$row[csf("item_category")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source=$row[csf("source")];
		$delivery_date=$row[csf("delivery_date")];
		$attention=$row[csf("attention")];
		$requisition_no=$row[csf("requisition_no")];
		$delivery_place=$row[csf("delivery_place")];
		$wo_item_category=$row[csf("item_category")];
		$wo_amount=$row[csf("wo_amount")];
		$up_charge=$row[csf("up_charge")];
		$discount=$row[csf("discount")];
		$net_wo_amount=$row[csf("net_wo_amount")];
		$upcharge_remarks=$row[csf("upcharge_remarks")];
		$discount_remarks=$row[csf("discount_remarks")];
		$insert_date = $row[csf("insert_date")];
		$inserted_by= $row[csf("inserted_by")];
		if($row[csf('is_approved')]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf('is_approved')];
		}
	}

	$approved_msg = '';
	if($db_type==0)
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
	}
	$approval_status=sql_select($approval_status);

	if($approval_status[0][csf('approval_need')] == 1)
	{
		if($is_approved==1){
			echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
			$approved_msg = "Approved";
		}
		else{
			echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
			$approved_msg = "Draft";
		}
	}


	if($requisition_no!="" && $wo_basis_id==1)
	{
		$req_location_data=array();
		// echo "select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)";
		$sql_requisition=sql_select("select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)");
		foreach($sql_requisition as $row)
		{
			$req_location_data[$row[csf("id")]]=$row[csf("location_id")];
		}
	}
	//echo "<pre>";print_r($req_location_data);

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
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<table cellspacing="0" width="1000" >
		<tr>
			<td rowspan="3" width="70"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
			<td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>
			 <td width="200"><b><?="ISO Number  :".$name_iso_Array[0]["ISO_NO"]?></b></td>
			<td rowspan="3" colspan="" width="250" id="barcode_img_id"> </td>
		</tr>
		<tr>
			<td colspan="2" align="center"><strong>
			<? echo $com_dtls[1]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><strong><? if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
		</tr>
	</table>
	<table cellspacing="0" width="1000" >
		<tr>
			<td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
			<td width="150"><strong>WO Number:</strong></td>
			<td width="150" align="left">
			<?
			echo $work_order_no;
			?>
			</td>
			<td width="150" align="left" ><strong>Date :</strong></td>
			<td width="150" align="left"><? echo $work_order_date; ?></td>
		</tr>
		<tr>
			<td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
			<td ><strong>Delivery Date :</strong></td>
			<td><? echo change_date_format($delivery_date); ?></td>
			<td align="left"><strong>Place of Delivery:</strong></td>
			<td align="left" ><? echo $delivery_place; ?></td>
		</tr>
		<tr>
			<td><strong>Currency:</strong></td>
			<td align="left"><? echo $currency[$currency_id]; ?></td>
			<td align="left"><strong>Item Category:</strong></td>
			<td align="left" ><? echo $item_category[$item_category_id]; ?></td>
		</tr>
		 <tr>
			<td><strong>Pay Mode:</strong></td>
			<td align="left" ><? echo $pay_mode[$pay_mode_id]; ?></td>
			<td align="left" ><strong>WO Basis:</strong></td>
			<td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>

		</tr>
		<tr>
			<td align="left" colspan="1"><strong>Location</strong></td>
			<td align="left" colspan="3"><? echo $location_arr[$data[3]];?></td>
		</tr>
		<tr>
			<td align="right" colspan="5" >&nbsp;</td>
		</tr>
	</table>
	<table cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<th width="50">SL</th>
			<th width="110" align="center">Requisition No</th>
			<th width="100" align="center">Location</th>
			<th width="80" align="center">Code</th>
			<th width="80" align="center">Item Number</th>
			<th width="80" align="center">Item Category</th>
			<th width="180" align="center">Item Name & Description</th>
			<th width="70" align="center">Item Size</th>
            <th width="150" align="center">Buyer</th>
            <th width="50" align="center">Season</th>
			<th width="100" align="center">Remarks</th>
			<th width="50" align="center">Cons UOM</th>
			<th width="70" align="center">Req.Qty</th>
			<th width="70" align="center">WO.Qty</th>
			<th width="60" align="center">Rate</th>
			<th align="center">Amount</th>
		</thead>
		<?
		$cond="";
		if($data[1]!="") $cond .= " and a.id='$data[1]'";
		$i=1;


		$sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code, d.item_number,e.short_name as category_name,b.buyer_id,b.season_id
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d,lib_item_category_list e
		where a.id=b.mst_id and b.item_id=d.id and d.item_category_id=e.category_id and b.is_deleted=0 and b.status_active=1 $cond");         
         

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

			$amount=$row[csf('amount')];
			$amount_sum += $amount;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td>
				<?
				$requesition_no="";
				$requisition_arr=array_unique(explode(",",$row[csf('requisition_no')]));
				$req_location="";
				foreach($requisition_arr as $req_id)
				{

					if($requesition_no=="")  $requesition_no=$requisition_library[$req_id]; else $requesition_no=$requesition_no.",".$requisition_library[$req_id];
					
					if($req_location=="")  $req_location=$location_arr[$req_location_data[$req_id]]; else $req_location=$req_location.",".$location_arr[$req_location_data[$req_id]];
				}
				echo  $requesition_no;
				?></td>
				<td><? echo $req_location; ?></td>
				<td><? echo $row[csf('item_code')]; ?></td>
				<td><? echo $row[csf('item_number')]; ?></td>
				<td><? echo $row[csf('category_name')]; ?></td>
				<td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
				<td><? echo $row[csf('item_size')]; ?></td>
                <td><? echo $lib_buyer[$row[csf('buyer_id')]]; ?></td>
                <td style='text-align:center'><? 
                    $buyer_id=$row[csf('buyer_id')];
                    $lib_season=return_library_array("select id, season_name from lib_buyer_season where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by season_name ASC","id","season_name");
                    // echo "<pre>";
                    // print_r($lib_season);
                    echo $lib_season[$row[csf('season_id')]]; 
                ?></td>
				<td><? echo $row[csf('remarks')]; ?></td>
				<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
				<td align="right"><? echo $row[csf('req_quantity')]; ?></td>
				<td align="<? echo $align_cond;?>"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
				<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
				<td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr>
			<th align="right" colspan="12" >Total :</th>
			<th align="right"><? echo number_format($req_quantity_sum,0) ?></th>
			<th align="<? echo $align_cond;?>"><? echo number_format($supplier_order_quantityl_sum,0) ?></th>
			<th align="right" colspan="3"><? echo $word_amount=number_format($amount_sum,2);  ?></th>
		</tr>

		<tr>
			<td colspan="14">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
			<td align="right" >Upcharge :&nbsp;</td>
			<td align="right"><? echo number_format($up_charge,2);  ?></td>
		</tr>
		<tr>
			<td colspan="14">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
			<td align="right">Discount :&nbsp;</td>
			<td align="right"><? echo number_format($discount,2);  ?></td>
		</tr>
		<tr>
			<td align="right" colspan="15"><strong>Net Total : </strong>&nbsp;</td>
			<td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
		</tr>



	</table>
   <table width="1100">
		<tr>
			<td colspan="12"> Amount in words:&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
		</tr>
	</table>
	<br>

	<?
	echo get_spacial_instruction($work_order_no,"1100px",147);


	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id =$work_order_id  group by mst_id, approved_by order by  approved_by");
	$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=17 AND  mst_id =$work_order_id  order by  approved_no,approved_date");
	$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
	?>
	 <? if(count($approved_sql)>0)
		{
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Others Purchase Order Approval Status </b></label>
				<thead>
					<tr style="font-weight:bold">
						<th width="20">SL</th>
						<th width="250">Name</th>
						<th width="200">Designation</th>
						<th width="100">Approval Date</th>
					</tr>
				</thead>
				<? foreach ($approved_sql as $key => $value)
				{
					?>
					<tr>
						<td width="20"><? echo $sl; ?></td>
						<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
						<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
						<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
					</tr>
					<?
					$sl++;
				}
				?>
			</table>
		</div>
		<?
	}
	?>
	<div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
	<? if(count($approved_his_sql) > 0)
	{
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Others Purchase Order Approval / Un-Approval History </b></label>
				<thead>
					<tr style="font-weight:bold">
						<th width="20">SL</th>
						<th width="150">Approved / Un-Approved</th>
						<th width="150">Designation</th>
						<th width="50">Approval Status</th>
						<th width="150">Reason for Un-Approval</th>
						<th width="150">Date</th>
					</tr>
				</thead>
				<? foreach ($approved_his_sql as $key => $value)
				{
					if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
						<td width="20"><? echo $sl; ?></td>
						<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
						<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
						<td width="50">Yes</td>
						<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
						<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
					</tr>
						<?
						$sl++;
						$un_approved_date= explode(" ",$value[csf('un_approved_date')]);
						$un_approved_date=$un_approved_date[0];
						if($db_type==0) //Mysql
						{
							if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
						}
						else
						{
							if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
						}

						if($un_approved_date!="")
						{
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
						<td width="20"><? echo $sl; ?></td>
						<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
						<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
						<td width="50">No</td>
						<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
						<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
					</tr>

						<?
						$sl++;

					}

				}
				?>
			</table>
		</div>
		<?
		$last_approved_by=$value[csf("approved_by")];
	}
		echo "<br><b>Approved By: </b>".$user_lib_name[$last_approved_by];
	?>
	 <br/>
	<?
		echo signature_table(152, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>

	  <?

	exit();
}



if ($action=="spare_parts_work_order_print2")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company=$data[0];
    $location=$data[4];
    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
    //print_r ($data); die;
	$cbo_template_id=$data[5];
    // $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    // $com_name=$sql_company[0][csf("company_name")];
    // $company_short_name=$sql_company[0][csf("company_short_name")];
    // $plot_no=$sql_company[0][csf("plot_no")];
    // $level_no=$sql_company[0][csf("level_no")];
    // $road_no=$sql_company[0][csf("road_no")];
    // $block_no=$sql_company[0][csf("block_no")];
    // $city=$sql_company[0][csf("city")];
    // $zip_code=$sql_company[0][csf("zip_code")];

    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $company_library=return_library_array( "select id,company_name from lib_company","id", "company_name"  );
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$data[0]",'bin_no');

    $sql="select id from electronic_approval_setup where company_id=$company and page_id in(628,2991) and is_deleted=0";
    $res_result_arr = sql_select($sql);
    $approval_arr=array();
    foreach($res_result_arr as $row){
        $approval_arr[$row["ID"]]["ID"]=$row["ID"];
    }

	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, insert_date, is_approved,inserted_by  FROM  wo_non_order_info_mst WHERE id = $data[1]");

    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by = $row[csf("inserted_by")];
        $is_approved = $row[csf("is_approved")];

        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }

    }
    ob_start();
    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
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
    //$quot_ref=return_field_value("requ_no as our_ref","inv_purchase_requisition_mst","id=$data[2]","our_ref" );
    if($db_type==2)
    {
        $quot_ref=return_field_value("LISTAGG(requ_prefix_num , ',') WITHIN GROUP (ORDER BY requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        //$quot_ref=return_field_value(" rtrim(xmlagg(xmlelement(e,requ_prefix_num,',').extract('//text()') order by requ_prefix_num).GetClobVal(),',') AS our_ref ","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        //$quot_ref = $quot_ref->load();

        //$quot_result=sql_select("select our_ref from inv_purchase_requisition_mst where id in($data[2])");
    //        foreach($quot_result as $row)
    //        {                
    //            $quot_ref .= $row[csf('our_ref')].",";
    //        }
    //        $quot_ref=implode(",",array_unique(explode(",",chop($quot_ref,","))));
    }
    else
    {
        $quot_ref=return_field_value("group_concat(requ_prefix_num ) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
    }
    $req_no="";
    $req_no_id=array_unique(explode(",",$quot_ref));
    foreach($req_no_id as $reg_id)
    {
    if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;

    }

    if($db_type==0)
    {
        $quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
    }
    else
    {
        //$quot_factor_val=return_field_value("LISTAGG(cast(c.value as  varchar2(4000)) , ',') WITHIN GROUP (ORDER BY c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
        /*$quot_factor_val=return_field_value("rtrim(xmlagg(xmlelement(e,c.value,',').extract('//text()') order by c.value).GetClobVal(),',') AS as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );

        $quot_factor_val = $quot_factor_val->load();*/

        $quot_result=sql_select("select value from inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c where a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'");
        foreach($quot_result as $row)
        {                
            $quot_factor_val .= $row[csf('value')].",";
        }
        $quot_factor_val=implode(",",array_unique(explode(",",chop($quot_factor_val,","))));
    }
    $quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id='$data[3]'","system_id" );
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        $i = 0;
        $total_ammount = 0;
        $com_dtls = fnc_company_location_address($company, $location, 2);
        ?>
        <div align="center" style="width:780px;">
        <table align="center" cellspacing="0" width="780" >
            <tr>
                <td width="70"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
                <td colspan="8" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="8" align="center"><strong>
                <?
                    echo $com_dtls[1];
                // if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
                // if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code.", ";
                ?></strong></td>
            </tr>
            <tr>
                <td colspan="8" align="center"><strong><? //if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
            </tr>
        </table>
        <table align="center" cellspacing="0" width="780" >
            <tr>
                <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
                <td width="150"></td>
                <td width="150" align="left">

                </td>
                <td width="150" align="left" ><strong>Date </strong></td>
                <td width="150" align="left">:&nbsp;&nbsp;<?
                echo change_date_format($work_order_date);
                ?></td>
            </tr>
            <tr>
                <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
                <td ></td>
                <td></td>
                <td align="left"><strong>WO Number </strong></td>
                <td align="left" >:&nbsp;&nbsp;<? echo $work_order_no; ?></td>
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
                <td align="left" ><strong>Our Ref</strong></td>
                <td align="left" >:&nbsp;&nbsp;<? echo $req_no; ?></td>
            </tr>
            <tr>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"><strong>Quotation ID</strong></td>
                <td align="left">:&nbsp;&nbsp;<? echo $quot_sys_id; ?></td>

            </tr>
            <tr>
                <td align="left"></td><td align="left"></td>
                <td align="left"></td>
                <td align="left"><strong>Currency</strong></td>
                <td align="left">:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>

            </tr>

            <tr>
                <td align="left"></td><td align="left"></td>
                <td align="left"></td>
                <td align="left"><strong>Location</strong></td>
                <td align="left">:&nbsp;&nbsp;<? echo $location_arr[$data[4]];?></td>

            </tr>
            <tr>
                <td align="left"></td><td align="left"></td>
                <td align="left"></td>
                <td align="left"><strong>BIN</strong></td>
                <td align="left">:&nbsp;&nbsp;<? echo $bin_no;?></td>

            </tr>
            <tr>
                <td align="left"></td><td align="left"></td>
                <td align="left"></td>
                <td align="left"><strong>Approval Status</strong></td>
                <td align="left">:&nbsp;&nbsp;<b style="color:red"><? if($is_approved==1 || $is_approved==2){ echo "Approved";}else{
                    echo "Not Approved";}?></b></td>
            </tr>
            <tr>
        
                <td colspan="4" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="4px"><? echo $data[3];?></font></strong></td>
            </tr>
            <tr>
                <td colspan="8" align="left">Dear Concern,<br><strong><? echo $company_library[$data[0]]; ?></strong> is Pleased to inform You that Your price offer has been accepted with the following terms .
                </td>
            </tr>
            <tr>
                <td  colspan="8" >&nbsp;</td>
            </tr>
        </table>
        <table align="center" cellspacing="0" width="780"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="180" align="center">Item Description</th>
                <th width="70" align="center">Item Size</th>
                <th width="70" align="center">Specification</th>
                <th width="50" align="center">Order UOM</th>
                <th width="70" align="center">WO.Qty</th>
                <th width="80" align="center">Rate</th>
                <th width="95" align="center">Amount</th>
                <th width="80" align="center">Remarks</th>
            </thead>
            <?
            //$reg_no=explode(',',$data[11]);
            $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id='$data[3]'");
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

            $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code
            from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
            where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");

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
                $amount=$row[csf('amount')];
                $amount_sum += $amount;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                    <td><? echo $row[csf('item_size')]; ?></td>
                     <td><? echo $quot_factor_val; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                    <td><? echo $row[csf('remarks')]; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr >
                <td align="right" colspan="7" ><strong>Total :</strong></td>
                <td align="right"><? echo $word_amount=number_format($amount_sum,2);  ?></td>
            </tr>
            <tr>
                <td colspan="6">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                <td align="right" >Upcharge :&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
            </tr>
            <tr>
                <td align="right" colspan="7">Discount :&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
            </tr>
            <tr>
                <td align="right" colspan="7"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
            </tr>
        </table>


    <table width="780" align="center">
            <tr>
                <td align="left"> <strong>Amount in words:</strong>&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
            <tr>
                <td colspan="8">&nbsp; </td>
            </tr>
        </table>
        <br>
        <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
        <table width="780" align="center">
        <?  echo get_spacial_instruction($work_order_no,"780px",147);?>
        </table>
        <table width="780" align="center">
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
        <table width="780" align="center">
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
        <table align="center" style="width:780px;">
            <?
            echo signature_table(152, $data[0], "780px",$cbo_template_id,1,$user_lib_name[$inserted_by]);
            ?>
        </table>
        </div>
        <?


        $mailBody=ob_get_contents();
        ob_clean();
        echo $mailBody;
        $mail_data = $data[6];
        $cbo_company_id = $data[0];

        //Mail send------------------------------------------
        list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

        if($is_mail_send==1){
        // require_once('../../../mailer/class.phpmailer.php');
            include('../../../auto_mail/setting/mail_setting.php');

            $mailToArr=array();
            if($msil_address){$mailToArr[]=$msil_address;}

            //-----------------------------
            $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  and a.entry_form=17 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
            $elcetronicSqlRes=sql_select($elcetronicSql);
            foreach($elcetronicSqlRes as $rows){
                if($rows['SEQUENCE_NO']==1 && $rows['BYPASS']==2){
                    if($rows['USER_EMAIL']){$mailToArr[100]=$rows['USER_EMAIL'];}
                }
                $elecDataArr[$rows['BYPASS']][]=$rows['USER_EMAIL'];
            }
            
            if($elecDataArr[1][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[1][0];}
            elseif($elecDataArr[2][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[2][0];}

            $to=implode(',',$mailToArr);
            
            $subject="Others Purchase Order";
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br>".$mailBody, $from_mail,$att_file_arr );
        }
        exit();

}



if ($action=="spare_parts_work_order_print3")
{
    extract($_REQUEST);
    $data=explode('*',$data);
	$cbo_template_id=$data[7];

    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
   //print_r ($data); die;
    $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $com_name=$sql_company[0][csf("company_name")];
    $com_email=$sql_company[0][csf("email")];
    $com_website=$sql_company[0][csf("website")];

    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    //$lib_location_arr=return_library_array('SELECT id,location_name FROM lib_location','id','location_name' );
    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $contact_person_library= return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $company_library=return_library_array( "select id,company_name from lib_company","id", "company_name"  );
    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, location_id, discount_remarks, insert_date,inserted_by, is_approved, contact  FROM  wo_non_order_info_mst WHERE id = $data[1]");
    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        //echo $item_category_id;die;
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];
		$inserted_by= $row[csf("inserted_by")];

        //echo $wo_item_category;die;

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];
        $lib_location_arr=$row[csf("location_id")];
        $insert_date = $row[csf("insert_date")];
        $insert_date = $row[csf("insert_date")];
        $contact = $row[csf("contact")];
        $remarks = $row[csf("remarks")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
    }

    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
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
    //$quot_ref=return_field_value("requ_no as our_ref","inv_purchase_requisition_mst","id=$data[2]","our_ref" );
    if($db_type==2)
    {
        //$quot_ref=return_field_value("LISTAGG(requ_prefix_num , ',') WITHIN GROUP (ORDER BY requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        /*$quot_ref=return_field_value(" rtrim(xmlagg(xmlelement(e,requ_prefix_num,',').extract('//text()') order by requ_prefix_num).GetClobVal(),',') AS our_ref ","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        $quot_ref = $quot_ref->load();*/

        $quot_result=sql_select("select our_ref from inv_purchase_requisition_mst where id in($data[2])");
        foreach($quot_result as $row)
        {                
            $quot_ref .= $row[csf('value')].",";
        }
        $quot_ref=implode(",",array_unique(explode(",",chop($quot_ref,","))));

    }
    else
    {
    $quot_ref=return_field_value("group_concat(requ_prefix_num ) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
    }
    $req_no="";
    $req_no_id=array_unique(explode(",",$quot_ref));
    foreach($req_no_id as $reg_id)
    {
    if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;

    }

    if($db_type==0)
    {
        $quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
    }
    else
    {
        // $quot_factor_val=return_field_value("LISTAGG(cast(c.value as  varchar2(4000)) , ',') WITHIN GROUP (ORDER BY c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
        /*$quot_factor_val=return_field_value("rtrim(xmlagg(xmlelement(e,c.value,',').extract('//text()') order by c.value).GetClobVal(),',') AS as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );

        $quot_factor_val = $quot_factor_val->load();*/

    $quot_result=sql_select("select a.value from inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c where a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'");
        foreach($quot_result as $row)
        {                
            $quot_factor_val .= $row[csf('value')].",";
        }
        $quot_factor_val=implode(",",array_unique(explode(",",chop($quot_factor_val,","))));
    }
    $quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id='$data[3]'","system_id" );
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        $i = 0;
        $total_ammount = 0;
        ?>


    <div style="padding-left: 10px; width: 950px;">
        <table align="center" cellspacing="0" width="950" >
            <tr>
                <td width="200px"> <img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
                <td width="750px">
                    <table align="center" cellspacing="0" width="750">
                        <tr>
                            <td style="text-align: center;"><h1><? echo $com_name; ?></h1></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><h3>100% Export oriented knit Composite Industry</h2></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><h3>
                            <?
                            if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
                            if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code;
                            ?>
                            </h2></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <table align="center" cellspacing="0" width="950"  rules="" class="rpt_table" >
            <tr>
                <th align="right" style="font-size: 16px;"> <b>WO Number: </b>&nbsp;</th>
                <th align="left" style="font-size: 16px;"><b> <? echo $data[4]; ?></b></th>
                <th align="right"><b>WO Date:</b>&nbsp;</th>
                <th align="left"><b> <? echo $data[5]; ?></b></th>
                <th align="right"><b>Location:</b>&nbsp;</th>
                <th align="left"><b> <? echo $location_arr[$data[6]];?></b></th>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table">

                <tr align="center">

                <th width="180" align="center" style="font-size: 16px;"><b>SUPPLIER</b></th>
                <th width="180" align="center">DELIVERY ADDRESS</th>
                <th width="180" align="center"> BILLING ADDRESS</th>

                </tr>
                <tr>
                    <td style="font-size: 16px;"><b><?php echo $supplier_name_library[$supplier_id]; //"Supplier name";           ?></b></td>
                    <td><?php echo $delivery_place; //"Delivery name";           ?></td>
                    <td><?php echo $com_name; //"Billing name";           ?></td>
                </tr>

                <tr>
                    <td><?php echo "Cell:" . $supplier_phone; ?></td>
                    <td><?php //echo "Cell:"; ?></td>
                    <td><?php //echo "Cell:".$com_email; ?></td>
                </tr>

                <tr>
                    <td><?php echo "Email:" . $supplier_email; ?></td>
                    <td><?php //echo "Email:"; ?></td>
                    <td><?php echo "Email:" . $com_email; ?></td>
                </tr>

        <!-- </tbody> -->

        </table>
        <br>
        <table align="center" cellspacing="0" width="950" >
            <tr>
                <td>Attention- <?php echo $attention;?></td>
            </tr>
            <tr>
                <td>Contact To- <?php echo $contact;?></td>
            </tr>
        </table>


        <table align="center" cellspacing="0" width="950" >

            <tr>
                <td rowspan="4"><?  echo "Attn :".$contact_person_library[$supplier_id];
                echo "<br>"; echo "Subject : ". " Work order for the supply of ".$item_category[$wo_item_category];//.$item_name_arr[$wo_item_category];//.$supplier_name_library[$supplier_id];;
                //echo "<br>"; echo "Email :".$supplier_email;//contact_person_library ?>

                </td>
                <td ></td>
                <td></td>

            </tr>

        <tr>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">&nbsp;&nbsp;</td>

            </tr>
            <tr>
                <td align="left"></td><td align="left"></td>
                <td align="left"></td>
                <td align="left"></td>
                <td align="left">&nbsp;&nbsp;</td>

            </tr>
            <tr>
                <td  colspan="5" >&nbsp;</td>
            </tr>
            <!-- <tr>

                <td colspan="4" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="4px"><? echo $data[3];?></font></strong></td>
            </tr> -->
            <tr>
                <td colspan="8" align="left">Dear Sir,<br><strong><? echo $company_library[$data[0]]; ?></strong> Reference to your valid document the management has accepted your price for the following goods/materials as per under noted terms & conditions. Please supply the goods/material within the time limit as stated below.
                </td>
            </tr>
            <tr>
                <td  colspan="8" >&nbsp;</td>
            </tr>
        </table>
        <table>
        <tr>
            <td align="center" style="padding-left: 300px"><strong><font size="4px"><? echo "WORK ORDER";?></font></strong></td>
        </tr>
        </table>
        <table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
            <!-- <thead bgcolor="#dddddd" align="center"> --><tr align="center">
                <th width="50">SL</th>
                <th width="180" align="center">Requisition No.</th>
                <th width="180" align="center">Item Description</th>
                <th width="70" align="center">Item Size</th>
                <th width="50" align="center"> UOM</th>
                <th width="70" align="center">Wo.Qty</th>
                <th width="80" align="center">Unit Price</th>
                <th width="95" align="center">Currency</th>
                <th width="95" align="center">Amount</th>
                <th width="80" align="center">Remarks</th>
            <!-- </thead> --></tr>
            <?
            //$reg_no=explode(',',$data[11]);
            $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id='$data[3]'");
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

            $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code,c.requ_no
            from wo_non_order_info_mst a,wo_non_order_info_dtls b left join  inv_purchase_requisition_mst c on b.requisition_no=c.id,product_details_master d
            where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");

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
                $amount=$row[csf('amount')];
                $amount_sum += $amount;

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="center"><? echo $row[csf('requ_no')]; ?></td>
                    <td><? echo $row[csf('item_description')]; ?></td>
                    <td><? echo $row[csf('item_size')];  ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>

                    <td align="right"><? echo $currency[$currency_id]; ?></td>

                    <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                    <td><? echo $row[csf('remarks')]; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr >
                <td align="right" colspan="8" ><strong>Grand Total :</strong></td>
                <td align="right"><? echo $word_amount=number_format($amount_sum,2);  ?></td>
            </tr>
            <tr>
                <!-- <td align="right"></td> -->
                <td align="right" colspan="8"><span style="float:left;text-align: left;"><b>Upcharge Remarks: </b><? echo $upcharge_remarks;?></span> <span style="float:right;text-align: right;"> Upcharge :</span>&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
            </tr>
            <tr>
                <td colspan="8"><span style="text-align:left; float:left;"><b>Discount Remarks: </b><? echo $discount_remarks;?></span>  <span style=" float:right;text-align:right"> Discount : </span>&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
            </tr>
            <tr>
                <td align="right" colspan="8"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
            </tr>
        </table>
    <table width="780" align="center">
            <tr>
                <td > <strong>Amount in words:</strong>&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
            <tr>
                <td > <strong>Remarks:</strong>&nbsp;<? echo $remarks; ?> </td>
            </tr>
            <tr>
                <td colspan="8">&nbsp; </td>
            </tr>
        </table>
        <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
        <br>
        <?  echo get_spacial_instruction($work_order_no,"780px",147);?>
        <table width="780" align="center">
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
    </div>
        <?
        // echo signature_table(152, $data[0], "780px");
        echo signature_table(152, $data[0], "780px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
}

if ($action=="spare_parts_work_order_print9")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //echo "shakil"; die;
    $cbo_template_id=$data[7];

    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
   //print_r ($data); die;
    $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $com_name=$sql_company[0][csf("company_name")];
    $com_email=$sql_company[0][csf("email")];
    $com_website=$sql_company[0][csf("website")];

    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    //$lib_location_arr=return_library_array('SELECT id,location_name FROM lib_location','id','location_name' );
    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $contact_person_library= return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $company_library=return_library_array( "select id,company_name from lib_company","id", "company_name"  );
    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, location_id, discount_remarks, insert_date,inserted_by, is_approved  FROM  wo_non_order_info_mst WHERE id = $data[1]");
    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        //echo $item_category_id;die;
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];
        $inserted_by= $row[csf("inserted_by")];

        //echo $wo_item_category;die;

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];
        $lib_location_arr=$row[csf("location_id")];
        $insert_date = $row[csf("insert_date")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
    }

    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
    }

    $sql_for_item_category= sql_select("select a.id,  d.item_description
    from wo_non_order_info_mst a,wo_non_order_info_dtls b, product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 and a.id = $data[1]");

    $item_desc_text='';
    $item_data_arr=array();
    foreach($sql_for_item_category as $row)
    {
        $item_desc_text.=$row[csf('item_description')].'***';
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
    //$quot_ref=return_field_value("requ_no as our_ref","inv_purchase_requisition_mst","id=$data[2]","our_ref" );
    if($db_type==2)
    {
        //$quot_ref=return_field_value("LISTAGG(requ_prefix_num , ',') WITHIN GROUP (ORDER BY requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        /*$quot_ref=return_field_value(" rtrim(xmlagg(xmlelement(e,requ_prefix_num,',').extract('//text()') order by requ_prefix_num).GetClobVal(),',') AS our_ref ","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
        $quot_ref = $quot_ref->load();*/

        $quot_result=sql_select("select our_ref from inv_purchase_requisition_mst where id in($data[2])");
        foreach($quot_result as $row)
        {                
            $quot_ref .= $row[csf('value')].",";
        }
        $quot_ref=implode(",",array_unique(explode(",",chop($quot_ref,","))));

    }
    else
    {
    $quot_ref=return_field_value("group_concat(requ_prefix_num ) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
    }
    $req_no="";
    $req_no_id=array_unique(explode(",",$quot_ref));
    foreach($req_no_id as $reg_id)
    {
    if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;

    }

    if($db_type==0)
    {
        $quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
    }
    else
    {
        // $quot_factor_val=return_field_value("LISTAGG(cast(c.value as  varchar2(4000)) , ',') WITHIN GROUP (ORDER BY c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
        /*$quot_factor_val=return_field_value("rtrim(xmlagg(xmlelement(e,c.value,',').extract('//text()') order by c.value).GetClobVal(),',') AS as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );

        $quot_factor_val = $quot_factor_val->load();*/

    $quot_result=sql_select("select a.value from inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c where a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'");
        foreach($quot_result as $row)
        {                
            $quot_factor_val .= $row[csf('value')].",";
        }
        $quot_factor_val=implode(",",array_unique(explode(",",chop($quot_factor_val,","))));
    }
    $quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id='$data[3]'","system_id" );
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        $i = 0;
        $total_ammount = 0;
        ?>


    <div style="padding-left: 10px; width: 550px;">
        <table align="center" cellspacing="0" style="height: 30px;" width="550" >
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>


        <table align="center" cellspacing="0" width="550"  >
            <tr>
                <th align="left" style="font-size: 16px;"><b> <? echo "Ref: ".$data[4]."<br>".change_date_format($data[5]); ?></b></th>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="550" >

                <tr align="center">
                    <th width="200" align="left" style="font-size: 16px;"><b>To</b></th>
                </tr>
                <tr>
                    <td style="font-size: 12px;"><b><?php echo $supplier_name_library[$supplier_id]." <br>".$supplier_address; //$supplier_country // $supplier_phone // $supplier_email?></b></td>
                </tr>
        </table>
        <table align="center" cellspacing="0" width="550" >
            <tr>
                <td style="font-size: 12px;"><? echo "Subject : ". " Work order for the supply of ".implode(",",array_unique(explode("***",chop($item_desc_text,"***")))); ?>
                </td>
            </tr>
            <br>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td >Dear Concern,<br> We are pleased to inform you that our management has accepted your offer and you are hereby requested to supply the following item as per the terms and condition mentioned below:<br>
                    1. Description, Unit Price & Quantity: 
                </td>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="550"  border="1" rules="all" class="rpt_table" >
            <tr align="center">
                <th width="50">SL</th>
                <th width="200" align="center">Description</th>
                <th width="100" align="center">Quantity</th>
                <th width="100" align="center">Unit price (Tk.)</th>
                <th width="" align="center">Total price (Tk.)</th>
            </tr>
            <?
            //$reg_no=explode(',',$data[11]);
            $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id='$data[3]'");
                    $remark_data_arr=array();
                    foreach($sql_result as $row)
                    {
                        $remark_data_arr[$row[csf('product_id')]]=$row[csf('remarks')];
                    }
            $cond="";
            if($data[1]!="") $cond .= " and a.id='$data[1]'";
            //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
            $i=1;
            $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code,c.requ_no
            from wo_non_order_info_mst a,wo_non_order_info_dtls b left join  inv_purchase_requisition_mst c on b.requisition_no=c.id,product_details_master d
            where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");

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
                $amount=$row[csf('amount')];
                $amount_sum += $amount;

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="center"><? echo $row[csf('item_description')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                     <td align="right"><? echo number_format($row[csf('rate')],4); ?></td> 
                    <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                    
                </tr>
                <?
                $i++;
            }
            ?>
            <tr >
                <td align="right" colspan="4" ><strong>Grand Total :</strong></td>
                <td align="right"><? echo $word_amount=number_format($amount_sum,2);  ?></td>
            </tr>
        </table>
    <table width="550" align="center">
            <tr>
                <td > <strong>Amount in words:</strong>&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp; </td>
            </tr>
        </table>
        <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? //echo $approved_msg ?> </font></div>
        <br>
        <?  //echo get_spacial_instruction($work_order_no,"550px",147);?>
        <table width="550" align="center">
            <?
            $trims_con_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='$work_order_no' order by id");

            if (count($trims_con_array) > 0) {
                $i = 1;
                foreach ($trims_con_array as $row) {  
                ?>
                <tr>
                    <td valign="top"><? echo $i; ?></td>
                    <td valign="top"><? echo $row[csf('terms')];?></td>
                </tr>
                <?
                $i++;
                }
            }
            ?>
        </table>
        <table width="550" align="center">
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4">We confidently hope that you will take proper care in executing the above order within the stipulated time.<br>
                        Please confirm receipt of this work order.</td>
                    </tr>
                    <tr>
                        <td colspan="">Thanking You</td>
                        <td colspan="">&nbsp;</td>
                        <td colspan="2">Acceptance of manufacturer/Supplier</td>
                    </tr>
        </table>
    </div>
        <?
        // echo signature_table(152, $data[0], "780px");
        echo signature_table(152, $data[0], "550px",$cbo_template_id,50,$user_lib_name[$inserted_by]);
}

if ($action=="spare_parts_work_order_print10")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_template_id=$data[7];

    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, location_id, discount_remarks, insert_date,inserted_by, is_approved ,dealing_marchant, wo_type, payterm_id,tenor, inco_term_id,reference,port_of_loading,pi_issue_to,contact FROM  wo_non_order_info_mst WHERE id = $data[1]");
    foreach($sql_data as $row)
    {
        $supplier_id=$row[csf("supplier_id")];
        $marchant_id=$row[csf("dealing_marchant")];
        $is_approved_val=$row[csf("is_approved")];
        $location_id=$row[csf("location_id")];
        $work_order_no=$row[csf("wo_number")];
        $work_order_date=$row[csf("wo_date")];
        $delivery_date=$row[csf("delivery_date")];
        $pay_mode_id=$row[csf("pay_mode")];
        $wo_type=$row[csf("wo_type")];
        $payterm_id=$row[csf("payterm_id")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $tenor=$row[csf("tenor")];
        $inco_term_id=$row[csf("inco_term_id")];
        $reference=$row[csf("reference")];
        $delivery_place=$row[csf("delivery_place")];
        $remarks=$row[csf("remarks")];
        $port_of_loading=$row[csf("port_of_loading")];
        $pi_issue_to=$row[csf("pi_issue_to")];



        $item_category_id=$row[csf("item_category")]; 
        $source=$row[csf("source")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $wo_item_category=$row[csf("item_category")];
        $inserted_by= $row[csf("inserted_by")];
        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];
        $insert_date = $row[csf("insert_date")];
        $contact_to = $row[csf("contact")];
    }

	$sql_supplier = sql_select("SELECT id,supplier_name,contact_person,contact_no,web_site,email,address_1 FROM lib_supplier WHERE id=$supplier_id");

    foreach($sql_supplier as $supplier_data)
	{
		if($supplier_data[csf('supplier_name')]!='')$supplier_name = $supplier_data[csf('supplier_name')];else $supplier_name='';
		if($supplier_data[csf('address_1')]!='')$supplier_address = $supplier_data[csf('address_1')];else $supplier_address='';
		if($supplier_data[csf('contact_no')]!='')$supplier_phone = $supplier_data[csf('contact_no')];else $supplier_phone='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$supplier_email = $supplier_data[csf('email')];else $supplier_email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
	}
    $com_dtls = fnc_company_location_address($data[0], $location_id, 1);
    ?>

	<div id="table_row" style="width:930px;">
    <?
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$lib_country_arr=return_library_array( "SELECT id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$marchant_arr=return_library_array( "SELECT id, team_member_name from lib_mkt_team_member_info where id in($marchant_id)",'id','team_member_name');
	$buyer_name_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "SELECT id, season_name from lib_buyer_season ",'id','season_name');
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
    $company_info=sql_select("SELECT EMAIL,WEBSITE,CONTACT_NO,CONTRACT_PERSON,BIN_NO from lib_company where id=$data[0]");
    $is_approved_val_arr=array(1=>"Approved",3=>"Partial Approved");
    $i = 0;
    $total_ammount = 0;
    ?>
        <table align="center" cellspacing="0" width="1180" >
            <tbody>
                <tr>
                    <td rowspan="4" width="70">
                        <img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60">
                    </td>
                    <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0];  ?></strong></td>
                    <td rowspan="3" colspan="2"> </td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><strong><? echo $com_dtls[1];  ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><strong><? echo "TEL: ".$company_info[0]["CONTACT_NO"].", Email: ".$company_info[0]["EMAIL"].", Website: ".$company_info[0]["WEBSITE"];  ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><strong style="font-size:25px;">Work Order: <?=$work_order_no;?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="1180" border="1" rules="all" class="rpt_table">
            <tbody>
                <tr>
                    <td colspan="7">
                        <b>Beneficiary/Supplier/Seller:</b>
                    </td>
                    <td colspan="7">
                        <b>Consignee/Buyer:</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" valign="top">
                        <? echo "<strong>".$supplier_name."</strong><br>".$supplier_address."<br>TEL# ".$supplier_phone."<br>E-mail: ".$supplier_email."<br>Contact Person: ".$contact_person; ?>
                    </td>
                    <td colspan="7" valign="top">
                        <? echo "<strong>".$com_dtls[0]."</strong><br>".$com_dtls[1]."<br>TEL# ".$company_info[0]["CONTACT_NO"]."<br>E-mail: ".$company_info[0]["EMAIL"]."<br>Contact Person: ".$contact_to; ?>
                    </td>
                </tr>
                <tr>
                    <td width="80"><b>WO Date</b></td>
                    <td width="80"><? echo $work_order_date; ?></td>
                    <td width="80"><b>Delivery Date</b></td>
                    <td width="80"><? echo $delivery_date; ?></td>
                    <td width="90"><b>Pay Mode</b></td>
                    <td width="80"><? echo $pay_mode[$pay_mode_id]; ?></td>
                    <td width="80"><b>WO Type</b></td>
                    <td width="80"><? echo $main_fabric_co_arr[$wo_type]; ?></td>
                    <td width="80"><b>Currency</b></td>
                    <td width="80"><? echo $currency[$currency_id]; ?></td>
                    <td width="80"><b>Pay Terms</b></td>
                    <td width="80"><? echo $pay_term[$payterm_id] ?></td>
                    <td width="80"><b>WO Basis</b></td>
                    <td><? echo $wo_basis[$wo_basis_id]; ?></td>
                </tr>
                <tr>
                    <td><b>Incoterm</b></td>
                    <td><? echo $incoterm[$inco_term_id]; ?></td>
                    <td><b>Port Of Loading</b></td>
                    <td><? echo $port_of_loading; ?></td>
                    <td><b>Tenor</b></td>
                    <td><? echo $tenor ?></td>
                    <td><b>PI Issue To</b></td>
                    <td colspan="3"><? echo $company_library[$pi_issue_to]; ?></td>
                    <td><b>Reference</b></td>
                    <td><? echo $reference; ?></td>
                    <td><b>BIN</b></td>
                    <td><? echo $company_info[0]["BIN_NO"]; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Place of Delivery:</b></td>
                    <td colspan="4"><? echo $delivery_place; ?></td>
                    <td><b>Remarks</b></td>
                    <td colspan="7"><? echo $remarks; ?></td>
                </tr>
            </tbody>
        </table>
        <table align="center" cellspacing="0" width="1180">
            <td align="center" style="font-size:30px; color:#FF0000;" >&nbsp; <? echo $is_approved_val_arr[$is_approved_val]; ?></td>
        </table>
        <br>
        <table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="110" >Requisition No</th>
                    <th width="80" >Item Category</th>
                    <th width="80" >Item Code</th>
                    <th width="80" >Item Group</th>
                    <th width="220" >Item Group & Description</th>
                    <th width="120" >Item Size</th>
                    <th width="50" >Order UOM</th>
                    <th width="70" >WO.Qty</th>
                    <th width="70" >Rate</th>
                    <th width="80">Amount</th>
                    <th >Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?
                $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
                $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');

                $i=1;
                $sql_result= sql_select("SELECT a.id,a.currency_id,b.requisition_no,b.req_quantity,b.uom,d.id as prod_id,b.supplier_order_quantity,b.remarks,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account,d.item_code,b.item_category_id
                from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 and a.id = $data[1]");
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    $total_amount+= $row[csf('gross_amount')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
                        <td ><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td ><? echo $row[csf('item_code')]; ?></td>
                        <td ><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                        <td><? echo $row[csf('item_description')]; ?></td>
                        <td><? echo $row[csf('item_size')]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td ><? echo number_format($row[csf('supplier_order_quantity')],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",""); ?></td>
                        <td><?echo $row[csf("remarks")]; ?></td>
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
            <!-- </tbody>
            <tfobot> -->
                <tr>
                    <td align="right" colspan="10" ><strong>Total :</strong></td>

                    <td align="right" colspan="1"><? echo $word_total_amount=number_format($total_amount, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" colspan="9">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                    <td align="right" >Upcharge :&nbsp;</td>
                    <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="left" colspan="9">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
                    <td align="right" >Discount :&nbsp;</td>
                    <td align="right"><? echo number_format($discount,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10"><strong>Net Total : </strong>&nbsp;</td>
                    <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
                </tr>
                <tr>
                <tr>
                    <td align="left" colspan="12"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <?echo get_spacial_instruction($work_order_no,"1180px",147);?>
        <br>
    <?
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");

    if(count($approved_sql)>0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Order Approval Status </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }
    ?>
    <!-- //approved status end-->
    <br/>
    <?
        echo signature_table(152, $data[0],"1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if ($action=="spare_parts_work_order_po_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
	$cbo_template_id=$data[7];
	$show=$data[8];
    $lc_type_arr=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT'];
    $lc_type=$data[10];
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
	
    $sql_company=sql_select("select id, group_id, tin_number,bin_no from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $tin_num=$sql_company[0][csf("tin_number")];
    $bin_num=$sql_company[0][csf("bin_no")];
    $group_id=$sql_company[0][csf("group_id")];
	// echo "<pre>"; print_r($sql_company); die;
    // echo $group_id; die;
	$com_name=return_field_value('company_name','lib_company',"id='$data[0]'",'company_name' );
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
	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, contact, wo_date, currency_id, supplier_id, attention, buyer_name, style, item_category, DELIVERY_PLACE,delivery_date, remarks,is_approved, is_approved,inserted_by, payterm_id,reference,wo_type,up_charge,discount, location_id,upcharge_remarks,discount_remarks, tenor  FROM  wo_non_order_info_mst WHERE id = $data[1]");
    // echo "<pre>";
    // print_r($sql_data);
    // echo "</pre>";
    $wo_condition = sql_select("SELECT item_category_id FROM  wo_non_order_info_dtls WHERE mst_id = $data[1] and item_category_id=114");

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
		$source_id= $row[csf("source")];
		$contact_per= $row[csf("contact")];
		$wo_type= $row[csf("wo_type")];
		$reference= $row[csf("reference")];
		$upcharge= $row[csf("up_charge")];
		$discount= $row[csf("discount")];
        $remarks= $row[csf("remarks")];
		$inserted_by= $row[csf("inserted_by")];
        $tenor= $row[csf("tenor")];
        $location_id= $row[csf("location_id")];
        $upcharge_remarks = $row[csf("upcharge_remarks")];
		$discount_remarks= $row[csf("discount_remarks")];
        $delivery_addrs=$row[csf("DELIVERY_PLACE")];
        
		
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");
    $com_address=return_field_value('address','lib_location',"id=$location_id",'address' );

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
	$sql_group=return_field_value("group_name","lib_group","id=$group_id",'group_name');
    // echo $sql_group; die;
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
	// $group_logo=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
    <style>
        body{
            margin-left:10px;
        }
    </style>
	<div class="fontincrease">
    <table cellspacing="0" width="1000" align="center" >
        <tr>
            <td rowspan="2" width="100"><img src="<?= "../../".$image_location;?>" height="60" width="90" alt="Group Logo"></td>
            <td colspan="3" style="font-size:25pt;" align="center"><strong><? echo $sql_group;?></strong></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:21pt;"><strong>
            <?  if($wo_condition[0][csf('item_category_id')]==114){
                    echo "Work Order";
                }else{
                    echo "Purchase Order- General Purchase";
                }?>    
            </strong></td>
        </tr>
         <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>
    </table>
    <table cellspacing="0" width="1000" class="headTable" >
        <tr>
            <td colspan="2" style="font-size:17pt;"><strong><?= $com_name;?></strong></td>
            <td align="left" class="bordertbl" style="font-size:16pt;"><strong>Purchase Type:</strong></td>
            <td align="left" class="bordertbl" style="font-size:16pt;"><strong><? echo $wo_type_array[$wo_type]; ?></strong></td>
        </tr>
        <tr>
            <td width="90" valign="top"><strong>Address:</strong></td>
            <td width="580" ><? echo "&nbsp;".$com_address; ?></td>
			<td width="160" align="left" class="bordertbl"><strong>P.O. Number:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td><strong>BIN:</strong></td>
            <td align="left"><? echo "&nbsp;".$bin_num; ?></td>
            <td align="left" class="bordertbl"><strong>P.O. Date:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_date; ?></strong></td>
        </tr>
         <tr>
            <td><strong>TIN:</strong><br></td>
            <td align="left" ><? echo "&nbsp;".$tin_num; ?></td>                          
			<td align="left" valign="top" class="bordertbl" rowspan="2"><strong>Delivery Date:</strong></td>
            <td align="left" valign="top" class="bordertbl" rowspan="2"><strong><? echo $delivery_date; ?></strong></td>
        </tr>
        <tr>
        <td colspan="2"><strong>Delivery Addr.:</strong> <? echo  $delivery_addrs; ?><br>         
        </tr>
        <tr>
            <td><strong>Contact:</strong></td>
            <td align="left" ><? echo $contact_per; ?></td>
            <td align="left" valign="top" class="bordertbl"><strong>Req No:</strong></td>
            <td align="left" valign="top" class="bordertbl"><strong><? echo $req_num; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td>
            <td align="left" style="font-size:15pt;"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
            <td align="left" class="bordertbl"><strong>Quotation No.:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Address:</strong></td>
            <td align="left" ><? echo $supplier_address ; ?></td>
            <td align="left" class="bordertbl"><strong>RFQ Number:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>
            <td><strong>Attn:</strong></td>
            <td align="left" valign="top"><? 
			$attn= explode(",",$attention);
			foreach($attn as $value){
                echo "<div class='paddingtbl'>".$value."</div>";
			}
			?></td>
            <td align="left" class="bordertbl"><strong>Reference: </strong></td>
            <td align="left" class="bordertbl"><? echo $reference; ?></td>
        </tr>
        <tr>
            <td><strong>Contact No:</strong></td>
            <td align="left" valign="top" ><? 
            $supplier_mobile= explode(",",$supplier_phone);
            foreach($supplier_mobile as $mobile){
                echo "<div class='paddingtbl'>".$mobile."</div>";
            }
            ?></td>
            <td align="left" class="bordertbl"><strong>L/C / Payment Terms:</strong></td>
            <td align="left" class="bordertbl"><? 
                if($lc_type>0 && $payterm_id!=0){
                    echo $lc_type_arr[$lc_type].", ";
                }
                else if($lc_type>0 && $payterm_id==0){
                    echo $lc_type_arr[$lc_type];
                }
                if($tenor){echo "LC ".$tenor." Days";}
                else{echo $pay_term[$payterm_id];}; 
            ?></td>
        </tr>
        <tr>
            <td><strong></strong></td>
            <td align="left" ></td>
            <td align="left" class="bordertbl"><strong>PO Status:</strong></td>
            <td align="left" class="bordertbl"><? echo $approved_note; ?></td>
        </tr>
        <tr>
            <td><strong></strong></td>
            <td align="left" ></td>
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
            <th width="160" >Item</th>
            <? if($show){?> <th width="100" >Size/MSR</th> <?}?>            
            <th width="<?=$show?170:440;?>" >Declaration Details</th>
            <? if($show){?> <th width="170">Narration</th> <?}?>            
            <th width="70" >Unit</th>
            <th width="80">Quantity</th>
            <th width="60" >Rate</th>
            <th >Amount</th>
        </thead>
		<tbody>
            <?


        	$cond="";
        	if($data[1]!="") $cond .= " and a.id='$data[1]'";
            $i=1;

            $sql_dtls="SELECT a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate, b.gross_amount,b.item_category_id,b.remarks, d.item_description, d.item_size,d.model, d.item_group_id,d.item_number, b.brand,e.origin, e.service_details
            from wo_non_order_info_mst a, product_details_master d, wo_non_order_info_dtls b
            left join  inv_purchase_requisition_dtls e on b.requisition_dtls_id=e.id
            where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 and b.item_category_id<>114 $cond
            union all 
            SELECT a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, null as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate, b.gross_amount,b.item_category_id,b.remarks, null as item_description, null as item_size,null as model, null as item_group_id, null as item_number, b.brand,e.origin,e.service_details
            from wo_non_order_info_mst a, wo_non_order_info_dtls b
            left join  inv_purchase_requisition_dtls e on b.requisition_dtls_id=e.id
            where a.id=b.mst_id  and b.is_deleted=0 and b.status_active=1 and b.item_category_id=114 $cond";
            // echo $sql_dtls; //die;

            $sql_result= sql_select( $sql_dtls);

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
                
                $narration="";
                if($row[csf('model')]){$narration=$row[csf('model')];}
                if($row[csf('brand')]){if($narration){$narration.=", ".$row[csf('brand')];}else{$narration=$row[csf('brand')];}}
                if($row[csf('item_number')]){if($narration){$narration.=", ".$row[csf('item_number')];}else{$narration=$row[csf('item_number')];}}
        		?>
                <tr bgcolor="#FFFFFF">
                    <td align="center" ><? echo $i; ?></td>
                    <td ><? //--- item_category_id 114 = service category 
                    if($row[csf('item_category_id')]!=114) echo $row[csf('item_description')]; else echo $row[csf('service_details')];?></td>
                    <? if($show){ ?> <td ><? echo $row[csf('item_size')]; ?></td> <? } ?>         			
        			<td ><? echo implode("<br>",explode(">",$row[csf('remarks')])); ?></td>
                    <? if($show){ ?> <td ><? echo $narration; ?></td> <? } ?> 
                    <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('gross_rate')],4); ?></td>
                    <td align="right"><? echo number_format($row[csf('gross_amount')],4);?></td>
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
            //echo "<pre>";
            //print_r($testPara_arr);
            $rowspan3 = 1;
            if($discount_remarks){
                $rowspan1=1; 
            }else{$rowspan1=2;}
            if($upcharge_remarks){
                $rowspan2=1; 
            }else{$rowspan2=2;}
        	?>
        	<tr >
                <!-- <td align="left" colspan="<? //=$show?6:4;?>" rowspan="4"></td> -->
                <? if($rowspan1 == 2  && $rowspan2 == 2){ $rowspan3++; ?> <td align="left" colspan="<?=$show?6:4;?>" rowspan="4"></td> <? }else{ ?> <td align="left" colspan="<?=$show?6:4;?>" rowspan="<?=$rowspan1?>"><strong></strong></td> <? } ?>
                
                <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
                <td align="right"><? echo number_format($total_amount,4); ?></td>
        	</tr>
        	<tr >
                <? if($rowspan1 == 1){ ?> <td align="left" colspan="<?=$show?6:4;?>" >&nbsp;&nbsp;<strong><? echo $discount_remarks; ?> </strong></td> <? } ?>
                <td align="right" colspan="2" ><strong>Discount</strong></td>
                <td align="right"><? echo number_format($discount,4); $total_amount=$total_amount-$discount; ?></td>
        	</tr>
        	<tr >
                <? if($rowspan2 == 1){ ?> <td align="left" colspan="<?=$show?6:4;?>" >&nbsp;&nbsp;<strong><? echo $upcharge_remarks; ?></strong></td> <? }elseif($rowspan1 != 2  && $rowspan2 == 2){ ?> <td align="left" colspan="<?=$show?6:4;?>" rowspan="<?=$rowspan2?>"><strong></strong></td> <? } ?>  
                <td align="right" colspan="2"><strong>PO Charge</strong></td>
                <td align="right"><? echo number_format($upcharge,4);$total_amount=$total_amount+$upcharge; ?></td>
        	</tr>
        	<tr >
                <? if($rowspan3 == 1  && $rowspan2 == 1){ ?> <td align="left" colspan="<?=$show?6:4;?>" rowspan="<?=$rowspan2?>"><strong></strong></td> <? } ?>
                <td align="right" colspan="2" style="font-size:15pt;"><strong>Total Amount </strong></td>
                <td align="right" style="font-size:15pt;"><strong><? echo $currency_sign_arr[$currency_id]." ". number_format($total_amount,4); ?></strong></td>
        	</tr>
        	<tr >
                
                <td align="left" colspan="9" ><strong style="font-size:15pt;"> Amount in words: </strong><? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?></td>
        	</tr>
        </tbody>
    </table>
    <br>
    <?
    //echo "SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by";
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    //echo count($approved_sql);
    /*$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
    $unapproved_request_arr=array();
    foreach($sql_unapproved as $rowu)
    {
        $unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
    }

    foreach ($approved_his_sql as $key => $row)
    {
        $array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
        if ($row[csf('un_approved_date')]!='')
        {
            $array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
            $array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
        }
    }*/
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                    <tr>
                        <th colspan="4">Approval Status</th>
                    </tr>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="250">Name</th>
                        <th width="200">Designation</th>
                        <th width="100">Approval Date</th>
                    </tr>
                </thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }
    ?>
	<br/>

	<table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
    	<tr >
            <td align="left" colspan="10" ><strong style="font-size:15pt;">Special Comments: </strong><? echo $remarks; ?></td>
    	</tr>
	</table>
	<br/>
	<br/>
    <?  //echo get_spacial_instruction($work_order_no,"1000px",147);?>
	<div><strong style="font-size:15pt;">Terms & Conditions:</strong></div>
	<?
	    $sql_term= sql_select("select terms from wo_booking_terms_condition where entry_form=147 and booking_no='$work_order_no'  order by id");
		$i=1;
	foreach ($sql_term as $value) {
		echo $i.". ".$value[csf('terms')]."</br>";
		$i++;
	}
	?>
	<br/>

		<?
		    //$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			//echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);

            // echo signature_table(152, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
            if ($cbo_template_id) {$template_id = " and template_id=$cbo_template_id ";}

			$sql = sql_select("select designation,name,user_id,prepared_by from variable_settings_signature where report_id=152 and company_id='$data[0]' $template_id order by sequence_no");

			$signature_sql = sql_select("SELECT c.master_tble_id as MASTER_TBLE_ID,c.image_location as IMAGE_LOCATION  from variable_settings_signature a, electronic_approval_setup b, common_photo_library c where a.user_id=b.user_id and a.user_id=c.master_tble_id and a.report_id=152 and a.company_id='$data[0]' and a.template_id=$cbo_template_id and b.page_id=628 and b.entry_form=17 and b.company_id=$data[0] and c.form_name='user_signature'");
			$signature_location=array();
			foreach($signature_sql as $row)
			{
				$signature_location[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
			}
			if($sql[0][csf("prepared_by")]==1){
				list($prepared_by,$activities)=explode('**',$prepared_by);
				$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
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
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
		// fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
		</script>
		<?
	exit();
}

if ($action=="spare_parts_work_order_po_print_11")
{
    extract($_REQUEST);
    $data=explode('*',$data);
	$cbo_template_id=$data[7];
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

        strong {
 
        font-size:15px;
        }

    </style>
    <?
	
		$sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,tin_number,bin_no,group_id from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
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
        $group_id=$sql_company[0][csf("group_id")];

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
	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, contact, wo_date, currency_id, supplier_id, attention, buyer_name, style, item_category, delivery_date, remarks,is_approved, is_approved,inserted_by, pay_mode,reference,wo_type,up_charge,discount,upcharge_remarks,discount_remarks FROM  wo_non_order_info_mst WHERE id = $data[1]");

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
		$pay_mode_id= $row[csf("pay_mode")];
		$source_id= $row[csf("source")];
		$contact_per= $row[csf("contact")];
		$wo_type= $row[csf("wo_type")];
		$reference= $row[csf("reference")];
		$upcharge= $row[csf("up_charge")];
		$discount= $row[csf("discount")];
        $remarks= $row[csf("remarks")];
		$inserted_by= $row[csf("inserted_by")];
		$upcharge_remarks= $row[csf("upcharge_remarks")];
		$discount_remarks= $row[csf("discount_remarks")];
		
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
	//$sql_group=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
    $sql_group=return_field_value("group_name","lib_group","id=$group_id",'group_name');
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
            <td colspan="3" style="font-size:25pt;" align="center"><b><? echo $com_name;//$sql_group;?></b></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:21pt;"><b>Purchase Order -General Purchases</b></td>
        </tr>
         <tr>
            <td colspan="4" align="center">&nbsp;</td>
        </tr>
    </table>
    <table cellspacing="0" width="1000" class="headTable" >
        <tr>
            <td width="350" colspan="2" style="font-size:17pt;"><b><?= $com_name;?></b></td>
            <td width="200" align="left" class="bordertbl" style="font-size:16pt;"><b>Purchase Type:</b></td>
            <td width="250" align="left" class="bordertbl" style="font-size:16pt;"><b><? echo $wo_type_array[$wo_type]; ?></b></td>
        </tr>
        <tr>
            <td valign="top"><strong>Address:</strong></td>
            <td valign="top"><strong><? echo $com_address; ?></strong></td>
			<td align="left" class="bordertbl"><strong>P.O. Number:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_no; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Contact:</strong></td>
            <td align="left" ><strong><? echo $contact_per; ?></strong></td>
            <td align="left" class="bordertbl"><strong>P.O. Date:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $work_order_date; ?></strong></td>
        </tr>
         <tr>
            <td><strong>Supplier:</strong></td>
            <td align="left" style="font-size:15pt;"><b><? echo $supplier_name_library[$supplier_id]; ?></b></td>
            <td align="left" class="bordertbl"><strong>Currency:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $currency[$currency_id]; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Address:</strong></td>
            <td align="left" ><strong><? echo $supplier_address ; ?></strong></td>
			<td align="left" class="bordertbl"><strong>Quotation No.:</strong></td>
            <td align="left" class="bordertbl"></td>
        </tr>
        <tr>       
            <td align="left"><strong>PO Status:</strong></td>
            <td align="left"><strong><? echo $approved_note; ?></strong></td>   
            <td align="left" class="bordertbl"><strong>Payment Mode:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $pay_mode[$pay_mode_id]; ?></strong></td>
        </tr>
        <tr>       
            <td><strong>Attn:</strong></td>
            <td align="left" valign="top" ><strong><? 
			$attn= explode(",",$attention);
			foreach($attn as $value){
				echo "<div class='paddingtbl'>".$value."</div>";
			}
			?></strong></td>
            <td align="left" class="bordertbl"><strong>PO Status:</strong></td>
            <td align="left" class="bordertbl"><strong><? echo $approved_note; ?></strong></td>      
        </tr>
        <tr>
            <td><strong>Contact No:</strong></td>
            <td align="left" colspan="3"  ><strong><? echo $address1[$supplier_id];?></strong></td>
        </tr>
        <tr>
            <td><strong>Department:</strong></td>
            <td align="left" colspan="3"  ><strong><? echo $department_num ; ?></strong></td>           
        </tr>
        <tr>
            <td><strong>Remarks:</strong></td>
            <td align="left" colspan="3" ><strong><? echo $remarks ; ?></strong></td>     
        </tr>
        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
	<table align="center" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="130" >Req No:</th>
            <th width="170" >Item</th>
            <th width="100" >Item Group</th>
            <th width="150" >Item Category</th>
            <th width="120">Brand</th>
            <th width="75">Origin</th>
            <th width="70" >Unit</th>
            <th width="60">Quantity</th>
            <th width="60" >Rate</th>
            <th width="100" >Amount</th>
            <th >Remarks</th>
        </thead>
		<tbody>
            <?


        	$cond="";
        	if($data[1]!="") $cond .= " and a.id='$data[1]'";
            $i=1;

            $sql_dtls="SELECT a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate, b.gross_amount,b.item_category_id,b.remarks, d.item_description, d.item_size,d.model, d.item_group_id, b.brand as brand_name, b.origin,f.requ_no
            from wo_non_order_info_mst a, product_details_master d,inv_purchase_requisition_mst f, wo_non_order_info_dtls b
            left join  inv_purchase_requisition_dtls e on b.requisition_dtls_id=e.id
            where a.id=b.mst_id and b.item_id=d.id and f.id=e.mst_id and b.is_deleted=0 and b.status_active=1 $cond ";
            //echo $sql_dtls; //die; 

            $sql_result= sql_select( $sql_dtls);

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
        		// $desc.="'".$row[csf('item_description')]."',";
        		$desc='';
        		$desc.=$row[csf('item_description')];
                if($row[csf('item_size')]){
                    if($desc!=""){$desc.=', '.$row[csf('item_size')];}else{$desc.=$row[csf('item_size')];}
                }
                if($row[csf('model')]){
                    if($desc!=""){$desc.=', '.$row[csf('model')];}else{$desc.=$row[csf('model')];}
                }
        		?>
                <tr bgcolor="#FFFFFF">
                    <td align="center" ></strong><? echo $i; ?></strong></td>
                    <td ><strong><? echo $row[csf("requ_no")];?></strong></td>
                    <td ><strong><? echo $desc;?></strong></td>
        			<td ><strong><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></strong></td>
        			<td ><strong><? echo $item_category[$row[csf('item_category_id')]]; ?></strong></td>
        			<td ><strong><? echo $row[csf('brand_name')];?></strong></td>
        			<td ><strong><? echo $lib_country_arr[$row[csf('origin')]]; ?></strong></td>
                    <td align="center"><strong><? echo $unit_of_measurement[$row[csf('uom')]]; ?></strong></td>
                    <td align="right"><strong><? echo number_format($row[csf('supplier_order_quantity')],2); ?></strong></td>
                    <td align="right"><strong><? echo number_format($row[csf('gross_rate')],4); ?></strong></td>
                    <td align="right"><strong><? echo number_format($row[csf('gross_amount')],2);?></strong></td>
                    <?
                        $carrency_id=$row[csf('currency_id')];
                        if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
                    ?>
                    <td align="right"><strong><? echo $row[csf('remarks')];?></strong></td>
                </tr>
            <?php
            $i++;
        	}

            $sub_total_amount= $total_amount + $upcharge - $discount;
            $word_total_amount=number_format($sub_total_amount, 2);
            //echo "<pre>";
            //print_r($testPara_arr);
        	?>
        	<tr >
                <td align="right" colspan="7"></td>
                <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
                <td align="right"><strong><? echo number_format($total_amount,4); ?></strong></td>
                <td align="right">&nbsp;</td>
        	</tr>
        	<tr >
                <td colspan="6"><strong>Upcharge Remarks</strong></td>
                <td align="right"><strong><? echo $upcharge_remarks; ?></strong></td>
                <td align="right" colspan="2"><strong>Upcharge</strong></td>
                <td align="right"><strong><? echo number_format($upcharge,4); ?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
            </tr>
        	<tr >
                <td colspan="6" ><strong>Discount Remarks</strong></td>
                <td align="right"><strong><? echo $discount_remarks; ?></strong></td>
                <td align="right" colspan="2" ><strong>Discount</strong></td>
                <td align="right"><strong><? echo number_format($discount,4); ?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
        	</tr>
        	<tr >
                <td align="right" colspan="8"></td>
                <td align="right" colspan="2" style="font-size:15pt;"><b>Total Amount </b></td>
                <td align="right" style="font-size:15pt;"><b><? echo $currency[$currency_id]."-".$word_total_amount; ?></b></td>
                <td align="right">&nbsp;</td>
        	</tr>
        	<tr >
                <td align="left" colspan="11" ><b style="font-size:15pt;"> Amount in words: <? echo number_to_words($word_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
        	</tr>
        </tbody>
    </table>
    <br>
    <?
    //echo "SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by";
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    //echo count($approved_sql);
    /*$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
    $unapproved_request_arr=array();
    foreach($sql_unapproved as $rowu)
    {
        $unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
    }

    foreach ($approved_his_sql as $key => $row)
    {
        $array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
        if ($row[csf('un_approved_date')]!='')
        {
            $array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
            $array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
        }
    }*/
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                    <tr>
                        <th colspan="4">Approval Status</th>
                    </tr>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="250">Name</th>
                        <th width="200">Designation</th>
                        <th width="100">Approval Date</th>
                    </tr>
                </thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }
    ?>
	<br/>

	<!-- <table align="center" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
    	<tr >
            <td align="left" colspan="10" ><strong style="font-size:15pt;">Special Comments: </strong><?// echo $remarks; ?></td>
    	</tr>
	</table> -->
	<br/>
	<br/>
    <?  //echo get_spacial_instruction($work_order_no,"1000px",147);?>
	<div><b style="font-size:15pt;">Terms & Conditions:</b></div>
	<?
	    $sql_term= sql_select("select terms from wo_booking_terms_condition where entry_form=147 and booking_no='$work_order_no' ");
		$i=1;
	foreach ($sql_term as $value) {
		echo $i.". ".$value[csf('terms')]."</br>";
		$i++;
	}
	?>
	<br/>

			<?
		//$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			//echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);

            echo signature_table(152, $data[0], "1100px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
		?>
    </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
		// fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
		</script>
		<?
	exit();
}

if ($action=="spare_parts_work_print_urmi")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'',''); 
    //print_r ($data); die;

    $cbo_template_id=$data[5];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];
    
    
    $location_arr=return_library_array( "select id,location_name from lib_location","id", "location_name"  );
    $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    
    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks,discount_remarks, inserted_by FROM  wo_non_order_info_mst WHERE id = $data[1]");

    foreach($sql_data as $row)
    {
        $mst_id=$row[csf("id")];
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];
        
        
        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];

        $inserted_by= $row[csf("inserted_by")];
        
        
    }


    
    if($requisition_no!="" && $wo_basis_id==1)
    {
        $req_location_data=array();
        $sql_requisition=sql_select("select id, location_id from  inv_purchase_requisition_mst where id in($requisition_no)");
        foreach($sql_requisition as $row)
        {
            $req_location_data[$row[csf("id")]]=$row[csf("location_id")];
        }
    }
    //echo "<pre>";print_r($req_location_data);
    
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

    $item_category_id=return_library_array( "select mst_id,item_category_id from  wo_non_order_info_dtls", "mst_id","item_category_id"  );
    
    
    //$sql_mst = sql_select("select id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no ,intendor_name,pi_basis_id,remarks from  com_pi_master_details where id= $pi_mst_id"); 
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $i = 0;
    $total_ammount = 0;
    $varcode_booking_no=$work_order_no;
    ?>
    <table align="center" cellspacing="0" width="1000" >
        <tr>
            <td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><strong>
            <? 
            if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
            if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code.", ";  
            ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><strong><? if($wo_item_category>0) echo $item_category[$wo_item_category]." " ."work order"; ?></strong></td>
        </tr>
    </table>
    <table align="center" cellspacing="0" width="1000" >
        <tr>
            <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="150"><strong>WO Number:</strong></td>
            <td width="150" align="left">
            <? 
            echo $work_order_no; 
            ?>
            </td>
            <td width="150" align="left" ><strong>Date :</strong></td>
            <td width="150" align="left"><? echo $work_order_date; ?></td>
        </tr>
        <tr>
            <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
            <td ><strong>Delivery Date :</strong></td>
            <td><? echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Place of Delivery:</strong></td>
            <td align="left" ><? echo $delivery_place; ?></td>
        </tr>
        <tr>
            <td><strong>Currency:</strong></td>
            <td align="left"><? echo $currency[$currency_id]; ?></td>
            <td align="left"><strong>Item Category:</strong></td>
            <td align="left" ><? echo $item_category[$item_category_id[$mst_id]]; //$item_category[$item_category_id]; ?></td>
        </tr>
         <tr>
            <td><strong>Pay Mode:</strong></td>
            <td align="left" ><? echo $pay_mode[$pay_mode_id]; ?></td>
            <td align="left" ><strong>WO Basis:</strong></td>
            <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
           
        </tr>
        <tr>
            <td align="left" colspan="1"><strong>Location</strong></td>
            <td align="left" colspan="3"><? echo $location_arr[$data[3]];?></td>
        </tr>
        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <table align="center" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="150" align="center">Requisition No</th>
            <th width="100" align="center">Location</th>
            <th width="80" align="center">Code</th>
            <th width="180" align="center">Item Name & Description</th>
            <th width="70" align="center">Item Size</th>
            <th width="200" align="center">Remarks</th>
            <th width="50" align="center">Order UOM</th>
            <th width="70" align="center">Req.Qty</th> 
            <th width="70" align="center">WO.Qty</th>
            <th width="80" align="center">Rate</th>
            <th width="95" align="center">Amount</th>
        </thead>
        <?
        //$reg_no=explode(',',$data[11]);
        $cond="";
        if($data[1]!="") $cond .= " and a.id='$data[1]'";
        //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
        $i=1;
        //echo "select a.id,a.wo_number,b.requisition_no,b.req_quantity,b.uom,b.supplier_order_quantity,b.amount,b.rate,d.item_description,d.item_size,d.item_group_id,d.item_account
        //from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
        //where a.id=b.mst_id and b.item_id=d.id $cond";
        
        $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code
        from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d
        where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");
        
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
            
            $amount=$row[csf('amount')];
            $amount_sum += $amount;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>"> 
                <td><? echo $i; ?></td>
                <td>
                <?
                $requesition_no="";
                $requisition_arr=array_unique(explode(",",$row[csf('requisition_no')]));
                $req_location="";
                foreach($requisition_arr as $req_id)
                {
                    if($requesition_no=="")  $requesition_no=$requisition_library[$req_id]; else $requesition_no=$requesition_no.",".$requisition_library[$req_id];
                    if($req_location=="")  $req_location=$lib_location_arr[$req_location_data[$req_id]]; else $req_location=$req_location.",".$lib_location_arr[$req_location_data[$req_id]];
                }
                echo  $requesition_no; 
                ?></td>
                <td><? echo $req_location; ?></td>
                <td><? echo $row[csf('item_code')]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                <td><? echo $row[csf('item_size')]; ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
                <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td align="right"><? echo $row[csf('req_quantity')]; ?></td>
                <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
        <tr>
            <th align="right" colspan="8" >Total :</th>
            <th align="right"><? echo number_format($req_quantity_sum,0) ?></th>
            <th align="right"><? echo number_format($supplier_order_quantityl_sum,0) ?></th>
            <th align="right" colspan="3"><? echo $word_amount=number_format($amount_sum,2);  ?></th>
        </tr>
        
        <tr>
            <td colspan="10">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
            <td align="right" >Upcharge :&nbsp;</td>
            <td align="right"><? echo number_format($up_charge,2);  ?></td>
        </tr>
        <tr>
            <td colspan="10">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
            <td align="right">Discount :&nbsp;</td>
            <td align="right"><? echo number_format($discount,2);  ?></td>
        </tr>
        <tr>
            <th align="right" colspan="11"><strong>Net Total : </strong>&nbsp;</th>
            <th align="right"><? echo number_format($net_wo_amount,2);  ?></th>
        </tr>
        
        
        
    </table>
   <table width="1200" align="center">
        <tr>
            <td colspan="10"> Amount in words:&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
        </tr>
    </table>
    <br>
    <?           
        echo get_spacial_instruction($work_order_no,"1200px");
        //echo signature_table(60, $data[0], "1100px");

        echo signature_table(152, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
      
      ?>
      
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    
      <?
      
    exit();
}

if ($action=="spare_parts_work_order_print8")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'',''); 
    //print_r ($data); die;

    $company=$data[0];
    $location=$data[3];
    $com_dtls = fnc_company_location_address($company, $location, 2);
    $cbo_template_id=$data[5];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");  
    
    $origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    
    $sql_data = sql_select("SELECT id as ID, wo_number as WO_NUMBER, requisition_no as REQUISITION_NO, delivery_place as DELIVERY_PLACE, wo_date as WO_DATE, currency_id as CURRENCY_ID, supplier_id as SUPPLIER_ID, attention as ATTENTION, wo_basis_id as WO_BASIS_ID, delivery_date as DELIVERY_DATE, pay_mode as PAY_MODE, up_charge as UP_CHARGE, discount as DISCOUNT,net_wo_amount as NET_WO_AMOUNT, inserted_by as INSERTED_BY FROM  wo_non_order_info_mst WHERE id = $data[1]");

    foreach($sql_data as $row)
    {
        $mst_id=$row["ID"];
        $work_order_no=$row["WO_NUMBER"];
        $supplier_id=$row["SUPPLIER_ID"];
        $work_order_date=$row["WO_DATE"];
        $currency_id=$row["CURRENCY_ID"];
        $wo_basis_id=$row["WO_BASIS_ID"];
        $pay_mode_id=$row["PAY_MODE"];
        $delivery_date=$row["DELIVERY_DATE"];
        $attention=$row["ATTENTION"];
        $requisition_no=$row["REQUISITION_NO"];
        // $delivery_place=$row[csf("delivery_place")];      
        
        $up_charge=$row["UP_CHARGE"];
        $discount=$row["DISCOUNT"];
        $net_wo_amount=$row["NET_WO_AMOUNT"];

        $inserted_by= $row["INSERTED_BY"];
        $delivery_dtls_info=explode('__',$row["DELIVERY_PLACE"]);
        
    }
    
    $sql_supplier = sql_select("SELECT id as ID,contact_no as CONTACT_NO,email as EMAIL,address_1 as ADDRESS_1 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data) 
    { 
        // $row_mst[csf('supplier_id')];        
        if($supplier_data['ADDRESS_1']!='')$address_1 = $supplier_data['ADDRESS_1'];else $address_1='';
        if($supplier_data['CONTACT_NO']!='')$contact_no = $supplier_data['CONTACT_NO'];else $contact_no='';
        if($supplier_data['EMAIL']!='')$email = $supplier_data['EMAIL'];else $email='';
        
        $supplier_address = $address_1;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
    }

    $cond="";
    if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $sql_result= sql_select("SELECT a.id as ID, a.currency_id as CURRENCY_ID,b.uom as UOM, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY, b.gross_rate as GROSS_RATE, b.gross_amount as GROSS_AMOUNT, d.item_description as ITEM_DESCRIPTION,d.item_size as ITEM_SIZE, b.item_category_id as ITEM_CATEGORY_ID, d.brand_name as BRAND_NAME,d.model as MODEL,d.origin as ORIGIN
    from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");
    $category_all='';
    foreach($sql_result as $value){
        $category_all.=$value['ITEM_CATEGORY_ID'].',';
    }
    $category_arr=array_unique(explode(",",chop($category_all,',')));
    $category_name='';

    foreach($category_arr as $value){
        $category_name.=$item_category[$value].', ';
    }
    ?>
    <table align="center" cellspacing="0" width="900" >
        <tr>
            <td width="80" rowspan="2"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
            <td colspan="10" style="font-size:xx-large;" align="right"><strong>PURCHASE ORDER</strong></td>
        </tr>
        <tr>
            <td colspan="10" align="right" height='10'></td>
        </tr>
        <tr>
            <td colspan="10" align="right"><strong><? echo "DATE : ".$work_order_date; ?></strong></td>
        </tr>
        <tr>
            <td ><strong>BILL TO</strong></td>
            <td colspan="10"><strong><? echo $com_dtls[0]; ?></strong></td>
        </tr>
        <tr>
            <td ><strong>P.O. Ref:</strong></td>
            <td colspan="10" ><strong><? echo $work_order_no; ?></strong></td>
        </tr>
    </table>

    <br>
    <table width="900">
        <tr>
            <td width="450" valign='top'>
                <table border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="400">
                            <strong>VENDOR &nbsp;&nbsp;:&nbsp;&nbsp;</strong><strong style="font-size:15px;"><? echo $supplier_name_library[$supplier_id]; ?></strong><br>
                            <strong>ADDRESS &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $supplier_address; ?><br>
                            <strong>ATTEN. &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $attention; ?><br>
                            <strong>CELL NO &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $supplier_phone; ?><br>
                            <strong>E-MAIL &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $supplier_email; ?><br>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="450" >
                <table border="1" rules="all" class="rpt_table">
                        <tr>
                            <td width="400">
                                <strong>SUPPLY TO &nbsp;&nbsp;:&nbsp;&nbsp;</strong><strong style="font-size:15px;"><? echo $delivery_dtls_info[1]; ?></strong><br>
                                <strong>ADDRESS &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $delivery_dtls_info[2]; ?><br>
                                <strong>CONTACT PERSON &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $delivery_dtls_info[3]; ?><br>
                                <strong>DESIGNATION &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $delivery_dtls_info[4]; ?><br>
                                <strong>CONTACT NO. &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $delivery_dtls_info[5]; ?><br>
                                <strong>E-MAIL &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $delivery_dtls_info[6]; ?><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>
    <table align="center" cellspacing="0" width="900" border="1" rules="all" class="rpt_table">
        <tr>
            <td width="100"><strong>TYPE OF ORDER</strong></td>
            <td colspan="10" style="text-transform: uppercase;">SUPPLY OF <? echo chop($category_name,', '); ?> ITEMS</td>
        </tr>
        <tr>
            <td  width="100"><strong>PAYMENT MODE</strong></td>
            <td colspan="10" style="text-transform: uppercase;" ><? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
    </table>
    <br>

    <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="200" align="center">Item</th>
            <th width="120" align="center">Specification</th>
            <th width="200" align="center">Brand/ Origin</th>
            <th width="70" align="center">Qty</th>
            <th width="70" align="center">Unit</th>
            <th width="80" align="center">Rate</th>
            <th align="right">Amount</th>
        </thead>
        <?
        
        $i=1;
        foreach($sql_result as $row)
        {
        if ($i%2==0)
        $bgcolor="#E9F3FF";
        else
        $bgcolor="#FFFFFF";

        $amount=$row['GROSS_AMOUNT'];
        $total_amount+= $amount;

        $brand_model_origin='';
        if($row['BRAND_NAME']!=''){$brand_model_origin.= $row['BRAND_NAME'].", ";}
        if($row['MODEL']!=''){$brand_model_origin.= $row['MODEL'].", ";}
        if($row['ORIGIN']!=''){$brand_model_origin.= $origin_arr[$row['ORIGIN']];}
        ?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td align="center"><? echo $i; ?></td>
            <td><? echo $row['ITEM_DESCRIPTION']; ?></td>
            <td><? echo  $row['ITEM_SIZE']; ?></td>
            <td ><? echo $brand_model_origin; ?></td>
            <td align="right"><? echo number_format($row['SUPPLIER_ORDER_QUANTITY'],2); ?></td>
            <td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>

            <td align="right"><? echo number_format($row['GROSS_RATE'],2,".",""); ?></td>
            <td align="right"><? echo number_format($row['GROSS_AMOUNT'],2,".",""); ?></td>
            <?
            $carrency_id=$row['CURRENCY_ID'];
            if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
            ?>
        </tr>
            <?php
                $i++;
            }
            ?>
        <tr>
            <td align="left" valign="middle" colspan="5" rowspan="4"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
            <td align="right" colspan="2" ><strong>Grand Total Amount (TK.)</strong>&nbsp;</td>
            <td align="right"><? echo $word_total_amount=number_format($total_amount,2,".",""); ?></td>
        </tr>
        <tr>
            <td  colspan="2" align="right" ><strong>Add: (If any)</strong>&nbsp;</td>
            <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
        </tr>
        <tr>
            <td align="right" colspan="2"><strong>Less: Discount (If Any)</strong>&nbsp;</td>
            <td align="right"><? echo number_format($discount,2,".","");  ?></td>
        </tr>
        <tr>
            <td align="right" colspan="2"><strong>Net Payable Amount (TK.)</strong>&nbsp;</td>
            <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
        </tr>
    </table>
    <br/>
    <table width="900" align="center">
        <tr>
        <td colspan="12">&nbsp;  </td>
        </tr>
        <tr>
        <td colspan="12">&nbsp; </td>
        </tr>

    </table>
    <br>
    <table  width="900" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr style="border:1px solid black;">
            <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Conditions</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select terms as TERMS from wo_booking_terms_condition where booking_no='$work_order_no'");
        if (count($data_array) > 0)
        {
            $k=0;
            foreach( $data_array as $row )
            {
                    $k++;
                    echo "<tr id='settr_1' style='border:1px solid black;'> <td style='border:1px solid black;'>
                    $k</td><td style='border:1px solid black;'> ".$row['TERMS']."</td></tr>";

            }
        }
        ?>
        </tbody>
    </table>
    <?           
        // echo get_spacial_instruction($work_order_no,"900px");

        echo signature_table(152, $data[0], "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);    
    exit();
}

if ($action=="spare_parts_work_order_print13")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'',''); 
    //print_r ($data); die;

    $company=$data[0];
    $location=$data[3];
    $com_dtls = fnc_company_location_address($company, $location, 2);
    // echo "<pre>";
    //     print_r($com_dtls);
    $cbo_template_id=$data[5];
    if($cbo_template_id==1) $align_cond='center'; else $align_cond='right';
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");  
    
    $origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');    

    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $lib_item_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1",'id','item_name');
    
    $sql_data = sql_select("SELECT id as ID, wo_number as WO_NUMBER, requisition_no as REQUISITION_NO, delivery_place , wo_date as WO_DATE, currency_id as CURRENCY_ID, supplier_id as SUPPLIER_ID, attention as ATTENTION, wo_basis_id as WO_BASIS_ID, delivery_date as DELIVERY_DATE, pay_mode as PAY_MODE,PAYTERM_ID, TENOR, up_charge as UP_CHARGE, discount as DISCOUNT,net_wo_amount as NET_WO_AMOUNT, inserted_by as INSERTED_BY,contact_no,contact FROM  wo_non_order_info_mst WHERE id = $data[1]");
    
    // echo "<pre>";    
    // print_r($sql_data);

    foreach($sql_data as $row)
    {
        $mst_id=$row["ID"];
        $work_order_no=$row["WO_NUMBER"];
        $supplier_id=$row["SUPPLIER_ID"];
        $work_order_date=$row["WO_DATE"];
        $currency_id=$row["CURRENCY_ID"];
        $wo_basis_id=$row["WO_BASIS_ID"];
        $pay_mode_id=$row["PAY_MODE"];
        $pay_term_id=$row["PAYTERM_ID"];
        $tenor=$row["TENOR"];
        $delivery_date=$row["DELIVERY_DATE"];
        $attention=$row["ATTENTION"];
        $requisition_no=$row["REQUISITION_NO"];
        $place_of_delivery=$row[csf("delivery_place")];
        $currency_id=$row[csf('currency_id')];
        $wo_basis= $row["WO_BASIS_ID"];
        $up_charge=$row["UP_CHARGE"];
        $discount=$row["DISCOUNT"];
        $net_wo_amount=$row["NET_WO_AMOUNT"];

        $inserted_by= $row["INSERTED_BY"];
        $delivery_dtls_info=explode('__',$row["DELIVERY_PLACE"]);
        $contact_no_mst=$row["CONTACT_NO"];
        $contact_to=$row["CONTACT"];
        
    }
    
    $sql_supplier = sql_select("SELECT id as ID,contact_no as CONTACT_NO,email as EMAIL,address_1 as ADDRESS_1 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data) 
    { 
        // $row_mst[csf('supplier_id')];        
        if($supplier_data['ADDRESS_1']!='')$address_1 = $supplier_data['ADDRESS_1'];else $address_1='';
        if($supplier_data['CONTACT_NO']!='')$contact_no = $supplier_data['CONTACT_NO'];else $contact_no='';
        if($supplier_data['EMAIL']!='')$email = $supplier_data['EMAIL'];else $email='';
        
        $supplier_address = $address_1;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
    }

    $cond="";
    if($data[1]!="") $cond .= " and a.id='$data[1]'";

    if($wo_basis==2){
        $sql_result= sql_select("SELECT a.id as ID, a.currency_id as CURRENCY_ID,b.uom as UOM, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY, b.gross_rate as GROSS_RATE, b.gross_amount as GROSS_AMOUNT, d.item_description as ITEM_DESCRIPTION,d.item_size as ITEM_SIZE, b.item_category_id as ITEM_CATEGORY_ID, b.brand as BRAND_NAME,d.model as MODEL,b.origin as ORIGIN, d.item_group_id, b.remarks , b.requisition_no
        from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d 
        where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");
    }
    else{
        // echo "SELECT a.id as ID, a.currency_id as CURRENCY_ID,b.uom as UOM, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY, b.gross_rate as GROSS_RATE, b.gross_amount as GROSS_AMOUNT, d.item_description as ITEM_DESCRIPTION,d.item_size as ITEM_SIZE, b.item_category_id as ITEM_CATEGORY_ID, b.brand as BRAND_NAME,d.model as MODEL,b.origin as ORIGIN, d.item_group_id, b.remarks , c.requisition_no as REQUISITION_NO
        // from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d , inv_purchase_requisition_mst c
        // where a.id=b.mst_id and b.item_id=d.id and c.id=b.requisition_no and b.is_deleted=0 and b.status_active=1 $cond";

        $sql_result= sql_select("SELECT a.id as ID, a.currency_id as CURRENCY_ID,b.uom as UOM, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY, b.gross_rate as GROSS_RATE, b.gross_amount as GROSS_AMOUNT, d.item_description as ITEM_DESCRIPTION,d.item_size as ITEM_SIZE, b.item_category_id as ITEM_CATEGORY_ID, b.brand as BRAND_NAME,d.model as MODEL,b.origin as ORIGIN, d.item_group_id, b.remarks , c.requ_no as REQUISITION_NO
        from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master d , inv_purchase_requisition_mst c
        where a.id=b.mst_id and b.item_id=d.id and c.id=b.requisition_no and b.is_deleted=0 and b.status_active=1 $cond");
    }


    // echo"<pre>";
    // print_r($sql_result);

    $category_all='';
    foreach($sql_result as $value){
        $category_all.=$value['ITEM_CATEGORY_ID'].',';
    }
    $category_arr=array_unique(explode(",",chop($category_all,',')));
    $category_name='';

    foreach($category_arr as $value){
        $category_name.=$item_category[$value].', ';
    }
    ?>
    <table align="center" cellspacing="0" width="1100" >
        <tr>
            <td width="80" rowspan="2"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
             
            <td colspan="10" style="font-size:xx-large;" align="center"><? echo "<h3>".$com_dtls[0]."</h3>"; ?><strong>PURCHASE ORDER</strong></td>
        </tr>                      
    </table>

    <br>
    <table width="1100">
        <tr>
            <td width="450" valign='top'>
                <table border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="100">Company Name</td> <td width="200"> <? echo $com_dtls[0]; ?> </td>
                    </tr>
                    <tr>
                        <td width="100">Supplier Address</td> <td width="200"><? echo $sql_supplier[0]['ADDRESS_1']; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Attn</td> <td width="200"><? echo $attention; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Cell</td> <td width="200"><? echo $contact_no_mst; ?></td>
                    </tr>
                </table>
            </td>
            <td width="450" >
                <table border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="100">WO Number</td> <td width="200"><? echo $work_order_no; ?></td>
                    </tr>
                    <tr>
                        <td width="100">WO Date</td> <td width="200"><? echo $work_order_date; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Delivery Date</td> <td width="200"><? echo $delivery_date; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Pay Mode</td> <td width="200"><? echo $pay_mode[$pay_mode_id]; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Pay Term</td> <td width="200"><? echo $pay_term[$pay_term_id]; ?></td>
                    </tr>
                    <tr>
                        <td width="100">Tenor</td> <td width="200"><? if($tenor!=null)echo $tenor." days"; ?></td>
                    </tr>
                </table>
            </td>
            <td width="450" valign='top'>
                <table border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="110">Currency</td> <td width="200"><? echo $currency[$currency_id]; ?></td>
                    </tr>
                    <tr>
                        <td width="120">Place to Delivery</td> <td width="200"><? echo $place_of_delivery; ?></td>
                    </tr>
                    <tr>
                        <td width="110">Contact To</td> <td width="200"><? echo $contact_to; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>     

    <table align="center" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="130" align="center">Requisition No</th>
            <th width="100" align="center">Item Category</th>
            <th width="130" align="center">Item Group</th>
            <th width="130" align="center">Description</th>
            <th width="70" align="center">Item Size</th>
            <th width="80" align="center">Brand</th>
            <th width="80" align="center">Origin</th>
            <th width="80" align="center">UOM</th>
            <th width="80" align="center">WO Qty</th>
            <th width="80" align="center">Rate</th>
            <th width="80" align="center">Amount</th>
            <th width="160" align="center">Remarks</th>
        </thead>
        <?
        $total_ammount=0;
        $i=1;
        foreach($sql_result as $row)
        {
            if ($i%2==0)
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";

            $amount=$row['GROSS_AMOUNT'];
            $total_amount+= $amount;

            $brand_model_origin='';
            if($row['BRAND_NAME']!=''){$brand_model_origin.= $row['BRAND_NAME'].", ";}
            if($row['MODEL']!=''){$brand_model_origin.= $row['MODEL'].", ";}
            if($row['ORIGIN']!=''){$brand_model_origin.= $origin_arr[$row['ORIGIN']];}
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td align="center"><? if($row['REQUISITION_NO']==null) echo "Independent"; else echo $row['REQUISITION_NO']; ?></td>
                <td align="center"><? echo  $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
                <td align="center"><? echo  $lib_item_arr[$row['ITEM_GROUP_ID']]; ?></td>
                <td align="center"><? echo  $row['ITEM_DESCRIPTION']; ?></td>
                <td align="center"><? echo  $row['ITEM_SIZE']; ?></td>
                <td align="center"><? echo  $row['BRAND_NAME']; ?></td>
                <td align="center"><? echo  $origin_arr[$row['ORIGIN']]; ?></td>
                <td align="center"><? echo  $unit_of_measurement[$row['UOM']]; ?></td>
                 
                <td align="right"><? echo number_format($row['SUPPLIER_ORDER_QUANTITY'],2); ?></td>                 
                <td align="right"><? echo number_format($row['GROSS_RATE'],2,".",""); ?></td>
                <td align="right"><? echo number_format($row['GROSS_AMOUNT'],2,".","");$tot_amount=$tot_amount+$row['GROSS_AMOUNT']; ?></td>
                <td align="center"><? echo $row["REMARKS"]; ?></td>                                 
            </tr>
        <?php
            $i++;
        }
        ?>
        <tr>
            <td colspan="11" align="right">Total: </td> <td align="right"><? echo number_format($tot_amount,2,".",""); ?></td>
        </tr>
        <tr>         
            <td colspan="11" align="right">Discount: </td> <td align="right"><? echo number_format($discount,2,".",""); ?></td>
        </tr>
        <tr>
            <td colspan="11" align="right">Upcharge: </td> <td align="right"><? echo number_format($up_charge,2,".",""); ?></td>
        </tr>
        <tr>
            <td colspan="11" align="right">Grand Total: </td> <td align="right"><? 
            $tot_with_discount=($tot_amount/100)*(100-$discount);
            $grandTotal=($tot_with_discount/100)*(100+$up_charge);
            echo number_format($grandTotal,2,".",""); ?></td>
        </tr>
    </table>
    <br/>
     
    <br/>
    <table  width="900" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr style="border:1px solid black;">
            <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Conditions</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select terms as TERMS from wo_booking_terms_condition where booking_no='$work_order_no'");
        if (count($data_array) > 0)
        {
            $k=0;
            foreach( $data_array as $row )
            {
                    $k++;
                    echo "<tr id='settr_1' style='border:1px solid black;'> <td style='border:1px solid black;'>
                    $k</td><td style='border:1px solid black;'> ".$row['TERMS']."</td></tr>";

            }
        }
        ?>
        </tbody>
    </table>
     <?
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=17 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=17 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="4">Approval Status</th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="6">Approval / Un-Approval History </th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	?>
       
    <?           
        // echo get_spacial_instruction($work_order_no,"900px");

        //echo signature_table(152, $data[0], "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);   
    exit();
}

if ($action=="spare_parts_work_order_print12")
{
    extract($_REQUEST);
    $data=explode('*',$data);
	$cbo_template_id=$data[7];

    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
   //print_r ($data); die;
    $sql_company=sql_select("SELECT id, company_name, company_short_name, contact_no, plot_no, level_no, road_no, block_no, city, zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $com_name=$sql_company[0][csf("company_name")];
    $com_email=$sql_company[0][csf("email")];
    $com_website=$sql_company[0][csf("website")];
    $com_contact_no=$sql_company[0][csf("contact_no")];

    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

	// $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    // $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    //$lib_location_arr=return_library_array('SELECT id,location_name FROM lib_location','id','location_name' );
    // $contact_person_library= return_library_array('SELECT id,contact_person FROM lib_supplier','id','contact_person');
    // $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    // $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    // $company_library=return_library_array( "select id,company_name from lib_company","id", "company_name"  );
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $group_add=sql_select( "SELECT id,address from lib_group where status_active=1 and address is not null " );

    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, location_id, discount_remarks, insert_date,inserted_by, is_approved, contact  FROM  wo_non_order_info_mst WHERE id = $data[1]");
    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=explode("__",$row[csf("delivery_place")]);
		$inserted_by= $row[csf("inserted_by")];

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $discount_remarks=$row[csf("discount_remarks")];
        $lib_location_arr=$row[csf("location_id")];
        $insert_date = $row[csf("insert_date")];
        $insert_date = $row[csf("insert_date")];
        $contact = $row[csf("contact")];
        $remarks = $row[csf("remarks")];
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
    }

    /* $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
    } */


    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
   {
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

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $i = 0;
    $total_ammount = 0;
    $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id='$data[3]'");
    $remark_data_arr=array();
    foreach($sql_result as $row)
    {
        $remark_data_arr[$row[csf('product_id')]]=$row[csf('remarks')];
    }
    $cond="";
    if($data[1]!="") $cond .= " and a.id='$data[1]'";
    $sql_result= sql_select("SELECT a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code, b.item_category_id, c.requ_no
    from wo_non_order_info_mst a,wo_non_order_info_dtls b 
    left join  inv_purchase_requisition_mst c on b.requisition_no=c.id,product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");
    foreach($sql_result as $row)
    {
        $item_category_nam.=$item_category[$row["ITEM_CATEGORY_ID"]].',';
    }
    ?>

    <div style="padding-left: 10px; width: 950px;">
        <table align="center" cellspacing="0" width="950" >
            <tr>
                <td width="200px"> <img src="../../<? echo $image_location; ?>" height="89" width="89"></td>
                <td width="750px">
                    <table align="center" cellspacing="0" width="450">
                        <tr>
                            <td style="text-align: center;font-size: 25px;"><b><? echo $com_name; ?></b></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;font-size: 15px;"><b>Factory Address: 
                            <?
                                if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
                                if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code;
                            ?>
                            </b></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-size: 15px;"><b>Corporate Office: <?=$group_add[0]['ADDRESS'];?></b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table align="left" cellspacing="0" width="450" border="1" rules="all" class="rpt_table" >
            <tr>
                <td width="220"><b>WO Number: </b><?=$data[4]; ?></td>
                <td><b>WO Date: </b><?=change_date_format($data[5]); ?></td>
            </tr>
            <tr>
                <td width="220"><b>Delivery Date: </b><?=change_date_format($delivery_date); ?></td>
                <td><b>Delivery Location: </b><?=$delivery_place[2]; ?></td>
            </tr>
            <tr>
                <td width="220"><b>Contact Per: </b><?=$delivery_place[3]; ?></td>
                <td><b>Designation: </b><?=$delivery_place[4]; ?></td>
            </tr>
            <tr>
                <td width="220"><b>Cell: </b><?=$delivery_place[5]; ?></td>
                <td><b>Email: </b><?=$delivery_place[6]; ?></td>
            </tr>
        </table>
        <table cellspacing="0" width="950" border="0"><tr><td height="20"></td></tr></table>
        <table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table">
            <tr align="center">
                <th width="470" align="center" ><b>SUPPLIER</b></th>
                <th align="center"> <b>BILLING ADDRESS</b></th>
            </tr>
            <tr>
                <td ><b><?php echo $supplier_name_library[$supplier_id];?></b></td>
                <td><b><?php echo $com_name;?></b></td>
            </tr>
            <tr>
                <td><?php echo "<b>Address:</b> ".$supplier_address; ?></td>
                <td><?php echo "<b>Address:</b> "; 
                    if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
                    if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code;
                ?></td>
            </tr>
            <tr>
                <td><? echo "<b>Attn:</b> ".$attention;?></td>
                <td><?php echo "<b>Cell No:</b> ".$com_contact_no; ?></td>
            </tr>
            <tr>
                <td><?php echo "<b>Cell No:</b> ".$supplier_phone; ?></td>
                <td><?php echo "<b>Email:</b> ". $com_email; ?></td>
            </tr>
            <tr>
                <td><?php echo "<b>Email:</b> ". $supplier_email; ?></td>
                <td></td>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="950" >
            <tr>
                <td ><? echo "<b>Subject :</b> ". " Work order for the supply of ".implode(", ",array_unique(explode(",",chop($item_category_nam,',')))); ?>
                </td>
            </tr>
            <tr>
                <td >&nbsp;</td>
            </tr>
            <tr>
                <td align="left">Dear Sir,<br>Reference to your valid document the management has accepted your price for the following goods/materials as per terms & conditions. Please supply the goods/material within the time limit as stated below.
                </td>
            </tr>
            <tr>
                <td >&nbsp;</td>
            </tr>
        </table>
        <table>
        <tr>
            <td align="center" style="padding-left: 300px"><strong><font size="4px"><? echo "WORK ORDER";?></font></strong></td>
        </tr>
        </table>
        <table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
            <tr align="center">
                <th width="50">SL</th>
                <th width="180" align="center">Requisition No.</th>
                <th width="180" align="center">Item Description</th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Details</th>
                <th width="50" align="center"> UOM</th>
                <th width="70" align="center">Wo.Qty</th>
                <th width="80" align="center">Unit Price</th>
                <th width="95" align="center">Currency</th>
                <th width="95" align="center">Amount</th>
            </tr>
            <?

            $i=1;
            foreach($sql_result as $row)
            {
                if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
                $req_quantity=$row[csf('req_quantity')];
                $req_quantity_sum += $req_quantity;
                $supplier_order_quantity=$row[csf('supplier_order_quantity')];
                $supplier_order_quantityl_sum += $supplier_order_quantity;
                $amount=$row[csf('amount')];
                $amount_sum += $amount;

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="center"><? echo $row[csf('requ_no')]; ?></td>
                    <td><? echo $row[csf('item_description')]; ?></td>
                    <td><? echo $row[csf('item_size')];  ?></td>
                    <td><? echo $row[csf('remarks')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                    <td align="right"><? echo $currency[$currency_id]; ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr >
                <td align="right" colspan="9" ><strong>Grand Total :</strong></td>
                <td align="right"><? echo $word_amount=number_format($amount_sum,2);  ?></td>
                <td></td>
            </tr>
            <tr>
                <td align="right" colspan="9"><span style="float:left;text-align: left;"><b>Upcharge Remarks: </b><? echo $upcharge_remarks;?></span> <span style="float:right;text-align: right;"> Upcharge :</span>&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="9"><span style="text-align:left; float:left;"><b>Discount Remarks: </b><? echo $discount_remarks;?></span>  <span style=" float:right;text-align:right"> Discount : </span>&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
                <td></td>
            </tr>
            <tr>
                <td align="right" colspan="9"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2);  ?></td>
                <td></td>
            </tr>
        </table>
        <table width="950" align="center">
            <tr>
                <td > <strong>Amount in words:</strong>&nbsp;<? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
            </tr>
            <!-- <tr>
                <td > <strong>Remarks:</strong>&nbsp;<? echo $remarks; ?> </td>
            </tr> -->
            <tr>
                <td >&nbsp; </td>
            </tr>
        </table>
        <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
        <br>
        <?  echo get_spacial_instruction($work_order_no,"780px",147);?>
        <table width="950" align="center">
            <tr>
                <td >&nbsp;</td>
            </tr>
            <tr>
                <td >Please maintain product quality and delivery schedule as per above. Your co-operation will be highly appreciated.<br><br> <br> <br>
                Thank you
            </td>
            </tr>
            <tr>
                <td >&nbsp;</td>
            </tr>
        </table>
    </div>
    <?
    echo signature_table(152, $data[0], "780px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
}

if ($action=="spare_parts_work_order_print6")  // print 6
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company=$data[0];
    $location=$data[4];
    echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
    //print_r ($data); die;
	$cbo_template_id=$data[5];
    // $sql_company=sql_select("select id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    // $com_name=$sql_company[0][csf("company_name")];
    // $company_short_name=$sql_company[0][csf("company_short_name")];
    // $plot_no=$sql_company[0][csf("plot_no")];
    // $level_no=$sql_company[0][csf("level_no")];
    // $road_no=$sql_company[0][csf("road_no")];
    // $block_no=$sql_company[0][csf("block_no")];
    // $city=$sql_company[0][csf("city")];
    // $zip_code=$sql_company[0][csf("zip_code")];

    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
    $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
    $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
    $company_library=return_library_array( "select id,company_name from lib_company","id", "company_name"  );
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$data[0]",'bin_no');


	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, remarks, location_id,reference, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, insert_date, is_approved,inserted_by  FROM  wo_non_order_info_mst WHERE id = $data[1]");
    foreach($sql_data as $row)
    {
        $work_order_no=$row[csf("wo_number")];
        $item_category_id=$row[csf("item_category")];
        $supplier_id=$row[csf("supplier_id")];
        $work_order_date=$row[csf("wo_date")];
        $currency_id=$row[csf("currency_id")];
        $wo_basis_id=$row[csf("wo_basis_id")];
        $pay_mode_id=$row[csf("pay_mode")];
        $source=$row[csf("source")];
        $delivery_date=$row[csf("delivery_date")];
        $attention=$row[csf("attention")];
        $requisition_no=$row[csf("requisition_no")];
        $delivery_place=$row[csf("delivery_place")];
        $wo_item_category=$row[csf("item_category")];

        $wo_amount=$row[csf("wo_amount")];
        $up_charge=$row[csf("up_charge")];
        $discount=$row[csf("discount")];
        $net_wo_amount=$row[csf("net_wo_amount")];
        $upcharge_remarks=$row[csf("upcharge_remarks")];
        $insert_date = $row[csf("insert_date")];
		$inserted_by = $row[csf("inserted_by")];
        $is_approved = $row[csf("is_approved")];
        $location_id = $row[csf("location_id")];
        $remarks = $row[csf("remarks")];
        $qut_reference = $row[csf("reference")];

        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
    }

	$req_no_sql="SELECT ID,REQU_NO from  inv_purchase_requisition_mst WHERE ID in ($requisition_no)";
    $req_no_sql_result=sql_select($req_no_sql);
	foreach($req_no_sql_result as $row)
	{
		if($row["REQU_NO"]!="") $req_no.="".$row["REQU_NO"].",";
	}
	$req_no_grp=chop($req_no,",");
    

    ob_start();
    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=22 and status_active=1 and is_deleted=0";
    }
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
    }

    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
    {//contact_no
        $row_mst[csf('supplier_id')];

        if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
        if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
        if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
        if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
        if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].' ';else $contact_no='';
        if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
        if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].' ';else $email='';
        //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
        $country = $supplier_data['country_id'];

        $supplier_address = $address_1;
        $supplier_country =$country;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
    }
    //$quot_ref=return_field_value("requ_no as our_ref","inv_purchase_requisition_mst","id=$data[2]","our_ref" );
    if($db_type==2)
    {
        $quot_ref=return_field_value("LISTAGG(requ_prefix_num , ',') WITHIN GROUP (ORDER BY requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
    }
    else
    {
        $quot_ref=return_field_value("group_concat(requ_prefix_num ) as our_ref","inv_purchase_requisition_mst","id in($data[2])","our_ref" );
    }
    $req_no="";
    $req_no_id=array_unique(explode(",",$quot_ref));
    foreach($req_no_id as $reg_id)
    {
    if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;

    }

    if($db_type==0)
    {
        $quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'","value" );
    }
    else
    {
        $quot_result=sql_select("select value from inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c where a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id='$data[3]'");
        foreach($quot_result as $row)
        {                
            $quot_factor_val .= $row[csf('value')].",";
        }
        $quot_factor_val=implode(",",array_unique(explode(",",chop($quot_factor_val,","))));
    }
    $quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id='$data[3]'","system_id" );
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    $i = 0;
    $total_ammount = 0;
    $com_dtls = fnc_company_location_address($company, $location, 2);
    ?>
        <div align="center" style="padding-left: 10px;  width:780px;">
            <table width="780" align="right" style="font-family: Arial Narrow;">
                <tr>
                    <?
                    $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td align="left" width="210">
                    <?
                    foreach($data_array as $img_row)
                    {
                        if ($formate_id==129)
                        {
                            ?>
                            <img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
                            <?
                        }
                        else
                        {
                            ?>
                            <img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
                            <?
                        }
                    }
                    $req=explode('-',$dataArray[0][csf('requ_no')]);
                    ?>
                    </td>
                    <td align="center" style="font-weight:bold;"><span style="font-size:28px;"><? echo $com_dtls[0]; ?></span><br><span style="font-size:18px;"><?php echo $com_dtls[1]; ?></span></td>
                    <td width="210">&nbsp;</td>
                </tr>
                <tr class="form_caption">
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr class="form_caption">
                    <td>&nbsp;</td>			
                    <td align="center" style="font-size:22px;"><strong><u><? //echo $data[2] ?></u></strong></td>
                    <td align="right" style="font-size:22px;"><strong>Location:&nbsp;<? echo $location_arr[$location]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="3">&nbsp;</td>
                </tr>
            </table>
            <table align="center" cellspacing="0" width="780" >
                <tr>
                    <td colspan="8" style="padding-top: 5px; font-size:x-large;" align="center"><strong><u>Purchase Order</u></strong></td>
                </tr>
                <tr>
                    <td colspan="8" align="center"><strong>
                    <?
                       // echo $com_dtls[1];
                    ?></strong></td>
                </tr>
                <tr>
                    <td colspan="8" align="center"><strong><? ?></strong></td>
                </tr>
            </table>
            
            <div align="center" style="padding-top: 10px; width:780px;">
                <table style="float: left;" width=50%>
                    <tr>
                        <td width="120" align="left" style="font-size:16px"><strong>To,</strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Supplier Name</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left" valign="top"><strong>Address</strong> </td>
                        <td style="font-size:16px" width="5" align="left" valign="top">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left" valign="top"><?
                         echo $supplier_address;  echo "&nbsp;&nbsp";  echo  $lib_country_arr[$country];?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>E-mail</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo $supplier_email; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Phone</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo $supplier_phone; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Attention</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo $attention;; ?> </td>
                    </tr>
                </table>

                <table style="float: left;"  width=50%>
                    <tr>
                        <td style="font-size:16px" width="120" align="left"><strong></strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Date</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo change_date_format($work_order_date); ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left" valign="top"><strong>Quotation Ref.</strong> </td>
                        <td style="font-size:16px" width="5" align="left" valign="top">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left" valign="top"><? echo $qut_reference;?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left" valign="top"><strong>Requisition No</strong> </td>
                        <td style="font-size:16px" width="5" align="left" valign="top">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left" valign="top"><? echo $req_no_grp; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Currency</strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo $currency[$currency_id]; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Location/Unit </strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo  $location_arr[$location_id]; ?> </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px" width="75" align="left"><strong>Wo/Po </strong> </td>
                        <td style="font-size:16px" width="5" align="left">:&nbsp;</td>
                        <td style="font-size:16px" width="220" align="left"><? echo  $work_order_no; ?> </td>
                    </tr>
                </table>
            </div>


            <table align="center" cellspacing="0" width="780" >
                <tr>
                    <td style="font-size:16px" colspan="8" align="left"><br><br>Dear Sir,<br><? echo $remarks;?> 
                    </td>
                </tr>
                <tr>
                    <td style="font-size:16px" colspan="8" >&nbsp;</td>
                </tr>
            </table>
            <table align="center" cellspacing="0" width="780"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th style="font-size:16px" width="50">SL</th>
                    <th style="font-size:16px" width="200" align="center">Item Description</th>
                    <th style="font-size:16px" width="90" align="center">Quantity</th>
                    <th style="font-size:16px" width="90" align="center">UOM</th>
                    <th style="font-size:16px" width="110" align="center">Rate</th>
                    <th style="font-size:16px" width="125" align="center">Amount</th>
                    <th style="font-size:16px" width="100" align="center">Remarks</th>
                </thead>
                <?
                //$reg_no=explode(',',$data[11]);
                $sql_result=sql_select("select b.product_id as product_id,b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id='$data[3]'");
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

                $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code
                from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                where a.id=b.mst_id and b.item_id=d.id and b.is_deleted=0 and b.status_active=1 $cond");

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
                    $amount=$row[csf('amount')];
                    $amount_sum += $amount;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td style="font-size:16px" align="center"><? echo $i; ?></td>
                        <td style="font-size:16px"><? echo $row[csf('item_description')]; ?></td> 
                        <td style="font-size:16px" align="center"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                        <td style="font-size:16px"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td style="font-size:16px" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                        <td style="font-size:16px" align="right"><? echo number_format($row[csf('amount')],2); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?></td>
                        <td style="font-size:16px"><? echo $row[csf('remarks')]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr >
                    <td style="font-size:16px" align="right" colspan="5" ><strong>Total Amount (Tk.) :</strong></td>
                    <td style="font-size:16px" align="right"><? echo $word_amount=number_format($amount_sum,2);  ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px" align="right" colspan="5"><strong>Upcharge :</strong></td>
                    <td style="font-size:16px" align="right"><? echo number_format($up_charge,2);  ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px" align="right" colspan="5"><strong>Discount :</strong></td>
                    <td style="font-size:16px" align="right"><? echo number_format($discount,2);  ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px" align="right" colspan="5"><strong>Net Payable Amount (Tk.) : </strong></td>
                    <td style="font-size:16px" align="right"><? echo number_format($net_wo_amount,2);  ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px" colspan="7"><strong>In words :</strong>&nbsp; <? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent);  ?>&nbsp;&nbsp;</td>
                </tr>
            </table>

            <br>
            <div style="color:#F00 ;font-size:x-large; text-align: center; margin-top: 10px"><font><? echo $approved_msg ?> </font></div>
            <table width="780" >
                <tr>
                    <td style="font-size:16px; border: none;" align="left"><u>Terms and Conditions :</u></td>
                </tr>
            </table>
            <?
            $width = 780;
            $html='
            <table  width='.$width.' cellpadding="0" cellspacing="0" rules="all">
            <tbody>';
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$work_order_no)."'");// quotation_id='$data'
                if ( count($data_array)>0)
                {
                    $i=0;
                    foreach( $data_array as $row )
                    {
                        $i++;
                        $html.='
                        <tr id="settr_1" align="" style="border: none;">
                        <td valign="top" style="border: none;">'.$i.'. '.'</td>
                        <td valign="top" style="border: none;"> '.$row[csf('terms')].'</td>
                        </tr>';
                    }
                }    
            $html.='
            </tbody>
            </table>';
            echo $html;
            //echo get_spacial_instruction($work_order_no,"780px",147);?>
            <table width="780" align="center">
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td style="font-size:16px" colspan="8">Thanks with Best Regards,<br><br>
                </td>
                </tr>
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
            </table>
            <table align="center" style="width:780px;">
                <?
                echo signature_table(152, $data[0], "780px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
                ?>
            </table>
        </div>
        <?


        $mailBody=ob_get_contents();
        ob_clean();
        echo $mailBody;
        $mail_data = $data[6];
        $cbo_company_id = $data[0];

        //Mail send------------------------------------------
        list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

        if($is_mail_send==1){
        // require_once('../../../mailer/class.phpmailer.php');
            include('../../../auto_mail/setting/mail_setting.php');

            $mailToArr=array();
            if($msil_address){$mailToArr[]=$msil_address;}

            //-----------------------------
            $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  and a.entry_form=17 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
            $elcetronicSqlRes=sql_select($elcetronicSql);
            foreach($elcetronicSqlRes as $rows){
                if($rows['SEQUENCE_NO']==1 && $rows['BYPASS']==2){
                    if($rows['USER_EMAIL']){$mailToArr[100]=$rows['USER_EMAIL'];}
                }
                $elecDataArr[$rows['BYPASS']][]=$rows['USER_EMAIL'];
            }
            
            if($elecDataArr[1][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[1][0];}
            elseif($elecDataArr[2][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[2][0];}

            $to=implode(',',$mailToArr);
            
            $subject="Others Purchase Order";
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br>".$mailBody, $from_mail,$att_file_arr );
        }
        exit();

}


if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=30 and is_deleted=0 and status_active=1");
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

if ($action=="spare_parts_work_order_po_print2")
{
    // echo "PO Print 2";die;
    extract($_REQUEST);
    $data = explode('*',$data);
    $company = $data[0];
    $mst_id = $data[1];
    $rpt_title = $data[2];
    //    $req_numbers_id = $data[3];
    $template_id = $data[3];

    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?
    $currency_sign_arr = array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    $company_library = return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
    $user_lib_name = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_designation_id = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $lib_designation_arr = return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    $data_result = sql_select("SELECT wo_non_order_info_mst.wo_number as WO_NUMBER, wo_non_order_info_mst.wo_type AS WO_TYPE, wo_non_order_info_mst.PAYTERM_ID as PAYTERM_ID, wo_non_order_info_mst.wo_date as WO_DATE, wo_non_order_info_mst.supplier_id as SUPPLIER_ID, wo_non_order_info_mst.pay_mode as PAY_MODE, wo_non_order_info_mst.currency_id as CURRENCY_ID, wo_non_order_info_mst.attention as ATTENTION,wo_non_order_info_mst.is_approved as IS_APPROVED, wo_non_order_info_mst.delivery_place as DELIVERY_PLACE, delivery_date as DELIVERY_DATE, wo_non_order_info_mst.inserted_by as INSERTED_BY, to_char(wo_non_order_info_mst.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, wo_non_order_info_mst.remarks as REMARKS, wo_non_order_info_mst.location_id as LOCATION_ID, user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, wo_non_order_info_mst.up_charge as UP_CHARGE, wo_non_order_info_mst.discount as DISCOUNT, wo_non_order_info_mst.upcharge_remarks as UPCHARGE_REMARKS, wo_non_order_info_mst.discount_remarks as DISCOUNT_REMARKS from wo_non_order_info_mst left join user_passwd on user_passwd.id = wo_non_order_info_mst.inserted_by left join lib_designation on lib_designation.id = user_passwd.designation where wo_non_order_info_mst.id=$mst_id");
    $insert_date=$data_result[0]['INSERT_DATE'];
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $currency_id=$data_result[0]['CURRENCY_ID'];
    $is_approved=$data_result[0]['IS_APPROVED'];
    if($is_approved==1){ $approved_status="Full Approved";}
    else if($is_approved==0){ $approved_status="Not Approved";}
    else{ $approved_status="Partial Approved";}

    $sql_company=sql_select("SELECT lib_company.id, lib_company.company_name, lib_company.company_short_name, lib_company.contact_no, lib_location.address from lib_location left join lib_company on lib_company.id = lib_location.company_id where lib_company.status_active = 1 and lib_company.is_deleted = 0 and lib_company.id = $company and lib_location.id = ".$data_result[0]['LOCATION_ID']);

    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $location_address = $sql_company[0][csf("address")];
    $phone_no=$sql_company[0][csf("contact_no")];

    $supplier_sql=sql_select("SELECT supplier_name,address_1,contact_person,contact_no,email from lib_supplier where id=".$data_result[0]['SUPPLIER_ID']);

    $getReqNumber = sql_select("select requisition_no from wo_non_order_info_mst where id = $mst_id");
    if(count($getReqNumber) > 0){
        $req_numbers_id =  $getReqNumber[0][csf('requisition_no')];
    }else{
        $req_numbers_id = 0;
    }

    $req_details_info_sql = sql_select("select inv_purchase_requisition_mst.id AS ID, inv_purchase_requisition_mst.requ_no AS REQU_NO, lib_store_location.store_name AS STORE_NAME from inv_purchase_requisition_mst LEFT JOIN lib_store_location ON lib_store_location.id = inv_purchase_requisition_mst.store_name WHERE inv_purchase_requisition_mst.id IN ($req_numbers_id)");

    $req_numbers_container = [];
    foreach ($req_details_info_sql as $req_key => $req_data){
        $req_numbers_container[$req_data['REQU_NO']] = $req_data['STORE_NAME'];
    }
    $electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 628 and company_id=$company and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("SELECT user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 17 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 17 and mst_id = $mst_id) and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 17 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");

    // $partial_approval_sql=sql_select("select b.ID, b.APPROVED_BY, b.APPROVED_DATE, b.APPROVED from wo_non_order_info_mst a, approval_history b where a.id=b.mst_id and b.mst_id=$mst_id and b.entry_form=17 and a.is_approved!=0 and b.approved=3 order by id desc fetch first 1 rows only");

    $partial_approval_sql=sql_select("select b.ID, b.APPROVED_BY, b.APPROVED_DATE from wo_non_order_info_mst a, APPROVAL_MST b where  b.mst_id=$mst_id and b.entry_form=17 and b.SEQUENCE_NO in(1,2)  order by id desc fetch first 1 rows only");
    
    $partial_user_name=$partial_user_desig=$partial_user_appr_date='';
    foreach ($partial_approval_sql as $row) {
        $partial_user_name=$user_lib_name[$row['APPROVED_BY']];
        $partial_user_desig=$lib_designation_arr[$user_designation_id[$row['APPROVED_BY']]];
        $partial_user_appr_date=$row['APPROVED_DATE'];
    }

    $approval_sql=sql_select("select b.APPROVED_BY, b.APPROVED_DATE from wo_non_order_info_mst a, approval_history b where a.id=b.mst_id and b.mst_id=$mst_id and b.entry_form=17 and a.is_approved=1 and a.status_active=1 and b.current_approval_status=1");
    $approve_user_name=$approve_user_desig=$approve_user_appr_date='';
    foreach ($approval_sql as $row) {
        $approve_user_name=$user_lib_name[$row['APPROVED_BY']];
        $approve_user_desig=$lib_designation_arr[$user_designation_id[$row['APPROVED_BY']]];
        $approve_user_appr_date=$row['APPROVED_DATE'];
    }

    ob_start();
    ?>

    <div style="width:930px;">
        <table align="center" cellspacing="0" width="910" >
            <tbody>
            <tr>
                <td width="600" style="font-size:24px;" ><strong><? echo $company_library[$company];  ?></strong></td>
                <td rowspan="2"><strong style="font-size:24px; border: 1px dashed #000; padding: 2px 4px;"><?=$rpt_title;?></strong></td>
            </tr>
            <tr>
                <td style="font-size:20px; padding-right: 40px; vertical-align: top;" ><?=$location_address?></td>
            </tr>
            <tr>
                <td style="font-size:20px; vertical-align: top;" ><? echo "Phone No: ".$phone_no; ?></td>
                <td style="font-size:18px; vertical-align: top;"><strong>Work Order Type: </strong><?=isset($wo_type_array[$data_result[0]['WO_TYPE']]) ? $wo_type_array[$data_result[0]['WO_TYPE']] : ''?><br><strong>Pay Term: </strong><?=isset($pay_term[$data_result[0]['PAYTERM_ID']]) ? $pay_term[$data_result[0]['PAYTERM_ID']] : ''?></td>
            </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="910" >
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
                <td width="180" align="center" rowspan="5" style="font-size:18px;color:red;"><?=$approved_status;?> </td>
                <td rowspan="5" style="vertical-align: top; font-size:18px;">
                    <?php
                    $format_delivery_address = explode('_', $data_result[0]['DELIVERY_PLACE']);
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
                <td style="font-size:18px;" >Address</td>
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
        <div style="border: 1px solid black; width:910px;">
            <table align="center"  cellspacing="0" width="910" >
                <tbody>
                <tr>
                    <td width="150" style="font-size:18px;"><b>REQ Number</b></td>
                    <td width="20" >:</td>
                    <td width="350" style="font-size:18px;"><?=implode(', ', array_keys($req_numbers_container))?></td>
                    <td width="150" style="font-size:18px;"><b>Delivery Date </b></td>
                    <td width="20" >:</td>
                    <td width="210" style="font-size:18px;"><? echo change_date_format($data_result[0]['DELIVERY_DATE']); ?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;"><b>Order Number</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><? echo $data_result[0]['WO_NUMBER']; ?></td>
                    <td style="font-size:18px;"><b>Pay Mode</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;"><b>Order Date</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><? echo change_date_format($data_result[0]['WO_DATE']); ?></td>
                    <td style="font-size:18px;"><b>Currency</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;"><b>Notes</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><?=$data_result[0]['REMARKS']; ?></td>
                    <td style="font-size:18px;"><b>Warehouse</b></td>
                    <td width="20" >:</td>
                    <td style="font-size:18px;"><?=implode(', ', array_unique(array_values($req_numbers_container)))?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <table align="left" cellspacing="0" width="911.5"  border="1" rules="all" class="rpt_table" >
            <thead>
            <tr></tr>
            <th width="30" style="font-size:18px;">SL</th>
            <th width="175" style="font-size:18px;" >Item Description</th>
            <th width="80" style="font-size:18px;" >Model</th>
            <th width="90" style="font-size:18px;" >Brand</th>
            <th width="90" style="font-size:18px;" >Item Size</th>
            <th width="45" style="font-size:18px;" >Unit</th>
            <th width="80" style="font-size:18px;">Qty</th>
            <th width="130" style="font-size:18px;">Unit Price</th>
            <th width="150" style="font-size:18px;">Amount</th>
            <th width="130.5" style="font-size:18px;">Remarks</th>
            </thead>
            <tbody>
            <?

            $sql_dtls= "SELECT a.id, a.supplier_order_quantity as WO_QNTY, a.item_size as ITEM_SIZE, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.model as MODEL, b.brand_name AS BRAND_NAME, a.uom as UOM from wo_non_order_info_dtls a left join product_details_master b on a.item_id=b.id and b.status_active=1
            where a.mst_id=$mst_id and a.status_active=1 ";

            $sql_result= sql_select($sql_dtls);
            // echo "<pre>"; print_r($sql_result); die;
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
                    <td style="font-size:18px;" ><? echo $row['ITEM_SIZE']; ?></td>
                    <td style="font-size:16px;" ><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                    <td style="font-size:18px;" align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                    <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$currency_id].' '.number_format($row['RATE'],4,".",","); ?></td>
                    <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$currency_id].' '.number_format($row['AMOUNT'],2,".",","); ?></td>
                    <td style="font-size:18px;" >&nbsp;<? echo $row['REMARKS']; ?></td>
                </tr>
                <?php
                $tot_wo_amount += $row['AMOUNT'];
                $i++;
            }
            ?>
            <tr>
                <td colspan="3" style="font-size:18px;"><strong>Upcharge Remarks</strong></td>
                <td colspan="4" style="font-size:18px;"><?=$data_result[0]['UPCHARGE_REMARKS']?></td>
                <td style="font-size:18px;" align="right"><strong>Upcharge :</strong></td>
                <td style="font-size:18px;" align="right"><?=$currency_sign_arr[$currency_id].' '.number_format($data_result[0]['UP_CHARGE'], 2, '.', ',')?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" style="font-size:18px;"><strong>Discount Remarks</strong></td>
                <td colspan="4" style="font-size:18px;"><?=$data_result[0]['DISCOUNT_REMARKS']?></td>
                <td style="font-size:18px;" align="right"><strong>Discount :</strong></td>
                <td style="font-size:18px;" align="right"><?=$currency_sign_arr[$currency_id].' '.number_format($data_result[0]['DISCOUNT'], 2, '.', ',')?></td>
                <td></td>
            </tr>
            <tr>
                <td  colspan="8" align="right" style="font-size:18px;"><strong>Total :</strong></td>
                <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$currency_id].' '.number_format($tot_wo_amount+$data_result[0]['UP_CHARGE']-$data_result[0]['DISCOUNT'], 2, '.', ','); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="9" style="font-size:18px;"><strong> Amount (in word):&nbsp;</strong><?echo number_to_words(number_format(($tot_wo_amount+$data_result[0]['UP_CHARGE'])-$data_result[0]['DISCOUNT'], 2, '.', ''),$currency[$currency_id]);?></td>
            </tr>
            </tbody>
        </table>
        <br/>
        <?echo get_spacial_instruction($data_result[0]['WO_NUMBER'],"911.5px", 147);?>
        <br/>

        <div class="signature" style="margin-left: -13%;">
            <?
            $sigDataArr = array('report_id' => 152,'company_id' => $company,'template_id' => $template_id,'width' => 1100,'padding_top' => 20,'break_tr' => 7,'app_entry_form' => 17,'sys_id' => $mst_id,'prepared_by' => $inserted_by,'prepared_date'=>$insert_date);
            get_group_app_signature($sigDataArr);
            ?>
        </div>
        
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?


        $mailBody=ob_get_contents();
        ob_clean();
        echo $mailBody;
        $mail_data = $data[6];
        $cbo_company_id = $data[0];

        //Mail send------------------------------------------
        list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

        if($is_mail_send==1){
        // require_once('../../../mailer/class.phpmailer.php');
            include('../../../auto_mail/setting/mail_setting.php');
        
            $mailToArr=array();
            if($msil_address){$mailToArr[]=$msil_address;}

            //-----------------------------
            $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  and a.entry_form=17 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
            $elcetronicSqlRes=sql_select($elcetronicSql);
            foreach($elcetronicSqlRes as $rows){
                if($rows['SEQUENCE_NO']==1 && $rows['BYPASS']==2){
                    if($rows['USER_EMAIL']){$mailToArr[100]=$rows['USER_EMAIL'];}
                }
                $elecDataArr[$rows['BYPASS']][]=$rows['USER_EMAIL'];
            }
            
            if($elecDataArr[1][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[1][0];}
            elseif($elecDataArr[2][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[2][0];}

            $to=implode(',',$mailToArr);
            
            $subject="Purchase Order- General Purchase";
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br>".$mailBody, $from_mail,$att_file_arr );
        }

    exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
        function js_set_value(val)
        {
            document.getElementById('text_new_remarks').value=val;
            parent.emailwindow.hide();
        }
        </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:400px;margin-left:4px;">
            <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="370" >
                    <tr>
                        <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                          <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:370px; height:250px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                     <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
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



if($action=="get_first_selected_print_button")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=30 and is_deleted=0 and status_active=1");
	$dataArr=explode(',',$print_report_format);
	echo $dataArr[0];
    exit();
}



?>
