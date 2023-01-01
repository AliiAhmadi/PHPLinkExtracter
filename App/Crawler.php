<?php

class Crawler
{
    private $curl;
    private $startLink;
    private $repeats;
    private array $links;
    private int $step;

    public function __construct($link, int $numberOfRepeats = 1)
    {
        $this->startLink = $link;
        $this->repeats = $numberOfRepeats;
        $this->step = 0;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->startLink);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        $this->links = [];
    }

    private function GoOneStep(Crawler $object)
    {

        $result = curl_exec($object->curl);
        $result = $this->extract($result);
        foreach ($object->links as $link) {
            $object->links[] = $object->extract($result);
        }
        $object->step++;
        return $result;
    }

    private function extract($html)
    {
        $links = [];
        $dom = new DOMDocument;
        if ($dom->loadHTML($html, LIBXML_NOWARNING)) {
            foreach ($dom->getElementsByTagName("a") as $link) {
                $href = $link->getAttribute("href");
                $links[] = $href;
            }
        }
        return $links;
    }

    public function run()
    {
        for ($i = 1; $i < $this->repeats; $i++) {
            $this->links[] = $this->GoOneStep($this);
        }
        return $this->links;
    }
}
