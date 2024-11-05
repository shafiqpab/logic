<?
/*-------------------------------------------- Comments
Version                  :
Purpose			         : 	 This form will create Sample Requisition Fabric Booking (Without Order)
Functionality	         :
JS Functions	         :
Created by		         :	Rehan Uddin
Creation date 	         :
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
 */

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number" );
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
 }
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_suplier")
{
	if($data==5 || $data==3)
	{
	 echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_requisition_booking_non_order_controller');",0,"" ); // new condition
	}
	else
	{
	 echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_requisition_booking_non_order_controller');",0 );
 	}
	exit();
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	$supplier=$data[0];
	$paymode=$data[1];
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$supplier."' and is_deleted=0 and status_active=1");
	if($paymode==1 || $paymode==2)
	{
		echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	}
	else
	{
		echo "document.getElementById('txt_attention').value = '';\n";
	}
	exit();
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}


if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}
if($action=="check_kniting_charge")
{
 	 $sql_result=sql_select("select process_costing_maintain from variable_settings_production where company_name='$data' and variable_list=34 and status_active=1 and is_deleted=0");
 	$maintain_setting=$sql_result[0][csf('process_costing_maintain')];
	if($maintain_setting==1)
	{
	echo "1"."_";
	}
	else
	{
	echo "0"."_";
	}

	exit();
}

if($action=="print_button_variable_setting")
{
 	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	//echo "select format_id from lib_report_template  where template_name ='".$data."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1";
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if($action=="show_fabric_booking")
{
	extract($_REQUEST);
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$arr=array (0=>$style_library,2=>$sample_library,3=>$body_part,4=>$color_type,8=>$color_library,9=>$color_library,10=>$size_library);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$sql= "select style_id,style_des,sample_type,body_part,color_type_id,construction,composition,gsm_weight,gmts_color ,fabric_color,gmts_size,item_size,width_dia_type as dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id FROM wo_non_ord_samp_booking_dtls  WHERE booking_no ='".$data."'  and status_active=1 and	is_deleted=0 order by id";

	echo  create_list_view("list_view", "Style,Style Des,Sample,Body Part,Color Type,Construction,Composition,GSM,Gmts Color,Fab.Color,Gmts Size,Item Size,Dia/ Width,Fin Fab Qnty,Process Loss,Gray Qnty,Rate,Amount", "60,100,100,130,100,100,150,50,80,80,80,100,50,60,60,60,60","1600","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "style_id,0,sample_type,body_part,color_type_id,0,0,0,gmts_color,fabric_color,gmts_size,0,0,0,0,0,0,0", $arr , "style_id,style_des,sample_type,body_part,color_type_id,construction,composition,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount", "requires/sample_requisition_booking_non_order_controller",'','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	exit();
}

if($action=="color_from_library")
{
  $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$data  and variable_list=23  and status_active=1 and is_deleted=0");
  echo trim($color_from_library);
  die;
}

if($action=="booking_no_check")
{
  $booking_no_check=sql_select(" select issue_number from inv_issue_master where  booking_no='$data' and issue_basis=1 and issue_purpose=8 and entry_form=3 and status_active=1 and is_deleted=0");

   $issue_number="";
  foreach($booking_no_check as $row_iss)
  {
	  if( $issue_number=="")  $issue_number=$row_iss[csf('issue_number')]; else $issue_number.="_".$row_iss[csf('issue_number')];

  }
  echo rtrim($issue_number);
  exit();
}

if($action=="color_popup")
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
    if($buyer_name=="" || $buyer_name==0 )
	{
	$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
	}
	else
	{
	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0";
	}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_requisition_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;


	?>
    </form>
    </div>
    </body>
    </html>
    <?
	exit();
}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');

	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
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
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px">
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>

                             <td style="font-size:20px">
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>
                             </td>

                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				//$season_con="";
				$buyer_req_no="";
				$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

				$nameseason=sql_select( "SELECT a.style_ref_no,a.requisition_number, a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b.sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and c.style_id=$hidden_requisition_id group by a.style_ref_no,a.requisition_number, a.season, b.buyer_req_no  ");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$season_con=$season_arr[$season];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$style_ref_no=$season_row[csf('style_ref_no')];
					$requisition_number=$season_row[csf('requisition_number')];

				}

				$fabric_source='';
                $nameArray=sql_select( "SELECT id,buyer_id,pay_mode,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader, dealing_marchant from wo_non_ord_samp_booking_mst where booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Fab. Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
				{
					echo $company_library[$result[csf('supplier_id')]];
				}
				else
				{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
				?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5 ){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';

					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?></td>
            </tr>


            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>

                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>

            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_con; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>

            </tr>
            <tr>
            	<td width="100" style="font-size:12px"><b>Style Ref.</b></td>
                <td width="110">:&nbsp;<? echo $style_ref_no; ?></td>
                <td width="100" style="font-size:12px"><b>Requisition. No</b></td>
                <td width="110">:&nbsp;<? echo $requisition_number; ?></td>
                <td colspan="2" ></td>
            </tr>
        </table>
        <?
			}
		?>

      <br/>
      <?
	  $composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and b.is_deleted=0 order by b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]].=$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].',';
			}
			else
			{
				$composition_arr[$row[csf('id')]].=$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].',';
			}
		}
	}

    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

	 $sql= sql_select("SELECT style_id,style_des,sample_type,gmts_item_id,body_part,fabric_description,color_type_id,construction,composition,gsm_weight,gmts_color,dia ,fabric_color,gmts_size,item_size,width_dia_type as dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,yarn_details,remarks ,uom,req_qty ,color_all_data,finish_fabric,grey_fabric,dtls_id,remarks FROM wo_non_ord_samp_booking_dtls  WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0 and entry_form_id=140 and grey_fabric>0  order by id");
   foreach($sql as $vals)
   {
   		$reqid_arr[$vals[csf("style_id")]]=$vals[csf("style_id")];
   }
      $req_all_id=implode(",", array_unique($reqid_arr));

	  $sample_req_fab="SELECT id, sample_name ,gmts_item_id, color_data ,required_dzn from sample_development_fabric_acc where status_active=1 and is_deleted=0 and form_type=1 and sample_mst_id in ($req_all_id) ";
	   foreach(sql_select($sample_req_fab) as $k=>$vals)
	   {
	   	 $sample_req_fab_arr[$vals[csf("id")]]["color"]=$vals[csf("color_data")];
	   	 $sample_req_fab_arr[$vals[csf("id")]]["required_dzn"]=$vals[csf("required_dzn")];
	   }


   	 $sample_req_fab="SELECT id, sample_name ,gmts_item_id, sample_color,sample_prod_qty from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=117  and sample_mst_id in ($req_all_id) ";
	   foreach(sql_select($sample_req_fab) as $k=>$vals)
	   {
	   	 $sample_req_qtyarr[$vals[csf("sample_name")]][$vals[csf("sample_color")]]+=$vals[csf("sample_prod_qty")];
	   }


?>
<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
<thead>
<tr>
	<th width="50">Sl</th>
	<th width="120">Sample Name</th>
	<th width="120">Garment Item</th>
	<th width="130">Body Part</th>
	<th width="120">Fabric Description</th>
	<th width="40">GSM</th>
	<th width="40">Dia</th>
	<th width="100">Color Type</th>
	<th width="110">Garments Color</th>
	<th width="110">Fabric Color</th>
	<th width="80">Dia/ Width</th>
	<th width="50">UOM</th>
	<th width="80" align="right">Req. Qty.</th>
	<th width="110" align="right">Process Loss</th>
	<th width="80" align="right">Req. Grey</th>
	<th width="50" align="right">Amount</th>
	<th width="50" align="right">Rate</th>
	<th width="100" align="center">Remark</th>
 </tr>
</thead>
<?
$style_id="";$sample_type_id="";
$total_finish_fabric=0;
$total_grey_fabric=0;
$toatl_rate=0;
$total_amount=0;

$i=1;
foreach ($sql as $row)
{
	$style_id.=$row[csf('style_id')].",";
	$sample_type_id.=$row[csf('sample_type')].",";
	if($row[csf('style_id')])$style_sting.=$style_library[$row[csf('style_id')]].'_';
	$color_data=$sample_req_fab_arr[$row[csf("dtls_id")]]["color"] ;

	//$col_data=explode("----",  $color_data);
	//foreach($col_data as $req_val)
	//{
			$req_val=explode("_", $req_val);
			$garment_color_id=$req_val[2];//
  			$req_qty=$row[csf('finish_fabric')];//($sample_req_fab_arr[$row[csf("dtls_id")]]["required_dzn"]/12)*$sample_req_qtyarr[$row[csf('sample_type')]][$garment_color_id];

$comp_type_count=rtrim($composition_arr[$row[csf('lib_yarn_count_deter_id')]],',');
$comp_type_counts=implode(",",array_unique(explode(",",$comp_type_count)));
	?>
		 <tr>
			<td align="center"><? echo $i; ?></td>
			<td align="center"><p><? echo $sample_library[$row[csf('sample_type')]]; ?></p></td>
			<td align="center"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
			<td align="center"><p><? echo $body_part[$row[csf('body_part')]]; ?></p></td>
			<td align="center"><p><? echo $row[csf('fabric_description')].', '.$comp_type_counts;//$row[csf('fabric_description')]; ?></p></td>
			<td align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
			<td align="center"><p><? echo $row[csf('dia')]; ?></p></td>
			<td align="center"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
			<td align="center"><p><? echo $color_library[$row[csf('gmts_color')]]; ?></p></td>
			<td align="center"><p><? echo $color_library[$row[csf('fabric_color')]]; ?></p></td>


			<td align="center"><p><? echo $fabric_typee[$row[csf('dia_width')]]; ?></p></td>
			<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
			<td align="right"><p><?  echo number_format($req_qty,2); ?></p></td>
			<td align="right"><p><? echo $process_loss= number_format($row[csf('process_loss')],2); ?></p></td>
			<td align="right">
				<p>
				<?
				
				 $gray=$row[csf('grey_fabric')];
				 echo number_format($gray,2);

				?>


				</p>
				</td>
			<td align="right"><p><? echo $amount= number_format($row[csf('amount')],2); ?></p></td>
			<td align="right"><p><? echo $rate= number_format($row[csf('rate')],2); ?></p></td>
			<td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>

		</tr>
		<?
		$total_req_qty +=$req_qty;
 		$total_grey_qty +=$gray;
		$total_rate +=$row[csf('rate')];
		$total_amount +=$row[csf('amount')];
		$total_loss +=$process_loss;

		$i++;
	//}
}
?>
<tr>
<th  colspan="12" align="right">Total </th>
<th   align="right"><? echo number_format($total_req_qty,2);  ?></th>
<th   align="right"><? echo number_format($total_loss,2);  ?></th>
<th   align="right"><? echo number_format($total_grey_qty,2);  ?></th>
<th   align="right"><? echo number_format($total_amount,2);  ?></th>
<th   align="right"><? echo number_format($total_rate,2);  ?></th>
<th   align="">&nbsp;</th>
</tr>
</table>
<br><br><br>
        
    <?

	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$mst_style_id=rtrim($req_all_id,',');
	$mst_style_id=implode(",",array_unique(explode(",",$mst_style_id)));
	$sql_stripe="select a.requisition_number,b.body_part_id,b.gmts_item_id,b.fabric_description,b.gsm as gsm_weight,b.dia as dia_width,b.color_type_id,b.required_dzn,b.required_qty,c.color_id as color_id,d.id as did,d.uom,d.measurement,d.stripe_color,d.fabreqtotkg,d.yarn_dyed from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b,wo_sample_stripe_color d where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id  and b.id=d.sample_fab_dtls_id and d.req_no=a.requisition_number and c.color_id=d.color_number_id  and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and  a.id in($mst_style_id) and b.color_type_id in (2,3,4,6,31,32,33,34) order by  d.id";
	$result_data=sql_select($sql_stripe);
	foreach($result_data as $row)
	{
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['construction']=$row[csf('construction')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['color_type_id']=$row[csf('color_type_id')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['req_no']=$row[csf('requisition_number')];
		$tot_stripe_measurement_arr[$row[csf('color_id')]]+=$row[csf('measurement')];


	}
	//echo $tot_stripe_measurement;

	if(count($stripe_arr)>0)
	{
		?>

		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="9" align="center"><b>Stripe Details</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>
	        	<th width="50"> Req. No</th>
	            <th width="100"> Body Part</th>
	            <th width="80"> Fabric Color</th>
	            <th width="70"> Fabric Qty(KG)</th>
	            <th width="70"> Stripe Color</th>
	            <th width="70"> Stripe Measurement</th>
	            <th width="70"> Stripe Uom</th>
	            <th  width="70"> Qty.(KG)</th>
	            <th  width="70"> Y/D Req.</th>
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();
		//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
			 $stripe_wise_fabkg_sql=sql_select("SELECT b.color_id as color_id,c.required_qty as sample_prod_qty,c.body_part_id,c.color_type_id,b.qnty,b.grey_fab_qnty from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where
				a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id)  and  a.sample_prod_qty>0
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");


			foreach($stripe_wise_fabkg_sql as $vals)
			{
				//$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
				$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("qnty")];
			}
	        foreach($stripe_arr as $body_id=>$body_data)
	        {
				foreach($body_data as $color_id=>$color_val)
				{
					$rowspan=count($color_val['stripe_color']);
					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
					?>
					<tr>
						<?
						$jobs=$stripe_arr2[$body_id][$color_id]['req_no'];
						$color_qty=$stripe_wise_fabkg_arr[$body_id][$color_type_id][$color_id]; //$stripe_wise_fabkg_arr[$jobs][$body_id][$color_type_id][$color_id];
						//echo $body_id.'='.$color_type_id.'='.$color_id;
						?>
						<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $jobs; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?></td>
						<?
						$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
						$total_fab_qty+=$color_qty;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$measurement=$color_val['measurement'][$strip_color_id];
							$uom=$color_val['uom'][$strip_color_id];
							$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
							?>
							<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
							<td align="right"> <? echo  number_format($measurement,2); ?></td>
		                    <td> <? echo  $unit_of_measurement[$uom]; ?></td>
							<td align="right" title="Stripe Measurement/Tot Stripe Measurement(<? echo $tot_stripe_measurement;?>)*Fabric Qty(KG)"> <? echo  number_format($fabreqtotkg,2); ?></td>
							<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
						}
							$i++;
				}
			}
			?>
	        <tfoot>
	        	<tr>
	        		<td colspan="4">Total </td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	        		<td></td>
	        		<td></td>
	        		<td>   </td>
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		<?
	}
	?>
	<br/><br/>
	<table width="1330">
		<tr>
			<td width="850">
				<table class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
				    	<thead>
				    		<tr>
				    			<th align="center" width="40">Sl</th>
				    			<th align="center" width="130" >Item Group</th>
				    			<th align="center" width="100" >Description</th>
				    			<th align="center" width="100" >UOM</th>
				    			<th align="center" width="100" >Qty</th>
				    			<th align="center"   >Remarks</th>
				    		</tr>
				    	</thead>
				    	<tbody>
				    		<?
				    		$lib_item_group_arr=array();
				    		$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
				    		foreach ($itemArray as $row)
				    		{
				    			$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
				    		}

				    		$data_array=sql_select("SELECT booking_no, item_group_id, description, uom, qty,   remarks from wo_non_ord_booking_acc_dtls where  booking_no=$txt_booking_no");
				    		if ( count($data_array)>0)
				    		{
				    			$l=1;
				    			$tot_qnty=0;
				    			foreach( $data_array as $key=>$row )
				    			{

				    				?>
				    				<tr>
				    					<td  align="center"> <? echo $l;?> </td>
				    					<td  align="center"> <? echo $lib_item_group_arr[$row[csf("item_group_id")]]; ?> </td>
				    					<td  align="center"> <? echo $row[csf("description")]; ?> </td>
				    					<td  align="center"> <? echo $unit_of_measurement[$row[csf("uom")]]; ?> </td>
				    					<td  align="center"> <? echo $qnty=$row[csf("qty")]; ?> </td>
				    					<td  align="center"> <? echo $row[csf("remarks")]; ?> </td>
				    				</tr>
				    				<?
				    				$l++;
				    				$tot_qnty+=$qnty;
				    			}
				    			?>
				    			<tr>
				    				<td colspan="4" align="right"><strong>Grand Total</strong> </td>
				    				<td  align="center"> <? echo $tot_qnty; ?> </td>
				    				<td  align="center">  </td>
				    			</tr>
				    				<?
				    		}

				    		?>
				    	</tbody>
				    </table>
			</td>
			<td rowspan="3">
				<table class="rpt_table" align="right" border="1" cellpadding="0" cellspacing="0" rules="all" width="500">
					<thead>
						<tr>
							<th colspan="3">Yarn Summary</th>
						</tr>
						<tr>
							<th>Count</th>
							<th>Composition</th>
							<th>Type</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?php 

							$sql="SELECT count_id,copm_one_id, type_id, sum(cons_qnty) as cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and booking_no ='".str_replace("'", "", $txt_booking_no)."' group by count_id,copm_one_id, type_id order by count_id";
							//echo $sql;
							$yarn_dtls = sql_select($sql);

							//print_r($composition);die;
							
						$composition_arr = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");


							$count_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
							foreach ($yarn_dtls as $yarnData) 
							{
								?>
								<tr id="yarncost_<? echo $i; ?>" align="center">
						                <td>
							               <? echo $count_arr[$yarnData[csf('count_id')]];  ?>
							               
						                </td>
						                <td><? echo $composition_arr[$yarnData[csf('copm_one_id')]];    ?></td>
						                <td> <?php echo $yarn_type[$yarnData[csf('type_id')]]; ?> </td>
						               
						                <td>
						                    <? echo number_format($yarnData[csf('cons_qnty')],4); ?>
						                </td>
						            </tr>
								<?
								$i++;
							}
						 ?>
					</tbody>
				</table>
			</td>
		</tr>
    </table>
    <br/>
        <table style="margin-top: 10px;" class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th align="left" width="40">Sl</th>
                    <th align="left" >Special Instruction</th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no=$txt_booking_no");
                if(count($data_array)>0)
                {
					$l=1;
					foreach( $data_array as $key=>$row )
					{
						?>
						<tr  align="">
                            <td> <? echo $l;?> </td>
                            <td> <? echo $row[csf("terms")]; ?> </td>
						</tr>
						<?
						$l++;
					}
                }

                ?>
            </tbody>
        </table>
        </br>
        <?
		$sql_img=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and master_tble_id=$txt_booking_no and file_type=1");
		?>
        <table class="rpt_table" width="100%"  border="0" cellpadding="0" cellspacing="0" rules="all">

        <tr>
        <td colspan="8" height="100">
       <?
        foreach($sql_img as $img)
		{
		?>
        	<img  src='../../<? echo $img[csf('image_location')]; ?>' height='180px' width='320px' /> &nbsp;&nbsp;
        <?
		}
		?>
        </td>
        </tr>
        </table>
        <br/>
	<br/><br/>
          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
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

