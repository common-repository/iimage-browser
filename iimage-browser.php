<?php
/*
*	IImage Browser for Wordpress 1.2 and higher
*	Version 1.5.2
*	Copyright (C) 2004-2005 Martin Chlupac (malyfred - http://fredfred.net/skriker/)
*	see iimage-browser-plugin.php for license information
*
*	based on
*
 * Image Browser Plugin for Wordpress 1.2
 * Copyright (C) 2004 Florian Jung
 */


$standalone = 1;
// include the WP base functions.
if (file_exists('admin.php')) {
	require_once('../wp-config.php');
    require_once('admin.php');		// WP after 1.3 alpha-3
} else {
    require_once('admin-header.php');	// WP before 1.3 alpha-3 

}
 
 
// ====( configuration options )================================================
$ib_images_per_page = 30;  // display ... images per page in the browser
$ib_set_title = true;     //set 'title'-attribute for images
$thumb_prefix = '_';		//prefix of thumbnails


//path to the "upload" directory
//relative to iimage-browser.php
$ib_settings['real_path'] = './../wp-content/';

//URI of that directory relative to your blog installation URI
$ib_settings['real_url'] = '/wp-content';

$ib_settings['max_filesize'] = 8*1024;//in kB


$ib_settings['delete_user_level'] = 5;
$ib_settings['upload_user_level'] = 5;
$ib_settings['dir_user_level'] = 5;
$ib_settings['thumbnail_user_level'] = 5;
$ib_settings['use_iib_user_level'] = 5;	


$ib_settings['path_to_iib'] = $_SERVER['PHP_SELF'];// path to iimage-browser.php - edit this when $_SERVER['PHP_SELF'] returns strange value

$ib_settings['default_thumbnail_size'] = 250; //default thumbnail size

/**
You can use:
%src - path to fullimage
%tsrc - path to thumbnail if thumbnail exist
%title - description:o)
%width - width of fullimage (only for fullimage custom code)
%height - height of fullimage (only for fullimage custom code)
%twidth - width of thumbnail (only for thumbnail custom code)
%theight - height of thumbnail (only for thumbnail custom code)

*/

//example
//$ib_custom_code_full = '<a href="img.php?image=%src"><img src="%src" title="%title" alt="%title" width="%width" height="%height" /></a>';
//$ib_custom_code_thumb = '<a href="img.php?image=%src"><img src="%tsrc" title="%title" alt="%title" width="%twidth" height="%theight" /></a>'; 

$ib_custom_code_full = 'Must be set in iimage-browser.php';
$ib_custom_code_thumb = 'Must be set in iimage-browser.php';


// ====( STOP EDITING HERE )====================================================
$title = 'Select Image';  //page title


if(IsSet($_REQUEST['rel_path']))
	$rel_path = str_replace ('..', '',$_REQUEST['rel_path']); //default path - relative to the image directory
															//removing '..' is VERY simple protection
else
	$rel_path = '/';



$abs_path = get_bloginfo('wpurl').$ib_settings['real_url'];

$ib_settings['patterns'][] = '/\%src/i';
$ib_settings['patterns'][] = '/\%tsrc/i';
$ib_settings['patterns'][] = '/\%title/i';
$ib_settings['patterns'][] = '/\%width/i';
$ib_settings['patterns'][] = '/\%height/i';
$ib_settings['patterns'][] = '/\%twidth/i';
$ib_settings['patterns'][] = '/\%theight/i';

$ib_settings['replacements'][] = '"+imgid.abs_path.value + imgid.relpath.value + imgid.file.value+"';
$ib_settings['replacements'][] = '"+imgid.abs_path.value + imgid.relpath.value + imgid.thumbprefix.value + imgid.file.value+"';
$ib_settings['replacements'][] = '"+imgid.imgdesc.value+"';
$ib_settings['replacements'][] = '"+imgid.bigw.value+"';
$ib_settings['replacements'][] = '"+imgid.bigh.value+"';
$ib_settings['replacements'][] = '"+imgid.thumbw.value+"';
$ib_settings['replacements'][] = '"+imgid.thumbh.value+"';




// ====( print header )=========================================================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &rsaquo; <?php 
bloginfo('name') ?> &rsaquo; <?php echo $title; ?></title>
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../wp-images/wp-favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php 
	echo get_settings('blog_charset'); ?>" />
<script type="text/javascript">
<!--
window.focus();

//just small shortcut it could be el=document.getElementById as well, but...
function eId(id){
 return document.getElementById(id);
 }
 
function eNm(id){
	return document.getElementsByName(id)[0];
}
// modified version of a script from Alex King (http://www.alexking.org)
// the code did not support the AtCursor across multiple windows
function insertAtCursor(myField, myValue) {
    //IE support
    if (document.selection && !window.opera) {
	// only insert text for IE (not at cursor)
	myField.value += myValue;
    }
    //MOZILLA/NETSCAPE/OPERA support
    else if (myField.selectionStart || myField.selectionStart == '0') {
	var startPos = myField.selectionStart;
	var endPos = myField.selectionEnd;
	myField.value = myField.value.substring(0, startPos)
	    + myValue 
	    + myField.value.substring(endPos, myField.value.length);
    } else {
	myField.value += myValue;
    }
}

//shows or hides the form for getting code
function showHideForm(imgid){
	var foo = eId(imgid);
	if(foo.style.display == 'none'){
		foo.style.display = 'block';
		foo.imgdesc.focus();
		}
	else
		foo.style.display = 'none';
	

	}
	
