<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Form;

class UserTypeTest extends TypeTestCase
{
    private $validator;

    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));
        return array(new ValidatorExtension($this->validator));
    }

    public function testSubmitForm()
    {
        $formData = array(
            'roles' => ['ROLE_USER'],
            'username' => 'testUser',
            'password' => array('first' => 'test', 'second' => 'test'),
            'email' => 'emailTest@test.com'
        );

        $form = $this->factory->create(UserType::class);

        $user = new User();
        $user->setRoles($formData['roles']);
        $user->setUsername($formData['username']);
        $user->setPassword($formData['password']['first']);
        $user->setEmail($formData['email']);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($user->getRoles(), $form->get('roles')->getData());
        $this->assertEquals($user->getUsername(), $form->get('username')->getData());
        $this->assertEquals($user->getPassword(), $form->get('password')->getData());
        $this->assertEquals($user->getEmail(), $form->get('email')->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}