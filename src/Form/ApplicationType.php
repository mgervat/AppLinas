<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType {


    protected function getConfiguration($required, $label, $placeholder) {
        return [
            'required' => $required,
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ];
    }

}