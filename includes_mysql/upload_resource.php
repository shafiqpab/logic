<?php
session_start();
$emp_code = $_SESSION['emp_code'];

function get_ext( $key ) {
	$key = strtolower( substr( strrchr( $key, "." ), 1 ) );
	$key = str_replace( "jpeg", "jpg", $key );
	return $key;
}

extract($_GET);

if( $resource_type == 'photo' ) {
	define( "MAX_SIZE", "400" );
	
	include('common.php');
	
	$time = time();
	
	$image = $_FILES["uploadfile"]["name"];
	$uploadedfile = $_FILES['uploadfile']['tmp_name'];
	
	if( $image ) {
		$filename = stripslashes( $_FILES['uploadfile']['name'] );
		$extension = get_ext( $_FILES["uploadfile"]["name"] );
		$extension = strtolower( $extension );
		if( !in_array( $extension, array( "jpg", "jpeg", "png", "bmp" ) ) ) {
			echo 'Unknown Image extension';
			$errors = 1;
		}
		else {
			$size = filesize( $_FILES['uploadfile']['tmp_name'] );
			if( $size > MAX_SIZE * 10 * 1024 ) {
				echo "You have exceeded the size limit";
				$errors = 1;
			}
			
			$uploadedfile = $_FILES['uploadfile']['tmp_name'];
			
			if( $extension == "jpg" || $extension == "jpeg" )	$src = imagecreatefromjpeg( $uploadedfile );
			else if( $extension == "png" )						$src = imagecreatefrompng( $uploadedfile );
			else												$src = imagecreatefromgif( $uploadedfile );
			
			list( $width, $height ) = getimagesize( $uploadedfile );
			$newwidth = $stretched_width;
			if( $width < $newwidth ) $newwidth = $width;
			
			$newheight = ( $height / $width ) * $newwidth;
			$stretched_tmp = imagecreatetruecolor( $newwidth, $newheight );
			
			$newwidth1 = $thumbnail_width;
			if( $width < $newwidth1 ) $newwidth1 = $width;
			
			$newheight1 = ( $height / $width ) * $newwidth1;
			$thumbnail_tmp = imagecreatetruecolor( $newwidth1, $newheight1 );
			
			imagecopyresampled( $stretched_tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			imagecopyresampled( $thumbnail_tmp, $src, 0, 0, 0, 0, $newwidth1, $newheight1, $width, $height );
			
			$id_field_name = "id";
			$table_name = "resource_photo";
			
			$stretched_id = return_next_id( $id_field_name, $table_name );
			$stretched_dir = '../resources/photo/';
			$stretched_file_name = "$module" . "_" . "$emp_code" . "_" . "$category" . "_" . "$stretched_id" . ".$extension";
			$stretched_file_location = "$stretched_dir$stretched_file_name";
			$stretched_file_db_location = "resources/photo/" . "$stretched_file_name";
			
			if( move_uploaded_file( $_FILES['uploadfile']['tmp_name'], $stretched_file_location ) ) {
				$stretched_sql = "INSERT INTO resource_photo ( id, module, identifier, category, type, location ) VALUES ( '$stretched_id', '$module', '$emp_code', '$category', 0, '$stretched_file_db_location' )";
				mysql_query( $stretched_sql ) or die( $stretched_sql . "<br />" . mysql_error() );
				
				$thumbnail_id = return_next_id( $id_field_name, $table_name );
				$thumbnail_dir = '../resources/photo/thumbnail/';
				$thumbnail_file_name = "$module" . "_" . "$emp_code" . "_" . "$category" . "_" . "$thumbnail_id" . ".$extension";
				$thumbnail_file_location = "$thumbnail_dir$thumbnail_file_name";
				$thumbnail_file_db_location = "resources/photo/thumbnail/" . "$thumbnail_file_name";
				
				move_uploaded_file( $_FILES['uploadfile']['tmp_name'], $thumbnail_file_location );
				$thumbnail_sql = "INSERT INTO resource_photo ( id, module, identifier, category, type, location ) VALUES ( '$thumbnail_id', '$module', '$emp_code', '$category', 1, '$thumbnail_file_db_location' )";
				mysql_query( $thumbnail_sql ) or die( $thumbnail_sql . "<br />" . mysql_error() );
				
				imagejpeg( $stretched_tmp, $stretched_file_location, 100 );
				imagejpeg( $thumbnail_tmp, $thumbnail_file_location, 100 );
				imagedestroy( $src );
				imagedestroy( $thumbnail_tmp );
				echo $thumbnail_file_db_location;
			}
		}
	}
}
?>