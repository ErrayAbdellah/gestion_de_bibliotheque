
<?php 
include('connect.php');
session_start();
unset($_SESSION["errorLogin"]) ;

//include('../database/connect.php');

if(isset($_POST['signIn']))    signIn() ;
 
   

if(isset($_POST['signUp']))         signUp() ;
if(isset($_POST['addNewBook']))     addNewBook() ;
if(isset($_POST['delete']))     delete() ;
if(isset($_POST['updateModel']))     update() ;
if(isset($_POST['Sell']))     sell() ;



function signIn()
{  
    
    $email = htmlspecialchars(trim(strtolower($_POST['SignIn_Email'])));
    $password =md5($_POST['SignIn_password']);
    $qrye = mysqli_query($GLOBALS['con'] , "SELECT *, count(*) as 'user'  from admin where email = '$email' and pasword = '$password'" ); 
    $qry = mysqli_fetch_assoc($qrye);
    if( $qry['user'] > 0)
    {
        $_SESSION['admin'] = $qry;

       
        header('Location:  Home.php') ;
    }
    else
    {
       $_SESSION['errorLogin'] = 'User is not registered <br> !! Please recheck email or password' ;
    //   return "check password !!!" ;
    }
}

function signUp()
{
    
    $nama = $_POST['txtName'];
    $LastNama = $_POST['txtLastName'];
    $date = $_POST['dateId'];
    $email = trim(strtolower($_POST['email']));
    $password = md5($_POST['passxord']);
    $qry = "INSERT INTO admin(name ,lastName,dateAdmin ,email ,pasword ) VALUES ('$nama','$LastNama','$date','$email','$password')";
    mysqli_query($GLOBALS['con'],$qry);
}

function addNewBook()
{
    $title      = $_POST['title'];
    $author     = $_POST['author'];
    $state      = $_POST['state'];
    $date       = $_POST['date'];
    $language   = $_POST['languge'];
    $price      = $_POST['price'];
    $admin      = $_SESSION['admin']['id'];
    $quantity   = $_POST['quantity'];
    $category   = $_POST['category'];

    if(isset($_FILES['formFile']))
    {
        $ImgName = $_FILES['formFile']['name'];
        $size = $_FILES['formFile']['size'];
        $tmp_name = $_FILES['formFile']['tmp_name'];
        $ImgError = $_FILES['formFile']['error'];
        if($ImgError ==0)
        {
            if($size <= 200000){

                $imgInfo = pathinfo($ImgName,PATHINFO_EXTENSION);
                $imgLower = strtolower($imgInfo);

                $arrayType = array("jpg","png","jpeg");
                if(in_array($imgLower,$arrayType))
                {
                    $newImageName = uniqid("IMG-",true).".".$imgLower;
                    $ImageUpload = 'assets/IMG/'.$newImageName;
                    move_uploaded_file($tmp_name,$ImageUpload);
                    
                    $qry = "INSERT INTO `books`(title, state, dateCreate, price, adminID, quntity, LanguagID, categoryID, author,image) 
                    VALUES ('$title','$state','$date',$price,$admin,$quantity,'$language',$category,'$author','$newImageName')" ;
                    mysqli_query($GLOBALS['con'],$qry);
                    
                    $_SESSION['message'] = "<strong>Success!</strong> book has been added successfully !!! !";
                    $_SESSION["addition"] = "success";
                    
                    
                }else
                {
                  
                    $_SESSION['message'] = "Sorry ! pictur not found !";
                    $_SESSION["addition"] = "danger";
                }
            }else
            {
                $_SESSION['message'] = "Sorry ! this picture if to large !";
                $_SESSION["addition"] = "warning";
            }

        }else{
               
            $_SESSION['message'] = "error";
            $_SESSION["addition"] = "warning";
        }
    }

    
}

