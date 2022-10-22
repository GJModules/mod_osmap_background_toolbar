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
window.mod_osmap_background_virtuemart = function () {
    var $ = jQuery;
    var self = this;

    // Домен сайта
    var host = Joomla.getOptions('GNZ11').Ajax.siteUrl;
    // Медиа версия
    var __v = '?rm=';
    var passiveSupported = false;
    try {
        window.addEventListener( "test", null,
            Object.defineProperty({}, "passive", { get: function() { passiveSupported = true; } }));
    } catch(err) {}
    this.__type = false;
    this.__plugin = false;
    this.__name = false;
    this._params = {
       
        __module: false,
        RecentlyViewed : false ,
    };
    // Ajax default options
    this.AjaxDefaultData = {
        group   : null ,
        plugin  : null ,
        module  : null ,
        method  : null ,
        option  : 'com_ajax' ,
        format  : 'json' ,
        task    : null ,
    };
    // Default object parameters
    this.ParamsDefaultData = {
        // Медиа версия
        __v: '1.0.0',
        // Режим разработки
        development_on: false,
    }

    /* -----------------------------------------------------------  */
    /**
     * Номер фала для компонента - контекст
     * @type {number}
     */
    this.indexFile = 1 ;
    /**
     * Номер строки с которой начинать записывать в файл
     * @type {number}
     */
    this.indexCategory = 0 ;
    /**
     * Объект - key-> category_id; val-> sefUrl - категории
     * @type {{}}
     */
    this.categoryObjectSlug = {} ;
    /**
     * Index товара с которого начинать запись в карту
     * @type {number}
     */
    this.offsetProduct = 0;
    /**
     * Массив с товарами
     * @type {*[]}
     */
    this.ListProducts = [];
    /**
     * Количество ссылок в одном файле xml
     * @type {number}
     */
    this.limitLinksFile = 50000 ;

    /* -----------------------------------------------------------  */

    /**
     *  Массив включенных языков для многоязычных сайтов
     *  @type {array}
     */
    this.languagesSef = window.Mod_jshopping_slider_module._params.languagesSef ;
    /**
     * Счетчик перебираемых языков
     * @type {number|number|*}
     */
    this.counterLanguages = window.Mod_jshopping_slider_module.counterLanguages ;

    /* -----------------------------------------------------------  */

    /**
     * Start Init
     * @constructor
     */
    this.Init = function () {
        this._params = Joomla.getOptions('mod_osmap_background_virtuemart', this.ParamsDefaultData);
        __v = self._params.development_on ? '' : '?v=' + self._params.__v;

        var modOsmapBackgroundToolParams = Joomla.getOptions('mod_osmap_background_tool', this.ParamsDefaultData);
        this._params.URL = modOsmapBackgroundToolParams.URL ;
        // console.log( modOsmapBackgroundToolParams.URL );

        // Параметры Ajax Default
        this.setAjaxDefaultData();

        self.AjaxDefaultData.module = 'osmap_background_toolbar';
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
        document.addEventListener('click' , function (e){
            console.log( e.target.dataset.evt )
            switch (e.target.dataset.evt) {
                case "" :
                    break ;
            }
        });
    }


    this.categoryTree = [];

    /**
     * Создание карты для Virtuemart
     * @constructor
     */
    this.VirtuemartStartMap = function (){
        // Если включено MultiLanguage и еще не перебрали все установленные языки
        /*if (
            window.Mod_jshopping_slider_module._params.languagesSef.length
            && window.Mod_jshopping_slider_module._params.languagesSef.length !== window.Mod_jshopping_slider_module.counterLanguages + 1 ){
            // Переставляем на следующий язык
            self.counterLanguages++;
            self.onEventMapGo();
            return ;
        }*/
        console.log( 'mod_osmap_background_virtuemart VirtuemartStartMap languagesSef' , this.languagesSef );
        console.log( 'mod_osmap_background_virtuemart VirtuemartStartMap counterLanguages' , this.counterLanguages );


        return new Promise(function (resolveAll, rejectAll) {
            self.VirtuemartGetCategoryId().then(function (r) {


                var categoryList = r.data.categories;

                // Loop Category
                for (let i = 0; i < categoryList.length; i++) {
                    var virtuemartCategoryId = categoryList[i].virtuemart_category_id;
                    self.categoryObjectSlug[virtuemartCategoryId] = categoryList[i].sef;
                }



                // Загружаем все товары
                self.VirtuemartGetProductsInCategory( self.categoryObjectSlug ).then(function (r) {
                        resolveAll('Virtuemart Complete!')
                    /*self.createMapXmlProducts().then(function (r) {

                        }, function (err) {
                            console.log('mod_osmap_background_virtuemart', err);
                        });*/

                    /*self.createMapXmlCategory().then(function (r) {



                        }, function (err) {
                            console.log('mod_osmap_background_virtuemart', err);
                        })*/


                    },
                    function (err) {
                        console.log('mod_osmap_background_virtuemart', err);
                    }
                )

            }, function (err) {
                console.log(err);
            });
        })

    }

    /**
     * Создать файл/файлы sitemap-com_virtuemart-category-{№}.xml для Категорий
     * @returns {Promise<unknown>}
     */
    this.createMapXmlCategory = function (   ){
        return new Promise(function (resolve, reject){
            var Data = self.AjaxDefaultData ;
            Data.method = 'onCreateMapXmlCategory';
            Data.categories = self.categoryObjectSlug ;
            Data.indexCategory = self.indexCategory ;
            Data.indexFile =  self.indexFile;
            var Params = {
                // URL : self._params.URL,
            }
            self.AjaxPost( Data , Params  ).then(function (r){
                self.AjaxDefaultData.categoryListSlug = null ;
                self.AjaxDefaultData.indexCategory = null ;
                self.indexCategory = 0  ;
                self.indexFile = 1 ;
                resolve( r );
            },function (err){ console.log( err ); })
        })
    }

    /**
     * Создать файл файл/файлы sitemap-com_virtuemart-product-{№}.xml для Товаров
     * @returns {Promise<unknown>}
     */
    this.createMapXmlProducts = function (){
        // self.AjaxDefaultData.categories = null ;
        return new Promise(function (resolve, reject){

            var Data = self.AjaxDefaultData ;
            Data.method = 'onCreateMapXmlProducts';
            // Data.Products = self.ListProducts ;

            Data.offset = self.offsetProduct ;
            // Количество строк-ссылок в файле (LIMIT)
            Data.limitLinksFile = self.limitLinksFile ;

            Data.indexFile =  self.indexFile;
            // список категорий
            Data.categoryListSlug = self.categoryObjectSlug ;
            var Params = {
                // URL : self._params.URL,
            }
            self.AjaxPost( Data , Params  ).then(function (r){

                self.offsetProduct = r.data.DataProducts.offset ;
                self.indexFile = r.data.DataProducts.indexFile ;

                if ( self.offsetProduct < self.ListProducts.length ){
                   self.createMapXmlProducts().then(function (r){
                        console.log( 'mod_osmap_background_virtuemart - resolve ' , r );
                       resolve( r )
                   },function (err){console.log( 'mod_osmap_background_virtuemart' , err ); } )
                }

                console.log( 'mod_osmap_background_virtuemart' , '----------------------------' );
                console.log( 'mod_osmap_background_virtuemart' , self.ListProducts.length );
                console.log( 'mod_osmap_background_virtuemart' , '----------------------------' );

                self.offsetProduct = 0 ;
                self.indexFile = 1 ;
                resolve( r );
            },function (err){ console.log( err ); })
        });

    }

    /**
     * Получить список категорий Virtuemart
     * @returns {Promise<Array>}
     * @constructor
     */
    this.VirtuemartGetCategoryId = function (){
        return new Promise(function(resolve, reject) {
            var Params = {
                 // URL : self._params.URL,
            }

            var Data = self.AjaxDefaultData ;
            Data.method = 'getCategoryIdList';
            Data.lang = 'getCategoryIdList';


            self.AjaxPost( Data , Params  ).then(function (r){
                resolve( r );
            },function (err){ console.log( err ); })
        });
    }



    /**
     * Загрузить все товары
     * @param categoryListSlug
     * @returns {Promise<unknown>}
     * @constructor
     */
    this.VirtuemartGetProductsInCategory = async function ( categoryListSlug ){

        
        return new Promise(function(resolve, reject) {
            var Params = {
                // URL : self._params.URL,
            }
            var Data = self.AjaxDefaultData ;
            Data.method = 'getProductsLinkList';
            Data.categoryListSlug = categoryListSlug ;

            self.AjaxPost( Data , Params  ).then(function (r){
                self.ListProducts = r.data.LisProducts
                console.log( 'mod_osmap_background_virtuemart' , r.data ); 
                
                resolve( r );
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
     *          $doc->addScriptOptions('mod_osmap_background_virtuemart' , $opt );
     *         ?>
     * @returns {Promise}
     * @constructor
     */
    this.AjaxPost = function (Data , Params ) {
        var data = $.extend(true, this.AjaxDefaultData, Data);
        return new Promise(function (resolve, reject) {
            self.getModul("Ajax").then(function (Ajax) {
                // Не обрабатывать сообщения
                Ajax.ReturnRespond = true;
                // Отправить запрос
                Ajax.send(data, 'mod_osmap_background_virtuemart' , Params ).then(function (r) {
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
        this.AjaxDefaultData.module = this._params.__module ;
        this._params.__name = this._params.__name || this._params.__module ;
    }
    this.Init();
};
(function(){
if (typeof window.GNZ11 === "undefined"){
    // Дожидаемся события GNZ11Loaded
    document.addEventListener('GNZ11Loaded', function (e) {
        start()
    }, false);
} else {
    start()
}
// Start prototype
function start(){
    window.mod_osmap_background_virtuemart.prototype = new GNZ11();
    window.Mod_osmap_background_virtuemart = new window.mod_osmap_background_virtuemart();
}
})()
















