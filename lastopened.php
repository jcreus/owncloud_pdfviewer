<?php

/**
* ownCloud - ajax frontend
*
* @author Robin Appelman
* @copyright 2010 Robin Appelman icewind1991@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/


// Init owncloud
require_once('../../lib/base.php');

// Check if we are a user
OC_Util::checkLoggedIn();

// Load the files we need
OC_Util::addStyle( "files", "files" );
OC_Util::addScript( "files", "files" );
OC_Util::addScript( 'files', 'filelist' );
OC_Util::addScript( 'files', 'fileactions' );
if(!isset($_SESSION['timezone'])){
	OC_Util::addScript( 'files', 'timezone' );
}
OC_App::setActiveNavigationEntry( "files_index" );
// Load the files
$dir = isset( $_GET['dir'] ) ? $_GET['dir'] : '';
// Redirect if directory does not exist
if(!OC_Filesystem::is_dir($dir)) {
	header("Location: ".$_SERVER['PHP_SELF']."");
}
$a = json_decode(OC_Preferences::getValue(OC_User::getUser(),"pdfviewer","lastopened","[]"));

OC_Filesystem::stat($directory.'/'.$filename);
$files = array();
foreach( $a as $f ){
        $i = OC_Filesystem::stat($f[0]."/".$f[1]);
	$i['name']=$f[1];
	$i['directory']=$f[0];
        $f = $f[0]."/".$f[1];
        $i['size']=OC_Filesystem::filesize($f);
        $i['mime']=OC_Files::getMimeType($f);
	$i['readable']=OC_Filesystem::is_readable($f);
	$i['writeable']=OC_Filesystem::is_writeable($f);
	$i['type']=OC_Filesystem::filetype($f);
	$i["date"] = OC_Util::formatDate($i["mtime"] );
	if($i['type']=='file'){
		$fileinfo=pathinfo($i['name']);
		$i['basename']=$fileinfo['filename'];
		if (!empty($fileinfo['extension'])) {
			$i['extention']='.' . $fileinfo['extension'];
		}
		else {
			$i['extention']='';
		}
	}
	if($i['directory']=='/'){
		$i['directory']='';
	}
	$files[] = $i;
}

// Make breadcrumb
$breadcrumb = array();
$pathtohere = "";
foreach( explode( "/", $dir ) as $i ){
	if( $i != "" ){
		$pathtohere .= "/$i";
		$breadcrumb[] = array( "dir" => $pathtohere, "name" => $i );
	}
}

// make breadcrumb und filelist markup
$list = new OC_Template( "files", "part.list", "" );
$list->assign( "files", $files );
$list->assign( "baseURL", OC_Helper::linkTo("files", "index.php?dir="));
$list->assign( "downloadURL", OC_Helper::linkTo("files", "download.php?file="));
$breadcrumbNav = new OC_Template( "files", "part.breadcrumb", "" );
$breadcrumbNav->assign( "breadcrumb", $breadcrumb );
$breadcrumbNav->assign( "baseURL", OC_Helper::linkTo("files", "index.php?dir="));

$upload_max_filesize = OC_Helper::computerFileSize(ini_get('upload_max_filesize'));
$post_max_size = OC_Helper::computerFileSize(ini_get('post_max_size'));
$maxUploadFilesize = min($upload_max_filesize, $post_max_size);

$freeSpace=OC_Filesystem::free_space('/');
$freeSpace=max($freeSpace,0);
$maxUploadFilesize = min($maxUploadFilesize ,$freeSpace);

$tmpl = new OC_Template( "files", "index", "user" );
$tmpl->assign( "fileList", $list->fetchPage() );
$tmpl->assign( "breadcrumb", $breadcrumbNav->fetchPage() );
$tmpl->assign( 'dir', $dir);
$tmpl->assign( "files", $files );
$tmpl->assign( 'uploadMaxFilesize', $maxUploadFilesize);
$tmpl->assign( 'uploadMaxHumanFilesize', OC_Helper::humanFileSize($maxUploadFilesize));
$tmpl->printPage();

?>
