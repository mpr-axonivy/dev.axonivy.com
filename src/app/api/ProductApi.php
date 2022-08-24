<?php

namespace app\api;

use app\domain\market\Market;
use app\domain\market\Product;

class ProductApi
{
  public function __invoke($request, $response, $args)
  {
    $key = $args['key'] ?? '';
    $data = $this->data($key);
    $response->getBody()->write((string) json_encode($data));
    $response = $response->withHeader('Content-Type', 'application/json');
    return $response;
  }

  private function data($key)
  {
    return [
      'product' => $this->getProduct($key)
    ];
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
