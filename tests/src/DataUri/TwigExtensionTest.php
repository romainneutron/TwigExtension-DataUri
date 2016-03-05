<?php

namespace DataURI;

class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    protected function setUp()
    {
        $loader = new \Twig_Loader_String();
        $this->extension = new TwigExtension();
        $this->twig = new \Twig_Environment($loader);
        $this->twig->addExtension($this->extension);
    }

    /**
     * @covers DataUri\TwigExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals('data_uri_twig_extension', $this->extension->getName());
    }

    /**
     * @covers DataUri\TwigExtension::getFilters
     */
    public function testGetFilters()
    {
        $filters = $this->extension->getFilters();
        $this->assertArrayHasKey('dataUri', $filters);
        $method = $filters['dataUri'];
        $this->assertInstanceOf('\\Twig_Filter_Method', $method);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromRessource
     */
    public function testDataUriRessource()
    {
        $file = __DIR__ . '/../../smile.png';
        $ressource = fopen($file, 'r');

        $data = $this->twig->render('{{ file | dataUri(false, "image/jpeg") }}', array('file' => $ressource));
        $this->assertEquals('data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAB'
            . 'AAAAAQCAMAAAAoLQ9TAAAAyVBMVEUzM2a9pUL90Bzi0phoZH3/6pb/9cytooiEg'
            . '3v93FhST2vuwhL977qVjGr/++z/1zl0cILPvoj/5X9eW3Xix1/9ywP/7aX/+NyL'
            . 'hnb/88Sfk37NwZ86OWb80ix0bHD/5HZZV3T/8LX//vn/20yFeWr/6I1jYob7zBD'
            . 'GqTrp4MNoZ4j/7J7/9tPNtWCDg4P/4mtSUHT1xwuckWT//fP+2kfpz2r/5oReXY'
            . 'D8zAr/7qz/+uSLhnaflYjPx7Q6Omn70zP977qAeHoAAAAdPTPOAAAAQ3RSTlP//'
            . '///////////////////////////////////////////////////////////////'
            . '//////////////////////8AQWIE7wAAAM5JREFUGFclj9lywjAMRcWSkIWGVTY'
            . 'ppTQMe0JCwA22hzZT//9HVTb3SeeMNJLA2KCNqwxYlJVSeSidIoFCJ5oXqWqtAe'
            . 'LN3YmMrWorpL7vH5r7/S/WyKMBrJLk/OCF35/luzYmwbSmAT/NlNcIXAAqzgvio'
            . 'cp3a4FvgN4JfiyzCXyPbEeYXsfDTDHvMwq7cUlb2PL3+hy8Rx8XgR0S2LLeXwTR'
            . 'di5kHbhLV00zXVehOOCtdL/U2Aox6mJN7IQ5xvRs3AmIX8KY4BaUFo35B3lHJ6U'
            . 'KnYJ8AAAAAElFTkSuQmCC', $data);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriScalar()
    {
        $data = $this->twig->render('{{ scalarValue | dataUri }}', array('scalarValue' => 'Hello world !'));
        $this->assertEquals('data:text/plain;charset=US-ASCII,Hello%20world%20%21', $data);

        $data = $this->twig->render('{{ scalarValue | dataUri(true, "text/csv", {"charset":"utf-8"}) }}', array('scalarValue' => 'Bonne année !'));
        $this->assertEquals('data:text/csv;charset=utf-8,Bonne%20ann%C3%A9e%20%21', $data);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriFile()
    {
        $filepath = __DIR__ . '/../../photo01.JPG';
        $this->twig->render('{{ filepath | dataUri(false) }}', array('filepath' => $filepath));

        $filepath = __DIR__ . '/../../smile.png';
        $this->twig->render('{{ filepath | dataUri }}', array('filepath' => $filepath));
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriUrl()
    {
        $url = 'http://www.alchemy.fr/images/header_03.png';
        $this->twig->render('{{ url | dataUri(false) }}', array('url' => $url));
    }


    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriBinary()
    {
        $data = file_get_contents( __DIR__ . '/../../photo01.JPG');
        $this->twig->render('{{ data | dataUri(false) }}', array('data' => $data));
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriFileStrict()
    {
        $data = $this->twig->render('{{ file | dataUri(false) }}', array('file' => __DIR__ . '/../../photo01.JPG'));
        $this->assertTrue(strlen($data) > 3000000);
        $this->assertTrue(strpos($data, 'data:image/jpeg;base64,/9j/4S/+RXhpZgAATU0AKgAAAAgAC') === 0);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @expectedException \Twig_Error_Runtime
     */
    public function testDataUriUnknownFormat()
    {
        $this->twig->render('{{ array | dataUri(false) }}', array('array' => array()));
    }
}
