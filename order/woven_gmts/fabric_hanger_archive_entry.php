<?
    /****************************************************************
    |   Purpose         :   This Entry page for Fabric Hanger Archive Entry
    |   Functionality   :   
    |   JS Functions    :
    |   Created by      :   MD. SAKIBUL ISLAM
    |   Creation date   :   30 MAY, 2023
    |   Updated by      :            
    |   Update date     :   
    |   QC Performed BY :       
    |   QC Date         :   
    |   Comments        :
    ******************************************************************/
    session_start();
    if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
    require_once('../../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission']=$permission;
    //----------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("Fabric Hanger Archive Entry", "../../", 1, 1,$unicode,1,'');
 
?>

        <script>
            if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
                    var permission='<? echo $permission; ?>';
            function fabric_hanger_archive_info( operation )
            {
                if (form_validation('cbo_company_name*txt_dispo_no*txt_fab_type_1*txt_finish_width*txtconstruction_1*txtcomposition_1*cboFinishType_1*cboSample_ref_types_1','Insert Company Name*Dispo no*Fabric Type*Finish Width*Construction*Composition*Finish Type*Sample Ref Type')==false)
                {
                    return;
                }
                else
                {
                    var fabric_gsm=$("#txt_fabric_gsm").val();
                    var fabric_ounce=$("#txt_fabric_ounce").val();
                    if(fabric_gsm=="" && fabric_ounce==""){
                        alert("Plz Add value in Fabric GSM or Fabric Ounce");
                        return;
                    }

                    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_name*cbo_buyer_name*txt_dispo_no*date_field*cbo_location_name*txt_fab_type_1*txt_finish_width*txtconstruction_1*yarnCountDeterminationId_1*txtcomposition_1*txt_fabric_gsm*cboFinishType_1*cboWashType_1*cboPrintType_1*cbo_floor_id*cbo_room*txt_rack*txt_shelf*txt_bin*cboSample_ref_types_1*cbo_status*update_id*txt_fabric_ounce*txt_article_no',"../../");
                    
                    freeze_window(operation);
                    http.open("POST","requires/fabric_hanger_archive_entry_controller.php",true);
                    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    http.send(data);
                    http.onreadystatechange = fabric_hanger_archive_info_response;
                }
            }
            function fabric_hanger_archive_info_response()
            {
                if(http.readyState == 4) 
                {
                    //release_freezing(); return;
                    var reponse=trim(http.responseText).split('**');
                    release_freezing();
                    show_msg(reponse[0]);
                    if (reponse[0] == 0) 
                    {
                        $("#update_id").val(reponse[1]);
				        $("#txt_system_id").val(reponse[2]);
                        //alert( reponse[2]);
                        set_button_status(1, permission, 'fabric_hanger_archive_info',0);
                        show_list_view(reponse[1],'list_view_div','list_view_div','requires/fabric_hanger_archive_entry_controller','setFilterGrid("list_view",-1)');
                        reset_form('fabric_hanger_archive_1','','');
                        release_freezing();
                        
                    }
                    else if (reponse[0] == 1) 
                    {
                        //$("#update_id").val(reponse[1]);
                        //alert( reponse[1]);
                        set_button_status(0, permission, 'fabric_hanger_archive_info',1,1);
                        show_list_view(reponse[1],'list_view_div','list_view_div','requires/fabric_hanger_archive_entry_controller','setFilterGrid("list_view",-1)');
                        //reset_form('fabric_hanger_archive_1','','');
                        release_freezing();
                        
                    }
                    else
                    {
                        if (reponse[0].length>2) reponse[0]=10;
                        show_msg(reponse[0]);
                       // show_list_view(reponse[1],'list_view_div','list_view_div','requires/fabric_hanger_archive_entry_controller','setFilterGrid("list_view",-1)');
                        reset_form('fabric_hanger_archive_1','','');
                        set_button_status(0, permission, 'fabric_hanger_archive_info',1);
                       // alert ("dfk");
                        release_freezing();
                    }
                    release_freezing();
                }
            }
            function open_fabric_decription_popup()
            {
                var cbo_company_id=document.getElementById('cbo_company_name').value;
                var page_link='requires/fabric_hanger_archive_entry_controller.php?action=fabric_description_popup&cbo_company_id='+cbo_company_id;
                emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Construction Popup', 'width=500px,height=350px,center=1,resize=1,scrolling=0','../');
                emailwindow.onclose=function()
                {
                    var mrrNumber=this.contentDoc.getElementById("id").value; 
                    get_php_form_data(mrrNumber, "data_to_form_new", "requires/fabric_hanger_archive_entry_controller");
                    //release_freezing();
                }
            }

            
            function fnc_print_report()
            {
                
                var data = $("#update_id").val()+'**'+ $("#txt_system_id").val();
                var report_title=$( "div.form_caption" ).html();                
                var action="fabric_print_button";
                
                freeze_window(operation);
                http.open( 'POST', 'requires/fabric_hanger_archive_entry_controller.php?action='+action+'&data='+ data );
                http.onreadystatechange = response_fabric_data;
                http.send(null);
            }
            function response_fabric_data()
            {
                if(http.readyState == 4)
                {
                    var response = http.responseText.split('###');
                    // alert(response.join('=='));return;
                    window.open(response[0], '', '');
                    release_freezing();
                }
            }

            function open_mrrpopup()
            {
                //reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');

                if( form_validation('cbo_company_name','Company Name')==false )
                {
                    return;
                }
                var buyer_id = $("#cbo_buyer_name").val();
                var company = $("#cbo_company_name").val();
                var page_link='requires/fabric_hanger_archive_entry_controller.php?action=mrr_popup&company='+company;
                var title="Search  Popup";
                emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=400px,center=1,resize=0,scrolling=0','../')
                emailwindow.onclose=function()
                {
                    var theform=this.contentDoc.forms[0];

                    var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
                    mrrNumber = mrrNumber.split("_");
                    //var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number

                    $("#txt_system_id").val(mrrNumber[0]);
                    $("#update_id").val(mrrNumber[1]);

                    get_php_form_data(mrrNumber[1], "load_php_data_to_form", "requires/fabric_hanger_archive_entry_controller");

                    show_list_view(mrrNumber[1],'list_view_div','list_view_div','requires/fabric_hanger_archive_entry_controller','setFilterGrid("list_view",-1)');

                    set_button_status(1, permission, 'fabric_hanger_archive_info',1);
                }
            }
            function fnc_sticker_print(type)
            {
                if(type==1){
                    var data = $("#update_id").val()+'***'+ $("#txt_system_id").val();
                    //alert(system_id);
                    var report_title=$( "div.form_caption" ).html();
                  //  print_report(update_id, "fabric_hanger_sticker_print", "requires/fabric_hanger_archive_entry_controller" );
                    var action="fabric_hanger_sticker_print";
                 //  var url=return_ajax_request_value(data, action, "requires/fabric_hanger_archive_entry_controller");
				//window.open(url,"###");
                freeze_window(operation);
                http.open( 'POST', 'requires/fabric_hanger_archive_entry_controller.php?action='+action+'&data='+ data );

                http.onreadystatechange = response_pdf_data;
                http.send(null);
                }   
                
            }
            function response_pdf_data()
            {
                if(http.readyState == 4)
                {
                    var response = http.responseText.split('###');
                    // alert(response.join('=='));return;
                    window.open('requires/'+response[1], '', '');
                    release_freezing();
                }
            }
            function load_floor()
            {

                var company_name= $("#cbo_company_name").val();
                var location_name= $("#cbo_location_name").val();
                //var floor_id= $("#cbo_location_name").val();
                var data_str=company_name+'_'+location_name;

                load_drop_down( 'requires/fabric_hanger_archive_entry_controller', data_str, 'load_drop_down_floor', 'floor_td' );

            }
        // function fnc_load_room_rack_shelf_bin(type_id)
        // {
        //    var company_name= $("#cbo_company_name").val();
        //    var location_name= $("#cbo_location_name").val();
        //    var cbo_floor= $("#cbo_floor_id").val()*1;
        //    var data_str=company_name+'_'+location_name+'_'+cbo_floor;
        //    alert(data_str);
        //    if(type_id==1) //Room
        //    {
        //     //load_drop_down( 'requires/fabric_hanger_archive_entry_controller', company_name+'_'+location_name+'_'+this.id, 'load_drop_down_room', 'room_td' );
        //    }
        //    if(type_id==2) //shelf
        //    {
        //    // load_drop_down( 'requires/fabric_hanger_archive_entry_controller', data_str, 'load_drop_down_shelf', 'shelf_td' );
        //    }


          
        //   // load_drop_down( 'requires/fabric_hanger_archive_entry_controller', data_str, 'load_drop_down_shelf', 'shelf_td' );
        //   // load_drop_down( 'requires/fabric_hanger_archive_entry_controller', data_str, 'load_drop_down_rack', 'rack_td' );
        //   // load_drop_down( 'requires/fabric_hanger_archive_entry_controller', data_str, 'load_drop_down_bin', 'bin_td' );
        // }

        </script>
    </head>	
    <body onLoad="set_hotkey()">
        <div align="centre" style="width:1200px;">
            <? echo load_freeze_divs ("../../",$permission);  ?>
            <form name="excelImport_1" id="excelImport_1" action="fabric_hanger_archive_excel_import.php" enctype="multipart/form-data" method="post">
                <table cellpadding="0" cellspacing="1" width="1200px" style="padding-left: 5px; padding-right: 5px;">
                    <tr>
                        <td width="200" align="left"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" /></td>
                        <td width="200" align="left"><input type="submit" name="submit" value="Excel File Upload" class="formbutton" style="width:110px" /></td>              
                        <td width="540" align="right"><a href="../../excel_format/FHAE.xls"><input type="button" value="Excel Format Download" name="excel" id="excel" class="formbutton" style="width:150px"/></a></td>
                    </tr>
                </table>
            </form>
            <fieldset style="width: 1200;">
                <legend>Fabric Hanger Archive Entry</legend>
                <form name="fabric_hanger_archive_1" id="fabric_hanger_archive_1" autocomplete="off">	
                    <table cellpadding="0" cellspacing="1" border="0" width="1200">
                        <tr>   
                            <td align="right" colspan=""  class="must_entry_caption">System ID</td>
                            <td style="width: 673px;">
                                <input style="width:95px;" type="text" title="Double Click to Search" onDblClick="open_mrrpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                                    <input type="hidden" name="update_id" id="update_id" />
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="1" border="0" width="1200">
                        <tr>
                            <td class="must_entry_caption" align="left">Company Name</td>
                            <td style="width: 100px;">
                                <? 
                                    $com_sql = "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by company_name";
                                    echo create_drop_down( "cbo_company_name", 105,$com_sql,"id,company_name", 1, "Select Company", $selected,"load_drop_down( 'requires/fabric_hanger_archive_entry_controller', this.value, 'load_drop_down_location', 'location' ); load_drop_down( 'requires/fabric_hanger_archive_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/fabric_hanger_archive_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ,0);
                                ?>
                            </td>
                            <td align="left">Buyer</td>
                            <td id="buyer_td" style="width: 100px;">
                        	    <? echo create_drop_down( "cbo_buyer_name", 100, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" ,0);?>
                            </td>
                            <td align="left" class="must_entry_caption">Dispo No</td>
                            <td style="width: 100px;">
                                <input type="text" name="txt_dispo_no" id="txt_dispo_no" class="text_boxes" style="width:90px"  maxlength="100" title="Maximum 100 Character"/>					
                            </td>
                            <td align="left">Date</td>
                            <td style="width: 100px;">
                                <input type="text" style="width:90px" class="datepicker" placeholder="Select Date"  name="date_field" id="date_field"/>						
                            </td>
                            <td align="left">Location</td>
                            <td id="location" style="width: 100px;"><?=create_drop_down( "cbo_location_name", 100, $blank_array,"", 1, "--Select--", 0, "" ); ?></td>
                            <td align="left" class="must_entry_caption" >Fabric Type</td>
                            <td style="width: 100px;">
                                <input type="text" id="txt_fab_type_1"  name="txt_fab_type_1" class="text_boxes" style="width:90px" placeholder="Display" readonly value="" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption" align="left">Finish Width</td>
                            <td style="width: 100px;">
                                <input type="text" name="txt_finish_width" id="txt_finish_width" class="text_boxes" style="width:90px" maxlength="100" title="Maximum 100 Character" /><input type="hidden" name="supplier_hidden_id" id="supplier_hidden_id" class="text_boxes" /> 
                            </td>
                            <td class="must_entry_caption" align="left">Fab Construction</td>
                            <td >
                					<input type="hidden" id="yarnCountDeterminationId_1"  name="yarnCountDeterminationId_1"  value="" />
                                    <input type="text" id="txtconstruction_1" name="txtconstruction_1" class="text_boxes" style="width:90px" onDblClick="open_fabric_decription_popup();" placeholder="Browse" readonly title="" />
                            </td>
                            <td align="left" class="must_entry_caption">Fab. Composition</td>
                            <td >
                                <input type="text" id="txtcomposition_1"  name="txtcomposition_1"  class="text_boxes" style="width:90px" value="" readonly placeholder="Display"/>
                                <input type="hidden" id="cbocomposition_1"  name="cbocomposition_1" class="text_boxes" style="width:90px" value="" />
                            </td>
                            <td  align="left" class="must_entry_caption">Fabric GSM</td>
                            <td >
                                <input type="text" name="txt_fabric_gsm" id="txt_fabric_gsm" class="text_boxes" style="width:90px"  maxlength="100" title="Maximum 100 Character"/>						
                            </td>
                            <td align="left" class="must_entry_caption">Finish Type</td>
                            <td >
								<? 
									$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
									echo create_drop_down("cboFinishType_1", 100, $finish_types, "", 1, "--Select--", 0, "");
								?>
										
                            <td align="left">Wash Type</td>
                            <td >
                                <? 
									$wash_types = $emblishment_wash_type;
									echo create_drop_down("cboWashType_1", 100, $wash_types, "", 1, "--Select--", 0, "");
								?>						
                            </td>
                        </tr>	
                        <tr>
                            <td align="left">Print Type</td>
                            <td style="width: 100px;">
                                <? 
									$print_types = $emblishment_print_type;
									echo create_drop_down("cboPrintType_1", 100, $print_types, "", 1, "--Select--", 0, "");
								?>	
                            </td>
                            <td align="left">Floor</td>
                            <td id="floor_td">
								<? echo create_drop_down( "cbo_floor_id", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
							</td>
                            <td align="left">Room</td>
                            <td id="room_td">
								<? echo create_drop_down( "cbo_room", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
							</td>
                            <td align="left">Rack</td>
                            <td id="rack_td">
								<? echo create_drop_down( "txt_rack", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
							</td>
                            <td align="left">Shelf</td>
                            <td id="shelf_td">
								<? echo create_drop_down( "txt_shelf", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
							</td>
                            <td align="left">Bin</td>
                            <td id="bin_td">
								<? echo create_drop_down( "txt_bin", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
							</td>
                        </tr>	
                        <tr>
                            <td class="must_entry_caption" align="left">Sample Ref Type </td>
                            <td style="width: 100px;">
                                    <? 
										$sample_ref_types = array(1=>"SSM-Yarn Dyed Sample",2=>"SSD-Solid Dyed Sample",3=>"SSR-Rotary Print Sample",4=>"SSP-Digital Print Sample");
										echo create_drop_down("cboSample_ref_types_1", 100, $sample_ref_types, "", 1, "--Select--", 0, "");
									?> 
                            </td>
                            <td  align="left">Status </td>
                            <td >
                            <? echo create_drop_down("cbo_status", 100, $row_status, "", "", "", 0, "","","1,2,3"); ?>						
                            </td>
                            <td class="must_entry_caption">Fabric Ounce</td>
                            <td>
                                <input type="text" name="txt_fabric_ounce" id="txt_fabric_ounce" class="text_boxes" style="width:90px"   />	
                            </td>
                            <td>Article No.</td>
                            <td colspan="2">
                                <input type="text" name="txt_article_no" id="txt_article_no" class="text_boxes" style="width:200px"  />	
                            </td>
                            <td >
                                <input type="button" class="image_uploader" style="width:100px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'hand_loom_requisition', 2 ,1)">						
                            </td>
                           
                            <td >
                                 <input type="button" class="image_uploader" style="width:100px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'hand_loom_requisition', 0 ,1)">			
                            </td>
                        <tr>
                            <td colspan="12" align="center" height="20" valign="middle" class="button_container"> 
                            <? 
                                echo load_submit_buttons( $permission, "fabric_hanger_archive_info", 0,0 ,"reset_form('fabric_hanger_archive_1','','')",1);
                            ?> 
                              <input type="button" id="Print" value="Print"   class="formbutton" style="width:100px; margin-right: 80px;" onClick="fnc_print_report();" > 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="11" align="center" height="40" valign="middle" class="" style="padding-top: 0px; height:0px"> 
                           
                                <input type="button" name="btn_stickerprint" id="btn_stickerprint" class="formbuttonplasminus" style="width:150px; margin-left: 100px;" onClick="fnc_sticker_print(1);" value="Sticker Print">
                            </td>
                        </tr>	
                    </table>
                </form>
            </fieldset>	
            
            <div style="width:100%; float:left; margin:auto" align="center" id="search_container">
                <fieldset style="width:1104px; margin-top:10px">
                    <table width="1104" cellspacing="2" cellpadding="0" border="0">
                            
                            <tr>
                                <td colspan="3">
                                    <div id="list_view_div" name="list_view_div">
                                        <?
                                            // $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
                                            // $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
                                            // $row_status = array(1 => "Active", 2 => "InActive", 3 => "Cancelled");
                                            // $sql="select id,company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,floor_id,room,rack,shelf,sample_ref_type,system_number,status_active from wo_fabric_hanger_archive_mst where is_deleted=0";
                                            // $arr=array (0=>$company_array,1=>$buyer_name_arr,8=>$finish_types,9=> $wash_types,10=>$print_types,11=>$sample_ref_types,17=>$row_status); 
                                            // echo  create_list_view ( "list_view", "Company,Buyer,Dispo No,Fabric Type,Finish Width,Fab. Construction,Fab. Composition,Fabric GSM, Finish Type,Wash Type,Print Type,Sample Ref Type,Floor, Room, Rack,Shelf,SYS ID,Status", "120,100,70,100,70,80,120,60,70,63,63,120,63,63,63,63,100","1564","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,buyer_id,0,0,0,0,0,0,finish_type,wash_type,print_type,sample_ref_type,0,0,0,0,0,status_active", $arr , "company_id,buyer_id,dispo_no,fabric_type,finish_width,fab_construction,fab_composition,fabric_gsm,finish_type,wash_type,print_type,sample_ref_type,floor_id,room,rack,shelf,system_number,status_active", "../woven_gmts/requires/fabric_hanger_archive_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0');
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                </fieldset>	
            </div>
        </div>
    </body>

<script>


</script>

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>