<?php
namespace BlogEngine {
    require "_config.php";
    class Dictionary {
        public $page_tags = [
            "{POSTS}" => "",
            "{LINKS}" => "",
            "{FAVICON}" => (BLOG_ICON != ""?"<link rel='icon' type='image/png' href='" . BLOG_ICON . "' sizes='any'>":""),
            "{KEYWORDS}" => BLOG_TAGS,
            "{TITLE}" => BLOG_TITLE,
            "{AUTHOR}" => BLOG_AUTHOR,
            "{DESCRIPTION}" => BLOG_DESC,
            "{JS}" => "",
            "{CSS}" => ""
        ];
    }
}
