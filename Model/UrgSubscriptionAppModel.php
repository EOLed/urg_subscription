<?php
App::uses("UrgComponent", "Urg.Controller/Component");
class UrgSubscriptionAppModel extends AppModel {
    var $components = array(
           "Auth" => array(
                   "loginAction" => array(
                           "plugin" => "urg",
                           "controller" => "users",
                           "action" => "login",
                           "admin" => false
                   )
           ), "Urg.Urg"
    );

    function log($msg, $type = LOG_ERROR) {
    	$trace = debug_backtrace();
        parent::log("[" . $this->toString() . "::" . $trace[1]["function"] . "()] $msg", $type);
    }
}
