<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$bodypat_type_arr=array(1=>"Main Body",2=>"Accessories");
 


$type_wise_bodypat_arr=array(1=>1,2=>1,3=>1,4=>1,5=>1);
if($action=="load_machine")
{
	if($db_type==2)
	{
		$sql="select (id || ':' || machine_no) as machine_name from lib_machine_name where category_id=1 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no";
	}
	else
	{
		$sql="select concat_ws(':',id,machine_no)  as machine_name from lib_machine_name where category_id=1 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no";
	}
	//$sql="select machine_no as machine_name from lib_machine_name where category_id=1  and status_active=1 and is_deleted=0 and is_locked=0 group by machine_no,seq_no order by seq_no ASC";
	echo "[".substr(return_library_autocomplete( $sql, "machine_name" ), 0, -1)."]";
	exit();	
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 140, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
	exit();     	 
} 

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", 1);
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company", 140, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_issue_to_knitting_floor_controller', this.value, 'load_drop_down_floor', 'floor_td' );",1 );
	exit();   
}

if ($action=="load_drop_down_lc_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
	exit();   
}

if($action=="bpdypart_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    //echo 3333;die;
	?>
	<script>

        var selected_id     = new Array(); 
        var selected_name   = new Array(); 
        var bodypart_type   = new Array(); 

    	function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('hidden_selectted_row_id').value; 
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
			//$('#hidden_bodypart_type').val('');
			var tbl_length =$('#tbl_list_search tr').length;
			var select_row=0; var sp=1;
			var select_str=$('#txt_bodypart_type_id' + str).val();
			
         
			if(($('#hidden_bodypart_type').val()*1)!=0 && select_str!=parseInt($('#hidden_bodypart_type').val()))
			{
				alert("Main Body Part and Accessories Body Part Mixed Not Allow.");
              
				return;
			}
			
			for(var i=1; i<=tbl_length; i++)
			{
				var string=$('#txt_bodypart_type_id' + i).val();
				if(select_str==string)
				{
					//alert(select_str+'='+string);
					if(select_row==0)
					{
						select_row=i; sp=1;
					}
					else
					{
						select_row+=','+i; sp=2;
					}
				}
			}

			var exrow = new Array(); var bodypartType='';
			if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
			else countrow=1;
	  
			for(var m=0; m<countrow; m++)
			{
				if(sp==2) exrow[m]=exrow[m]; else exrow[m]=select_row;
				//alert(exrow[m]+'_'+select_str+'_'+$('#txt_bodypart_type_id' + exrow[m]).val())

				if(select_str==2){
				 toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
					 if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {


						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_name.push( $('#txt_individual' + str).val() );
						//bodypartType=parseInt($('#txt_bodypart_type_id' + exrow[m]).val());
						bodypart_type.push( $('#txt_bodypart_type_id' + str).val() );
						break;
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + exrow[m]).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						bodypart_type.splice( i, 1 );
					}
				}else{
					toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
						if( jQuery.inArray( $('#txt_individual_id' + exrow[m]).val(), selected_id ) == -1 ) {

							selected_id.push( $('#txt_individual_id' + exrow[m]).val() );
							selected_name.push( $('#txt_individual' + exrow[m]).val() );
							//bodypartType=parseInt($('#txt_bodypart_type_id' + exrow[m]).val());
								
							bodypart_type.push( $('#txt_bodypart_type_id' + exrow[m]).val() );
							
						}
						else {
							for( var i = 0; i < selected_id.length; i++ ) {
								if( selected_id[i] == $('#txt_individual_id' + exrow[m]).val() ) break;
								}
								selected_id.splice( i, 1 );
								selected_name.splice( i, 1 );
								bodypart_type.splice( i, 1 );
							}
					   }
				   }
         
				var id = ''; var name = ''; var btype="";
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					btype += bodypart_type[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				//btype = btype.substr( 0, btype.length - 1 );
				btype = btype.substr( 0, btype.length - 1 );
			   // alert(name);
				$('#hidden_bodypart_id').val(id);
				$('#hidden_bodypart_name').val(name);
				$('#hidden_bodypart_type').val(btype);
             }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    	<form name="searchwofrm"  id="searchwofrm">
    		<fieldset style="width:480px;">
    		<legend></legend>           
                <table cellpadding="0" cellspacing="0" width="450" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<th width="30">Sl</th>
                        <th width="200">Body Part Name</th>
                        <th>Body Part Type</th>                   
                    </thead>                    
               </table>
               <div style="width:450px; overflow-y:scroll; max-height:280px;"id="buyer_list_view"align="center"> 
               		<table cellspacing="0"cellpadding="0"border="1"rules="all"width="430"class="rpt_table"id="tbl_list_search" >
                        <?
                        $i=1; 
                        $bodyPart_id_arr=explode(",",$txt_bodyPart_id);
                        if($txt_style_no!="" || $txt_job_no!="")
                        {
							if($txt_job_no!="")
							{
								$sql="select a.id, b.body_part_id from sample_development_mst a, sample_development_fabric_acc b, wo_pre_cost_fabric_cost_dtls c where a.id=b.sample_mst_id and a.id=c.sample_id and c.job_no='".$txt_job_no."' and b.knitinggm>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
								$result=sql_select($sql);
							}
							if($txt_style_no!="" && count($result)<1)
							{
                            	$sql="select a.id, b.body_part_id from sample_development_mst a,sample_development_fabric_acc b where a.id=b.sample_mst_id and a.style_ref_no='".$txt_style_no."'  and b.knitinggm>0";
								$result=sql_select($sql);
							}
                             //echo $sql;
                            
                            foreach($result as $row)
                            {
                                $job_bodypart_id[$row[csf('body_part_id')]]=$time_weight_panel[$row[csf('body_part_id')]];
                            }

                            foreach($job_bodypart_id as $id=>$name)
                            {
                                if($id!=14)
                                {
                                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    if(in_array($id,$bodyPart_id_arr)) 
                                    { 
                                        if($selected_row_id=="") $selected_row_id=$i; else $selected_row_id.=",".$i;
                                    }

                                    if(array_key_exists($id,$type_wise_bodypat_arr))
                                    {
                                        $body_part_type_id=1;
                                        $body_part_type_string=$bodypat_type_arr[1];
                                    }
                                    else
                                    {
                                        $body_part_type_id=2;
                                        $body_part_type_string=$bodypat_type_arr[2];
                                    }

                                    ?>
                                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)"> 
                                        <td width="30" align="center"><?=$i; ?>
                                            <input type="hidden"name="txt_individual_id[]"id="txt_individual_id<?=$i ?>"value="<?=$id; ?>"/> 
                                            <input type="hidden"name="txt_individual[]"id="txt_individual<?=$i ?>"value="<?=$name; ?>"/> 
                                            <input type="hidden"name="txt_bodypart_type_id[]"id="txt_bodypart_type_id<?=$i ?>"value="<?=$body_part_type_id; ?>"/>
                                        </td>   
                                        <td width="200" style="word-break:break-all"><?=$name; ?></td>
                                        <td style="word-break:break-all"><?=$body_part_type_string; ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
                        }
                        else
                        {
                           foreach($time_weight_panel as $id=>$name)
                            {
                                
                                if($id!=14)
                                {
                                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    if(in_array($id,$bodyPart_id_arr)) 
                                    { 
                                        if($selected_row_id=="") $selected_row_id=$i; else $selected_row_id.=",".$i;
                                    }

                                    if(array_key_exists($id,$type_wise_bodypat_arr))
                                    {
                                        $body_part_type_id=1;
                                        $body_part_type_string=$bodypat_type_arr[1];
                                    }
                                    else
                                    {
                                        $body_part_type_id=2;
                                        $body_part_type_string=$bodypat_type_arr[2];
                                    }

                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                                        <td width="30" align="center"><?php echo $i; ?>
                                            <input type="hidden"name="txt_individual_id[]"id="txt_individual_id<?php echo $i ?>"value="<? echo $id; ?>"/> 
                                            <input type="hidden"name="txt_individual[]"id="txt_individual<?php echo $i ?>"value="<? echo $name; ?>"/> 
                                            <input type="hidden"name="txt_bodypart_type_id[]"id="txt_bodypart_type_id<?php echo $i ?>"value="<? echo $body_part_type_id; ?>"/>
                                        </td>   
                                        <td width="200"><p><? echo $name; ?></p></td>
                                        <td id="td_type<?php echo $i; ?>"><p><? echo $body_part_type_string; ?></p></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            } 
                        }
                        ?>
                    </table>
                    </div>

                    <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%"> 
                               
                                <div style="width:100%; float:left" align="center">
                                    <input type="hidden"name="hidden_bodypart_id"id="hidden_bodypart_id"value="<?php //echo $txt_bodyPart_id; ?>"/> 
                                    <input type="hidden"name="hidden_bodypart_type"id="hidden_bodypart_type"value="<?php //echo $bodypart_type; ?>"/> 
                                    <input type="hidden"name="hidden_bodypart_name"id="hidden_bodypart_name"value="<?php //echo $party_row_id; ?>"/> 
                                    <input type="hidden"name="hidden_selectted_row_id"id="hidden_selectted_row_id"value="<?php echo $selected_row_id; ?>"/> 
                                    <input type="button"name="close"onClick="parent.emailwindow.hide();"class="formbutton"value="Close"style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div 
                    style="width:100%; 
                            margin-top:5px; 
                            margin-left:10px" 
                    id="search_div" 
                    align="left">
                </div>
    		</fieldset>
    	</form>
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

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$ratio_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $bodypart_ids;die;
	?>
	<script>
	
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			for( var i = 1; i <= tbl_row_count; i++ ) {
				
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		
		
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		 
		}
		
		function fnc_close()
		{	
			//alert($('#hidden_bundle_nos').val());
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    	<form name="searchwofrm"  id="searchwofrm">
    		<fieldset style="width:810px;">
    		<legend></legend>           
                <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<th>Company</th>
                        <th>Buyer</th>
                        <th>Lot Ratio Year</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th class="must_entry_caption">Ratio No</th>
                        <th>Bundle No</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
                            <input type="hidden" name="hidden_lot_no" id="hidden_lot_no" value="<?php echo $lot_ratio; ?>" />
                        </th>
                    </thead>
                    <tr class="general">
                    	<td><?=create_drop_down( "cbo_company_name", 140, "select id, company_name from  lib_company comp where status_active =1 and  is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select --", $company_id,"load_drop_down( 'bundle_issue_to_knitting_floor_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );",0 ); ?></td>
                        <td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
                        <td align="center"><?=create_drop_down( "cbo_lot_year", 60, $year, '', "", '-- Select --', $ryear, "" ); ?></td> 				
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 				
                        <td align="center" id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" /></td> 				
                        <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:120px" class="text_boxes" value="<?php if($ratio_prifix) echo $ratio_prifix*1; ?>" /></td>  		
                        <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>  		
                		<td align="center"><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>'+'_'+'<? echo trim($bodypart_ids,','); ?>'+'_'+'<? echo trim($bodypart_type,','); ?>'+'_'+document.getElementById('cbo_buyer_name').value, 'create_bundle_search_list_view','search_div','bundle_issue_to_knitting_floor_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');" style="width:100px;" />
                         </td>
                    </tr>
               </table>
               <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
    		</fieldset>
    	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	if($("#hidden_lot_no").val()!="")
    	{
    		show_list_view (document.getElementById('txt_order_no').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>'+'_'+'<? echo trim($bodypart_ids,','); ?>'+'_'+'<? echo trim($bodypart_type,','); ?>'+'_'+document.getElementById('cbo_buyer_name').value,'create_bundle_search_list_view','search_div','bundle_issue_to_knitting_floor_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
    	}
    </script>
    </html>
    <?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$txt_order_no 			= "%".trim($ex_data[0])."%";
	$company 				= $ex_data[1];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[3]))."'";
	//echo $selectedBuldle;die;
	$job_no					=$ex_data[4];
	$lot_no					=$ex_data[5];
	$syear 					= substr($ex_data[6],2);
	$full_lot_no			=$ex_data[7];
    $bodypart_ids           =$ex_data[8];
    $bodypart_type          =$ex_data[9];
	$buyerid          =$ex_data[10];
	
	if(trim($ex_data[2]))	$bundle_no = "".trim($ex_data[2])."";
	else					$bundle_no = "%".trim($ex_data[2])."%";
	 
	if( trim($ex_data[1])==0)
    {
        echo "<h2 style='color:#D00; text-align:center;'>Please Select Company First.</h2>";
        exit();
    }
	/*if( trim($ex_data[5])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Lot No</h2>";
		exit();
	}*/
	
	if (trim($job_no) == '' && trim($txt_order_no) == '' && trim($lot_no) == '' && trim($full_lot_no) == '')
    {
        echo "<h2 style='color:#D00; text-align:center;font-size:20px;'>Please enter value of any one search field.</h2>";
        exit();
    }

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyerArr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$cutCon='';
	if ($lot_no != '') $cutCon = " and a.cut_num_prefix_no=".$lot_no."";
	if ($full_lot_no != '') $cutCon = " and a.cutting_no='".$full_lot_no."'";
	
	if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="") $selected_bundle_cond=" and c.bundle_no not in (".$selectedBuldle.")"; else $selected_bundle_cond="";
		
		
	if(str_replace("'","",$buyerid)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyerIdCond=" and f.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyerIdCond="";
		}
		else $buyerIdCond="";
	}
	else $buyerIdCond=" and f.buyer_name=$buyerid";

	$scanned_bundle_arr=return_library_array("SELECT b.bundle_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b, pro_gmts_bundle_bodypart c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.production_type=50 and b.production_type=50 and c.production_type=50 and b.bodypart_type_id in ($bodypart_type) and c.body_part_id in (".$bodypart_ids.") $cutCon_a $buyerIdCond and b.status_active=1 and b.is_deleted=0 group by b.bundle_no ", 'bundle_no', 'bundle_no');
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="50">Buyer</th>
            <th width="90">Order No</th>
            <th width="120">Gmts Item</th>
            <th width="100">Country</th>
            <th width="80">Color</th>
            <th width="50">Size</th>
            <th width="70">Lot Ratio No.</th>
            <th width="80">Bundle No</th>
            <th width="80">QR Code No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:940px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
			$sql="select a.job_no , a.cutting_no , a.cut_num_prefix_no, b.color_id , b.gmt_item_id , c.size_id , c.bundle_no, c.order_id , c.country_id, e.po_number, c.size_qty , c.barcode_no, f.buyer_name from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c, wo_po_break_down e, wo_po_details_master f where a.entry_form=253 and a.company_id=$company and a.id=b.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id and c.bundle_no like '$bundle_no' and c.order_id=e.id and e.po_number like '$txt_order_no' and e.job_no_mst=f.job_no and f.job_no=a.job_no and c.hold=0 $cutCon $jobCon $selected_bundle_cond order by a.job_no, a.cutting_no, length(c.bundle_no) asc, c.bundle_no asc ";
			 //echo $sql;
			$result = sql_select($sql);	
			foreach ($result as $row)
			{  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no')]);	
				?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);"> 
						<td width="30"><?=$i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>"  value="<?=$row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50" align="center"><? echo $year; ?></td>
						<td width="50" align="center"><? echo $job*1; ?></td>
                        <td width="50" style="word-break:break-all"><?=$buyerArr[$row[csf('buyer_name')]]; ?></td>
						<td width="90" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
						<td width="120" style="word-break:break-all"><? echo $garments_item[$row[csf('gmt_item_id')]]; ?></td>
						<td width="100" style="word-break:break-all"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
						<td width="80" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td width="50" style="word-break:break-all"><? echo $size_arr[$row[csf('size_id')]]; ?></td>
						<td width="70" style="word-break:break-all"><? echo $row[csf('cutting_no')]; ?></td>
						<td width="80" style="word-break:break-all"><? echo $row[csf('bundle_no')]; ?></td>
                        <td width="80" style="word-break:break-all"><?=$row[csf('barcode_no')]; ?></td>
						<td align="right"><? echo $row[csf('size_qty')]; ?></td>
					</tr>
				<?
					$i++;
				}
			}
			
        	?>
            <input type="hidden" name="hidden_cutting_no" value="<?=$row[csf('cutting_no')]; ?>" id="hidden_cutting_no"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />
                    Check / Uncheck All
               </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    <?	
	exit();	
}

