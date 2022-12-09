/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date 23.09.22 15:55
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/* global jQuery , Joomla   */
window.mod_osmap_background_com_filter = function () {
    var $ = jQuery;
    var self = this;

    // Домен сайта
    var host = Joomla.getOptions('GNZ11').Ajax.siteUrl;
    // Медиа версия
    var __v = '?rm=';
    var passiveSupported = false;
    try {
        window.addEventListener("test", null,
            Object.defineProperty({}, "passive", {
                get: function () {
                    passiveSupported = true;
                }
            }));
    } catch (err) {
    }
    this.__type = false;
    this.__plugin = false;
    this.__name = false;
    this._params = {

        __module: false,
        RecentlyViewed: false,
    };
    // Ajax default options
    this.AjaxDefaultData = {
        group: null,
        plugin: null,
        module: null,
        method: null,
        option: 'com_ajax',
        format: 'json',
        task: null,
    };
    // Default object parameters
    this.ParamsDefaultData = {
        // Медиа версия
        __v: '1.0.0',
        // Режим разработки
        development_on: false,
    }

    /*---------------------------------------------------------------*/
    /**
     * Индекс файла
     * @type {number}
     */
    this.indexFile = 1 ;
    /**
     * offset - выборки ссылок
     * @type {number}
     */
    this.offsetLink = 0 ;
    /*---------------------------------------------------------------*/


    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {
        this._params = Joomla.getOptions('mod_osmap_background_com_filter', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;

        // Параметры Ajax Default
        this.setAjaxDefaultData();
        this.addEvtListener();
    };

    /**
     * Добавить слушателей событий
     * Для элементов с событиями должен быть установлен атрибут data-evt=""
     * etc. -   <a data-evt="map-go">
     *              <span class="icon-database" aria-hidden="true"></span>
     *              Map-Go
     *          </a>
     */
    this.addEvtListener = function () {
        document.addEventListener('click', function (e) {
            console.log(e.target.dataset.evt)
            switch (e.target.dataset.evt) {
                case "" :
                    break;
            }
        });
    }

    /**
     * Начало создание карты для фильтра
     * @returns {Promise<unknown>}
     */
    this.modOsmapBackgroundComFilterStartMap = function (){
       return new Promise(function (resolveAll, rejectAll){
            self.createComFilterMap().then(function (r){
                if ( +r.data.count === +r.data.offset ) {
                    console.log( 'mod_osmap_background_com_filter' , 'Osmap Background Com_Filter - FINISH' );
                    console.log( 'mod_osmap_background_com_filter indexFile' , self.indexFile );
                    console.log( 'mod_osmap_background_com_filter offsetLink' , self.offsetLink );
                    console.log( 'mod_osmap_background_com_filter +r.data.count' , +r.data.count );
                    console.log( 'mod_osmap_background_com_filter +r.data.offset' , +r.data.offset );

                    resolveAll( r );
                    console.log( 'mod_osmap_background_com_filter - r ' , r );
                    console.log( 'mod_osmap_background_com_filter' , '--resolveAll--' );
                    window.Mod_jshopping_slider_module.onEventMapGo()

                }
            })
        })
    }

    /**
     * Создание карты для фильтра
     * @returns {Promise<unknown>}
     */
    this.createComFilterMap = function (){
        return new Promise(function(resolve, reject) {
            var Params = {
                // URL : self._params.URL,
            }
            var Data = self.AjaxDefaultData ;
            Data.module = 'osmap_background_toolbar' ;
            Data.method = 'createComFilterMap';
            Data.indexFile =  self.indexFile;
            Data.offset = self.offsetLink ;
            Data.module_params = window.Mod_jshopping_slider_module._params ;


            self.AjaxPost( Data , Params  ).then(function (r){


                if ( +r.data.offset < +r.data.count ){
                    self.indexFile = +r.data.indexFile ;
                    self.offsetLink = +r.data.offset ;
                    self.modOsmapBackgroundComFilterStartMap().then(function (r){
                        // resolve( r  );
                    },function (err){console.log(err)});

                }else{
                    console.log( 'mod_osmap_background_com_filter+' , 'Com Filter Map Complete' );
                    resolve( r );
                }


            },function (err){ console.log( err ); })
        });
    }


    /**
     * Отправить запрос
     * @param Data - отправляемые данные
     * Должен содержать Data.task = 'taskName';
     * @param Params - Array
     *          Params = {
     *             URL : this._params.URL,
     *             dataType : this._params.dataType , 
     *         }
     *         <?php
     *          $doc = \Joomla\CMS\Factory::getDocument();
     *          $opt = [
     *              // Медиа версия
     *              '__v' => '1.0.0',
     *                 // Режим разработки
     *              'development_on' => false,
     *              // URL - Сайта
     *              'URL' => JURI::root(),
     *              'dataType' => 'html' , - по умлчанию 'json'
     *          ];
     *          $doc->addScriptOptions('mod_osmap_background_com_filter' , $opt );
     *         ?>
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function (Data, Params) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send(data, 'mod_osmap_background_com_filter', Params).then(function (r) {
                    resolve(r);
                }, function (err) {
                    console.error(err);
                    reject(err);
                })
            });
        });
    };
    /**
     * Параметры Ajax Default
     */
    this.setAjaxDefaultData = function () {
        this.AjaxDefaultData.group = this._params.__type;
        this.AjaxDefaultData.plugin = this._params.__name;
        this.AjaxDefaultData.module = this._params.__module;
        this._params.__name = this._params.__name || this._params.__module;
    }
    this.Init();
};
(function () {
    if (typeof window.GNZ11 === "undefined") {
        // Дожидаемся события GNZ11Loaded
        document.addEventListener('GNZ11Loaded', function (e) {
            start()
        }, false);
    } else {
        start()
    }

// Start prototype
    function start() {
        window.mod_osmap_background_com_filter.prototype = window.Mod_jshopping_slider_module;
        window.Mod_osmap_background_com_filter = new window.mod_osmap_background_com_filter();
    }
})()
















