<?php

namespace DataURI;

class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var TwigExtension
     */
    protected $extension;

    protected function setUp()
    {
        $loader = new \Twig_Loader_Array(array());
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
        $this->assertArrayHasKey(0, $filters);
        $simpleFilter = $filters[0];
        $this->assertInstanceOf('\\Twig_SimpleFilter', $simpleFilter);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromResource
     */
    public function testDataUriResource()
    {
        $file = __DIR__ . '/../../smile.png';
        $resource = fopen($file, 'r');

        $template = $this->twig->createTemplate('{{ file | dataUri(false, "image/jpeg") }}');
        $data = $template->render(array('file' => $resource));
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
        $template = $this->twig->createTemplate('{{ scalarValue | dataUri }}');
        $data = $template->render(array('scalarValue' => 'Hello world !'));
        $this->assertEquals('data:text/plain;charset=US-ASCII,Hello%20world%20%21', $data);

        $template = $this->twig->createTemplate('{{ scalarValue | dataUri(true, "text/csv", {"charset":"utf-8"}) }}');
        $data = $template->render(array('scalarValue' => 'Bonne année !'));
        $this->assertEquals('data:text/csv;charset=utf-8,Bonne%20ann%C3%A9e%20%21', $data);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriFile()
    {
        $filepath = __DIR__ . '/../../photo01.JPG';
        $template = $this->twig->createTemplate('{{ filepath | dataUri(false) }}');
        $template->render(array('filepath' => $filepath));

        $filepath = __DIR__ . '/../../smile.png';
        $template = $this->twig->createTemplate('{{ filepath | dataUri }}');
        $template->render(array('filepath' => $filepath));
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriUrl()
    {
        $url = 'http://twig.sensiolabs.org/images/logo.png';
        $template = $this->twig->createTemplate('{{ url | dataUri(false) }}');
        $template->render(array('url' => $url));
    }


    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriBinary()
    {
        $data = file_get_contents( __DIR__ . '/../../photo01.JPG');
        $template = $this->twig->createTemplate('{{ data | dataUri(false) }}');
        $template->render(array('data' => $data));
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @covers DataUri\TwigExtension::getDataFromScalar
     */
    public function testDataUriFileStrict()
    {
        $template = $this->twig->createTemplate('{{ file | dataUri(false) }}');
        $data = $template->render(array('file' => __DIR__ . '/../../photo01.JPG'));
        $this->assertTrue(strlen($data) > 3000000);
        $this->assertTrue(strpos($data, 'data:image/jpeg;base64,/9j/4S/+RXhpZgAATU0AKgAAAAgAC') === 0);
    }

    /**
     * @covers DataUri\TwigExtension::dataUri
     * @expectedException \Twig_Error_Runtime
     */
    public function testDataUriUnknownFormat()
    {
        $template = $this->twig->createTemplate('{{ array | dataUri(false) }}');
        $template->render(array('array' => array()));
    }
}