if ($action=="save_update_delete")
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
		if($db_type==0)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}


		$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
		$field_array="id, booking_type, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, supplier_id, attention, ready_to_approved, team_leader, dealing_marchant, grouping,remarks, inserted_by, insert_date, entry_form_id";
		 $data_array ="(".$id.",4,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_int_ref.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','140')";
		 //echo "10** insert into wo_non_ord_samp_booking_mst ($field_array) values $data_array";die;txt_remarks
		 $rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);


		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$sales_order=0;
		$ready_to_approved_id=str_replace("'","",$cbo_ready_to_approved);
		$ready_to_approved_id_arr=array(1,2);
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			if(!in_array($ready_to_approved_id,$ready_to_approved_id_arr))
			{
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
			}
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="buyer_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*supplier_id*attention*ready_to_approved*team_leader*dealing_marchant*grouping*remarks*updated_by*update_date";
		$data_array ="".$cbo_buyer_name."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_int_ref."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_non_ord_samp_booking_mst",$field_array,$data_array,"id","".$update_id."",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
			    oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_non_ord_samp_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row)
		{
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1)
		{
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$sql_yarn_iss_knit_prod=sql_select("SELECT booking_no,entry_form,issue_number as challan  from inv_issue_master where entry_form=3  and item_category=1 and issue_purpose=8 and booking_no=$txt_booking_no and issue_basis=1
			 union SELECT booking_no,entry_form,recv_number as challan  from inv_receive_master  where entry_form=2  and booking_no=$txt_booking_no and receive_basis=1");
		if(count($sql_yarn_iss_knit_prod)>0)
		{
			if($sql_yarn_iss_knit_prod[0][csf("entry_form")]==2)
			{
				echo "knit**".$sql_yarn_iss_knit_prod[0][csf("challan")];
				disconnect($con);die;
			}
			if($sql_yarn_iss_knit_prod[0][csf("entry_form")]==3)
			{
				echo "yarn**".$sql_yarn_iss_knit_prod[0][csf("challan")];
				disconnect($con);die;
			}

		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows)
		{
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order)
		{
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2)
		{
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number)
			{
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
 		$field_array="updated_by*update_date*status_active*is_deleted";
 		$field_array2="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$data_array2="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID_dtls=sql_delete("wo_non_ord_samp_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID && $rID_dtls){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtls){
				 oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				 oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="fabric_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script>

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}


function js_set_value(data)
{

	var data=data.split('_');
	var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller');
	var fabric_yarn_description_arr=fabric_yarn_description.split("**");
	var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
    document.getElementById('fab_des_id').value=data[0];
	document.getElementById('fab_nature_id').value=data[1];
	document.getElementById('fab_desctiption').value=trim(fabric_description);
	document.getElementById('fab_gsm').value=trim(data[3]);
	document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
	document.getElementById('process_loss').value=trim(data[4]);
	document.getElementById('construction').value=trim(data[2]);
	document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
    parent.emailwindow.hide();


}
</script>
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="fab_des_id" name="fab_des_id" />
<input type="hidden" id="fab_nature_id" name="fab_des_id" />
<input type="hidden" id="fab_desctiption" name="fab_des_id" />
<input type="hidden" id="fab_gsm" name="fab_gsm" />
<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
<input type="hidden" id="process_loss" name="process_loss" />
<input type="text" id="construction" name="construction" />
<input type="text" id="composition" name="composition" />


<?
	/*$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$arr=array (0=>$item_category,2=>$composition, 4=>$composition, 7=>$color_range,8=>$lib_yarn_count,9=>$yarn_type, 10=>$lib_yarn_count,11=>$yarn_type,13=>$row_status);
	echo  create_list_view ( "list_view", "Fab Nature, Construction,Comp-1,%,Comp-2,%,GSM/Weight,Color Range,Cotton Count,Cotton Type,Denier Count,Denier Type,Stich Length,Status", "100,100,100,50,90,50,80,100,70,100,70,100,75,95","1230","350",0, "select fab_nature_id,construction,copm_one_id,percent_one,copm_two_id, percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active,id from  lib_yarn_count_determination  where  fab_nature_id= '$fabric_nature' and is_deleted=0", "js_set_value", "id,fab_nature_id,construction,copm_one_id,percent_one,copm_two_id,percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id", "",1, "fab_nature_id,0,copm_one_id,0,copm_two_id,0,0,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,0,status_active", $arr , "fab_nature_id,construction,copm_one_id,percent_one,copm_two_id,percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0') ;*/
	                    $composition_arr=array();
					    $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

						$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
						$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and  b.is_deleted=0 order by a.id,b.id";

						//$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
						$data_array=sql_select($sql);
						if (count($data_array)>0)
					    {
							foreach( $data_array as $row )
							{
								if(array_key_exists($row[csf('id')],$composition_arr))
								{
									$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";

								}
								else
								{
									$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
								}
							}
						}

		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 and  b.is_deleted=0 group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss order by a.id";
		$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$lib_yarn_count,9=>$yarn_type);
		echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stich Length,Process Loss,Composition", "100,100,100,100,90,50,300","950","350",0, $sql, "js_set_value", "id,fab_nature_id,construction,gsm_weight,process_loss", "",1, "fab_nature_id,0,0,color_range_id,0,0,id", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0') ;
?>
</form>
</div>
</body>
</html>
<?
}

if($action=="sample_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);


$bh_qty_arr=array();

$bh_qty_sql=sql_select("select a.id,b.id as dtls_id,sum(c.size_qty) as tsizeqty,sum(c.bh_qty) as tbh_qty from sample_development_mst a,sample_development_dtls b,sample_development_size c where a.id=b.sample_mst_id and  b.id=c.dtls_id group by a.id,b.id");
	foreach( $bh_qty_sql  as $row)
	{
		$bh_qty_arr[$row[csf('id')]][$row[csf('dtls_id')]]['size_qty']=$row[csf('tsizeqty')];
		$bh_qty_arr[$row[csf('id')]][$row[csf('dtls_id')]]['bh_qty']=$row[csf('tbh_qty')];
		//echo $bh_qty_arr[$row[csf('id')]]['bh_qty'];
	}

?>
<script>
function js_set_value(id,style_no,sample_id,article_no,bh_qty,gmt_color,size_qty,dtls_id,sample_name)
{

	document.getElementById('style_id').value=trim(id);
	document.getElementById('style_no').value=trim(style_no);
	document.getElementById('sample_id').value=trim(sample_id);
	document.getElementById('article_no').value=trim(article_no);
	document.getElementById('bh_qty').value=trim(bh_qty);
	document.getElementById('gmt_color').value=trim(gmt_color);
	document.getElementById('size_qty').value=trim(size_qty);
	document.getElementById('hid_dtls_id').value=trim(dtls_id);
	document.getElementById('sample_name_id').value=trim(sample_name);
    parent.emailwindow.hide();


}
$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,'');
	});
</script>
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="style_id" name="style_id" />
<input type="hidden" id="style_no" name="style_no" />
<input type="hidden" id="sample_id" name="sample_id" />
<input type="hidden" id="article_no" name="article_no" />
<input type="hidden" id="bh_qty" name="bh_qty"  />
<input type="hidden" id="gmt_color" name="gmt_color"  />
<input type="hidden" id="size_qty" name="size_qty"  />
<input type="hidden" id="hid_dtls_id" name="hid_dtls_id"  />
<input type="hidden" id="sample_name_id" name="sample_name_id"  />



	<?

		  $sql= "select  a.id, a.style_ref_no, a.company_id,a.buyer_name,a.article_no, b.id as bid,b.sample_name,b.sample_color,a.requisition_number_prefix_num,b.working_factory,b.receive_date_from_factory,b.sent_to_factory_date,b.sent_to_buyer_date,b.approval_status,b.status_date,b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name'  and a.is_deleted=0 and b.is_deleted=0 order by a.id";
	$nameArray=sql_select( $sql );

	?>
	<div style="width:1000px;">
	    <table width="967" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="35">SL</th>
					<th width="57">Style Id</th>
                    <th width="57">Style</th>
                    <th width="57">Requisition No</th>
					<th width="109">Sample Name</th>
					<th width="78">Sample Color</th>
					<th width="105">Working Factory</th>
					<th width="100">Buyer Recieve Date</th>
					<th width="100">Sent To factory</th>
					<th width="100">Submission to Buyer</th>
					<th width="36">Approval Status</th>
					<th >Status Date</th>
				</tr>
		</thead>
	     </table>
	<div id="" style="max-height:400px; width:1000px; overflow-y:scroll" >
	    <table width="967" cellspacing="0" cellpadding="0" border="0" rules="all" id="tbl_list_search"  class="rpt_table" align="left">

			<tbody>
	        <?
			$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	  		$color_name_arr=return_library_array( "select id, color_name from  lib_color where status_active=1 and is_deleted=0",'id','color_name');

			/*$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$color_name_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');

	$arr=array (2=>$sample_name_arr,3=>$color_name_arr,8=>$approval_status);

	echo  create_list_view ( "list_view1", "Style Id,Style,Sample Name,Sample Color,Working Factory,Buyer Recieve Date,Sent To factory,Submission to Buyer,Approval Status,Status Date", "60,100,100,90,100,80,80,80,85,80","1005","300",0, $sql, "js_set_value", "id,style_ref_no,sample_name,article_no","", 1, "0,0,sample_name,sample_color,0,0,0,0,approval_status,0", $arr , "id,style_ref_no,sample_name,sample_color,working_factory,recieve_date_from_buyer,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date", "../woven_order/requires/sample_requisition_booking_non_order_controller", 'setFilterGrid("list_view1",-1);','0,0,0,0,0,3,3,3,0,3,3' ) ;
	 exit();*/
			//id,style_ref_no,sample_name,sample_color,working_factory,recieve_date_from_buyer,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date
	         $i=1;
					foreach ($nameArray as $row)
				   {
	        ?>
			  	<tr  bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf("id")]; ?>','<? echo $row[csf("style_ref_no")]; ?>','<? echo $row[csf("sample_name")]; ?>','<? echo $row[csf("article_no")]; ?>','<? echo $bh_qty_arr[$row[csf('id')]][$row[csf('bid')]]['bh_qty'] ?>','<? echo $color_name_arr[$row[csf("sample_color")]]; ?>','<? echo $bh_qty_arr[$row[csf('id')]][$row[csf('bid')]]['size_qty']; ?>','<? echo $row[csf('bid')]; ?>','<? echo $row[csf('sample_name')]; ?>')">
		            <td width="35"><? echo $i; ?></td>
					<td width="57"><? echo $row[csf("id")];?></td>
				    <td width="57" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("style_ref_no")];?></td>
                    <td width="57"><? echo $row[csf("requisition_number_prefix_num")];?></td>
				   	<td width="109" style="word-wrap: break-word;word-break: break-all;"><? echo $sample_name_arr[$row[csf("sample_name")]];?></td>
				    <td width="78"><? echo $color_name_arr[$row[csf("sample_color")]];?></td>
				    <td width="105"><? echo $row[csf("working_factory")];?></td>
		            <td width="100" align="right"><? echo change_date_format($row[csf("recieve_date_from_buyer")]);?></td>
				    <td width="100" align="right"><? echo change_date_format($row[csf("sent_to_factory_date")]);?></td>
		            <td width="100" align="right"><? echo change_date_format($row[csf("sent_to_buyer_date")]);?></td>
		            <td width="46" align="right"><? echo $approval_status[$row[csf("approval_status")]];?></td>
		            <td align="right"><? echo change_date_format($row[csf("status_date")]);?></td>
			  	</tr>

	            <? $i++; }?>
	            </tbody>
		</table>

	     </div>
     </div>



</form>
</div>
</body>
</html>
<?
}


if ($action=="fabric_booking_popup")
{
	extract($_REQUEST);
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>

	<script>
	 function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}


    </script>

</head>

<body>
    <div align="center">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1130" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                	<tr>
                        <th align="center" colspan="6">
                        <?
                        echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                        ?>
                        </th>
                        <th align="center" colspan="3"><input type="checkbox" id="chk_job_wo_po" name="chk_job_wo_po" onClick="set_checkvalue()" value="0"> Booking Without Req.</th>
                    </tr>
                    <tr>
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
						<th width="100">Requisition. No</th>
                        <th width="100">Style Ref.</th>
						<th width="80">Style Desc.</th>
                        <th width="200">Date Range</th>
                        <th width="100">Internal Ref.</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> <input type="hidden" id="selected_booking">
                        <?
                        //if($_SESSION['logic_erp']['company_id'])$company_cond=" and id in(".$_SESSION['logic_erp']['company_id'].")"; else $company_cond="";
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'sample_requisition_booking_non_order_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );");
                        ?>
                        </td>
                        <td id="buyer_td">
                        <?
                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                        ?>	</td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                        <td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px" placeholder="Write Prifix" /></td>
						<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Write" /></td>


                        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" />
                        </td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                        <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_internal_ref').value, 'create_booking_search_list_view', 'search_div', 'sample_requisition_booking_non_order_controller','setFilterGrid(\'table_body\',1)')" style="width:100px;" /></td>
                    </tr>
                </tbody>
                	<tr>
                        <th colspan="9"  align="center" height="25" valign="middle">
							<?
                            echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
                            echo load_month_buttons();
                            ?>
                        </th>
                    </tr>
            </table>
        </form>
        <br>
        <div id="search_div"> </div>
    </div>
    <script type="text/javascript">
    	$(document).ready(function(){
    		var company = '<? echo $company;?>';
    		var buyer = '<? echo $buyer;?>';
    		document.getElementById('cbo_company_mst').value=company;
    		if(company !=0)
    		{
    			document.getElementById('cbo_company_mst').setAttribute("disabled",true);
    			load_drop_down( 'sample_requisition_booking_non_order_controller', company, 'load_drop_down_buyer_pop', 'buyer_td' );
    		}
    		document.getElementById('cbo_buyer_name').value=buyer;
    		if(buyer !=0)
    		{
    			document.getElementById('cbo_buyer_name').setAttribute("disabled",true);
    		}
    		// alert(company);
    	});
    </script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	$internal_ref=$data[11];
	$internal_ref_cond='';
	 if ($internal_ref)
	 {
       $internal_ref_cond=" and a.grouping like '%$internal_ref%'";
	 }

	//echo $internal_ref_cond;
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	//echo $data[10].'D';

	if($db_type==0)
	{
		// $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
		if (str_replace("'","",$data[8])!="") $req_no_cond=" and requisition_number_prefix_num like '%$data[8]%' "; else $req_no_cond="";
		if (str_replace("'","",$data[9])!="") $style_ref_cond=" and style_ref_no like '%$data[9]%' "; else $style_ref_cond="";
	}
	if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
		if (str_replace("'","",$data[8])!="") $req_no_cond=" and requisition_number_prefix_num ='$data[8]' "; else $req_no_cond="";
		if (str_replace("'","",$data[9])!="") $style_ref_cond=" and style_ref_no ='$data[9]' "; else $style_ref_cond="";
	}
	if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
		if (str_replace("'","",$data[8])!="") $req_no_cond=" and requisition_number_prefix_num like '$data[8]%' "; else $req_no_cond="";
		if (str_replace("'","",$data[9])!="") $style_ref_cond=" and style_ref_no like '$data[9]%' "; else $style_ref_cond="";
	}
	if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
		if (str_replace("'","",$data[8])!="") $req_no_cond=" and requisition_number_prefix_num like '%$data[8]' "; else $req_no_cond="";
		if (str_replace("'","",$data[9])!="") $style_ref_cond=" and style_ref_no like '%$data[9]' "; else $style_ref_cond="";
	}

	//$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	//echo "select id,style_ref_no,requisition_number_prefix_num from sample_development_mst where entry_form_id=117 $req_no_cond $style_ref_cond";
	$sql_style=sql_select("select id,style_ref_no,requisition_number_prefix_num from sample_development_mst where entry_form_id=117 $req_no_cond $style_ref_cond");
	$req_mst_id="";
	foreach($sql_style as $row)
	{
		$style_library[$row[csf('id')]]=$row[csf('style_ref_no')];
		$req_no_library[$row[csf('id')]]=$row[csf('requisition_number_prefix_num')];
		if(str_replace("'","",$data[8])!="" || str_replace("'","",$data[9])!="")
		{
				$req_mst_id.=$row[csf('id')].',';
		}
	}
	$booking_without_req=str_replace("'","",$data[10]);

	$req_mst_id=rtrim($req_mst_id,',');
	$req_mst_id=implode(",",array_unique(explode(",",$req_mst_id)));
	if($req_mst_id!='') $req_mst_cond="and b.style_id in($req_mst_id)";else $req_mst_cond="";

    $approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	if($booking_without_req==1)
	{
		 $dtls_found= "SELECT  a.id as mst_id,a.booking_no  from wo_non_ord_samp_booking_mst  a  , wo_non_ord_samp_booking_dtls b  where  a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  and   a.entry_form_id=140 and  b.entry_form_id=140 and   $company  $buyer  and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0   ";
		$booking_dtls_arr=array();
		$booking_not_in_cond="";
		foreach(sql_select($dtls_found) as $v)
		{
			$booking_dtls_arr[$v[csf("mst_id")]]=$v[csf("mst_id")];
		}

		if(count($booking_dtls_arr)>999 && $db_type==2)
		{

			$booking_not_in_cond .="";
			$chunk=array_chunk($booking_dtls_arr, 999);
			foreach($chunk as $val)
			{
				$val_imp=implode(",", $val);
				if($booking_not_in_cond=="")
					$booking_not_in_cond.=" and (a.id not in ($val_imp)";
				else
					$booking_not_in_cond.=" and a.id not in ($val_imp)";
			}
			$booking_not_in_cond .=" ) ";
		}
		else
		{
			$booking_not_in_cond.=" and a.id not in( ".implode(",", $booking_dtls_arr).")";
		}

		   $sqls= "SELECT a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.grouping,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode, null as style_id,null as style_des from wo_non_ord_samp_booking_mst  a      where  a.entry_form_id=140   and   $company  $buyer $booking_date $booking_cond $internal_ref_cond  and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0  $booking_not_in_cond order by a.id desc";
	}
	else
	{
		 $sqls= "SELECT a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.grouping,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where  a.entry_form_id=140 and  b.entry_form_id=140 and   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $internal_ref_cond $booking_date $booking_cond $style_des_cond $req_mst_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0  order by b.id desc";
	}
	 //echo $sqls;


	?>
   <table class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="0" rules="all"  style="word-break:break-all;">
        <thead>
        	<tr>
                <th width="50">SL</th>
                <th width="100">Booking No</th>
                <th width="80">Booking Date</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="80">Pay Mode</th>
                <th width="80">Supplier</th>
                <th width="50">Style</th>
                <th width="80">Style Desc.</th>
                <th width="80">Int ref no</th>
                <th width="50">Approved</th>
                <th width="70">Is-Ready</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll">
    <table class="rpt_table" width="1060" cellpadding="0" cellspacing="0" border="0" rules="all" id="table_body" style="word-break:break-all;">
        <tbody>
			<?
            $i=1;
            //echo $sqls;
            $sql_data=sql_select($sqls);
            foreach($sql_data as $row){
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                    <td width="50"><? echo $i;?></td>
                    <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>
                    <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                    <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                    <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                    <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                    <td width="80">
                    	<? echo $pay_mode[$row[csf('pay_mode')]];?>
                    </td>
                    <td width="80">
						<?
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                    </td>
                    <td width="50"><? echo $style_library[$row[csf('style_id')]];?></td>
                    <td width="80"><? echo $row[csf('style_des')];?></td>
                    <td width="80"><? echo $row[csf('grouping')];?></td>
                    <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
                    <td width="50"><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
				</tr>
				<?
				$i++;
            }
            ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
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

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
			}
			var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","sample_requisition_booking_non_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
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
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no=$txt_booking_no");// quotation_id='$data'
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
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
					$data_array2=sql_select("select id, terms from  lib_terms_condition  where is_default=1");// quotation_id='$data'
					foreach( $data_array2 as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
<script type="text/javascript">
	var data_array='<? echo count($data_array) ;?>';
	var permissions='<? echo $permission ;?>';
	if(data_array*1>0)
	{
		set_button_status(1, permissions, 'fnc_fabric_booking_terms_condition',1);
 	}

</script>
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

	if ($operation==0 || $operation==1 )  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.",140)";
			$id=$id+1;
		 }
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where entry_form=140 and  booking_no =".$txt_booking_no."",0);
		if($operation==0)
		{
			$rID_de3=1;
		}

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3 ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3 ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}

}


if($action=="save_update_delete_trims_acc")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0 || $operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_non_ord_booking_acc_dtls", 1 ) ;
		 $field_array="id,booking_no,item_group_id,description,uom,qty,remarks";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $trim_group="itemgroup_".$i;
			 $description="description_".$i;
			 $cons_uom="uom_".$i;
			 $qty="qty_".$i;
			 $remarks="remarks_".$i;

			if ($i!=1) $data_array .=",";
			$data_array.="(".$id.",".$txt_booking_no.",".$$trim_group.",".$$description.",".$$cons_uom.",".$$qty.",".$$remarks.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de=execute_query( "delete from wo_non_ord_booking_acc_dtls where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_non_ord_booking_acc_dtls",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);

		//echo "10**".$data_array;die;

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="populate_data_from_search_popup")
{
	$requisition_id=return_field_value( "style_id", "wo_non_ord_samp_booking_dtls","booking_no='$data' and status_active=1 and is_deleted=0 and entry_form_id=140");
	$requisition_no=return_field_value( "requisition_number", "sample_development_mst","id='$requisition_id' and entry_form_id=117 and status_active=1 and is_deleted=0");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	 $sql= "SELECT booking_no,id,booking_date,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,is_approved,ready_to_approved,team_leader,	dealing_marchant,grouping,remarks  from wo_non_ord_samp_booking_mst  where booking_no='$data' and entry_form_id='140' and status_active=1 and is_deleted=0";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
 		echo "load_drop_down( 'requires/sample_requisition_booking_non_order_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n";
 		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('txt_int_ref').value = '".$row[csf("grouping")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";

		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "load_drop_down( 'requires/sample_requisition_booking_non_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_suplier', 'sup_td' )\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		/*if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5){
			echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		}else{
			echo "document.getElementById('cbo_supplier_name').value = '".$supplier_name_arr[$row[csf("supplier_id")]]."';\n";
		}*/

		//echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";

		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";


		if($is_approved==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
		echo "enable_disable('".$row[csf("fabric_source")]."');\n";
	 }
	 if($requisition_id)
	 {
	 	echo "fnc_load_tr('".$requisition_id."','2');\n";
	 	echo "set_button_status(1, permission, 'fnc_required_fabric_details_info',3,0);\n";
	 	echo "$('#txt_requisition').val('".$requisition_no."');\n";
		 echo "$('#hidden_requisition_id').val('".$requisition_id."');\n";
	 	// type 1 for requisition browse and 2 for booking browse
 	 }
	exit();
 }

