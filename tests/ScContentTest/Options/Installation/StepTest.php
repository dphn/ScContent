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

use ScContent\Options\Installation\Step,
    //
    PHPUnit_Framework_TestCase;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * @coversDefaultClass \ScContent\Options\Installation\Step
 */
class StepTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetStepName()
    {
        $stepName = 'test';
        $step = new Step($stepName);
        $this->assertAttributeEquals($stepName, 'name', $step);
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
        $step = new Step('test', $options);
        $this->assertAttributeEquals($layout, 'layout', $step);
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
        $step = new Step('test', $options);
        $this->assertAttributeEquals($template, 'template', $step);
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
        $step = new Step('test', $options);
        $this->assertAttributeEquals($title, 'title', $step);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetHeaderFromOptions()
    {
        $header = 'fake_header';
        $options = [
            'header' => $header,
        ];
        $step = new Step('test', $options);
        $this->assertAttributeEquals($header, 'header', $step);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetInfoFromOptions()
    {
        $info = 'fake_info';
        $options = [
            'info' => $info,
        ];
        $step = new Step('test', $options);
        $this->assertAttributeEquals($info, 'info', $step);
    }

    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $stepName = 'test';
        $step = new Step($stepName);
        $this->assertEquals($stepName, $step->getName());
    }

    /**
     * @covers ::setLayout
     */
    public function testSetLayout()
    {
        $layout = 'fake_layout';
        $step = new Step('test');
        $step->setLayout($layout);
        $this->assertAttributeEquals($layout, 'layout', $step);
    }

    /**
     * @covers ::getLayout
     */
    public function testGetLayout()
    {
        $layout = 'fake_layout';
        $step = new Step('test');
        $step->setLayout($layout);
        $this->assertEquals($layout, $step->getLayout());
    }

    /**
     * @covers ::setTemplate
     */
    public function testSetTemplate()
    {
        $template = 'fake_template';
        $step = new Step('test');
        $step->setTemplate($template);
        $this->assertAttributeEquals($template, 'template', $step);
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplate()
    {
        $template = 'fake_template';
        $step = new Step('test');
        $step->setTemplate($template);
        $this->assertEquals($template, $step->getTemplate());
    }

    /**
     * @covers ::setTitle
     */
    public function testSetTitle()
    {
        $title = 'fake_title';
        $step = new Step('test');
        $step->setTitle($title);
        $this->assertAttributeEquals($title, 'title', $step);
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $title = 'fake_title';
        $step = new Step('test');
        $step->setTitle($title);
        $this->assertEquals($title, $step->getTitle());
    }

    /**
     * @covers ::setHeader
     */
    public function testSetHeader()
    {
        $header = 'fake_header';
        $step = new Step('test');
        $step->setHeader($header);
        $this->assertAttributeEquals($header, 'header', $step);
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeader()
    {
        $header = 'fake_header';
        $step = new Step('test');
        $step->setHeader($header);
        $this->assertEquals($header, $step->getHeader());
    }

    /**
     * @covers ::setInfo
     */
    public function testSetInfo()
    {
        $info = 'fake_info';
        $step = new Step('test');
        $step->setInfo($info);
        $this->assertAttributeEquals($info, 'info', $step);
    }

    /**
     * @covers ::getInfo
     */
    public function testGetInfo()
    {
        $info = 'fake_info';
        $step = new Step('test');
        $step->setInfo($info);
        $this->assertEquals($info, $step->getInfo());
    }

    /**
     * @covers ::addMember
     */
    public function testAddMember()
    {
        $memberName = 'fake_member_name';
        $fakeMember = $this
            ->getMockBuilder('ScContent\Options\Installation\Member')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $step = new Step('test');
        $fakeMember->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($memberName));

        $step->addMember($fakeMember);
        $this->assertAttributeEquals(
            [$memberName => $fakeMember],
            'items',
            $step
        );
    }

    /**
     * @covers ::getMember
     */
    public function testGetUnknownMember()
    {
        $memberName = 'unknown_member_name';
        $step = new Step('test');
        $this->setExpectedException(
            '\ScContent\Exception\InvalidArgumentException'
        );
        $step->getMember($memberName);
    }

    /**
     * @covers ::getMember
     */
    public function testGetMember()
    {
        $memberName = 'fake_member_name';
        $fakeMember = $this
            ->getMockBuilder('ScContent\Options\Installation\Member')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $step = new Step('test');
        $fakeMember->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($memberName));

        $step->addMember($fakeMember);

        $member = $step->getMember($memberName);

        $this->assertEquals($member, $fakeMember);
    }

    /**
     * @covers ::setCurrentMemberName
     */
    public function testSetUnknownCurrentMemberName()
    {
        $currentMemberName = 'unknown_member_name';
        $step = new Step('test');
        $this->setExpectedException(
            '\ScContent\Exception\InvalidArgumentException'
        );
        $step->setCurrentMemberName($currentMemberName);
    }

    /**
     * @covers ::setCurrentMemberName
     */
    public function testSetCurrentMemberName()
    {
        $memberName = 'fake_member_name';
        $fakeMember = $this
            ->getMockBuilder('ScContent\Options\Installation\Member')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $step = new Step('test');
        $fakeMember->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($memberName));
        $step->addMember($fakeMember);

        $step->setCurrentMemberName($memberName);

        $this->assertAttributeEquals($memberName, 'currentMemberName', $step);
    }

    /**
     * @covers ::getCurrentMemberName
     */
    public function testGetCurrentMemberNameWhenCurrentMemberNameWasNotSet()
    {
        $step = new Step('test');
        $this->setExpectedException(
            'ScContent\Exception\DomainException'
        );
        $step->getCurrentMemberName();
    }

    /**
     * @covers ::getCurrentMemberName
     */
    public function testGetCurrentMemberName()
    {
        $memberName = 'fake_member_name';
        $fakeMember = $this
            ->getMockBuilder('ScContent\Options\Installation\Member')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $step = new Step('test');
        $fakeMember->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($memberName));
        $step->addMember($fakeMember);

        $step->setCurrentMemberName($memberName);
        $this->assertEquals($memberName, $step->getCurrentMemberName());
    }

    /**
     * @covers ::getCurrentMember
     */
    public function testGetCurrentMember()
    {
        $memberName = 'fake_member_name';
        $fakeMember = $this
            ->getMockBuilder('ScContent\Options\Installation\Member')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $step = new Step('test');
        $fakeMember->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($memberName));
        $step->addMember($fakeMember);

        $step->setCurrentMemberName($memberName);
        $member = $step->getCurrentMember();
        $this->assertEquals($member, $fakeMember);
    }
}
