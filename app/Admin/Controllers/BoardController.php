<?php

namespace App\Admin\Controllers;

use App\Models\Board;
use App\Models\Image;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BoardController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Board';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Board());
        $grid->model()->orderBy('updated_at', 'desc');

        $grid->column('id', __('Id'));
        $grid->column('user_id', 'user-id');
        $grid->column('user_name', '登録者')->sortable();
        $grid->column('department_name', '部署名')->sortable();
        $grid->column('title', '題名')->sortable();
        $grid->column('text', '本文')->sortable();

        // 添付ファイルを表示
        $grid->column('images', '添付ファイル')->display(function () {
            return $this->images->map(function ($image) {
                return "<a href='/storage/uploads/{$image->file_path}' target='_blank'>{$image->file_path}</a>";
            })->implode('<br>');
        });

        $grid->column('updated_at', '登録日')->sortable()->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('deleted_at', __('Deleted at'))->sortable()->display(function ($time) {
            return $time ? date("Y/m/d H:i:s", strtotime($time)) : '';
        });

        $grid->filter(function ($filter) {
            $filter->like('user_name', '登録者');
            $filter->like('department_name', '部署名');
            $filter->like('title', '題名');
            $filter->like('text', '本文');
            $filter->between('updated_at', '登録日')->datetime();
            $filter->scope('trashed', 'Soft deleted data')->onlyTrashed();
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
        $show = new Show(Board::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_name', __('User name'));
        $show->field('department_name', __('Department name'));
        $show->field('title', __('Title'));
        $show->field('text', __('Text'));

        // 添付ファイルを表示
        $show->field('images', '添付ファイル')->as(function () {
            return $this->images->map(function ($image) {
                return "<a href='/storage/uploads/{$image->file_path}' target='_blank'>{$image->file_path}</a>";
            })->implode('<br>');
        })->unescape();

        $show->field('updated_at', __('Updated at'));
        $show->field('updated_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Board());

        // ユーザー名を選択式で表示（User モデルから選択肢を取得）
        $form->select('user_id', 'ユーザー名')->options(
            User::all()->pluck('name', 'id')  // id と name を選択肢として表示
        )->rules('required')
            ->default(User::first()->id); // デフォルトで最初のユーザーIDを設定

        // hiddenでuser_nameを設定（user_idに基づいて自動設定）
        $form->hidden('user_name')->default(function ($form) {
            return User::find($form->user_id)->name ?? '';  // user_idに基づいてuser_nameを設定
        });

        // 部署名をselectで表示
        $form->select('department_id', '部署名')->options(
            Department::all()->pluck('department_name', 'id')
        )->rules('required');

        // 部署名を表示（readonly）
        $form->hidden('department_name', '部署名表示')->readonly()->default(function ($form) {
            return Department::find($form->department_id)->department_name ?? '';
        });

        // 題名
        $form->text('title', '題名')->rules('required');

        // 本文
        $form->textarea('text', '本文')->rules('required');

        // 添付ファイル（複数）
        $form->hasMany('images', '添付ファイル', function (Form\NestedForm $form) {
            $form->file('file_path', 'ファイルアップロード')->rules('mimes:jpg,jpeg,png,gif,pdf')->removable();
        });

        // データを保存する際にuser_nameとdepartment_nameを設定
        $form->saving(function (Form $form) {
            // user_nameを設定（選択されたuser_idに基づいて）
            $user = User::find($form->user_id);
            if ($user) {
                $form->user_name = $user->name;
            }

            // department_nameを設定
            $department = Department::find($form->department_id);
            if ($department) {
                $form->department_name = $department->department_name;
            }
        });

        $form->saving(function (Form $form) {
            if (!$form->isEditing()) {
                $form->ignore(['deleted_at']);
            }
        });

        if ($form->isEditing()) {
            $form->datetime('deleted_at', __('Deleted at'))->default(NULL);
        }

        return $form;
    }
}
