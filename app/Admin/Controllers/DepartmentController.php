<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DepartmentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Department';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Department());

        $grid->column('id', __('Id'));
        $grid->column('department_name', '部署名')->sortable();
        $grid->column('created_at', __('Created at'))->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('updated_at', __('Updated at'))->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });

        $grid->filter(function ($filter) {
            $filter->like('department_name', '部署名');
        });

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
        $show = new Show(Department::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('department_name', __('Department name'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Department());

        $form->text('department_name','部署名');

        return $form;
    }
}
