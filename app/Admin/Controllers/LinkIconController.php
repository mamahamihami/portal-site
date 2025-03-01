<?php

namespace App\Admin\Controllers;

use App\Models\LinkIcon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LinkIconController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'LinkIcon';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LinkIcon());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('ikon_image', 'アイコン')->image('', 50, 50); // 画像表示
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(LinkIcon::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('ikon_image', __('Ikon image'));
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
        $form = new Form(new LinkIcon());

        // 画像を 'public/storage/icons' フォルダに保存

        $form->image('ikon_image', 'アイコン')
            ->move('icons')
            ->name(function ($file) {
                return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.' . $file->guessExtension();
            })
            ->removable();


        return $form;
    }
}
