<?php
include_once 'api_lib.php';

$rezu=fnCarriageListGet($StartDate, $StopDate, $PageSize, $PageNumber, $State);

include 'template/TemplateCarriageListGet.php';

?>

