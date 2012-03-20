<?php
App::import("Component", "FlyLoader");
App::import("Lib", "Urg.AbstractWidgetComponent");
App::import("Component", "ImgLib.ImgLib");
class NotifySubscribersComponent extends AbstractWidgetComponent {
    var $IMAGES = "/app/plugins/urg_post/webroot/img";

    var $components = array("Email", "ImgLib", "FlyLoader");

    var $email_delivery;

    function build_widget() {
        Configure::load("config");
        $this->email_delivery = Configure::read("Email.delivery");

        if ($this->email_delivery == "" || $this->email_delivery == null) {
            $this->email_delivery = "mail";
        }
    }
    
    function execute() {
        $group_id = $this->controller->data["Post"]["group_id"];

        $banners = $this->get_banners($this->controller->data);

        $this->controller->Post->bindModel(array("hasMany" => array(
                "Subscription" => array(
                        "className" => "Subscription"
                )
        )));

        $subscriptions = $this->controller->Post->Subscription->find("all", array("conditions" => 
                array("Subscription.group_id" => $group_id)));

        Configure::load("config");

        foreach ($subscriptions as $subscription) {
            $this->controller->FlyLoader->load("Helper", "HtmlText");
            $this->controller->FlyLoader->load("Component", "Email");
            $this->controller->Email->to = $subscription["Subscription"]["email"];
            $from = "no-reply@churchie.org";
            if (isset($this->widget_settings["reply_to_name"])) {
                $from = $this->widget_settings["reply_to_name"] . " <$from>";
            }
            $this->controller->Email->from = $from;
            $this->controller->Email->subject = $this->controller->data["Post"]["title"];

            $this->controller->Email->delivery = $this->email_delivery;
            $this->controller->Email->template = "banner";

            $this->controller->Email->sendAs = "both";

            if (isset($this->widget_settings["reply_to"])) {
                $this->controller->Email->replyTo = $this->widget_settings["reply_to"];
            }

            if (sizeof($banners) > 0) {
                $this->controller->set("banner", $banners[0]);
            }

            $this->controller->set("post", $this->controller->data);

            $this->controller->set("subscription", $subscription);

            if ($this->email_delivery == "smtp") {
                $this->controller->Email->smtpOptions = array(
                        "port" => Configure::read("Email.smtpPort"),
                        "timeout" => Configure::read("Email.smtpTimeout"),
                        "host" => Configure::read("Email.smtpHost"),
                        "username" => Configure::read("Email.smtpUsername"),
                        "password" => Configure::read("Email.smtpPassword")
                );
            }
            $this->controller->Email->send();
            $this->controller->Email->reset();
        }
    }

    function get_banners($post) {
        $this->controller->loadModel("Attachment");
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