if($action=="populate_details_data_from_for_update")
{
		if($db_type==0)
		{
		$data_array=sql_select("select id,body_part,body_type_id,item_qty,knitting_charge,style_id,style_des,sample_type,color_type_id,lib_yarn_count_deter_id as  lib_yarn_count_deter_id,construction,composition,fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,width_dia_type as dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,bh_qty,rf_qty,fabric_source,dtls_id,delivery_date  FROM wo_non_ord_samp_booking_dtls WHERE id ='".$data."'  and status_active=1 and	is_deleted=0");
		}
		if($db_type==2)
		{
		$data_array=sql_select("select id,body_part,body_type_id,item_qty,knitting_charge,style_id,style_des,sample_type,color_type_id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,construction,composition,fabric_description,gsm_weight,gmts_color,fabric_color,gmts_size,item_size,width_dia_type asdia_width,finish_fabric,process_loss,grey_fabric,rate,amount,yarn_breack_down,process_loss_method,article_no,yarn_details,remarks,bh_qty,rf_qty,fabric_source,dtls_id,delivery_date FROM wo_non_ord_samp_booking_dtls WHERE id ='".$data."'  and status_active=1 and	is_deleted=0");
		}

		foreach ($data_array as $row)
		{
		$style_id=$row[csf("style_id")];
		$style=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		echo "document.getElementById('cbo_body_part').value = '".$row[csf("body_part")]."';\n";
		echo "document.getElementById('cbo_color_type').value = '".$row[csf("color_type_id")]."';\n";
		echo "document.getElementById('txt_style').value = '".$row[csf("style_id")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$style."';\n";
		echo "document.getElementById('txt_style_des').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type")]."';\n";
		echo "document.getElementById('libyarncountdeterminationid').value = '".$row[csf("lib_yarn_count_deter_id")]."';\n";
		echo "document.getElementById('construction').value = '".$row[csf("construction")]."';\n";
		echo "document.getElementById('composition').value = '".$row[csf("composition")]."';\n";
		echo "document.getElementById('yarnbreackdown').value = '".$row[csf("yarn_breack_down")]."';\n";
		echo "document.getElementById('txt_fabricdescription').value = '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_gsm').value = '".$row[csf("gsm_weight")]."';\n";
		echo "document.getElementById('txt_gmt_color').value = '".$color_library[$row[csf("gmts_color")]]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("fabric_color")]]."';\n";
		echo "document.getElementById('txt_gmts_size').value = '".$size_library[$row[csf("gmts_size")]]."';\n";
		echo "document.getElementById('txt_size').value = '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_dia_width').value = '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_finish_qnty').value = '".$row[csf("finish_fabric")]."';\n";
		echo "document.getElementById('txt_process_loss').value = '".$row[csf("process_loss")]."';\n";
		echo "document.getElementById('txt_grey_qnty').value = '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('txt_knitting_charge').value = '".$row[csf("knitting_charge")]."';\n";
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n";
        echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('process_loss_method').value = '".$row[csf("process_loss_method")]."';\n";
		echo "document.getElementById('txt_article_no').value = '".$row[csf("article_no")]."';\n";
		echo "document.getElementById('txt_yarn_details').value = '".$row[csf("yarn_details")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_body_type').value = '".$row[csf("body_type_id")]."';\n";
		echo "document.getElementById('txt_item_qty').value = '".$row[csf("item_qty")]."';\n";
		echo "document.getElementById('txt_delivery_dates').value = '".change_date_format($row[csf("delivery_date")])."';\n";



		echo "document.getElementById('txt_bh_qty').value = '".$row[csf("bh_qty")]."';\n";
		echo "document.getElementById('txt_rf_qty').value = '".$row[csf("rf_qty")]."';\n";
		echo "document.getElementById('cbo_fabric_source_dtls').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('txt_style_dtls_id').value = '".$row[csf("dtls_id")]."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";
		//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2)
		}
		/*echo "if($('#cbo_fabric_source').val()!=0){";
		echo "$('#cbo_fabric_source_dtls').attr('disabled',false);\n";
		echo "} else { $('#cbo_fabric_source_dtls').attr('disabled',true) }";*/

		echo "$('#cbo_fabric_source_dtls').attr('disabled',false);\n";
		  if($style !='')
		  {
		  	echo "$('#txt_article_no').attr('disabled','true');\n";
			echo "$('#txt_bh_qty').attr('disabled','true');\n";
			echo "$('#txt_rf_qty').attr('disabled','true');\n";
			echo "$('#txt_gmts_size').attr('disabled','true');\n";
			echo "$('#cbo_sample_type').attr('disabled','true');\n";
			echo "$('#txt_gmt_color').attr('disabled','true');\n";
			echo "$('#txt_article_no').attr('disabled','true');\n";

		  }
		exit();

}


if($action=="acc_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
	 //$lib_item_uom_arr=return_library_array( "select trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by trim_uom", "id", "trim_uom");

	?><script> var trim_uom_arr=Array(); </script><?

	$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
	foreach ($itemArray as $row)
	{
	?>
		<script> trim_uom_arr[<? echo $row[csf('id')];?>]='<? echo $row[csf('trim_uom')].'*'.$unit_of_measurement[$row[csf('trim_uom')]];?>' </script>
	<?
		$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
	}


?>
	<script>

function load_trims_uom(item_group,str){
	var uom=trim_uom_arr[item_group].split('*');
	var html="<option value='"+uom[0]+"'>"+uom[1]+"</option>";
	document.getElementById('uom_'+str).innerHTML=html;
}


function add_break_down_tr(i)
 {
	var row_num=$('#tbl_accessories_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		 $("#tbl_accessories_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return '' }
			});
		  }).end().appendTo("#tbl_accessories_details");

		 $('#itemgroup_'+i).removeAttr("onChange").attr("onChange","load_trims_uom(this.value,"+i+");");

		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");

		 $('#increase_'+i).removeAttr("value").attr("value","+");
		 $('#decrease_'+i).removeAttr("value").attr("value","-");


		 $('#termscondition_'+i).val("");
		 $('#tbl_accessories_details tbody tr:last td:first-child').text(i);
	}

}

function fn_deletebreak_down_tr(rowNo)
{

		var numRow = $('table#tbl_accessories_details tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_accessories_details tbody tr:last').remove();
		}

}


function fnc_trims_acc( operation )
{
	    var row_num=$('#tbl_accessories_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{


			data_all=data_all+get_submitted_data_string('txt_booking_no*itemgroup_'+i+'*description_'+i+'*uom_'+i+'*qty_'+i+'*remarks_'+i,"");
		}
		var data="action=save_update_delete_trims_acc&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sample_requisition_booking_non_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_acc_reponse;
}

function fnc_trims_acc_reponse()
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


    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
    <form id="termscondi_1" autocomplete="off">
    <input type="hidden" id="txt_booking_no"   name="txt_booking_no"  value="<? echo str_replace("'","",$txt_booking_no);?>"  />
    <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_accessories_details" rules="all">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Item Group</th>
                    <th width="100">Description</th>
                    <th width="70">UOM</th>
                    <th width="70">Qty</th>
                    <th>Remarks</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
            <?

           $sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_non_ord_booking_acc_dtls where booking_no=".$txt_booking_no."";

            $data_array=sql_select($sql);
            $is_updates=0;

            if ( count($data_array)>0)
            {
            	$is_updates=1;

                $i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $lib_item_group_arr,"", 1, "-- Select --", $row[csf('item_group_id')], "load_trims_uom(this.value,$i)",0,"" );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  />
                            </td>

                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('uom')], "",1,$row[csf('uom')] );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value="<? echo$row[csf('qty')];?>"  />
                            </td>

                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo$row[csf('remarks')];?>"  />
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

                $i=1;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $lib_item_group_arr,"", 1, "-- Select --", $row[csf('trim_group')], "load_trims_uom(this.value,$i)",0,"" );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  />
                            </td>

                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('cons_uom')], "",0,$row[csf('cons_uom')] );
                            ?>
                            </td>

                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value=""  />
                            </td>

                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value=""  />
                            </td>


                            <td>
                            <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

                            </td>
                        </tr>
                    <?
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
                        echo load_submit_buttons( $permission, "fnc_trims_acc", $is_updates,0 ,"reset_form('termscondi_1','','','','')",1) ;
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


if($action=="requisition_id_popup")
{
 	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);

	$int_ref_cond="";
	if($int_ref=="") $int_ref_cond=""; else $int_ref_cond="disabled";
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th colspan="9">
                      <? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                    </th>
                </tr>
                <tr>
                    <th width="140">Company Name  </th>
                    <th width="157">Buyer Name</th>
                    <th width="70">Requisition No</th>
                    <th width="80">Style ID</th>
                    <th width="90">Style Name</th>
                    <th width="80">Internal Ref.</th>
                    <th width="130" colspan="2">Est. Ship Date</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>
            </thead>
            <tr class="general">
                <td><input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --","$company","load_drop_down( 'sample_requisition_booking_non_order_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );",'1' ); ?>
                </td>
                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 157, "select id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0   order by buyer_name","id,buyer_name", 1, "-- Select Buyer --","$buyer","",'1' ); ?></td>
                <td><input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num" /></td>

                <td><input type="text" style="width:70px" class="text_boxes" name="txt_style_id" id="txt_style_id" /></td>
                <td><input type="text" style="width:80px" class="text_boxes" name="txt_style_name1" id="txt_style_name1" /></td>
                <td><input type="text" style="width:70px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="to"></td>
                <td>
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('txt_internal_ref').value, 'create_requisition_id_search_list_view', 'search_div', 'sample_requisition_booking_non_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
    </form>
    <div id="search_div"></div>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	$variable_setting_app= "select b.approval_need as approval_need from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and b.page_id=30 and a.company_id ='$data[2]' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.setup_date=(select max(setup_date) from approval_setup_mst where company_id='$data[2]' and status_active=1 and is_deleted=0 )";
	$variable_setting_app_sql=sql_select($variable_setting_app);
	$variable_app_value=$variable_setting_app_sql[0][csf("approval_need")];
	//$variable_cond=($variable_app_value==1)? " and id in( select mst_id from approval_history where entry_form=25)  " : "";
	if($variable_app_value==1) $variable_cond="and is_acknowledge=1"; else $variable_cond="";

	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		if ($data[8]!="") $intRef_cond=" and internal_ref='$data[8]'"; else $intRef_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		if ($data[8]!="") $intRef_cond=" and internal_ref like '%$data[8]%' "; else $intRef_cond="";
	}
	else if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		if ($data[8]!="") $intRef_cond=" and internal_ref like '$data[8]%' "; else $intRef_cond="";
	}
	else if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		if ($data[8]!="") $intRef_cond=" and internal_ref like '%$data[8]' "; else $intRef_cond="";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant);
	$sql="";
	if($db_type==0) $year_cond="SUBSTRING_INDEX(insert_date, '-', 1)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";

	$sql= "select id, requisition_number_prefix_num, $year_cond as year, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant from sample_development_mst where   sample_stage_id in(2,3) and entry_form_id=117 and  status_active=1 and is_deleted=0 and id not in(select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0 and entry_form_id=140) $company $style_id_cond $style_cond $estimated_shipdate $requisition_num $variable_cond $intRef_cond $buyer order by id DESC";

	// echo $sql;
	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchant", "60,140,140,100,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,dealing_marchant", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_requisition_search_popup")
{
    $res =sql_select("select id, requisition_number, company_id, buyer_name, internal_ref from sample_development_mst where id='$data' and entry_form_id=117 and is_deleted=0 and status_active=1");
    foreach($res as $result)
    {
    	echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
    	echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
    	echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
     	echo "$('#txt_requisition').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#txt_int_ref').val('".$result[csf('internal_ref')]."');\n";
    	echo "fnc_load_tr('".$result[csf('id')]."',1);\n";
    }
     exit();
 }

if($action=="load_php_dtls_form")
{
	$data=explode("___", $data);
	//echo $data[1].'=D';
	if($data[1]=="1") 
	{
		 $sql_fabric="Select b.id, b.sample_mst_id, b.sample_name, b.sample_mst_id, b.gmts_item_id, b.body_part_id, b.fabric_nature_id, b.fabric_description, b.determination_id, b.gsm, b.dia, b.sample_color, b.color_type_id, b.width_dia_id, b.uom_id, b.required_dzn, b.color_data, b.remarks_ra, c.color_id as gmt_color_id, c.fabric_color, c.qnty as required_qty, c.grey_fab_qnty, c.process_loss_percent from sample_development_fabric_acc b, sample_development_rf_color c where b.id=c.dtls_id and b.sample_mst_id=c.mst_id and b.sample_mst_id='$data[0]' and c.grey_fab_qnty>0 and b.form_type=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id ASC";

		$sql_resultf =sql_select($sql_fabric);  $i=1;
		$deteIdArr=array();
		foreach($sql_resultf as $row)
			{
				$deteIdArr[$row[csf('determination_id')]]=$row[csf('determination_id')];
			}
		
			$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0  and b.is_deleted=0 and a.id in(".implode(",",$deteIdArr).")");
			//echo "SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0  and b.is_deleted=0 and a.id in(".implode(",",$deteIdArr).")";
			$fab_arr=array();
			foreach($select_deter as $row)
			{
				//$fab_arr[$row[csf("id")]]=$row[csf("sample_name")];
				$fab_arr[$row[csf("id")]].=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
				/*$fab_arr[$row[csf("id")]]['percent']=$row[csf('percent')];
				$fab_arr[$row[csf("id")]]['copmposition_id']=$row[csf('copmposition_id')];
				$fab_arr[$row[csf("id")]]['count_id']=$row[csf('count_id')];
				$fab_arr[$row[csf("id")]]['type_id']=$row[csf('type_id')];*/
			}
			
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				$yarn_cons_str=rtrim($fab_arr[$row[csf("determination_id")]],'##');
				//echo $ff.'<br>';
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_<?=$i; ?>">
						<?
							$sql="select id,sample_name from lib_sample where status_active=1 and is_deleted=0";
							echo create_drop_down( "cboRfSampleName_$i", 95, $sql,"id,sample_name", '', "", $row[csf("sample_name")],"",'1');
						?>
					</td>
					<td align="center" id="rfItemId_1">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$data'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","1",$gmtsf);
						?>
					</td>
					<td align="center" id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');",'1'); ?></td>
					<td align="center" id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","1","2,3"); ?></td>
					<td align="center" id="rf_fabric_description_<?=$i; ?>" title="<?=$row[csf("fabric_description")]; ?>">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" onDblClick="open_fabric_description_popup(<?=$i; ?>)" readonly value="<?=$row[csf("fabric_description")]; ?>" disabled="" />
                        <input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
					</td>
					<td align="center" id="rf_gsm_<?=$i; ?>">
                        <input style="width:40px;" type="text" class="text_boxes_numeric"  name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>" disabled="" />
                        <input type="hidden" id="updateidbookdDtl_<?=$i; ?>" name="updateidbookdDtl_<?=$i; ?>" class="text_boxes" style="width:20px" />
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                         <input type="hidden" id="txtyarnconsbreakdown_<?=$i; ?>" name="txtyarnconsbreakdown_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$yarn_cons_str;?>" />
					</td>
					<td align="center" id="rf_dia_<?=$i; ?>"><input style="width:40px;" type="text" class="text_boxes"  name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" disabled=""/></td>
					<td align="center" id="rf_color_<?=$i; ?>" title="<?=$color_library[$row[csf("gmt_color_id")]];?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" readonly disabled=""  value="<?=$color_library[$row[csf("gmt_color_id")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfColorID_<?=$i; ?>" id="txtRfColorID_<?=$i; ?>" readonly disabled=""  value="<?=$row[csf("gmt_color_id")];?>"/>
                        <input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$row[csf("color_data")]; ?>"  class="text_boxes">
					</td>
					<td align="center" id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "",'1'); ?></td>
					<td align="center" id="rf_fab_color_<?=$i; ?>" title="<?=$color_library[$row[csf("fabric_color")]]; ?>">
                         <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabColor_<?=$i; ?>" id="txtRfFabColor_<?=$i; ?>" readonly disabled=""  value="<?=$color_library[$row[csf("fabric_color")]]; ?>"/>
                         <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfFabColorID_<?=$i; ?>" id="txtRfFabColorID_<?=$i; ?>" readonly disabled=""  value="<?=$row[csf("fabric_color")];?>"/>
					</td>
					<td align="center" id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], "",'1'); ?></td>
					<td align="center" id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",'1',"12,27,1,23" ); ?></td>
					<td style="display: none;" align="center" id="rf_req_dzn_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="write" value="<?=$row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i; ?>');" disabled="" /></td>
				   <td align="center" id="rf_req_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" value="<?=$row[csf('required_qty')]; ?>" onChange="calculate_requirement('<?=$i; ?>',1);" disabled /></td>
				   <td align="center" id="tdwoqty_<?=$i; ?>">
                   <input style="width:50px;" type="text" class="text_boxes_numeric" name="txtwoqty_<?=$i; ?>" id="txtwoqty_<?=$i; ?>" value="<?=$row[csf('required_qty')]; ?>" onChange="calculate_requirement('<?=$i; ?>',1);" />
                    <input style="width:50px;" type="hidden" class="text_boxes_numeric" name="txthiddenwoqty_<?=$i; ?>" id="txthiddenwoqty_<?=$i; ?>" readonly disabled=""  value="<?=$row[csf("required_qty")];?>"/>
                   </td>
					<td align="center" id="rf_reqs_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" onChange="calculate_requirement('<?=$i; ?>',1); fnc_process_loss_copy(<?=$i; ?>);" value="<?=$row[csf('process_loss_percent')]; ?>" disabled/></td>
					<td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" onChange="calculate_requirement('<?=$i; ?>',2);" value="<?=$row[csf('grey_fab_qnty')]; ?>" disabled/>
                    
                    </td>
					<td align="center" id="td_additional_process_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes" name="txtAdditionalProcess_<?=$i; ?>" id="txtAdditionalProcess_<?=$i; ?>" placeholder="write" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" onChange="calculate_amount('<?=$i; ?>');" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" /> </td>
                    <td align="center"><input style="width:50px;" type="text" class="text_boxes" name="txtremark_<?=$i; ?>" id="txtremark_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>" onDblClick="remark_popup('<?=$i; ?>');" placeholder="" /></td>
                    <td width="70">
                        <input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+" disabled="" />
                        <input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-"  disabled="" />
                        <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_<?=$i;?>" id="txtMemoryDataRf_<?=$i;?>" />
                    </td>
                </tr>
				<?
				$i++;
			}
		}
	}
	else
	{
		$sql_fabric="SELECT id as booking_dtls_id, booking_no, style_id as sample_mst_id, sample_type as sample_name, gmts_item_id, body_part as body_part_id, item_category as fabric_nature_id, fabric_description, gsm_weight as gsm,dia, color_all_data as color_data,yarn_cons_breakdown, color_type_id, fabric_color, gmts_color, width_dia_type as width_dia_id, uom as uom_id, req_dzn as required_dzn, finish_fabric as required_qty, dtls_id as dtls_id, process_loss, grey_fabric, rate, amount, lib_yarn_count_deter_id, remarks, wo_qty,additional_process from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0 and style_id='$data[0]' and entry_form_id=140";
		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_<?=$i; ?>">
						<?
							$sql="select id,sample_name from lib_sample where status_active=1 and is_deleted=0";
							echo create_drop_down( "cboRfSampleName_$i", 95, $sql,"id,sample_name", '', "", $row[csf("sample_name")],"",'1');
						?>
					</td>
					<td align="center" id="rfItemId_<?=$i; ?>">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$data'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","1",$gmtsf);
						?>
					</td>
					<td align="center" id="rf_body_part_<?=$i; ?>">
						<?
						echo create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');",'1');
						?>
					</td>
					<td align="center" id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","1","2,3"); ?></td>
                    <td align="center" id="rf_fabric_description_<?=$i; ?>" title="<?=$row[csf("fabric_description")]; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>" disabled="" />
                        <input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("lib_yarn_count_deter_id")]; ?>">
                    </td>
                    <td align="center" id="rf_gsm_<?=$i; ?>">
                        <input style="width:40px;" type="text" class="text_boxes_numeric" name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>" disabled="" />
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("dtls_id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf" class="text_boxes" style="width:20px" value="" />
                        <input type="hidden" id="updateidbookdDtl_<?=$i; ?>" name="updateidbookdDtl_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$row[csf("booking_dtls_id")]; ?>"  />
                        <input type="hidden" id="txtyarnconsbreakdown_<?=$i; ?>" name="txtyarnconsbreakdown_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$row[csf("yarn_cons_breakdown")]; ?>" />
                        
                    </td>
                    <td align="center" id="rf_dia_<?=$i; ?>"><input style="width:40px;" type="text" class="text_boxes" name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" disabled=""/></td>
                    <td align="center" id="rf_color_<?=$i; ?>" title="<?=$color_library[$row[csf("gmts_color")]];?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" readonly disabled="" value="<?=$color_library[$row[csf("gmts_color")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfColorID_<?=$i; ?>" id="txtRfColorID_<?=$i; ?>" value="<?=$row[csf("gmts_color")]; ?>"/>
                        <input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$row[csf("color_data")]; ?>" class="text_boxes">
                    </td>
                    <td align="center" id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "",'1'); ?></td>
                    <td align="center" id="rf_fab_color_<?=$i; ?>" title="<?=$color_library[$row[csf("fabric_color")]];?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabColor_<?=$i; ?>" id="txtRfFabColor_<?=$i; ?>" readonly disabled="" value="<?= $color_library[$row[csf("fabric_color")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfFabColorID_<?=$i; ?>" id="txtRfFabColorID_<?=$i; ?>" value="<?=$row[csf("fabric_color")];?>"/>
                    </td>
                    <td align="center" id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], "",'1'); ?></td>
                    <td align="center" id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",'1',"12,27,1,23" ); ?></td>
                    <td style="display: none;" align="center" id="rf_req_dzn_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i; ?>');" disabled="" /></td>
                    <td align="center" id="rf_req_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" value="<?=$row[csf("required_qty")]; ?>" onChange="calculate_requirement('<?=$i; ?>',1);" disabled/></td>
                    <td align="center" id="tdwoqty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtwoqty_<?=$i; ?>" id="txtwoqty_<?=$i; ?>" value="<?=$row[csf('wo_qty')]; ?>" onChange="calculate_requirement('<?=$i; ?>',1);" />
                     <input style="width:50px;" type="hidden" class="text_boxes_numeric" name="txthiddenwoqty_<?=$i; ?>" id="txthiddenwoqty_<?=$i; ?>" readonly disabled=""  value="<?=$row[csf("wo_qty")];?>"/>
                    </td>
                    <td align="center" id="rf_reqs_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" disabled="" onChange="calculate_requirement('<?=$i;?>',1); fnc_process_loss_copy(<?=$i; ?>);" value="<?=$row[csf("process_loss")]; ?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input disabled="" style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" value="<?=$row[csf("grey_fabric")]; ?>" onChange="calculate_requirement('<?=$i; ?>',2);"/></td>
					<td align="center" id="td_additional_process_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes" name="txtAdditionalProcess_<?=$i; ?>" id="txtAdditionalProcess_<?=$i; ?>" placeholder="write" value="<?=$row[csf("additional_process")];?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input  style="width:50px;" type="text" class="text_boxes_numeric" name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" placeholder="" onChange="calculate_amount('<?=$i; ?>');" value="<?=$row[csf("rate")]; ?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" placeholder="" value="<?=$row[csf("amount")]; ?>" /></td>
                    <td align="center">
                        <input style="width:100px;" type="text" class="text_boxes"  name="txtremark_<?=$i; ?>" id="txtremark_<?=$i; ?>" value="<?=$row[csf("remarks")]; ?>" onDblClick="remark_popup('<?=$i; ?>');" placeholder="" />
                        <input type="hidden" class="text_boxes" name="txtMemoryDataRf_<?=$i; ?>" id="txtMemoryDataRf_<?=$i; ?>" />
                    </td>
                    <td width="70">
                        <!--  <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<?=$i; ?>" id="txtRfFile_<?=$i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<?=$i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td> -->
                        <input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+"  disabled="" />
                        <input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-"  disabled="" />
                    </td>
                </tr>
				<?
				$i++;
			}
		}
	}
 	exit();
}

