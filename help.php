<?
//$db_type=0; ------------$db_type=(0=Mysql,1=mssql,2=oracle);
/*
function csf($data)	->csf function is used for change the field name(upper case or lower case) based on database.

function return_field_value($field_name, $table_name, $query_cond)  

->This function return the field value from the table based on table and condition parameter.
->This function will Return Single or Multiple field value 
->concated with seperator having only one row result
->Return value:  query result as filed value
->Uses  single field:: return_field_value("buyer_name", "lib_buyer", "id=1");
->Uses  multi field:: return_field_value("concate(buyer_name,'_',contact_person)", "lib_buyer", "id=1"); do not use concat

function return_next_id( $field_name, $table_name, $increment=0 )   

->This function return the last row ID of the table based on table parameter. 
->This function will Return Last number of Row of table 
->To generate next Id 
->Return value:  number
->Uses  single field:: return_next_id("id", "lib_buyer", "1");

function is_duplicate_field( $field_name, $table_name, $query_cond )

->This function will Return Last number of Row of table 
->To generate next Id   
->Return value:  true false
->Uses  single field:: is_duplicate_field("buyer", "lib_buyer", "buyer_name like 'eta'");

function split_string($main_string, $fld_width, $position) 

->This function will return space seperated string of a 
->joint long string to fit a html table row and column
->uses  --> echo split_string($main_string, 25)

function get_button_level_permission($permission)

->This Function will Return a String with Permission Definition 
->Uses : echo get_button_level_permission($permission);

function change_date_format($date, $new_format, $new_sep)

->This function will return newly formatted date String
->uses  --> echo change_date_format($date,"dd-mm-yyyy","/")

function add_time($event_time,$event_length)

->This function will return new time after adding a given value with a given time
->Here $event_time= Time ,$event_length= integer Minutes
->uses  --> add_time($event_time,50)

function encrypt( $string )

->Retrun String after Ecryption
->Here $string= Given Text to be encrypted

function decrypt( $string )

->Retrun String after decryption
->Here $string= Given encrypted Text to be decryption 

function GetDays( $sStartDate, $sEndDate )

->Retrun array of days 

function datediff( $interval, $datefrom, $dateto, $using_timestamps = false )

->This function will return Date difference between two Date
-> interval: day or month or year or ........

function number_to_words($number, $full_unit, $half_unit)

->This function returns amount in word
->uses :: echo number_to_words("55555555250", "USD", "CENTS");

function return_mrr_number( $company, $location, $category, $year, $num_length, $main_query, $str_fld_name, $num_fld_name, $old_mrr_no )

// This function will Return Last number of Row of table 
// To generate next Id 
//Return value:  number
// Uses  single field:: return_next_id("id", "lib_buyer", "1");
//"select string_fld, num_fld from tbl where company and location and param order by num_fld desc limit 1";


function return_library_array( $query, $id_fld_name, $data_fld_name  )

// This function will Return a array which hold some value like company name, buyer name
//This function carry 3 parameters: query, id and field name 
//This function return all value from a specific table.

function return_library_autocomplete( $query, $data_fld_name  )

// This function will Return a array which hold some value like company name, buyer name
//This function carry 3 parameters: query, id and field name 
//This function return all value from a specific table.




 
*/




?>

<script>
function set_button_status(is_update, permission, submit_func,btn_id)
//is_update=0 for save,
//is_update=1 for updade, Delete
//permission= this is for checking button level permision for user, I.E if a user has save permision update permision delete permision or not. This permision has taken in the top of every page in permision variable.so programmers  need to pass the variable.
//submit_func= function name for form submision 
//btn_id= button index of a page,If a page have two form then for the first form pass 1 and for the 2nd form pass 2.
//example:set_button_status(0, permission, 'fnc_marchant_team_info',1);

 function get_submitted_variables( flds )
//flds='submited field id'
//example :get_submitted_variables('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info')

 function get_submitted_data_string( flds, path )
 //flds='submited field id'
 //path=controller  path
 //example:get_submitted_data_string('txt_team_name*txt_team_leader_name*txt_team_leader_desig*cbo_team_status*txt_team_leader_email*update_id*id_lib_mkt_team_member_info',"../../")
 function change_date_format(date, path, new_format, new_sep)
 //date=2012-01-01
//path=controller path
//new_format=dd/mm/yy
//new_sep=/
reset_form( forms,  divs, fields );
		// forms=""
		// divs=""
		// divs=""

</script>