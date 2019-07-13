# A Laravel Nova multicolumn filter
Filter for several columns

### Demo

![Demo](http://g.recordit.co/0w1DQs9rbG.gif)

### Installation

Run this command in your Laravel Nova project:

`composer require dddeeemmmooonnn/nova-multicolumn-filter`

### Usage

Create a filter with `artisan`

```shell 
 $ php artisan nova:filter UserFilter 
 ```
Extend your filter from the NovaMulticolumnFilter and add the columns() method with necessary columns.

`'column' => 'Label'` for Text field

```
'column' => [
    'label' => 'Label',
    'type' => 'date',
];
```
for Date field

```php
use dddeeemmmooonnn\NovaMulticolumnFilter\NovaMulticolumnFilter;

class UserFilter extends NovaMulticolumnFilter
{
    public $name = 'Filters';

    public function columns()
    {
        return [
            'name' => 'Name',
            'email' => 'E-mail',
            'updated_at' => [
                'label' => 'Updated',
                'type' => 'date',
            ],
        ];
    }
}
```

### Customization

You can customize the operator list by overriding the operators method.

### Authors

* Dmitry Turov dddeeemmmooonnntrue@gmail.com

The filter inspired by [philperusse/nova-column-filter](https://github.com/philperusse/nova-column-filter)