if($action=="load_php_dtls_form_apply")
{
	$data=explode("___", $data);
	$booking_no=$data[3];
	if($data[2]==1) //Last Apply ID=1
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");

		execute_query( "update wo_non_ord_samp_booking_mst set is_apply_last_update=1 where booking_no ='".$booking_no."' and status_active=1 and is_deleted=0",0);
		$rID= execute_query( "update wo_non_ord_samp_booking_dtls set status_active=3, is_deleted=2 where booking_no ='".$booking_no."' and status_active=1 and is_deleted=0",0);				
		$rID2= execute_query( "update sample_development_yarn_dtls set status_active=3, is_deleted=2 where booking_no ='".$booking_no."' and status_active=1 and is_deleted=0",0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
			}
			else{
				mysql_query("ROLLBACK");
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
			}
			else{
				oci_rollback($con);
			}
		}
	}
	if($data[1]=="1") 
	{
		$sql_fabric="Select b.id, b.sample_mst_id, b.sample_name, b.sample_mst_id, b.gmts_item_id, b.body_part_id, b.fabric_nature_id, b.fabric_description, b.determination_id, b.gsm, b.dia, b.sample_color, b.color_type_id, b.width_dia_id, b.uom_id, b.required_dzn, b.color_data, b.remarks_ra, c.color_id as gmt_color_id, c.fabric_color, c.qnty as required_qty, c.grey_fab_qnty, c.process_loss_percent from sample_development_fabric_acc b,sample_development_rf_color c where b.id=c.dtls_id and b.sample_mst_id='$data[0]' and c.grey_fab_qnty>0 and b.form_type=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id ASC";

		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				?>
                <tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
                    <td align="center" id="rfSampleId_<?=$i; ?>" style="color:#F00">
						<?
                        $sql="select id,sample_name from lib_sample where status_active=1 and is_deleted=0";
                        echo create_drop_down( "cboRfSampleName_$i", 95, $sql,"id,sample_name", '', "", $row[csf("sample_name")],"",'1');
                        ?>
                    </td>
                    <td align="center" id="rfItemId_<?=$i; ?>">
						<?
                        $sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$data'");
                        $gmtsf="";
                        foreach ($sql_f as $rowf)
                        {
                        $gmtsf.=$rowf[csf("gmts_item_id")].",";
                        }
                        echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","1",$gmtsf);
                        ?>
                    </td>
                    <td align="center" id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');",'1'); ?></td>
                    <td align="center" id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","1","2,3"); ?></td>
                    <td align="center" id="rf_fabric_description_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="" onDblClick="open_fabric_description_popup(<?=$i; ?>)" readonly value="<?=$row[csf("fabric_description")]; ?>" disabled="" />
                        <input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("determination_id")]; ?>">
                    </td>
                    <td align="center" id="rf_gsm_<?=$i; ?>">
                        <input style="width:40px;" type="text" class="text_boxes_numeric" name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>" disabled="" />
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>"  />
                        <input type="hidden" id="updateidbookdDtl_<?=$i; ?>" name="updateidbookdDtl_<?=$i; ?>" class="text_boxes" style="width:20px" />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                         <input type="hidden" id="txtyarnconsbreakdown_<?=$i; ?>" name="txtyarnconsbreakdown_<?=$i; ?>"  class="text_boxes" style="width:20px" value="" />
                    </td>
                    <td align="center" id="rf_dia_<?=$i; ?>"><input style="width:40px;" type="text" class="text_boxes" name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" disabled=""/></td>
                    
                    <td align="center" id="rf_color_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" readonly disabled=""  value="<?=$color_library[$row[csf("gmt_color_id")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfColorID_<?=$i; ?>" id="txtRfColorID_<?=$i; ?>" readonly disabled="" value="<?=$row[csf("gmt_color_id")];?>"/>
                        <input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$row[csf("color_data")]; ?>" class="text_boxes" />
                    </td>
                    <td align="center" id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "",'1'); ?></td>
                    <td align="center" id="rf_fab_color_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabColor_<?=$i; ?>" id="txtRfFabColor_<?=$i; ?>" readonly disabled=""  value="<?=$color_library[$row[csf("fabric_color")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfFabColorID_<?=$i; ?>" id="txtRfFabColorID_<?=$i; ?>" value="<?=$row[csf("fabric_color")];?>"/>
                    </td>
                    <td align="center" id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], "",'1'); ?></td>
                    <td align="center" id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",'1',"12,27,1,23" ); ?></td>
                    <td style="display: none;" align="center" id="rf_req_dzn_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="Write" value="<?=$row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i ;?>');" disabled="" /></td>
                    <td align="center" id="rf_req_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" value="<?=$row[csf('required_qty')]; ?>" onChange="calculate_requirement('<?=$i; ?>',1);" disabled /></td>
                    <td align="center" id="rf_reqs_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" onChange="calculate_requirement('<?=$i; ?>',1); fnc_process_loss_copy(<?=$i; ?>);" value="<?=$row[csf('process_loss_percent')]; ?>" disabled/></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" onChange="calculate_requirement('<?=$i; ?>',2);" value="<?=$row[csf('grey_fab_qnty')]; ?>" disabled/></td>
					<td align="center" id="td_additional_process_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes" name="txtAdditionalProcess_<?=$i; ?>" id="txtAdditionalProcess_<?=$i; ?>" placeholder="write" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" placeholder="" onChange="calculate_amount('<?=$i; ?>')" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" placeholder="" /></td>
                    <td align="center" id="">
                        <input style="width:50px;" type="text" class="text_boxes" name="txtremark_<?=$i; ?>" id="txtremark_<?=$i; ?>" value="<?=$row[csf("remarks_ra")]; ?>"  onDblClick="remark_popup('<?=$i;?>');" placeholder="" />
                        <input type="hidden" class="text_boxes" name="txtMemoryDataRf_<?=$i;?>" id="txtMemoryDataRf_<?=$i;?>" />
                    </td>
                    <td width="70">
                        <!--  <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<?=$i; ?>" id="txtRfFile_<?=$i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<?=$i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td> -->
                        <input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+"  disabled="" />
                        <input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-"  disabled="" />
                    </td>
                </tr>
				<?
				$i++;
			}
		}
	}
	else
	{
		$sql_fabric="SELECT id as booking_dtls_id, booking_no, style_id as sample_mst_id, sample_type as sample_name, gmts_item_id, body_part as body_part_id, item_category as fabric_nature_id, fabric_description, gsm_weight as gsm, dia, color_all_data as color_data, yarn_cons_breakdown,color_type_id, fabric_color, gmts_color, width_dia_type as width_dia_id, uom as uom_id, req_dzn as required_dzn, finish_fabric as required_qty, dtls_id as dtls_id, process_loss, grey_fabric, rate, amount, lib_yarn_count_deter_id, remarks,additional_process from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0 and style_id='$data[0]' and entry_form_id=140";
		$sql_resultf =sql_select($sql_fabric);  $i=1;
		if(count($sql_resultf)>0)
		{
			foreach($sql_resultf as $row)
			{
				?>
				<tr id="tr_<?=$i; ?>" style="height:10px;" class="general">
                    <td align="center" id="rfSampleId_<?=$i; ?>">
						<?
                        $sql="select id,sample_name from lib_sample where status_active=1 and is_deleted=0";
                        echo create_drop_down( "cboRfSampleName_$i", 95, $sql,"id,sample_name", '', "", $row[csf("sample_name")],"",'1');
                        ?>
                    </td>
                    <td align="center" id="rfItemId_<?=$i; ?>">
						<?
                        $sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=117 and sample_mst_id='$data'");
                        $gmtsf="";
                        foreach ($sql_f as $rowf)
                        {
                        	$gmtsf.=$rowf[csf("gmts_item_id")].",";
                        }
                        echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","1",$gmtsf);
                        ?>
                    </td>
                    <td align="center" id="rf_body_part_<?=$i; ?>"><?=create_drop_down( "cboRfBodyPart_$i", 95, $body_part,"", 1, "Select Body Part", $row[csf("body_part_id")], "load_data_to_rfcolor('$i');",'1'); ?></td>
                    <td align="center" id="rf_fabric_nature_<?=$i; ?>"><?=create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","1","2,3"); ?></td>
                    <td align="center" id="rf_fabric_description_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfFabricDescription_<?=$i; ?>" id="txtRfFabricDescription_<?=$i; ?>" placeholder="" onDblClick="open_fabric_description_popup(<?=$i; ?>);" readonly value="<?=$row[csf("fabric_description")]; ?>" disabled="" />
                        <input type="hidden" name="libyarncountdeterminationid_<?=$i; ?>" id="libyarncountdeterminationid_<?=$i; ?>" class="text_boxes" style="width:10px" value="<?=$row[csf("lib_yarn_count_deter_id")]; ?>">
                    </td>
                    <td align="center" id="rf_gsm_<?=$i; ?>">
                        <input style="width:40px;" type="text" class="text_boxes_numeric" name="txtRfGsm_<?=$i; ?>" id="txtRfGsm_<?=$i; ?>" value="<?=$row[csf("gsm")]; ?>" disabled="" />
                        <input type="hidden" id="updateidRequiredDtl_<?=$i; ?>" name="updateidRequiredDtl_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("dtls_id")]; ?>"  />
                        <input type="hidden" id="updateidbookdDtl_<?=$i; ?>" name="updateidbookdDtl_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("booking_dtls_id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                         <input type="hidden" id="txtyarnconsbreakdown_<?=$i; ?>" name="txtyarnconsbreakdown_<?=$i; ?>"  class="text_boxes" style="width:20px" value="<?=$row[csf("yarn_cons_breakdown")]; ?>" />
                    </td>
                    <td align="center" id="rf_dia_<?=$i; ?>"><input style="width:40px;" type="text" class="text_boxes" name="txtRfDia_<?=$i; ?>" id="txtRfDia_<?=$i; ?>" value="<?=$row[csf("dia")]; ?>" disabled=""/></td>
                    <td align="center" id="rf_color_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes" name="txtRfColor_<?=$i; ?>" id="txtRfColor_<?=$i; ?>" readonly disabled=""  value="<?=$color_library[$row[csf("gmts_color")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes" name="txtRfColorID_<?=$i; ?>" id="txtRfColorID_<?=$i; ?>" value="<?=$row[csf("gmts_color")]; ?>"/>
                        <input type="hidden" name="txtRfColorAllData_<?=$i; ?>" id="txtRfColorAllData_<?=$i; ?>" value="<?=$row[csf("color_data")]; ?>" class="text_boxes">
                    </td>
                    <td align="center" id="rf_color_type_<?=$i; ?>"><?=create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "",'1'); ?></td>
                    <td align="center" id="rf_fab_color_<?=$i; ?>">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabColor_<?=$i; ?>" id="txtRfFabColor_<?=$i; ?>" readonly disabled="" value="<?=$color_library[$row[csf("fabric_color")]];?>"/>
                        <input style="width:60px;" type="hidden" class="text_boxes"  name="txtRfFabColorID_<?=$i; ?>" id="txtRfFabColorID_<?=$i; ?>" readonly disabled=""  value="<?=$row[csf("fabric_color")];?>"/>
                    </td>
                    <td align="center" id="rf_width_dia_<?=$i; ?>"><?=create_drop_down( "cboRfWidthDia_$i", 80, $fabric_typee,"", 1, "Select Width/Dia", $row[csf("width_dia_id")], "",'1'); ?></td>
                    <td align="center" id="rf_uom_<?=$i; ?>"><?=create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",'1',"12,27,1,23" ); ?></td>
                    <td style="display: none;" align="center" id="rf_req_dzn_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRfReqDzn_<?=$i; ?>" id="txtRfReqDzn_<?=$i; ?>" placeholder="write" value="<?=$row[csf("required_dzn")]; ?>" onBlur="calculate_required_qty('1','<?=$i ;?>');" disabled="" /></td>
                    <td align="center" id="rf_req_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRfReqQty_<?=$i; ?>" id="txtRfReqQty_<?=$i; ?>" placeholder="" value="<?=$row[csf("required_qty")]; ?>" onChange="calculate_requirement('<?=$i;?>',1);" disabled/></td>
                    <td align="center" id="rf_reqs_qty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtProcessLoss_<?=$i; ?>" id="txtProcessLoss_<?=$i; ?>" placeholder="" disabled="" onChange="calculate_requirement('<?=$i;?>',1); fnc_process_loss_copy(<?=$i; ?>);" value="<?=$row[csf("process_loss")]; ?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input disabled="" style="width:50px;" type="text" class="text_boxes_numeric" name="txtGrayFabric_<?=$i; ?>" id="txtGrayFabric_<?=$i; ?>" placeholder="" value="<?=$row[csf("grey_fabric")]; ?>" onChange="calculate_requirement('<?=$i; ?>',2);"/></td>
					<td align="center" id="td_additional_process_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes" name="txtAdditionalProcess_<?=$i; ?>" id="txtAdditionalProcess_<?=$i; ?>" placeholder="write" value="<?=$row[csf("additional_process")]; ?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtRate_<?=$i; ?>" id="txtRate_<?=$i; ?>" placeholder="" onChange="calculate_amount('<?=$i; ?>')" value="<?=$row[csf("rate")]; ?>" /></td>
                    <td align="center" id="rf_req_qnty_<?=$i; ?>"><input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtAmount_<?=$i; ?>" id="txtAmount_<?=$i; ?>" placeholder="" value="<?=$row[csf("amount")]; ?>" /></td>
                    <td align="center" id="">
                        <input style="width:100px;" type="text" class="text_boxes" name="txtremark_<?=$i; ?>" id="txtremark_<?=$i; ?>" value="<?=$row[csf("remarks")]; ?>"  onDblClick="remark_popup('<?=$i;?>');" placeholder="" />
                        <input type="hidden" class="text_boxes"  name="txtMemoryDataRf_<?=$i; ?>" id="txtMemoryDataRf_<?=$i; ?>" />
                    </td>
                    <td width="70">
                        <!--  <td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<?=$i; ?>" id="txtRfFile_<?=$i; ?>" onClick="file_uploader ( '../../', document.getElementById('updateidRequiredDtl_<?=$i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td> -->
                        <input type="button" id="increaserf_<?=$i; ?>" name="increaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="+"  disabled="" />
                        <input type="button" id="decreaserf_<?=$i; ?>" name="decreaserf_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" value="-"  disabled="" />
                    </td>
				</tr>
				<?
				$i++;
			}
		}
	}
 	exit();
}

