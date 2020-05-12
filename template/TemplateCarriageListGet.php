<?php
include 'head.html';
?>

<body>

<div class="vidimaja-oblast">

    <a href="CarriageReceive.php">Принять</a>

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

    <section class="search">
        <form action="ArticlePostingSearch.php" method="get" class="filter">
            <div>
                <div>
                    <label for="nomer">Номер</label>
                    <input type="text" id="nomer" name="Name">
                </div>

                <div>
                    <label for="fio">ФИО</label>
                    <input type="text" id="fio" name="PersonName">
                </div>

                <div>
                    <label for="strih-cod">Штрихкод</label>
                    <input type="text" id="strih-cod" name="Barcode">
                </div>

                <div>
                    <label for="mail">Email</label>
                    <input type="text" id="mail" name="mail">
                </div>
            </div>

            <input name="submit" type="submit" value="Найти">
        </form>
    </section>

<section class="otpravlenia">
	<div class="otpr-header">
		<span class="titul">Перевозки </span>
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
            <td>Состояние</td>
            <td>Номер перевозки</td>
            <td>ШК перевозки</td>
            <td>Наименование маршрута</td>
            <td>ID</td>
            <td>Description</td>
            <td>Moment</td>
            <td>Последнее место</td>
        </tr>

		<?php
		// выведем результаты
		for ($i=0; $i<count($rezu); $i++) {
            echo "<tr>";
            $link="CarriageContentGet.php?CarriageID=".$rezu[$i]['ID'];

            echo "<td>". $rezu[$i]['StateName']. "</td>";
            echo "<td>". $rezu[$i]['RouteID']. "</td>";
            echo "<td>". $rezu[$i]['Barcode']. "</td>";
            echo "<td>". $rezu[$i]['RouteName']. "</td>";
            echo "<td><a href=$link>". $rezu[$i]['ID']. "</a></td>";
            echo "<td>". $rezu[$i]['Description']. "</td>";
            echo "<td>". $rezu[$i]['Moment']. "</td>";
            echo "<td>". $rezu[$i]['Name']. "</td>";

            echo "</tr>";
        }
		?>
	</table>

</section>

</div>

</body>
</html>