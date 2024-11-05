<? 
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Trims Booking
Functionality	 :	
JS Functions	 :
Created by		 : MONZU 
Creation date 	 : 27-12-2012
Requirment Client: Fakir Apperels
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$trim_group= return_library_array("select id, item_name from lib_item_group where item_category=4",'id','item_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/trims_sample_booking_without_order_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );" );
	exit();	
} 
if ($action=="load_drop_down_buyer_tag_sample")
{
    $data=explode("_", $data);
    $sample_type_cond="";
    if(count($data)>1)
    {
        
        $sql_d=sql_select("select sample_name_ra from sample_development_fabric_acc where sample_mst_id=$data[1]");
        $sample_name_arr=array();
        foreach ($sql_d as $value) 
        {
            if(!empty($value[csf('sample_name_ra')]))
            {
                array_push($sample_name_arr, $value[csf('sample_name_ra')]);
            }
        }
       $sample_type_cond= where_con_using_array($sample_name_arr,0,"a.id");
    }
    $sql="select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c where b.buyer_id=c.id and  b.tag_sample=a.id  and b.buyer_id=$data[0] and b.sequ>0 and a.is_deleted=0 $sample_type_cond";
   // echo $sql;
	echo create_drop_down( "cbo_sample_type", 172, $sql,"id,sample_name", 1, "--Select--", $selected, "" ); 
    
	
exit();
} 
if ($action=="load_drop_down_trim_group")
{
    //echo "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$data  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $data=explode("_",$data);
    $sample_mst_id=$data[0];
    $sample_type_id=$data[1];
	$cbo_supplier_name=$data[2];
	
	$sql_supp="select rf_id from sample_development_supplier_dtls where mst_id in ($sample_mst_id) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
	$sql_suppRes=sql_select( $sql_supp ); $trim_id="";
	foreach($sql_suppRes as $row)
	{
		$trim_id.=$row[csf('rf_id')].",";
	}
	unset($sql_suppRes);
	$trim_ids=chop($trim_id,',');
	if($db_type==2)
	{
		if($trim_ids!="") $trim_idCond="and (b.id in ($trim_ids) or b.nominated_supp_multi is null or b.nominated_supp_multi='0')"; else $trim_idCond=" and (b.nominated_supp_multi is null or b.nominated_supp_multi='0')";
	}
	else
	{
		if($trim_ids!="") $trim_idCond="and (b.id in ($trim_ids) or b.nominated_supp_multi='')"; else $trim_idCond=" and b.nominated_supp_multi=''";
	}
	
    //echo  "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
  // echo "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id $trim_idCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
    echo create_drop_down( "cbo_trim_group", 172, "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id $trim_idCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id","id,item_name", 1, "--Select--", $selected, "fnc_disable();" ); //set_trim_cons_uom(this.value);
    exit();
}



function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_without_order_controller');",0,"" );
	}
	else
	{
	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_without_order_controller');","");
	}
	return $cbo_supplier_name;
	exit();
}
if ($action=="load_drop_down_supplier")
{
	echo $action($data);
	//echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
	exit();
}

