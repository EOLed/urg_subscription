<?
class NotifySubscribersBehavior extends ModelBehavior {
    function afterSave(&$model, $created) {
        CakeLog::write("debug", "notifying subscribers...");
        CakeLog::write("debug", "data: " . Debugger::exportVar($model->data[$model->alias], 3));

        $group_id = $model->data[$model->alias]["group_id"];

        $model->bindModel(array("hasMany" => array(
                "Subscription" => array(
                        "className" => "Subscription"
                )
        )));

        $subscriptions = $model->Subscription->find("all", array("conditions" => 
                array("Subscription.group_id" => $group_id)));

        App::import("Component", "Email");
        $email = new EmailComponent();

        foreach ($subscriptions as $subscription) {
            $email->to = $subscription["Subscription"]["email"];
            $email->from = "test@churchie.org";
            $email->subject = "test";
            $email->send("WTF");
            $email->reset();
        }

        CakeLog::write("debug", "subscriptions: " . Debugger::exportVar($subscriptions, 3));
    }
}
