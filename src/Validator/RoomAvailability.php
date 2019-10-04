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
    public $message = 'The Room isn\'t available between "{{ value.bookedFrom }}" and "{{ value.bookedTo }}".';
}
