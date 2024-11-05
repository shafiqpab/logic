<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$mrr_date_check="";
if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
}


// load location
if ($action=="load_drop_down_location")
{
	
    $sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
    $nameArray=sql_select($sql);
    $location_no=count($nameArray);
	
    if($location_no==1)
    {
        $selected=$nameArray[0][csf("id")];
    }
    else
    {
        $selected=0;
    }
    //$data=explode("_",$data);
    echo create_drop_down( "cbo_location", 150, $sql,"id,location_name", 1, "-- Select Location --", $selected,"");
    exit();
}
//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_supplier")
{
	//if($data==4) $prty_type=5; else if($data==11) $prty_type=8;
	//echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in($prty_type) and a.status_active=1 and a.is_deleted=0  group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
    $data=explode("_",$data);
    if($data[1]==3 || $data[1]==5){
        echo create_drop_down( "cbo_supplier", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );
    }else{
        echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(5,8) and a.status_active=1 and a.is_deleted=0 and c.tag_company ='$data[0]' group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/stationary_work_order_controller');",0 );
    }
    
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
                                
                                    if($cbo_pay_mode==3 || $cbo_pay_mode==5){
                                        $data_sql=sql_select("SELECT comp.id as ID, comp.company_name as SUPPLIER_NAME from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name");
                                    }
                                    else
                                    {
                                        if($mst_id=="")
                                        {
                                         $data_sql=sql_select("SELECT a.id as ID,a.supplier_name as SUPPLIER_NAME from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(8) and a.status_active=1 and a.is_deleted=0 and c.tag_company =$cbo_company_name group by a.id, a.supplier_name order by a.supplier_name");
                                       }else{
                                        $data_sql=sql_select( "SELECT distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c 
										where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(8) and c.tag_company in($cbo_company_name) and a.status_active IN(1) and a.is_deleted=0 
										union all
										select distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c, WO_NON_ORDER_INFO_MST d 
										where a.id=b.supplier_id and a.id=c.supplier_id and a.id = d.supplier_id and b.party_type in(8) and c.tag_company in($cbo_company_name) and a.status_active IN(1,3) and a.is_deleted=0 and d.id = $mst_id
										order by supplier_name");
                                       }
                                    }
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
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date,'yyyy-mm-dd')."' and company_id='$data')) and page_id=16 and status_active=1 and is_deleted=0 order by id desc";
    }else{
        $necessity_setup_sql ="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($date, "", "",1)."' and company_id='$data')) and page_id=16 and status_active=1 and is_deleted=0 order by id desc";
    }
    $necessity_setup_res=sql_select($necessity_setup_sql);
    $necessity_setup=$necessity_setup_res[0][csf("approval_need")];
    // $necessity_setup=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_importer_id and variable_list=23 and status_active=1","source");
    
    echo $necessity_setup;die;
}

if ($action=="load_details_container"){   //Accories & Stationary

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


			<table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all">
					<thead>
						<tr>
							 <th class="must_entry_caption">Item Group</th>
                            <th>Item Account</th>
							<th class="must_entry_caption">Item Description</th>
                            <th>Item Category</th>
							<th>Item Size</th>

                            <th>Brand</th>
                            <th>Origin</th>
                            <th>Model</th>
                            <th>Buyer</th>
                            <th>Season</th>
							<th>Order UOM</th>
							<th class="must_entry_caption">Quantity</th>
							<th class="must_entry_caption">Rate</th>
							<th class="must_entry_caption">Amount</th>
                            <th>Remarks</th>
                            <th>Action</th>
						</tr>
					</thead>
                   	<tbody>
                        <tr class="general" id="<? echo $i;?>">
                        <td width="110"  align="center">
                                <?
                                    echo create_drop_down( "cbogroup_".$i, 100, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1 );
                                ?>
                            </td>
                            <td width="130" align="center">
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

                            <td width="140"  align="center">
                                <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:130px" <? echo $itemDescDoubleClick; ?> />
                            </td>
                            <td width="80"  align="center">
                                <?
                                //echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", "", "",1,"4,11" );
								echo create_drop_down( "cbo_item_category_".$i,80, $item_category,'', 1, '-- Select --',"","",1,"4,11","","","","","","cbo_item_category[]","cbo_item_category_".$i);
                                ?>
                            </td>

                            <td width="100"  align="center">
                                <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                            </td>

                            <td width="70"  align="center">
                                <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px"/>
                            </td>
                            <td width="70"  align="center">
                                <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i );?>
                            </td>
                            <td width="70"  align="center">
                                <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px"/>
                            </td>
                           <td width="60"  align="center">
                                 <?
							   	echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-Select-",0, "",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

 							?>
                            </td>
                            <td width="60"  align="center">
                                <?
							   	echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-Select-",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

 							?>
                          </td>
                            <td width="70"  align="center">
                                <?
                                    echo create_drop_down( "cbouom_".$i, 70, $unit_of_measurement,"", 1, "Select", 0, "",1,"","","","","","","cbouom[]","cbouom_".$i );

                                ?>
                            </td>
                            <td width="70"  align="center">
                                <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:70px;"/>
                            </td>
                            <td width="50"  align="center">
                                <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                            </td>
                            <td width="70"  align="center">
                                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:70px;" readonly />
                            </td>
                            <td width="100"  align="center">
                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
                            </td>
                            <td  align="center">
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

                            <td>Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:70px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="8">Upcharge Remarks:</td>
                            <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Upcharge</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:70px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="8">Discount Remarks:</td>
                            <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $val[csf("discount")];?>" style="width:70px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>                           
                        </tr>
                        <!-- <tr>
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
                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:70px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr> -->
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
                            <td>Net Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:70px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
				</table>
           <!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>*/-->

	<?
		exit();
	}
	else //requisition container  header
	{
		?>
			<div style="width:1500px;">
                <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all">
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
                                <th>Model</th>
                           		<th>Buyer</th>
                                <th>Season</th>
                                <th>Order UOM</th>
                                <th>Req.Qnty</th>
                                <th>WO.Qnty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<!-- append here -->
                        </tbody>
                </table>
                <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
			</div>
		<?
		exit();
	}

}
if ($action=="append_load_details_container"){
	//echo $data;
 	$explodeData = explode("**",$data);
	$i = $explodeData[0];
	$company = $explodeData[1];
	//$category = $explodeData[2];
	//echo $i;
	$user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company'  and status_active=1 and is_deleted=0");
	$itemAcctDoubleClick="";$itemDescDoubleClick="";
	if($user_given_code_status==1)
		$itemAcctDoubleClick = 'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';
	else
		$itemDescDoubleClick = 	'onDblClick="itemDetailsPopup()" placeholder="Double Click To Search"';

	?>
				<tr class="general" id="<? echo $i;?>">
                    <td width="110">
                    <?
                    echo create_drop_down( "cbogroup_".$i, 100, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", 0, "",1,"","","","","","","cbogroup[]","cbogroup_".$i );

                    ?>
                    </td>
                    <td width="130">
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
                    <td width="140">
                    <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:130px" <? echo $itemDescDoubleClick; ?> />
                    </td>
                    <td width="80"  align="center">
                    <?
                    echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", "", "",1,"4,11","","","","","","cbo_item_category[]","cbo_item_category_".$i );

                    ?>
                    </td>

                    <td width="100">
                    <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                    </td>


                    <td width="70"  align="center">
                    <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" readonly value="<? echo $val[csf("brand")];?>" />
                    </td>
                    <td width="70"  align="center">
                    <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i );?>
                    </td>
                    <td width="70"  align="center">
                    <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" readonly value="<? echo $val[csf("model")];?>" />
                    </td>
                    <td width="60"  align="center">
                    <?
                    //$company=$val[csf("company_name")];$buyer_id=$val[csf("buyer_id")];$season_id=$val[csf("season_id")];
                    echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",0, "load_drop_down( 'requires/stationary_work_order_controller', this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i'); ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

                    ?>
                    </td>
                    <td width="60" id="season_td_<? echo $i;?>"  align="center">
                    <?
                    echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-- Select --",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );
                    ?>
                    </td>
                    <td width="50">
                    <?
                    echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", 0, "",1,"","","","","","","cbouom[]","cbouom_".$i );

                    ?>
                    </td>
                    <td width="70">
                    <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:80px;"/>
                    </td>
                    <td width="50">
                    <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="70">
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
                    </td>
                    <td width="100"  align="center">
                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:90px" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
                    </td>
                    <td>
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
                        <th>Item group</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                	<tr>
                        <td>
                        <?
                        echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "-- Select --", "", "",0,"4,11" );
                        ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td>
                        <td align="center">
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value+'**'+document.getElementById('cbo_string_search_type').value, 'account_order_popup_list_view', 'search_div', 'stationary_work_order_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
?>

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
            if($item_group!=""){$search_con .= " and b.item_name like'%$item_group%'";}
        }
        else if($search_type==1)
        {

            if($item_description!=""){$search_con=" and a.item_description = '$item_description'";}
            if($item_code!=""){$search_con .= " and a.item_code = '$item_code'";}
            if($item_group!=""){$search_con .= " and b.item_name = '$item_group'";}
        }
        else if($search_type==2)
        {

            if($item_description!=""){$search_con=" and a.item_description like '$item_description%'";}
            if($item_code!=""){$search_con .= " and a.item_code like '$item_code%'";}
            if($item_group!=""){$search_con .= " and b.item_name like '$item_group%'";}
        }
        else if($search_type==3)
        {

            if($item_description!=""){$search_con=" and a.item_description like '%$item_description'";}
            if($item_code!=""){$search_con .= " and a.item_code like '%$item_code'";}
            if($item_group!=""){$search_con .= " and b.item_name like '%$item_group'";}
        }

        if($itemCategory){$search_con .= " and item_category_id='$itemCategory'";}

		  if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
		  $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,

		  //$entry_cond="";
		  //if(str_replace("'","",$itemCategory)==4) $entry_cond="and a.entry_form=20";
		  //$sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, b.order_uom from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.is_deleted=0 and company_id='$company' and a.item_category_id in (4,11) $itemIDScond $entry_cond $search_con";

		  $sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom 
		  from product_details_master a, lib_item_group b 
		  where a.item_group_id=b.id and a.is_deleted=0 and company_id='$company' and a.item_category_id in (4,11) and a.entry_form<>24 $itemIDScond $search_con";
		  //echo $sql;//die;
		  echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,ReOrder Labale,Product ID,Status", "120,100,140,80,100,80,80,100,50,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','',1 );

    ?>
      	</fieldset>
      </form>
	 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if ($action=="load_php_popup_to_form_asd")
{
	$explode_data = explode("**",$data);
	$data_id=$explode_data[0];
	//echo $total_row=$explode_data[1];
	//$item=$explode_data[1];

	$i=$explode_data[1];
	$company=$explode_data[2];

    if($data_id!="")
	{
		//echo "select a.id,a.item_account,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name from product_details_master a,lib_item_group b where a.id in ($data_id) and a.status_active=1 and a.item_group_id=b.id";
		//echo "select a.id,a.item_account,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name from product_details_master a,lib_item_group b where a.id in ($data_id) and a.status_active=1 and a.item_group_id=b.id";die;
		$nameArray=sql_select( "select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, a.brand_name as brand, a.origin, a.model, b.item_name, a.order_uom 
		from product_details_master a, lib_item_group b 
		where a.id in ($data_id) and a.status_active=1 and a.item_group_id=b.id");
		//var_dump($nameArray);

		//$user_given_code_status=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$company' and item_category_id='$category' and status_active=1 and is_deleted=0");
		$origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name");

		foreach ($nameArray as $val)
		{
		?>
			<tr class="general" id="<? echo $i;?>">
                <td width="110"  align="center">
				<?
				echo create_drop_down( "cbogroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$val[csf("item_group_id")], "",1,"","","","","","","cbogroup[]","cbogroup_".$i );
				//echo $val[csf("item_group_id")];

                ?>
                </td>
                <td width="130" align="center">
                    <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_account")];?>" />
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                     <!-- Only for show. not used for Independent -->
                    <input type="hidden" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="" readonly />
                    <input type="hidden" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" readonly value="" />
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="" />
                    <!----------------- END ------------------------>
                </td>
                <td width="140"  align="center">
                    <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $val[csf("item_description")];?>"  readonly />
                </td>
                <td width="80"  align="center">
                    <?
                    echo create_drop_down( "cbo_item_category_".$i, 80, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,"4,11","","","","","","cbo_item_category[]","cbo_item_category_".$i );

                    ?>
                </td>

                <td width="100"  align="center">
                    <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $val[csf("item_size")];?>" readonly />
                </td>

                 <td width="70"  align="center">
                    <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("brand")];?>" readonly />
                </td>
                <td width="70"  align="center">
                    <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i ); ?>                    
                </td>
                <td width="70"  align="center">
                    <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("model")];?>" readonly />
                </td>
                 <td width="70"  align="center">
					 <?
                    echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",0, "load_drop_down( 'requires/stationary_work_order_controller', this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i'); ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

                ?>
                </td>
                 <td width="70" id="season_td_<? echo $i;?>"  align="center">
                    <?
                    echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-- Select --",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

                ?>
              </td>
                <td width="50"  align="center">
                    <?
                        echo create_drop_down( "cbouom_".$i, 50, $unit_of_measurement,"", 1, "Select", $val[csf("order_uom")], "",1,"","","","","","","cbouom[]","cbouom_".$i );
                    ?>
                </td>
                <td width="70"  align="center">
                    <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:80px;" />
                </td>
                <td width="50"  align="center">
                    <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:70px;" />
                </td>
                <td width="70"  align="center">
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
                </td>
                <td width="100">
                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:90px" value="" />
                </td>
                <td >
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

	function js_set_value( strParam )
	{
			var splitArr = strParam.split("_");
			var str = splitArr[0];
			var numbers = splitArr[1];
			var ids = splitArr[2]; //requisition id
            var req_dtls_id = splitArr[3];  // item id
			var is_approved = splitArr[4]; 
			var approval_need = splitArr[5];  // is_approved
            var allow_partial = splitArr[6];  // is_approved 
            if (is_approved==0  && approval_need == 1) {
                alert("Please Approve First...");
                return;
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

			var num =''; var id = '';
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
	<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
									echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", "", "","","4,11" );
								?>
                    		</td>
                            <td  align="center"> <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px;" ></td>
                            <td>
                                <?
                                if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
                                else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
                                $approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=13 and status_active=1 and is_deleted=0";   
                                $app_need_setup=sql_select($approval_status);
								$approval_need=$app_need_setup[0][csf("approval_need")];
                                $allow_partial=$app_need_setup[0][csf("allow_partial")];
                                echo create_drop_down( "cbo_approval_type", 100, $yes_no, "", 1, "-- Select--", $approval_need, "","","" );
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
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $txt_req_dtls_id; ?>'+'_'+'<? echo $garments_nature; ?>'+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('cbo_approval_type').value+'_'+'<? echo $approval_need; ?>'+'_'+'<? echo $allow_partial; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_requisition_search_list_view', 'search_div', 'stationary_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:100px;" />
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
	//echo "nazim"; die;
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
    $approval_need=$ex_data[8];
	$allow_partial=$ex_data[9];
	$reqsition_year=$ex_data[10];

	$sql_cond="";
 	if($companyName!=0)
		$sql_cond = " and a.company_id = '".$companyName."'";
	if($itemCategory!=0)
		$sql_cond .= " and b.item_category = '".$itemCategory."'";

 	/*if($txt_date_from!="" || $txt_date_to!="")
		$sql_cond .= " and a.requisition_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";*/

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
    if ($approval_type==1) 
    {
    	if ($allow_partial==2) $approval_cond=" and a.is_approved in(1)";
    	else $approval_cond=" and a.is_approved in(1,3)";
    }
    if ($approval_type==2) $approval_cond=" and a.is_approved=0";

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$reqsition_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$reqsition_year.""; }

	if($req_dtls_id=="") $req_dtls_id=0;
 	$prev_req_wo=return_library_array("SELECT requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity from  wo_non_order_info_dtls where status_active=1 and requisition_dtls_id>0 and requisition_dtls_id not in($req_dtls_id) group by requisition_dtls_id ","requisition_dtls_id","supplier_order_quantity");

  	//if($req_dtls_id!="") $sql_cond .= " and b.id NOT IN ($req_dtls_id)";
 	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));

    //"select b.approval_need from approval_setup_mst a, approval_setup_dtls b where a.id = b.mst_id and b.page_id = 13 and a.company_id = $companyName and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $companyName )";

    /*$approval_cond='';    
    if ($approval_type==1) $approval_cond=" and a.is_approved in(1,3)";
    if ($approval_type==2) $approval_cond=" and a.is_approved=0";*/
    /*$approval_need=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id = b.mst_id
    and b.page_id = 13 and a.company_id = $companyName
    and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $companyName )");
    if($approval_need==1)
    {
        $approval_cond = " and a.is_approved = '1'";
    }else{
        $approval_cond="";
    }*/

	$sql = "select a.id, a.requ_no,a.requ_prefix_num, a.requisition_date, a.company_id, a.location_id, a.is_approved, c.item_account, c.item_description, c.item_group_id, c.item_size, b.id as req_dtls_id, b.quantity
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b left join product_details_master c on  b.product_id = c.id and c.status_active = 1 and c.is_deleted = 0
	where a.pay_mode <> 4 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 $sql_cond and b.item_category in (4,11) $approval_cond
	union
	select a.id, a.requ_no,a.requ_prefix_num, a.requisition_date, a.company_id, a.location_id, a.is_approved, c.item_account, c.item_description, c.item_group_id, c.item_size, b.id as req_dtls_id, b.quantity
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b left join product_details_master c on  b.product_id = c.id and c.status_active = 1 and c.is_deleted = 0
	where a.pay_mode <> 4 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.item_category in (4,11) and b.id in($req_dtls_id)
	order by requ_no, requisition_date";
	//echo $sql;
	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
 	$item_name=return_library_array("select id,item_name from lib_item_group",'id','item_name');

	?>
    <div style="margin-top:10px; width:1100px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="65">Requisition No</th>
                <th width="70">Requisition Date</th>
                <th width="100">Company</th>
                <th width="100">Location</th>
                <th width="90">Item Account</th>
                <th width="120">Description</th>
                <th width="90">Item Group</th>
                <th width="80">Item Size</th>
                <th width="80">Requisition Qnty</th>
                <th width="80">Prev. WO Qnty</th>
                <th width="80">Balance</th>
                <th>Approval Status</th>
            </thead>
         </table>
         <div style="width:1100px; overflow-y:scroll; max-height:200px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="table_body">
                <?php
                $i = 1;
                $txt_row_data = "";
                $hidden_dtls_id = explode(",", $req_dtls_id);
                $nameArray = sql_select($sql);
                foreach ($nameArray as $selectResult) {
                	if ($i % 2 == 0) {
                		$bgcolor = "#E9F3FF";
                	} else {
                		$bgcolor = "#FFFFFF";
                	}
					$balance=$selectResult[csf("quantity")]- $prev_req_wo[$selectResult[csf("req_dtls_id")]];
                	if ($selectResult[csf("quantity")] > $prev_req_wo[$selectResult[csf("req_dtls_id")]]) 
					{
                		$data = $i . "_" . $selectResult[csf('requ_no')] . "_" . $selectResult[csf('id')] . "_" . $selectResult[csf('req_dtls_id')]. "_" . $selectResult[csf('is_approved')]."_".$approval_need."_".$allow_partial;
                		if (in_array($selectResult[csf('req_dtls_id')], $hidden_dtls_id)) 
						{
                			if ($txt_row_data == "") {
                				$txt_row_data = $data;
                			} else {
                				$txt_row_data .= "," . $data;
                			}
                		}
		                ?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $data; ?>')">
                            <td width="30" align="center"><?php echo "$i"; ?></td>
                            <td width="65" align="center"><p><?php echo $selectResult[csf('requ_prefix_num')]; ?></p></td>
                            <td width="65" align="center"><?php echo change_date_format($selectResult[csf('requisition_date')]); ?></td>
                            <td width="100"><p><?php echo $company[$selectResult[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><?php echo $location[$selectResult[csf('location_id')]]; ?>&nbsp;</p></td>
                            <td width="90"><p><?php echo $selectResult[csf('item_account')]; ?>&nbsp;</p></td>
                            <td width="120"><p><?php echo $selectResult[csf('item_description')]; ?></p></td>
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
        <table width="1080" cellspacing="0" cellpadding="0" border="1" align="left">
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
	echo  create_list_view("table_body", "Requisition No, Requisition Date, Company, Location, Item Account, Description, Item Group, Item Size", "120,80,100,110,110,100,90,80","900","200", 0, $sql, "js_set_value", "requ_no,id,req_dtls_id", "",1,"0,0,company_id,location_id,0,0,item_group_id,0", $arr, "requ_no,requisition_date,company_id,location_id,item_account,item_description,item_group_id,item_size","stationary_work_order_controller","setFilterGrid('table_body',-1)",'0,3,0,0,0,0,0,0',"",1) ;*/

exit();
}

if($action=="populate_pay_mode_data")
{
	$data=str_replace("'","",$data);
	$sql=sql_select("select id,pay_mode,source from  inv_purchase_requisition_mst where id in($data)");
	foreach($sql as $row)
	{
		echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode")].");\n";
        // echo "fnc_load_supplier('".$row[csf("pay_mode")]."');\n";
	}
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
	$company = $explodeData[4];
	$update_cond="";
	if($update_id>0) $update_cond=" and mst_id!=$update_id";
    if($update_id>0) $wo_update_cond=" and e.mst_id=$update_id";
	if($reqDtlsID=="") return; // for empty request
	$sql=sql_select("select requisition_no,sum(supplier_order_quantity) as order_quantity, requisition_dtls_id from wo_non_order_info_dtls where requisition_no in ('".implode("','",$requisition_numberID_arr)."') and status_active=1 and is_deleted=0 $update_cond  group by requisition_no,requisition_dtls_id");
	$requisitionQnty=array();
	foreach($sql as $result)
	{
		$requisitionQnty[$result[csf("requisition_no")]][$result[csf("requisition_dtls_id")]]=$result[csf("order_quantity")];
	}

	$origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name");

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
    
    /*echo "<pre>";
    print_r($req_arr);die;*/

    $sql = "SELECT a.id as requisition_id, a.requ_no, b.id, c.item_account, c.id as item_id, c.item_description, c.item_size, c.item_group_id, c.item_category_id, b.cons_uom, b.quantity, b.rate, b.amount, b.remarks, c.order_uom, c.conversion_factor, b.brand_name as brand, b.origin, c.model
    from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,  product_details_master c
    where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.id in ($reqDtlsID)";
	//echo $sql;
	$sqlResult = sql_select($sql);
	if( count($sqlResult)==0 ){ echo "No Data Found";die;}

	$i=$rowNo+1; // row no increse 1
	foreach($sqlResult as $key=>$val)
	{
        $wo_dtls_id=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_dtls_id'];
        $wo_rate=$req_arr[$val[csf("requisition_id")]][$val[csf("id")]]['wo_rate'];
 		?>
        <tbody>
            <tr class="general" id="<? echo $i;?>">
                <td width="120">
                <input type="text" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
                <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
                <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("id")];?>" readonly />
                <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_id")];?>" readonly />
                <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="<? echo $wo_dtls_id;?>" />
                </td>
                <td width="100">
                <?
                echo create_drop_down( "cbogroup_".$i, 100, "select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "Select", $val[csf("item_group_id")], "",1,"","","","","","","cbogroup[]","cbogroup_".$i );

                ?>
                </td>
                <td width="110">
                <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>" title="<? echo $val[csf("item_account")];?>" />
                </td>
                <td width="130">
                <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:120px" readonly value="<? echo $val[csf("item_description")];?>" title="<? echo $val[csf("item_description")];?>" />
                </td>
                <td width="80">
                <?
				echo create_drop_down( "cbo_item_category_".$i,80, $item_category,'', 1, '-- Select --',$val[csf("item_category_id")],"",1,"4,11","","","","","","cbo_item_category[]","cbo_item_category_".$i);
                ?>
                </td>
                <td width="70">
                <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes" style="width:60px" value="<? echo $val[csf("item_size")];?>" />
                </td>

                <td width="70"  align="center">
                <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("brand")];?>"/>
                </td>
                <td width="70"  align="center">
                <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", 0, "",0,"","","","","","","cboorigin[]","cboorigin_".$i ); ?>
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
                echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-- Select --",0, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

                ?>
                </td>
                <td width="60">
                <?
                echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "Select", $val[csf("order_uom")], "",1,"","","","","","","cbouom[]","cbouom_".$i);

                ?>
                </td>
                <td width="70">
                <? 
				//$quantityRemaing = number_format((($val[csf("quantity")]/$val[csf("conversion_factor")]) - $requisitionQnty[$val[csf("requisition_id")]][$val[csf("id")]]),3,".","");
				$quantityRemaing = $val[csf("quantity")] - $requisitionQnty[$val[csf("requisition_id")]][$val[csf("id")]];
				?>
                <input type="text" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" readonly value="<? echo number_format($quantityRemaing,2,'.',''); ?>" />
                </td>
                <td width="70">
                <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"   class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($quantityRemaing,2,'.',''); ?>" />	<!-- This is wo qnty here -->
                </td>
                <td width="50">
                    <?
                    if($wo_rate!=''){
                        $conv_rate = number_format($wo_rate,4,".","");
                        $disabled="disabled"; 
                    }else{
                        $conv_rate = $val[csf("rate")];
                        $disabled=""; 
                    }
                    ?>
                <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"   class="text_boxes_numeric"  style="width:50px;"  <? //echo $disabled; ?>  value="<? echo number_format($conv_rate,4,'.',''); ?>" />
                </td>
                <td width="70">
                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:70px;" readonly value="<? echo number_format($conv_rate*$quantityRemaing,4,'.',''); ?>" />
                </td>
                <td width="110">
                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)" />
                </td>
                <td>
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
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Total</td>
            <td style="text-align:center">
                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo number_format($val[csf("wo_amount")],4,'.',''); ?>" style="width:80px;" readonly/>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right" colspan="6">Upcharge Remarks:</td>
            <td colspan="5" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
            <td>Upcharge</td>
            <td style="text-align:center">
                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $val[csf("up_charge")];?>" style="width:70px;" onKeyUp="calculate_total_amount(2)" />
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right" colspan="6">Discount Remarks:</td>
            <td colspan="5" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
            <td>Discount</td>
            <td style="text-align:center">
                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $val[csf("discount")];?>" style="width:70px;" onKeyUp="calculate_total_amount(2)" />
            </td>
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
                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo number_format($val[csf("net_wo_amount")],4,'.',''); ?>" style="width:70px;" readonly/>
            </td>
            <td>&nbsp;</td>
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
		$terms_name.= '{value:"'.$result[csf('terms')].'",id:'.$result[csf('id')]."},";
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

			data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"");
		}
		var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","stationary_work_order_controller.php",true);
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

				data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"../../../",i);
			}
			var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","stationary_work_order_controller.php",true);

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
					{
						$condd = " is_default=1";
					}
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

		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

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
		//echo $rID;die;
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
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
}







