<?php

namespace app\api;

use app\domain\market\Market;

class MarketApi
{
  public function __invoke($request, $response, $args)
  {
    $data = $this->artifacts();
    $response->getBody()->write((string) json_encode($data));
    $response = $response->withHeader('Content-Type', 'application/json');
    $response = $response->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    return $response;
  }

  private function artifacts()
  {
    return [
      'artifacts' => $this->getMarketProducts(),
      'types' => $this->getMarketTypes(),
      'tags' => $this->getMarketTags()
    ];
  }

  private function getMarketTags(): array
  {
    return Market::tags(Market::listed());
  }

  private function getMarketTypes(): array
  {
    $types = [];
    foreach (Market::types() as $type) {
      $t = [
        'name' => $type->getName(),
        'filter' => $type->getFilter(),
        'icon' => $type->getIcon(),
      ];
      $types[] = $t;
    }
    return $types;
  }

  private function getMarketProducts(): array
  {
    $products = [];
    foreach (Market::listed() as $product) {
      $p = [
        'key' => $product->getKey(),
        'name' => $product->getName(),
        'shortDesc' => $product->getShortDescription(),
        'vendorImage' => $product->getVendorImage(),
        'type' => $product->getType(),
        'tags' => $product->getTags(),
        'url' => $product->getUrl()
      ];
      $products[] = $p;
    }
    return $products;
  }
}
