<?php
/*
   Библиотека функций для работы с API Озон Доставка (для пунктов выдачи заказов)
*/

// Константы для задания режима работы функций

// Доступы к API Озон Доставка
// Рабочий доступ
define('Login', 'рабочий логин');
define('Password', 'рабочий пароль');
define('ContractID', 'рабочий номер контракта');

// Тестовый доступ
define('TestLogin', 'тестовый логин');
define('TestPassword', 'тестовый пароль');
define('TestContractID', 'тестовый номер контракта');

// символьное название для выбора режима работы функции
define('SetWorkModeAPI', '1');  // выбор "боевого" API - работа с реальными данными
define('SetTestModeAPI', '0');  // выбор тестового API - работа с тестовым сервисом API

define('ShowAPIChangesDetailInfo', '0');  // показывать подробную информацию при обмене с API
define('HideAPIChangesDetailInfo', '1');  // не показывать подробную информацию при обмене с API

// Раскомментировать нужный режим работы для всех функций в библиотеке
//$ModeAPI=SetWorkModeAPI;     // задать для всех функций работу с боевым API
$ModeAPI=SetTestModeAPI;        // задать для всех функций работу с тестовым API

// Базовая функция для работы с API Озон
/*
Параметры:
$ModeAPI   - выбор боевого ($SetWorkModeAPI) или тестового ($SetTestModeAPI) API

$APIChangesDetailInfo
показывать подробную информацию при обмене с API    ($ShowAPIChangesDetailInfo)
не показывать подробную информацию при обмене с API ($HideAPIChangesDetailInfo)

$APIwsdl   - адрес боевого wsdl
$TestAPIwsdl   - адрес тестового wsdl

$InputParam    массив с перечнем входных параметров для функции (кроме Logi, Password и ContractID)

$ItemNames=array('StartDate', 'StopDate', 'PageSize', 'PageNumber', 'State');

задание названий полей входны параметров
$param=array('StartDate', 'StopDate', 'PageSize', 'PageNumber', 'State');
$paramValues=array('25.09.2019', '25.09.2019', 100, 1, All);

// названия выходных параметров
$outParam=array('ID', 'Name', 'Barcode', 'StateID', 'StateName', 'StateSysName', 'Description',
 'RouteID', 'RouteName', 'Moment');

*/

// Вспомогательная функция
function GetParam($rty, $outParam, $paramNumber, $ResultNumber, $results){

    if ($results==1) {
        $op=$outParam[$paramNumber];
        $res1=$rty->$op;
    }
    else {
        $op=$outParam[$paramNumber];
        $res1=$rty[$ResultNumber]->$op;
    }

    return $res1;
}