if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

    if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        $txt_delivery_place=$hidden_delivery_info_dtls;
    }
	
	$all_item_id_arr=array();
	for($i=1;$i<=$total_row;$i++)
	{
		$item_id 	 	= "txt_item_id_".$i;
		if(str_replace("'","",$$item_id)>0) $all_item_id_arr[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);
	}
	
	
	$company_id=str_replace("'", "",  $cbo_company_name);
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
	//echo "10**".$des_wise_prod_sql;die;
	$des_wise_prod_sql_result=sql_select($des_wise_prod_sql);
	$prod_data_arr=array();
	foreach($des_wise_prod_sql_result as $val)
	{
		$prod_data_arr[$val["COMPANY_ID"]][$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][trim($val["ITEM_DESCRIPTION"])][trim($val["ITEM_SIZE"])][trim($val["MODEL"])]=$val["ID"];
	}
    $cbo_lc_type = str_replace("'","",$cbo_lc_type);
	//echo "10**<pre>";print_r($prod_data_arr);die;
	unset($des_wise_prod_sql_result);
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
        //echo $cbo_wo_basis.'='.$hid_approval_necessity_setup;die;
		//table lock here
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		//-----------------------------------------------wo_non_order_info_mst table insert START here----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		//echo "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and item_category in(11,4) $mrr_date_check order by id desc";die;
		$id=return_next_id("id", "wo_non_order_info_mst", 1);
		//$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SWO', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and item_category in(11,4) $mrr_date_check order by id desc", "wo_number_prefix", "wo_number_prefix_num" ));

		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SWO', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and entry_form = 146 $mrr_date_check order by id desc", "wo_number_prefix", "wo_number_prefix_num" ));
        //echo "10**".$new_wo_number[0];die;
		$field_array_mst="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, location_id,requisition_no,delivery_place, wo_date, supplier_id, attention, wo_basis_id, entry_form,dealing_marchant,currency_id, delivery_date, source, pay_mode, wo_amount, up_charge, discount, net_wo_amount,upcharge_remarks, discount_remarks, inserted_by, insert_date, ready_to_approved, inco_term_id, tenor,contact,payterm_id,wo_type,remarks,reference,lc_type";
       
        // echo "hh".$cbo_lc_type;
		
		//echo $field_array."<br>".$data_array;die;
 		//$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
 		//-----------------------------------------------wo_non_order_info_mst table insert END here-------------------------------------//
		//-----------------------------------------------wo_non_order_info_dtls table insert START here----------------------------------//
		//-------------------------------------------------------------------------------------------------------------------------------//
		$total_row = str_replace("'","",$total_row);
		$field_array="id, mst_id, requisition_dtls_id, requisition_no, item_id, uom,item_category_id, buyer_id,season_id, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount, remarks, inserted_by, insert_date, item_size, brand, origin, model";
		$req_dtls_field="product_id*updated_by*update_date";
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		$dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
		$data_array=""; $req_no_id_mst='';
		$check_item_id=array();$prod_scrtipt=true;
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
			$item_brand     = "txt_item_brand_".$i;
			$item_origin    = "cboorigin_".$i;
			$item_model     = "txt_item_model_".$i;
			$cbogroup	 	= "cbogroup_".$i;
			$cbouom	 		= "cbouom_".$i;
			$txt_req_qnty   = "txt_req_qnty_".$i; 	//requisition qnty
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$txt_amount  	= "txt_amount_".$i;
			$txt_remarks  	= "txt_remarks_".$i;
			$cbo_buyer  	= "cbo_buyer_".$i;
			$cbo_season  	= "cbo_season_".$i;
			$previous_prod_id=str_replace("'","",$$item_id);

            if( str_replace("'","",$cbo_wo_basis) == 1  && ( str_replace("'","",$hid_approval_necessity_setup) == 2 || str_replace("'","",$hid_approval_necessity_setup) == 0))    
            {
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) ){
                    echo "11**Work Order Qty Can't over than Requisition Qty"; check_table_status( 175,0);
    				disconnect($con);die;
                }
            }

            if(str_replace("'","",$cbo_wo_basis)==2) // Independent Basis
            {
    			if($check_item_id[str_replace("'","",$$item_id)]!="")
    			{
    				echo "11**Duplicate Item Not Allow In Same WO**0";
    				check_table_status( 175,0);
    				disconnect($con);die;
    			}
            }
			

			

			//echo $net_rate."<br>".$net_amount; die;

			if( str_replace("'","",$$txt_quantity) != "" )
			{
				if($item_create_setting==1)
				{
					$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))];
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
				$net_rate=number_format($net_rate,4,'.','');
				$net_amount=number_format($net_amount,4,'.','');
               
                
				
				
				$data_array.="(".$dtlsid.",".$id.",'".$$req_dtls_id."','".$$req_no_id."','".$prod_id."','".$$cbouom."','".$$item_category."','".$$cbo_buyer."','".$$cbo_season."','".$$txt_req_qnty."','".$$txt_quantity."','".$net_rate."','".$net_amount."','".$$txt_rate."','".$$txt_amount."','".$$txt_remarks."','".$user_id."','".$pc_date_time."','".$$item_size."','".$$item_brand."','".$$item_origin."','".$$item_model."')";
				$dtlsid=$dtlsid+1;
                $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
				
				if (str_replace("'","",$$req_dtls_id) != "") {
                    $req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
				    $req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
                }                
			}

		}
        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));

        // if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        //     $txt_delivery_place=$hidden_delivery_info_dtls;
        // }
        $data_array_mst="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",".$cbo_location.",'".$req_no_id_mst."',".$txt_delivery_place.",".$txt_wo_date.",".$cbo_supplier.",".$txt_attention.",".$cbo_wo_basis.",146,".$cbo_deal_merchant.",".$cbo_currency.",".$txt_delivery_date.",".$cbo_source.",".$cbo_pay_mode.",".$txt_total_amount.",".$txt_upcharge.",".$txt_discount.",".$txt_total_amount_net.",".$txt_up_remarks.",".$txt_discount_remarks.",'".$user_id."','".$pc_date_time."',".$cbo_ready_to_approved.",".$cbo_inco_term.",".$txt_tenor.",".$txt_contact.",".$cbo_payterm_id.",".$cbo_wo_type.",".$txt_remarks_mst.",".$txt_reference.",".$cbo_lc_type.")";
		//echo "10** insert into wo_non_order_info_dtls ($field_array) values $data_array";die;
		// echo "10** insert into wo_non_order_info_mst ($field_array_mst) values $data_array_mst";die;
		$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array,$data_array,1);
		$req_dtlsrID=true;
       
		if(count($req_dtla_id_arr)>0 && $item_create_setting==1)
        {
            // bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
            $req_dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$req_dtls_field,$req_dtls_data,$req_dtla_id_arr),1);
        }
		//-----------------------------------------------wo_non_order_info_dtls table insert END here-----------------------------------//

		//oci_commit($con); oci_rollback($con);

		//echo "5**".$rID."**".$dtlsrID."**".$prod_scrtipt."**".$req_dtlsrID; die;

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
				echo "0**".$new_wo_number[0]."**".$id."**".$dtlsid_check;
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
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here

		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$update_check=str_replace("'","",$update_id);
		if($update_check>0)
		{
			 $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$update_check and status_active=1","pay_mode");
			 $is_approved = return_field_value("is_approved","wo_non_order_info_mst","id = $update_check and status_active=1 and is_approved in(1,3)");
		}
		if($is_approved==1 || $is_approved==3)
		{
			echo "14**Approved";
			check_table_status( 175,0);
			disconnect($con);
			die;
		}


		/*if($update_check>0 && $pay_mode==2)
		{
			$pi_sql=sql_select("select a.id as pi_id, a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=11 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$update_check");
			if(count($pi_sql)>0)
			{
				echo "11**PI Number Found :".$pi_sql[0][csf("pi_number")]." \n So Update/Delete Not Possible.";check_table_status( 175,0);disconnect($con);die;
			}
		}
		//echo "10**jahid".$update_check."==".$cbo_pay_mode;check_table_status( 175,0);die;

		if($update_check>0 && $pay_mode!=2)
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number from inv_receive_master a where a.entry_form=20 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.booking_id=$update_check");
			if(count($mrr_sql)>0)
			{
				echo "11**Receive Number Found :".$mrr_sql[0][csf("recv_number")]." \n So Update/Delete Not Possible.";check_table_status( 175,0);disconnect($con);die;
			}
		}*/

		$wo_data=array();
		for($i=1;$i<=$total_row;$i++)
		{

			$item_id 	 	= "txt_item_id_".$i;
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$wo_data[str_replace("'","",$$item_id)]["quantity"]+=str_replace("'","",$$txt_quantity);
			$wo_data[str_replace("'","",$$item_id)]["rate"]=str_replace("'","",$$txt_rate);
		}

		if($update_check>0 && $pay_mode==2)
		{
			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.item_category_id=11 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$update_check");
			if(count($pi_sql)>0)
			{
				$pi_data=array();
				foreach($pi_sql as $row)
				{
					$pi_data[$row[csf("item_prod_id")]]["quantity"]+=$row[csf("quantity")];
					$pi_data[$row[csf("item_prod_id")]]["rate"]=$row[csf("rate")];
                    $pi_data[$row[csf("item_prod_id")]]["pi_number"][$row[csf("pi_id")]]=$row[csf("pi_number")];
				}
				foreach($pi_data as $prod_id=>$prod_pi_val)
				{
					//if($wo_data[$prod_id]["quantity"]<$prod_pi_val["quantity"] && $wo_data[$prod_id]["rate"]!=$prod_pi_val["rate"])
                    $pi_number_all = implode(",", $prod_pi_val[$prod_id]["pi_number"]);
					if($wo_data[$prod_id]["quantity"]<$prod_pi_val["quantity"] || number_format($wo_data[$prod_id]["rate"],4,".","")!=number_format($prod_pi_val["rate"],4,".",""))
					{
						echo "11**PI Number Found(".$pi_number_all."), WO Quantity Not Allow Less Then PI Quantity  Or Rate Change Not Allow. \n So Update/Delete Not Possible.**$update_check";check_table_status( 175,0);disconnect($con);die;
					}
				}
                echo "11**PI Number Found(".$pi_sql[0][csf('pi_number')].")";check_table_status( 175,0);disconnect($con);die;

			}
		}
		//echo "10**jahid".$update_check."==".$cbo_pay_mode;check_table_status( 175,0);die;

		/*if($update_check>0 && $pay_mode!=2)
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number,a.recv_number_prefix_num, a.booking_id, b.prod_id, b.order_qnty, b.order_rate	from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=20 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$update_check");
			if(count($mrr_sql)>0)
			{
				$mrr_data=array();
				foreach($mrr_sql as $row)
				{
					$mrr_data[$row[csf("prod_id")]]["quantity"]+=$row[csf("order_qnty")];
                    $mrr_data[$row[csf("prod_id")]]["rate"]=$row[csf("order_rate")];
					$mrr_data[$row[csf("prod_id")]]["rcv_no"][$row[csf("recv_number")]]=$row[csf("recv_number_prefix_num")];
				}

				foreach($mrr_data as $prod_id=>$prod_mrr_val)
				{
					//if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"] && $wo_data[$prod_id]["rate"]!=$prod_mrr_val["rate"])
                    $rcv_no = implode(",", $mrr_data[$prod_id]["rcv_no"]);
					if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"] && number_format($wo_data[$prod_id]["rate"],4,".","")!=number_format($prod_mrr_val["rate"],4,".",""))
					{
						echo "11**Receive Number Found(".$rcv_no."), WO Quantity Or Rate  Not Allow Less Then MRR Quantity and Rate,  \n So Update/Delete Not Possible.**$update_check";
                        check_table_status( 175,0);
                        disconnect($con);die;
					}
				}
			}
		}*/

        if($update_check>0 && $pay_mode!=2)
        {
            $mrr_sql=sql_select("select a.id as mrr_id, a.recv_number,a.recv_number_prefix_num, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
            from inv_receive_master a, inv_transaction b
            where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=20 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$update_check");
            if(count($mrr_sql)>0)
            {
                $next_opt_check=1;
                $mrr_data=array();
                foreach($mrr_sql as $row)
                {
                    $mrr_data[$row[csf("prod_id")]]["quantity"]+=$row[csf("order_qnty")];
                    $mrr_data[$row[csf("prod_id")]]["rate"]=$row[csf("order_rate")];
                    $mrr_data[$row[csf("prod_id")]]["rcv_no"][$row[csf("recv_number")]]=$row[csf("recv_number_prefix_num")];
                }

                foreach($mrr_data as $prod_id=>$prod_mrr_val)
                {
                    //if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"])
                    $rcv_no = implode(",", $mrr_data[$prod_id]["rcv_no"]);
                    if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"] || number_format($wo_data[$prod_id]["rate"],2,'.','')!=number_format($prod_mrr_val["rate"],2,'.',''))
                    {
                        echo "11**Receive Number Found(".$rcv_no."), WO Quantity Or Rate  Not Allow Less Then MRR Quantity and Rate,  \n So Update/Delete Not Possible.**$update_check"; check_table_status( 175,0); disconnect($con); die;
                    }
                }
            }
        }



		//-----------------wo_non_order_info_mst table UPDATE START here----------------------------------//
		//*pay_mode *".$cbo_pay_mode."*supplier_id *".$cbo_supplier." *wo_basis_id *".$cbo_wo_basis."

		
		//-----------------------------------------------wo_non_order_info_mst table UPDATE END here-------------------------------------//
		//-----------------------------------------------wo_non_order_info_dtls table UPDATE START here----------------------------------//

		$total_row = str_replace("'","",$total_row);
		$txt_delete_row = str_replace("'","",$txt_delete_row);
		/*if($txt_delete_row!="")
		{
			$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",0);
			//$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
		}*/
		$data_array_insert="";
 		$field_array_insert="id, mst_id, requisition_dtls_id, requisition_no, item_id, uom, item_category_id, buyer_id,season_id, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount, remarks,status_active,is_deleted, inserted_by, insert_date, item_size, brand, origin, model";

 		$field_array="requisition_dtls_id*requisition_no*item_id*uom*buyer_id*season_id*req_quantity*supplier_order_quantity*gross_rate*gross_amount*rate*amount*remarks*status_active*is_deleted*updated_by*update_date*item_size*brand*origin*model";
		$req_dtls_field="product_id*updated_by*update_date";
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		$dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
		$data_array=array(); $req_no_id_mst='';$prod_scrtipt=true;
		for($i=1;$i<=$total_row;$i++)
		{

			$req_no_id	 	= "txt_req_no_id_".$i;
			$req_dtls_id	= "txt_req_dtls_id_".$i;
			$item_id 	 	= "txt_item_id_".$i;
			$item_acct 	 	= "txt_item_acct_".$i;
			$item_desc	 	= "txt_item_desc_".$i;
            $item_category	= "cbo_item_category_".$i;
			$item_size	 	= "txt_item_size_".$i;
			$item_brand     = "txt_item_brand_".$i;
			$item_origin    = "cboorigin_".$i;
			$item_model     = "txt_item_model_".$i;
			$cbogroup	 	= "cbogroup_".$i;
			$cbouom	 		= "cbouom_".$i;
			$txt_req_qnty   = "txt_req_qnty_".$i; 	//reuisition qnty
			$txt_quantity   = "txt_quantity_".$i;	//work order qnty
			$txt_rate    	= "txt_rate_".$i;
			$txt_amount  	= "txt_amount_".$i;
			$txt_remarks  	= "txt_remarks_".$i;

			$cbo_season  	= "cbo_season_".$i;
			$cbo_buyer  	= "cbo_buyer_".$i;
			$dtls_ID  		= "txt_row_id_".$i;
			$dtlsID_up 		= str_replace("'","",$$dtls_ID);
			$previous_prod_id=str_replace("'","",$$item_id);

            if( str_replace("'","",$cbo_wo_basis) == 1  && ( str_replace("'","",$hid_approval_necessity_setup) == 2 || str_replace("'","",$hid_approval_necessity_setup) == 0))
            {
                if( str_replace("'","",$$txt_req_qnty) < str_replace("'","",$$txt_quantity) ){
                    echo "11**Work Order Qty Can't over than Requisition Qty";
                    check_table_status( 175,0);
                    disconnect($con);die;
                }
            }

            if(str_replace("'","",$cbo_wo_basis)==2) // Independent Basis
            {
                if($check_item_id[str_replace("'","",$$item_id)]!=""){
                    echo "11**Duplicate Item Not Allow In Same WO**$update_check";
                    check_table_status( 175,0);
                    disconnect($con);die;
                }
            }

			$check_item_id[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);
			$perc=(str_replace("'","",$$txt_amount)/str_replace("'","",$txt_total_amount))*100;
			if($perc==INF) $perc=0;

			$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
			//echo "10**".$net_amount;die;
			$net_rate=$net_amount/str_replace("'","",$$txt_quantity);

			$net_rate=number_format($net_rate,4,'.','');
			$net_amount=number_format($net_amount,4,'.','');
           
			if($dtlsID_up>0) //update
			{
				if($item_create_setting==1)
				{
					$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))];
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
				$data_array[$dtlsID_up]=explode("*",("'".$$req_dtls_id."'*'".$$req_no_id."'*'".$prod_id."'*'".$$cbouom."'*'".$$cbo_buyer."'*'".$$cbo_season."'*'".$$txt_req_qnty."'*'".$$txt_quantity."'*'".$$txt_rate."'*'".$$txt_amount."'*'".$net_rate."'*'".$net_amount."'*'".$$txt_remarks."'*1*0*'".$user_id."'*'".$pc_date_time."'*'".$$item_size."'*'".$$item_brand."'*'".$$item_origin."'*'".$$item_model."'"));
				$req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
				$req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
 			}
			else  // new insert
			{
				if( str_replace("'","",$$txt_quantity) != "" )
				{
					if($item_create_setting==1)
					{
						$prod_id=$prod_data_arr[str_replace("'","",$cbo_company_name)][str_replace("'","",$$item_category)][str_replace("'","",$$cbogroup)][trim(str_replace("'","",$$item_desc))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$item_model))];
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
					
					
					if($data_array_insert!="")$data_array_insert .=",";
					$data_array_insert.="(".$dtlsid.",".$update_id.",'".$$req_dtls_id."','".$$req_no_id."','".$prod_id."','".$$cbouom."','".$$item_category."','".$$cbo_buyer."','".$$cbo_season."','".$$txt_req_qnty."','".$$txt_quantity."','".$net_rate."','".$net_amount."','".$$txt_rate."','".$$txt_amount."','".$$txt_remarks."',1,0,'".$user_id."','".$pc_date_time."','".$$item_size."','".$$item_brand."','".$$item_origin."','".$$item_model."')";
					$req_dtla_id_arr[]=str_replace("'","",$$req_dtls_id);
					$req_dtls_data[str_replace("'","",$$req_dtls_id)]=explode("*",("'".$prod_id."'*'".$user_id."'*'".$pc_date_time."'"));
					$dtlsid=$dtlsid+1;
				}
			}
            $req_no_id_mst .=str_replace("'","",$$req_no_id).',';
			//echo "10**nazim".$data_array; die;
		}
        $req_no_id_mst=implode(",",array_unique(explode(",",chop($req_no_id_mst,','))));
        $mst_id=str_replace("'","",$update_id);
        /*if($update_check>0)
        {//echo "10**nazim up"; die;
            $mst_id = return_field_value("id","wo_non_order_info_mst","wo_number=$txt_wo_number");
            $field_array_mst="requisition_no*delivery_place*wo_date*attention*location_id*dealing_marchant*currency_id*delivery_date*source*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*updated_by*update_date*ready_to_approved*inco_term_id*pay_mode*tenor*contact*payterm_id*wo_type*remarks*reference";

            $data_array_mst="".$txt_req_numbers_id."*".$txt_delivery_place."*".$txt_wo_date."*".$txt_attention."*".$cbo_location."*".$cbo_deal_merchant."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_pay_mode."*".$txt_tenor."*".$txt_contact."*".$cbo_payterm_id."*".$cbo_wo_type."*".$txt_remarks_mst."*".$txt_reference."";
            //echo $field_array_mst."<br />".$data_array_mst;die;
            //$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,0);
        }*/

        if($mst_id>0)
        {//*
            //*".",payterm_id,tenor,pi_issue_to,port_of_loading,".$cbo_payterm_id.",".$txt_tenor.",".$cbo_pi_issue_to.",".$txt_port_of_loading."
            $field_array_mst="requisition_no*supplier_id*delivery_place*wo_date*attention*location_id*dealing_marchant*currency_id*delivery_date*source*pay_mode*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*updated_by*update_date*ready_to_approved*inco_term_id*pay_mode*tenor*contact*payterm_id*wo_type*remarks*reference*lc_type";

            $data_array_mst="".$txt_req_numbers_id."*".$cbo_supplier."*".$txt_delivery_place."*".$txt_wo_date."*".$txt_attention."*".$cbo_location."*".$cbo_deal_merchant."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$cbo_pay_mode."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_discount_remarks."*'".$user_id."'*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_inco_term."*".$cbo_pay_mode."*".$txt_tenor."*".$txt_contact."*".$cbo_payterm_id."*".$cbo_wo_type."*".$txt_remarks_mst."*".$txt_reference."*".$cbo_lc_type."";            
            //echo $field_array."<br />".$data_array;die;
            //$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
        }


		$rID=$delete_details=$dtlsrIDI=$dtlsrID=true;
		$field_array_insert= "id, mst_id, requisition_dtls_id, requisition_no, item_id, uom, item_category_id, buyer_id,season_id, req_quantity, supplier_order_quantity, rate, amount, gross_rate, gross_amount, remarks,status_active,is_deleted, inserted_by, insert_date, item_size, brand, origin, model";
		//$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE mst_id=$update_check",1);

		/*if($txt_delete_row!="")
		{
			$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
			//$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
		}*/
		//echo "10**insert into wo_non_order_info_dtls (".$field_array_insert.") values".$data_array_insert.""; die;
		//echo "10**".$data_array_insert;die;
		$field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
	    $data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$delete_details=sql_update("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"mst_id",$update_check,1);
		if($update_check>0)
		{
			$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$update_check,1);
		}
		if($data_array_insert!="")
		{
			$dtlsrIDI=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,1);
		}
		if(count($update_ID)>0)
		{
			// bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
			$dtlsrID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$update_ID));
		}

		$req_dtlsrID=true;
        //echo '<pre>';print_r($req_dtla_id_arr);die;
		//if(!empty($req_dtla_id_arr) && $item_create_setting==1)
		if($req_dtla_id_arr[0]!='' && $item_create_setting==1)
        {
            //echo bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$req_dtls_field,$req_dtls_data,$req_dtla_id_arr);
            $req_dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$req_dtls_field,$req_dtls_data,$req_dtla_id_arr),1);
        }

		//-----------------------------------------------wo_non_order_info_dtls table UPDATE END here-----------------------------------//
		//echo "10**".$rID."**".$dtlsrID."**".$delete_details."**".$dtlsrIDI."**".$prod_scrtipt."**".$req_dtlsrID; oci_rollback($con);disconnect($con);die;


		if($db_type==0)
		{
			if($rID && $dtlsrID && $delete_details && $dtlsrIDI && $prod_scrtipt && $req_dtlsrID)
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
			if($rID && $dtlsrID && $delete_details && $dtlsrIDI && $prod_scrtipt && $req_dtlsrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_wo_number)."**".$update_check."**".$dtlsid_check;
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
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
        $txt_wo_number = str_replace("'", "", $txt_wo_number);
		$mst_sql=sql_select("select id, pay_mode from wo_non_order_info_mst where status_active=1 and entry_form=146 and wo_number = '$txt_wo_number'");
		$mst_id = $mst_sql[0][csf("id")];
		$prev_pay_mode = $mst_sql[0][csf("pay_mode")];
		if($mst_id=="" || $mst_id==0){ echo "15**Work Order Not Found To Delete";disconnect($con);die;}

		$is_approved = return_field_value("is_approved","wo_non_order_info_mst","wo_number = '$txt_wo_number' and status_active=1 and is_approved in(1,3)");

		if($is_approved==1 || $is_approved==3)
		{
			echo "14**Approved";
			check_table_status( 175,0);
			disconnect($con);
			die;
		}
		//echo "10**".$prev_pay_mode;die;

        $pi_arr = sql_select("select a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.work_order_no = '$txt_wo_number' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

        $pi_number = $pi_arr[0][csf('pi_number')];
        //echo "10**$pi_number";disconnect($con);die;
        if($pi_number)
        {
            echo "15**Work Order Attached To Pro Forma Invoice No. ".str_replace("'", "", $pi_number);disconnect($con);die;
        }

        $rcv_arr = sql_select("select a.recv_number from inv_receive_master a, wo_non_order_info_mst b where a.booking_id = b.id and a.entry_form=20 and a.receive_basis = 2 and a.booking_id = $mst_id and a.company_id = $cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
        $rcv_no = $rcv_arr[0][csf('recv_number')];
		//echo "10**".$rcv_no;die;
        if($rcv_no)
        {
            echo "15**Receive No Found againts this Work Order No. ".$rcv_no;disconnect($con);die;
        }

        $rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,0);
		$dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_wo_number);
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
				echo "2**".str_replace("'","",$rID);
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
                    $supplier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a , lib_supplier_party_type b WHERE a.id= b.supplier_id and b.party_type in (5,8) and a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 order by a.supplier_name", 'id', 'supplier_name');
                    

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

    </script>

    </head>

    <body>
    <div align="center" style="width:100%;" >

    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table"  align="center">
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
                                    echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", "", "","","4,11");
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
                                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'stationary_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:100px;" />
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

if($action=="create_wo_search_list_view_copy")
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
	if(trim($itemCategory)) $sql_cond .= " and b.item_category_id='$itemCategory'";
	if(trim($txt_search_common)!="")
	{

		if(trim($txt_search_by)==1)
			$sql_cond .= " and a.wo_number like '%".trim($txt_search_common)."'";
		else if(trim($txt_search_by)==2)
			$sql_cond .= " and a.supplier_id=trim('$txt_search_common')";
        else if(trim($txt_search_by)==3)
		    $sql_cond .= " and a.requisition_no=trim('$txt_search_common')";
 	}
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";

     if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$wo_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$wo_year.""; }

 	/*$sql = "select id,wo_number_prefix_num, wo_number,company_name,buyer_po,wo_date,supplier_id,attention,wo_basis_id,item_category,currency_id,delivery_date,source,pay_mode
			from
				wo_non_order_info_mst
			where
				status_active=1 and
				is_deleted=0  and entry_form = 146
				$sql_cond order by id";*/ //and garments_nature=$garments_nature
	$sql = " SELECT a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
			a.item_category,a.currency_id,a.delivery_date,source,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where a.id = b.mst_id and a.entry_form = 146
			and a.status_active=1 and a.is_deleted=0
			$sql_cond
                        group by a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
                        a.item_category,a.currency_id,a.delivery_date,source,a.pay_mode,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
                        order by a.wo_date desc";
        //echo $sql;die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
    $yes_no_arr=array(0=>"No",1=>"Yes",2=>"No",3 => "Yes");
	$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$wo_basis,6=>$source,7=>$user_arr,8=>$yes_no_arr,9=>$yes_no_arr);

	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )
	echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source,Insert Users,Ready To Approved,Approval Status", "150,100,100,100,150,100,100,100,80,80","1160","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", "",'','0,0,3,0,0,0,0,0,0,0,0');


 	exit();
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
    if(trim($itemCategory)) $sql_cond .= " and b.item_category_id='$itemCategory'";
    if(trim($txt_search_common)!="")
    {

        if(trim($txt_search_by)==1)
            $sql_cond .= " and a.wo_number like '%".trim($txt_search_common)."'";
        else if(trim($txt_search_by)==2)
            $sql_cond .= " and a.supplier_id=trim('$txt_search_common')";
        else if(trim($txt_search_by)==3)
            $sql_cond .= " and d.requ_prefix_num=trim('$txt_search_common')";  
    }
    //print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
    //if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";

     if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else 
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$wo_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$wo_year.""; }

                    /*$sql = "select id,wo_number_prefix_num, wo_number,company_name,buyer_po,wo_date,supplier_id,attention,wo_basis_id,item_category,currency_id,delivery_date,source,pay_mode
                    from
                    wo_non_order_info_mst
                    where
                    status_active=1 and
                    is_deleted=0  and entry_form = 146
                    $sql_cond order by id";*/ //and garments_nature=$garments_nature
                if($txt_search_by==3){
                    $sql = " SELECT a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,a.item_category,a.currency_id,a.delivery_date,a.source,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
                    from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c, inv_purchase_requisition_mst d
                    where a.id = b.mst_id and a.entry_form = 146 and b.requisition_dtls_id = c.id and d.id=c.mst_id
                    and a.status_active=1 and a.is_deleted=0 $sql_cond
                    group by a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
                    a.item_category,a.currency_id,a.delivery_date,a.source,a.pay_mode,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
                    order by a.wo_date desc";
                }else{
                    $sql = " SELECT a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,a.item_category,a.currency_id,a.delivery_date,a.source,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
                    from wo_non_order_info_mst a, wo_non_order_info_dtls b
                    where a.id = b.mst_id and a.entry_form = 146 
                    and a.status_active=1 and a.is_deleted=0 $sql_cond
                    group by a.id,a.wo_number_prefix_num, a.wo_number,a.requisition_no,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,
                    a.item_category,a.currency_id,a.delivery_date,a.source,a.pay_mode,a.pay_mode,a.inserted_by,a.ready_to_approved,a.is_approved
                    order by a.wo_date desc";

                }
            // echo $sql;die;
    $result = sql_select($sql);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
    $yes_no_arr=array(0=>"No",1=>"Yes",2=>"No",3 => "Yes");
    ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="150">Company</th>
                <th width="100">WO Number</th>
                <th width="100">WO Date</th>
                <th width="100">Pay Mode</th>
                <th width="150">Supplier</th>
                <th width="120">WO Basis</th>
                <th width="90">Source</th>
                <th width="80">Insert Users</th>
                <th width="80">Ready To Approved</th>
                <th width="80">Approval Status</th>
            </thead>
         </table>
         <div style="width:1080px; overflow-y:scroll; max-height:300px">
            <table style="margin-bottom: 10px;" id="list_view" cellspacing="0" cellpadding="0" border="1" rules="all" align="left" width="1080" class="rpt_table">
                <?php
                $i = 1;
                $txt_row_data = "";
                $hidden_dtls_id = explode(",", $req_dtls_id);
                $nameArray = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0) {
                        $bgcolor = "#E9F3FF";
                    } else {
                        $bgcolor = "#FFFFFF";
                    }
                    if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
                    {
                        $supplier = $company_arr[$row[csf('supplier_id')]];
                    }
                    else
                    {
                        $supplier = $supplier_arr[$row[csf('supplier_id')]];
                    }

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('wo_number')].'_'.$row[csf('id')]; ?>')">
                        <td width="30" align="center"><?php echo "$i"; ?></td>
                        <td width="150" align="center"><p><?php echo $company_arr[$row[csf('company_name')]]; ?></p></td>
                        <td width="100" align="center"><?php echo $row[csf('wo_number_prefix_num')]; ?></td>
                        <td width="100"><p><?php echo $row[csf('wo_date')]; ?></p></td>
                        <td width="100"><p><?php echo $pay_mode[$row[csf('pay_mode')]]; ?>&nbsp;</p></td>
                        <td width="150"><p><?php echo $supplier; ?>&nbsp;</p></td>
                        <td width="120"><p><?php echo $wo_basis[$row[csf('wo_basis_id')]]; ?></p></td>
                        <td width="90"><p><?php echo $source[$row[csf('source')]]; ?></p></td>
                        <td width="80"><p><?php echo $user_arr[$row[csf('inserted_by')]]; ?></p></td>
                        <td width="80" ><?php echo $yes_no_arr[$row[csf('ready_to_approved')]]; ?></td>
                        <td width="80"><p><?php echo $yes_no_arr[$row[csf('is_approved')]]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                 }
                 ?>
            </table>
        </div>
