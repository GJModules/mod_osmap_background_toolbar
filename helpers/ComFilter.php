<?php

namespace OsmapBackgroundHelper;

use JDatabaseQueryMysqli;

class ComFilter extends BackgroundComponent
{
    protected $db ;
    protected $component = 'com_filter';


    public function __construct()
    {
        $this->db = \JFactory::getDbo();
	    parent::__construct();
    }

    /**
     * Создание карты для фильтра
     * @return array
     * @throws \Exception
     * @since 3.9
     */
    public function createFilterMap(): array
    {
        $app = \Joomla\CMS\Factory::getApplication();
		$offset = $app->input->get('offset' , 0, 'INT');
        // Номер файла
        $indexFile = $app->input->get('indexFile' , 0, 'INT');
		$limit_links = $this->params['modOsmapBackgroundToolbar_params']['limit_links'];


        
		/** @var JDatabaseQueryMysqli $Query */
        $Query = $this->db->getQuery(true);
        $select = [
            $this->db->quoteName('sef_url'),

        ];
        $Query->select( $select );
        $Query->from( $this->db->quoteName('#__cf_customfields_setting_seo'))
	        ->where($this->db->quoteName('no_index') . ' = 0 ' );
	    $Query->setLimit( $limit_links , $offset );
        $this->db->setQuery($Query);

        /** @var array $resultFiltersUrl */
        $resultFiltersUrl = $this->db->loadColumn( 0 );


	    $Query = $this->db->getQuery(true);
	    $Query->select( 'COUNT(*)' );
	    $Query->from( $this->db->quoteName('#__cf_customfields_setting_seo'))
		    ->where($this->db->quoteName('no_index') . ' = 0 ' );
	    $Query->setLimit( 0 , 0 );
	    $this->db->setQuery($Query);
		/** @var int $count */
	    $count = $this->db->loadResult();



        foreach ( $resultFiltersUrl as $item)
        {
            $offset ++ ;
            $this->addUrlLocTag( $item );
        }#END FOREACH



	    $this->writeFileMap(  'link' );

        $indexFile ++ ;

        $returnData = [
	        'count'     => $count,         // Общее количество SEF ссылок фильтра
	        'offset'    => $offset,        // Индекс строки на которой закончилась выборка
	        'indexFile' => $indexFile,
        ];
        return $returnData ;



    }
}















