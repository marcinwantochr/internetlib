<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rejestracja</title>
</head>

<body>
<table>
<form action="" method="post">
<tr><td>Login:</td><td><input type="text" name="login" /></td></tr>
<tr><td>Hasło:</td><td><input type="password" name="password" /></td></tr>
<tr><td>Imię:</td><td><input type="text" name="name" /></td></tr>
<tr><td>Nazwisko:</td><td><input type="text" name="sname" /></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="Rejestruj" /></td></tr>
</form>
</table>
<?
require_once('config.php');
require_once('functions.php');

connect_db();
if($_POST['submit'] && $_POST['login']!="" && $_POST['password']!="" && $_POST['name']!=""){
	$result = mysql_query("SELECT `login` FROM `user` WHERE `login` = '".$_POST['login']."'");
	$num_rows = mysql_num_rows($result);
	if($num_rows==0){
		$sql = "INSERT INTO `biblioteka`.`user` (`id`, `login`, `password`, `name`, `sname`, `permission`) VALUES (NULL, '".$_POST['login']."', '".$_POST['password']."', '".$_POST['name']."', '".$_POST['sname']."', '2');";
		mysql_query($sql);
		print "Konto utworzone. <a href='index.php'>Zaloguj się</a>";
	} else {
		print "Taki login już istnieje";
	}
} else {
	print "Wypełnij wszystkie pola";
}
?>
</body>
</html>