function OzonDoctavkaAPI($APIChangesDetailInfo, $APIwsdl, $TestAPIwsdl,
                         $param, $paramValues, $MetodName, $ItemNames, $outParam) {
    // подключение доступа к глобальным переменным
    global $ModeAPI;

// если класс SoapClient доступен, работаем с API
    if (class_exists('SoapClient')){

        // отключаем кэширование
        ini_set("soap.wsdl_cache_enabled", "0" );

        // создаем класс SoapClient с нужным вариантом подключения - боевым или тестовым
        if ($ModeAPI==SetWorkModeAPI) {
            // подключаемся к боевому серверу
            $client = new SoapClient(
                $APIwsdl,
                Array("exceptions" => 0, "trace" => 1, "encoding" => "utf-8", "cache_wsdl" => WSDL_CACHE_NONE, "soap_version" => SOAP_1_1)
            );

        }

        if ($ModeAPI==SetTestModeAPI) {
            // подключаемся к тестовому серверу
            $client = new SoapClient(
                $TestAPIwsdl,
                Array("exceptions" => 0, "trace" => 1, "encoding" => "utf-8", "cache_wsdl" => WSDL_CACHE_NONE, "soap_version" => SOAP_1_1)
            );

        }

        // если режим проверки работы API, вывести ответ на запрос о доступных функциях
        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
            echo "<h2>Результат функции __getFunctions()</h2>";
            var_dump($client->__getFunctions());
        }

        // задаем параметры запроса API
        $header = new SOAPHeader('', '', '');
        $client->__setSOAPHeaders(array($header));

        // задаем параметры доступа для работы с "боевым" API
        if ($ModeAPI==SetWorkModeAPI) {
            $InputParam = array(
                'Login' => Login,
                'Password' => Password,
                'ContractID' => ContractID
            );
            for ($i=0; $i<count($param); $i++){
                $InputParam[$param[$i]]=$paramValues[$i];
            }
        }

        // задаем параметры доступа для работы с тестовым API
        if ($ModeAPI==SetTestModeAPI) {
            $InputParam = array(
                'Login' => TestLogin,
                'Password' => TestPassword,
                'ContractID' => TestContractID
            );
            for ($i=0; $i<count($param); $i++){
                $InputParam[$param[$i]]=$paramValues[$i];
            }
        }

        // если режим проверки работы API, вывести переданные в функцию параметры
        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
            echo "<h2>Переданные в функцию параметры</h2>";
            var_dump($InputParam);
        }

        // Названия из структуры ответа функции API для получения результатов вызова
        $Level1ItemName=$ItemNames[0];
        $Level2ItemName=$ItemNames[1];
        $Level3ItemName=$ItemNames[2];
        $Level4ItemName=$ItemNames[3];
        $Level5ItemName=$ItemNames[4];

        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo)
            printf('<h2>Полученные параметры для разбора ответа</h2>
                    <h3>%s %s %s</h3>',$Level1ItemName, $Level2ItemName, $Level3ItemName);

        //помещаем наш массив параметров в массив запроса request.
        $arRequest['request'] = $InputParam;
        //обращаемся к функции API
        $ret = $client->$MetodName($arRequest);
        // готовимся к тому, чтобы в дальнейшем возможно было обращаться к данным
        // в таком "$t1[0] = $rty[$i]->$op0;" виде
        $rty=$ret->$Level1ItemName->$Level2ItemName->$Level3ItemName;

        // получить базовые параметры единые для всех функций
        $ResultCode = $ret->$Level1ItemName->ResultCode;
        $ResultMessage = $ret->$Level1ItemName->ResultMessage;
        $ResultProcessTime = $ret->$Level1ItemName->ResultProcessTime;
        $result_number = $ret->$Level1ItemName->Rows;
        // Если в ответе на запрос нет парметра Rows, отпределить коиество результатов иначе
        if ($result_number=='') $result_number = count($rty);

        // если режим проверки работы API, вывести передаваемую и принимаемую
        // информацию при обмене с API
        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
            echo '<h2>Результат функции __getLastRequestHeaders:</h2>';
            echo '<p style="font-size:18px;">' . htmlspecialchars($client->__getLastRequestHeaders(), ENT_QUOTES) . '</p>' ;

            echo '<h2>Результат функции __getLastRequest:</h2>';
            echo '<p style="font-size:18px;">' . htmlspecialchars($client->__getLastRequest(), ENT_QUOTES) . '</p>' ;

            echo '<h2>Результат функции __getLastResponseHeaders:</h2>';
            echo '<p style="font-size:18px;">' . htmlspecialchars($client->__getLastResponseHeaders(), ENT_QUOTES) . '</p>' ;

            echo '<h2>Результат функции __getLastResponse:</h2>';
            echo '<p style="font-size:18px;">' . htmlspecialchars($client->__getLastResponse(), ENT_QUOTES) . '</p>' ;

            echo '<h2>Результат var_dump($ret) (структура ответа веб-функции) :</h2>';
            var_dump($ret); // увидеть структуру ответа веб-функции

            echo "<h2>ResultCode = ". $ResultCode . " ";
            switch ($ResultCode) {
                case 0: echo "Операция выполнена успешно"; break;
                case 1: echo "Редкий случай, когда операция отработала без ошибок, но результат операции определен как неудовлетворительный"; break;
                case 2: echo "Операция не выполнена"; break;
                case 3: echo "Данные по запросу не найдены"; break;
                case 4: echo "Доступ запрещен (например, несоответствие логина или пароля)"; break;
                case 5: echo "Ошибка валидации. Используется только в отдельных методах. В методах  API Субагента не используется.
"; break;
                case 6: echo "Неверный аргумент (входящий параметр). Ошибка возвращается в случае если полученный от клиента параметр: 1. Является обязательным, но не заполнен. 2. Заполнен некорректным значением.
"; break;
                case 7: echo "У пользователя нет прав на выполнение данной операции (необходимо обратиться в поддержку проекта для решения данного вопроса)."; break;
                case 8: echo "Ошибка запроса. Возвращается в случае некорректно сформированного запроса (ошибки в названии полей или вместо тела запроса передан null)"; break;
                case 104: echo "Неизвестная ошибка, возможно баг самой системы (необходимо обратиться в поддержку проекта для решения данного вопроса)"; break;
            }
            echo "</h2>";
            echo "<h2>ResultMessage = ". $ResultMessage . " ";

            echo '<h2>Количество результатов:'.$result_number.'</h2>';

        }

        // если режим проверки работы API
        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
            echo "<h2>Начало разбора ответа при result_number=1</h2>";
        }

        // Сохраним количество выходных параметров, полученное как аргумент функции
        $outParamRealCount=count($outParam);

        // если режим проверки работы API
        if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
            // Выведем для проверки полученный массив названий параметров
            echo "<h2>Массив с названиями входных параметров запроса:</h2>";
            for ($k=0;$k<$outParamRealCount;$k++) {
                echo "<h3>Парамер[ ".$k. " ]: ".$outParam[$k]."</h3>";
            }
            echo "<br>";
        }

        $res = array(); // Готовим пустой массив для результатов

        // Разберем ответ от API и положим в массив для результатов
        // В ответе от API может быть от 0 до $result_number блоков с данными
        for ($i=0; $i<$result_number; $i++)
        {
            $t1=array();
            // Сохраним в массиве $t1 значения параметров для $i-го блока ответа от API
            for ($a=0; $a<54; $a++)
                $t1[$a] = GetParam($rty, $outParam, $a, $i, $result_number);

            // если режим проверки работы API
            if ($APIChangesDetailInfo==ShowAPIChangesDetailInfo) {
                // Выведем для проверки массив значений параметров полученных от API
                echo "<br><h2>Массив  значений параметров полученных от API:</h2>";
                for ($k=0;$k<$outParamRealCount;$k++) {
                    //    echo "<h3>Парамер ".$outParam[$k]. " = ".$t1[$k]."</h3>";
                    printf("<h3>Парамер %s = %s </h3>", $outParam[$k], $t1[$k]);
                }
            }

            // Создадим ассоциативный массив с результатом разбора овета для 1 блока данных
            // с названиями параметров и полученными при запросе API значениями
            $item=array_combine(array_slice($outParam, 0, $outParamRealCount+1), array_slice($t1, 0, $outParamRealCount+1));

            // Добавляем новый элемент-массив (еще одну "перевозку") в результат
            $res[$i]=$item;
        }

        return $res;

    } else echo "Включите поддержку SOAP в PHP!";

} // OzonDoctavkaAPI


