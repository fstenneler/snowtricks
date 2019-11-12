<?php

namespace App\Tests\Form\Type\Trick;

use App\Form\Type\Trick\VideoType;
use App\Entity\Media;
use Symfony\Component\Form\Test\TypeTestCase;

class VideoTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {

        // object to compare
        $objectToCompare = new Media();
        $form = $this->factory->create(VideoType::class, $objectToCompare);
        $formData = [
            'url' => 'https://youtu.be/PHjpWo4edfw?list=RDGMEMJQXQAmqrnmK1SEjY_rKBGAVMPHjpWo4edfw'
        ];
        $form->submit($formData);

        // object
        $object = new Media();
        $object->setUrl('https://youtu.be/PHjpWo4edfw?list=RDGMEMJQXQAmqrnmK1SEjY_rKBGAVMPHjpWo4edfw');

        // check if form is synchronized
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