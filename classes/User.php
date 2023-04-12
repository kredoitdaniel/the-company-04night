<?php

require_once "Database.php";

class User extends Database
{
    // store()
    public function store($request)
    {
        // $request will catch the data from the $_POST in actions/register.php
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $password   = $request['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql_userCheck = "SELECT * FROM users WHERE username = '$username'";

        if($result = $this->conn->query($sql_userCheck)){
            if($result->num_rows != 0){
                die('Username is already existing!');
            } else {
                $sql = "INSERT INTO users (first_name, last_name, username, password) VALUES ('$first_name', '$last_name', '$username', '$password')";

                if($this->conn->query($sql)){
                    header('location: ../views'); // go to index.php which is the login page
                    exit;
                } else {
                    die('Error creating user: ' . $this->conn->error);
                }
            }
        }    
    }// end store()



    // login()
    public function login($request)
    {
        $username = $request['username'];
        $password = $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($sql);

        # Check the username
        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            // $user = ['id' => 1, 'first_name' => 'dan'];

            # Check if the password is correct
            if(password_verify($password, $user['password'])){
                # create session variables
                session_start();
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['first_name'] . " " . $user['last_name'];

                header('location: ../views/dashboard.php');
                exit;
            } else {
                die('Password is incorrect!');
            }
        } else {
            die('Username not found!');
        }
    }
    // end login()


    // logout()
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
    }
    // logout()


    // getAllUsers()
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        if($result = $this->conn->query($sql)){
            return $result;
        } else {
            die('Error retrieving all users: ' . $this->conn->error);
        }
    }
    // getAllUsers()


    // getUser()
    public function getUser()
    {
        // $_SESSION['id'] is the ID of the logged in user
        $id = $_SESSION['id'];

        $sql = "SELECT * FROM users WHERE id = $id";

        if($result = $this->conn->query($sql)){
            return $result->fetch_assoc();
            // ['id' => 1, 'first_name' => 'dan']
        } else {
            die('Error retrieving the user: ' . $this->conn->error);
        }
    }
    // end getUser()


    // update()
    public function update($request, $files)
    {
        session_start();
        $id         = $_SESSION['id'];
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $photo_name = $files['photo']['name'];
        $tmp_photo  = $files['photo']['tmp_name'];
        // photo is the name of the input
        // name is the name of the actual image
        // tmp_name is the temporary storage of the image

        $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

        if($this->conn->query($sql)){
            $_SESSION['username']  = $username;
            $_SESSION['full_name'] = $first_name . " " . $last_name;

            # if there is a new uploaded photo name, save it to the db and save the actual image to images folder.
            if($photo_name){
                $sql = "UPDATE users SET photo = '$photo_name' WHERE id = $id";
                $destination = "../assets/images/$photo_name";

                # Save the image name to the database
                if($this->conn->query($sql)){
                    #Save the image to images folder
                    if(move_uploaded_file($tmp_photo, $destination)){
                        header('location: ../views/dashboard.php');
                        exit;
                    } else {
                        die('Error moving the photo.');
                    }
                } else {
                    die('Error uploading photo: ' . $this->conn->error);
                }
            }
            header('location: ../views/dashboard.php');
            exit;
        } else {
            die('Error updating the user: ' . $this->conn->error);
        }
    }
    // end update()


    // delete()
    public function delete()
    {
        session_start();
        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if($this->conn->query($sql)){
            $this->logout();
        } else {
            die('Error deleting your account: ' . $this->conn->error);
        }
    }
    // end delete()

}