// ****** Получение списка перевозок CarriageListGet ******************
/*
  Параметры:
StartDate	Начальная дата (формат даты: 01.01.2000) – дата создания перевозки	string
StopDate	Конечная дата (формат даты: 01.01.2000)	string
PageSize	Пейджинг - количество элементов на страницу	int?
PageNumber	Пейджинг - номер страницы	int?
State	Фильтр по статусу перевозки. *	string

  Результаты запроса:
ID	Идентификатор перевозки	long?
Name	Имя перевозки	string
Barcode	Штрихкод перевозки	string
StateID	Идентификатор статуса перевозки	long?
StateName	Название статуса перевозки	string
StateSysName	Системное наименование статуса	string
Description	Описание перевозки	string
RouteID	Идентификатор маршрута перевозки	long?
RouteName	Название маршрута перевозки	string
Moment	Дата и время перевозки (Формат: 01.01.2000 23:59:59)	string

*/
function fnCarriageListGet($StartDate='23.09.2019', $StopDate='', $PageSize='100', $PageNumber='1', $State='All') {

    $MetodName='CarriageListGet';
    $ItemNames=array('CarriageListGetResult', 'CarriageInfoList', 'CarriageInfo');
    $param=array('StartDate', 'StopDate', 'PageSize', 'PageNumber', 'State');
    $paramValues=array($StartDate, $StopDate, $PageSize, $PageNumber, $State);

    // названия выходных параметров
    $outParam=array('ID', 'Name', 'Barcode', 'StateID', 'StateName', 'StateSysName', 'Description',
        'RouteID', 'RouteName', 'Moment');

    return OzonDoctavkaAPI(HideAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );


}