if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}
if ($action=="set_cons_uom")
{
	$cons_uom=return_field_value("order_uom", "lib_item_group", "id=$data");
	echo $cons_uom; die;
}
if($action=="show_fabric_booking")
{
	extract($_REQUEST);
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

	$arr=array (0=>$style_library,2=>$sample_library,3=>$trim_group,4=>$unit_of_measurement,6=>$color_library,7=>$color_library,8=>$size_library);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$sql= "select style_id,style_des,sample_type,trim_group,uom,composition,barnd_sup_ref,gmts_color ,fabric_color,gmts_size,item_size,trim_qty,rate,amount,id FROM wo_non_ord_samp_booking_dtls  WHERE booking_no ='".$data."'  and status_active=1 and	is_deleted=0"; 
	 
	echo  create_list_view("list_view", "Style,Style Des,Sample,Trim Group,UOM,Brand/ Supp. Ref,Gmts Color,Item Color,Gmts Size,Item Size,Trim Qnty,Rate,Amount", "60,100,100,130,100,150,80,80,80,50,60,60","1300","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "style_id,0,sample_type,trim_group,uom,0,gmts_color,fabric_color,gmts_size,0,0,0,0", $arr , "style_id,style_des,sample_type,trim_group,uom,barnd_sup_ref,gmts_color,fabric_color,gmts_size,item_size,trim_qty,rate,amount", "requires/trims_sample_booking_without_order_controller",'','0,0,0,0,0,0,0,0,0,0,2,2,2') ;
}

if($action=="color_from_library")
{
  $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$data  and variable_list=23  and status_active=1 and is_deleted=0");
  echo trim($color_from_library);
  die;
}


if($action=="trim_description_popup")
{
    echo load_html_head_contents("Trim Desc. List","../../../", 1, 1, $unicode);
    extract($_REQUEST);

    ?>
        <script> 
            function js_set_value(data)
            {
                
                var data=data.split('_');
                document.getElementById('description').value=trim(data[0]);
                document.getElementById('req_qty').value=trim(data[1]);
                document.getElementById('uom_id').value=trim(data[2]);
                document.getElementById('prev_qty').value=trim(data[3]);
                parent.emailwindow.hide();
                

            }
        </script> 
        </head>
        <body>
            <div align="center">
                <form>
                    <input type="hidden" id="description" name="description" />
                    <input type="hidden" id="req_qty" name="req_qty" />
                    <input type="hidden" id="uom_id" name="uom_id" />
                    <input type="hidden" id="prev_qty" name="prev_qty" />
                         <?
                    
                    
                        if($cbo_sample_type>0) $sample_cond="and c.sample_name_ra=$cbo_sample_type ";else $sample_cond="";
                    
                         $sql_trim="select a.requisition_number,a.style_ref_no,c.req_qty_ra,c.uom_id_ra,c.sample_name_ra,c.trims_group_ra,c.description_ra from sample_development_fabric_acc c,sample_development_mst a where a.id=c.sample_mst_id and c.form_type=2 and a.id=$txt_style_id and c.trims_group_ra=$cbo_trim_group and a.status_active=1 and c.status_active=1 $sample_cond";
                        $trims_req_result=sql_select($sql_trim);
                        $sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
                    
                         ?>
                        <table  width="620" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
                            <caption> Description dtls</caption>
                            <thead>
                                <tr style="border:1px solid black;">
                                <th width="30">Sl</th>
                                <th width="110">Req No</th>
                                <th width="100">Style Ref.</th>
                                <th width="100">Sample</th>
                                <th width="100">Description</th>
                                <th width="100"> Req.Qty</th>
                                <th width="100"> Previous Qty</th>
                                <th width=""> Uom</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $id_cond="";
                                if(!empty($update_id_details))
                                {
                                    $id_cond=" and id not in ($update_id_details)";
                                }
                                $sql_trim_prv="select trim_qty as  trim_qty,fabric_description,trim_group from wo_non_ord_samp_booking_dtls where style_id=$txt_style_id and trim_group=$cbo_trim_group and status_active=1 and is_deleted=0 $id_cond"; 
                                //echo $sql_trim_prv;
                                $trims_result=sql_select($sql_trim_prv);
                                foreach($trims_result as $row)
                                {
                                    $prev_qty_arr[$row[csf('trim_group')]][$row[csf('fabric_description')]]+=$row[csf('trim_qty')];
                                }
                                $i=1; 
                                foreach($trims_req_result as $row)
                                {
                                    if ($i%2==0)
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";//description_ra,req_qty_ra,uom_id_ra,uom_id_ra
                                    $prev_qty=0;
                                    $prev_qty=$prev_qty_arr[$row[csf('trims_group_ra')]][$row[csf('description_ra')]];
                                        //echo $prev_qty.'ssss';
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $row[csf('requisition_number')];?>" onClick="js_set_value('<? echo $row[csf('description_ra')].'_'.$row[csf('req_qty_ra')].'_'.$row[csf('uom_id_ra')].'_'.$prev_qty;?>')">
                                        <td width="20"><? echo $i;?></td>
                                        <td width="110"><? echo $row[csf('requisition_number')];?></td>
                                        <td width="100"><? echo $row[csf('style_ref_no')];?></td>
                                        <td width="100"><? echo $sample_name_arr[$row[csf('sample_name_ra')]];?></td>
                                        <td width="100"><? echo $row[csf('description_ra')];?></td>
                                        <td width="100" align="right"><? echo $row[csf('req_qty_ra')] ;?></td>
                                        <td width="100" align="right"><? echo  $prev_qty ;?></td>
                                        <td width=""><? echo $unit_of_measurement[$row[csf('uom_id_ra')]];?></td>
                                    </tr>
                                        <?
                                        $i++;
                                }
                                    ?>
                            </tbody>
                        </table>
                    <?
                     exit();
                    ?>
                </form>
            </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
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
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/trims_sample_booking_without_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	
	
	
	?>
    </form>
    </div>
    </body>
    </html>
    <?
}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
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
                                            <? echo $result[csf('plot_no')]; ?> 
                                        	<? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                            <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <? echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
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
                              
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id"> 
               
               </td>       
            </tr>
       </table>
       
                <?
				$season="";
				$buyer_req_no="";
				
				$nameseason=sql_select( "select a.season, b.buyer_req_no  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					
				}
				
				$fabric_source_id='';
                $nameArray=sql_select( "select buyer_id,pay_mode,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source,trims_source from wo_non_ord_samp_booking_mst   where  booking_no=$txt_booking_no"); 
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
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
               			
            </tr>
            <tr>
                
                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td> 
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
                <td width="110">:&nbsp;<? echo $season; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Trims Source</b></td>
                <td  width="110" >:&nbsp;<? echo $fabric_source[$result[csf('trims_source')]]; ?></td>
                
            </tr> 
        </table>  
        <?
		
			}
		?>
            
      <br/>  
      <? 
	 
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$sql= sql_select("select style_id, style_des, sample_type, trim_group, uom, barnd_sup_ref, gmts_color, fabric_color, gmts_size, item_size, trim_qty, rate, amount, fabric_description, id FROM wo_non_ord_samp_booking_dtls WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0"); 
?>
<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <thead>
        <tr>
            <th width="20">Sl</th>
            <th width="60">Style</th>
            <th width="110">Style Des</th>
            <th width="100">Sample</th>
            <th width="140">Trim Group</th>
            <th width="150">Description</th>
            <th width="60">UOM</th>
            <th width="60">Brand/ Supp. Ref</th>
            <th width="80">Gmts Color</th>
            <th width="80">Item Color</th>
            <th width="80">Gmts Size</th>
            <th width="80">Item Size</th>
            <th width="60">Trim Qty</th>
            <th width="60">Rate</th>
            <th>Amount</th>
        </tr>
	</thead>