<?php
    exit();
}



if($action=="populate_data_from_search_popup")
{
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$sql = "select id,requisition_no,delivery_place,company_name,buyer_po,wo_date,supplier_id,attention,dealing_marchant,wo_basis_id,currency_id,delivery_date,source,pay_mode,ready_to_approved,is_approved,location_id,inco_term_id,tenor,reference,payterm_id,contact,wo_type,remarks,lc_type from wo_non_order_info_mst where id='$data'";
	//echo $sql;die;
    $lc_type_arr= array( 4 => "TT/Pay Order", 5 => "FTT", 6 => "FDD/RTGS");
	$result = sql_select($sql);
	foreach($result as $resultRow)
	{
		echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
        echo "$('#cbo_location').val('".$resultRow[csf("location_id")]."');\n";
		//echo "$('#cbo_company_name').attr('disabled',true);\n";
		//echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
		echo "$('#cbo_deal_merchant').val('".$resultRow[csf("dealing_marchant")]."');\n";
		//echo "$('#cbo_item_category').attr('disabled',true);\n";
		
		echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
		echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";
		echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
		echo  "fn_disable_enable('".$resultRow[csf("wo_basis_id")]."');\n";
		//echo "$('#cbo_wo_basis').attr('disabled',true);\n";
		echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";

        // echo "fnc_load_supplier('".$resultRow[csf("pay_mode")]."');\n";
        if($resultRow[csf("pay_mode")]==3 || $resultRow[csf("pay_mode")]==5)
        {
            echo "$('#txt_supplier_name').val('".$company_arr[$resultRow[csf("supplier_id")]]."');\n";
        }
        else
        {
            echo "$('#txt_supplier_name').val('".$supplier_arr[$resultRow[csf("supplier_id")]]."');\n";
        }
        echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";

		echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
		echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
		echo "$('#txt_req_numbers_id').val('".$resultRow[csf("requisition_no")]."');\n";

        $hdn_delivery=explode('__',$resultRow[csf("delivery_place")]);

		echo "$('#txt_delivery_place').val('".$hdn_delivery[0]."');\n";
        if(count($hdn_delivery)>1)
        {
            echo "$('#hidden_delivery_info_dtls').val('".$resultRow[csf("delivery_place")]."');\n";
        }

        echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
        echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term_id")]."');\n";
        echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
		echo "$('#txt_contact').val('".$resultRow[csf("contact")]."');\n";
		echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
		echo "$('#cbo_wo_type').val('".$resultRow[csf("wo_type")]."');\n";
		echo "$('#txt_remarks_mst').val('".$resultRow[csf("remarks")]."');\n";
		echo "$('#txt_reference').val('".$resultRow[csf("reference")]."');\n";
        echo "$('#cbo_lc_type').val('".$resultRow[csf("lc_type")]."');\n";
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
            echo "$('#approved').text('Approved');\n";
        }
        else  if($resultRow[csf("is_approved")]==3)
        {
            echo "$('#approved').text('Partial Approved');\n";
        }
        else
        {
            echo "$('#approved').text('');\n";
        }

        $pay_mode=$resultRow[csf("pay_mode")];
        $receiveStats = false;

        if($data > 0 && $pay_mode == 2)
        {
            $pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=11 and a.pi_basis_id=1 and a.goods_rcv_status<>1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$data");
            if(count($pi_sql)>0)
            {
                $receiveStats = true;
            }
        }

        if($data >0 && $pay_mode!= 2)
        {
            $mrr_sql=sql_select("select a.id as mrr_id, a.recv_number,a.recv_number_prefix_num, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
            from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=20 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$data");
            if(count($mrr_sql)>0)
            {

                $receiveStats = true;                

            }
        }

        if($resultRow[csf("is_approved")] > 0 || $receiveStats == true){
            echo "$('#cbo_pay_mode').prop('disabled', true);\n";
            echo "$('#cbo_supplier').prop('disabled', true);\n";
            echo "$('#txt_supplier_name').attr('onDblClick',false);\n";
        }else{
            echo "$('#cbo_pay_mode').prop('disabled', false);\n";
            echo "$('#cbo_supplier').prop('disabled', false);\n";
        }

        if($resultRow[csf("ready_to_approved")]!=1){
            $refusing_cause = return_field_value("REFUSING_REASON","REFUSING_CAUSE_HISTORY","MST_ID = ".$resultRow[csf("id")]." and ENTRY_FORM=5 order by id desc");
            if($refusing_cause!=''){
            echo "$('#refusing_cause').text('".$refusing_cause."');\n";
            }
        }
	}
	exit();
}


