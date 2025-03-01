<?php

namespace App\Admin\Controllers;

use App\Models\LinkUrl;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Form\Field\Select;

class LinkUrlController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'LinkUrl';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LinkUrl());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('link_icon_id', 'アイコン')->image('', 50, 50);
        $grid->column('name', 'リンク名');
        $grid->column('address', 'URL');
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
        $show = new Show(LinkUrl::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('link_icon_id', __('Link icon id'));
        $show->field('name', 'リンク名');
        $show->field('address', 'URL');
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
        $form = new Form(new LinkUrl());

        // 画像付きセレクトボックス
        $icons = \App\Models\LinkIcon::all()->pluck('ikon_image', 'id')->toArray();
        $iconOptions = [];

        foreach ($icons as $id => $image) {
            // $image がすでに icons/ を含んでいる場合、そのまま使う
            $iconPath = strpos($image, 'icons/') === false ? 'icons/' . $image : $image;


            $iconOptions[$id] = "<img src='" . asset("storage/icons/$image") . "' width='50' height='50' style='border-radius:5px;'> $image";
        }


        // セレクトボックスの表示部分
        $form->select('link_icon_id', 'アイコン')
            ->options($iconOptions)
            ->setLabelClass(['custom-icon-select'])
            ->attribute(['data-html' => 'true']);  // HTMLを有効化
        $form->text('name', 'リンク名');
        $form->text('address', 'URL');


        return $form;
    }
}