$new_conn=integration_params(2);
if($action=='populate_data_from_issue')
{
	$data_array=sql_select("SELECT a.sys_number, a.size_set_no, a.company_id , a.location_id , a.production_type , a.production_source, a.serving_company , a.working_location_id, a.working_company_id, a.body_part_type , a.body_part_ids, a.floor_id, a.challan_no , a.remarks , b.cut_no, a.production_source, c.job_no_mst, a.operator_id, a.supervisor_id,a.delivery_date, a.id, a.shift_name from pro_gmts_delivery_mst  a, pro_garments_production_mst  b, wo_po_break_down c where a.id=$data and a.id=b.delivery_mst_id and b.po_break_down_id=c.id and a.production_type=50 and b.production_type=50 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cut_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_size_set_no').value 				= '".$row[csf("size_set_no")]."';\n";
        echo "document.getElementById('txt_job_no').value                   = '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".$row[csf("working_location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value  				= '".($row[csf("location_id")])."';\n";
		echo "document.getElementById('txt_remarks').value  				= '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('txt_shift_name').value  				= '".($row[csf("shift_name")])."';\n";
        echo "document.getElementById('txt_operator_id').value              = '".($row[csf("operator_id")])."';\n"; 
		echo "document.getElementById('hidden_sup_id').value  			= '".($row[csf("supervisor_id")])."';\n";	
		

		$bpdypart_id_arr=explode(",", $row[csf("body_part_ids")]);
        $bodypart_name='';
        foreach ($bpdypart_id_arr as $key => $value) {
            if($bodypart_name!="") $bodypart_name.=",";
            $bodypart_name.=$time_weight_panel[$value];
        }
        echo "document.getElementById('txt_bodyPart_id').value              = '".$row[csf("body_part_ids")]."';\n";
        echo "document.getElementById('txt_bodypart_name').value            = '".$bodypart_name."';\n";
        echo "document.getElementById('cbo_bodypart_type').value            = '".$row[csf("body_part_type")]."';\n";

        $style_ref_no=return_field_value( "style_ref_no","wo_po_details_master"," job_no='".($row[csf("job_no_mst")])."' and status_active=1 and is_deleted=0");
        
        echo "document.getElementById('txt_style_no').value                = '".$style_ref_no."';\n";	
		
		if($new_conn!=""){
			$employee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", "id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);
			$supemployee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", " id_card_no='".($row[csf("supervisor_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);
		}
		else
		{
			$employee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "lib_employee", "id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name","");
			$supemployee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "lib_employee", " id_card_no='".($row[csf("supervisor_id")])."' and status_active=1 and is_deleted=0", "emp_name","");
		}
		 
        echo "document.getElementById('txt_operation_name').value           = '".$employee_name."';\n"; 
       
        echo "document.getElementById('txt_sup_name').value           = '".$supemployee_name."';\n"; 
		exit();
	}
}


