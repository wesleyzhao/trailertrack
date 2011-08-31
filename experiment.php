<html> <body>
<form action="experiment.php" method="POST">
<input type="text" name="num1" />
<input type="text" name="num2" />
<input type="submit" value="submit" />
</form>
<?php
echo "You just typed in " . $_POST['num1'] . " and " . $_POST['num2'];
echo "<br/>The sum is " . $_POST['num1'] + $_POST['num2'];
?>
</body> </html>