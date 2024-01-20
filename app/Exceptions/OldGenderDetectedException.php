<?php

namespace App\Exceptions;

class OldGenderDetectedException extends \Exception {
  const OLD_GENDERS_FOR_DE = [
    'frau',
    'mann',
  ];

  const MAPPING_OLD_TO_NEW = [
    'frau' => 'damen',
    'mann' => 'herren',
  ];

  public $old_gender;

  public function getNewGender() {
    return self::MAPPING_OLD_TO_NEW[$this->old_gender];
  }
}
