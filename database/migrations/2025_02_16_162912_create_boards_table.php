<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            // 外部キー
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            // ユーザー名(ログインしているユーザーのnameを登録)
            $table->string('user_name');
            // 部署名(ログインしているユーザーの部署を保存)
            $table->string('department_name');
            $table->string('title');
            $table->text('text');
            $table->string('image')->nullable(); //nullable() を設定しているので、添付ファイルなしでも登録できる。
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards');
    }
};
