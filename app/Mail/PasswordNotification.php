<?php

namespace App\Mail;

// メール送信を キューに入れて非同期処理 できるようにする
use Illuminate\Bus\Queueable;
// 	メールの 送信クラス を作るための基盤
use Illuminate\Mail\Mailable;
// 	Eloquentモデルをデータ化 してメールに安全に渡せるようにする
use Illuminate\Queue\SerializesModels;

class PasswordNotification extends Mailable
{
  use Queueable, SerializesModels;

  public $email;
  public $password;
  public $name;
  public $employee_number;

  public function __construct($email, $password ,$name, $employee_number)
  {
    $this->email = $email;
    $this->password = $password;
    $this->name = $name;
    $this->employee_number = $employee_number;
  }

  public function build()
  {
    return $this->to($this->email)
      ->subject('重要:ポータルサイトのパスワード')
      ->view('emails.password_notification')
      ->with([
        'email' => $this->email,
        'password' => $this->password,
        'name' => $this->name,
        'employee_number' => $this->employee_number,
      ]);
  }
}
