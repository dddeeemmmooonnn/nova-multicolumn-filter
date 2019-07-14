<?php

namespace dddeeemmmooonnn\NovaMulticolumnFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class NovaMulticolumnFilter extends Filter
{
    public $component = 'nova-multicolumn-filter';

    protected static $default_column_type = 'text';

    public function apply(Request $request, $query, $value)
    {
        foreach (json_decode($value, true) as $val) {
            $val['value'] = urldecode($val['value']);
            if (!$val['operator'] && in_array($this->getOptions()[$val['column']]['type'], ['select', 'checkbox'])) {
                $val['operator'] = '=';
            }

            if (!$val['column'] || !$val['operator'] || $val['value'] === '') {
                continue;
            }

            if (strtolower($val['operator']) === 'like') {
                $val['value'] = '%' . $val['value'] . '%';
            }

            if (isset($this->columns()[$val['column']]['type']) && $this->columns()[$val['column']]['type'] === 'date') {
                $query = $query->whereDate($val['column'], $val['operator'], $val['value']);
            } else {
                $query = $query->where($val['column'], $val['operator'], $val['value']);
            }
        }
        return $query;
    }

    protected function columns()
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => 'Имя',
//                'operators' => 'default',
                'defaultOperator' => '=',
                'defaultValue' => 'админ',
                'preset' => true,
            ],
            'email' => [
                'type' => 'email',
                'label' => 'мыло',
                'operators' => [
                    '=' => '=',
                    //...
                ],
                'defaultOperator' => '=',
                'defaultValue' => 'admin@admin.ru',
            ],
            'id' => [
                'type' => 'select',
                'label' => 'ид',
                'options' => [
                    '1' => 'первый',
                    '2' => 'второй',
                    //...
                ],
                'defaultValue' => '1',
            ],
            'updated_at' => [
                'type' => 'date',
                'label' => 'обновлено',
            ],
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
            if (!isset($value['label'])) {
                $value['label'] = Nova::humanize($column);//!!
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
