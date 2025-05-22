<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable
{
  use Queueable, SerializesModels;

  public $data;
  public $isClient;

  public function __construct(array $data, bool $isClient = false)
  {
    $this->data = $data;
    $this->isClient = $isClient;
  }

  public function build()
  {
    $subject = $this->isClient
      ? 'Ваш заказ принят'
      : 'Новый заказ от клиента';

    return $this->subject($subject)
      ->view('emails.order');
  }
}
