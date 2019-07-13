<?php

namespace dddeeemmmooonnn\NovaMulticolumnFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class NovaMulticolumnFilter extends Filter
{
    public $component = 'nova-multicolumn-filter';

    public function apply(Request $request, $query, $value)
    {
        foreach (json_decode($value, true) as $val) {
            $val['search'] = urldecode($val['search']);

            if (collect($val)->values()->filter()->count() !== 3) {
                continue;
            }

            if (strtolower($val['operator']) === 'like') {
                $val['search'] = '%' . $val['search'] . '%';
            }

            if (isset($this->columns()[$val['column']]['type']) && $this->columns()[$val['column']]['type'] === 'date') {
                $query = $query->whereDate($val['column'], $val['operator'], $val['search']);
            } else {
                $query = $query->where($val['column'], $val['operator'], $val['search']);
            }
        }
        return $query;
    }

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

    public function operators()
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
            'columns' => $this->columns(),
            'operators' => $this->operators(),
        ]);
    }
}
