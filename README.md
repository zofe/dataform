DataForm
============


DataForm is a form builder
By default it produce Bootstrap 3 compatible output. 

At this moment is built on [Deficient](https://github.com/zofe/deficient) (a subset of laravel components including eloquent and blade, plus [burp](https://github.com/zofe/burp) router).
The plan is to make it compatible also with laravel, as standard package.

It can   

- Make a Form
- Bind the form with an eloquent model 


## usage

as form helper:
 

```php
    $form = DataForm::create();
    $form->text('title','Title'); //field name, label
    $form->text('body','Body')->rule('required'); //validator 
    
    $form->saved(function() use ($form)
    {
        //do something with post, then..
        
        $form->message("ok record saved");
        $form->linkRoute("home","Back to the Form");
    });
    ...
```
form with model binding 

```php
    $form = DataForm::source(User::find(1));
    $form->text('title','Title'); //field name, label
    $form->text('body','Body')->rule('required'); //validation
    
    $form->saved(function() use ($form)
    {
        // user where id = 1 will be saved, 
        // you can access it with $form->model inside this closure
        
        $form->message("ok record saved");
        $form->linkRoute("home","Back to the Form");
    });
    ...

```
for field "rules" you can reference to laravel validator included by deficient

note that @ this time: 

  - there is only "text" field
  - model-binding still not support relations
 

## why not starting from laravel?

Because it can be used stand alone, and in any other framework.  
It has really minimal dependencies.


## Installation

install via composer 

    {
        "require": {
            "zofe/dataform": "dev-master"
        }
    }
    
## Setup

To configure database, views, you must reference to [Deficient](https://github.com/zofe/deficient)  
This is a small how-to 

 - create minimum folders / configuration files
 - deploy dataform views
 - deploy a front controller and a sample (optional, but suggested)

```
  $ php vendor/zofe/deficient/deficient setup:folders
  $ php vendor/zofe/datagrid/dataform setup:views
  $ php vendor/zofe/datagrid/dataform setup:router
```
