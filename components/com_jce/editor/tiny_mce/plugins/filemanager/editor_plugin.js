/*  
 * File Manager                 2.1.6
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    13 February 2013
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function(){tinymce.create('tinymce.plugins.FileManager',{init:function(ed,url){ed.addCommand('mceFileManager',function(){var e=ed.selection.getNode();ed.windowManager.open({file:ed.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=filemanager',width:780+ed.getLang('filemanager.delta_width',0),height:660+ed.getLang('filemanager.delta_height',0),inline:1,popup_css:false},{plugin_url:url});});this.editor=ed;this.url=url;function isFile(n){return ed.dom.is(n,'a.jce_file, a.wf_file, a.mceItemGoogleDocs, img.mceItemGoogleDocs');}
ed.addButton('filemanager',{title:'filemanager.desc',cmd:'mceFileManager',image:url+'/img/filemanager.png'});ed.onNodeChange.add(function(ed,cm,n,co){if((n&&n.nodeName=='IMG'||n.nodeName=='SPAN')&&/(jce|wf)_/i.test(ed.dom.getAttrib(n,'class'))){n=ed.dom.getParent(n,'A');}
cm.setActive('filemanager',co&&isFile(n));if(n&&isFile(n)){cm.setActive('filemanager',true);}});ed.onInit.add(function(ed){if(!ed.settings.compress.css)
ed.dom.loadCSS(url+"/css/content.css");if(ed&&ed.plugins.contextmenu){ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:'filemanager.desc',icon_src:url+'/img/filemanager.png',cmd:'mceFileManager'});});}});ed.onSetContent.add(function(){tinymce.each(ed.dom.select('img.mceItemIframe',ed.getBody()),function(n){if(n.className.indexOf('mceItemGoogleDocs')==-1){var data=tinymce.util.JSON.parse(ed.dom.getAttrib(n,'data-mce-json'));if(data&&data.iframe&&data.iframe.src&&/:\/\/docs.google.com\/viewer/i.test(data.iframe.src)){ed.dom.addClass(n,'mceItemGoogleDocs');}}});});},insertUploadedFile:function(o){var ed=this.editor,extensions=ed.getParam('filemanager_dragdrop_upload_files');if(extensions&&new RegExp('\.('+extensions.split(',').join('|')+')$','i').test(o.file)){var args={'href':o.file,'title':o.title||o.name},html='';if(o.googledocs){args.href='http://docs.google.com/viewer?url='+encodeURIComponent(decodeURIComponent(ed.documentBaseURI.toAbsolute(args.href,ed.settings.remove_script_host)));if(o.googledocs=='embedded'){args.href+='&embedded=true';var w=o.width||'100%',h=o.height||'100%';return ed.dom.create('img',{'alt':o.name,'width':w,'height':h,'src':this.url+'/img/trans.gif','data-mce-json':'{"iframe":{"src" : "'+args.href+'"}}','class':'mceItemIframe mceItemGoogleDocs'});}}
if(o.features){tinymce.each(o.features,function(n){html+=ed.dom.createHTML(n.node,n.attribs||{},n.html||'');});}else{html=o.name;}
var cls=['wf_file'];var attribs=['target','id','dir','class','charset','style','hreflang','lang','type','rev','rel','tabindex','accesskey'];if(o.style){args.style=ed.dom.parseStyle(o.style);delete o.style;}
tinymce.each(attribs,function(k){if(typeof o[k]!=='undefined'){if(k=='class'){cls.push(o[k]);}else{args[k]=o[k];}}});args['class']=cls.join(' ');return ed.dom.create('a',args,html);}
return false;},getUploadURL:function(file){var ed=this.editor,extensions=ed.getParam('filemanager_dragdrop_upload_files');if(extensions&&new RegExp('(application|audio|video|text|image)\/('+extensions.split(',').join('|')+')','i').test(file.type)){return this.editor.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=filemanager';}
return false;},getInfo:function(){return{longname:'File Manager',author:'Ryan Demmer',authorurl:'http://www.joomlacontenteditor.net',infourl:'http://www.joomlacontenteditor.net/index.php?option=com_content&amp;view=article&amp;task=findkey&amp;tmpl=component&amp;lang=en&amp;keyref=filemanager.about',version:'2.1.6'};}});tinymce.PluginManager.add('filemanager',tinymce.plugins.FileManager);})();