// ********** CarriageContentGet *************************
/*
Получение содержания перевозки (по отправлениям) CarriageContentGet

  Входящие параметры:
CarriageID	Идентификатор перевозки	long?
CarriageBarcode	Штрихкод перевозки	string
PageSize		Пейджинг - количество элементов на страницу	int?
PageNumber	Пейджинг - номер страницы	int?

  Результаты запроса:
PostingID		Идентификатор отправления	long?
PostingBarcode	Штрихкод отправления	string
PostingName	Номер отправления	string
CarriageID	Идентификатор перевозки, в которой находится отправление в текущий момент	long?
CarriageBarcode	Штрихкод перевозки, в которой находится отправление в текущий момент	string
Description	Описание отправления	string
StateName	Статус отправления	string
StateSysName	Системное имя статуса отправления	string
SourcePlaceID	Идентификатор места передачи отправления		long?
SourcePlaceName	Наименование места передачи отправления	string
DestinationPlaceID	Идентификатор места назначения	long?
DestinationPlaceName	Наименование места назначения	string
ClientName	ФИО Клиента	string
ClientPrice	Сумма получения с клиента	decimal?
ClientEmail	Электронный адрес клиента	string
ClientPhone	Телефоны клиента	string
RecipientName	ФИО Получателя	string
DeliveryAddress	Адрес доставки	string
DeliveryVariantID	Идентификатор способа доставки	long?
DeliveryVariantName	Название способа доставки	string
StorageExpirationDate	Срок хранения до (не включая)
(Формат: 01.01.2000 23:59:59)	string
StoreName	Наименование магазина	string
ETAMomentFrom	Согласованная дата-время доставки "с" (Формат: 01.01.2000 23:59:59)	string
ETAMomentTo	Согласованная дата-время доставки "по" (Формат: 01.01.2000 23:59:59)	string
Weight	Вес отправления	string
VolumeWeight	Объёмный вес	string
Length	Длина	string
Width	Ширина	string
Height	Высота	string
DeliveryVariantCode	Внешний идентификатор пункта выдачи субагента.	string
IsReturn	Признак возврата true / false	bool?
IsPartialGiveoutDisabled	Если 1, то запрещена частичная выдача. Если 0, то разрешена.	bool?
IsClientReturnDisabled	Если 1, то запрещен приём клиентского возврата. Если 0, то разрешен.	bool?
ContractorPhone	Номер телефона отправителя	string
DeliveryVATRate	Ставка НДС по стоимости доставки	string
PrepaymentAmount	Сумма предоплаты по заказу	decimal?
PrepaymentType	Признак способа расчета,  см. таблицу возможных значений в описании метода ArticleInfoGet	string
CustomsImportIsDone	Признак импортного оформления	bool?
CustomsExportIsDone	Признак экспортного оформления	bool?
*/
function fnCarriageContentGet($CarriageID, $CarriageBarcode, $PageSize, $PageNumber) {

    $MetodName='CarriageContentGet';
    $ItemNames=array('CarriageContentGetResult', 'PostingInfoList', 'PostingInfo');
    $param=array('CarriageID', 'CarriageBarcode', 'PageSize', 'PageNumber');
    $paramValues=array($CarriageID, $CarriageBarcode, $PageSize, $PageNumber);

    // названия выходных параметров
    $outParam=array('PostingID', 'PostingBarcode', 'PostingName', 'CarriageID', 'CarriageBarcode',
        'Description', 'StateName', 'StateSysName', 'SourcePlaceID', 'SourcePlaceName', 'DestinationPlaceID',
        'DestinationPlaceName', 'ClientName', 'ClientPrice', 'ClientEmail', 'ClientPhone', 'RecipientName',
        'DeliveryAddress', 'DeliveryVariantID', 'DeliveryVariantName', 'StorageExpirationDate', 'StoreName',
        'ETAMomentFrom', 'ETAMomentTo', 'Weight', 'VolumeWeight', 'Length', 'Width', 'Height',
        'DeliveryVariantCode', 'IsReturn', 'IsPartialGiveoutDisabled', 'IsClientReturnDisabled',
        'ContractorName', 'ContractorName', 'ContractorPhone','DeliveryVATRate','PrepaymentAmount',
        'PrepaymentType', 'CustomsImportIsDone','CustomsExportIsDone'
    );

    return OzonDoctavkaAPI(HideAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );

}


