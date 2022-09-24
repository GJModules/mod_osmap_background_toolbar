<?php
/**
 * @package    mod_osmap_background_toolbar
 *
 * @author     Gartes <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

// Access to module parameters
$domain = $params->get('domain', 'https://www.joomla.org');
?>
<div class="btn-group">
    <span class="btn-group separator"></span>
<!--    href="https://www.new.marketprofil.ru/" -->
    <a  data-evt="map-go">
        <span class="icon-database" aria-hidden="true"></span>
        Map-Go
    </a>

</div>
