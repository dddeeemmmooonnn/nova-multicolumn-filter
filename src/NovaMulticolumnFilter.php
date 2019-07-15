<?php

namespace dddeeemmmooonnn\NovaMulticolumnFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Nova;

class NovaMulticolumnFilter extends Filter
{
    public $component = 'nova-multicolumn-filter';

    protected static $default_column_type = 'text';

    public function apply(Request $request, $query, $value)
    {
        $columns = $this->getOptions();

        foreach (json_decode($value, true) as $val) {
            $val['value'] = urldecode($val['value']);
            if (!$val['operator'] && in_array($columns[$val['column']]['type'], ['select', 'checkbox'])) {
                $val['operator'] = '=';
            }

            if (!$val['column'] || !$val['operator'] || $val['value'] === '') {
                continue;
            }

            if (strtolower($val['operator']) === 'like') {
                $val['value'] = '%' . $val['value'] . '%';
            }

            if ($columns[$val['column']]['type'] === 'date') {
                $query = $query->whereDate($columns[$val['column']]['column'], $val['operator'], $val['value']);
            } else {
                $query = $query->where($columns[$val['column']]['column'], $val['operator'], $val['value']);
            }
        }
        return $query;
    }

    protected function columns()
    {
        return [
            'name' => '',
            'email' => [
                'defaultOperator' => 'LIKE',
                'defaultValue' => 'adm',
                'preset' => true,
            ],
            'id1' => [
                'type' => 'number',
                'column' => 'id'
            ],
            'id2' => [
                'type' => 'select',
                'options' => [
                    '1' => 'One',
                    '2' => 'Two',
                ],
                'column' => 'id',
            ],
            'id3' => [
                'type' => 'checkbox',
                'column' => 'id'
            ],
            'updated_at' => [
                'type' => 'date',
            ]
        ];
    }

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

    public function options(Request $request)
    {
        return json_encode($this->getOptions());
    }

    private function getOptions()
    {
        $columns = $this->columns();
        foreach ($columns as $column => $value) {
            if (is_string($value)) {
                $value = [];
            }

            if (!isset($value['label'])) {
                $value['label'] = Nova::humanize($column);
            }

            if (!isset($value['type'])) {
                $value['type'] = static::$default_column_type;
            }

            if ($value['type'] === 'select') {
                if (!isset($value['options']) || !$value['options']) {
                    throw new \Exception("Column $column has no options in " . get_class($this));
                }

                if (is_string($value['options'])) {
                    $ucf = ucfirst($value['options']);
                    if (method_exists($this, "options$ucf")) {
                        $value['options'] = self::restructureArray($this->{"options$ucf"}());
                    } else {
                        throw new \Exception("Method options$ucf not exists in " . get_class($this));
                    }
                } else {
                    $value['options'] = self::restructureArray($value['options']);
                }
            } elseif ($value['type'] !== 'checkbox') {
                if (!isset($value['operators']) || !$value['operators']) {
                    $value['operators'] = self::restructureArray($this->operatorsDefault());
                } elseif (is_string($value['operators'])) {
                    $ucf = ucfirst($value['operators']);
                    if (method_exists($this, "operators$ucf")) {
                        $value['operators'] = self::restructureArray($this->{"operators$ucf"}());
                    } else {
                        throw new \Exception("Method operators$ucf not exists in " . get_class($this));
                    }
                } else {
                    $value['operators'] = self::restructureArray($value['operators']);
                }
            }

            if (!isset($value['column'])) {
                $value['column'] = $column;
            }

            $value['value'] = $column;

            $columns[$column] = $value;
        }

        return $columns;
    }

    private static function restructureArray($array)
    {
        $return = [];
        foreach ($array as $value => $label) {
            $return[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $return;
    }
}
