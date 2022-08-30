<?php

namespace app\api;

use app\domain\market\Market;
use app\domain\market\MavenProductInfo;
use app\domain\market\ProductDescription;
use app\domain\market\Product;
use app\domain\market\ProductMavenArtifactDownloader;
use app\domain\market\OpenAPIProvider;

class ProductApi
{
  public function __invoke($request, $response, $args)
  {
    $key = $args['key'] ?? '';
    $version = $args['version'] ?? '';
    $data = $this->data($key, $version);
    $response->getBody()->write((string) json_encode($data));
    $response = $response->withHeader('Content-Type', 'application/json');
    return $response;
  }

  private function data(string $key, string $version)
  {
    $product = $this->getProduct($key);
    return [
      'product' => $product,
      'versionedData' => $this->getVersionedData($product, $version)
    ];
  }

  private function getVersionedData(Product $product, string $version) {
    $mavenProductInfo = $product->getMavenProductInfo();
    if (empty($version) && $mavenProductInfo != null) {
      $version = $mavenProductInfo->getLatestVersionToDisplay();
    }
    return [
      'description' => $this->getDescription($product, $version),
      'assetBaseUrl' => $product->assetBaseUrl($version),
      'openApiUrl' => $this->getOpenApiUrl($product, $version),
      'docUrl' => $this->getDocUrl($product, $version, $mavenProductInfo)
    ];
  }

  private function getDescription(Product $product, string $version)
  {
    return ProductDescription::readme($product, $version);
  }
  
  private function getOpenApiUrl(Product $product, string $version)
  {
    $openApiProvider = new OpenAPIProvider($product);
    return $openApiProvider->getOpenApiUrl($version);
  }
  
  private function getDocUrl(Product $product, string $version, $mavenProductInfo)
  {
    if ($mavenProductInfo == null) {
      return '';
    }
    $docArtifact = $mavenProductInfo->getFirstDocArtifact();
    if ($docArtifact != null) {
      $exists = (new ProductMavenArtifactDownloader())->downloadArtifact($product, $docArtifact, $version);
      if ($exists) {
        return $docArtifact->getDocUrl($product, $version);
      }
    }
    return '';
  }

  private function getProduct($key)
  {
    return Market::getProductByKey($key);
    // $products = [];
    // foreach (Market::listed() as $product) {
    //   $p = [
    //     'key' => $product->getKey(),
    //     'name' => $product->getName(),
    //     'shortDesc' => $product->getShortDescription(),
    //     'vendorImage' => $product->getVendorImage(),
    //     'type' => $product->getType(),
    //     'tags' => $product->getTags(),
    //     'url' => $product->getUrl()
    //   ];
    //   $products[] = $p;
    // }
    // return $products;
  }
}
