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
    
    function execQuery($conn, $sql_st) {
        $res = $conn->query($sql_st);
        $row=mysqli_fetch_row($res);
        return $row[0];
    }
          
    foreach($xml->network as $network) {               
        foreach($network->service as $service) {                     
            foreach($service->event as $event) {                               
                foreach($event->language as $language) {
                                                                                                  
                    $stmt = $mysqli->prepare('insert into
                        service_livetv_program(ext_program_id, show_type, long_title, duration, iso_2_lang, updated_at, deleted_at)
                        values(?, ?, ?, ?, ?, ?, ?)');                    
                    $stmt->bind_param('issssss', $programId, $show, $language->short_event['name'], $event['duration'],
                        $language['code'], $default, $default);                     
                    $programId = (int)$event['id'];
                    $show = 'other';
                    $default = '0000-00-00 00:00:00';                   
                    $stmt->execute();                   
                                                                               
                }                                
            }                       
        }        
    }
 
    foreach($xml->network as $network) {        
        foreach($network->service as $service) {           
            foreach($service->event as $event) {                
                foreach($event->language as $language) {
                                                            
                    // calculate channel_id and program_id
                    $q_channel = 'select id from service_livetv_channel where source_id = ' . $service['id']; 
                    $q_program = 'select id from service_livetv_program where ext_program_id = ' . $event['id'];
                    $qArray = [$q_channel, $q_program];
                    $rArray = [];                    
                    foreach($qArray as $item => $sql) {                        
                        $rArray[$item] = execQuery($mysqli, $sql);                                                  
                    }
                    
                    // calculate end_time
                    $sql = 'select time_to_sec("' . $event['duration'] . '")';
                    $duration_in_sec = execQuery($mysqli, $sql);
                    $sql = 'select timestampadd(second,' . $duration_in_sec . ',"' . $event['start_time'] . '")';
                    $end_t = execQuery($mysqli, $sql);
                             
                    $stmt = $mysqli->prepare('insert into
                        service_livetv_schedule(ext_schedule_id, channel_id, start_time, end_time, run_time,
                        program_id, is_live, updated_at, deleted_at)
                        values(?, ?, ?, ?, ?, ?, ?, ?, ?)');                    
                    $stmt->bind_param('iisssiiss', $scheduleId, $rArray[0], $event['start_time'], $end_t, $event['duration'],
                        $rArray[1], $live, $default, $default);                    
                    $scheduleId = (int)$event['id'];                  
                    $live = 0;
                    $default = '0000-00-00 00:00:00'; 
                    $stmt->execute();
                                                                           
                }                
            }            
        }        
    }
        
}


$sql_channel = 'select * from service_livetv_channel';
$sql_program = 'select * from service_livetv_program';
$sql_schedule = 'select * from service_livetv_schedule';
$queryset = [$sql_channel, $sql_program, $sql_schedule];
$resset = [];

foreach($queryset as $item => $sql){
    $resset[$item] = $mysqli->query($sql);
}

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
<div style="margin-top: 20px;">
<h3>Channel</h3>
<table class="table table-sm table-bordered" style="font-size: 0.8em;">
<thead>
<tr>
<th scope="col">id</th>
<th scope="col">uuid</th>
<th scope="col">source_id</th>
<th scope="col">short_name</th>
<th scope="col">full_name</th>
<th scope="col">time_zone</th>
<th scope="col">primary_language</th>
<th scope="col">weight</th>
<th scope="col">created_at</th>
<th scope="col">updated_at</th>
<th scope="col">deleted_at</th>
</tr>
</thead>
<?php while($obj = $resset[0]->fetch_object()) { ?>
	<tr>
		<td><?php echo $obj->id; ?></td>
		<td><?php echo $obj->uuid; ?></td>
		<td><?php echo $obj->source_id; ?></td>
		<td><?php echo $obj->short_name; ?></td>
		<td><?php echo $obj->full_name; ?></td>	
		<td><?php echo $obj->time_zone; ?></td>	
		<td><?php echo $obj->primary_language; ?></td>	
		<td><?php echo $obj->weight; ?></td>															
		<td><?php echo $obj->created_at; ?></td>
		<td><?php echo $obj->updated_at; ?></td>
		<td><?php echo $obj->deleted_at; ?></td>
	</tr>	
	<?php } ?>
</table>
</div>

</body>
</html>
