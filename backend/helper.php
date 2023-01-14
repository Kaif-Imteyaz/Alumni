<?php

class Helper
{
    public function validateParameters($data)
    {
        $type = $data->type;
        $name = $data->name;
        $email = $data->email;
        $id = $data->id;
        $yoj = $data->yoj;
        $password = $data->password;


        //validating $type [type of account]
        //acceptable type value -> alumini, faculty, student
<<<<<<< HEAD
        $type = gettype($type) == 'string' && array_search($type, ['alumni', 'faculty', 'student']) ? $type : false;
=======
        $type = gettype($type) == 'string' && array_search($type, ['alumini', 'faculty', 'student'])>-1 ? $type : false;
>>>>>>> yusuf
        if (!$type) {
            http_response_code(400);
            echo "please specify the type of account";
            exit;
        }
        //name should be of string and should not be blank and white space
        $name = gettype($name) == 'string' && strlen(trim($name)) > 0 ? trim($name) : false;
        if (!$name) {
            http_response_code(400);
            echo "please specify your name";
            exit;
        }
        //name should not be greater than 15 characters long
        if (strlen($name) > 15) {
            http_response_code(400);
            echo "name should not be more than 15 characters";
            exit;
        }
        //id should be string 
        if (gettype($id) != 'string') {
            http_response_code(400);
            echo "id should be valid string";
            exit;
        }
        //the length of id is set to be 9. [202003131]--> 9 characters
        if (strlen(trim($id)) != 9) {
            http_response_code(400);
            echo "invalid id";
            exit;
        }
        //trim removes white spaces from the left and right of the string.
        $id = trim($id);

        //email should not be empty string.
        if (strlen(trim($email)) <= 0) {
            http_response_code(400);
            echo "please enter your email";
            exit;
        }

        $email = trim($email);
        //checking for the valid format of the email
        if (!preg_match("/[a-zA-z0-9+_.-]+@[a-zA-Z0-9.-]+$/", $email)) {
            http_response_code(400);
            echo "please enter a valid email [special characters except \"+.-_@\" are not allowed]";
            exit;
        }
        if (strlen($email) > 30) {
            http_response_code(400);
            echo "Email shouldn't exceed 30 characters";
            exit;
        }

        //checking for the emptiness of year of joining input
        $yoj = gettype($yoj) == 'string' && strlen(trim($yoj)) > 0 ? trim($yoj) : false;
        if (!$yoj) {
            http_response_code(400);
            echo "please specify your year of joining";
            exit;
        }
        //---determining the current year of the student----

        $yoj = date_parse($yoj);
        $currentDate = date_parse(date('d-m-Y'));
        $currentYear = false;

        //checking if the year of joining is less than or equal to the current year.
        if ($currentDate['year'] >= $yoj['year']) {
            if ($currentDate['year'] == $yoj['year'] && $currentDate['month'] < $yoj['month']) {
                http_response_code(400);
                echo "You are yet to join Polytechnic!";
                exit;
            } else {
                $currentYear = $currentDate['year'] - $yoj['year'];
            }
        }
        // if year of joining is greater than the current year the student hasn't join the polytechnic yet.
        else {
            http_response_code(400);
            echo "You are yet to join Polytechnic!";
            exit;
        }
        //current year variable stores the current year of the student. This variable needs to be updated each year.
        if (gettype($currentYear) == 'boolean' && !$currentYear) {
            http_response_code(500);
            echo "some server issue";
            exit;
        }

        $password = gettype($password) == 'string' && strlen(trim($password)) > 0 ? trim($password) : false;
        if (!$password) {
            http_response_code(400);
            echo "please enter your password";
            exit;
        }

        if (strlen($password) < 10) {
            http_response_code(400);
            echo "password should be atleast 10 characters long";
            exit;
        }

        //hashing the password
        $password = sha1($password);

        //changing property of the data object with their new corresponding value.
        $data->type = $type;
        $data->name = $name;
        $data->email = $email;
        $data->id = $id;
        $data->currentYear = $currentYear;
        $data->password = $password;
    }

    public function generate_token(){
        $str = "";
        $n=10;
        $symbols = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789");
        for($i=0;$i<$n;$i++){
            $str = $str.$symbols[array_rand($symbols)];
        }
        return $str;
    }
    
}

?>