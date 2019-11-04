<?php

namespace dddeeemmmooonnn\NovaMulticolumnFilter;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Filters\Filter;

class NovaMulticolumnFilter extends Filter
{
    public $component = 'nova-multicolumn-filter';

    protected $columns = [];

    protected $manual_update = false;

    protected $default_column_type = 'text';

    public function __construct($columns = null, $manual_update = null, $default_column_type = null)
    {
        if ($columns !== null) {
            $this->columns = $columns;
        }

        if ($manual_update !== null) {
            $this->manual_update = $manual_update;
        }

        if ($default_column_type !== null) {
            $this->default_column_type = $default_column_type;
        }
    }

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

            if ($columns[$val['column']]['apply']) {
                $query = $this->{$columns[$val['column']]['apply']}($query, $columns[$val['column']]['column'], $val['operator'], $val['value']);
            } elseif ($columns[$val['column']]['type'] === 'date') {
                $query = $query->whereDate($columns[$val['column']]['column'], $val['operator'], $val['value']);
            } else {
                $query = $query->where($columns[$val['column']]['column'], $val['operator'], $val['value']);
            }
        }
        return $query;
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
        return json_encode([
            'columns' => $this->getOptions(),
            'manual' => $this->manual_update,
        ]);
    }

    private function getOptions()
    {
        $columns = [];
        foreach ($this->columns as $column => $value) {
            if (is_string($value)) {
                $value = [];
            }

            if (!isset($value['label'])) {
                $value['label'] = str_replace('_', ' ', Str::title(Str::snake($column, '_')));
            }

            if (!isset($value['type'])) {
                $value['type'] = $this->default_column_type;
            }

            if ($value['type'] === 'select') {
                if (!isset($value['options']) || !$value['options']) {
                    throw new \Exception("Column $column has no options in " . get_class($this));
                }

                if (is_string($value['options'])) {
                    $method = 'options' . ucfirst($value['options']);
                    if (method_exists($this, $method)) {
                        $value['options'] = $this->restructureArray($this->$method());
                    } else {
                        throw new \Exception("Method $method not exists in " . get_class($this));
                    }
                } else {
                    $value['options'] = $this->restructureArray($value['options']);
                }
            } elseif ($value['type'] !== 'checkbox') {
                if (!isset($value['operators']) || !$value['operators']) {
                    $value['operators'] = $this->restructureArray($this->operatorsDefault());
                } elseif (is_string($value['operators'])) {
                    $method = 'operators' . ucfirst($value['operators']);
                    if (method_exists($this, $method)) {
                        $value['operators'] = $this->restructureArray($this->$method());
                    } else {
                        throw new \Exception("Method $method not exists in " . get_class($this));
                    }
                } else {
                    $value['operators'] = $this->restructureArray($value['operators']);
                }
            }

            if (!isset($value['column'])) {
                $value['column'] = $column;
            }

            if (isset($value['apply'])) {
                $method = 'apply' . ucfirst($value['apply']);
                if (method_exists($this, $method)) {
                    $value['apply'] = $method;
                } else {
                    throw new \Exception("Method $method not exists in " . get_class($this));
                }
            } else {
                $value['apply'] = false;
            }

            $value['value'] = $column;

            $columns[$column] = $value;
        }

        return $columns;
    }

    private function restructureArray($array)
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
