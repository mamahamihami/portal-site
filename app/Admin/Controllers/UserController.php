<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// Str クラスは Laravel の 文字列操作用のヘルパー を提供しており、Str::random(8) のように使うことで ランダムな8文字の英数字を生成 できます。
use Illuminate\Support\Str;
// Mail ファサードは、Laravel の メール送信機能 を簡単に扱うためのクラスです。
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordNotification;
// ユーザー情報csv
use App\Admin\Extensions\Tools\CsvImport;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // CSVインポートボタンを追加
        $grid->tools(function ($tools) {
            $tools->append(new CsvImport());
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Name'));
        $grid->column('employee_number', '社員番号')->sortable();

        // departments の表示　pluck('department_name') で部署名の配列を取得。implode(', ') でカンマ区切りの文字列に変換。
        $grid->column('dpm_departments.department_name', '部署名')->display(function () {
            return $this->dpm_departments->pluck('department_name')->implode(', ');
        });

        $grid->column('email', __('Email'));

        $grid->column('created_at', __('Created at'))->sortable()->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('updated_at', __('Updated at'))->sortable()->display(function ($time) {
            return date("Y/m/d H:i:s", strtotime($time));
        });
        $grid->column('deleted_at', __('Deleted at'))->sortable()->display(function ($time) {
            return $time ? date("Y/m/d H:i:s", strtotime($time)) : '';
        });
        $grid->column('new_flag', '社員フラグ');

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
        $show->field('email', __('Email'));
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

        $form->text('name', __('氏名'))->placeholder('姓と名の間はスペースをあけてください。')->rules('required');
        $form->text('employee_number', '社員番号')->rules('required');

        // departmentsの選択肢を追加
        $form->multipleSelect('dpm_departments', '部署名')->options(
            Department::all()->pluck('department_name', 'id')
        )->rules('required')->help('複数選択可');

        $form->datetime('employee_number_at', __('Employee number at'))->default(date('Y-m-d H:i:s'))->disable();

        $form->email('email', __('Email'))
            ->rules(function ($form) {
                // 編集時は unique を無視し、required と email だけ適用
                if ($form->model()->exists) {
                    return 'required|email';  // 編集時のみ unique を無視
                } else {
                    return 'required|email|unique:users,email';  // 新規登録時は unique を適用
                }
            });

        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'))->disable();

        $form->saving(function (Form $form) {
            if (!$form->isEditing()) {
                $form->ignore(['deleted_at']);
            }
        });

        if ($form->isEditing()) {
            $form->datetime('deleted_at', __('Deleted at'))->default(NULL);
        }

        // 編集時のみパスワード再設定用のフィールドを表示
        if ($form->isEditing()) { // 編集フォームであることを確認
            $form->password('password', 'パスワード再設定用')
                ->help('パスワードを忘れた方への再設定用')
                ->rules('nullable|min:6'); // 入力がある場合のみバリデーション適用
        }


        //新規登録　パスワードランダムに設定　登録メールへ送信
        $form->saving(function (Form $form) {
            if (!$form->model()->id) { // 新規登録時のみ
                $randomPassword = Str::random(8);
                $form->model()->password = bcrypt($randomPassword); // `password` を適切にセット
                $form->model()->new_flag = 1; // new-flagをチェック状態にする

                // パスワード通知メール送信
                Mail::to($form->email)->send(new PasswordNotification(
                    $form->email,
                    $randomPassword, // ここを `$randomPassword` に変更
                    $form->name,
                    $form->employee_number
                ));
            } else { // 編集時のみ、入力があればパスワードを更新
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            }
        });

        return $form;
    }

    public function csvImport(Request $request)
    {
        $file = $request->file('file');
        $lexer_config = new LexerConfig();
        $lexer = new Lexer($lexer_config);

        $interpreter = new Interpreter();
        $interpreter->unstrict();

        $rows = [];
        $interpreter->addObserver(function (array $row) use (&$rows) {
            $rows[] = $row;
        });

        $lexer->parse($file, $interpreter);

        foreach ($rows as $key => $value) {
            if (count($value) == 4) {
                // 既存のユーザーを検索（employee_numberで検索）
                $user = User::where('employee_number', $value[1])->first();

                if (!$user) { // 既存ユーザーがいなければ新規作成
                    // ランダムパスワードを生成
                    $randomPassword = Str::random(8);

                    // 新規ユーザーを作成
                    $user = User::create([
                        'name' => $value[0],
                        'employee_number' => $value[1],
                        'email' => $value[3],
                        'password' => bcrypt($randomPassword),
                        'new_flag' => 1,
                    ]);

                    // メール送信（新規作成時のみ）
                    Mail::to($user->email)->send(new PasswordNotification(
                        $user->email,
                        $randomPassword,
                        $user->name,
                        $user->employee_number
                    ));

                    // 部署IDを取得（部署名が複数ある場合を考慮）
                    $departmentNames = array_map('trim', explode(',', $value[2])); // 各部署名の前後の空白を削除
                    $departmentIds = Department::whereIn('department_name', $departmentNames)->pluck('id')->toArray();

                    // 取得したIDが空でないかチェック
                    if (!empty($departmentIds)) {
                        // ユーザーと部署の関連付け（新規ユーザーでも既存ユーザーでも実行）
                        $user->dpm_departments()->sync($departmentIds);
                    } else {
                        // 部署が存在しない場合は関連付けをリセット
                        $user->dpm_departments()->sync([]);
                    }
                    // ユーザーと部署の関連付け
                    $user->dpm_departments()->sync($departmentIds);
                }
            }
        }

        return response()->json(
            ['data' => '成功'],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
