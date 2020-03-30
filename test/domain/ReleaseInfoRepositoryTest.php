<?php
namespace test\domain;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use app\domain\ReleaseInfoRepository;

class ReleaseInfoRepositoryTest extends TestCase
{

    public function test_getBestMatchingVersion()
    {
        Assert::assertEquals('8.0.0', self::bestMatchingVersion('8.0.0'));
        Assert::assertEquals('8.0.1', self::bestMatchingVersion('8.0.1'));
        
        Assert::assertEquals('8.0.1', self::bestMatchingVersion('8.0'));
        Assert::assertEquals('8.0.1', self::bestMatchingVersion('8'));
        
        Assert::assertEquals('9.1.1', self::bestMatchingVersion('9'));
        Assert::assertEquals('9.1.1', self::bestMatchingVersion('9.1'));
        Assert::assertEquals('9.1.0', self::bestMatchingVersion('9.1.0'));
        
        Assert::assertEquals('dev', self::bestMatchingVersion('dev'));
        Assert::assertEquals('sprint', self::bestMatchingVersion('sprint'));
        Assert::assertEquals('nightly', self::bestMatchingVersion('nightly'));
        
        Assert::assertNull(ReleaseInfoRepository::getBestMatchingVersion('2.0.0'));
        Assert::assertNull(ReleaseInfoRepository::getBestMatchingVersion('notexisting'));
    }

    private static function bestMatchingVersion(string $version): string
    {
        return ReleaseInfoRepository::getBestMatchingVersion($version)->getVersion()->getVersionNumber();
    }
    
    public function test_isReleased()
    {
        Assert::assertTrue(self::isReleased('8.0.0'));
        Assert::assertTrue(self::isReleased('8.0'));
        Assert::assertTrue(self::isReleased('8'));
        
        Assert::assertTrue(self::isReleased('dev'));
        Assert::assertTrue(self::isReleased('sprint'));
        Assert::assertTrue(self::isReleased('nightly'));
        
        Assert::assertFalse(self::isReleased('2.0.0'));
        Assert::assertFalse(self::isReleased('notexisting'));
    }
    
    private static function isReleased(string $version): bool
    {
        return ReleaseInfoRepository::isReleased($version);
    }
}
