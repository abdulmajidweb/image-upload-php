<?php 
include 'inc/header.php';
include 'lib/Database.php';
 ?>


<section class="container alert alert-primary">
<?php
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$permited = array('jpg', 'jpeg', 'png', 'gif');
	$file_name = $_FILES['image']['name'];
	$file_size = $_FILES['image']['size'];
	$file_tmp = $_FILES['image']['tmp_name'];

	$div = explode(".", $file_name);
	$file_ext = strtolower(end($div));
	$unique_image = substr(md5(time()), 0, 10) . '.' . $file_ext;
	$upload_image = "uploads/".$unique_image;

	if (empty($file_name)) {
		echo "<span class='text-danger'>Error! Please, select an image.</span>";
	}elseif ($file_size>1048576) {
		echo "<span class='text-danger'>Error! Image size should be less than 1 MB.</span>";
	}elseif (in_array($file_ext, $permited) == false) {
		echo "<span class='text-danger'>Error! You can upload only :- ".implode(",", $permited)."</span>";
	}else{
		move_uploaded_file($file_tmp, $upload_image);
		$query = "INSERT INTO image(img) VALUES('$upload_image')";
		$inserted_rows = $db->insert($query);
		if ($inserted_rows) {
			echo "<span class='text-success'>Image inserted successfully.</span>";
		}else{
			echo "<span class='text-danger'>Error! Image not inserted.</span>";
		}
	}

	
}
?>
	<form action="" method="POST" enctype="multipart/form-data">
		<div class="form-group">
			<label for="image">Select an image</label>
			<input type="file" name="image" id="image" class="form-control-file" />
		</div>
		<button type="submit" name="upload" class="btn btn-success">Upload</button>

	</form>






<table class="table bg-info">
	<tr>
		<th scope="col">Serial</th>
		<th scope="col">Image</th>
		<th scope="col">Action</th>
	</tr>

<?php
if (isset($_GET['del'])) {
	$id = $_GET['del'];

	$getquery = "SELECT * FROM image WHERE id = '$id'";
	$getImg = $db->select($getquery);
	if ($getImg) {
		while ($imgdata = $getImg->fetch_assoc()) {
			$delimg = $imgdata['img'];
			unlink($delimg);
		}
	}
	

	$query = "DELETE FROM image WHERE id ='$id'";
	$delImage = $db->delete($query);
	if ($delImage) {
		echo "<span class='text-success'>Image deleted successfully.</span>";
	}else{
		echo "<span class='text-danger'>Image not deleted.</span>";
	}
}
?>

<?php
$query = "SELECT * FROM image";
$getImage = $db->select($query);
if ($getImage) {
	$i = 0;
	while ($result = $getImage->fetch_assoc()) {
		$i++;
?>

	<tr>
		<td><?php echo $i; ?></td>
		<td><img src="<?php echo $result['img']; ?>" width="200px"></td>
		<td><a href="?del=<?php echo $result['id']; ?>" class="btn btn-danger" onclick="confirm('Are you sure to delete your photo?')">Delete</a></td>
	</tr>
	<?php } } ?>
</table>

</section>

<?php include 'inc/footer.php'; ?>