<?php

namespace App\Tests\Form\Type\User;

use App\Form\Type\User\ResetPasswordType;
use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class ResetPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'password' => 'azerty'
        ];

        $objectToCompare = new User();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ResetPasswordType::class, $objectToCompare);

        $object = new User();
        $object->setPassword('azerty');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        // check that each field will be rendered in the form
        $view = $form->createView();
        $children = $view->children;
    
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}