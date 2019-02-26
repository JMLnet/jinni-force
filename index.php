<?php

require_once 'classes/class.main.php';
$classMain = new main;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_numeric($_GET['id']))
{
  $classMain->createFile($_GET['id']);
}

?>
<form method="post">
  <h3>Wybierz adres</h3>
  <ul>
    <li><button type="submit" name="id" value="1">https://www.elgordo.com/results/euromillonariaen.asp</button></li>
    <li><button type="submit" name="id" value="2">http://www.lotto.pl/lotto/wyniki-i-wygrane</button></li>
    <li><button type="submit" name="id" value="3">http://www.lotto.pl/eurojackpot/wyniki-i-wygrane</button></li>
  </ul>
</form>