<?
$total_trim_qty=0;
$total_grey_fabric=0;
$toatl_rate=0;
$total_amount=0;

$i=1;
foreach ($sql as $row)
{
	$all_style_arr[]=$style_library[$row[csf('style_id')]];
	?>
	<tr>
        <td width="20"><? echo $i; ?></td>
        <td width="60"><? echo $style_library[$row[csf('style_id')]]; ?></td>
        <td width="110"><? echo $row[csf('style_des')]; ?></td>
        <td width="100"><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
        <td width="140"><? echo $trim_group[$row[csf('trim_group')]]; ?></td>
        <td width="150"><? echo $row[csf('fabric_description')]; ?></td>
        <td width="60"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
        <td width="60"><? echo $row[csf('barnd_sup_ref')]; ?></td>
        <td width="80"><? echo $color_library[$row[csf('gmts_color')]]; ?></td>
        <td width="80"><? echo $color_library[$row[csf('fabric_color')]]; ?></td>
        <td width="80"><? echo $size_library[$row[csf('gmts_size')]]; ?></td>
        <td width="80"><? echo $row[csf('item_size')]; ?></td>
        <td width="60" align="right"><? echo number_format($row[csf('trim_qty')],4); $total_trim_qty+=$row[csf('trim_qty')]; ?></td>
        <td width="60" align="right"><? echo number_format($row[csf('rate')],4); $toatl_rate+=$row[csf('rate')]; ?></td>
        <td align="right"><? echo number_format($row[csf('amount')],4); $total_amount+=$row[csf('amount')];?></td>
	</tr>
	<?
	$i++;
}
?>
    <tfoot>
        <tr>
            <th width="50" colspan="12" align="right">Total </th>
            <th width="60" align="right"><? echo number_format($total_trim_qty,4); ?></th>
            <th width="60" align="right"><? echo number_format($toatl_rate,4);?></th>
            <th align="right"><? echo number_format($total_amount,4); ?></th>
        </tr>
    </tfoot>
