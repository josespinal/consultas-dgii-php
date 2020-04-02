<?php

use DGII\Consultas\Rnc;
use PHPUnit\Framework\TestCase;

// class RncTest extends TestCase
// {
//   public function testCanBeCreatedFromValidEmailAddress(): void
//     {
//         $this->assertInstanceOf(
//             Email::class,
//             Email::fromString('user@example.com')
//         );
//     }
// }

$consulta = new Rnc();

echo $consulta->queryDoc('131233351')."\n"; // Rnc valido Test OK
echo $consulta->queryDoc('00543266297'); // cedula Fail
