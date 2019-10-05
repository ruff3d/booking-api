<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RoomAvailability extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The Room isn\'t available between {{ from }} and {{ to }}.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
