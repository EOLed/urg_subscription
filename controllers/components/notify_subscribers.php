<?php
class NotifySubscribersComponent extends Object {
    var $controller = null;
    var $settings = null;
    var $components = array("Email");

    function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
        $this->settings = $settings;
    }

    function build($widget_id) {
    }
    
    function execute() {
        $group_id = $this->controller->data["Post"]["group_id"];

        $this->controller->Post->bindModel(array("hasMany" => array(
                "Subscription" => array(
                        "className" => "Subscription"
                )
        )));

        $subscriptions = $this->controller->Post->Subscription->find("all", array("conditions" => 
                array("Subscription.group_id" => $group_id)));

        foreach ($subscriptions as $subscription) {
            $this->Email->to = $subscription["Subscription"]["email"];
            $this->Email->from = "test@churchie.org";
            $this->Email->subject = "test";
            $this->Email->send("WTF");
            $this->Email->reset();
        }
    }
}
