<?php
include_once 'api_lib.php';

echo "Вызвана функция fnCarriageContentGet с параметром CarriageID=".$CarriageID;

$rezu=fnCarriageContentGet($CarriageID, null, 100, 1);

include 'template/TemplateCarriageContentGet.php';

?>