if($action=="check_is_booking_used")
{
	$data=explode("_",$data);
	$txt_booking_no="'".$data[0]."'";
	if($txt_booking_no!="")
	{
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_non_ord_samp_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		 foreach($sql as $row){
           // if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		    $is_approved=$row[csf('is_approved')];
        }
        if($is_approved==1) { echo "approved**".str_replace("'","",$txt_booking_no); die; }
		else if($is_approved==3) { echo "papproved**".str_replace("'","",$txt_booking_no); die; }

		$sql_knitting="select a.recv_number from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_no=".$txt_booking_no." and a.entry_form=2 and a.is_deleted=0 and a.status_active=1";
		$knitting_data_array=sql_select($sql_knitting,1);
		$recv_number=$knitting_data_array[0][csf('recv_number')];
		if(count($knitting_data_array)>0)
		{
			//echo "13**".'Knitting Prod Found';die;
			echo "Knitting**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}
	}
	exit();
}

if($action=="remark_popup")
{
	echo load_html_head_contents("Remark POPUP","../../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	//echo $txtremark;
	$permission=$_SESSION['page_permission'];
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function js_set_value2()
		{
			document.getElementById('txtcomments').value;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <fieldset>
            <table  width="400px" cellpadding="0" cellspacing="0" border="0"  align="center" >
            <?
				if($txtremark!="")
				{
					?>
					<tr>
                        <td width="50" height="50" align="center"><h4>Remark  Details.</h4></td>
					</tr>
					<tr>
                        <td colspan="2" align="center" ><textarea id="txtcomments" cols="7" name="txtcomments" class="text_area" style="width:420px; height:100px" placeholder="Remark Details"> <? echo $txtremark; ?></textarea></td>
					</tr>
					<tr>
                        <td width="100" height="50" align="center"><input type="button" name="search" id="search" value="Close" onClick="js_set_value2()" style="width:80px" class="formbutton" /></td>
					</tr>
					<?
				}
				else
				{
					?>
					<tr>
                        <td width="50" height="50" align="center"><h4>Remark  Details.</h4></td>
					</tr>
					<tr>
                        <td colspan="2" align="center" ><textarea id="txtcomments" cols="7" name="txtcomments" class="text_area" style="width:420px; height:100px" placeholder="Remark Details" ></textarea></td>
					</tr>
					<tr>
                        <td width="100" height="50" align="center"><input type="button" name="search" id="search" value="Close" onClick="js_set_value2()" style="width:80px" class="formbutton" /></td>
					</tr>
					<?
				}
            ?>
            </table>
        </fieldset>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete_required_fabric")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//%@!/<>?+[]{};: these are restricted, crm id - 57 (2024) 
	$str_rep=array("<?","?>","_", "&", "@","*", "(", ")", "=","'","\r", "\n",'"','#','%','!','/','<','>','?','+','[',']','{','}',';',':');
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			if(str_replace("'","",$booking_no)!='')
			{
				$sql=sql_select("select b.style_id,b.booking_no from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.booking_no=b.booking_no and   b.style_id=$requisition_id  and a.booking_type=4 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
				foreach($sql as $row){
		
					if($row[csf('style_id')]>0) $bookingNo=$row[csf('booking_no')];
					else  $bookingNo="";
					
				}
				if($bookingNo!=''){
					echo "11**Requisition. is Found by BookingNo=".$bookingNo;
					disconnect($con);die;
				}
			}
	

			$id_dtls=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
			$field_array= "id, booking_no, booking_mst_id, style_id, sample_type, gmts_item_id, body_part, item_category, fabric_description, gsm_weight, dia, color_all_data,yarn_cons_breakdown, color_type_id, gmts_color, fabric_color, width_dia_type, uom, req_dzn, finish_fabric, dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss, grey_fabric, rate, amount, remarks,additional_process, lib_yarn_count_deter_id,wo_qty";
			$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
			$yarn_field_array="id, mst_id,req_fab_dtls_id, booking_no,booking_mst_id,entry_form,gsm_weight,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
			
			$yarn_deter_id="";
			//echo "10**".$total_row.'d';die; wo_qty
			$m=0;$yarn_data_array_dtls="";
			/*$str_rep=array("<?","?>","_", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');*/
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboRfSampleName="cboRfSampleName_".$i;
				$cboRfGarmentItem="cboRfGarmentItem_".$i;
				$cboRfBodyPart="cboRfBodyPart_".$i;
				$cboRfFabricNature="cboRfFabricNature_".$i;
				$txtRfFabricDescription="txtRfFabricDescription_".$i;
				$txtyarnconsbreakdown="txtyarnconsbreakdown_".$i;
				$txtRfGsm="txtRfGsm_".$i; 
				$txtRfDia="txtRfDia_".$i;
				$txtRfColor="txtRfColor_".$i;
				$txtRfColorID="txtRfColorID_".$i;
				$txtRfFabColorID="txtRfFabColorID_".$i;
				$cboRfColorType="cboRfColorType_".$i;
				$cboRfWidthDia="cboRfWidthDia_".$i;
				$cboRfUom="cboRfUom_".$i;
				 
				$txtRfReqDzn="txtRfReqDzn_".$i;
				$txtRfReqQty="txtRfReqQty_".$i;
				$txtwoqty="txtwoqty_".$i;
				$txtRfColorAllData="txtRfColorAllData_".$i;
				$required_fab_id="updateidRequiredDtl_".$i;
				$updateidbookdDtl="updateidbookdDtl_".$i;
				$txtProcessLoss="txtProcessLoss_".$i;
				$txtGrayFabric="txtGrayFabric_".$i;
				$txtRate="txtRate_".$i;
				$txtAmount="txtAmount_".$i;
				$txtremark="txtremark_".$i;
				$txtAdditionalProcess="txtAdditionalProcess_".$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';
				
				$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
				
				$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
				$fab_greyQty_arr[$libDeterId]+=str_replace("'",'',$$txtGrayFabric);
				$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);
				
				$txtremark=str_replace("'",'',$$txtremark);
				 $txtdtlsremark='';
				 $txtdtlsremark=str_replace($str_rep,' ',$txtremark);
				
				// $txtAdditionalProcess=str_replace("'",'',$$txtAdditionalProcess);
				// $txtdtlsremark='';
				// $txtAdditionalProcess_str=str_replace($str_rep,' ',$txtAdditionalProcess);
				//$libyarn_id=str_replace("'","",$$libyarncountdeterminationid);
				
				/*$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
				$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
				$fab_greyQty_arr[$libDeterId]=str_replace("'",'',$$txtGrayFabric);
				$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);*/
				
				
			    $yarncons_breakdown=str_replace("'",'',$$txtyarnconsbreakdown);
				$ex_data=explode("##",$yarncons_breakdown);
				foreach($ex_data as $deter_data)
				{
					if ($m!=0) $yarn_data_array_dtls .=",";
					$ex_dtl_data=explode("**",$deter_data);
 					$deter_mst_id=$ex_dtl_data[0];
					$percent=$ex_dtl_data[1];
					$copmposition_id=$ex_dtl_data[2];
					$count_id=$ex_dtl_data[3];
					$type_id=$ex_dtl_data[4];
					$fab_nature=str_replace("'",'',$$cboRfFabricNature);
					$fab_greyQty=str_replace("'",'',$$txtGrayFabric);
					$fab_gsm=str_replace("'",'',$$txtRfGsm);
					
					if(str_replace("'",'',$fab_nature)==2)
					{
						$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
					}
					if(str_replace("'",'',$fab_nature)==3)
					{
						$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
					}
					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$requisition_id.",".$id_dtls.",'".$booking_no."','".$update_id."','140',".$fab_gsm.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				} //foreach end

				if ($i!=1) $data_array .=",";
 				$data_array .="(".$id_dtls.",'".$booking_no."','".$update_id."','".$requisition_id."',".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$txtyarnconsbreakdown.",".$$cboRfColorType.",".$$txtRfColorID.",".$$txtRfFabColorID.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','140',".$$txtProcessLoss.",".$$txtGrayFabric.",".$$txtRate.",".$$txtAmount.",'".$txtdtlsremark."',".$$txtAdditionalProcess.",".$$libyarncountdeterminationid.",".$$txtwoqty.")";

				$id_dtls=$id_dtls+1;

		    }
		/*	$yarn_deter_ids=rtrim($yarn_deter_id,',');//id_dtls
			$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0  and b.is_deleted=0 and a.id in($yarn_deter_ids)");
			$determin_arr="";
			foreach($select_deter as $row)
			{
				$determin_arr.=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
			}
			
			$determin_data=rtrim($determin_arr,'##');
			$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
			$yarn_field_array="id, mst_id, booking_no,booking_mst_id,entry_form,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
			$m=0;$yarn_data_array_dtls="";
			
				$ex_data=explode("##",$determin_data);
				foreach($ex_data as $deter_data)
				{
					if ($m!=0) $yarn_data_array_dtls .=",";
					$ex_dtl_data=explode("**",$deter_data);
 					$deter_mst_id=$ex_dtl_data[0];
					$percent=$ex_dtl_data[1];
					$copmposition_id=$ex_dtl_data[2];
					$count_id=$ex_dtl_data[3];
					$type_id=$ex_dtl_data[4];
					$fab_nature=$fab_nature_arr[$deter_mst_id];
					$fab_greyQty=$fab_greyQty_arr[$deter_mst_id];
					$fab_gsm=$fab_gsm_arr[$deter_mst_id];
					
					if(str_replace("'",'',$fab_nature)==2)
					{
						$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
					}
					if(str_replace("'",'',$fab_nature)==3)
					{
						$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
					}
					

					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$requisition_id.",'".$booking_no."','".$update_id."','140',".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				} //foreach end
			*/
			
			$flag=1;
			$rID_1=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
			//echo "10**insert into wo_non_ord_samp_booking_dtls (".$field_array.") Values ".$data_array;die;
			if($rID_1) $flag=1;else  $flag=0;
			if($yarn_data_array_dtls!="")
			 {
				//echo "10**insert into sample_development_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
				if($flag==1)
				{
				$rID_2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);
				 if($rID_2) $flag=1;else  $flag=0;
				}
				

			 }
			 if($flag==1){
			 $rID= execute_query("update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no ='".$booking_no."' and status_active=1 and is_deleted=0",0);
			 }
		//echo "10**".$rID_1."=".$rID_2."=".$flag;die;
  			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$requisition_id)."**2".$booking_no;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$requisition_id)."**2".$booking_no;

				}
			else{
					oci_rollback($con);
					echo "10**";
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}


			$sql_knitting="select a.booking_no from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_no='".$booking_no."' and a.entry_form=2 and a.is_deleted=0 and a.status_active=1";
			$knitting_data_array=sql_select($sql_knitting,1);
			if(count($knitting_data_array)>0)
			{
				echo "13**".'Knitting Prod Found';disconnect($con);die;
			}

 			$id_dtls=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
			$field_array= "id, booking_no, booking_mst_id, style_id, sample_type, gmts_item_id, body_part, item_category, fabric_description, gsm_weight, dia, color_all_data,yarn_cons_breakdown, color_type_id, gmts_color, fabric_color, width_dia_type, uom,req_dzn, finish_fabric, dtls_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id, process_loss, grey_fabric, rate, amount, remarks,additional_process, lib_yarn_count_deter_id,wo_qty";
 			$field_array_up="gmts_color*fabric_color*dia*width_dia_type*finish_fabric*process_loss*grey_fabric*rate*amount*remarks*additional_process*updated_by*update_date*wo_qty";
			$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
		
			$yarn_field_array="id, mst_id,req_fab_dtls_id, booking_no,booking_mst_id,entry_form,gsm_weight,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";
			 $m=0;$yarn_data_array_dtls="";
			$yarn_deter_id="";
			for ($i=1;$i<=$total_row;$i++)
		    {
				$cboRfSampleName="cboRfSampleName_".$i;
				$cboRfGarmentItem="cboRfGarmentItem_".$i;
				$cboRfBodyPart="cboRfBodyPart_".$i;
				$cboRfFabricNature="cboRfFabricNature_".$i;
				$txtRfFabricDescription="txtRfFabricDescription_".$i;
				$txtyarnconsbreakdown="txtyarnconsbreakdown_".$i;
				$txtRfGsm="txtRfGsm_".$i;
				$txtRfDia="txtRfDia_".$i;
				$txtRfColor="txtRfColor_".$i;
				$txtRfColorID="txtRfColorID_".$i;
				$txtRfFabColorID="txtRfFabColorID_".$i;
				$cboRfColorType="cboRfColorType_".$i;
				$cboRfWidthDia="cboRfWidthDia_".$i;
				$cboRfUom="cboRfUom_".$i;
				$txthiddenwoqty="txthiddenwoqty_".$i;
				
				$txtRfReqDzn="txtRfReqDzn_".$i;
				$txtRfReqQty="txtRfReqQty_".$i;
				$txtwoqty="txtwoqty_".$i;
				$txtRfColorAllData="txtRfColorAllData_".$i;
				$required_fab_id="updateidRequiredDtl_".$i;
				$updateidbookdDtl="updateidbookdDtl_".$i;
				$txtProcessLoss="txtProcessLoss_".$i;
				$txtGrayFabric="txtGrayFabric_".$i;
				$txtRate="txtRate_".$i;
				$txtAmount="txtAmount_".$i;
				$txtremark="txtremark_".$i;
				$txtAdditionalProcess="txtAdditionalProcess_".$i;
				$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
				$yarn_deter_id.=str_replace("'","",$$libyarncountdeterminationid).',';
				
				$txtremark=str_replace("'",'',$$txtremark);
				 $txtdtlsremark='';
				 $txtdtlsremark=str_replace($str_rep,' ',$txtremark);
				
				// $txtAdditionalProcess=str_replace("'",'',$$txtAdditionalProcess);
				// $txtdtlsremark='';
				// $txtAdditionalProcess_str=str_replace($str_rep,' ',$txtAdditionalProcess);
				//$yarn_consbreakdown=str_replace("'",'',$$txtyarnconsbreakdown);
				
				$libDeterId=str_replace("'",'',$$libyarncountdeterminationid);
				$hiddenwoqty=str_replace("'",'',$$txthiddenwoqty);
				$woqty=str_replace("'",'',$$txtwoqty);
				$dtl_chk_update=0;
				if($woqty==$hiddenwoqty)
				{
					$dtl_chk_update=2;
				}
				//$fab_nature_arr[$libDeterId]=str_replace("'",'',$$cboRfFabricNature);
				//$fab_greyQty_arr[$libDeterId]+=str_replace("'",'',$$txtGrayFabric);
				//$fab_gsm_arr[$libDeterId]=str_replace("'",'',$$txtRfGsm);

				
				$yarncons_breakdown=str_replace("'",'',$$txtyarnconsbreakdown);
				$ex_data=explode("##",$yarncons_breakdown);
				foreach($ex_data as $deter_data)
				{
					if ($m!=0) $yarn_data_array_dtls .=",";
					$ex_dtl_data=explode("**",$deter_data);
 					$deter_mst_id=$ex_dtl_data[0];
					$percent=$ex_dtl_data[1];
					$copmposition_id=$ex_dtl_data[2];
					$count_id=$ex_dtl_data[3];
					$type_id=$ex_dtl_data[4];
					$fab_nature=str_replace("'",'',$$cboRfFabricNature);
					$fab_greyQty=str_replace("'",'',$$txtGrayFabric);
					$fab_gsm=str_replace("'",'',$$txtRfGsm);
					
					if(str_replace("'",'',$fab_nature)==2)
					{
						$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
					}
					if(str_replace("'",'',$fab_nature)==3)
					{
						$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
					}
					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$requisition_id.",".$$updateidbookdDtl.",'".$booking_no."','".$update_id."','140',".$fab_gsm.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				} //foreach end
				
   				if (str_replace("'",'',$$updateidbookdDtl)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateidbookdDtl);

					$data_array_up[str_replace("'",'',$$updateidbookdDtl)] =explode("*",("".$$txtRfColorID."*".$$txtRfFabColorID."*".$$txtRfDia."*".$$cboRfWidthDia."*".$$txtRfReqQty."*".$$txtProcessLoss."*".$$txtGrayFabric."*".$$txtRate."*".$$txtAmount."*'".$txtdtlsremark."'*".$$txtAdditionalProcess."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$txtwoqty.""));
				}
				else
				{
					if ($i!=1) $data_array .=",";
					$data_array .="(".$id_dtls.",'".$booking_no."','".$update_id."','".$requisition_id."',".$$cboRfSampleName.",".$$cboRfGarmentItem.",".$$cboRfBodyPart.",".$$cboRfFabricNature.",".$$txtRfFabricDescription.",".$$txtRfGsm.",".$$txtRfDia.",".$$txtRfColorAllData.",".$$txtyarnconsbreakdown.",".$$cboRfColorType.",".$$txtRfColorID.",".$$txtRfFabColorID.",".$$cboRfWidthDia.",".$$cboRfUom.",".$$txtRfReqDzn.",".$$txtRfReqQty.",".$$required_fab_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','140',".$$txtProcessLoss.",".$$txtGrayFabric.",".$$txtRate.",".$$txtAmount.",'".$txtdtlsremark."',".$$txtAdditionalProcess.",".$$libyarncountdeterminationid.",".$$txtwoqty.")";
	
					$id_dtls=$id_dtls+1;	
				}
		    }
			
			/*$yarn_deter_ids=rtrim($yarn_deter_id,',');//id_dtls
			$select_deter=sql_select("SELECT a.id,b.id as dtls_id, b.copmposition_id,b.percent,b.count_id,b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($yarn_deter_ids)");//and  a.status_active=1 and a.is_deleted=0
			
			$determin_arr="";
			foreach($select_deter as $row)
			{
				$determin_arr.=$row[csf('id')].'**'.$row[csf('percent')].'**'.$row[csf('copmposition_id')].'**'.$row[csf('count_id')].'**'.$row[csf('type_id')].'##';
			}
			$determin_data=rtrim($determin_arr,'##');
			$yarn_id_dtls=return_next_id( "id", "sample_development_yarn_dtls", 1 ) ;
		
			$yarn_field_array="id, mst_id, booking_no,booking_mst_id,entry_form,determin_id,count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty, inserted_by, insert_date";*/
			//is_updated //$hiddenwoqty
			$is_updated_yarn=0;
			if($booking_no!="")
			{
			$is_updated_yarn=return_field_value( "is_updated", "sample_development_yarn_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0 and entry_form=140 and is_updated>0");
			}
			$requisition_id=str_replace("'","",$requisition_id);
			if($requisition_id=='')
			{
			$requisition_id=return_field_value( "style_id", "wo_non_ord_samp_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0 and entry_form_id=140");
			}
			/*$m=0;$yarn_data_array_dtls="";
			$ex_data=explode("##",$determin_data);
				foreach($ex_data as $deter_data)
				{
					if ($m!=0) $yarn_data_array_dtls .=",";
					$ex_dtl_data=explode("**",$deter_data);
 					$deter_mst_id=$ex_dtl_data[0];
					$percent=$ex_dtl_data[1];
					$copmposition_id=$ex_dtl_data[2];
					$count_id=$ex_dtl_data[3];
					$type_id=$ex_dtl_data[4];
					$deter_mst_id=str_replace("'",'',$deter_mst_id);
					$fab_nature=$fab_nature_arr[$deter_mst_id];
					$fab_greyQty=$fab_greyQty_arr[$deter_mst_id];
					$fab_gsm=$fab_gsm_arr[$deter_mst_id];
					
					if(str_replace("'",'',$fab_nature)==2)
					{
						$yanr_cons=(str_replace("'",'',$fab_greyQty)*$percent)/100;
					}
					if(str_replace("'",'',$fab_nature)==3)
					{
						$yanr_cons=(str_replace("'",'',$fab_gsm)*$percent)/100;
					}
					

					$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$requisition_id.",'".$booking_no."','".$update_id."','140',".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				}*/ //foreach end
				
			//$flag=1;
			//$rID_1=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
			//if($rID_1) $flag=1;else  $flag=0;
			
			//echo "10**".$is_updated_yarn.'='.$dtl_chk_update;die;
			$flag=1;
			if($data_array_up!="")
			{
 				$rID=execute_query(bulk_update_sql_statement("wo_non_ord_samp_booking_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID) $flag=1; else $flag=0;
 			}
			if($data_array!="")
			{
			 	$rID_3=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
				//echo "10**insert into wo_non_ord_samp_booking_dtls (".$field_array.") Values ".$data_array;die;
				if($rID_3) $flag=1; else $flag=0;
			}
			
			if($is_updated_yarn!=2) // Delete will be restricted ## issue Id-10684 Knit Asia
			{
				if($flag==1)
				{
					$yarn_delete=execute_query( "delete from sample_development_yarn_dtls where booking_no  in('$booking_no')",0);
					if($yarn_delete) $flag=1; else $flag=0;
				}
			 
				if($yarn_data_array_dtls!="")
				 {
					//echo "10**insert into sample_development_yarn_dtls (".$yarn_field_array.") Values ".$yarn_data_array_dtls;die;
					if($flag==1)
					{
					$rID_2=sql_insert("sample_development_yarn_dtls",$yarn_field_array,$yarn_data_array_dtls,0);
					 if($rID_2) $flag=1;else  $flag=0;
					}
				 }
			}
			if($flag==1){
				$rID= execute_query("update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no ='".$booking_no."' and status_active=1 and is_deleted=0",0);
				}
			//echo "10**".$rID.'='.$rID_2.'='.$rID_3;die;
			 
			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$requisition_id)."**2";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$requisition_id)."**2";

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
	else if ($operation==2) //Deletation
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sql_knitting="select a.booking_no from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_no='".$booking_no."' and a.entry_form=2 and a.is_deleted=0 and a.status_active=1";
		$knitting_data_array=sql_select($sql_knitting,1);
		if(count($knitting_data_array)>0)
		{
			echo "13**".'Knitting Prod Found';disconnect($con);die;
		}

		$rID_dtls=execute_query( "update wo_non_ord_samp_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no='$booking_no' ",0);
		$rID_dtls=execute_query( "update sample_development_yarn_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no='$booking_no' ",0);

		//echo "10**".$rID_dtls;die;
		if($db_type==0)
		{
			if($rID_dtls){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_dtls){
				 oci_commit($con);
				echo "2**".str_replace("'","",$booking_no);
			}
			else{
				 oci_rollback($con);
				echo "10**".str_replace("'","",$booking_no);
			}
		}
		disconnect($con);
		die;
	}
}
//Yarn Dtls