/*
  Получение информации о статусах предмета ArticleTrack

  Параметры:
  ArticleID Идентификатор предмета (отправления / экземпляра)

  Результаты запроса:
  Moment	 Дата статуса Формат (01.01.2000 23:59:59)	string
  PlaceID	 Идентификатор места смены статуса	long?
  PlaceName	 Наименование места	string
  Name	     Наименование статуса	string
*/
function fnArticleTrack($ArticleID) {

    $MetodName='ArticleTrack';
    $ItemNames=array('ArticleTrackResult', 'TrackingStateInfoList', 'TrackingStateInfo');
    $param=array('ArticleID');
    $paramValues=array($ArticleID);

    // названия выходных параметров
    $outParam=array('Moment', 'PlaceID', 'PlaceName', 'Description', 'Name', 'Priority');

    return OzonDoctavkaAPI(ShowAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );

} // fnArticleTrack


/*
  Прием перевозки CarriageReceive

  Параметры:
  CarriageBarcode/ID    Штрихкод/id перевозки   string
  IsBroken              Признак “Перевозка повреждена” (true / false / null = false) bool?
  Comment               Комментарий к перевозке string

  Результаты запроса:
  ResultCode  Системное поле. Код ошибки  int
  ResultMessage   Системное поле. Текст ошибки    string
  ResultProcessTime   Системное поле. Время выполнения операции на сервере    string
 */
function fnCarriageReceive($CarriageBarcode, $IsBroken, $Comment) {

    $MetodName='CarriageReceive';
    $ItemNames=array('CarriageReceiveResponse', 'CarriageReceiveResult');
    $param=array('CarriageBarcode', 'IsBroken', 'Comment');
    $paramValues=array($CarriageBarcode, $IsBroken, $Comment);

    // названия выходных параметров
    // параметры ResultCode, ResultMessage, ResultProcessTime
    // имеются у всех функций, их не указываем
    $outParam=array();

    return OzonDoctavkaAPI(ShowAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/CarriageService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );
} // fnCarriageReceive


/*
  Установка статуса выдачи отправления в момент вручения клиенту PostingGiveout

  Параметры:
  PostingBarcode	Штрихкод отправления/ID отправления	string
  IsCash	Признак наличной\безналичной оплаты. bool?
            Если IsCash  = 1, то оплата наличными, иначе безналичная оплата  ( по умолчанию Cash = 1 ).
  ExemplarReturnedInfoList string	Информация о штрихкодах возвращаемых экземпляров
                            отправления с причинами возврата в xml. Если причина для экземпляра не указана,
                            то она значение по умолчанию:
                            6.1. Была попытка доставить: изменил решение о покупке.
                            *метод используется для частичной выдачи, полного возврата поэкземплярно
                            (если клиент вскрыл коробку)

  Результаты запроса:
  ResultCode	Системное поле. Код ошибки	int
  ResultMessage	Системное поле. Текст ошибки	string
  ResultProcessTime	Системное поле. Время выполнения операции на сервере	string
*/
function fnPostingGiveout($PostingBarcode, $IsCash, $ExemplarReturnedInfoList) {

    $MetodName='PostingGiveout';
    $ItemNames=array('PostingGiveoutResult', 'PostingInfoList', 'PostingInfo');
    $param=array('PostingBarcode', 'IsCash', 'ExemplarReturnedInfoList');
    $paramValues=array($PostingBarcode, $IsCash, $ExemplarReturnedInfoList);

    // названия выходных параметров
    $outParam=array();

    return OzonDoctavkaAPI(ShowAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );


} // fnPostingGiveout


