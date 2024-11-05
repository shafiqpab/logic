<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$bank_arr=return_library_array( "select id,bank_name from  lib_bank", "id", "bank_name"  );




if($action=="supplier_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(str)
	{
		document.getElementById("hidden_suplier_id").value=str;
		parent.emailwindow.hide(); 
	}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Supplier Name</th>
                    <th>Designation</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_supplier" id="txt_supplier" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_designation" id="txt_designation" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('txt_supplier').value+'**'+document.getElementById('txt_designation').value, 'supplier_popup_list_view', 'search_div', 'lc_opening_payment_entry_controller', 'setFilterGrid(\'list_view1\',-1)');" style="width:100px;" />
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

if($action=="supplier_popup_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company,$supplier,$designation)=explode('**',$data);
	//echo $company;

	  if($supplier!=""){$search_con=" and a.supplier_name like('%$supplier%')";}
	  if($designation!=""){$search_con .= " and a.designation like('%$designation%')";}
	
	
	$sql="select a.id,a.supplier_name,a.designation from  lib_supplier a,lib_supplier_tag_company b where a.id=b.supplier_id and  a.status_active=1 and b.tag_company=$company $search_con order by supplier_name";
	//echo $sql;die; 
	$arr=array();
	echo  create_list_view("list_view1", "Supplier Name,Designation", "200,150","400","260",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0,0", $arr , "supplier_name,designation", "","setFilterGrid('list_view1',-1)");
	echo "<input type='hidden' id='hidden_suplier_id' />";
	exit();

}



if($action=="lc_no_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(str)
	{
		//$("#hidden_lc").val(str);
		document.getElementById("hidden_lc").value=str;
		parent.emailwindow.hide();
		
	}
		
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>LC No</th>
                    <th>Year</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_lc_no" id="txt_lc_no" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_year" id="txt_year" value="" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $bank; ?>'+'**'+'<? echo $supplier; ?>'+'**'+'<? echo $date_from; ?>'+'**'+'<? echo $date_to; ?>'+'**'+document.getElementById('txt_lc_no').value+'**'+document.getElementById('txt_year').value, 'lc_no_popup_list_view', 'search_div', 'lc_opening_payment_entry_controller', 'setFilterGrid(\'list_view2\',-1)');" style="width:100px;" />
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

if($action=="lc_no_popup_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company,$bank,$supplier,$date_from,$date_to,$txt_lc_no,$txt_year)=explode('**',$data);

	if($txt_lc_no!=""){$search_con=" and lc_number like('%$txt_lc_no%')";}
	if($txt_year!=""){$search_con .= " and lc_year =$txt_year";}


	$company=str_replace("'","",$company);
	$bank=str_replace("'","",$bank);
	$supplier=str_replace("'","",$supplier);
	$date_from=str_replace("'","",$date_from);
	$date_to=str_replace("'","",$date_to);
	if($bank!=0) $bank="and issuing_bank_id=$bank"; else $bank="";
	if($supplier!="") $supplier="and supplier_id=$supplier"; else $supplier="";
	$date_from=change_date_format($date_from,"yyyy-mm-dd");
	$date_to=change_date_format($date_to,"yyyy-mm-dd");
	if($date_from!="") $lc_date="and lc_date between '$date_from' and '$date_to' "; else $lc_date="";
	//echo $lc_date;

	$sql="select id,lc_number,lc_year,importer_id from com_btb_lc_master_details where importer_id=$company and status_active=1 $bank $lc_date  $supplier $search_con";
	//echo $sql;die; 
	$arr=array(2=>$company_library);
	echo  create_list_view("list_view2", "Lc Number,Year,Company", "150,60,135","395","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,0,importer_id,", $arr , "lc_number,lc_year,importer_id", "","setFilterGrid('list_view2',-1)");
	echo "<input type='hidden' id='hidden_lc' />";
	exit();

}

if($action=="list_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hide_lc_id=str_replace("'","",$hide_lc_id);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_issue_banking=str_replace("'","",$cbo_issue_banking);
	$txt_supplier_name=str_replace("'","",$txt_supplier_name);
	$txt_supplier=str_replace("'","",$txt_supplier);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_lc_no=str_replace("'","",$txt_lc_no);
	//echo $txt_supplier_name;die;
	if($cbo_issue_banking!=0) $cbo_issue_banking="and issuing_bank_id=$cbo_issue_banking"; else $cbo_issue_banking="";
	if($txt_supplier!="") $txt_supplier="and supplier_id=$txt_supplier"; else $txt_supplier="";
	if($db_type==0)$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
	if($db_type==0)$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	
	if($txt_date_from!="") $la_date="and lc_date between '$txt_date_from' and '$txt_date_to'"; else $la_date="";
	if($txt_lc_no!="") $lc_id="and lc_number='$txt_lc_no'"; else $lc_id="";
	//echo $la_date;die;
	
	$sql=("select id,lc_number,lc_year,supplier_id,importer_id,item_category_id,lc_value,issuing_bank_id,lc_type_id from com_btb_lc_master_details where importer_id=$cbo_company_id $cbo_issue_banking $txt_supplier $la_date $lc_id and status_active=1 and is_deleted=0");
	//echo $sql;//die;
	$sql_lc=sql_select($sql);
	?>
<div style="width:900px">
    <fieldset style="width:100%;">
    	<table width="900" cellpadding="0" cellspacing="0" id="" rules="all" class="rpt_table">
        	<thead>
            	<tr>
                	<th width="40">Sl</th>
                    <th width="120">Lc No</th>
                    <th width="170">Supplier</th>
                    <th width="170">Bank</th>
                    <th width="120">Item Catg.</th>
                    <th width="90">LC Type</th>
                    <th width="90">LC Value</th>
                    <th>Charge/ Payment</th>
                </tr>
            </thead>
    	</table>
        <table width="900" cellpadding="0" cellspacing="0" id="tbl_lc_list" rules="all" class="rpt_table">
            <tbody>
            <?
			$i=1;
			foreach($sql_lc as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				if($db_type==0)
				{
					$sql_pay=sql_select("select group_concat(id) as id,entry_id,btb_lc_id,sum(amount) as total_amt from com_lc_charge where btb_lc_id=".$row[csf('id')]." and status_active=1 and is_deleted=0 group by entry_id,btb_lc_id");
				}
				else
				{
					$sql_pay=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id,entry_id,btb_lc_id,sum(amount) as total_amt from com_lc_charge where btb_lc_id=".$row[csf('id')]." and status_active=1 and is_deleted=0 group by entry_id,btb_lc_id");
				}
					
				//var_dump($sql_pay);
				if(!empty($sql_pay))
				{
					foreach($sql_pay as $result)
					{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="40" align="center"><? echo $i;  ?></td>
						<td width="120" id="lc_num_<? echo $i;?>"><? echo $row[csf("lc_number")]; ?><input type="hidden" id="lc_id_<? echo $i; ?>" name="lc_id_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" ></td>
						<td width="170" id="supplier_id_<? echo $i;?>"><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
						<td width="170" id="issue_bank_<? echo $i;?>"><? echo $bank_arr[$row[csf("issuing_bank_id")]]; ?></td>
						<td width="120" id="item_cat_<? echo $i;?>"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
						<td width="90" id="lc_type_<? echo $i;?>"><? echo $lc_type[$row[csf("lc_type_id")]]; ?></td>
						<td width="90" align="right" id="lc_val_<? echo $i;?>"><? echo number_format($row[csf("lc_value")],2); ?></td>
						<td align="center">
						<input type="text" id="txt_charge_<? echo $i;?>" name="txt_charge_<? echo $i;?>" class="text_boxes_numeric" style="width:80px; font-weight:bold; font-size:10px;" placeholder="Browse" onClick="open_payment(<? echo $i;?>)" value="<? echo number_format($result[csf("total_amt")],2);   ?>" readonly />
						<input type="hidden" id="hidden_entry_id_<? echo $i;?>" name="hidden_entry_id_<? echo $i;?>" value="<?  echo $result[csf("entry_id")]; ?>" >
						</td>
					</tr>
					<?
					}
				}
				else
				{
				
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                	<td width="40" align="center"><? echo $i;  ?></td>
                    <td width="120" id="lc_num_<? echo $i;?>"><? echo $row[csf("lc_number")]; ?><input type="hidden" id="lc_id_<? echo $i; ?>" name="lc_id_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" ></td>
                    <td width="170" id="supplier_id_<? echo $i;?>"><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
                    <td width="170" id="issue_bank_<? echo $i;?>"><? echo $bank_arr[$row[csf("issuing_bank_id")]]; ?></td>
                    <td width="120" id="item_cat_<? echo $i;?>"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td width="90" id="lc_type_<? echo $i;?>"><? echo $lc_type[$row[csf("lc_type_id")]]; ?></td>
                    <td width="90" align="right" id="lc_val_<? echo $i;?>"><? echo number_format($row[csf("lc_value")],2); ?></td>
                    <td align="center">
                    <input type="text" id="txt_charge_<? echo $i;?>" name="txt_charge_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" placeholder="Browse" onClick="open_payment(<? echo $i;?>)" readonly />
                    <input type="hidden" id="hidden_entry_id_<? echo $i;?>" name="hidden_entry_id_<? echo $i;?>" >
                    </td>
                </tr>
                <?
				}
				$i++;
			}
            ?>
            </tbody>
        </table>
    </fieldset>	
</div>
<!--<script type="text/javascript"></script>-->
    <?
}

if($action=="charge_popup")
{
	$permission=$_SESSION['page_permission'];
	//echo $permission;die;
	?>
	<script>var permission='<? echo $permission; ?>';</script>
    <?
	//echo "yes";
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$hidden_entry_id=str_replace("'","",$hidden_entry_id);
	$company_id=str_replace("'","",$company_id);
	//echo $hidden_entry_id;die;
	//echo $lc_num."****";echo $supplier."****";echo $issue_bank."****";echo $item_cat."****";echo $lc_val."****";
	?>
	<script>
	function js_set_value(str)
	{
		//alert(str);
		$('#hedden_value').val(str);
		
		parent.emailwindow.hide(); 
	}

	function add_factor_row( i) 
	{	
		var chargefor=0; var AdjustmentSource=0;
		var row_num=$('#tbl_pay_head tbody tr').length;
		var update_dts_id=$('#update_dts_id').val();
		/* if(update_dts_id)
		{
			return false;
		} */
		//alert(row_num);
		if (row_num!=i)
		{
			return false;
		}
		i++;
		
		if(i!=2)
		{
			chargefor=$('#cbochargefor_'+(i-1)).val();
			AdjustmentSource=$('#cboAdjustmentSource_'+(i-1)).val();
		}
		else
		{
			chargefor=$('#cbochargefor_1').val();
			AdjustmentSource=$('#cboAdjustmentSource_1').val();
		}
		
		//alert(AdjustmentSource);
	 
	 	$("#tbl_pay_head tbody tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i; },
			'value': function(_, value) { if(value=='+' || value=="-" || value=="chargefor"){return value}else{return ''} }              
			});
			
		}).end().appendTo("#tbl_pay_head");
		$("#tbl_pay_head tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		
		
		var k=i-1;
		$('#incrementfactor_'+k).hide();
		$('#decrementfactor_'+k).hide();
		//$('#updateiddtls_'+i).val('');
		
		
		$('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
		
		$('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
		$('#txtamount_'+i).removeAttr("onChange").attr("onChange","total_val("+i+");");
		$('#txtamount_'+i).attr("onChange","fn_local_val("+i+",1);");
		$('#txtForeignamount_'+i).removeAttr("onChange").attr("onChange","fn_local_val("+i+",2);");
		$('#cbochargefor_'+i).val(chargefor);
		$('#cboAdjustmentSource_'+i).val(AdjustmentSource);
		
	}

	function fn_deletebreak_down_tr(rowNo ) 
	{
		
		document.getElementById('total_pay').value =(document.getElementById('total_pay').value*1)-($('#txtamount_'+rowNo).val()*1);
		var numRow = $('#tbl_pay_head tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			var k=rowNo-1;
			$('#incrementfactor_'+k).show();
			$('#decrementfactor_'+k).show();
			
			$('#tbl_pay_head tbody tr:last').remove();
		}
		else
			return false;
		
	}


	function fnc_charge_payment( operation )
	{
			if( form_validation('txt_pay_date','Pay Date')==false )
			{
				return;
			}
		    var row_num=$('#tbl_pay_head tbody tr').length;
			var btb_lc_id=$('#btb_lc_id').val();
			//var update_id=$('#update_id').val();
			var txt_pay_date=$('#txt_pay_date').val();
			var txt_entry_id=$('#txt_entry_id').val();
			var cbo_currency_id=$('#cbo_currency_id').val();
			var update_dts_id=$('#update_dts_id').val();
			var txt_exchange_rate=$('#txt_exchange_rate').val();
			var data_all="";
			
			var pay_head_arr=new Array();
			var charge_for_arr=new Array();
			
			for (var i=1; i<=row_num; i++)
			{
				if( form_validation('cboissuebanking_'+i+'*cbochargefor_'+i+'*txtamount_'+i+'*cboAdjustmentSource_'+i,'Pay Head*Charge For*Amount*AdjustmentSource')==false )
				{
					return;
				}
				
				
				
				if( jQuery.inArray( $('#cboissuebanking_' + i).val(), pay_head_arr ) == -1 )
				{
					pay_head_arr.push( $('#cboissuebanking_' + i).val() );
					
					if(jQuery.inArray($('#cbochargefor_'+i).val(),charge_for_arr)==-1)
					{
						charge_for_arr.push($('#cbochargefor_'+i).val());
					}
				}
				else
				{
					if(jQuery.inArray($('#cbochargefor_'+i).val(),charge_for_arr)==-1)
					{
						charge_for_arr.push($('#cbochargefor_'+i).val());
					}
					else
					{
						alert("Duplicate Pay Head Or Charge For Not Allow");return;
					}
				}
				
				data_all=data_all+get_submitted_data_string('cboissuebanking_'+i+'*txtamount_'+i+'*cbochargefor_'+i+'*cboAdjustmentSource_'+i+'*txtpostedaccount_'+i+'*txtForeignamount_'+i+'*updateDtlsId_'+i,"../../../");
				
			}
			
			//alert(data_all);return;
			var data="action=save_update_delete_charge&operation="+operation+'&total_row='+row_num+data_all+'&btb_lc_id='+btb_lc_id+'&txt_pay_date='+txt_pay_date+'&cbo_currency_id='+cbo_currency_id+'&txt_exchange_rate='+txt_exchange_rate+'&txt_entry_id='+txt_entry_id+'&update_dts_id='+update_dts_id;//+'&update_id='+update_id
			freeze_window(operation);
			http.open("POST","lc_opening_payment_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_charge_payment_response;
	}

	function fnc_charge_payment_response()
	{
		
		if(http.readyState == 4) 
		{
		    var reponse=trim(http.responseText).split('**');
			//alert(http.responseText);
			//release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$('#txt_entry_id').val(reponse[1]);
				var list_view_lc =return_global_ajax_value( reponse[1], 'populate_data_lc_form', '', 'lc_opening_payment_entry_controller');
				show_list_view(reponse[1],'show_dtls_list_view','pay_list_view','lc_opening_payment_entry_controller','');
				set_button_status(0, permission, 'fnc_charge_payment',1,0);
				reset_form('lc_pay_1','','','','','txt_entry_id*btb_lc_id');
				$("#incrising_table_body").html("");
				$('#txt_pay_date').attr('disabled',false);
				$("#incrising_table_body").html(list_view_lc);
				release_freezing();
				
			}
			if(reponse[0]==2)
			{
				show_msg(trim(reponse[0]));
				$('#txt_entry_id').val(reponse[1]);
				var list_view_lc =return_global_ajax_value( reponse[1], 'populate_data_lc_form', '', 'lc_opening_payment_entry_controller');
				show_list_view(reponse[1],'show_dtls_list_view','pay_list_view','lc_opening_payment_entry_controller','');
				set_button_status(0, permission, 'fnc_charge_payment',1,0);
				reset_form('lc_pay_1','','','','','txt_entry_id*btb_lc_id');
				$("#incrising_table_body").html("");
				$('#txt_pay_date').attr('disabled',false);
				$("#incrising_table_body").html(list_view_lc);
				release_freezing();
				
			}
			else if(reponse[0]==20)
			{
				show_msg(trim(reponse[0]));
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]*1==102*1)
			{
				alert(reponse[1]);
				release_freezing(); return;
			}
			else 
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			
		}
	}

	function load_exchange_rate(company_id)
	{
		var currency_id= $("#cbo_currency_id").val();
		var data=currency_id+'__'+company_id;
		if(currency_id>1)
		{
			get_php_form_data(data, "get_library_exchange_rate", "lc_opening_payment_entry_controller" );
			var ini_body =return_global_ajax_value( currency_id, 'populate_data_lc_form', '', 'lc_opening_payment_entry_controller');
			$('#incrising_table_body').html(ini_body);
			$("#txtamount_1").attr('disabled',true);
			$("#txtForeignamount_1").attr('disabled',false);
		}
		else
		{
			$("#txt_exchange_rate").val(1).attr('disabled',true);
			var ini_body =return_global_ajax_value( currency_id, 'populate_data_lc_form', '', 'lc_opening_payment_entry_controller');
			$('#incrising_table_body').html(ini_body);
			$("#txtForeignamount_1").attr('disabled',true);
			$("#txtamount_1").attr('disabled',false);
		}
	}

	function fn_local_val(str,type)
	{
		var exchange_rate=$("#txt_exchange_rate").val()*1;
		var foreign_amtount=$("#txtForeignamount_"+str).val()*1;
		var local_amtount=$("#txtamount_"+str).val()*1;
		if(exchange_rate>1)
		{
			if(type==2)
			{
				var local_amt=foreign_amtount*exchange_rate;
				$("#txtamount_"+str).val(number_format (local_amt, 2,'.' , ""));
			}
			else
			{
				var foreign_amt=local_amtount/exchange_rate;
				//alert(local_amtount+"="+exchange_rate);
				$("#txtForeignamount_"+str).val(number_format (foreign_amt, 2,'.' , ""))
			}
		}
		
		total_val();
	}

	function total_val()
	{
		var ddd={ dec_type:1, comma:0, currency:''}
		var numRow = $('table#tbl_pay_head tbody tr').length;
		math_operation( "total_pay", "txtamount_", "+", numRow,ddd );
	}

	function fn_dtls_reload()
	{
		var exchange_rate=$('#txt_exchange_rate').val()*1;
		if(exchange_rate>1)
		{
			$("#tbl_pay_head").find('tbody tr').each(function()
			{
				var txtamount=$(this).find('input[name="txtForeignamount[]"]').val();
				var new_amt=txtamount*exchange_rate;
				$(this).find('input[name="txtamount[]"]').val(number_format(new_amt,2,'.',''));
			});
			total_val();
		}
	}

	</script>
	</head>
	<body>
	<div style="width:680px;">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:100%">
	<form id="lc_pay_1" autocomplete="off">
    <div id="top_part" style="margin-bottom:20px; margin-top:5px;">
    	<table width="650" cellpadding="0" cellspacing="0" id="" border="1" rules="all" class="rpt_table">
        	<thead>
            	<tr>
                	<th width="120">LC No</th>
                    <th width="140">Supplier</th>
                    <th width="180">Bank</th>
                    <th width="100">Item Catg.</th>
                    <th>LC Value</th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                	<td><? echo $lc_num; ?></td>
                    <td><? echo $supplier; ?></td>
                    <td><? echo $issue_bank; ?></td>
                    <td><? echo $item_cat; ?></td>
                    <td align="right"><? echo $lc_val; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="bottom_part">
    	<table width="670" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="" style="margin-bottom:20px;">
            <tr>
            	<td width="100" align="right">Pay Date:</td>
                <td width="110"><input type="text" id="txt_pay_date" name="txt_pay_date" class="datepicker" style="width:90px;" ></td>
                <td width="100" align="right">Currency:</td>
                <td width="110"><?php echo create_drop_down("cbo_currency_id", 100, $currency, '', 0, '', 0, "load_exchange_rate('".$company_id."')",0); ?>  </td>
                <td width="110" align="right">Exchange Rate</td>
                <td><input type="text" id="txt_exchange_rate" name="txt_exchange_rate" value="1" class="text_boxes_numeric" style="width:90px;" onBlur="fn_dtls_reload()" disabled ></td>
            </tr>
        </table>
        <table width="670" cellpadding="0" cellspacing="0" id="tbl_pay_head" rules="all" border="1" class="rpt_table" style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th width="130">Pay Head</th>
                    <th width="130">Charge For</th>
                    <th width="110">Local Amount</th>
                    <th width="100">Foreign Amount</th>
                    <th width="130" class="must_entry_caption">Adjustment Source</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody id="incrising_table_body">
            	<tr id="tr_1">
                	<td align="center">
					<? 
                    	echo create_drop_down( "cboissuebanking_1", 130, $commercial_head,"", 1, "--Select --", $selected, "","","46,47,71,86,88,89,90,91,96,97,98,101,102,111,112,113,114,115,116,117,118,139,140,173,174,175,182,183,184,198,199,200,201,202,203","","","","","","cboissuebanking[]");
                    ?> 
                    </td>
                    <td align="center">
					<? 
                    	echo create_drop_down( "cbochargefor_1", 130, $lc_charge_arr,"", 1, "--Select --", $selected, "","","","","","","","","cbochargefor[]");
                    ?> 
                    </td>
                    <td align="center">
                    <input type="text" id="txtamount_1" name="txtamount[]" style="width:100px;" class="text_boxes_numeric"  onChange="fn_local_val(1,1);">
                    <input type="hidden" id="txtpostedaccount_1" name="txtpostedaccount[]" style="width:140px;" class="text_boxes_numeric">
                    </td>
                    <td align="center">
                    <input type="text" id="txtForeignamount_1" name="txtForeignamount[]" style="width:90px;" class="text_boxes_numeric"  onChange="fn_local_val(1,2)" disabled />
                    </td>
                    <td align="center">
					<? 
						echo create_drop_down( "cboAdjustmentSource_1",130,$commercial_head,'',1,'--Select--',"","",0,'5,6,10,11,15,16,30,31,32,33,34,35,36,71,75,76,80,81,82,83,175',"","","","","","cboAdjustmentSource[]"); 
					?>
                    </td>
                    <td>
						<input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor[]"  class="formbutton" value="+" onClick="javascript:add_factor_row(1)"/>
						<input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor[]"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>
						<input type="hidden" id="updateDtlsId_1" name="updateDtlsId[]" disabled readonly />
                    </td>
                </tr>
            </tbody>
            <tfoot>
            	<tr>
                	<td>&nbsp;</td>
                    <td align="right">Total:</td>
                    <td align="center"><input type="text" id="total_pay" name="total_pay" style="width:100" class="text_boxes_numeric" value="" readonly></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <div id="posted_account_msg" style="display: none"><p style="font-size: 18px; color: red; text-align: center; font-weight: bold;">Already Posted In Accounting</p></div>
        <table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="3" valign="middle" class="button_container">
                <?
					echo load_submit_buttons( $permission, "fnc_charge_payment", 0,0,"",0);
				 ?>
                <input type="hidden" id="txt_entry_id" name="txt_entry_id" value="<? echo $hidden_entry_id;?>">
                
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="">
                <input type="hidden" id="btb_lc_id" name="btb_lc_id" value="<? echo $btb_lc_id; ?>">
                </td>
            </tr>
            <?
			
			
			?>
            

        </table>
    </div>
    </form>
    <br>
    <div id="pay_list_view">
    <?
	if($hidden_entry_id!="")
	{
		$sql = "select id, entry_id, pay_head_id, pay_date, change_for, amount, adjustment_source, is_posted_account from com_lc_charge where entry_id='$hidden_entry_id' and status_active=1 and is_deleted=0"; 
		//echo $sql;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$hidden_to_val+=$row[csf("amount")];
			$hiddenpostedaccount=$row[csf("is_posted_account")];
		}
		?>
		<table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
			<tr >
				<td>&nbsp;</td>
				<td align="center"  valign="middle" class="" onClick="js_set_value(document.getElementById('hidden_to_val').value+'_'+document.getElementById('txt_entry_id').value+'_'+document.getElementById('hidden_posted_account').value)">
				<input type="hidden" id="hedden_value">
				<input type="hidden" id="hidden_to_val" value="<?  echo $hidden_to_val; ?>">
                 <input type="hidden" id="hidden_posted_account" value="<?  echo $hiddenpostedaccount; ?>">
				<input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px">
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<?
		$arr=array(0=>$commercial_head,2=>$lc_charge_arr,3=>$commercial_head);
		echo create_list_view("list_view3", "Pay Head,Pay Date,Charge For,Adjustment Source,Amount","150,100,120,120","670","200",0, $sql, "get_php_form_data", "id", "'child_form_input_data','lc_opening_payment_entry_controller'", 1, "pay_head_id,0,change_for,adjustment_source,0", $arr, "pay_head_id,pay_date,change_for,adjustment_source,amount", "","setFilterGrid('list_view3',-1)",'0,3,0,0,2',"5,amount");	
	}
	?>
     </div>
    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>   
    <?
	
}

