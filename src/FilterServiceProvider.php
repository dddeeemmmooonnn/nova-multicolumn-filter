<?php

namespace dddeeemmmooonnn\NovaMulticolumnFilter;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::translations([
            'multicolumn.select_empty_label' => '—',
            'multicolumn.add' => 'Add',
            'multicolumn.apply' => 'Apply',
        ]);

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-multicolumn-filter', __DIR__.'/../dist/js/filter.js');
            Nova::style('nova-multicolumn-filter', __DIR__.'/../dist/css/filter.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
