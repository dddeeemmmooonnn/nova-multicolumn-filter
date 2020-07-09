# A Laravel Nova multicolumn filter
Filter for several columns

### Demo

![Demo](http://g.recordit.co/0ivt9YJcpy.gif)

### Installation

Run this command in your Laravel Nova project:

`composer require dddeeemmmooonnn/nova-multicolumn-filter`

### Usage

#### Simplified:

```
new NovaMulticolumnFilter([
        'column' => '', //columns
    ],
    $manual_update = false, // Apply filter with the button
    $default_column_type = 'text', // Default input type
    $name = 'filter' // Filter name
),
```

#### Detailed:

Intended use: extend filter, add different options and operators, use it with column declaration in constructor. Or extend again, and add column declaration there.

Create a filter with `artisan`

```shell 
 $ php artisan nova:filter UserFilter 
 ```
Extend your filter from the NovaMulticolumnFilter and customize operators, options, columns.

Сomprehensive example:

```php
use dddeeemmmooonnn\NovaMulticolumnFilter\NovaMulticolumnFilter;

class UserFilter extends NovaMulticolumnFilter
{
    public $name = 'Filters';

    protected $columns = [
        // Simple text column declaration
        'column1' => '',
        
        // Customizing all
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
            'apply' => 'customApply',
            'placeholder' => 'input placeholder'
        ],
        
        // For checkboxes
        'column3' => [
            'type' => 'checkbox',
            'label' => 'Checkbox',
            'defaultValue' => 'anything', // Not empty = checked
        ],
        
        // Select type, options in array
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
        
        // Select type, options in optionsCustomOptions() method
        'column5' => [
            'type' => 'select',
            'label' => 'Select again',
            'options' => 'customOptions',
            'defaultValue' => '1',
        ],
        
        // operators in operatorsCustomOperators() method
        'column6' => [
            'type' => 'number',
            'label' => 'Number',
            'operators' => 'customOperators',
            'defaultValue' => '1',
        ],
        
        // Date type
        'column7' => [
            'type' => 'date',
            'label' => 'Date',
        ],
        
        // Using one db column in different declarations
        'column8' => [
            'type' => 'date',
            'label' => 'Date',
            'column' => 'column7',
        ],
    ];

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

    protected function applyCustomApply($query, $column, $operator, $value)
    {
        return $query->where($column, $operator, $value);
    }
    
    //Also you can override default values

    protected $default_column_type = 'text';
    
    protected $manual_update = false;
    
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
    * date - using whereDate in apply() function
    * checkbox - operator `=`
    * text, email, number and other \<input type="\*\*\*"\/\>

* label - column label

* operators - list of column's operators. Array `['value' => 'label']` or string indicating method name. 
Method must be declared as `operatorsYourName`

* options - for `select` type, similarly operators, but method naming starts with `options`

* defaultOperator

* defaultValue

* preset - preset columns when filter is empty, applied after opening filters menu

* column - column name, if you want to use several types for one column

* apply - custom apply method, that will filter the column

* placeholder - \<input\> placeholder 

##### Localization:
add to nova translation json file (resources/lang/vendor/nova/\<lang\>.json)
```
    "multicolumn.select_empty_label": "—",
    "multicolumn.add": "Add",
    "multicolumn.apply": "Apply"
```

### Authors

* Dmitry Turov dddeeemmmooonnntrue@gmail.com

The filter inspired by [philperusse/nova-column-filter](https://github.com/philperusse/nova-column-filter) and
[beyondcode/nova-filterable-cards](https://github.com/beyondcode/nova-filterable-cards)