if($action == "yarn_dtls_popup")
{
	//print_r($_REQUEST);
	extract($_REQUEST);
	echo load_html_head_contents("Sample Req yarn dtls info","../../../", 1, 1, $unicode);
	//print_r($composition);
	$sql_non=sql_select("select lib_yarn_count_deter_id as deter_id,gsm_weight from wo_non_ord_samp_booking_dtls where booking_no ='".$booking_no."' and status_active=1");
	//echo "select lib_yarn_count_deter_id,gsm_weight from wo_non_ord_samp_booking_dtls where booking_no ='".$booking_no."' and status_active=1";
	foreach($sql_non as $row)
	{
		if($row[csf('gsm_weight')]!="")
		{
		$fab_gsm_arr[$row[csf('deter_id')]]=$row[csf('gsm_weight')];
		}
	}
	if($booking_no != '')
	{
		$yarn_dtls = sql_select("SELECT id,samp_fab_dtls_id,determin_id, mst_id,gsm_weight, count_id,copm_one_id, cons_ratio, type_id, cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and booking_no ='".$booking_no."'");
	}
	// echo "SELECT id,samp_fab_dtls_id,determin_id, mst_id, count_id,copm_one_id, cons_ratio, type_id, cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and booking_no ='".$booking_no."'";


	?>
    <script>
				 var permission='<? echo $permission;?>';
				function fnc_yarn_dtls( operation )
				{

					//alert(operation);
					var delete_cause="";
					if(operation==2){
						//release_freezing();
						alert('Not allowed');
							return;
					}

					var row_num=$('#tbl_yarn_cost tr').length;
					//release_freezing();

					var data_all="";
					for (var i=1; i<=row_num; i++){  
					
					/*if (form_validation('cbocount_'+i,'Count')==false)
					{
						return;
					}*/
				
						data_all=data_all+get_submitted_data_string('hiddenreqid*cbocount_'+i+'*yarndtlsid_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'+i+'*determinid_'+i+'*sampfabdtldid_'+i+'*cbohiddencount_'+i,"../../../",i);
					}
					var data="action=save_update_delete_yarn_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
			       //alert(data);
					//return;
					http.open("POST","sample_requisition_booking_non_order_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_yarn_dtls_reponse;
				}

				function fnc_yarn_dtls_reponse()
				{
					if(http.readyState == 4){
						 var reponse=trim(http.responseText).split('**');
						 if(parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
							alert("Update is successfully");
							//reset_form('','form_data_con','','');
							//release_freezing();
							parent.emailwindow.hide();
							//show_msg(trim(reponse[0]));
						 }


						// release_freezing();
					}
				}

				function fnc_close()
				{
					parent.emailwindow.hide();
				}
	</script>
	 <body>
	<div align="center" style="width:100%;" >
 	<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:780px;" id="yarn_dtls_1">
    <label><b>Yarn Details</b> </label>
     <input style="width:125px;" type="hidden" class="text_boxes"  name="hiddenreqid" id="hiddenreqid" value="<? echo trim($booking_no);  ?>" />
		<table width="780" cellspacing="0" class="rpt_table" border="0" rules="all">
			<thead>
		    	<tr>
		        	<th width="50">GSM</th>
                    <th width="60">Count</th>
		            <th width="180" class="must_entry_caption">Comp.</th>
		            <th width="50" class="must_entry_caption">%</th>
		            <th width="110">Type</th>
		            <th width="75" class="must_entry_caption">Cons Qnty</th>
		            </th>
		        </tr>
		    </thead>
		    <tbody id="tbl_yarn_cost" >
				<?
				$i=1;

				foreach ($yarn_dtls as $yarnData) {
					$fab_gsm=rtrim($fab_gsm_arr[$yarnData[csf('determin_id')]],',');
					$fab_gsms=$yarnData[csf('gsm_weight')];//implode(",",array_unique(explode(",",$fab_gsm)));
				?>
				<tr id="yarncost_<? echo $i; ?>" align="center">
                		 <td title="<? echo $yarnData[csf('determin_id')];?>">
		                    <input type="text" id="txtgsm_<? echo $i; ?>" name="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $fab_gsms; ?>"   readonly />
		                </td>
		                <td>
		               <? echo create_drop_down( "cbocount_".$i, 100, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $yarnData[csf('count_id')],""); ?>
		                <input type="hidden" name="yarndtlsid_<? echo $i?>" id="yarndtlsid_<? echo $i?>" value="<? echo $yarnData[csf('id')]; ?>">
                        <input type="hidden" name="cbohiddencount_<? echo $i?>" id="cbohiddencount_<? echo $i?>" value="<? echo $yarnData[csf('count_id')]; ?>">
		                 <input type="hidden" name="sampfabdtldid_<? echo $i?>" id="sampfabdtldid_<? echo $i?>" value="<? echo $yarnData[csf('samp_fab_dtls_id')]; ?>">
		                 <input type="hidden" name="consratio_<? echo $i?>" id="consratio_<? echo $i?>" value="<? echo $yarnData[csf('cons_ratio')]; ?>">
		                  <input type="hidden" name="determinid_<? echo $i?>" id="determinid_<? echo $i?>" value="<? echo $yarnData[csf('determin_id')]; ?>">
		                </td>
		                <td title="<?=$composition[$yarnData[csf('copm_one_id')]]?>"><? echo create_drop_down( "cbocompone_".$i, 180, $composition,"", 1, "-- Select --", $yarnData[csf('copm_one_id')], "",1,"" ); ?></td>
		               <td><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $yarnData[csf('cons_ratio')];  ?>" readonly/>
		                </td>

		                <td><? echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $yarnData[csf('type_id')], "",$disabled,"" ); ?></td>
		                <td>
		                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $yarnData[csf('cons_qnty')] ?>"   />
		                </td>
		            </tr>
				<?
				$i++;
				}

				?>
			</tbody>
             <tr>
              	<td align="center" colspan="6">&nbsp;</td>
            </tr>
            <tr>
              	<td align="center" colspan="6">
					<?
                    echo load_submit_buttons( $permission, "fnc_yarn_dtls",1,0,"reset_form('yarn_dtls_1','','')",1);
                    ?>
            	</td>
            </tr>
            <tr>
              	<td align="center" colspan="5">
					 <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            	</td>
            </tr>
	</table>
	</fieldset>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
    </body>

	<?
	exit();
}
if ($action=="save_update_delete_yarn_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1)  // Update Here
	{
			$con = connect();
 			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//hidden_req_id*cbocount_'+i+'*yarn_dtls_id_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*consqnty_'


	$field_yarn_dtls_up="samp_fab_dtls_id*determin_id*count_id*copm_one_id*percent_one*type_id*cons_qnty*is_updated*updated_by*update_date";

			$m=0;$yarn_data_array_dtls="";
			for ($i=1;$i<=$total_row;$i++) //Yarn Start here
		    {
				$hidden_req_id="hiddenreqid";
				$samp_fab_dtls_id="sampfabdtldid_".$i;
				$determin_id="determinid_".$i;
				$yarn_dtls_id="yarndtlsid_".$i;
				$percent_one="percentone_".$i;
				$consqnty="consqnty_".$i;
				$count_id="cbocount_".$i;
				$cbohiddencount="cbohiddencount_".$i;
				$copm_one_id="cbocompone_".$i;
				$determinid="determinid_".$i;
				$cbotype="cbotype_".$i;
				
				$countid=str_replace("'",'',$$count_id);
				$hiddencount=str_replace("'",'',$$cbohiddencount);
				$is_count_update=0;
				if($countid!=$hiddencount)
				{
					$is_count_update=2;
				}
				
				//if ($i!=1) $libyarncountdeterminationid .=",";
					//if ($m!=0) $yarn_data_array_dtls .=",";

				if (str_replace("'",'',$$yarn_dtls_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$yarn_dtls_id);

					$yarn_data_dtls_up[str_replace("'",'',$$yarn_dtls_id)] =explode("*",("".$$samp_fab_dtls_id."*".$$determinid."*".$$count_id."*".$$copm_one_id."*".$$percent_one."*".$$cbotype."*".$$consqnty."*".$is_count_update."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

					//$yarn_data_array_dtls .="(".$yarn_id_dtls.",".$hidden_req_id.",".$id_dtls.",".$deter_mst_id.",".$count_id.",'".$copmposition_id."','".$percent."','".$type_id."','".$percent."','".$yanr_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//$yarn_id_dtls=$yarn_id_dtls+1;
					$m++;
				}
				 //foreach end
			}//For End
			//print_r($yarn_data_dtls_up);
		//echo "10**";die;



 			$flag=1;
 			if(count($yarn_data_dtls_up))
			{
				$rIDup=execute_query(bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr ));
				//echo "10**".bulk_update_sql_statement("sample_development_yarn_dtls", "id",$field_yarn_dtls_up,$yarn_data_dtls_up,$id_arr );die;
				if($rIDup) $flag=1; else $flag=0;
			}

			//echo "10**".$rIDs.'='.$rID1.'='.$rID_size_dlt.'='.$flag;die;



			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$hidden_req_id)."**2";

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

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**";die;
		if($db_type==0)
		{
			if($rID  && $update_dtls )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  && $update_dtls )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}

}

