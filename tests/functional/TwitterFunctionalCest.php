<?php


class TwitterFunctionalCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage(['monitor/index']);
    }


    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->see('Monitor Social Media!', 'h1');
    }
}
