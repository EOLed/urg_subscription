<?php
App::uses("AbstractWidgetComponent", "Urg.Lib");
class SubscribeComponent extends AbstractWidgetComponent  {
    function build_widget() {
        $settings = $this->widget_settings;
        if (isset($settings["group_slug"])) {
            $group = $this->controller->Group->findBySlug($settings["group_slug"]);
            $settings["group_id"] = $group["Group"]["id"];
        }

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
