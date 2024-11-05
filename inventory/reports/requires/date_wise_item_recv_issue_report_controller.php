<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

//This for crone fire mail...........................................start;
extract($_REQUEST);
if($auto_mail_user_id!=''){$_SESSION['logic_erp']["user_id"]=$auto_mail_user_id;}
//............................end;



$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}


//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//$yarn_requisition_arr=return_library_array( "select id, requisition_no from  ppl_yarn_requisition_entry",'id','requisition_no');
//$yarn_booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst",'id','booking_no');


if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=187 and is_deleted=0 and status_active=1");

	//echo $print_report_format; disconnect($con); die;

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";
	echo "$('#search4').hide();\n";
	echo "$('#search5').hide();\n";
	echo "$('#search6').hide();\n";
	echo "$('#search7').hide();\n";
	echo "$('#search8').hide();\n";
	echo "$('#search9').hide();\n";





	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{

			if($id==725){echo "$('#search1').show();\n";}
			if($id==726){echo "$('#search2').show();\n";}
			if($id==384){echo "$('#search3').show();\n";}
			if($id==727){echo "$('#search4').show();\n";}
			if($id==733){echo "$('#search5').show();\n";}
			if($id==386){echo "$('#search6').show();\n";}
			if($id==206){echo "$('#search7').show();\n";}
			if($id==385){echo "$('#search8').show();\n";}
			if($id==387){echo "$('#search9').show();\n";}

		}
	}
	exit();
}





//load drop down Buyer
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	die;
}

if ($action=="load_drop_down_store")
{
	$data_ref=explode("**",$data);
	if($data_ref[1]>0) $cetegory_cond=" and b.category_type=$data_ref[1] ";
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data_ref[0] and a.status_active=1 and a.is_deleted=0 $cetegory_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	die;
}



if($action=="style_refarence_surch")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}


	function fn_selected()
	{
		var style_no='<? echo $txt_style_ref_no;?>';
		var style_id='<? echo $txt_style_ref_id;?>';
		var style_des='<? echo $txt_style_ref;?>';
		//alert(style_id);
		if(style_no!="")
		{
			style_no_arr=style_no.split(",");
			style_id_arr=style_id.split(",");
			style_des_arr=style_des.split(",");
			var str_ref="";
			for(var k=0;k<style_no_arr.length; k++)
			{
				str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
				js_set_value(str_ref);
			}
		}
	}


    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Style Ref No</th>
                    <th>Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value, 'style_refarence_surch_list_view', 'search_div', 'date_wise_item_recv_issue_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
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



//style search------------------------------//
if($action=="style_refarence_surch_list_view")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$style_ref_no,$job_no)=explode('**',$data);

	if($style_ref_no!=""){$search_con=" and style_ref_no like('%$style_ref_no%')";}
	if($job_no!=""){$search_con .=" and job_no like('%$job_no')";}

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if($buyer!=0) $buyer_cond=" and buyer_name=$buyer"; else $buyer_cond="";
	if($cbo_year!=0){ if($db_type==0) $year_cond=" and year(insert_date)='$cbo_year'"; else  $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";}else {$year_cond="";}
	//echo $year_cond.jahid;die;
	$sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  $search_con and is_deleted=0 order by job_no_prefix_num";
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","235",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	exit();
}

if($action=="order_surch")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}


	function fn_selected()
	{
		var order_no='<? echo $txt_order_id_no; ?>';
		var order_id='<? echo $txt_order_id;?>';
		var order_des='<? echo $txt_order;?>';
		//alert(style_id);
		if(order_no!="")
		{
			order_no_arr=order_no.split(",");
			order_id_arr=order_id.split(",");
			order_des_arr=order_des.split(",");
			var order_ref="";
			for(var k=0;k<order_no_arr.length; k++)
			{
				order_ref=order_no_arr[k]+'_'+order_id_arr[k]+'_'+order_des_arr[k];
				js_set_value(order_ref);
			}
		}
	}


    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Order No</th>
                    <th>Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_job_no').value, 'order_surch_list_view', 'search_div', 'date_wise_item_recv_issue_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
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





//order search------------------------------//
if($action=="order_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$order_no,$job_no)=explode('**',$data);

	if($order_no!=""){$search_con=" and a.po_number like('%$order_no%')";}
	if($job_no!=""){$search_con .=" and a.job_no_mst like('%$job_no')";}

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$style_all=str_replace("'","",$style_all);

	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_year(b.insert_date $year_con) as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond  $style_cond $search_con and a.status_active=1";
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","230",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

 	exit();
}


if($action=="generate_report_summary")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_search_id=str_replace("'","",$cbo_search_id);
	$txt_search_val=str_replace("'","",$txt_search_val);


	$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	//echo $cbo_order_type;die;


	/*$receive_num_arr=return_library_array( "select id, recv_number from  inv_receive_master",'id','recv_number');
	$issue_num_arr=return_library_array( "select id, issue_number from  inv_issue_master",'id','issue_number');
	*/

	//var_dump($transfer_num_arr);die;
	//echo $cbo_item_cat;die;


	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";

		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}


		if($rptType==4)
		{
			if($cbo_dyed_type==0)
			{
				$sql="select
							b.id as prod_id,
							b.brand,
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,

							case when a.transaction_type in(3) then a.cons_quantity else 0 end as receive_ret_qty ,
							case when a.transaction_type in(4) then a.cons_quantity else 0 end as issue_ret_qty ,

							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd  as yarn_comp_percent2nd,
							b.lot,
							b.supplier_id,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b
						where
							a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,2,3,4,5,6) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $yarn_count_cond order by a.transaction_date, a.id";
			}
			else
			{
				if($cbo_dyed_type==1) $purpose='2'; else $purpose='1,3,4,5,6,7,8,12,15,16,26,29,30';
				$sql="select
							b.id as prod_id,
							b.brand,
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,

							0 as receive_ret_qty ,
							case when a.transaction_type in(4) then a.cons_quantity else 0 end as issue_ret_qty,

							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.supplier_id,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							1 as type,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b,  inv_receive_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and c.receive_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond
				union all
						select
							b.id as prod_id,
							b.brand,
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,

							case when a.transaction_type in(3) then a.cons_quantity else 0 end as receive_ret_qty ,
							0 as issue_ret_qty ,

							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.supplier_id,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							2 as type,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b, inv_issue_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and c.issue_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond

							order by transaction_date, trans_id";

			}
		}



		$sql_result=sql_select($sql);



	ob_start();
	if($cbo_item_cat==1){

	?>
    <div style="width:620px;">
    <table width="600" align="left">
        <tr style="border:none;">
            <td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
        </tr>
        <tr style="border:none;">
            <td colspan="8" align="center" style="border:none; font-size:14px;">
                Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
            </td>
        </tr>
        <tr>
            <th colspan="8" style="font-size:20px;">Receive/ Issue Summary </th>
        </tr>

   </table>

    <table width="600" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
        <thead>
            <th width="35">SL</th>
            <th width="60">Yarn Count</th>
            <th width="60">Yarn Lot</th>
            <th width="60">Brand Name</th>
            <th>Yarn Description</th>
            <th width="60">Yarn Type</th>
            <th width="80">Receive Qty</th>
            <th width="80">Issue Qty</th>
        </thead>
    </table>
    <div style="width:618px; overflow-y:scroll; max-height:240px; float:left;" id="scroll_body">
    <table width="600" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_body" rules="all" align="left">
    	<?
		$i=1;
		foreach($sql_result as $row){
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

		?>
        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
            <td width="35"><? echo $i; ?></td>
            <td width="60"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]];?></p></td>
            <td width="60"><p><? echo $row[csf('lot')];?></p></td>
            <td width="60"><p><? echo $brand_arr[$row[csf('brand')]];?></p></td>
            <td><p>
					<?
                    if($row[csf("yarn_comp_percent1st")]!=0) {$parcent1st=$row[csf("yarn_comp_percent1st")]."%";} else {$parcent1st="";}
                    if($row[csf("yarn_comp_percent2nd")]!=0 ){ $parcent2nd=$row[csf("yarn_comp_percent2nd")]."%";} else {$parcent2nd="";}
                     echo $composition[$row[csf("yarn_comp_type1st")]].' '.$parcent1st.' '.$composition[$row[csf("yarn_comp_type2nd")]].' '.$parcent2nd;
                     ?> </p>
            </td>
            <td width="60"><? echo $yarn_type[$row[csf('yarn_type')]];?></td>
            <td width="80" align="right"><? echo number_format($row[csf('receive_qty')],2); $totRecQty+=$row[csf('receive_qty')];?></td>
            <td width="80" align="right"><? echo number_format($row[csf('issue_qty')],2); $totIssueQty+=$row[csf('issue_qty')]; ?></td>
    	</tr>
       <? $i++;} ?>
    </table>
    </div>
    <table width="600" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
    	<tfoot>
            <th width="35"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th></th>
            <th width="60"></th>
            <th align="right" width="80" id="tot_iss_qty"><? echo number_format($totRecQty,2);?></th>
            <th align="right" width="80" id="tot_ret_qty"><? echo number_format($totIssueQty,2);?></th>
    	</tfoot>
    </table>
	</div>
	<?

	}




	foreach (glob($user_id."*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	exit();
}


if($action=="generate_report_issue_return")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_search_id=str_replace("'","",$cbo_search_id);
	$txt_search_val=str_replace("'","",$txt_search_val);


	$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (27)");
	oci_commit($con);


	//echo $cbo_order_type;die;
	//$receive_num_arr=return_library_array( "select id, recv_number from  inv_receive_master",'id','recv_number');
	//$issue_num_arr=return_library_array( "select id, issue_number from  inv_issue_master",'id','issue_number');
	//var_dump($transfer_num_arr);die;
	//echo $cbo_item_cat;die;

	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";

		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}

	if($rptType==6)
	{
		$sql="SELECT b.id as prod_id, b.brand, a.id as trans_id, a.receive_basis, a.transaction_type, a.mst_id as rec_issue_id, a.transaction_date, a.cons_uom, b.yarn_comp_type1st as yarn_comp_type1st, b.yarn_comp_percent1st as yarn_comp_percent1st, b.yarn_comp_type2nd as yarn_comp_type2nd, b.yarn_comp_percent2nd as yarn_comp_percent2nd, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.store_id, a.inserted_by, $select_insert_date, $select_insert_time, p.buyer_name, p.job_no, p.style_ref_no, q.grouping as ref_no, a.cons_reject_qnty, sum(r.quantity) as quantity
		from wo_po_details_master p, wo_po_break_down q, order_wise_pro_details r, inv_transaction a, product_details_master b
		where p.job_no=q.job_no_mst and q.id=r.po_breakdown_id and r.trans_id=a.id and a.prod_id=b.id and r.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type=4 and r.trans_type=4 and r.entry_form=9  and a.status_active=1 and a.is_deleted=0 and r.status_active=1 and r.is_deleted=0 $date_cond $yarn_count_cond
		group by b.id, b.brand, a.id, a.receive_basis, a.transaction_type, a.mst_id, a.transaction_date, a.cons_uom, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.store_id, a.inserted_by, a.insert_date, p.buyer_name, p.job_no, p.style_ref_no, q.grouping, a.cons_reject_qnty
		union all
		select b.id as prod_id, b.brand, a.id as trans_id, a.receive_basis, a.transaction_type, a.mst_id as rec_issue_id, a.transaction_date, a.cons_uom, b.yarn_comp_type1st as yarn_comp_type1st, b.yarn_comp_percent1st as yarn_comp_percent1st, b.yarn_comp_type2nd as yarn_comp_type2nd, b.yarn_comp_percent2nd as yarn_comp_percent2nd, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.store_id, a.inserted_by, $select_insert_date, $select_insert_time, 0 as buyer_name, c.booking_no as job_no, null as style_ref_no, null as ref_no, a.cons_reject_qnty, a.cons_quantity as quantity
		from inv_receive_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and a.item_category=1 and c.booking_without_order=1 and a.company_id=$cbo_company_name and a.transaction_type=4 and c.entry_form=9 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $yarn_count_cond
		order by transaction_date, trans_id";//and a.cons_quantity>0
	}
	// echo $sql;die;
	$sql_result=sql_select($sql);

	$mrr_ids_arr = array();

	foreach($sql_result as $row)
	{
		$mrr_ids_arr[$row[csf("rec_issue_id")]]=$row[csf("rec_issue_id")];
	}

	if (count($mrr_ids_arr)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 23, $mrr_ids_arr, $empty_arr);

		$mrr_sql=sql_select("SELECT A.ID, A.RECV_NUMBER, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.CHALLAN_NO, A.BOOKING_NO
		FROM INV_RECEIVE_MASTER A,GBL_TEMP_ENGINE B
		WHERE A.ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ENTRY_FORM=9 AND  B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=23");
		$mrr_data=array();
		foreach($mrr_sql as $row)
		{
			$mrr_data[$row["ID"]]["recv_number"]=$row["RECV_NUMBER"];
			$mrr_data[$row["ID"]]["knitting_source"]=$row["KNITTING_SOURCE"];
			$mrr_data[$row["ID"]]["knitting_company"]=$row["KNITTING_COMPANY"];
			$mrr_data[$row["ID"]]["challan_no"]=$row["CHALLAN_NO"];
			$mrr_data[$row["ID"]]["booking_no"]=$row["BOOKING_NO"];
		}
	}

	ob_start();
	if($cbo_item_cat==1)
	{
		?>
		<div style="width:2020px;">
		<table width="2000" align="left">
			<tr style="border:none;">
				<td colspan="23" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
			</tr>
			<tr style="border:none;">
				<td colspan="23" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
			<tr>
				<th colspan="23" style="font-size:20px;">Issue Return </th>
			</tr>
	   </table>
		<table width="2100" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
			<thead>
				<th width="30">SL</th><!--700  9-->
				<th width="40">Prod. Id</th>
				<th width="120">Store Name</th>
				<th width="70">Trans. Date</th>
				<th width="100">Trans. Ref.</th>
				<th width="60">Internal Ref. No</th>
				<th width="80">Requisiton No</th>
				<th width="100">Job No</th>
                <th width="100">Style Ref. No</th>

                <th width="70">Challan No</th><!--500 6-->
				<th width="70">Buyer</th>
				<th width="120">Knitting Party</th>
				<th width="120">Supplier Name</th>
				<th width="70">Lot</th>
                <th width="70">Count</th>

                <th width="200">Composition</th><!--790 8-->
				<th width="90">Yarn Type</th>
				<th width="90">Color</th>
				<th width="80">Return Qty</th>
				<th width="80">Reject Qty</th>
                <th width="100">User</th>
                <th width="130">Insert Date</th>
                <th>Remarks</th>
			</thead>
		</table>
		<div style="width:2120px; overflow-y:scroll; max-height:240px; float:left;" id="scroll_body">
		<table width="2100" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_body" rules="all" align="left">
			<?
			$i=1;
			foreach($sql_result as $row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
					<td width="30" align="center"><? echo $i;?></td>
                    <td width="40" align="center"><? echo $row[csf("prod_id")];?></td>
                    <td width="120"><p><? echo $store_arr[$row[csf("store_id")]];?></p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("transaction_date")]);?></p></td>
                    <td width="100"><p><? echo $mrr_data[$row[csf("rec_issue_id")]]["recv_number"];?></p></td>
                    <td width="60"><p><? echo $row[csf("ref_no")];?></p></td>
                    <td width="80"><p><? if($row[csf("receive_basis")]==3) echo $mrr_data[$row[csf("rec_issue_id")]]["booking_no"]; ?></p></td>
                    <td width="100"><p><? echo $row[csf("job_no")];?></p></td>
                    <td width="100"><p><? echo $row[csf("style_ref_no")];?></p></td>

                    <td width="70"><p><? echo $mrr_data[$row[csf("rec_issue_id")]]["challan_no"];?></p></td>
                    <td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]];?></p></td>
                    <td width="120"><? if($mrr_data[$row[csf("rec_issue_id")]]["knitting_source"]==1) echo $company_arr[$mrr_data[$row[csf("rec_issue_id")]]["knitting_company"]]; else echo $supplier_arr[$mrr_data[$row[csf("rec_issue_id")]]["knitting_company"]]; ?></p></td>
                    <td width="120"><p><? echo $supplier_arr[$row[csf("supplier_id")]];?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf("lot")];?></p></td>
                    <td width="70" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]];?></p></td>

                    <td width="200"><p>
                    <?
                    if($row[csf("yarn_comp_percent1st")]!=0) {$parcent1st=$row[csf("yarn_comp_percent1st")]."%";} else {$parcent1st="";}
                    if($row[csf("yarn_comp_percent2nd")]!=0 ){ $parcent2nd=$row[csf("yarn_comp_percent2nd")]."%";} else {$parcent2nd="";}
                    echo $composition[$row[csf("yarn_comp_type1st")]].' '.$parcent1st.' '.$composition[$row[csf("yarn_comp_type2nd")]].' '.$parcent2nd;
                    ?>
                    </p></td>
                    <td width="90"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
                    <td width="90"><p><? echo $color_arr[$row[csf("color")]];?></p></td>
                    <td width="80" align="right"><? echo number_format($row[csf("quantity")],2,".","");?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("cons_reject_qnty")],2,".","");?></td>
                    <td width="100" align="center"><p><? echo $user_arr[$row[csf("inserted_by")]];?></p></td>
                    <td width="130" align="center"><p><? echo $row[csf("insert_date")];?></p></td>
                    <td>&nbsp;</td>
				</tr>
			    <?
				$tot_return_qnty+=$row[csf("quantity")];
				$tot_reject_qnty+=$row[csf("cons_reject_qnty")];
				$i++;
		   }
		    ?>
		</table>
		</div>
		<table width="2120" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>

                <th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>

                <th width="200">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="90">Total</th>
                <th align="right" width="80" id="value_tot_return_qnty"><? echo number_format($tot_return_qnty,2);?></th>
            	<th align="right" width="80" id="value_tot_reject_qnty"><? echo number_format($tot_reject_qnty,2);?></th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th>&nbsp;</th>
			</tfoot>
		</table>
		</div>
		<?
	}

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (27)");
	oci_commit($con);
	disconnect($con);


	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	exit();
}


if($action=="generate_report_general")
{
	$process = array( &$_POST );
	//print_r($process);die;
	//echo "**k**$cbo_item_cat**$rptType";die;
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$rptType=str_replace("'","",$rptType);
	//echo $rptType;die;
	$fso_id=str_replace("'","",$fso_id);
	$show_booking=str_replace("'","",$show_booking);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_search_id=str_replace("'","",$cbo_search_id);
	$txt_search_val=str_replace("'","",$txt_search_val);
	$cbo_use_for=str_replace("'","",$cbo_use_for);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	//$rptType=2;

	if($general_item_category[$cbo_item_cat]=="")
	{
		echo "This Button Only For General Item Category";die;
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');

	//echo $txt_search_val;die;
	/*$receive_num_arr=return_library_array( "select id, recv_number from  inv_receive_master",'id','recv_number');
	$issue_num_arr=return_library_array( "select id, issue_number from  inv_issue_master",'id','issue_number');
	*/
	//var_dump($transfer_num_arr);die;
	//echo $cbo_item_cat;die;

	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";

			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond_2="and b.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond_2="";

			if($txt_date_from!="" && $txt_date_to!="") {
				$date_cond_roll="and x.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
				$date_cond_rolldata="and a.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
				$date_cond_issue=" and a.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
			} else {
				$date_cond="";
				$date_cond_roll="";
				$date_cond_rolldata="";
				$date_cond_issue="";
			}
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";

		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS,32,34,35,36,37,38,39
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}



	$receive_sql=sql_select("select a.id, a.recv_number, a.challan_no, a.supplier_id,a.knitting_source, a.knitting_company, a.currency_id, a.exchange_rate,  a.booking_id, a.booking_no, a.receive_basis
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in($cbo_item_cat) and a.entry_form in (20,27,263,266) and a.company_id=$cbo_company_name and b.transaction_type in(1,4)");
	foreach($receive_sql as $row)
	{
		$receive_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$receive_num_arr[$row[csf("id")]]["recv_number"]=$row[csf("recv_number")];
		$receive_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$receive_num_arr[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
		$receive_num_arr[$row[csf("id")]]["knitting_source"]=$row[csf("knitting_source")];
		$receive_num_arr[$row[csf("id")]]["knitting_company"]=$row[csf("knitting_company")];
		$receive_num_arr[$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
		$receive_num_arr[$row[csf("id")]]["exchange_rate"]=$row[csf("exchange_rate")];
		$receive_num_arr[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
		$receive_num_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$receive_num_arr[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];
	}

	unset($receive_sql);

	/*$issue_sql=sql_select("select a.id, a.issue_number, a.challan_no, a.req_no, a.knit_dye_source, a.knit_dye_company
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.item_category in($cbo_item_cat) and a.entry_form in(21,26,308,250,298) and b.transaction_type in(2,3) and a.company_id=$cbo_company_name");
	foreach($issue_sql as $row)
	{
		$issue_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$issue_num_arr[$row[csf("id")]]["issue_number"]=$row[csf("issue_number")];
		$issue_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$issue_num_arr[$row[csf("id")]]["req_no"]=$row[csf("req_no")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
	}*/

	$transfer_sql=sql_select("select a.id, a.transfer_system_id, a.challan_no, a.transfer_date from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category in($cbo_item_cat) and a.entry_form=57");
	foreach($transfer_sql as $row)
	{
		$transfer_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$transfer_num_arr[$row[csf("id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		$transfer_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$transfer_num_arr[$row[csf("id")]]["transfer_date"]=$row[csf("transfer_date")];
	}

	unset($transfer_sql);

	$req_sql=sql_select("select id, division_id, department_id, section_id, manual_req, requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and status_active=1");
	$reqisition_data=array();
	foreach ($req_sql as $row)
	{
		$reqisition_data[$row[csf('id')]]['division_id']=$row[csf('division_id')];
		$reqisition_data[$row[csf('id')]]['department_id']=$row[csf('department_id')];
		$reqisition_data[$row[csf('id')]]['section_id']=$row[csf('section_id')];
		$reqisition_data[$row[csf('id')]]['manual_req']=$row[csf('manual_req')];
		$reqisition_data[$row[csf('id')]]['requ_prefix_num']=$row[csf('requ_prefix_num')];
	}
	unset($req_sql);

	$wo_sql=sql_select("select id, wo_number_prefix_num, requisition_no, wo_number from wo_non_order_info_mst where company_name=$cbo_company_name and status_active=1");
	$wo_arr=array();
	foreach($wo_sql as $row)
	{
		$wo_arr[$row[csf("id")]]['wo_number']=$row[csf("wo_number")];
		$reqs=explode(",", $row[csf("requisition_no")]);
		for ($i=0; $i <count($reqs) ; $i++) {
			$wo_arr[$row[csf("id")]]['requ_no']=$reqs[$i];
		}

		$wo_arr[$row[csf("id")]]['id']=$row[csf("id")];
	}

	unset($wo_sql);

	$pi_sql="select a.id, a.pi_number, b.work_order_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1 and b.item_category_id in($cbo_item_cat)";

	$pi_sql_result=sql_select($pi_sql);
	foreach($pi_sql_result as $row)
	{
		$pi_num_arr[$row[csf("id")]]=$row[csf("pi_number")];
		$pi_wo_id[$row[csf("id")]]=$row[csf("work_order_id")];
	}
	unset($pi_sql_result);

	if($cbo_item_cat>0) $item_cond=" and a.item_category=$cbo_item_cat and b.item_category_id=$cbo_item_cat";
	//echo $date_cond;die;
	$use_for_cond="";
	if($cbo_use_for>0) $use_for_cond=" and a.use_for=$cbo_use_for";
	if($cbo_store_name>0) $use_for_cond .=" and a.store_id=$cbo_store_name";
	$sql="select
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.cons_quantity as receive_qty,
				'' as issue_qty,
				a.cons_uom,
				a.supplier_id,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				b.item_size,
				a.cons_rate,
				a.cons_amount,
				a.department_id,
				a.section_id,
				c.receive_basis,
				c.booking_id,
				c.booking_no,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				a.machine_category,
				a.machine_id,
				a.floor_id,
				a.use_for,
				a.store_id,
				c.remarks,
				a.expire_date
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.mst_id=c.id and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_cond $date_cond $use_for_cond
			order by a.transaction_date, a.id";

	//echo $sql;die;

	$sql_result=sql_select($sql);

	$div_width="2800 px";
	$table_width="2780";
	ob_start();
	?>
	<div style="width:<? echo $div_width; ?>">
	<fieldset style="width:<? echo $div_width; ?>">
		<table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
			</tr>
			<tr style="border:none;">
				<td colspan="12" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0"  class="
			rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="60" >Prod. Id</th>
					<th width="100" >Store Name</th>
					<th width="70" >Trans. Date</th>
					<th width="110" >Trans. Ref.</th>
					<th width="70" >Challan No</th>
					<th width="110">Receive Basis</th>
					<th width="140">Supplier</th>
					<th width="100">PI/<br />WO/<br />Reqsn</th>
					<th width="100">WO/PI No.</th>
					<th width="100">Division</th>
					<th width="100">Department</th>
					<th width="100">Section</th>
					<th width="100">Manual Req. No.</th>
					<th width="100">Item Group</th>
					<th width="100">Item Sub-Group</th>
					<th width="120">Item Description</th>
					<th width="100">Item Code</th>
					<th width="80">Size</th>
					<th width="80">Currency</th>
					<th width="80">Exchange Rate</th>
					<th width="80">Actual Rate</th>
					<th width="80">Receive Qty</th>
					<th width="80">Actual Amt</th>
					<th width="80">Rate(TK)</th>
					<th width="100">Amount(TK)</th>
					<th width="70">Warranty Exp. Date</th>
					<th width="70">Warranty DOH</th>
					<th width="110">User</th>
					<th>Insert Date</th>
				</tr>
			</thead>
		</table>
		<br />
        <? //echo "test";print_r($sql_result);die; ?>
		<div style="width:2800px; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?
				//echo "test";print_r($sql_result);die;
				$i=1;$total_receive=$total_issue="";
				foreach($sql_result as $row)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" title="<?= "transac id=".$row[csf("trans_id")];?>"><p><? echo $i; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
						<td width="100"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
						<td width="70" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
						<td width="110"  align="center"><p>
						<?
						if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
						{
							echo $receive_num_arr[$row[csf('rec_issue_id')]]["recv_number"];
						}
						else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3)
						{
							echo $issue_num_arr[$row[csf('rec_issue_id')]]["issue_number"];
						}
						else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
						{
							echo $transfer_num_arr[$row[csf('rec_issue_id')]]["transfer_system_id"];
						}
						?>
						</p>
						</td>
						<td width="70"><p>
						<?
						if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
						{
							echo $receive_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
						}
						else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
						{
							echo $issue_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
						}
						?>
						</p>
						</td>
						<td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
						<td width="140"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
						<td width="100"><p>
						<?
						if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
						{
							//$req_book_no=$row[csf('rec_issue_id')];
							if($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==7)
							{
								$req_book_no=$reqisition_data[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]]['requ_prefix_num'];
							}
							else if ($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==2)
							{
								//$req_book_no=$receive_num_arr[$row[csf('rec_issue_id')]]["booking_no"];
								$req_book_no=$wo_arr[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]]['wo_number'];
							}
							else
							{
								$req_book_no="";
							}
						}
						else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
						{
							$req_book_no=$issue_num_arr[$row[csf('rec_issue_id')]]["req_no"];
						}
						echo $req_book_no;

						?></p></td>
						<td width="100" align="center"><p>
							<?
							$department_name='';
							$section_name='';
							$divission='';
							$manual_req='';
							if($row[csf("receive_basis")]==1)
							{
								//echo $pi_num_arr[$row[csf("booking_id")]];
								echo $pi_num_arr[$row[csf("booking_id")]];
							   $requ_no=$wo_arr[$pi_wo_id[$row[csf("booking_id")]]]['requ_no'];
								$divission=$reqisition_data[$requ_no]['division_id'];
								$department_name=$reqisition_data[$requ_no]['department_id'];
								 $section_name=$reqisition_data[$requ_no]['section_id'];
								$manual_req=$reqisition_data[$requ_no]['manual_req'];
							}
							else if($row[csf("receive_basis")]==2)
							{
								echo $wo_arr[$row[csf("booking_id")]]['wo_number'];

								$requ_no=$wo_arr[$row[csf("booking_id")]]['requ_no'];
							   $divission =$reqisition_data[$requ_no]['division_id'];
								 $department_name=$reqisition_data[$requ_no]['department_id'];
								$section_name=$reqisition_data[$requ_no]['section_id'];
								$manual_req=$reqisition_data[$requ_no]['manual_req'];

							}
							else if($row[csf("receive_basis")]==7)
							{
								$requ_no=$row[csf("booking_id")];
								$divission =$reqisition_data[$requ_no]['division_id'];
								 $department_name=$reqisition_data[$requ_no]['department_id'];
								$section_name=$reqisition_data[$requ_no]['section_id'];
								$manual_req=$reqisition_data[$requ_no]['manual_req'];
							}
						?></p>
						</td>
						<td width="100" align="center"><p><? echo $division_arr[$divission]; ?></p></td>
						<td width="100" align="center"><p><? echo $department_arr[$department_name]; ?></p></td>
						<td width="100" align="center"><p><? echo $section_arr[$section_name]; ?></p></td>
						<td width="100" align="center"><p><? echo $manual_req; ?></p></td>
						<td width="100" ><p><? echo $group_arr[$row[csf('item_group_id')]]; ?></p></td>
						<td width="100" ><p><? echo $row[csf('sub_group_name')]; ?></p></td>
						<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>
						<td width="100" ><p><? echo $row[csf('item_code')]; ?></p></td>
						<td width="80"><p><? echo $row[csf('item_size')]; ?></p></td>
						<td width="80" align="center"><p><? if($row[csf("transaction_type")]==1) echo $currency[$receive_num_arr[$row[csf('rec_issue_id')]]["currency_id"]]; ?></p></td>
						<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($receive_num_arr[$row[csf('rec_issue_id')]]["exchange_rate"],2); ?></p></td>
						<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($row[csf('order_rate')],2); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("receive_qty")],2); $total_receive +=$row[csf("receive_qty")]; ?></p></td>
						<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) $order_amt=$row[csf('order_qnty')]*$row[csf('order_rate')]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("cons_rate")],2); ?></p></td>
						<td align="right" width="100" style="padding-right:3px"><p><? echo number_format($row[csf("cons_amount")],2); $total_amount +=$row[csf("cons_amount")]; ?></p></td>
						<td width="70" align="center"><p><? if($row[csf("expire_date")]!="0000-00-00" && $row[csf("expire_date")]!="") echo change_date_format($row[csf("expire_date")]); else echo ""; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
						if($row[csf("expire_date")]!=''){
							$daysOnHand = (datediff("d",date("Y-m-d"),$row[csf("expire_date")])-1);
						}else{
							$daysOnHand='';
						}

						echo $daysOnHand; ?></p></td>
						<td width="107"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></p></td>
						<td><p><? echo change_date_format($row[csf("insert_date")])." ".$row[csf("insert_time")]; ?></p></td>
					</tr>
					<?
					$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv']+=$row[csf("receive_qty")];
					//$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_amt']+=$row[csf('order_qnty')]*$row[csf('order_rate')];
					$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['issue']+=$row[csf("issue_qty")];
					if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
					{
						$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_amt']+=$row[csf("cons_amount")];
					}
					else
					{
						$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['issue_amt']+=$row[csf("cons_amount")];
					}

					$i++;
				}
				//print_r($item_wise_qty_arr);
				?>
				</tbody>
			</table>
			<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="140">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >Total:</th>
						<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
						<th width="80"  id="value_total_order_amt"><? //echo number_format($total_order_amt,2); ?></th>
						<th width="80" >&nbsp;</th>
						<th id="value_total_amount"  width="100"><? echo number_format($total_amount,2); ?></th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="107">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		 </div>
	</fieldset>
	</div>
	<?
	foreach (glob($user_id."*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	disconnect($con);
	exit();
}

//supplier search------------------------------//
if($action=="supplier_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				if($('#tr_' + str_or).css("display") !='none')
				{
					toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

					if( jQuery.inArray( selectID, selected_id ) == -1 ) {
						selected_id.push( selectID );
						selected_name.push( selectDESC );
						selected_no.push( str_or );
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == selectID ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						selected_no.splice( i, 1 );
					}
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";

	 $sql = "select c.supplier_name,c.id,c.short_name from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name,c.short_name order by c.supplier_name";
	//echo $sql; die;
	echo create_list_view("list_view", "Supplier Name,Short Name","150,80","350","310",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0", $arr, "supplier_name,short_name", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";


	?>
    <script language="javascript" type="text/javascript">
	var txt_supplier_id_no='<? echo $txt_supplier_id_no;?>';
	var txt_supplier_id='<? echo $txt_supplier_id;?>';
	var txt_supplier='<? echo $txt_supplier;?>';
	//alert(style_id);
	if(txt_supplier_id_no!="")
	{
		var txt_supplier_id_no_arr=txt_supplier_id_no.split(",");
		var txt_supplier_id_arr=txt_supplier_id.split(",");
		var txt_supplier_arr=txt_supplier.split(",");
		var txt_supplier_ref="";
		for(var k=0;k<txt_supplier_id_no_arr.length; k++)
		{
			txt_supplier_ref=txt_supplier_id_no_arr[k]+'_'+txt_supplier_id_arr[k]+'_'+txt_supplier_arr[k];
			js_set_value(txt_supplier_ref);
		}
	}
	</script>
	<?
	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
	$started = microtime(true);
	$process = array( &$_POST );
	//var_dump($process);die;
	//print_r($process);die;
	//echo "**k**$cbo_item_cat**$rptType";die;
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$rptType=str_replace("'","",$rptType);
	//echo $rptType;die;
	$fso_id=str_replace("'","",$fso_id);
	$show_booking=str_replace("'","",$show_booking);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_search_id=str_replace("'","",$cbo_search_id);
	$txt_search_val=str_replace("'","",$txt_search_val);
	$cbo_use_for=str_replace("'","",$cbo_use_for);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_supplier_id=str_replace("'","",$txt_supplier_id);
	$print_action="";

	if($cbo_year!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(a.insert_date)='$cbo_year'";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
		}
	}
	else {$year_cond="";}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	$division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$buyer_session_arr=return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0 order by season_name ASC",'id','season_name');
	//echo $txt_search_val;die;
	/*$receive_num_arr=return_library_array( "select id, recv_number from  inv_receive_master",'id','recv_number');
	$issue_num_arr=return_library_array( "select id, issue_number from  inv_issue_master",'id','issue_number');
	*/
	//var_dump($transfer_num_arr);die;
	//echo $cbo_item_cat;die;


	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (27)");
	oci_commit($con);

	if($txt_supplier_id!="") $supplier_cond=" and b.supplier_id in($txt_supplier_id)";else $supplier_cond="";

	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";

			if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond_2="and b.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond_2="";

			if($txt_date_from!="" && $txt_date_to!="") {
				$date_cond_roll="and x.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
				$date_cond_rolldata="and a.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
				$date_cond_issue=" and a.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
				$date_cond_transfer="and a.TRANSFER_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
			} else {
				$date_cond="";
				$date_cond_roll="";
				$date_cond_rolldata="";
				$date_cond_issue="";
				$date_cond_transfer="";
			}
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";

		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS,32,34,35,36,37,38,39
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}


	if($cbo_item_cat==4)
	{
		$print_report_trim_rcv=explode(",",fnc_report_button($cbo_company_name,6,191,0));
		$print_report_trim_rcv_multi_v3=explode(",",fnc_report_button($cbo_company_name,6,230,0));

		if($cbo_company_name!=0) $comp_cond="and a.company_id=$cbo_company_name"; else $comp_cond="";
		// if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_id=$cbo_buyer_name"; else $buyer_cond="";
		if($cbo_buyer_name!=0) $buyer_cond_trns="and b.buyer_id=$cbo_buyer_name"; else $buyer_cond_trns="";
		if($txt_order_id!="") $order_id_cond="and b.order_id=$txt_order_id"; else $order_id_cond="";

		$receive_sql=sql_select("SELECT a.id as ID, a.recv_number as RECV_NUMBER, a.challan_no as CHALLAN_NO, a.challan_date as CHALLAN_DATE, a.supplier_id as SUPPLIER_ID,a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, a.currency_id as CURRENCY_ID, a.exchange_rate as EXCHANGE_RATE, a.booking_id as BOOKING_ID, a.booking_no as BOOKING_NO, a.receive_basis as RECEIVE_BASIS, a.is_multi as IS_MULTI, a.entry_form as ENTRY_FORM, a.is_posted_account as IS_POSTED_ACCOUNT, a.PAY_MODE
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4
		 and b.transaction_type in(1,4) $comp_cond $order_id_cond $date_cond_2");

		foreach($receive_sql as $row)
		{
			$receive_num_arr[$row["ID"]]["id"]=$row["ID"];
			$receive_num_arr[$row["ID"]]["recv_number"]=$row["RECV_NUMBER"];
			$receive_num_arr[$row["ID"]]["challan_no"]=$row["CHALLAN_NO"];
			$receive_num_arr[$row["ID"]]["supplier_id"]=$row["SUPPLIER_ID"];
			$receive_num_arr[$row["ID"]]["knitting_source"]=$row["KNITTING_SOURCE"];
			$receive_num_arr[$row["ID"]]["knitting_company"]=$row["KNITTING_COMPANY"];
			$receive_num_arr[$row["ID"]]["currency_id"]=$row["CURRENCY_ID"];
			$receive_num_arr[$row["ID"]]["exchange_rate"]=$row["EXCHANGE_RATE"];
			$receive_num_arr[$row["ID"]]["booking_id"]=$row["BOOKING_ID"];
			$receive_num_arr[$row["ID"]]["booking_no"]=$row["BOOKING_NO"];
			$receive_num_arr[$row["ID"]]["receive_basis"]=$row["RECEIVE_BASIS"];
			$receive_num_arr[$row["ID"]]["is_multi"]=$row["IS_MULTI"];
			$receive_num_arr[$row["ID"]]["entry_form"]=$row["ENTRY_FORM"];
			$receive_num_arr[$row["ID"]]["challan_date"]=change_date_format($row["CHALLAN_DATE"]);
			$receive_num_arr[$row["ID"]]["is_posted_account"]=$row["IS_POSTED_ACCOUNT"];
			$receive_num_arr[$row["ID"]]["pay_mode"]=$row["PAY_MODE"];
		}

		unset($receive_sql);
		// echo "<pre>";print_r($receive_num_arr);die;
		$issue_sql=sql_select("SELECT a.id as ID, a.issue_number as ISSUE_NUMBER, a.challan_no as CHALLAN_NO, a.req_no as REQ_NO, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY,a.entry_form as ENTRY_FORM, a.is_posted_account as IS_POSTED_ACCOUNT
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 and b.transaction_type in(2,3) $comp_cond $order_id_cond $date_cond_2");

		foreach($issue_sql as $row)
		{
			$issue_num_arr[$row["ID"]]["id"]=$row["ID"];
			$issue_num_arr[$row["ID"]]["issue_number"]=$row["ISSUE_NUMBER"];
			$issue_num_arr[$row["ID"]]["challan_no"]=$row["CHALLAN_NO"];
			$issue_num_arr[$row["ID"]]["req_no"]=$row["REQ_NO"];
			$issue_num_arr[$row["ID"]]["knit_dye_source"]=$row["KNIT_DYE_SOURCE"];
			$issue_num_arr[$row["ID"]]["knit_dye_company"]=$row["KNIT_DYE_COMPANY"];
			$issue_num_arr[$row["ID"]]["entry_form"]=$row["ENTRY_FORM"];
			$issue_num_arr[$row["ID"]]["is_posted_account"]=$row["IS_POSTED_ACCOUNT"];
		}
		unset($issue_sql);
		//echo "<pre>";print_r($issue_num_arr);die;

		$transfer_sql=sql_select("SELECT a.id as ID, a.transfer_system_id as TRANSFER_SYSTEM_ID, a.challan_no as CHALLAN_NO, a.transfer_date as TRANSFER_DATE, a.is_posted_account as IS_POSTED_ACCOUNT,a.to_company as TO_COMPANY
        from inv_item_transfer_mst a, inv_transaction b
        where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category=4 and b.transaction_type in(5,6) $buyer_cond_trns $comp_cond $order_id_cond $date_cond_2");
		foreach($transfer_sql as $row)
		{
			$transfer_num_arr[$row["ID"]]["id"]=$row["ID"];
			$transfer_num_arr[$row["ID"]]["transfer_system_id"]=$row["TRANSFER_SYSTEM_ID"];
			$transfer_num_arr[$row["ID"]]["challan_no"]=$row["CHALLAN_NO"];
			$transfer_num_arr[$row["ID"]]["transfer_date"]=$row["TRANSFER_DATE"];
			$transfer_num_arr[$row["ID"]]["is_posted_account"]=$row["IS_POSTED_ACCOUNT"];
			$transfer_num_arr[$row["ID"]]["issue_to"]= $row["TO_COMPANY"];
		}
		unset($transfer_sql);
		//echo "<pre>";print_r($transfer_num_arr);die;

		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$txt_style_ref_id="and d.id in($txt_style_ref_id)";
			}
			else
			{
				$txt_style_ref_id="and d.job_no_prefix_num like'%$txt_style_ref'";

			}
		}
		else
		{
			 $txt_style_ref_id="";
		}
		if($txt_order!="")
		{
			if($txt_order_id!="")
			{
				$txt_order_id="and c.id in($txt_order_id)";
			}
			else
			{
				$txt_order_id="and c.po_number='$txt_order'";
			}
		}
		else
		{
			$txt_order_id="";
		}
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		if($cbo_buyer_name!=0) $cbo_buyer_name="and d.buyer_name=$cbo_buyer_name"; else $cbo_buyer_name="";


		//echo $date_cond;die;
		//if($db_type==2) $issue_cond="and b.issue_basis=(CASE WHEN b.entry_form = 25 THEN '2' ELSE null END)"; else if($db_type==0) $issue_cond="and b.issue_basis=(CASE WHEN b.entry_form = 25 THEN '2' ELSE '0' END)";
		if($db_type==2)
		{
			$issue_cond=" and CASE b.entry_form
                WHEN 25 THEN
                        CASE WHEN b.issue_basis=2 THEN 1 ELSE 0 END
                ELSE
                        CASE WHEN b.issue_basis=0 THEN 1 ELSE 0 END
            	END = 1";
		}
		else if($db_type==0)
		{
			$issue_cond=" and b.issue_basis=(CASE WHEN b.entry_form = 25 THEN '2' ELSE '0' END)";
		}

        if ($cbo_style_owner!=0) {$style_woner_cond = "and e.knit_dye_company=$cbo_style_owner";}
        if ($cbo_company_name!=0) {$company_cond = "and d.company_name=$cbo_company_name";}
		$store_cond="";
		if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
        //echo $rptType.'ddddddddddd';  A.INSERTED_BY, $SELECT_INSERT_DATE, $SELECT_INSERT_TIME, A.ORDER_QNTY, P.ITEM_DESCRIPTION, P.PRODUCT_NAME_DETAILS, P.COLOR, P.ITEM_COLOR, P.GMTS_SIZE, P.ITEM_SIZE, P.ITEM_GROUP_ID, E.KNIT_DYE_COMPANY AS ISSUE_TO, A.STORE_ID
        /*case when b.trans_type in(4) then a.quantity else 0 end as issue_ret_qty,*/

		if($rptType==1)
		{
			$sql="select a.id as TRANS_ID, b.po_breakdown_id as ORDER_ID, a.transaction_date as TRANSACTION_DATE
			, 0 as RECEIVE_QTY,
            case when b.trans_type in(4) then b.quantity else 0 end as RETURN_QTY,
            case when b.trans_type in(2,3) then b.quantity else 0 end as ISSUE_QTY,
            0 as ISSUE_RET_QTY,
            a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, d.job_no as JOB_NO, d.job_no_prefix_num as JOB_NO_PREFIX_NUM, d.style_ref_no as STYLE_REF_NO, $select_year(d.insert_date $year_con) as JOB_YEAR, d.buyer_name as BUYER_NAME, c.po_number as PO_NUMBER, c.grouping as REF_NO, c.shipment_date as SHIPMENT_DATE, b.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.order_rate as ORDER_RATE, a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, a.order_qnty as ORDER_QNTY, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, e.knit_dye_company as ISSUE_TO,a.store_id as STORE_ID, b.trans_type as TRANS_TYPE
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p, inv_issue_master e
			where a.id=b.trans_id and a.mst_id = e.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.item_category=4 and b.trans_type in(2,3,4) $company_cond $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond $style_woner_cond and a.status_active=1 and a.is_deleted=0 and b.entry_form in(25,49) $store_cond
			union all
			select a.id as TRANS_ID, b.po_breakdown_id as ORDER_ID, a.transaction_date as TRANSACTION_DATE,
			case when b.trans_type in(1,4) then b.quantity else 0 end as RECEIVE_QTY,
			case when b.trans_type in(3) then b.quantity else 0 end as  RETURN_QTY,
			0 as ISSUE_QTY,
			case when b.trans_type in(4) then b.quantity else 0 end as ISSUE_RET_QTY,
			a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, d.job_no as JOB_NO, d.job_no_prefix_num as JOB_NO_PREFIX_NUM, d.style_ref_no as STYLE_REF_NO, $select_year(d.insert_date $year_con) as JOB_YEAR, d.buyer_name as BUYER_NAME, c.po_number as PO_NUMBER, c.grouping as REF_NO, c.shipment_date as SHIPMENT_DATE, b.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.order_rate as ORDER_RATE, a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, a.order_qnty as ORDER_QNTY, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, 0 as ISSUE_TO, a.store_id as STORE_ID, b.trans_type as TRANS_TYPE
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p
			where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.item_category=4 $company_cond $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.trans_type in(1,3,4) and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25,49,73) $store_cond
			order by trans_id";
		}
		else if($rptType==2)
		{
			$sql="SELECT a.id as TRANS_ID,a.expire_date as EXPIRE_DATE, b.po_breakdown_id as ORDER_ID, a.transaction_date as TRANSACTION_DATE,
			case when b.trans_type in(1,4) then b.quantity else 0 end as RECEIVE_QTY,
			case when b.trans_type in(3) then b.quantity else 0 end as RETURN_QTY,
			'' as ISSUE_QTY,
			a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, d.job_no as JOB_NO, d.job_no_prefix_num as JOB_NO_PREFIX_NUM, d.style_ref_no as STYLE_REF_NO, $select_year(d.insert_date $year_con) as JOB_YEAR, d.buyer_name as BUYER_NAME, c.po_number as PO_NUMBER, c.grouping as REF_NO, c.shipment_date as SHIPMENT_DATE, b.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.order_rate as ORDER_RATE, a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, a.order_qnty as ORDER_QNTY, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, 0 as ISSUE_TO, a.store_id as STORE_ID, b.trans_type as TRANS_TYPE, d.season_buyer_wise as SEASON_BUYER_WISE
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p
			where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.item_category=4 $company_cond $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.trans_type in(1,3,4) and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25,49) $store_cond";
		}
		else if($rptType==8)
		{
			$sql="select a.id as TRANS_ID, b.po_breakdown_id as ORDER_ID, a.transaction_date as TRANSACTION_DATE,
			case when b.trans_type in(1,4) then b.quantity else 0 end as RECEIVE_QTY,
			case when b.trans_type in(3) then b.quantity else 0 end as RETURN_QTY,
			'' as ISSUE_QTY,
			a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, d.job_no as JOB_NO, d.job_no_prefix_num as JOB_NO_PREFIX_NUM, d.style_ref_no as STYLE_REF_NO, $select_year(d.insert_date $year_con) as JOB_YEAR, d.buyer_name as BUYER_NAME, c.po_number as PO_NUMBER, c.grouping as REF_NO, c.shipment_date as SHIPMENT_DATE, b.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.order_rate as ORDER_RATE, a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, a.order_qnty as ORDER_QNTY, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, 0 as ISSUE_TO, a.store_id as STORE_ID, f.booking_no as WO_NUMBER, f.booking_date as BOOKING_DATE, g.delivery_date as DELIVERY_DATE, b.trans_type as TRANS_TYPE,g.wo_qnty as WO_QNTY
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p, inv_receive_master e, wo_booking_mst f, wo_booking_dtls g
			where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and  a.mst_id=e.id and e.booking_id=f.id and f.booking_no=g.booking_no and p.item_group_id=g.trim_group
			 and a.item_category=4 $company_cond $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.trans_type in(1,3,4) and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25,49) $store_cond";
			//  and p.item_description=h.description and  g.id=h.wo_trim_booking_dtls_id ,wo_trim_book_con_dtls h
		}
		else if($rptType==3)
		{
			$sql="SELECT a.id as TRANS_ID, b.po_breakdown_id as ORDER_ID, a.transaction_date as TRANSACTION_DATE,
			'' as RECEIVE_QTY,
			case when b.trans_type in(4) then b.quantity else 0 end as RETURN_QTY,
			case when b.trans_type in(2,3) then b.quantity else 0 end as ISSUE_QTY,
			a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, d.job_no as JOB_NO, d.job_no_prefix_num as JOB_NO_PREFIX_NUM, d.style_ref_no as STYLE_REF_NO, $select_year(d.insert_date $year_con) as JOB_YEAR, d.buyer_name as BUYER_NAME, c.po_number as PO_NUMBER, c.grouping as REF_NO, c.shipment_date as SHIPMENT_DATE, b.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.order_rate as ORDER_RATE, a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, a.order_qnty as ORDER_QNTY, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, e.knit_dye_company as ISSUE_TO, a.store_id as STORE_ID, b.trans_type as TRANS_TYPE, d.season_buyer_wise as SEASON_BUYER_WISE
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d, product_details_master p, inv_issue_master e
			where a.id=b.trans_id and a.mst_id = e.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.item_category=4 and b.trans_type in(2,3,4) $company_cond $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond $style_woner_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(25,49,73) $store_cond";
		}

		$trans_id_arrs=array();
		//echo $sql."<br>";die;
		$sql_result=sql_select($sql);
		$trms_rasult_arr=array();
		foreach($sql_result as $row)
		{
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["trans_id"]=$row["TRANS_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["order_id"]=$row["ORDER_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["prod_id"]=$row["PROD_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["transaction_type"]=$row["TRANSACTION_TYPE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["rec_issue_id"]=$row["MST_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["job_no"]=$row["JOB_NO"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["style_ref_no"]=$row["STYLE_REF_NO"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["job_no_prefix_num"]=$row["JOB_NO_PREFIX_NUM"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["job_year"]=$row["JOB_YEAR"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["buyer_name"]=$row["BUYER_NAME"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["po_number"]=$row["PO_NUMBER"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["ref_no"]=$row["REF_NO"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["shipment_date"]=$row["SHIPMENT_DATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["receive_qty"]+=$row["RECEIVE_QTY"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["issue_qty"]+=$row["ISSUE_QTY"];
			if($row["TRANS_TYPE"]==3) $trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["receive_ret_qty"]+=$row["RETURN_QTY"];
			if($row["TRANS_TYPE"]==4) $trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["issue_ret_qty"]+=$row["ISSUE_RET_QTY"];


			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["cons_uom"]=$row["CONS_UOM"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["company_id"]=$row["COMPANY_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["cons_rate"]=$row["CONS_RATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["inserted_by"]=$row["INSERTED_BY"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["insert_date"]=$row[csf("insert_date")];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["insert_time"]=$row[csf("insert_time")];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["order_qnty"]=$row["ORDER_QNTY"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["order_rate"]=$row["ORDER_RATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["item_description"]=$row["ITEM_DESCRIPTION"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["product_name_details"]=$row["PRODUCT_NAME_DETAILS"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["color"]=$row["COLOR"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["item_color"]=$row["ITEM_COLOR"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["gmts_size"]=$row["GMTS_SIZE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["item_size"]=$row["ITEM_SIZE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["item_group_id"]=$row["ITEM_GROUP_ID"];
            $trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["issue_to"]=$row["ISSUE_TO"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["store_id"]=$row["STORE_ID"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["wo_number"]=$row["WO_NUMBER"];

			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["wo_date"]=$row["BOOKING_DATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["delivery_date"]=$row["DELIVERY_DATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["expire_date"]=$row["EXPIRE_DATE"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["wo_qnty"]=$row["WO_QNTY"];
			$trms_rasult_arr[$row["TRANS_ID"]][$row["ORDER_ID"]]["season_buyer_wise"]=$row["SEASON_BUYER_WISE"];
			//array_push($trans_id_arrs, $row["TRANS_ID"]);
		}
		//echo $date_cond;die;
		unset($sql_result);
		// echo '<pre>';print_r($trms_rasult_arr);die;



		$general_order_cond="";
		if($txt_order !="")
		{
			$ord_id=return_field_value("a.id as po_id","wo_po_break_down a, wo_po_details_master b","a.job_no_mst=b.job_no and b.company_name=$cbo_company_name and a.po_number='$txt_order'","po_id");
			if($ord_id) $general_order_cond=" and a.order_id = $ord_id";
		}
		if($rptType==1)
		{
			//A.ID AS TRANS_ID, A.TRANSACTION_DATE, A.CONS_QUANTITY AS RECEIVE_QTY, 0 AS ISSUE_QTY, A.CONS_UOM, A.COMPANY_ID, A.PROD_ID, A.TRANSACTION_TYPE, A.MST_ID, A.CONS_RATE, A.INSERTED_BY, A.RECEIVE_BASIS, B.ENTRY_FORM, B.BOOKING_ID, B.BOOKING_NO, $SELECT_INSERT_DATE, $SELECT_INSERT_TIME, P.ITEM_DESCRIPTION, P.PRODUCT_NAME_DETAILS, P.COLOR, P.ITEM_COLOR, P.GMTS_SIZE, P.ITEM_SIZE, P.ITEM_GROUP_ID, A.STORE_ID, 1 AS TYPE
			$sql_non_order="SELECT a.id as TRANS_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as RECEIVE_QTY, 0 as ISSUE_QTY, a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, a.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.inserted_by as INSERTED_BY, a.receive_basis as RECEIVE_BASIS,a.order_id as ORDER_ID, b.entry_form as ENTRY_FORM, b.booking_id as BOOKING_ID, b.booking_no as BOOKING_NO, $select_insert_date, $select_insert_time, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, a.store_id as STORE_ID, 1 as TYPE
			from inv_transaction a, inv_receive_master b, product_details_master p
			where b.id=a.mst_id and p.id=a.prod_id and a.item_category=4 and a.company_id=$cbo_company_name $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(20,24,27) and a.transaction_type in(1,4) and b.booking_without_order = (CASE WHEN b.entry_form = 24 THEN '1' ELSE '0' END) $store_cond
			union all
			select a.id as TRANS_ID, a.transaction_date as TRANSACTION_DATE, 0 as RECEIVE_QTY, a.cons_quantity as ISSUE_QTY, a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, a.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.inserted_by as INSERTED_BY, a.receive_basis as RECEIVE_BASIS,a.order_id as ORDER_ID, b.entry_form as ENTRY_FORM, 0 as BOOKING_ID, null as BOOKING_NO, $select_insert_date, $select_insert_time, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, a.store_id as STORE_ID, 2 as TYPE
			from inv_transaction a, inv_issue_master b, product_details_master p
			where b.id=a.mst_id and p.id=a.prod_id and a.item_category=4 and a.company_id=$cbo_company_name $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(21,25,26) and a.transaction_type in(2,3) $issue_cond $store_cond $general_order_cond";
		}

		else if($rptType==2)
		{
			$sql_non_order="SELECT a.id as TRANS_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as RECEIVE_QTY, 0 as ISSUE_QTY, a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, a.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.inserted_by as INSERTED_BY, a.receive_basis as RECEIVE_BASIS,a.order_id as ORDER_ID, b.entry_form as ENTRY_FORM, b.booking_id as BOOKING_ID, b.booking_no as BOOKING_NO, $select_insert_date, $select_insert_time, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE,a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, a.store_id as STORE_ID, 1 as TYPE
			from inv_transaction a, inv_receive_master b, product_details_master p
			where b.id=a.mst_id and p.id=a.prod_id and a.item_category=4 and a.company_id=$cbo_company_name $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(20,24,27) and a.transaction_type in(1,4) and b.booking_without_order = (CASE WHEN b.entry_form = 24 THEN '1' ELSE '0' END) $store_cond";
		}
		else if($rptType==8)
		{
			$sql_non_order="SELECT a.id as TRANS_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as RECEIVE_QTY, 0 as ISSUE_QTY, a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, a.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.inserted_by as INSERTED_BY, a.receive_basis as RECEIVE_BASIS,a.order_id as ORDER_ID, b.entry_form as ENTRY_FORM, b.booking_id as BOOKING_ID, b.booking_no as BOOKING_NO, $select_insert_date, $select_insert_time, p.item_description as ITEM_DESCRIPTION, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR,a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, a.store_id as STORE_ID, 1 as TYPE
			from inv_transaction a, inv_receive_master b, product_details_master p
			where b.id=a.mst_id and p.id=a.prod_id and a.item_category=4 and a.company_id=$cbo_company_name $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(20,24,27) and a.transaction_type in(1,4) and b.booking_without_order = (CASE WHEN b.entry_form = 24 THEN '1' ELSE '0' END) $store_cond";
		}
		else if($rptType==3)
		{

			// echo $ord_id.test;die;
			$sql_non_order="SELECT a.id as TRANS_ID, a.transaction_date as TRANSACTION_DATE, 0 as RECEIVE_QTY, a.cons_quantity as ISSUE_QTY, a.cons_uom as CONS_UOM, a.company_id as COMPANY_ID, a.prod_id as PROD_ID, a.transaction_type as TRANSACTION_TYPE, a.mst_id as MST_ID, a.cons_rate as CONS_RATE, a.inserted_by as INSERTED_BY, a.receive_basis as RECEIVE_BASIS,a.order_id as ORDER_ID, b.entry_form as ENTRY_FORM, 0 as BOOKING_ID, null as BOOKING_NO, $select_insert_date, $select_insert_time, p.item_description as ITEM_DESCRIPTION,a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, p.product_name_details as PRODUCT_NAME_DETAILS, p.color as COLOR, p.item_color as ITEM_COLOR, p.gmts_size as GMTS_SIZE, p.item_size as ITEM_SIZE, p.item_group_id as ITEM_GROUP_ID, a.store_id as STORE_ID, 2 as TYPE
				from inv_transaction a, inv_issue_master b, product_details_master p
				where b.id=a.mst_id and p.id=a.prod_id and a.item_category=4 and a.company_id=$cbo_company_name $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(21,25,26) and a.transaction_type in(2,3) $issue_cond $store_cond $general_order_cond";
		}
		//echo "<br>";
		//echo $sql_non_order."<br>";die;
		$sql_result_non_order=sql_select($sql_non_order);
		$trms_non_order_arr=array();$buyer_orer_id="";
		$transaction_id_arr = array();
		$buyer_orer_id_arr = array();
		$transaction_ids="";

		// $non_ord_booking=return_library_array("select id, wo_number from wo_non_order_info_mst where status_active=1 and company_name=$cbo_company_name and entry_form=146","id","wo_number");

		foreach($sql_result_non_order as $row)
		{
			$booking_id_arr = array();
			if($row["ENTRY_FORM"]==20 && $row["RECEIVE_BASIS"]==2)
			{
				$booking_id_arr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
			}
		}

		if(!empty($booking_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 8, $booking_id_arr, $empty_arr);
			$bo_sql=sql_select("SELECT A.ID, A.WO_NUMBER FROM WO_NON_ORDER_INFO_MST A, GBL_TEMP_ENGINE B
			WHERE A.ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.COMPANY_NAME=$cbo_company_name AND A.ENTRY_FORM=146 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=8");
			foreach($bo_sql as $row){
				$boDataArr[$row['ID']] = $row['WO_NUMBER'];
			}
		}


		foreach($sql_result_non_order as $row)
		{
			$trms_non_order_arr[$row["TRANS_ID"]]["trans_id"]=$row["TRANS_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["prod_id"]=$row["PROD_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["transaction_type"]=$row["TRANSACTION_TYPE"];
			$trms_non_order_arr[$row["TRANS_ID"]]["rec_issue_id"]=$row["MST_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$trms_non_order_arr[$row["TRANS_ID"]]["receive_qty"]+=$row["RECEIVE_QTY"];
			$trms_non_order_arr[$row["TRANS_ID"]]["issue_qty"]+=$row["ISSUE_QTY"];
			$trms_non_order_arr[$row["TRANS_ID"]]["cons_uom"]=$row["CONS_UOM"];
			$trms_non_order_arr[$row["TRANS_ID"]]["company_id"]=$row["COMPANY_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["cons_rate"]=$row["CONS_RATE"];
			$trms_non_order_arr[$row["TRANS_ID"]]["inserted_by"]=$row["INSERTED_BY"];
			$trms_non_order_arr[$row["TRANS_ID"]]["insert_date"]=$row[csf("insert_date")];
			$trms_non_order_arr[$row["TRANS_ID"]]["insert_time"]=$row[csf("insert_time")];
			$trms_non_order_arr[$row["TRANS_ID"]]["item_description"]=$row["ITEM_DESCRIPTION"];
			$trms_non_order_arr[$row["TRANS_ID"]]["product_name_details"]=$row["PRODUCT_NAME_DETAILS"];
			$trms_non_order_arr[$row["TRANS_ID"]]["color"]=$row["COLOR"];
			$trms_non_order_arr[$row["TRANS_ID"]]["item_color"]=$row["ITEM_COLOR"];
			$trms_non_order_arr[$row["TRANS_ID"]]["gmts_size"]=$row["GMTS_SIZE"];
			$trms_non_order_arr[$row["TRANS_ID"]]["item_size"]=$row["ITEM_SIZE"];
			$trms_non_order_arr[$row["TRANS_ID"]]["item_group_id"]=$row["ITEM_GROUP_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["store_id"]=$row["STORE_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["division_id"]=$row["DIVISION_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["department_id"]=$row["DEPARTMENT_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["order_id"]=$row["ORDER_ID"];
			$trms_non_order_arr[$row["TRANS_ID"]]["entry_form"]=$row["ENTRY_FORM"];
			if($row["ENTRY_FORM"]==20 && $row["RECEIVE_BASIS"]==2)
			{
				//$trms_non_order_arr[$row["TRANS_ID"]]["booking_no"]=$non_ord_booking[$row["BOOKING_ID"]];
				$trms_non_order_arr[$row["TRANS_ID"]]["booking_no"]=$boDataArr[$row["BOOKING_ID"]];
			}
			else
			{
				$trms_non_order_arr[$row["TRANS_ID"]]["booking_no"]=$row["BOOKING_NO"];
			}
			if ($rptType==3 && $row["ENTRY_FORM"]==21) {
				//$transaction_ids.=$row["TRANS_ID"].',';
				$transaction_id_arr[$row['TRANS_ID']] = $row['TRANS_ID'];
			}
			if($row["ORDER_ID"])
			//$buyer_orer_id.=$row["ORDER_ID"].",";
			$buyer_orer_id_arr[$row['ORDER_ID']] = $row['ORDER_ID'];
			//array_push($trans_id_arrs, $row["TRANS_ID"]);

		}

		$trans_id_arrs=array_unique($trans_id_arrs);
		// $buyer_orer_id = implode(",",array_unique(explode(",",chop($buyer_orer_id, ","))));
		// $transaction_ids = implode(",",array_unique(explode(",",chop($transaction_ids, ","))));
		// if($buyer_orer_id!="")
		if(!empty($buyer_orer_id_arr))
		{
			// $sql_byer_order = "select b.id as ID, a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY
			// from wo_po_details_master a, wo_po_break_down b
			// where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in($buyer_orer_id)";

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 5, $buyer_orer_id_arr, $empty_arr);

			$sql_byer_order = "select b.id as ID, a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY
			from wo_po_details_master a, wo_po_break_down b, gbl_temp_engine c
			where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id =  c.ref_val and c.user_id= $user_id and c.entry_form=27 and c.ref_from=5";

			//echo $sql_byer_order;die;
			$buyer_ord_result = sql_select($sql_byer_order);
			foreach ($buyer_ord_result as $row) {
				$buyer_order_arr[$row["ID"]]=$row["PO_NUMBER"];
			}
		}

		//if($transaction_ids!="")
		if(!empty($transaction_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 4, $transaction_id_arr, $empty_arr);
			$generalissue_buyer_order_arr=array();
			// $sql_buyer_order = "select a.po_breakdown_id as PO_BREAKDOWN_ID, a.trans_id as TRANS_ID, b.po_number as PO_NUMBER
			// from order_wise_general_details a, wo_po_break_down b
			// where a.po_breakdown_id = b.id and a.status_active=1 and a.is_deleted=0 and a.trans_id in($transaction_ids)";

			$sql_buyer_order = "select a.po_breakdown_id as PO_BREAKDOWN_ID, a.trans_id as TRANS_ID, b.po_number as PO_NUMBER
			from order_wise_general_details a, wo_po_break_down b, gbl_temp_engine c
			where a.po_breakdown_id = b.id and a.status_active=1 and a.is_deleted=0 and a.trans_id = c.ref_val and c.user_id= $user_id and c.entry_form=27 and c.ref_from=4";
			//echo $sql_buyer_order;die;
			$sql_buyer_order_res = sql_select($sql_buyer_order);
			foreach ($sql_buyer_order_res as $row) {
				$generalissue_buyer_order_arr[$row["TRANS_ID"]].=$row["PO_NUMBER"].',';
			}
		}
		//var_dump($generalissue_buyer_order_arr);die;
		if($rptType==1)
		{
			$table_width=2950;
			$div_width="2970px";
		}
		else if($rptType==2)
		{
			$table_width=3170;
			$div_width="3190px";
		}
		else if($rptType==3)
		{
			$table_width=3170;
			$div_width="3190px";
		}
		else if($rptType==8)
		{
			$table_width=2950;
			$div_width="2970px";
		}
		else
		{
			$table_width=2490;
			$div_width="2510px";
		}

		if($rptType==1)
		{
			$table_width_non_order=2000;
			$div_width_non_order="2000px";
			$col_span_non_order=19;
		}
		else
		{
			$table_width_non_order=2000;
			$div_width_non_order="2000px";
			$col_span_non_order=19;
		}


		ob_start();
		?>

        <div style="width:<? echo $div_width; ?>;">
            <table width="<? echo $table_width; ?>" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="19" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
                </tr>
            </table>
            <br />
            <?
            $sql_item_group=sql_select("select id, item_name,conversion_factor from  lib_item_group where status_active=1 and is_deleted =0");
            foreach($sql_item_group as $row)
            {
                $group_arr[$row[csf("id")]]["item_name"]=$row[csf("item_name")];
                $group_arr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
            }
            $size_arr=return_library_array( "select id, size_name from  lib_size",'id','size_name');
            if($cbo_order_type==0 || $cbo_order_type==1)
            {
                ?>
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
                    <thead>
                        <tr>
                            <th width="30" style="word-wrap: break-word; word-break:break-all; ">SL</th>
                            <th width="50" style="word-wrap: break-word; word-break:break-all;">Prod. Id</th>
                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Store Name</th>
                            <th width="80" style="word-wrap: break-word; word-break:break-all;">Trans. Date</th>
                            <th width="120" style="word-wrap: break-word; word-break:break-all;">Trans. Ref.</th>
                            <?
                            if($rptType==8)
                            {
                                ?>
                                <th width="60" style="word-wrap: break-word; word-break:break-all;">Wo Date</th>
                                <th width="90" style="word-wrap: break-word; word-break:break-all;">Wo Number</th>
                                <?
                            }?>
                            <th width="60" style="word-wrap: break-word; word-break:break-all;">Year</th>
                            <th width="50" style="word-wrap: break-word; word-break:break-all;">Job No</th>
                            <?
                            if($rptType==2 || $rptType==3)
                            {
                                ?>
                                 <th width="100" style="word-wrap: break-word; word-break:break-all;">Internal <br>Ref</th>
                                 <th width="100" style="word-wrap: break-word; word-break:break-all;">Season</th>
                                <?
                            }?>

                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Style<br> Ref.No</th>
                            <th width="90" style="word-wrap: break-word; word-break:break-all;">Buyer</th>
                            <th width="120" style="word-wrap: break-word; word-break:break-all;">Order No</th>
                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Challan <br>No</th>
                            <?
                            if(($rptType==2 || $rptType==8) && $cbo_item_cat==4 )
                            {
                                ?>
                                <th width="60" style="word-wrap: break-word; word-break:break-all;">Challan <br>Date</th>
                                <?
                            }?>
                            <th width="120" style="word-wrap: break-word; word-break:break-all;">Party Name</th>
                            <th width="80" style="word-wrap: break-word; word-break:break-all;">Ship Date</th>
                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Group Name</th>
                            <th width="220" style="word-wrap: break-word; word-break:break-all;">Description</th>
							<?
                            if($rptType==8)
                            {
                                ?>
                                <th width="100" style="word-wrap: break-word; word-break:break-all;">Item <br>Delivery<br> Date</th>
                                <?
                            }?>
                            <th width="80" style="word-wrap: break-word; word-break:break-all;">RMG Color</th>
                            <th width="60" style="word-wrap: break-word; word-break:break-all;">RMG Size</th>
                            <?
                            if($rptType==2 || $rptType==1 || $rptType==8)
                            {
                                ?>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Currency</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Exchange<br> Rate</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Actual Rate</th>
								<?
								if($rptType==8)
								{
									?>
									<th width="60" style="word-wrap: break-word; word-break:break-all;">Wo Qnty</th>
									<?
								}?>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Receive <br>Qty</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Receive <br>Return<br> Qty</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Actual Amt</th>
                                <?
                            }
                            if($rptType==3 || $rptType==1 )
                            {
                                ?>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Issue Qty</th>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Issue <br> Return <br>Qty</th>
                                <?
                            }
                            ?>
                            <th width="80" style="word-wrap: break-word; word-break:break-all;">Rate(TK)</th>
                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Amount(TK)</th>
							<? if($rptType==2)
							{
								?>
                                <th width="80" style="word-wrap: break-word; word-break:break-all;">Warranty <br>Exp. Date</th>
                            	<?
							}
							?>
                            <th width="50" style="word-wrap: break-word; word-break:break-all;">UOM</th>
                            <? if($rptType==3 || $rptType==1 )
							{
								?>
                                <th width="150" style="word-wrap: break-word; word-break:break-all;">Issue To</th>
                            	<?
							}
							?>
                            <th width="100" style="word-wrap: break-word; word-break:break-all;">Accounting Posting</th>
                            <th width="110" style="word-wrap: break-word; word-break:break-all;">User</th>
                            <th  style="word-wrap: break-word; word-break:break-all;">Insert Time</th>
                        </tr>
                    </thead>
                </table>
                  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:290px;" id="scroll_body">
                    <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                        <tbody>
                        <?
                        $i=1;
                        foreach($trms_rasult_arr as $trans_key=>$value)
                        {
                            foreach($value as $order_key=>$val)
                            {
                                if ($i%2==0)
                                $bgcolor="#E9F3FF";
                                else
                                $bgcolor="#FFFFFF";

                                if($val["transaction_type"]==1 )
                                {
                                    $mst_id=$receive_num_arr[$val['rec_issue_id']]["id"];
                                    $is_multi=$receive_num_arr[$val['rec_issue_id']]["is_multi"];
                                    $entry_form=$receive_num_arr[$val['rec_issue_id']]["entry_form"];
									if($is_multi==0)
									{
										if($print_report_trim_rcv[0]==86){$print_action="trims_receive_entry_print";}
										else if($print_report_trim_rcv[0]==116){$print_action="trims_receive_entry_print_2";}
										else if($print_report_trim_rcv[0]==136){$print_action="trims_receive_entry_print_4";}
										else {$print_action="trims_receive_entry_print";}
									}
									else if($is_multi==3)
									{
										if($print_report_trim_rcv_multi_v3[0]==78){$print_action="trims_receive_entry_print";}
										if($print_report_trim_rcv_multi_v3[0]==84){$print_action="trims_receive_entry_print2";}
										else {$print_action="trims_receive_entry_print";}
									}
									else
									{
										$print_action="trims_receive_entry_print";
									}
                                }
								else if($val["transaction_type"]==4)
                                {
                                    $mst_id=$receive_num_arr[$val['rec_issue_id']]["id"];
                                    $is_multi=$receive_num_arr[$val['rec_issue_id']]["is_multi"];
                                    $entry_form=$receive_num_arr[$val['rec_issue_id']]["entry_form"];

                                }
                                else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                {
                                    $mst_id=$issue_num_arr[$val['rec_issue_id']]["id"];
                                    $entry_form=$issue_num_arr[$val['rec_issue_id']]["entry_form"];
                                    $is_multi=2;
                                }

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" title="<? echo $val['rec_issue_id']; ?>" style="word-wrap: break-word; word-break:break-all;"><? echo $i; ?></td>
                                    <td width="50" style="word-wrap: break-word; word-break:break-all;"><? echo $val["prod_id"]; ?></td>
                                    <td width="100" style="word-wrap: break-word; word-break:break-all;"><? echo $store_library[$val["store_id"]]; ?></td>
                                    <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? if($val["transaction_date"]!="0000-00-00") echo change_date_format($val["transaction_date"]); else echo ""; ?>&nbsp;</td>
                                    <td width="120"  align="center" style="word-wrap: break-word; word-break:break-all;"><a href="##" onClick="generate_trims_print_report('<? echo $val["transaction_type"];?>','<? echo $mst_id; ?>','<? echo $is_multi; ?>','<? echo $val["company_id"];?>','<? echo $entry_form; ?>','<?=$print_action;?>')">
                                    <?
                                        if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                        {
                                            echo $receive_num_arr[$val['rec_issue_id']]["recv_number"];
                                            /*if(empty($receive_num_arr[$val['rec_issue_id']]["recv_number"]))
                                            {
                                            	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['system_no'];
                                            }*/

                                        }
                                        else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                        {
                                            echo $issue_num_arr[$val['rec_issue_id']]["issue_number"];
                                            /*if(empty($issue_num_arr[$val['rec_issue_id']]["issue_number"]))
                                            {
                                            	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['system_no'];
                                            }*/
                                        }

                                    ?></a>
                                    </td>
										<?
										if($rptType==8 ){
											?>
										<td width="60"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo change_date_format($val["wo_date"]); ?>&nbsp;</td>
										<td width="90"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $val["wo_number"]; ?></td>
											<?
										}
										?>
                                    <td width="60"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $val["job_year"]; ?></td>
                                    <td width="50"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $val["job_no_prefix_num"]; ?></td>
                                    <?
		                            if($rptType==2 || $rptType==3)
		                            {
		                                ?>
		                                 <td width="100"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $val["ref_no"]; ?></td>
		                                 <td width="100"  align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $buyer_session_arr[$val["season_buyer_wise"]]; ?></td>
		                                <?
		                            }?>

                                    <td width="100" style="word-wrap: break-word; word-break:break-all;"><? echo $val["style_ref_no"]; ?></td>
                                    <td width="90" style="word-wrap: break-word; word-break:break-all;"><? echo $buyer_short_arr[$val["buyer_name"]]; ?></td>
                                    <td width="120" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo $val["po_number"]; ?></td>
                                    <td width="100" style="word-wrap: break-word; word-break:break-all;">
                                    <?
                                        if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                        {
                                            echo $receive_num_arr[$val['rec_issue_id']]["challan_no"];
                                            if(empty($receive_num_arr[$val['rec_issue_id']]["challan_no"]))
                                            {
                                            	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['challan_no'];
                                            }

                                        }
                                        else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                        {
                                            echo $issue_num_arr[$val['rec_issue_id']]["challan_no"];
                                            if(empty($issue_num_arr[$val['rec_issue_id']]["challan_no"]))
                                            {
                                            	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['challan_no'];
                                            }

                                        }

                                    ?>
                                    </td>
                                    <?
		                            if( ($rptType==2 || $rptType==8) && $val["transaction_type"]==1 && $cbo_item_cat==4 )
		                            {
		                                ?>
		                                <td width="60" style="word-wrap: break-word; word-break:break-all;"><? echo $receive_num_arr[$val['rec_issue_id']]["challan_date"]; ?></td>
		                                <?
		                            }?>
                                    <td width="120" style="word-wrap: break-word; word-break:break-all;">
                                    <?
                                        if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                        {
											if($receive_num_arr[$val['rec_issue_id']]["knitting_source"]==3 || $receive_num_arr[$val['rec_issue_id']]["knitting_source"]==5 || $receive_num_arr[$val['rec_issue_id']]["pay_mode"]==5)
											{
												echo $company_arr[$receive_num_arr[$val['rec_issue_id']]["supplier_id"]];
											}
											else
											{
												echo $supplier_arr[$receive_num_arr[$val['rec_issue_id']]["supplier_id"]];
											}

                                        }
                                        else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                        {
                                            if($issue_num_arr[$val['rec_issue_id']]["knit_dye_source"]==1)
                                            {
                                                echo $company_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
                                            }
                                            else
                                            {
                                                echo $supplier_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
                                            }

                                        }

                                    ?>
                                    </td>
                                    <td width="80"  align="center" style="word-wrap: break-word; word-break:break-all;"><? if($val["shipment_date"]!="0000-00-00") echo change_date_format($val["shipment_date"]); else echo ""; ?></td>
                                    <td width="100" style="word-wrap: break-word; word-break:break-all;"><? echo $group_arr[$val["item_group_id"]]["item_name"]; ?></td>
                                    <td width="220" style="word-wrap: break-word; word-break:break-all;"><? echo $val["product_name_details"]; ?></td>
									<?
										if($rptType==8 ){
											?>
                                    	<td width="100" align="center" style="word-wrap: break-word; word-break:break-all;"><? echo change_date_format($val["delivery_date"]); ?></td>
											<?
										}
										?>

                                    <td width="80" style="word-wrap: break-word; word-break:break-all;"><? echo $color_arr[$val["color"]]; ?></td>
                                    <td width="60" style="word-wrap: break-word; word-break:break-all;"><? echo $size_arr[$val["gmts_size"]]; ?></td>
                                    <?
                                    if($rptType==2 || $rptType==1 || $rptType==8 )
                                    {
                                        ?>
                                        <td width="80" align="center" style="word-wrap: break-word; word-break:break-all;"><? if($val["transaction_type"]==1) echo $currency[$receive_num_arr[$val['rec_issue_id']]["currency_id"]]; ?></td>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;"><? if($val["transaction_type"]==1) echo number_format($receive_num_arr[$val['rec_issue_id']]["exchange_rate"],2); ?></td>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;"><? if($val["transaction_type"]==1) echo number_format($val["order_rate"],4); ?></td>
										<?
										if($rptType==8)
										{
											?>
											<td width="60" align="right" style="word-wrap: break-word; word-break:break-all;"><? echo $val["wo_qnty"]; ?></td>
											<?
										}?>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;">
                                        <?
                                        if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                        {
                                            $recv_issue_qnty=$group_arr[$val["item_group_id"]]["conversion_factor"] *$val["receive_qty"];
                                            echo number_format($recv_issue_qnty,2,".",""); $total_receive_qty+=$recv_issue_qnty;
                                        }
                                        ?>
                                        </td>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;">
                                        <?
                                        $receive_ret_qty = $group_arr[$val["item_group_id"]]["conversion_factor"] *$val["receive_ret_qty"];
                                        echo number_format($receive_ret_qty,2,".",""); $tot_receive_ret_qty+=$receive_ret_qty;

                                        ?>
                                        </td>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;"><? if($val["transaction_type"]==1) $order_amt=$val["receive_qty"]*$val["order_rate"]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></td>
                                        <?
                                    }
                                    if($rptType==3 || $rptType==1 )
                                    {
                                        ?>
                                        <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;">
                                        <?
                                         if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                         {
                                             $recv_issue_qnty=$group_arr[$val["item_group_id"]]["conversion_factor"] *$val["issue_qty"];
                                             echo number_format($recv_issue_qnty,2,".","");
                                             $tot_issue_qty+=$recv_issue_qnty;
                                             ;
                                         }
                                         ?>
                                        </td>

                                         <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;">
                                         <?
                                         $issue_ret_qty = $group_arr[$val["item_group_id"]]["conversion_factor"] *$val["issue_ret_qty"];
                                         echo number_format($issue_ret_qty,2,".","");
                                         $tot_issue_ret_qty+=$issue_ret_qty;

                                         ?>
                                         </td>
                                        <?
                                    }
                                    ?>
                                    <td width="80" align="right" style="word-wrap: break-word; word-break:break-all;"><? echo number_format($val["cons_rate"],4,".",""); ?></td>
                                    <td width="100" align="right" style="word-wrap: break-word; word-break:break-all;">
                                    <?
                                    $amount=$recv_issue_qnty*$val["cons_rate"];
                                    $rate=$val["cons_rate"];
                                     echo number_format($amount,2,".","");
									 $total_amount += $amount;
									 $total_rate += $rate;
                                    ?>
                                     </td>
									 <? if($rptType==2){?>
									<td width="80" style="word-wrap: break-word; word-break:break-all;"><? echo change_date_format($val["expire_date"]); ?></td>
									<? } ?>
                                    <td width="50" style="word-wrap: break-word; word-break:break-all;"><? echo $unit_of_measurement[$val["cons_uom"]]; ?></td>
                                    <? if($rptType==3 || $rptType==1 ){?>
										<td width="150" style="word-wrap: break-word; word-break:break-all;"><? echo $company_arr[$val["issue_to"]]; ?></td><? } ?>
                                    <td width="100" style="word-wrap: break-word; word-break:break-all;">
										<?
											if($val["transaction_type"]==1)
											{ echo $yes_no[$receive_num_arr[$val['rec_issue_id']]["is_posted_account"]];}
											elseif($val["transaction_type"]==2)
											{ echo $yes_no[$issue_num_arr[$val['rec_issue_id']]["is_posted_account"]];}
											elseif($val["transaction_type"]==5 || $val["transaction_type"]==6)
											{
												if($transfer_num_arr[$val['rec_issue_id']]["is_posted_account"]>0)
												{
													$transfer_num_arr[$val['rec_issue_id']]["is_posted_account"]=1;
												}
												echo $yes_no[$transfer_num_arr[$val['rec_issue_id']]["is_posted_account"]];
											}
										?>
									</td>
                                    <td width="110" style="word-wrap: break-word; word-break:break-all;"><? echo $user_name_arr[$val["inserted_by"]]; ?></td>
                                    <td style="word-wrap: break-word; word-break:break-all;" ><? echo change_date_format($val["insert_date"])." ".$val["insert_time"]; ?></td>
                                </tr>
                                <?
                                $i++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <!--<script language="javascript"> setFilterGrid('table_body',-1)</script> -->
                 <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                        <tfoot>
                            <tr>
                                <th width="30">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="120">&nbsp;</th>
									<?
								if($rptType==8)
								{
									?>
									<th width="60">&nbsp;</th>
									<th width="90">&nbsp;</th>
									<?
								}?>
                                <th width="60">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <?
	                            if($rptType==2 || $rptType==3)
	                            {
	                                ?>
	                                 <th width="100">&nbsp;</th>
	                                 <th width="100">&nbsp;</th>
	                                <?
	                            }?>

                                <th width="100">&nbsp;</th>
                                <th width="90">&nbsp;</th>
                                <th width="120">&nbsp;</th>
                                <th width="100">&nbsp;</th>
								<?
								if(($rptType==2 || $rptType==8) && $cbo_item_cat==4 )
								{
									?>
									<th width="60">&nbsp;</th>
									<?
								}?>
                                <th width="120">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="220">&nbsp;</th>
								<? if($rptType==8 ){
	                                ?>
	                                <th width="100">&nbsp;</th>
	                                <?
	                            }?>
                                <th width="80">&nbsp;</th>
                                <th width="60"><? if($rptType==3) echo "Total:"; ?> </th>
                                <?
                                if($rptType==2 || $rptType==1 || $rptType==8 )
                                {
                                    ?>
                                    <th width="80" >&nbsp;</th>
                                    <th width="80" >Total:</th>
                                    <th width="80" >&nbsp;</th>

									<?if($rptType==8){?><th width="60" >&nbsp;</th><?}?>
									<th width="80" id="value_total_receive_qty"><? echo number_format($total_receive_qty,2); ?>&nbsp;&nbsp;</th>
                                    <th width="80" align="right" id="val_tot_receive_ret_qty"><? echo number_format($tot_receive_ret_qty,2); ?>&nbsp;&nbsp;</th>
                                    <th width="80" id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?>&nbsp;&nbsp;</th>

                                    <?
                                }
                                if($rptType==3 || $rptType==1 )
                                {
                                    ?>
                                    <th width="80" id="value_total_issue_qty"><? echo number_format($tot_issue_qty,2); ?>&nbsp;&nbsp;</th>
                                    <th width="80" align="right" id="val_tot_issue_ret_qty"><? echo number_format($tot_issue_ret_qty,2); ?>&nbsp;&nbsp;</th>
                                    <?
                                }
                                ?>


                                <th width="80" ><? //echo number_format($total_rate,2); ?></th>
                                <th width="100" id="value_total_amount"><? echo number_format($total_amount,2); ?>&nbsp;&nbsp;</th>
								<? if($rptType==2){?>
                                <th width="80">&nbsp;</th>
                            	<? } ?>
								<th width="50">&nbsp;</th>
                                <? if($rptType==3 || $rptType==1 ){?>
                                <th width="150">&nbsp;</th>
                            	<? } ?>
								<th width="100">&nbsp;</th>
								<th width="110">&nbsp;</th>
                                <th >&nbsp;</th>
                            </tr>
                        </tfoot>
                   </table>
                 </div>
                 <br />
             <?
            }//die;
            if($cbo_order_type==0 || $cbo_order_type==2)
            {
                ?>
                <table width="<? echo $table_width_non_order; ?>" id="table_header_3" align="left">
                    <tr>
                        <td colspan="<? echo $col_span_non_order; ?>"> <span style="font-weight:bold; font-size:16px;">Non Order Item</span></td>
                    </tr>
                </table>
                <table width="<? echo $table_width_non_order; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left">
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="50" >Prod. Id</th>
                            <th width="100">Store Name</th>
                            <th width="80"> Division</th>
                            <th width="80">Department </th>
                            <th width="80">Trans. Date</th>
                            <th width="120">Trans. Ref.</th>
                            <th width="110">WO Num</th>
                            <th width="80">Buyer Order Num</th>
                            <th width="100">Challan No</th>
                            <th width="120">Party Name</th>
                            <th width="100">Group Name</th>
                            <th width="220">Description</th>
                            <th width="80">RMG Color</th>
                            <th width="60">RMG Size</th>
                            <?
                            if($rptType==2 || $rptType==1 )
                            {
                            ?>
                            <th width="80">Receive Qty</th>
                            <?
                            }
                            if($rptType==3 || $rptType==1 )
                            {
                            ?>
                            <th width="80">Issue Qty</th>
                            <?
                            }
                            ?>
                            <th width="80">Rate(TK)</th>
                            <th width="100">Amount(TK)</th>
                            <th width="48">UOM</th>
                            <th width="110">User</th>
                            <th width="160">Insert Time</th>
                        </tr>
                    </thead>
                        <tbody id="table_header_non_order_3">
                        <?

                        $j=1;
                        foreach($trms_non_order_arr as $trans_key=>$val)
                        {
                            if ($i%2==0)
                            $bgcolor="#E9F3FF";
                            else
                            $bgcolor="#FFFFFF";


                            if($val["transaction_type"]==1 )
                            {
                                $mst_id=$receive_num_arr[$val['rec_issue_id']]["id"];
                                $is_multi=$receive_num_arr[$val['rec_issue_id']]["is_multi"];
                                $entry_form=$receive_num_arr[$val['rec_issue_id']]["entry_form"];
								if($is_multi==0)
								{
									if($print_report_trim_rcv[0]==86){$print_action="trims_receive_entry_print";}
									else if($print_report_trim_rcv[0]==116){$print_action="trims_receive_entry_print_2";}
									else if($print_report_trim_rcv[0]==136){$print_action="trims_receive_entry_print_4";}
									else {$print_action="trims_receive_entry_print";}
								}
								else if($is_multi==3)
								{
									if($print_report_trim_rcv_multi_v3[0]==78){$print_action="trims_receive_entry_print";}
									if($print_report_trim_rcv_multi_v3[0]==84){$print_action="trims_receive_entry_print2";}
									else {$print_action="trims_receive_entry_print";}
								}
								else
								{
									$print_action="trims_receive_entry_print";
								}
                            }
							else if($val["transaction_type"]==4)
							{
								$mst_id=$receive_num_arr[$val['rec_issue_id']]["id"];
                                $is_multi=$receive_num_arr[$val['rec_issue_id']]["is_multi"];
                                $entry_form=$receive_num_arr[$val['rec_issue_id']]["entry_form"];
							}
                            else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                            {
                                $mst_id=$issue_num_arr[$val['rec_issue_id']]["id"];
                                $is_multi=2;
                                $entry_form=$issue_num_arr[$val['rec_issue_id']]["entry_form"];
                            }

                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><p><? echo $j; ?></p></td>
                                <td width="50"><p><? echo $val["prod_id"]; ?></p></td>
                                <td width="100"><p><? echo $store_library[$val["store_id"]]; ?></p></td>
                                <td width="80"><p><? echo $division_arr[$val["division_id"]]; ?></p></td>
                                <td width="80"><p><? echo $department_arr[$val["department_id"]]; ?></p></td>
                                <td width="80" align="center"><p><? if($val["transaction_date"]!="0000-00-00") echo change_date_format($val["transaction_date"]); else echo ""; ?>&nbsp;</p></td>
                                <td width="120"  align="center"><p><a href="##" onClick="generate_trims_print_report('<? echo $val["transaction_type"];?>','<? echo $mst_id; ?>','<? echo $is_multi; ?>','<? echo $val["company_id"];?>','<? echo $entry_form; ?>','<?=$print_action;?>')">
                                <?
                                    if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                    {
                                        echo $receive_num_arr[$val['rec_issue_id']]["recv_number"];
                                        if(empty($receive_num_arr[$val['rec_issue_id']]["recv_number"])){
                                        	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['system_no'];
                                        }
                                    }
                                    else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                    {
                                        echo $issue_num_arr[$val['rec_issue_id']]["issue_number"];
                                        if(empty($issue_num_arr[$val['rec_issue_id']]["issue_number"])){
                                        	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['system_no'];
                                        }
                                    }

                                ?>
                                </a></p></td>
                                <td width="110" title="<? echo $trans_key; ?>"><p><? echo $val["booking_no"]; ?></p></td>
                                <td width="80" title=""><p>
									<?
									if ($rptType==3 && $val["entry_form"]==21) echo rtrim($generalissue_buyer_order_arr[$trans_key],',');
									else echo $buyer_order_arr[$val["order_id"]];
									?>
								</p></td>
                                <td width="100"><p>
                                <?

                                    if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                    {
                                        echo $receive_num_arr[$val['rec_issue_id']]["challan_no"];
                                        if(empty($receive_num_arr[$val['rec_issue_id']]["challan_no"]))
                                        {
                                        	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['challan_no'];
                                        }
                                    }
                                    else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                    {
                                        echo $issue_num_arr[$val['rec_issue_id']]["challan_no"];
                                        if(empty($issue_num_arr[$val['rec_issue_id']]["challan_no"]))
                                        {
                                        	echo $system_data[$trans_key][$val["prod_id"]][$val["transaction_type"]]['challan_no'];
                                        }
                                    }

                                ?>
                                </p></td>
                                <td width="120"><p>
                                <?
                                    if($val["transaction_type"]==1 || $val["transaction_type"]==4)
                                    {
                                        echo $supplier_arr[$receive_num_arr[$val['rec_issue_id']]["supplier_id"]];
                                    }
                                    else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
                                    {
                                        if($issue_num_arr[$val['rec_issue_id']]["knit_dye_source"]==1)
                                        {
                                            echo $company_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
                                        }
                                        else
                                        {
                                            echo $supplier_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
                                        }
                                    }

                                ?>
                                </p></td>
                                <td width="100"><p><? echo $group_arr[$val["item_group_id"]]["item_name"]; ?></p></td>
                                <td width="220"><p><? echo $val["product_name_details"]; ?></p></td>
                                <td width="80"><p><? echo $color_arr[$val["color"]]; ?></p></td>
                                <td width="60"><p><? echo $size_arr[$val["gmts_size"]]; ?></p></td>
                                <?
                                if($rptType==2 || $rptType==1 )
                                {
                                    ?>
                                    <td width="80" align="right"><p><?  $rcv_issue_qnty=$val["receive_qty"]; echo number_format($val["receive_qty"],2,".",""); $total_receive_qty_non_order+=$val["receive_qty"];?></p></td>
                                    <?
                                }
                                if($rptType==3 || $rptType==1 )
                                {
                                    ?>
                                    <td width="80" align="right"><p><? $rcv_issue_qnty=$val["issue_qty"]; echo number_format($val["issue_qty"],2,".",""); $total_issue_qty_non_order+=$val["issue_qty"];?></p></td>
                                    <?
                                }
                                ?>
                                <td width="80" align="right"><p><? echo number_format($val["cons_rate"],4,".",""); ?></p></td>
                                <td width="100" align="right"><p>
                                <?
                                $amount=$rcv_issue_qnty*$val["cons_rate"];
                                 echo number_format($amount,2,".",""); $total_amount_non_order+=$amount;
                                ?>
                                 </p></td>
                                <td width="48"><p><? echo $unit_of_measurement[$val["cons_uom"]]; ?></p></td>
                                <td width="110"><p><? echo $user_name_arr[$val["inserted_by"]]; ?></p></td>
                                <td width="160"><p><? echo change_date_format($val["insert_date"])." ".$val["insert_time"]; ?></p></td>
                            </tr>
                            <?
                            $i++;$j++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <!--<script language="javascript"> setFilterGrid('table_body',-1)</script> -->
                 <table width="<? echo $table_width_non_order; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                        <tfoot>
                            <tr>
                                <th width="30">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="120">&nbsp;</th>
                                <th width="110">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="120">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="220">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="60"><? if($rptType==3) echo "Total:"; ?> </th>
                                <?
                                if($rptType==2 || $rptType==1 )
                                {
                                    ?>
                                    <th width="80" id="value_total_receive_qty_non_order"><? echo number_format($total_receive_qty_non_order,2); ?></th>

                                    <?
                                }
                                if($rptType==3 || $rptType==1 )
                                {
                                    ?>
                                    <th width="80" id="value_total_issue_qty_non_order"><? echo number_format($total_issue_qty_non_order,2); ?></th>
                                    <?
                                }
                                ?>
                                <th width="80"></th>
                                <th width="100" id="value_total_amount_non_order"><? echo number_format($total_amount_non_order,2); ?></th>
                                <th width="48">&nbsp;</th>
                                <th width="110">&nbsp;</th>
                                <th width="160">&nbsp;</th>
                            </tr>
                        </tfoot>
                   </table>
                <?
            }
            ?>
        </div>
			<?
	}

	else if($cbo_item_cat==2 || $cbo_item_cat==3)
	{

		$composition_arr=array();
		$construction_arr=array();
		//$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id ";
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";


		//echo $sql_deter; die;
		$data_array=sql_select($sql_deter);
		//echo "ok"; die;
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$construction_arr))
				{
					$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
				}
				else
				{
					$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				}
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}


		//$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');


		if($cbo_order_type==0 || $cbo_order_type==1) //All and With order
		{
			if($txt_style_ref!="")
			{
				if($txt_style_ref_id!="")
				{
					$style_cond=" and a.id in($txt_style_ref_id)";
				}
				else
				{
					$style_cond=" and a.job_no_prefix_num ='$txt_style_ref'";
				}
			}
			else
			{
				 $style_cond="";
			}

			if($txt_order!="")
			{
				if($txt_order_id!="")
				{
					$order_cond=" and b.id in($txt_order_id)";
				}
				else
				{
					$order_cond=" and b.po_number='$txt_order'";
				}
			}
			else
			{
				$order_cond="";
			}

			if($cbo_search_id==1) //File
			{
				if($txt_search_val!='') //File
				{
					$file_cond=" and b.file_no=$txt_search_val";

				}
				else
				{
					$file_cond="";
				}


			}
			else //Ref
			{
				if($txt_search_val!='')
				{
					$ref_cond=" and b.grouping='$txt_search_val'";
				}
				else
				{
					$ref_cond="";
				}
			}

			if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";

			//echo $date_cond;die;
			$job_sql=sql_select("SELECT a.job_no_prefix_num as job_no, $select_year(a.insert_date $year_con) as job_year, a.buyer_name as buyer_name, a.style_ref_no as style_ref_no, b.id as id, b.grouping as ref_no, b.file_no as file_no, b.po_number as po_number, b.shipment_date as shipment_date, b.po_quantity as po_quantity, a.season_buyer_wise as season_buyer_wise from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $style_cond $order_cond $buyer_cond $file_cond $ref_cond");
 			$job_order_data=array(); $order_id_all=""; $tot_rows=0;
			foreach($job_sql as $row)
			{
				$tot_rows++;
				$order_id_all.=$row[csf('id')].",";
				$job_order_data[$row[csf('id')]][1]=$row[csf("job_no")];
				$job_order_data[$row[csf('id')]][2]=$row[csf("job_year")];
				$job_order_data[$row[csf('id')]][3]=$row[csf("buyer_name")];
				$job_order_data[$row[csf('id')]][4]=$row[csf("po_number")];
				$job_order_data[$row[csf('id')]][5]=$row[csf("shipment_date")];
				$job_order_data[$row[csf('id')]][6]=$row[csf("style_ref_no")];
				$job_order_data[$row[csf('id')]][7]=$row[csf("ref_no")];
				$job_order_data[$row[csf('id')]][8]=$row[csf("file_no")];
				$job_order_data[$row[csf('id')]][9]=$row[csf("po_quantity")];
				$job_order_data[$row[csf('id')]][10]=$row[csf("season_buyer_wise")];
			}
			unset($job_sql);

			$order_id_all=chop($order_id_all," , ");
			$order_propo_cond="";
			if($txt_style_ref!="" || $txt_order!="" || $cbo_buyer_name!=0)
			{
				if($db_type==2 && $tot_rows>1000)
				{
					$poIds_cond_pre=" and (";
					$poIds_cond_suff.=")";
					$poIdsArr=array_chunk(explode(",",$order_id_all),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$order_propo_cond.=" b.po_breakdown_id in($ids) or ";
					}

					$order_propo_cond=$poIds_cond_pre.chop($order_propo_cond,'or ').$poIds_cond_suff;
				}
				else
				{
					$order_propo_cond=" and b.po_breakdown_id in($order_id_all)";
				}
			}
 			//case when b.trans_type in(4) then b.quantity else 0 end as issue_ret_qty,
			$store_cond="";
			if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
			if($rptType==1) //All Button
			{
				$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.pi_wo_batch_no, a.supplier_id, a.batch_id_from_fissuertn, a.rack,b.quantity,b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.batch_lot, a.stitch_length, a.yarn_count, a.store_id, a.cons_rate, a.cons_amount
				from inv_transaction a, order_wise_pro_details b
				where b.po_breakdown_id in(select id from wo_po_break_down b where  is_deleted = 0  and status_active = 1 $file_cond $ref_cond) and a.id=b.trans_id and a.item_category=$cbo_item_cat and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,15,37,18,46,66,68,71,52,134,126,19,202,209,258,17,14,306) $order_propo_cond $store_cond
				order by a.transaction_date, a.id";
				//19,202,209
			}
			else if($rptType==2) //Receive Button
			{
				$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.pi_wo_batch_no, a.batch_id, a.supplier_id, a.batch_id_from_fissuertn, a.rack, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.stitch_length, a.yarn_count, a.store_id, a.cons_rate, a.cons_amount
				from inv_transaction a, order_wise_pro_details b
				where b.po_breakdown_id in(select id from wo_po_break_down b where  is_deleted = 0  and status_active = 1 $file_cond $ref_cond) and a.id=b.trans_id and a.item_category=$cbo_item_cat and a.transaction_type in(1,4,5,3) and b.trans_type in(1,4,5,3) and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,37,15,66,68,46,134,126,52,209,202,258,17) $order_propo_cond $store_cond
				order by a.transaction_date, a.id";
			}
			else if($rptType==3)// Issue Button
			{
				$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.pi_wo_batch_no, a.supplier_id, a.batch_id_from_fissuertn, a.rack, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.stitch_length, a.yarn_count, a.store_id, a.cons_rate, a.cons_amount
				from inv_transaction a, order_wise_pro_details b
				where b.po_breakdown_id in(select id from wo_po_break_down b where  is_deleted = 0  and status_active = 1 $file_cond $ref_cond) and a.id=b.trans_id and a.item_category=$cbo_item_cat and a.transaction_type in(2,3,6,4) and b.trans_type in(2,3,6,4) and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in (15,18,46,71,52,134,19,202,209) $order_propo_cond $store_cond
				order by a.transaction_date, a.id";
			}
		}
		//echo $sql;die;
		$result=sql_select($sql);
		$batch_id_arr = array();
		$po_id_arr = array();
		$prod_id_arr = array();

		$product_ids_arr = array();
		$receive_ids_arr = array();
		$issue_ids_arr = array();
		$transfer_ids_arr = array();
		foreach($result as $row)
		{
            $product_ids_arr[$row["PROD_ID"]]=$row["PROD_ID"];

			if ($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4){
                $receive_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
            } else if ($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3) {
                $issue_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
            } else $transfer_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
		}

		if (count($product_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 16, $product_ids_arr, $empty_arr);

			$product_arr=array();
			$prodDataArr=sql_select("SELECT A.ID, A.DETARMINATION_ID, A.GSM, A.DIA_WIDTH, A.COLOR,A.LOT
			FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND  A.ITEM_CATEGORY_ID IN ($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=16");

			foreach($prodDataArr as $row)
			{
				$product_arr[$row['ID']]['detarmination_id']=$row['DETARMINATION_ID'];
				$product_arr[$row['ID']]['gsm']=$row['GSM'];
				$product_arr[$row['ID']]['dia_width']=$row['DIA_WIDTH'];
				$product_arr[$row['ID']]['color']=$row['COLOR'];
				$product_arr[$row['ID']]['lot']=$row['LOT'];
			}
			unset($prodDataArr);
		}

		if (count($receive_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 17, $receive_ids_arr, $empty_arr);
			$receive_num_arr=array();
			$receive_sql=sql_select("SELECT A.ID, A.RECV_NUMBER, A.CHALLAN_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.RECEIVE_BASIS, A.BOOKING_NO
			FROM INV_RECEIVE_MASTER A , GBL_TEMP_ENGINE B
			WHERE A.ID=B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=17");
			foreach($receive_sql as $row)
			{
				$receive_num_arr[$row['ID']]['recv_number']=$row["RECV_NUMBER"];//1
				$receive_num_arr[$row['ID']]['challan_no']=$row["CHALLAN_NO"];//2
				$receive_num_arr[$row['ID']]['knitting_source']=$row["KNITTING_SOURCE"];//3
				$receive_num_arr[$row['ID']]['knitting_company']=$row["KNITTING_COMPANY"];//4
				$receive_num_arr[$row['ID']]['receive_basis']=$row["RECEIVE_BASIS"];//5
				$receive_num_arr[$row['ID']]['booking_no']=$row["BOOKING_NO"];//6
			}
			unset($receive_sql);
		}

		if (count($issue_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 18, $issue_ids_arr, $empty_arr);
			$issue_num_arr=array();
			$issue_sql=sql_select("SELECT A.ID, A.ISSUE_NUMBER, A.ISSUE_PURPOSE, A.CHALLAN_NO, A.KNIT_DYE_SOURCE, A.KNIT_DYE_COMPANY, A.RECEIVED_ID
			FROM INV_ISSUE_MASTER A, GBL_TEMP_ENGINE B
			WHERE  A.ID=B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=18");
			foreach($issue_sql as $row)
			{
				$issue_num_arr[$row['ID']]['issue_number']=$row["ISSUE_NUMBER"];//1
				$issue_num_arr[$row['ID']]['challan_no']=$row["CHALLAN_NO"];//2
				$issue_num_arr[$row['ID']]['knit_dye_source']=$row["KNIT_DYE_SOURCE"];//3
				$issue_num_arr[$row['ID']]['knit_dye_company']=$row["KNIT_DYE_COMPANY"];//4
				$issue_num_arr[$row['ID']]['received_id']=$row["RECEIVED_ID"];//5
				$issue_num_arr[$row['ID']]['issue_purpose']=$row["ISSUE_PURPOSE"];//6
			}
			unset($issue_sql);
		}

		if (count($transfer_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 19, $transfer_ids_arr, $empty_arr);
			$transfer_sql=sql_select("SELECT A.ID, A.TRANSFER_SYSTEM_ID, A.CHALLAN_NO
			FROM INV_ITEM_TRANSFER_MST A, GBL_TEMP_ENGINE B
			WHERE A.ID=B.REF_VAL AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=19");
			foreach($transfer_sql as $row)
			{
				$transfer_num_arr[$row['ID']][1]=$row["TRANSFER_SYSTEM_ID"];
				$transfer_num_arr[$row['ID']][2]=$row["CHALLAN_NO"];
			}
		}


		foreach ($result as $val)
		{
			if($val[csf("transaction_type")]==1) //Receive
			{
				if ($val[csf("entry_form")]==68)
				{
					$batch_id_arr[$val[csf('batch_id')]] = $val[csf('batch_id')];
				}
				else
				{
					$batch_id_arr[$val[csf('pi_wo_batch_no')]] = $val[csf('pi_wo_batch_no')];
				}
			}
			else if($val[csf("transaction_type")]==4) // issue Return
			{
				$batch_id_arr[$val[csf('batch_id_from_fissuertn')]] = $val[csf('batch_id_from_fissuertn')];
			}
			else if($val[csf("transaction_type")]==2) //issue
			{
				$batch_id_arr[$val[csf('pi_wo_batch_no')]] = $val[csf('pi_wo_batch_no')];
			}

			else if($val[csf("transaction_type")]==3) //Receive Return
			{
				$batch_id_arr[$val[csf('batch_id_from_fissuertn')]] = $val[csf('batch_id_from_fissuertn')];
			}
			else if($val[csf("transaction_type")]==5 || $val[csf("transaction_type")]==6) //Receive
			{
				$batch_id_arr[$val[csf('pi_wo_batch_no')]] = $val[csf('pi_wo_batch_no')];
			}

			$po_id_arr[$val[csf('order_id')]] = $val[csf('order_id')];
			$prod_id_arr[$val[csf('prod_id')]] = $val[csf('prod_id')];
		}

		if(!empty($batch_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 7, $batch_id_arr, $empty_arr);
			$po_batch_sql=sql_select("SELECT A.ID, A.BATCH_NO FROM PRO_BATCH_CREATE_MST A, GBL_TEMP_ENGINE B WHERE A.ID = B.REF_VAL AND  B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=7");
			foreach($po_batch_sql as $row){
				$batchDataArr[$row['ID']] = $row['BATCH_NO'];
			}
		}


		//$poIdAll = implode(",", $po_id_arr);
		//$prodIdAll = implode(",", $prod_id_arr);

		// =============================================================
		$booking_arr=array();

		if(!empty($po_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 6, $po_id_arr, $empty_arr);

			$bookingDataArr=sql_select("SELECT a.fabric_source, b.po_break_down_id as order_id,b.booking_no, b.fabric_color_id, b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.gmts_color_id as color_id from wo_booking_mst a, wo_booking_dtls b , gbl_temp_engine c
			where a.booking_no=b.booking_no
			and b.po_break_down_id = c.ref_val  and a.booking_type in(1,4) and  c.user_id= $user_id and c.entry_form=27 and c.ref_from=6 and  b.status_active=1 and a.status_active=1 group by a.fabric_source,b.po_break_down_id,b.booking_no, b.fabric_color_id, b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.gmts_color_id");

			// echo "SELECT a.fabric_source, b.po_break_down_id as order_id,b.booking_no, b.fabric_color_id, b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.gmts_color_id as color_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($poIdAll) and a.booking_type in(1,4) and b.status_active=1 and a.status_active=1 group by a.fabric_source,b.po_break_down_id,b.booking_no, b.fabric_color_id, b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.gmts_color_id";
		}
		foreach($bookingDataArr as $row)
		{
			$booking_arr[$row[csf('order_id')]][$row[csf('fabric_color_id')]]['booking_no']	= $row[csf('booking_no')];
			$booking_arr[$row[csf('order_id')]][$row[csf('fabric_color_id')]]['construction']	= $row[csf('construction')];
			$booking_arr[$row[csf('order_id')]][$row[csf('fabric_color_id')]]['copmposition']	= $row[csf('copmposition')];
			$booking_arr[$row[csf('order_id')]][$row[csf('fabric_color_id')]]['gsm_weight']	= $row[csf('gsm_weight')];
			$booking_arr[$row[csf('order_id')]][$row[csf('fabric_color_id')]]['fabric_source']	= $row[csf('fabric_source')];
		}
		// print_r($booking_arr);
		unset($bookingDataArr);
		// =====================================================
		if($rptType==3)
		{
			$sql_get_pass_fin="select id,issue_id,challan_no,sys_number from inv_gate_pass_mst where company_id=$cbo_company_name and basis=4 and is_deleted=0 and status_active=1";

			$sql_get_pass_fin_data = sql_select($sql_get_pass_fin);
			$sql_get_pass_fin_arr=array();
			foreach($sql_get_pass_fin_data as $row)
			{
				$sql_get_pass_fin_arr[$row[csf("challan_no")]]["sys_number"]=$row[csf("sys_number")];
			}
			//var_dump($sql_get_pass_fin_arr);die;

			unset($sql_get_pass_fin);
		}

		if($rptType==1 || $rptType==3)
		{

			$sql_knit_fin="select transfer_system_id, challan_no, company_id, transfer_criteria, item_category from inv_item_transfer_mst where company_id=$cbo_company_name and item_category=2 and entry_form in(14,306) and is_deleted=0 and status_active=1";
			$sql_knit_fin_data = sql_select($sql_knit_fin);
			$sql_knit_fin_data_arr=array();
			foreach($sql_knit_fin_data as $row)
			{
				$sql_knit_fin_data_arr[$row[csf("transfer_system_id")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
			}
			//var_dump($sql_knit_fin_data_arr);die;

			unset($sql_knit_fin);


			$sql_non_order_booking_info="select a.grouping,b.sample_type,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_name and a.entry_form_id=140 and a.is_deleted=0 and a.status_active=1 group by a.grouping,b.sample_type,a.booking_no";
			$sql_non_order_bookingData = sql_select($sql_non_order_booking_info);
			$sql_non_order_booking_data_arr=array();
			foreach($sql_non_order_bookingData as $row)
			{
				$sql_non_order_booking_data_arr[$row[csf("booking_no")]]["internalRef"]=$row[csf("grouping")];
				$sql_non_order_booking_data_arr[$row[csf("booking_no")]]["sample_type"]=$row[csf("sample_type")];
			}
			//var_dump($sql_non_order_booking_data_arr);die;

			unset($sql_non_order_booking_info);

			$sample_type_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
		}

		if($cbo_order_type==2)
		{
			$scroll_div="scroll_body";
			$scroll_div_width="1430px";
			$table_width=1410;
		}
		else
		{
			$scroll_div="";
			$scroll_div_width="1900px";
			$table_width=1880;
		}
		?>
		<!--<style>
		.extraThStyle {
				background-image: linear-gradient(rgb(194, 220, 255) 10%, rgb(136, 170, 214) 96%);
				border: 1px solid #8dafda;
				color: #444;
				font-size: 13px;
				font-weight: bold;
				height: 25px;
				line-height: 12px;
				text-align: center;
			}
		</style> -->
		<?
		ob_start();
		?>

		<div style="width:<? echo $scroll_div_width; ?>" id="<? echo $scroll_div; ?>">
			<table width="<? echo $table_width; ?>" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
					</tr>
					<tr style="border:none;">
                        <td colspan="20" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                        </td>
					</tr>
			   </table>
			   <br />
			<?
			if($cbo_order_type==0 || $cbo_order_type==1) //All and With Order
			{
				?>
				<table width="4030" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2">SL</th>
                            <th width="50" rowspan="2">Prod. Id</th>
                            <th width="100" rowspan="2">Store Name</th>
                            <th width="60" rowspan="2">Year</th>
                            <th width="50" rowspan="2">Job No</th>
                            <th width="100" rowspan="2">Style No</th>
                            <th width="70" rowspan="2">Buyer</th>
                            <th width="70" rowspan="2">Season</th>
                            <th width="120" rowspan="2">Order No</th>
                            <th width="80" rowspan="2">Order Qnty.</th>
                            <th width="60" rowspan="2">File No</th>
                            <th width="70" rowspan="2">Ref. No</th>
                            <th width="80" rowspan="2">Ship Date</th>
                            <th width="80" rowspan="2">Trans. Date</th>
                            <th width="130" rowspan="2">Trans. Ref.</th>
							<? if($rptType==1) {?>
							<th width="130" rowspan="2">Transfer Criteria</th>
							<? } ?>
							<? if($rptType==3) {?>
							<th width="130" rowspan="2">Gate Pass No</th>
							<? } ?>
                            <th width="100" rowspan="2">Challan No</th>
                            <th width="120" rowspan="2">Party Name</th>
                            <th width="120" rowspan="2">Booking No</th>
                            <th width="100" rowspan="2">Fab. Source</th>
                            <th width="100" rowspan="2">Construction</th>
                            <th width="110" rowspan="2">Composition</th>
                            <th width="80" rowspan="2">Color</th>
                            <th width="50" rowspan="2">GSM</th>
                            <th width="50" rowspan="2">Dia</th>
                            <th width="60" rowspan="2">Stitch Lenth</th>
                            <th width="240" colspan="3" class="extraThStyle">Receive</th>
                            <th width="960" colspan="12" class="extraThStyle">Issue</th>
                            <th width="100" rowspan="2">Rate</th>
                            <th width="100" rowspan="2">Amount</th>
                            <th width="70" rowspan="2">Batch No</th>
                            <th width="110" rowspan="2">User</th>
                            <th rowspan="2">Insert Date</th>
                        </tr>
                        <tr>
                            <th width="80">Receive Qty</th>
                            <th width="80">Issue ReturnQty</th>
                            <th width="80">Trans. In Qty</th>
                            <th width="80">Bulk Sewing Prod</th>
                            <th width="80">Sample With Order</th>
                            <th width="80">Sample Without Order</th>
                            <th width="80">Trans. Out Qty</th>
                            <th width="80">Receive Return Qty</th>
                            <th width="80">Re-Process</th>
                            <th width="80">Sales</th>
                            <th width="80">Fabric Test</th>
                            <th width="80">Scrap Store</th>
                            <th width="80">Damage</th>
                            <th width="80">Adjustment</th>
                            <th width="80">Stolen</th>
                        </tr>
                    </thead>
			   </table>
			  	<div style="width:4050px; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="4030" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					// echo $sql;die();
					$i=1; $total_receive=""; $total_issue=""; $total_amounts="";

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$shipment_date=$job_order_data[$row[csf("order_id")]][5];

						$mst_id=$row[csf("mst_id")]; $dtls_id=$row[csf("dtls_id")];
						$trasRef=''; $challan_no=''; $knitting_source=''; $knitting_company=''; $yarn_lot=''; $yarn_count=''; $stitch_length=''; $color_id='';$bacth_no='';
						$trasRef=$row[csf("rcv_issue_no")];//die("with tt");
						$challan_no=$row[csf("challan_no")];
						$knitting_source=$row[csf("knitting_source")];



						if($cbo_item_cat==2)
						{
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$row[csf("knitting_company")]];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$row[csf("knitting_company")]];
							}
						}
						else
						{
							$knitting_company=$supplier_arr[$row[csf("supplier_id")]];
						}

						$issue_purpose = "";
						$rcv_booing_no="";
						if($row[csf("transaction_type")]==1) //Receive
						{
							$trasRef=$receive_num_arr[$mst_id]['recv_number'];
							$challan_no=$receive_num_arr[$mst_id]['challan_no'];
							$knitting_source=$receive_num_arr[$mst_id]['knitting_source'];

							if($receive_num_arr[$mst_id][5]==2) $rcv_booing_no=$receive_num_arr[$mst_id][6];
							// $rcv_booing_no=$receive_num_arr[$mst_id]['recv_number'];
							if ($row[csf("entry_form")]==68)
							{
								$bacth_no=$batchDataArr[$row[csf("batch_id")]];
							}
							else
							{
								$bacth_no=$batchDataArr[$row[csf("pi_wo_batch_no")]];
							}
							// $bacth_no=$batch_arr[$row[csf("pi_wo_batch_no")]];
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}

							/*$yarn_lot=$receive_num_arr[$dtls_id][5];
							$yarn_count=$receive_num_arr[$dtls_id][6];
							$stitch_length=$receive_num_arr[$dtls_id][7];
							$color_id=$receive_num_arr[$dtls_id][8];*/
							$yarn_count = $yarn_count_array[6];
						}
						else if($row[csf("transaction_type")]==4) // issue Return
						{
							$trasRef=$receive_num_arr[$mst_id]['recv_number'];
							$challan_no=$receive_num_arr[$mst_id]['challan_no'];
							$knitting_source=$receive_num_arr[$mst_id]['knitting_source'];

                            $bacth_no=$batchDataArr[$row[csf("batch_id_from_fissuertn")]];
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}

							$yarn_lot=$row[csf("batch_lot")];
							$yarn_count=$row[csf("yarn_count")];
							$stitch_length=$row[csf("stitch_length")];
						}
						else if($row[csf("transaction_type")]==2) //issue
						{
							$trasRef=$issue_num_arr[$mst_id]['issue_number'];
							$challan_no=$issue_num_arr[$mst_id]['challan_no'];
							$knitting_source=$issue_num_arr[$mst_id]['knit_dye_source'];
                            $issue_purpose=$issue_num_arr[$mst_id]['issue_purpose'];

                            $bacth_no=$batchDataArr[$row[csf("pi_wo_batch_no")]];
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$issue_num_arr[$mst_id]['knit_dye_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$issue_num_arr[$mst_id]['knit_dye_company']];
							}

							$yarn_lot=$issue_num_arr[$dtls_id]['received_id'];
							$yarn_count=$issue_num_arr[$dtls_id]['issue_purpose'];
							$stitch_length=$issue_num_arr[$dtls_id][7];
							$color_id=$issue_num_arr[$dtls_id][8];
						}
						else if($row[csf("transaction_type")]==3) //Receive Return
						{
							$trasRef=$issue_num_arr[$mst_id]['issue_number'];
							$challan_no=$issue_num_arr[$mst_id]['challan_no'];

							$bacth_no=$batchDataArr[$row[csf("batch_id_from_fissuertn")]];

							$received_id=$issue_num_arr[$mst_id]['received_id'];


							$knitting_source=$receive_num_arr[$received_id]['knitting_source'];
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$receive_num_arr[$received_id]['knitting_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$receive_num_arr[$received_id]['knitting_company']];
							}



							$yarn_lot=$row[csf("batch_lot")];
							$yarn_count=$row[csf("yarn_count")];
							$stitch_length=$row[csf("stitch_length")];
						}
						else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6) //Receive
						{
							$bacth_no=$batchDataArr[$row[csf("pi_wo_batch_no")]];
						}
						else
						{
							$trasRef=$transfer_num_arr[$mst_id][1];
							$challan_no=$transfer_num_arr[$mst_id][2];
							//$yarn_lot=$transfer_num_arr[$dtls_id][3];
							//$yarn_count=$transfer_num_arr[$dtls_id][4];
							//$stitch_length=$transfer_num_arr[$dtls_id][5];
						}

						$knit_fin_feb = $sql_get_pass_fin_arr[$trasRef]["sys_number"];
						$knit_fin_feb_trns = $sql_knit_fin_data_arr[$trasRef]["transfer_criteria"];


						$color='';
						$color_ids=explode(",",$color_id);
						foreach($color_ids as $val)
						{
							if($val>0) $color.=$color_arr[$val].",";
						}
						$color=chop($color,',');

						$yarn_count_name='';
						$yarn_counts=explode(",",$yarn_count);
						foreach($yarn_counts as $val)
						{
							if($val>0) $yarn_count_name.=$yarn_count_arr[$val].",";
						}
						$yarn_count_name=chop($yarn_count_name,',');

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="50" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
							<td width="60" align="center" style="word-break:break-all;"><p><? echo $job_order_data[$row[csf("order_id")]][2]; ?></p></td>
							<td width="50" align="center" style="word-break:break-all;"><p><? echo $job_order_data[$row[csf("order_id")]][1]; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $job_order_data[$row[csf("order_id")]][6]; ?></p></td>
							<td width="70" style="word-break:break-all;"><p><? echo $buyer_short_arr[$job_order_data[$row[csf("order_id")]][3]]; ?></p></td>
							<td width="70" style="word-break:break-all;"><p><? echo $buyer_session_arr[$job_order_data[$row[csf("order_id")]][10]]; ?></p></td>
							<td width="120" style="word-break:break-all;"><p><? echo $job_order_data[$row[csf("order_id")]][4]; ?></p></td>
                            <td width="80" align="right"><? echo $job_order_data[$row[csf("order_id")]][9]; ?></td>
                            <td width="60" align="center" style="word-break:break-all;"><p><? echo $job_order_data[$row[csf("order_id")]][8]; ?></p></td>
                            <td width="70" align="center" style="word-break:break-all;" ><p><? echo $job_order_data[$row[csf("order_id")]][7]; ?></p></td>
							<td width="80" align="center"><p><? if($shipment_date!="0000-00-00" && $shipment_date!="") echo change_date_format($shipment_date); else echo "&nbsp;"; ?></p></td>
							<td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="130" title="<? echo $dtls_id; ?>"><p><? echo $trasRef; ?></p></td>
							<? if($rptType==1) {?>
							<td width="130" ><p><? echo $item_transfer_criteria[$knit_fin_feb_trns]; ?></p></td>
							<? } ?>
							<? if($rptType==3) {?>
							<td width="130" ><p><? echo $knit_fin_feb; ?></p></td>
							<? } ?>
							<td width="100"><p><? echo $challan_no; ?></p></td>
                            <td width="120" title="<? echo $receive_num_arr[$mst_id]['knitting_company']."=".$knitting_source;?>"><p><? echo $knitting_company; ?></p></td>
                            <td width="120" title="<? echo $receive_num_arr[$mst_id][5]."==".$receive_num_arr[$mst_id][6];?>"><p>
                            <?
	                            if($row[csf("transaction_type")]==1)
	                            {
	                            	echo $booking_arr[$row[csf("order_id")]][trim($product_arr[$row[csf('prod_id')]]['color'])]['booking_no'];
	                            }
	                            else
	                            {
	                            	echo $rcv_booing_no;
	                            }
                             ?>

                            </p></td>
							<td width="100"><p>
							<?
								echo $fabric_source[$booking_arr[$row[csf("order_id")]][$product_arr[$row[csf('prod_id')]]['color']]['fabric_source']];
							 ?>
							</p></td>
							<td width="100"><p><? echo $construction_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
							<td width="110"><p><? echo $composition_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$product_arr[$row[csf('prod_id')]]['color']];//$color; ?></p></td>
							<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['gsm']; ?></p></td>
							<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['dia_width']; ?></p></td>
							<td width="60"><p><? echo $stitch_length; ?></p></td>

							<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1){ echo number_format($row[csf("quantity")],2); $total_receive +=$row[csf("quantity")];} ?></p></td>
                            <td width="80" align="right"><p><? if($row[csf("transaction_type")]==4){ echo number_format($row[csf("quantity")],2); $total_issue_return +=$row[csf("quantity")];} ?></p></td>
                            <td width="80" align="right"><p><? if($row[csf("transaction_type")]==5){ echo number_format($row[csf("quantity")],2); $total_trans_in +=$row[csf("quantity")];} ?></p></td>

                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 9){ echo number_format($row[csf("quantity")],2); $total_issue_bulkSewing +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 4){ echo number_format($row[csf("quantity")],2); $total_issue_sampleWithOrder +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 8){ echo number_format($row[csf("quantity")],2); $total_issue_sampleWithOutOrder +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==6){ echo number_format($row[csf("quantity")],2); $total_trans_out +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==3){ echo number_format($row[csf("quantity")],2); $total_rcv_return +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 44){ echo number_format($row[csf("quantity")],2); $total_issue_rePocess +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 3){ echo number_format($row[csf("quantity")],2); $total_issue_sales +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 10){ echo number_format($row[csf("quantity")],2); $total_issue_fabricTest +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 31){ echo number_format($row[csf("quantity")],2); $total_issue_scrapStore +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 26){ echo number_format($row[csf("quantity")],2); $total_issue_damage +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 30){ echo number_format($row[csf("quantity")],2); $total_issue_adjustment +=$row[csf("quantity")];} ?></td>
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 && $issue_purpose == 29){ echo number_format($row[csf("quantity")],2); $total_issue_stolen +=$row[csf("quantity")];} ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
                            <td width="100" align="right"><?
                            	//echo number_format($row[csf("cons_amount")],2);
                            	//$total_amounts +=$row[csf("cons_amount")]; // product level
                            	$cons_amount=$row[csf("quantity")]*$row[csf("cons_rate")];
                            	echo number_format($cons_amount,2); // order level
                            	$total_amounts +=$cons_amount;
                            ?></td>
                            <td width="70"><p><? echo $bacth_no; ?></p></td>
							<td width="110"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></p></td>
							<td><p><? echo $row[csf("insert_date")]; ?></p></td>
						</tr>
						<?
						$i++;
					}
					unset($result);
					?>
					</tbody>
				</table>
				<table width="4030" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<? if($rptType==1) {?>
						<th width="130">&nbsp;</th>
						<? } ?>
						<? if($rptType==3) {?>
						<th width="130">&nbsp;</th>
						<? } ?>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="60">Total:</th>
                        <th width="80" align="right" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_return"><? echo number_format($total_issue_return,2); ?></th>
                        <th width="80" align="right" id="value_total_trans_in"><? echo number_format($total_trans_in,2); ?></th>

                        <th width="80" align="right" id="value_total_issue_bulkSewing"><? echo number_format($total_issue_bulkSewing,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_sampleWithOrder"><? echo number_format($total_issue_sampleWithOrder,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_sampleWithOutOrder"><? echo number_format($total_issue_sampleWithOutOrder,2); ?></th>
                        <th width="80" align="right" id="value_total_trans_out"><? echo number_format($total_trans_out,2); ?></th>
                        <th width="80" align="right" id="value_total_rcv_return"><? echo number_format($total_rcv_return,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_rePocess"><? echo number_format($total_issue_rePocess,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_sales"><? echo number_format($total_issue_sales,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_fabricTest"><? echo number_format($total_issue_fabricTest,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_scrapStore"><? echo number_format($total_issue_scrapStore,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_damage"><? echo number_format($total_issue_damage,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_adjustment"><? echo number_format($total_issue_adjustment,2); ?></th>
                        <th width="80" align="right" id="value_total_issue_stolen"><? echo number_format($total_issue_stolen,2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right" id="value_total_amounts"><? echo number_format($total_amounts,2); ?></th>
                        <th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			 </div>
				<?
			}
			if($cbo_order_type==0 || $cbo_order_type==2) //All and Without Order
			{
				$store_cond="";
				if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
				if($rptType==1) // All Button
				{
					$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.rack, a.inserted_by, $select_insert_date, a.cons_quantity as qty, a.prod_id, b.batch_no, a.store_id,b.booking_no
					from pro_batch_create_mst b, inv_transaction a
					where b.id=case when a.transaction_type in(1,2) then a.pi_wo_batch_no when a.transaction_type in(3,4) then a.batch_id_from_fissuertn when a.transaction_type in(5,6) then a.pi_wo_batch_no end and a.item_category=$cbo_item_cat and b.booking_without_order=1 and a.transaction_type in(1,2,3,4,5,6) and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 $store_cond
					order by a.transaction_date, a.id";
					//echo $sql;
				}
				else if($rptType==2) // Receive Button
				{
					$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.rack, a.inserted_by, $select_insert_date, a.cons_quantity as qty, a.prod_id, b.batch_no, a.store_id
					from pro_batch_create_mst b, inv_transaction a
					where b.id=case when a.transaction_type in(1) then a.pi_wo_batch_no when a.transaction_type in(4) then a.batch_id_from_fissuertn when a.transaction_type in(5) then a.pi_wo_batch_no end and a.item_category=$cbo_item_cat and b.booking_without_order=1 and a.transaction_type in(1,4,5) and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 $store_cond
					order by a.transaction_date, a.id";
				}
				else if($rptType==3) // Issue Button
				{
					$sql="SELECT a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.rack, a.inserted_by, $select_insert_date, a.cons_quantity as qty, a.prod_id, b.batch_no, a.store_id,b.booking_no
					from pro_batch_create_mst b, inv_transaction a
					where b.id=case when a.transaction_type in(2) then a.pi_wo_batch_no when a.transaction_type in(3) then a.batch_id_from_fissuertn when a.transaction_type in(6) then a.pi_wo_batch_no end and a.item_category=$cbo_item_cat and b.booking_without_order=1 and a.transaction_type in(2,3,6) and a.company_id=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 $store_cond
					order by a.transaction_date, a.id";
				}

			$result=sql_select($sql);
			$batch_id_arr = array();
			$po_id_arr = array();
			$prod_id_arr = array();
			$product_ids_arr = array();
			$receive_ids_arr = array();
			$issue_ids_arr = array();
			$transfer_ids_arr = array();
			foreach($result as $row)
			{
				$product_ids_arr[$row["PROD_ID"]]=$row["PROD_ID"];

				if ($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4){
					$receive_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
				} else if ($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3) {
					$issue_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
				} else $transfer_ids_arr[$row["MST_ID"]]=$row["MST_ID"];
			}

			if (count($product_ids_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 16, $product_ids_arr, $empty_arr);

				$product_arr=array();
				$prodDataArr=sql_select("SELECT A.ID, A.DETARMINATION_ID, A.GSM, A.DIA_WIDTH, A.COLOR,A.LOT
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND  A.ITEM_CATEGORY_ID IN ($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=16");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['detarmination_id']=$row['DETARMINATION_ID'];
					$product_arr[$row['ID']]['gsm']=$row['GSM'];
					$product_arr[$row['ID']]['dia_width']=$row['DIA_WIDTH'];
					$product_arr[$row['ID']]['color']=$row['COLOR'];
					$product_arr[$row['ID']]['lot']=$row['LOT'];
				}
				unset($prodDataArr);
			}

			if (count($receive_ids_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 17, $receive_ids_arr, $empty_arr);
				$receive_num_arr=array();
				$receive_sql=sql_select("SELECT A.ID, A.RECV_NUMBER, A.CHALLAN_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.RECEIVE_BASIS, A.BOOKING_NO
				FROM INV_RECEIVE_MASTER A , GBL_TEMP_ENGINE B
				WHERE A.ID=B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=17");
				foreach($receive_sql as $row)
				{
					$receive_num_arr[$row['ID']]['recv_number']=$row["RECV_NUMBER"];//1
					$receive_num_arr[$row['ID']]['challan_no']=$row["CHALLAN_NO"];//2
					$receive_num_arr[$row['ID']]['knitting_source']=$row["KNITTING_SOURCE"];//3
					$receive_num_arr[$row['ID']]['knitting_company']=$row["KNITTING_COMPANY"];//4
					$receive_num_arr[$row['ID']]['receive_basis']=$row["RECEIVE_BASIS"];//5
					$receive_num_arr[$row['ID']]['booking_no']=$row["BOOKING_NO"];//6
				}
				unset($receive_sql);
			}

			if (count($issue_ids_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 18, $issue_ids_arr, $empty_arr);
				$issue_num_arr=array();
				$issue_sql=sql_select("SELECT A.ID, A.ISSUE_NUMBER, A.ISSUE_PURPOSE, A.CHALLAN_NO, A.KNIT_DYE_SOURCE, A.KNIT_DYE_COMPANY, A.RECEIVED_ID
				FROM INV_ISSUE_MASTER A, GBL_TEMP_ENGINE B
				WHERE  A.ID=B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=18");
				foreach($issue_sql as $row)
				{
					$issue_num_arr[$row['ID']]['issue_number']=$row["ISSUE_NUMBER"];//1
					$issue_num_arr[$row['ID']]['challan_no']=$row["CHALLAN_NO"];//2
					$issue_num_arr[$row['ID']]['knit_dye_source']=$row["KNIT_DYE_SOURCE"];//3
					$issue_num_arr[$row['ID']]['knit_dye_company']=$row["KNIT_DYE_COMPANY"];//4
					$issue_num_arr[$row['ID']]['received_id']=$row["RECEIVED_ID"];//5
					$issue_num_arr[$row['ID']]['issue_purpose']=$row["ISSUE_PURPOSE"];//6
				}
				unset($issue_sql);
			}

			if (count($transfer_ids_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 19, $transfer_ids_arr, $empty_arr);
				$transfer_sql=sql_select("SELECT A.ID, A.TRANSFER_SYSTEM_ID, A.CHALLAN_NO
				FROM INV_ITEM_TRANSFER_MST A, GBL_TEMP_ENGINE B
				WHERE A.ID=B.REF_VAL AND A.ITEM_CATEGORY IN($cbo_item_cat) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=19");
				foreach($transfer_sql as $row)
				{
					$transfer_num_arr[$row['ID']][1]=$row["TRANSFER_SYSTEM_ID"];
					$transfer_num_arr[$row['ID']][2]=$row["CHALLAN_NO"];
				}
			}
				?>
                <table width="2290"  id="table_header_3" align="left">
                    <tr>
                        <td colspan="21"><span style="font-size:16px; font-weight:bold;">Non Order Item</span></td>
                    </tr>
                </table>
                <table width="2290" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_filter_2" align="left">
					<thead>
						<tr>
							<th width="30" rowspan="2" >SL</th>
							<th width="50" rowspan="2" >Prod. Id</th>
							<th width="120" rowspan="2" >Store Name</th>
							<th width="80"  rowspan="2">Trans. Date</th>
							<th width="120" rowspan="2" >Trans. Ref.</th>
							<th width="120" rowspan="2" >Transfer Criteria</th>
							<th width="100" rowspan="2" >Sample Type</th>
							<th width="100" rowspan="2" >Challan No</th>
                            <th width="120"  rowspan="2">Party Name</th>
                            <th width="120"  rowspan="2">Internal Ref.</th>
							<th width="160" rowspan="2">Construction</th>
							<th width="130" rowspan="2">Composition</th>
							<th width="80" rowspan="2">Color</th>
							<th width="50" rowspan="2">GSM</th>
							<th width="50" rowspan="2">Dia</th>
                            <th width="80" rowspan="2">Rack</th>
                            <th width="240" colspan="3" class="extraThStyle">Receive</th>
                            <th width="240" colspan="3" class="extraThStyle">Issue</th>
							<th width="70" rowspan="2">Batch No</th>
							<th width="110" rowspan="2">User</th>
							<th rowspan="2">Insert Date</th>
						</tr>
						<tr>
							<th width="80">Receive Qty</th>
                            <th width="80">Issue ReturnQty</th>
                            <th width="80">Trans. In Qty</th>
							<th width="80">Issue Qty</th>
                            <th width="80">Receive Return Qty</th>
                            <th width="80">Trans. Out Qty</th>
						</tr>
					</thead>
			   	</table>
				<table width="2290" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_non_order_3" align="left">
			   	<tbody>
					<?
					$j=1; $total_receive=""; $total_issue="";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$mst_id=$row[csf("mst_id")]; $bacth_no=$row[csf("batch_no")];
						$trasRef=''; $challan_no=''; $knitting_source=''; $knitting_company='';
						if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4)
						{
							$trasRef=$receive_num_arr[$mst_id]['recv_number'];
							$challan_no=$receive_num_arr[$mst_id]['challan_no'];
							$knitting_source=$receive_num_arr[$mst_id]['knitting_source'];

							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$receive_num_arr[$mst_id]['knitting_company']];
							}
						}
						else if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3)
						{
							$trasRef=$issue_num_arr[$mst_id]['issue_number'];
							$challan_no=$issue_num_arr[$mst_id]['challan_no'];
							$knitting_source=$issue_num_arr[$mst_id]['knit_dye_source'];

							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$issue_num_arr[$mst_id]['knit_dye_company']];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$issue_num_arr[$mst_id]['knit_dye_company']];
							}
						}
						else
						{
							$trasRef=$transfer_num_arr[$mst_id][1];
							$challan_no=$transfer_num_arr[$mst_id][2];
							//$yarn_lot=$transfer_num_arr[$dtls_id][3];
							//$yarn_count=$transfer_num_arr[$dtls_id][4];
							//$stitch_length=$transfer_num_arr[$dtls_id][5];
							$knit_fin_feb_trns = $sql_knit_fin_data_arr[$trasRef]["transfer_criteria"];
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $j; ?></p></td>
							<td width="50"><p><? echo $row[csf("prod_id")]; ?></p></td>
							<td width="120"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
							<td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $trasRef; ?></p></td>
							<td width="120"><p><? echo $item_transfer_criteria[$knit_fin_feb_trns]; ?></p></td>
							<td width="100"><p><? echo $sample_type_arr[$sql_non_order_booking_data_arr[$row[csf("booking_no")]]["sample_type"]]; ?></p></td>
							<td width="100"><p><? echo $challan_no; ?></p></td>
							<td width="120"><p><? echo $knitting_company; ?></p></td>
							<td width="120"><p><? echo $sql_non_order_booking_data_arr[$row[csf("booking_no")]]["internalRef"]; ?></p></td>
                            <td width="160"><p><? echo $construction_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
							<td width="130"><p><? echo $composition_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$product_arr[$row[csf('prod_id')]]['color']]; ?></p></td>
							<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['gsm']; ?></p></td>
							<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['dia_width']; ?></p></td>
							<td width="80"><p><? echo $row[csf("rack")]; ?></p></td>
                            <td width="80" align="right">
							<?
								if($row[csf("transaction_type")]==1){ //Receive
									echo number_format($row[csf("qty")],2);
									$total_receive_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td width="80" align="right">
							<?
								if($row[csf("transaction_type")]==4){ //Issue Return
									echo number_format($row[csf("qty")],2);
									$total_issue_return_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td width="80" align="right">
							<?
								if($row[csf("transaction_type")]==5){ // Transfer In
									echo number_format($row[csf("qty")],2);
									$total_transfer_in_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td align="right" width="80">
							<?
								if($row[csf("transaction_type")]==2){ //Issue
									echo number_format($row[csf("qty")],2);
									$total_issue_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td width="80" align="right">
							<?
								if($row[csf("transaction_type")]==3){ //Receive Return
									echo number_format($row[csf("qty")],2);
									$total_receive_return_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td width="80" align="right" title="Trans Out: <? echo $row[csf("qty")]; ?>">
							<?
								if($row[csf("transaction_type")]==6){ //Transfer Out
									echo number_format($row[csf("qty")],2);
									$total_transfer_out_non_order +=$row[csf("qty")];
								} else echo '&nbsp;';
							?>
							</td>
							<td width="70"><p><? echo $bacth_no; ?></p></td>
							<td width="110"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></p></td>
							<td><p><? echo $row[csf("insert_date")]; ?></p></td>
						</tr>
						<?
						$i++;$j++;
					}
					unset($result);
					?>
					</tbody>
				</table>
				<table width="2290" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer2" align="left">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
						<th width="160">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="80">Total:</th>
						<th width="80" ><? echo number_format($total_receive_non_order,2); ?></th>
						<th width="80" ><? echo number_format($total_issue_return_non_order,2); ?></th>
						<th width="80" ><? echo number_format($total_transfer_in_non_order,2); ?></th>
						<th width="80" ><? echo number_format($total_issue_non_order,2); ?></th>
						<th width="80" ><? echo number_format($total_receive_return_non_order,2); ?></th>
						<th width="80" ><? echo number_format($total_transfer_out_non_order,2); ?></th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
				</table>
				<?
			}
			?>
        </div>
		<?
	}

	else if($cbo_item_cat==13)
	{

		$composition_arr=array();
		$construction_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";

		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$construction_arr))
				{
					$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
				}
				else
				{
					$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				}
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}

		function where_con($arrayData,$dataType=0,$table_coloum)
		{
			$chunk_list_arr=array_chunk($arrayData,999);
			$p=1;
			foreach($chunk_list_arr as $process_arr)
			{
				if($dataType==0){
					if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
					else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
				}
				else{
					if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
					else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
				}
				$p++;
			}

			$sql.=") ";
			// echo $sql;die;
			return $sql;
		}
		if ($rptType==7) // Issue 3 Button
		{
			if($cbo_order_type==2)
			{
				$scroll_div="scroll_body";
				$scroll_div_width="2160px";
				$table_width=2140;
			}
			else
			{
				$scroll_div="";
				$scroll_div_width="2160px";
				$table_width=2140;
			}
			$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
			$lib_yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

			//if($cbo_order_type==0 || $cbo_order_type==1 || $cbo_order_type==2)
			//{

				if($txt_style_ref!="")
				{
					if($txt_style_ref_id!="")
					{

						$style_cond=" and a.id in($txt_style_ref_id)";
					}
					else
					{
						$style_cond=" and a.job_no_prefix_num ='$txt_style_ref'";
					}
				}
				else
				{
					 $style_cond="";
				}

				if($txt_order!="")
				{
					if($txt_order_id!="")
					{
						$order_cond=" and b.id in($txt_order_id)";
					}
					else
					{
						$order_cond=" and b.po_number='$txt_order'";
					}
				}
				else
				{
					$order_cond="";
				}
				if($cbo_search_id==1) //File
				{
					if($txt_search_val!='') //File
					{
						$file_cond="and b.file_no=$txt_search_val";
					}
					else
					{
						$file_cond="";
					}
				}
				else //Ref
				{
					if($txt_search_val!='')
					{
						$ref_cond="and b.grouping='$txt_search_val'";
					}
					else
					{
						$ref_cond="";
					}
				}

				$buyer_cond="";
				if($cbo_buyer_name>0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";


				$job_sql=sql_select("SELECT a.job_no_prefix_num as job_no, $select_year(a.insert_date $year_con) as job_year, a.buyer_name as buyer_name, a.style_ref_no as style_ref_no, b.id as id, b.grouping as ref_no, b.file_no as file_no, b.po_number as po_number, b.shipment_date as shipment_date, b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $style_cond $order_cond $buyer_cond $file_cond $ref_cond");
	 			$job_order_data=array(); $order_id_all=""; $tot_rows=0;
				foreach($job_sql as $row)
				{
					$tot_rows++;
					$order_id_all.=$row[csf('id')].",";
					/*$job_order_data[$row[csf('id')]][1]=$row[csf("job_no")];
					$job_order_data[$row[csf('id')]][2]=$row[csf("job_year")];
					$job_order_data[$row[csf('id')]][3]=$row[csf("buyer_name")];
					$job_order_data[$row[csf('id')]][4]=$row[csf("po_number")];
					$job_order_data[$row[csf('id')]][5]=$row[csf("shipment_date")];
					$job_order_data[$row[csf('id')]][6]=$row[csf("style_ref_no")];
					$job_order_data[$row[csf('id')]][7]=$row[csf("ref_no")];
					$job_order_data[$row[csf('id')]][8]=$row[csf("file_no")];
					$job_order_data[$row[csf('id')]][9]=$row[csf("po_quantity")];*/
				}
				unset($job_sql);

				$order_id_all=chop($order_id_all,",");

				$order_propo_cond="";
				if($txt_style_ref!="" || $txt_order!="" || $txt_search_val!=""  || $cbo_buyer_name!=0)
				{

					if($db_type==2 && $tot_rows>1000)
					{
						$poIds_cond_pre=" and (";
						$poIds_cond_suff.=")";
						$poIdsArr=array_chunk(explode(",",$order_id_all),999);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							// $order_propo_cond.=" b.po_breakdown_id in($ids) or ";
							$order_propo_cond.=" d.po_breakdown_id in($ids) or ";
						}
						$order_propo_cond=$poIds_cond_pre.chop($order_propo_cond,'or ').$poIds_cond_suff;
					}
					else
					{
						// $order_propo_cond=" and b.po_breakdown_id in($order_id_all)";
						$order_propo_cond=" and d.po_breakdown_id in($order_id_all)";
					}

				}

				$source_cond_issue="";
				if($cbo_knitting_source>0) $source_cond_issue=" and p.knit_dye_source=$cbo_knitting_source";
				$store_cond="";
				if($cbo_store_name>0) $store_cond.=" and a.store_id=$cbo_store_name";

				/*$sql="SELECT p.id as rece_issue_id, p.issue_number as rcv_issue_no, p.knit_dye_source as knitting_source, p.knit_dye_company as knitting_company, p.booking_id,d.booking_no,p.issue_basis as receive_basis,
				p.issue_purpose, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, d.qnty as quantity, a.prod_id, p.remarks, a.store_id, c.yarn_lot, c.yarn_count, c.color_id, a.stitch_length, a.floor_id, a.room, a.rack, a.self
				from  inv_issue_master p, inv_transaction a, order_wise_pro_details b, inv_grey_fabric_issue_dtls c, pro_roll_details d
				where p.id=a.mst_id and a.id=b.trans_id and  p.id=c.mst_id and a.id=c.trans_id and p.id=d.mst_id and c.id=d.dtls_id and b.po_breakdown_id=d.po_breakdown_id and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.transaction_type in(2) and b.trans_type in(2) $date_cond $order_propo_cond  $source_cond_issue $store_cond";*/
				$sql="SELECT p.id as rece_issue_id, p.issue_number_prefix_num as rcv_issue_no, p.knit_dye_source as knitting_source, p.knit_dye_company as knitting_company, p.booking_id,d.booking_no,p.issue_basis as receive_basis,
				p.issue_purpose, a.id as trans_id, a.transaction_type, a.mst_id, d.po_breakdown_id as order_id, a.transaction_date, d.qnty as quantity, a.prod_id, p.remarks, a.store_id, c.yarn_lot, c.yarn_count, c.color_id, a.stitch_length, a.floor_id, a.room, a.rack, a.self, d.booking_without_order, d.is_sales
				from  inv_issue_master p, inv_transaction a, inv_grey_fabric_issue_dtls c, pro_roll_details d
				where p.id=a.mst_id and  p.id=c.mst_id and a.id=c.trans_id and p.id=d.mst_id and c.id=d.dtls_id and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.transaction_type in(2) and d.is_sales!=1 $date_cond $order_propo_cond  $source_cond_issue $store_cond order by p.id,d.booking_without_order";
				//echo $sql; //die;
				$roll_data= sql_select($sql);
				$issue_data_arr=array();
				foreach($roll_data as $row)
				{
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['issue_qty']+=$row[csf('quantity')];

					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['floor_id'].=$row[csf('floor_id')].',';
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['room'].=$row[csf('room')].',';
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['rack'].=$row[csf('rack')].',';
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['self'].=$row[csf('self')].',';
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['remarks']=$row[csf('remarks')];
					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['transaction_type']=$row[csf('transaction_type')];

					$issue_data_arr[$row[csf('booking_without_order')]][$row[csf('rcv_issue_no')].'!!!!'.$row[csf('store_id')].'!!!!'.$row[csf('knitting_source')].'!!!!'.$row[csf('knitting_company')].'!!!!'.$row[csf('booking_no')].'!!!!'.$row[csf('order_id')].'!!!!'.$row[csf('prod_id')].'!!!!'.$row[csf('yarn_lot')].'!!!!'.$row[csf('yarn_count')].'!!!!'.$row[csf('color_id')].'!!!!'.$row[csf('stitch_length')]]['roll_no']++;

					//$issue_data_arr[$row[csf('rcv_issue_no')]][$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('stitch_length')]]['issue_qty']+=$row[csf('quantity')];
					if ($row[csf('is_sales')]==2 && $row[csf('booking_without_order')]==1)
					{
						$non_order_booking[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					if ($row[csf('is_sales')]==0 && $row[csf('booking_without_order')]==0)
					{
						$with_order_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						$with_order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
				}

				$product_ids_arr = array();
				foreach ($issue_data_arr[0] as $issue_data_key => $row)
				{
					$ex_iss_data=explode("!!!!",$issue_data_key);
					$product_ids_arr[$ex_iss_data[6]]=$ex_iss_data[6];
				}

				if (count($product_ids_arr)>0)
				{
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 20, $product_ids_arr, $empty_arr);

					$product_arr=array();
					$prodDataArr=sql_select("SELECT A.ID, A.ITEM_DESCRIPTION, A.GSM, A.DIA_WIDTH
					FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND  A.ITEM_CATEGORY_ID=13 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=20");

					foreach($prodDataArr as $row)
					{
						$product_arr[$row['ID']]['item_description']=$row['ITEM_DESCRIPTION'];
						$product_arr[$row['ID']]['gsm']=$row['GSM'];
						$product_arr[$row['ID']]['dia_width']=$row['DIA_WIDTH'];
					}
					unset($prodDataArr);
				}


				//echo "<pre>"; print_r($issue_data_arr);die;
				if (count($non_order_booking)>0) // Non-Order booking
				{
					$all_booking_noOrder=array_chunk($non_order_booking,999);
					$all_booking_noOrder_cond=" and";
					foreach($all_booking_noOrder as $dtls_id)
					{
						if($all_booking_noOrder_cond==" and")  $all_booking_noOrder_cond.="(a.id in(".implode(',',$dtls_id).")"; else $all_booking_noOrder_cond.=" or a.id in(".implode(',',$dtls_id).")";
					}
					$all_booking_noOrder_cond.=")";
					//echo $all_booking_noOrder_cond;die;
					$sql_non_order_booking="SELECT a.id,a.booking_no_prefix_num, a.buyer_id, c.style_ref_no
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
					where a.booking_no=b.booking_no and b.style_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_booking_noOrder_cond
					group by a.id,a.booking_no_prefix_num, a.buyer_id, c.style_ref_no";
					//echo $sql_non_order_booking;
					$sql_non_order_booking_data=sql_select($sql_non_order_booking);
					$non_order_booking_data_arr=array();
					foreach($sql_non_order_booking_data as $row)
					{
						$non_order_booking_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no_prefix_num")];
						$non_order_booking_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
						$non_order_booking_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
					}
				}
				//echo "<pre>";print_r($non_order_booking_data_arr);//die;

				if (count($with_order_arr)>0) // Order booking
				{
					$with_order_booking=array_chunk($with_order_arr,999);
					$with_order_booking_cond=" and";
					foreach($with_order_booking as $dtls_id)
					{
						if($with_order_booking_cond==" and")  $with_order_booking_cond.="(b.id in(".implode(',',$dtls_id).")"; else $with_order_booking_cond.=" or b.id in(".implode(',',$dtls_id).")";
					}
					$with_order_booking_cond.=")";
					//echo $with_order_booking_cond;die;
					$sql_order_booking="SELECT b.id, c.booking_no_prefix_num as booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_mst c where a.id=b.mst_id and a.booking_no=c.booking_no $with_order_booking_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, c.booking_no_prefix_num";
					//echo $sql_order_booking;
					$sql_order_booking_data=sql_select($sql_order_booking);
					$with_order_booking_data_arr=array();
					foreach($sql_order_booking_data as $row)
					{
						$with_order_booking_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
					}
				}
				//echo "<pre>";print_r($with_order_booking_data_arr);//die;

				if (count($with_order_id_arr)>0) // Order po_query
				{
					$with_order_id_booking=array_chunk($with_order_id_arr,999);
					$with_order_id_cond=" and";
					foreach($with_order_id_booking as $ids)
					{
						if($with_order_id_cond==" and")  $with_order_id_cond.="(b.id in(".implode(',',$ids).")"; else $with_order_id_cond.=" or b.id in(".implode(',',$ids).")";
					}
					$with_order_id_cond.=")";

					$sql_po_query = "SELECT a.job_no_prefix_num as job_no, $select_year(a.insert_date $year_con) as job_year, a.buyer_name as buyer_name, a.style_ref_no as style_ref_no, b.id as id, b.grouping as ref_no,b.file_no as file_no,b.po_number as po_number, b.shipment_date as shipment_date
					from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $with_order_id_cond";
					//echo $sql_po_query;//die;
					$sql_all_po_data=sql_select($sql_po_query);
					$order_data_arr=array(); $order_id_alls=""; $tot_rowss=0;
					foreach($sql_all_po_data as $row)
					{
						$tot_rowss++;
						$order_id_alls.=$row[csf("id")].",";
						$order_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
						$order_data_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
						$order_data_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
						$order_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
					}
					unset($sql_all_po_data);
				}



			//}
			ob_start();
			?>

			<div style="width:<? echo $scroll_div_width; ?>" id="<? echo $scroll_div;?>">
				<table width="<? echo $table_width; ?>">
					<tr class="form_caption" style="border:none;">
						<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
					</tr>
					<tr style="border:none;">
							<td colspan="24" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
							</td>
					</tr>
			   	</table>
				<br />
				<!-- =======Order data Start===== -->
				<h3 align="left" style="text-align: left;">Order Item</h3>
				<table width="<? echo 2140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
                            <th width="100">Store Name</th>
                            <th width="70">Buyer</th>
							<th width="50">Job No</th>
                            <th width="100">Style No</th>
							<th width="120">Order No</th>
							<th width="100">Fabric Booking No</th>
                            <th width="60">Prog. No</th>
                            <th width="120">Party Name</th>
                            <th width="160">Fabric Types</th>
                            <th width="50">Dia</th>
                            <th width="80">Yarn Count</th>
							<th width="80">Yarn Lot</th>
							<th width="60">S.T.L</th>
							<th width="50">GSM</th>
							<th width="80">Fabric Color</th>
							<th width="70">Issue No</th>
							<th width="70">Roll No</th>
							<th width="80">Issue Qty</th>
							<th width="90">Floor</th>
							<th width="90">Room</th>
                            <th width="90">Rack</th>
                            <th width="90">Shelf</th>
                            <th width="160">Remarks</th>
						</tr>
					</thead>
			   	</table>

			  	<div style="width:<? echo 2160; ?>px; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="<? echo 2140; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1; $total_order_issue=0;$total_order_roll=0;
					//$result=sql_select($sql);
					//foreach($result as $row){
					foreach ($issue_data_arr[0] as $issue_data_key => $row)
					{
						$ex_iss_data=explode("!!!!",$issue_data_key);
						$issue_no=$ex_iss_data[0].'<br>';
						$store_id=$ex_iss_data[1];
						$knitting_source=$ex_iss_data[2];
						$knitting_company_id=$ex_iss_data[3];
						$program_no=$ex_iss_data[4];
						$order_id=$ex_iss_data[5];
						$prod_id=$ex_iss_data[6];
						$yarn_lot=$ex_iss_data[7];
						$yarn_count=$ex_iss_data[8];
						$color_id=$ex_iss_data[9];
						$stitch_length=$ex_iss_data[10];
						//$booking_without_order=$ex_iss_data[11];
						$order_issue_qty=$row['issue_qty'];
						$order_roll_no=$row['roll_no'];
						$floor_ids=$row['floor_id'];
						$room_ids=$row['room'];
						$rack_ids=$row['rack'];
						$shelf_ids=$row['self'];
						$remarks=$row['remarks'];
						$transaction_type=$row['transaction_type'];


						$yarn_count_arr = array_filter(explode(",", $yarn_count));
						$yarn_count_name="";
						foreach ($yarn_count_arr as $val)
						{
							$yarn_count_name .= $lib_yarn_count[$val].", ";
						}
						$yarn_count_name = chop($yarn_count_name,", ");

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($transaction_type==2)
						{
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$knitting_company_id];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$knitting_company_id];
							}
						}

						$floor_id='';
						$floor_id_arr= array_unique( explode(",",$floor_ids));
						foreach($floor_id_arr as $val)
						{
							if($val>0) $floor_id.=$floor_room_rack_array[$val].",";
						}
						$floor_id=chop($floor_id,',');

						$room_id='';
						$room_id_arr= array_unique( explode(",",$room_ids));
						foreach($room_id_arr as $val)
						{
							if($val>0) $room_id.=$floor_room_rack_array[$val].",";
						}
						$room=chop($room_id,',');

						$rack_id='';
						$rack_id_arr= array_unique( explode(",",$rack_ids));
						foreach($rack_id_arr as $val)
						{
							if($val>0) $rack_id.=$floor_room_rack_array[$val].",";
						}
						$rack=chop($rack_id,',');

						$shelf_id='';
						$shelf_id_arr= array_unique( explode(",",$shelf_ids));
						foreach($shelf_id_arr as $val)
						{
							if($val>0) $shelf_id.=$floor_room_rack_array[$val].",";
						}
						$shelf=chop($shelf_id,',');

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $store_library[$store_id]; ?>&nbsp;</p></td>
                            <td width="70"><p><?
							echo $buyer_short_arr[$order_data_arr[$order_id]['buyer_name']]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p>
							<? echo $order_data_arr[$order_id]['job_no']; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $order_data_arr[$order_id]['style_ref_no']; ?>&nbsp;</p></td>
                            <td width="120"><p><? echo $order_data_arr[$order_id]['po_number']; ?>&nbsp;</p></td>
                            <td width="100" title="<? echo $order_id; ?>"><p><? echo $with_order_booking_data_arr[$program_no]['booking_no']; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $program_no; ?>&nbsp;</p></td>
                            <td width="120"><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td width="160" style="word-wrap:break-word; word-break:break-word;"><p><? echo $product_arr[$prod_id]['item_description']; ?>&nbsp;</p></td>
                            <td width="50"><p><? echo $product_arr[$prod_id]['dia_width']; ?>&nbsp;</p></td>
                            <td width="80" style="word-wrap:break-word; word-break:break-word;"><p><? echo $yarn_count_name;//$yarn_count; ?>&nbsp;</p></td>
							<td width="80" style="word-wrap:break-word; word-break:break-word;"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="60" style="word-wrap: break-word; word-break:break-word; width:60px;"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="50"><p><? echo $product_arr[$prod_id]['gsm']; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $issue_no; ?></p></td>
							<td width="70"><p><? echo $order_roll_no; ?>&nbsp;</p></td>
							<td align="right" width="80"><p><? echo number_format($order_issue_qty,2); $total_order_issue+=$order_issue_qty;
								$total_order_roll+=$order_roll_no; ?>&nbsp;</p></td>
							<td width="90"><p><? echo $floor_id; ?></p></td>
							<td width="90"><p><? echo $room; ?></p></td>
							<td width="90"><p><? echo $rack; ?></p></td>
							<td width="90"><p><? echo $shelf; ?></p></td>
							<td width="160"><p><? echo $remarks; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					//unset($result);
					?>
					</tbody>
				</table>

				<table width="<? echo 2140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<th width="30"></th>
                        <th width="100" title="Store Name"></th>
                        <th width="70" title="Buyer"></th>
						<th width="50" title="Job No"></th>
                        <th width="100" title="Style No"></th>
						<th width="120" title="Order No"></th>
						<th width="100" title="Fabric Booking No"></th>
                        <th width="60" title="Prog. No"></th>
                        <th width="120" title="Party Name"></th>
                        <th width="160" title="Fabric Types"></th>
                        <th width="50" title="Dia"></th>
                        <th width="80" title="Yarn Count"></th>
						<th width="80" title="Yarn Lot"></th>
						<th width="60" title="S.T.L"></th>
						<th width="50" title="GSM"></th>
						<th width="80" title="Fabric Color"></th>
						<th width="70" title="Issue No"></th>
						<th width="70" align="right">Total</th>
						<th width="80" id="value_total_issue"><? echo number_format($total_order_issue,2); ?></th>
						<th width="90" title="Floor"></th>
						<th width="90" title="Room"></th>
                        <th width="90" title="Rack"></th>
                        <th width="90" title="Shelf"></th>
                        <th width="160" title="Remarks"></th>
					</tfoot>
				</table>
			 	</div><!-- =======Order data End===== -->

			 	<!-- ===============Non-Order Start=========== -->
			 	<br>
			 	<br>
			 	<h3 align="left" style="text-align: left;">Non-Order Item</h3>
			 	<table width="<? echo 2140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
                            <th width="100">Store Name</th>
                            <th width="70">Buyer</th>
							<th width="50">Job No</th>
                            <th width="100">Style No</th>
							<th width="120">Order No</th>
							<th width="100">Fabric Booking No</th>
                            <th width="60">Prog. No</th>
                            <th width="120">Party Name</th>
                            <th width="160">Fabric Types</th>
                            <th width="50">Dia</th>
                            <th width="80">Yarn Count</th>
							<th width="80">Yarn Lot</th>
							<th width="60">S.T.L</th>
							<th width="50">GSM</th>
							<th width="80">Fabric Color</th>
							<th width="70">Issue No</th>
							<th width="70">Roll No</th>
							<th width="80">Issue Qty</th>
							<th width="90">Floor</th>
							<th width="90">Room</th>
                            <th width="90">Rack</th>
                            <th width="90">Shelf</th>
                            <th width="160">Remarks</th>
						</tr>
					</thead>
			   	</table>

			  	<!-- <div style="width:<? echo 2160; ?>px; overflow-y: scroll; max-height:250px;" id="scroll_body"> -->
				<table width="<? echo 2140; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
					<?
					$j=1; $total_non_order_issue=0;$total_non_order_roll=0;
					//$result=sql_select($sql);
					//foreach($result as $row){
					foreach ($issue_data_arr[1] as $issue_data_key => $row)
					{
						$ex_iss_data=explode("!!!!",$issue_data_key);
						$issue_no=$ex_iss_data[0].'<br>';
						$store_id=$ex_iss_data[1];
						$knitting_source=$ex_iss_data[2];
						$knitting_company_id=$ex_iss_data[3];
						$program_no=$ex_iss_data[4];
						$order_id=$ex_iss_data[5];
						$prod_id=$ex_iss_data[6];
						$yarn_lot=$ex_iss_data[7];
						$yarn_count=$ex_iss_data[8];
						$color_id=$ex_iss_data[9];
						$stitch_length=$ex_iss_data[10];
						//$booking_without_order=$ex_iss_data[11];
						$non_order_issue_qty=$row['issue_qty'];
						$non_order_roll_no=$row['roll_no'];
						$floor_ids=$row['floor_id'];
						$room_ids=$row['room'];
						$rack_ids=$row['rack'];
						$shelf_ids=$row['self'];
						$remarks=$row['remarks'];
						$transaction_type=$row['transaction_type'];

						$yarn_count_arr = array_filter(explode(",", $yarn_count));
						$yarn_count_name="";
						foreach ($yarn_count_arr as $val)
						{
							$yarn_count_name .= $lib_yarn_count[$val].", ";
						}
						$yarn_count_name = chop($yarn_count_name,", ");

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($transaction_type==2)
						{
							if($knitting_source==1)
							{
								$knitting_company=$company_arr[$knitting_company_id];
							}
							else if($knitting_source==3)
							{
								$knitting_company=$supplier_arr[$knitting_company_id];
							}
						}

						$floor_id='';
						$floor_id_arr= array_unique( explode(",",$floor_ids));
						foreach($floor_id_arr as $val)
						{
							if($val>0) $floor_id.=$floor_room_rack_array[$val].",";
						}
						$floor_id=chop($floor_id,',');

						$room_id='';
						$room_id_arr= array_unique( explode(",",$room_ids));
						foreach($room_id_arr as $val)
						{
							if($val>0) $room_id.=$floor_room_rack_array[$val].",";
						}
						$room=chop($room_id,',');

						$rack_id='';
						$rack_id_arr= array_unique( explode(",",$rack_ids));
						foreach($rack_id_arr as $val)
						{
							if($val>0) $rack_id.=$floor_room_rack_array[$val].",";
						}
						$rack=chop($rack_id,',');

						$shelf_id='';
						$shelf_id_arr= array_unique( explode(",",$shelf_ids));
						foreach($shelf_id_arr as $val)
						{
							if($val>0) $shelf_id.=$floor_room_rack_array[$val].",";
						}
						$shelf=chop($shelf_id,',');

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $j; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $store_library[$store_id]; ?>&nbsp;</p></td>
                            <td width="70"><p><?
							echo $buyer_short_arr[$non_order_booking_data_arr[$order_id]['buyer_id']]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p>&nbsp;</p></td>
                            <td width="100"><p><? echo $non_order_booking_data_arr[$order_id]['style_ref_no']; ?>&nbsp;</p></td>
                            <td width="120"><p>&nbsp;</p></td>
                            <td width="100" title="<? echo $order_id; ?>"><p><? echo $non_order_booking_data_arr[$order_id]['booking_no']; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $program_no; ?>&nbsp;</p></td>
                            <td width="120"><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td width="160" style="word-wrap:break-word; word-break:break-word;"><p><? echo $product_arr[$prod_id]['item_description']; ?>&nbsp;</p></td>
                            <td width="50"><p><? echo $product_arr[$prod_id]['dia_width']; ?>&nbsp;</p></td>
                            <td width="80" style="word-wrap:break-word; word-break:break-word;"><p><? echo $yarn_count_name//$yarn_count; ?>&nbsp;</p></td>
							<td width="80" style="word-wrap:break-word; word-break:break-word;"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
							<td width="60" style="word-wrap: break-word; word-break:break-word; width:60px;"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="50"><p><? echo $product_arr[$prod_id]['gsm']; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
							<td width="70" title="Trans Type=<? echo $row[csf("transaction_type")];?>"><p><? echo $issue_no; ?></p></td>
							<td width="70"><p><? echo $non_order_roll_no; ?>&nbsp;</p></td>
							<td align="right" width="80"><p><? echo number_format($non_order_issue_qty,2);
							$total_non_order_issue+=$non_order_issue_qty;
							$total_non_order_roll+=$non_order_roll_no;
							?></p></td>
							<td width="90"><p><? echo $floor_id; ?></p></td>
							<td width="90"><p><? echo $room; ?></p></td>
							<td width="90"><p><? echo $rack; ?></p></td>
							<td width="90"><p><? echo $shelf; ?></p></td>
							<td width="160"><p><? echo $remarks; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;$j++;
					}
					//unset($result);
					?>
					</tbody>
				</table>

				<table width="<? echo 2140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<th width="30"></th>
                        <th width="100" title="Store Name"></th>
                        <th width="70" title="Buyer"></th>
						<th width="50" title="Job No"></th>
                        <th width="100" title="Style No"></th>
						<th width="120" title="Order No"></th>
						<th width="100" title="Fabric Booking No"></th>
                        <th width="60" title="Prog. No"></th>
                        <th width="120" title="Party Name"></th>
                        <th width="160" title="Fabric Types"></th>
                        <th width="50" title="Dia"></th>
                        <th width="80" title="Yarn Count"></th>
						<th width="80" title="Yarn Lot"></th>
						<th width="60" title="S.T.L"></th>
						<th width="50" title="GSM"></th>
						<th width="80" title="Fabric Color"></th>
						<th width="70" title="Issue No"></th>
						<th width="70" align="right">Total</th>
						<th width="80"><? echo number_format($total_non_order_issue,2); ?></th>
						<th width="90" title="Floor"></th>
						<th width="90" title="Room"></th>
                        <th width="90" title="Rack"></th>
                        <th width="90" title="Shelf"></th>
                        <th width="160" title="Remarks"></th>
					</tfoot>
				</table>
				<table width="<? echo 2140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<th width="30"></th>
                        <th width="100" title="Store Name"></th>
                        <th width="70" title="Buyer"></th>
						<th width="50" title="Job No"></th>
                        <th width="100" title="Style No"></th>
						<th width="120" title="Order No"></th>
						<th width="100" title="Fabric Booking No"></th>
                        <th width="60" title="Prog. No"></th>
                        <th width="120" title="Party Name"></th>
                        <th width="160" title="Fabric Types"></th>
                        <th width="50" title="Dia"></th>
                        <th width="80" title="Yarn Count"></th>
						<th width="80" title="Yarn Lot"></th>
						<th width="60" title="S.T.L"></th>
						<th width="50" title="GSM"></th>
						<th width="80" title="Fabric Color"></th>
						<th width="70" title="Issue No"></th>
						<th width="70" align="right">Grand Total</th>
						<th width="80"><? echo number_format($total_order_issue+$total_non_order_issue,2); ?></th>
						<th width="90" title="Floor"></th>
						<th width="90" title="Room"></th>
                        <th width="90" title="Rack"></th>
                        <th width="90" title="Shelf"></th>
                        <th width="160" title="Remarks"></th>
					</tfoot>
				</table>
			 	<!-- </div> -->
			 	<!-- Non-Order End -->

				<!-- Summary Start -->
				<br>
				<table width="300"  style=" margin-top:5px;"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
					<thead>
						<tr>
							<th colspan="3" style="text-align: center;"><strong>Summary</strong></th>
						</tr>
						<tr>
							<th><strong>Name</strong></th>
							<th><strong>Total Roll</strong></th>
							<th><strong>Total Qty.</strong></th>
						</tr>
						<tr>
							<td style="background: #FFFFFF;"><strong>Order Item</strong></td>
							<td style="background: #FFFFFF;"><strong><? echo $total_order_roll; ?></strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? echo number_format($total_order_issue,2,".",""); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #E9F3FF;"><strong>Non Order Item</strong></td>
							<td style="background: #E9F3FF;"><strong><? echo $total_non_order_roll; ?></strong></td>
							<td align="right" style="background: #E9F3FF;"><strong><? echo number_format($total_non_order_issue,2,".",""); ?></strong></td>
						</tr>
						<tr>
							<td style="background: #FFFFFF;"><strong>Total</strong></td>
							<td style="background: #FFFFFF;"><strong><? echo $total_order_roll+$total_non_order_roll; ?></strong></td>
							<td align="right" style="background: #FFFFFF;"><strong><? echo number_format(($total_order_issue+$total_non_order_issue),2,".",""); ?></strong></td>
						</tr>
					</thead>
				</table>
				<!-- Summary End -->
			</div>
			<?
		} // Issue 3 Button End
		else
		{

			if($cbo_order_type==2)
			{
				$scroll_div="scroll_body";
				$scroll_div_width="1650px";
				$table_width=1630;
			}
			else
			{
				$scroll_div="";
				$scroll_div_width="2130px";
				$table_width=2740;
			}


			$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count where status_active=1",'id','yarn_count');

			$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$cbo_company_name' and item_category_id=13 and variable_list=3 and is_deleted=0 and status_active=1");
			if($roll_maintained!='') $roll_maintained=$roll_maintained;else $roll_maintained=2;


			if($cbo_order_type==0 || $cbo_order_type==1)
			{
				if($txt_style_ref!="")
				{
					if($txt_style_ref_id!="")
					{
						$style_cond=" and a.id in($txt_style_ref_id)";
					}
					else
					{
						$style_cond=" and a.job_no_prefix_num ='$txt_style_ref'";
					}
				}
				else
				{
					 $style_cond="";
				}

				if($txt_order!="")
				{
					if($txt_order_id!="")
					{
						$order_cond=" and b.id in($txt_order_id)";
					}
					else
					{
						$order_cond=" and b.po_number='$txt_order'";
					}
				}
				else
				{
					$order_cond="";
				}
				if($cbo_search_id==1) //File
				{
					if($txt_search_val!='') //File
					{
						$file_cond="and b.file_no=$txt_search_val";
					}
					else
					{
						$file_cond="";
					}
				}
				else //Ref
				{
					if($txt_search_val!='')
					{
						$ref_cond="and b.grouping='$txt_search_val'";
					}
					else
					{
						$ref_cond="";
					}
				}
				//echo $date_cond_issue;die;
				$buyer_cond="";
				if($cbo_buyer_name>0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";

				$job_sql=sql_select("SELECT a.job_no_prefix_num as job_no, $select_year(a.insert_date $year_con) as job_year, a.buyer_name as buyer_name, a.style_ref_no as style_ref_no, b.id as id, b.grouping as ref_no, b.file_no as file_no, b.po_number as po_number, b.shipment_date as shipment_date, b.po_quantity as po_quantity
				from wo_po_details_master a, wo_po_break_down b
				where a.id=b.job_id $style_cond $order_cond $buyer_cond $file_cond $ref_cond $year_cond");

				// $con = connect();
				// $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (187)");
				// oci_commit($con);
				//$r_id2=execute_query("delete from tmp_poid where userid=$user_id");
				//$r_id3=execute_query("delete from tmp_mrr_no where userid=$user_id");
				//$r_id5=execute_query("delete from tmp_prog_no where userid=$user_id");
				//if($r_id2 || $r_id3 || $r_id5)
				//{
					//oci_commit($con);
				//}

				$po_breakdown_id_arr=array();
				if(!empty($job_sql))
				{
					foreach ($job_sql as $row)
					{
						$po_breakdown_id_arr[$row[csf('id')]]=$row[csf('id')];
						$sales_order_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];//1
						$sales_order_data_arr[$row[csf("id")]]['job_year']=$row[csf("job_year")];//2
						$sales_order_data_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];//3
						$sales_order_data_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];//4
						$sales_order_data_arr[$row[csf("id")]]['shipment_date']=$row[csf("shipment_date")];//5
						$sales_order_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];//6
						$sales_order_data_arr[$row[csf("id")]]['ref_no']=$row[csf("ref_no")];//7
						$sales_order_data_arr[$row[csf("id")]]['file_no']=$row[csf("file_no")];//8
					}
				}
				else
				{
					echo "Data Not Found";die;
				}

				if(count($po_breakdown_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 1, $po_breakdown_id_arr, $empty_arr);
				unset($job_sql);
				//echo "try2";die;
				$order_propo_cond="";$order_propo_cond2="";$order_propo_cond3="";$order_propo_cond4="";
				$sql_deter=sql_select("select a.id, a.machine_no, a.dia_width, a.gauge from lib_machine_name  a");
				//echo "select a.id, a.machine_no, a.dia_width, a.gauge from lib_machine_name  a";//die;
				foreach($sql_deter as $row)
				{
					$mc_data_arr[$row[csf("id")]]['machine_no']=$row[csf("machine_no")];
					$mc_data_arr[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
					$mc_data_arr[$row[csf("id")]]['gauge']=$row[csf("gauge")];
				}
				unset($sql_deter);
				$sql_brand=sql_select("select a.id, a.brand_name from lib_brand a where a.brand_name is not null");
				foreach($sql_brand as $row)
				{
					$brand_data_arr[$row[csf("id")]]=$row[csf("brand_name")];
				}
				unset($sql_brand);
				$program_no_arr=array();
				//echo "try2 = $roll_maintained";die;

				if($roll_maintained==1)
				{
					//join tmp table

					$sql_roll_knit = "select x.knitting_source, x.knitting_company, x.store_id, a.color_range_id, a.machine_dia, a.color_id, a.machine_gg, a.brand_id, c.po_breakdown_id, a.machine_no_id as machine_no_id, c.booking_no, c.barcode_no
					from inv_receive_master x, pro_grey_prod_entry_dtls a, pro_roll_details c, GBL_TEMP_ENGINE y
					where x.id=a.mst_id and a.id=c.dtls_id and c.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and x.ENTRY_FORM in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.machine_no_id>0 $date_cond_roll";//die("with parinde");
					//echo $sql_roll_knit;die;
					$knit_data=sql_select($sql_roll_knit);
					$knit_data_arr=array();$knit_data_brand_arr=array(); $knit_data_party_store_arr=array();

					foreach($knit_data as $rows)
					{

						if($rows[csf('machine_gg')]!="" || $rows[csf('machine_dia')]!="" || $rows[csf('color_range_id')]!=0 || $rows[csf('brand_id')]!=0 || $rows[csf('color_id')]!=0)
						{
							$knit_data_arr[$rows[csf('barcode_no')]][$rows[csf('machine_no_id')]][$rows[csf('booking_no')]]['machine_gg']=$rows[csf('machine_gg')];
							$knit_data_arr[$rows[csf('barcode_no')]][$rows[csf('machine_no_id')]][$rows[csf('booking_no')]]['machine_dia']=$rows[csf('machine_dia')];
							$knit_data_brand_arr[$rows[csf('barcode_no')]][$rows[csf('booking_no')]]['brand_id']=$rows[csf('brand_id')];

							$knit_data_brand_arr[$rows[csf('barcode_no')]][$rows[csf('booking_no')]]['machine_no_id']=$rows[csf('machine_no_id')];


							$knit_data_party_store_arr[$rows[csf('barcode_no')]]['color_id']=$rows[csf('color_id')];

							$knit_data_party_store_arr[$rows[csf('barcode_no')]]['knitting_source']=$rows[csf('knitting_source')];
							$knit_data_party_store_arr[$rows[csf('barcode_no')]]['knitting_company']=$rows[csf('knitting_company')];
							$knit_data_party_store_arr[$rows[csf('barcode_no')]]['store_id']=$rows[csf('store_id')];
							$knit_data_party_store_arr[$rows[csf('barcode_no')]]['color_range_id']=$rows[csf('color_range_id')];
						}

					}

					unset($knit_data);//die("with parinde");
					//print_r($knit_data_party_store_arr);die("with purple");
					if($rptType==1 || $rptType==2 || $rptType==3)
					{
						$sql_rolldata="select a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id,b.color_id,b.yarn_lot,b.yarn_count, b.stitch_length, c.roll_no, b.width, b.body_part_id, b.brand_id, b.machine_no_id, b.color_id, b.color_range_id, c.barcode_no, c.booking_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales
						from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, GBL_TEMP_ENGINE y
						where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond_rolldata";//die("with despacito");
						//echo $sql_rolldata;die;
					 	$data_array_mst = sql_select($sql_rolldata);
						$all_program_no=array();
						foreach( $data_array_mst as $row)
						{
							$machine_gg=$knit_data_arr[$row[csf('barcode_no')]][$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_gg'];
							$machine_dia=$knit_data_arr[$row[csf('barcode_no')]][$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_dia'];
							$brand_id=$knit_data_brand_arr[$row[csf('barcode_no')]][$row[csf('booking_no')]]['brand_id'];

							$machine_no_id=$knit_data_brand_arr[$row[csf('barcode_no')]][$row[csf('booking_no')]]['machine_no_id'];

							$knitting_source=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_source'];
							$knitting_company=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_source'];
							$store_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['store_id'];
							$color_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_id'];
							$color_range_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_range_id'];

							//$knit_data_brand_arr[$rows[csf('barcode_no')]][$rows[csf('booking_no')]]['machine_no_id']=$rows[csf('machine_no_id')];

							//echo $machine_gg.'DDD';
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['roll_no']=$row[csf("roll_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['booking_no']=$row[csf("booking_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['brand_id']=$brand_data_arr[$brand_id];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['machine_no']=$mc_data_arr[$machine_no_id]['machine_no'];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['mdia']=$machine_dia;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['mgg']=$machine_gg;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['color_range_id']=$color_range_id;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['barcode_no']=$row[csf("barcode_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['machine_no_id']=$machine_no_id;//$row[csf("machine_no_id")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['yarn_lot']=$row[csf("yarn_lot")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['y_count']=$row[csf("yarn_count")];
							//$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['color_id']=$row[csf("color_id")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['stitch_length']=$row[csf("stitch_length")];

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['knitting_source']=$knitting_source;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['knitting_company']=$knitting_company;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['store_id']=$store_id;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['color_id']=$color_id;
							if(!strpos($row[csf('booking_no')], "-"))
							{
								array_push($program_no_arr, $row[csf('booking_no')]);
							}
							if(strlen($row[csf('booking_no')])<7)
							{
								$all_program_no[$row[csf('booking_no')]]=$row[csf('booking_no')];
								//$r_id6=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')");
								//if($r_id6)
								//{
									//$r_id6=1;
								//}
								//else
								//{
									//echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')";
									//oci_rollback($con);
									//die;
								//}
							}
						}
						unset($data_array_mst);


						//print_r($roll_data_arr);die; $date_cond_transfer


						/* $sql_grysts_query = "SELECT a.id, a.entry_form, a.transfer_system_id, b.yarn_lot,b.y_count,b.stitch_length, b.from_prod_id as prod_id,b.color_id,b.machine_no_id, b.brand_id,c.dtls_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.booking_no, c.roll_id as roll_id_prev, 3 as type
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, GBL_TEMP_ENGINE y
						WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.entry_form in(183,110,133,82) and c.entry_form in(183,110,133,82) and c.status_active=1 and c.is_deleted=0 order by barcode_no"; */

						$sql_grysts_query = "SELECT a.id, a.entry_form, a.transfer_system_id, b.yarn_lot,b.y_count,b.stitch_length, b.from_prod_id as prod_id,b.color_id,b.machine_no_id, b.brand_id,c.dtls_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.booking_no, c.roll_id as roll_id_prev, 3 as type
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, inv_transaction d,order_wise_pro_details  e, GBL_TEMP_ENGINE y
						WHERE a.id=b.mst_id and a.id=d.mst_id and d.id = e.trans_id and b.id=c.dtls_id and e.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.entry_form in(183,110,133,82) and c.entry_form in(183,110,133,82) and c.status_active=1 and c.is_deleted=0 order by barcode_no";//die("with despacito");
						//echo $sql_grysts_query;die;
						$sql_grysts=sql_select($sql_grysts_query);

						foreach( $sql_grysts as $row)
						{

							$brand_id=$knit_data_brand_arr[$row[csf('barcode_no')]][$row[csf('booking_no')]]['brand_id'];
							$color_range_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_range_id'];

							$machine_no_id=$knit_data_brand_arr[$row[csf('barcode_no')]][$row[csf('booking_no')]]['machine_no_id'];

							$machine_gg=$knit_data_arr[$row[csf('barcode_no')]][$machine_no_id][$row[csf('booking_no')]]['machine_gg'];
							$machine_dia=$knit_data_arr[$row[csf('barcode_no')]][$machine_no_id][$row[csf('booking_no')]]['machine_dia'];

							$knitting_source=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_source'];
							$knitting_company=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_company'];
							//echo $knitting_source.', ';
							$store_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['store_id'];

							$color_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_id'];


							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['roll_no']=$row[csf("roll_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['booking_no']=$row[csf("booking_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['barcode_no']=$row[csf("barcode_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['yarn_lot']=$row[csf("yarn_lot")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['y_count']=$row[csf("y_count")];
							//$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['color_id']=$row[csf("color_id")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['stitch_length']=$row[csf("stitch_length")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['brand_id']=$brand_data_arr[$brand_id];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['machine_no']=$mc_data_arr[$machine_no_id]['machine_no'];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['mdia']=$machine_dia;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['mgg']=$machine_gg;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['color_range_id']=$color_range_id;

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['knitting_source']=$knitting_source;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['knitting_company']=$knitting_company;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['store_id']=$store_id;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]['color_id']=$color_id;
							if(!strpos($row[csf('booking_no')], "-"))
							{
								array_push($program_no_arr, $row[csf('booking_no')]);
							}
							if(strlen($row[csf('booking_no')])<7 && $all_program_no[$row[csf('booking_no')]]=="")
							{
								$all_program_no[$row[csf('booking_no')]]=$row[csf('booking_no')];
								//$r_id6=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')");
								//if($r_id6)
								//{
									//$r_id6=1;
								//}
								//else
								//{
									//echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')";
									//oci_rollback($con);
									//die;
								//}
							}
						}
						unset($sql_grysts);

					} //Recv End
					// echo "<pre>";print_r($roll_data_arr);die;
					if($rptType==1 || $rptType==3)
					{
						//c.is_returned!=1 need to discus about this

						$sql_issue_query = "select a.id, a.entry_form, a.issue_number, a.booking_id, b.id as dtls_id, b.prod_id, b.color_id, b.stitch_length, b.machine_id as machine_no_id, b.brand_id, b.yarn_lot, b.yarn_count, c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no
						from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, GBL_TEMP_ENGINE y
						where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(61) and c.entry_form in(61) and c.status_active=1 and c.is_deleted=0 and c.is_returned!=1 and c.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 $date_cond_issue";//die("with despacito");
						//echo $sql_issue_query; die;
						$data_isssue_array=sql_select($sql_issue_query);

						foreach( $data_isssue_array as $row)
						{
							$machine_gg=$knit_data_arr[$row[csf('barcode_no')]][$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_gg'];
							$machine_dia=$knit_data_arr[$row[csf('barcode_no')]][$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_dia'];
							$brand_id=$knit_data_brand_arr[$row[csf('barcode_no')]][$row[csf('booking_no')]]['brand_id'];
							$color_range_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_range_id'];

							$knitting_source=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_source'];
							$knitting_company=$knit_data_party_store_arr[$row[csf('barcode_no')]]['knitting_company'];
							$store_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['store_id'];
							$color_id=$knit_data_party_store_arr[$row[csf('barcode_no')]]['color_id'];


							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['roll_no']=$row[csf("roll_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['booking_no']=$row[csf("booking_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['barcode_no']=$row[csf("barcode_no")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['brand_id']=$brand_data_arr[$brand_id];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['machine_no']=$mc_data_arr[$row[csf("machine_no_id")]]['machine_no'];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['mdia']=$machine_dia;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['mgg']=$machine_gg;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['color_range_id']=$color_range_id;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['yarn_lot']=$row[csf("yarn_lot")];
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['y_count']=$row[csf("yarn_count")];
							//print_r("kakku");
							//$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['color_id']=$color_id;
							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['stitch_length']=$row[csf("stitch_length")];

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['knitting_source']=$knitting_source;

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['knitting_company']=$knitting_company;

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['store_id']=$store_id;

							$roll_data_arr[$row[csf("dtls_id")]][$row[csf("issue_number")]]['color_id']=$color_id;
							if(!strpos($row[csf('booking_no')], "-"))
							{
								array_push($program_no_arr, $row[csf('booking_no')]);
							}
							if(strlen($row[csf('booking_no')])<7  && $all_program_no[$row[csf('booking_no')]]=="")
							{
								$all_program_no[$row[csf('booking_no')]]=$row[csf('booking_no')];
								//$r_id6=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')");
								//if($r_id6)
								//{
									//$r_id6=1;
								//}
								//else
								//{
									//echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,'".$row[csf('booking_no')]."')";
									//oci_rollback($con);
									//die;
								//}
							}
						}

						unset($data_isssue_array);

					} //Issue End

				}

				//echo $sql_issue_query; die;
				//echo "<pre>";print_r($roll_data_arr);die;
				if(count($all_program_no)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 2, $all_program_no, $empty_arr);

				$program_arr=array();
				if(count($all_program_no)>0 && $show_booking==1 && ($rptType==2 || $rptType==3))
				{
					//TMP table
					//$program_con=where_con($program_no_arr,1,"dtls_id");
					$program_arr=return_library_array( "select a.dtls_id, a.booking_no from ppl_planning_entry_plan_dtls a, GBL_TEMP_ENGINE y
					where a.dtls_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=2 and a.status_active=1 ",'dtls_id','booking_no');
				}


				//die("with nadan");
				//echo $sql_qry;
				$source_cond_rcv="";
				if($cbo_knitting_source>0) $source_cond_rcv=" and p.knitting_source=$cbo_knitting_source";

				$source_cond_issue="";
				if($cbo_knitting_source>0) $source_cond_issue=" and p.knit_dye_source=$cbo_knitting_source";
				$store_cond="";
				if($cbo_store_name>0) $store_cond.=" and a.store_id=$cbo_store_name";

				if($rptType==1)
				{
					//TMP table
					$sql="select p.id as rece_issue_id, a.id as inv_id, p.recv_number as rcv_issue_no, p.challan_no, p.knitting_source, p.knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 1 as type, p.remarks
					from inv_receive_master p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and p.is_deleted=0 and b.status_active =1 and a.transaction_type in(1,4) and b.trans_type in(1,4)  $date_cond $source_cond_rcv $store_cond
					union all
					select p.id as rece_issue_id, a.id as inv_id, p.issue_number as rcv_issue_no, p.challan_no, p.knit_dye_source as knitting_source, p.knit_dye_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 2 as type, p.remarks
					from  inv_issue_master p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and p.is_deleted=0 and b.status_active =1 and a.transaction_type in(2,3) and b.trans_type in(2,3)   $date_cond $source_cond_issue $store_cond
					union all
					select p.id as rece_issue_id, a.id as inv_id, p.transfer_system_id as rcv_issue_no, p.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 3 as type, p.remarks
					from  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and p.is_deleted=0 and b.status_active =1 and a.transaction_type in(5,6) and b.trans_type in(5,6)   $date_cond $store_cond
					";
					//order by transaction_date, trans_id
					//echo $sql;die;
				}
				else if($rptType==2)
				{
					$is_sales_cond = ($fso_id==1)?" and b.is_sales=1":"";
					$sql="select p.id as rece_issue_id, p.recv_number as rcv_issue_no, p.challan_no, p.knitting_source, p.knitting_company,p.booking_id,p.booking_no,p.receive_basis, a.id as trans_id, a.transaction_type, a.mst_id,a.pi_wo_batch_no, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 1 as type,b.is_sales, p.remarks
					from inv_receive_master p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(1,4) and b.trans_type in(1,4) $date_cond  $source_cond_rcv $store_cond $is_sales_cond
					union all
					select p.id as rece_issue_id, p.transfer_system_id as rcv_issue_no, p.challan_no, 0 as knitting_source, 0 as knitting_company,null as booking_id,null as booking_no,null as receive_basis, a.id as trans_id, a.transaction_type, a.mst_id,b.dtls_id as pi_wo_batch_no, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 3 as type,b.is_sales, p.remarks
					from  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(5) and b.trans_type in(5) $date_cond  $store_cond $is_sales_cond order by transaction_date, trans_id";
				}
				else if($rptType==3)
				{
					$is_sales_cond = ($fso_id==1)?" and b.is_sales=1":"";
					$sql="select p.id as rece_issue_id, p.issue_number as rcv_issue_no, p.challan_no, p.knit_dye_source as knitting_source, p.knit_dye_company as knitting_company,p.booking_id,p.booking_no,p.issue_basis as receive_basis,p.issue_purpose, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 2 as type,b.is_sales, p.remarks
					from  inv_issue_master p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(2,3) and b.trans_type in(2,3) $date_cond   $source_cond_issue $store_cond $is_sales_cond
					union all
					select p.id as rece_issue_id, p.transfer_system_id as rcv_issue_no, p.challan_no, 0 as knitting_source, 0 as knitting_company,null as booking_id,null as booking_no,null as receive_basis,null as issue_purpose, a.id as trans_id, a.transaction_type, a.mst_id, b.po_breakdown_id as order_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, b.quantity, b.prod_id, a.inserted_by, $select_insert_date, b.entry_form, b.dtls_id, a.store_id, 3 as type,b.is_sales, p.remarks
					from  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, GBL_TEMP_ENGINE y
					where p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(6) and b.trans_type in(6) $date_cond  $store_cond $is_sales_cond order by transaction_date, trans_id";
				}
				//echo $sql;die;
				$issue_num_arr=array(); $receive_num_arr=array();$trans_id_arr=array(); $transfer_num_arr=array();
				$result=sql_select($sql);$mrr_no_arr=array();

				foreach($result as $row)
				{
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][1]=$row[csf("rcv_issue_no")];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][2]=$row[csf("challan_no")];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][3]=$row[csf("knitting_source")];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][4]=$row[csf("knitting_company")];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][5]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['yarn_lot'];//$row[csf("yarn_lot")]; // ?
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][6]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['y_count'];//$row[csf("yarn_count")];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][7]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['stitch_length'];//$row[csf("stitch_length")];//
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][8]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['color_id'];//$row[csf("color_id")]; // ?

					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][9]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_source'];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][10]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_company'];
					$issue_num_arr[$row[csf("dtls_id")]]['challan_no'][11]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['store_id'];

					$trans_id_arr[$row[csf("trans_id")]][2][2]=$row[csf("id")];

					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['rcv_issue_no']=$row[csf("rcv_issue_no")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['challan_no']=$row[csf("challan_no")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['knitting_source']=$row[csf("knitting_source")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['knitting_company']=$row[csf("knitting_company")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['yarn_lot']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['yarn_lot'];//$row[csf("yarn_lot")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['y_count']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['y_count'];//$row[csf("yarn_count")];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['stitch_length']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['stitch_length'];
					$receive_num_arr[$row[csf('dtls_id')]]['recv_number']['color_id']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['color_id'];//$row[csf("color_id")];

					//$receive_num_arr[$row[csf('dtls_id')]][1][9]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_source'];
					//$receive_num_arr[$row[csf('dtls_id')]][1][10]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_company'];
					//$receive_num_arr[$row[csf('dtls_id')]][1][11]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['store_id'];

					$trans_Id_arr[$row[csf("trans_id")]][1][1]=$row[csf("id")];

					$transfer_num_arr[$row[csf('dtls_id')]][6][1]=$row[csf("rcv_issue_no")];
					$transfer_num_arr[$row[csf('dtls_id')]][6][2]=$row[csf("challan_no")];
					$transfer_num_arr[$row[csf('dtls_id')]][6][3]=$row[csf("yarn_count")];
					$transfer_num_arr[$row[csf('dtls_id')]][6][5]=$row[csf("yarn_lot")];
					$transfer_num_arr[$row[csf('dtls_id')]][6][7]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['stitch_length'];
					$transfer_num_arr[$row[csf('dtls_id')]][6][8]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['color_id'];

					$transfer_num_arr[$row[csf('dtls_id')]][2][9]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_source'];
					$transfer_num_arr[$row[csf('dtls_id')]][2][10]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['knitting_company'];
					$transfer_num_arr[$row[csf('dtls_id')]][2][11]=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("rcv_issue_no")]]['store_id'];


					$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['yarn_lot']=$row[csf("yarn_lot")];
					$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['y_count']=$row[csf("yarn_count")];



					$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
					$mrr_no_arr[$row[csf('rcv_issue_no')]]=$row[csf('rcv_issue_no')];
					$product_ids_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
					//$r_id4=execute_query("insert into tmp_mrr_no (userid, mrr_no) values ($user_id,'".$row[csf('rcv_issue_no')]."')");
					//if($r_id4)
					//{
						//$r_id4=1;
					//}
					//else
					//{
						//echo "insert into tmp_mrr_no (userid, mrr_no) values ($user_id,'".$row[csf('rcv_issue_no')]."')";
						//oci_rollback($con);
						//die;
					//}

					//$knitting_data_arr[$row[csf("dtls_id")]]['knitting_source']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['knitting_source'];
					//$knitting_data_arr[$row[csf("dtls_id")]]['knitting_company']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['knitting_company'];
					//$knitting_data_arr[$row[csf("dtls_id")]]['store_id']=$roll_data_arr[$row[csf("dtls_id")]][$row[csf("recv_number")]]['store_id'];

				}

				if (count($product_ids_arr)>0)
				{
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 21, $product_ids_arr, $empty_arr);

					$product_arr=array();
					$prodDataArr=sql_select("SELECT A.ID, A.DETARMINATION_ID, A.GSM, A.DIA_WIDTH FROM PRODUCT_DETAILS_MASTER A , GBL_TEMP_ENGINE B  WHERE A.ID = B.REF_VAL AND A.ITEM_CATEGORY_ID=13 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=21");

					foreach($prodDataArr as $row)
					{
						$product_arr[$row['ID']]['detarmination_id']=$row['DETARMINATION_ID']; //1
						$product_arr[$row['ID']]['gsm']=$row['GSM'];//2
						$product_arr[$row['ID']]['dia_width']=$row['DIA_WIDTH'];//3
					}
					unset($prodDataArr);
				}

				if($r_id4)
				{
					oci_commit($con);
				}
				else
				{
					oci_rollback($con);
					disconnect($con);
				}

				if($rptType==3)
				{
					//TMP table
					$sql_get_pass_grey="select a.id,a.issue_id,a.challan_no,a.sys_number from inv_gate_pass_mst a  where a.company_id=$cbo_company_name and a.basis=3 and a.is_deleted=0 and a.status_active=1";

					$sql_get_pass_grey_data = sql_select($sql_get_pass_grey);
					$sql_get_pass_grey_arr=array();
					foreach($sql_get_pass_grey_data as $row)
					{
						$sql_get_pass_grey_arr[$row[csf("challan_no")]]["sys_number"]=$row[csf("sys_number")];
					}
					unset($sql_get_pass_grey);

				}

				if($rptType==1)
				{
					//TMP table
					/* $sql_grey_trns= "SELECT c.id,a.id as inv_id, c.transfer_system_id, c.transfer_criteria, c.challan_no,b.from_store, b.to_store,b.yarn_lot,b.y_count,d.dtls_id,b.brand_id,b.stitch_length,b.color_id,e.barcode_no
					from  inv_item_transfer_mst c, inv_item_transfer_dtls b, inv_transaction a, order_wise_pro_details d, pro_roll_details e, GBL_TEMP_ENGINE y
					where c.id=b.mst_id and c.id=a.mst_id and a.id = d.trans_id and b.id=e.dtls_id and d.po_breakdown_id=y.REF_VAL and e.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and c.entry_form=82 and b.item_category=13 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $date_cond"; */
					$sql_grey_trns= "SELECT c.id,a.id as inv_id, c.transfer_system_id, c.transfer_criteria, c.challan_no,b.from_store, b.to_store,b.yarn_lot,b.y_count,d.dtls_id,b.brand_id,b.stitch_length,b.color_id,e.barcode_no
					from  inv_item_transfer_mst c, inv_item_transfer_dtls b, inv_transaction a, order_wise_pro_details d, pro_roll_details e, GBL_TEMP_ENGINE y
					where c.id=b.mst_id and c.id=a.mst_id and a.id = d.trans_id and b.id=e.dtls_id and d.po_breakdown_id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=1 and c.entry_form in(82,110) and b.item_category=13 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $date_cond";
					//echo $sql_grey_trns;die;
					$sql_grey_trns_data = sql_select($sql_grey_trns);
					$sql_grey_trns_data_arr1=array();
					$sql_grey_trns_data_arr=array();
					foreach($sql_grey_trns_data as $row)
					{
						$sql_grey_trns_data_arr1[$row[csf("dtls_id")]][$row[csf("transfer_system_id")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]["from_store"]=$row[csf("from_store")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]["to_store"]=$row[csf("to_store")];
						//$sql_grey_trns_data_arr[$row[csf("transfer_system_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]["y_count"]=$row[csf("y_count")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]['yarn_lot']=$row[csf("yarn_lot")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]['color_id']=$row[csf("color_id")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]['stitch_length']=$row[csf("stitch_length")];
						$sql_grey_trns_data_arr[$row[csf("barcode_no")]][$row[csf("transfer_system_id")]]['brand_id']=$row[csf("brand_id")];
					}
					//var_dump($sql_grey_trns_data_arr);die;
					unset($sql_grey_trns_data);
				}

				//echo "</pre>";
				//print_r($receive_num_arr); die;

				if($rptType==1 || $rptType==2 || $rptType==3)
				{

					$sql_datas=sql_select($sql);
					$booking_ids_plan="";$booking_ids_sales=""; $all_booking_no="";$order_ids="";
					$plan_arr=$sales_ids_arr=array();
					foreach($sql_datas as $rows)
					{
						if($rows[csf("is_sales")]==1)
						{
							$sales_ids_arr[$rows[csf("order_id")]] = $rows[csf("order_id")];
						}
						//echo $order_ids;
					}
					if(count($sales_ids_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 3, $sales_ids_arr, $empty_arr);
					//echo $booking_id_cond_sales;die;
					if($db_type==0) $selected_year="year(a.insert_date) as job_year"; else $selected_year="to_char(a.insert_date,'YYYY') as job_year";
					 $sql_booking_query = "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no, a.booking_without_order, $selected_year
					 from fabric_sales_order_mst a, GBL_TEMP_ENGINE y
					 where a.id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=27 and y.REF_FROM=3 a.status_active=1 and a.is_deleted=0  $booking_id_cond_sales";//die;

					$sql_booking_data_sales=sql_select($sql_booking_query);
					foreach($sql_booking_data_sales as $row)
					{
						$booking_data_sales_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
						$booking_data_sales_arr[$row[csf("id")]]['sales_booking_no']=$row[csf("sales_booking_no")];
						if($row[csf("booking_without_order")]==0)
						{
							$booking_no_arr[$row[csf("sales_booking_no")]] = "'".$row[csf("sales_booking_no")]."'";
						}
						else
						{
							$booking_noOrder_arr[$row[csf("sales_booking_no")]] = "'".$row[csf("sales_booking_no")]."'";
						}

						$booking_data_sales_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
						$booking_data_sales_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
						$booking_data_sales_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
						$booking_data_sales_arr[$row[csf("id")]]['job_year']=$row[csf("job_year")];
					}
					// sales order query end
					if(count($booking_no_arr)>0 && count($booking_noOrder_arr)>0)
					{
						$all_booking_no=array_chunk($booking_no_arr,999);
						$all_booking_no_cond=" and";
						foreach($all_booking_no as $dtls_id)
						{
							if($all_booking_no_cond==" and")  $all_booking_no_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $all_booking_no_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
						}
						$all_booking_no_cond.=")";
						$all_booking_noOrder=array_chunk($booking_noOrder_arr,999);
						$all_booking_noOrder_cond=" and";
						foreach($all_booking_noOrder as $dtls_id)
						{
							if($all_booking_noOrder_cond==" and")  $all_booking_noOrder_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $all_booking_noOrder_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
						}
						$all_booking_noOrder_cond.=")";
						$sql_all_booking_sql="SELECT a.id,a.booking_type,a.entry_form,a.booking_no,b.po_break_down_id,0 as buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0  $all_booking_no_cond
						union all
						SELECT a.id, a.booking_type, 90 as entry_form,a.booking_no, 0 as po_break_down_id,a.buyer_id from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 $all_booking_noOrder_cond";
					}
					else
					{
						if(count($booking_no_arr)>0)
						{
							$all_booking_no=array_chunk($booking_no_arr,999);
							$all_booking_no_cond=" and";
							foreach($all_booking_no as $dtls_id)
							{
								if($all_booking_no_cond==" and")  $all_booking_no_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $all_booking_no_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
							}
							$all_booking_no_cond.=")";
							$sql_all_booking_sql="select a.id,a.booking_type,a.entry_form,a.booking_no,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0  $all_booking_no_cond";
						}
						else if(count($booking_noOrder_arr)>0)
						{
							$all_booking_noOrder=array_chunk($booking_noOrder_arr,999);
							$all_booking_noOrder_cond=" and";
							foreach($all_booking_noOrder as $dtls_id)
							{
								if($all_booking_noOrder_cond==" and")  $all_booking_noOrder_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $all_booking_noOrder_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
							}
							$all_booking_noOrder_cond.=")";
							$sql_all_booking_sql="select a.id, a.booking_type, 90 as entry_form, a.booking_no, 0 as po_break_down_id from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0  $all_booking_noOrder_cond";
						}
					}
					$poIds="";
					//echo $sql_all_booking_sql;die;
					$sql_all_booking_data=sql_select($sql_all_booking_sql);
					foreach($sql_all_booking_data as $row)
					{
						$poIds.=$row[csf("po_break_down_id")].',';
						$booking_all_data_arr[$row[csf("booking_no")]]['entry_form']=$row[csf("entry_form")];
						$booking_all_data_arr[$row[csf("booking_no")]]['booking_type']=$row[csf("booking_type")];
						$po_all_data_arr[$row[csf("booking_no")]]=$row[csf("po_break_down_id")];
					}
					//print_r($booking_all_data_arr);//die;
					//echo $poIds;

					$poIds=chop($poIds,',');
					$poIds=array_unique(explode(",",$poIds));
					$poIds=array_chunk($poIds,999);
					$poIds_cond=" and";
					foreach($poIds as $dtls_id)
					{
					if($poIds_cond==" and")  $poIds_cond.="(b.id in(".implode(',',$dtls_id).")"; else $poIds_cond.=" or b.id in(".implode(',',$dtls_id).")";
					}
					$poIds_cond.=")";

					/*$sql_po_query = "select a.job_no_prefix_num as job_no, $select_year(a.insert_date $year_con) as job_year, a.buyer_name as buyer_name, a.style_ref_no as style_ref_no, b.id as id, b.grouping as ref_no,b.file_no as file_no,b.po_number as po_number, b.shipment_date as shipment_date
					from wo_po_details_master a, wo_po_break_down b, GBL_TEMP_ENGINE y
					where a.id=b.job_id and b.id=y.REF_VAL and y.USER_ID=$user_id and y.ENTRY_FORM=187 and y.REF_FROM=1 $year_cond";//die;
					//echo $sql_po_query; die;
					$sql_all_po_data=sql_select($sql_po_query);
					$sales_order_data_arr=array(); $order_id_alls=""; $tot_rowss=0;
					foreach($sql_all_po_data as $row)
					{
						$tot_rowss++;
						$order_id_alls.=$row[csf("id")].",";
						$sales_order_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];//1
						$sales_order_data_arr[$row[csf("id")]]['job_year']=$row[csf("job_year")];//2
						$sales_order_data_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];//3
						$sales_order_data_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];//4
						$sales_order_data_arr[$row[csf("id")]]['shipment_date']=$row[csf("shipment_date")];//5
						$sales_order_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];//6
						$sales_order_data_arr[$row[csf("id")]]['ref_no']=$row[csf("ref_no")];//7
						$sales_order_data_arr[$row[csf("id")]]['file_no']=$row[csf("file_no")];//8
					}*/
					//echo "stringxxx"; die;
					//unset($sql_all_po_data);
				}
			}
			ob_start();
			?>

			<div style="width:<? echo $scroll_div_width; ?>" id="<? echo $scroll_div;?>">
				<table width="<? echo $table_width; ?>" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="31" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
					</tr>
					<tr style="border:none;">
							<td colspan="31" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
							</td>
					</tr>
			   	</table>
				<br />
				<?



				if($cbo_order_type==0 || $cbo_order_type==1) //All and with order
				{

					if($fso_id==1){$fso_width="500";}else{$fso_width="";}
					if($show_booking==1 && ($rptType==2 || $rptType==3) && $fso_id!=1){$booking_width="120";}else{$booking_width="";}
					$barcode_td=650;
					if($roll_maintained==1) $barcode_td=$barcode_td;else $barcode_td=0;
					//echo $roll_maintained.'DDDD';

					?>
					<table width="<? echo 3000+$fso_width+$barcode_td+$booking_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
						<thead>
							<tr>
								<th width="30" >SL</th>
								<th width="50" >Prod. Id</th>
	                            <th width="100">Store Name</th>
								<th width="60" >Year</th>
								<th width="50" >Job No</th>
	                            <th width="100" >Style No</th>
								<th width="70">Buyer</th>
								<? if($show_booking==1 && ($rptType==2 || $rptType==3) && $fso_id!=1)
								{
								?>

									<th width="120" >Booking No</th>

								<?
								}
								 if($fso_id==1)
								{
								?>
									<th width="150" >Booking Type</th>
									<th width="100" >Booking No</th>
									<th width="150" >FSO No</th>
								<?
								}
								else
								{
								?>
									<th width="120">Order No</th>
								<?
								}
								?>

	                            <th width="60">File No</th>
	                            <th width="70">Ref. No</th>
								<th width="80">Ship Date</th>
								<th width="80" >Trans. Date</th>
								<th width="130" >Trans. Ref.</th>
								<? if($rptType==1) {?>
								<th width="130" >Transfer Criteria</th>
								<? } ?>
								<? if($rptType==3) {?>
								<th width="130" >Gate Pass No</th>
								<? } ?>
								<th width="100">Challan No</th>
								<th width="120">Party Name</th>
								<? if($rptType==3 && $fso_id==1)
								{
								?>
									<th width="100" >Issue Purpose</th>
								<?
								}
								?>
								<th width="80" >Yarn Count</th>
								<th width="80" >Yarn Lot</th>
								<th width="100">Construction</th>
								<th width="110">Composition</th>
								<th width="80">Color</th>
								<th width="50">GSM</th>
								<th width="50">Dia</th>
								<th width="60">Stitch Length</th>
								<th width="80">Receive Qty</th>
								<th width="80">Receive Return Qty</th>
	                            <th width="80">Transfer In Qty</th>
								<th width="80">Issue Qty</th>
	                            <th width="80">Issue ReturnQty</th>
	                            <th width="80">Transfer Out Qty</th>
								<th width="110">User</th>
							<? if($roll_maintained==1)
								{
								?>
								<th width="100">Barcode</th>
								<th width="100">Roll No</th>
								<th width="100">Prog. No</th>
								<th width="100">Yarn Brand</th>
								<th width="100">ColorRange</th>
								<th width="50">MC.No</th>
								<th width="50">MC.Dia</th>
								<th width="50">MC.GG</th>
								<?
								}
								?>

								<th width="160">Insert Date</th>
								<th width="160">Remarks</th>
							</tr>
						</thead>
				   </table>

				  	<div style="width:<? echo 3020+$fso_width+$barcode_td+$booking_width; ?>px; overflow-y: scroll; max-height:250px;" id="scroll_body">
					<table width="<? echo 3000+$fso_width+$barcode_td+$booking_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<tbody>
						<?
						$i=1; $total_receive=""; $total_issue="";
						//echo $sql;die;
						$result=sql_select($sql);
						// execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (187)");
						// oci_commit($con);
						// disconnect($con);
						foreach($result as $row)
						{


							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$shipment_date=$sales_order_data_arr[$row[csf("order_id")]]['shipment_date'];

							$mst_id=$row[csf("mst_id")]; $dtls_id=$row[csf("dtls_id")];
							$trasRef=''; $challan_no=''; $knitting_source=''; $knitting_company=''; $yarn_lot=''; $yarn_count=''; $stitch_length=''; $color_id='';
							$trasRef=$row[csf("rcv_issue_no")];
							$challan_no=$row[csf("challan_no")];

							$barcode_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['barcode_no'];


							 if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==4)
							 {
								$knitting_source=$row[csf("knitting_source")];
								if($knitting_source==1)
								{
									$knitting_company=$company_arr[$row[csf("knitting_company")]];
								}
								else if($knitting_source==3)
								{
									$knitting_company=$supplier_arr[$row[csf("knitting_company")]];
								}
							}
							else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6 || $row[csf("transaction_type")]==2 )
							{

								$knitting_source=$transfer_num_arr[$dtls_id][2][9];
								$knitting_company_id=$transfer_num_arr[$dtls_id][2][10];
								//echo $knitting_source.'='.$knitting_company_id.',';
								if($knitting_source==1)
								{
									$knitting_company=$company_arr[$knitting_company_id];
								}
								else if($knitting_source==3)
								{
									$knitting_company=$supplier_arr[$knitting_company_id];
								}
							}

							if($row[csf("transaction_type")]==1)
							{
								$yarn_lot=$receive_num_arr[$dtls_id]['recv_number']['yarn_lot'];
								$yarn_count=$receive_num_arr[$dtls_id]['recv_number']['y_count'];
								$stitch_length=$receive_num_arr[$dtls_id]['recv_number']['stitch_length'];
								$color_id=$receive_num_arr[$dtls_id]['recv_number']['color_id'];
								$store_name=$store_library[$row[csf("store_id")]];
								//echo $yarn_lot."A";
								//$receive_num_arr[$row[csf('dtls_id')]][1][5]=$row[csf("yarn_lot")];
							}
							else if($row[csf("transaction_type")]==4)
							{
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
								$store_name=$store_library[$row[csf("store_id")]];
								//echo "BB";
							}
							else if($row[csf("transaction_type")]==2)
							{
								$yarn_lot=$issue_num_arr[$dtls_id]['challan_no'][5];
								$yarn_count=$issue_num_arr[$dtls_id]['challan_no'][6];
								$stitch_length=$issue_num_arr[$dtls_id]['challan_no'][7];
								$color_id=$issue_num_arr[$dtls_id]['challan_no'][8];
								//$store_name=$store_library[$row[csf("store_id")]];
								$store_name=$store_library[$issue_num_arr[$dtls_id]['challan_no'][11]];

								//echo $color_id.'=';
							}
							else if($row[csf("transaction_type")]==3)
							{
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
								$store_name=$store_library[$row[csf("store_id")]];
							}
							else if($row[csf("transaction_type")]==5)
							{
								$store_name = $store_library[$sql_grey_trns_data_arr[$barcode_no][$trasRef]["to_store"]];
								$yarn_lot   = $sql_grey_trns_data_arr[$barcode_no][$trasRef]["yarn_lot"];
								$yarn_count = $sql_grey_trns_data_arr[$barcode_no][$trasRef]["y_count"];
								$color_id = $sql_grey_trns_data_arr[$barcode_no][$trasRef]['color_id'];
								$stitch_length=$sql_grey_trns_data_arr[$barcode_no][$trasRef]['stitch_length'];
								$brand_id=$sql_grey_trns_data_arr[$barcode_no][$trasRef]['brand_id'];
								// $yarn_lot=$roll_data_arr[$dtls_id][$trasRef]['yarn_lot'];
								// $yarn_count=$roll_data_arr[$dtls_id][$trasRef]['y_count'];

							}
							else if($row[csf("transaction_type")]==6)
							{

								$store_name = $store_library[$sql_grey_trns_data_arr[$barcode_no][$trasRef]["from_store"]];
								$color_id = $sql_grey_trns_data_arr[$barcode_no][$trasRef]['color_id'];
								$yarn_lot=$roll_data_arr[$dtls_id][$trasRef]['yarn_lot'];
								$yarn_count=$roll_data_arr[$dtls_id][$trasRef]['y_count'];
								$stitch_length=$sql_grey_trns_data_arr[$barcode_no][$trasRef]['stitch_length'];
								$brand_id=$sql_grey_trns_data_arr[$barcode_no][$trasRef]['brand_id'];

							}
							else
							{
								//$yarn_lot=$transfer_num_arr[$dtls_id][6][5];
								//$yarn_count=$transfer_num_arr[$dtls_id][6][3];
								$stitch_length=$transfer_num_arr[$dtls_id][6][7];
								$yarn_lot=$roll_data_arr[$dtls_id][$trasRef]['yarn_lot'];
								$yarn_count=$roll_data_arr[$dtls_id][$trasRef]['y_count'];
								$color_id=$transfer_num_arr[$dtls_id][6][8];

								$store_name=$store_library[$transfer_num_arr[$dtls_id][2][11]];
							//	$transfer_num_arr[$row[csf('dtls_id')]][6][3]=$row[csf("yarn_lot")];
								//echo $color_id."BB";
							}

							$color='';
							$color_ids=explode(",",$color_id);
							foreach($color_ids as $val)
							{
								if($val>0) $color.=$color_arr[$val].",";
							}
							$color=chop($color,',');

							$yarn_count_name='';
							$yarn_counts=explode(",",$yarn_count);
							foreach($yarn_counts as $val)
							{
								if($val>0) $yarn_count_name.=$yarn_count_arr[$val].",";
							}
							$yarn_count_name=chop($yarn_count_name,',');

							//$barcode_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['barcode_no'];

							$roll_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['roll_no'];

							$machine_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['machine_no'];

							$machine_no_id=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['machine_no_id'];
							$color_range_id=$color_range[$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['color_range_id']];
							$brand_id=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['brand_id'];
							$prog_booking_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['booking_no'];
							$mc_gauge=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['mgg'];
							$dia_width=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['mdia'];

							$grey_feb_knit = $sql_get_pass_grey_arr[$trasRef]["sys_number"];
							 $grey_feb_knit_trans = $sql_grey_trns_data_arr1[$dtls_id][$trasRef]["transfer_criteria"];


							//echo $mc_gauge.'DDD';

							//$barcode_no=$roll_data_arr[$row[csf("pi_wo_batch_no")]][$trasRef]['barcode_no'];

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $row[csf("prod_id")]; ?>&nbsp;</p></td>
	                            <td width="100"><p>
								<?

									echo $store_name;//$store_library[$row[csf("store_id")]];


								 ?>&nbsp;</p></td>
								<td width="60" align="center"><p>
								<? //$booking_data_sales_arr[$row[csf("id")]]['job_year']
									if ($fso_id==1) {
										if($booking_data_sales_arr[$row[csf("order_id")]]['within_group']==2) {
											echo $booking_data_sales_arr[$row[csf("order_id")]]['job_year'];
										} else {
											echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['job_year'];
										}
									}else{
										echo $sales_order_data_arr[$row[csf("order_id")]]['job_year'];
										} ?>&nbsp; </p>
								</td>
								<td width="50" align="center"><p>
									<?
									if ($fso_id==1) { echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['job_no'];}
									else{ echo $sales_order_data_arr[$row[csf("order_id")]]['job_no'];}

									?>
								&nbsp;</p></td>
	                            <td width="100" title="<? echo $booking_data_sales_arr[$row[csf("order_id")]]['within_group']; ?>"><p>
								<? if ($fso_id==1) {
										if($booking_data_sales_arr[$row[csf("order_id")]]['within_group']==2) {
											echo $booking_data_sales_arr[$row[csf("order_id")]]['style_ref_no'];
										} else {
											echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['style_ref_no'];
										}
									}else{
										echo $sales_order_data_arr[$row[csf("order_id")]]['style_ref_no'];
										} ?>&nbsp;
									</p>
								</td>
								<td width="70"><p><?
									if ($fso_id==1)
									{
										if($booking_data_sales_arr[$row[csf("order_id")]]['within_group']==2)
										{
											echo $buyer_short_arr[$booking_data_sales_arr[$row[csf("order_id")]]['buyer_id']];
										}
										else
										{
											// echo "string";
											$booking_type = $booking_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]['booking_type'];
											if($booking_type==4){
												echo $buyer_short_arr[$booking_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]['buyer_id']];
											}else{
												echo $buyer_short_arr[$sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['buyer_name']];
											}

										}
									}
									else
									{
										echo $buyer_short_arr[$sales_order_data_arr[$row[csf("order_id")]]['buyer_name']];
									}
										?>&nbsp;</p></td>

								<? if($show_booking==1 && ($rptType==2 || $rptType==3) && $fso_id!=1)
								{
								?>

									<td width="120" ><?php echo $program_arr[$prog_booking_no]; ?></td>

								<?
								}
								 if($fso_id==1)
								{
									 $booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
								?>
									<td width="150" title="<? echo $booking_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]['entry_form']; ?>"><p>
										<?
										if ($row[csf("receive_basis")]==1) {
											echo $booking_type_arr[$booking_all_data_arr[$row[csf("booking_no")]]['entry_form']];
											echo "++".print_r($booking_type_arr);
										}
										else if ($row[csf("receive_basis")]==2) {
											echo $booking_all_data_arr[$booking_data_plan_arr[$row[csf("booking_id")]]['booking_no']]['booking_type'];
	                                        echo "--".print_r($booking_type_arr);
										}
										else if ($row[csf("receive_basis")]==4) {
											echo $booking_type_arr[$booking_all_data_arr[$booking_data_sales_arr[$row[csf("booking_id")]]['booking_no']]['entry_form']];
	                                        echo "**".print_r($booking_type_arr);
										}
										if ($row[csf("is_sales")]==1) {
											echo $booking_type_arr[$booking_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]['entry_form']];
										}
										?>

									&nbsp;</p></td>
									<td width="100"><p>
										<?
										if ($row[csf("is_sales")]==1) {
											echo $booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no'];
										}
										?>
									&nbsp;</p></td>
									<td width="150">
										<?
										echo $booking_data_sales_arr[$row[csf("order_id")]]['job_no'];
										?>
										</td>
								<?
								}
								else
								{


									?>
									<td width="120"><p>
									<?
									if ($fso_id==1) {
										echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['po_number'];
									}else{
										echo $sales_order_data_arr[$row[csf("order_id")]]['po_number'];
									} ?>&nbsp;</p></td>

									<?
								}
								?>
	                            <td width="60"><p>
								<?
								if ($fso_id==1) {
									echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['file_no'];
								}else{
									echo $sales_order_data_arr[$row[csf("order_id")]]['file_no'];
								}
								?>&nbsp;</p></td>
	                            <td width="70"><p>
								<?
								if ($fso_id==1) {
									echo $sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['ref_no'];
								}else{
									echo $sales_order_data_arr[$row[csf("order_id")]]['ref_no'];
									}
								?>&nbsp;</p>
								</td>
								<td width="80" align="center"><p>
								<?
								if ($fso_id==1) {
									echo change_date_format($sales_order_data_arr[$po_all_data_arr[$booking_data_sales_arr[$row[csf("order_id")]]['sales_booking_no']]]['shipment_date']);
								}else{
									if($shipment_date!="0000-00-00" && $shipment_date!="") echo change_date_format($shipment_date); else echo "&nbsp;";
								} ?></p>
								</td>
								<td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
								<td width="130" title="Trans Type=<? echo $row[csf("transaction_type")];?>"><p><? echo $trasRef; ?></p></td>
								<? if($rptType==1)
								{
								?>
									<td width="130" title="<? echo "grey_feb_knit_trans=".$grey_feb_knit_trans;?>"><p><? echo $item_transfer_criteria[$grey_feb_knit_trans]; ?></p></td>
								<?
								}
								?>
								<? if($rptType==3)
								{
								?>
									<td width="130" ><p><? echo $grey_feb_knit; ?></p></td>
								<?
								}
								?>

								<td width="100"><p><? echo $challan_no; ?></p></td>
								<td width="120"><p><? echo $knitting_company; ?>&nbsp;</p></td>
								<? if($rptType==3 && $fso_id==1)
								{
								?>
									<td width="100"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?>&nbsp;</p></td>
								<?
								}
								?>
								<td style="word-wrap:break-word; word-break:break-word; width:80px;"><p><? echo $yarn_count_name; ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break:break-word; width:80px;"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break:break-word; width:100px;"><p><? echo $construction_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break:break-word; width:110px;"><p><? echo $composition_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['gsm']; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['dia_width']; ?>&nbsp;</p></td>
								<td  style="word-wrap: break-word; word-break:break-word; width:60px;"><p><? echo $stitch_length; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1){ echo number_format($row[csf("quantity")],2); $total_receive +=$row[csf("quantity")];} ?></p></td>
	                            <td width="80" align="right"><? if($row[csf("transaction_type")]==3){ echo number_format($row[csf("quantity")],2); $total_ret_receive +=$row[csf("quantity")];} ?></td>
	                            <td width="80" align="right"><? if($row[csf("transaction_type")]==5){ echo number_format($row[csf("quantity")],2); $total_trans_in +=$row[csf("quantity")];} ?></td>

								<td align="right" width="80"><p><? if($row[csf("transaction_type")]==2){ echo number_format($row[csf("quantity")],2); $total_issue +=$row[csf("quantity")];} ?></p></td>

	                            <td width="80" align="right"><? if($row[csf("transaction_type")]==4){ echo number_format($row[csf("quantity")],2); $total_ret_receive +=$row[csf("quantity")];} ?></td>
	                            <td width="80" align="right"><? if($row[csf("transaction_type")]==6){ echo number_format($row[csf("quantity")],2); $total_trans_out +=$row[csf("quantity")];} ?></td>
								<td width="110"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
								<? if($roll_maintained==1)
								{
								?>
								<td width="100" title="Dtls Id=<? echo $row[csf("dtls_id")];?>"><p><? echo $barcode_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $roll_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $prog_booking_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $brand_id; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $color_range_id; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $machine_no; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $dia_width; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $mc_gauge; ?>&nbsp;</p></td>
								<?
								}
								?>

								<td width="160"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
								<td width="160"><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						unset($result);
						?>
						</tbody>
					</table>

					<table width="<? echo 3000+$fso_width+$barcode_td+$booking_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<? if($show_booking==1 && ($rptType==2 || $rptType==3) && $fso_id!=1)
							{
							?>

								<th width="120" ></th>

							<?
							}
							if($fso_id==1)
							{
							?>
								<th width="150">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="150">&nbsp;</th>
							<?
							}
							else
							{
								?>
								<th width="120">&nbsp;</th>
								<?
							}
							?>

	                        <th width="60">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="130">&nbsp;</th>
							<? if($rptType==1)
							{
							?>
								<th width="130">&nbsp;</th>
							<?
							}
							?>
							<? if($rptType==3)
							{
							?>
								<th width="130">&nbsp;</th>
							<?
							}
							?>
							<th width="100">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<? if($rptType==3 && $fso_id==1)
							{
							?>
								<th width="100">&nbsp;</th>
							<?
							}
							?>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="60">Total:</th>
							<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
	                        <th width="80" id="value_total_ret_receive"><? echo number_format($total_ret_receive,2); ?></th>
	                        <th width="80" id="value_total_trans_in"><? echo number_format($total_trans_in,2); ?></th>
							<th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
	                        <th width="80" id="value_total_ret_issue"><? echo number_format($total_ret_issue,2); ?></th>
	                        <th width="80" id="value_total_trans_out"><? echo number_format($total_trans_out,2); ?></th>
							<th width="110">&nbsp;</th>
							<? if($roll_maintained==1)
							{
							?>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<?
							}
							?>
							<th width="160">&nbsp;</th>
							<th width="160">&nbsp;</th>
						</tfoot>
					</table>
				 </div>
					<?
				}



				if($txt_style_ref=="" && $txt_order=="" && $txt_search_val=="")
				{
					$decisionForOrderNonOrder=1;
				}
				// && ($decisionForOrderNonOrder==1)
				//if($cbo_order_type==0 || $cbo_order_type==2) //Non Order Item
				if(($cbo_order_type==0 || $cbo_order_type==2) && $decisionForOrderNonOrder==1 )//Non Order Item
				{
					//die;
					$source_cond_rcv="";
					if($cbo_knitting_source>0) $source_cond_rcv=" and b.knitting_source=$cbo_knitting_source";

					$source_cond_issue="";
					if($cbo_knitting_source>0) $source_cond_issue=" and b.knit_dye_source=$cbo_knitting_source";

					$store_cond="";
					if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";

					if($rptType==1) // All Button
					{
						$sql="SELECT b.recv_number as tran_ref, b.challan_no, b.knitting_source as knitting_source, b.knitting_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date , a.store_id,null as from_samp_dtls_id,null as to_samp_dtls_id
						from inv_receive_master b, inv_transaction a
						where b.id=a.mst_id and a.item_category=13 and a.company_id=$cbo_company_name and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=1 and a.transaction_type in(1,4) and b.entry_form in (2,22,51,58) $date_cond $source_cond_rcv $store_cond
						union all
						select b.issue_number as tran_ref, b.challan_no, b.knit_dye_source as knitting_source, b.knit_dye_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date , a.store_id,null as from_samp_dtls_id,null as to_samp_dtls_id
						from inv_issue_master b, inv_transaction a where b.id=a.mst_id and a.item_category=13 and a.company_id=$cbo_company_name and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.issue_purpose=8 and a.transaction_type in(2,3) and b.entry_form in (16,45,61) $date_cond $source_cond_issue  $store_cond
						union all
						select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id,b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
						from  inv_item_transfer_mst b, inv_transaction a, wo_non_ord_samp_booking_dtls c
						where b.id=a.mst_id and b.from_order_id=a.order_id and b.from_samp_dtls_id = c.id and b.from_samp_dtls_id is not null and b.transfer_criteria=7 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(6) and b.entry_form in (183) $date_cond $store_cond
						union all
						select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id, b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
						from  inv_item_transfer_mst b, inv_transaction a
						where b.id=a.mst_id and b.from_samp_dtls_id is not null and b.to_samp_dtls_id is not null and b.transfer_criteria=8 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5,6) and b.entry_form in (180) $date_cond $store_cond
						union all
						select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id, b.from_samp_dtls_id as from_samp_dtls_id,b.to_samp_dtls_id as to_samp_dtls_id
						from  inv_item_transfer_mst b, inv_transaction a
						where b.id=a.mst_id and b.to_order_id=a.order_id and b.from_samp_dtls_id is null and b.to_samp_dtls_id is not null and b.transfer_criteria=6 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5) and b.entry_form in (110) $date_cond $store_cond
						order by transaction_date, trans_id";

						//echo "Execution Time: " . (microtime(true) - $started) . "Sx";
						//echo $sql; //die;
					}
					else if($rptType==2) // Receive Button
					{
						// echo $roll_maintained;die;
						if($roll_maintained==1)
						{
							$sql="SELECT DISTINCT b.recv_number as tran_ref, b.challan_no, b.knitting_source as knitting_source, b.knitting_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date , a.store_id,null as from_samp_dtls_id,null as to_samp_dtls_id
							from inv_receive_master b, inv_transaction a, pro_roll_details c where b.id=a.mst_id and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and a.company_id=$cbo_company_name and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and c.booking_without_order=1 and a.transaction_type in(1,4) and b.entry_form in (2,22,51,58) $date_cond $source_cond_rcv $store_cond
							union all
							select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id,b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
							FROM inv_item_transfer_mst b, inv_transaction a,inv_item_transfer_dtls c, pro_roll_details d
							WHERE b.id = a.mst_id and b.id=c.mst_id and c.to_trans_id=a.id and b.id=d.mst_id
							and c.id=d.dtls_id and b.from_samp_dtls_id is not null and b.to_samp_dtls_id is not null and b.transfer_criteria=8 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5) and b.entry_form in (180) $date_cond $store_cond
							union all
							select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id, b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
							FROM inv_item_transfer_mst b, inv_transaction a,inv_item_transfer_dtls c, pro_roll_details d
							WHERE b.id = a.mst_id and b.id=c.mst_id and c.to_trans_id=a.id and b.id=d.mst_id
							and c.id=d.dtls_id and b.to_order_id=a.order_id and b.from_samp_dtls_id is null and b.to_samp_dtls_id is not null and b.transfer_criteria=6 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5) and b.entry_form in (110) $date_cond $store_cond

						  	order by transaction_date, trans_id";
						}
						else
						{
							$sql="SELECT b.recv_number as tran_ref, b.challan_no, b.knitting_source as knitting_source, b.knitting_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date , a.store_id,null as from_samp_dtls_id, null as to_samp_dtls_id
							from inv_receive_master b, inv_transaction a where b.id=a.mst_id and a.item_category=13 and a.company_id=$cbo_company_name and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.booking_without_order=1 and a.transaction_type in(1,4) and b.entry_form in (2,22,51,58) $date_cond $source_cond_rcv $store_cond
							union all
							select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id, b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
							FROM inv_item_transfer_mst b, inv_transaction a
							WHERE b.id = a.mst_id and b.from_samp_dtls_id is not null and b.to_samp_dtls_id is not null and b.transfer_criteria=8 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5) and b.entry_form in (180) $date_cond $store_cond
							union all
							select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id,b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
							FROM inv_item_transfer_mst b, inv_transaction a
							WHERE b.id = a.mst_id and b.to_order_id=a.order_id and b.from_samp_dtls_id is null and b.to_samp_dtls_id is not null and b.transfer_criteria=6 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(5) and b.entry_form in (110) $date_cond $store_cond
							order by transaction_date, trans_id";
						}
					}
					else if($rptType==3) // Issue Button
					{
						$sql="SELECT b.issue_number as tran_ref, b.challan_no, b.knit_dye_source as knitting_source, b.knit_dye_company as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date , a.store_id,null as from_samp_dtls_id, null as to_samp_dtls_id
						from inv_issue_master b, inv_transaction a
						where b.id=a.mst_id and a.item_category=13 and a.company_id=$cbo_company_name and b.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.issue_purpose=8 and a.transaction_type in(2,3) and b.entry_form in (16,45,61) $date_cond $source_cond_issue $store_cond
						union all
						select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id,b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
						FROM inv_item_transfer_mst b, inv_transaction a, wo_non_ord_samp_booking_dtls c
						WHERE b.id = a.mst_id and b.from_order_id=a.order_id and b.from_samp_dtls_id = c.id and b.from_samp_dtls_id is not null and b.transfer_criteria=7 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(6) and b.entry_form in (183) $date_cond $store_cond
						union all
						select b.transfer_system_id as tran_ref, b.challan_no, 0 as knitting_source, 0 as knitting_company, a.id as trans_id, a.transaction_type, a.mst_id, a.transaction_date, a.batch_lot, a.yarn_count, a.stitch_length, a.prod_id, a.cons_quantity as qty, a.inserted_by, $select_insert_date, a.store_id,b.from_samp_dtls_id as from_samp_dtls_id, b.to_samp_dtls_id as to_samp_dtls_id
						from  inv_item_transfer_mst b, inv_transaction a
						where b.id=a.mst_id and b.from_samp_dtls_id is not null and b.to_samp_dtls_id is not null and b.transfer_criteria=8 and a.item_category=13 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active =1 and a.transaction_type in(6) and b.entry_form in (180) $date_cond $store_cond
						order by transaction_date, trans_id";
					}
					//echo $sql;//die;
					$non_order_data = sql_select($sql);
					$mst_id_arr=array();
					$booking_dtls_id_arr=array();
					foreach( $non_order_data as $row)
					{
						/*if(!strpos($row[csf('mst_id')], "-"))
						{
							array_push($mst_id_arr, $row[csf('mst_id')]);
						}*/
						$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
						$product_ids_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];

						$booking_dtls_id_arr[$row[csf('from_samp_dtls_id')]]=$row[csf('from_samp_dtls_id')];
						$booking_dtls_id_arr[$row[csf('to_samp_dtls_id')]]=$row[csf('to_samp_dtls_id')];
					}
					//echo "<pre>";print_r($booking_dtls_id_arr); 

					$booking_dtls_id_arr = array_filter($booking_dtls_id_arr);
					if(!empty($booking_dtls_id_arr))
					{	
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 34,$booking_dtls_id_arr, $empty_arr); //recv id
						//die;
						$smnBookingSql="SELECT A.ID, A.BOOKING_NO FROM WO_NON_ORD_SAMP_BOOKING_DTLS A, GBL_TEMP_ENGINE B  WHERE A.ID = B.REF_VAL AND B.USER_ID = $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=34 AND A.IS_DELETED=0 AND A.IS_DELETED=0 GROUP BY A.ID, A.BOOKING_NO";
						//echo $smnBookingSql;die;
						$smnBookingArr=sql_select($smnBookingSql);
						$smn_booking_arr = array();
						foreach($smnBookingArr as $row)
						{
							$smn_booking_arr[$row['ID']]['BOOKING_NO']=$row['BOOKING_NO']; 
						}
						unset($smnBookingArr);
					}

					if (count($product_ids_arr)>0)
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 22, $product_ids_arr, $empty_arr);

						$product_arr=array();
						$prodDataArr=sql_select("SELECT A.ID, A.DETARMINATION_ID, A.GSM, A.DIA_WIDTH FROM PRODUCT_DETAILS_MASTER A , GBL_TEMP_ENGINE B  WHERE A.ID = B.REF_VAL AND  A.ITEM_CATEGORY_ID=13 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=22");
						foreach($prodDataArr as $row)
						{
							$product_arr[$row['ID']]['detarmination_id']=$row['DETARMINATION_ID']; //1
							$product_arr[$row['ID']]['gsm']=$row['GSM'];//2
							$product_arr[$row['ID']]['dia_width']=$row['DIA_WIDTH'];//3
						}
						unset($prodDataArr);
					}

					if (count($mst_id_arr>0))
					{
						$chunk_list_arr=array_chunk($mst_id_arr,999);
						$p=1;$table_coloum=" a.id";
						foreach($chunk_list_arr as $process_arr)
						{
							if($dataType==0){
								if($p==1){$mst_sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
								else {$mst_sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
							}
							else{
								if($p==1){$mst_sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
								else {$mst_sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
							}
							$p++;
						}
					}
					$mst_sql.=") ";
					// echo $mst_sql;die;

					$program_no_arr=array();
					if($roll_maintained==1 && $mst_sql!="") // Non Order
					{
						if($rptType==1 || $rptType==2 || $rptType==3)
						{
							$sql_rolldata="SELECT a.id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id,b.color_id,b.yarn_lot,b.yarn_count, b.stitch_length,c.roll_no,b.width,b.body_part_id,b.brand_id,b.machine_no_id,b.color_id, c.barcode_no, c.booking_no, c.id as roll_id, c.booking_without_order,c.is_sales
							from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
							where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58,84) and c.entry_form in(58,84) and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $mst_sql $date_cond_rolldata";//die("with despacito");
							// echo $sql_rolldata;//die;
						 	$data_array_mst = sql_select($sql_rolldata);

							foreach( $data_array_mst as $row)
							{
								$roll_data_arr[$row[csf("trans_id")]][$row[csf("recv_number")]]['booking_no']=$row[csf("booking_no")];
								if(!strpos($row[csf('booking_no')], "-"))
								{
									array_push($program_no_arr, $row[csf('booking_no')]);
								}
							}
						} //Recv End
						if($rptType==1 || $rptType==3)
						{
							$sql_issue_query = "SELECT a.id, a.entry_form,a.issue_number, a.booking_id,b.id as dtls_id,b.prod_id,b.color_id,b.stitch_length,b.machine_id as machine_no_id, b.brand_id,b.yarn_lot,b.yarn_count,c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no, b.trans_id
							from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c
							where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(61) and c.entry_form in(61) and c.status_active=1 and c.is_deleted=0   $mst_sql $date_cond_issue";//and c.is_returned!=1
							$data_isssue_array=sql_select($sql_issue_query);

							foreach( $data_isssue_array as $row)
							{
								$roll_data_arr[$row[csf("trans_id")]][$row[csf("issue_number")]]['booking_no']=$row[csf("booking_no")];
								if(!strpos($row[csf('booking_no')], "-"))
								{
									array_push($program_no_arr, $row[csf('booking_no')]);
								}
							}
						} //Issue End
					}
					// echo "<pre>";print_r($roll_data_arr);die;

					$program_arr=array();
					if(count($program_no_arr))
					{
						$program_con=where_con($program_no_arr,1,"dtls_id");
						$program_arr=return_library_array( "select dtls_id, booking_no from  ppl_planning_entry_plan_dtls where status_active=1 $program_con",'dtls_id','booking_no');
					}
					?>
	             	<br />
	             	<table width="1910" id="table_header_3" align="left">
	                <tr>
	                    <td><span style="font-size:16px; font-weight:bold;">Non Order Item</span></td>
	                </tr>
	             	</table>
	             	<table width="1910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left">
	                    <thead>
	                        <tr>
	                            <th width="30">SL</th>
	                            <th width="50">Prod. Id</th>
	                            <th width="100">Store Name</th>
	                            <th width="80">Trans. Date</th>
	                            <th width="130">Trans. Ref.</th>
	                            <th width="100">Challan No</th>
	                            <th width="100">Booking No</th>
	                            <th width="120">Party Name</th>
	                            <th width="80">Yarn Lot</th>
	                            <th width="80">Yarn Count</th>
	                            <th width="100">Construction</th>
	                            <th width="110">Composition</th>
	                            <th width="80">Color</th>
	                            <th width="50">GSM</th>
	                            <th width="50">Dia</th>
	                            <th width="60">Stitch Lenth</th>
	                            <th width="80">Receive Qty</th>
	                            <th width="80">Issue Qty</th>
	                            <th width="80">Transfer In Qty</th>
	                            <th width="80">Transfer Out Qty</th>
	                            <th width="110">User</th>
	                            <th>Insert Date</th>
	                        </tr>
	                    </thead>
	               	</table>
	                <table width="1910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
	                    <tbody>
	                    <?
	                    $j=1; 
						$total_receive_non_order=$total_issue_non_order=$total_trans_in_non_order=$total_trans_in_non_order=0;
						$smn_in_booking_no=$smn_out_booking_no='';
	                    // echo $sql;die;
	                    $resultx=sql_select($sql);
	                    foreach($resultx as $row)
	                    {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	                       $knitting_company=''; $yarn_lot=''; $yarn_count=''; $stitch_length=''; $color_id='';
	                        if($row[csf("knitting_source")]==1)
	                        {
	                            $knitting_company=$company_arr[$row[csf("knitting_company")]];
	                        }
	                        else if($row[csf("knitting_source")]==3)
	                        {
	                            $knitting_company=$supplier_arr[$row[csf("knitting_company")]];
	                        }

							if($row[csf("transaction_type")]==1)
							{
								$dtls_id=$trans_Id_arr[$row[csf("trans_id")]][1][1];
								$yarn_lot=$receive_num_arr[$dtls_id]['recv_number']['yarn_lot'];
								$yarn_count=$receive_num_arr[$dtls_id]['recv_number']['y_count'];
								$stitch_length=$receive_num_arr[$dtls_id]['recv_number']['stitch_length'];
								$color_id=$receive_num_arr[$dtls_id]['recv_number']['color_id'];
							}
							else if($row[csf("transaction_type")]==4)
							{
								$dtls_id=$trans_Id_arr[$row[csf("trans_id")]][1][1];
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
							}
							else if($row[csf("transaction_type")]==2)
							{
								$dtls_id=$trans_Id_arr[$row[csf("trans_id")]][2][2];
								$yarn_lot=$issue_num_arr[$dtls_id]['challan_no'][5];
								$yarn_count=$issue_num_arr[$dtls_id]['challan_no'][6];
								$stitch_length=$issue_num_arr[$dtls_id]['challan_no'][7];
								$color_id=$issue_num_arr[$dtls_id]['challan_no'][8];
							}
							else if($row[csf("transaction_type")]==3)
							{
								$dtls_id=$trans_Id_arr[$row[csf("trans_id")]][2][2];
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
							}
							else if($row[csf("transaction_type")]==5)
							{
								$smn_in_booking_no = $smn_booking_arr[$row[csf('to_samp_dtls_id')]]['BOOKING_NO'];
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
							}
							else if($row[csf("transaction_type")]==6)
							{
								$smn_out_booking_no = $smn_booking_arr[$row[csf('from_samp_dtls_id')]]['BOOKING_NO'];
								$yarn_lot=$row[csf("batch_lot")];
								$yarn_count=$row[csf("yarn_count")];
								$stitch_length=$row[csf("stitch_length")];
							}

							$color='';
							$color_ids=explode(",",$color_id);
							foreach($color_ids as $val)
							{
								if($val>0) $color.=$color_arr[$val].",";
							}
							$color=chop($color,',');

							$yarn_count_name='';
							$yarn_counts=explode(",",$yarn_count);
							foreach($yarn_counts as $val)
							{
								if($val>0) $yarn_count_name.=$yarn_count_arr[$val].",";
							}
							$yarn_count_name=chop($yarn_count_name,',');

							//$prog_booking_no=$roll_data_arr[$row[csf("dtls_id")]][$trasRef]['booking_no'];
							$prog_booking_no="";$roll_booking_no="";
							$trasRef=$row[csf("tran_ref")];
							if ($roll_data_arr[$row[csf("trans_id")]][$trasRef]['booking_no']!="")
							{
								$prog_booking_no=$program_arr[$roll_data_arr[$row[csf("trans_id")]][$trasRef]['booking_no']];
								$roll_booking_no=$roll_data_arr[$row[csf("trans_id")]][$trasRef]['booking_no'];
							}


	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

	                            <td width="30"><p><? echo $j; ?>&nbsp;</p></td>
	                            <td width="50"><p><? echo $row[csf("prod_id")]; ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $store_library[$row[csf("store_id")]]; ?>&nbsp;</p></td>
	                            <td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo "&nbsp;"; ?></p></td>
	                            <td width="130"><p><? echo $row[csf("tran_ref")]; ?></p></td>
	                            <td width="100"><p><? echo $row[csf("challan_no")]; ?></p></td>
	                            <td width="100" title="<? echo $row[csf("trans_id")].'='.$trasRef; ?>"><p>
									<?
									if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
									{
										if($row[csf("transaction_type")]==5)
										{
											echo $smn_in_booking_no;
										}
										else
										{
											echo $smn_out_booking_no;
										}

									}
									else
									{
										if ($prog_booking_no!="")
										{
											echo $prog_booking_no;
										}
										else
										{
											echo $roll_booking_no;
										}
									}
									
									?>
									</p></td>
	                            <td width="120"><p><? echo $knitting_company; ?>&nbsp;</p></td>
	                            <td width="80"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $yarn_count_name; ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $construction_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?>&nbsp;</p></td>
	                            <td width="110"><p><? echo $composition_arr[$product_arr[$row[csf('prod_id')]]['detarmination_id']]; ?>&nbsp;</p></td>
	                            <td width="80"><p><? echo $color; ?>&nbsp;</p></td>
	                            <td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['gsm']; ?>&nbsp;</p></td>
	                            <td width="50"><p><? echo $product_arr[$row[csf('prod_id')]]['dia_width']; ?>&nbsp;</p></td>
	                            <td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>

	                            <td width="80" align="right"><p>
									<? if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
									{ echo number_format($row[csf("qty")],2); 
									$total_receive_non_order +=$row[csf("qty")];}
									else
									{
										echo "0.00";
										$total_receive_non_order +=0;
									} 
									?>
								</p></td>
								<td width="80" align="right"><p>
									<? if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
									{ echo number_format($row[csf("qty")],2); 
									$total_receive_non_order +=$row[csf("qty")];}
									else
									{
										echo "0.00";
										$total_receive_non_order +=0;
									} 
									?>
								</p></td>
								<td width="80" align="right"><p>
									<? if($row[csf("transaction_type")]==5 )
									{ echo number_format($row[csf("qty")],2); 
									$total_trans_in_non_order +=$row[csf("qty")];}
									else
									{
										echo "0.00";
										$total_trans_in_non_order +=0;
									} 
									?>
								</p></td>
								<td align="right" width="80"><p>
									<? if($row[csf("transaction_type")]==6)
									{ echo number_format($row[csf("qty")],2); 
									  $total_trans_out_non_order +=$row[csf("qty")];
									} 
									else
									{
										echo "0.00";
										$total_trans_out_non_order +=0;
									}
									?>
								</p></td>
	                            <td width="110"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
	                            <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
	                        </tr>
	                        <?
	                        $i++;$j++;
	                    }
						unset($result);
	                    ?>
	                    </tbody>
	                </table>
	                <table width="1910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
	                    <tfoot>
	                        <th width="30">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="130">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="120">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="110">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="60">Total:</th>
	                        <th width="80" ><? echo number_format($total_receive_non_order,2); ?></th>
	                        <th width="80" ><? echo number_format($total_issue_non_order,2); ?></th>
	                        <th width="80" ><? echo number_format($total_trans_in_non_order,2); ?></th>
	                        <th width="80" ><? echo number_format($total_trans_out_non_order,2); ?></th>
	                        <th width="110">&nbsp;</th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	                <?
				}
				?>
			</div>
			<?
		}

	}
	else if($cbo_item_cat==8 || $cbo_item_cat==9 || $cbo_item_cat==10 || $cbo_item_cat==11 || $cbo_item_cat==15 ||
            $cbo_item_cat==16 || $cbo_item_cat==17 || $cbo_item_cat==18 || $cbo_item_cat==19 || $cbo_item_cat==20 ||
            $cbo_item_cat==21 || $cbo_item_cat==22 || $cbo_item_cat==32 || $cbo_item_cat==34 || $cbo_item_cat==35 ||
            $cbo_item_cat==36 || $cbo_item_cat==37 || $cbo_item_cat==38 || $cbo_item_cat==39    || $cbo_item_cat==23 ||
            $cbo_item_cat==33 || $cbo_item_cat==44 || $cbo_item_cat==45 || $cbo_item_cat==46 || $cbo_item_cat==47 ||
            $cbo_item_cat==48 || $cbo_item_cat==49 || $cbo_item_cat==50 || $cbo_item_cat==51 || $cbo_item_cat==52 ||
            $cbo_item_cat==53 || $cbo_item_cat==54 || $cbo_item_cat==55 || $cbo_item_cat==56 || $cbo_item_cat==57 ||
            $cbo_item_cat==58 || $cbo_item_cat==59 || $cbo_item_cat==60 || $cbo_item_cat==61 || $cbo_item_cat==62 ||
            $cbo_item_cat==63 || $cbo_item_cat==64 || $cbo_item_cat==65 || $cbo_item_cat==66 || $cbo_item_cat==67 || $cbo_item_cat==68 ||
            $cbo_item_cat==69 || $cbo_item_cat==70 || $cbo_item_cat==89 || $cbo_item_cat==90 || $cbo_item_cat==91 ||
            $cbo_item_cat==92 || $cbo_item_cat==93 || $cbo_item_cat==94 || $cbo_item_cat==40 || $cbo_item_cat==41 || $cbo_item_cat==99 || $cbo_item_cat==99 || $cbo_item_cat==101 || $cbo_item_cat==106 || $cbo_item_cat==107)
	  {

		$receive_sql=sql_select("SELECT a.id, a.recv_number, a.challan_no, a.supplier_id,a.knitting_source, a.knitting_company, a.currency_id, a.exchange_rate,  a.booking_id, a.booking_no, a.receive_basis,a.is_posted_account
		from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in($cbo_item_cat) and a.entry_form in(20,27,263,266) and a.company_id=$cbo_company_name and b.transaction_type in(1,4)");
		foreach($receive_sql as $row)
		{
			$receive_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
			$receive_num_arr[$row[csf("id")]]["recv_number"]=$row[csf("recv_number")];
			$receive_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
			$receive_num_arr[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
			$receive_num_arr[$row[csf("id")]]["knitting_source"]=$row[csf("knitting_source")];
			$receive_num_arr[$row[csf("id")]]["knitting_company"]=$row[csf("knitting_company")];
			$receive_num_arr[$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
			$receive_num_arr[$row[csf("id")]]["exchange_rate"]=$row[csf("exchange_rate")];
			$receive_num_arr[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
			$receive_num_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
			$receive_num_arr[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];
			$receive_num_arr[$row[csf("id")]]["is_posted_account"]=$row[csf("is_posted_account")];
		}

		$issue_sql=sql_select("SELECT a.id, a.issue_number, a.challan_no, a.req_no, a.knit_dye_source, a.knit_dye_company,a.is_posted_account
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.item_category in($cbo_item_cat) and a.entry_form in (21,26,308,250,298,264,265) and b.transaction_type in (2,3) and a.company_id=$cbo_company_name");
		foreach($issue_sql as $row)
		{
			$issue_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
			$issue_num_arr[$row[csf("id")]]["issue_number"]=$row[csf("issue_number")];
			$issue_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
			$issue_num_arr[$row[csf("id")]]["req_no"]=$row[csf("req_no")];
			$issue_num_arr[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
			$issue_num_arr[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
			$issue_num_arr[$row[csf("id")]]["is_posted_account"]=$row[csf("is_posted_account")];
		}

		$transfer_sql=sql_select("SELECT a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.is_posted_account,a.to_company from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category in($cbo_item_cat) and a.entry_form in (57,487)");
		foreach($transfer_sql as $row)
		{
			$transfer_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
			$transfer_num_arr[$row[csf("id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
			$transfer_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
			$transfer_num_arr[$row[csf("id")]]["transfer_date"]=$row[csf("transfer_date")];
			$transfer_num_arr[$row[csf("id")]]["is_posted_account"]=$row[csf("is_posted_account")];
			$transfer_num_arr[$row[csf("id")]]["issue_to"]=$row[csf("to_company")];
		}

		if($cbo_item_cat>0) $item_cond=" and a.item_category=$cbo_item_cat and b.item_category_id=$cbo_item_cat";
		//echo $date_cond;die;
		$use_for_cond="";
		if($cbo_use_for>0) $use_for_cond=" and a.use_for=$cbo_use_for";
		if($cbo_store_name>0) $use_for_cond .=" and a.store_id=$cbo_store_name";
		if($rptType==1)
		{
			$sql="SELECT
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty,
					case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					a.department_id,
					b.item_code,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					a.machine_category,
					a.machine_id,
					a.floor_id,
					a.use_for,
					a.store_id,
					a.expire_date,
                    a.remarks
				from
					inv_transaction a, product_details_master b
				where
					a.prod_id=b.id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_cond $date_cond $use_for_cond
				order by a.transaction_date, a.id";

			$table_width=2560;
			$div_width="2580px";
		}
		else if($rptType==2)
		{
			$sql="select
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.cons_quantity as receive_qty,
					'' as issue_qty,
					a.cons_uom,
					a.supplier_id,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					a.department_id,
					a.section_id,
					c.receive_basis,
					c.booking_id,
					c.booking_no,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					a.machine_category,
					a.machine_id,
					a.floor_id,
					a.use_for,
					a.store_id,
					c.remarks,
					a.expire_date,
					c.supplier_referance,
					c.lc_sc_no,
				    a.remarks
                from
					inv_transaction a, product_details_master b, inv_receive_master c
				where
					a.mst_id=c.id and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_cond $date_cond $use_for_cond
				order by a.transaction_date, a.id";

			$table_width=2820;
			$div_width="2840px";
		}
		else if($rptType==3)
		{
			if($cbo_item_cat==107)
			{


				$order_con .= ($txt_order != "") ? " AND c.PO_NUMBER IN ('" . str_replace(",", "','", $txt_order) . "')" : "";

				//$order_con = " and c.id in ($txt_order_id)";
				//echo $order_con = " and c.PO_NUMBER in ('$txt_order')";


				// if($txt_style_ref_id!="")
				// {
				// 	$style_cond=" and d.id in($txt_style_ref_id)";
				// }
				$style_cond .= ($txt_style_ref != "") ? " AND d.JOB_NO_PREFIX_NUM  IN ('" . str_replace(",", "','", $txt_style_ref) . "')" : "";
				$sql="SELECT a.id as trans_id,a.transaction_type,a.mst_id as rec_issue_id,a.transaction_date,'' as receive_qty,b.quantity as issue_qty,a.cons_uom,a.location_id,p.id as prod_id,p.item_group_id,p.sub_group_name,p.item_description,p.item_code,p.item_size,a.cons_rate,a.cons_amount,a.department_id,a.division_id,a.section_id,a.inserted_by,	$select_insert_date,$select_insert_time,a.order_qnty,a.order_rate,a.machine_category,a.machine_id,a.PRODUCTION_FLOOR as floor,a.use_for,a.store_id,a.expire_date, a.remarks,b.po_breakdown_id as ORDER_ID,d.STYLE_REF_NO,d.BUYER_NAME,c.PO_NUMBER,d.JOB_NO,d.SEASON_BUYER_WISE
				from inv_transaction a, ORDER_WISE_GENERAL_DETAILS b, wo_po_break_down c, wo_po_details_master d, product_details_master p, inv_issue_master e
				where a.id=b.trans_id and a.mst_id = e.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.transaction_type in(2,3,4) and p.ITEM_CATEGORY_ID=107  and  e.company_id=$cbo_company_name $order_con $date_cond $style_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(21) ";
				//echo $sql;die;

			}
			else
			{
			$sql="SELECT
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					'' as receive_qty,
					a.cons_quantity as issue_qty,
					a.cons_uom,
					a.location_id,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					a.department_id,
					a.division_id,
					a.section_id,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					a.machine_category,
					a.machine_id,
					a.PRODUCTION_FLOOR as floor,
					a.use_for,
					a.store_id,
					a.expire_date,
                    a.remarks
				from
					inv_transaction a, product_details_master b
				where
					a.prod_id=b.id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.transaction_type in(2,3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $item_cond $date_cond $use_for_cond $use_for_cond
				order by a.transaction_date, a.id";
			}

			if($cbo_item_cat==101){
				$table_width=3340;
			    $div_width="3360px";
			}
			elseif($cbo_item_cat==107)
			{
				$table_width=3740;
			    $div_width="3760px";
			}

			else{
				$table_width=2840;
			    $div_width="2860px";
			}
		}
	    //echo $sql;//die;

		$sql_result=sql_select($sql);
		//print_r($sql_result);
		//echo count($sql_result);die;
		$issue_id="";

		$issue_arr=array();
		foreach($sql_result as $row)
		{
			if ($rptType==1 || $rptType==3)
			{
				if($issue_id=="") $issue_id=$row[csf('rec_issue_id')]; else $issue_id.=",".$row[csf('rec_issue_id')];

			}
			if($rptType==3){
				$issue_arr[$row[csf('rec_issue_id')]]=$row[csf('rec_issue_id')];
			}
		}

		$issueIds=chop($issue_id,','); $issue_id_cond_in="";
		$po_ids=count(array_unique(explode(",",$issue_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$issue_id_cond_in=" and (";
			$issueIdsArr=array_chunk(array_unique(explode(",",$issueIds)),999);
			foreach($issueIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$issue_id_cond_in.=" a.id in($ids) or";
			}
			$issue_id_cond_in=chop($issue_id_cond_in,'or ');
			$issue_id_cond_in.=")";
		}
		else
		{
			$issue_id_cond_in=" and a.id in($issueIds)";
		}
		//echo $issue_id_cond_in;die;
		$req_sql="SELECT a.id, b.itemissue_req_sys_id as req_no,a.remarks, c.item_group, c.item_sub_group, c.item_description, c.req_for, c.item_account, a.item_category
		from inv_issue_master a, inv_item_issue_requisition_mst b, inv_itemissue_requisition_dtls c
		where a.req_no=b.itemissue_req_sys_id and b.id=c.mst_id and a.entry_form=21 $issue_id_cond_in";
		// and b.itemissue_req_sys_id='OG-20-00007'
		// echo $req_sql;
		$req_data=sql_select($req_sql);
		$req_data_arr = array();

		foreach($req_data as $row)
		{
			$req_data_arr[$row[csf("req_no")]][$row[csf("item_group")]][$row[csf("item_sub_group")]][$row[csf("item_description")]]["req_for"]=$row[csf("req_for")];
		}

		// print_r($issue_arr);
		if($rptType==3 && $cbo_item_cat==101){
			if(!empty($issue_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 4, $issue_arr,$empty_arr);
				$sql_result_data =sql_select("SELECT a.ID, a.KNIT_DYE_COMPANY, a.SECTION_ID, a.KNIT_DYE_SOURCE, a.ISSUE_BASIS, a.REQ_ID from  inv_issue_master a,gbl_temp_engine d where a.id=d.ref_val and d.user_id= $user_id and  a.company_id=$cbo_company_name and d.entry_form=27 and d.REF_FROM=4");

				$issue_to_data_arr=$req_data_arr=$job_card_id_arr=array();
				foreach($sql_result_data as $row){
					$issue_to_data_arr[$row["ID"]]["KNIT_DYE_COMPANY"]=$row["KNIT_DYE_COMPANY"];
					$issue_to_data_arr[$row["ID"]]["SECTION_ID"]=$row["SECTION_ID"];
					$issue_to_data_arr[$row["ID"]]["KNIT_DYE_SOURCE"]=$row["KNIT_DYE_SOURCE"];
					$issue_to_data_arr[$row["ID"]]["ISSUE_BASIS"]=$row["ISSUE_BASIS"];
					$issue_to_data_arr[$row["ID"]]["REQ_ID"]=$row["REQ_ID"];

					if($row["ISSUE_BASIS"]==7){
						$req_data_arr[$row["REQ_ID"]]=$row["REQ_ID"];  //from issue to req to job to receive
					}
					elseif($row["ISSUE_BASIS"]==15)
					{
						$job_card_id_arr[$row["REQ_ID"]]=$row["REQ_ID"]; //from issue to job_card to receive
					}
				}
			}


			$order_rec_data_arr=$order_id_data_arr=array();
			if(!empty($req_data_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 5, $req_data_arr,$empty_arr);
				$sql_result_data =sql_select("SELECT a.ID, c.PARTY_ID, c.ORDER_NO, f.BUYER_PO_ID, c.TRIMS_REF, c.SUBCON_JOB, c.WITHIN_GROUP, f.BUYER_STYLE_REF, f.BUYER_BUYER from trims_raw_mat_requisition_mst a,trims_job_card_mst b,subcon_ord_mst c, subcon_ord_dtls f, gbl_temp_engine d where  a.id = d.ref_val AND b.id = a.job_id AND C.id = B.received_id  AND C.id = f.mst_id  AND d.user_id = $user_id and  a.company_id=$cbo_company_name AND C.entry_form = 255  AND b.entry_form=257 AND d.entry_form = 27 AND d.REF_FROM = 5 group by a.ID, C.PARTY_ID, c.ORDER_NO, f.BUYER_PO_ID, f.BUYER_STYLE_REF, f.BUYER_BUYER, c.TRIMS_REF, c.SUBCON_JOB, c.WITHIN_GROUP");
				foreach($sql_result_data as $row){
					$order_rec_data_arr[$row["ID"]]["PARTY_ID"]=$row["PARTY_ID"];
					$order_rec_data_arr[$row["ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
					$order_rec_data_arr[$row["ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
					$order_rec_data_arr[$row["ID"]]["TRIMS_REF"]=$row["TRIMS_REF"];
					$order_rec_data_arr[$row["ID"]]["SUBCON_JOB"]=$row["SUBCON_JOB"];
					$order_rec_data_arr[$row["ID"]]["WITHIN_GROUP"]=$row["WITHIN_GROUP"];
					$order_rec_data_arr[$row["ID"]]["ORDER_NO"]=$row["ORDER_NO"];
					$order_rec_data_arr[$row["ID"]]["BUYER_PO_ID"]=$row["BUYER_PO_ID"];
					$order_id_data_arr[$row["ID"]]=$row["BUYER_PO_ID"];
				}
			}

			if(!empty($job_card_id_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 7, $job_card_id_arr,$empty_arr);
				$sql_result_data =sql_select("SELECT b.ID, c.PARTY_ID, c.ORDER_NO, f.BUYER_PO_ID, c.TRIMS_REF, c.SUBCON_JOB, c.WITHIN_GROUP, f.BUYER_STYLE_REF, f.BUYER_BUYER from trims_job_card_mst b,subcon_ord_mst c, subcon_ord_dtls f, gbl_temp_engine d where  b.id = d.ref_val AND C.id = b.received_id  AND C.id = f.mst_id  AND d.user_id = $user_id and  c.company_id=$cbo_company_name AND C.entry_form = 255  AND b.entry_form=257 AND d.entry_form = 27 AND d.REF_FROM = 7 group by b.ID, C.PARTY_ID, c.ORDER_NO, f.BUYER_PO_ID, f.BUYER_STYLE_REF, f.BUYER_BUYER, c.TRIMS_REF, c.SUBCON_JOB, c.WITHIN_GROUP");
				foreach($sql_result_data as $row){
					$order_rec_data_arr[$row["ID"]]["PARTY_ID"]=$row["PARTY_ID"];
					$order_rec_data_arr[$row["ID"]]["BUYER_STYLE_REF"]=$row["BUYER_STYLE_REF"];
					$order_rec_data_arr[$row["ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
					$order_rec_data_arr[$row["ID"]]["TRIMS_REF"]=$row["TRIMS_REF"];
					$order_rec_data_arr[$row["ID"]]["SUBCON_JOB"]=$row["SUBCON_JOB"];
					$order_rec_data_arr[$row["ID"]]["WITHIN_GROUP"]=$row["WITHIN_GROUP"];
					$order_rec_data_arr[$row["ID"]]["ORDER_NO"]=$row["ORDER_NO"];
					$order_rec_data_arr[$row["ID"]]["BUYER_PO_ID"]=$row["BUYER_PO_ID"];
					$order_id_data_arr[$row["ID"]]=$row["BUYER_PO_ID"];
				}
			}


			$internal_ref_arr=array();
			if(!empty($order_id_data_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 33, $order_id_data_arr,$empty_arr);
				$sql_int_ref_data =sql_select("SELECT a.ID, a.GROUPING  from wo_po_break_down a, gbl_temp_engine d where  a.id = d.ref_val AND d.user_id = $user_id  AND d.entry_form = 27 AND d.REF_FROM = 33");

				foreach($sql_int_ref_data as $row){
					$internal_ref_arr[$row["ID"]]["GROUPING"]=$row["GROUPING"];
				}
			}
		}

		$req_sql=sql_select("SELECT a.id,a.division_id,a.department_id,a.section_id,a.manual_req, a.requ_prefix_num, a.requ_no, b.product_id, b.required_for from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0");
		$reqisition_data=array();
		foreach ($req_sql as $row)
		{
			$reqisition_data[$row[csf('id')]]['division_id']=$row[csf('division_id')];
			$reqisition_data[$row[csf('id')]]['department_id']=$row[csf('department_id')];
			$reqisition_data[$row[csf('id')]]['section_id']=$row[csf('section_id')];
			$reqisition_data[$row[csf('id')]]['manual_req']=$row[csf('manual_req')];
			$reqisition_data[$row[csf('id')]]['requ_prefix_num']=$row[csf('requ_prefix_num')];
			$reqisition_data[$row[csf('id')]]['requ_no']=$row[csf('requ_no')];
			$reqisition_data[$row[csf('id')]][$row[csf('product_id')]]['required_for']=$row[csf('required_for')];
		}


		//$requisiton_arr=return_library_array( "select id, requ_prefix_num from inv_purchase_requisition_mst",'id','requ_prefix_num');
		$wo_sql=sql_select("select id, wo_number_prefix_num,requisition_no,wo_number from wo_non_order_info_mst where item_category not in(1,2,3,12,13,14)");
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', "supplier_name");
		$wo_arr=array();
		foreach($wo_sql as $row)
		{
			$wo_arr[$row[csf("id")]]['wo_number']=$row[csf("wo_number")];
			/*$reqs=explode(",", $row[csf("requisition_no")]);
			for ($i=0; $i <count($reqs) ; $i++) {
				$wo_arr[$row[csf("id")]]['requ_no']=$reqs[$i];
			}*/
			$wo_arr[$row[csf("id")]]['requ_no']=$row[csf("requisition_no")];
			$wo_arr[$row[csf("id")]]['id']=$row[csf("id")];
		}

		$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
		$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details",'id','pi_number');
		$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
		$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
		$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
		$pi_wo_id=return_library_array( "select pi_id, work_order_id from com_pi_item_details",'pi_id','work_order_id');
		$pi_wo_sql=sql_select("SELECT pi_id as PI_ID, work_order_id as WORK_ORDER_ID from com_pi_item_details where status_active=1 group by pi_id, work_order_id");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		
		 
		$supplier_library=return_library_array( "SELECT a.id, a.buyer_name FROM lib_buyer a, lib_buyer_party_type  b, lib_buyer_tag_company c WHERE a.id = b.buyer_id AND a.id = c.buyer_id AND c.tag_company = $cbo_company_name AND b.party_type IN (80) AND a.status_active = 1 GROUP BY a.id, a.buyer_name ORDER BY a.buyer_name", "id","buyer_name");

		// select id, buyer_name from lib_buyer where status_active=1
		$pi_wo_arr=array();
		foreach($pi_wo_sql as $row)
		{
			$pi_wo_arr[$row['PI_ID']].=$row['WORK_ORDER_ID'].',';
		}
		$remarks_arr=array();
		if($rptType==3)
		{

			$remarks_arr=return_library_array( "select a.id, a.remarks from inv_issue_master a where a.status_active=1 $issue_id_cond_in",'id','remarks');
			//echo "select a.id, a.remarks from inv_issue_master a where a.status_active=1 $issue_id_cond_in";
		}
		ob_start();

		?>

		<div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
			<table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="
				rpt_table" rules="all" id="" align="left">
				<tr class="form_caption" style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
					</td>
				</tr>
		   	</table>
		   	<br />
			<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0"  class="
				rpt_table" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th width="30" >SL</th>
						<th width="70" >Prod. Id</th>
                        <th width="100" >Store Name</th>
                     	<?
							if($rptType==3)
							{
						?>
								<th width="100">Location</th>
						<?
							}
						?>
						<th width="100" >Trans. Date</th>
						<th width="130" >Trans. Ref.</th>
						<?
						if($rptType==3)
						{
							?>
							<th  width="80">Year</th>
						<th  width="80">Job No</th>
						<th width="80">Season</th>
						<th width="80">Style Ref No</th>
						<th  width="80">Buyer</th>
						<th width="80">Order No</th>
                            <?
						}
						?>



                        <th  style="word-break: break-all;"width="100" >Challan No</th>
                        <?
						if($rptType==2)
						{
							?>
							<th style="word-break: break-all;"width="80">Receive Basis</th>
							<th style="word-break: break-all;"width="80">Supplier</th>
                            <?
						}
						?>
                        <th style="word-break: break-all;" width="100">Pur. Reqsn/<br /> Book. No/<br />Store Reqsn</th>
                        <?
						if($rptType==3 && $cbo_item_cat==101)
						{
							?>
							<th width="100">Party.</th>
							<th width="100">Work Order No</th>
							<th width="100">Trims Ref</th>
							<th width="100">Internal Ref</th>
							<th width="100">Buyers Buyer</th>
							<th width="100">Order Receive</th>
							<?
						}
						if($rptType==2)
						{
							?>
							<th width="100">WO/PI No.</th>
							<th width="100">Required For</th>
							<th width="100">Supplier Ref. No</th>
							<th width="100">L/C No</th>
                            <?
                            if($cbo_item_cat==18)
                            {
	                            ?>
								<th width="100">Division</th>
								<th width="100">Department</th>
	                            <th width="100">Section</th>
								<th width="100">Manual Req. No.</th>
	                            <?
                       		 }
						}
						else if($rptType==3  )
						{
							?>
							<th style="word-break: break-all;" width="100">Division</th>
							<th style="word-break: break-all;" width="100">Department</th>
                            <th style="word-break: break-all;" width="100">Section</th>
                            <?
						}
						?>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-Group</th>
                        <th width="120">Item Description</th>
                        <th width="100">Item Code</th>
                        <?
                        if($rptType==3 || $rptType==1 )
						{
							?>
                            <th width="100">Machine Category</th>
                            <th width="100">Floor</th>
                            <th width="100">Machine No</th>
                            <?
						}
						?>
                        <th width="80">Size</th>
                        <?
						if($rptType==2 || $rptType==1 )
						{
							?>
                            <th width="80">Currency</th>
                            <th width="80">Exchange Rate</th>
                            <th width="80">Actual Rate</th>
							<th width="80">Receive Qty</th>
							<?
							if($rptType==2 )
							{
							?>
							<th width="60">UOM</th>
							<?
							}
							?>
                            <th width="80">Actual Amt</th>
                            <?
						}
						if($rptType==3 || $rptType==1 )
						{
							?>
							<th  width="80">Issue Qty</th>
                            <?
							if($rptType==3)
							{
								?>
								<th  width="60">UOM</th>
								<?
							}
						}
						?>
                        <th  width="80">Rate(TK)</th>

                        <th  width="100">Amount(TK)</th>
                        <? if($rptType==2 || $rptType==1 ){?>
                                <th width="60">Warranty DOH</th>
                        <? } ?>
                        <?php
                       		if(($rptType==2 && $cbo_item_cat==18) || $rptType==3)
                       		{
                       				?>
                       				<th  width="130">Remarks</th>
                       				<?
                       		}
                       	 ?>
                          <? if($rptType==3 || $rptType==1 ){?>
                                <th width="100">Issue To</th>
                            <? } ?>
                        <th  width="100">Accounting Posting</th>
                        <th  width="110">User</th>
                        <th  width="140">Insert Date</th>
                        <th  width="160">Comments</th>
                        <?
						if($rptType==3)
						{
							?>
                            <th width="100">Use For</th>
                            <?
						}
						if($rptType==3 || $rptType==1 )
						{
							?>
                            <th width="100">Req For</th>
                            <?
						}
						?>
					</tr>
				</thead>
		   	</table>
           	<br />
		  	<div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0"
					border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');

					$i=1;$total_receive="";$total_issue="";
					foreach($sql_result as $row)
					{
						$req_id=$issue_to_data_arr[$row["REC_ISSUE_ID"]]["REQ_ID"];
						$issue_basis=$issue_to_data_arr[$row["REC_ISSUE_ID"]]["ISSUE_BASIS"];
					    $buyer_po_id= $order_rec_data_arr[$req_id]["BUYER_PO_ID"];
						if($issue_basis==7){
							$party_id= $order_rec_data_arr[$req_id]["PARTY_ID"];
							$buyer_int_ref= $internal_ref_arr[$buyer_po_id]["GROUPING"];
							$buyer_buyer= $order_rec_data_arr[$req_id]["BUYER_BUYER"];
							$trims_ref= $order_rec_data_arr[$req_id]["TRIMS_REF"];
							$subcon_job= $order_rec_data_arr[$req_id]["SUBCON_JOB"];
							$order_no= $order_rec_data_arr[$req_id]["ORDER_NO"];
							$within_group= $order_rec_data_arr[$req_id]["WITHIN_GROUP"];
						}
						else
						{
							$party_id= $order_rec_data_arr[$req_id]["PARTY_ID"];
							$buyer_int_ref= $internal_ref_arr[$buyer_po_id]["GROUPING"];
							$buyer_buyer= $order_rec_data_arr[$req_id]["BUYER_BUYER"];
							$trims_ref= $order_rec_data_arr[$req_id]["TRIMS_REF"];
							$subcon_job= $order_rec_data_arr[$req_id]["SUBCON_JOB"];
							$order_no= $order_rec_data_arr[$req_id]["ORDER_NO"];
							$within_group= $order_rec_data_arr[$req_id]["WITHIN_GROUP"];
						}

						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td style="word-break:break-all;width:30px;" title="<?= "transac id=".$row[csf("trans_id")];?>"><p><? echo $i; ?></p></td>
							<td style="word-break:break-all;width:70px;" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                            <td style="word-wrap: break-word;word-break:break-all; width:100px;"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
                            <?
							if($rptType==3)
							{
								?>
                            	<td style=" word-wrap: break-word;word-break: break-all;" width="100"><p><? echo $location_arr[$row[csf("location_id")]]; ?></p></td>
                            	<?
                        	}
                        	?>
							<td style="word-wrap: break-word;word-break: break-all;" width="100" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td  style="word-break: break-all;"width="130"  align="center"><p>
							<?
							if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4)
							{
								echo $receive_num_arr[$row[csf('rec_issue_id')]]["recv_number"];
							}
							else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3)

							{
								echo $issue_num_arr[$row[csf('rec_issue_id')]]["issue_number"];
							}
							else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
							{
								echo $transfer_num_arr[$row[csf('rec_issue_id')]]["transfer_system_id"];
							}
							?>
							</p>
                            </td>
							<?
							if($rptType==3)
							{
								?>
                            <td style="word-wrap: break-word;word-break: break-all;"width="80"><?echo  date('Y', strtotime($row["TRANSACTION_DATE"]));?></td>
							<td style="word-wrap: break-word;word-break: break-all;"width="80"><?echo $row["JOB_NO"];?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"><?echo $buyer_session_arr[$row["SEASON_BUYER_WISE"]];?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"><?echo $row["STYLE_REF_NO"];?></td>
							<td style="word-wrap: break-word;word-break: break-all;"width="80"><?echo $buyer_library[$row["BUYER_NAME"]];?></td>
							<td style="word-wrap: break-word;word-break: break-all;"width="80"><?echo $row["PO_NUMBER"]?></td>
                            	<?
                        	}
                        	?>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><p>
							<?

								if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
								{
									echo $receive_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
								}
								else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
								{
									echo $issue_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
								}

							?>
							</p>
                            </td>
                            <?
							if($rptType==2)
							{
								?>
								<td style="word-break: break-all;" width="80"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
								<td ,style="word-break: break-all;"width="80"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                                <?
							}
							?>
                            <td style="word-break: break-all;" width="100"><p>
                            <?
							if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
							{
								//$req_book_no=$row[csf('rec_issue_id')];
								if($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==7)
								{
									$req_book_no=$reqisition_data[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]]['requ_prefix_num'];
								}
								else if ($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==2)
								{
									//$req_book_no=$receive_num_arr[$row[csf('rec_issue_id')]]["booking_no"];
									// $req_book_no=$wo_arr[$receive_num_arr[$row[csf('rec_issue_id')]]["booking_id"]]['wo_number'];
									$requ_no=$wo_arr[$row[csf("booking_id")]]['requ_no'];
									// $req_book_no=$reqisition_data[$requ_no]['requ_no'];
									$reqs=explode(",", rtrim($requ_no,','));
									$req_book_no='';
									foreach($reqs as $val)
									{
										$req_book_no.=$reqisition_data[$val]['requ_no'].", ";
									}
									$req_book_no=rtrim($req_book_no,', ');
								}
								else if ($receive_num_arr[$row[csf('rec_issue_id')]]["receive_basis"]==1)
								{
									$wo_no=explode(",", rtrim($pi_wo_arr[$row[csf("booking_id")]],','));
									$req_book_no='';
									foreach($wo_no as $wo_id)
									{
										$requ_no=$wo_arr[$wo_id]['requ_no'];
										$reqs=explode(",", rtrim($requ_no,','));
										foreach($reqs as $val)
										{
											$req_book_no.=$reqisition_data[$val]['requ_no'].", ";
										}
									}
									$req_book_no=rtrim($req_book_no,', ');
								}
								else
								{
									$req_book_no="";
								}
							}
							else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
							{
								$req_book_no=$issue_num_arr[$row[csf('rec_issue_id')]]["req_no"];
							}
							echo $req_book_no;

                            ?></p></td>
                            <?
							if($rptType==3 && $cbo_item_cat==101)
							{
								?>
								<td style="word-break: break-all;" width="100" align="center"><p><? echo ($within_group == 2) ? $buyer_library[$party_id] : $company_arr[$party_id];?></p></td>
								<td style="word-break: break-all;" width="100" align="center"><p><? echo $order_no; ?></p></td>
								<td style="word-break: break-all;"width="100" align="center"><p><? echo  $trims_ref; ?></p></td>
								<td style="word-break: break-all;" width="100" align="center"><p><? echo $buyer_int_ref; ?></p></td>
								<td style="word-break: break-all;" width="100" align="center"><p><? echo $buyer_library[$buyer_buyer]; ?></p></td>
								<td style="word-break: break-all;" width="100" align="center"><p><? echo $subcon_job; ?></p></td>
								<?
							}
							if($rptType==2)
							{
								$department_name='';
								$section_name='';
								$divission='';
								$manual_req='';
								$required_for='';
								?>
								<td style="word-break: break-all;" width="100" align="center"><p>
								<?

                                if($row[csf("receive_basis")]==1)
                                {
                                    //echo $pi_num_arr[$row[csf("booking_id")]];
                                    echo $pi_num_arr[$row[csf("booking_id")]];
                                   $requ_no=$wo_arr[$pi_wo_id[$row[csf("booking_id")]]]['requ_no'];
                                    // $divission=$reqisition_data[$requ_no]['division_id'];
									// $department_name=$reqisition_data[$requ_no]['department_id'];
									// $section_name=$reqisition_data[$requ_no]['section_id'];
									// $manual_req=$reqisition_data[$requ_no]['manual_req'];
									// $required_for=$reqisition_data[$requ_no][$row[csf("prod_id")]]['required_for'];
									$reqs_arr=explode(",", rtrim($requ_no,','));
									$divission=$department_name=$section_name=$manual_req=$required_for='';
									foreach($reqs_arr as $val)
									{
										$divission .=$division_arr[$reqisition_data[$val]['division_id']].", ";
										$department_name.=$department_arr[$reqisition_data[$val]['department_id']].", ";
										$section_name.=$section_arr[$reqisition_data[$val]['section_id']].", ";
										$manual_req.=$reqisition_data[$val]['manual_req'].", ";
										$required_for.=$use_for[$reqisition_data[$val][$row[csf("prod_id")]]['required_for']].", ";
									}
									$divission=rtrim($divission,', ');
									$department_name=rtrim($department_name,', ');
									$section_name=rtrim($section_name,', ');
									$manual_req=rtrim($manual_req,', ');
									$required_for=rtrim($required_for,', ');
                                }
                                else if($row[csf("receive_basis")]==2)
                                {
                                    echo $wo_arr[$row[csf("booking_id")]]['wo_number'];

                                    $requ_no=$wo_arr[$row[csf("booking_id")]]['requ_no'];
                                   	// $divission =$reqisition_data[$requ_no]['division_id'];
									// $department_name=$reqisition_data[$requ_no]['department_id'];
									// $section_name=$reqisition_data[$requ_no]['section_id'];
									// $manual_req=$reqisition_data[$requ_no]['manual_req'];
									// $required_for=$reqisition_data[$requ_no][$row[csf("prod_id")]]['required_for'];
									$reqs_arr=explode(",", rtrim($requ_no,','));
									$divission=$department_name=$section_name=$manual_req=$required_for='';
									foreach($reqs_arr as $val)
									{
										$divission .=$division_arr[$reqisition_data[$val]['division_id']].", ";
										$department_name.=$department_arr[$reqisition_data[$val]['department_id']].", ";
										$section_name.=$section_arr[$reqisition_data[$val]['section_id']].", ";
										$manual_req.=$reqisition_data[$val]['manual_req'].", ";
										$required_for.=$use_for[$reqisition_data[$val][$row[csf("prod_id")]]['required_for']].", ";
									}
									$divission=rtrim($divission,', ');
									$department_name=rtrim($department_name,', ');
									$section_name=rtrim($section_name,', ');
									$manual_req=rtrim($manual_req,', ');
									$required_for=rtrim($required_for,', ');

                                }
                                else if($row[csf("receive_basis")]==7)
                                {
                                	$requ_no=$row[csf("booking_id")];
                                	$divission =$division_arr[$reqisition_data[$requ_no]['division_id']];
									$department_name=$department_arr[$reqisition_data[$requ_no]['department_id']];
									$section_name=$section_arr[$reqisition_data[$requ_no]['section_id']];
									$manual_req=$reqisition_data[$requ_no]['manual_req'];
									$required_for=$use_for[$reqisition_data[$requ_no][$row[csf("prod_id")]]['required_for']];
                                }
                                ?></p>
                                </td>
								<td style="word-break: break-all;"width="100" align="center"><p><? echo $required_for; ?></p></td>
								<td style="word-break: break-all;"width="100" ><p><? echo $row[csf("supplier_referance")]; ?></p></td>
								<td style="word-break: break-all;"width="100" ><p><? echo $row[csf("lc_sc_no")]; ?></p></td>
                                <?
                                if($cbo_item_cat==18)
                                {
                                	?>

									<td style="word-break: break-all;"width="100" align="center"><p><? echo $divission; ?></p></td>
									<td style="word-break: break-all;"width="100" align="center"><p><? echo $department_name; ?></p></td>
	                                <td style="word-break: break-all;"width="100" align="center"><p><? echo $section_name; ?></p></td>
	                                <td style="word-break: break-all;"width="100" align="center"><p><? echo $manual_req; ?></p></td>
	                                <?
                                }
							}
							else if($rptType==3 && $cbo_item_cat==101){
								?>
								<td  style="word-wrap: break-word;word-break: break-all;"width="100" align="center"><p><? echo $division_arr[$row[csf("division_id")]]; ?></p></td>
								<td  style="word-wrap: break-word;word-break: break-all;" width="100" align="center"><p><? echo $department_arr[$row[csf("department_id")]]; ?></p></td>
                                <td  style="word-wrap: break-word;word-break: break-all;" width="100" align="center"><p><? echo $trims_section[$issue_to_data_arr[$row["REC_ISSUE_ID"]]["SECTION_ID"]]; ?></p></td>
                                <?
							}
							else if($rptType==3  )
							{
								?>
								<td  style="word-wrap: break-word;word-break: break-all;""width="100" align="center"><p><? echo $division_arr[$row[csf("division_id")]]; ?></p></td>
								<td  style="word-wrap: break-word;word-break: break-all;" width="100" align="center"><p><? echo $department_arr[$row[csf("department_id")]]; ?></p></td>
                                <td  style="word-wrap: break-word;word-break: break-all;" width="100" align="center"><p><? echo $section_arr[$row[csf("section_id")]]; ?></p></td>
                                <?
							}

							?>
                            <td  style="word-wrap: break-word;word-break: break-all;" width="100" ><p><? echo $group_arr[$row[csf('item_group_id')]]; ?></p></td>
                            <td  style="word-wrap: break-word;word-break: break-all;"width="100" ><p><? echo $row[csf('sub_group_name')]; ?></p></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="120"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td  style="word-wrap: break-word;word-break: break-all;" width="100" ><p><? echo $row[csf('item_code')]; ?></p></td>
                            <?
							if($rptType==3 || $rptType==1 )
							{
								?>
                                <td style="word-wrap: break-word;word-break: break-all;" width="100" ><p><? echo $machine_category[$row[csf('machine_category')]]; ?></p></td>
                                <td style="word-wrap: break-word;word-break: break-all;" width="100" ><p><? echo $floor_arr[$row[csf('floor')]]; ?></p></td>
                                <td style="word-wrap: break-word;word-break: break-all;"width="100"><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                                <?
							}
							?>
                            <td style="word-break: break-all;" width="80"><p><? echo $row[csf('item_size')]; ?></p></td>
                             <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <td width="80" align="center"><p><? if($row[csf("transaction_type")]==1) echo $currency[$receive_num_arr[$row[csf('rec_issue_id')]]["currency_id"]]; ?></p></td>
                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($receive_num_arr[$row[csf('rec_issue_id')]]["exchange_rate"],2); ?></p></td>
                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($row[csf('order_rate')],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf("receive_qty")],2); $total_receive +=$row[csf("receive_qty")]; ?></p></td>
								<?
								if($rptType==2 )
								{
								?>
								<td width="60" align="right"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]];  ?></p></td>
								<? }?>

                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) $order_amt=$row[csf('order_qnty')]*$row[csf('order_rate')]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<td style="word-break: break-all;"width="80" align="right"><p><? echo number_format($row[csf("issue_qty")],2); $total_issue +=$row[csf("issue_qty")]; ?></p></td>
                                <?
								if($rptType==3)
								{
									?>
									<td style="word-break: break-all;"width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]];  ?></p></td>
									<?
								}
							}
							?>
                            <td style="word-break: break-all;"width="80" align="right"><p><? echo number_format($row[csf("cons_rate")],2); ?></p></td>

                            <td style="word-break: break-all;" align="right" width="100" style="padding-right:3px">
							<p><?
							echo  number_format($row[csf("cons_amount")],2); $total_amount +=$row[csf("cons_amount")];
							//echo number_format($row[csf("issue_qty")],2,'.','')*number_format($row[csf("cons_rate")],2,'.','');
							//$total_amount +=number_format($row[csf("issue_qty")],2,'.','')*number_format($row[csf("cons_rate")],2,'.','');

							?></p></td>

                            <? if($rptType==2 || $rptType==1 ){?><td width="60" align="center"><p><?
                            if($row[csf("expire_date")]!=''){
                            	$daysOnHand = (datediff("d",date("Y-m-d"),$row[csf("expire_date")])-1);
                            }else{
                            	$daysOnHand='';
                            }

                            echo $daysOnHand; ?></p></td><? } //$department_arr ?>
                            <?php
                       		if(($rptType==2 && $cbo_item_cat==18) || $rptType==3)
                       		{
                       				?>
                       				<td style="word-break: break-all;" width="130"><?php 
									if($rptType==2) {echo $row[csf("remarks")];} 
									else {echo $remarks_arr[$row[csf('rec_issue_id')]]; }?></td>
                       				<?
                       		}
                       	 	?>

							<? 

								if($rptType==3 && $cbo_item_cat==101 && $cbo_item_cat=107){
							?>
							<td width="100" style="word-break: break-all;">
							<p><? 
								if($issue_to_data_arr[$row["REC_ISSUE_ID"]]["KNIT_DYE_SOURCE"]==1) {
									echo $company_arr[$issue_to_data_arr[$row["REC_ISSUE_ID"]]["KNIT_DYE_COMPANY"]];
							}

							else{
									echo $supplier_library[$issue_to_data_arr[$row["REC_ISSUE_ID"]]["KNIT_DYE_COMPANY"]];

									  
							}; 
							
							?></p></td>
							
							<? }elseif($rptType==3 || $rptType==1){
							?><td style="word-break: break-all;"width="100"><p><? echo $company_arr[$cbo_company_name];//$company_arr[$row[csf("company_id")]];  ?></p></td><?
							} //$department_arr ?>


                            <td style="word-break: break-all;" width="100">
								<?
									if($row[csf("transaction_type")]==1)
									{ echo $yes_no[$receive_num_arr[$row[csf('rec_issue_id')]]["is_posted_account"]];}
									elseif($row[csf("transaction_type")]==2)
									{ echo $yes_no[$issue_num_arr[$row[csf('rec_issue_id')]]["is_posted_account"]];}
									elseif($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
									{
										if($transfer_num_arr[$row[csf('rec_issue_id')]]["is_posted_account"]>0)
										{
											$transfer_num_arr[$row[csf('rec_issue_id')]]["is_posted_account"]=1;
										}
										echo $yes_no[$transfer_num_arr[$row[csf('rec_issue_id')]]["is_posted_account"]];
									}
								?>
							</td>
                            <td style="word-break: break-all;"width="110"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></p></td>
                            <td style="word-break: break-all;"width="140"><p><? echo change_date_format($row[csf("insert_date")])." ".$row[csf("insert_time")]; ?></p></td>
                            <td style="word-break: break-all;" width="160"><p><? echo $row[csf("remarks")]; ?></p></td>
                            <?
							if($rptType==3)
							{
								?>
                                <td style="word-break: break-all;"width="100"><p><? echo $use_for[$row[csf("use_for")]]; ?></p></td>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
                                <td style="word-break: break-all;" width="100"><p><? echo $req_data_arr[$issue_num_arr[$row[csf('rec_issue_id')]]["req_no"]][$row[csf('item_group_id')]][$row[csf('sub_group_name')]][$row[csf('item_description')]]["req_for"]; ?>&nbsp;</p></td>
                                <?
							}
							?>
						</tr>
						<?
						$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv']+=$row[csf("receive_qty")];
						//$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_amt']+=$row[csf('order_qnty')]*$row[csf('order_rate')];
						$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['issue']+=$row[csf("issue_qty")];
						$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['cons_uom']=$row[csf("cons_uom")];

						if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
						{
							$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_amt']+=$row[csf("cons_amount")];
						}
						else
						{
							$item_wise_qty_arr[$row[csf('item_group_id')]][$row[csf('item_description')]]['issue_amt']+=$row[csf("cons_amount")];
						}

						$i++;
					}
					//print_r($item_wise_qty_arr);
					?>
					</tbody>
				    <tfoot>
                    	<tr>
                            <th width="30">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <?
							if($rptType==3)
							{
								?>
                            	<th width="100">&nbsp;</th>
                                <?
							}
							?>
                            <th width="100">&nbsp;</th>
                            <th width="130">&nbsp;</th>
							<?
							if($rptType==3)
							{
								?>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<?
							}?>
                            <th width="100">&nbsp;</th>
                            <?
							if($rptType==2)
							{
								?>
                            	<th width="80">&nbsp;</th>
                            	<th width="80">&nbsp;</th>
                                <?
							}
							?>
                            <th width="100">&nbsp;</th>
							<?
							if($rptType==3 && $cbo_item_cat==101)
							{
								?>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                                <?
							}
							?>
                            <?
							if($rptType==2)
							{
								?>
                            	<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
                                <?
                                if($cbo_item_cat==18)
                                {
                                	?>
	                            	<th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <?
                                }
							}
							else if($rptType==3  )
							{
								?>
                            	<th width="100">&nbsp;</th>
                            	<th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <?
							}

							?>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <?
							if($rptType==3 || $rptType==1 )
							{
								?>
                                <th width="100" >&nbsp;</th>
                                <th width="100" >&nbsp;</th>
                                <th width="100" >&nbsp;</th>
                                <?
							}
							?>
                            <th width="80"><? if($rptType==3) echo "Total:"; ?></th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >Total:</th>
                            	<th width="80" id="value_total_receive"><? //echo number_format($total_receive,2); ?></th>
								<?
								if($rptType==2 )
								{
								?>
								<th width="60" >&nbsp;</th>
								<? } ?>

                                <th width="80"  id="value_total_order_amt"><? //echo number_format($total_order_amt,2); ?></th>

                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
                            	<!-- <th width="80" id="value_total_issue"><?// echo number_format($total_issue,2); ?></th> -->
								<th width="80" id=""><? echo number_format($total_issue,2); ?></th>
                                <?
								if($rptType==3)
								{
								?>
								<th width="60" ></th>
								<?
								}
							}
							?>
                            <th width="80" >&nbsp;</th>
                           <!--  <th id="value_total_amount"  width="100"><? //echo number_format($total_amount,2); ?></th> -->
						   <th id=""  width="100"><? echo number_format($total_amount,2); ?></th>
                            <?
                            if($rptType==2 || $rptType==1 )
							{
								?>
                            	<th width="60"><? //echo number_format($total_issue,2); ?></th>
                                <?
							}
							?>
                            <?php
                       		if(($rptType==2 && $cbo_item_cat==18) || $rptType==3)
                       		{
                       				?>
                       				<th  width="130"></th>
                       				<?
                       		}
                       	 ?>
                            <?
                            if($rptType==3 || $rptType==1 )
							{
								?>
                            	<th width="100"><? //echo number_format($total_issue,2); ?></th>
                                <?
							}
							?>
                            <th width="100" >&nbsp;</th>
                            <th width="107" >&nbsp;</th>
                            <th width="140" >&nbsp;</th>
                            <th width="160" >&nbsp;</th>
                            <?
							if($rptType==3)
							{
								?>
                                <th width="100" >&nbsp;</th>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
                            	<th width="100" >&nbsp;</th>
                                <?
							}
							?>

                        </tr>
                    </tfoot>
                </table>
			 </div>
        </fieldset>
        <br>
        <?
    	if($cbo_item_cat==11 || $cbo_item_cat==15 || $cbo_item_cat==21 || $cbo_item_cat==16 || $cbo_item_cat==9 || $cbo_item_cat==38 || $cbo_item_cat==10 || $cbo_item_cat==19 || $cbo_item_cat==17 || $cbo_item_cat==69 || $cbo_item_cat==15 || $cbo_item_cat==8 || $cbo_item_cat==36 || $cbo_item_cat==18)
		{
			?>
	        <table width="710" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
	        	<caption> <strong> Item Summary</strong></caption>
	        <thead>
	            <tr>
	            <th width="30" >SL</th>
	            <th width="100" >Item Group</th>
	            <th width="120" >Item Desc.</th>
	            <?
	            if($rptType==2 || $rptType==1 )
					{ ?>
	            <th width="70" >Rcv Qty</th>
	            <th width="60" >UOM</th>
	            <th width="50" >Rate(TK)</th>
	            <th width="80" >Amount</th>
	            <?
					}
					if($rptType==3 || $rptType==1 )
					{
	            ?>
	            <th width="70" >Issue Qty</th>
				<?
				if($rptType==3 )
				{
				?>
	            <th width="60" >UOM</th>
				<? } ?>
	            <th width="50" >Rate(TK)</th>
	            <th width="" >Amount</th>
	            <?
					}
				?>
	            </tr>
	        </thead>
	        <?
			$k=1;$total_rec_qty=0;$total_rec_amt=0;$total_issue_qty=0;$total_issue_amt=0;
	        foreach($item_wise_qty_arr as $item_key=>$item_data)
			{
				foreach($item_data as $desc_key=>$desc_value)
				{
			?>
	        <tr>
	             <td width="30"><? echo $k; ?> </td>
	             <td width="100"> <? echo $group_arr[$item_key]; ?></td>
	             <td width="120" ><? echo $desc_key; ?> </td>
	              <?
	            if($rptType==2 || $rptType==1 )
					{ ?>
	             <td width="70" align="right"><? echo number_format($desc_value['recv'],2); ?>  </td>

	             <td width="60" align="center"><? echo $unit_of_measurement[$desc_value['cons_uom']]; ?>  </td>

	             <td width="50"> <? echo number_format($desc_value['recv_amt']/$desc_value['recv'],2); ?> </td>
	             <td width="80"  align="right"><? echo number_format($desc_value['recv_amt'],2); ?>  </td>
	             <?	}
				 if($rptType==3 || $rptType==1 )
					{

				 ?>
	             <td width="70"  align="right"><? echo number_format($desc_value['issue'],2); ?>  </td>
				 <?
				 if($rptType==3 )
				 {
					?>
					<td width="70"  align="center"><? echo $unit_of_measurement[$desc_value['cons_uom']]; ?>  </td>
					<?
				 }
				 ?>
	             <td width="50"><? echo number_format($desc_value['issue_amt']/$desc_value['issue'],2); ?>  </td>
	             <td width=""  align="right"><? echo number_format($desc_value['issue_amt'],2); ?>  </td>
	             <?
					}
				 ?>
	        </tr>
	        <?
				$total_rec_qty+=$desc_value['recv'];
				$total_rec_amt+=$desc_value['recv_amt'];
				$total_issue_qty+=$desc_value['issue'];
				$total_issue_amt+=$desc_value['issue_amt'];
				$k++;
				}
			}
			?>
	        <tfoot bgcolor="#D2D2D2">
	       		 <td colspan="3"> </td>
	             <?
	             if($rptType==2 || $rptType==1 )
					{ ?>
	         	 <td width="70" align="right"> <? echo number_format($total_rec_qty,2); ?></td>
	         	 <td width="60" align="right"> </td>
	             <td width="50"  align="right">  </td>
	             <td width="80"  align="right"><? echo number_format($total_rec_amt,2); ?>  </td>
	             <?
					}
					 if($rptType==3 || $rptType==1 )
					{
				 ?>
	             <td width="70"  align="right"><? echo number_format($total_issue_qty,2); ?>   </td>
				 <?
				 if($rptType==3)
				 {
					?>
					<td width="60"> </td>
					<?
				 }
				 ?>
	             <td width="50"> </td>
	             <td width=""  align="right"><? echo number_format($total_issue_amt,2); ?>   </td>

	             <?
					}
				 ?>
	        </tfoot>
	        </table>
	        <?
		}
		?>
		</div>
		<?
	}
	else if($cbo_item_cat==1) //issue button
	{
 		/*$receive_sql=sql_select("select a.id, a.recv_number, a.challan_no, a.supplier_id,a.knitting_source, a.knitting_company, a.currency_id, a.exchange_rate,  a.booking_id, a.booking_no, a.receive_basis from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category=1");
		foreach($receive_sql as $row)
		{
			$receive_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
			$receive_num_arr[$row[csf("id")]]["recv_number"]=$row[csf("recv_number")];
			$receive_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
			$receive_num_arr[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
			$receive_num_arr[$row[csf("id")]]["knitting_source"]=$row[csf("knitting_source")];
			$receive_num_arr[$row[csf("id")]]["knitting_company"]=$row[csf("knitting_company")];
			$receive_num_arr[$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
			$receive_num_arr[$row[csf("id")]]["exchange_rate"]=$row[csf("exchange_rate")];
			$receive_num_arr[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
			$receive_num_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
			$receive_num_arr[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];
		}

		$issue_sql=sql_select("select a.id, a.issue_number, a.challan_no, a.req_no, a.knit_dye_source, a.knit_dye_company from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category=1");
		foreach($issue_sql as $row)
		{
			$issue_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
			$issue_num_arr[$row[csf("id")]]["issue_number"]=$row[csf("issue_number")];
			$issue_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
			$issue_num_arr[$row[csf("id")]]["req_no"]=$row[csf("req_no")];
			$issue_num_arr[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
			$issue_num_arr[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
		}*/

		//$yarn_recv_buyer_arr=return_library_array( "select mst_id, buyer_id from inv_transaction where transaction_type in(1) and item_category=1",'mst_id','buyer_id');

		$sql_lc_pi = "select a.id,a.lc_number,b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$result_lc_pi = sql_select($sql_lc_pi);
		foreach ($result_lc_pi as  $row) {
			$pi_lc_data_arr[$row[csf("pi_id")]]=$row[csf("lc_number")];
			$lc_data_arr[$row[csf("id")]]=$row[csf("lc_number")];
		}


		if($rptType==1)
		{
			//  echo "select a.transfer_criteria,a.transfer_system_id, a.company_id,  b.from_store, b.to_store from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.entry_form=10 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
			$sql_yarn_trns_data=sql_select("select a.transfer_criteria,a.transfer_system_id, a.company_id,  b.from_store, b.to_store from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.entry_form=10 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

			$sql_yarn_trns_data_arr=array();
			foreach($sql_yarn_trns_data as $row)
			{
				$sql_yarn_trns_data_arr[$row[csf("transfer_system_id")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
				$sql_yarn_trns_data_arr[$row[csf("transfer_system_id")]]["from_store"]=$row[csf("from_store")];
				$sql_yarn_trns_data_arr[$row[csf("transfer_system_id")]]["to_store"]=$row[csf("to_store")];
			}
			//var_dump($sql_yarn_trns_data_arr);die;
			unset($sql_yarn_trns_data);
		}

		// $yarn_pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id=1",'id','pi_number');
		// //var_dump($yarn_pi_num_arr);//die;
		// $yarn_work_order_arr=return_library_array( "select id, wo_number from  wo_non_order_info_mst where item_category in(0,1)",'id','wo_number');
		// $yarn_dyeing_wo_arr=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where entry_form in(42,94,135,41,114)",'id','ydw_no');
		// $yarn_dyeing_pay_mood_arr=return_library_array( "select id, pay_mode from wo_yarn_dyeing_mst where entry_form in(42,94,135,41,114)",'id','pay_mode');
		// $btb_lc_arr=return_library_array( "select id, lc_number from com_btb_lc_master_details where item_category_id=1",'id','lc_number');
		$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=".$cbo_company_name."", "floor_room_rack_id", "floor_room_rack_name");

		$yarn_count_cond="";
		if($cbo_yarn_count!=0) $yarn_count_cond=" and b.yarn_count_id=$cbo_yarn_count";
		if($cbo_store_name>0) $yarn_count_cond.=" and a.store_id=$cbo_store_name";
		if($cbo_dyed_type>0) $yarn_count_cond.=" and b.dyed_type=$cbo_dyed_type";
		//echo $date_cond;die;
		if($rptType==1)
		{
			$sql="SELECT
						b.id as prod_id,
						a.id as trans_id,
						a.receive_basis,
						a.transaction_type,
						a.mst_id as rec_issue_id,
						a.transaction_date,
						case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
						case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
						case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,
						case when a.transaction_type in(3) then a.cons_quantity else 0 end as receive_ret_qty ,
						case when a.transaction_type in(4) then a.cons_quantity else 0 end as issue_ret_qty ,
						a.cons_uom,
						b.yarn_comp_type1st as yarn_comp_type1st,
						b.yarn_comp_percent1st as yarn_comp_percent1st,
						b.yarn_comp_type2nd as yarn_comp_type2nd,
						b.yarn_comp_percent2nd  as yarn_comp_percent2nd,
						b.lot,
						b.supplier_id,
						b.yarn_count_id,
						b.yarn_type,
						b.color,
						a.cons_rate,
						a.cons_amount,
						a.inserted_by,

						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						a.order_rate,
						a.store_id,
						a.floor_id,
						a.room,
						a.pi_wo_batch_no
					from
						inv_transaction a, product_details_master b
					where
						a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,2,3,4,5,6) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 $date_cond $yarn_count_cond order by a.transaction_date, a.id";
		}
		else if($rptType==2)
		{
			$sql="SELECT b.id as prod_id, a.id as trans_id, a.receive_basis, a.transaction_type, a.RD_NO as tc_no,a.mst_id as rec_issue_id, a.transaction_date, a.buyer_id, case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty , case when a.transaction_type in(3) then a.cons_quantity else 0 end as receive_ret_qty, a.cons_uom, b.yarn_comp_type1st as yarn_comp_type1st, b.yarn_comp_percent1st as yarn_comp_percent1st, b.yarn_comp_type2nd as yarn_comp_type2nd, b.yarn_comp_percent2nd as yarn_comp_percent2nd, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.cons_rate, a.cons_amount, a.inserted_by, $select_insert_date, $select_insert_time, a.order_qnty, a.order_rate, a.store_id, a.floor_id, a.room,a.pi_wo_batch_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5,3)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $supplier_cond $yarn_count_cond order by a.transaction_date, a.id";
		}
		else if($rptType==3)
		{
			if($cbo_dyed_type>0) $dyed_cond=" and b.dyed_type=$cbo_dyed_type";
			$sql="SELECT b.id as prod_id, a.id as trans_id, a.receive_basis, a.buyer_id, a.requisition_no, a.transaction_type, a.mst_id as rec_issue_id, a.transaction_date, case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty , a.return_qnty as return_qnty , case when a.transaction_type in(4) then a.cons_quantity else 0 end as issue_ret_qty , a.cons_uom, b.yarn_comp_type1st as yarn_comp_type1st, b.yarn_comp_percent1st as yarn_comp_percent1st, b.yarn_comp_type2nd as yarn_comp_type2nd, b.yarn_comp_percent2nd as yarn_comp_percent2nd, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.cons_rate, a.cons_amount, a.inserted_by, $select_insert_date, $select_insert_time, a.order_qnty, a.order_rate, a.store_id, a.floor_id, a.room, a.cons_reject_qnty, a.issue_id, a.btb_lc_id
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6,4) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 $date_cond $yarn_count_cond order by a.transaction_date, a.id";
			//echo $sql;
		}

		else if($rptType==5)
		{
			if($cbo_dyed_type>0) $dyed_cond=" and b.dyed_type=$cbo_dyed_type";
			$sql="select
						b.id as prod_id,
						a.id as trans_id,
						a.receive_basis,
						a.buyer_id,
						a.requisition_no,
						a.transaction_type,
						a.mst_id as rec_issue_id,
						a.transaction_date,
						case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
						a.return_qnty as return_qnty ,
						case when a.transaction_type in(4) then a.cons_quantity else 0 end as issue_ret_qty ,
						a.cons_uom,
						b.yarn_comp_type1st as yarn_comp_type1st,
						b.yarn_comp_percent1st as yarn_comp_percent1st,
						b.yarn_comp_type2nd as yarn_comp_type2nd,
						b.yarn_comp_percent2nd as yarn_comp_percent2nd,
						b.lot,
						b.supplier_id,
						b.yarn_count_id,
						b.yarn_type,
						b.color,
						a.cons_rate,
						a.cons_amount,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						a.order_rate,
						a.store_id,
						a.floor_id,
						a.room
					from
						inv_transaction a, product_details_master b
					where
						a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6,4) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 $date_cond $yarn_count_cond order by a.transaction_date, a.id";
		}

		//echo $sql;die();
		//$sql_result=sql_select($sql);
		//var_dump($issue_data_arr);die;
		if($rptType==1)
		{
			$table_width=3530;
			$div_width="3550px";
		}
		else if($rptType==2)
		{
			$table_width=2900;
			$div_width="2920px";
		}
		else if($rptType==5)
		{
			$table_width=2730;
			$div_width="2750px";
		}
		else
		{
			$table_width=2990;
			$div_width="3010px";
		}
		// echo $sql;
		$sql_result=sql_select($sql);

		foreach($sql_result as $row)
		{
			if ($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4){
                $receive_ids_arr[$row["REC_ISSUE_ID"]]=$row["REC_ISSUE_ID"];
            } else if ($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3) {
                $issue_ids_arr[$row["REC_ISSUE_ID"]]=$row["REC_ISSUE_ID"];
            } else $transfer_ids_arr[$row["REC_ISSUE_ID"]]=$row["REC_ISSUE_ID"];
		}

		foreach($sql_result as $row)
		{
			if ($row["TRANSACTION_TYPE"]==4){
                $issue_return_arr[$row["ISSUE_ID"]]=$row["ISSUE_ID"];
			}
		}

		// echo "<pre>";
		// print_r($receive_ids_arr);die;

		if (count($receive_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 12, $receive_ids_arr, $empty_arr);
			$sql_receive=sql_select("SELECT A.ID,A.ENTRY_FORM,A.RECV_NUMBER,A.KNITTING_SOURCE,A.KNITTING_COMPANY,A.RECEIVE_BASIS,A.BOOKING_NO,A.BUYER_ID,
			A.RECEIVE_PURPOSE,A.SUPPLIER_ID,A.BOOKING_ID,A.CURRENCY_ID,A.EXCHANGE_RATE,A.CHALLAN_NO,A.LC_NO,A.REMARKS,A.LOAN_PARTY,A.IS_POSTED_ACCOUNT
			FROM INV_RECEIVE_MASTER A, GBL_TEMP_ENGINE B
			WHERE A.ID=B.REF_VAL AND  A.COMPANY_ID=$cbo_company_name AND A.ITEM_CATEGORY=1 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=12");

			// echo "<pre>";
			// print_r($sql_receive);die;

			$receive_buyer_arr = array();
			foreach($sql_receive as $row)
			{
				$receive_buyer_arr[$row["ID"]]=$row["ID"];
			}
			if (count($receive_buyer_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 15, $receive_buyer_arr, $empty_arr);
				$yarn_buyer_sql=sql_select("SELECT A.MST_ID, A.BUYER_ID FROM INV_TRANSACTION A, GBL_TEMP_ENGINE B WHERE A.ID = B.REF_VAL AND A.ITEM_CATEGORY = 1 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=15");

				foreach($yarn_buyer_sql as $row){
					$yarnBuyerArr[$row['ID']] = $row['BUYER_ID'];
				}
			}

			$receive_data_arr=array();
			foreach($sql_receive as $row)
			{
				$receive_data_arr[$row[csf("id")]]['rec_id']=$row[csf("id")];
				$receive_data_arr[$row[csf("id")]]['recv_number']=$row[csf("recv_number")];
				$receive_data_arr[$row[csf("id")]]['receive_basis']=$row[csf("receive_basis")];
				$receive_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
				$receive_data_arr[$row[csf("id")]]['receive_purpose']=$row[csf("receive_purpose")];
				$receive_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
				$receive_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
				$receive_data_arr[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
				$receive_data_arr[$row[csf("id")]]['exchange_rate']=$row[csf("exchange_rate")];
				$receive_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
				$receive_data_arr[$row[csf("id")]]['lc_no']=$row[csf("lc_no")];
				$receive_data_arr[$row[csf("id")]]['knitting_source']=$row[csf("knitting_source")];
				$receive_data_arr[$row[csf("id")]]['knitting_company']=$row[csf("knitting_company")];
				$receive_data_arr[$row[csf("id")]]['remarks']=$row[csf("remarks")];
				$receive_data_arr[$row[csf("id")]]['buyer']=$yarnBuyerArr[$row[csf("id")]];
				$receive_data_arr[$row[csf("id")]]['loan_party']=$row[csf("loan_party")];
				$receive_data_arr[$row[csf("id")]]['is_posted_account']=$row[csf("is_posted_account")];

				if($row[csf("receive_basis")]==1)
				{
					$receive_data_arr[$row[csf("id")]]['pi_id']=$row[csf("booking_id")];
				}
				else
				{
					$receive_data_arr[$row[csf("id")]]['pi_id']=0;
				}
			}
		}

		if (count($issue_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 13, $issue_ids_arr, $empty_arr);
			$sql_issue=sql_select("SELECT A.ID,A.ENTRY_FORM,A.ISSUE_NUMBER,A.ISSUE_BASIS,A.BOOKING_NO,A.ISSUE_PURPOSE,A.SUPPLIER_ID,A.BOOKING_ID,A.REMARKS,A.KNIT_DYE_COMPANY AS ISSUE_TO,A.KNIT_DYE_SOURCE,A.CHALLAN_NO,A.RECEIVED_ID, A.BUYER_ID,A.LOAN_PARTY,A.IS_POSTED_ACCOUNT FROM INV_ISSUE_MASTER A,GBL_TEMP_ENGINE B
			WHERE  A.ID=B.REF_VAL AND A.COMPANY_ID=$cbo_company_name AND A.ITEM_CATEGORY=1  AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=13");

			$issue_data_arr=array();
			foreach($sql_issue as $row)
			{
				$issue_data_arr[$row[csf("id")]]['issue_id']=$row[csf("id")];
				$issue_data_arr[$row[csf("id")]]['issue_number']=$row[csf("issue_number")];
				$issue_data_arr[$row[csf("id")]]['issue_basis']=$row[csf("issue_basis")];
				$issue_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
				$issue_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
				$issue_data_arr[$row[csf("id")]]['issue_purpose']=$row[csf("issue_purpose")];
				$issue_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
				$issue_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
				$issue_data_arr[$row[csf("id")]]['knit_dye_source']=$row[csf("knit_dye_source")];
				$issue_data_arr[$row[csf("id")]]['issue_to']=$row[csf("issue_to")];
				$issue_data_arr[$row[csf("id")]]['remarks']=$row[csf("remarks")];
				$issue_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
				$issue_data_arr[$row[csf("id")]]['received_id']=$row[csf("received_id")];
				$issue_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
				$issue_data_arr[$row[csf("id")]]['loan_party']=$row[csf("loan_party")];
				$issue_data_arr[$row[csf("id")]]['is_posted_account']=$row[csf("is_posted_account")];
			}
		}

		if (count($issue_return_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 34, $issue_return_arr, $empty_arr);
			$sql_issue=sql_select("SELECT A.ID,A.ENTRY_FORM,A.ISSUE_NUMBER,A.ISSUE_BASIS, A.BUYER_ID 
			FROM INV_ISSUE_MASTER A,GBL_TEMP_ENGINE B
			WHERE  A.ID=B.REF_VAL AND A.COMPANY_ID=$cbo_company_name AND A.ITEM_CATEGORY=1  AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=34");

			$issue_return_data_arr=array();
			foreach($sql_issue as $row)
			{
				$issue_return_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
			}
		}

		if (count($transfer_ids_arr)>0)
        {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 14, $transfer_ids_arr, $empty_arr);
			$transfer_sql=sql_select("SELECT A.ID, A.TRANSFER_SYSTEM_ID, A.CHALLAN_NO, A.TRANSFER_DATE, A.REMARKS,A.IS_POSTED_ACCOUNT,a.to_company as TO_COMPANY FROM INV_ITEM_TRANSFER_MST A, INV_TRANSACTION B , GBL_TEMP_ENGINE  C
			WHERE A.ID=B.MST_ID AND A.ID=C.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.ITEM_CATEGORY=1
			AND C.USER_ID= $user_id AND C.ENTRY_FORM=27 AND C.REF_FROM=14");
			foreach($transfer_sql as $row)
			{
				$transfer_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
				$transfer_num_arr[$row[csf("id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
				$transfer_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
				$transfer_num_arr[$row[csf("id")]]["transfer_date"]=$row[csf("transfer_date")];
				$transfer_num_arr[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
				$transfer_num_arr[$row[csf("id")]]["issue_to"]=$row[csf("to_company")];

			}
		}

		if(count($issue_ids_arr)>0)
        {
			if($rptType==3)
			{
				$sql_get_pass_yrn="SELECT A.ID,A.ISSUE_ID,A.CHALLAN_NO,A.SYS_NUMBER
				FROM INV_GATE_PASS_MST A, GBL_TEMP_ENGINE B
				WHERE A.ISSUE_ID=CAST(B.REF_VAL AS VARCHAR2(100)) AND A.COMPANY_ID=$cbo_company_name AND A.BASIS=2 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND  B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=13";

				$sql_get_pass_yrn_data = sql_select($sql_get_pass_yrn);
				$sql_get_pass_yrn_arr=array();
				foreach($sql_get_pass_yrn_data as $row)
				{
					$sql_get_pass_yrn_arr[$row["ISSUE_ID"]]["sys_number"]=$row["SYS_NUMBER"];
				}
				unset($sql_get_pass_yrn);
				//print_r($sql_get_pass_yrn_arr);die;
			}
		}

		$appro_hist=sql_select("SELECT max( a.approved_date) as dates ,b.issue_number  from approval_history a, inv_issue_master b where b.id=a.mst_id and  a.entry_form=14 and b.item_category=1 and b.entry_form=3 and b.status_active=1 group by b.issue_number");
		$appro_hist_arr=array();
		foreach($appro_hist as $vals)
		{
			$appro_hist_arr[$vals[csf("issue_number")]]=$vals[csf("dates")];
		}
		//print_r($appro_hist_arr);die;
		ob_start();
		?>

		<div style="width:<? echo $div_width; ?>">
	    	<fieldset style="width:<? echo $div_width; ?>;">
				<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                    <tr class="form_caption" style="border:none;">
                        <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
                    </tr>
                    <tr style="border:none;">
                            <td colspan="14" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                            </td>
                    </tr>
               </table>
               <br />
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="60" >Prod. Id</th>
							<?
							if($rptType==5)
							{
								?>
								<th width="120" >Approved Date & Time</th>
								<?
							}
							?>
	                        <th width="100" >Store Names</th>
	                        <? if($rptType==1) {?>
	                        <th width="80" >Floor</th>
	                        <th width="80" >Room</th>
	                        <? } ?>
							<th width="70" >Trans. Date</th>
							<th width="130" >Trans. Ref.</th>
							<? if($rptType==3) {?>
							<th width="130" >Gate Pass No</th>
							<? } ?>
	                        <th width="100" >Challan No</th>
	                        <? if($rptType==2 || $rptType==3) {?>
	                        <th width="100">Buyer</th>
	                        <?
							}
							if($rptType==3) {?>
	                        <th width="100">Style Ref. No</th>
	                        <th width="100">Sales Order No</th>
	                        <th width="100">Booking No</th>
	                        <? }
							if($rptType==5) {?>
	                        <th width="100">Book./Req. No</th>
	                        <? }
							 ?>
	                        <th width="120"><? echo $caption = ($rptType==3 || $rptType==5)?" Supplier Name ":" Party Name "; ?></th>
	                        <th width="100">Yarn Lot</th>
							<th width="100">TC NO</th>
	                        <th width="100">Yarn Count</th>
							<th width="150">Composition</th>
							<th width="90">Yarn Type</th>
							<th width="100">Color</th>
	                        <th width="100">Basis</th>
	                        <th width="100">WO/PI NO.</th>
	                        <th width="100">BTB LC NO.</th>
	                        <th width="100">Accounting Posting</th>
	                        <th width="120">Purpose</th>
							<?
							if($rptType==1)
							{   ?>
								<th width="120">Transfer Criteria</th>
								<th width="120">From Store</th>
								<th width="120">To Store</th>
								<?
							} ?>
	                        <? if($rptType==3 || $rptType==5)
							{
								?>
	                            <th width="145">Issue To</th>
	                            <?
							}

							if($rptType==2 || $rptType==1 )
							{
								?>
	                            <th width="80">Currency</th>
	                            <th width="80">Exchange Rate</th>
	                            <th width="80">Actual Rate</th>
								<th width="80">Receive Qty</th>
								<th width="80">Receive Return Qty</th>
	                            <th width="80">Actual Amt</th>
	                            <?
							}
							if($rptType==3 || $rptType==1  || $rptType==5)
							{
								?>
								<th width="80">Issue Qty</th>
	                            <th width="80">Issue Return Qty</th>
								<th width="80">Reject Qty</th>
	                            <th width="80">Returnable Qty</th>
	                            <?
							}
							?>
	                        <th width="80">Rate(TK)</th>
	                        <th width="100">Amount(TK)</th>
	                        <th width="110">User</th>
	                        <th width="140">Insert Date</th>
	                        <th width="100">Remarks..</th>
						</tr>
					</thead>
			   </table>
			  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:320px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$book_sql=sql_select("select booking_no, buyer_id from wo_booking_mst where booking_type=1 and status_active=1 and is_deleted=0
					union all
					select booking_no, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
					foreach($book_sql as $row)
					{
						$bookingArr[$row[csf("booking_no")]]=$row[csf("buyer_id")];
					}
					//$bookingArr = return_library_array("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0","booking_no","buyer_id");

					$yarnDyeingArr=sql_select("select a.id, a.pay_mode, a.ydw_no, b.job_no as sales_order_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");
					foreach($yarnDyeingArr as $row)
					{
						$YarnfsoArr[$row[csf("ydw_no")]]=$row[csf("sales_order_no")];
						$YarnDyingSupplierArr[$row[csf("id")]]=$row[csf("pay_mode")];
					}

					$sql_sales= "select a.id, a.sales_booking_no, a.job_no, a.within_group, a.buyer_id, a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0";
					$result_sales=sql_select($sql_sales);
					foreach($result_sales as $row)
					{
						$sales_numArr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
						$sales_numArr[$row[csf('sales_booking_no')]]['job_no']=$row[csf('job_no')];
						$sales_numArr[$row[csf('sales_booking_no')]]['buyer_id']=$row[csf('buyer_id')];
						$sales_numArr[$row[csf('sales_booking_no')]]['style_ref_no']=$row[csf('style_ref_no')];
						$sales_numArr2[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
						$sales_numArr2[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
					}

					$sql_requ= "select a.dtls_id, a.booking_no, b.requisition_no from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 and a.is_sales=1 ";
					$result_requ=sql_select($sql_requ);
					foreach($result_requ as $row)
					{
						$requisition_numArr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
						$requisition_numArr[$row[csf('requisition_no')]]['dtls_id']=$row[csf('dtls_id')];
						//$sales_numArr[$row[csf('requisition_no')]]['buyer_id']=$row[csf('buyer_id')];
					}

					$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
					$i=1;$total_receive="";$total_issue="";

					$yarn_pi_num_arr = array();
					$yarn_dyeing_wo_arr = array();
					$yarn_work_order_arr = array();
					foreach($sql_result as $val)
					{
						if($val[csf("transaction_type")]==1 || $val[csf("transaction_type")]==4 )
						{
							if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']!=9)
							{
								if($val[csf("receive_basis")]==1)
								{
									$yarn_pi_num_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']] = $receive_data_arr[$val[csf("rec_issue_id")]]['booking_id'];
								}
								if($val[csf("receive_basis")]==2)
								{
									if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==2 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==15 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==38 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==50 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==51 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==46 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==12 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==7)
									{
										$yarn_dyeing_wo_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']] = $receive_data_arr[$val[csf("rec_issue_id")]]['booking_id'];
									}else{
										$yarn_work_order_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']] = $receive_data_arr[$val[csf("rec_issue_id")]]['booking_id'];
									}
								}
							}
						}
					}


					if(!empty($yarn_pi_num_arr))
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 9, $yarn_pi_num_arr, $empty_arr);
						$yarn_pi_sql=sql_select("SELECT A.ID, A.PI_NUMBER FROM COM_PI_MASTER_DETAILS A, GBL_TEMP_ENGINE B WHERE A.ID = B.REF_VAL AND A.ITEM_CATEGORY_ID=1 AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=9");
						foreach($yarn_pi_sql as $row){
							$yarnNumArr[$row['ID']] = $row['PI_NUMBER'];
						}
					}

					if(!empty($yarn_dyeing_wo_arr))
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 10, $yarn_dyeing_wo_arr, $empty_arr);
						$yarn_dyeing_sql=sql_select("SELECT A.ID, A.YDW_NO FROM WO_YARN_DYEING_MST A, GBL_TEMP_ENGINE B WHERE A.ID = B.REF_VAL AND A.ENTRY_FORM IN(42,94,135,41,114) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=10");
						foreach($yarn_dyeing_sql as $row){
							$yarnDyingArr[$row['ID']] = $row['YDW_NO'];
						}
					}

					if(!empty($yarn_work_order_arr))
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 27, 11, $yarn_work_order_arr, $empty_arr);
						$yarn_wo_sql=sql_select("SELECT A.ID, A.WO_NUMBER FROM WO_NON_ORDER_INFO_MST A, GBL_TEMP_ENGINE B WHERE A.ID = B.REF_VAL AND A.ITEM_CATEGORY IN(0,1) AND B.USER_ID= $user_id AND B.ENTRY_FORM=27 AND B.REF_FROM=1");
						foreach($yarn_wo_sql as $row){
							$yarnWoArr[$row['ID']] = $row['WO_NUMBER'];
						}
					}


					//var_dump($sql_result);
					// echo "<pre>";
					// print_r($sql_result); die;
					foreach($sql_result as $val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$receive_basis=$val[csf("receive_basis")];
						//$buyer_job_no=$val[csf('buyer_job_no')];
						//$booking_no=$val[csf('booking_no')];
						$buyer_name=$buyer_id="";
						if($rptType==3 || $rptType==2)
						{
							if($receive_basis==3) //Requisition
							{
								if($val[csf("transaction_type")]==1 || $val[csf("transaction_type")]==4) //Recv
								{
									$requisition_no=$receive_data_arr[$val[csf('rec_issue_id')]]['booking_id'];
								}
								else
								{
									$requisition_no=$val[csf('requisition_no')];
								}
								$booking_no=$requisition_numArr[$requisition_no]['booking_no'];
								$sales_booking_no=$sales_numArr[$booking_no]['job_no'];
								$style_ref_no=$sales_numArr[$booking_no]['style_ref_no'];
								$within_group=$sales_numArr[$booking_no]['within_group'];

								if($within_group==1) //Yes
								{
									$buyer_id=$bookingArr[$booking_no];
									//echo $buyer_id."=".$within_group."=".$booking_no.test;die;
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									if ($val[csf("transaction_type")]==4)
									{
										//if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('issue_id')]]['buyer_id'];
										if($buyer_id<1) $buyer_id=$issue_return_data_arr[$val[csf('issue_id')]]['buyer_id'];
									}
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
								else
								{
									$buyer_id=$sales_numArr[$booking_no]['buyer_id'];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									if ($val[csf("transaction_type")]==4)
									{
										//if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('issue_id')]]['buyer_id'];
										if($buyer_id<1) $buyer_id=$issue_return_data_arr[$val[csf('issue_id')]]['buyer_id'];
									}
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
							}
							else if($receive_basis==1 && $val[csf("transaction_type")]==2 ) //Booking///Issue
							{
								//$requisition_no=$receive_data_arr[$val[csf('rec_issue_id')]]['booking_id'];
								//$booking_no=$val[csf('booking_no')];
								$booking_no=$issue_data_arr[$val[csf('rec_issue_id')]]['booking_no'];
								$sales_booking_no=$sales_numArr[$booking_no]['job_no'];
								$style_ref_no=$sales_numArr[$booking_no]['style_ref_no'];
								$within_group=$sales_numArr[$booking_no]['within_group'];
								$receive_purpose=$issue_data_arr[$val[csf('rec_issue_id')]]['issue_purpose'];
								if($receive_purpose==2) //Issue//Yarn Dyeing
								{
									$sales_booking_no=$YarnfsoArr[$booking_no];
									$fso_booking_no=$sales_numArr2[$sales_booking_no]['sales_booking_no'];
									$within_group=$sales_numArr[$fso_booking_no]['within_group'];
									if($within_group==1) //Yes
									{
										$buyer_id=$bookingArr[$fso_booking_no];
										if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
										$buyer_name=$buyer_short_arr[$buyer_id];
									}
									else
									{
										$buyer_id=$sales_numArr[$fso_booking_no]['buyer_id'];
										if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
										$buyer_name=$buyer_short_arr[$buyer_id];
									}
								}
								else if($within_group==1 && $receive_purpose!=2) //Yes
								{
									$buyer_id=$bookingArr[$booking_no];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
								else
								{   $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
							}
							else if($receive_basis==1 && $val[csf("transaction_type")]==1 ) //PI///Recv
							{
								$buyer_id=$val[csf('buyer_id')];
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
							else if($val[csf("transaction_type")]==3 ) //Recv Ret
							{

								$received_ids=$issue_data_arr[$val[csf("rec_issue_id")]]['received_id'];
								$buyer_id=$receive_data_arr[$received_ids]['buyer'];
								$buyer_name=$buyer_short_arr[$buyer_id];
								//echo $buyer_id.'ddd';
							}
							else
							{
								if($val[csf("transaction_type")]==1)
								{
									$buyer_id=$val[csf('buyer_id')];
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
								elseif($val[csf("transaction_type")]==4)
								{
									$buyer_id=$issue_return_data_arr[$val[csf('issue_id')]]['buyer_id'];
									$buyer_name=$buyer_short_arr[$buyer_id]; 
								}
								else
								{
									$receive_purpose=$issue_data_arr[$val[csf('rec_issue_id')]]['issue_purpose'];
									$booking_no=$val[csf('booking_no')];
									if($receive_purpose==2) //Issue//Yarn Dyeing
									{
										$buyer_id=$sales_numArr[$booking_no]['buyer_id'];
										if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									}
									else
									{
										$buyer_id=$bookingArr[$booking_no];
										if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									}
									$buyer_name=$buyer_short_arr[$val[csf("buyer_id")]];
								}
								$sales_booking_no='';$style_ref_no='';
							}
						}
						else if($rptType==5)
						{
							if($val[csf('receive_basis')]==1)

							{
								$booking_no=$issue_data_arr[$val[csf('rec_issue_id')]]['booking_no'];
							}
							else
							{
								$booking_no=$val[csf('requisition_no')];
							}

						}
						else
						{
							$buyer_name=$buyer_short_arr[$val[csf('buyer_id')]];
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $val[csf("prod_id")]; ?>&nbsp;</p></td>
							<?
							if($rptType==5)
							{
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 )
								{
									$sys_id= $receive_data_arr[$val[csf('rec_issue_id')]]["recv_number"];

								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3)
								{
									$sys_id= $issue_data_arr[$val[csf('rec_issue_id')]]["issue_number"];

								}
								else if($val[csf("transaction_type")]==5  || $val[csf("transaction_type")]==6)
								{
									$sys_id= $transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"];
								}


								?>
								<td width="120"><p><? $date_arr=explode(" ", $appro_hist_arr[trim($sys_id)]);
								echo change_date_format($date_arr[0])." ".$date_arr[1]." ".$date_arr[2];?></p></td>
								<?
							}
	                        ?>

	                        <td width="100"><p><? echo $store_library[$val[csf("store_id")]]; ?></p></td>
	                        <? if($rptType==1) { ?>
	                        <td width="80" title="<? echo $val[csf("floor_id")];?>"><p><? echo $floorRoomRackShelf_array[$val[csf("floor_id")]]; ?></p></td>
	                        <td width="80" title="<? echo $val[csf("room")]; ?>"><p><? echo $floorRoomRackShelf_array[$val[csf("room")]]; ?></p></td>
	                        <? } ?>
							<td width="70" align="center"><p><? if($val[csf("transaction_date")]!="0000-00-00") echo change_date_format($val[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="130"  align="center" title = "mst_id: <? echo $val[csf('rec_issue_id')];?>"><p>
							<?
								//$issue_no_pro ="";
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 )
								{
									echo $receive_data_arr[$val[csf('rec_issue_id')]]["recv_number"];
									//$issue_no_pro = $receive_data_arr[$val[csf('rec_issue_id')]]["recv_number"];
									$remarks=$receive_data_arr[$val[csf('rec_issue_id')]]["remarks"];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3)
								{
									echo $issue_data_arr[$val[csf('rec_issue_id')]]["issue_number"];
									//$issue_no_pro = $issue_data_arr[$val[csf('rec_issue_id')]]["issue_number"];
									$remarks=$issue_data_arr[$val[csf('rec_issue_id')]]["remarks"];
									//echo $val[csf('rec_issue_id')];
								}
								else if($val[csf("transaction_type")]==5  || $val[csf("transaction_type")]==6)
								{
									echo $transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"];
									//$issue_no_pro = $transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"];
									$remarks=$transfer_num_arr[$val[csf('rec_issue_id')]]["remarks"];
								}
								//echo $val[csf("transaction_type")];

							?>
							</p></td>
							<?
	                        if($rptType==3)
							{
								?>
							<td width="130"  align="center"><p><? echo $sql_get_pass_yrn_arr[$val[csf('rec_issue_id')]]["sys_number"];?></p></td>

							<?
							}
								?>

	                        <td width="100"><p>
							<?
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 )
								{
									echo $receive_data_arr[$val[csf('rec_issue_id')]]["challan_no"];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3)
								{
									echo $issue_data_arr[$val[csf('rec_issue_id')]]["challan_no"];
									//echo $val[csf('rec_issue_id')];
								}
								else if($val[csf("transaction_type")]==5  || $val[csf("transaction_type")]==6)
								{
									echo $transfer_num_arr[$val[csf('rec_issue_id')]]["challan_no"];
									//echo $val[csf('rec_issue_id')];
								}

							?></p></td?>
	                        <?
	                        if($rptType==2 || $rptType==3)
							{
								?>
	                            <td width="100" align="center"><p><? echo $buyer_name; //echo $buyer_short_arr[$val[csf("buyer_id")]]; ?> </p></td>
	                            <?
							}
	                        if($rptType==3)
							{
								?>
								<td width="100" align="center"><p><? echo $style_ref_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $sales_booking_no; ?></p></td>
	                            <td width="100" align="center"><p><?  echo $booking_no;?> </p></td>
	                            <?
							}
							if($rptType==5)
							{
								?>
	                            <td width="100" align="center"><p><?  echo $booking_no;?></p></td>
	                            <?
							}
							?>
	                        <td width="120" align="center" style="word-break:break-all"><p>
							<?
								/*if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 || $val[csf("transaction_type")]==5)
								{
									echo $supplier_arr[$receive_data_arr[$val[csf('rec_issue_id')]]["supplier_id"]];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3 || $val[csf("transaction_type")]==6)
								{
									echo $supplier_arr[$issue_data_arr[$val[csf('rec_issue_id')]]["supplier_id"]];
								}*/
								if($val[csf("receive_basis")]==2)
								{
									if( $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==7 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==12 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==15 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==38 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==46 ||  $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==50 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==51)
									{
										if($YarnDyingSupplierArr[$val[csf('pi_wo_batch_no')]]==3 || $YarnDyingSupplierArr[$val[csf('pi_wo_batch_no')]]==5)
										{
											echo $company_arr[$val[csf("supplier_id")]];//.'@A@'
										}
										else
										{
											echo $supplier_arr[$val[csf("supplier_id")]];//.'@AA@'
										}
										//echo $company_arr[$val[csf("supplier_id")]].'@A@';
									}
									else if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==2 )
									{
										if($YarnDyingSupplierArr[$val[csf('pi_wo_batch_no')]]== 3 || $YarnDyingSupplierArr[$val[csf('pi_wo_batch_no')]]==5)
										{
											echo $company_arr[$val[csf("supplier_id")]];//.'@B@'
										}
										else
										{
											echo $supplier_arr[$val[csf("supplier_id")]];//.'@C@'
										}
									}
									else if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==16)
									{
										echo $supplier_arr[$val[csf("supplier_id")]];//.'@D@'
									}
									else if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==5)
									{
										echo $supplier_arr[$receive_data_arr[$val[csf('rec_issue_id')]]['loan_party']];//.'==E'
									}
									else if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==6)
									{
										echo $supplier_arr[$val[csf("supplier_id")]];//.'==Y=='
									}
									else{
										echo $supplier_arr[$val[csf("supplier_id")]];//.'==YIR=='
									}

								}
								else
								{
									echo $supplier_arr[$val[csf("supplier_id")]];//.'@F@'
								}
								//echo $val[csf("receive_basis")].'*'.$receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'].'#'.$YarnDyingSupplierArr[$val[csf('pi_wo_batch_no')]];
							?></p></td>
	                        <td width="100" align="center" style="mso-number-format:'\@';"><? echo $val[csf("lot")]; ?>&nbsp;</td>
							<td width="100" align="center" style="mso-number-format:'\@';"><? echo $val["TC_NO"]; ?>&nbsp;</td>
	                        <td width="100" align="center" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$val[csf("yarn_count_id")]]; ?>&nbsp;</td>
	                        <td width="150"><p><?
							if($val[csf("yarn_comp_percent1st")]!=0) {$parcent1st=$val[csf("yarn_comp_percent1st")]."%";} else {$parcent1st="";}
							if($val[csf("yarn_comp_percent2nd")]!=0 ){ $parcent2nd=$val[csf("yarn_comp_percent2nd")]."%";} else {$parcent2nd="";}
							 echo $composition[$val[csf("yarn_comp_type1st")]].' '.$parcent1st.' '.$composition[$val[csf("yarn_comp_type2nd")]].' '.$parcent2nd;
							 ?></p></td>
	                        <td width="90"><p><? echo $yarn_type[$val[csf("yarn_type")]]; ?></p></td>
	                        <td width="100"><p><? echo $color_arr[$val[csf("color")]]; ?></p></td>
							<?
							if($val[csf("transaction_type")]==1 || $val[csf("transaction_type")]==4 )
							{
								?>
								<td width="100"><p>
								<?
								if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']!=9)
								{
									if($receive_data_arr[$val[csf("rec_issue_id")]]['receive_basis']!=3)
									{
										echo $receive_basis_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_basis']];
									}

								}
								?></p></td>
								<td width="100"><p>
								<?
								$knitting_soucre=$receive_data_arr[$val[csf("rec_issue_id")]]['knitting_source'];
								$knitting_company=$receive_data_arr[$val[csf("rec_issue_id")]]['knitting_company'];


								 if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']==9)
								 {
									 if($knitting_soucre==1)
									 {
										$supplier_name=$company_arr[$knitting_company];
									 }
									 else
									 {
										$supplier_name=$supplier_arr[$knitting_company];
									 }

									 echo "Issue Return".'('.$supplier_name.')';
								 }
								 else
								 {
									 if($val[csf("receive_basis")]==1) echo $yarnNumArr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
									 if($val[csf("receive_basis")]==2){
										 if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==2 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==15 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==38 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==50 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==51 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==46 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==12 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==7){
											 echo $yarnDyingArr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
										 }else{
										 	echo $yarnWoArr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
										 }
									 }
								 }
								?></p></td>
								<td width="100"><p>&nbsp;<? echo  $pi_lc_data_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['pi_id']]; ?></p></td>
								<td width="100"><p><? echo  $yes_no[$receive_data_arr[$val[csf("rec_issue_id")]]['is_posted_account']]; ?></p></td>
	                            <td width="120" style="word-break:break-all"><p><? echo $yarn_issue_purpose[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_purpose']]; ?></p></td>
								<?
							}
							else if($val[csf("transaction_type")]==2 || $val[csf("transaction_type")]==3 )
							{
								?>
								<td width="100"><p><? echo $issue_basis[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_basis']];?></p></td>
								<td width="100"><p> <? //if($val[csf("receive_basis")]==1) echo $yarn_booking_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
								//if($val[csf("receive_basis")]==2) echo $yarn_work_order_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
								//if($val[csf("receive_basis")]==3) echo $yarn_requisition_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']]; ?></p></td>
	                            <td width="100">&nbsp;<? echo $lc_data_arr[$val[csf("btb_lc_id")]];?></td>
	                            <td width="100"><p><? echo  $yes_no[$issue_data_arr[$val[csf("rec_issue_id")]]['is_posted_account']]; ?></p></td>
								<td width="120" style="word-break:break-all"><p><? echo $yarn_issue_purpose[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_purpose']]; ?></p></td>
								<?
							}
							else if($val[csf("transaction_type")]==5 || $val[csf("transaction_type")]==6 )
							{
								?>
								<td width="100"><p><? echo "Item Transfer";?></p></td>
								<td width="100"><p></p></td>
	                            <td width="100"><p></p></td>
	                            <td width="100">
	                            	<p><?
	                            	if($transfer_num_arr[$val[csf("rec_issue_id")]]['is_posted_account']>0)
	                            	{
	                            		$transfer_num_arr[$val[csf("rec_issue_id")]]['is_posted_account']=1;
	                            	}
	                            	echo  $yes_no[$transfer_num_arr[$val[csf("rec_issue_id")]]['is_posted_account']]; ?></p>
	                            </td>
								<td width="120" style="word-break:break-all"><p><? echo "Item Transfer";?></p></td>
								<?
							}
							if($rptType==1)
							{
								?>
								<td width="120" style="word-break:break-all"><p>
								<?
								if($val[csf("transaction_type")]==5 || $val[csf("transaction_type")]==6 )
								{
									echo $item_transfer_criteria[$sql_yarn_trns_data_arr[$transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"]]["transfer_criteria"]];

								}?>
								</p></td>
								<td width="120" style="word-break:break-all"><p>
								<?
								if($val[csf("transaction_type")]==5 || $val[csf("transaction_type")]==6 )
								{
									echo $store_library[$sql_yarn_trns_data_arr[$transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"]]["from_store"]];

								}?>
								</p></td>
								<td width="120" style="word-break:break-all"><p>
								<?
								if($val[csf("transaction_type")]==5 || $val[csf("transaction_type")]==6 )
								{
									echo $store_library[$sql_yarn_trns_data_arr[$transfer_num_arr[$val[csf('rec_issue_id')]]["transfer_system_id"]]["to_store"]];

								}?>
								</p></td>

							   <?

							}

							if($rptType==2 || $rptType==1 )
							{
								?>
	                            <td width="80" align="center"><p>
								<?
								if($val[csf("transaction_type")]==1)
								{
									echo $currency[$receive_data_arr[$val[csf('rec_issue_id')]]["currency_id"]];
								}
								else if($val[csf("transaction_type")]==5)
								{
									echo $currency[1];
								}
								?></p></td>
	                            <td width="80" align="right"><p>
								<?
								if($val[csf("transaction_type")]==1)
								{
									echo number_format($receive_data_arr[$val[csf('rec_issue_id')]]["exchange_rate"],2);
								}
								else if($val[csf("transaction_type")]==5)
								{
									echo "1";
								}
								else echo "0";
							?></p></td>
	                            <td width="80" align="right"><p>
								<?
								if($val[csf("transaction_type")]==1) echo number_format($val[csf("order_rate")],4);
								else if($val[csf("transaction_type")]==5) echo number_format($val[csf("cons_rate")],4);
								else echo number_format(0,4);
								?></p></td>
								<td width="80" align="right"><p><? echo number_format($val[csf("receive_qty")],2,".",""); $total_receive +=$val[csf("receive_qty")]; ?></p></td>
	                            <td width="80" align="right"><? echo number_format($val[csf("receive_ret_qty")],2,".",""); $tot_receive_ret_qty +=$val[csf("receive_ret_qty")]; ?></td>
	                            <td width="80" align="right"><p>
								<?
								if($val[csf("transaction_type")]==1)
								{
									$order_amt=$val[csf("order_qnty")]*$val[csf("order_rate")];
									echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0;
								}
								else if($val[csf("transaction_type")]==5) echo number_format($val[csf("cons_amount")],4);
								else echo number_format(0,4);
								 ?></p></td>

	                            <?
							}

	                        if($rptType==3 || $rptType==5)
	                        {
								?>
	                           <td align="left"  width="145">
	                           <?
	                           if($issue_data_arr[$val[csf("rec_issue_id")]]['knit_dye_source'] ==1)
	                           {
								   echo $company_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_to']];
	                           }
	                           else
							   {
							   		if ($val[csf("transaction_type")]==2) {
	                               		echo $supplier_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_to']];
	                           		}
	                           }
	                           ?>
	                           </td>
	                        	<?
							}

							if($rptType==3 || $rptType==1  || $rptType==5)
							{
								?>
								<td align="right"  width="80"><p><? echo number_format($val[csf("issue_qty")],2,".",""); $total_issue +=$val[csf("issue_qty")]; ?></p></td>
	                            <td width="80" align="right"><? echo number_format($val[csf("issue_ret_qty")],2,".",""); $tot_issue_ret_qty +=$val[csf("issue_ret_qty")]; ?></td>
	                            <td width="80" align="right"><? echo number_format($val[csf("cons_reject_qnty")],2,".",""); $tot_issue_reject_qty +=$val[csf("cons_reject_qnty")]; ?></td>
	                            <td align="right"  width="80"><p><? echo number_format($val[csf("return_qnty")],2,".",""); $total_return +=$val[csf("return_qnty")]; ?></p></td>

	                            <?
							}
							?>
	                        <td align="right"  width="80"><p><? echo number_format($val[csf("cons_rate")],2,".",""); ?></p></td>
	                        <td align="right" width="100" style="padding-right:3px;"><p><? echo number_format($val[csf("cons_amount")],2,".",""); $totla_amount +=$val[csf("cons_amount")]; ?></p></td>
	                        <td width="107"><p><? echo $user_name_arr[$val[csf("inserted_by")]]; ?></p></td>
	                        <td width="140"><p><? echo change_date_format($val[csf("insert_date")])." ".$val[csf("insert_time")]; ?>&nbsp;</p></td>
	                        <td width="100"><p><? echo $remarks; ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
					</tbody>
				</table>
	            <table width="<?  echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
	                <tfoot>
	                	<th width="30">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <?
	                    if($rptType==5)
	                    {
	                    	?>
	                    	<th width="120">&nbsp;</th>
	                    	<?
	                    }
	                    ?>
	                    <th width="100">&nbsp;</th>
	                    <? if($rptType==1) { ?>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <? } ?>
	                    <th width="70">&nbsp;</th>
	                    <th width="130">&nbsp;</th>
						<?

						if($rptType==3)
						{
						 ?>
	                    <th width="130">&nbsp;</th>
						<? } ?>
	                    <th width="100">&nbsp;</th>
	                    <?
						if($rptType==2 || $rptType==3)
						{
						 ?>
	                       <th width="100">&nbsp;</th>
	                     <?
						}
						if($rptType==3)
						{
						 ?>
	                       <th width="100">&nbsp;</th>
	                       <th width="100">&nbsp;</th>
	                       <th width="100">&nbsp;</th>
	                     <?
						}
						if($rptType==5)
						{
						 ?>
	                       <th width="100">&nbsp;</th>
	                     <?
						}
						?>
	                    <th width="120">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="90">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
						<? if($rptType==1)
						{
							?>
	                        <th width="120">&nbsp;</th>
	                        <th width="120">&nbsp;</th>
	                        <th width="120">&nbsp;</th>
	                        <?
						}
	                     if($rptType==3 || $rptType==5)
						{
							?>
	                        <th width="145">Total:</th>
	                        <?
						}

						if($rptType==2 || $rptType==1 )
						{
							?>
	                        <th width="80" >&nbsp;</th>
	                        <th width="80" >&nbsp;</th>
	                        <th width="80" >Total:</th>
	                    	<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
	                        <th width="80" id="val_tot_receive_ret_qty"><? echo number_format($tot_receive_ret_qty,2); ?></th>
	                        <th width="80" id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
	                        <?
						}
						if($rptType==3 || $rptType==1  || $rptType==5)
						{
							?>
	                        <th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
	                        <th width="80" id="val_tot_issue_ret_qty"><? echo number_format($tot_issue_ret_qty,2); ?></th>
	                        <th width="80" id="val_tot_issue_reject_qty"><? echo number_format($tot_issue_reject_qty,2); ?></th>
	                        <th width="80" id="value_total_return"><? echo number_format($total_return,2); ?></th>
	                        <?
						}
						?>
	                    <th width="80">&nbsp;</th>
	                    <th id="value_totla_amount" width="100" style="padding-right:3px;"><? echo number_format($totla_amount,2); ?></th>
	                    <th width="107">&nbsp;</th>
	                    <th width="140">&nbsp;</th>
	                    <th width="100">&nbsp;</th>

	                </tfoot>
	            </table>
			 </div>
	    	</fieldset>
		</div>
		<?
	}

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (27)");
	oci_commit($con);
	disconnect($con);

	//echo "Execution Time: " . (microtime(true) - $started) . "S";
	foreach (glob($user_id."*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());

	echo "$html**$filename**$cbo_item_cat**$rptType";
	disconnect($con);

	//Mail send------------------------------------------

	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailToArr=array();

		if($cbo_item_cat==3){$mail_item=73;}
		else if($cbo_item_cat==4){$mail_item=70;}

		$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and a.COMPANY_ID=$cbo_company_name and b.mail_user_setup_id=c.id";
		$mail_sql=sql_select($sql);
		$mailArr=array();
		foreach($mail_sql as $row)
		{
			$mailArr[$row[MAIL]]=$row[MAIL];
		}
		$to=implode(',',$mailArr);

		$att_file_arr[]=$filename.'**'.$filename;

		$subject="Date Wise Item Receive and Issue";
		$mailBody="Sir,Please Check Att file.";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
	}
	//------------------------------------End;
	exit();
}


if($action=="generate_report_fso")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	$buyer_session_arr=return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0 order by season_name ASC",'id','season_name');
	//echo $cbo_item_cat."=".$cbo_company_name."=".$cbo_style_owner."=".$cbo_buyer_name."=".$cbo_store_name."=".$txt_date_from."=".$txt_date_to;die;


	/*$product_arr=array();
	$prodDataArr=sql_select("select id, detarmination_id, gsm, dia_width, color from product_details_master where item_category_id in($cbo_item_cat)");
	foreach($prodDataArr as $row)
	{
		$product_arr[$row[csf('id')]][1]=$row[csf('detarmination_id')];
		$product_arr[$row[csf('id')]][2]=$row[csf('gsm')];
		$product_arr[$row[csf('id')]][3]=$row[csf('dia_width')];
		$product_arr[$row[csf('id')]][4]=$row[csf('color')];
	}
	unset($prodDataArr);
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');*/

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and b.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and b.store_id=$cbo_store_name";
	$str_cond_mst="";
	if($cbo_company_name>0) $str_cond_mst.=" and a.company_id=$cbo_company_name";
	$receive_num_arr=array();
	if($db_type==0)
	{
		$color_type_select=" group_concat(b.color_type_id) as color_type_id";
		$select_year=" year(a.insert_date) as year";
		$devision_select=" group_concat(b.division_id) as division_id";
	}
	else
	{
		$color_type_select=" listagg(cast(b.color_type_id as varchar(4000)),',') within group(order by b.color_type_id) as color_type_id";
		$select_year=" to_char(a.insert_date,'YYYY') as year";
		$devision_select=" listagg(cast(b.division_id as varchar(4000)),',') within group(order by b.division_id) as division_id";
	}
	if($rptType==2)
	{
		$receive_sql=sql_select("select a.id, a.recv_number, a.challan_no, a.knitting_source, a.knitting_company, a.receive_basis, a.booking_no, b.body_part_id, b.fabric_description_id, b.batch_id, b.trans_id, b.gsm, b.width, b.color_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in(225,233) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($cbo_item_cat) and b.is_sales=1 and a.receive_date between '$txt_date_from' and '$txt_date_to' $str_cond_mst");
		foreach($receive_sql as $row)
		{
			$receive_issue_trans[$row[csf('trans_id')]]["recv_number"]=$row[csf("recv_number")];
			$receive_issue_trans[$row[csf('trans_id')]]["challan_no"]=$row[csf("challan_no")];
			$receive_issue_trans[$row[csf('trans_id')]]["knitting_source"]=$row[csf("knitting_source")];
			$receive_issue_trans[$row[csf('trans_id')]]["knitting_company"]=$row[csf("knitting_company")];
			$receive_issue_trans[$row[csf('trans_id')]]["receive_basis"]=$row[csf("receive_basis")];
			$receive_issue_trans[$row[csf('trans_id')]]["booking_no"]=$row[csf("booking_no")];

			$receive_issue_trans[$row[csf('trans_id')]]["body_part_id"]=$row[csf("body_part_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["fabric_description_id"]=$row[csf("fabric_description_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["gsm"]=$row[csf("gsm")];
			$receive_issue_trans[$row[csf('trans_id')]]["width"]=$row[csf("width")];
			$receive_issue_trans[$row[csf('trans_id')]]["color_id"]=$row[csf("color_id")];
		}
		unset($receive_sql);

		$transfer_sql=sql_select("select a.id, a.transfer_system_id, a.challan_no, b.trans_id, b.to_trans_id, b.gsm, b.dia_width, b.feb_description_id, b.body_part_id, b.batch_id, b.color_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($cbo_item_cat) and a.transfer_date between '$txt_date_from' and '$txt_date_to' $str_cond_mst");
		foreach($transfer_sql as $row)
		{
			$receive_issue_trans[$row[csf('to_trans_id')]]["recv_number"]=$row[csf("transfer_system_id")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["challan_no"]=$row[csf("challan_no")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["gsm"]=$row[csf("gsm")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["dia_width"]=$row[csf("dia_width")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["feb_description_id"]=$row[csf("feb_description_id")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["body_part_id"]=$row[csf("body_part_id")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["batch_id"]=$row[csf("batch_id")];
			$receive_issue_trans[$row[csf('to_trans_id')]]["color_id"]=$row[csf("color_id")];
		}
		unset($transfer_sql);
		$sql="select a.id as prod_id, a.detarmination_id, a.gsm, a.dia_width, a.color, a.unit_of_measure, b.id as trans_id, b.mst_id, b.receive_basis, b.pi_wo_batch_no, b.transaction_type, b.transaction_date, b.store_id, c.po_breakdown_id, c.quantity, b.insert_date, b.inserted_by, b.cons_rate, b.remarks
		from product_details_master a, inv_transaction b, order_wise_pro_details c where a.id=b.prod_id and b.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1 and c.entry_form in(225,233,230) and b.transaction_type in(1,4,5) and c.trans_type in(1,4,5) and b.transaction_date between '$txt_date_from' and '$txt_date_to' and a.item_category_id=$cbo_item_cat and b.item_category=$cbo_item_cat $str_cond";

	}
	else
	{
		$issue_num_arr=array();
		$issue_sql=sql_select("SELECT a.id, a.issue_number, a.issue_purpose, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.received_id, b.trans_id, b.batch_id, b.body_part_id, a.supplier_id from inv_issue_master a, inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form in(224,287) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($cbo_item_cat) and a.issue_date between '$txt_date_from' and '$txt_date_to' $str_cond_mst");
		foreach($issue_sql as $row)
		{
			$receive_issue_trans[$row[csf('trans_id')]]["recv_number"]=$row[csf("issue_number")];
			$receive_issue_trans[$row[csf('trans_id')]]["challan_no"]=$row[csf("challan_no")];
			$receive_issue_trans[$row[csf('trans_id')]]["knitting_source"]=$row[csf("knit_dye_source")];
			$receive_issue_trans[$row[csf('trans_id')]]["knitting_company"]=$row[csf("knit_dye_company")];
			$receive_issue_trans[$row[csf('trans_id')]]["supplier_id"]=$row[csf("supplier_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["received_id"]=$row[csf("received_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
			$receive_issue_trans[$row[csf('trans_id')]]["batch_id"]=$row[csf("batch_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["body_part_id"]=$row[csf("body_part_id")];
		}
		unset($issue_sql);

		$transfer_sql=sql_select("SELECT a.id, a.transfer_system_id, a.challan_no, b.trans_id, b.to_trans_id, b.gsm, b.dia_width, b.feb_description_id, b.body_part_id, b.batch_id, b.color_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($cbo_item_cat) and a.transfer_date between '$txt_date_from' and '$txt_date_to' $str_cond_mst");
		foreach($transfer_sql as $row)
		{
			$receive_issue_trans[$row[csf('trans_id')]]["recv_number"]=$row[csf("transfer_system_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["challan_no"]=$row[csf("challan_no")];
			$receive_issue_trans[$row[csf('trans_id')]]["gsm"]=$row[csf("gsm")];
			$receive_issue_trans[$row[csf('trans_id')]]["dia_width"]=$row[csf("dia_width")];
			$receive_issue_trans[$row[csf('trans_id')]]["feb_description_id"]=$row[csf("feb_description_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["body_part_id"]=$row[csf("body_part_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["batch_id"]=$row[csf("batch_id")];
			$receive_issue_trans[$row[csf('trans_id')]]["color_id"]=$row[csf("color_id")];
		}
		unset($transfer_sql);

		$sql="SELECT a.id as prod_id, a.detarmination_id, a.gsm, a.dia_width, a.color, a.unit_of_measure, b.id as trans_id, b.mst_id, b.receive_basis, b.pi_wo_batch_no, b.transaction_type, b.transaction_date, b.store_id, c.po_breakdown_id, c.quantity, b.insert_date, b.inserted_by, b.cons_rate, b.remarks
		from product_details_master a, inv_transaction b, order_wise_pro_details c
		where a.id=b.prod_id and b.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1 and c.entry_form in(224,287,230) and b.transaction_type in(2,3,6) and c.trans_type in(2,3,6) and b.transaction_date between '$txt_date_from' and '$txt_date_to' and a.item_category_id=$cbo_item_cat and b.item_category=$cbo_item_cat $str_cond order by b.id";
	}
	//echo $sql;die;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($row[csf("po_breakdown_id")]) $all_po_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		if($row[csf("prod_id")]) $all_product_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	//echo "<pre>";print_r($all_product_id_arr);die;

	// ============================================= Start =======================================
	//echo $delivery_id_cond;die;
	if($db_type==2 && count($all_product_id_arr)>999)
	{
		$all_po_id_chunk=array_chunk($all_product_id_arr,999);
		$product_id_cond=" and";
		foreach($all_po_id_chunk as $product_id_row)
		{
			$product_id_cond.= "( b.product_id in(".implode(",",$product_id_row).") or";
		}
		$product_id_cond=chop($product_id_cond,"or");
		$product_id_cond.=")";
	}
	else
	{
		$product_id_cond=" and b.product_id in(".implode(",",$all_product_id_arr).")";
	}
	//echo $product_id_cond;die;

	$delivery_sql=sql_select("SELECT a.delivery_to, a.delivery_address, b.product_id
	from pro_fin_deli_multy_challan_mst a, pro_fin_deli_multy_challa_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $product_id_cond
	group by a.delivery_to, a.delivery_address, b.product_id");

	$delivery_dataArr=array();
	foreach($delivery_sql as $row)
	{
		$delivery_dataArr[$row[csf("product_id")]]["delivery_to"]=$row[csf("delivery_to")];
		$delivery_dataArr[$row[csf("product_id")]]["delivery_address"]=$row[csf("delivery_address")];
	}
	unset($delivery_sql);
	//echo "<pre>";print_r($delivery_dataArr);die;

	// =========================================== End =========================================

	$sales_id_cond="";
	if($db_type==2 && count($all_po_id_arr)>999)
	{
		$all_po_id_chunk=array_chunk($all_po_id_arr,999);
		$sales_id_cond=" and";
		foreach($all_po_id_chunk as $sales_id)
		{
			$sales_id_cond.= "( a.id in(".implode(",",$sales_id).") or";
		}
		$sales_id_cond=chop($sales_id_cond,"or");
		$sales_id_cond.=")";
	}
	else
	{
		$sales_id_cond=" and a.id in(".implode(",",$all_po_id_arr).")";
	}

	$sales_order_sql=sql_select("SELECT a.id, a.job_no, a.within_group, a.style_ref_no, a.sales_booking_no, a.booking_id, a.delivery_date, a.buyer_id, a.po_buyer, a.season, a.season_id, a.po_job_no, a.booking_type, $select_year, $color_type_select
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_id_cond
	group by a.id, a.job_no, a.within_group, a.style_ref_no, a.sales_booking_no, a.booking_id, a.delivery_date, a.buyer_id, a.po_buyer, a.season, a.po_job_no, a.booking_type, a.season_id, a.insert_date");
	//echo "<pre>";print_r($sales_order_sql);die;
	$sales_order_data=array();
	foreach($sales_order_sql as $row)
	{
		if($row[csf("booking_id")]) $all_book_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		$sales_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
		$sales_order_data[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
		$sales_order_data[$row[csf("id")]]["sales_booking_no"]=$row[csf("sales_booking_no")];
		$sales_order_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$sales_order_data[$row[csf("id")]]["year"]=$row[csf("year")];
		$sales_order_data[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
		$sales_order_data[$row[csf("id")]]["delivery_date"]=$row[csf("delivery_date")];
		if($row[csf("within_group")]==1) $buyer_id=$row[csf("po_buyer")]; else $buyer_id=$row[csf("buyer_id")];
		$sales_order_data[$row[csf("id")]]["buyer_id"]=$buyer_id;
		$sales_order_data[$row[csf("id")]]["season"]=$row[csf("season")];
		$sales_order_data[$row[csf("id")]]["season_id"]=$row[csf("season_id")];
		$sales_order_data[$row[csf("id")]]["po_job_no"]=$row[csf("po_job_no")];
		$sales_order_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];
		$sales_order_data[$row[csf("id")]]["color_type_id"]=implode(",",array_unique(explode(",",$row[csf("color_type_id")])));
	}
	unset($sales_order_sql);
	//echo "<pre>";print_r($all_book_id_arr);die;
	$book_id_cond="";
	if($db_type==2 && count($all_book_id_arr)>999)
	{
		$all_book_id_chunk=array_chunk($all_book_id_arr,999);
		$book_id_cond=" and";
		foreach($all_book_id_chunk as $book_id)
		{
			$book_id_cond.= "( a.id in(".implode(",",$book_id).") or";
		}
		$book_id_cond=chop($book_id_cond,"or");
		$book_id_cond.=")";
	}
	else
	{
		$book_id_cond=" and a.id in(".implode(",",$all_book_id_arr).")";
	}
	//echo $book_id_cond;die;
	$booking_sql=sql_select("SELECT a.id as book_id, a.booking_no, a.booking_type, a.is_short, a.short_booking_type, a.entry_form, b.division_id
	from wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4) and b.booking_type in(1,4) $book_id_cond
	group by a.id, a.booking_no, a.booking_type, a.is_short, a.short_booking_type, a.entry_form, b.division_id");
	$booking_data=array();
	foreach($booking_sql as $row)
	{
		$booking_data[$row[csf("booking_no")]]["booking_no"]=$row[csf("booking_no")];
		$booking_data[$row[csf("booking_no")]]["booking_type"]=$row[csf("booking_type")];
		$booking_data[$row[csf("booking_no")]]["is_short"]=$row[csf("is_short")];
		if($row[csf("booking_type")]==1)
		{
			if($row[csf("entry_form")]==108)
			{
				$booking_data[$row[csf("booking_no")]]["book_type"]="Partial";
			}
			else
			{
				if($row[csf("is_short")]==1) $booking_data[$row[csf("booking_no")]]["book_type"]="Short"; else $booking_data[$row[csf("booking_no")]]["book_type"]="Main";
			}

		}
		else
		{
			$booking_data[$row[csf("booking_no")]]["book_type"]="Sample";
		}
		$booking_data[$row[csf("booking_no")]]["is_short"]=$row[csf("is_short")];
		$booking_data[$row[csf("booking_no")]]["short_booking_type"]=$row[csf("short_booking_type")];
		$booking_data[$row[csf("booking_no")]]["division_id"] .=$row[csf("division_id")].',';
	}
	//print_r($booking_data[13449]); echo "test";die;
	unset($booking_sql);

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.color_range_id, a.batch_for, a.batch_against, a.extention_no from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form in(0,230,225) $str_cond_mst "); // crm 10730

	$batch_data=array();
	foreach($batch_sql as $row)
	{
		$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_data[$row[csf("id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_data[$row[csf("id")]]["batch_for"]=$row[csf("batch_for")];
		$batch_data[$row[csf("id")]]["batch_against"]=$row[csf("batch_against")];
		$batch_data[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
	}
	unset($batch_sql);


	//$str_cond_mst

	//echo $booking_sql;die;

	$construction_arr=array(); $composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$construction_arr[$row[csf('id')]]=$row[csf('construction')];

		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}


	ob_start();
	if($rptType==2)
	{

		//echo $sql;die;


		?>
        <div style="width:4050px" id="main_body">
            <table width="4030" id="" align="left">
                    <tr class="form_caption" style="border:none;">
                        <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="20" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                        </td>
                    </tr>
               </table>
		   	<br />
           	<table width="4030" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="50" rowspan="2">Prod. Id</th>
                        <th width="80" rowspan="2">Trans. Date</th>
						<th width="120" rowspan="2">Trans. Ref.</th>
                        <th width="80" rowspan="2">Challan No</th>
                        <th width="80" rowspan="2">Receive Basis</th>
                        <th width="120" rowspan="2">WO/PI/Production No</th>
						<th width="100" rowspan="2">Store Name</th>
						<th width="60" rowspan="2">Year</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style No</th>
                        <th width="70" rowspan="2">Season</th>
						<th width="120" rowspan="2">Fabric Booking</th>
						<th width="80" rowspan="2">Booking Type</th>
						<th width="80" rowspan="2">Short Booking Type</th>
						<th width="80" rowspan="2">Compensate Division</th>
						<th width="120" rowspan="2">FSO No</th><!--1410-->

						<th width="80" rowspan="2">Booking Delivery Date</th>
						<th width="80" rowspan="2">Dyeing Source</th>
						<th width="120" rowspan="2">Dyeing Company</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Construction</th>
                        <th width="140" rowspan="2">Fabric Composition</th>
                        <th width="50" rowspan="2">GSM</th>
						<th width="50" rowspan="2">Dia</th>
                        <th width="100" rowspan="2">Batch No</th>
						<th width="80" rowspan="2">Batch Extension</th>
                        <th width="80" rowspan="2">Batch Against</th>
                        <th width="80" rowspan="2">Batch For</th>
                        <th width="80" rowspan="2">Color</th>
                        <th width="80" rowspan="2">Color Range</th>
                        <th width="80" rowspan="2">Color Type</th><!--1800-->

						<th width="240" colspan="3">Receive (Kg)</th>
						<th width="240" colspan="3">Receive (Yds)</th>
                        <th width="240" colspan="3">Receive (Mtr)</th>
                        <th width="80" rowspan="2">Rate</th>
                        <th width="100" rowspan="2">Amount</th>
						<th width="110" rowspan="2">User</th>
						<th rowspan="2" width="130">Insert Date</th><!--1150-->
                        <th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="80">Receive Qty</th>
						<th width="80">Issue ReturnQty</th>
						<th width="80">Trans. In Qty</th>
						<th width="80">Receive Qty</th>
						<th width="80">Issue ReturnQty</th>
						<th width="80">Trans. In Qty</th>
						<th width="80">Receive Qty</th>
						<th width="80">Issue ReturnQty</th>
						<th width="80">Trans. In Qty</th>
					</tr>
				</thead>
		   </table>
			<div style="width:4050px; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table width="4030" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?

				//echo $rcv_sql;die;
				$i=1; $total_receive=""; $total_issue="";
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$rcv_qnty_kg=$iss_rtn_qnty_kg=$trans_in_qnty_kg=$rcv_qnty_mtr=$iss_rtn_qnty_mtr=$trans_in_qnty_mtr=$rcv_qnty_yds=$iss_rtn_qnty_yds=$trans_in_qnty_yds=0;
					if($row[csf("unit_of_measure")]==12)
					{
						if($row[csf("transaction_type")]==1)
						{
							$rcv_qnty_kg=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==4)
						{
							$iss_rtn_qnty_kg=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==5)
						{
							$trans_in_qnty_kg=$row[csf("quantity")];
						}
					}
					elseif($row[csf("unit_of_measure")]==23)
					{
						if($row[csf("transaction_type")]==1)
						{
							$rcv_qnty_mtr=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==4)
						{
							$iss_rtn_qnty_mtr=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==5)
						{
							$trans_in_qnty_mtr=$row[csf("quantity")];
						}
					}
					elseif($row[csf("unit_of_measure")]==27)
					{
						if($row[csf("transaction_type")]==1)
						{
							$rcv_qnty_yds=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==4)
						{
							$iss_rtn_qnty_yds=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==5)
						{
							$trans_in_qnty_yds=$row[csf("quantity")];
						}
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" title="<? echo "trans id = ".$row[csf("trans_id")]; ?>"><p><? echo $i; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00" && $row[csf("transaction_date")]!="") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
                        <td width="120" style="word-break:break-all;" align="center" title="<? echo $row[csf("trans_id")];?>"><p><? echo $receive_issue_trans[$row[csf("trans_id")]]["recv_number"]; ?></p></td>
                        <td width="80" style="word-break:break-all;" align="center"><p><? echo $receive_issue_trans[$row[csf("trans_id")]]["challan_no"]; ?></p></td>
                        <td width="80" style="word-break:break-all;" title="<? echo $row[csf("receive_basis")]; ?>"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $receive_issue_trans[$row[csf("trans_id")]]["booking_no"]; ?></p></td>
                        <td width="100" style="word-break:break-all;"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
						<td width="60" style="word-break:break-all;" title="<? echo $row[csf("po_breakdown_id")]; ?>" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["year"]; ?></p></td>
                        <td width="70" style="word-break:break-all;"><p><? echo $buyer_short_arr[$sales_order_data[$row[csf("po_breakdown_id")]]["buyer_id"]]; ?></p></td>
                        <td width="100" style="word-break:break-all;"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["style_ref_no"]; ?></p></td>
                        <td width="70" style="word-break:break-all;"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["season"]; ?></p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]; ?></p></td>
                        <td width="80" style="word-break:break-all;" align="center" title="<? //echo $booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["is_short"]."=".$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]."=".$row[csf("po_breakdown_id")]; ?>">
						<p>
						<?
							if($sales_order_data[$row[csf("po_breakdown_id")]]["booking_type"]==4)
							{
								echo "Sample without Order";
							}
							else
							{
								echo $booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["book_type"];
							}

							?>
						</p>
					</td>
                        <td width="80" style="word-break:break-all;" align="center"><p><? echo $short_booking_type[$booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["short_booking_type"]]; ?></p></td>
                        <td width="80" style="word-break:break-all;"><p>
						<?
						$divi_id_arr=array_unique(array_filter(explode(",",chop($booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["division_id"],','))));
						$div_name="";
						foreach($divi_id_arr as $div_id)
						{
							$div_name.=$short_division_array[$div_id].",";
						}
						echo chop($div_name,",");
						?></p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["job_no"]; ?></p></td>


                        <td width="80" align="center"><p><? if($sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]!="0000-00-00" && $sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]!="") echo change_date_format($sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]); else echo ""; ?>&nbsp;</p></td>
                        <td width="80" style="word-break:break-all;"><p><? echo $knitting_source[$receive_issue_trans[$row[csf("trans_id")]]["knitting_source"]]; ?></p></td>
                        <td width="120" style="word-break:break-all;"><p><? if($receive_issue_trans[$row[csf("trans_id")]]["knitting_source"]==1) echo $company_arr[$receive_issue_trans[$row[csf("trans_id")]]["knitting_company"]]; else echo $supplier_arr[$receive_issue_trans[$row[csf("trans_id")]]["knitting_company"]]; ?></p></td>

						<td width="100" style="word-break:break-all;"><p><? echo $body_part[$receive_issue_trans[$row[csf("trans_id")]]["body_part_id"]];  ?></p></td>
						<td width="100" style="word-break:break-all;"><p><? echo $construction_arr[$row[csf("detarmination_id")]]; ?></p></td>
						<td width="140" style="word-break:break-all;"><p><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></p></td>
						<td width="50" style="word-break:break-all;"><p><? echo $row[csf("gsm")]; ?></p></td>
						<td width="50" style="word-break:break-all;"><p><? echo  $row[csf("dia_width")]; ?></p></td>
						<td width="100" style="word-break:break-all;" align="center"><? echo $batch_data[$row[csf("pi_wo_batch_no")]]["batch_no"]; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $batch_data[$row[csf("pi_wo_batch_no")]]["extention_no"]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $batch_against[$batch_data[$row[csf("pi_wo_batch_no")]]["batch_against"]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $batch_for[$batch_data[$row[csf("pi_wo_batch_no")]]["batch_for"]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $color_arr[$row[csf("color")]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $color_range[$batch_data[$row[csf("pi_wo_batch_no")]]["color_range_id"]]; ?></td>
                        <td width="80" style="word-break:break-all;">
						<?
						$color_type_data="";
						$color_type_id_arr=explode(",",$sales_order_data[$row[csf("po_breakdown_id")]]["color_type_id"]);
						foreach($color_type_id_arr as $color_id)
						{
							$color_type_data.=$color_type[$color_id].",";
						}
						echo chop($color_type_data,","); ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_kg,2); $tot_rcv_qnty_kg +=$rcv_qnty_kg; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_kg,2); $tot_iss_rtn_qnty_kg +=$iss_rtn_qnty_kg; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_kg,2); $tot_trans_in_qnty_kg +=$trans_in_qnty_kg; ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_yds,2); $tot_rcv_qnty_yds +=$rcv_qnty_yds; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_yds,2); $tot_iss_rtn_qnty_yds +=$iss_rtn_qnty_yds; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_yds,2); $tot_trans_in_qnty_yds +=$trans_in_qnty_yds; ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_mtr,2); $tot_rcv_qnty_mtr +=$rcv_qnty_mtr; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_mtr,2); $tot_iss_rtn_qnty_mtr +=$iss_rtn_qnty_mtr; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_mtr,2); $tot_trans_in_qnty_mtr +=$trans_in_qnty_mtr; ?></td>

                        <td width="80" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
                        <td width="100" align="right"><? $amt=$row[csf("cons_rate")]*$row[csf("quantity")]; echo number_format($amt,2);  $tot_amt +=$amt; ?></td>
						<td width="110" align="center"><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></td>
						<td align="center" width="130"><? echo $row[csf("insert_date")]; ?></td>
                        <td align="center"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<?
					$i++;
				}
				unset($result);
				?>
				</tbody>
			</table>
			<table width="4030" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr>
                    	<th width="30">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="120" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="60" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th><!--1410-->

						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
                        <th width="140" >&nbsp;</th>
                        <th width="50" >&nbsp;</th>
						<th width="50" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" align="right">Total:</th><!--1800-->
                        <th width="80" align="right" id="value_tot_rcv_qnty_kg"><? echo number_format($tot_rcv_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_kg"><? echo number_format($tot_iss_rtn_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_kg"><? echo number_format($tot_trans_in_qnty_kg,2); ?></th>

                        <th width="80" align="right" id="value_tot_rcv_qnty_yds"><? echo number_format($tot_rcv_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_yds"><? echo number_format($tot_iss_rtn_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_yds"><? echo number_format($tot_trans_in_qnty_yds,2); ?></th>

                        <th width="80" align="right" id="value_tot_rcv_qnty_mtr"><? echo number_format($tot_rcv_qnty_mtr,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_mtr"><? echo number_format($tot_iss_rtn_qnty_mtr,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_mtr"><? echo number_format($tot_trans_in_qnty_mtr,2); ?></th>

                        <th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_tot_amt"><? echo number_format($tot_amt,2); ?></th>
                        <th width="110">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
				</tfoot>
			</table>
			</div>
		</div>
        <?
	}
	else
	{
		?>
        <div style="width:4110px" id="main_body">
            <table width="4090" id="" align="left">
                    <tr class="form_caption" style="border:none;">
                        <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="20" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
                        </td>
                    </tr>
               </table>
		   	<br />
           	<table width="4090" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="50" rowspan="2">Prod. Id</th>
                        <th width="80" rowspan="2">Trans. Date</th>
						<th width="120" rowspan="2">Trans. Ref.</th>
						<th width="100" rowspan="2">Store Name</th>
						<th width="60" rowspan="2">Year</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style No</th>
                        <th width="70" rowspan="2">Season</th>
						<th width="120" rowspan="2">Fabric Booking</th>
						<th width="80" rowspan="2">Booking Type</th>
						<th width="80" rowspan="2">Short Booking Type</th>
						<th width="80" rowspan="2">Compensate Division</th>
						<th width="120" rowspan="2">FSO No</th><!--1410-->

						<th width="80" rowspan="2">Booking Delivery Date</th>
                        <th width="120" rowspan="2">Party Name</th>
                        <th width="120" rowspan="2">Delivery To</th>
                        <th width="120" rowspan="2">Delivery</th>


						<th width="80" rowspan="2">Dyeing Source</th>
						<th width="120" rowspan="2">Dyeing Company</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Construction</th>
                        <th width="140" rowspan="2">Fabric Composition</th>
                        <th width="50" rowspan="2">GSM</th>
						<th width="50" rowspan="2">Dia</th>
                        <th width="100" rowspan="2">Batch No</th>
						<th width="80" rowspan="2">Batch Extension</th>
                        <th width="80" rowspan="2">Batch Against</th>
                        <th width="80" rowspan="2">Batch For</th>
                        <th width="80" rowspan="2">Color</th>
                        <th width="80" rowspan="2">Color Range</th>
                        <th width="80" rowspan="2">Color Type</th><!--1800-->

						<th width="240" colspan="3">Issue (Kg)</th>
						<th width="240" colspan="3">Issue (Yds)</th>
                        <th width="240" colspan="3">Issue (Mtr)</th>
                        <th width="80" rowspan="2">Rate</th>
                        <th width="100" rowspan="2">Amount</th>
						<th width="110" rowspan="2">User</th>
						<th rowspan="2" width="130">Insert Date</th><!--1150-->
                        <th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="80">Issue Qty</th>
						<th width="80">Rcv. Return Qty</th>
						<th width="80">Trans. Out Qty</th>
						<th width="80">Issue Qty</th>
						<th width="80">Rcv. Return Qty</th>
						<th width="80">Trans. In Qty</th>
						<th width="80">Issue Qty</th>
						<th width="80">Rcv. Return Qty</th>
						<th width="80">Trans. Out Qty</th>
					</tr>
				</thead>
		   </table>
			<div style="width:4110px; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table width="4090" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?

				//echo $rcv_sql;die;
				$i=1; $total_receive=""; $total_issue="";
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$rcv_qnty_kg=$iss_rtn_qnty_kg=$trans_in_qnty_kg=$rcv_qnty_mtr=$iss_rtn_qnty_mtr=$trans_in_qnty_mtr=$rcv_qnty_yds=$iss_rtn_qnty_yds=$trans_in_qnty_yds=0;
					if($row[csf("unit_of_measure")]==12)
					{
						if($row[csf("transaction_type")]==2)
						{
							$rcv_qnty_kg=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==3)
						{
							$iss_rtn_qnty_kg=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==6)
						{
							$trans_in_qnty_kg=$row[csf("quantity")];
						}
					}
					elseif($row[csf("unit_of_measure")]==23)
					{
						if($row[csf("transaction_type")]==2)
						{
							$rcv_qnty_mtr=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==3)
						{
							$iss_rtn_qnty_mtr=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==6)
						{
							$trans_in_qnty_mtr=$row[csf("quantity")];
						}
					}
					elseif($row[csf("unit_of_measure")]==27)
					{
						if($row[csf("transaction_type")]==2)
						{
							$rcv_qnty_yds=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==3)
						{
							$iss_rtn_qnty_yds=$row[csf("quantity")];
						}
						if($row[csf("transaction_type")]==6)
						{
							$trans_in_qnty_yds=$row[csf("quantity")];
						}
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" title="<? echo "trans id = ".$row[csf("trans_id")]; ?>"><p><? echo $i; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="80" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00" && $row[csf("transaction_date")]!="") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $receive_issue_trans[$row[csf("trans_id")]]["recv_number"]; ?></p></td>
                        <td width="100" style="word-break:break-all;"><p><? echo $store_library[$row[csf("store_id")]]; ?></p></td>
						<td width="60" style="word-break:break-all;" title="<? echo $row[csf("po_breakdown_id")]; ?>" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["year"]; ?></p></td>
                        <td width="70" style="word-break:break-all;"><p><? echo $buyer_short_arr[$sales_order_data[$row[csf("po_breakdown_id")]]["buyer_id"]]; ?></p></td>
                        <td width="100" style="word-break:break-all;"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["style_ref_no"]; ?></p></td>
                        <td width="70" style="word-break:break-all;"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["season"]; ?></p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]; ?></p></td>
                        <td width="80" style="word-break:break-all;" align="center" title="<? //echo $booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["is_short"]."=".$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]."=".$row[csf("po_breakdown_id")]; ?>">
						<p>
							<?
							echo $booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["book_type"];
							?>
						</p>
						</td>
                        <td width="80" style="word-break:break-all;" align="center">
						<p>
							<?
							echo $short_booking_type[$booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["short_booking_type"]]; ?>
						</p>
					</td>
                        <td width="80" style="word-break:break-all;"><p>
						<?
						$divi_id_arr=array_unique(array_filter(explode(",",chop($booking_data[$sales_order_data[$row[csf("po_breakdown_id")]]["sales_booking_no"]]["division_id"],','))));
						$div_name="";
						foreach($divi_id_arr as $div_id)
						{
							$div_name.=$short_division_array[$div_id].",";
						}
						echo chop($div_name,",");
						?></p></td>
                        <td width="120" style="word-break:break-all;" align="center"><p><? echo $sales_order_data[$row[csf("po_breakdown_id")]]["job_no"]; ?></p></td>


                        <td width="80" align="center"><p><? if($sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]!="0000-00-00" && $sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]!="") echo change_date_format($sales_order_data[$row[csf("po_breakdown_id")]]["delivery_date"]); else echo ""; ?>&nbsp;</p></td>
                        <td width="120" style="word-break:break-all;"><p><? if($receive_issue_trans[$row[csf("trans_id")]]["knitting_source"]==1) echo $company_arr[$receive_issue_trans[$row[csf("trans_id")]]["supplier_id"]]; else echo $supplier_arr[$receive_issue_trans[$row[csf("trans_id")]]["supplier_id"]];  ?></p></td>
                        <td width="120" title="<? echo $row[csf('prod_id')]; ?>" style="word-break:break-all;"><p>
                        	<? echo $delivery_dataArr[$row[csf('prod_id')]]["delivery_to"]; ?></p></td>
                        <td width="120" style="word-break:break-all;"><p>
                        	<? echo $delivery_dataArr[$row[csf('prod_id')]]["delivery_address"]; ?></p></td>
                        <td width="80" style="word-break:break-all;"><p><? echo $knitting_source[$receive_issue_trans[$row[csf("trans_id")]]["knitting_source"]]; ?></p></td>
                        <td width="120" style="word-break:break-all;"><p><? if($receive_issue_trans[$row[csf("trans_id")]]["knitting_source"]==1) echo $company_arr[$receive_issue_trans[$row[csf("trans_id")]]["knitting_company"]]; else echo $supplier_arr[$receive_issue_trans[$row[csf("trans_id")]]["knitting_company"]]; ?></p></td>

						<td width="100" style="word-break:break-all;"><p><? echo $body_part[$receive_issue_trans[$row[csf("trans_id")]]["body_part_id"]];  ?></p></td>
						<td width="100" style="word-break:break-all;"><p><? echo $construction_arr[$row[csf("detarmination_id")]]; ?></p></td>
						<td width="140" style="word-break:break-all;"><p><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></p></td>
						<td width="50" style="word-break:break-all;"><p><? echo $row[csf("gsm")]; ?></p></td>
						<td width="50" style="word-break:break-all;"><p><? echo  $row[csf("dia_width")]; ?></p></td>
						<td width="100" style="word-break:break-all;" align="center"><? echo $batch_data[$row[csf("pi_wo_batch_no")]]["batch_no"]; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $batch_data[$row[csf("pi_wo_batch_no")]]["extention_no"]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $batch_against[$batch_data[$row[csf("pi_wo_batch_no")]]["batch_against"]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $batch_for[$batch_data[$row[csf("pi_wo_batch_no")]]["batch_for"]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $color_arr[$row[csf("color")]]; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $color_range[$batch_data[$row[csf("pi_wo_batch_no")]]["color_range_id"]]; ?></td>
                        <td width="80" style="word-break:break-all;">
						<?
						$color_type_data="";
						$color_type_id_arr=explode(",",$sales_order_data[$row[csf("po_breakdown_id")]]["color_type_id"]);
						foreach($color_type_id_arr as $color_id)
						{
							$color_type_data.=$color_type[$color_id].",";
						}
						echo chop($color_type_data,","); ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_kg,2); $tot_rcv_qnty_kg +=$rcv_qnty_kg; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_kg,2); $tot_iss_rtn_qnty_kg +=$iss_rtn_qnty_kg; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_kg,2); $tot_trans_in_qnty_kg +=$trans_in_qnty_kg; ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_yds,2); $tot_rcv_qnty_yds +=$rcv_qnty_yds; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_yds,2); $tot_iss_rtn_qnty_yds +=$iss_rtn_qnty_yds; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_yds,2); $tot_trans_in_qnty_yds +=$trans_in_qnty_yds; ?></td>

                        <td width="80" align="right"><? echo number_format($rcv_qnty_mtr,2); $tot_rcv_qnty_mtr +=$rcv_qnty_mtr; ?></td>
                        <td width="80" align="right"><? echo number_format($iss_rtn_qnty_mtr,2); $tot_iss_rtn_qnty_mtr +=$iss_rtn_qnty_mtr; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_in_qnty_mtr,2); $tot_trans_in_qnty_mtr +=$trans_in_qnty_mtr; ?></td>

                        <td width="80" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
                        <td width="100" align="right"><? $amt=$row[csf("cons_rate")]*$row[csf("quantity")]; echo number_format($amt,2);  $tot_amt +=$amt; ?></td>
						<td width="110" align="center"><? echo $user_name_arr[$row[csf("inserted_by")]]; ?></td>
						<td align="center" width="130"><? echo $row[csf("insert_date")]; ?></td>
                        <td align="center"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<?
					$i++;
				}
				unset($result);
				?>
				</tbody>
			</table>
			<table width="4090" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr>
                    	<th width="30">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="60" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>

						<th width="80" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
                        <th width="140" >&nbsp;</th>
                        <th width="50" >&nbsp;</th>
						<th width="50" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
						<th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" align="right">Total:</th><!--1800-->
                        <th width="80" align="right" id="value_tot_rcv_qnty_kg"><? echo number_format($tot_rcv_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_kg"><? echo number_format($tot_iss_rtn_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_kg"><? echo number_format($tot_trans_in_qnty_kg,2); ?></th>

                        <th width="80" align="right" id="value_tot_rcv_qnty_yds"><? echo number_format($tot_rcv_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_yds"><? echo number_format($tot_iss_rtn_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_yds"><? echo number_format($tot_trans_in_qnty_yds,2); ?></th>

                        <th width="80" align="right" id="value_tot_rcv_qnty_mtr"><? echo number_format($tot_rcv_qnty_mtr,2); ?></th>
                        <th width="80" align="right" id="value_tot_iss_rtn_qnty_mtr"><? echo number_format($tot_iss_rtn_qnty_mtr,2); ?></th>
                        <th width="80" align="right" id="value_tot_trans_in_qnty_mtr"><? echo number_format($tot_trans_in_qnty_mtr,2); ?></th>

                        <th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_tot_amt"><? echo number_format($tot_amt,2); ?></th>
                        <th width="110">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
				</tfoot>
			</table>
			</div>
		</div>
        <?
	}

	foreach (glob($user_id."*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	disconnect($con);
	exit();
}
?>
