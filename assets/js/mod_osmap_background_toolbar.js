/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author Gartes | sad.net79@gmail.com | Skype : agroparknew | Telegram : @gartes
 * @date 29.11.2020 03:00
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/
/* global jQuery , Joomla   */
window.mod_osmap_background_tool = function () {
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
        // Максимаьное количество ошибок для Ajax запроса
        maxAjaxErr: 5,

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

    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {
        this._params = Joomla.getOptions('mod_osmap_background_tool', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;

        // Параметры Ajax Default
        this.setAjaxDefaultData();
        this.addEvtListener();
    };

    /**
     * Добавить слушателей событий
     */
    this.addEvtListener = function () {

        document.addEventListener('click' , function (e){
            console.log( e.target.dataset.evt )
            switch (e.target.dataset.evt) {
                case "map-go" : self.onEventMapGo(); break ;
            }
        });
    }

    /**
     * Счетчик ошибок Ajax запросов
     * @type {number}
     */
    this.counterErrorAjax = 0
    /**
     * Счетчик для плагинов osMap
     * @type {number}
     */
    this.counterPlugins = 0 ;

    /**
     * Создать карту сайта
     */
    this.onEventMapGo = function ( ){
        var countPlugins = this._params.plugins.length ;

        if ( countPlugins < this.counterPlugins ) return ;

        var pluginObj = this._params.plugins[this.counterPlugins]
        var Data = {
            option : 'com_osmap',
            view : 'xml',
            id: 1 ,
            format : 'xml' ,
            task : 'background_map',

        }
        var urlParamQuery = '';
        if ( pluginObj.element !== 'com_menu' ) {

            Data.component =  pluginObj.element;
            if ( Data.component === 'joomla' )  Data.component = 'com_content' ;
            urlParamQuery = '?component='+Data.component

        }

        Params = {
            URL : this._params.URL+ urlParamQuery ,
            dataType : this._params.dataType ,
        }


        if ( this._params.plugins[this.counterPlugins].element === 'com_virtuemart' ){
            self.counterPlugins++;
            self.load.js('/administrator/modules/mod_osmap_background_toolbar/assets/js/mod_osmap_background_virtuemart.js')
                .then(function (e){
                    console.info( 'mod_osmap_background_virtuemart - Is Loaded!' );
                    window.Mod_osmap_background_virtuemart.VirtuemartStartMap().then(function (r){
                        if ( countPlugins === self.counterPlugins ) return ;
                        console.log(r);

                        if ( countPlugins !== self.counterPlugins ) {
                            self.onEventMapGo();
                        }else{
                            self.createFileAllMapXml();
                        }
                    },function (err){console.log(err)});;
                },function (err){ console.log( err );});

            return;
        }

        if ( this._params.plugins[this.counterPlugins].element === 'com_filter' ){
            self.counterPlugins++;
            self.load.js('/administrator/modules/mod_osmap_background_toolbar/assets/js/mod_osmap_background_com_filter.js')
                .then(function (r){
                    console.info( 'mod_osmap_background_com_filter - Is Loaded!' );
                    window.Mod_osmap_background_com_filter.modOsmapBackgroundComFilterStartMap().then(function (r){
                        if ( countPlugins !== self.counterPlugins ) {
                            self.onEventMapGo();
                        }else{
                            self.createFileAllMapXml();
                        }

                    },function (err){console.log(err)});
            },function (err){console.log(err)});
        }



        var Timeout = 10000 ;
        self.AjaxPost(Data, Params).then(
            function (r) {
                self.counterPlugins++;
                console.log(r);
                self.onEventMapGo();
            },
            function (err) {
                self.counterErrorAjax++
                console.log(err);
                console.log(self._params.maxAjaxErr);
                console.log(self.counterErrorAjax);

                if (self._params.maxAjaxErr > self.counterErrorAjax) {
                    console.info('Start Timeout ' + (Timeout / 1000) + ' s.');

                    setTimeout(function () {
                        self.onEventMapGo();
                    }, Timeout)
                }
            }
        )

    }

    /**
     * Создать общий файл карты сайта
     * @returns {Promise<unknown>}
     */
    this.createFileAllMapXml = function (){
        return new Promise(function(resolve, reject) {
            var Params = {
                // URL : self._params.URL,
            }

            var Data =  self.AjaxDefaultData  ;
            Data.option =  'com_ajax' ;
            Data.task = null ;
            Data.view = null ;
            Data.component = null ;
            Data.module = 'osmap_background_toolbar' ;
            Data.method = 'createFileAllMapXml';
            self.AjaxPost( Data , Params  ).then(function (r){
                alert('All Map Xml Complete')

                resolve( 'All Map Xml Complete' );
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
     *          $doc->addScriptOptions('mod_osmap_background_tool' , $opt );
     *         ?>
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function ( Data ,   Params  ) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send( data , 'mod_osmap_background_tool' , Params ).then(function (r) {
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
        window.mod_osmap_background_tool.prototype = new GNZ11();
        window.Mod_jshopping_slider_module = new window.mod_osmap_background_tool();
    }
})()
















