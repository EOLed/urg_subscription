<?php
class SubscribeHelper extends AppHelper {
    var $helpers = array("Html", "Time", "Form");
    var $widget_options = array("subscribe_title", "subscribe_group_id", "subscribe_message");

    function build($options = array()) {
        $this->Html->css("/urg_subscription/css/urg_subscription.css", null, array("inline"=>false));
        $title = $this->Html->tag("h2", __($options["subscribe_title"]), 
                                  array("class" => "subscription-header"));
        $button = $this->Form->button("Subscribe", 
                array("type"=>"button", 
                      "id"=>"subscribe-button-$options[subscribe_group_id]"));
        $form = $this->build_form($options);
        return $this->Html->div("subscription-widget", $title . $form );
    }

    function build_form($options) {
        $errors = $this->Html->div("", "", array("id"=>"subscription-errors"));
        $indicator = $this->Html->div("", $this->Html->image("/urg_subscription/img/loading.gif"),
                                      array("id" => "subscription-indicator",
                                            "style" => "display: none"));
        $help = $this->Html->div("", __("Press enter to subscribe."), 
                array("id" => "subscription-help",
                      "style" => "display: none"));
        $message = $this->Html->div("", $options["subscribe_message"], array("id"=>"subscription-message"));
        $form = $this->Form->create(null, array("id" => "subscription-form",
                                                "url" => "/urg_subscription/subscriptions/subscribe"));
        $form .= $this->Form->input("Subscription.email", 
                array("label" => false,
                      "after" => $help . $indicator,
                      "placeholder" => __("Please enter email address"),
                      "autocomplete" => "off"));
        $form .= $this->Form->hidden("Subscription.group_id", 
                array("value"=>$options["subscribe_group_id"]));
        $form .= $this->Form->end();

        $form_js = $this->Html->script("/urg_subscription/js/jquery.form.js");
        $js = "$('#subscription-form').ajaxForm({ 
                dataType: 'json',
                success: function(data) { 
                    if (data.errors != false) {
                        $('#subscription-errors').text(data.errors.email);
                    } else {
                        $('#subscription-errors').text('');
                        $('#SubscriptionEmail').val('').blur();
                        $('#subscription-message').text(data.message);
                    }
                    $('#subscription-indicator').hide(); 
                },
                beforeSubmit: function() { $('#subscription-indicator').show(); } });";
        $js .= "$('#SubscriptionEmail').bind('blur', function(){ $('#subscription-help').hide(); });";
        $js .= "$('#SubscriptionEmail').bind('focus', function(){ $('#subscription-help').show(); });";
        $form_js .= $this->Html->scriptBlock("$(function() { $js } );");
        return $this->Html->div("", $message . $form . $errors . $form_js, 
                                array("id" => "subscription-form-$options[subscribe_group_id]"));
    }

    function dialog_js($options) {
        $js = "$('#subscription-form-$options[subscribe_group_id]').dialog({
                autoOpen: false,
                modal: true,
                title: '" . __($options["subscribe_title"]) . "',
                buttons: {
                    '" . __("Subscribe now") . "' : function() {
                        if (checkRegexp('#SubscriptionEmail', /^((([a-z]|\\d|[!#\\$%&'\\*\\+\\-\\/=\\?\\^_`{\\|}~]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])+(\\.([a-z]|\\d|[!#\\$%&'\\*\\+\\-\\/=\\?\\^_`{\\|}~]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])+)*)|((\\x22)((((\\x20|\\x09)*(\\x0d\\x0a))?(\\x20|\\x09)+)?(([\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]|\\x21|[\\x23-\\x5b]|[\\x5d-\\x7e]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(\\\\([\\x01-\\x09\\x0b\\x0c\\x0d-\\x7f]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF]))))*(((\\x20|\\x09)*(\\x0d\\x0a))?(\\x20|\\x09)+)?(\\x22)))@((([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-z]|\\d|-|\\.|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])))\\.)+(([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-z]|\\d|-|\\.|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])))\\.?$/i, '" . __("Please enter a valid email.") . "')) {
                            $('#subscription-form-$options[subscribe_group_id] form').submit();
                        }
                    },
                    '" . __("Cancel") . "' : function() {
                        $(this).dialog('close');
                    },
                },
                close: function() {
                    $('#SubscriptionEmail').val('');
                }
                });";
        $js .= "$('#subscribe-button-$options[subscribe_group_id]').click(
                function() {
                    $('#subscription-form-$options[subscribe_group_id]').dialog('open');
                });";
        $js .= "function checkRegexp( o, regexp, n ) { 
                    if ( !( regexp.test( $(o).val() ) ) ) { 
                        $(o).addClass('ui-state-error'); 
                        $('.subscription-errors').text(n); 
                        return false; 
                    } else { 
                        return true; 
                    } 
                }";
        return $this->Html->scriptBlock($js);
    }
}
