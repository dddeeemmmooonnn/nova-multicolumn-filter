# A Laravel Nova multicolumn filter
Filter for several columns

### Demo

![Demo](http://g.recordit.co/0ivt9YJcpy.gif)

### Installation

Run this command in your Laravel Nova project:

`composer require dddeeemmmooonnn/nova-multicolumn-filter`

### Usage

Create a filter with `artisan`

```shell 
 $ php artisan nova:filter UserFilter 
 ```
Extend your filter from the NovaMulticolumnFilter and add the columns() method with necessary columns.

Сomprehensive example:

```php
use dddeeemmmooonnn\NovaMulticolumnFilter\NovaMulticolumnFilter;

class UserFilter extends NovaMulticolumnFilter
{
    public $name = 'Filters';

    protected function columns()
    {
        return [
            'column1' => '',
            'column2' => [
                'type' => 'email',
                'label' => 'E-mail',
                'operators' => [
                    '=' => '=',
                    //...
                ],
                'defaultOperator' => '=',
                'defaultValue' => 'admin@admin.com',
                'preset' => true,
            ],
            'column3' => [
                'type' => 'checkbox',
                'label' => 'Checkbox',
                'defaultValue' => 'anything', // Not empty = checked
            ],
            'column4' => [
                'type' => 'select',
                'label' => 'Select',
                'options' => [
                    '1' => 'First',
                    '2' => 'Second',
                    //...
                ],
                'defaultValue' => '1',
            ],
            'column5' => [
                'type' => 'select',
                'label' => 'Select again',
                'options' => 'customOptions',
                'defaultValue' => '1',
            ],
            'column6' => [
                'type' => 'number',
                'label' => 'Number',
                'operators' => 'customOperators',
                'defaultValue' => '1',
            ],
            'column7' => [
                'type' => 'date',
                'label' => 'Date',
            ],
            'column8' => [
                'type' => 'date',
                'label' => 'Date',
                'column' => 'column7',
            ],
        ];
    }

    protected function operatorsCustomOperators()
    {
        return [
            '=' => 'Equals',
            '>' => 'Greater',
            '<' => 'Less',
        ];
    }

    protected function optionsCustomOptions()
    {
        return [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
        ];
    }
    
    //Also you can override default values

    protected static $default_column_type = 'text';
    
    protected function operatorsDefault()
    {
        return [
            '=' => '=',
            '>' => '>',
            '>=' => '>=',
            '<' => '<',
            '<=' => '<=',
            'LIKE' => 'Like',
        ];
    }
}
```

Column options:

* type - input type
    * select - options required, operator `=`
    * date - using whereDate
    * checkbox - operator `=`
    * text, email, number and other \<input type="\*\*\*"\/\>

* label - column label

* operators - list of column's operators. Array `['value' => 'label']` or string indicating method name. 
Method must be declared as `operatorsYourName`

* options - for `select` type, similarly operators, but method naming starts with `options`

* defaultOperator

* defaultValue

* preset - preset columns when filter is empty, applied after opening filters menu

* column - column name, if you want to use several types for one column (why?)

### Authors

* Dmitry Turov dddeeemmmooonnntrue@gmail.com

The filter inspired by [philperusse/nova-column-filter](https://github.com/philperusse/nova-column-filter) and
[beyondcode/nova-filterable-cards](https://github.com/beyondcode/nova-filterable-cards)
