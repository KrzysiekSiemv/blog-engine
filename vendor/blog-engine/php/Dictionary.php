<?php
namespace BlogEngine {
    if(!defined("BLOG_TITLE"))
        require "_config.php";
    
    class Dictionary {
        public function PageTags() : array {
            return [
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

        public function BlogTags() : array {
            return [
                "{POST_TITLE}" => "title",
                "{POST_AUTHOR}" => "author",
                "{POST_CONTENT}" => "content",
                "{POST_ADDED}" => "added_at",
                "{POST_VIEWS}" => "views"
            ];
        }
    }
}
