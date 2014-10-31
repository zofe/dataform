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
    $form->submit('Save');
    $form->saved(function() use ($form)
    {
        //do something with post values, then..
        
        $form->message("ok record saved");
        $form->linkRoute("home","Back to the Form");
    });
    ...
```
form with model binding (preset values and store new values on save):

```php
    $form = DataForm::source(User::find(1));
    $form->text('title','Title'); //field name, label
    $form->textarea('body','Body')->rule('required'); //validation
    $form->checkbox('public','Public');
    $form->submit('Save');
    $form->saved(function() use ($form)
    {
        $form->message("ok record saved");
        $form->linkRoute("home","Back to the Form");
    });
    ...

```
for field "rules" you can reference to laravel validation

note that @ this time: 

  - there are only text,textarea and checkbox fields
  - model-binding still not support relations
 

## why not starting from laravel?

We choose "deficient" (a subset of laravel components) 
to be more isoladed, and give the ability to use it stand-alone or embedded in any other project.  

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
