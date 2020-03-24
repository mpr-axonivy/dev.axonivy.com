<?php
namespace app\api;

use app\release\model\ReleaseInfo;
use app\release\model\ReleaseInfoRepository;
use app\release\model\Version;
use app\util\StringUtil;
use app\util\ArrayUtil;
use Slim\Psr7\Request;
use Psr\Container\ContainerInterface;

class ApiCurrentRelease
{
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, $response, $args) {
        $releaseVersion = $request->getQueryParams()['releaseVersion'] ?? '';
        $data = [
            'latestReleaseVersion' => $this->getLatestReleaseVersion(),
            'latestServiceReleaseVersion' => $this->getLatestServiceReleaseVersion($releaseVersion)
        ];
        
        $response->getBody()->write((string) json_encode($data));
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
    
    private function getLatestReleaseVersion(): string
    {
        $releaseInfo = ReleaseInfoRepository::getLatest();
        return $releaseInfo == null ? '' : $releaseInfo->getVersion()->getBugfixVersion();
    }

    private function getLatestServiceReleaseVersion(string $currentReleaseVersion): string
    {
        $releaseInfo = null;
        
        if (Version::isValidVersionNumber($currentReleaseVersion)) {
            $version = new Version($currentReleaseVersion);
            $minorVersion = $version->getMinorVersion();
            
            $releaesInfos = ReleaseInfoRepository::getAvailableReleaseInfos();
            $releaseInfos = array_filter($releaesInfos, function (ReleaseInfo $releaseInfo) use ($minorVersion) {
                if (!$releaseInfo->getVersion()->isLongTermSupportVersion()) {
                    return false;
                }
                return StringUtil::startsWith($releaseInfo->getVersion()->getMinorVersion(), $minorVersion);
            });
            
            $releaseInfo = ArrayUtil::getLastElementOrNull($releaseInfos);
        }
        
        if ($releaseInfo == null) {
            $releaseInfo = ReleaseInfoRepository::getLatestLongTermSupport();
        }
            
        return $releaseInfo == null ? '' : $releaseInfo->getVersion()->getBugfixVersion();
    }
}

