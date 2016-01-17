<?php
/*
Plugin Name:    Facebook Comments Sync
Plugin URI:     http://btdogan.com/facebook-comments-sync/
Description:    Add the Facebook Comments box to your website and sync it with your Wordpress database; show comment counts and use latest comments widget.
Version:        1.1.0
Author:         btdogan
Author URI:     http://btdogan.com
License:        GPL v3, MIT

=====GNU General Public License V3 (GPL v3)=====

Copyright(C) 2015, Burak T. DOGAN - burak@btdogan.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

=====The MIT License (MIT)=====

Copyright (c) 2015 Burak T. DOGAN - burak@btdogan.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

All rights reserved.

*/
if (is_admin())
    require 'fbcs-admin.php';
else
    require 'fbcs-frontend.php';


// Add settings link on plugin page
function fbcs_link($links)
{
    $settings_link = '<a href="options-general.php?page=fbcommentssync">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'fbcs_link');
?>