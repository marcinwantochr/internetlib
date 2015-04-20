<?

function login(){
		print "<td align='left' valign='top'>";
		print "Zaloguj się lub <a href='rejestruj.php'>zarejestruj</a>";
		print '<form action="?menu=1" method="post">';
		print '<input name="login" type="text" value="login" onclick=this.value="" /><br />';
		print '<input name="password" type="password" value="xxx" onclick=this.value="" /><br />';
		print '<input name="submit" type="submit" value="Loguj"/>';
		print '</form>';
		print "</td>";
}

function listaKsiazek(){
				sortuj();
				global $sqlsort;
				global $sort;
				$querysort = mysql_query($sqlsort);
				print "<td align='left' valign='top'><table border='1'><tr><td><a href='?menu=1&sortby=id&sorttype=".$sort."'>ID</a></td><td><a href='?menu=1&sortby=autor&sorttype=".$sort."'>Autor</a></td><td><a href='?menu=1&sortby=tytul&sorttype=".$sort."'>Tytuł</a></td><td>STAN</td>";
    	    	while ($row = mysql_fetch_assoc($querysort)) {
				  print "<tr><td>".$row["id"]."</td>";
				  print "<td>".$row["autor"]."</td>";
				  print "<td>".$row["tytul"]."</td>";
				  if($row['stan']==1 ){
					  print '<td><font color="#33FF00">dostępna</font></td>';
				  } else {
					  print '<td><font color="#FF0000">niedostępna</font></td>';
				  }	
				  print "</tr>";
				}
				print "</table>";
}

function connect_db(){
	mysql_connect(DB_HOST, DB_USER, DB_PASS) or die ("bład 1");
	mysql_select_db(DB_DATABASE) or die ("bład 2");
}


function wyloguj(){
	if($_GET['logout']==1){
		session_destroy();
		header("Location: index.php?menu=".$_GET['menu']);
	}
}
function menu(){
	print "<a href='?menu=1'> LISTA</a><br />";
	if($_SESSION['permission']<=1){
	print "<a href='?menu=2'> DODAJ</a><br />";
	}
	if($_SESSION['permission']==0){
	print "<a href='?menu=3'> UŻYTKOWNICY</a><br />";
	}
}

function menu2(){
	print '<form action="?menu=2" method="post">';
	print '<input name="autor" type="text" value="Autor" onclick=this.value="" /><br />';
	print '<input name="tytul" type="text" value="Tytuł" onclick=this.value="" /><br />';
	print '<input name="stan" type="hidden" value="1"  /><br />';
	/*print '<select name="stan">
			<option value="0">0</option>
			<option value="1">1</option>
			</select>';*/
	print '<input name="submit2" type="submit" value="Dodaj książkę" />';
	print '</form>';
	if($_POST['submit2']){
		$sql = "INSERT INTO `biblioteka`.`books` (`id`, `autor`, `tytul`, `stan`, `user_id`) VALUES (NULL, '".$_POST['autor']."', '".$_POST['tytul']."', '".$_POST['stan']."', NULL);";
		$query = mysql_query($sql);
		print "KSIĄŻKA DODANA";
		
	}	
	$sqlsort = "SELECT * FROM `books` ORDER BY `tytul` ASC";
	$querysort = mysql_query($sqlsort);
	print "<table border='1'><tr><td>ID</td><td>Autor</td><td>Tytuł</td><td>Stan</td><td>Usuń</td>";
	while ($rowbook = mysql_fetch_assoc($querysort)) {
    		    print "<tr><td>".$rowbook["id"]."</td>";
				print "<td>".$rowbook["autor"]."</td>";
    		    print "<td>".$rowbook["tytul"]."</td>";			
				print "<td>".$rowbook["stan"]."</td>";
				print "<td><a href='?menu=".$_GET['menu']."&delbook=".$rowbook["id"]."'>Usuń</a>";	
				print "</tr>";
	}
	print "</table>";
	if($_GET['delbook']){
	$sql = "DELETE FROM `biblioteka`.`books` WHERE `books`.`id` = '".$_GET['delbook']."';";
	mysql_query($sql);
	header("Location: index.php?menu=".$_GET['menu']);
	}
	
	
}

function menu3(){
	$sql_user = "SELECT * FROM `user` ORDER BY `id` ASC";
	$sql_user = mysql_query($sql_user);
	global $menu;
	print "<table border='1'>";
	while ($row = mysql_fetch_assoc($sql_user)) {

	  print "<tr><form action='' method='post'><input type='hidden' name='id' value='".$row["id"]."' /><td>".$row["id"]."</td>";
	  print "<td>".$row["login"]."</td>";
	  print "<td>".$row["name"]."</td>";
	  print "<td>".$row["sname"]."</td>";
	  print "<td><input size='1' type='text' name='perm' value='".$row["permission"]."' /><input type='submit' name='permsubmit' value='Zastosuj'/></td>";
	  print "</form></tr>";
	  if($_POST['permsubmit']){
		  $sql_perm="UPDATE `biblioteka`.`user` SET `permission` = '".$_POST['perm']."' WHERE `user`.`id` =  '".$_POST['id']."';";
		  mysql_query($sql_perm);
		  header('Location: ?menu='.$menu);
	  }
	}
	print "</table>";
}

function sortuj(){
				global $sqlsort;
				global $sort;
				global $sorttype;
				global $sortby;
				$sortby = $_GET['sortby'];
				$sorttype = $_GET['sorttype'];
				if($sortby=="tytul"){
					if($sorttype=="desc"){
						$sort="asc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `tytul` ASC";
					} else {
						$sort="desc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `tytul` DESC";
					}
				} elseif($sortby=="id") {
					if($sorttype=="desc"){
						$sort="asc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `id` ASC";
					} else {
						$sort="desc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `id` DESC";
					}
				} else {
					if($sorttype=="desc"){						
						$sort="asc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `autor` ASC";
					} else {
						$sort="desc";
						$sqlsort = "SELECT * FROM `books` ORDER BY `autor` DESC";
					}
				}
				return $sqlsort;
				return $sort;
}

?>