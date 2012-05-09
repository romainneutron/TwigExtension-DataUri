<?php

/**
 * This tig extension is released under MIT license
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DataURI;

/**
 * Twig extension for data URI, see README for example of use
 * Converts datas to the data URI Url scheme
 *
 * @see https://www.ietf.org/rfc/rfc2397.txt
 */
class TwigExtension extends \Twig_Extension
{

    /**
     *
     * @return string
     */
    public function getName()
    {
        return 'data_uri_twig_extension';
    }

    /**
     *
     * @return type
     */
    public function getFilters()
    {
        return array(
            'dataUri' => new \Twig_Filter_Method($this, 'dataUri'),
        );
    }

    /**
     *
     * @param mixed     $source     DataURI source
     * @param boolean   $strict     Use strict mode (length output)
     * @param string    $mime       the mime type
     * @param array     $parameters Extra parameters, see rfc
     * @return null
     */
    public function dataUri($source, $strict = true, $mime = null, $parameters = array())
    {
        $data = null;

        try {
            switch (true) {
                case is_resource($source):

                    $data = $this->getDataFromRessource($source, $strict, $mime, $parameters);

                    break;
                case is_scalar($source):

                    $data = $this->getDataFromScalar($source, $strict, $mime, $parameters);

                    break;
                default:
                    trigger_error("Tried to convert an unsupported source format", E_USER_WARNING);
                    break;
            }
        } catch (Exception\Exception $e) {

            trigger_error(sprintf("Error while building DataUri : %s", $e->getMessage()), E_USER_WARNING);
        }

        if ($data) {

            return Dumper::dump($data);
        }

        return null;
    }

    /**
     *
     * @param ressource     $source
     * @param boolean       $strict
     * @param string        $mime
     * @param array         $parameters
     * @return \DataURI\Data
     */
    protected function getDataFromRessource($source, $strict, $mime, Array $parameters)
    {

        $streamDatas = null;

        while ( ! feof($source)) {
            $streamDatas .= fread($source, 8192);
        }

        $data =  new Data($streamDatas, $mime, $parameters, $strict);
        $data->setBinaryData(true);

        return $data;
    }

    /**
     *
     * @param string        $source
     * @param boolean       $strict
     * @param string        $mime
     * @param array         $parameters
     * @return \DataURI\Data
     */
    protected function getDataFromScalar($source, $strict, $mime, $parameters)
    {

        if (file_exists($source)) {
            return Data::buildFromFile($source, $strict);
        }

        return new Data($source, $mime, $parameters, $strict);
    }
}