if($action=="show_dtls_listview_update")
{
	$edata=explode("__",$data);
	$id=$edata[0];
	$wo_basis_id=$edata[1];
	$origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name"); 
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$wo_pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$id","pay_mode");
	$pi_mrr_data=array();
	if($wo_pay_mode==2)
	{
		$pi_mrr_sql="select b.item_prod_id as prod_id, b.quantity as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=11 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$id";
	}
	else
	{
		$pi_mrr_sql="select b.prod_id as prod_id, b.order_qnty as quantity from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.entry_form=20 and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$id";
	}
	
	$pi_mrr_result=sql_select($pi_mrr_sql);
	foreach($pi_mrr_result as $row)
	{
		$pi_mrr_data[$row[csf("prod_id")]]+=$row[csf("quantity")];
	}

	//echo $pi_mrr_sql;die;

	if($wo_basis_id==1)
	{
		$sql = "select b.id,a.wo_basis_id,a.company_name, b.requisition_dtls_id, b.po_breakdown_id,b.buyer_id,b.season_id, b.requisition_no, b.item_id,p.item_account, p.item_description,p.item_category_id, p.item_size, p.item_group_id as item_group, b.BRAND as brand, b.origin, p.model, b.req_quantity, b.color_name, b.supplier_order_quantity, b.uom, b.gross_rate , b.gross_amount, b.remarks, c.requ_no, a.wo_amount, a.up_charge, a.discount, a.net_wo_amount,a.upcharge_remarks,a.discount_remarks
		from 
			product_details_master p, wo_non_order_info_mst a, wo_non_order_info_dtls b left join  inv_purchase_requisition_mst c on b.requisition_no=c.id
		where
			a.id=$id and a.id=b.mst_id and b.item_id=p.id and b.status_active=1 and b.is_deleted=0";
	}
	else
	{
		$sql = "select b.id, a.wo_basis_id, a.company_name, b.requisition_dtls_id, b.po_breakdown_id, b.buyer_id, b.season_id, b.requisition_no, b.item_id,p.item_account, p.item_description, p.item_category_id, p.item_size, p.item_group_id as item_group, p.brand_name as brand, p.origin, p.model, b.req_quantity, b.color_name, b.supplier_order_quantity, b.uom, b.gross_rate, b.gross_amount, b.remarks, a.wo_amount, a.up_charge, a.discount, a.net_wo_amount,a.upcharge_remarks,a.discount_remarks
		from 
			product_details_master p, wo_non_order_info_mst a, wo_non_order_info_dtls b
		where
			a.id=$id and a.id=b.mst_id and b.item_id=p.id and b.status_active=1 and b.is_deleted=0";
	}


	//echo $sql; //b.item_category,
	$result = sql_select($sql);
	$i=1;
	foreach($result as $val)
	{
		if($i==1)
		{
		  ?>
        	<div style="width:1470px;">
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

                            <th>Brand</th>
                            <th>Origin</th>
                            <th>Model</th>
                            <th>Buyer</th>
                            <th>Season</th>
                            <th>Order UOM</th>
                            <? if($val[csf("wo_basis_id")]==1 ){?>
                            <th>Req.Qnty</th>
                            <? } ?>

                            <th>WO.Qnty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Action</th>
						</tr>
					</thead>
                    <tbody>
         <? }
		 ?>

                <tr class="general" id="<? echo $i;?>">

                <!-- This is for requisition number selected in WO Basis START -->
                <?
                if($val[csf("wo_basis_id")]==1)
                {
                    $des_width='120px;';
                    $remarks_width="90";
					$item_size="60";
					$item_group="90";
					$uom="50";
					$qnty="60";
					$rate="60";
                }
                else
                {
                    $remarks_width="90";
                    $des_width='130px;';
					$item_size="80";
					$item_group="100";
					$uom="50";
					$qnty="80";
					$rate="70";

                }
                 if($val[csf("wo_basis_id")]==1){
                    echo "<td width=\"120\" align=\"center\">";
                }
				if($pi_mrr_data[$val[csf("item_id")]]>0) $disable_field='disabled="disabled"'; else $disable_field='';
				?>
                    <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_no[]" id="txt_req_no_<? echo $i;?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("requ_no")];?>" readonly />
                     <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("item_id")];?>" readonly />
                     <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_dtls_id")];?>" readonly />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $val[csf("requisition_no")];?>" readonly />
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="<? echo $val[csf("id")]; ?>" />
                <? if($val[csf("wo_basis_id")]==1){
                echo "</td>";
                } ?>
                 <!-- This is for requisition number selected in WO Basis END -->
                  <td width="110" align="center">
                    <?
                        echo create_drop_down( "cbogroup_".$i, $item_group, "select id,item_name  from lib_item_group","id,item_name", 1, "Select", $val[csf("item_group")], "",1 ,"","","","","","","cbogroup[]","cbogroup_".$i);

                    ?>
                </td>
                <td width="130" align="center">
                    <input type="text" name="txt_item_acct[]" id="txt_item_acct_<? echo $i;?>" class="text_boxes" style="width:100px" readonly value="<? echo $val[csf("item_account")];?>" title="<? echo $val[csf("item_account")];?>" />
                </td>
                <td width="140" align="center">
                    <input type="text" name="txt_item_desc[]" id="txt_item_desc_<? echo $i;?>" class="text_boxes" style="width:<? echo $des_width; ?>" readonly value="<? echo $val[csf("item_description")];?>" title="<? echo $val[csf("item_description")];?>" />
                </td>
                <td width="80">
                <?
					echo create_drop_down( "cbo_item_category_".$i,80,$item_category,'', 1, '-- Select --', $val[csf("item_category_id")],"",1,"4,11","","","","","","cbo_item_category[]","cbo_item_category_".$i);
                ?>
                </td>

                <td width="100" align="center">
                    <input type="text" name="txt_item_size[]" id="txt_item_size_<? echo $i;?>" class="text_boxes"  style="width:<? echo $item_size;?>px" value="<? echo $val[csf("item_size")];?>" />
                </td>

                 <td width="70"  align="center">
                    <input type="text" name="txt_item_brand[]" id="txt_item_brand_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("brand")];?>" />
                </td>
                <td width="70"  align="center">
                    <? echo create_drop_down( "cboorigin_".$i, 60, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name","id,country_name", 1, "Select", $val[csf("origin")], "",0,"","","","","","","cboorigin[]","cboorigin_".$i ); ?>
                </td>
                <td width="70"  align="center">
                    <input type="text" name="txt_item_model[]" id="txt_item_model_<? echo $i;?>" class="text_boxes" style="width:65px" value="<? echo $val[csf("model")];?>" />
                </td>
                 <td width="60"  align="center">
				 <?
                 $company=$val[csf("company_name")];$buyer_id=$val[csf("buyer_id")];$season_id=$val[csf("season_id")];
                 echo create_drop_down( "cbo_buyer_".$i, 60, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --",$buyer_id, "load_drop_down( 'requires/stationary_work_order_controller', this.value+'_'+$i, 'load_drop_down_season', 'season_td_$i'); ",0,"","","","","","","cbo_buyer[]","cbo_buyer_".$i );

                ?>
                </td>
                 <td width="60" id="season_td_<? echo $i;?>"  align="center">
                    <?
                    echo create_drop_down( "cbo_season_".$i, 60, "select id,season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name","id,season_name", 1, "-- Select --",$season_id, "",0,"","","","","","","cbo_season[]","cbo_season_".$i );

                ?>
              	</td>
                <td width="50" align="center">
                    <?
                        echo create_drop_down( "cbouom_".$i,$uom, $unit_of_measurement,"", 1, "Select", $val[csf("uom")], "",1,"","","","","","","cbouom[]","cbouom_".$i );

                    ?>
                </td>

                <? if($val[csf("wo_basis_id")]==1){
                    echo "<td width=\"70\" align=\"center\">";
                } ?>
                    <input type="<? if($val[csf("wo_basis_id")]==1)echo 'text'; else echo 'hidden';?>" name="txt_req_qnty[]" id="txt_req_qnty_<? echo $i;?>" class="text_boxes_numeric" style="width:<? echo $qnty;?>px" readonly value="<? echo number_format($val[csf("req_quantity")],2,'.','');?>" />
                <? if($val[csf("wo_basis_id")]==1){
                    echo "</td>";
                } ?>

                <td width="100">
                    <input type="text" name="txt_quantity[]" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:<? echo $qnty;?>px" value="<? echo number_format($val[csf("supplier_order_quantity")],2,'.','');?>" />	<!-- This is wo qnty here -->
                </td>
                <td width="90">
                    <input type="text" name="txt_rate[]" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:<? echo $rate;?>px" value="<? echo number_format($val[csf("gross_rate")],4,'.','');?>" <? echo $disable_field;?> />
                </td>
                <td width="80">
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo number_format($val[csf("gross_amount")],4,'.',''); $tot_amount += $val[csf("gross_amount")];?>" />
                </td>
                <td width="<? echo $remarks_width;?>">
                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:<? echo $remarks_width;?>px" value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/>
                </td>
                <? if($val[csf("wo_basis_id")]==1){?>
                 <td >
                      <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
                <? }else{  ?>
                <td>
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo number_format($tot_amount,4,'.','');?>" style="width:80px;" readonly/>
                            </td>
                            <td>&nbsp;</td>
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
                            <td align="right" colspan="8">Upcharge Remarks:</td>
                            <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character"  value="<? echo $val[csf("upcharge_remarks")];?>"/></td>
                            <td>Upcharge</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $val[csf("up_charge")];?>" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
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
                            <td align="right" colspan="8">Discount Remarks:</td>
                            <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character"  value="<? echo $val[csf("discount_remarks")];?>"/></td>
                            <td>Discount</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $val[csf("discount")];?>" style="width:80px;" onKeyUp="calculate_total_amount(2)" />
                            </td>
                            <td>&nbsp;</td>
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>Net Total</td>
                            <td style="text-align:center">
                                <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo number_format($val[csf("net_wo_amount")],4,'.','');?>" style="width:80px;" readonly/>
                            </td>
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
 if ($action=="stationary_work_print")
{
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?
    extract($_REQUEST);
	$data=explode('*',$data);
    $formate_id=$data[19];
    $company=$data[0];
    $location=$data[17];
    $template_id=$data[18];
    if($template_id==2) $align_cond='center'; else $align_cond='right';

	echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');

    if($data[7]==3 || $data[7]==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4 FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id, web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4 FROM  lib_supplier WHERE id = $data[3]");
    }
	
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
    $com_dtls = fnc_company_location_address($company, $location, 2);
    ?>

	<div id="table_row" style="width:930px;">
    <?
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
    $loc_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$location=return_field_value("location_name","lib_location","company_id=$data[0]" );
	$address=return_field_value("address","lib_location","company_id=$data[0]");
    $marchant_id=return_field_value("dealing_marchant","wo_non_order_info_mst","id=$data[15]");
	$inserted_by=return_field_value("inserted_by","wo_non_order_info_mst","id=$data[15]");
	$is_approved = return_field_value("is_approved","wo_non_order_info_mst","id =$data[15] and status_active=1 and is_approved in(1,3)");
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	//$location=return_field_value("location_name","lib_location","company_id=$data[1]");
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    //$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$importer_name_library = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');
	$marchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where id in($marchant_id)",'id','team_member_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season ",'id','season_name');

    $name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$company and status_active=1 and module_id=5 and menu_id=119");

    $approved_msg = '';
    if($db_type==0)
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($data[4],'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=16 and status_active=1 and is_deleted=0";
    }
    else
    {
        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($data[4], "", "",1)."' and company_id='$data[0]')) and page_id=16 and status_active=1 and is_deleted=0";
    }

    //echo  $approval_status; die;
    $approval_status=sql_select($approval_status);

    if($approval_status[0][csf('approval_need')] == 1)
    {
        if($is_approved==1 || $is_approved==3){
            echo '<style > body{ background-image: url("../../img/approved.gif"); } </style>';
            $approved_msg = "Approved";
        }
        else{
            echo '<style > body{ background-image: url("../../img/draft.gif"); } </style>';
            $approved_msg = "Draft";
        }
    }

    if($data[7]==3 || $data[7]==5)
    {
        $supplier = $company_library[$data[3]];
    }
    else
    {
        $supplier = $supplier_name_library[$data[3]];
    }

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	 	$i = 0;
		$total_ammount = 0;
		$varcode_booking_no=$data[1];
		?>

            <table align="center" cellspacing="0" width="900" >
                <tr>
                    <?
                        $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td rowspan="3" width="70">
                    <?
                    foreach($data_array as $img_row)
                    {
                        if ($formate_id==134)
                        {
                            ?>
                            <img src="../../../<? echo $com_dtls[2]; ?>" height="50" width="60">
                            <?
                        }
                        else
                        {
                            ?>
                            <img src="../../<? echo $com_dtls[1]; ?>" height="50" width="60">
                            <?
                        }
                    }
                    ?>
                    </td>
                    <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; //echo $company_library[$data[0]]; ?></strong></td>
		       	    <td width="200"><b><?="ISO Number  :".$name_iso_Array[0]["ISO_NO"]?></b></td>
                    <td rowspan="3"  id="barcode_img_id"> </td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><strong><? echo show_company($data[0],'',''); ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><strong style="font-size:25px;">Work Order</strong></td>
                </tr>
            </table><br>

            <table align="center" cellspacing="0" width="900" >
                <tr>
                    <td width="150" align="left" style="font-size: 16px;"><strong>To</strong>,&nbsp;<? echo $data[10]; ?></td>
                    <td width="80" style="font-size: 16px;"><strong>PO</strong></td>
                    <td width="100" align="left" style="font-size: 14px;"> : <? echo "<strong>".$data[1]."</strong>"; ?></td>
                    <td width="80" align="left" ><strong>Date</strong></td>
                    <td width="100" align="left"> : <? echo $data[4]; ?></td>
                </tr>
                <tr>
                    <td rowspan="4" style="font-size: 14px;"><? echo "<strong>".$supplier."</strong>"; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
                    <td width="100"><strong>Delivery Date </strong></td>
                    <td>: <? echo $data[9]; ?></td>
                    <td align="left"><strong>Place of Delivery</strong></td>
                	<td align="left" >: <? echo $data[14]; ?></td>
                </tr>
                <tr>
                	<td><strong>Currency</strong></td>
                	<td align="left">: <? echo $currency[$data[5]]; ?></td>
                    <!--                    <td align="left"><strong>Item Category</strong></td>
                	<td align="left" >: <? //echo $item_category[$data[2]]; ?></td>-->
                        <td><strong>Pay Mode</strong></td>
                	<td align="left" >: <? echo $pay_mode[$data[7]]; ?></td>
                </tr>
                 <tr>
                    <td align="left" ><strong>WO Basis</strong></td>
                	<td align="left" >: <? echo $wo_basis[$data[6]]; ?></td>
                        <td><strong>Location</strong></td>
                    <td>:<? echo $loc_arr[$data[17]] ?></td>
                </tr>
                <tr>
                    <td><strong>Dealing Merchant</strong></td>
                    <td>: &nbsp; <? echo $marchant_arr[$marchant_id] ?></td>
                </tr>
                <tr>
                	<td align="center" colspan="8" style="font-size:30px; color:#FF0000;" >&nbsp; <? echo $approved_msg; ?></td>
                </tr>
            </table>
          	<br>
            <table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="110" >Requisition No</th>
                    <th width="80" >Buyer</th>
                    <th width="60" >Season</th>
                    <th width="80" >Code</th>
                    <th width="80" >Item Category</th>
                    <th width="220" >Item Name & Description</th>
                    <th width="120" >Item Size</th>
                    <th width="50" >Order UOM</th>
                    <th width="70" style="display:none">Req.Qty</th>
                    <th width="70" >WO.Qty</th>
                    <th width="70" >Rate</th>
                    <th width="80">Amount</th>
                    <th >Remarks</th>
                </thead>
				<?

                $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
                $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
                $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
                //$reg_no=explode(',',$data[11]);
                $cond="";
                if($data[1]!="") $cond .= " and a.id='$data[15]'";
                //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
                $i=1;
                $sql_result= sql_select("select a.id,a.wo_number,a.currency_id,a.wo_number,a.up_charge,a.discount,a.net_wo_amount,a.upcharge_remarks,b.requisition_no,b.req_quantity,b.uom,d.id as prod_id,b.supplier_order_quantity,b.remarks,b.buyer_id,b.season_id,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account,d.item_code,b.item_category_id
                from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond");
					//print_r($sql_result[0][csf("remarks")]);//die;
                foreach($sql_result as $row)
                {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";

                $req_quantity=$row[csf('req_quantity')];
				$mst_id=$row[csf('id')];
                $req_quantity_sum += $req_quantity;

                $supplier_order_quantity=$row[csf('supplier_order_quantity')];
                $supplier_order_quantityl_sum += $supplier_order_quantity;

                $amount=$row[csf('gross_amount')];
                $total_amount+= $amount;

				$wo_amount=$row[csf("wo_amount")];
				$up_charge=$row[csf("up_charge")];
				$discount=$row[csf("discount")];
				$net_wo_amount=$row[csf("net_wo_amount")];
				$upcharge_remarks=$row[csf("upcharge_remarks")];
				$remarks=$row[csf("remarks")];
				//echo $remarks;die;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
                     <td align="center"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
                    <td align="center"><? echo $season_name_arr[$row[csf('season_id')]]; ?></td>
                    <td align="center"><? echo $row[csf('item_code')]; ?></td>
                    <td align="center"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                    <td><? echo $row[csf('item_size')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right" style="display:none"><? echo $row[csf('req_quantity')]; ?></td>
                    <td align="<? echo $align_cond;?>"><? echo $row[csf('supplier_order_quantity')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",""); ?></td>

                    <td><?= $remarks; ?></td>
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
                <td align="right" colspan="11" ><strong>Total :</strong></td>

                <td align="right" colspan="1"><? echo $word_total_amount=number_format($total_amount, 2, '.', ''); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="left" colspan="10">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                <td align="right" >Upcharge :&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
            </tr>
            <tr>
            	<td align="right" colspan="11">Discount :&nbsp;</td>
                <td align="right"><? echo number_format($discount,2,".","");  ?></td>
            </tr>
            <tr>
            	<td align="right" colspan="11"><strong>Net Total : </strong>&nbsp;</td>
                <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
            </tr>
            <tr>
            <tr>
                <td align="left" colspan="13"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
            </tr>
        </table>
        	<br/>
        <table width="900">
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
        </table>
        <table  width="900" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Condition/Note</th>
                </tr>
            </thead>
            <tbody>
            <?
            //echo "select terms_and_condition from wo_non_order_info_mst where id=$data[15]"; die;
            //$data_array=sql_select("select terms_and_condition from wo_non_order_info_mst where id=$data[15]");
            $data_array=sql_select("select terms from wo_booking_terms_condition where booking_no='$data[1]'");
            //echo $data_array[0][csf("terms_and_condition")]."jah";die;
            if ($data_array[0][csf("terms")]!="")
            {
        		$k=0;
        		foreach( $data_array as $row )
        		{

        			$k++;
        			echo "<tr id='settr_1' style='border:1px solid black;'> <td style='border:1px solid black;'>
        			$k<td style='border:1px solid black;'> ".$row[csf('terms')]."</td><br/> </tr>";

        		}
            }
            ?>
            </tbody>
        </table>
    	<br>
        <?
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=5 AND  mst_id =".$mst_id."  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=5 AND  mst_id =".$mst_id." order by  approved_no,approved_date");
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


        ?>
    <? if(count($approved_sql)>0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval Status </b></label>
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
    <? if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval / Un-Approval History </b></label>
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
    }
    ?>
        <!-- //approved status end-->
        <br/>
			 <?
                echo signature_table(55, $data[0],"900px",$data[18],70,$user_lib_name[$inserted_by]);
             ?>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>

<?
exit();
}
?>
<?
if ($action=="stationary_work_order_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');
    //print_r ($data); die;
    $company=$data[0];
    $location=$data[17];
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$company",'bin_no');

    $sql="select id from electronic_approval_setup where company_id=$company and page_id in(2588) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
	  $approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

    if($data[7]==3 || $data[7]==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4 FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id,web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4 FROM  lib_supplier WHERE id = $data[3]");
    }

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
        $com_dtls = fnc_company_location_address($company, $location, 2);
        ob_start();
        ?>

        <div id="table_row" style="width:1170px;">
        <?
        $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
        $loc_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
        $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
        $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
        $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
        $currency_id=return_field_value("currency_id as currency_id","wo_non_order_info_mst","id=$data[15]","currency_id");
        $dealing_marchant=return_field_value("dealing_marchant as dealing_marchant","wo_non_order_info_mst","id=$data[15]","dealing_marchant");
        $is_approved = return_field_value("is_approved","wo_non_order_info_mst","id =$data[15] and status_active=1 and is_approved in(1,3)");
        $is_approved_con = return_field_value("is_approved","wo_non_order_info_mst","id =$data[15] and status_active=1");
        //$dealing_marchant=return_field_value("team_member_name as team_member_name","lib_mkt_team_member_info","id=$data[15]","dealing_marchant");
        $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
        $marchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where id in($dealing_marchant)",'id','team_member_name');
        $buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
        $season_name_arr=return_library_array( "select id, season_name from lib_buyer_season ",'id','season_name');

        if($data[12]!="")
        {
            /*if($db_type==2)
            {
                $quot_ref=return_field_value("LISTAGG(requ_prefix_num , ',') WITHIN GROUP (ORDER BY requ_prefix_num) as our_ref","inv_purchase_requisition_mst","id in($data[12])","our_ref" );

            }
            else
            {
                $quot_ref=return_field_value("group_concat(requ_prefix_num ) as our_ref","inv_purchase_requisition_mst","id in($data[12])","our_ref" );
            }
            $req_no="";
            $req_no_id=array_unique(explode(",",$quot_ref));
            foreach($req_no_id as $reg_id)
            {
                if($req_no=="") $req_no=$reg_id; else $req_no.=",".$reg_id;
            }*/


            if($db_type==0)
            {
                $quot_factor_val=return_field_value("group_concat(c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id=$data[12]","value" );
            }
            else
            {
                //$quot_factor_val=return_field_value("LISTAGG(cast(c.value as  varchar2(4000)) , ',') WITHIN GROUP (ORDER BY c.value) as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c"," a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id=$data[12]","value" );
                /*$quot_factor_val=return_field_value("rtrim(xmlagg(xmlelement(e,c.value,',').extract('//text()') order by c.value).GetClobVal(),',') as value","inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c","  a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id=$data[12]","value" );

                $quot_factor_val = $quot_factor_val->load();*/ 

                $quot_result=sql_select("select c.value from inv_quot_evalu_mst a,inv_quot_evalu_dtls b, inv_quot_evalu_factor c where a.id=b.mst_id  and b.id=c.dtls_mst_id and c.evalu_factor_id=2 and a.requ_no_id=$data[12]");
                foreach($quot_result as $row)
                {                
                    $quot_factor_val .= $row[csf('value')].",";
                }
                $quot_factor_val=implode(",",array_unique(explode(",",chop($quot_factor_val,","))));
            }

            $quot_sys_id=return_field_value("system_id as system_id","inv_quot_evalu_mst","requ_no_id=$data[12]","system_id" );
            $sql_result=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, b.product_id as product_id, b.remarks from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id in($data[12])");
            $remark_data_arr=array();
            foreach($sql_result as $row)
            {
                $remark_data_arr[$row[csf('product_id')]]=$row[csf('remarks')];
                $req_data_arr[$row[csf('req_id')]]['requ_no']=$row[csf('requ_no')];
                $req_no.=$row[csf('requ_prefix_num')].",";
            }
            $req_no=implode(",",array_unique(explode(",",chop($req_no,","))));
        }
        $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
        if($data[7]==3 || $data[7]==5)
        {
            $supplier = $company_library[$data[3]];
        }
        else
        {
            $supplier = $supplier_name_library[$data[3]];
        }
            $i = 0;
            $total_ammount = 0;
            ?>
                <table align="center" cellspacing="0" width="1140px" >
                    <tr>
                        <td width="70" align="right"><img src="../../<? echo $com_dtls[2]; ?>" height="50" width="60"></td>
                        <td colspan="10" style="font-size:xx-large;" align="center"><strong><? echo $com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center"><strong><? echo $com_dtls[1]; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center"><strong><? //echo $data[16] ;?></strong></td>
                    </tr>
                </table>

                <table align="center" cellspacing="0" width="1170" >
                    <tr>
                        <td width="200" align="left"><strong>To</strong>,&nbsp;<? echo $data[10]; ?></td>
                        <td width="50" align="left" ></td>
                        <td width="120" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Date </strong></td>
                        <td width="150">:&nbsp;&nbsp;<? echo change_date_format($data[4]); ?></td>
                        <td align="left" width="120">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Our Ref.</strong></td>
                        <td width="150">:&nbsp;&nbsp;<? echo $req_no ?></td>

                    </tr>
                    <tr>
                        <td rowspan="4"><? echo $supplier; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; ?></td>
                        <td width="50"></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;<strong>WO No </strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $data[1];//$data[9]; ?></td>
                        <td align="left" >&nbsp;&nbsp;&nbsp;&nbsp;<strong>Quotation ID</strong></td>
                        <td>:&nbsp;&nbsp;<? echo $quot_sys_id ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Place of Delivery</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $data[14];//$currency[$data[5]]; ?></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Currency</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Delivery Date</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $data[9];//$currency[$data[5]]; ?></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Dealing Merchant</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $marchant_arr[$dealing_marchant]; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Location</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo  $loc_arr[$data[17]]; ?></td>
                        <td align="left"><? //echo $wo_basis[$data[6]]; ?></td>
                    </tr>
                    <tr>
                        
                        <td colspan="2" ><? if($bin_no) echo '<strong style="width: 150px;display: inline-block;">BIN</strong>: &nbsp;'.$bin_no; else echo '';?></td>
                        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Contact To</strong></td>
                        <td align="left">:&nbsp;&nbsp;<? echo $data[19]; ?></td>
                        <td align="left"><? //echo $wo_basis[$data[6]]; ?></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center"><strong><font size="4px"></td>
                    </tr>

                    <tr>
                        <td colspan="7" align="center"><strong><font size="4px"><? echo $item_category[$data[2]].' Purchase Order';//echo $data[16];?></font><b style="float:right; font-size:25px; color:#FF0000"> <?  if($is_approved==1 || $is_approved==3) echo "Approved";else echo " "; ?></b></strong></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="left">Dear Sir,<br><strong><? echo $company_library[$data[0]]; ?></strong> is Pleased to inform You that Your price offer has been accepted with the following terms .
        </td>
                    </tr>
                </table>
                <br>
                <table align="center" cellspacing="0" width="1170"  border="1" rules="all" class="rpt_table" >
                    <thead>
                        <th width="30">SL</th>
                        <th width="110" align="center">Req No</th>
                        <th width="80" align="center">Buyer</th>
                        <th width="60" align="center">Season</th>
                        <th width="80" align="center">Item Category</th>
                        <th width="200" align="center">Item Description</th>
                        <th width="120" align="center">Item Size</th>
                        <th width="70" align="center">Specification</th>
                        <th width="50" align="center">UOM</th>
                        <th width="70" align="center">Qty</th>
                        <th width="80" align="center">Rate</th>
                        <th width="100" align="right">Amount</th>
                        <th align="center">Remarks</th>
                    </thead>
                    <?


        //$product_id=return_field_value("b.product_id as product_id","inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b"," a.id=b.mst_id and a.id=$data[12]","product_id" );

                    //$reg_no=explode(',',$data[11]);
                    $cond="";
                    if($data[1]!="") $cond .= " and a.id='$data[15]'";
                    //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
                    $i=1;

                    $sql_result= sql_select("select a.id, a.wo_number, a.currency_id, a.wo_number, a.up_charge, a.discount, a.net_wo_amount, a.upcharge_remarks, b.requisition_no, b.req_quantity, b.uom, d.id as prod_id,b.buyer_id,b.season_id,b.supplier_order_quantity, b.remarks, b.gross_amount, b.gross_rate, d.item_description, d.item_size, d.item_group_id, d.item_account,  d.item_size,b.item_category_id
                    from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                    where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond");

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

                    $wo_amount=$row[csf("wo_amount")];
                    $up_charge=$row[csf("up_charge")];
                    $discount=$row[csf("discount")];
                    $net_wo_amount=$row[csf("net_wo_amount")];
                    $upcharge_remarks=$row[csf("upcharge_remarks")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $req_data_arr[$row[csf('requisition_no')]]['requ_no'] ; ?></td>
                        <td><p><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></p></td>
                        <td><p><? echo  $season_name_arr[$row[csf('season_id')]]; ?></p></td>
                        <td><p><? echo  $item_category[$row[csf('item_category_id')]]; ?></p></td>
                        <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                        <td><? echo  $row[csf('item_size')]; ?></td>
                        <td><? echo  $quot_factor_val; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>

                        <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",""); ?></td>
                        <td><? echo $row[csf('remarks')];//$remark_data_arr[$row[csf('prod_id')]];  ?></td>
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
                            <td align="right" colspan="11" ><strong>Total :</strong></td>
                        <?php /*?> <td align="right"><? //echo number_format($tot_wo_qty,2) ?></td>
        <td align="right"><? //echo number_format($supplier_order_quantityl_sum,0,'',',') ?></td><?php */?>
                        <td align="right"><? echo $word_total_amount=number_format($total_amount,2,".",""); ?></td>
                        <td align="right"><? ?></td>
                    </tr>
                    <tr>
                        <td colspan="10">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                        <td align="right" >Upcharge :&nbsp;</td>
                        <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="11">Discount :&nbsp;</td>
                        <td align="right"><? echo number_format($discount,2,".","");  ?></td>
                    </tr>
                    <tr>
                        <td align="right" colspan="11"><strong>Net Total : </strong>&nbsp;</td>
                        <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
                    </tr>
                    <tr>
                    <td align="left" colspan="13"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
                    </tr>
                </table>
                <br/>
                <table width="1080" align="center">
                    <tr>
                    <td colspan="12">&nbsp;  </td>
                    </tr>
                    <tr>
                    <td colspan="12">&nbsp; </td>
                    </tr>

                </table>
                <table  width="1170" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
                    <thead>
                        <tr style="border:1px solid black;">
                        <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Condition/Note</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    //echo "select terms_and_condition from wo_non_order_info_mst where id=$data[15]"; die;
                    //$data_array=sql_select("select terms_and_condition from wo_non_order_info_mst where id=$data[15]");
                    $data_array=sql_select("select terms  from wo_booking_terms_condition where booking_no='$data[1]'");

                    //echo $data_array[0][csf("terms_and_condition")]."jah";die;
                    if (count($data_array) > 0)
                    {
                        $k=0;
                        foreach( $data_array as $row )
                        {
                                $k++;
                                echo "<tr id='settr_1' style='border:1px solid black;'> <td style='border:1px solid black;'>
                                $k</td><td style='border:1px solid black;'> ".$row[csf('terms')]."</td></tr>";

                        }
                    }
                /*    else
                    {
                        //echo "select id, terms from  lib_terms_condition where is_default=1";die;
                        $i=0;
                        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
                        //echo count($data_array)."jahid";
                        foreach( $data_array as $row )
                        {
                            $i++;
                            ?>
                                <tr id="settr_1" align="" style="border:1px solid black;">
                                    <td style="border:1px solid black;">
                                    <? echo $i;?>
                                    </td>
                                    <td style="border:1px solid black;">
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
                        }
                    } */
                    ?>
                    </tbody>
                </table>
                <table width="1170" align="center">
                    <tr>
                        <td colspan="12">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="12">Your scheduled delivery with quality and co-operation will be highly appreciated.<br><br>
                        Thank you
                    </td>
                    </tr>
                    <tr>
                        <td colspan="12">&nbsp;</td>
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
                <?
                    echo signature_table(30, $data[0],"1170px",$data[18],1);
                ?>

        <?

            $mailBody=ob_get_contents();
            ob_clean();
            echo $mailBody;
            $mail_data = $data[20];
            $cbo_company_id = $data[0];

            //Mail send------------------------------------------
            list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

            if($is_mail_send==1){
            // require_once('../../../mailer/class.phpmailer.php');
                include('../../../auto_mail/setting/mail_setting.php');
            
                $mailToArr=array();
                if($msil_address){$mailToArr[]=$msil_address;}

                //-----------------------------
                $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.entry_form=5 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
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
if ($action=="stationary_work_order_print3")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');
    // print_r ($data); die;
    $company=$data[0];
    $location=$data[17];
    $delivery_dtls_info=explode('__',$data[19]);
    
    if($data[7]==3 || $data[7]==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4 FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id, web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4 FROM  lib_supplier WHERE id = $data[3]");
    }

    foreach($sql_supplier as $supplier_data)
        {
            $row_mst[csf('supplier_id')];
            if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
            if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
            if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
            if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
            $supplier_address = $address_1;
            $supplier_phone =$contact_no;
            $supplier_email = $email;
        }
        $com_dtls = fnc_company_location_address($company, $location, 2);
        ?>

        <div id="table_row" style="width:800px;">
        <?
        $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
        $origin_arr=return_library_array("select id, country_name from  lib_country","id","country_name");
        $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
        // $currency_id=return_field_value("currency_id as currency_id","wo_non_order_info_mst","id=$data[15]","currency_id");

        $cond="";
        if($data[1]!="") $cond .= " and a.id='$data[15]'";
        $sql_result= sql_select("select a.id as ID, a.wo_number as WO_NUMBER, a.currency_id as CURRENCY_ID, a.wo_number as WO_NUMBER, a.up_charge as UP_CHARGE, a.discount as DISCOUNT, a.net_wo_amount as NET_WO_AMOUNT, b.uom as UOM, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY,  b.gross_amount as GROSS_AMOUNT, b.gross_rate as GROSS_RATE, d.item_description as ITEM_DESCRIPTION, d.item_size as ITEM_SIZE, b.item_category_id as ITEM_CATEGORY_ID, d.brand_name as BRAND_NAME,d.model as MODEL, d.origin as ORIGIN, a.CURRENCY_ID
        from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
        where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond");
        $category_all='';
        foreach($sql_result as $value){
            $category_all.=$value['ITEM_CATEGORY_ID'].',';
        }
        $category_arr=array_unique(explode(",",chop($category_all,',')));
        $category_name='';

        foreach($category_arr as $value){
            $category_name.=$item_category[$value].', ';
        }
            
            if($data[7]==3 || $data[7]==5)
            {
                $supplier = $company_library[$data[3]];
            }
            else
            {
                $supplier = $supplier_name_library[$data[3]];
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
                        <td colspan="10" align="right"><strong><? echo "DATE : ".change_date_format($data[4]); ?></strong></td>
                    </tr>
                    <tr>
                        <td ><strong>BILL TO</strong></td>
                        <td colspan="10"><strong><? echo $com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr>
                        <td ><strong>P.O. Ref:</strong></td>
                        <td colspan="10" ><strong><? echo $data[1]; ?></strong></td>
                    </tr>
                </table>
                
                <br>
                <table width="900">
                    <tr>
                        <td width="450" valign='top'>
                            <table border="1" rules="all" class="rpt_table">
                                <tr>
                                    <td width="400">
                                        <strong>VENDOR &nbsp;&nbsp;:&nbsp;&nbsp;</strong><strong style="font-size:15px;"><? echo $supplier; ?></strong><br>
                                        <strong>ADDRESS &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $supplier_address; ?><br>
                                        <strong>ATTEN. &nbsp;&nbsp;:&nbsp;&nbsp;</strong><? echo $data[10]; ?><br>
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
                        <td colspan="10" style="text-transform: uppercase;" ><? echo $pay_mode[$data[7]]; ?></td>
                    </tr>
                </table>
                <br>
                <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
                    <thead>
                        <th width="30">SL</th>
                        <th width="200" align="center">Item</th>
                        <th width="120" align="center">Specification</th>
                        <th width="170" align="center">Brand/ Origin</th>
                        <th width="70" align="center">Qty</th>
                        <th width="70" align="center">Unit</th>
                        <th width="70" align="center">Currency</th>
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

                    $up_charge=$row["UP_CHARGE"];
                    $discount=$row["DISCOUNT"];
                    $net_wo_amount=$row["NET_WO_AMOUNT"];

                    $brand_model_origin='';
                    if($row['BRAND_NAME']!=''){$brand_model_origin.= $row['BRAND_NAME'].", ";}
                    if($row['MODEL']!=''){$brand_model_origin.= $row['MODEL'].", ";}
                    if($row['ORIGIN']!=''){$brand_model_origin.= $origin_arr[$row['ORIGIN']];}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $row['ITEM_DESCRIPTION']; ?></td>
                        <td><? echo  $row['ITEM_SIZE'];; ?></td>
                        <td ><? echo $brand_model_origin; ?></td>
                        <td align="right"><? echo number_format($row['SUPPLIER_ORDER_QUANTITY'],2); ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                        <td align="center"><? echo $currency[$row['CURRENCY_ID']]; ?></td>

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
                        <td align="left" valign="middle" colspan="6" rowspan="4"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
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
                <table  width="900" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
                    <thead>
                        <tr style="border:1px solid black;">
                        <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Conditions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("select terms as TERMS from wo_booking_terms_condition where booking_no='$data[1]'");
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
                </br>
                <?
                 echo signature_table(55, $data[0],"900px",$data[18]);
    exit();

}

if ($action=="stationary_work_order_po_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
	$cbo_template_id=$data[18];
	$show=$data[19];
	// print_r ($data); die;
	echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');
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
	
    $sql_company=sql_select("select id, company_name,tin_number,bin_no from lib_company where status_active=1 and is_deleted=0 and id=$data[0]");
    $com_name=$sql_company[0][csf("company_name")];
    $tin_num=$sql_company[0][csf("tin_number")];
    $bin_num=$sql_company[0][csf("bin_no")]; 

    $location=return_field_value('location_name','lib_location',"company_id='$data[0]'",'location_name' );
    $address=return_field_value("city as address","lib_company","id=$data[0]",'address');
	$address1=return_library_array('SELECT id,contact_no FROM lib_supplier','id','contact_no');
	$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $company_name_library = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');
	$requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
	$requisition_department=return_library_array( "select id,department_id from  inv_purchase_requisition_mst", "id","department_id"  );
	$department=return_library_array( "select id,department_name from lib_department ", "id","department_name"  );
	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_data = sql_select("SELECT id, wo_number_prefix_num, pay_mode, wo_number, buyer_po, requisition_no, contact, wo_date, currency_id, supplier_id, attention, buyer_name, style, item_category, DELIVERY_PLACE,delivery_date, remarks,is_approved, is_approved,inserted_by, payterm_id,reference,wo_type,up_charge,discount, location_id, tenor, lc_type  FROM  wo_non_order_info_mst WHERE id = $data[15]");

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
        $pay_mode= $row[csf("pay_mode")];
        $tenor= $row[csf("tenor")];
        $location_id= $row[csf("location_id")];
        $cbo_lc_type= $row[csf("lc_type")];
        $delivery_addrs=$row[csf("DELIVERY_PLACE")];
		
        if($row[csf('is_approved')]==3){
            $is_approved=1;
        }else{
            $is_approved=$row[csf('is_approved')];
        }
	}

    $com_address=return_field_value('address','lib_location',"id=$location_id",'address' );
    
	if($pay_mode==3 || $pay_mode==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4 FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id,web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4 FROM  lib_supplier WHERE id = $supplier_id");
    }

    if($pay_mode==3 || $pay_mode==5)
    {
        $supplier = $company_name_library[$supplier_id];
    }
    else
    {
        $supplier = $supplier_name_library[$supplier_id];
    }

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
	$req_no= explode(",",$data[12]);
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
	//$group_logo=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");

    $company_id = $sql_company[0][csf("id")];

    $com_dtls = fnc_company_location_address($company_id,'', 2);

    ob_start();
	?>
	<div class="fontincrease">
    <table cellspacing="0" width="1000" align="center" >
        <tr>
            <td rowspan="2" width="100"><img src="<?= "../../".$com_dtls[2];?>" height="60" width="90" alt="Group Logo"></td>
            <td colspan="3" style="font-size:25pt;" align="center"><strong><? echo $sql_group;?></strong></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:21pt;"><strong>Purchase Order- General Purchase</strong></td>
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
            <td width="580" valign="top"><? echo $com_address; ?></td>
			<td width="160" align="left" class="bordertbl"><strong>P.O. Number:</strong></td>
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
			<td align="left" valign="top" rowspan="2" class="bordertbl"><strong>Delivery Date:</strong></td>
            <td align="left" valign="top" rowspan="2" class="bordertbl"><strong><? echo $delivery_date; ?></strong></td>
        </tr>
        <tr>
        <td colspan="2"><strong>Delivery Address:</strong> <? echo  $delivery_addrs; ?><br>         
        </tr>
        <tr>
            <td><strong>Contact:</strong></td>
            <td align="left" ><? echo $contact_per; ?></td>
            <td align="left" valign="top" class="bordertbl"><strong>Req No:</strong></td>
            <td align="left" valign="top" class="bordertbl"><strong><? echo $req_num; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td>
            <td align="left" style="font-size:15pt;"><strong><? echo $supplier; ?></strong></td>
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
            if($cbo_lc_type>0 && $payterm_id!=0){
                echo $lc_type[$cbo_lc_type]." / ";
            }
            if($cbo_lc_type>0 && $payterm_id==0){
                echo $lc_type[$cbo_lc_type];
            }
            if($tenor){echo "LC ".$tenor." Days";}
            else{echo $pay_term[$payterm_id];} ?></td>
        </tr>
        <tr>
            <td><strong></strong></td>
            <td align="left" ><? //echo $department_num ; ?></td>
            <td align="left" class="bordertbl"><strong>PO Status:</strong></td>
            <td align="left" class="bordertbl"><? echo $approved_note; ?></td>
        </tr>
        <tr>
            <td><strong></strong></td>
            <td align="left" ><? //echo $department_num ; ?></td>
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
                <th width="140" >Item</th>
                <? if($show){?> <th width="100" >Size/MSR</th> <?}?> 
                <th width="<?=$show?150:420;?>" >Declaration Details</th>
                <? if($show){?> <th width="170">Narration</th> <?}?> 
                <th width="70" >Unit</th>
                <th width="100">Quantity</th>
                <th width="60" >Rate</th>
                <th >Amount</th>
            </thead>
			<tbody>
     <?
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[15]'";
    $i=1;

    $sql_result= sql_select("SELECT a.id, a.wo_number, a.currency_id, a.wo_number, a.up_charge, a.discount, a.net_wo_amount, a.upcharge_remarks, b.requisition_no, b.req_quantity, b.uom, d.id as prod_id,b.buyer_id,b.season_id,b.supplier_order_quantity, b.remarks, b.gross_amount, b.rate as net_rate, b.gross_rate, d.item_description, d.item_size,d.model, d.item_group_id, d.item_account,d.item_number,b.item_category_id,e.brand_name,e.origin
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

        $narration="";
        if($row[csf('model')]){$narration=$row[csf('model')];}
        if($row[csf('brand_name')]){if($narration){$narration.=", ".$row[csf('brand_name')];}else{$narration=$row[csf('brand_name')];}}
        if($row[csf('item_number')]){if($narration){$narration.=", ".$row[csf('item_number')];}else{$narration=$row[csf('item_number')];}}
		?>
        <tr bgcolor="#FFFFFF">
            <td align="center" ><? echo $i; ?></td>
            <td ><? echo $row[csf('item_description')]; ?></td>
            <? if($show){ ?> <td ><? echo $row[csf('item_size')]; ?></td> <? } ?> 
			<td ><? echo implode("<br>",explode(">",$row[csf('remarks')])); ?></td>
			<? if($show){ ?> <td ><? echo $narration; ?></td> <? } ?> 
            <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
            <td align="right"><? echo $row[csf('supplier_order_quantity')]; ?></td>
            <td align="right"><? echo number_format($row[csf('gross_rate')],2); ?></td>
            <td align="right"><? echo number_format($row[csf('gross_amount')],2);?></td>
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
	?>
	<tr >
        <td align="left" colspan="<?=$show?6:4;?>" rowspan="4"></td>
        <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
        <td align="right"><? echo number_format($total_amount,2); ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2" ><strong>Discount</strong></td>
        <td align="right"><? echo number_format($discount,2); ?></td>
	</tr>
	<tr >
        <td align="right" colspan="2"><strong>PO Charge</strong></td>
        <td align="right"><? echo number_format($upcharge,2); ?></td>
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
    <?  //echo get_spacial_instruction($work_order_no,"1000px");?>
	<div><strong style="font-size:15pt;">Terms & Conditions:</strong></div>
	<?
	    $sql_term= sql_select("select terms from wo_booking_terms_condition where booking_no='$work_order_no' ");
		$i=1;
	foreach ($sql_term as $value) {
		echo $i.". ".$value[csf('terms')]."</br>";
		$i++;
	}
	?>
	<br/>

		<?
		    // $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			// echo signature_table(59, $data[0], "1000px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
            // echo signature_table(55, $data[0],"1080px",$data[18]);
			if ($cbo_template_id != '') {
				$template_id = " and template_id=$cbo_template_id ";
			}

			$sql = sql_select("select designation,name,user_id,prepared_by from variable_settings_signature where report_id=55 and company_id='$data[0]' $template_id order by sequence_no");

			$signature_sql = sql_select("SELECT c.master_tble_id as MASTER_TBLE_ID,c.image_location as IMAGE_LOCATION  from variable_settings_signature a, electronic_approval_setup b, common_photo_library c where a.user_id=b.user_id and a.user_id=c.master_tble_id and a.report_id=55 and a.company_id='$data[0]' and a.template_id=$cbo_template_id and b.page_id=627 and b.entry_form=5 and b.company_id=$data[0] and c.form_name='user_signature'");
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

       

            $mailBody=ob_get_contents();
            ob_clean();
            echo $mailBody;
            $mail_data = $data[20];
            $cbo_company_id = $data[0];

            //Mail send------------------------------------------
            list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);
       
            if($is_mail_send==1){
               // require_once('../../../mailer/class.phpmailer.php');
                include('../../../auto_mail/setting/mail_setting.php');
              
                $mailToArr=array();
                if($msil_address){$mailToArr[]=$msil_address;}

                //-----------------------------
                $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.entry_form=5 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
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

if ($action=="stationary_work_order_print4")
{
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?
    extract($_REQUEST);
	$data=explode('*',$data);
    $formate_id=$data[19];
    $company=$data[0];
    $location=$data[17];
    $template_id=$data[18];
    if($template_id==2) $align_cond='center'; else $align_cond='right';

	echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');

    if($data[7]==3 || $data[7]==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4 FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id,web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4 FROM  lib_supplier WHERE id = $data[3]");
    }

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
    $com_dtls = fnc_company_location_address($company, $location, 1);
    ?>

	<div id="table_row" style="width:930px;">
    <?
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
    $loc_arr=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
    $user_arr=return_library_array( "SELECT id, user_name from user_passwd", "id", "user_name"  );
    $marchant_id=return_field_value("dealing_marchant","wo_non_order_info_mst","id=$data[15]");
	$inserted_by=return_field_value("inserted_by","wo_non_order_info_mst","id=$data[15]");
	$is_approved_val = return_field_value("is_approved","wo_non_order_info_mst","id =$data[15] and status_active=1 and is_approved in(1,3)");
	$lib_country_arr=return_library_array( "SELECT id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$marchant_arr=return_library_array( "SELECT id, team_member_name from lib_mkt_team_member_info where id in($marchant_id)",'id','team_member_name');
	$buyer_name_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "SELECT id, season_name from lib_buyer_season ",'id','season_name');
    $company_info=sql_select("SELECT EMAIL,WEBSITE from lib_company where id=$data[0]");
    $is_approved_val_arr=array(1=>"Approved",3=>"Partial Approved");
    $i = 0;
    $total_ammount = 0;

    if($data[7]==3 || $data[7]==5)
    {
        $supplier = $company_library[$data[3]];
    }
    else
    {
        $supplier = $supplier_name_library[$data[3]];
    }
    ?>
        <table align="center" cellspacing="0" width="900" >
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
                <td colspan="2" align="center"><strong><? echo $company_info[0]["EMAIL"].", ".$company_info[0]["WEBSITE"];  ?></strong></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><strong style="font-size:25px;">Work Order</strong></td>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tr>
                <td width="200" colspan="2" align="left" style="font-size: 16px;"><strong>To</strong>,&nbsp;<? echo $data[10]; ?></td>
                <td width="80" style="font-size: 16px;"><strong>PO</strong></td>
                <td width="100" align="left" style="font-size: 14px;"> : <? echo "<strong>".$data[1]."</strong>"; ?></td>
                <td width="80" align="left" ><strong>Date</strong></td>
                <td width="100" align="left"> : <? echo $data[4]; ?></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2"  style="font-size: 14px;"><? echo "<strong>".$supplier."</strong>"; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; ?></td>
                <td width="100"><strong>Delivery Date </strong></td>
                <td>: &nbsp; <? echo $data[9]; ?></td>
                <td align="left"><strong>Place of Delivery</strong></td>
                <td align="left" >: &nbsp; <? echo $data[14]; ?></td>
            </tr>
            <tr>
                <td><strong>Currency</strong></td>
                <td align="left">: &nbsp; <? echo $currency[$data[5]]; ?></td>
                <td><strong>Pay Mode</strong></td>
                <td align="left" >: &nbsp; <? echo $pay_mode[$data[7]]; ?></td>
            </tr>
            <tr>
                <td width="80" align="left" ><strong>Cell</strong></td>
                <td width="120" align="left" >: &nbsp; <? echo $supplier_phone; ?></td>
                <td align="left" ><strong>WO Basis</strong></td>
                <td align="left" >: &nbsp; <? echo $wo_basis[$data[6]]; ?></td>
                <td><strong>Location</strong></td>
                <td>:<? echo $loc_arr[$data[17]] ?></td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>: &nbsp; <? echo $supplier_email; ?></td>
                <td><strong>Dealing Merchant</strong></td>
                <td>: &nbsp; <? echo $marchant_arr[$marchant_id] ?></td>
                <td><strong>Pay Term</strong></td>
                <td>: &nbsp; <? echo $pay_term[$data[18]] ?></td>
            </tr>
            <tr>
                <td><strong>Remarks</strong></td>
                <td>: &nbsp; <? echo $data[19]; ?></td>
                <td><strong>Contact To</strong></td>
                <td>: &nbsp; <? echo $data[20]; ?></td>
                <td><strong>Tenor</strong></td>
                <td>: &nbsp; <? echo $data[21]; ?></td>
            </tr>
            <tr>
                <td align="center" colspan="6" style="font-size:30px; color:#FF0000;" >&nbsp; <? echo $is_approved_val_arr[$is_approved_val]; ?></td>
            </tr>
        </table>
        <br>
        <table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
            <br>
            <thead>
                <th width="30">SL</th>
                <th width="110" >Requisition No</th>
                <th width="80" >Buyer</th>
                <th width="60" >Season</th>
                <th width="80" >Code</th>
                <th width="80" >Item Category</th>
                <th width="220" >Item Name & Description</th>
                <th width="120" >Item Size</th>
                <th width="50" >Order UOM</th>
                <th width="70" style="display:none">Req.Qty</th>
                <th width="70" >WO.Qty</th>
                <th width="70" >Rate</th>
                <th width="80">Amount</th>
                <th >Remarks</th>
            </thead>
            <tbody>
            <?

                $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
                $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
                $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
                //$reg_no=explode(',',$data[11]);
                $cond="";
                if($data[1]!="") $cond .= " and a.id='$data[15]'";
                //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
                $i=1;
                $sql_result= sql_select("select a.id,a.wo_number,a.currency_id,a.wo_number,a.up_charge,a.discount,a.net_wo_amount,a.upcharge_remarks,b.requisition_no,b.req_quantity,b.uom,d.id as prod_id,b.supplier_order_quantity,b.remarks,b.buyer_id,b.season_id,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account,d.item_code,b.item_category_id
                from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond");
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}

                    $req_quantity=$row[csf('req_quantity')];
                    $mst_id=$row[csf('id')];
                    $req_quantity_sum += $req_quantity;

                    $supplier_order_quantity=$row[csf('supplier_order_quantity')];
                    $supplier_order_quantityl_sum += $supplier_order_quantity;

                    $amount=$row[csf('gross_amount')];
                    $total_amount+= $amount;

                    $wo_amount=$row[csf("wo_amount")];
                    $up_charge=$row[csf("up_charge")];
                    $discount=$row[csf("discount")];
                    $net_wo_amount=$row[csf("net_wo_amount")];
                    $upcharge_remarks=$row[csf("upcharge_remarks")];
                    $remarks=$row[csf("remarks")];
                    //echo $remarks;die;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
                        <td align="center"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
                        <td align="center"><? echo $season_name_arr[$row[csf('season_id')]]; ?></td>
                        <td align="center"><? echo $row[csf('item_code')]; ?></td>
                        <td align="center"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                        <td><? echo $row[csf('item_size')]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right" style="display:none"><? echo $row[csf('req_quantity')]; ?></td>
                        <td align="<? echo $align_cond;?>"><? echo $row[csf('supplier_order_quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",""); ?></td>

                        <td><?= $remarks; ?></td>
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
                    <td align="right" colspan="11" ><strong>Total :</strong></td>

                    <td align="right" colspan="1"><? echo $word_total_amount=number_format($total_amount, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" colspan="10">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                    <td align="right" >Upcharge :&nbsp;</td>
                    <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="11">Discount :&nbsp;</td>
                    <td align="right"><? echo number_format($discount,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="11"><strong>Net Total : </strong>&nbsp;</td>
                    <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
                </tr>
                <tr>
                <tr>
                    <td align="left" colspan="13"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <table width="900">
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
        </table>
        <table  width="900" class="rpt_table" border="0" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms & Condition/Note</th>
                </tr>
            </thead>
            <tbody>
            <?
            $data_array=sql_select("select terms from wo_booking_terms_condition where booking_no='$data[1]'");
            if ($data_array[0][csf("terms")]!="")
            {
                $k=0;
                foreach( $data_array as $row )
                {

                    $k++;
                    echo "<tr id='settr_1' style='border:1px solid black;'> <td style='border:1px solid black;'>
                    $k<td style='border:1px solid black;'> ".$row[csf('terms')]."</td><br/> </tr>";

                }
            }
            ?>
            </tbody>
        </table>
        <br>
    <?
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=5 AND  mst_id =".$mst_id."  group by mst_id, approved_by order by  approved_by");
    // $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=5 AND  mst_id =".$mst_id." order by  approved_no,approved_date");
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql)>0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval Status </b></label>
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

    /*if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval / Un-Approval History </b></label>
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
    }*/
    ?>
    <!-- //approved status end-->
    <br/>
    <?
        echo signature_table(55, $data[0],"900px",$data[22],70,$user_lib_name[$inserted_by]);
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if ($action=="stationary_work_order_print5")
{
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?
    extract($_REQUEST);
	$data=explode('*',$data);
    $formate_id=$data[19];
    $company=$data[0];
    $location=$data[17];
    $template_id=$data[22];
    if($template_id==2) $align_cond='center'; else $align_cond='right';

	echo load_html_head_contents($data[16],"../../", 1, 1, $unicode,'','');

	if($data[7]==3 || $data[7]==5)
    {
        $sql_supplier = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4, CONTRACT_PERSON as contact_person FROM  lib_company WHERE id = $data[3]");
    }
    else{
        $sql_supplier = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id, web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4, contact_person as contact_person FROM  lib_supplier WHERE id = $data[3]");

    }

    foreach($sql_supplier as $supplier_data)
	{
		if($supplier_data[csf('address_1')]!='')$supplier_address = $supplier_data[csf('address_1')];else $supplier_address='';
		if($supplier_data[csf('contact_no')]!='')$supplier_phone = $supplier_data[csf('contact_no')];else $supplier_phone='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
		if($supplier_data[csf('email')]!='')$supplier_email = $supplier_data[csf('email')];else $supplier_email='';
		if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
	}
    $com_dtls = fnc_company_location_address($company, $location, 1);
    ?>

	<div id="table_row" style="width:930px;">
    <?
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
    $marchant_id=return_field_value("dealing_marchant","wo_non_order_info_mst","id=$data[15]");
	$inserted_by=return_field_value("inserted_by","wo_non_order_info_mst","id=$data[15]");
	$is_approved_val = return_field_value("is_approved","wo_non_order_info_mst","id =$data[15] and status_active=1 and is_approved in(1,3)");
	$lib_country_arr=return_library_array( "SELECT id,country_name from lib_country","id", "country_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
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

    if($data[7]==3 || $data[7]==5)
    {
        $supplier = $company_library[$data[3]];
    }
    else
    {
        $supplier = $supplier_name_library[$data[3]];
    }
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
                    <td colspan="2" align="center"><strong style="font-size:25px;">Work Order: <?=$data[1];?></strong></td>
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
                        <? echo "<strong>".$supplier."</strong><br>".$supplier_address."<br>TEL# ".$supplier_phone."<br>E-mail: ".$supplier_email."<br>Contact Person: ".$contact_person; ?>
                    </td>
                    <td colspan="7" valign="top">
                        <? echo "<strong>".$com_dtls[0]."</strong><br>".$com_dtls[1]."<br>TEL# ".$company_info[0]["CONTACT_NO"]."<br>E-mail: ".$company_info[0]["EMAIL"]."<br>Contact Person: ".$data[20]; ?>
                    </td>
                </tr>
                <tr>
                    <td width="80"><b>WO Date</b></td>
                    <td width="80"><? echo ':'.$data[4]; ?></td>
                    <td width="80"><b>Delivery Date</b></td>
                    <td width="80"><? echo $data[9]; ?></td>
                    <td width="90"><b>Pay Mode</b></td>
                    <td width="80"><? echo $pay_mode[$data[7]]; ?></td>
                    <td width="80"><b>WO Type</b></td>
                    <td width="80"><? echo $main_fabric_co_arr[$data[24]]; ?></td>
                    <td width="80"><b>Currency</b></td>
                    <td width="80"><? echo $currency[$data[5]]; ?></td>
                    <td width="80"><b>Pay Terms</b></td>
                    <td width="80"><? echo $pay_term[$data[18]] ?></td>
                    <td width="80"><b>WO Basis</b></td>
                    <td><? echo $wo_basis[$data[6]]; ?></td>
                </tr>
                <tr>
                    <td><b>Incoterm</b></td>
                    <td><? echo $incoterm[$data[23]]; ?></td>
                    <td><b>Tenor</b></td>
                    <td><? echo $data[21]; ?></td>
                    <td><b>Dealing Merchant</b></td>
                    <td colspan="3"><? echo $marchant_arr[$marchant_id] ?></td>
                    <td><b>Reference</b></td>
                    <td colspan="3"><? echo $data[25]; ?></td>
                    <td><b>BIN</b></td>
                    <td><? echo $company_info[0]["BIN_NO"]; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Place of Delivery:</b></td>
                    <td colspan="4"><? echo $data[14]; ?></td>
                    <td><b>Remarks</b></td>
                    <td colspan="7"><? echo $data[19]; ?></td>
                </tr>
            </tbody>
        </table>
        <table align="center" cellspacing="0" width="1180">
            <td align="center" style="font-size:30px; color:#FF0000;" >&nbsp; <? echo $is_approved_val_arr[$is_approved_val]; ?></td>
        </table>
        <br>
        <table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="110" >Requisition No</th>
                <th width="80" >Buyer</th>
                <th width="60" >Season</th>
                <th width="80" >Item Code</th>
                <th width="80" >Item Category</th>
                <th width="220" >Item Group & Description</th>
                <th width="120" >Item Size</th>
                <th width="50" >Order UOM</th>
                <th width="70" style="display:none">Req.Qty</th>
                <th width="70" >WO.Qty</th>
                <th width="70" >Rate</th>
                <th width="80">Amount</th>
                <th >Remarks</th>
            </thead>
            <tbody>
            <?

                $requisition_library=return_library_array( "select id,requ_no from  inv_purchase_requisition_mst", "id","requ_no"  );
                $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
                $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
                //$reg_no=explode(',',$data[11]);
                $cond="";
                if($data[1]!="") $cond .= " and a.id='$data[15]'";
                //if($reg_no!="") $cond .= " and b.requisition_no='$reg_no'";
                $i=1;
                $sql_result= sql_select("select a.id,a.wo_number,a.currency_id,a.wo_number,a.up_charge,a.discount,a.net_wo_amount,a.upcharge_remarks,a.discount_remarks,b.requisition_no,b.req_quantity,b.uom,d.id as prod_id,b.supplier_order_quantity,b.remarks,b.buyer_id,b.season_id,b.gross_amount,b.gross_rate,d.item_description,d.item_size,d.item_group_id,d.item_account,d.item_code,b.item_category_id
                from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master d
                where a.id=b.mst_id and b.item_id=d.id and b.status_active=1 and b.is_deleted=0 $cond");
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}

                    $req_quantity=$row[csf('req_quantity')];
                    $mst_id=$row[csf('id')];
                    $req_quantity_sum += $req_quantity;

                    $supplier_order_quantity=$row[csf('supplier_order_quantity')];
                    $supplier_order_quantityl_sum += $supplier_order_quantity;

                    $amount=$row[csf('gross_amount')];
                    $total_amount+= $amount;

                    $wo_amount=$row[csf("wo_amount")];
                    $up_charge=$row[csf("up_charge")];
                    $discount=$row[csf("discount")];
                    $net_wo_amount=$row[csf("net_wo_amount")];
                    $upcharge_remarks=$row[csf("upcharge_remarks")];
                    $discount_remarks=$row[csf("discount_remarks")];
                    $remarks=$row[csf("remarks")];
                    //echo $remarks;die;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $requisition_library[$row[csf('requisition_no')]]; ?></td>
                        <td align="center"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
                        <td align="center"><? echo $season_name_arr[$row[csf('season_id')]]; ?></td>
                        <td align="center"><? echo $row[csf('item_code')]; ?></td>
                        <td align="center"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td><? echo $item_name_arr[$row[csf('item_group_id')]].', '.$row[csf('item_description')]; ?></td>
                        <td><? echo $row[csf('item_size')]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right" style="display:none"><? echo $row[csf('req_quantity')]; ?></td>
                        <td align="<? echo $align_cond;?>"><? echo $row[csf('supplier_order_quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_rate')],4,".",""); ?></td>
                        <td align="right"><? echo number_format($row[csf('gross_amount')],2,".",""); ?></td>

                        <td><?= $remarks; ?></td>
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
                    <td align="right" colspan="11" ><strong>Total :</strong></td>

                    <td align="right" colspan="1"><? echo $word_total_amount=number_format($total_amount, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" colspan="10">Upcharge Remarks :&nbsp; <? echo $upcharge_remarks ?>&nbsp;&nbsp;</td>
                    <td align="right" >Upcharge :&nbsp;</td>
                    <td align="right"><? echo number_format($up_charge,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="left" colspan="10">Discount Remarks :&nbsp; <? echo $discount_remarks ?>&nbsp;&nbsp;</td>
                    <td align="right" >Discount :&nbsp;</td>
                    <td align="right"><? echo number_format($discount,2,".","");  ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="11"><strong>Net Total : </strong>&nbsp;</td>
                    <td align="right"><? echo number_format($net_wo_amount,2,".","");  ?></td>
                </tr>
                <tr>
                <tr>
                    <td align="left" colspan="13"><strong> Amount in words:&nbsp;</strong><? echo number_to_words($net_wo_amount,$currency[$carrency_id],$paysa_sent); ?>  </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <table width="900">
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
            <tr>
            <td colspan="12">&nbsp;   </td>
            </tr>
        </table>
        <?echo get_spacial_instruction($data[1],"1180px",146);?>
        <br>
    <?
    $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=5 AND  mst_id =".$mst_id."  group by mst_id, approved_by order by  approved_by");

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
        echo signature_table(55, $data[0],"1100px",$data[22],70,$user_lib_name[$inserted_by]);
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if($action == "stationary_work_order_po_print2"){
    extract($_REQUEST);
    $data = explode('*',$data);
    $company = $data[0];
    $mst_id = $data[1];
    $rpt_title = $data[2];
    $req_numbers_id = $data[3];
    $template_id = $data[4];

    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?
    $currency_sign_arr = array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    $company_library = return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
    $user_lib_name = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    $data_result = sql_select("SELECT wo_non_order_info_mst.wo_number as WO_NUMBER, wo_non_order_info_mst.wo_date as WO_DATE, wo_non_order_info_mst.wo_type AS WO_TYPE, wo_non_order_info_mst.PAYTERM_ID as PAYTERM_ID, wo_non_order_info_mst.supplier_id as SUPPLIER_ID, wo_non_order_info_mst.pay_mode as PAY_MODE, wo_non_order_info_mst.currency_id as CURRENCY_ID, wo_non_order_info_mst.attention as ATTENTION,wo_non_order_info_mst.is_approved as IS_APPROVED, wo_non_order_info_mst.delivery_place as DELIVERY_PLACE, delivery_date as DELIVERY_DATE, wo_non_order_info_mst.inserted_by as INSERTED_BY, to_char(wo_non_order_info_mst.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, wo_non_order_info_mst.remarks as REMARKS, wo_non_order_info_mst.location_id as LOCATION_ID, user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, wo_non_order_info_mst.up_charge as UP_CHARGE, wo_non_order_info_mst.discount as DISCOUNT, wo_non_order_info_mst.upcharge_remarks as UPCHARGE_REMARKS, wo_non_order_info_mst.discount_remarks as DISCOUNT_REMARKS from wo_non_order_info_mst left join user_passwd on user_passwd.id = wo_non_order_info_mst.inserted_by left join lib_designation on lib_designation.id = user_passwd.designation where wo_non_order_info_mst.id=$mst_id");
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $currency_id=$data_result[0]['CURRENCY_ID'];
    $is_approved=$data_result[0]['IS_APPROVED'];
    if($is_approved==1){ $approved_status="Full Approved";}
    else if($is_approved==0){ $approved_status="Not Approved";}
    else{ $approved_status="Partial Approved";}

    $sql_company=sql_select("SELECT lib_company.id, lib_company.company_name, lib_company.company_short_name, lib_company.contact_no, lib_location.address from lib_location left join lib_company on lib_company.id = lib_location.company_id where lib_company.status_active = 1 and lib_company.is_deleted = 0 and lib_company.id = $company and lib_location.id = ".$data_result[0]['LOCATION_ID']);
    //$sql_company=sql_select("SELECT lib_company.id, lib_company.company_name, lib_company.company_short_name, lib_company.plot_no, lib_company.level_no, lib_company.road_no, lib_company.block_no, lib_company.city, lib_company.zip_code, lib_company.contact_no, lib_country.country_name from lib_location left join lib_company on lib_company.id = lib_location.company_id left join lib_country on lib_country.id = lib_location.country_id where lib_company.status_active = 1 and lib_company.is_deleted = 0 and lib_company.id = $company and lib_location.id = ".$data_result[0]['LOCATION_ID']);

    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $location_address = $sql_company[0][csf("address")];
    //    $plot_no=$sql_company[0][csf("plot_no")];
    //    $level_no=$sql_company[0][csf("level_no")];
    //    $road_no=$sql_company[0][csf("road_no")];
    //    $block_no=$sql_company[0][csf("block_no")];
    //    $city=$sql_company[0][csf("city")];
    //    $country=$sql_company[0][csf("country_name")];
    //    $zip_code=$sql_company[0][csf("zip_code")];
    $phone_no=$sql_company[0][csf("contact_no")];

    //    $com_address='';
    //    if($plot_no !=''){ $com_address.=$plot_no;}
    //    if($level_no !=''){ $com_address.=", ".$level_no;}
    //    if($road_no !=''){ $com_address.=", ".$road_no;}
    //    if($block_no !=''){ $com_address.=", ".$block_no;}
    //    if($city !=''){ $com_address.=", ".$city;}
    //    if($zip_code !=''){ $com_address.=", ".$zip_code;}
    //    if($country !=''){ $com_address.=", ".$country;}

    if($pay_mode==3 || $pay_mode==5)
    {
        $supplier_sql = sql_select("SELECT id,company_name as supplier_name,contact_no as contact_no,country_id as country_id,website as web_site,email as email,plot_no as address_1, level_no as address_2, road_no as address_3,block_no as address_4, CONTRACT_PERSON as contact_person FROM  lib_company WHERE id =".$data_result[0]['SUPPLIER_ID']);
    }
    else{
        $supplier_sql = sql_select("SELECT id,supplier_name as supplier_name,contact_no as contact_no,country_id as country_id, web_site as web_site,email as email,address_1 as address_1,address_2 as address_2,address_3 as address_3,address_4 as address_4, contact_person as contact_person FROM  lib_supplier WHERE id =".$data_result[0]['SUPPLIER_ID']);
    }

    $req_details_info_sql = sql_select("select inv_purchase_requisition_mst.id AS ID, inv_purchase_requisition_mst.requ_no AS REQU_NO, lib_store_location.store_name AS STORE_NAME from inv_purchase_requisition_mst LEFT JOIN lib_store_location ON lib_store_location.id = inv_purchase_requisition_mst.store_name WHERE inv_purchase_requisition_mst.id IN ($req_numbers_id)");

    $req_numbers_id_explode = explode(',', $req_numbers_id);
    $req_numbers_container = [];
    foreach ($req_details_info_sql as $req_key => $req_data){
        $req_numbers_container[$req_data['REQU_NO']] = $req_data['STORE_NAME'];
    }
    $electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 627 and company_id=$company and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 5 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 5 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 5 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_non_order_info_mst on wo_non_order_info_mst.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where mst_id = $mst_id and approval_history.entry_form = 5 and wo_non_order_info_mst.is_approved = 1 and approval_history.current_approval_status = 1 and approval_history.sequence_no =".max($electronic_sequence_arr));

    ob_start();
    ?>

        <div style="width:930px;">
            <table align="center" cellspacing="0" width="900" >
                <tbody>
                <tr>
                    <td style="font-size:24px;" width="600"><strong><? echo $company_library[$company];  ?></strong></td>
                    <td rowspan="2"><strong style="font-size:24px; border: 1px dashed #000; padding: 2px 4px;"><?=$rpt_title;?></strong></td>
                </tr>
                <tr>
                    <td style="font-size:20px; vertical-align: top;" ><?=$location_address?></td>
                </tr>
                <tr>
                    <td style="font-size:20px; vertical-align: top;" ><? echo "Phone No: ".$phone_no; ?></td>
                    <td style="font-size:18px; vertical-align: top;"><strong>Work Order Type: </strong><?=isset($wo_type_array[$data_result[0]['WO_TYPE']]) ? $wo_type_array[$data_result[0]['WO_TYPE']] : ''?><br><strong>Pay Term: </strong><?=isset($pay_term[$data_result[0]['PAYTERM_ID']]) ? $pay_term[$data_result[0]['PAYTERM_ID']] : ''?></td>

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
            <div style="border: 1px solid black; width:900px;">
                <table align="center"  cellspacing="0" width="900" >
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
            <table align="left" cellspacing="0" width="901.5"  border="1" rules="all" class="rpt_table" >
                <thead>
                <tr></tr>
                <th width="30" style="font-size:18px;">SL</th>
                <th width="175" style="font-size:18px;" >Item Description</th>
                <th width="80" style="font-size:18px;" >Item Size</th>
                <th width="80" style="font-size:18px;" >Model</th>
                <th width="90" style="font-size:18px;" >Brand</th>
                <th width="45" style="font-size:18px;" >Unit</th>
                <th width="70" style="font-size:18px;">Qty</th>
                <th width="130" style="font-size:18px;">Unit Price</th>
                <th width="150" style="font-size:18px;">Amount</th>
                <th width="130.5" style="font-size:18px;">Remarks</th>
                </thead>
                <tbody>
                <?

                $sql_dtls= "SELECT a.id, a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.model as MODEL, b.brand_name AS BRAND_NAME, a.uom AS UOM, b.item_size from wo_non_order_info_dtls a left join product_details_master b on a.item_id=b.id and b.status_active=1
                        where a.mst_id=$mst_id and a.status_active=1 ";

                $sql_result= sql_select($sql_dtls);
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td style="font-size:18px;" ><? echo $i; ?></td>
                        <td style="font-size:18px;" ><? echo $row['ITEM_DESCRIPTION']; ?></td>
                        <td style="font-size:18px;" ><? echo $row[csf('item_size')]; ?></td>
                        <td style="font-size:18px;" ><? echo $row['MODEL']; ?></td>
                        <td style="font-size:18px;" ><? echo $row['BRAND_NAME']; ?></td>
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
                    <td colspan="2" style="font-size:18px;"><strong>Upcharge Remarks</strong></td>
                    <td colspan="5" style="font-size:18px;"><?=$data_result[0]['UPCHARGE_REMARKS']?></td>
                    <td style="font-size:18px;" align="right"><strong>Upcharge :</strong></td>
                    <td style="font-size:18px;" align="right"><?=$currency_sign_arr[$currency_id].' '.number_format($data_result[0]['UP_CHARGE'], 2, '.', ',')?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:18px;"><strong>Discount Remarks</strong></td>
                    <td colspan="5" style="font-size:18px;"><?=$data_result[0]['DISCOUNT_REMARKS']?></td>
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
                    <td colspan="10" style="font-size:18px;"><strong> Amount (in word):&nbsp;</strong><?echo number_to_words(number_format(($tot_wo_amount+$data_result[0]['UP_CHARGE'])-$data_result[0]['DISCOUNT'], 2, '.', ''),$currency[$currency_id]);?></td>
                </tr>
                </tbody>
            </table>
            <br/>
            <?echo get_spacial_instruction($data_result[0]['WO_NUMBER'],"900px", 146);?>
            <br/>
            <table id="signatureTblId" width="901.5" style="padding-top:70px;">
                <tr>
                    <td style="text-align: center; font-size: 18px;" width="230">
                        <strong><?=$data_result[0]['USER_FULL_NAME']?></strong>
                        <br>
                        <strong><?=$data_result[0]['CUSTOM_DESIGNATION']?></strong>
                        <br>
                        <?=$data_result[0]['INSERT_DATE']?>
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
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <?

            $mailBody=ob_get_contents();
            ob_clean();
            echo $mailBody;
            $mail_data = $data[5];
            $cbo_company_id = $data[0];

            //Mail send------------------------------------------
            list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

            if($is_mail_send==1){
            // require_once('../../../mailer/class.phpmailer.php');
                include('../../../auto_mail/setting/mail_setting.php');
            
                $mailToArr=array();
                if($msil_address){$mailToArr[]=$msil_address;}

                //-----------------------------
                $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.entry_form=5 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
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
                
                $subject="Stationary Purchase Order";
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br>".$mailBody, $from_mail,$att_file_arr );
            }


        exit();
}

if($action == "stationary_work_order_print6"){
    extract($_REQUEST);
    $data = explode('*',$data);
    $company = $data[0];
    $mst_id = $data[1];
    $rpt_title = $data[2];
    $template_id = $data[3];

    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');
    ?><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /> <?
    $currency_sign_arr = array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    $sql_company=sql_select("SELECT id, company_name, company_short_name, contact_no, plot_no, level_no, road_no, block_no, city, zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$company");
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

    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
    $location_arr=return_library_array( "select id, location_name from lib_location","id","location_name");
    $group_add=sql_select( "SELECT id,address from lib_group where status_active=1 and address is not null " );
    
    $sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, delivery_date, source, pay_mode, remarks, wo_amount, up_charge, discount, net_wo_amount, upcharge_remarks, location_id, discount_remarks, insert_date,inserted_by, is_approved, contact  FROM  wo_non_order_info_mst WHERE id = $mst_id");
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
   
    $sql_result= sql_select("SELECT a.id, a.wo_number, a.currency_id, b.requisition_no, b.req_quantity, b.uom, b.supplier_order_quantity, d.id as prod_id, b.amount as net_amount, b.rate as net_rate, b.gross_rate as rate, b.gross_amount as amount, d.item_description, d.item_size, d.item_group_id, d.item_account, b.remarks, d.item_code, b.item_category_id, c.requ_no
    from wo_non_order_info_mst a,wo_non_order_info_dtls b 
    left join  inv_purchase_requisition_mst c on b.requisition_no=c.id,product_details_master d
    where a.id=b.mst_id and b.item_id=d.id and a.id=$mst_id and b.is_deleted=0 and b.status_active=1");
    foreach($sql_result as $row)
    {
        $item_category_nam.=$item_category[$row["ITEM_CATEGORY_ID"]].',';
    }
    
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");
    ob_start();
    ?>
    <div style="padding-left: 10px; width: 950px;">
        <table align="center" cellspacing="0" width="950" >
            <tr>
                <td width="200px"> <img src="../../<? echo $image_location; ?>" height="89" width="89"></td>
                <td width="750px">
                    <table align="center" cellspacing="0" width="750">
                        <tr>
                            <td style="text-align: right;font-size: 25px;"><b><? echo $com_name; ?></b></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;font-size: 15px;"><b>Factory Address: 
                            <?
                                if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";
                                if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code;
                            ?>
                            </b></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size: 15px;"><b>Corporate Office: <?=$group_add[0]['ADDRESS'];?></b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table align="left" cellspacing="0" width="450" border="1" rules="all" class="rpt_table" >
            <tr>
                <td width="220"><b>WO Number: </b><?=$work_order_no; ?></td>
                <td><b>WO Date: </b><?=change_date_format($work_order_date); ?></td>
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
                <th width="50" align="center"> UOM</th>
                <th width="70" align="center">Wo.Qty</th>
                <th width="80" align="center">Unit Price</th>
                <th width="95" align="center">Currency</th>
                <th width="95" align="center">Amount</th>
                <th width="80" align="center">Remarks</th>
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
                    <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('supplier_order_quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
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
                <td></td>
            </tr>
            <tr>
                <td align="right" colspan="8"><span style="float:left;text-align: left;"><b>Upcharge Remarks: </b><? echo $upcharge_remarks;?></span> <span style="float:right;text-align: right;"> Upcharge :</span>&nbsp;</td>
                <td align="right"><? echo number_format($up_charge,2);  ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"><span style="text-align:left; float:left;"><b>Discount Remarks: </b><? echo $discount_remarks;?></span>  <span style=" float:right;text-align:right"> Discount : </span>&nbsp;</td>
                <td align="right"><? echo number_format($discount,2);  ?></td>
                <td></td>
            </tr>
            <tr>
                <td align="right" colspan="8"><strong>Net Total : </strong>&nbsp;</td>
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
                <td >Please maintain product quality and delivery schedule as per above. Your co-operation will be highly appreciated.<br><br>
                Thank you
            </td>
            </tr>
            <tr>
                <td >&nbsp;</td>
            </tr>
        </table>
    </div>
    <?
    echo signature_table(55, $data[0], "780px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?

    $mailBody=ob_get_contents();
    ob_clean();
    echo $mailBody;
    $mail_data = $data[4];
    $cbo_company_id = $data[0];

    //Mail send------------------------------------------
    list($msil_address,$is_mail_send,$mail_body)=explode('___',$mail_data);

    if($is_mail_send==1){
        include('../../../auto_mail/setting/mail_setting.php');
    
        $mailToArr=array();
        if($msil_address){$mailToArr[]=$msil_address;}

        //-----------------------------
        $elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.entry_form=5 and a.company_id=$cbo_company_id order by a.SEQUENCE_NO";
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
        
        $subject="Stationary Purchase Order";
        $header=mailHeader();
        echo sendMailMailer( $to, $subject, $mail_body."<br>".$mailBody, $from_mail,$att_file_arr );
    }
    exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=61 and is_deleted=0 and status_active=1");
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
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=61 and is_deleted=0 and status_active=1");
	$dataArr=explode(',',$print_report_format);
	echo $dataArr[0];
}



?>