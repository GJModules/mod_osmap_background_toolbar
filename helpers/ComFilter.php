<?php

namespace OsmapBackgroundHelper;

class ComFilter extends BackgroundComponent
{
    protected $db ;
    protected $component = 'com_filter';


    public function __construct()
    {
        $this->db = \JFactory::getDbo();
    }

    /**
     * Создание карты для фильтра
     * @return array
     * @throws \Exception
     * @since 3.9
     */
    public function createFilterMap(){
        $app = \Joomla\CMS\Factory::getApplication();
        $offset = $app->input->get('offset' , 0, 'INT');
        // Номер файла
        $indexFile = $app->input->get('indexFile' , 0, 'INT');


        $Query = $this->db->getQuery(true);
        $select = [
            $this->db->quoteName('sef_url'),
        ];
        $Query->select( $select );
        $Query->from( $this->db->quoteName('#__cf_customfields_setting_seo'));
        $this->db->setQuery($Query);
        $resultFiltersUrl = $this->db->loadColumn( 0   );


        foreach ( $resultFiltersUrl as $item)
        {
            $offset ++ ;
            $this->addUrlLocTag( $item );
        }#END FOREACH
        $this->writeFileMap( $indexFile ,'link' );

        $indexFile ++ ;

        $returnData = [
            'offset'=> $offset ,
            'indexFile'=> $indexFile ,
        ];
        return $returnData ;

//        echo'<pre>';print_r( $resultFiltersUrl );echo'</pre>'.__FILE__.' '.__LINE__;
//        die(__FILE__ .' '. __LINE__ );

    }
}