//shows / hides upload form
function showHideButtonForm(which){

var foo = eId(which+"_hidden_part");
//var bar = eId("show_upload_button");

	if(foo.style.display == 'block')
	{
		foo.style.display = 'none';
		}
	else{
		foo.style.display = 'block';
		}
}

//disable button 'Add it to the post!' if the textarea is empty
function finalOnKeyUp(imgid){
	var foo = eId(imgid);
	if(foo.finalcode.value.length > 0)
		foo.add_it.disabled = false;
	else
		foo.add_it.disabled = true;
}

function getTheCode(imgid){
	var radio = imgid.imgselect;//array of radiobuttons
	var selectedRadio = '';
	var htmlCode = '';
	var imgtitle = '';

	//which code is selected
	for(i=0;i<radio.length;i++){
		if(radio[i].checked){
			selectedRadio = radio[i].value ;}
	}
	
	if(<?php echo $ib_set_title;?>)
		imgtitle = ' title="' + imgid.imgdesc.value + '"';
	
	//let's generate HTML code according to selected radiobutton

	switch(selectedRadio){
		case "thumbfull": 
			htmlCode = "<a href=\"" + imgid.abs_path.value + imgid.relpath.value + imgid.file.value + "\"><img src=\""+imgid.abs_path.value + imgid.relpath.value + imgid.thumbprefix.value + imgid.file.value +"\" width=\""+ imgid.thumbw.value +"\" height=\""+ imgid.thumbh.value +"\" alt=\""+ imgid.imgdesc.value +"\"" + imgtitle + "  /></a>";
			break;
		case "thumb": 
			htmlCode = "<img src=\""+imgid.abs_path.value + imgid.relpath.value + imgid.thumbprefix.value + imgid.file.value +"\" width=\""+ imgid.thumbw.value +"\" height=\""+ imgid.thumbh.value +"\" alt=\""+ imgid.imgdesc.value +"\"" + imgtitle + " />";
			break;
		case "full":
			htmlCode = "<img src=\""+imgid.abs_path.value + imgid.relpath.value + imgid.file.value +"\" width=\""+ imgid.bigw.value +"\" height=\""+ imgid.bigh.value +"\" alt=\""+ imgid.imgdesc.value +"\"" + imgtitle + " />";
			break;
		case "linkthumb": 
			htmlCode = "<a href=\"" + imgid.abs_path.value + imgid.relpath.value + imgid.thumbprefix.value + imgid.file.value + "\"" + imgtitle + ">"+imgid.thumbprefix.value + imgid.file.value +"</a>";
			break;
		case "linkfull": 
			htmlCode = "<a href=\"" + imgid.abs_path.value + imgid.relpath.value + imgid.file.value + "\"" + imgtitle + ">" + imgid.file.value +"</a>";
			break;
		case "customfull":
			htmlCode = "<?php echo preg_replace($ib_settings['patterns'],$ib_settings['replacements'],addcslashes ($ib_custom_code_full, '"\''));?>"
			break;
		case "customthumb":
			htmlCode = "<?php echo preg_replace($ib_settings['patterns'],$ib_settings['replacements'],addcslashes ($ib_custom_code_thumb, '"\''));?>"
			break;
		default:
			htmlCode = "Error: report to mail@fredfred.net";
			break;	
	}	
	
	imgid.add_it.disabled = false;
	imgid.finalcode.focus();
	return imgid.finalcode.value =  htmlCode;


}

//enable/disable "delete:" button
function deleteCheckBoxOnClick(imgid){
	
	if(imgid.delSmall != null){
		imgid.delete_it.disabled = !(imgid.delSmall.checked || imgid.delBig.checked);
	}
	else{
	    imgid.delete_it.disabled = !imgid.delete_it.disabled;
	}
	
	
}//end deleteCheckBoxOnClick


//confirmation of deleting
function confirmLink(button,imgid,action, question)
{
	if(imgid.delBig.checked)
		question += unescape(imgid.delBig.value) + '\n';
	if(imgid.delSmall && imgid.delSmall.checked)
		question += unescape(imgid.delSmall.value) + '\n';
		
    var is_confirmed = confirm(question);
    if (is_confirmed) {
        button.form.action += '&is_js_confirmed=1&action=' + action;
    }

	
    return is_confirmed;
}

//-->
</script>
<style type="text/css">
img { border: 0px; }
#ibwrapper { margin-left: 8px;
margin-right: 8px;
margin-bottom: 8px;
 padding: 0; }
#step { margin 0; padding: 8px; background: #f0f8ff; border-bottom: 1px solid #69c; }
.image { margin: 0 0 12px 0; padding: 4px; 
	 background: #eeeeee; border: 1px solid #cccccc; 
}
.image .title { font-size: 1em; padding:0; margin: 0 0 8px 0; }
.highlight { color: #ff0000; 
}
/*.infoform { margin: 6px 0 12px 0; text-align: left; }
.select { margin: 6px 0 0 0; text-align: left; }*/

.hidden_part {
	display:none;
}
</style>
</head>
<body>
<?php

if ($user_level < $ib_settings['use_iib_user_level']) //Checks to see if user has logged in
    die (__("Your 'user level' doesn't allow you to use IImage Browser.").'</body></html>');


// ====( functions )============================================================

/**
 * get some useful infos for a file 
 *
 * @param  string $filename name of the file with path on disk (not url)
 * @return mixed FALSE if not found, else $meta for html and code creation
 */