function getBooks()
{
    $qry =mysqli_query($GLOBALS['con'],"SELECT b.*, l.name AS 'language',c.name AS 'category' FROM books b 
                                        INNER JOIN languag l ON l.id  = b.LanguagID 
                                        INNER JOIN category c ON c.id = b.categoryID WHERE  b.quntity > 0 and  b.adminID = ".$_SESSION['admin']['id']);

   
    while($row = mysqli_fetch_assoc($qry))
    {
        $id         = $row["id"];
        $title      = $row["title"];
        $author     = $row["author"];
        $state      = $row["state"];
        $date       = $row["dateCreate"];
        $language   = $row["language"];
        $language2  = $row["LanguagID"];
        $price      = $row["price"];
        $category   = $row['category'];
        $quantity   = $row['quntity'];
        $image      = $row['image'];
       
        ?>
                <div class="cards my-5 mx-3 ">
                    <div class="card shadow rounded-3">
                        <div class="p-auto d-flex justify-content-center" style="height: 10rem ;">
                            <img class="card-img-top w-50" src="./assets/IMG/<?php echo $image?>" alt="Company logo">
                            <!-- <h1><?php echo $image?></h1> -->
                        </div>
                        <div class="card-body py-0">
                            <h5 class="card-title"> <?php echo $title  ?></h5>
                            <ul class="list-group" style="height: 13rem;">
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-user"       style="font-size: 12px;"></i> <?php echo  $author    ;?>      </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-balloons"      style="font-size: 12px;"></i> <?php echo  $category  ;?>      </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-language"   style="font-size: 12px;"></i> <?php echo  $language  ;?>      </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-map-marker" style="font-size: 12px;"></i> <?php echo  $state     ;?>      </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-calendar"   style="font-size: 12px;"></i> <?php echo  $date      ;?>      </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-money-bill-1"      style="font-size: 12px;"></i> <?php echo  $price     ;?> MAD  </li>
                                <li class="list-group-item list-group-item-light" style="height: 14%; font-size: 14px;"><i class="fa fa-box-open"></i> <?php echo  $quantity  ?></li>
                            </ul>
                        </div>
                        <!-- <input type="text" id="bookID" name="bookID" value="<?php echo $id ;?>" hidden> -->
                        <div class=" d-flex justify-content-between pt-1">
                                <button type="button" class="btn btn-danger rounded-pill" 
                                id="left-panel-link" value ="<?php echo $row['id'] ;?>" 
                                data-bs-toggle="modal" data-bs-target="#SellBooks"
                                onclick="sell(<?php echo $id ;?>)">
                                Sell Now</button>
                            
                            <button type="submit" class="btn btn-dark rounded-pill"data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="right-panel-link" value="<?php echo $row['id'] ;?>" name="update"
                            onclick="getBook(<?php echo $id ;?> ,'<?php echo $title ; ?>' , '<?php echo $author ;?>' ,'<?php echo $language2 ;?>' , '<?php echo $state ;?>' , '<?php echo $date ;?>' ,<?php echo  $price ;?>,<?php echo  $quantity ;?>,<?php echo  $row['categoryID'] ;?> ,'<?php echo $image;?>')"
                            >Update</button>
                        </div>
                    </div>          
                </div>
            <?php
    }
}

function update() 
{
    $id = $_POST['idUpdate'];
    $title      = $_POST['title'];
    $author     = $_POST['author'];
    $state      = $_POST['state'];
    $date       = $_POST['date'];
    $language   = $_POST['languge'];
    $price      = $_POST['price'];
    $quantity   = $_POST['quantity'];
    $category   = $_POST['category'];
    $image      = $_POST['imgUpdate'];

    if(!empty($_FILES['formFile']))
    {
        $ImgName  = $_FILES['formFile']['name'];
        $size     = $_FILES['formFile']['size'];
        $tmp_name = $_FILES['formFile']['tmp_name'];
        $ImgError = $_FILES['formFile']['error'];
        if($ImgError ==0)
        {
            if($size <= 2000000)
            {

                $imgInfo = pathinfo($ImgName,PATHINFO_EXTENSION);
                $imgLower = strtolower($imgInfo);

                $arrayType = array("jpg","png","jpeg");
                if(in_array($imgLower,$arrayType))
                {
                    if(pathinfo($image,PATHINFO_EXTENSION)==="jpeg"){
                        
                        $newImageName = substr($image,-4);
                    }
                    else{
                        $newImageName = substr($image,-3);
                    }
                    echo $newImageName ; 
                    $new_img = $newImageName.$imgLower ;
                    $ImageUpload = 'assets/IMG/'.$new_img;
                    move_uploaded_file($tmp_name,$ImageUpload);    
                    $qry = "UPDATE `books` SET `title`='$title',`state`='$state',`dateCreate`='$date',`price`='$price',`quntity`='$quantity',`LanguagID`='$language',
                    `categoryID`='$category',`author`='$author',`image`='$new_img' WHERE id = ". $id ;
                    mysqli_query($GLOBALS['con'],$qry);
                    $_SESSION['message'] = "<strong>Success!</strong> Task has been updated successfully !";
                    $_SESSION["addition"] = "success";
                    // header('location: Home.php');
                    
                
                }else
                {
                    $_SESSION["addition"] = "danger" ;
                    $_SESSION['message']  = "Sorry ! this not picture," ;
                    // header("location: Home.php"); 
                }

            }else
            {  
                $_SESSION["addition"] = "danger" ;
                $_SESSION['message']  = "Sorry ! this picture if to large," ;
                // header("location: Home.php"); 
            }
        }
        else
        {
            $qryy = "UPDATE `books` SET `title`='$title',`state`='$state',`dateCreate`='$date',`price`='$price',`quntity`='$quantity',`LanguagID`='$language',
                    `categoryID`='$category',`author`='$author' WHERE id = ". $id ;
            mysqli_query($GLOBALS['con'],$qryy);
            $_SESSION["addition"] = "success";
            $_SESSION['message'] = "<strong>Success!</strong>book has been updated successfully !";
        }
    
   
    }
}
function delete()
{



    $id = $_POST['idUpdate'];
    $qry = "DELETE FROM `books` WHERE id = ".$id ;
    mysqli_query($GLOBALS['con'],$qry);
    $_SESSION["addition"] = "success";
    $_SESSION['message'] = "<strong>Success!</strong>Task has been deleted successfully !";


   // header('location: Home.php');
    
}

function sell()
{
    $dateSell = date("Y-m-d");

    $qnt    = $_POST['qnt'] ;
    $book   = $_POST['idBookSell'] ; 
    $checkQnt    = "SELECT quntity FROM books WHERE id = $book";
    $result =  mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$checkQnt));
    
    if($result['quntity']>=$qnt)
    {
        $qry    = "UPDATE books SET quntity = (quntity - $qnt) WHERE id = $book";
        $qry2   = "INSERT INTO sell( bookID,quantity,dateSell) VALUES ($book,$qnt,'$dateSell')";
        
        mysqli_query($GLOBALS['con'],$qry);
        mysqli_query($GLOBALS['con'],$qry2);
    }else{
        header('location: Home.php');

    }
}
?>