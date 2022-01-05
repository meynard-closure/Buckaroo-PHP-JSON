<?php

namespace SeBuDesign\BuckarooJson\Tests\Unit\Transaction;

use GuzzleHttp\Client;
use SeBuDesign\BuckarooJson\Transaction;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_should_be_an_instance_of_transaction()
    {
        $oTransaction = $this->getTransaction();

        $this->assertInstanceOf(
            Transaction::class,
            $oTransaction
        );
    }

    /** @test */
    public function it_should_have_set_the_right_constructor_properties()
    {
        $oTransaction = $this->getTransaction();

        $this->assertEquals(
            'website-key',
            $this->accessProtectedProperty($oTransaction, 'sWebsiteKey')
        );
        $this->assertEquals(
            'secret-key',
            $this->accessProtectedProperty($oTransaction, 'sSecretKey')
        );
    }

    /** @test */
    public function it_should_have_set_the_right_testing_properties()
    {
        $oTransaction = $this->getTransaction();

        $this->assertFalse(
            $this->accessProtectedProperty($oTransaction, 'bTesting')
        );
        $this->assertEquals(
            'https://checkout.buckaroo.nl/json/',
            $this->accessProtectedProperty($oTransaction, 'aRequestData')['base_uri']
        );

        $this->assertInstanceOf(
            Transaction::class,
            $oTransaction->putInTestMode()
        );

        $this->assertTrue(
            $this->accessProtectedProperty($oTransaction, 'bTesting')
        );
        $this->assertEquals(
            'https://testcheckout.buckaroo.nl/json/',
            $this->accessProtectedProperty($oTransaction, 'aRequestData')['base_uri']
        );
    }

    /** @test */
    public function it_should_have_a_http_client()
    {
        $oTransaction = $this->getTransaction();

        $this->assertInstanceOf(
            Client::class,
            $this->accessProtectedProperty($oTransaction, 'oHttpClient')
        );
    }

    /** @test */
    public function it_should_have_an_empty_body()
    {
        $oTransaction = $this->getTransaction();

        $this->assertFalse(
            isset($oTransaction->oData)
        );
    }
}
