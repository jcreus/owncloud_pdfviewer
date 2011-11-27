<?php
require_once('../../../lib/base.php');

OC_JSON::checkLoggedIn();

echo OC_Preferences::getValue(OC_User::getUser(),"pdfviewer","lastopened","[]");

?>
