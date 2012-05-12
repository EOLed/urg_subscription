<?php
App::uses("FlyLoaderComponent", "Controller/Component");
App::uses("AbstractWidgetComponent", "Urg.Lib");
App::uses("ImgLibComponent", "ImgLib.Controller/Component");
App::uses("CakeEmail", "Network/Email");
App::uses("MarkdownHelper", "Markdown.View/Helper");
class NotifySubscribersComponent extends AbstractWidgetComponent {
    var $IMAGES = "/app/Plugin/UrgPost/webroot/img";

    var $components = array("Session", "ImgLib.ImgLib", "FlyLoader");

    var $email_delivery;

    private $__reply_to_cache = array();

    function build_widget() {
    }
    
    function __is_assoc ($arr) {
        return (is_array($arr) && count(array_filter(array_keys($arr),'is_string')) == count($arr));
    }

    function __get_sender() {
        CakeLog::write(LOG_DEBUG, "widget settings to convert to email sender: " . Debugger::exportVar($this->widget_settings, 5));
        if (!isset($this->widget_settings["reply_to"]))
            return null;

        $reply_to = $this->widget_settings["reply_to"];
        
        if ($this->__is_assoc($reply_to)) {
            return array("email" => $this->__get_reply_to_val($reply_to["email"]),
                         "name" => $this->__get_reply_to_val($reply_to["name"]));
        } else {
            return $reply_to;
        }
    }

    function __get_reply_to_val($key) {
        $model = $key["model"];
        $logged_user = $this->Session->read("User");

        $object = null;
        // if exists, get object from cache
        if (isset($this->__reply_to_cache[$model][$logged_user["User"]["id"]])) {
            $object = $this->__reply_to_cache[$model][$logged_user["User"]["id"]];
        } else {
            $this->controller->loadModel($model);
            $object = $this->controller->{$model}->findByUserId($logged_user["User"]["id"]);
            $this->__reply_to_cache[$model][$logged_user["User"]["id"]] = $object;
        }

        CakeLog::write(LOG_DEBUG, "the reply to object: " . Debugger::exportVar($object, 5));

        return $object[$model][$key["field"]];
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
            $email_config = Configure::read("Email.config");

            $email = $email_config == "default" ? new CakeEmail() : new CakeEmail($email_config);

            $email->helpers(array("HtmlText", "Html", "Markdown.Markdown"));

            $sender = $this->__get_sender();
            CakeLog::write(LOG_DEBUG, "email sender: " . Debugger::exportVar($sender, 5));
            $from = "no-reply@churchie.org";
            if (isset($sender["name"])) {
                $from = array($sender["email"] => $sender["name"]);
            } else {
                if (isset($this->widget_settings["reply_to_name"])) {
                    $from = array($from => $this->widget_settings["reply_to_name"]);
                } else {
                    $logged_user = $this->Session->read("User");
                    $from = array($from => $logged_user["User"]["username"]);
                }

                if ($sender != null) {
                    $email->replyTo($sender["email"]);
                }
            }

            $email->from($from)
                  ->to($subscription["Subscription"]["email"])
                  ->subject($this->controller->request->data["Post"]["title"])
                  ->template("UrgPost.banner")
                  ->emailFormat("both");

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
