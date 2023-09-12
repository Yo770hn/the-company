<?php
require_once "Database.php";

class User extends Database {
    // create
    // $request holds the data from the FORM of register
    // "store" name could be createData 
    public function store($request) {
        // save the user to the db
        /*
        $_POST['first_name']; -> $request['first_name'];
        $_POST['last_name'];  -> $request['last_name'];
        $_POST['username'];   -> $request['username'];
        $_POST['password'];   -> $request['password'];
        */

       $first_name = $request['first_name'];
       $last_name = $request['last_name'];
       $username =  $request['username'];
       $password =  $request['password'];

        $sqlUserCheck ="SELECT * FROM users WHERE username ='$username'";

        if($result = $this->conn->query($sqlUserCheck)){
            if ($result->num_rows != 0){
               echo "<p>Username already exist!</P>" ;
            } else {

                $password = password_hash($password, PASSWORD_DEFAULT);
                
                    $sql = "INSERT INTO users (first_name, last_name, username, password) VALUES ('$first_name', '$last_name','$username','$password')";

                    if($this->conn->query($sql)){
                        header('location: ../views'); // go to index.oho which is the login page
                    } else {
                        die('Error creating the user:' .$this->conn->error);
                    }
            }
        }    
    }


    // read
    // login()
    public function login($request){
        $username =  $request['username'];
        $password =  $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username' ";
        
        $result = $this->conn->query($sql);

        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            // $user is now the array name
            // check if the password is correct
            // check if the password is correct

            if(password_verify($password,$user['password'])){
                #create session variables for future use.
                session_start();
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['first_name']." ".$user['last_name'];

                header('location:../views/dashboard.php');
                exit;
            }else{
                die('password is incorrect');
            }
        }else{
            die('Username not found.');
        }


    }

public function logout(){
session_start();
session_unset();
session_destroy();
header("location: ../views/");
exit;

}


// getAllusers()
public function getAllUsers(){


    $sql = "SELECT * FROM `users`";

    If($result =$this->conn->query($sql)){
        return $result;
    }else{
        die("Error retrieving all users: " .$this->conn->error);
    }

    }

    // update
    // delete


public function getUser(){

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM `users`";
    If($result =$this->conn->query($sql)){
        return $result->fetch_assoc();
    }else{
        die("Error retrieving the users: " .$this->conn->error);
    }

}


public function update($request, $files){

        //$request = ['first_name] 
    session_start();
    $id = $_SESSION['id'];
    $first_name = $request['first_name'];
    $last_name = $request['last_name'];
    $username = $request['username'];
    $photo = $files['photo']['name'];
    $tmp_photo = $files['photo']['tmp_name'];

    $sql="UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = '$id' ";

    if($this->conn->query($sql)){
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = "$first_name $last_name";

        #if there's an uploaded photo, save it to the db and save the file to the images folder.(assets/images)
        if($photo){
            $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
            $destination = "../assets/images/$photo";
        
            if($this->conn->query($sql)){
                move_uploaded_file($tmp_photo, $destination);
                header('location: ../views/dashboard.php');
                exit;
            }else{
                die('Error moving the photo');
            }
        header('location: ../views/dashboard.php');
        exit;
        }else{
            die('Error updating the user: '.$this->conn->error);
        }        
    }
    }

    public function delete(){
        session_start();
        $id = $_SESSION['id'];
        $sql="DELETE FROM users WHERE id = '$id' ";

        if ($this->conn->query($sql)){
            $this->logout();
        }else{
            die('Error deleting your account: '.$this->conn->error);
        }
        }


}