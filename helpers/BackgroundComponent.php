<?php

namespace OsmapBackgroundHelper;

use Joomla\CMS\Filesystem\File as JFile;

class BackgroundComponent
{
    public function createFileAllMapXml(){

        echo'<pre>';print_r( $_SERVER['DOCUMENT_ROOT'] );echo'</pre>'.__FILE__.' '.__LINE__;
        die(__FILE__ .' '. __LINE__ );


        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/some_directory/';

        foreach (glob($filepath . 'filename_*.txt') as $file)
        {

            // далее получаем последний добавленный/измененный файл

            $LastModified[] = filemtime($file); // массив файлов со временем изменения файла

            $FileName[] = $file; // массив всех файлов

        }

// Сортируем массив с файлами по дате изменения

        $files = array_multisort($LastModified, SORT_NUMERIC, SORT_ASC, $FileName);
        $lastIndex = count($LastModified) - 1;

// И вот он наш последний добавленный или измененный файл

        $LastModifiedFile = $FileName[$lastIndex];


        echo '<pre>';
        print_r($php_files);
        echo '</pre>' . __FILE__ . ' ' . __LINE__;
        die(__FILE__ . ' ' . __LINE__);

    }

    /**
     * @var string Название компонента для которого создается карта
     * @since 3.9
     */
    protected $component;
    /**
     * Хранение тегов <url><loc>....<loc><url>
     * @var string
     * @since 3.9
     */
    protected $urlLocTag = '' ;

    /**
     * Добавить ссылку в коллекцию
     * @param $url
     * @return void
     * @since 3.9
     */
    protected function addUrlLocTag( $url ){
        $link = \JUri::root().$url ;
        $link = str_replace('//' , '/' , $link );

        $this->urlLocTag .= '<url>';
        $this->urlLocTag .=     '<loc>'.$link.'</loc>';
        $this->urlLocTag .= '</url>';
    }
    /**
     * Запись в файл sitemap-com_...-{$context}-{$indexFile}.xml
     * @param $indexFile
     * @param $context
     * @return void
     * @since 3.9
     */
    protected function writeFileMap( $indexFile , $context ){

        $mapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $mapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $mapContent .=      $this->urlLocTag ;
        $mapContent .= '</urlset>';

        $fileMapName = 'sitemap-'.$this->component.'-'.$context.'-'.$indexFile.'.xml';
        $pathFile = JPATH_SITE .'/'. $fileMapName ;
        try
        {
            // Code that may throw an Exception or Error.
            JFile::write( $pathFile , $mapContent);
            // throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
        }
        catch (\Exception $e)
        {
            // Executed only in PHP 5, will not be reached in PHP 7
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
            die(__FILE__ .' '. __LINE__ );
        }
    }

    /**
     * Поиск файла по маске
     * @param $pattern
     * @param $flags
     * @return array|false
     * @since 3.9
     */
    protected function search_file_by($pattern, $flags = 0)
    {

        // поиск по маске в папке
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
        {

            // поиск в подпапках
            $files = array_merge($files, $this->search_file_by($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }
}