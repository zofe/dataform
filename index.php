<?php

require_once __DIR__ . '/vendor/autoload.php';

use Zofe\Deficient\Deficient;
use Zofe\DataForm\DataForm;

Deficient::boot("./");


## burp,  move it somewhere
route_any('store', array('as'=>'save', function() {

    Zofe\Burp\BurpEvent::queue('dataform.save');
}));

route_any('^/{save?}$', array('as'=>'home', function () {
    
    $form = DataForm::create();
    $form->text('title','Title')->rule('required');
    $form->text('subtitle','Subtitle');
    $form->submit('Save');

    $form->saved(function() use ($form)
    {
        $form->message("ok record saved");
        $form->linkRoute("home","Back to the Form");
    });
    
    echo blade('dataform.tests.form', compact('form'));
}));



route_missing(function() {
    echo blade('dataform.tests.error', array(), 404);
    die;
});


route_dispatch();

