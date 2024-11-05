<?
/*-------------------------------------------- Comments
Purpose			: 	Barcode Issue To Finishing		
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	09-03-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

	require_once('../includes/common.php');
	extract($_REQUEST);

	$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Finish Barcode Generate", "../", 1, 1, $unicode, '', '');
	?>
<script>

		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
		var permission = '<? echo $permission; ?>';

        function generate_report(type){
           
            var working_company_id=document.getElementById('working_company_id').value;
            var wc_location_id=document.getElementById('wc_location_id').value;
            var lc_company_id=document.getElementById('lc_company_id').value;
            var lc_location_id=document.getElementById('lc_location_id').value;
            var wc_floor=document.getElementById('wc_floor').value;
           
            var txt_line_no_hidden=document.getElementById('txt_line_no_hidden').value;
            var txt_date_from=document.getElementById('txt_date_from').value;
            var txt_date_to=document.getElementById('txt_date_to').value;
           if ( form_validation('working_company_id*wc_location_id','Working Company Name*Working company location')==false )
            {
                return;
            }
           
            
            var data='action=generate_report&type='+type+get_submitted_data_string('working_company_id*wc_location_id*lc_company_id*lc_location_id*txt_date_from*txt_date_to*wc_floor*txt_line_no_hidden*txt_line_no','../');
           // alert(data);
            //return;
            freeze_window(5);
            http.open("POST","requires/finish_barcode_generate_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_show_details_reponse;  
        }
        function generate_barcode(po_break_down_id,challan_no,country_id,company_id,location_id,floor_id,size_id,color_type_id,color_id,item_id,line_id,production_date,production_hour,qnty,i){
          
            var working_company_id=document.getElementById('working_company_id').value;
            var wc_location_id=document.getElementById('wc_location_id').value;
            var lc_company_id=document.getElementById('lc_company_id').value;
            var lc_location_id=document.getElementById('lc_location_id').value;
            var wc_floor=document.getElementById('wc_floor').value;
           
            var txt_line_no_hidden=document.getElementById('txt_line_no_hidden').value;
            var txt_date_from=document.getElementById('txt_date_from').value;
            var txt_date_to=document.getElementById('txt_date_to').value;
          // alert(txt_date_to);
          var data= 'action=generate_barcode&type=1&working_company_id='+working_company_id+'&wc_location_id='+wc_location_id+'&lc_company_id='+lc_company_id+'&lc_location_id='+lc_location_id+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&wc_floor='+wc_floor+'&txt_line_no_hidden='+txt_line_no_hidden+'&po_break_down_id='+po_break_down_id+'&challan_no='+challan_no+'&country_id='+country_id+'&company_id='+company_id+'&location_id='+location_id+'&floor_id='+floor_id+'&size_id='+size_id+'&color_type_id='+color_type_id+'&color_id='+color_id+'&item_id='+item_id+'&line_id='+line_id+'&production_date='+production_date+'&production_hour='+production_hour+'&qnty='+qnty+'&g_id='+i+'';
           // alert(data);
            //return;
            freeze_window(5);
            http.open("POST","requires/finish_barcode_generate_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_reponse;  
            return;
        }

        function view_barcode(po_break_down_id,challan_no,country_id,company_id,location_id,floor_id,size_id,color_type_id,color_id,item_id,line_id,production_date,production_hour){
            
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_barcode_generate_controller.php?'+'&po_break_down_id='+po_break_down_id+'&challan_no='+challan_no+'&country_id='+country_id+'&company_id='+company_id+'&location_id='+location_id+'&floor_id='+floor_id+'&size_id='+size_id+'&color_type_id='+color_type_id+'&color_id='+color_id+'&item_id='+item_id+'&line_id='+line_id+'&production_date='+production_date+'&production_hour='+production_hour+'&action='+'view_barcode', "View Barcode", 'width=670px,height=400px,center=1,resize=0,scrolling=0','../');
        }
        function fnc_show_details_reponse(){
            if(http.readyState == 4) 
            {
                var response=trim(http.responseText);
                //alert(response);
                $('#report_container').html(response);
                set_all_onclick();
                show_msg('18');
                release_freezing();
                
               
            }
        }
        function generate_reponse()
        {
            if(http.readyState == 4) 
            {
               
                //console.log(http.responseText);

                release_freezing();
                //return;
                var response = trim(http.responseText).split('**');
                show_msg(response[0]);
                if(response[0]==0){
                    
                    document.getElementById("generate_"+response[1]).classList.add("formbutton_disabled");
                   
                    document.getElementById("view_"+response[1]).classList.remove("formbutton_disabled");
                    document.getElementById("view_"+response[1]).classList.add("formbutton");
                    document.getElementById("view_"+response[1]).disabled = false; 
                }
               
                set_all_onclick();
                
                
               
            }
        }
        

        function openmypage(page_link,title)
        {
          
                emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=370px,center=1,resize=0,scrolling=0','')

                emailwindow.onclose=function()
                {
                    var theform=this.contentDoc.forms[0];

                    var txt_line_no=this.contentDoc.getElementById("txt_line_no1").value;//po id
                    var txt_line_no_hidden=this.contentDoc.getElementById("txt_line_no_hidden1").value;//po id
                   
                    if(txt_line_no!="" && txt_line_no_hidden!=""){
                        $("#txt_line_no").val(txt_line_no);
                        $("#txt_line_no_hidden").val(txt_line_no_hidden);
                    }
                    
                  
                    release_freezing();
                   
                }
                
        
        }

</script>
</head>


<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",''); ?>
         <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:1220px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1320px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th>Working Company</th>
                            <th>WC. Location</th>
                            <th>LC Company</th>
                            <th>LC Location</th>
                            <th>Floor</th>
                            <th>Line No</th>
                           
                            <th class="must_entry_caption">Prod. Date</th>
                            <th>
                                <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" />

                            </th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                 <td>
                                  <?
                                    echo create_drop_down("working_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/finish_barcode_generate_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/finish_barcode_generate_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )", 0);//
                                    ?>
                                </td>
                               <td id="wc_location_td">
                                    <?
                                    echo create_drop_down("wc_location_id", 130, "select id, location_name from lib_location", "id,location_name", 1, "--Select--", 0, "", 1);
                                    ?>
                                </td>

                                <td> 
                                     <?
                                        echo create_drop_down("lc_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by com.company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/finish_barcode_generate_controller', this.value, 'load_drop_down_lc_company_location', 'lc_location_td' );", 0);//
                                        ?>
                                </td>
                               
                                <td id="lc_location_td">
                                    <?
                                        $arr=array();
                                     echo create_drop_down("lc_location_id", 130, $arr, "", 1, "-- Select Location --", 0, "", 1);
                                    ?>
                                        
                                </td>
                                <td id="wc_floor_td">
                                     <?
                                        $arr=array();
                                        echo create_drop_down("wc_floor", 130, $arr, "", 1, "-- Select Floor --", 0, "", 1);
                                    ?>
                                </td>
                               
                               
                               <td>
                                   <input type="text" name="txt_line_no" class="text_boxes" id="txt_line_no" placeholder="Line no" onDblClick="openmypage('requires/finish_barcode_generate_controller.php?action=line_popup','Line Search');setFilterGrid('list_view_line',-1)">
                                   <input type="hidden" name="txt_line_no_hidden" id="txt_line_no_hidden">
                               </td>
                              
                               
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="generate_report(1)" />
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <table>
                <tr>
                    <td colspan="9">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
            <br />
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
        </form>
    </div>
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>