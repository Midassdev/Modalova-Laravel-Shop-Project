<?php

namespace App\Admin\Controllers;

use App\Models\Gender;
use App\Models\Source;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Admin\Actions\Models\EnableSource;
use App\Admin\Actions\Models\DisableSource;
use App\Admin\Actions\Source\Sample as SampleSource;

class SourceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = Source::class;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Source);

        $grid->column('id')->sortable();
        $grid->column('name')->sortable();
        $grid->column('title')->sortable();
        $grid->column('path')->link()->sortable()->hide();
        $grid->column('parser')->label()->sortable();
        $grid->column('config')->display(function ($value) {
            return (empty($value) || empty(array_values(array_filter($value)))) ? false : count($value);
        })->label('warning');
        $grid->column('mapping')->display(function ($value) {
            return (empty($value) || empty(array_values(array_filter($value)))) ? false : count($value);
        })->label('warning');
        $grid->column('enabled')->sortable()->switch();
        $grid->column('language')->sortable();
        $grid->column('priority')->sortable()->editable();
        $grid->column('nb_of_products', __('#'))->sortable();
        $grid->column('created_at')->sortable()->hide();
        $grid->column('updated_at')->sortable()->hide();

        $grid->filter(function ($filter) {
            $parsers = \App\Console\Commands\UpdateSources::$all_providers;

            if(($key = array_search('modalova', $parsers)) !== false)
                unset($parsers[$key]);
            if(($key = array_search('netaffiliation', $parsers)) !== false)
                unset($parsers[$key]);

            $parsers = [...$parsers, ...[
                'wix',
                'prestashop',
                'shopify',
                'woocommerce',
                'netaffiliationv3',
                'netaffiliationv4',
            ]];

            if ('postgres' == config('database.default')) {
                $filter->ilike('name');
                $filter->ilike('title');
                $filter->ilike('path');
                $filter->ilike('language');
            } else {
                $filter->like('name');
                $filter->like('title');
                $filter->like('path');
                $filter->like('language');
            }
            $filter->equal('parser')->select(array_combine($parsers, $parsers));
            $filter->between('nb_of_products');
            $filter->equal('enabled')->radio([
                __('NON'),
                __('OUI'),
            ]);
        });

        $grid->batchActions(function ($batch) {
            $batch->add(new EnableSource());
            $batch->add(new DisableSource());
        });

        $grid->actions(function ($actions) {
            $actions->add(new SampleSource);
        });

        $grid->sortable();

        $grid->quickSearch('name');

        $grid->paginate(50);

        return $grid;
    }

    public function sample(\Request $request, Source $source) {
        $params = [
            'source' => $source->id,
        ];

        if($skip = $request::get('skip'))
            $params['--skip'] = $skip;
        if($quantity = $request::get('quantity'))
            $params['--quantity'] = $quantity;

        echo '<pre>';
        \Artisan::call('sample:source', $params);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Source::findOrFail($id));

        $show->field('id');
        $show->field('name');
        $show->field('title');
        $show->field('parser');
        $show->field('path')->link();
        $show->field('enabled');
        $show->field('language');
        $show->field('priority');
        $show->field('nb_of_products');
        $show->field('config')->json();
        $show->field('mapping')->json();
        $show->field('extra')->unescape()->as($this->fn_display_raw_array);
        $show->field('created_at');
        $show->field('updated_at');

        $show->panel()->tools(function ($tools) use ($id) {
            $sample = 'Sample';
            $route = route(config('admin.route.prefix') . '.sources.sample', [$id]);

            $tools->append(
                <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$route}" class="btn btn-sm btn-warning" title="{$sample}" target="_blank">
        <i class="fa fa-file-text"></i><span class="hidden-xs"> {$sample}</span>
    </a>
</div>
HTML);
        });

        return $show;
    }

    private $fn_display_raw_array;

    public function __construct()
    {
        $this->fn_display_raw_array = function ($v) {
            return '<pre>'.print_r($v, true).'</pre>';
        };
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Source);

        $model = $form->model();

        $form->tab('Basic Info', function($form) {
            $form->text('name');
            $form->text('title');
            $form->text('parser');
            $form->textarea('path');
            $form->switch('enabled');
            $form->text('language');
            $form->text('priority');
            $form->number('nb_of_products');
        })->tab('Config', function ($form) use ($model) {
            $form->embeds('config', function ($form) use ($model) {
                $form->select(Source::CONFIG_USE_WGET)->options([
                    false => 'NON',
                    true => 'OUI',
                ]);
                $form->select(Source::CONFIG_USE_TIMEOUT_COMMAND)->options([
                    false => 'NON',
                    true => 'OUI',
                ]);
                $form->select(Source::CONFIG_FIX_UTF8)->options([
                    false => 'NON',
                    true => 'OUI',
                ]);

                $form->select(Source::CONFIG_FETCHER)->options([
                    \App\Models\Fetchers\CSV::class  => 'CSV',
                    \App\Models\Fetchers\XML::class  => 'XML',
                    \App\Models\Fetchers\JSON::class => 'JSON',
                ]);

                $form->select(Source::CONFIG_COL_SEPARATOR)->options([
                    ','  => 'COMMA (,)',
                    ';'  => 'SEMICOLON (;)',
                    '|'  => 'PIPE (|)',
                    "\t" => 'TAB (\t)',
                ]);
                $form->text(Source::CONFIG_CSV_HEADERS);
                $form->text(Source::CONFIG_XML_UNIQUENODE);
                $form->text(Source::CONFIG_XML_NAMESPACES);
                $form->number(Source::CONFIG_TIMEOUT);
                $form->text(Source::CONFIG_FORCE_BRAND_NAME);
                $form->select(Source::CONFIG_FORCE_GENDER)->options(array_flip(Gender::genders()));
                $form->text(Source::CONFIG_APPEND_CATEGORY);
                $form->text(Source::CONFIG_TRANSFORM_URL);
                $form->textarea(Source::CONFIG_STR_REPLACE_IMAGE);
                $form->textarea(Source::CONFIG_STR_REPLACE_BRAND);

                $form->text(Source::CONFIG_CONVERT_CURRENCY_FROM);

                $form->text(Source::CONFIG_PRESTASHOP_LANGUAGE);
                $form->number(Source::CONFIG_PRESTASHOP_LANGUAGE_ID);
                $form->text(Source::CONFIG_PRESTASHOP_IMAGE_TYPE);

                foreach([
                    Source::CONFIG_DEBUG_SHOW_BRANDS_ADDED,
                    Source::CONFIG_DEBUG_SHOW_CATEGORIES_ADDED,
                    Source::CONFIG_DEBUG_STORE_PAYLOAD,
                ] as $key) $form->select($key)->options([
                    false => 'NON',
                    true => 'OUI',
                ]);

                return $form;
            });
        })->tab('Mapping', function ($form) use ($model) {
            $form->embeds('mapping', function ($mapping_form) use ($model, $form) {
                try {
                    $parser = $form->model()->getParser();
                } catch (\Exception $e) {
                    return false;
                }

                foreach([
                    'product_id',
                    'product_name',
                    'brand_name',
                    'price',
                    'old_price',
                    'url',
                    'image_url',
                    'gender',
                    'description',
                    'categories',
                    'colors',
                    'sizes',
                    'materials',
                ] as $field)
                    if($fields = $parser->getFields($field))
                        $mapping_form->text($field)->placeholder(implode(', ', $fields));
            });
        });

        return $form;
    }
}