</table>
        <br/>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
               <tr>
                <td>
					<?
                       echo get_spacial_instruction($txt_booking_no);
                    ?>
    			</td>
               </tr>
                </table>
                <!--Not Used-->
                    <table class="rpt_table" style="display:none" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%">Spacial Instruction</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
						$i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} 
					?>
                </tbody>
                </table>
                </td>
                
            </tr>
             <tr>
             <td width="2%">&nbsp; </td>
             </tr>
            <tr>
                 
                <td width="100%">
                  <?
	 $sql_array="select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_non_ord_samp_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0";
	  $data_array=sql_select($sql_array);
	?>
    <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="50%" style="border:1px solid black;">Name</th><th width="27%" style="border:1px solid black;">Approval Date</th><th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
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
      <?
      echo signature_table(115, $cbo_company_name, "1330px", 1);
			echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),$job_no);
		  ?>
       </div>
       
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    
       <?
      
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
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TSN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=5 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TSN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=5 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		
		$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
		$field_array="id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,supplier_id,attention,trims_source,ready_to_approved,inserted_by,insert_date"; 
		 $data_array ="(".$id.",5,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",4,".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_trims_source.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 $rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		 
		
		$booking_mst_id=str_replace("'","",$booking_mst_id);

		$field_array="company_id*buyer_id*item_category*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*supplier_id*attention*trims_source*ready_to_approved*updated_by*update_date"; 
		 $data_array ="".$cbo_company_name."*".$cbo_buyer_name."*4*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_trims_source."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
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




