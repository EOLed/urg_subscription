<?php
class SubscribeComponent extends Object {
    var $controller = null;
    var $settings = null;

    function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
        $this->settings = $settings;
    }

    function build($widget_id) {
        $settings = $this->settings[$widget_id];

        $options = array();

        if (!isset($settings["title"])) {
            $settings["title"] = "Subscribe";
        }

        if (!isset($settings["message"])) {
            $settings["message"] = "Sign up now and receive all of our latest news!";
        }
        
        $this->controller->set("subscribe_title_$widget_id", $settings["title"]);
        $this->controller->set("subscribe_group_id_$widget_id", $settings["group_id"]);
        $this->controller->set("subscribe_message_$widget_id", $settings["message"]);
    }
}