if($action=='populate_data_from_yarn_lot')
{
	
	$data_array=sql_select("select id, company_id, source, working_company_id , location_id, cutting_no, floor_id, job_no, size_set_no from ppl_cut_lay_mst where cutting_no='$data'  and entry_form=253 and status_active=1 and is_deleted=0 ");
	
	foreach ($data_array as $row)
	{ 
        echo "document.getElementById('txt_size_set_no').value                = '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".($row[csf("source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

        $location_id=return_field_value(
                                    "location_name as location_name",
                                    "wo_po_details_master",
                                    " job_no='".$row[csf("job_no")]."' and status_active=1 and is_deleted=0  ",
                                    "location_name");  

        echo "document.getElementById('cbo_location').value                   = '".$location_id."';\n";		
		exit();
	}
}

if($action=='populate_data_from_yarn_lot_bundle')
{
	
	$data_array=sql_select("SELECT a.id, a.company_id, a.source, a.working_company_id , a.location_id, a.cutting_no, a.floor_id, job_no, size_set_no from ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and b.barcode_no='$data' and a.entry_form=253 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
        echo "document.getElementById('txt_size_set_no').value              = '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		
		if ($row[csf("source")]== 1) {
			$knitting_com= create_drop_down("cbo_working_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $row[csf("company_id")], "load_location();", 1);
		} else if ($row[csf("source")]== 3) {
			$knitting_com= create_drop_down("cbo_working_company", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
		} else {
			$knitting_com= create_drop_down("cbo_working_company", 140, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
		}
		
		//echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".($row[csf("source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		
		if ($row[csf("source")] == 1)
		{
			$workingloaction=create_drop_down( "cbo_working_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("working_company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
			$workingFloor=create_drop_down( "cbo_floor", 140, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='".$row[csf("location_id")]."' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
			//echo "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='".$row[csf("working_location_id")]."' and production_process in (2) order by floor_name";
			
		}
		else
		{
			$workingloaction= create_drop_down("cbo_working_location", 140, $blank_array, "", 1, "-- Select Location --", 0, "");
			$workingFloor= create_drop_down("cbo_floor", 140, $blank_array, "", 1, "-- Select Floor --", 0, "");
		}
		$lc_location=create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
		
		echo "document.getElementById('location_td').innerHTML = '".$lc_location."';\n";
		echo "document.getElementById('knitting_com').innerHTML = '".$knitting_com."';\n";
		echo "document.getElementById('working_location_td').innerHTML = '".$workingloaction."';\n";
		echo "document.getElementById('floor_td').innerHTML = '".$workingFloor."';\n";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		//echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		//echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
         $location_id=return_field_value(
                                    "location_name as location_name",
                                    "wo_po_details_master",
                                    " job_no='".$row[csf("job_no")]."' and status_active=1 and is_deleted=0  ",
                                    "location_name");  

        echo "document.getElementById('cbo_location').value                   = '".$location_id."';\n";
		
		exit();
	}
}

if($action=="show_dtls_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);
	$body_part_id=$data[3];
    $bodypart_cond="b.body_part_id in (".$body_part_id.")";

	$sql_cut=sql_select("select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
    $size_set_no        =$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$data_array_strip=sql_select("select a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
	
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid	=$data_array_strip[0][csf("id")];
	$colspan			=count($data_array_strip);
	$table_width		=1300+$colspan*100;
	$div_width			=$table_width+20;
	
	$color_size_result=sql_select("select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);
	
	$sqlStripe=sql_select("select id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	//echo "select id, yarn_color_id, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 //print_r($sizeWiseProdQtyArr);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;
	 
	$sqlWetSheet="select b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

	$sqlWetSheetRes=sql_select($sqlWetSheet);
	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach ($sqlWetSheetRes as  $value) {
		if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
	   $knitting_gmm_total+=$value[csf('bodycolor')];
	}
	unset($sqlWetSheetRes);
	//print_r($bodypart_color_qty_arr); die;
	
	
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			//echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	//die;
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	
	//print_r($color_bodypartacc_total_arr); die; 
	
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;
	
	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
            $avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			// $avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
            // echo $sample_color[csf('sample_color')]."=".$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]."/".$colorWiseTotArr[$sample_color[csf('sample_color')]]."<br>";
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}
	
	//$yarnColorWiseLbsQtyArr=array();
	
	
	
	
	
	$sql_wet_sheet="select b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id";
              // echo $sql_wet_sheet;die;
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];
        $color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')]; 	
	}

    foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }



    //	print_r($color_percentage_bodypart);die;
	//echo $sql;die;
	
	?>	
   
        <table cellpadding="0" width="<?=$div_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            
            <thead>
            	<tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="100" rowspan="3">Bundle No</th>
                    <th width="100" rowspan="3">Barcode No</th>
                    <th width="80" rowspan="3" style="color:#2A3FFF">MC No <input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    <th width="120" rowspan="3"> G. Color</th>
                    <th width="50"  rowspan="3">Size</th>
                    <th width="60"  rowspan="3">Bundle Qty.(Pcs)</th>
                    <th width="100"	colspan="2">RMG No.</th>
                    <th width="<?php echo $colspan*100; ?>" colspan="<?php echo $colspan; ?>">Yarn Color Wise Cons Qty. (Lbs)</th>
                    <th width="100"  rowspan="3">Bndl. Cons. Qty.(Lbs)</th>
                    <th width="50"  rowspan="3">Year</th>
                    <th width="60"  rowspan="3">Job No</th>
                    <th width="65"  rowspan="3">Buyer</th>
                    <th width="90"  rowspan="3">Order No</th>
                    <th width="100" rowspan="3">Gmts. Item</th>
                    <th width="100" rowspan="3">Country</th>
                    <th rowspan="3">
                    	<input type="hidden" id="txt_total_color" name="txt_total_color" style="width:80px;" value="<?php echo $colspan; ?>">
                    </th>
                </tr>
                <tr>
                	<th width="50"  rowspan="2">From</th>
                	<th width="50" rowspan="2">To</th>
                    <?php
						
                        foreach($data_array_strip as $scolor)
                        {
                            if($scolor[csf("sample_color_ids")])
                            {
                                ?>
                                <th width="100" >
                                <?php
                                   // echo $scolor[csf("sample_color_ids")];
                                    foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                                       echo $color_library[$sin_sample_color]." ";
                                    }
                                ?> 
                                </th>
                                <?php
                            }
                            else
                            { 
                                ?>
                                <th width="100" ><?php echo $color_library[$scolor[csf("sample_color")]];?> </th>
                                <?php
                            }
                        }
					?>
                    
                </tr>
                <tr>
                	
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" style="word-break:break-all"><?php echo $color_library[$scolor[csf("stripe_color")]];?></th>
							<?php
						}
					?>
                </tr>
            </thead>
        </table>
 	
			
		
		
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
           
        <table cellpadding="0"width="<?php echo $table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all" id="tbl_details">
           <tbody> 
		<?php 

			$i=1;	
			$total_production_qnty=0;
			$grand_color_cons_arr=array();
			$sqlResult =sql_select("SELECT b.* , a.gmt_item_id, c.bundle_qty, c.color_size_break_down_id, c.machine_id, c.bundle_qty, c.id as dtls_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b , pro_garments_production_dtls c where c.delivery_mst_id=".$data[1]." and c.bundle_no=b.bundle_no and c.barcode_no=b.barcode_no and b.mst_id=$mst_id and b.dtls_id=$dtls_id and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            $issue_bundle_arr = array();
            foreach($sqlResult as $row)
            {
                $issue_bundle_arr[$row['BUNDLE_NO']] = $row['BUNDLE_NO'];
            }
            $bundle_cond = where_con_using_array($issue_bundle_arr,1,"bundle_no");
            $receive_bundle_array = return_library_array( "SELECT bundle_no,bundle_no as bundle_nos from pro_garments_production_dtls where status_active=1 and production_type=51 $bundle_cond",'bundle_no','bundle_nos');
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="80" align="center">
                    	<input onBlur="checkMachineId(<? echo $i; ?>)"type="text"id="txt_machine_no_<? echo $i; ?>"name="txt_machine_no[]"style="width:60px;"value="<? echo $machine_library[$selectResult[csf('machine_id')]]; ?>"class="text_boxes">
                            
                       	<input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>">
                        <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>">
						<input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>">
                       	<input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>">
                     	<input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>">
                        
                         <input type="hidden"id="txt_dtls_id_<? echo $i; ?>"name="txt_dtls_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('dtls_id')]; ?>">
                        <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value="<?php echo $selectResult[csf('machine_id')]; ?>">
                       	<input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">                       
                            
                    </td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    		
                    <td width="60" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
                    <td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
                    <?php
						$total_consumption=0;
						foreach($data_array_strip as $scolor)
						{
							?>
							<td width="100" style="word-break:break-all" align="right" id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>" title=""><?php $yarnColorWiseLbsQty=0;
                                if($scolor[csf("sample_color_ids")])
                                { 
                                     /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
                                     $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									echo number_format($yarnColorWiseLbsQty,4,".","");
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
                                }
                                else 
                                {
                                    /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
                                    $grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];

                                    // echo "((".$colorWiseAvgArr[$scolor[csf("sample_color")]]."*".$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]].")/12)*".$selectResult[csf('size_qty')]."<br>";

									echo number_format($yarnColorWiseLbsQty,4,".","");
                                    //.'-'.$colorWiseAvgArr[$scolor[csf("sample_color")]].'-'.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]];
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;

                                    // echo "<br>".$colorWiseAvgArr[$scolor[csf("sample_color")]]."*".$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]]."/12)*".$selectResult[csf('size_qty')]."<br>";
                                }
                                ?></td>
							<?php
						}
                     
						//print_r($grand_color_cons_arr);die;
						$grand_total_consumption+=$total_consumption;//($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
						$total_size_qty+=$selectResult[csf('size_qty')];
					?>
                    <td width="100" align="right" title=""><p><?=number_format($total_consumption,4,".","");; ?></p></td>
                    <td width="50" align="center"><p><?=$year; ?></p></td>
                    <td width="60" align="center"><p><?=$job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?=$jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><?=$country_library[$selectResult[csf('country_id')]]; ?></p></td>
                     <td>
                        <?
                        if($receive_bundle_array[$selectResult[csf('bundle_no')]]=="")
                        {
                            ?>
                         	<input type="button"value="-" name="minusButton[]" id="minusButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<? echo $i;  ?>')"/>
                            <?
                        }
                        else
                        {
                            ?>
                            <input type="button"value="-" name="minusButton[]" id="minusButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="javascript:return alert('This bundle allready receive.Delete not allow! ')"/>
                            <?
                        }
                        ?>
                	</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
                <tr id="bundle_footer">
                    <th   colspan="6" > Total</th>
                    <th width="60"  id="total_bundle_qty"><?php echo $total_size_qty; ?></th>
                    <th width="50"  ></th>       
                    <th width="50"  ></th>
                     <?php
                        $strip_color_arr=array();
                        foreach($data_array_strip as $scolor)
                        {
                            $strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
                            ?>
                            <th width="100" style="word-break:break-all" id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php 
                                //echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                if($scolor[csf("sample_color_ids")])
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
                                }
                                else
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                }
                                ?></th>
                            <?php
                        }
                    ?>
                    <th width="100" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id="">
                        <input
                            type="hidden" 
                            id="color_id_string" 
                            name="color_id_string"
                            value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}


if($action=="show_dtls_listview")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("_",$data);
    $body_part_id=$data[3];
    
	//echo $data[1];die;
	$sql_cut=sql_select("select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
    $size_set_no        =$sql_cut[0][csf("size_set_no")];

	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$data_array_strip=sql_select("SELECT a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
	//echo "select a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ";
  
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid	=$data_array_strip[0][csf("id")];
	$colspan			=count($data_array_strip);
	$table_width		=1300+$colspan*100;
	$div_width			=$table_width+20;
	
	$color_size_result=sql_select("select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);
	
	
	$sqlStripe=sql_select("SELECT id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	//echo "select id, yarn_color_id, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 //print_r($sizeWiseProdQtyArr);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;
	 
	$sqlWetSheet="SELECT b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

	$sqlWetSheetRes=sql_select($sqlWetSheet);
	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach ($sqlWetSheetRes as  $value) {
		if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
	   $knitting_gmm_total+=$value[csf('bodycolor')];
	}
	unset($sqlWetSheetRes);
	//print_r($bodypart_color_qty_arr); die;
	
	
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			//echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	//die;
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				//echo $body_part_row[$sample_color[csf('sample_color')]].'='.$consumtion_without_process_loss_lbs_per_pcs.'='.$knitting_gmm_total.'<br>';
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	
	//print_r($color_bodypartacc_total_arr); die; 
	
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;
	
	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
			$avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			
			//$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}
	
	$bodypart_cond="b.body_part_id in (".$body_part_id.")";
	
	
	$sql_wet_sheet="select b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id";

	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];	
        $color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')]; 	
	}

    foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
    //	print_r($color_percentage_bodypart);die;
	//echo $sql;die;
	
	?>	
   
        <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            
            <thead>
            	<tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="100" rowspan="3">Bundle No</th>
                    <th width="100" rowspan="3">Barcode No</th>
                    <th width="80" rowspan="3" style="color:#2A3FFF">MC No <input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    <th width="120" rowspan="3"> G. Color</th>
                    <th width="50"  rowspan="3">Size</th>
                    <th width="60"  rowspan="3">Bundle Qty.(Pcs)</th>
                    <th width="100"	colspan="2">RMG No.</th>
                    <th width="<?php echo $colspan*100; ?>" colspan="<?php echo $colspan; ?>">Yarn Color Wise Cons Qty. (Lbs)</th>
                    <th width="100"  rowspan="3">Bndl. Cons. Qty.(Lbs)</th>
                    <th width="50"  rowspan="3">Year</th>
                    <th width="60"  rowspan="3">Job No</th>
                    <th width="65"  rowspan="3">Buyer</th>
                    <th width="90"  rowspan="3">Order No</th>
                    <th width="100" rowspan="3">Gmts. Item</th>
                    <th width="100" rowspan="3">Country</th>
                    <th 			rowspan="3">
                    	<input type="hidden"id="txt_total_color"name="txt_total_color"style="width:80px;"value="<?php echo $colspan; ?>">
                    </th>
                </tr>
                <tr>
                	<th width="50"  rowspan="2">From</th>
                	<th width="50" rowspan="2">To</th>
                    <?php
						foreach($data_array_strip as $scolor)
						{
                            if($scolor[csf("sample_color_ids")])
                            {
                                ?>
                                <th width="100" >
                                <?php
                                   // echo $scolor[csf("sample_color_ids")];
                                    foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                                       echo $color_library[$sin_sample_color]." ";
                                    }
                                ?> 
                                </th>
                                <?php
                            }
                            else
                            { 
    							?>
    							<th width="100" ><?php echo $color_library[$scolor[csf("sample_color")]];?> </th>
    							<?php
                            }
						}
					?>
                    
                </tr>
                <tr>
                	
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" style="word-break:break-all"><?php echo $color_library[$scolor[csf("stripe_color")]];?></th>
							<?php
						}
					?>
                </tr>
            </thead>
        </table>
 	
			
		
		
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
           
        <table cellpadding="0"width="<?php echo $table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details"> <tbody>
		<?php  
			$i=1;	
			$total_production_qnty=0;
			$grand_color_cons_arr=array();
			$sqlResult =sql_select("select b.* , a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where b.mst_id=$mst_id and b.dtls_id=$dtls_id and a.id=b.dtls_id and a.color_id=$color_id  and b.barcode_no in (".$data[1].") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
							
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="80" align="center">
                    	<input type="text"onblur="checkMachineId(<? echo $i; ?>)"id="txt_machine_no_<? echo $i; ?>"name="txt_machine_no[]"style="width:60px;"class="text_boxes"> 
                        <input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>"> 
                        <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>"> 
                        <input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>"> 
                        <input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>"> 
                        <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>"> 
                        <input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden"id="txt_colorsize_id_<? echo $i; ?>"name="txt_colorsize_id[]"style="width:80px;"class="text_boxes"value=""> 
                        <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value=""> 
                        <input type="hidden"id="txt_dtls_id_<? echo $i; ?>"name="txt_dtls_id[]"style="width:80px;"class="text_boxes"value=""> 
                        <input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">                       
                    </td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    		
                    <td width="60" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
                    <td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
                    <?php
						$total_consumption=0;
						
						foreach($data_array_strip as $scolor)
						{
							?>
							<td width="100"style="word-break:break-all"align="right"id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>"title=""><?php
							//echo $colorWiseAvgArr[$scolor[csf("sample_color")]].'='.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]].'='.$selectResult[csf('size_qty')].'='.$scolor[csf("sample_color")].'<br>';
                                if($scolor[csf("sample_color_ids")])
                                { 
                                    /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
                                    $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									echo number_format($yarnColorWiseLbsQty,4,".","");
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
                                }
                                else 
                                {
                                   /* $total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
                                    $grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									echo number_format($yarnColorWiseLbsQty,4,".","");//.'-'.$colorWiseAvgArr[$scolor[csf("sample_color")]].'-'.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]];
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
                                }
								//echo $total_consumption.'<br>';
                        ?></td>
							<?php
						}
						//print_r($grand_color_cons_arr);die;
						$grand_total_consumption+=$total_consumption;//($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
						$total_size_qty+=$selectResult[csf('size_qty')];
					?>
                    <td width="100" align="right"><?php echo number_format($total_consumption,4,".","");?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td>
                     	<input type="button"value="-"name="minusButton[]"id="minusButton_<? echo $i;  ?>"style="width:30px"class="formbuttonplasminus"onClick="fnc_minusRow('<? echo $i;  ?>')"/>
                	</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th   colspan="6" > Total</th>
                    <th width="60"  id="total_bundle_qty"><?php echo $total_size_qty; ?></th>
                    <th width="50"  ></th>       
                    <th width="50"  ></th>
                     <?php
					 	$strip_color_arr=array();
						foreach($data_array_strip as $scolor)
						{
							$strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
							?>
							<th 
                            	width="100" 
                                style="word-break:break-all"
                                id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php 
                                if($scolor[csf("sample_color_ids")])
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
                                }
                                else
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                }
                               
                                ?></th>
							<?php
						}
					?>
                    <th width="100" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id="">
                    	<input type="hidden"id="color_id_string"name="color_id_string"value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}


if($action=="show_rest_of_bundle")
{

    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
    $country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
    $data=explode("__",$data);
    $style=$data[0];
    $body_part_ids=$data[1];
    $emp_code=$data[2];
    

    $sql_cut=sql_select("SELECT a.job_no,a.cutting_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,wo_po_details_master c where c.style_ref_no='$style' and c.job_no=a.job_no and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
    $job_no             =$sql_cut[0][csf("job_no")];
    foreach ($sql_cut as $val) 
    {
        $color_id_arr[$val[csf("color_id")]] =$val[csf("color_id")];
        $consumption_string_arr[$val[csf("roll_data")]] =$val[csf("roll_data")];
        $mst_id_arr[$val[csf("id")]] =$val[csf("id")];
        $dtls_id_arr[$val[csf("dtls_id")]]=$val[csf("dtls_id")];
        $size_set_no_arr[$val[csf("size_set_no")]] =$val[csf("size_set_no")];
        $cutting_no_arr[$val[csf("cutting_no")]] =$val[csf("cutting_no")];
    }
    $color_id = implode(",", $color_id_arr);
    $consumption_string = implode(",", $consumption_string_arr);
    $mst_id = implode(",", $mst_id_arr);
    $dtls_id = implode(",", $dtls_id_arr);
    $size_set_no = "'".implode("','", $size_set_no_arr)."'";
    $cutting_nos = "'".implode("','", $cutting_no_arr)."'";

    list($shortName,$year,$job_prifix)=explode('-',$job_no);
    //echo $consumption_string;die;
    $consumption_data_arr=explode("**",$consumption_string);
    $color_wise_cons_arr=array();
    foreach($consumption_data_arr as $single_color_consumption)
    {
        $single_color_cons_arr=explode("=",$single_color_consumption);
        $color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
    }
    

    $data_array_strip=sql_select("SELECT a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no in($size_set_no) and b.id=a.mst_id and a.color_id in($color_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
  
    foreach ($data_array_strip as $val) 
    {       
        $sample_reference_arr[$val[csf("sample_ref")]]   =$val[csf("sample_ref")];
        $size_set_mstid_arr[$val[csf("id")]] =$val[csf("id")];
    }

    $sample_reference = "'".implode("','", $sample_reference_arr)."'";
    $size_set_mstid = implode(",", $size_set_mstid_arr);

    $colspan            =count($data_array_strip);
    $table_width        =320+$colspan*80;
    $div_width          =$table_width+20;
    
    $color_size_result=sql_select("SELECT gmt_size_id, production_weight from ppl_size_set_dtls where mst_id in($size_set_mstid) and color_id in($color_id) and status_active=1 and is_deleted=0 order by id");
    $sizeWiseProdQtyArr=array();
    foreach($color_size_result as $row)
    {
        if($row[csf('gmt_size_id')]!=0) 
        {
            $sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
        }
    }
    unset($color_size_result);
    
    
    $sqlStripe=sql_select("SELECT id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id in($size_set_mstid) and color_id in($color_id) and status_active=1 and is_deleted=0 order by id"); 
    //echo "select id, yarn_color_id, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
    $yarnColorArr=array(); $consumtion_without_process_loss=0;
     foreach ($sqlStripe as $row)
     {
         if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
         $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
         $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
         $consumtion_without_process_loss+=$row[csf('consumption')];
     }
     unset($sqlStripe);
     //print_r($sizeWiseProdQtyArr);
     $sizeSummArr=array();
     foreach($yarnColorArr as $ycolor=>$ycolorVal)
     {
        foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
        {
            $colorSizeQty=0;
            $colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
            //echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
            $sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
        }
     }
    // print_r($sizeSummArr); die;
     
    $sqlWetSheet="SELECT b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number in($sample_reference) and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

    $sqlWetSheetRes=sql_select($sqlWetSheet);
    $bodypart_color_qty_arr=array();
    $knitting_gmm_total=0;
    foreach ($sqlWetSheetRes as  $value) {
        if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
       $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
       $knitting_gmm_total+=$value[csf('bodycolor')];
    }
    unset($sqlWetSheetRes);
    //print_r($bodypart_color_qty_arr); die;
    
    
    $bodypart_color_total_arr=array();
    $color_bodypartmain_total_arr=array();
    $consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
    foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
    { 
        foreach ($data_array_strip as $sample_color)
        {
            //echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
            if($sample_color[csf('sample_color_ids')])
            {
                foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                    $body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                    $bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                    $color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                }
            }
            else
            {
                $bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                $color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
            }
        }
    }
    //die;
    
    $bodypart_color_total_arr=array();
    $color_bodypartacc_total_arr=array();
    $bodypart_main_total=0;
    foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
    { 
        foreach ($data_array_strip as  $sample_color)
        {
            if($sample_color[csf('sample_color_ids')])
            {
                foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
                    $body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                    $bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                    $color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                    $bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                }
            }
            else
            {
                $bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                //echo $body_part_row[$sample_color[csf('sample_color')]].'='.$consumtion_without_process_loss_lbs_per_pcs.'='.$knitting_gmm_total.'<br>';
                $color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
                $bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
            }
        }
    }
    
    //print_r($color_bodypartacc_total_arr); die; 
    
    $colorWiseTotArr=array();
    foreach ($data_array_strip as  $sample_color)
    {
        if($sample_color[csf('sample_color_ids')])
        {
            $colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
        }
        else
        {
            $colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
        }
    }
    //print_r($colorWiseTotArr); die;
    
    $colorWiseAvgArr=array();
    foreach ($data_array_strip as  $sample_color)
    {
        $avgQty=0;
        if($sample_color[csf('sample_color_ids')])
        {
            $avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
            $colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
        }
        else
        {
            $avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
            
            //$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
            $colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
        }
    }
    
    $bodypart_cond="b.body_part_id in (".$body_part_ids.")";
    
    
    $sql_wet_sheet="SELECT b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number in($sample_reference) and b.bodycolor>0 group by b.color_id";

    $wet_sheet_result=sql_select($sql_wet_sheet);
    $color_percentage_bodypart=array();
    foreach($wet_sheet_result as $wet_row)
    {
        $color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')];  
    }

    foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
    //  print_r($color_percentage_bodypart);die;
    //echo $sql;die;
    
    ?>  
   
        <table cellpadding="0" width="<?php echo $table_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            
            <thead>
                <tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="100" rowspan="3">Bundle No</th>
                    <th width="80" rowspan="3">MC No</th>
                    <th width="50"  rowspan="3">Size</th>
                    <th width="60"  rowspan="3">Bundle Qty</th>
                    <th width="<?php echo $colspan*50; ?>" colspan="<?php echo $colspan; ?>">Operator Wise Yarn In Hand</th>
                </tr>
                <tr>
                    <?php
                        foreach($data_array_strip as $scolor)
                        {
                            if($scolor[csf("sample_color_ids")])
                            {
                                ?>
                                <th width="80" >
                                <?php
                                   // echo $scolor[csf("sample_color_ids")];
                                    foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                                       echo $color_library[$sin_sample_color]." ";
                                    }
                                ?> 
                                </th>
                                <?php
                            }
                            else
                            { 
                                ?>
                                <th width="80" ><?php echo $color_library[$scolor[csf("sample_color")]];?> </th>
                                <?php
                            }
                        }
                    ?>
                    
                </tr>
                <tr>
                    
                    <?php
                        foreach($data_array_strip as $scolor)
                        {
                            ?>
                            <th width="80" style="word-break:break-all"><?php echo $color_library[$scolor[csf("stripe_color")]];?></th>
                            <?php
                        }
                    ?>
                </tr>
            </thead>
        </table>
    
            
        
        
    <div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
           
        <table cellpadding="0" width="<?php echo $table_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_detail"> <tbody>
        <?php  


            $sql = "SELECT production_type,bundle_no from pro_garments_production_dtls where status_active=1 and cut_no in($cutting_nos) and production_type in(50,51) and operator_id='$emp_code'";
            // echo $sql;
            $res = sql_select($sql);
            foreach ($res as $val) 
            {
                $bundle_arr[$val['PRODUCTION_TYPE']][$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
            }
            $issue_bundle = "'".implode("','", $bundle_arr[50])."'";

            $i=1;   
            $total_production_qnty=0;
            $grand_color_cons_arr=array();
            $sqlResult =sql_select("SELECT b.* , a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where b.mst_id in($mst_id) and b.dtls_id in($dtls_id) and a.id=b.dtls_id and a.color_id in($color_id) and b.bundle_no in($issue_bundle)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            // echo "SELECT b.* , a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where b.mst_id in($mst_id) and b.dtls_id in($dtls_id) and a.id=b.dtls_id and a.color_id in($color_id) and b.bundle_no in($issue_bundle)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

            
                            
            foreach($sqlResult as $selectResult)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                if($bundle_arr[51][$selectResult[csf('bundle_no')]]=="")// do not receve yet
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                        <td width="80" align="center"> </td>
                        <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                                
                        <td width="60" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
                        <?php
                            $total_consumption=0;
                            
                            foreach($data_array_strip as $scolor)
                            {
                                ?>
                                <td width="80"style="word-break:break-all"align="right"id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>"title=""><?php
                                //echo $colorWiseAvgArr[$scolor[csf("sample_color")]].'='.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]].'='.$selectResult[csf('size_qty')].'='.$scolor[csf("sample_color")].'<br>';
                                    if($scolor[csf("sample_color_ids")])
                                    { 
                                        /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
                                        $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
                                        echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12,4,".","");*/
                                        $yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
                                        echo number_format($yarnColorWiseLbsQty,4,".","");
                                        $total_consumption+=$yarnColorWiseLbsQty;
                                        $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
                                    }
                                    else 
                                    {
                                       /* $total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
                                        $grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
                                        echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");*/
                                        $yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
                                        echo number_format($yarnColorWiseLbsQty,4,".","");//.'-'.$colorWiseAvgArr[$scolor[csf("sample_color")]].'-'.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]];
                                        $total_consumption+=$yarnColorWiseLbsQty;
                                        $grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
                                    }
                                    //echo $total_consumption.'<br>';
                            ?></td>
                                <?php
                            }
                            //print_r($grand_color_cons_arr);die;
                            $grand_total_consumption+=$total_consumption;//($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
                            $total_size_qty+=$selectResult[csf('size_qty')];
                        ?>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
            </tbody>
            <!-- <tfoot>
                <tr id="bundle_footer">
                    <th   colspan="5" > Total</th>
                     <?php
                        $strip_color_arr=array();
                        foreach($data_array_strip as $scolor)
                        {
                            $strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
                            ?>
                            <th 
                                width="80" 
                                style="word-break:break-all"
                                id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php 
                                if($scolor[csf("sample_color_ids")])
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
                                }
                                else
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                }
                               
                                ?></th>
                            <?php
                        }
                    ?>
                </tr>
            </tfoot> -->
        </table>
    </div>
    <?
    exit();
}

if($action=="populate_bundle_data")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
    $body_part_id=$data[3];
    $bodypart_cond="b.body_part_id in (".$body_part_id.")";
	$i=$data[1]+1;
	//echo $i;die;
	$sql_cut=sql_select("select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[4]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
    $size_set_no        =$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $job_no;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$data_array_strip=sql_select("select a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");

	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid	=$data_array_strip[0][csf("id")];
	$colspan=count($data_array_strip);
	$table_width	=1300+$colspan*100;
	$div_width		=$table_width+20;
	
	$color_size_result=sql_select("select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);

	
	$sql_wet_sheet="select b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id";
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];	
        $color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')]; 	
	}

    foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
	
	$sqlStripe=sql_select("select id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	//echo "select id, yarn_color_id, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 //print_r($sizeWiseProdQtyArr);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;
	
	$sqlWetSheet="select b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

	$sqlWetSheetRes=sql_select($sqlWetSheet);
	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach ($sqlWetSheetRes as  $value) {
		if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
	   $knitting_gmm_total+=$value[csf('bodycolor')];
	}
	unset($sqlWetSheetRes);
	//print_r($bodypart_color_qty_arr); die;
	
	
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			//echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	//die;
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	
	//print_r($color_bodypartacc_total_arr); die; 
	
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;
	
	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
			$avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			//$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}


	$total_production_qnty=0;
	$sqlResult =sql_select("select b.* , a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where b.mst_id=$mst_id and b.dtls_id=$dtls_id and a.id=b.dtls_id and a.color_id=$color_id  and b.barcode_no in (".$data[0].") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$k=1;						
	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('size_qty ')]; 	
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="100" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
			<td width="80" align="center">
				<input type="text"onblur="checkMachineId(<? echo $i; ?>)"id="txt_machine_no_<? echo $i; ?>"name="txt_machine_no[]"style="width:60px;"class="text_boxes">
                <input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>">
                <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>">
                <input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>">
                <input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
                <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>">
                <input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>">
                <input type="hidden"id="txt_colorsize_id_<? echo $i; ?>"name="txt_colorsize_id[]"style="width:80px;"class="text_boxes"value="">
                <input type="hidden"id="txt_dtls_id_<? echo $i; ?>"name="txt_dtls_id[]"style="width:80px;"class="text_boxes"value="">
                <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value="">
                <input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">                         
					
			</td>
			<td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
			<td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
					
			<td width="60" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
			<td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
			<td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
  
			<?php
				$total_consumption=0;
				foreach($data_array_strip as $scolor)
				{
                    
					?>
					<td width="100" style="word-break:break-all" align="right" id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>" title=""><?php 
                        if($scolor[csf("sample_color_ids")])
                        { 
                            /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
                            $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
                            echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12,4,".","");*/
							$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
							echo number_format($yarnColorWiseLbsQty,4,".","");
							$total_consumption+=$yarnColorWiseLbsQty;
							$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
                        }
                        else 
                        {
                            /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
                            $grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
                            echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");*/
							$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
							echo number_format($yarnColorWiseLbsQty,4,".","");//.'-'.$colorWiseAvgArr[$scolor[csf("sample_color")]].'-'.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]];
							$total_consumption+=$yarnColorWiseLbsQty;
							$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
                        }
                        ?> 
                    </td>
					<?php
				}
				//print_r($grand_color_cons_arr);die;
				$grand_total_consumption+=$total_consumption;//($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
				$total_size_qty+=$selectResult[csf('size_qty')];
			?>
			<td width="100" align="right"><?php echo number_format($total_consumption,4,".",""); ?></td>
			<td width="50" align="center"><p><? echo $year; ?></p></td>
			<td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
			<td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
			<td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
					
			<td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
			<td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
			 <td>
				<input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>')"/>
			</td>
		</tr>
	<?php
		$i++;
        $k++;
	}

	exit();
}