function ib_get_fileinfo($file) {
    if (!is_file($file)) {
	return FALSE;
    }
    
    $meta = array();
    $meta['name'] = basename($file);

    // filesize
    if (FALSE !== ($temp = @filesize($file))) {
	$meta['filesize'] = $temp;
    }
    
    // filemtime (last modified)
    if (FALSE !== ($temp = @filemtime($file))) {
	$meta['mtime'] = $temp;
    }

    // image size (if possible)
    if (FALSE !== ($temp = @getimagesize($file))) {
	$meta['width'] = $temp[0];
	$meta['height'] = $temp[1];
	$meta['img'] = $temp[3];
    }
    
    return $meta;
} // ib_get_fileinfo


/**
* enhanced copy of wp_create_thumbnail()
*
*/
function iimage_create_thumbnail($file, $max_side, $effect = '', $method = 1, $quality = 80) {//method ... resampled or resized?
global $thumb_prefix;
    // 1 = GIF, 2 = JPEG, 3 = PNG

    if(file_exists($file)) {
        $type = getimagesize($file);
        
        // if the associated function doesn't exist - then it's not
        // handle. duh. i hope.
        
        if(!function_exists('imagegif') && $type[2] == 1) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagejpeg') && $type[2] == 2) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagepng') && $type[2] == 3) {
            $error = __('Filetype not supported. Thumbnail not created.');
        } else {
        
            // create the initial copy from the original file
            if($type[2] == 1) {
                $image = imagecreatefromgif($file);
            } elseif($type[2] == 2) {
                $image = imagecreatefromjpeg($file);
            } elseif($type[2] == 3) {
                $image = imagecreatefrompng($file);
            }
            
			if (function_exists('imageantialias'))
	            imageantialias($image, TRUE);
            
            $image_attr = getimagesize($file);
            
            // figure out the longest side
            
            if($image_attr[0] > $image_attr[1]) {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_width = $max_side;
                
                $image_ratio = $image_width/$image_new_width;
                $image_new_height = $image_height/$image_ratio;
                //width is > height
            } else {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_height = $max_side;
                
                $image_ratio = $image_height/$image_new_height;
                $image_new_width = $image_width/$image_ratio;
                //height > width
            }
            
            $thumbnail = imagecreatetruecolor($image_new_width, $image_new_height);
			if( function_exists('imagecopyresampled') && $method == 1 ){
				@imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
				}
			else{
            	@imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
				}
            
            // move the thumbnail to it's final destination
            
            $path = explode('/', $file);
            $thumbpath = substr($file, 0, strrpos($file, '/')) . "/{$thumb_prefix}" . $path[count($path)-1];
            
			if(file_exists($thumbpath))
				return sprintf(__("The filename '%s' already exists!"), $thumb_prefix.$path[count($path)-1]);
			
            if($type[2] == 1) {
                if(!imagegif($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 2) {
//			echo $quality;
                if(!imagejpeg($thumbnail, $thumbpath,$quality)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 3) {
                if(!imagepng($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            }
            
        }
    }
    
    if(!empty($error))
    {
        return $error;
    }
    else
    {
		chmod($thumbpath, 0666);
        return 1;
    }
}
/** 
 * recognize the thumbnails by filename
 *
 * The thumbnails have the same names like the fullsize version
 * with a 'thumb-' added as prefix. Data of recognized thumbs is
 * added to the $meta of the fullsized image as $meta['thumb']
 *
 * @param array $files hash of filenames with metadata
 * @return array thumb-data is now under $files[$fullsize]['thumb']
 */
function ib_recognize_thumbs($files) {
    
	GLOBAL $thumb_prefix;
	
    //fred
    foreach ($files as $file => $meta) {
	if (0 !== strpos($file, $thumb_prefix)) 
	    continue; // not starting with thumb_prefix

	// Check for a fullsize version
	$fullsize = substr($file,strlen($thumb_prefix));	
	if(isset($files[$fullsize])) {
	    // it actually *is* a thumb, so delete it from $files
	    unset($files[$file]); 
	    // ... add meta as 'thumb' to the fullsize version
	    $files[$fullsize]['thumb'] = $meta;
	    // ... and set mtime to the newest of both
	    if ($files[$fullsize]['mtime'] < $meta['mtime'])
		$files[$fullsize]['mtime'] = $meta['mtime'];
	}
    }
    
    return $files;
} // end ib_recognize_thumbs

/**
Creates the directory navigation

*/
function dir_menu(){
	global $rel_path,$dirs;
	if($dirs != null)
		sort($dirs);
//------ navigation for Dirs
	$dir_menu = "<a href=\"?rel_path=/\">[root]</a> /";
	$tok = strtok ($rel_path,"/");
	$rel_path_tok = "/$tok/";
	while ($tok) {
	    $dir_menu .= "<a href=\"?rel_path={$rel_path_tok}\">{$tok}</a>/";
	    $tok = strtok ("/");
		$rel_path_tok .="{$tok}/";
	}
	
	$dir_menu .= '&gt;';
	
	for($i=0;$i<count($dirs);$i++){
		$dir_menu .= "<a href=\"?rel_path={$rel_path}{$dirs[$i]}/\">".$dirs[$i].'</a> | ';
	}
	return '<div>'.$dir_menu.'</div>';
	//-------------------
}

/**
* Crates upload form
*
*/
function hidden_menu(){
	global $rel_path, $ib_settings;
	
	$t = "<div id=\"hidden_menu\">

		<div id=\"create_dir_hidden_part\" class=\"hidden_part\">
		<hr />
		<form action=\"{$ib_settings['path_to_iib']}?rel_path=$rel_path&amp;action=createdir\" method=\"post\" enctype=\"multipart/form-data\">
		Name of directory: <input type=\"text\" size=\"30\" name=\"ib_name_of_dir\" /><input type=\"submit\" value=\"Create!\" />
		</form>
		</div>
	
	
		<div id=\"upload_hidden_part\" class=\"hidden_part\">
			<form action=\"{$ib_settings['path_to_iib']}?rel_path=$rel_path&amp;action=upload\" method=\"post\" enctype=\"multipart/form-data\">
		<hr />
		<input type=\"File\" name=\"upl_file\" />		<input type=\"Submit\" value=\"Upload\" /> Max. upload size: ".$ib_settings['max_filesize']."KB <br />
		Create thumbnail:<input type=\"Checkbox\" name=\"create_thumbnail\" value=\"true\"> Largest side:<input type=\"Text\" name=\"l_side\" size=\"4\" value=\"{$ib_settings['default_thumbnail_size'] }\" />px "
		."Quality: <input type=\"text\" name=\"quality\" size=\"4\" value=\"90\" /> ";
		if( function_exists('imagecopyresampled') ) $t .= "<input type=\"Radio\" name=\"method\" value=\"1\" checked=\"checked\" /> Resample <input type=\"Radio\" name=\"method\" value=\"2\" /> Resize";
	$t .="</form>
		</div>

	</div>";


	return $t;

}

/**
 * return files with allowed extension from upload directory
 *
 * The filenames are matched against the allowed file extension
 * from upload option. Thumbnails will be recognized
 * The returned array is 
 *
 * @return mixed hash with filename as key or FALSE
 */
function ib_get_uploaded_files() {
	GLOBAL $dirs,$rel_path,$ib_settings; //fred
    $files = array();
   
   // regex for allowed filetypes, e.g. @\.(gif|png|jpg|jpeg)$@i
/*    $fileglob = '@\.('
	. str_replace(' ', '|',
		      trim(strtolower(get_settings('fileupload_allowedtypes'))))
	. ')$@i';*/
    
    // open upload directory
    if (FALSE === ($upload_dir = opendir($ib_settings['real_path'].$rel_path))) {//fixme
	print "<p>Cannot open upload directory :-(<br />Check <em>Destination directory</em> and <em>URI of this directory</em> in admin area > options > miscellaneous</p>\n";
	return FALSE;
    }
//	else { echo "OK";}
    
    // sort out the allowed filetypes
    while (FALSE !== ($file = readdir($upload_dir))) {
	$file_on_disk = $ib_settings['real_path'].$rel_path."$file";
	
	if(is_dir($file_on_disk)){//fred Reads the DirNames for navigation
		if($file != '.' && $file != '..')
			$dirs[] = $file;
			//echo $file_on_disk;
	}
	
	if (!is_file($file_on_disk)) {
	    // not a regular file or disallowed filetype
	    continue;
	}
	
	$files["$file"] = ib_get_fileinfo($file_on_disk);
    }
    
    // tidy up and return
    closedir($upload_dir);
    $files = ib_recognize_thumbs($files);
    return $files;
} // ib_get_uploaded_files()


/**
 * sort an array by the values of $array[$primary_key][$secondary_key]
 *
 * The function make use of a helper array for sorting.
 *
 * @param array $hash 2D hash to sort
 * @param string $secondary_key
 * @param string $sort_order should be one of 'asc', 'desc'
 * @return mixed FALSE if $secondary_key is not found, else the sorted array
 */
function ib_sort_hash_by_secondary_value($hash, $secondary_key,
					 $sort_order = 'asc') {
    $result = array();
    
    foreach ($hash as $key => $value) {
	if (!isset($value[$secondary_key])) return FALSE;
	$result[$key] = $value[$secondary_key];
    }
    // $helper is now: array ( primary_key => secondary_VALUE, ... )
    // just sort this array by the values
    if ('asc' == $sort_order) {
	asort($result);
    } else {
	arsort($result);
    }
    
    // insert the data into the array
    foreach ($result as $key => $value) {
	$result[$key] = $hash[$key];
    }
    
    return $result;
} // ib_sort_hash_by_secondary_key


/**
 * make <img ... /> html snippet for an image
 *
 * @see ib_make_code_simple_image()
 * @param  array  $image Metainfo for the image (includes name)
 * @param  mixed  $link FALSE (no link) or Metainfo
 * @return string html snippet
 */
function ib_make_html_img($image, $link=FALSE) {
	global $rel_path,$thumb_prefix,$abs_path;
    $html = '';

    // ---- image code
    if (FALSE !== $image) {
	$html .= "<img src='" . $abs_path
	    .$rel_path. rawurlencode($image['name']) . "' ";
	if (0 !== $image['img']) // the getimagesize-html-snippet
	    $html .= $image['img'] . ' ';
	// add alternate text (if not set, then set to an empty string
	if (!isset($image['alt']))
	    $image['alt'] = '';
 	$html .= "alt='{$image['alt']}' ";
	// add title (only if given)
	if (isset($image['title']))
	    $html .= "title='{$image['title']}' ";
	// add showHide
		$html .= "onClick='showHideForm(\"{$image['file']}\")'";
 	$html .= '/>';
    } elseif (FALSE !== $link and isset($link['name'])) {
	// this will be the link text, if no image is given
	$html .= $link['name'];
    }
    
    // ---- $link code
    if (FALSE !== $link and isset($link['name'])) {
	// add it around the already existing code
	if (!isset($link['title']))
	    $link['title'] = '';
	$html = "<a href='" . $abs_path
	    .$rel_path. rawurlencode($link['name']) . "'"
	    . " title='{$link['title']}'>"
	    . $html . '</a>';
    }

    return $html;

} // ib_make_html_single_image





/**
 * remove magic Quotes
 *
 * @param array &$array POST or GET with magic quotes
 */
function ib_magic_quotes_remove(&$array) {
    if(!get_magic_quotes_gpc())
	return;
    foreach($array as $key => $elem) {
	if(is_array($elem))
	    ib_magic_quotes_remove($elem);
	else
	    $array[$key] = stripslashes($elem);
    }
} // ib_magic_quotes_remove

/**
 * make html for select element
 *
 * The hash $options should contain values and Description:
 * array( 
 *   'value' => array( 
 *       text     => 'Description', 
 *       selected => (TRUE|FALSE),
 *    ),
 *  ...
 * )
 *
 * @param string $name Name for select
 * @param array  $options hash with options
 * @return string html snippet
 */
function ib_make_html_select($name,$options) {
    $html = "<select name='$name' id='$name' size='1'>";
    foreach ($options as $value => $option) {
	$html .= "<option value='{$value}'";
	if (isset($option['selected']) and $option['selected'])
	    $html .= " selected='selected'";
	$html .= ">{$option['text']}</option>";
    }
    $html .= "</select>";

    return $html;
} // ib_make_html_select


/**
 * make html for set of radio buttons
 *
 * the hash is the same as in ib_make_html_select
 * The buttons will have a linebreak after each option
 *
 * @see ib_make_html_select()
 * @param string $name Name for radiobuttons
 * @param array  $options hash with options
 * @return string html snippet
 */
function ib_make_html_radiobuttons($name,$options) {
    $elements = array();
    foreach ($options as $value => $option) {
	$html = "<input type='radio' name='{$name}' value='{$value}'";
	if (isset($option['selected']) and $option['selected'])
	    $html .= " checked='checked'";
	$html .= ">{$option['text']}</input>";
	$elements[] = $html;
    }
    
    return implode('<br />', $elements);
} // ib_make_html_radiobuttons


// ====( find out what to do )==================================================

// remove magic quotes
//ib_magic_quotes_remove($_REQUEST);
//ib_magic_quotes_remove($_REQUEST);
ib_magic_quotes_remove($_REQUEST);


// ---- how to sort the files?
// possible values
$ib_sortkey_allowed = array ('name', 'mtime'); 
$ib_sortorder_allowed = array('asc', 'desc');

// array for output
$ib_sortoptions = array(
    'mtime_desc' => array(
	'text' => 'last modification (newer first)'),
    'mtime_asc'	=> array(
	'text' => 'last modification (older first)'),
    'name_asc' => array(
	'text' => 'name (A-z)'),
    'name_desc'	=> array(
	'text' => 'name (z-A)'),
    );

// these are the defaults
$ib_sortkey   = 'mtime';
$ib_sortorder = 'desc';

if (isset($_REQUEST['sortby'])) { // GET or POST
    list ($key, $order) = explode('_',$_REQUEST['sortby']);
    if (in_array($key,$ib_sortkey_allowed))
	$ib_sortkey = $key;
    if (in_array($order,$ib_sortorder_allowed))
	$ib_sortorder = $order;
}

$ib_sortby = $ib_sortkey . '_' . $ib_sortorder;
$ib_sortoptions[$ib_sortby]['selected'] = TRUE;

// ---- if multiple pages, which page do you want?
$ib_page = 1; // the default

if (isset($_REQUEST['ibpage']) and is_numeric($_REQUEST['ibpage'])) {
    $ib_page = floor($_REQUEST['ibpage']);
}

$imgdesc = '';
if (isset($_REQUEST['imgdesc'])) {
    $imgdesc = str_replace('"', '&quot;', $_REQUEST['imgdesc']);
    $imgdesc = str_replace("'", '&#8217;', $imgdesc);
    $imgdesc = htmlspecialchars($imgdesc);
}


// ---- "Sort by: ..." selector
    // -> 'sortby' = ( see $ib_sortoptions )
    print "<div id='step'>"
	. "<form action='{$ib_settings['path_to_iib']}' method='get'>"
	. "<input type='hidden' name='rel_path' value='{$rel_path}'>"
	. "Sorted by: "	. ib_make_html_select('sortby',$ib_sortoptions)
	. "<input type='submit' value='Resort' />"
	. "</form>"
	.hidden_menu()
	."</div><br /><div style=\"position: absolute; top: 5px; right: 5px; text-align: right;\"><a href=\"http://fredfred.net/skriker/\" target=\"_blank\" title=\"http://fredfred.net/skriker/\">IImage Browser homesite</a><br />"
	."<input type=\"Button\" id=\"create_dir_button\" value=\"Create Dir\" onclick=\"showHideButtonForm('create_dir')\" />"
	."<input type=\"Button\" id=\"show_upload_button\" value=\"Upload\" onclick=\"showHideButtonForm('upload')\" />"
	."</div>"
//	."</div>\n"
	
	. "<div id='ibwrapper'>";

//---------Delete selected & confirmed files
if($_REQUEST['action'] == 'delete' && $_REQUEST['is_js_confirmed'] == 1)
{
	echo "<span class='highlight'>";
	if($user_level >= $ib_settings['delete_user_level'])
	{
		if(isset($_REQUEST['delBig']))
		if(!@unlink($ib_settings['real_path'].$rel_path.str_replace ('/', '',rawurldecode($_REQUEST['delBig']) )))// repleacing of '/' is VERY simple protection
			echo rawurldecode($_REQUEST['delBig'])." cannot be deleted!<br />";
		else
			echo rawurldecode($_REQUEST['delBig'])." has been deleted.<br />";
			
		if(isset($_REQUEST['delSmall']))
		if(!@unlink($ib_settings['real_path'].$rel_path.str_replace ('/', '',rawurldecode($_REQUEST['delSmall']) )))
			echo rawurldecode($_REQUEST['delSmall'])." cannot be deleted!<br />";
		else
			echo rawurldecode($_REQUEST['delSmall'])." has been deleted.<br />";
	}
	else {_e("Your 'user level' doesn't allow you to delete files.");}
	echo "</span>";
}
//upload file
else if($_REQUEST['action'] == "upload"){
echo "<span class='highlight'>";

/*if (!get_settings('use_fileupload')){ //Checks if file upload is enabled in the config
	echo (__("The admin disabled this function. You can enable it in WP administration &gt; Options &gt; Miscellaneous."));
	}
else*/

if ($user_level >= $ib_settings['upload_user_level']) {
	$upl_path = $ib_settings['real_path'].$rel_path;
	if (!is_writable($upl_path)){
		printf(__("It doesn't look like you can use the file upload feature at this time because the directory you have specified (<code>%s</code>) doesn't appear to be writable by WordPress. Check the permissions on the directory and for typos."), $ib_settings['real_path'].$rel_path);
		}
		else {
				//$allowed_types = explode(' ', trim(strtolower(get_settings('fileupload_allowedtypes'))));
				$img1_name = $_FILES['upl_file']['name'];
				$img1 = $_FILES['upl_file']['tmp_name'];
				
				    $imgtype = explode(".",$img1_name);
				    $imgtype = strtolower($imgtype[count($imgtype)-1]);
				
				    /*if (in_array($imgtype, $allowed_types) == false) {
				       echo sprintf(__('File %1$s of type %2$s is not allowed.') , $img1_name, $imgtype);
				    }
					else {//file type is allowed
					*/
					if( $_FILES['upl_file']['size'] > ($ib_settings['max_filesize']*1024))
						echo __("File is larger than allowed limit!");
					else {//filesize is OK
						if($_FILES['upl_file']['size'] > 0){//larger than 0 is allowed:o)
							$pathtofile2 = $upl_path.$img1_name;
							if(file_exists($pathtofile2)){
								printf(__("The filename '%s' already exists!"), $img1_name);
								}
								else {
										//let's move the file
								        $moved = move_uploaded_file($img1, $pathtofile2);
								        // if move_uploaded_file() fails, try copy()
								        if (!$moved) {
								            $moved = copy($img1, $pathtofile2);
								        }
								        if (!$moved) {
								            printf(__("Couldn't upload your file to %s."), $pathtofile2);
								        } else {
											chmod($pathtofile2, 0666);
								            @unlink($img1);
								        }
										
										if($moved){//if moved - we can create thumbnail
										  if($user_level >= $ib_settings['thumbnail_user_level']){
											if($_REQUEST['create_thumbnail'] == "true" && intval($_REQUEST['l_side']) > 0){//thumnail should be lareger than 0
												        $result = iimage_create_thumbnail($pathtofile2, intval($_REQUEST['l_side']), NULL, $_REQUEST['method'],max(0,min(100,intval($_REQUEST['quality']))));
												        
												        if($result != 1) {
												            print $result;
												        }
											}
										  }//user level
											else {_e("Your 'user level' doesn't allow you to create thumbnails.");}
										}
								
								}//doesn't exist
							}//>0
						}//<maxSize
					
					
					//}
				
			}
	
	}
	else {_e("Your 'user level' doesn't allow you to upload files.");}
echo "</span>";
}//end of file upload
//creates thumbnail
else if(isset($_REQUEST['create_thumb_button'])){
	echo "<span class='highlight'>";
	if($user_level >= $ib_settings['thumbnail_user_level']){
	$result = iimage_create_thumbnail((  $ib_settings['real_path'].$rel_path.str_replace ('/', '',rawurldecode($_REQUEST['file']) )  ), intval($_REQUEST['l_side']), NULL, $_REQUEST['method'],max(0,min(100,intval($_REQUEST['quality']))));
	//Hier einbauen, dass für Bilder für die kein Thumb existiert einer gemacht 
    //wird
    if ($_REQUEST['create_thumbnail_for_all'] == 1)
    {
    	$path_to_open = $ib_settings['real_path'].$rel_path;
		//open Folder
		$handle=opendir($path_to_open); 
		if ($handle)
		{
			//Read all images
			$files_in_dir = array();
			
			while (false !== ($file = readdir ($handle))) { 
			$files_in_dir[] = $file;
			}
			
			closedir($handle); 
			
			//echo count($files_in_dir);
			
			foreach ($files_in_dir as $onefile) { 
				//Ignore thumbnails now, we compare later
				
			    if ($onefile != '.' && $onefile != '..' && is_file($ib_settings['real_path'].$rel_path.$onefile) && strpos($onefile,$thumb_prefix) !== 0 ) { 
					 
			         //If not exists thumbnail, create one
			         $thumbnail_name = $ib_settings['real_path'].$rel_path.$thumb_prefix.$onefile;
			         if (!file_exists($thumbnail_name))
			         {
			         	 //Now create thumbnail
			         	$result = iimage_create_thumbnail((  $ib_settings['real_path'].$rel_path.$onefile ), intval($_REQUEST['l_side']), NULL, $_REQUEST['method'],max(0,min(100,intval($_REQUEST['quality']))));
			         }
			    } 
			}
			
    	}
    }
	if($result != 1) {
	  print $result;}
	else {
	  _e('Thumbnail has been created.');
	}
	 }
	 else {_e("Your 'user level' doesn't allow you to create thumbnails.");}
	 echo "</span>";

	}//end of thumbnail
//creates directory
else if( $_REQUEST['action']=='createdir' &&  $_REQUEST['ib_name_of_dir']!='' ){
	$ib_dir_full_address = $ib_settings['real_path'].$rel_path.str_replace ('/', '',rawurldecode($_REQUEST['ib_name_of_dir']) );
	
	echo "<div class=\"highlight\">";
	
	if($user_level >= $ib_settings['dir_user_level']){
	if(@mkdir($ib_dir_full_address, 0777)){
		@chmod($ib_dir_full_address,0777);
		
	}else {
		_e("Cannot create directory!");
		
	}
	}
	else {_e("Your 'user level' doesn't allow you to create directories.");}
	echo "</div>";
	unset($ib_dir_full_address);
	}//end of create dir


// ---- get contents of the upload directory
    $ib_files = ib_get_uploaded_files();
	
	print dir_menu();
	
//----------use contents of upload directory	
    if (empty($ib_files)) {
	print "<p>Sorry, I can't find any files in this directory :-(</p>\n"
	    . "<p><a href='#' onclick='window.close()'>Close window</a></p>\n";
	return FALSE;
    }

    // sort files
    if (FALSE === ($ib_files = ib_sort_hash_by_secondary_value($ib_files, $ib_sortkey, 
							       $ib_sortorder))) {
	print "<p>Error sorting by $ib_sortkey :-(</p>\n";
	return FALSE;
    }

// ---- navigation for pages of images
    $ib_pages = ceil(count($ib_files)/$ib_images_per_page);
    if ($ib_page > $ib_pages) $ib_page = $ib_pages;

    $ib_pagelinks = array();
    for ($count = 1; $count <= $ib_pages; $count++) {
	if ($ib_page == $count) {
	    $ib_pagelinks[] = "<strong>$count</strong>";
	} else {
	    $ib_pagelinks[] = "<a href='{$ib_settings['path_to_iib']}?ibpage={$count}"
		. "&amp;sortby={$ib_sortby}&amp;rel_path={$rel_path}'>$count</a>";
	}
    }
	

    print '<div>Page: ' . implode('&nbsp;- ', $ib_pagelinks) ."</div>\n";

// ---- display images for selection
    reset($ib_files);
    for ($count = 1; $count <= count($ib_files); $count++) {
	list ($file, $meta) = each($ib_files);
	
	if (!(($ib_page-1)*$ib_images_per_page < $count
	      and $count <= $ib_page*$ib_images_per_page))
	    continue; // this image is not part of the requested page
	
	$html = '';

	// description and display image
	if (isset($meta['thumb'])) { // thumb
		$meta['thumb']['file'] = $file;
	    $html .= "<div class='image'><div class='title'"."onClick='showHideForm(\"".htmlspecialchars($file)."\")'"."><strong>"
		. "<a href='{$abs_path}{$rel_path}{$file}'>" 
		. htmlspecialchars($file) . "</a></strong>"
		. " ( {$meta['width']} x {$meta['height']} px, ".(round($meta['filesize']/1024,2))
		." kB, <span class='highlight'>T</span>: ";
	    if (isset($meta['thumb']['width']) and isset($meta['thumb']['height']))
		$html .= "{$meta['thumb']['width']} x {$meta['thumb']['height']} px, ";
	    $html .= round($meta['thumb']['filesize']/1024,2) . ' kB)'
	 	. "</div>\n" . ib_make_html_img($meta['thumb']);
	} else { // single image
		$meta['file'] = $file;
	    $html .= "<div class='image'><div class='title'"."onClick='showHideForm(\"".htmlspecialchars($file)."\")'"."><strong>" 
		. htmlspecialchars($file) . "</strong> (";
	    if (isset($meta['width']) and isset($meta['height']))
		$html .= "{$meta['width']} x {$meta['height']} px, ";
	    $html .= round($meta['filesize']/1024,2) . ' kB)'
		. "</div>\n" . ib_make_html_img($meta);
	}

	//--------inserted by fred
    $ib_image_ext_regex = '@(jpg|jpeg|png|gif)$@i';
	
	    if (isset($meta['thumb'])) {
	// it is a thumbnail:
 	$ib_filetype = 'thumb';
	$ib_imgselect_radiobuttons = array(
	    'thumbfull' => array(
		'text'  => 'Thumb with link to full size' ),
	    'thumb'     => array(
		'text'  => 'Thumb only' ),
	    'full'	=> array(
		'text'  => 'Full size only' ),
	    'linkthumb' => array(
		'text'  => 'Link to thumb' ),
	    'linkfull'  => array(
		'text'  => 'Link to full size' ),
		'customfull'  => array(
		'text'  => 'Custom code - full size' ),
		'customthumb'  => array(
		'text'  => 'Custom code - thumbnail' ),
	    );
	$ib_imgselect_radiobuttons['thumbfull']['selected'] = TRUE;
    } elseif (preg_match($ib_image_ext_regex,$file)) {
	// looks like a single image
	$ib_filetype = 'image';
	$ib_imgselect_radiobuttons = array(
	    'full'	=> array(
		'text'  => 'Include as image' ),
	    'linkfull'  => array(
		'text'  => 'Link to file' ),
		'customfull'  => array(
		'text'  => 'Custom code - full size' ),
	    );
	$ib_imgselect_radiobuttons['full']['selected'] = TRUE;
    } else {
	// looks like a simple file
	$ib_filetype = 'file';
	$ib_imgselect_radiobuttons = array(
	    'full'	=> array(
		'text'  => 'Include as image' ),
	    'linkfull'  => array(
		'text'  => 'Link to file' ),
	    );
	$ib_imgselect_radiobuttons['linkfull']['selected'] = TRUE;
    }
	
	
    $html .= "<form action='{$ib_settings['path_to_iib']}?rel_path={$rel_path}' method='post' id='{$file}' style='display: none;'>"
	. "<div class='infoform'>"
	// How to include?
	. "<fieldset><legend>How to include?</legend>"
	. ib_make_html_radiobuttons('imgselect', $ib_imgselect_radiobuttons)
	. "</fieldset></div><div class='infoform'>"
	// Description
	. "<fieldset><legend>Code:</legend>"
	. "<label for='imgdesc'>Description:</label><input type='text' name='imgdesc' id='imgdesc' size='35' maxlength='150' "
	. "value='{$imgdesc}' /><br />"
	."<textarea name='finalcode' id='finalcode' rows='8' cols='60' onKeyUp='finalOnKeyUp(\"{$file}\")'></textarea>"
	// hidden fields
	. "<input type='hidden' name='file' value='". rawurlencode($file) ."' />"
	. "<input type='hidden' name='sortby' value='{$ib_sortby}' />"
	. "<input type='hidden' name='ibpage' value='{$ib_page}' />"
	. "<input type='hidden' name='relpath' value='{$rel_path}' />"
	. "<input type='hidden' name='abs_path' value='{$abs_path}' />"
	. "<input type='hidden' name='step' value='3' />"
	. "<input type='hidden' name='thumbw' value='{$meta['thumb']['width']}' />"
	. "<input type='hidden' name='thumbh' value='{$meta['thumb']['height']}' />"
	. "<input type='hidden' name='bigw' value='{$meta['width']}' />"
	. "<input type='hidden' name='bigh' value='{$meta['height']}' />"
	. "<input type='hidden' name='thumbprefix' value='{$thumb_prefix}' />"
	. "</fieldset></div><div class='select'><fieldset>"
	//buttons
	."<input type='button' onclick='getTheCode(eId(\"{$file}\"))' id='get_it' value='Get the code' /> "
	."<input type='button' onclick='insertAtCursor(window.opener.document.post.content,eId(\"{$file}\").finalcode.value)' name='add_it' value='Add it to the post!' disabled='disabled' /> "
	."<input type='submit' onclick='return confirmLink(this, eId(\"{$file}\"), \"delete\",\"Do you really want to delete:\\n\")' id='delete_it' value='Delete:' disabled='disabled' /> "
	."<input type='checkbox' onclick='deleteCheckBoxOnClick(eId(\"{$file}\"))' id='delBig' name='delBig' value='". rawurlencode($file) ."'>Full size</input>"
	.((isset($meta['thumb'])) ? "<input type='checkbox' onclick='deleteCheckBoxOnClick(eId(\"{$file}\"))' id='delSmall' name='delSmall' value='". rawurlencode($thumb_prefix.$file) ."'>Thumbnail</input>" : "")
	."</fieldset>";
		//form for thumbnail creation
	if(!isset($meta['thumb'])) {
			$html .= "<fieldset>"
					."<input type='submit' id='create_thumb_button' name='create_thumb_button' value='Create Thumbnail' /> "
					."Largest side:<input type=\"Text\" name=\"l_side\" size=\"4\" value=\"{$ib_settings['default_thumbnail_size'] }\" />px "
					."Quality: <input type=\"text\" name=\"quality\" size=\"4\" value=\"90\" /> ";
		
			if( function_exists('imagecopyresampled') ) $html .= "<input type=\"Radio\" name=\"method\" value=\"1\" checked=\"checked\" /> Resample <input type=\"Radio\" name=\"method\" value=\"2\" /> Resize";
			$html .= "<input type=\"checkbox\" name=\"create_thumbnail_for_all\" value=\"1\" /> ".__("Create for all images in this directory?");
			$html .= '</fieldset>';
			}
	
	$html .= "</div></form></div>";
	
	//----------end fred
		
	print $html;
    }
// ---- footer
    print '<p>Page: ' . implode('&nbsp;- ', $ib_pagelinks) . "</p>\n";
    print "<p><a href='#' onclick='window.close()'>Close window</a></p>\n";


// ====( output footer )========================================================
?>
</div><!-- ibwrapper -->
</body>
</html>
<?php // ex:set tabstop=3: ?>
