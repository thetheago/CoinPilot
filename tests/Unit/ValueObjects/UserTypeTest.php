<?php

declare(strict_types=1);
namespace Tests\Unit\ValueObjects;

use App\ValueObjects\UserType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserTypeTest extends TestCase
{
    public function testShouldCreateCommonUserType(): void
    {
        $userType = new UserType(UserType::COMMON);
        
        $this->assertEquals(UserType::COMMON, $userType->getValue());
        $this->assertTrue($userType->isCommon());
        $this->assertFalse($userType->isLojista());
    }

    public function testShouldCreateLojistaUserType(): void
    {
        $userType = new UserType(UserType::LOJISTA);
        
        $this->assertEquals(UserType::LOJISTA, $userType->getValue());
        $this->assertTrue($userType->isLojista());
        $this->assertFalse($userType->isCommon());
    }

    public function testShouldThrowExceptionForInvalidUserType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User type inválido, os tipos válidos são: comum, lojista');
        
        new UserType('invalid_type');
    }
} 