<?php

namespace App\Listeners;

use App\Models\LogEmailEmployer;
use App\Models\LogEmailSeeker;
use Illuminate\Mail\Events\MessageSent;

class EmailHasBeenSentListener {

    public function handle(MessageSent $event) {
        /* $custom_message_id = 0;
          $custom_log_type = '';
          $headers = $event->message->getHeaders()->getAll();
          foreach($headers as $key => $value)
          {
          $fieldName = $value->getFieldName();
          $matchName = 'custom_message_id';
          $matchType = 'custom_log_type';
          if ($fieldName==$matchName)
          {
          if (
          $value instanceof \Swift_Mime_Headers_UnstructuredHeader ||
          $value instanceof \Swift_Mime_Headers_OpenDKIMHeader
          ) {
          if ($fieldName != 'X-PM-Tag') {
          $custom_message_id = $value->getValue();
          } else {
          $custom_message_id = $value->getValue();
          }
          } else if (
          $value instanceof \Swift_Mime_Headers_DateHeader ||
          $value instanceof \Swift_Mime_Headers_IdentificationHeader ||
          $value instanceof \Swift_Mime_Headers_ParameterizedHeader ||
          $value instanceof \Swift_Mime_Headers_PathHeader
          ) {
          $custom_message_id = $value->getFieldBody();
          }
          }
          elseif ($fieldName==$matchType)
          {
          if (
          $value instanceof \Swift_Mime_Headers_UnstructuredHeader ||
          $value instanceof \Swift_Mime_Headers_OpenDKIMHeader
          ) {
          if ($fieldName != 'X-PM-Tag') {
          $custom_log_type = $value->getValue();
          } else {
          $custom_log_type = $value->getValue();
          }
          } else if (
          $value instanceof \Swift_Mime_Headers_DateHeader ||
          $value instanceof \Swift_Mime_Headers_IdentificationHeader ||
          $value instanceof \Swift_Mime_Headers_ParameterizedHeader ||
          $value instanceof \Swift_Mime_Headers_PathHeader
          ) {
          $custom_log_type = $value->getFieldBody();
          }
          }
          }

          if($custom_log_type == 'employer')
          {
          $Log = LogEmailEmployer::find($custom_message_id);

          if(!empty($Log))
          {
          $Log->is_success = 1;
          $Log->success_time = time();

          $Log->save();
          }
          }
          elseif($custom_log_type == 'seeker')
          {
          $Log = LogEmailSeeker::find($custom_message_id);

          if(!empty($Log))
          {
          $Log->is_success = 1;
          $Log->success_time = time();

          $Log->save();
          }
          } */

        return false;
    }

    private function parseAddresses(array $array): array {
        $parsed = [];
        foreach ($array as $address) {
            $parsed[] = $address->getAddress();
        }
        return $parsed;
    }

    private function parseBodyText($body): string {
        return preg_replace('~[\r\n]+~', '<br>', $body);
    }

}
