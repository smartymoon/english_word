<?php
/**
 * Created by PhpStorm.
 * User: lee
 * Date: 2018/8/21
 * Time: 10:17
 * @param $text
 * @param $attachments
 */

function bearyChat($text, $attachments = []) {
    $client = new ElfSundae\BearyChat\Client('https://hook.bearychat.com/=bwEhR/incoming/5a3ae7edf759a1d938e8914a1b49d22b', [
        'channel' => 'Tools'
    ]);
    $client->sendMessage([
        'text' => $text,
        'attachments' => $attachments
    ]);
}