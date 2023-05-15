<?php
namespace BlogEngine\Posts {
    use BlogEngine\Authentication\AuthController;

    class PostController {
        public AuthController $auth;
        public function __construct($auth)
        {
            $this->auth = $auth;
        }

        public function CreatePost($title, $content, $name, $tags, $commentStatus, $add_time, $status){
            $srv = $this->auth->Connection();
            $date = (is_bool($add_time)?"NOW()":Date("'y-m-d H:i:s'", strtotime($add_time)));
            $status = ($status == "public"?"public":"draft");
            $id_user = $this->auth->GetID($_SESSION['uToken_BE']);

            $last_id_post = mysqli_fetch_row(mysqli_query($srv, "SELECT MAX(id_post) FROM posts;"))[0] + 1;

            $insert_post = "INSERT INTO posts VALUES($last_id_post, $id_user, '$name', '$title', '$content', '$status', '$commentStatus', 0, $date, null);";
            mysqli_query($srv, $insert_post);

            foreach ($tags as $tag){
                mysqli_query($srv, "INSERT INTO tag_to_post VALUES($tag, $last_id_post)");
            }

            header("location: ../../../be_panel.php?s=posts");
        }
    }
}