if($action=="get_library_exchange_rate")
{
	$ex_data=explode("__",$data);
	$currency=$ex_data[0];
	$company_id=$ex_data[1];
	$exchange_rate=sql_select("select conversion_rate from currency_conversion_rate where currency=$currency and company_id=$company_id and status_active=1 and is_deleted=0 order by id desc");
	if($ex_data[0]==1)
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '1';\n";
	}
	else
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '".$exchange_rate[0][csf("conversion_rate")]."';\n";
		//echo "$('#txt_exchange_rate').attr('disabled'false);\n";
		echo "$('#txt_exchange_rate').attr('disabled',false);\n";
	}
	exit();
}

if($action=="populate_data_lc_form")
{
	?>
    <tr id="tr_1">
        <td align="center">
        <? 
            echo create_drop_down( "cboissuebanking_1", 130, $commercial_head,"", 1, "--Select --", $selected, "","","46,47,71,86,88,89,90,91,96,97,98,101,102,111,112,113,114,115,116,117,118,139,140,173,174,175","","","","","","cboissuebanking[]");
        ?> 
        </td>
        <td align="center">
        <? 
            echo create_drop_down( "cbochargefor_1", 130, $lc_charge_arr,"", 1, "--Select --", $selected, "","","","","","","","","cbochargefor[]");
        ?> 
        </td>
        <td align="center">
        <input type="text" id="txtamount_1" name="txtamount[]" style="width:100px;" class="text_boxes_numeric"  onChange="fn_local_val(1,1);">
        <input type="hidden" id="txtpostedaccount_1" name="txtpostedaccount[]" style="width:140px;" class="text_boxes_numeric">
        </td>
        <td align="center">
        <input type="text" id="txtForeignamount_1" name="txtForeignamount[]" style="width:90px;" class="text_boxes_numeric"  onChange="fn_local_val(1,2)" disabled />
        </td>
        <td align="center">
        <? 
            echo create_drop_down( "cboAdjustmentSource_1",130,$commercial_head,'',1,'--Select--',"","",0,'5,6,10,11,15,16,30,31,32,33,34,35,36,71,75,76,80,81,82,83,175',"","","","","","cboAdjustmentSource[]"); 
        ?>
        </td>
        <td>
        <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor[]"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
         <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor[]"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>
         <input type="hidden" id="updateDtlsId_1" name="updateDtlsId[]" disabled readonly />
        </td>
    </tr>
    <?
}

