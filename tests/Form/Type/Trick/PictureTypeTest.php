<?php

namespace App\Tests\Form\Type\Trick;

use App\Entity\Media;
use App\Form\Type\Trick\PictureType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class PictureTypeTest extends TypeTestCase
{

    protected function getExtensions()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {

        // object to compare
        $objectToCompare = new Media();
        $form = $this->factory->create(PictureType::class, $objectToCompare);
        $formData = [
            'url' => 'suitcase-grab-1.jpg'
        ];
        $form->submit($formData);

        // check if form is synchronized
        $this->assertTrue($form->isSynchronized());

        // check that each field will be rendered in the form
        $view = $form->createView();
        $children = $view->children;    
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

    }
}