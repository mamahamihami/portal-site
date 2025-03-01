<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     * User モデルのデータを取得し、テーブル形式で一覧表示
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Name'));
        $grid->column('employee_number', '社員番号')->sortable();
        $grid->column('employee_number_at', __('Employee number at'))->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });

        // departments の表示　pluck('department_name') で部署名の配列を取得。implode(', ') でカンマ区切りの文字列に変換。
        $grid->column('dpm_departments.department_name', '部署名')->display(function () {
            return $this->dpm_departments->pluck('department_name')->implode(', ');
        });

        $grid->column('email', __('Email'));
        $grid->column('email_verified_at', __('Email verified at'))->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('created_at', __('Created at'))->sortable()->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('updated_at', __('Updated at'))->sortable()->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('deleted_at', __('Deleted at'))->sortable()->display(function ($time) {
            return $time ? date("Y/m/d H:i:s", strtotime($time)) : '';
        });
        $grid->filter(function ($filter) {
            $filter->like('name', '氏名');
            $filter->like('employee_number', '社員番号');

            // `department_name` フィルターの追加
            $filter->like('dpm_departments.department_name', '部署名'); // ここで `department_name` を指定

            // `department_name` をフィルタリング
            if ($departmentName = request('dpm_departments.department_name')) {
                // `department_user` と `departments` を結びつけて `department_name` を検索
                $filter->builder()->whereHas('dpm_departments', function ($query) use ($departmentName) {
                    $query->where('department_name', 'like', "%$departmentName%"); // `departments` テーブルで `department_name` を検索
                });
            }

            $filter->like('email', 'メールアドレス');
            $filter->between('created_at', '登録日')->datetime(); //->datetime() をつけることで、日付の入力フィールドをカレンダー形式にできる。
            $filter->scope('trashed', 'Soft deleted data')->onlyTrashed(); // onlyTrashed()論理削除（Soft Delete）されたデータのみ を表示できる。


        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     * 管理画面でユーザーの詳細を表示するために必須の機能！
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('employee_number', '社員番号');
        $show->field('employee_number_at', __('Employee number at'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('氏名'))->placeholder('姓と名の間はスペースをあけてください。');
        $form->text('employee_number', '社員番号');

        // departmentsの選択肢を追加
        $form->multipleSelect('dpm_departments', '部署名')->options(
            Department::all()->pluck('department_name', 'id')
        )->help('複数選択可');

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            } else {
                $form->password = $form->model()->password;
            }
        });
        $form->datetime('employee_number_at', __('Employee number at'))->default(date('Y-m-d H:i:s'));
        $form->email('email', __('Email'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->datetime('deleted_at', __('Deleted at'))->default(NULL);



        return $form;
    }
}