/*
  Получение списка отправлений, готовых к выдаче PostingReadyForGiveoutListGet

  Параметры:
  PostingName	Номер заказа	string	необяз
  RecipientName	Фамилия / Имя / Отчество клиента	string	необяз
  PageSize		Пейджинг - количество элементов на страницу	int?
  PageNumber	Пейджинг - номер страницы	int?

  Результаты запроса:
  PostingID		Идентификатор отправления	long?
  PostingBarcode	Штрихкод отправления	string
  PostingName	Номер отправления	string
  CarriageID	Идентификатор перевозки, в которой находится отправление в текущий момент long?
  CarriageBarcode	Штрихкод перевозки, в которой находится отправление в текущий момент	string
  Description	Описание отправления	string
  StateName	Статус отправления	string
  StateSysName	Системное имя статуса отправления	string
  SourcePlaceID	Идентификатор места передачи отправления		long?
  SourcePlaceName	Наименование места передачи отправления	string
  DestinationPlaceID	Идентификатор места назначения	long?
  DestinationPlaceName	Наименование места назначения	string
  ClientName	ФИО Клиента	string
  ClientPrice	Стоимость отправления	decimal?
  ClientEmail	Электронный адрес клиента	string
  ClientPhone	Телефоны клиента	string
  RecipientName	ФИО Получателя	string
  DeliveryAddress	Адрес доставки	string
  DeliveryVariantID	Идентификатор способа доставки	long?
  DeliveryVariantName	Название способа доставки	string
  StorageExpirationDate	Срок хранения до (не включая) (Формат: 01.01.2000 23:59:59)	string
  StoreName	Наименование магазина	string
  ETAMomentFrom	Согласованная дата-время доставки "с" (Формат: 01.01.2000 23:59:59)	string
  ETAMomentTo	Согласованная дата-время доставки "по" (Формат: 01.01.2000 23:59:59)	string
  Weight	Вес отправления	string
  VolumeWeight	Объёмный вес	string
  Length	Длина	string
  Width	Ширина	string
  Height	Высота	string
  DeliveryVariantCode	Внешний идентификатор пункта выдачи субагента.	string
  IsReturn	Признак возврата true / false	bool?
  IsPartialGiveoutDisabled	Если 1, то запрещена частичная выдача. Если 0, то разрешена.	bool?
  IsClientReturnDisabled	Если 1, то запрещен приём клиентского возврата. Если 0, то разрешен.	bool?
  ContractorPhone	Номер телефона отправителя	string
  DeliveryVATRate	Ставка НДС по стоимости доставки	string
  PrepaymentAmount	Сумма предоплаты по заказу	decimal?
  PrepaymentType	Признак способа расчета,  см. таблицу возможных значений в описании метода ArticleInfoGet	string
*/
function fnPostingReadyForGiveoutListGet($PostingName, $RecipientName, $PageSize, $PageNumber) {

    $MetodName='PostingReadyForGiveoutListGet';
    $ItemNames=array('PostingReadyForGiveoutListGetResult', 'PostingInfoList', 'PostingInfo');
    $param=array('PostingName', 'RecipientName', 'PageSize', 'PageNumber');
    $paramValues=array($PostingName, $RecipientName, $PageSize, $PageNumber);

    // названия выходных параметров
    $outParam=array('PostingID', 'PostingBarcode', 'PostingName', 'CarriageID', 'CarriageBarcode',
        'Description', 'StateName', 'StateSysName', 'SourcePlaceID', 'SourcePlaceName',
        'DestinationPlaceID', 'DestinationPlaceName', 'ClientName', 'ClientPrice',
        'ClientEmail', 'ClientPhone', 'RecipientName', 'DeliveryAddress',
        'DeliveryVariantID', 'DeliveryVariantName', 'StorageExpirationDate', 'StoreName',
        'ETAMomentFrom', 'ETAMomentTo', 'Weight', 'VolumeWeight',
        'Length', 'Width', 'Height', 'DeliveryVariantCode',
        'IsReturn', 'IsPartialGiveoutDisabled', 'IsClientReturnDisabled', 'ContractorPhone',
        'DeliveryVATRate', 'PrepaymentAmount', 'PrepaymentType');

    return OzonDoctavkaAPI(ShowAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );
} // fnPostingReadyForGiveoutListGet

/*
  Прием отправления на складе субагента PostingReceive

  Параметры:
  PostingBarcode  Штрихкод отправления/ ID отправления    string  да
  CarriageBarcode Штрихкод перевозки  string  
  IsDamaged   Признак повреждения упаковки отправления    bool?   
  IsTransit   Призник транзита    bool?   

  Результаты запроса:
  ResultCode    Системное поле. Код ошибки  int
  ResultMessage Системное поле. Текст ошибки    string
  ResultProcessTime Системное поле. Время выполнения операции на сервере    string
 */
function fnPostingReceive($PostingBarcodeOrID, $CarriageBarcode, $IsDamaged, $IsTransit) {

    $MetodName='PostingReceive';
    $ItemNames=array('PostingReceiveResult', 'PostingReceiveResult');
    $param=      array('PostingBarcode',   'CarriageBarcode', 'IsDamaged', 'IsTransit');
    $paramValues=array($PostingBarcodeOrID, $CarriageBarcode, $IsDamaged, $IsTransit);

    // названия выходных параметров
    $outParam=array();

    return OzonDoctavkaAPI(ShowAPIChangesDetailInfo,
        'http://api.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        'http://apitest.ozon-dostavka.ru/subagent/v2/ArticleService.svc?wsdl',
        $param, $paramValues, $MetodName, $ItemNames, $outParam
    );


} // fnPostingReceive