<?php namespace Way\Tests;

trait ControllerHelpers {
    protected function see()
    {
        return call_user_func_array(array($this, 'assertSee'), func_get_args());
    }

    protected function shouldSee()
    {
        return call_user_func_array(array($this, 'assertSee'), func_get_args());
    }

    protected function notSee()
    {
        return call_user_func_array(array($this, 'assertNotSee'), func_get_args());
    }

    protected function shouldNotSee()
    {
        return call_user_func_array(array($this, 'assertNotSee'), func_get_args());
    }

    protected function assertSee($text, $element = 'body')
    {
        $matches = $this->getMatches($text, $element);

        $this->assertGreaterThan(
            0,
            count($matches),
            "Expected to see the text '$text' within a '$element' element."
        );
    }

    protected function assertNotSee($text, $element = 'body')
    {
        $matches = $this->getMatches($text, $element);

        $this->assertEquals(
            0,
            count($matches),
            "Didn't expect to see the text '$text' within a '$element' element."
        );
    }

    protected function getMatches($text, $element)
    {
        $crawler = $this->client->getCrawler();

        return $crawler->filter("{$element}:contains('{$text}')");
    }

}

