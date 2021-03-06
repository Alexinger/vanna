/*
 * @licstart  The following is the entire license notice for the 
 * JavaScript code in this page.
 * 
 * Copyright (c) 2014 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @licend  The above is the entire license notice
 * for the JavaScript code in this page.
 */

tinymce.PluginManager.add('cforms', function (ed) {

    ed.addButton('cforms', {
        title: 'Insert a form',
        type: 'menubutton',
        text: 'cforms',
        menu: cforms2_formnames.map(function (item) {
            return {
                text: item,
                onclick: function () {
                    ed.insertContent('[cforms name="' + item + '"]');
                }
            };
        })
    });

});
