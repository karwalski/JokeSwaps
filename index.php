<?PHP

echo 'This is a placeholder for the index.php page';

$user = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));

echo '<BR /> User: ' . $user;


?>