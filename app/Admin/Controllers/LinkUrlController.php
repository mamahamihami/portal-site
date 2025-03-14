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
        $grid->column('link_icon_id', 'アイコン')->display(function ($link_icon_id) {
            // もしアイコンが存在する場合はその画像を表示、なければデフォルト画像を表示
            $icon = \App\Models\LinkIcon::find($link_icon_id);
            $iconImage = $icon && $icon->ikon_image ? $icon->ikon_image : 'icons/default_1740837631.png';

            return "<img src='" . asset('storage/' . $iconImage) . "' width='20' height='20'>";
        });
        $grid->column('name', 'リンク名');
        $grid->column('address', 'URL');
        $grid->column('created_at', __('Created at'))->display(function ($time) {
            return $time ? date("Y/m/d H:i:s", strtotime($time)) : '';
        });
        $grid->column('updated_at', __('Updated at'))->display(function ($time) {
            return $time ? date("Y/m/d H:i:s", strtotime($time)) : '';
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

            // セレクトボックスで画像を表示できるように、オプションを HTML フォーマットにする
            $iconOptions[$id] = "<img src=" . asset('storage/' . $iconPath) . "> $image";
        }

        // セレクトボックスの表示部分
        $form->select('link_icon_id', 'アイコン')
            ->options($iconOptions)
            ->setLabelClass(['custom-icon-select'])
            ->attribute(['data-html' => 'true'])
            ->help('default_1740837631.pngを使用する場合は入力不要  変更する場合はアイコンを登録してください');  // HTMLを有効化
        $form->text('name', 'リンク名')->rules('required');
        $form->text('address', 'URL')->rules('required');


        return $form;
    }
}
