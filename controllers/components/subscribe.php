<?php
App::import("Lib", "Urg.AbstractWidgetComponent");
class SubscribeComponent extends AbstractWidgetComponent  {
    function build_widget() {
        $settings = $this->widget_settings;

        $options = array();

        if (!isset($settings["title"])) {
            $settings["title"] = "Subscribe";
        }

        if (!isset($settings["message"])) {
            $settings["message"] = "Sign up now and receive all of our latest news!";
        }
        
        $this->set("subscribe_title", $settings["title"]);
        $this->set("subscribe_group_id", $settings["group_id"]);
        $this->set("subscribe_message", $settings["message"]);
    }
}