if($action=="show_dtls_yarn_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$data=explode("_",$data);
	//echo "select a.color_id as color_id, a.lot as lot from ppl_cut_lay_prod_dtls a, ppl_cut_lay_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  andb.status_active=1 and b.is_deleted=0 and b.cutting_no='$data[0]'";
	$lotArr=return_library_array( "select a.color_id as color_id, a.lot as lot from ppl_cut_lay_prod_dtls a, ppl_cut_lay_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.cutting_no='$data[0]'", "color_id", "lot");
	//print_r($lotArr);
	$data_array_strip=sql_select("SELECT id, gmts_color, yarn_color, sample_color, sample_color_ids, required_qty, returanable_qty, issue_qtygm, issue_qty, short_excess_qty, issue_balance_qty from pro_gmts_knitting_issue_dtls where delivery_mst_id=$data[1] and production_type=50 and status_active=1 and is_deleted=0 order by id ");
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan			=count($data_array_strip);
	?>	
    <table cellpadding="0"width="950"cellspacing="0"border="1"class="rpt_table"rules="all">
        
        <thead>
            <th width="30">SL</th>
            <th width="70">Sample Color</th>
            <th width="180">Yarn Color</th>
            <th width="100">Lot No</th>
            <th width="80">Required Qty(Lbs)</th>
            <th width="80">Required Qty(GM)</th>
            <th width="80">Returnable Qty (Lbs)</th>
            <th width="80">Issue Qty. (GM)</th>
            <th width="80">Issue Qty. (Lbs)</th>
            <th width="80">Current Short/Excess</th>
            <th>Issue Balance</th>
        </thead>
    </table>
		
	<div style="width:970px;max-height:250px;overflow-y:scroll" align="left"> 
        <table cellpadding="0"width="950"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_yarn_details"> <tbody>
		<?php  
			$i=1;	
			foreach($data_array_strip as $selectResult)
			{
                $returanable_qty    =($selectResult[csf("issue_qty")] - $selectResult[csf("required_qty")]);
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i; ?>" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="70" align="center"><p><?
                        foreach (explode(",",$selectResult[csf('sample_color_ids')]) as  $sin_smaple_color) {
                           echo $color_library[$sin_smaple_color]." ";
                        }
                     ?></p></td>
                    <td width="180" align="center" style="word-break:break-all"><?=$color_library[$selectResult[csf('yarn_color')]]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$lotArr[$selectResult[csf('yarn_color')]]; ?></td>
                    <td width="80" align="right" id="required_qty_<?=$i; ?>"><?=number_format($selectResult[csf('required_qty')],4,".",""); ?></td>
                    <td width="80" align="right" id="reqqtygm_<?=$i; ?>"><?=number_format(($selectResult[csf('required_qty')]*453.59237),4,".",""); ?></td>
                    <td width="80" align="right"><p><?php echo number_format($returanable_qty,4,".",""); ?></p></td>
                    <!-- $returanable_qty = $selectResult[csf('returanable_qty')]/454; -->
                    <td width="80" align="center"><input onKeyUp="fnc_total_issue_balance(<? echo $i; ?>);" type="text" id="txtIssueQtyGm_<? echo $i; ?>" name="txtIssueQtyGm[]"style="width:70px;" value="<?php echo number_format($selectResult[csf('issue_qtygm')],4,".",""); ?>" class="text_boxes_numeric"></td>
                    <td width="80" align="center">
                    	<input type="text" id="txt_issue_qty_<? echo $i; ?>" name="txt_issue_qty[]" style="width:70px;" value="<?php echo number_format($selectResult[csf('issue_qty')],4,".",""); ?>" class="text_boxes_numeric" readonly>
                        <input type="hidden"id="hidden_yarn_color_<? echo $i; ?>"name="hidden_yarn_color[]"value="<?php echo $selectResult[csf('yarn_color')]; ?>">
                        <input type="hidden"id="hidden_sample_color_<? echo $i; ?>"name="hidden_sample_color[]"value="<?php echo $selectResult[csf('sample_color_ids')]; ?>">
                        <input type="hidden"id="hidden_yarn_dtls_id_<? echo $i; ?>"name="hidden_yarn_dtls_id[]"value="<?php echo $selectResult[csf('id')]; ?>">
                    </td>
                    <td width="80" align="right"><p><?php echo number_format($selectResult[csf('short_excess_qty')],4,".",""); ?></p></td>
                    <td align="right"><?php echo number_format($selectResult[csf('issue_balance_qty')],4,".","");?></td>
                </tr>
            <?php

            	$total_required_qty 		+=$selectResult[csf('required_qty')];
				$total_required_gm 			+=($selectResult[csf('required_qty')]*453.59237);
                // $total_returanable_qty      +=$selectResult[csf('returanable_qty')];
            	$total_returanable_qty 		+=$returanable_qty;
				$total_issue_qtygm 			+=$selectResult[csf('issue_qtygm')];
            	$total_issue_qty 			+=$selectResult[csf('issue_qty')];
            	$total_short_excess_qty 	+=$selectResult[csf('short_excess_qty')];
            	$total_issue_balance_qty 	+=$selectResult[csf('issue_balance_qty')];
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th colspan="4" > Total</th>
                    <th id="total_required_qty"><?php echo number_format($total_required_qty,4,".","");?></th>
                    <th id="total_required_gm"><?php echo number_format($total_required_gm,4,".","");?></th>
                    <th id="total_returnable_qty"><?php echo number_format($total_returanable_qty,4,".","");?></th>
                    <th id="total_issue_qtygm"><?php echo number_format($total_issue_qtygm,4,".","");?></th>       
                    <th id="total_issue_qty"><?php echo number_format($total_issue_qty,4,".","");?></th>
                    <th id="total_short_excess_qty"><?php echo number_format($total_short_excess_qty,4,".","");?></th>
                    <th id="total_balance_qty"><?php echo number_format($total_issue_balance_qty,4,".","");?></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_dtls_yarn_listview")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$data=explode("_",$data);
	$operator_id=$data[2];
	$sql_cut=sql_select("select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
    $size_set_no        =$sql_cut[0][csf("size_set_no")];
	
	$lotArr=return_library_array( "select color_name, lot from ppl_cut_lay_prod_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0", "color_name", "lot");

	$data_array_strip=sql_select("select a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
	//print_r($data_array_strip);die;
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan			=count($data_array_strip);

	$sql_returnable=sql_select(" select yarn_color, lot_ratio_no, required_qty, (required_qty-issue_qty) as returnable_qty from pro_gmts_knitting_issue_dtls where operator_id=$operator_id and lot_ratio_no='".$data[0]."' and production_type=50 and status_active=1 and is_deleted=0");

    $returnable_qty_arr=array();
	foreach ($sql_returnable as $value) {
		$returnable_qty_arr[$value[csf('yarn_color')]]['reqQty']+=$value[csf('required_qty')];
       $returnable_qty_arr[$value[csf('yarn_color')]]['retQty']+=$value[csf('returnable_qty')];
	   $returnable_qty_arr[$value[csf('yarn_color')]]['lot']=$value[csf('lot_ratio_no')];
    }
	?>	
    <table cellpadding="0"width="950"cellspacing="0"border="1"class="rpt_table"rules="all">
        
        <thead>
            <th width="30">SL</th>
            <th width="70">Sample Color</th>
            <th width="180">Yarn Color</th>
            <th width="100">Lot No</th>
            <th width="80">Required Qty(Lbs)</th>
            <th width="80">Required Qty(GM)</th>
            <th width="80">Returnable Qty (Lbs)</th>
            <th width="80">Issue Qty. (GM)</th>
            <th width="80">Issue Qty.(Lbs)</th>
            <th width="80">Current Short/Excess</th>
            <th>Issue Balance</th>           
        </thead>
    </table>
	<div style="width:970px;max-height:250px;overflow-y:scroll" align="left"> 
        <table cellpadding="0"width="950"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_yarn_details"> <tbody>
		<?php  
			$i=1;	
			foreach($data_array_strip as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<?=$i; ?>" > 
                    <td width="30" align="center"><?=$i; ?></td>
                    <td width="70" align="center"><p><?
                        if($selectResult[csf("sample_color_ids")])
                        {
                            foreach (explode(",", $selectResult[csf("sample_color_ids")]) as $sin_sample_color) {
                                echo $color_library[$sin_sample_color]." ";
                            }
                        }
                        else
                        {
                            echo $color_library[$selectResult[csf('sample_color')]];
                        }
                        ?> 
                        </p></td>
                    <td width="180" align="center" style="word-break:break-all"><?php echo $color_library[$selectResult[csf('stripe_color')]]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?php echo $lotArr[$color_library[$selectResult[csf('stripe_color')]]]; ?></td>
                    <td width="80" align="right" id="required_qty_<?=$i; ?>"></td>
                    <td width="80" align="right" id="reqqtygm_<?=$i; ?>"></td>
                    <td width="80" align="right"><p><?php echo number_format($returnable_qty_arr[$selectResult[csf('stripe_color')]]['retQty'],4,".",""); ?></p></td>
                    <td width="80" align="center"><input onKeyUp="fnc_total_issue_balance(<? echo $i; ?>) "type="text" id="txtIssueQtyGm_<? echo $i; ?>" name="txtIssueQtyGm[]" style="width:70px;" value="" class="text_boxes_numeric"></td>
                    <td width="80" align="center">
                    	<input type="text" id="txt_issue_qty_<?=$i; ?>" name="txt_issue_qty[]" style="width:70px;" class="text_boxes_numeric" readonly>
                        <input type="hidden"id="hidden_yarn_color_<?=$i; ?>"name="hidden_yarn_color[]"value="<?php echo $selectResult[csf('stripe_color')]; ?>">
                        <input type="hidden"id="hidden_sample_color_<?=$i; ?>"name="hidden_sample_color[]"value="<?php if($selectResult[csf("sample_color_ids")]) echo $selectResult[csf('sample_color_ids')]; else  echo $selectResult[csf('sample_color')]; ?>">
                        <input type="hidden"id="hidden_yarn_dtls_id_<?=$i; ?>"name="hidden_yarn_dtls_id[]"value="">

                    </td>
                    <td width="80" align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th colspan="4"> Total</th>
                    <th id="total_required_qty"></th>
                    <th id="total_required_gm"></th>
                    <th id="total_returnable_qty"></th>
                    <th id="total_issue_qtygm"></th>        
                    <th id="total_issue_qty"></th>
                    <th id="total_short_excess_qty"></th>
                    <th id="total_balance_qty"></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_dtls_listview_bundle")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("_", $data);
	//echo $data[1];die;
	$sql_cut=sql_select(" select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where c.barcode_no='".$data[0]."' and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
    $size_set_no        =$sql_cut[0][csf("size_set_no")];

	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select(" SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	
	$data_array_strip=sql_select("SELECT a.sample_color_id as sample_color, a.sample_color_ids as sample_color_ids, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, b.sample_ref, b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by sample_color_id ");
								
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid	=$data_array_strip[0][csf("id")];
	$colspan=count($data_array_strip);
	$table_width	=1300+$colspan*100;
	$div_width		=$table_width+20;
	
	
	$color_size_result=sql_select("SELECT gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);
	
	$sqlStripe=sql_select("SELECT id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	//echo "select id, yarn_color_id, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id";
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 //print_r($sizeWiseProdQtyArr);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;
	 
	$sqlWetSheet="select b.color_id, sum(b.bodycolor) as  bodycolor, b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id, b.body_part_id order by b.body_part_id";

	$sqlWetSheetRes=sql_select($sqlWetSheet);
	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach ($sqlWetSheetRes as  $value) {
		if($value[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   $bodypart_color_qty_arr[$body_type][$value[csf('body_part_id')]][$value[csf('color_id')]]+=$value[csf('bodycolor')];
	   $knitting_gmm_total+=$value[csf('bodycolor')];
	}
	unset($sqlWetSheetRes);
	//print_r($bodypart_color_qty_arr); die;
	
	
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			//echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	//die;
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	
	//print_r($color_bodypartacc_total_arr); die; 
	
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;
	
	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
			$avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
            // echo $sample_color[csf('sample_color')]."=".$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]."/".$colorWiseTotArr[$sample_color[csf('sample_color')]]."<br>";
			
			//$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}
	
	if($data[1]==1)	$bodypart_cond="b.body_part_id in (1,2,3,4,5)"; else $bodypart_cond="b.body_part_id not in (1,2,3,4,5)";
	
	$sql_wet_sheet=" select b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id";
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];
        $color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')];
	}

    foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
    ?>	
        <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            
            <thead>
            	<tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="100" rowspan="3">Bundle No</th>
                    <th width="100" rowspan="3">Barcode No</th>
                    <th width="80" rowspan="3" style="color:#2A3FFF">MC No<input type="checkbox" id="all_check" onClick="check_all('all_check')"></th>
                    <th width="120" rowspan="3"> G. Color</th>
                    <th width="50"  rowspan="3">Size</th>
                    <th width="60"  rowspan="3">Bundle Qty.(Pcs)</th>
                    <th width="100"	colspan="2">RMG No.</th>
                    <th width="<?=$colspan*100; ?>" colspan="<?=$colspan; ?>">Yarn Color Wise Cons Qty. (Lbs)</th>
                    <th width="100"  rowspan="3">Bndl. Cons. Qty.(Lbs)</th>
                    <th width="50"  rowspan="3">Year</th>
                    <th width="60"  rowspan="3">Job No</th>
                    <th width="65"  rowspan="3">Buyer</th>
                    <th width="90"  rowspan="3">Order No</th>
                    <th width="100" rowspan="3">Gmts. Item</th>
                    <th width="100" rowspan="3">Country</th>
                    <th 			rowspan="3"></th>
                </tr>
                <tr>
                	<th width="50"  rowspan="2">From</th>
                	<th width="50" rowspan="2">To</th>
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" ><?php echo $color_library[$scolor[csf("sample_color")]];?> </th>
							<?php
						}
					?>
                    
                </tr>
                <tr>
                	
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" style="word-break:break-all"><?php echo $color_library[$scolor[csf("stripe_color")]];?></th>
							<?php
						}
					?>
                </tr>
            </thead>
        </table>		
		
	<div 
        style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" 
        align="left"> 
           
        <table cellpadding="0"width="<?php echo $table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details">
            <tbody>
		<?php  
			$i=1;	
			$total_production_qnty=0;
			$sqlResult =sql_select("select b.* , a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where b.mst_id=$mst_id and b.dtls_id=$dtls_id and a.id=b.dtls_id and a.color_id=$color_id  and b.barcode_no in (".$data[0].") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
						
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('size_qty ')]; 	
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="80" align="center">
                    	<input type="text"onblur="checkMachineId(<? echo $i; ?>)"id="txt_machine_no_<? echo $i; ?>"name="txt_machine_no[]"style="width:60px;"class="text_boxes"> 
                        <input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>"> 
                        <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>">
                        <input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>"> 
                        <input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>"> 
                        <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>"> 
                        <input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value=""> 
                        <input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">                        
                            
                    </td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    		
                    <td width="60" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
                    <td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
          
                    <?php
						
						$total_consumption=0;
						
						foreach($data_array_strip as $scolor)
						{
							//$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
							//$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
							
							?>
							<td width="100" style="word-break:break-all" align="right" id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>" title=""><?php
                                if($scolor[csf("sample_color_ids")])
                                { 
                                    /*$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
                                    $grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									echo number_format($yarnColorWiseLbsQty,4,".","");
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
                                }
                                else 
                                {
                                   /* $total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
                                    $grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
                                    echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");*/
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];

                                    // echo "((".$colorWiseAvgArr[$scolor[csf("sample_color")]]."*".$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]].")/12)*".$selectResult[csf('size_qty')]."<br>";

									echo number_format($yarnColorWiseLbsQty,4,".","");
                                    //.'-'.$colorWiseAvgArr[$scolor[csf("sample_color")]].'-'.$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]];
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
                                }
                                ?> 
                        </td>
							<?php
						}
						//print_r($grand_color_cons_arr);die;
						$grand_total_consumption+=$total_consumption;//($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
						$total_size_qty+=$selectResult[csf('size_qty')];
					?>
                    <td width="100" align="right"><?php echo number_format($total_consumption,4,".","");//number_format(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                     <td>
                     	<input 
                        	type="button" 
                            value="-" 
                            name="minusButton[]" 
                            id="minusButton_<? echo $i;  ?>" 
                            style="width:30px" 
                            class="formbuttonplasminus" 
                            onClick="fnc_minusRow('<? echo $i;  ?>')"/>
                	</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th   colspan="6" > Total</th>
                    <th width="60"  id="total_bundle_qty" ><?php echo $total_size_qty; ?></th>
                    <th width="50"  ></th>       
                    <th width="50"  ></th>
                     <?php
					 	$strip_color_arr=array();
						foreach($data_array_strip as $scolor)
						{
							$strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
							?>
							<th 
                            	width="100" 
                                style="word-break:break-all"
                                id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php
                                if($scolor[csf("sample_color_ids")])
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
                                }
                                else
                                {
                                     echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                }
                                ?></th>
							<?php
						}
					?>
                    <th width="100" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id="">
                    	<input
                        	type="hidden" 
                            id="color_id_string" 
                            name="color_id_string"
                            value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action=="operator_popup")
{
	echo load_html_head_contents("Operator Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
    ?> 
	<script>
		function js_set_value(str)
		{
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}
    </script>

    </head>

    <body>
    <div align="center" style="width:1020px;">
    	<form name="searchwofrm"  id="searchwofrm">
    		<fieldset style="width:1020px;">
    		<legend>Enter search words</legend>           
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
    	                <th width="160" align="center">Company</th>
    	                <th width="135" align="center">Location</th>
    	                <th width="135" align="center">Division</th>
    	                <th width="135" align="center">Department</th>
    	                <th width="135" align="center">Section</th>
    	            	<th width="135" align="center">Employee Code</th>
    	                <th width="90" align="center"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /> <input type="hidden" id="hidden_emp_number"  /></th>           
    	            </thead>
                    <tr class="general">
                        <td align="center">
                        	<?
                        		$sql_com="select 
    											id,
    											company_name
    										from 
    											lib_company comp
    						 				where 
    											status_active =1 and 
    											is_deleted=0 											 
    										order by company_name";
    							
    								
    							echo create_drop_down( "cbo_company_name",
    													160, 
    													$sql_com,
    													"id,company_name", 
    													1, 
    													"--- Select Company ---", 
    													$selected, 
    													"load_drop_down( 	 
    																	'bundle_issue_to_knitting_floor_controller', this.value, 
    																	'load_drop_down_location_hrm', 
    																	'location_td_hrm');",
    													"",
    													"",
    													"",
    													"",
    													"",
    													"" );  
    						?>       
                        </td>
                        <td id="location_td_hrm">
    					 <? 
    						echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected );
                        ?>
    	                </td>
    	                 <td id="division_td_hrm">
    						 <? 
    	                    	echo create_drop_down( "cbo_division_name", 135,$blank_array ,"", 1, "-- Select Division --", $selected );
    	                    ?>
    	                </td> 
    	                <td id="department_td_hrm">
    						<? 
    							echo create_drop_down( "cbo_dept_name", 135,$blank_array ,"", 1, "-- Select Department --", $selected );
    	                    ?>
    	                </td>   
    	                <td id="section_td_hrm">
    						<? 
    							echo create_drop_down( "cbo_section_name", 135,$blank_array ,"", 1, "-- Select Section --", $selected );
    	                    ?>
    	                </td>
    	           
    	                <td>
    						<input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:135px;" >
    	                </td> 
    	                <td>
    	                	<input type="button" 
    	                	name="btn_show" 
    	                	class="formbutton" 
    	                	value="Show" 
    	                	onClick="show_list_view ( 
    	                								document.getElementById('cbo_company_name').value+'_'+
    	                								document.getElementById('cbo_location_name').value+'_'+
    	                								document.getElementById('cbo_division_name').value+'_'+
    	                								document.getElementById('cbo_dept_name').value+'_'+
    	                								document.getElementById('cbo_section_name').value+'_'+
    	                								document.getElementById('src_emp_code').value, 'create_emp_search_list_view', 
    	                								'search_div', 
    	                								'bundle_issue_to_knitting_floor_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
    	                </td>
    	            </tr> 
               </table>
               <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
    		</fieldset>
    	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$emp_code = $ex_data[5];

	//$new_conn="";
 	//$sql_cond="";
	if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
	if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
	if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
	if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
	if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
	if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
	
 
		//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
		//print_r($company_arr);die;

	
	/*if($db_type==2 || $db_type==1 )
	{
      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
	if($db_type==0)
	{
	  $sql = "select emp_code,id_card_no, concat(first_name,'  ',middle_name,last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
		
	}*/
	
	if($db_type==2 || $db_type==1 )
	{
      $emp_name = " (first_name||' '||middle_name|| '  ' || last_name) ";
    }
	if($db_type==0)
	{
	  $emp_name = " concat(first_name,'  ',middle_name,last_name) ";
	}
	//echo 111;die;
	if($new_conn!=""){
		$table_name = "hrm_employee";
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
		$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
		$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
		$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
		$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
		$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);
	}else{
		$table_name = "lib_employee";
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
		$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
		$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
	}
	
	$sql = "select emp_code, id_card_no, $emp_name as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from $table_name where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";

	$arr=array(2=>$designation_arr,3=>$line_no_arr,3=>$company_arr,4=>$location_arr,5=>$division_arr,6=>$department_arr,7=>$section_arr);
	
	if($new_conn!=""){
		echo  create_list_view("list_view","Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section","80,140,120,110,110,110,110,110,80","1040","260",0,$sql,"js_set_value","emp_code,id_card_no,emp_name","",1,"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id",$arr ,"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id","employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","",$new_conn);
	}else{
		echo  create_list_view("list_view","Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section","80,140,120,110,110,110,110,110,80","1040","260",0,$sql,"js_set_value","emp_code,id_card_no,emp_name","",1,"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id",$arr ,"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id","employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","");
	}	
	exit();
}
if ($action=="supervisor_popup")
{
    echo load_html_head_contents("Operator Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?> 
    <script>
    
        function js_set_value(str)
        {
            $("#hidden_emp_number").val(str);
            parent.emailwindow.hide(); 
        }
    
    </script>
    </head>

    <body>
    <div align="center" style="width:1020px;">
        <form name="searchwofrm"  id="searchwofrm">
            <fieldset style="width:1020px;">
            <legend>Enter search words</legend>           
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <th width="160" align="center">Company</th>
                        <th width="135" align="center">Location</th>
                        <th width="135" align="center">Division</th>
                        <th width="135" align="center">Department</th>
                        <th width="135" align="center">Section</th>
                        <th width="135" align="center">Employee Code</th>
                        <th width="90" align="center"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /> <input type="hidden" id="hidden_emp_number"  /></th>           
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                $sql_com="select 
                                                id,
                                                company_name
                                            from 
                                                lib_company comp
                                            where 
                                                status_active =1 and 
                                                is_deleted=0                                             
                                            order by company_name";
                                
                                    
                                echo create_drop_down( "cbo_company_name",
                                                        160, 
                                                        $sql_com,
                                                        "id,company_name", 
                                                        1, 
                                                        "--- Select Company ---", 
                                                        $selected, 
                                                        "load_drop_down(     
                                                                        'bundle_issue_to_knitting_floor_controller', this.value, 
                                                                        'load_drop_down_location_hrm', 
                                                                        'location_td_hrm');",
                                                        "",
                                                        "",
                                                        "",
                                                        "",
                                                        "",
                                                        "" );  
                            ?>       
                        </td>
                        <td id="location_td_hrm">
                         <? 
                            echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected );
                        ?>
                        </td>
                         <td id="division_td_hrm">
                             <? 
                                echo create_drop_down( "cbo_division_name", 135,$blank_array ,"", 1, "-- Select Division --", $selected );
                            ?>
                        </td> 
                        <td id="department_td_hrm">
                            <? 
                                echo create_drop_down( "cbo_dept_name", 135,$blank_array ,"", 1, "-- Select Department --", $selected );
                            ?>
                        </td>   
                        <td id="section_td_hrm">
                            <? 
                                echo create_drop_down( "cbo_section_name", 135,$blank_array ,"", 1, "-- Select Section --", $selected );
                            ?>
                        </td>
                   
                        <td>
                            <input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:135px;" >
                        </td> 
                        <td>
                            <input type="button" 
                            name="btn_show" 
                            class="formbutton" 
                            value="Show" 
                            onClick="show_list_view ( 
                                                        document.getElementById('cbo_company_name').value+'_'+
                                                        document.getElementById('cbo_location_name').value+'_'+
                                                        document.getElementById('cbo_division_name').value+'_'+
                                                        document.getElementById('cbo_dept_name').value+'_'+
                                                        document.getElementById('cbo_section_name').value+'_'+
                                                        document.getElementById('src_emp_code').value, 'create_supervisor_search_list_view', 
                                                        'search_div', 
                                                        'bundle_issue_to_knitting_floor_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />                
                        </td>
                    </tr> 
               </table>
               <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_supervisor_search_list_view")
{
    $ex_data = explode("_",$data);
    $company = $ex_data[0];
    $location = $ex_data[1];
    $division = $ex_data[2];
    $department = $ex_data[3];
    $section = $ex_data[4];
    $emp_code = $ex_data[5];

    //$new_conn="";
    //$sql_cond="";
    if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
    if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
    if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
    if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
    if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
    if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
    

    
    /*if($db_type==2 || $db_type==1 )
    {
      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
    if($db_type==0)
    {
      $sql = "select emp_code,id_card_no, concat(first_name,'  ',middle_name,last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
        
    }*/
    
    if($db_type==2 || $db_type==1 )
    {
      $emp_name = " (first_name||' '||middle_name|| '  ' || last_name) ";
    }
    if($db_type==0)
    {
      $emp_name = " concat(first_name,'  ',middle_name,last_name) ";
    }
    
    if($new_conn!="")
    {
        $table_name = "hrm_employee";
        $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
        $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
        $division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
        $department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
        $section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
        $designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);
    }
    else
    {
        $table_name = "lib_employee";
        $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
        $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
        $division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
        $department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
        $section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
        $designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
    }
    
    $sql = "SELECT emp_code, id_card_no, $emp_name as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from $table_name where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    
    
    

    $arr=array(2=>$designation_arr,3=>$line_no_arr,3=>$company_arr,4=>$location_arr,5=>$division_arr,6=>$department_arr,7=>$section_arr);

    if($new_conn!=""){
        echo  create_list_view("list_view","Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section","80,140,120,110,110,110,110,110,80","1040","260",0,$sql,"js_set_value","emp_code,id_card_no,emp_name","",1,"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id",$arr ,"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id","employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","",$new_conn);
    }else{
        echo  create_list_view("list_view","Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section","80,140,120,110,110,110,110,110,80","1040","260",0,$sql,"js_set_value","emp_code,id_card_no,emp_name","",1,"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id",$arr ,"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id","employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","");
    }   
    exit();
}



if ($action=="load_drop_down_location_hrm")
{
	if($new_conn!=""){
   		echo create_drop_down("cbo_location_name",135,"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-- Select Location --",$selected,"load_drop_down('bundle_issue_to_knitting_floor_controller', this.value,'load_drop_down_division','division_td_hrm');","","","","","","",$new_conn );
	}else{
   		echo create_drop_down("cbo_location_name",135,"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-- Select Location --",$selected, "load_drop_down('bundle_issue_to_knitting_floor_controller', this.value,'load_drop_down_division','division_td_hrm');","","","","","","" );
	}
	exit();
}


if ($action=="load_drop_down_division")
{
	if($new_conn!=""){
   		echo create_drop_down("cbo_division_name",135,"select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0","id,division_name",1,"-- Select Division --",$selected,"load_drop_down('bundle_issue_to_knitting_floor_controller',this.value,'load_drop_down_department','department_td_hrm');","","","","","","",$new_conn );
	}else{
   		echo create_drop_down("cbo_division_name",135,"select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0","id,division_name",1,"-- Select Division --",$selected,"load_drop_down('bundle_issue_to_knitting_floor_controller',this.value,'load_drop_down_department','department_td_hrm');","","","","","","" );
	}
	exit();
}

if ($action=="load_drop_down_department")
{
	if($new_conn!=""){
	   echo create_drop_down("cbo_dept_name",135,"select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name", 1,"-- Select Department --",$selected,"load_drop_down( 'bundle_issue_to_knitting_floor_controller',this.value, 'load_drop_down_section', 'section_td_hrm');","","","","","","",$new_conn );
	}else{
	   echo create_drop_down("cbo_dept_name",135,"select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name", 1,"-- Select Department --",$selected,"load_drop_down( 'bundle_issue_to_knitting_floor_controller',this.value, 'load_drop_down_section', 'section_td_hrm');","","","","","","");
	}
	exit();
}

if ($action=="load_drop_down_section")
{
	if($new_conn!=""){
	   echo create_drop_down("cbo_section_name",135,"select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0","id,section_name",1,"-- Select Section --",$selected,"","","","","","","",$new_conn );
	}else{
	   echo create_drop_down("cbo_section_name",135,"select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0","id,section_name",1,"-- Select Section --",$selected,"","","","","","","");
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con 			= connect();
		$delivery_basis =3;
		if($db_type==0)	{ mysql_query("BEGIN"); }

			
		if($db_type==0) 	 	$year_cond="YEAR(insert_date)"; 
		else if($db_type==2) 	$year_cond="to_char(insert_date,'YYYY')";
		else 					$year_cond="";

		$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'PIKF',0,date("Y",time()),0,0,50,0,0));
	 	
		$field_array_delivery=" id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, operator_id,supervisor_id, delivery_basis, production_source, serving_company, floor_id, delivery_date, body_part_type, body_part_ids, working_company_id, working_location_id, remarks, size_set_no,shift_name, inserted_by, insert_date";

		$mst_id = return_next_id_by_sequence( "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );
		$cbo_bodypart_type="'".implode(',',array_unique(explode(',',str_replace("'", "", $cbo_bodypart_type))))."'";

		$data_array_delivery="(".$mst_id.", '".$new_sys_number[1]."', '".(int)$new_sys_number[2]."', '".$new_sys_number[0]."', ".$cbo_company_name.", 50, ".$cbo_location.", ".$txt_operator_id.", ".$hidden_sup_id.", ".$delivery_basis.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_floor.", ".$txt_issue_date.", ".$cbo_bodypart_type.", ".$txt_bodyPart_id.", ".$cbo_working_company.", ".$cbo_working_location.", ".$txt_remarks.", ".$txt_size_set_no.", ".$txt_shift_name.", ".$user_id.", '".$pc_date_time."')";

		$challan_no 	=(int)$new_sys_number[2];
		$txt_challan_no =$new_sys_number[0];

		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;       
        }
            
        $bundle 		="'".implode("','",$bundleCheckArr)."'";

         $receive_sql="select b.barcode_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b , pro_gmts_bundle_bodypart c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.production_type=50 and b.production_type=50 and c.production_type=50 and b.bodypart_type_id=$cbo_bodypart_type and c.body_part_id in (".str_replace("'", "", $txt_bodyPart_id).") and b.bundle_no  in ($bundle)  and b.production_type=50 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and (b.is_rescan=0 or b.is_rescan is null)";
       // echo "10**".$receive_sql;die;

        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }

 		$colorsize_sql="select id, po_break_down_id, country_id , size_number_id, color_number_id, item_number_id from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 ";

		$colorSizeIdArr 	=array();
		$colorsize_result 	= sql_select($colorsize_sql);
		foreach($colorsize_result as $cs_row)
		{
			$colorSizeIdArr[$cs_row[csf('po_break_down_id')]]
								[$cs_row[csf('country_id')]]
									[$cs_row[csf('item_number_id')]]
										[$cs_row[csf('color_number_id')]]
											[$cs_row[csf('size_number_id')]]=$cs_row[csf('id')];
		}

		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$field_array_mst="  id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$machine_id 	="machine_id_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo] 						+=$$qty;
				$dtlsArrColorSize[$$bundleNo] 				=$colorSizeIdArr[$$orderId][$$countryId][$$gmtsitemId][$$colorId][$$sizeId];
				$bundleRescanArr[$$bundleNo]				=0;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$bundleMachineArr[$$bundleNo] 				=$$machine_id;
			}
			else
			{
				echo "scan**".$$bundleNo; disconnect($con); die;
			}
		}
		
		$gmts_color_id=$$colorId;
		//echo "10**";print_r($dtlsArrColorSize);die;
		
		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.", ".$mst_id.", ".$txt_lot_ratio.", ".$cbo_company_name.", ".$garments_nature.", '".$challan_no."', ".$orderId.", ".$gmtsItemId.", ".$countryId.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_location.", ".$txt_issue_date.", ".$qty.", 50, 3, ".$txt_remarks.", ".$cbo_floor.", ".$user_id.", '".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
        $field_array_dtls="  id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, is_rescan, operator_id,supervisor_id, machine_id, bodypart_type_id, body_part_ids"; 
		$field_array_bodypart_dtls="id, mst_id, dtls_id, delivery_mst_id, bundle_no, is_rescan, barcode_no, bodypart_type_id, machine_id, body_part_id, production_type"; 
		
		$field_array_color_dtls="id, delivery_mst_id, production_type, operator_id,supervisor_id, gmts_color, sample_color, sample_color_ids, yarn_color, required_qty, returanable_qty, issue_qtygm, issue_qty, short_excess_qty, issue_balance_qty, lot_ratio_no, bodypart_type_id, body_part_ids, inserted_by, insert_date";
		
		foreach($dtlsArr as $bundle_no=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.", ".$mst_id.", ".$gmtsMstId.", 50, '".$dtlsArrColorSize[$bundle_no]."', '".$qty."', ".$txt_lot_ratio.", '".$bundle_no."', '".$bundleBarcodeArr[$bundle_no]."', '".$bundleRescanArr[$bundle_no]."', ".$txt_operator_id.", ".$hidden_sup_id.", '".$bundleMachineArr[$bundle_no]."', ".$cbo_bodypart_type.", ".$txt_bodyPart_id.")";

            $bodypart_id_arr=explode(",", str_replace("'", "", $txt_bodyPart_id));
           // print_r($bodypart_id_arr);die;
            foreach($bodypart_id_arr as $single_body_part)
            {
                $bodypart_dtls_id   = return_next_id_by_sequence("pro_gmts_bundle_bodypart_seq","pro_gmts_bundle_bodypart", $con );
                if($data_array_bodypart_dtls!="") $data_array_bodypart_dtls.=",";

                $data_array_bodypart_dtls.= "(  ".$bodypart_dtls_id.", ".$gmtsMstId.", ".$dtls_id.", ".$mst_id.", '".$bundle_no."', '".$bundleRescanArr[$bundle_no]."', '".$bundleBarcodeArr[$bundle_no]."', ".$cbo_bodypart_type.", '".$bundleMachineArr[$bundle_no]."', ".$single_body_part.", 50)";
            }
		}
		
        $color_data_string='';
		for($k=1;$k<=$yarn_color_row;$k++)
		{ 	
			
			$yarnColor 		="yarnColor_".$k;
			$sampleColor 	="sampleColor_".$k;			
			$requiredQty 	="requiredQty_".$k;
			$returnableQty 	="returnableQty_".$k;
			$shortExcess 	="shortExcess_".$k;
			$issueBalance 	="issueBalance_".$k;
			$issueQtyGm 	="issueQtyGm_".$k;
			$issueQty 		="issueQty_".$k;			
			$yarn_dtls_id = return_next_id_by_sequence(  "pro_gmts_knit_issue_dtls_seq",  "pro_gmts_knitting_issue_dtls", $con );

            if(count(explode(",", $$sampleColor))>1) $sample_color_id="";
            else  $sample_color_id=$$sampleColor;
			if($data_array_color_dtls!="") $data_array_color_dtls.=",";
			$data_array_color_dtls.= "(".$yarn_dtls_id.", ".$mst_id.", 50, ".$txt_operator_id.", ".$hidden_sup_id.", '".$gmts_color_id."', '".$sample_color_id."', '".$$sampleColor."', '".$$yarnColor."', '".$$requiredQty."', '".$$returnableQty."', '".$$issueQtyGm."', '".$$issueQty."', '".$$shortExcess."', '".$$issueBalance."', ".$txt_lot_ratio.", ".$cbo_bodypart_type.", ".$txt_bodyPart_id.", ".$user_id.", '".$pc_date_time."')";
		      $color_data_string.=$$yarnColor."_".$yarn_dtls_id."#";
		}


			//echo "10**insert into pro_gmts_delivery_mst (".$field_array_delivery.") values ".$data_array_delivery;die;
		
		$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$yarnColorrID=sql_insert("pro_gmts_knitting_issue_dtls",$field_array_color_dtls,$data_array_color_dtls,1);
        $bundleBodyPartrID=sql_insert("pro_gmts_bundle_bodypart",$field_array_bodypart_dtls,$data_array_bodypart_dtls,1);
		//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		//echo "10**".$challanrID."**".$rID."**".$dtlsrID."**".$yarnColorrID."**".$bundleBodyPartrID;die;
	
		if($db_type==0)
		{  
			if($challanrID && $rID && $dtlsrID && $yarnColorrID && $bundleBodyPartrID)
			{

				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".str_replace("'","",$color_data_string);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($challanrID && $rID && $dtlsrID && $yarnColorrID && $bundleBodyPartrID)
			{
				oci_commit($con); 
				echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".str_replace("'","",$color_data_string); 
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		
		//check_table_status( 160,0);
		disconnect($con);
		die;
		
		//check_table_status( $_SESSION['menu_id'],0);
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
		$challan_no=(int) $txt_chal_no[3];
		$cbo_bodypart_type="'".implode(',',array_unique(explode(',',str_replace("'", "", $cbo_bodypart_type))))."'";
		
		$field_array_delivery="delivery_date*updated_by*shift_name*update_date";
		$data_array_delivery="".$txt_issue_date."*".$user_id."*".$txt_shift_name."*'".$pc_date_time."'";
	
		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;
              $bundleCheckArr[$$bundleCheck]=$$bundleCheck; 
        }
 
        $bundle="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="select c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b , pro_gmts_bundle_bodypart c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.production_type=50 and b.production_type=50 and c.production_type=50 and b.bodypart_type_id=$cbo_bodypart_type and c.body_part_id in (".str_replace("'", "", $txt_bodyPart_id).") and b.bundle_no  in ($bundle)  and b.production_type=50 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.delivery_mst_id!=$mst_id and b.delivery_mst_id!=$mst_id and (b.is_rescan=0 or b.is_rescan is null)"; 
       // echo "10**".$receive_sql;die;
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		
		$non_delete_arr=production_validation($mst_id,'3_1');
		$issue_data_arr=production_data($mst_id,'2_1');
		
 		$colorsize_sql="select id, po_break_down_id, country_id , size_number_id, color_number_id, item_number_id from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 "; 

		$colorSizeIdArr 	=array();
		$colorsize_result 	= sql_select($colorsize_sql);
		foreach($colorsize_result as $cs_row)
		{
			$colorSizeIdArr[$cs_row[csf('po_break_down_id')]]
								[$cs_row[csf('country_id')]]
									[$cs_row[csf('item_number_id')]]
										[$cs_row[csf('color_number_id')]]
											[$cs_row[csf('size_number_id')]]=$cs_row[csf('id')];
		}

		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$field_array_mst="  id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$machine_id 	="machine_id_".$j;
            $bundle_cons    ="bundle_cons_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo] 						+=$$qty;
				$dtlsArrColorSize[$$bundleNo] 				=$colorSizeIdArr[$$orderId][$$countryId][$$gmtsitemId][$$colorId][$$sizeId];
				$bundleRescanArr[$$bundleNo]				=0;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$bundleMachineArr[$$bundleNo] 				=$$machine_id;
			}
		}
		
		$gmts_color_id=$$colorId;
		//echo "10**";print_r($dtlsArrColorSize);die;
		
		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.", ".$mst_id.", ".$txt_lot_ratio.", ".$cbo_company_name.", ".$garments_nature.", '".$challan_no."', ".$orderId.", ".$gmtsItemId.", ".$countryId.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_location.", ".$txt_issue_date.", ".$qty.", 50, 3, ".$txt_remarks.", ".$cbo_floor.", ".$user_id.", '".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
		$field_array_dtls=" id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, is_rescan, machine_id, supervisor_id,supervisor_id, bodypart_type_id, body_part_ids"; 
		
		$field_array_bodypart_dtls="id, mst_id, dtls_id, delivery_mst_id, bundle_no, is_rescan, barcode_no, bodypart_type_id, machine_id, body_part_id, production_type"; 
		
		$field_array_color_dtls="operator_id*supervisor_id* gmts_color* sample_color* sample_color_ids* yarn_color* required_qty* returanable_qty*issue_qtygm* issue_qty* short_excess_qty* issue_balance_qty* lot_ratio_no* bodypart_type_id* body_part_ids* updated_by* update_date";
		
		foreach($dtlsArr as $bundle_no=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.", ".$mst_id.", ".$gmtsMstId.", 50, '".$dtlsArrColorSize[$bundle_no]."', '".$qty."', ".$txt_lot_ratio.", '".$bundle_no."', '".$bundleBarcodeArr[$bundle_no]."', '".$bundleRescanArr[$bundle_no]."', '".$bundleMachineArr[$bundle_no]."', ".$txt_operator_id.", ".$hidden_sup_id.", ".$cbo_bodypart_type.", ".$txt_bodyPart_id.")";

            $bodypart_id_arr=explode(",", str_replace("'", "", $txt_bodyPart_id));
            foreach($bodypart_id_arr as $single_body_part)
            {
                $bodypart_dtls_id   = return_next_id_by_sequence("pro_gmts_bundle_bodypart_seq","pro_gmts_bundle_bodypart", $con );
                if($data_array_bodypart_dtls!="") $data_array_bodypart_dtls.=",";

                $data_array_bodypart_dtls.= "(  ".$bodypart_dtls_id.", ".$gmtsMstId.", ".$dtls_id.", ".$mst_id.", '".$bundle_no."', '".$bundleRescanArr[$bundle_no]."', '".$bundleBarcodeArr[$bundle_no]."', ".$cbo_bodypart_type.", '".$bundleMachineArr[$bundle_no]."', ".$single_body_part.", 50)";
            }
		}
		

		for($k=1;$k<=$yarn_color_row;$k++)
		{ 	
			$yarnColor 			="yarnColor_".$k;
			$yarnColor 			="yarnColor_".$k;
			$sampleColor 		="sampleColor_".$k;			
			$requiredQty 		="requiredQty_".$k;
			$returnableQty 		="returnableQty_".$k;
			$shortExcess 		="shortExcess_".$k;
			$issueBalance 		="issueBalance_".$k;
			$issueQtyGm 		="issueQtyGm_".$k;
			$issueQty 			="issueQty_".$k;
			$yarnDtlsId 		="yarnDtlsId_".$k;

			$color_dtls_id 		=$$yarnDtlsId;
			$color_dtls_id_arr[]=$color_dtls_id;

            if(count(explode(",", $$sampleColor))>1) $sample_color_id="";
            else  $sample_color_id=$$sampleColor;
			$data_array_color_dtls[$color_dtls_id]= explode("*",($txt_operator_id."*".$hidden_sup_id."* '".$gmts_color_id."'* '".$sample_color_id."'* '".$$sampleColor."'* '".$$yarnColor."'* '".$$requiredQty."'* '".$$returnableQty."'* '".$$issueQtyGm."'* '".$$issueQty."'* '".$$shortExcess."'* '".$$issueBalance."'* ".$txt_lot_ratio."* ".$cbo_bodypart_type."* ".$txt_bodyPart_id."* ".$user_id."* '".$pc_date_time."'"));
		}
		

        $delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$mst_id and production_type=50");
        $delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$mst_id and production_type=50");
        $delete_bodypart_dtls = execute_query("update pro_gmts_bundle_bodypart set status_active=0,is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=50");
		$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
        $bundleBodyPartrID=sql_insert("pro_gmts_bundle_bodypart",$field_array_bodypart_dtls,$data_array_bodypart_dtls,1);
		//$challanrID=sql_update("pro_gmts_knitting_issue_dtls",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
		$color_dtlsrID=execute_query(bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr ));
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		// echo "10**".$challanrID .'&&'. $rID .'&&'. $dtlsrID .'&&'. $delete .'&&'. $delete_dtls."**".$color_dtlsrID;oci_rollback($con);die;
		//echo "10**".$dtlsrID;oci_rollback($con);die;
		if($db_type==0)
		{  
			if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls && $bundleBodyPartrID && $delete_bodypart_dtls && $color_dtlsrID)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls && $bundleBodyPartrID && $delete_bodypart_dtls  && $color_dtlsrID)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
        echo '13'; die;
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
	
		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:1020px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1020px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
	                <thead>
	                	<th>Company Name</th>
	                    <th>Job No</th>
	                    <th>Style No</th>
	                    <th>Challan No</th>
	                    <th>Lot Ratio No</th>
                        <th>QR Code</th>
                        <th colspan="2">Issue Date</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" /> 
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td>
	                    	<? 
							$sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name"; 
							echo create_drop_down( "cbo_company_name", 140, $sql_com, "id,company_name", 1, "-- Select --", $selected, "",0 );
                        	?>
	                    </td>
	                    <td><input type="text" style="width:90px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 
	                    <td><input type="text"style="width:90px"class="text_boxes"name="txt_style_no"id="txt_style_no" /></td>
	                    <td><input type="text"style="width:90px"class="text_boxes"name="txt_challan_no"id="txt_challan_no" /></td> 
                        <td><input type="text"style="width:90px"class="text_boxes"name="txt_cutting_no"id="txt_cutting_no" /></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_barcode_no" id="txt_barcode_no" /></td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>
                        </td>   
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
	            		<td>
	                     	<input type="button"name="button2"class="formbutton"value="Show"style="width:70px;"onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+ document.getElementById('txt_challan_no').value+'_'+ document.getElementById('txt_style_no').value+'_'+ document.getElementById('txt_job_no').value+'_'+ document.getElementById('txt_cutting_no').value+'_'+ document.getElementById('txt_barcode_no').value+'_'+ document.getElementById('txt_date_from').value+'_'+ document.getElementById('txt_date_to').value, 'create_challan_search_list_view', 'search_div', 'bundle_issue_to_knitting_floor_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" />
	                     </td>
	                </tr>

                    <tr>
                        <td colspan="9" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
	           	</table>
	           	<div id="search_div"align="left"style=" width:100%; margin-top:10px; margin-left:3px"> </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	//$search_string="'%".trim($data[1)."'";

	$company_id =$data[0];
    if($company_id==0)
    {
        echo "<div style='font-size:20px;text-align:center;color:red;font-weight:bold;'> Please Select Company First. </div>";die;
    }
	if($data[1]!="") $search_field_cond=" and a.sys_number like '%".trim($data[1])."%'";
	if($data[4]!="") $search_field_cond.=" and b.cut_no like '%".trim($data[4])."%'";
	if($data[3]!="") $search_field_cond=" and c.job_no_mst like '$data[3]'";
	if($data[2]!="") $search_field_cond.=" and d.style_ref_no like '%".trim($data[2])."%'";
	if(trim($data[5])!="") $search_field_cond.=" and e.barcode_no ='".trim($data[5])."'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

    if($data[6]!="" && $data[7]!="")
    {
        if($db_type==0)
        {
            $txt_datefrom=change_date_format($data[6],'yyyy-mm-dd');
            $txt_dateto=change_date_format($data[7],'yyyy-mm-dd');
        }
        else if($db_type==2)
        {
            $txt_datefrom=change_date_format($data[6],'','',-1);
            $txt_dateto=change_date_format($data[7],'','',-1);
        }
        $search_field_cond .= " and b.production_date between '$txt_datefrom' and '$txt_dateto'";
    }

    if($data[1]=="" && $data[2]=="" && $data[3]=="" && $data[4]=="" && $data[5]=="" && $data[6]=="" && $data[7]=="")
    {
        echo "<div style='font-size:20px;text-align:center;color:red;font-weight:bold;'>Please enter search value of anyone field.</div>";
        die();
    }

	$sql = "SELECT a.id, a.delivery_date, $year_field, a.sys_number_prefix_num, a.sys_number, a.production_source, a.serving_company, a.location_id, a.floor_id, b.cut_no, d.job_no, d.style_ref_no, sum(b.production_quantity) as total_production_qty from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d, pro_garments_production_dtls e 
	where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and b.id=e.mst_id and a.production_type=50 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.production_type=50 and a.company_id=$company_id $search_field_cond 
	group by a.id, a.delivery_date, a.insert_date, a.sys_number_prefix_num, a.sys_number, a.production_source, a.serving_company, a.location_id, a.floor_id, b.cut_no, d.job_no, d.style_ref_no 
	order by a.id DESC";
    //echo $sql;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Challan</th>
            <th width="50">Year</th>
            <th width="70">Delivery Date</th>
            <th width="90">Job No</th>               
            <th width="100">Source</th>
            <th width="110">W. Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th width="80">Lot Ratio No</th>
            <th width="60">Challan Qty</th>
            <th>Style Ref.</th>
        </thead>
	</table>
	<div style="width:1020px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1) $serv_comp=$company_arr[$row[csf('serving_company')]]; else $serv_comp=$supllier_arr[$row[csf('serving_company')]];
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="30"><? echo $i; ?></td>
                    <td width="50"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="90"><p><? echo $row[csf('job_no')]; ?></p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="80"><p><? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="60" align="right"><p><? echo $row[csf('total_production_qty')]; ?></p></td>
                    <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
	<?	
    exit();
}

