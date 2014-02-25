<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest\Options\Installation;

use ScContent\Options\Installation\Installation,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Options\Installation\Installation
 */
class InstallationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetModuleName()
    {
        $moduleName = 'test';
        $installation = new Installation($moduleName);
        $this->assertAttributeEquals($moduleName, 'moduleName', $installation);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetLayoutFromOptions()
    {
        $layout = 'fake_layout';
        $options = [
            'layout' => $layout,
        ];
        $installation = new Installation('test', $options);
        $this->assertAttributeEquals($layout, 'layout', $installation);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetTemplateFromOptions()
    {
        $template = 'fake_template';
        $options = [
            'template' => $template,
        ];
        $installation = new Installation('test', $options);
        $this->assertAttributeEquals($template, 'template', $installation);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetBrandFromOptions()
    {
        $brand = 'fake_brand';
        $options = [
            'brand' => $brand,
        ];
        $installation = new Installation('test', $options);
        $this->assertAttributeEquals($brand, 'brand', $installation);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetTitleFromOptions()
    {
        $title = 'fake_title';
        $options = [
            'title' => $title,
        ];
        $installation = new Installation('test', $options);
        $this->assertAttributeEquals($title, 'title', $installation);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetRedirectOnSuccessFromOptions()
    {
        $redirectOnSuccess = 'fake_redirect_on_success';
        $options = [
            'redirect_on_success' => $redirectOnSuccess,
        ];
        $installation = new Installation('test', $options);
        $this->assertAttributeEquals(
            $redirectOnSuccess,
            'redirectOnSuccess',
            $installation
        );
    }

    /**
     * @covers ::getModuleName
     */
    public function testGetModuleName()
    {
        $moduleName = 'test';
        $installation = new Installation($moduleName);
        $this->assertEquals($moduleName, $installation->getModuleName());
    }

    /**
     * @covers ::setLayout
     */
    public function testSetLayout()
    {
        $layout = 'fake_layout';
        $installation = new Installation('test');
        $installation->setLayout($layout);
        $this->assertAttributeEquals($layout, 'layout', $installation);
    }

    /**
     * @covers ::getLayout
     */
    public function testGetLayout()
    {
        $layout = 'fake_layout';
        $installation = new Installation('test');
        $installation->setLayout($layout);
        $this->assertEquals($layout, $installation->getLayout());
    }

    /**
     * @covers ::setTemplate
     */
    public function testSetTemplate()
    {
        $template = 'fake_template';
        $installation = new Installation('test');
        $installation->setTemplate($template);
        $this->assertAttributeEquals($template, 'template', $installation);
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplate()
    {
        $template = 'fake_template';
        $installation = new Installation('test');
        $installation->setTemplate($template);
        $this->assertEquals($template, $installation->getTemplate());
    }

    /**
     * @covers ::setBrand
     */
    public function testSetBrand()
    {
        $brand = 'fake_brand';
        $installation = new Installation('test');
        $installation->setBrand($brand);
        $this->assertAttributeEquals($brand, 'brand', $installation);
    }

    /**
     * @covers ::getBrand
     */
    public function testGetBrand()
    {
        $brand = 'fake_brand';
        $installation = new Installation('test');
        $installation->setBrand($brand);
        $this->assertEquals($brand, $installation->getBrand());
    }

    /**
     * @covers ::setTitle
     */
    public function testSetTitle()
    {
        $title = 'fake_title';
        $installation = new Installation('test');
        $installation->setTitle($title);
        $this->assertAttributeEquals($title, 'title', $installation);
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $title = 'fake_title';
        $installation = new Installation('test');
        $installation->setTitle($title);
        $this->assertEquals($title, $installation->getTitle());
    }

    /**
     * @covers ::setRedirectOnSuccess
     */
    public function testSetRedirectOnSuccess()
    {
        $redirectOnSuccess = 'fake_redirect_on_success';
        $installation = new Installation('test');
        $installation->setRedirectOnSuccess($redirectOnSuccess);
        $this->assertAttributeEquals(
            $redirectOnSuccess,
            'redirectOnSuccess',
            $installation
        );
    }

    /**
     * @covers ::getRedirectOnSuccess
     */
    public function testGetRedirectOnSuccess()
    {
        $redirectOnSuccess = 'fake_redirect_on_success';
        $installation = new Installation('test');
        $installation->setRedirectOnSuccess($redirectOnSuccess);
        $this->assertEquals(
            $redirectOnSuccess,
            $installation->getRedirectOnSuccess()
        );
    }

    /**
     * @covers ::addStep
     */
    public function testAddStep()
    {
        $stepName = 'fake_step_name';
        $fakeStep = $this
            ->getMockBuilder('ScContent\Options\Installation\Step')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $installation = new Installation('test');
        $fakeStep->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($stepName));

        $installation->addStep($fakeStep);
        $this->assertAttributeEquals(
            [$stepName => $fakeStep],
            'items',
            $installation
        );
    }

    /**
     * @covers ::getStep
     */
    public function testGetUnknownStep()
    {
        $stepName = 'unknown_step_name';
        $installation = new Installation('test');
        $this->setExpectedException(
            '\ScContent\Exception\InvalidArgumentException'
        );
        $installation->getStep($stepName);
    }

    /**
     * @covers ::getStep
     */
    public function testGetStep()
    {
        $stepName = 'fake_step_name';
        $fakeStep = $this
            ->getMockBuilder('ScContent\Options\Installation\Step')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $installation = new Installation('test');
        $fakeStep->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($stepName));

        $installation->addStep($fakeStep);

        $step = $installation->getStep($stepName);

        $this->assertEquals($step, $fakeStep);
    }

    /**
     * @covers ::setCurrentStepName
     */
    public function testSetUnknownCurrentStepName()
    {
        $currentStepName = 'unknown_step_name';
        $installation = new Installation('test');
        $this->setExpectedException(
            '\ScContent\Exception\InvalidArgumentException'
        );
        $installation->setCurrentStepName($currentStepName);
    }

    /**
     * @covers ::setCurrentStepName
     */
    public function testSetCurrentStepName()
    {
        $stepName = 'fake_step_name';
        $fakeStep = $this
            ->getMockBuilder('ScContent\Options\Installation\Step')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $installation = new Installation('test');
        $fakeStep->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($stepName));
        $installation->addStep($fakeStep);

        $installation->setCurrentStepName($stepName);

        $this->assertAttributeEquals(
            $stepName,
            'currentStepName',
            $installation
        );
    }

    /**
     * @covers ::getCurrentStepName
     */
    public function testGetCurrentStepNameWhenCurrentStepNameWasNotSet()
    {
        $installation = new Installation('test');
        $this->setExpectedException(
            '\ScContent\Exception\DomainException'
        );
        $installation->getCurrentStepName();
    }

    /**
     * @covers ::getCurrentStepName
     */
    public function testGetCurrentStepName()
    {
        $stepName = 'fake_step_name';
        $fakeStep = $this
            ->getMockBuilder('ScContent\Options\Installation\Step')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $installation = new Installation('test');
        $fakeStep->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($stepName));
        $installation->addStep($fakeStep);

        $installation->setCurrentStepName($stepName);
        $this->assertEquals($stepName, $installation->getCurrentStepName());
    }

    /**
     * @covers ::getCurrentStep
     */
    public function testGetCurrentStep()
    {
        $stepName = 'fake_step_name';
        $fakeStep = $this
            ->getMockBuilder('ScContent\Options\Installation\Step')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $installation = new Installation('test');
        $fakeStep->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($stepName));
        $installation->addStep($fakeStep);

        $installation->setCurrentStepName($stepName);
        $step = $installation->getCurrentStep();
        $this->assertEquals($step, $fakeStep);
    }
}
