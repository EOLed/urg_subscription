<?php
App::uses("SubscribeComponent", "UrgSubscription.Controller/Component");

class I18nSubscribeComponent extends SubscribeComponent {
    function build_widget() {
        $this->widget_settings = $this->widget_settings[$this->controller->Session->read("Config.language")];
        parent::build_widget();
    }
}