if($action=="sample_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(data)
{
	
	var data=data.split('_');
	document.getElementById('style_id').value=trim(data[0]);
	document.getElementById('style_no').value=trim(data[1]);
	document.getElementById('sample_id').value=trim(data[2]);
    parent.emailwindow.hide();
	

}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="style_id" name="style_id" />
<input type="hidden" id="style_no" name="style_no" />
<input type="hidden" id="sample_id" name="sample_id" />
<?
	
		$sql= "select  a.id,a.style_ref_no,a.requisition_number, a.company_id,a.buyer_name, b.id as bid,b.sample_name,b.sample_color,b.working_factory,b.receive_date_from_factory,b.sent_to_factory_date,b.sent_to_buyer_date,b.approval_status,b.status_date,b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name'  and a.is_deleted=0 and b.is_deleted=0 order by a.id DESC";
	
	
	$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$color_name_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	//print_r($approval_status);
	$arr=array (3=>$sample_name_arr,4=>$color_name_arr,9=>$approval_status);
	
	echo  create_list_view ( "list_view1", "Style Id,Style,Requisition,Sample Name,Sample Color,Working Factory,Buyer Recieve Date,Sent To factory,Submission to Buyer,Approval Status,Status Date", "60,100,100,100,90,100,80,80,80,85,80","1105","300",0, $sql, "js_set_value", "id,style_ref_no,sample_name","", 1, "0,0,0,sample_name,sample_color,0,0,0,0,approval_status,0", $arr , "id,style_ref_no,requisition_number,sample_name,sample_color,working_factory,recieve_date_from_buyer,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date", "../woven_order/requires/trims_sample_booking_without_order_controller", 'setFilterGrid("list_view1",-1);','0,0,0,0,0,0,3,3,3,0,3,3' ) ;	
	 exit();
?>
</form>
</div>
</body>
</html>
<?
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
    $booking_mst_id=str_replace("'","",$booking_mst_id);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}	
		 
		 $id=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
		 
		 $field_array="id,booking_no,booking_mst_id,style_id,style_des,sample_type,trim_group,uom, fabric_description,barnd_sup_ref,gmts_color,fabric_color,gmts_size,item_size,trim_qty,rate,amount,inserted_by,insert_date";
		 
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date"; 
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;	
        if(str_replace("'","",$txt_gmt_color) !="")
        {
            if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
            {
                $gmts_color_id = return_id( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","143");
                $new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
            }
            else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
        }
        else
        {
            $gmts_color_id=0;
        }
        if(str_replace("'","",$txt_color) !="")
        {
            if (!in_array(str_replace("'","",$txt_color),$new_array_color))
            {
                $color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","143");
                $new_array_color[$color_id]=str_replace("'","",$txt_color);
            }
            else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
        }
        else
        {
            $color_id=0;
        }
        if(str_replace("'","",$txt_gmts_size)!="")
        {
            if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_gmts_size))
            {
                $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_library, "lib_size", "id,size_name","143");
                $new_array_gmts_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
            }
            else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_gmts_size);
        }
        else
        {
            $gmts_size_id=0;
        }
		
		$data_array="(".$id.",".$txt_booking_no.",".$booking_mst_id.",".$txt_style.",".$txt_style_des.",".$cbo_sample_type.",".$cbo_trim_group.",".$cbo_uom.",".$txt_description.",".$txt_barnd_sup_ref.",'".$gmts_color_id."','".$color_id."','".$gmts_size_id."',".$txt_size.",".$txt_trim_qty.",".$txt_rate.",".$txt_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
		 
		 $rID=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
		 $rID_in2=1;
		 if ($data_array2!="")
		 {
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
		 }
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_in2){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_in2){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=$cbo_trim_group and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a, inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=$cbo_trim_group and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		
		$field_array_up="style_id*style_des*sample_type*trim_group*uom*fabric_description*barnd_sup_ref*gmts_color*fabric_color*gmts_size*item_size*trim_qty*rate*amount*updated_by*update_date";
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date"; 
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;	
            if(str_replace("'","",$txt_gmt_color) !="")
            {
                if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
                {
                    $gmts_color_id = return_id( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","143");
                    $new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
                }
                else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
            }
            else
            {
                $gmts_color_id=0;
            }

            if(str_replace("'","",$txt_color) !="")
            {
                if (!in_array(str_replace("'","",$txt_color),$new_array_color))
                {
                    $color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","143");
                    $new_array_color[$color_id]=str_replace("'","",$txt_color);
                }
                else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
            }
            else
            {
                $color_id=0;
            }

            if(str_replace("'","",$txt_gmts_size)!="")
            {
                if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_gmts_size))
                {
                    $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_library, "lib_size", "id,size_name","143");
                    $new_array_gmts_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
                }
                else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_gmts_size);
            }
            else
            {
                $gmts_size_id=0;
            }
            
			$data_array_up="".$txt_style."*".$txt_style_des."*".$cbo_sample_type."*".$cbo_trim_group."*".$cbo_uom."*".$txt_description."*".$txt_barnd_sup_ref."*'".$gmts_color_id."'*'".$color_id."'*'".$gmts_size_id."'*".$txt_size."*".$txt_trim_qty."*".$txt_rate."*".$txt_amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array; die; 
		
			
	    $rID=sql_update("wo_non_ord_samp_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		$rID_in2=1;
		if ($data_array2!="")
		 {
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
		 }
		//echo  $rID; die;
	    check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_in2){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_in2){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=$cbo_trim_group and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a, inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=$cbo_trim_group and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		
		$rID=execute_query( "update wo_non_ord_samp_booking_dtls set status_active=0,is_deleted =1 where id =$update_id_details",0);	
		$rID_de2=execute_query( "delete from wo_non_ord_samp_yarn_dtls where wo_non_ord_samp_book_dtls_id =".$update_id_details."",0);
		if($db_type==0)
		{
			if($rID && $rID_de2){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de2){
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


if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
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
        <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                 <th width="100">Booking No</th>
                <th width="200">Date Range</th>
                <th></th>           
            </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                    <? 
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_sample_booking_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                </td>
            <td id="buyer_td">
             <? 
                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
            ?>	</td>
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
              <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
             </td> 
             <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_booking_search_list_view', 'search_div', 'trims_sample_booking_without_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td align="center" colspan="5" valign="middle">
                <?=load_month_buttons(1); ?>
            </td>
        </tr>
     </table>
    <div align="center" id="search_div"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else   $buyer=""; //{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		
	$booking_year_cond=" and to_char(insert_date,'YYYY')=$data[4]";	
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==4 || $data[6]==0)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		}
   if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		}
	$po_array=array();
	$sql_po= sql_select("select booking_no_prefix_num, booking_no,po_break_down_id from wo_non_ord_samp_booking_mst  where $company $buyer $booking_date $booking_cond and booking_type=5  and   status_active=1  and 	is_deleted=0  order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	 $approved=array(0=>"No",1=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$suplier,6=>$approved,7=>$is_ready);
	$sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,item_category,supplier_id,is_approved,ready_to_approved from wo_non_ord_samp_booking_mst  where $company $buyer $booking_date $booking_cond and booking_type=5  and  status_active=1  and 	is_deleted=0 order by booking_no"; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Item Category,Supplier,Approved,Is-Ready", "100,90,100,100,100,60,70","870","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
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
		http.open("POST","trims_sample_booking_without_order_controller.php",true);
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
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
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
	 $sql= "select id,booking_no,booking_date,company_id,buyer_id,item_category,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,trims_source,is_approved,ready_to_approved from wo_non_ord_samp_booking_mst  where booking_no='$data'"; 
	
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_buyer_tag_sample', 'sample_td' );\n";
		//load_drop_down( 'requires/trims_sample_booking_without_order_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' )
        echo "color_from_library('".$row[csf("company_id")]."');\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
        echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_trims_source').value = '".$row[csf("trims_source")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	 }
}