if($action=="job_search_popup")
{
    echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    
    ?>
    <script>
        function js_set_order(strCon ) 
        {
        document.getElementById('hidden_job_no').value=strCon;
        parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr>                     
                        <th width="150">Company name</th>
                        <th width="60">Job No</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Order No</th>
                        <th width="220">Date Range</th>
                        <th width=""><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">                    
                        <td>
                              <? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",0);
                             ?>                       
                                <input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
                                <input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
                                <input type="hidden" id="hidden_prifix" name="hidden_prifix" />
                                <input type="hidden" id="hidden_job_no" name="hidden_job_no" />
                        </td>
                        <td> <input style="width:50px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  /></td>
                        <td><input style="width:90px;" type="text"  class="text_boxes"   name="txt_style_no" id="txt_style_no"  /></td>
                        <td> <input style="width:90px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  /></td>
                        <td>
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                        </td>
                        <td>
                             <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_style_no').value, 'create_job_search_list_view', 'search_div', 'bundle_issue_to_knitting_floor_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />                
                        </td>
                </tr>
                    <tr>                  
                    <td align="center" valign="middle" colspan="8">
                        <? echo load_month_buttons(1);  ?>
                    </td>
                </tr>   
                </tbody>
             </tr>         
            </table> 
              <div align="center" valign="top" id="search_div"> </div>  
            </form>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
    $company = $ex_data[0]; 
    $from_date = $ex_data[1];
    $to_date = $ex_data[2];
    $job_prifix= $ex_data[3];
    $job_year = $ex_data[4];
    $po_no = $ex_data[5];
    $style_reff = $ex_data[6];
    $job_cond="";
    
    if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
    if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
    if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
   if(str_replace("'","",$style_reff)!="")  $style_cond="and b.style_ref_no like '%".str_replace("'","",$style_reff)."%' "; else $style_cond="";
    
    if($db_type==0)
    {
        if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
    $sql_order="SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where  b.garments_nature=100 and a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond  $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.buyer_name,b.job_no,a.po_number ";  
    }
    
    if($db_type==2)
    {
		if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
		{
			$sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
     
    	$sql_order="SELECT b.id, b.job_no, b.buyer_name, a.po_number, a.pub_shipment_date, b.style_ref_no, b.job_no_prefix_num as job_prefix, extract(year from b.insert_date) as year, a.file_no, a.grouping from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  $sql_cond $conpany_cond $job_cond $order_cond   $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.id, b.job_no, b.buyer_name, a.po_number, a.pub_shipment_date, b.style_ref_no, b.job_no_prefix_num, b.insert_date, a.file_no, a.grouping order by b.id desc";  
    }
    //echo $sql_order;
    $buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
    $arr=array (3=>$buyer_arr);
    echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name, Order No,Shipment Date","60,60,150,150,150,100","800","270",0, $sql_order , "js_set_order", "job_no,style_ref_no", "", 1, "0,0,0,buyer_name,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ; 
	exit();
}

if($action=="knitting_issue_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    //$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
   
	$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee",'id_card_no','emp_name',$new_conn);
	
    $machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );
    $order_array=array();
    
    $sql="select a.id, a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.company_id, a.location_id, a.production_source, a.serving_company, a.floor_id, a.body_part_ids , a.operator_id , a.delivery_date, a.working_company_id, a.working_location_id, a.size_set_no, b.*, c.size_id, c.size_qty from pro_gmts_delivery_mst a, pro_garments_production_dtls b, ppl_cut_lay_bundle c where a.id='$data[1]' and a.production_type=50 and a.status_active=1 and a.is_deleted=0 and a.id=b.delivery_mst_id and b.status_active=1 and b.is_deleted=0 and b.bundle_no=c.bundle_no and b.barcode_no=c.barcode_no and c.status_active=1 and c.is_deleted=0"; 
    //echo $sql; 
	$dataArray=sql_select($sql); 
	
	$sql_knitting=" select gmts_color, yarn_color, sample_color, required_qty, returanable_qty , issue_qty, short_excess_qty , issue_balance_qty from pro_gmts_knitting_issue_dtls where delivery_mst_id='$data[1]' and status_active=1 and is_deleted=0 order by sample_color"; 
	$dataArrayKnitting=sql_select($sql_knitting); 
	
	$order_sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, a.style_description, b.sizeset_no from wo_po_details_master a, ppl_size_set_mst b, ppl_size_set_dtls c where a.job_no='".$data[3]."' and b.sizeset_no='".$dataArray[0][csf('size_set_no')]."' and a.job_no=b.job_no and b.id=c.mst_id and c.color_id=".$dataArrayKnitting[0][csf('gmts_color')]." and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0  and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by a.job_no, a.buyer_name, a.style_ref_no, a.style_description, b.sizeset_no";
    //echo $order_sql;die;
    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $style_no=$row[csf('style_ref_no')];
        $buyer_name=$buyer_arr[$row[csf('buyer_name')]];
    }
	
	$cuttingNo=$dataArray[0][csf('cut_no')];
	
	$lotArr=return_library_array( "select a.color_id as color_id, a.lot as lot from ppl_cut_lay_prod_dtls a, ppl_cut_lay_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.cutting_no='$cuttingNo'", "color_id", "lot");

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    //echo $image_location;die;
    ?>
    <div style="width:1030px;">
    <table width="1000" cellspacing="0" align="left">
        <tr height="100">
            <td colspan="2"  align="left"><img src="../../../<?php echo $image_location; ?>" height="70" width="140"></td>
            <td colspan="6" align="center"  style="font-size:xx-large;"><strong ><? echo $company_library[$data[0]]; ?></strong>
            </td>
            <td colspan="2" align="right"  style="text-align:right;">
            	<div id="qrcode"></div> 
            </td>
        </tr>

        <tr>
            <td colspan="10" align="center" style="font-size:20px"><u><strong>Issue Card - Knitting</strong></u></td>
        </tr>
        <tr>
            <td width="135"><strong>Issue ID:</strong></td>
            <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Issue Date :</strong></td>
            <td width="175px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            <td width="155"><strong>MC No:</strong></td>
            <td  colspan="3">
            <? 
                foreach ($dataArray as $val) {
                    $machine_string_arr[$val[csf('machine_id')]]=$machine_library[$val[csf('machine_id')]];
                }
                echo implode(",", $machine_string_arr);
             ?>
            </td>
            <td width="145"><strong>Size Set No:</strong></td>
            <td width="175px"><? echo $dataArray[0][csf('size_set_no')]; ?></td>
        </tr>
        <tr>
            <td width="125"><strong>OP Card No:</strong></td>
            <td width="175px"><? echo $dataArray[0][csf('operator_id')]; ?></td>
            <td width="125"><strong>Style No :</strong></td>
            <td width="175px"><? echo $style_no; ?></td>
            <td width="125"><strong>Body Part:</strong></td>
            <td  colspan="5">
            <? 
                $bodypart_id_arr=explode(",", $dataArray[0][csf('body_part_ids')]);
                foreach ($bodypart_id_arr as $bodypart_id) {
                    $bodypart_string_arr[$bodypart_id]=$time_weight_panel[$bodypart_id];
                }
                echo implode(",", $bodypart_string_arr);
             ?>
            </td>
        </tr>
        <tr>
            <td width="125"><strong>OP Name:</strong></td>
            <td ><? echo $emp_arr[$dataArray[0][csf('operator_id')]]; ?></td>
            <td>Sup Name</td>
            <td><?=$emp_arr[$dataArray[0][csf('supervisor_id')]];?></td>
            <td><strong>Buyer Name :</strong></td>
            <td width="175px"><? echo $buyer_name; ?></td>
            <td width="125"><strong>Knit Floor:</strong></td>
            <td colspan="3"><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        </tr>
    </table>
         <br>
    <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd" align="left">
            <th width="180" align="center">Gmt Color</th>
            <th width="80" align="center">Color Seq</th>
            <th width="180" align="center">Yarn Color</th>
            <th width="100" align="center">Lot No</th>
            <th width="100" align="center">Req. Qty.</th>
            <th width="100" align="center">Issue Qty.</th>
            <th width="100" align="center">Returnable Qty.</th>
            <th align="center">Yarn Returned</th>
        </thead>
        <tbody>

        <?php
            $j=1;
            foreach($dataArrayKnitting as $val)
            {
                $returnable_quantity=($val[csf('issue_qty')]-$val[csf('required_qty')]);
                if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $color_count=count($cid);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                    <?php 
                    if($j==1)
                    {
                        ?>
                        <td align="center" rowspan="<?php echo count($dataArrayKnitting); ?>"><? echo $color_library[$val[csf('gmts_color')]];?></td>
                        <?php
                    }
                    ?>
                    <td align="center"><? echo $color_library[$val[csf('sample_color')]];?></td>
                    <td align="center"><? echo $color_library[$val[csf('yarn_color')]];?></td>
                    <td align="center"><? echo $lotArr[$val[csf('yarn_color')]]; ?></td>
                    <td align="center"><? echo number_format($val[csf('required_qty')],4); ?></td>
                    <td align="center"><? echo number_format($val[csf('issue_qty')],4); ?></td>
                    <td align="center"><? echo number_format($returnable_quantity,4); ?></td>
                    <td align="right"><?  //echo $val[csf('required_qty')]; ?></td>           
                    
                </tr>
                <?
                $required_quantity+=$val[csf('required_qty')];
                $issue_quantity+=$val[csf('issue_qty')];
                $returnable_qnty+=$returnable_quantity;
               // $issue_quantity+=$val[csf('issue_qty')];
                $j++;
            }

        ?>
        </tbody>
        <tfooter bgcolor="#eee" align="left">
            <th align="center" colspan="4">Total</th>
            
            <th width="100" align="center"><?php echo number_format($required_quantity,4); ?></th>
            <th width="100" align="center"><?php echo number_format($issue_quantity,4); ?></th>
            <th width="100" align="center"><?php echo number_format($returnable_qnty,4); ?></th>
            <th width="" align="center"></th>
        </tfooter>
    </table>  
         
   
    <table align="left" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd">
            <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="80" align="center" rowspan="2">Gmt Size</th>
                <th width="80" align="center" rowspan="2">Bundle No</th>
                <th width="90" align="center" rowspan="2">Barcode No</th>
                <th width="80" align="center" rowspan="2">Bundle Qty</th>
                <th width="" align="center">Hand Writing</th>
                <th width="100" align="center" rowspan="2">Bundle Weight</th>
                <th width="" align="center" colspan="3">Hand Writing</th>
            </tr>
            <tr>
                <th width="100" align="center">Rec. Qty (Pcs)</th>
                <th width="100" align="center">Receive Weight</th>
                <th width="80" align="center">Wastage  </th>
                <th width="" align="center">Remarks</th>
            </tr>
        </thead>
        <tbody>
        <?
        
            $i=1;
            $tot_qnty=array();
            foreach($dataArray as $val)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; $color_count=count($cid);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                    <td><? echo $i;  ?></td>
                    <td align="center"><? echo $size_library[$val[csf('size_id')]]; ?></td>
                    <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                    <td align="center"><? echo $val[csf('barcode_no')]; ?></td>
                    <td align="center"><? echo $val[csf('size_qty')]; ?></td>
                    <td align="center"><? //echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                    <td align="center"><? //echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                    <td align="center"><?// echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                    <td align="center"><? //echo $country_library[$val[csf('country_id')]]; ?></td>
                    <td align="right"><?  //echo $val[csf('production_qnty')]; ?></td>                        
                </tr>
                <?
                $i++;
            }
        ?>
        </tbody>                           
        </table>
            <br>
        <table width="900" cellspacing="0" align="left">
            <tr>
                <td colspan="9" align="center" style="font-size:24px"></td>
            </tr>
           
            <tr>
                <td colspan="9" align="center" style="font-size:20px">
                    <img src="diclaration.png" height="50" width="540">
                </td>
            </tr>
            <tr height="50">
                <td colspan="9" align="center" style="font-size:20px"></td>
            </tr>
            <tr>
                <td colspan="2" width="250"  align="center">
                    <hr/>
                    <strong>Supervisor</strong>
                </td>
                <td width="50px">
                </td>
                <td colspan="2" width="250" align="center">
                    <hr/>
                    <strong>Distributor</strong>
                </td>
                <td width="50px">
                </td>
              
                <td  colspan="2" width="250" align="center">
                    <hr/>
                    <strong>QC</strong>
                </td>               
            </tr>
 
        </table>

    </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
    <script>
        var main_value='<? echo $dataArray[0][csf('sys_number')]; ?>';
        $('#qrcode').qrcode(main_value);
    </script>
	<?
    exit(); 
}