if($action=="supplier_popup")
{
  	echo load_html_head_contents("Supplier Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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
			var old=document.getElementById('txt_buyer_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
		}
    </script>

</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Supplier Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
					if($pay_mode==3  || $pay_mode==5){
						$sql_supplier=sql_select("select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name");
						foreach($sql_supplier as $row)
						{
							$supplier_arr[$row[csf('id')]]=$row[csf('company_name')];
						}
					}else{
						$sql_supplier=sql_select("select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name");
						foreach($sql_supplier as $row)
						{
							$supplier_arr[$row[csf('id')]]=$row[csf('supplier_name')];
						}
					}


                    $i=1; $supplier_row_id="";
					$hidden_supplier_id=explode(",",$supplier_id);
                    foreach($supplier_arr as $id=>$name)
                    {
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if(in_array($id,$hidden_supplier_id))
							{
								if($supplier_row_id=="") $supplier_row_id=$i; else $supplier_row_id.=",".$i;
							}

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
                                    <input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
								</td>
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
                    }
                ?>
                    <input type="hidden" name="txt_buyer_row_id" id="txt_buyer_row_id" value="<?php echo $buyer_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}


if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
}

if($action=="show_fabric_booking_report_micro") // rehan for barnali old 
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name='$cbo_company_name'  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");


	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	if ($db_type == 0) $select_group_row = " order by master_tble_id desc limit 0,3";
	else if ($db_type == 2) $select_group_row = " and  rownum<=4 order by id desc";
	$imge_arr_for_book=sql_select( "select master_tble_id,image_location,real_file_name from   common_photo_library where  master_tble_id=$txt_booking_no and file_type=1  $select_group_row ");

	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");

	$name_iso_Array=sql_select( "select iso_no from lib_iso where company_id=$cbo_company_name and status_active=1 and module_id=2 and menu_id=904");

	// echo $name_iso_Array[0]["ISO_NO"];

	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");

	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="900">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php
								echo $company_library[$cbo_company_name];
								?>
                              </strong>

						   </td>

                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
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
								<? echo $result[csf('plot_no')].'&nbsp;'; ?>
								<? echo $result[csf('level_no')].'&nbsp;' ?>
								<? echo $result[csf('road_no')].'&nbsp;'; ?>
								<? echo $result[csf('block_no')].'&nbsp;';?>
								<? echo $result[csf('city')].'&nbsp;';?>
								<? echo $result[csf('zip_code')].'&nbsp;'; ?>
								<? echo $result[csf('province')] . '&nbsp;'; ?>
								<? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br>
								<? echo $result[csf('email')];?>
								<? echo $result[csf('website')];
                            }
                                ?>

                               </td>
                        </tr>
                            <tr>

                            <td align="center" style="font-size:20px;"> <strong style="margin-left:77px;"><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             <div style="margin-left:854px; margin-top:-75px; position: absolute; float:right;  ">
	                            <?
								foreach($imge_arr_for_book as $row)
								{
									?>
	                                <img  src='../../<? echo $row[csf('image_location')]; ?>' height='80' width='80' />
									<?
								}

								?>
                            </div>
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>

                             </td>

                            </tr>
                      </table>
                </td>
				<td width="200"><b><?="ISO Number :".$name_iso_Array[0]["ISO_NO"]?></b> </td>
                <td width="250" id="barcode_img_id">
                </td>
            </tr>
       </table>

        <?
		$season="";
		$req_no="";
		$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";$remarks="";

		$nameseason=sql_select( "SELECT a.season as season_buyer_wise, b.buyer_req_no,a.buyer_ref,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept,a.requisition_number_prefix_num,a.remarks  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and b.status_active=1 and   b.is_deleted=0 and c.status_active=1 and   c.is_deleted=0 ");
		foreach ($nameseason as $season_row)
		{
			$season=$season_row[csf('season_buyer_wise')];
			$buyer_req_no=$season_row[csf('buyer_ref')];
			$bh_merchant=$season_row[csf('bh_merchant')];
			$style_ref_no=$season_row[csf('style_ref_no')];
			$product_code=$season_row[csf('product_code')];
			$product_department=$product_dept[$season_row[csf('product_dept')]];
			$req_no=$season_row[csf('requisition_number_prefix_num')];
			$remarks=$season_row[csf('remarks')];

		}

		$fabric_source='';
		$season_library=return_library_array( "SELECT id,season_name from lib_buyer_season", "id", "season_name");
        $nameArray=sql_select( "SELECT buyer_id,fabric_source,booking_no,pay_mode,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant,grouping,remarks from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
		foreach ($nameArray as $result)
		{
			$fabric_source_id=$result[csf('fabric_source')];
			$varcode_booking_no=$result[csf('booking_no')];
			//$remarks=$result[csf('remarks')];
		?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:18px"><b>Booking No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

                <td width="100"><span style="font-size:12px">&nbsp;</span></td>
                <td width="110">&nbsp;</td>


            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:12px">:&nbsp; <b><?
					if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5 ){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?></b></td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;

				<?
				if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5 ){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';

					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>

            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>


            </tr>
             <tr>
             	<td width="100" style="font-size:12px"><b>Fabric Source</b></td>
             	<td width="110">:&nbsp;<? echo $fabric_source_new[$fabric_source_id]; ?></td>
             	<td  width="100" style="font-size:12px"><b>Req. No</b></td>
             	<td  width="110" >:&nbsp;<? echo $req_no; ?></td>
             	<td  width="100" style="font-size:12px"><b>Style Ref.</b></td>
             	<td  width="110" >:&nbsp;<? echo $style_ref_no ; ?></td>
             	<td  width="100" style="font-size:12px"><b>Internal Ref.</b></td>
             	<td  width="110" >:&nbsp;<strong><? echo $result[csf('grouping')] ; ?></strong></td>

              </tr>
			  <tr>
             	<td width="100" style="font-size:12px"><b>Remarks</b></td>
             	<td width="110">:&nbsp;<? echo $remarks ?></td>
             	<td  width="100" style="font-size:12px">&nbsp;</td>
             	<td  width="110" >&nbsp;</td>
             	<td  width="100" style="font-size:12px">&nbsp;</td>
             	<td  width="110" >&nbsp;</td>
             	<td  width="100" style="font-size:12px">&nbsp;</td>
             	<td  width="110" >&nbsp;</td>

              </tr>
        </table>
        <?
		}
		?>
      <br/>

      <?


    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$size_lib=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );

	 $sql_dtls= "SELECT b.color_id, b.contrast, b.qnty as finish_fabric, b.process_loss_percent as process_loss, a.grey_fabric as grey_fabric, b.fabric_color, a.lib_yarn_count_deter_id,a.gsm_weight,a.style_id,a.style_des,a.sample_type,a.body_part,a.color_type_id,a.construction,a.yarn_details,a.composition,a.gsm_weight,a.gmts_color , a.gmts_size,a.item_size,a.dia_width, a.width_dia_type, a.rate,a.amount,a.id,a.lib_yarn_count_deter_id as lib_yarn_count_deter_id,a.yarn_details,a.remarks,a.fabric_source,a.delivery_date,a.dtls_id,a.uom ,a.fabric_description,a.gmts_item_id,a.color_all_data,a.dia,a.req_dzn,a.remarks , a.wo_qty
	FROM wo_non_ord_samp_booking_dtls a,sample_development_rf_color b 
	WHERE a.style_id=b.mst_id and a.dtls_id=b.dtls_id and a.fabric_color=b.fabric_color and b.qnty>0 and a.booking_no =$txt_booking_no and a.entry_form_id=140  and a.status_active=1 and	a.is_deleted=0  and b.status_active=1 and	b.is_deleted=0 ";
	//echo $sql_dtls;die;
	$sample_result=sql_select($sql_dtls);
	$style_id='';
	foreach($sample_result as $key=>$row)
	{
		$sample_type=$row[csf("sample_type")];
		$gmts_item_id=$row[csf("gmts_item_id")];
		$color_id=$row[csf("color_id")];
		$body_part_id=$row[csf("body_part")];
		$dtm_id=$row[csf("lib_yarn_count_deter_id")];
		$gsm_id=$row[csf("gsm_weight")];
		$fabric_color=$row[csf("fabric_color")];


		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["uom"]=$row[csf("uom")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["remarks"]=$row[csf("remarks")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["rate"]=$row[csf("rate")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["amount"]=$row[csf("amount")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["fabric_description"]=$row[csf("fabric_description")];

		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["req_dzn"]+=$row[csf("req_dzn")];

		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["gsm_weight"]=$row[csf("gsm_weight")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["color_type_id"]=$row[csf("color_type_id")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["dia_width"]=$row[csf("width_dia_type")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["dia"]=$row[csf("dia")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["grey_fabric"]=$row[csf("grey_fabric")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["process_loss"]=$row[csf("process_loss")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["finish_fabric"]=$row[csf("finish_fabric")];
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["fabric_color"]=$row[csf("contrast")];

		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["gmts_color"]=$color_id;
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["gmts_color_id"]=$color_id;
		$data_array_color_wise[$sample_type][$gmts_item_id][$color_id][$body_part_id][$dtm_id][$gsm_id][$fabric_color][$row[csf("color_type_id")]][$row[csf("dia")]][$row[csf("dia_width")]]["wo_qty"]=$row[csf("wo_qty")];
	}

	//echo "<pre>";print_r($data_array_color_wise);die;

	foreach($data_array_color_wise as $sample_type=>$gmts_data)
	{
		foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
		{
			foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
			{
				$sample_span=0;
				foreach($body_part_data as $body_part_id=>$dtm_data)
				{
					foreach($dtm_data as $dtm_id=>$gsm_data)
					{
						foreach($gsm_data as $gsm_id=>$fabric_color_data)
						{
							foreach($fabric_color_data as $color_id=>$color_type_data)
							{
								foreach($color_type_data as $color_type_id=>$dia_data)
								{
									foreach($dia_data as $dia=>$dia_width_data)
									{
										foreach($dia_width_data as $dia_width=>$row)
										{
											$sample_span++;
										}
									}
								}
							}
						}
					}
					$sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id]=$sample_span;
				}
			}

		}
	}
	/*echo "<pre>";
	print_r($sample_item_wise_span);die;*/

	$sample_mst_id=$sample_result[0][csf("style_id")];

	$sql_sample_dtls= "SELECT a.sample_name,a.article_no,b.id as color_name  from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=117  and sample_mst_id=$sample_mst_id and b.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,b.id";

	foreach(sql_select($sql_sample_dtls) as $key=>$value)
	{
		if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=="")
		{
			$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=$value[csf("article_no")];
		}
		else
		{
			if(!in_array($value[csf("article_no")], $sample_wise_article_no))
			{
				$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]].= ','.$value[csf("article_no")];
			}

		}
	}

	$sample_dtls_sql="SELECT sample_name,gmts_item_id,sample_color,sample_prod_qty from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$sample_mst_id and entry_form_id=117";
	 foreach(sql_select($sample_dtls_sql) as $vals)
	 {
	 	$sample_dtls_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("sample_color")]]+=$vals[csf("sample_prod_qty")];

	 }
	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr>
				<th width="30">Sl</th>
				<th width="90">Article No</th>
				<th width="110">Sample</th>
				<th width="80"> Gmts Color</th>
				<th width="120">Body Part</th>
				<th width="200">Fabric Details and Composition</th>
				<th width="80">Color Type</th>
				<th width="80">Fab.Color</th>

				<th width="40">GSM</th>
				<th width="55">Dia</th>
				<th width="80">Dia Type</th>
				<th width="80">Fin Fab Qnty</th>
				<th width="80">WO Qnty</th>
				<th width="40">P. Loss</th>
				<th width="60">Grey Qnty</th>
				<?php
					if($show_comment==1){?>
				<th width="60">Rate</th>
				<th width="60">Amount</th>
				<?}?>

				<th width="40">UOM</th>
				<th>Remarks</th>
            </tr>
        </thead>
        <tbody>
	        <?
	        $p=1;
	        $total_finish=0;
	        $total_grey=0;
	        $total_process=0;

	        foreach($data_array_color_wise as $sample_type=>$gmts_data)
	        {
	        	foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
	        	{
	        		foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
	        		{
	        			$i=0;
	        			foreach($body_part_data as $body_part_id=>$dtm_data)
	        			{
	        				foreach($dtm_data as $dtm_id=>$gsm_data)
	        				{
	        					foreach ($gsm_data as $gsm_id => $color_data) 
	        					{
	        						foreach ($color_data as $color_id => $color_type_data) 
	        						{
	        							foreach ($color_type_data as $color_type_id => $dia_data) 
	        							{
	        								foreach ($dia_data as $dia => $dia_width_data) 
	        								{
	        									foreach($dia_width_data as $dia_width=>$value)
					        					{
					        						$txt_finish_qnty=($value["req_dzn"]/12)*$sample_dtls_array[$sample_type][$gmts_item_id][$value["gmts_color_id"]];
					        						$processloss=$value["process_loss"];
					        						$WastageQty='';
					        						if($process_loss_method==1)
					        						{
					        							$WastageQty=$txt_finish_qnty+$txt_finish_qnty*($processloss/100);
					        						}
					        						else if($process_loss_method==2)
					        						{
					        							$devided_val = 1-($processloss/100);
					        							$WastageQty=$txt_finish_qnty/$devided_val;
					        						}
					        						else
					        						{
					        							$WastageQty=0;
					        						}



					        						?>
					        						<tr>

					        							<?
					        							if($i==0)
					        							{
					        								?>
					        								<td width="30" rowspan="<? echo $sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id];?>" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
					        								<td width="90" rowspan="<? echo $sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_wise_article_no[$sample_type][$gmts_color_id];?></td>
					        								<td width="110" rowspan="<? echo $sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_library[$sample_type]; ?></td>
					        								<td width="80"  align="center" rowspan="<? echo $sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id];?>"><? echo $color_lib[$value["gmts_color"]];?> </td>
					        								<?
					        							}
					        							$i++;
					        							?>
					        								<td width="120"     align="center"><? echo $body_part[$body_part_id];?></td>

					        						<td width="200"  align="center"><? echo $value["fabric_description"]. ",GSM ".$value["gsm_weight"];?></td>
					        						<td width="80"  align="center"> <? echo $color_type[$value["color_type_id"]]; ?></td>
					        						<td width="80"  align="center"><? echo $color_lib[$color_id]; ?></td>

					        						<td width="40"  align="center"><? echo $value["gsm_weight"]; ?></td>
					        						<td width="55"  align="center"><? echo $value["dia"]; ?></td>
					        						<td width="80"  align="center"><? echo $fabric_typee[$value["dia_width"]]; ?></td>
					        							<td width="80" align="right" title="<? echo 'Req/dzn('.$value["req_dzn"].')/12'.'*Sample Requisition Qty='.$sample_dtls_array[$sample_type][$gmts_item_id][$value["gmts_color_id"]];?>"><? echo number_format($value["finish_fabric"],2);?></td>
													<td width="80" align="right"><? echo number_format($value["wo_qty"],2);?></td>
					        						<td width="40" align="right"><? echo $value["process_loss"];?></td>
					        						<td width="60" align="right"><? echo number_format($value["grey_fabric"],2);?></td>
					        						
													<?php
														if($show_comment==1){?>
													<td width="60" align="right"><? echo number_format($value["rate"],2);?></td>
													<td width="60" align="right"><? echo number_format($value["amount"],2);?></td>
														<?}?>
					        						<td width="40"  align="center"><? echo $unit_of_measurement[$value["uom"]];?></td>
					        						<td><p><? echo  $value["remarks"];?> </p></td>


					        						</tr>


						        					<?
						        					//$i++;
						        					$total_finish +=$value["finish_fabric"];
						        					$total_grey +=$value["grey_fabric"];
						        					$total_process +=$value["process_loss"];
													$total_amount +=$value["amount"];
													$total_wo_qty +=$value["wo_qty"];
					        					}
	        								}
	        							}
	        						}
	        					}
	        				}
	        			}
	        		}
	        	}
	    	}
	        ?>

	   			<tr>
					<th colspan="11" align="right"><b>Total</b></th>
					<th width="80" align="right"><? echo number_format($total_finish,2);?></th>
					<th width="80" align="right"><? echo number_format($total_wo_qty,2);?></th>
					<th width="40" align="right">&nbsp;</th>
					<th width="60" align="right"><? echo number_format($total_grey,2);?></th>
					<?php
					if($show_comment==1){?>
					<th width="40" align="right">&nbsp;</th>
					<th width="40" align="right"><? echo number_format($total_amount,2);?></th>
					<?}?>
					<th width="80" colspan="2"> </th>

	            </tr>
        </tbody>
    </table>
    <br/>
  	<br/>
	<?
	// echo "Tipu";die;
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$mst_style_id=rtrim($sample_mst_id,',');
	$mst_style_id=implode(",",array_unique(explode(",",$mst_style_id)));
	$sql_stripe="select a.requisition_number,b.body_part_id,b.gmts_item_id,b.fabric_description,b.gsm as gsm_weight,b.dia as dia_width,b.color_type_id,b.required_dzn,b.required_qty,c.color_id as color_id,d.id as did,d.uom,d.measurement,d.stripe_color,d.fabreqtotkg,d.yarn_dyed from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b,wo_sample_stripe_color d where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id  and b.id=d.sample_fab_dtls_id and d.req_no=a.requisition_number and c.color_id=d.color_number_id  and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and  a.id in($mst_style_id) and b.color_type_id in (2,3,4,6,31,32,33,34) order by  d.id";
	$result_data=sql_select($sql_stripe);
	foreach($result_data as $row)
	{
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['construction']=$row[csf('construction')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['color_type_id']=$row[csf('color_type_id')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['req_no']=$row[csf('requisition_number')];
		$tot_stripe_measurement_arr[$row[csf('color_id')]]+=$row[csf('measurement')];


	}
	//echo $tot_stripe_measurement;

	if(count($stripe_arr)>0)
	{
		?>

		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="9" align="center"><b>Stripe Details</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>
	        	<th width="50"> Req. No</th>
	            <th width="100"> Body Part</th>
	            <th width="80"> Fabric Color</th>
	            <th width="70"> Fabric Qty(KG)</th>
	            <th width="70"> Stripe Color</th>
	            <th width="70"> Stripe Measurement</th>
	            <th width="70"> Stripe Uom</th>
	            <th  width="70"> Qty.(KG)</th>
	            <th  width="70"> Y/D Req.</th>
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();
		//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
			 $stripe_wise_fabkg_sql=sql_select("SELECT b.color_id as color_id,c.required_qty as sample_prod_qty,c.body_part_id,c.color_type_id,b.qnty,b.grey_fab_qnty from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where
				a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id)  and  a.sample_prod_qty>0
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");


			foreach($stripe_wise_fabkg_sql as $vals)
			{
				//$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
				$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("qnty")];
			}
	        foreach($stripe_arr as $body_id=>$body_data)
	        {
				foreach($body_data as $color_id=>$color_val)
				{
					$rowspan=count($color_val['stripe_color']);
					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
					?>
					<tr>
						<?
						$jobs=$stripe_arr2[$body_id][$color_id]['req_no'];
						$color_qty=$stripe_wise_fabkg_arr[$body_id][$color_type_id][$color_id]; //$stripe_wise_fabkg_arr[$jobs][$body_id][$color_type_id][$color_id];
						//echo $body_id.'='.$color_type_id.'='.$color_id;
						?>
						<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $jobs; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?></td>
						<?
						$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
						$total_fab_qty+=$color_qty;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$measurement=$color_val['measurement'][$strip_color_id];
							$uom=$color_val['uom'][$strip_color_id];
							$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
							?>
							<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
							<td align="right"> <? echo  number_format($measurement,2); ?></td>
		                    <td> <? echo  $unit_of_measurement[$uom]; ?></td>
							<td align="right" title="Stripe Measurement/Tot Stripe Measurement(<? echo $tot_stripe_measurement;?>)*Fabric Qty(KG)"> <? echo  number_format($fabreqtotkg,2); ?></td>
							<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
						}
							$i++;
				}
			}
			?>
	        <tfoot>
	        	<tr>
	        		<td colspan="4">Total </td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	        		<td></td>
	        		<td></td>
	        		<td>   </td>
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		<?
	}
	?>
	<br/><br/>
    <table class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="130" >Item Group</th>
    			<th align="center" width="100" >Description</th>
    			<th align="center" width="100" >UOM</th>
    			<th align="center" width="100" >Qty</th>
    			<th align="center"   >Remarks</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?
    		$lib_item_group_arr=array();
    		$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
    		foreach ($itemArray as $row)
    		{
    			$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
    		}

    		$data_array=sql_select("SELECT booking_no, item_group_id, description, uom, qty,   remarks from wo_non_ord_booking_acc_dtls where  booking_no=$txt_booking_no");
    		if ( count($data_array)>0)
    		{
    			$l=1;
    			$tot_qnty=0;
    			foreach( $data_array as $key=>$row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $lib_item_group_arr[$row[csf("item_group_id")]]; ?> </td>
    					<td  align="center"> <? echo $row[csf("description")]; ?> </td>
    					<td  align="center"> <? echo $unit_of_measurement[$row[csf("uom")]]; ?> </td>
    					<td  align="center"> <? echo $qnty=$row[csf("qty")]; ?> </td>
    					<td  align="center"> <? echo $row[csf("remarks")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_qnty+=$qnty;
    			}
    			?>
    			<tr>
    				<td colspan="4" align="right"><strong>Grand Total</strong> </td>
    				<td  align="center"> <? echo $tot_qnty; ?> </td>
    				<td  align="center">  </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>
	<br/><br/>
    <table class="rpt_table"  style="margin-top: 10px;" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="120" >Size Name</th>
    			<th align="center" width="100" >BH Qty</th>
    			<th align="center" width="100" >Plan </th>
    			<th align="center" width="100" >Dyeing </th>
				<th align="center" width="100" >Test </th>
				<th align="center" width="100" >Self </th>
    			<th align="center"   >Total</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?
    		//sample_development_mst
			$sql_size="select c.id, c.mst_id, c.dtls_id, c.size_id, c.size_qty,c.bh_qty,c.plan_qty,c.dyeing_qty,c.test_qty,c.self_qty,c.total_qty from sample_development_mst a,sample_development_size c where a.id=c.mst_id and a.entry_form_id=117  and a.id in($mst_style_id)";
			$size_result=sql_select($sql_size);

    		if ( count($size_result)>0)
    		{
    			$l=1;
    			$tot_plan_qnty=$tot_bh_qty=$tot_dyeing_qty=$tot_test_qty=$tot_self_qty=$tot_total_qty=0;
    			foreach( $size_result as $row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $size_library[$row[csf("size_id")]]; ?> </td>
    					<td  align="right"> <? echo $row[csf("bh_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("plan_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("dyeing_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("test_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("self_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("total_qty")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_plan_qnty+=$row[csf("plan_qty")];
					$tot_bh_qty+=$row[csf("bh_qty")];
					$tot_dyeing_qty+=$row[csf("dyeing_qty")];
					$tot_test_qty+=$row[csf("test_qty")];
					$tot_self_qty+=$row[csf("self_qty")];
					$tot_total_qty+=$row[csf("total_qty")];
    			}
    			?>
    			<tr>
    				<td colspan="2" align="right"><strong>Grand Total</strong> </td>
    				<td  align="right"> <? echo number_format($tot_bh_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_plan_qnty,0); ?> </td>
    				<td  align="right"> <? echo number_format($tot_dyeing_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_test_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_self_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_total_qty,0); ?> </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>

            <br/> <br/>


    		<table style="margin-top: 10px;" class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no=$txt_booking_no");
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{

							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}

					?>
                </tbody>
            </table>
             </br>

          <?
		 	echo signature_table(6, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?

}

if($action=="show_fabric_booking_report_barnali") // rehan for barnali
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name='$cbo_company_name'  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");


	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	if ($db_type == 0) $select_group_row = " order by master_tble_id desc limit 0,3";
	else if ($db_type == 2) $select_group_row = " and  rownum<=4 order by id desc";
	$imge_arr_for_book=sql_select( "select master_tble_id,image_location,real_file_name from   common_photo_library where  master_tble_id=$txt_booking_no and file_type=1  $select_group_row ");

	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");


	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");

	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">
    										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php	echo $company_library[$cbo_company_name];			?>
                              </strong>

						   </td>

                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
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
                                           <? echo $result[csf('plot_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('level_no')].'&nbsp;' ?>
                                            <? echo $result[csf('road_no')].'&nbsp;'; ?>
                                            <? echo $result[csf('block_no')].'&nbsp;';?>
                                            <? echo $result[csf('city')].'&nbsp;';?>
                                            <? echo $result[csf('zip_code')].'&nbsp;'; ?>
                                             <?php echo $result[csf('province')] . '&nbsp;'; ?>
                                           <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br>
                                            <? echo $result[csf('email')];?>
                                             <? echo $result[csf('website')];
                            }
                                            ?>

                               </td>
                            </tr>
                            <tr>

                            <td align="center" style="font-size:20px;"> <strong style="margin-left:77px;"><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             <div style="margin-left:854px; margin-top:-75px; position: absolute; float:right;  ">
	                            <?
								foreach($imge_arr_for_book as $row)
								{
									?>
	                                <img  src='../../<? echo $row[csf('image_location')]; ?>' height='80' width='80' />
									<?
								}

								?>
                            </div>
                             </td>

                              <td>
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>

								  <?
								 }
							  	?>

                             </td>

                            </tr>
                      </table>

                </td>
                <td width="250" id="barcode_img_id">

                </td>
            </tr>
       </table>

                <?
				$season="";
				$req_no="";
				$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";

				$nameseason=sql_select( "SELECT a.season as season_buyer_wise, b.buyer_req_no,a.buyer_ref,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept,a.requisition_number_prefix_num,a.remarks  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and b.status_active=1 and   b.is_deleted=0 and c.status_active=1 and   c.is_deleted=0 ");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season_buyer_wise')];
					$buyer_req_no=$season_row[csf('buyer_ref')];
					$bh_merchant=$season_row[csf('bh_merchant')];
					$style_ref_no=$season_row[csf('style_ref_no')];
					$product_code=$season_row[csf('product_code')];
					$product_department=$product_dept[$season_row[csf('product_dept')]];
					$req_no=$season_row[csf('requisition_number_prefix_num')];
					$remarks=$season_row[csf('remarks')];

				}

				$fabric_source='';
				$season_library=return_library_array( "SELECT id,season_name from lib_buyer_season", "id", "season_name");
                $nameArray=sql_select( "SELECT buyer_id,fabric_source,booking_no,pay_mode,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,team_leader,	dealing_marchant,grouping,remarks from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source_id=$result[csf('fabric_source')];
					$varcode_booking_no=$result[csf('booking_no')];
				?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:18px"><b>Booking No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>

                <td width="100"><span style="font-size:12px">&nbsp;</span></td>
                <td width="110">&nbsp;</td>


            </tr>
            <tr>

                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:12px">:&nbsp; <b><?
					if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5 ){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?></b></td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;

				<?
				if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5 ){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]);
					foreach ($comAdd as $comAddRow){
						echo $comAddRow[csf('plot_no')].'&nbsp;';
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;';
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';

					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>

            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>


            </tr>
             <tr>
             	<td width="100" style="font-size:12px"><b>Fabric Source</b></td>
             	<td width="110">:&nbsp;<? echo $fabric_source_new[$fabric_source_id]; ?></td>
             	<td  width="100" style="font-size:12px"><b>Req. No</b></td>
             	<td  width="110" >:&nbsp;<? echo $req_no; ?></td>
             	<td  width="100" style="font-size:12px"><b>Style Ref.</b></td>
             	<td  width="110" >:&nbsp;<? echo $style_ref_no ; ?></td>
             	<td  width="100" style="font-size:12px"><b>Internal Ref.</b></td>
             	<td  width="110" >:&nbsp;<strong><? echo $result[csf('grouping')] ; ?></strong></td>

              </tr>
			  <tr>
             	<td width="100" style="font-size:12px"><b>Remarks</b></td>
             	<td width="110">:&nbsp;<? echo $remarks ; ?></td>
             	<td  width="100" style="font-size:12px"><b></b></td>
             	<td  width="110" ></td>
             	<td  width="100" style="font-size:12px"></td>
             	<td  width="110" ></td>
             	<td  width="100" style="font-size:12px"></td>
             	<td  width="110" ></td>

              </tr>
        </table>
        <?
			}
		?>

      <br/>

      <?


    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	 $sql_dtls= "SELECT  a.lib_yarn_count_deter_id,a.gsm_weight,a.style_id,a.style_des,a.sample_type,a.body_part,a.color_type_id,a.construction,a.yarn_details,a.composition,a.fabric_color,a.gmts_color , a.gmts_size,a.item_size,a.dia_width,a.finish_fabric,a.grey_fabric,a.process_loss, a.rate,a.amount,a.id,a.remarks,a.fabric_source,a.delivery_date,a.dtls_id,a.uom ,a.width_dia_type as dia_width,a.dia,a.fabric_description,a.gmts_item_id,a.color_all_data,a.req_dzn, a.wo_qty,a.additional_process FROM wo_non_ord_samp_booking_dtls a WHERE   a.finish_fabric>0 and a.booking_no =$txt_booking_no and a.entry_form_id=140  and a.status_active=1 and	a.is_deleted=0 order by a.id";
	
	//echo $sql_dtls;
	$sample_result=sql_select($sql_dtls);
	foreach($sample_result as $key=>$row)  
	{
		$dtm_idArr[$row[csf("lib_yarn_count_deter_id")]]=$row[csf("lib_yarn_count_deter_id")];
	}
	$sql_deter_dtls= "SELECT a.id as deter_id,c.yarn_count from lib_yarn_count_determina_dtls b,lib_yarn_count_determina_mst a,lib_yarn_count c  where  a.id=b.mst_id and c.id=b.count_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1  and a.id in(". implode(",",$dtm_idArr).")";
	$deter_result=sql_select($sql_deter_dtls);
	foreach($deter_result as $key=>$row)  
	{
		$lib_yarn_count[$row[csf("deter_id")]]=$row[csf("yarn_count")];
	}
	
	$style_id='';
	foreach($sample_result as $key=>$row)  
	{
		$sample_type=$row[csf("sample_type")];
		$gmtsitem_id=$row[csf("gmts_item_id")];
		$color_id=$row[csf("gmts_color")];
		$body_part_id=$row[csf("body_part")];
		$dtm_id=$row[csf("lib_yarn_count_deter_id")];
		$gsm_id=$row[csf("gsm_weight")];
		$fabric_color=$row[csf("fabric_color")];

		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["gsm_weight"]=$row[csf("gsm_weight")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["fabric_description"]=$row[csf("fabric_description")]." ".$lib_yarn_count[$dtm_id];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["color_type_id"]=$row[csf("color_type_id")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["finish_fabric"]+=$row[csf("finish_fabric")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["grey_fabric"]+=$row[csf("grey_fabric")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["amount"]+=$row[csf("amount")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["rate"]=$row[csf("rate")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["process_loss"]=$row[csf("process_loss")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["remarks"]=$row[csf("remarks")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["additional_process"]=$row[csf("additional_process")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["dia"]=$row[csf("dia")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["dia_width"]=$row[csf("dia_width")];
		$booking_dtls_color_wise[$row[csf("uom")]][$sample_type][$gmtsitem_id][$body_part_id][$dtm_id][$color_id][$fabric_color]["wo_qty"]=$row[csf("wo_qty")];
		$booking_stripe_wise_fabkg_arr[$body_part_id][$row[csf("color_type_id")]][$color_id] +=$row[csf("finish_fabric")];
	}

	//echo "<pre>";print_r($data_array_color_wise);die;
	$sample_item_wise_span_arr=array();$sample_body_wise_span_arr=array();$sample_fab_wise_span_arr=array();
	foreach($booking_dtls_color_wise as $uom_id=>$uom_data)
	{
		foreach($uom_data as $sample_type_id=>$sample_data)
		{
			$sample_row_span=0;
			foreach($sample_data as $item_id=>$item_data)
			{
				$body_row_span=0;
				foreach($item_data as $body_part_id=>$body_data)
				{
					$fab_row_span=0;
					foreach($body_data as $deter_id=>$deter_data)
					{

						foreach($deter_data as $gmt_color_id=>$gmt_color_data)
						{

							foreach($gmt_color_data as $fab_color_id=>$value)
							{
								$sample_row_span++; $body_row_span++;$fab_row_span++;
							}
							$sample_item_wise_span_arr[$sample_type_id][$uom_id]=$sample_row_span;
							$sample_body_wise_span_arr[$uom_id][$sample_type_id][$item_id][$body_part_id]=$body_row_span;
							$sample_fab_wise_span_arr[$uom_id][$sample_type_id][$item_id][$body_part_id][$deter_id]=$fab_row_span;
						}
					}
				}
			}
		}
	}
//	echo "<pre>";
	//print_r($sample_body_wise_span_arr);



	$sample_mst_id=$sample_result[0][csf("style_id")];

	$sql_sample_dtls= "SELECT a.sample_name,a.article_no,b.id as color_name  from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=117  and sample_mst_id=$sample_mst_id and b.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,b.id";

	foreach(sql_select($sql_sample_dtls) as $key=>$value)
	{
		if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=="")
		{
			$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=$value[csf("article_no")];
		}
		else
		{
			if(!in_array($value[csf("article_no")], $sample_wise_article_no))
			{
				$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]].= ','.$value[csf("article_no")];
			}

		}

	}

	$sample_dtls_sql="SELECT sample_name,gmts_item_id,sample_color,sample_prod_qty from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$sample_mst_id and entry_form_id=117";
	 foreach(sql_select($sample_dtls_sql) as $vals)
	 {
	 	$sample_dtls_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("sample_color")]]+=$vals[csf("sample_prod_qty")];

	 }

	?>
    <div>
    <?
	$show_comment=str_replace("'","",$show_comment);
    foreach($booking_dtls_color_wise as $uom_id=>$uom_data)
	{
	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
    	<caption> <b> <? echo "Uom:".$unit_of_measurement[$uom_id];?></b></caption>
        <thead>
            <tr>
				<th width="30">Sl</th>
				<th width="90">Article No</th>
				<th width="110">Sample</th>
				<th width="110">Garment Item</th>
				<th width="120">Body Part</th>
				<th width="200">Fabric Details and Composition</th>
				<th width="100">Additional Process</th>
                <th width="80"> Gmts Color</th>
				<th width="80">Color Type</th>
				<th width="80">Fab.Color</th>

				<th width="40">GSM</th>
				<th width="55">Dia</th>
				<th width="80">Dia Type</th>
				<th width="80">Fin Fab Qnty</th>
				<th width="80">WO Qnty</th>
				<th width="40">P. Loss</th>
				<th width="60">Grey Qnty</th>

				<th width="40">UOM</th>

                <?
                if($show_comment==1)
				{
				?>
                <th width="40">Rate</th>
                <th width="50">Amount</th>
                <?
				}
				?>
				<th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        <?
        $p=1;
        $total_finish=0;
        $total_grey=$total_amount=0;
        $total_process=0;




		foreach($uom_data as $sample_type_id=>$sample_data)
		{
			$s=1;
			foreach($sample_data as $item_id=>$item_data)
			{
				$b=1;
				foreach($item_data as $body_part_id=>$body_data)
				{
					$f=1;
					foreach($body_data as $deter_id=>$deter_data)
					{
						foreach($deter_data as $gmt_color_id=>$gmt_color_data)
						{
							foreach($gmt_color_data as $fab_color_id=>$value)
							{
				        						?>
				        						<tr>
				        							<?
				        							if($s==1)
				        							{
				        								?>
				        								<td width="30" rowspan="<? echo $sample_item_wise_span_arr[$sample_type_id][$uom_id];?>" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
				        								<td width="90" rowspan="<? echo $sample_item_wise_span_arr[$sample_type_id][$uom_id];?>"  align="center"><? echo $sample_wise_article_no[$sample_type_id][$gmt_color_id];?></td>
				        								<td width="110" rowspan="<? echo $sample_item_wise_span_arr[$sample_type_id][$uom_id];?>"  align="center"><? echo $sample_library[$sample_type_id]; ?></td>
														

				        								<? //$sample_fab_wise_span_arr[$uom_id][$sample_type_id][$item_id][$body_part_id][$deter_id];
				        							}
				        							?>
                                                <td width="110" rowspan="<? //echo $sample_item_wise_span_arr[$sample_type_id][$uom_id];?>"  align="center"><? echo $garments_item[$item_id]; ?></td>
				        						<td width="120" rowspan="<? //echo $sample_body_wise_span_arr[$uom_id][$sample_type_id][$item_id][$body_part_id];?>"    align="center"><? echo $body_part[$body_part_id];?></td>
				        						<td width="200" rowspan="<? //echo $sample_fab_wise_span_arr[$uom_id][$sample_type_id][$item_id][$body_part_id][$deter_id];?>"  align="center"><? echo $value["fabric_description"]. ",GSM ".$value["gsm_weight"];?></td>
												<td width="100" align="center"><?=$value["additional_process"];?></td>
                                                <td width="80"  align="center" rowspan="<? //echo $sample_item_wise_span[$sample_type][$gmts_item_id][$gmts_color_id];?>"><? echo $color_lib[$gmt_color_id];?> </td>
				        						<td width="80"  align="center"> <? echo $color_type[$value["color_type_id"]]; ?></td>
				        						<td width="80"  align="center"><? echo $color_lib[$fab_color_id]; ?></td>

				        						<td width="40"  align="center"><? echo $value["gsm_weight"]; ?></td>
				        						<td width="55"  align="center"><? echo $value["dia"]; ?></td>
				        						<td width="80"  align="center"><? echo $fabric_typee[$value["dia_width"]]; ?></td>
				        						<td width="80" align="right"><? echo number_format($value["finish_fabric"],2);?></td>
				        						<td width="80" align="right"><? echo number_format($value["wo_qty"],2);?></td>
				        						<td width="40" align="right"><? echo $value["process_loss"];?></td>
				        						<td width="60" align="right"><? echo number_format($value["grey_fabric"],2);?></td>
				        						<td width="40"  align="center"><? echo $unit_of_measurement[$uom_id];?></td>
                                                 <?
												if($show_comment==1)
												{
												?>
												<td width="40"  align="right"><? echo $value["rate"]; ?></td>
												<td width="50"  align="right"><? echo number_format($value["amount"],4); ?></td>
												<?
												}
												?>
				        						<td><p><? echo  $value["remarks"];?> </p></td>
				        						</tr>
					        					<?
					        					//$i++;
												$s++;$b++;//$f++;
					        					$total_finish +=$value["finish_fabric"];
					        					$total_grey +=$value["grey_fabric"];
												$total_amount +=$value["amount"];
					        					$total_process +=$value["process_loss"];
					        					$total_wo_qty +=$value["wo_qty"];

				        					}
        								}
        							}
        						}
        					}
        				}
        ?>
       			<tr>
					<th colspan="13" align="right"><b>Total</b></th>
					<th width="80" align="right"><? echo number_format($total_finish,2);?></th>
					<th width="80" align="right"><? echo number_format($total_wo_qty,2);?></th>
					<th width="40" align="right">&nbsp;</th>
					<th width="60" align="right"><? echo number_format($total_grey,2);?></th>
                    <th width="40" align="right">&nbsp;</th>
                     <?
					if($show_comment==1)
					{
					?>
                    <th width="40" align="right">&nbsp;</th>
					<th width="50" align="right"> <? echo number_format($total_amount,4);?></th>
                    <?
					}
					?>
					<th width=""> </th>
	            </tr>
        </tbody>
  	  </table>
   	 <br/>
		<?
        } //Uom End here
        ?>
     </div>
    <br/>
  <br/>
	<?

	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$mst_style_id=rtrim($sample_mst_id,',');
	$mst_style_id=implode(",",array_unique(explode(",",$mst_style_id)));
	$sql_stripe="select a.requisition_number,b.body_part_id,b.gmts_item_id,b.fabric_description,b.gsm as gsm_weight,b.dia as dia_width,b.color_type_id,b.required_dzn,b.required_qty,c.color_id as color_id,d.id as did,d.uom,d.measurement,d.stripe_color,d.fabreqtotkg,d.yarn_dyed,b.sample_name from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b,wo_sample_stripe_color d where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id  and b.id=d.sample_fab_dtls_id and d.req_no=a.requisition_number and c.color_id=d.color_number_id  and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 and  a.id in($mst_style_id) and b.color_type_id in (2,3,4,6,31,32,33,34) group by a.requisition_number,b.body_part_id,b.gmts_item_id,b.fabric_description,b.gsm,b.dia,b.color_type_id,b.required_dzn,b.required_qty,c.color_id,d.id,d.uom,d.measurement,d.stripe_color,d.fabreqtotkg,d.yarn_dyed,b.sample_name order by  d.id";// group by added for issue id 28516
	$result_data=sql_select($sql_stripe);
	foreach($result_data as $row)
	{
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabric_description'][$row[csf('did')]]=$row[csf('fabric_description')];

		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['construction']=$row[csf('construction')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['color_type_id']=$row[csf('color_type_id')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['sample_name']=$row[csf('sample_name')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_id')]]['req_no']=$row[csf('requisition_number')];
		$tot_stripe_measurement_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]+=$row[csf('measurement')];


	}
	//echo $tot_stripe_measurement;

	if(count($stripe_arr)>0)
	{
		?>

		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="9" align="center"><b>Stripe Details</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>
	        	<th width="50"> Req. No</th>
				<th width="100"> Sample 	</th>
				<th width="100"> Garment Item</th>
	            <th width="100"> Body Part</th>
	            <th width="150"> Fabric Details and Composition </th>
	            <th width="80"> Fabric Color</th>
	            <th width="70"> Fabric Qty(KG)</th>
	            <th width="70"> Stripe Color</th>
	            <th width="70"> Stripe Measurement</th>
	            <th width="70"> Stripe Uom</th>
	            <th  width="70"> Qty.(KG)</th>
	            <th  width="70"> Y/D Req.</th>
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();
		//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
			 $stripe_wise_fabkg_sql=sql_select("SELECT b.color_id as color_id,sum(c.required_qty) as sample_prod_qty,c.body_part_id,c.color_type_id,b.qnty,b.grey_fab_qnty from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where
				a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id)  and  a.sample_prod_qty>0
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_id  ,c.body_part_id,c.color_type_id,b.qnty,b.grey_fab_qnty");


			foreach($stripe_wise_fabkg_sql as $vals)
			{
				//$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
				$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("qnty")];
			}
			
			$tot_stripe_measurement=0;
			foreach($stripe_arr as $body_id=>$body_data)
	        {
				foreach($body_data as $color_id=>$color_val)
				{
					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$tot_stripe_measurement+=$color_val['measurement'][$strip_color_id];
							
						}
					
				}
				
			}
			
	        foreach($stripe_arr as $body_id=>$body_data)
	        {
				foreach($body_data as $color_id=>$color_val)
				{
					$rowspan=count($color_val['stripe_color']);
					$fabric_description=$stripe_arr2[$body_id][$color_id]['fabric_description'];
					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
					$sample_name=$stripe_arr2[$body_id][$color_id]['sample_name'];
					$gmts_item_id=$stripe_arr2[$body_id][$color_id]['gmts_item_id'];
					?>
				
					<tr>
						<?
						$jobs=$stripe_arr2[$body_id][$color_id]['req_no'];
						//$color_qty=$stripe_wise_fabkg_arr[$body_id][$color_type_id][$color_id]; //$stripe_wise_fabkg_arr[$jobs][$body_id][$color_type_id][$color_id];
						$color_qty=$booking_stripe_wise_fabkg_arr[$body_id][$color_type_id][$color_id];
						//echo $body_id.'='.$color_type_id.'='.$color_id;
						?>
						<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $jobs; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $sample_library[$sample_name]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $garments_item[$gmts_item_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $fabric_description; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?></td>
						<?
						$tot_stripe_measurement=$tot_stripe_measurement_arr[$body_id][$color_id];//Issue id=22968, NZ-Sumchu
						$total_fab_qty+=$color_qty;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$measurement=$color_val['measurement'][$strip_color_id];
							$uom=$color_val['uom'][$strip_color_id];
							$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
							?>
							<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
							<td align="right"> <? echo  number_format($measurement,2); ?></td>
		                    <td> <? echo  $unit_of_measurement[$uom]; ?></td>
							<td align="right" title="Stripe Measurement(<? echo $measurement;?>)/Tot Stripe Measurement(<? echo $tot_stripe_measurement;?>)*Fabric Qty(KG)"> <? echo  number_format($fabreqtotkg,2); ?></td>
							<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
						}
							$i++;
				}
			}
			?>
	        <tfoot>
	        	<tr>
	        		<td colspan="7" align="right"><b>Total </b></td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	        		<td></td>
	        		<td></td>
	        		<td>   </td>
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		<?
	}
	?>
	<br/><br/>

	<table width="1330">
		<tr>
			<td width="850">
				<table class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
				    	<thead>
				    		<tr>
				    			<th align="center" width="40">Sl</th>
				    			<th align="center" width="130" >Item Group</th>
				    			<th align="center" width="100" >Description</th>
				    			<th align="center" width="100" >UOM</th>
				    			<th align="center" width="100" >Qty</th>
				    			<th align="center"   >Remarks</th>
				    		</tr>
				    	</thead>
				    	<tbody>
				    		<?
				    		$lib_item_group_arr=array();
				    		$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
				    		foreach ($itemArray as $row)
				    		{
				    			$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
				    		}

				    		$data_array=sql_select("SELECT booking_no, item_group_id, description, uom, qty,   remarks from wo_non_ord_booking_acc_dtls where  booking_no=$txt_booking_no");
				    		if ( count($data_array)>0)
				    		{
				    			$l=1;
				    			$tot_qnty=0;
				    			foreach( $data_array as $key=>$row )
				    			{

				    				?>
				    				<tr>
				    					<td  align="center"> <? echo $l;?> </td>
				    					<td  align="center"> <? echo $lib_item_group_arr[$row[csf("item_group_id")]]; ?> </td>
				    					<td  align="center"> <? echo $row[csf("description")]; ?> </td>
				    					<td  align="center"> <? echo $unit_of_measurement[$row[csf("uom")]]; ?> </td>
				    					<td  align="center"> <? echo $qnty=$row[csf("qty")]; ?> </td>
				    					<td  align="center"> <? echo $row[csf("remarks")]; ?> </td>
				    				</tr>
				    				<?
				    				$l++;
				    				$tot_qnty+=$qnty;
				    			}
				    			?>
				    			<tr>
				    				<td colspan="4" align="right"><strong>Grand Total</strong> </td>
				    				<td  align="center"> <? echo $tot_qnty; ?> </td>
				    				<td  align="center">  </td>
				    			</tr>
				    				<?
				    		}

				    		?>
				    	</tbody>
				    </table>
			</td>
			<td rowspan="3">
				<table class="rpt_table" align="right" border="1" cellpadding="0" cellspacing="0" rules="all" width="500">
					<thead>
						<tr>
							<th colspan="3">Yarn Summary</th>
						</tr>
						<tr>
							<th>Count</th>
							<th>Composition</th>
							<th>Type</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?php 

							$sql="SELECT count_id,copm_one_id, type_id, sum(cons_qnty) as cons_qnty from sample_development_yarn_dtls where is_deleted=0 and status_active=1 and booking_no ='".str_replace("'", "", $txt_booking_no)."' group by count_id,copm_one_id, type_id order by count_id";
							//echo $sql;
							$yarn_dtls = sql_select($sql);

							//print_r($composition);die;
							
						$composition_arr = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");


							$count_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
							foreach ($yarn_dtls as $yarnData) 
							{
								?>
								<tr id="yarncost_<? echo $i; ?>" align="center">
						                <td>
							               <? echo $count_arr[$yarnData[csf('count_id')]];  ?>
							               
						                </td>
						                <td><? echo $composition_arr[$yarnData[csf('copm_one_id')]];    ?></td>
						                <td> <?php echo $yarn_type[$yarnData[csf('type_id')]]; ?> </td>
						               
						                <td>
						                    <? echo number_format($yarnData[csf('cons_qnty')],4); ?>
						                </td>
						            </tr>
								<?
								$i++;
							}
						 ?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td width="850">
				    <table class="rpt_table"  style="margin-top: 10px;" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
				        	<thead>
				        		<tr>
				        			<th align="center" width="40">Sl</th>
				        			<th align="center" width="120" >Size Name</th>
				        			<th align="center" width="100" >BH Qty</th>
				        			<th align="center" width="100" >Plan </th>
				        			<th align="center" width="100" >Dyeing </th>
				    				<th align="center" width="100" >Test </th>
				    				<th align="center" width="100" >Self </th>
				        			<th align="center"   >Total</th>
				        		</tr>
				        	</thead>
				        	<tbody>
				        		<?
				        		//sample_development_mst
				    			$sql_size="select c.id, c.mst_id, c.dtls_id, c.size_id, c.size_qty,c.bh_qty,c.plan_qty,c.dyeing_qty,c.test_qty,c.self_qty,c.total_qty from sample_development_mst a,sample_development_size c where a.id=c.mst_id and a.entry_form_id=117  and a.id in($mst_style_id)";
				    			$size_result=sql_select($sql_size);

				        		if ( count($size_result)>0)
				        		{
				        			$l=1;
				        			$tot_plan_qnty=$tot_bh_qty=$tot_dyeing_qty=$tot_test_qty=$tot_self_qty=$tot_total_qty=0;
				        			foreach( $size_result as $row )
				        			{

				        				?>
				        				<tr>
				        					<td  align="center"> <? echo $l;?> </td>
				        					<td  align="center"> <? echo $size_library[$row[csf("size_id")]]; ?> </td>
				        					<td  align="right"> <? echo $row[csf("bh_qty")]; ?> </td>
				        					<td  align="right"> <? echo $row[csf("plan_qty")]; ?> </td>
				        					<td  align="right"> <? echo $row[csf("dyeing_qty")]; ?> </td>
				        					<td  align="right"> <? echo $row[csf("test_qty")]; ?> </td>
				    						<td  align="right"> <? echo $row[csf("self_qty")]; ?> </td>
				    						<td  align="right"> <? echo $row[csf("total_qty")]; ?> </td>
				        				</tr>
				        				<?
				        				$l++;
				        				$tot_plan_qnty+=$row[csf("plan_qty")];
				    					$tot_bh_qty+=$row[csf("bh_qty")];
				    					$tot_dyeing_qty+=$row[csf("dyeing_qty")];
				    					$tot_test_qty+=$row[csf("test_qty")];
				    					$tot_self_qty+=$row[csf("self_qty")];
				    					$tot_total_qty+=$row[csf("total_qty")];
				        			}
				        			?>
				        			<tr>
				        				<td colspan="2" align="right"><strong>Grand Total</strong> </td>
				        				<td  align="right"> <? echo number_format($tot_bh_qty,0); ?> </td>
				    					<td  align="right"> <? echo number_format($tot_plan_qnty,0); ?> </td>
				        				<td  align="right"> <? echo number_format($tot_dyeing_qty,0); ?> </td>
				    					<td  align="right"> <? echo number_format($tot_test_qty,0); ?> </td>
				    					<td  align="right"> <? echo number_format($tot_self_qty,0); ?> </td>
				    					<td  align="right"> <? echo number_format($tot_total_qty,0); ?> </td>
				        			</tr>
				        				<?
				        		}

				        		?>
				        	</tbody>
				        </table>
			</td>
		</tr>
		<tr>
			<td width="850">
				<table style="margin-top: 10px;" class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
			                	<thead>
			                    	<tr>
			                        	<th align="left" width="40">Sl</th>
			                        	<th align="left" >Special Instruction</th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                    <?
								$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no=$txt_booking_no");
								if ( count($data_array)>0)
								{
									$l=1;
									foreach( $data_array as $key=>$row )
									{

										?>
			                            	<tr  align="">
			                                    <td> <? echo $l;?> </td>
			                                    <td> <? echo $row[csf("terms")]; ?> </td>
			                                </tr>
			                            <?
			                            $l++;
									}
								}

								?>
			                </tbody>
			            </table>
            
			</td>
		</tr>
	</table>
	<br>
	<br>
	<? $booking_mst_info=sql_select("SELECT a.id as mst_id, a.insert_date, b.user_full_name, c.custom_designation,a.company_id from wo_non_ord_samp_booking_mst a join user_passwd b on b.id = a.inserted_by join lib_designation c on c.id = b.designation where a.status_active=1 and a.is_deleted=0 and a.BOOKING_NO=$txt_booking_no");
	$mst_arr=array('insert_date','user_full_name','custom_designation','mst_id','company_id');
	foreach($booking_mst_info as $row){
		foreach($mst_arr as $data){
			$$data=$row[csf($data)];
		}
	}

	$electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 411 and company_id=$company_id and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 9 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 9 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 9 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
	
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_non_ord_samp_booking_mst a on a.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.mst_id = $mst_id and approval_history.entry_form = 9 and a.is_approved = 1 and approval_history.current_approval_status = 1 ");
	// and approval_history.sequence_no =".max($electronic_sequence_arr)
	?>

	<table id="signatureTblId" width="901.5" style="padding-top:70px;">
		<tr>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=$user_full_name?></strong>
				<br>
				<strong><?=$custom_designation?></strong>
				<br>
				<?= date('d-m-Y h:i:s a',strtotime($insert_date)); ?>
			</td>
			<!-- <td width="95"></td>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['USER_FULL_NAME'] : ''?></strong>
				<br>
				<strong><?=$sql_get_checked_user[0]['CUSTOM_DESIGNATION']?></strong>
				<br>
				<?= isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['APPROVED_DATE'] : ""?>
			</td> -->
			<td width="75"></td>
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
			<!-- <td width="75"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Checked by</strong></td> -->
			<td width="75"></td>
			<td width="95"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
		</tr>
	</table>
    
			 

          <!-- <?
		 	//echo signature_table(6, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?> -->
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?

}
?>
