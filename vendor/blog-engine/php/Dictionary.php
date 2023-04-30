<?php
namespace BlogEngine {
    require "_config.php";
    class Dictionary {
        public $page_tags = [
            "{POSTS}" => "",
            "{LINKS}" => "",
            "{KEYWORDS}" => BLOG_TAGS,
            "{TITLE}" => BLOG_TITLE,
            "{AUTHOR}" => BLOG_AUTHOR,
            "{DESCRIPTION}" => BLOG_DESC,
            "{JS}" => "",
            "{CSS}" => ""
        ];
    }
}