if($action=="challan_duplicate_check")
{
    $bundle_no="'".implode("','",explode(",",$data))."'";
    $msg=1;
    
    $bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";

    $bundle_nos_cond=" and b.barcode_no in ($bundle_no)";

    $hold=sql_select("select barcode_no from PPL_CUT_LAY_BUNDLE where barcode_no in ($bundle_no) and hold=1");
    if(count($hold))
    {
        echo "22_This bundle is in hold , can not be scanned_$bundle_no";
        exit();
    }

    $search_lot_no=return_field_value("a.cutting_no as cutting_no","ppl_cut_lay_mst a, ppl_cut_lay_bundle b","b.barcode_no='".$data."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","cutting_no");  

    $result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=50 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by a.sys_number,b.bundle_no");

    $datastr="";
    if(count($result)>0)
    {
        foreach ($result as $row)
        { 
            $msg=2;
            $datastr=$row[csf('bundle_no')]."*".$row[csf('sys_number')];
        }
    }
    echo rtrim($msg)."_".rtrim($datastr)."_".$search_lot_no;
    exit();
}


if($action=='populate_data_from_challan_popup')
{
    $data_array=sql_select("
                            select 
                                    id, 
                                    company_id, 
                                    sys_number, 
                                    embel_type, 
                                    embel_name, 
                                    production_source, 
                                    serving_company, location_id, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where id='$data'  and production_type=2 and status_active=1 and is_deleted=0");
    
    foreach ($data_array as $row)
    { 
        echo "document.getElementById('txt_challan_no').value               = '".$row[csf("sys_number")]."';\n";
        echo "document.getElementById('cbo_company_name').value             = '".$row[csf("company_id")]."';\n";
        echo "$('#cbo_source').val('".$row[csf('production_source')]."');\n";
        echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', ".$row[csf('production_source')].", 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
        
        echo "$('#cbo_emb_company').val('".$row[csf('serving_company')]."');\n";
        echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";
        echo "$('#cbo_location').val('".$row[csf('location_id')]."');\n";
        
        echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('location_id')]."', 'load_drop_down_floor', 'floor_td' );\n";
        echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "load_drop_down( 'requires/print_embro_delivery_entry_controller', '".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "$('#cbo_floor').val('".$row[csf('floor_id')]."');\n";
        echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
        echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
        echo "$('#txt_organic').val('".$row[csf('organic')]."');\n";
        echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
        echo "$('#txt_issue_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
        
        echo "$('#cbo_body_part').val('".$row[csf('body_part')]."');\n";
        echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
        echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";
        
        echo "disable_enable_fields('cbo_company_name*cbo_source*cbo_emb_company*cbo_location*cbo_floor',1);\n";
        
        echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_issue_print_embroidery_entry',1,1);\n";  
        exit();
    }
}

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );


if($action=="load_report_format")
{
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");       
    echo trim($print_report_format);    
    exit();

}


 
if ($action=="load_drop_down_working_location")
{
    echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
    exit();   
}


?>