if($action=="save_update_delete_charge")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$total_row=str_replace("'","",$total_row);
	$btb_lc_id=str_replace("'","",$btb_lc_id);
	$txt_pay_date=str_replace("'","",$txt_pay_date);
	$cbo_currency_id=str_replace("'","",$cbo_currency_id);
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	//echo "10**".$txt_pay_date;die;
	if($db_type==0) $txt_pay_date= change_date_format($txt_pay_date,"yyyy-mm-dd"); 
	else if($db_type==1 ||$db_type==2) $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	$txt_entry_id=str_replace("'","",$txt_entry_id);
	$update_dts_id=str_replace("'","",$update_dts_id);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$txt_entry_id)!="")
		{
			$duplicate_sql="select id,change_for,is_posted_account from com_lc_charge where entry_id=$txt_entry_id and status_active=1";
			for($i=1;$i<=$total_row;$i++)
			{
				$pay_head="cboissuebanking_".$i;
				$change_for="cbochargefor_".$i;
				if($i==1)
					$duplicate_sql.=" and (( pay_head_id= ".$$pay_head."  and change_for= ".$$change_for."  and pay_date= '".$txt_pay_date."') ";
				else
					$duplicate_sql.=" or ( pay_head_id=".$$pay_head ." and change_for=".$$change_for ." and pay_date='".$txt_pay_date ."') ";
			}			
			$duplicate_sql.=" )";
			$duplicate_result=sql_select($duplicate_sql);
			if(count($duplicate_result)>0)
			{
				echo "20**Duplicate Pay Head Or Charge For is Not Allow in Same Master.";
				disconnect($con);
				die;
			}
			$posted_account_sql="select id, change_for, pay_date, is_posted_account from com_lc_charge where entry_id=$txt_entry_id and status_active=1";
			for($i=1;$i<=$total_row;$i++)
			{
				$pay_head="cboissuebanking_".$i;
				$change_for="cbochargefor_".$i;
				
				if($i==1)
					$posted_account_sql.=" and (( change_for= ".$$change_for."  and pay_date= '".$txt_pay_date."' and is_posted_account=1) ";
				else
					$posted_account_sql.=" or (change_for=".$$change_for ." and pay_date= '".$txt_pay_date."' and is_posted_account=1) ";
			}			
			$posted_account_sql.=" )";
			//echo "10**$posted_account_sql";die;
			$posted_account_result=sql_select($posted_account_sql);
			if(count($posted_account_result)>0)
			{
				echo "20**Charge For  Already Posted in Accounts. Same Charge Save Not Allow.";
				disconnect($con);
				die;
			}
			
			$id=return_next_id( "id", "com_lc_charge", 1 ) ;
			$entry_id=$txt_entry_id ;
			if($update_id=="")
			{
				$field_array="id,entry_id,btb_lc_id,pay_date,exchange_rate,pay_head_id,change_for,adjustment_source,amount,foreign_amt,currency_id,inserted_by,insert_date";
				$data_array="";
				for($i=1;$i<=$total_row;$i++)
				{
					if($i!=1)$id=$id+1;
					$pay_head="cboissuebanking_".$i;
					$txtamount="txtamount_".$i;
					$txtForeignamount="txtForeignamount_".$i;
					$change_for="cbochargefor_".$i;
					$AdjustmentSourc="cboAdjustmentSource_".$i;
					
					if ($i!=1) $data_array .=",";
					$data_array	.="(".$id.",".$entry_id.",'".$btb_lc_id."','".$txt_pay_date."','".$txt_exchange_rate."',".str_replace("'","",$$pay_head).",".str_replace("'","",$$change_for).",".str_replace("'","",$$AdjustmentSourc).",".str_replace("'","",$$txtamount).",'".str_replace("'","",$$txtForeignamount)."','".$cbo_currency_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$rID=sql_insert("com_lc_charge",$field_array,$data_array,1);
				//echo "10**INSERT INTO com_lc_charge (".$field_array.") VALUES ".$data_array;die;
			}
		}
		else
		{
			//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
			$dup_entry_sql=sql_select("select entry_id from com_lc_charge where status_active=1 and is_deleted=0 and btb_lc_id=$btb_lc_id");
			if(count($dup_entry_sql)>0)
			{
				echo "20**Duplicate Entry Not Allow In Same BTB.";
				disconnect($con);
				die;
			}
				
			$id=return_next_id( "id", "com_lc_charge", 1 ) ;
			$entry_id=return_next_id( "entry_id", "com_lc_charge", 1 ) ;
			if($update_id=="")
			{
				$field_array="id,entry_id,btb_lc_id,pay_date,exchange_rate,pay_head_id,change_for,adjustment_source,amount,foreign_amt,currency_id,inserted_by,insert_date";
				$data_array="";
				for($i=1;$i<=$total_row;$i++)
				{
					if($i!=1)$id=$id+1;
					$pay_head="cboissuebanking_".$i;
					$txtamount="txtamount_".$i;
					$txtForeignamount="txtForeignamount_".$i;
					$change_for="cbochargefor_".$i;
					$AdjustmentSourc="cboAdjustmentSource_".$i;
					if ($i!=1) $data_array .=",";
					$data_array	.="(".$id.",".$entry_id.",'".$btb_lc_id."','".$txt_pay_date."','".$txt_exchange_rate."',".str_replace("'","",$$pay_head).",".str_replace("'","",$$change_for).",".str_replace("'","",$$AdjustmentSourc).",".str_replace("'","",$$txtamount).",'".str_replace("'","",$$txtForeignamount)."','".$cbo_currency_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				//echo $data_array;
				$rID=sql_insert("com_lc_charge",$field_array,$data_array,1);
			}
		}
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$entry_id."**".str_replace("'",'',$id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$entry_id."**".str_replace("'",'',$id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$entry_id."**".str_replace("'",'',$id);
			}
			else{
				oci_rollback($con); 
				echo "10**".$entry_id."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	
	if ($operation==1)  // Update Here
	{
		//echo $txt_entry_id;die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/* if($update_dts_id!="")
		{
			$duplicate_sql="select id from com_lc_charge where entry_id=$txt_entry_id and status_active=1 and pay_head_id=".$cboissuebanking_1 ." and change_for=".$cbochargefor_1."  and pay_date='".$txt_pay_date."' and id <>$update_dts_id";
			$duplicate_result=sql_select($duplicate_sql);
			if(count($duplicate_result)>0)
			{
				echo "20**Duplicate Pay Head Or Charge For is Not Allow in Same Master.";
				disconnect($con);
				die;
			}
			
			$accounting_check = return_field_value("is_posted_account"," com_lc_charge","id=$update_dts_id and status_active=1","is_posted_account");
			//echo $accounting_check; die; 
			
			if( $accounting_check==1)
			{
				echo "102**Already Posted in Accounts. Update Not Allow.";
				disconnect($con);
				die;
			} 
			
			$field_array = "pay_date*pay_head_id*change_for*adjustment_source*amount*foreign_amt*updated_by*update_date";
			$data_array = "'".$txt_pay_date."'*".$cboissuebanking_1."*".$cbochargefor_1."*".$cboAdjustmentSource_1."*".$txtamount_1."*'".str_replace("'","",$$txtForeignamount)."'*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
			
			$rID= sql_update("com_lc_charge",$field_array,$data_array,"id",$update_dts_id,1); 
		} */

		if($txt_entry_id!="")
		{
			$duplicate_sql="select id,change_for,is_posted_account from com_lc_charge where entry_id=$txt_entry_id and status_active=1";
			for($i=1;$i<=$total_row;$i++)
			{
				$pay_head="cboissuebanking_".$i;
				$change_for="cbochargefor_".$i;
				if($i==1)
					$duplicate_sql.=" and (( pay_head_id= ".$$pay_head."  and change_for= ".$$change_for."  and pay_date= '".$txt_pay_date."') ";
				else
					$duplicate_sql.=" or ( pay_head_id=".$$pay_head ." and change_for=".$$change_for ." and pay_date='".$txt_pay_date ."') ";
			}			
			$duplicate_sql.=" ) and id <>$update_dts_id ";
			// echo "20**$duplicate_sql";die;
			$duplicate_result=sql_select($duplicate_sql);
			if(count($duplicate_result)>0)
			{
				echo "20**Duplicate Pay Head Or Charge For is Not Allow in Same Master.";
				disconnect($con);
				die;
			}

			$accounting_check = return_field_value("is_posted_account"," com_lc_charge","id=$update_dts_id and status_active=1","is_posted_account");
			//echo $accounting_check; die; 
			
			if( $accounting_check==1)
			{
				echo "20**Already Posted in Accounts. Update Not Allow.";
				disconnect($con);
				die;
			} 

			$id=return_next_id( "id", "com_lc_charge", 1 ) ;
			$entry_id=$txt_entry_id ;
			$field_array = "pay_date*pay_head_id*change_for*adjustment_source*amount*foreign_amt*updated_by*update_date";
			$field_array2="id,entry_id,btb_lc_id,pay_date,exchange_rate,pay_head_id,change_for,adjustment_source,amount,foreign_amt,currency_id,inserted_by,insert_date";
			$data_array=$data_array2="";
			for($i=1;$i<=$total_row;$i++)
			{
				$pay_head="cboissuebanking_".$i;
				$txtamount="txtamount_".$i;
				$txtForeignamount="txtForeignamount_".$i;
				$change_for="cbochargefor_".$i;
				$AdjustmentSourc="cboAdjustmentSource_".$i;
				$updateDtlsId="updateDtlsId_".$i;

				if(str_replace("'","",$$updateDtlsId))
				{
					$data_array = "'".$txt_pay_date."'*".str_replace("'","",$$pay_head)."*".str_replace("'","",$$change_for)."*".str_replace("'","",$$AdjustmentSourc)."*".str_replace("'","",$$txtamount)."*'".str_replace("'","",$$txtForeignamount)."'*'".$user_id."'*'".$pc_date_time."'";
				}
				else
				{
					if($data_array2!=""){ $data_array2 .=","; }
					$data_array2 .="(".$id.",".$entry_id.",'".$btb_lc_id."','".$txt_pay_date."','".$txt_exchange_rate."',".str_replace("'","",$$pay_head).",".str_replace("'","",$$change_for).",".str_replace("'","",$$AdjustmentSourc).",".str_replace("'","",$$txtamount).",'".str_replace("'","",$$txtForeignamount)."','".$cbo_currency_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id=$id+1;
				}
			}
			//echo $field_array."<br>".$data_array;die;
			$rID= sql_update("com_lc_charge",$field_array,$data_array,"id",$update_dts_id,1); 
			$rID2=true;
			//echo "10**INSERT INTO com_lc_charge (".$field_array2.") VALUES ".$data_array2;die;
			if($data_array2!=""){ $rID2=sql_insert("com_lc_charge",$field_array2,$data_array2,1); }
		}
		// echo "10**$rID**$rID2";oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		disconnect($con);
		die;
	}

	if($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$accounting_check = return_field_value("is_posted_account"," com_lc_charge","id=$update_dts_id and status_active=1","is_posted_account");
		
		if( $accounting_check==1)
		{
			echo "20**Already Posted in Accounts. Delete Not Allow.";
			disconnect($con);
			die;
		} 

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID= sql_delete("com_lc_charge",$field_array,$data_array,"id",$update_dts_id,1); 
		// echo "10**".$rID; oci_rollback($con); die;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_entry_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_entry_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}
}

if($action=="show_dtls_list_view")
{
 	$sql = "select id,entry_id,pay_head_id,pay_date,change_for,amount, adjustment_source,currency_id,is_posted_account from  com_lc_charge  where entry_id='$data'  and status_active=1 and is_deleted=0"; 
	//echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$hidden_to_val+=$row[csf("amount")];
		$hiddenpostedaccount=$row[csf("is_posted_account")];
	}
	?>
    <table width="680" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
        <tr >
            <td>&nbsp;</td>
            <td align="center"  valign="middle" class="" onClick="js_set_value(document.getElementById('hidden_to_val').value+'_'+document.getElementById('txt_entry_id').value+'_'+document.getElementById('hidden_posted_account').value)">
            <input type="hidden" id="hedden_value">
            <input type="hidden" id="hidden_to_val" value="<?  echo $hidden_to_val; ?>">
             <input type="hidden" id="hidden_posted_account" value="<?  echo $hiddenpostedaccount; ?>">
            <input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px">
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?
	//echo $data;
	$arr=array(0=>$commercial_head,2=>$lc_charge_arr,3=>$commercial_head);
	echo create_list_view("list_view3", "Pay Head,Pay Date,Charge For,Adjustment Source,Amount","150,100,120,120","670","200",0, $sql, "get_php_form_data", "id", "'child_form_input_data','lc_opening_payment_entry_controller'", 1, "pay_head_id,0,change_for,adjustment_source,0", $arr, "pay_head_id,pay_date,change_for,adjustment_source,amount", "","setFilterGrid('list_view3',-1)",'0,3,0,0,2',"5,amount");	
	
} 

if($action=="child_form_input_data")
{
	//echo $data;die;
	//$data = details table ID 
	
	$sql="select id,entry_id,pay_date, currency_id,exchange_rate,pay_head_id,change_for,amount, foreign_amt, adjustment_source,currency_id,is_posted_account from  com_lc_charge where id=$data  and status_active=1 and is_deleted=0"; 
	$result = sql_select($sql);
	
	foreach($result as $row)
	{
		//$pa_date=change_date_format($row[csf("pay_date")]);
		echo "$('#txt_pay_date').val('".change_date_format($row[csf("pay_date")])."');\n";
		echo "$('#cbo_currency_id').val('".$row[csf("currency_id")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("exchange_rate")]."');\n";
		echo "$('#txt_pay_date').attr('disabled',true);\n";
		echo "$('#cbo_currency_id').attr('disabled',true);\n";
		echo "$('#txt_exchange_rate').attr('disabled',true);\n";
		if($row[csf("currency_id")]==1)
		{
			echo "$('#txtForeignamount_1').attr('disabled',true);\n";
			echo "$('#txtamount_1').attr('disabled',false);\n";
		}
		else
		{
			echo "$('#txtamount_1').attr('disabled',true);\n";
			echo "$('#txtForeignamount_1').attr('disabled',false);\n";
		}
		echo "$('#cboissuebanking_1').val(".$row[csf("pay_head_id")].");\n";
		echo "$('#cbochargefor_1').val(".$row[csf("change_for")].");\n";
		echo "$('#cbo_currency_id').val(".$row[csf("currency_id")].");\n";
		echo "$('#txtamount_1').val(".$row[csf("amount")].");\n";
		echo "$('#txtForeignamount_1').val(".$row[csf("foreign_amt")].");\n";
		echo "$('#cboAdjustmentSource_1').val(".$row[csf("adjustment_source")].");\n";
		//update id here
		echo "$('#update_dts_id').val(".$row[csf("id")].");\n";	
		echo "$('#updateDtlsId_1').val(".$row[csf("id")].");\n";	
		echo "$('#txt_entry_id').val(".$row[csf("entry_id")].");\n";		
		//echo "show_list_view(".$row[csf("wo_po_type")]."+'**'+".$row[csf("wo_pi_no")].",'show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";
		if ($row[csf("is_posted_account")]==1)
		{
			echo "$('#posted_account_msg').removeAttr('style');\n";
			echo "set_button_status(1, permission, 'fnc_charge_payment',1,1);\n";
			echo "$('#update1').addClass('formbutton_disabled');\n";
			echo "$('#Delete1').addClass('formbutton_disabled');\n";
		}				
		else 
		{
			echo "$('#posted_account_msg').attr('style','display:none');\n";
			echo "set_button_status(1, permission, 'fnc_charge_payment',1,1);\n";
		}		
	}
	exit();
}

?>