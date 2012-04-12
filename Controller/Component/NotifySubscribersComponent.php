<?php
App::uses("FlyLoaderComponent", "Controller/Component");
App::uses("AbstractWidgetComponent", "Urg.Lib");
App::uses("ImgLibComponent", "ImgLib.Controller/Component");
App::uses("CakeEmail", "Network/Email");
App::uses("MarkdownHelper", "Markdown.View/Helper");
class NotifySubscribersComponent extends AbstractWidgetComponent {
    var $IMAGES = "/app/Plugin/UrgPost/webroot/img";

    var $components = array("ImgLib.ImgLib", "FlyLoader");

    var $email_delivery;

    function build_widget() {
    }
    
    function execute() {
        $group_id = $this->controller->request->data["Post"]["group_id"];

        $banners = $this->get_banners($this->controller->request->data);

        $this->controller->Post->bindModel(array("hasMany" => array(
                "Subscription" => array(
                        "className" => "Subscription"
                )
        )));

        $subscriptions = $this->controller->Post->Subscription->find("all", array("conditions" => 
                array("Subscription.group_id" => $group_id)));

        Configure::load("config");

        foreach ($subscriptions as $subscription) {
            $email = new CakeEmail(Configure::read("Email.config"));

            $email->helpers(array("HtmlText", "Html", "Markdown.Markdown"));

            $from = "no-reply@churchie.org";
            if (isset($this->widget_settings["reply_to_name"])) {
                $from = array($this->widget_settings["reply_to_name"] => $from);
            }

            $email->from($from)
                  ->to($subscription["Subscription"]["email"])
                  ->subject($this->controller->request->data["Post"]["title"])
                  ->template("UrgPost.banner")
                  ->emailFormat("both");


            if (isset($this->widget_settings["reply_to"])) {
                $email->replyTo($this->widget_settings["reply_to"]);
            }

            $vars = array();
            if (sizeof($banners) > 0) {
                $vars["banner"] = $banners[0];
            }

            $vars["post"] = $this->controller->request->data;
            $vars["subscription"] = $subscription;

            $email->viewVars($vars);

            $email->send();
        }
    }

    function get_banners($post) {
        $this->controller->loadModel("UrgPost.Attachment");
        $this->controller->Attachment->bindModel(array("belongsTo" => array("AttachmentType")));

        $banner_type = $this->controller->Attachment->AttachmentType->findByName("Banner");

        $banners = array();

        if (isset($post["Attachment"])) {
            Configure::load("config");
            foreach ($post["Attachment"] as $attachment) {
                if ($attachment["attachment_type_id"] == $banner_type["AttachmentType"]["id"]) {
                    array_push($banners, $this->get_image_path($attachment["filename"],
                                                               $post,
                                                               Configure::read("Banner.defaultWidth")));
                }
            }
        }

        return $banners;
    }

    function get_image_path($filename, $post, $width, $height = 0) {
        $this->controller->FlyLoader->load("Component", "ImgLib.ImgLib");
        
        $full_image_path = $this->get_doc_root($this->IMAGES) . "/" .  $post["Post"]["id"];
        $image = $this->controller->ImgLib->get_image("$full_image_path/$filename", $width, $height, 'landscape'); 
        return "/urg_post/img/" . $post["Post"]["id"] . "/" . $image["filename"];
    }

    function get_doc_root($root = null) {
        $doc_root = $this->remove_trailing_slash(env('DOCUMENT_ROOT'));

        if ($root != null) {
            $root = $this->remove_trailing_slash($root);
            $doc_root .=  $root;
        }

        return $doc_root;
    }

    /**
     * Removes the trailing slash from the string specified.
     * @param $string the string to remove the trailing slash from.
     */
    function remove_trailing_slash($string) {
        $string_length = strlen($string);
        if (strrpos($string, "/") === $string_length - 1) {
            $string = substr($string, 0, $string_length - 1);
        }

        return $string;
    }
}
