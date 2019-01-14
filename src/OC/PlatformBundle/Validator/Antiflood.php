<?php

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Merci d'attendre une minute entre la publication de deux annonces.";

    public function validateBy()
    {
        return 'oc_platform_antiflood';
    }
}