DataForm
============

** IN DEVELOPMENT ** 


DataForm is a form builder
By default it produce Bootstrap 3 compatible output. 

At this moment is built on [Deficient](https://github.com/zofe/deficient) (a subset of laravel components including eloquent and blade, plus [burp](https://github.com/zofe/burp) router).
The plan is to make it compatible also with laravel, as standard package.

It can   

- Make a Form
- Bind the form with an eloquent model 


## usage
```php

   //todo

```


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