if($action=="populate_details_data_from_for_update")
{
	$data_array=sql_select("select id, trim_group, style_id, style_des, sample_type, uom, fabric_description, barnd_sup_ref, gmts_color, fabric_color, gmts_size, item_size, trim_qty, rate, amount FROM wo_non_ord_samp_booking_dtls WHERE id ='".$data."' and status_active=1 and	is_deleted=0");
	foreach ($data_array as $row)
	{
		$style_id=$row[csf("style_id")];
		$style=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		
		$sql_trim_prv=sql_select("select sum(trim_qty) as  trim_qty from wo_non_ord_samp_booking_dtls where style_id='".$row[csf('style_id')]."' and trim_group='".$row[csf('trim_group')]."' and status_active=1 and is_deleted=0 and id not in ('".$row[csf('id')]."')");
		$sql_trim=sql_select("select sum(c.req_qty_ra) as req_qty_ra from sample_development_fabric_acc c,sample_development_mst a where a.id=c.sample_mst_id and c.form_type=2 and a.id='".$row[csf('style_id')]."' and c.trims_group_ra='".$row[csf('trim_group')]."' and a.status_active=1 and c.status_active=1 and c.sample_name_ra='".$row[csf('sample_type')]."'");
		$cur_bl=0;
		if(count($sql_trim))
		{
			$cur_bl=$sql_trim[0][csf('req_qty_ra')];
		}
		$prev_bl=0;
		if(count($sql_trim_prv))
		{
			$prev_bl=$sql_trim_prv[0][csf('trim_qty')];
		}
		$balance=$cur_bl-$prev_bl;
		echo "document.getElementById('cbo_trim_group').value = '".$row[csf("trim_group")]."';\n";  
		echo "document.getElementById('cbo_uom').value = '".$row[csf("uom")]."';\n"; 
		
		echo "document.getElementById('txt_style').value = '".$row[csf("style_id")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$style."';\n";
		echo "document.getElementById('txt_style_des').value = '".$row[csf("style_des")]."';\n";
		
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type")]."';\n";
		
		echo "document.getElementById('txt_description').value = '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_barnd_sup_ref').value = '".$row[csf("barnd_sup_ref")]."';\n";
		echo "document.getElementById('txt_gmt_color').value = '".$color_library[$row[csf("gmts_color")]]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("fabric_color")]]."';\n";
		echo "document.getElementById('txt_gmts_size').value = '".$size_library[$row[csf("gmts_size")]]."';\n";
		echo "document.getElementById('txt_size').value = '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_trim_req_qty').value = '".$balance."';\n";
		
		echo "document.getElementById('txt_trim_qty').value = '".$row[csf("trim_qty")]."';\n";
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";	
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";	
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";
	
	//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2)
	}
}


?>