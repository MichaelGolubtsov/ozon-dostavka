<?php
include 'head.html';
?>

<body>

<div class="vidimaja-oblast">

    <header>
        <div class="hdr">
            <span class="titul">Веб-интерфейс для Озон</span>
            <div>
                <span>Добро пожаловать, </span>
                <span id="user-id">User1</span>
                <span> !</span>
                <input type="button" value="Выход">
            </div>
        </div>
        <nav>
            <div class="nav-current"><a href="index.php">Перевозки</a></div>
            <div>Прием предметов</div>
            <div>Выдача</div>
            <div>Возврат</div>
            <div>Список документов</div>
        </nav>
    </header>


    <section class="otpravlenia">

        <div class="otpr-header">
            <span class="titul">Список грузов в перевозке</span>
            <div>
                <span>Показывать по</span>
                <button class="bt-pokazivat-po btn-active" id="bt10">10</button>
                <button class="bt-pokazivat-po" id="bt20">20</button>
                <button class="bt-pokazivat-po" id="bt50">50</button>
                <button class="bt-pokazivat-po" id="bt100">100</button>
                <button class="bt-pokazivat-po" id="bt300">300</button>
            </div>
        </div>

        <table class="otpr-table">

            <tr class="tbl-title">
                <td>ID</td>
                <td>Тип</td>
                <td>Штрихкод</td>
                <td>Номер</td>
                <td>Текущее место</td>
                <td>Место назначения</td>
                <td>Место выгрузки</td>
                <td>Цена, руб.</td>
            </tr>

            <?php
            // выведем результаты
            for ($i=0; $i<count($rezu); $i++) {
                echo "<tr>";

                $link="template/TemplatePostingReceive.php?PostingBarcode=".$PostingBarcode.
                        "&CarriageBarcode=".$CarriageBarcode;

                echo "<td><a href=$link>". $rezu[$i]['PostingID']. "</a></td>";
                echo "<td>". $rezu[$i]['PostingID']. "</td>";
                echo "<td>". $rezu[$i]['PostingBarcode']. "</td>";
                echo "<td>". $rezu[$i]['PostingName']. "</td>";
                echo "<td>". $rezu[$i]['CarriageID']. "</td>";
                echo "<td>". $rezu[$i]['DestinationPlaceName']. "</td>";
                echo "<td>". $rezu[$i]['SourcePlaceName']. "</td>";
                echo "<td>". $rezu[$i]['PrepaymentAmount']. "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </section>

</div>

</body>
</html>