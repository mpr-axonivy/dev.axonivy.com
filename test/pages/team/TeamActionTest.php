<?php

namespace test\pages\team;

use PHPUnit\Framework\TestCase;
use test\AppTester;

class TeamActionTest extends TestCase
{

  public function testRender()
  {
    AppTester::assertThatGet('/team')
      ->statusCode(200)
      ->bodyContains('<b>ivyTeam</b> is the core development team of the <b>Axon Ivy Platform</b>');
  }

  public function testRender_brunoExists()
  {
    AppTester::assertThatGet('/team')
      ->statusCode(200)
      ->bodyContains('Bruno Bütler')
      ->bodyContains('/images/team/bb.jpg')
      ->bodyContains('Inf. Ing. HTL')
      ->bodyContains('Product Owner &amp; Team Leader');
  }
}
