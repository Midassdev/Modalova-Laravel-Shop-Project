<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Gender;
use App\Models\WhiteList;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Admin\Actions\WhiteList\SeePage;

class WhiteListController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = WhiteList::class;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WhiteList);

        $grid->id()->sortable();
        $grid->gender()->sortable();
        $grid->column('brand.name')->sortable();
        $grid->column('category.title')->sortable();
        $grid->column('category.depth')->sortable();
        $grid->amount()->sortable();
        $grid->colors()->pluck('name')->label();

        $grid->created_at()->sortable();
        $grid->updated_at()->sortable();

        $grid->filter(function ($filter) {
            $filter->equal('brand_id')->select(function ($id) {
                if ($brand = Brand::find($id)) {
                    return [$brand->id => $brand->name];
                }
            })->ajax('/'.config('admin.route.prefix').'/api/brands');
            $filter->equal('category_id')->select(function ($id) {
                if ($category = Category::find($id)) {
                    return [$category->id => $category->title];
                }
            })->ajax('/'.config('admin.route.prefix').'/api/categories');
            $filter->equal('gender')->select(array_combine(Gender::genders(), Gender::genders()));
            $filter->equal('category.depth');
            $filter->between('amount');
        });

        $grid->actions(function ($actions) {
            $actions->add(new SeePage);
        });

        $grid->model()->orderBy('updated_at', 'desc');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $object = WhiteList::findOrFail($id);
        $show = new Show($object);

        $show->id();
        $show->gender();
        $show->field('brand.id');
        $show->field('brand.name');
        $show->field('category.id');
        $show->field('category.title');
        $show->amount();
        $show->colors()->as(function ($color) {
            return $color->pluck('name');
        })->label();

        $show->created_at();
        $show->updated_at();

        $show->panel()->tools(function ($tools) use ($object) {
            $text = 'See Page';
            $route = $object->getRoute();

            $tools->append(
                <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$route}" class="btn btn-sm btn-warning" title="{$text}" target="_blank">
        <i class="fa fa-link"></i><span class="hidden-xs"> {$text}</span>
    </a>
</div>
HTML);
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WhiteList);

        $form->select('brand_id')->options(function ($id) {
            if ($brand = Brand::find($id)) {
                return [$brand->id => $brand->name];
            }
        })->ajax('/'.config('admin.route.prefix').'/api/brands');
        $form->select('category_id')->options(function ($id) {
            if ($category = Category::find($id)) {
                return [$category->id => $category->title];
            }
        })->ajax('/'.config('admin.route.prefix').'/api/categories');
        $form->select('gender')->options(array_flip(Gender::genders()))->default(Gender::GENDER_BOTH);
        $form->number('amount')->default(0);
        $form->multipleSelect('colors')->options(Color::all()->pluck('name', 'id'));

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }
}
