<?php namespace Way\Tests;

trait ControllerHelpers {
    protected function see()
    {
        return call_user_func_array(array($this, 'assertSee'), func_get_args());
    }

    protected function assertSee($text, $element = 'body')
    {
        $crawler = $this->client->getCrawler();
        $matches = $crawler->filter("{$element}:contains('{$text}')");

        $this->assertGreaterThan(
            0,
            count($matches),
            "Expected to see the text '$text' within a '$element' element."
        );
    }
}

