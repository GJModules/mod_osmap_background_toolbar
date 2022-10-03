<?php

namespace OsmapBackgroundHelper;

use Joomla\CMS\Filesystem\File as JFile;

class BackgroundComponent
{
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
	 * Подпись для основного файла карты сайта
	 * @var string
	 * @since version
	 */
	protected $contextRootFiles = 'root' ;

	protected $params = [] ;
	public function __construct()
	{
		$app          = \Joomla\CMS\Factory::getApplication();
		$this->params = $app->input->get('module_params' , [] , 'ARRAY' );
	}


	/**
	 * Создать основной файл карты сайта
	 * @return string[]
	 * @since 3.9
	 */
    public function createFileAllMapXml(): array
    {
	    $app = \Joomla\CMS\Factory::getApplication();

	    $filepath = $_SERVER['DOCUMENT_ROOT'];
	    $files    = glob($filepath . '/sitemap-com_*');


	    foreach ($files as &$file)
	    {
		    // далее получаем последний добавленный/измененный файл
		    $LastModified[] = filemtime($file); // массив файлов со временем изменения файла

		    $file = str_replace($filepath, '', $file); // массив всех файлов

		    $this->addFileSitemapLoc($file);
	    }
	    $fileMapRoot    = $this->writeFileRootMap();
	    $fileMapRootURL = \JUri::root() . $fileMapRoot;


	    $app->enqueueMessage('Основной файл карты сайта создан.');


	    return [
		    'fileMapRootURL' => $fileMapRootURL,
	    ];


// Сортируем массив с файлами по дате изменения

//        $files = array_multisort($LastModified, SORT_NUMERIC, SORT_ASC, $FileName);
//        $lastIndex = count($LastModified) - 1;
//
// И вот он наш последний добавленный или измененный файл

//        $LastModifiedFile = $FileName[$lastIndex];


    }

	/**
	 * Добавление ссылок  на файлы карт компонентов ect/ (sitemap-com_content-1.xml , sitemap-com_virtuemart-category-1.xml)
	 * @param $url
	 * @since version
	 */
	protected function addFileSitemapLoc( $url ){
		$link = \JUri::root().$url ;
		$link = str_replace('//' , '/' , $link );
		$this->urlLocTag .= '<sitemap>';
		$this->urlLocTag .=     '<loc>'.$link.'</loc>';
		$this->urlLocTag .= '</sitemap>';
	}



	/**
	 * Создать основной файл карты сайта
	 * @return string
	 *
	 * @since version
	 */
	protected function writeFileRootMap(   ): string
	{
		$mapContent = '<?xml version="1.0" encoding="UTF-8"?>';
		$mapContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$mapContent .=      $this->urlLocTag ;
		$mapContent .= '</sitemapindex>';
		return $this->writeFile($this->contextRootFiles, false, $mapContent);
	}

	/**
	 * Записать ссылки в XML файл
	 * @param           $context
	 * @param           $indexFile
	 * @param   string  $mapContent
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function writeFile($context, $indexFile, string $mapContent): string
	{
		if ( $indexFile )
		{
			$fileMapName = 'sitemap-' . $this->component . '-' . $context . '-' . $indexFile . '.xml';
		}else{
			$fileMapName = 'sitemap-' . $context . '.xml' ;
		}#END IF

		$pathFile    = JPATH_SITE . '/' . $fileMapName;
		try
		{
			// Code that may throw an Exception or Error.
			JFile::write($pathFile, $mapContent);

			return $fileMapName;
			// throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch (\Exception $e)
		{
			// Executed only in PHP 5, will not be reached in PHP 7
			echo 'Выброшено исключение: ', $e->getMessage(), "\n";
			echo '<pre>';
			print_r($e);
			echo '</pre>' . __FILE__ . ' ' . __LINE__;
			die(__FILE__ . ' ' . __LINE__);
		}
	}

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

	    return $this->writeFile($context, $indexFile, $mapContent);
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

	/**
	 * Получить настройки модуля
	 * @return array
	 * @since 3.9
	 */
	protected static function getPluginSetting(){
		$plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('osmap', 'com_virtuemart');
		$Registry = new \Joomla\Registry\Registry();
		return $Registry->loadObject( json_decode( $plugin->params ))->toArray();
	}

}