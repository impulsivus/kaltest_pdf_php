<?php
   if(isset($_FILES['pdf'])){
      $errors= array();
      $file_name = $_FILES['pdf']['name'];
      $file_size =$_FILES['pdf']['size'];
      $file_tmp =$_FILES['pdf']['tmp_name'];
      $file_type=$_FILES['pdf']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['pdf']['name'])));
      
      $extensions= array("pdf");
      
      if(in_array($file_ext,$extensions)=== false){
         $errors[]="extension not allowed, please choose a pdf file.";
      }
      
      
      if(empty($errors)==true){
         move_uploaded_file($file_tmp,"pdf/".$file_name);
         echo "Success";
         header("Location: excelformat.php?file=pdf/".$file_name);
      }else{
         print_r($errors);
      }
   }
?>
<html>
   <body>
      
      <form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="pdf" />
         <input type="submit"/>
      </form>
      
   </body>
</html>