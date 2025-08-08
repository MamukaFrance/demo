<?php

namespace App\Service;

use PHPUnit\Framework\TestCase;

class BankServiceTest extends TestCase
{

    // Un test pour tester que récupérer les cours publiés fonctionnent
    public function testGetPublishedCourses(){

        $bankService = new BankService();

        $result_1 = $bankService->virement(3000);

        $this->assertEquals($result_1['code'], "4889");

        $result_2 = $bankService->virement(100);
        $this->assertEquals($result_2['code'], "200");
    }
}