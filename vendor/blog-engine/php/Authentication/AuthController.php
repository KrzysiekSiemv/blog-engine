<?php
namespace BlogEngine\Authentication {
    $file = "vendor/blog-engine/php/Database/DatabaseController.php";
    if(file_exists($file))
        require $file;
    else if(file_exists("../$file"))
        require "../$file";
    else if(file_exists("../../Database/DatabaseController.php"))
        require "../../Database/DatabaseController.php";

    use BlogEngine\Database\DatabaseController;

    class AuthController extends DatabaseController{

        public function Authenticate($login, $password, $remember) : bool|string {
            $status = false;
            $srv = $this->Connection();

            $check_auth = "SELECT login_token FROM users WHERE login = '$login' AND password = PASSWORD('$password');";
            $res = mysqli_query($srv, $check_auth);
            if(mysqli_num_rows($res) > 0){
                if($row = mysqli_fetch_row($res)){
                    if($remember)
                        setcookie("uToken_BE", $row[0], time()+24*60*60*7, "/");

                    if($_SESSION['uToken_BE'] = $row[0])
                        $status = true;
                    else
                        $status = "Błąd przy dodawaniu do sesji! (Authentication\AuthController.php:18)";
                } else
                    $status = mysqli_error($row);
            } else
                $status = "Nie ma takiego użytkownika";

            $this->Close($srv);
            return $status;
        }

        public function CheckRole($login_token) : string{
            $srv = $this->Connection();

            $check_role = "SELECT role FROM users WHERE login_token = '$login_token';";
            $res = mysqli_query($srv, $check_role);
            if(mysqli_num_rows($res) > 0){
                if($row = mysqli_fetch_row($res)){
                    return $row[0];
                } else
                    return mysqli_error($res);
            } else
                return "Nie ma użytkownika o takim tokenie";

            $this->Close($srv);
        }

        public function GetID($login_token) : int|string {
            $srv = $this->Connection();

            $check_id = "SELECT id_user FROM users WHERE login_token = '$login_token';";
            $res = mysqli_query($srv, $check_id);
            if(mysqli_num_rows($res) > 0){
                if($row = mysqli_fetch_row($res)){
                    return intval($row[0]);
                } else
                    return mysqli_error($res);
            } else
                return "Nie ma użytkownika o takim tokenie";

            $this->Close($srv);
        }

        public function NewUser($login, $password, $email, $role = 'user', $display_name = 'Użytkownik', $fname = '', $lname = '') : bool|string{
            $status = false;
            $srv = $this->Connection();

        }

        public function Valid($tekst) : string {
            $srv = $this->Connection();
            $tekst = htmlspecialchars($tekst);
            return mysqli_real_escape_string($srv, $tekst);
            $this->Close($srv);
        }

        public function ToHTML($tekst) : string {
            return htmlspecialchars_decode($tekst);
        }
    }
}