<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>PHP importer</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container">


<?php
require 'database.php';
require 'createTable.php';
require 'insertData.php';
if(isset($_POST['buttonImport'])) {
    copy($_FILES['xmlFile']['tmp_name'],
        'data/'.$_FILES['xmlFile']['name']);
    $xml = simplexml_load_file('data/'.$_FILES['xmlFile']['name']);
    echo "<br>";
    
    foreach($xml->children() as $child)
    {
        echo $child->getName() . ": " . $child . "<br>";
    }
    
    
    echo "<br>";
    //print_r($products);
    
    /*foreach($products as $product) {
        $stmt = $mysqli->prepare('insert into
            productimport(id, name, price, quantity)
            values(?, ?, ?, ?)');
        $stmt->bind_param('sssd', $product->id, $product->name, 
            $product->price, $product->quantity );
        $stmt->execute();    
    }*/
    
}
//$query = 'select * from productimport';
//$result = $mysqli->query($query);
//<input type="submit" value="Import" name="buttonImport">

?>


<h1>PHP Importer</h1>
<h5>Import XML File to MySQL in PHP</h5>
<form method="post" enctype="multipart/form-data">
  <div class="form-group">  
    <label for="inputFile">Input XML File</label>
    <input type="file" class="form-control-file" id="inputFile" name="xmlFile">
   </div>   
   <button type="submit" class="btn btn-primary btn-sm" name="buttonImport">Import</button>   

</form>




</body>
</html>
